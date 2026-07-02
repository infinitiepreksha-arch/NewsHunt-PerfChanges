<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PaymentGatewaySettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['payment-gateway-settings']);

        $stripeDetails   = PaymentSetting::where('gateway', 'stripe')->first();
        $razorpayDetails = PaymentSetting::where('gateway', 'razorpay')->first();
        $applepayDetails = PaymentSetting::where('gateway', 'applepay')->first();

        $paymentGateway = [
            'Stripe'   => [
                'currency_code'      => $stripeDetails->currency ?? '',
                'currency_symbol'    => $stripeDetails->currency_symbol ?? '',
                'stripe_secret'      => $stripeDetails->stripe_secret ?? '',
                'stripe_publishable' => $stripeDetails->stripe_publishable ?? '',
                'webhook_secret_key' => $stripeDetails->stripe_webhook_secret ?? '',
                'webhook_url'        => $stripeDetails->stripe_webhook_url ?? '',
                'status'             => $stripeDetails->status ?? 0,
            ],
            'Razorpay' => [
                'currency_code'      => $razorpayDetails->currency ?? '',
                'currency_symbol'    => $razorpayDetails->currency_symbol ?? '',
                'secret_key'         => $razorpayDetails->razorpay_secret ?? '',
                'publishable_key'    => $razorpayDetails->razorpay_key ?? '',
                'webhook_secret_key' => $razorpayDetails->razorpay_webhook_secret ?? '',
                'webhook_url'        => $razorpayDetails->razorpay_webhook_url ?? '',
                'status'             => $razorpayDetails->status ?? 0,
            ],
            'applepay' => [
                'currency_code'       => $applepayDetails->currency ?? '',
                'currency_symbol'     => $applepayDetails->currency_symbol ?? '',
                'status'              => $applepayDetails->status ?? 0,
                'apple_shared_secret' => $applepayDetails->apple_shared_secret ?? '',
                'apple_issuer_id'     => $applepayDetails->apple_issuer_id ?? '',
                'apple_key_id'        => $applepayDetails->apple_key_id ?? '',
                'apple_bundle_id'     => $applepayDetails->apple_bundle_id ?? '',
                'apple_api_key_path'  => $applepayDetails->apple_api_key_path ?? '',
                'apple_environment'   => $applepayDetails->apple_environment ?? '',
            ],
        ];

        $data = [
            'paymentGateway' => $paymentGateway,
        ];

        return view('admin.settings.payment-gateway', $data);
    }

    public function store(Request $request)
    {

        try {
            $gatewayData = $request->input('gateway');

            foreach ($gatewayData as $gatewayName => $data) {
                $gatewayKey = strtolower($gatewayName);

                // Base rules
                $rules = [
                    'currency_code'   => 'required|string|max:10',
                    'currency_symbol' => 'required|string|max:5',
                    'status'          => 'nullable|in:0,1',
                ];

                // Gateway specific rules
                if ($gatewayKey === 'stripe') {
                    $rules = array_merge($rules, [
                        'stripe_secret'      => 'required|string',
                        'stripe_publishable' => 'required|string', // maps to stripe_publishable
                        'webhook_secret_key' => 'required|string', // maps to stripe_webhook_secret
                    ]);
                }

                if ($gatewayKey === 'razorpay') {
                    $rules = array_merge($rules, [
                        'secret_key'         => 'required|string', // maps to razorpay_secret
                        'publishable_key'    => 'required|string', // maps to razorpay_key
                        'webhook_secret_key' => 'required|string', // maps to razorpay_webhook_secret
                    ]);
                }

                if ($gatewayKey === 'applepay') {
                    $rules = array_merge($rules, [
                        'apple_shared_secret' => 'required|string',
                        'apple_issuer_id'     => 'required|string',
                        'apple_key_id'        => 'required|string',
                        'apple_bundle_id'     => 'required|string',
                        'apple_api_key_file'  => 'nullable|file',
                    ]);
                }

                // Validate
                $validator = Validator::make($data + $request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'errors' => $validator->errors(),
                    ], 422);
                }

                // Save or update
                $paymentSetting                  = PaymentSetting::firstOrNew(['gateway' => $gatewayKey]);
                $paymentSetting->currency        = $data['currency_code'] ?? '';
                $paymentSetting->currency_symbol = $data['currency_symbol'] ?? '';
                $paymentSetting->status          = $data['status'] ?? 0;

                switch ($gatewayKey) {
                    case 'stripe':
                        $paymentSetting->stripe_secret         = $data['stripe_secret'];
                        $paymentSetting->stripe_publishable    = $data['stripe_publishable'];
                        $paymentSetting->stripe_webhook_secret = $data['webhook_secret_key'];
                        $paymentSetting->stripe_webhook_url    = $data['webhook_url'];
                        break;

                    case 'razorpay':
                        $paymentSetting->razorpay_secret         = $data['secret_key'];
                        $paymentSetting->razorpay_key            = $data['publishable_key'];
                        $paymentSetting->razorpay_webhook_secret = $data['webhook_secret_key'];
                        $paymentSetting->razorpay_webhook_url    = $data['webhook_url'];
                        break;

                    case 'applepay':
                        $paymentSetting->apple_shared_secret = $data['apple_shared_secret'] ?? '';
                        $paymentSetting->apple_issuer_id     = $data['apple_issuer_id'] ?? '';
                        $paymentSetting->apple_key_id        = $data['apple_key_id'] ?? '';
                        $paymentSetting->apple_bundle_id     = $data['apple_bundle_id'] ?? '';
                        $paymentSetting->apple_environment   = $data['apple_environment'] ?? '';

                        // Handle Apple API Key file upload
                        if ($request->hasFile('apple_api_key_file')) {
                            $file = $request->file('apple_api_key_file');

                            // Validate file extension
                            if ($file->getClientOriginalExtension() !== 'p8') {
                                throw new \Exception('Apple API Key file must be in .p8 format');
                            }

                            // Delete old file if exists
                            if ($paymentSetting->apple_api_key_path && Storage::exists($paymentSetting->apple_api_key_path)) {
                                Storage::delete($paymentSetting->apple_api_key_path);
                            }

                            // Store file in storage/app/keys/ directory
                            $fileName = 'AppleApiKey_' . time() . '.p8';
                            $filePath = $file->storeAs('keys', $fileName);

                                                                             // Save the file path
                            $paymentSetting->apple_api_key_path = $filePath; // This will be "keys/AppleApiKey_timestamp.p8"
                        }
                        break;
                }

                $paymentSetting->save();
            }

            return response()->json([
                'status'   => true,
                'message'  => 'Payment gateway settings updated successfully!',
                'redirect' => route('payment-gateway.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update payment gateway settings: ' . $e->getMessage(),
            ], 500);
        }
    }
}
