<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\AppleCreditTransaction;
use App\Models\CreditPack;
use App\Models\UserCredits;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreditPackApiController extends Controller
{
    public function index()
    {
        $creditPacks = CreditPack::orderBy('id', 'desc')->get();

        // Get Apple Pay settings
        $appleSettings = DB::table('payment_settings')
            ->where('gateway', 'applepay')
            ->where('status', 1)
            ->first();

        $currency = $appleSettings->currency;

        $creditPacks = $creditPacks->map(function ($pack) use ($currency) {
            $pack->currency = $currency;
            return $pack;
        });

        return response()->json([
            'error'   => false,
            'message' => 'Credit packs fetched successfully',
            'data'    => $creditPacks,
        ]);
    }

    public function user_credits()
    {
        $userId = auth()->id(); // returns null if not authenticated

        if (! $userId) {
            return response()->json([
                'error'   => true,
                'message' => 'User not authenticated',
                'data'    => [],
            ], 401);
        }

        $userCredits = UserCredits::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'error'   => false,
            'message' => 'Credit packs fetched successfully',
            'data'    => $userCredits,
        ]);
    }

    public function verifyCreditPacksApplePurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'product_id'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => true,
                'message' => $validator->errors()->first(),
                'data'    => (object) [],
            ], 422);
        }

        // Get authenticated user
        $user = auth()->user();
        if (! $user) {
            Log::error('User not authenticated for Apple credit pack verification');
            return response()->json([
                'error'   => true,
                'message' => 'User not authenticated',
                'data'    => (object) [],
            ], 401);
        }

        // Get Apple settings
        $appleSettings = DB::table('payment_settings')
            ->where('gateway', 'applepay')
            ->where('status', 1)
            ->first();

        if (! $appleSettings) {
            return response()->json([
                'error'   => true,
                'message' => 'Apple Pay settings not found or inactive',
                'data'    => (object) [],
            ], 400);
        }

        try {
            // Get credit pack
            $creditPack = CreditPack::where('product_id', $request->product_id)->first();
            if (! $creditPack) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Credit pack not found',
                    'data'    => (object) [],
                ], 400);
            }

            // Generate JWT
            $privateKeyPath = storage_path('app/' . $appleSettings->apple_api_key_path);
            if (! file_exists($privateKeyPath)) {
                throw new \Exception('Apple API key file not found');
            }

            $privateKey = file_get_contents($privateKeyPath);
            if ($privateKey === false) {
                throw new \Exception('Failed to read Apple API key file');
            }

            $issuedAt   = time();
            $expiration = $issuedAt + (60 * 10); // 10 minutes
            $payload    = [
                'iss' => $appleSettings->apple_issuer_id,
                'iat' => $issuedAt,
                'exp' => $expiration,
                'aud' => 'appstoreconnect-v1',
                'bid' => $appleSettings->apple_bundle_id,
            ];

            try {
                $jwt = JWT::encode($payload, $privateKey, 'ES256', $appleSettings->apple_key_id);
            } catch (\Exception $e) {
                throw new \Exception('Failed to generate JWT: ' . $e->getMessage());
            }

            // Apple StoreKit API call
            $transactionId = $request->transaction_id;
            $url           = $appleSettings->apple_environment === 'Sandbox'
                ? "https://api.storekit-sandbox.apple.com/inApps/v1/transactions/{$transactionId}"
                : "https://api.storekit.apple.com/inApps/v1/transactions/{$transactionId}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $jwt,
                'Content-Type: application/json',
            ]);

            $result    = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($result === false || $curlError) {
                throw new \Exception('Failed to communicate with Apple API: ' . $curlError);
            }

            if ($httpCode === 404) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Transaction not found or invalid',
                    'data'    => (object) [],
                ], 400);
            }

            if ($httpCode === 401) {
                throw new \Exception('Authentication with Apple API failed');
            }

            if ($httpCode !== 200) {
                throw new \Exception('Apple API returned error: HTTP ' . $httpCode);
            }

            // Decode Apple API response
            $responseData = null;
            $jwtParts     = explode('.', trim($result, '"'));
            if (count($jwtParts) !== 3) {
                $responseData = json_decode($result, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid response format from Apple API');
                }
            } else {
                $payload = json_decode(base64_decode(str_pad(strtr($jwtParts[1], '-_', '+/'), strlen($jwtParts[1]) % 4, '=', STR_PAD_RIGHT)), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Failed to decode Apple API response');
                }
                $responseData = $payload;
            }

            // Validate bundle ID
            $responseBundleId = $responseData['bundleId'] ?? $responseData['bid'] ?? null;
            if ($responseBundleId && $responseBundleId !== $appleSettings->apple_bundle_id) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Bundle ID mismatch',
                    'data'    => (object) [],
                ], 400);
            }

            // Validate product ID
            $responseProductId = $responseData['productId'] ?? $responseData['pid'] ?? null;
            if ($responseProductId !== $request->product_id) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Product ID mismatch',
                    'data'    => (object) [],
                ], 400);
            }

            // Extract transaction data
            $transactionId         = $responseData['transactionId'] ?? $request->transaction_id;
            $originalTransactionId = $responseData['originalTransactionId'] ?? $transactionId;

            // Prevent duplicate processing
            $duplicateCheck = AppleCreditTransaction::where('transaction_id', $transactionId)->exists();
            if ($duplicateCheck) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Transaction already processed',
                    'data'    => [],
                ], 400);
            }

            // Process credits
            return DB::transaction(function () use ($user, $creditPack, $transactionId, $originalTransactionId, $request) {

                AppleCreditTransaction::create([
                    'user_id'         => $user->id,
                    'transaction_id'  => $originalTransactionId,
                    'product_id'      => $request->product_id,
                    'credits_awarded' => $creditPack->credits,
                    'created_at'      => now(),
                ]);

                $userCredit = UserCredits::where('user_id', $user->id)->first();

                if ($userCredit) {
                    $userCredit->credits_purchased += $creditPack->credits;
                    $userCredit->total_credits += $creditPack->credits;
                    $userCredit->available_credits += $creditPack->credits;
                    $userCredit->save();
                } else {
                    $userCredit = UserCredits::create([
                        'user_id'           => $user->id,
                        'credits_purchased' => $creditPack->credits,
                        'credits_consumed'  => 0,
                        'total_credits'     => $creditPack->credits,
                        'available_credits' => $creditPack->credits,
                    ]);
                }

                return response()->json([
                    'error'   => false,
                    'message' => 'Credit pack purchase verified successfully',
                    'data'    => [
                        'purchase' => [
                            'credits_awarded'   => $creditPack->credits,
                            'total_credits'     => $userCredit->total_credits,
                            'available_credits' => $userCredit->available_credits,
                            'credits_purchased' => $userCredit->credits_purchased,
                            'product_id'        => $request->product_id,
                            'transaction_id'    => $transactionId,
                            'credit_pack'       => [
                                'id'      => $creditPack->id,
                                'name'    => $creditPack->name,
                                'credits' => $creditPack->credits,
                                'price'   => $creditPack->price,
                            ],
                        ],
                    ],
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Apple Credit Pack Verification Failed: ' . $e->getMessage(), [
                'transaction_id' => $request->transaction_id ?? null,
                'product_id'     => $request->product_id ?? null,
                'user_id'        => auth()->id(),
                'trace'          => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => true,
                'message' => 'Failed to verify credit pack purchase: ' . $e->getMessage(),
                'data'    => (object) [],
            ], 500);
        }
    }

}
