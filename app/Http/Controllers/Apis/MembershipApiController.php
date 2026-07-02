<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\PaymentSetting;
use App\Models\Plan;
use App\Models\PlanTenure;
use App\Models\Setting;
use App\Models\SmartAd;
use App\Models\SmartAdsDetail;
use App\Models\SmartAdsPayment;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCredits;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use Stripe\Stripe;

class MembershipApiController extends Controller
{
    /**
     * Get membership plans + plan tenure details + payment URL (if tenureId provided)
     * Also includes user's active subscription and transactions
     */
    public function membership_plan(Request $request)
    {
        $user = Auth::user();

        $plans = Plan::with(['features', 'planTenures'])
            ->where('status', true)
            ->get();

        foreach ($plans as $plan) {
            foreach ($plan->planTenures as $tenure) {
                $tenure->start_date = "";
                $tenure->end_date   = "";
            }
        }
        $tenureId = $request->input('tenure_id');
        $planId   = $request->input('plan_id');
        $tenure   = null;
        $plan     = null;

        if ($tenureId) {
            $tenure = PlanTenure::with('plan')->find($tenureId);
            $plan   = $tenure->plan;
        }

        // Get active subscription with features (only if user is authenticated)
        $activeSubscription = $user ? Subscription::with(['plan', 'planTenure', 'feature'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->first() : null;

        // Get payment gateway from transaction table for active subscription

        $payment_gateway = "";
        if ($activeSubscription && $user) {
            $transaction = DB::table('transaction')
                ->where('user_id', $user->id)
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->first();

            $payment_gateway = $transaction ? $transaction->payment_gateway : "";
        }

        // Settings
        // Retrieve settings
        $settings           = Setting::all()->keyBy('name')->pluck('value', 'name')->all();
        $freeTrialPostLimit = (int) ($settings['free_trial_post_limit'] ?? '101');
        $storyLimit         = (int) ($settings['free_trial_story_limit'] ?? '102');
        $epaperLimit        = (int) ($settings['free_trial_e_papers_and_magazines_limit'] ?? '102');

        // Features
        $features = $activeSubscription ? Feature::where('plan_id', $activeSubscription->plan_id)->first() : null;

        if ($activeSubscription && $features) {
            // when feature number of articles is less than active subscription article count, then greater value -1 and final artical count send
            if ($activeSubscription->article_count > $features->number_of_articles) {
                $activeSubscription->article_count = $activeSubscription->article_count - 1 ?? 0;
            } else {
                $activeSubscription->article_count = $activeSubscription->article_count ?? 0;
            }

            if ($activeSubscription->story_count > $features->number_of_stories) {
                $activeSubscription->story_count = $activeSubscription->story_count - 1 ?? 0;
            } else {
                $activeSubscription->story_count = $activeSubscription->story_count ?? 0;
            }

            if ($activeSubscription->e_paper_count > $features->number_of_e_papers_and_magazines) {
                $activeSubscription->e_paper_count = $activeSubscription->e_paper_count - 1 ?? 0;
            } else {
                $activeSubscription->e_paper_count = $activeSubscription->e_paper_count ?? 0;
            }
        }
        
        // Active subscription data
        $activeSubscriptionData = $activeSubscription ? [
            'duration'                                => $activeSubscription->duration,
            'status'                                  => $activeSubscription->status ? true : false,
            'start_date'                              => $activeSubscription->start_date,
            'end_date'                                => $activeSubscription->end_date,
            'plan_id'                                 => $activeSubscription->plan_id,
            'tenure_id'                               => $activeSubscription->plan_tenure_id,
            'free_trial_post_limit'                   => 0, // Set to 0 when subscription exists
            'free_trial_story_limit'                  => 0, // Set to 0 when subscription exists
            'free_trial_e_papers_and_magazines_limit' => 0, // Set to 0 when subscription exists
            'plan_name'                               => $activeSubscription ? $activeSubscription->plan->name ?? null : null,
            'is_ads_Active'                           => $features ? $features->is_ads_free : false,
            'article_count'                           => $activeSubscription->article_count ?? 0,
            'story_count'                             => $activeSubscription->story_count ?? 0,
            'e_paper_count'                           => $activeSubscription->e_paper_count ?? 0,
            'total_count'                             => ($activeSubscription->article_count ?? 0) + ($activeSubscription->story_count ?? 0) + ($activeSubscription->e_paper_count ?? 0),
            'max_articles'                            => $features ? $features->number_of_articles : 0,
            'max_stories'                             => $features ? $features->number_of_stories : 0,
            'max_epaper'                              => $features->number_of_e_papers_and_magazines,
            'payment_gateway'                         => $payment_gateway,
        ] : [
            'duration'                                => 0,
            'status'                                  => false,
            'start_date'                              => "",
            'end_date'                                => "",
            'plan_id'                                 => "",
            'tenure_id'                               => "",
            'free_trial_post_limit'                   => $freeTrialPostLimit,
            'free_trial_story_limit'                  => $storyLimit,
            'free_trial_e_papers_and_magazines_limit' => $epaperLimit,
            'plan_name'                               => "",
            'is_ads_Active'                           => false,
            'article_count'                           => 0,
            'story_count'                             => 0,
            'e_paper_count'                           => 0,
            'total_count'                             => 0,
            'max_articles'                            => 0,
            'max_stories'                             => 0,
            'max_epaper'                              => 0,
            'payment_gateway'                         => "",
        ];

        // Limit Config
        $typeConfig = [
            'article' => [
                'sub_field'     => 'article_count',
                'feature_field' => 'number_of_articles',
                'read_field'    => 'article_read_count',
                'total_field'   => 'total_article_read_count',
                'free_limit'    => $freeTrialPostLimit,
            ],
            'story'   => [
                'sub_field'     => 'story_count',
                'feature_field' => 'number_of_stories',
                'read_field'    => 'story_read_count',
                'total_field'   => 'total_story_read_count',
                'free_limit'    => $storyLimit,
            ],
            'epaper'  => [
                'sub_field'     => 'e_paper_count',
                'feature_field' => 'number_of_e_papers_and_magazines',
                'read_field'    => 'epaper_read_count',
                'total_field'   => 'total_epaper_read_count',
                'free_limit'    => $epaperLimit,
            ],
        ];

        // Build limit details
        $limitDetails = [];

        foreach ($typeConfig as $type => $cfg) {

            $used = $activeSubscription
                ? (int) ($activeSubscription->{$cfg['sub_field']} ?? 0)
                : 0;

            $max = $features
                ? (int) ($features->{$cfg['feature_field']} ?? 0)
                : 0;

            // ✅ Fix here
            $used = ($max > 0) ? min($used, $max) : $used;

            $limitDetails[$type . '_limit_details'] = [
                'plan_' . $type . '_count'      => $used,
                'plan_total_max_' . $type . 's' => $max,
            ];
        }

        // Mark active plan
        $plans = $plans->map(function ($plan) use ($activeSubscription) {
            $plan->is_active_plan = $activeSubscription &&
            $activeSubscription->plan_id === $plan->id;
            return $plan;
        });

        // Payment setting
        $paymentsetting = PaymentSetting::where('status', true)->first();

        // Last subscription end date
        $lastSubscriptionEndDate = $user
            ? Subscription::where('user_id', $user->id)
            ->orderByDesc('end_date')
            ->value('end_date') ?? ""
            : "";

        return response()->json([
            'error'   => false,
            'data'    => [
                'plans'                   => $plans ?? "",
                'active_subscription'     => $activeSubscriptionData,
                'currency'                => $paymentsetting->currency ?? "",
                'currency_symbol'         => $paymentsetting->currency_symbol ?? "",
                'lastSubscriptionEndDate' => $lastSubscriptionEndDate,

                // ✅ NEW LIMIT OBJECTS
                'article_limit_details'   => $limitDetails['article_limit_details'],
                'story_limit_details'     => $limitDetails['story_limit_details'],
                'epaper_limit_details'    => $limitDetails['epaper_limit_details'],
            ],
            'message' => 'Membership data retrieved successfully',
        ]);
    }

    public function createStripeSession(Request $request)
    {
        $setting = PaymentSetting::where('gateway', 'stripe')->where('status', true)->first();
        if (! $setting) {
            return response()->json(['status' => 'error', 'message' => 'Stripe not enabled'], 403);
        }

        if ($request->tenure_id) {
            $tenure = PlanTenure::with('plan')->find($request->tenure_id);

            if (! $tenure || ! $tenure->plan) {
                return response()->json(['status' => 'error', 'message' => 'Invalid tenure or plan not found'], 404);
            }

            $currency   = strtolower($setting->currency ?? 'inr');
            $amount     = floatval($tenure->price);
            $unitAmount = intval(round($amount * 100));

            $minUnitAmount = match ($currency) {
                'inr'   => 50,
                'usd'   => 50,
                'amd'   => 130,
                default => 50
            };

            if ($unitAmount < $minUnitAmount) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Minimum amount required for $currency is $minUnitAmount units.",
                ]);
            }

            Stripe::setApiKey($setting->stripe_secret);

            try {
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => [[
                        'price_data' => [
                            'currency'     => $currency,
                            'unit_amount'  => $unitAmount,
                            'product_data' => [
                                'name' => $tenure->plan->name . ' Plan',
                            ],
                        ],
                        'quantity'   => 1,
                    ]],
                    'mode'                 => 'payment',
                    'success_url'          => route('payment.success'),
                    'cancel_url'           => route('payment.cancel'),
                    'metadata'             => [
                        'user_id'   => Auth::id(),
                        'plan_id'   => $tenure->plan->id,
                        'tenure_id' => $tenure->id,
                    ],
                ]);

                return response()->json([
                    'error'   => false,
                    'message' => 'Stripe checkout url generated successfully',
                    'data'    => [
                        'checkout_url' => $session->url,
                    ],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Stripe Error: ' . $e->getMessage(),
                ]);
            }
        } elseif ($request->ad_details_id) {
            $adDetails = SmartAdsDetail::find($request->ad_details_id);
            if (! $adDetails) {
                return response()->json(['status' => 'error', 'message' => 'Invalid ad details'], 404);
            }

            // Get smart_ad_id from request if present, otherwise from adDetails
            $smartAdId = $request->smart_ad_id ?? $adDetails->smart_ad_id;

            $ad = SmartAd::find($smartAdId);
            if (! $ad) {
                return response()->json(['status' => 'error', 'message' => 'Ad not found'], 404);
            }

            $currency   = strtolower($setting->currency ?? 'usd');
            $amount     = floatval($adDetails->total_price);
            $unitAmount = intval(round($amount * 100));

            $minUnitAmount = match ($currency) {
                'inr'   => 50,
                'usd'   => 50,
                'amd'   => 130,
                default => 50,
            };

            if ($unitAmount < $minUnitAmount) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Minimum amount required for $currency is $minUnitAmount units.",
                ], 400);
            }

            // Set Stripe API key
            Stripe::setApiKey($setting->stripe_secret);

            try {
                // Create Stripe checkout session
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => [[
                        'price_data' => [
                            'currency'     => $currency,
                            'unit_amount'  => $unitAmount,
                            'product_data' => [
                                'name' => $ad->name . ' Ad',
                            ],
                        ],
                        'quantity'   => 1,
                    ]],
                    'mode'                 => 'payment',
                    'success_url'          => route('payment.success', [
                        'ad_details_id' => $adDetails->id,
                        'smart_ad_id'   => $smartAdId,
                    ]),
                    'cancel_url'           => route('payment.cancel'),
                    'metadata'             => [
                        'user_id'       => Auth::id(),
                        'smart_ad_id'   => $ad->id,
                        'ad_details_id' => $adDetails->id,
                    ],
                ]);

                return response()->json([
                    'error'   => false,
                    'message' => 'Stripe checkout URL generated successfully',
                    'data'    => [
                        'checkout_url' => $session->url,
                    ],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Stripe Error: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    // create a payment setting api and get all payment settings
    public function getPaymentSettings(Request $request)
    {
        $data = [];

        $stripe         = PaymentSetting::where('gateway', 'stripe')->first();
        $data['stripe'] = ($stripe && $stripe->status) ? [
            'gateway'            => 'stripe',
            'currency'           => $stripe->currency,
            'currency_symbol'    => $stripe->currency_symbol,
            'status'             => true,
            'secret_key'         => $stripe->stripe_secret,
            'publishable_key'    => $stripe->stripe_publishable,
            'webhook_secret_key' => $stripe->stripe_webhook_secret,
            'webhook_url'        => $stripe->stripe_webhook_url,
        ] : [
            'gateway'            => '',
            'currency'           => '',
            'currency_symbol'    => '',
            'status'             => false,
            'secret_key'         => '',
            'publishable_key'    => '',
            'webhook_secret_key' => '',
            'webhook_url'        => '',
        ];

        $razorpay         = PaymentSetting::where('gateway', 'razorpay')->first();
        $data['razorpay'] = ($razorpay && $razorpay->status) ? [
            'gateway'            => 'razorpay',
            'currency'           => $razorpay->currency,
            'currency_symbol'    => $razorpay->currency_symbol,
            'status'             => true,
            'secret_key'         => $razorpay->razorpay_secret,
            'publishable_key'    => $razorpay->razorpay_key,
            'webhook_secret_key' => $razorpay->razorpay_webhook_secret,
            'webhook_url'        => $razorpay->razorpay_webhook_url,
        ] : [
            'gateway'            => '',
            'currency'           => '',
            'currency_symbol'    => '',
            'status'             => false,
            'secret_key'         => '',
            'publishable_key'    => '',
            'webhook_secret_key' => '',
            'webhook_url'        => '',
        ];

        $applepay         = PaymentSetting::where('gateway', 'applepay')->first();
        $data['applepay'] = ($applepay && $applepay->status) ? [
            'gateway'         => 'applepay',
            'currency'        => $applepay->currency,
            'currency_symbol' => $applepay->currency_symbol,
            'status'          => true,
        ] : [
            'status' => false,
        ];

        return response()->json([
            'error'   => false,
            'message' => 'Payment settings retrieved successfully',
            'data'    => $data,
        ]);
    }

    // // create transaction history api
    public function transaction_history(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'error'   => true,
                'data'    => null,
                'message' => 'User not authenticated',
            ], 401);
        }

        // Get membership transactions
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get ads transactions
        $ads_transactions = SmartAdsPayment::select(
            'smart_ads_payments.user_id',
            'smart_ads_payments.paid_at',
            'smart_ads_payments.payment_gateway',
            'smart_ads_payments.amount',
            'smart_ads_details.payment_status',
            'smart_ads_details.start_date',
            'smart_ads_details.end_date',
            'smart_ads.name'
        )
            ->join('smart_ads', 'smart_ads.id', '=', 'smart_ads_payments.smart_ad_id')
            ->join('smart_ads_details', 'smart_ads_details.smart_ad_id', '=', 'smart_ads.id')
            ->where('smart_ads_payments.user_id', $user->id)
            ->orderBy('smart_ads_payments.created_at', 'desc')
            ->get();

        $credit_transactions = DB::table('apple_credit_transactions')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                return (array) $t; // cast to array
            });

        // Merge both with type
        $allTransactions = collect()
            ->merge($transactions->map(function ($t) {
                $arr         = $t->toArray();
                $arr['type'] = 'membership';
                return $arr;
            }))
            ->merge($ads_transactions->map(function ($t) {
                $arr         = $t->toArray();
                $arr['type'] = 'ads';
                return $arr;
            }))
            ->merge($credit_transactions->map(function ($t) {
                $arr         = $t->toArray();
                $arr['type'] = 'credit';
                return $arr;
            }))
            ->sortByDesc('created_at')
            ->values();

        if ($request->has('type')) {
            $filterType      = $request->get('type');
            $allTransactions = $allTransactions->where('type', $filterType)->values();
        }

        return response()->json([
            'error'   => false,
            'data'    => $allTransactions,
            'message' => 'Transaction history retrieved successfully',
        ]);
    }

    public function generateRazorpaySignature(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'amount'        => 'required|numeric|min:1',
                'plan_id'       => 'nullable|exists:plans,id',
                'tenure_id'     => 'nullable|exists:plan_tenures,id',
                'ad_details_id' => 'nullable|exists:smart_ads_details,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $setting = PaymentSetting::where('gateway', 'razorpay')->first();
            // Initialize Razorpay API
            $razorpayKey    = $setting->razorpay_key;
            $razorpaySecret = $setting->razorpay_secret;
            if (empty($razorpayKey) || empty($razorpaySecret)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Razorpay credentials are not configured',
                ], 500);
            }

            $api = new Api($razorpayKey, $razorpaySecret);

            // Create Razorpay order
            $attributes = [
                'amount'   => $request->amount * 100, // Convert to paise
                'currency' => $setting->currency ?? 'INR',
                'receipt'  => 'rcpt_' . uniqid(),
            ];

            $order = $api->order->create($attributes);

            $signaturePayload   = $order->id . '|' . ($request->amount * 100);
            $generatedSignature = hash_hmac('sha256', $signaturePayload, $razorpaySecret);
            $generatedSignature = hash_hmac('sha256', $order->id . "|" . $razorpayKey, $razorpaySecret);

            if ($request->ad_details_id) {
                // Fetch Razorpay settings
                if (! $setting) {
                    return response()->json([
                        'status'  => 'success',
                        'data'    => [
                            'key_id'        => '',
                            'order_id'      => '',
                            'amount'        => '',
                            'currency'      => '',
                            'ad_details_id' => '',
                        ],
                        'message' => 'Razorpay is not enabled, returning empty signature data',
                    ], 200);
                }

                // Fetch ad details
                $adDetails = SmartAdsDetail::find($request->ad_details_id);
                if (! $adDetails) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid ad details ID',
                    ], 400);
                }

                // Verify ad exists
                $ad = SmartAd::find($adDetails->smart_ad_id);
                if (! $ad) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Ad not found',
                    ], 400);
                }

                // Verify amount matches
                if ($request->amount != $adDetails->total_price) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Amount does not match ad total price',
                    ], 400);
                }

                // Prepare signature data
                $signatureData = [
                    'key_id'         => $razorpayKey,
                    'order_id'       => $order->id,
                    'payment_id'     => $request->razorpay_payment_id,
                    'amount'         => $request->amount,
                    'currency'       => $setting->currency ?? 'USD',
                    'ad_details_id'  => $request->ad_details_id,
                    'signature'      => $generatedSignature,
                    'transaction_id' => null,
                ];

                // Store payment details
                $payment = SmartAdsPayment::create([
                    'smart_ad_id'     => $ad->id,
                    'user_id'         => Auth::id(),
                    'order_id'        => $order->id,
                    'amount'          => $request->amount,
                    'currency'        => $setting->currency ?? 'USD',
                    'payment_gateway' => 'razorpay',
                    'status'          => 'pending',
                ]);

                return response()->json([
                    'status'  => 'success',
                    'data'    => $signatureData,
                    'message' => 'Payment signature generated successfully. Payment ID will be available after payment completion.',
                ], 200);

            } elseif ($request->plan_id && $request->tenure_id) {

                // Check if Razorpay is inactive or not configured
                if (! $setting || ! $setting->status) {
                    return response()->json([
                        'status'  => 'success',
                        'data'    => [
                            'key_id'    => '',
                            'order_id'  => '',
                            'amount'    => '',
                            'currency'  => '',
                            'plan_id'   => '',
                            'tenure_id' => '',
                        ],
                        'message' => 'Razorpay is not enabled, returning empty signature data',
                    ], 200);
                }

                // Verify plan and tenure
                $plan = Plan::with(['planTenures'])->find($request->plan_id);
                if (! $plan) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid plan ID',
                    ], 400);
                }

                $selectedTenure = $plan->planTenures->where('id', $request->tenure_id)->first();
                if (! $selectedTenure) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid tenure ID',
                    ], 400);
                }

                // Prepare signature data
                $signatureData = [
                    'key_id'         => $razorpayKey,
                    'order_id'       => $order->id,
                    'amount'         => $request->amount,
                    'currency'       => $setting->currency ?? 'INR',
                    'plan_id'        => $request->plan_id,
                    'tenure_id'      => $request->tenure_id,
                    'signature'      => $generatedSignature,
                    'transaction_id' => null, // Payment ID is not available at order creation
                ];

                // Store the order details in the Transaction model
                // $transaction = Transaction::create([
                //     'user_id'            => Auth::id(),
                //     'order_id'           => $order->id,
                //     'transaction_id'     => null,
                //     'payment_gateway'    => 'razorpay',
                //     'amount'             => $request->amount,
                //     'currency'           => $setting->currency ?? 'INR',
                //     'discount'           => session('discount', 0),
                //     'plan_tenure_id'     => $request->tenure_id,
                //     'signature_response' => $signatureData,
                //     'status'             => 'pending',
                // ]);

                return response()->json([
                    'status'  => 'success',
                    'data'    => $signatureData,
                    'message' => 'Payment signature generated successfully. Payment ID will be available after payment completion.',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Razorpay signature generation failed: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to generate signature: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'razorpay_payment_id' => 'required',
                'razorpay_order_id'   => 'required',
                'razorpay_signature'  => 'required',
                'tenure_id'           => 'nullable|exists:plan_tenures,id',
                'ad_details_id'       => 'nullable|exists:smart_ads_details,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Get Razorpay settings
            $setting = PaymentSetting::where('gateway', 'razorpay')
                ->where('status', true)
                ->first();

            if (! $setting) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Razorpay is not enabled',
                ], 403);
            }

            // Verify signature
            $data               = $request->razorpay_order_id . '|' . $request->razorpay_payment_id;
            $generatedSignature = hash_hmac('sha256', $request->razorpay_order_id . "|" . $request->razorpay_payment_id, $setting->razorpay_secret);
            if (hash_equals($generatedSignature, $request['razorpay_signature'])) {
                if ($generated_signature !== $request->razorpay_signature) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid payment signature',
                    ], 400);
                }
            }

            $api = new Api($setting->razorpay_key, $setting->razorpay_secret);
            try {
                $payment = $api->payment->fetch($request->razorpay_payment_id);
                // Capture only if authorized
                if ($payment->status === 'authorized' && $payment->captured == false) {
                    try {
                        $payment->capture(['amount' => $payment->amount]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Payment capture failed: ' . $e->getMessage(),
                        ], 500);
                    }
                }

            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to fetch payment: ' . $e->getMessage(),
                ], 500);
            }

            if ($request->tenure_id) {
                // Get plan and tenure details
                $plan = Plan::with(['features', 'planTenures', 'subscriptions'])
                    ->findOrFail($request->plan_id);

                if ($plan->features->isEmpty()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'No features available for this plan',
                    ], 400);
                }

                // Get the selected tenure
                $selectedTenure = $plan->planTenures->where('id', $request->tenure_id)->first();
                if (! $selectedTenure) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid tenure ID',
                    ], 400);
                }
                // Check if transaction_id already exists
                $existingTransaction = Transaction::where('transaction_id', $request->razorpay_payment_id)->first();

                if ($existingTransaction) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Transaction already exists',
                    ], 400);
                }

                // Calculate subscription dates
                $start_date  = now();
                $duration    = $selectedTenure->duration;
                $tenure_name = strtolower($selectedTenure->name);

                $end_date = str_contains($tenure_name, 'month')
                    ? now()->addMonths($duration)
                    : now()->addMonths($duration);

                // Format plan details with only the selected tenure
                $plan_details = [
                    'plan'     => [
                        'plan_id'          => $plan->id,
                        'plan_name'        => $plan->name,
                        'plan_description' => $plan->description,
                        'plan_slug'        => $plan->slug,
                        'plan_status'      => $plan->status,
                    ],
                    'features' => $plan->features->map(function ($feature) {
                        return [
                            'feature_id'                       => $feature->id,
                            'plan_id'                          => $feature->plan_id,
                            'is_ads_free'                      => $feature->is_ads_free,
                            'number_of_articles'               => $feature->number_of_articles,
                            'number_of_stories'                => $feature->number_of_stories,
                            'number_of_e_papers_and_magazines' => $feature->number_of_e_papers_and_magazines,
                        ];
                    }),
                    'tenures'  => [ // Only include the selected tenure as a single item array
                        [
                            'tenure_id'      => $selectedTenure->id,
                            'plan_id'        => $selectedTenure->plan_id,
                            'tenure_name'    => $selectedTenure->name,
                            'duration'       => $selectedTenure->duration,
                            'price'          => $selectedTenure->price,
                            'discount_price' => $selectedTenure->discount_price,
                            'start_date'     => $selectedTenure->start_date,
                            'end_date'       => $selectedTenure->end_date,
                        ],
                    ],

                ];

                // Process the verified payment in a database transaction
                $transaction = DB::transaction(function () use ($request, $plan, $selectedTenure, $start_date, $end_date, $plan_details) {
                    $userId     = Auth::id();
                    $feature_id = $plan->features->first()->id;

                    // Create transaction
                    $transaction = Transaction::create([
                        'user_id'         => $userId,
                        'order_id'        => $request->razorpay_order_id,
                        'transaction_id'  => $request->razorpay_payment_id,
                        'payment_gateway' => 'razorpay',
                        'amount'          => $request->amount,
                        'discount'        => session('discount', 0),
                        'plan_details'    => $plan_details,
                        'start_date'      => $start_date,
                        'end_date'        => $end_date,
                        'status'          => 'success',
                    ]);

                    // Create subscription
                    Subscription::create([
                        'user_id'        => $userId,
                        'plan_id'        => $plan->id,
                        'feature_id'     => $feature_id,
                        'plan_tenure_id' => $selectedTenure->id,
                        'transaction_id' => $transaction->id,
                        'duration'       => $selectedTenure->duration,
                        'start_date'     => $start_date,
                        'end_date'       => $end_date,
                        'status'         => 'active',
                    ]);

                    return $transaction;
                });

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Payment verified and subscription created successfully',
                    'error'   => false,
                    'data'    => [
                        'transaction_id'      => $transaction->id,
                        'payment_id'          => $request->razorpay_payment_id,
                        'subscription_period' => [
                            'start_date' => $start_date->format('Y-m-d H:i:s'),
                            'end_date'   => $end_date->format('Y-m-d H:i:s'),
                        ],
                    ],
                ]);
            } elseif ($request->ad_details_id) {
                // Fetch ad details
                $adDetails = SmartAdsDetail::findOrFail($request->ad_details_id);
                if (! $adDetails) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Invalid ad details ID',
                    ], 400);
                }

                // Verify ad exists
                $ad = SmartAd::find($adDetails->smart_ad_id);
                if (! $ad) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Ad not found',
                    ], 400);
                }

                // Check if transaction already exists
                $existingPayment = SmartAdsPayment::where('transaction_id', $request->razorpay_payment_id)->first();
                if ($existingPayment) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Transaction already exists',
                    ], 400);
                }

                // Process the verified payment in a database transaction
                $payment = DB::transaction(function () use ($request, $ad, $adDetails) {
                    // Update or create payment record
                    $payment = SmartAdsPayment::where('order_id', $request->razorpay_order_id)->first();

                    $transaction_details = [
                        'transaction'     => [
                            'payment_id'     => $request->razorpay_payment_id,
                            'order_id'       => $request->razorpay_order_id,
                            'signature'      => $request->razorpay_signature,
                            'amount'         => $adDetails->total_price,
                            'transaction_id' => $request->razorpay_payment_id,
                        ],
                        'contact_details' => [
                            'contact_name'  => $adDetails->contact_name,
                            'contact_email' => $adDetails->contact_email,
                            'contact_phone' => $adDetails->contact_phone,
                        ],
                        'price_summary'   => [
                            'total_price'   => $adDetails->total_price,
                            'daily_price'   => $adDetails->daily_price,
                            'price_summary' => $adDetails->price_summary,
                        ],
                    ];

                    $payment = SmartAdsPayment::updateOrCreate(
                        ['order_id' => $request->razorpay_order_id],
                        [
                            'user_id'             => auth()->id() ?? $ad->user_id, // make sure correct user id is set
                            'smart_ad_id'         => $ad->id,
                            'transaction_id'      => $request->razorpay_payment_id,
                            'status'              => 'success',
                            'paid_at'             => now(),
                            'transaction_details' => $transaction_details,
                            'payment_gateway'     => 'razorpay',
                            'amount'              => $adDetails->total_price,
                            'currency'            => 'INR',
                        ]
                    );

                    // Update ad details payment status
                    $adDetails->update([
                        'payment_status' => 'success',
                        'start_date'     => now(),
                        'end_date'       => now()->addDays($adDetails->total_days),
                    ]);

                    return $payment;
                });

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Payment verified successfully',
                    'error'   => false,
                    'data'    => [
                        'transaction_id' => $payment->id,
                        'payment_id'     => $request->razorpay_payment_id,
                        'ad_period'      => [
                            'start_date' => $adDetails->start_date->format('Y-m-d H:i:s'),
                            'end_date'   => $adDetails->end_date->format('Y-m-d H:i:s'),
                        ],
                    ],
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to verify payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storyEpaperLimitCount(Request $request)
    {
        try {
            if ($request->method() !== 'POST') {
                return response()->json(['error' => true, 'message' => 'Method not allowed', 'data' => null], 405);
            }

            $user = Auth::user();
            if (! $user) {
                return response()->json(['error' => true, 'message' => 'User not authenticated', 'data' => null], 401);
            }
            // Fetch settings
            $settings        = Setting::all()->keyBy('name')->pluck('value', 'name')->all();
            $freeTrialStatus = (int) ($settings['free_trial_status'] ?? 0);
            $articleLimit    = (int) ($settings['free_trial_post_limit'] ?? 0);
            $storyLimit      = (int) ($settings['free_trial_story_limit'] ?? 0);
            $epaperLimit     = (int) ($settings['free_trial_e_papers_and_magazines_limit'] ?? 0);

            // Map each type to its request flag fields
            $typeConfig = [
                'article' => [
                    'read_count_flag' => 'article_read_count', // e.g. article_read_count=1
                    'plan_flag'       => 'plan_article_count', // e.g. plan_article_count=1
                    'sub_field'       => 'article_count',
                    'feature_field'   => 'number_of_articles',
                    'read_field'      => 'article_read_count',
                    'total_field'     => 'total_article_read_count',
                ],
                'story'   => [
                    'read_count_flag' => 'story_read_count', // e.g. story_read_count=1
                    'plan_flag'       => 'plan_story_count', // e.g. plan_story_count=1
                    'sub_field'       => 'story_count',
                    'feature_field'   => 'number_of_stories',
                    'read_field'      => 'story_read_count',
                    'total_field'     => 'total_story_read_count',
                ],
                'epaper'  => [
                    'read_count_flag' => 'epaper_read_count', // e.g. epaper_read_count=1
                    'plan_flag'       => 'plan_epaper_count', // e.g. plan_epaper_count=1
                    'sub_field'       => 'e_paper_count',
                    'feature_field'   => 'number_of_e_papers_and_magazines',
                    'read_field'      => 'epaper_read_count',
                    'total_field'     => 'total_epaper_read_count',
                ],
            ];

            // Only process types whose flags are actually present in the request
            $requestedTypes = array_filter(
                array_keys($typeConfig),
                fn($type) =>
                $request->has($typeConfig[$type]['read_count_flag']) ||
                $request->has($typeConfig[$type]['plan_flag'])
            );

            if (empty($requestedTypes)) {
                return response()->json(['error' => true, 'message' => 'No valid count fields provided in request.', 'data' => null], 422);
            }

            // Find active subscription
            $subscription = Subscription::with('feature')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now())
                ->first();

            $responseData = [
                'subscription'      => $subscription ? $subscription->toArray() : null,
                'is_ads_free'       => $subscription ? ($subscription->feature->is_ads_free ?? false) : false,
                'plan_status'       => $subscription && $subscription->status == 'active',
                'free_trial_status' => ($freeTrialStatus == 1),
            ];
            foreach ($requestedTypes as $type) {
                $cfg             = $typeConfig[$type];
                $requestPlanFlag = (int) $request->input($cfg['plan_flag'], 0);

                if ($subscription && $requestPlanFlag == 1) {
                    $features = $subscription->feature;
                    if ($features) {
                        $subField = $cfg['sub_field'];
                        $maxCount = $features->{$cfg['feature_field']} ?? 0;

                        if ($subscription->$subField <= $maxCount) {
                            $subscription->increment($subField);
                            $subscription->refresh();

                        }
                    }
                }
            }

            // Re-build response data with updated subscription counts and 0 for daily stats
            foreach ($typeConfig as $type => $cfg) {
                $subField = $cfg['sub_field'];
                $planUsed = $subscription ? ($subscription->$subField ?? 0) : 0;
                $planMax  = $subscription ? ($subscription->feature->{$cfg['feature_field']} ?? 0) : 0;

                // ✅ always shift by -1
                $displayUsed = max(0, $planUsed - 1);

                // ✅ when OVER limit → jump to max + 1
                if ($subscription && $planUsed > $planMax) {
                    $displayUsed = $planMax + 1;
                }

                $responseData[$type] = [
                    'plan_' . $type . '_count'      => $displayUsed,
                    'plan_total_max_' . $type . 's' => $planMax,
                ];
            }

            return response()->json([
                'error'   => false,
                'message' => 'Count(s) updated successfully',
                'data'    => $responseData,
            ], 200);

            return response()->json([
                'error'   => false,
                'message' => 'Count(s) updated successfully',
                'data'    => $responseData,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Content count update failed: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Update failed: ' . $e->getMessage(), 'data' => null], 500);
        }
    }

    public function verifyAppleReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'nullable|string',
            'plan_id'        => 'nullable|exists:plans,id',
            'tenure_id'      => 'nullable|exists:plan_tenures,id',
            'user_id'        => 'nullable|exists:users,id',
            'amount'         => 'nullable|numeric|min:0',
            'ad_details_id'  => 'nullable|exists:smart_ads_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => true,
                'message' => $validator->errors()->first(),
                'data'    => (object) [],
            ], 422);
        }

        $appleSettings = DB::table('payment_settings')
            ->where('gateway', 'applepay')
            ->where('status', 1)
            ->first();

        if (! $appleSettings) {
            throw new \Exception('Apple Pay settings not found or inactive.');
        }

        if ($request->tenure_id && $request->plan_id) {
            try {
                // Generate JWT
                $privateKeyPath = storage_path('app/' . $appleSettings->apple_api_key_path);

                // Verify file exists
                if (! file_exists($privateKeyPath)) {
                    Log::error('Apple API key file not found at: ' . $privateKeyPath);
                    throw new \Exception('Apple API key file not found at: ' . $privateKeyPath);
                }

                // Read private key
                $privateKey = file_get_contents($privateKeyPath);
                if ($privateKey === false) {
                    Log::error('Failed to read Apple API key file at: ' . $privateKeyPath);
                    throw new \Exception('Failed to read Apple API key file');
                }

                // Build JWT payload
                $issuedAt   = time();
                $expiration = $issuedAt + (60 * 10); // 10 minutes
                $payload    = [
                    'iss' => $appleSettings->apple_issuer_id,
                    'iat' => $issuedAt,
                    'exp' => $expiration,
                    'aud' => 'appstoreconnect-v1',
                    'bid' => $appleSettings->apple_bundle_id,
                ];

                // Generate JWT
                try {
                    $jwt = JWT::encode($payload, $privateKey, 'ES256', $appleSettings->apple_key_id);
                    Log::debug('Generated JWT: ' . $jwt);
                } catch (\Exception $e) {
                    Log::error('JWT Encoding Failed: ' . $e->getMessage());
                    throw new \Exception('Failed to generate JWT: ' . $e->getMessage());
                }

                // Make cURL request to Apple StoreKit API
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

                // Handle cURL errors
                if ($result === false || $curlError) {
                    Log::error('cURL request failed: ' . $curlError);
                    throw new \Exception('Failed to communicate with Apple API: ' . $curlError);
                }

                // Log raw response for debugging
                Log::debug('Apple API Response', ['response' => $result, 'http_code' => $httpCode]);

                // Handle HTTP status codes
                if ($httpCode === 404) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Transaction not found or invalid',
                        'data'    => (object) [],
                    ], 400);
                }

                if ($httpCode === 401) {
                    Log::error('Apple API authentication failed: Invalid JWT');
                    throw new \Exception('Authentication with Apple API failed');
                }

                if ($httpCode !== 200) {
                    Log::error('Apple API returned error: HTTP ' . $httpCode . ' - ' . $result);
                    throw new \Exception('Apple API returned error: HTTP ' . $httpCode);
                }

                $responseData = null;

                try {
                    $jwtParts = explode('.', trim($result, '"'));
                    if (count($jwtParts) !== 3) {
                        $responseData = json_decode($result, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            Log::error('Invalid response format from Apple API: ' . $result);
                            throw new \Exception('Invalid response format from Apple API');
                        }
                    } else {
                        $payload = json_decode(base64_decode(str_pad(strtr($jwtParts[1], '-_', '+/'), strlen($jwtParts[1]) % 4, '=', STR_PAD_RIGHT)), true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            Log::error('Failed to decode JWT payload from Apple API');
                            throw new \Exception('Failed to decode Apple API response');
                        }
                        $responseData = $payload;
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing Apple API response: ' . $e->getMessage());
                    throw new \Exception('Error processing Apple API response: ' . $e->getMessage());
                }

                // Log decoded response for debugging
                Log::debug('Decoded Apple Response', ['data' => $responseData]);

                // Validate bundle ID - check both possible field names
                $responseBundleId = $responseData['bundleId'] ?? $responseData['bid'] ?? null;
                $expectedBundleId = $appleSettings->apple_bundle_id;

                if (! $responseBundleId) {
                    Log::warning('No bundle ID found in Apple response', ['response' => $responseData]);
                    // Don't fail if bundle ID is missing, just log it
                } elseif ($responseBundleId !== $expectedBundleId) {
                    Log::error('Bundle ID mismatch', [
                        'expected'      => $expectedBundleId,
                        'received'      => $responseBundleId,
                        'full_response' => $responseData,
                    ]);
                    return response()->json([
                        'error'   => true,
                        'message' => 'Bundle ID mismatch',
                        'data'    => (object) [],
                    ], 400);
                }
                $plan           = Plan::with(['features', 'planTenures'])->find($request->plan_id);
                $selectedTenure = $plan->planTenures->where('id', $request->tenure_id)->first();
                // Extract transaction data with fallbacks
                $productId             = $selectedTenure->product_id;
                $transactionId         = $responseData['transactionId'] ?? $responseData['tid'] ?? $request->transaction_id;
                $originalTransactionId = $responseData['originalTransactionId'] ?? $responseData['otid'] ?? $transactionId;
                $environment           = $responseData['environment'] ?? $responseData['env'] ?? 'Production';
                $autoRenewStatus       = $responseData['autoRenewStatus'] ?? $responseData['ars'] ?? true;

                $user = auth()->user();

                if (! $user && $request->has('user_id')) {
                    $user = User::find($request->user_id);
                }

                if (! $user) {
                    Log::error('User not authenticated for Apple receipt verification', [
                        'has_auth_user'   => auth()->check(),
                        'has_user_id'     => $request->has('user_id'),
                        'request_user_id' => $request->user_id ?? null,
                    ]);
                    return response()->json([
                        'error'   => true,
                        'message' => 'User not authenticated',
                        'data'    => (object) [],
                    ], 401);
                }
                $plan = Plan::with(['features', 'planTenures'])->find($request->plan_id);
                if (! $plan) {
                    Log::error('Plan not found: ' . $request->plan_id);
                    return response()->json([
                        'error'   => true,
                        'message' => 'Plan not found',
                        'data'    => (object) [],
                    ], 404);
                }

                if ($plan->features->isEmpty()) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'No features available for this plan',
                        'data'    => (object) [],
                    ], 400);
                }

                // Get the selected tenure by tenure_id
                $selectedTenure = $plan->planTenures->where('id', $request->tenure_id)->first();
                if (! $selectedTenure) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Invalid tenure ID',
                        'data'    => (object) [],
                    ], 400);
                }

                // Start date and end date for subscription
                $start_date       = now();
                $tenure_name      = strtolower($selectedTenure->name);
                $duration         = $selectedTenure->duration;
                $end_date         = str_contains($tenure_name, 'month') ? now()->addMonths($duration) : now()->addMonths($duration);
                $subscriptionType = str_contains($tenure_name, 'month') ? 'monthly' : 'yearly';

                // Format plan details - only include the selected tenure
                $plan_details = [
                    'plan'     => [
                        'plan_id'          => $plan->id,
                        'plan_name'        => $plan->name,
                        'plan_description' => $plan->description,
                        'plan_slug'        => $plan->slug,
                        'plan_status'      => $plan->status,
                    ],
                    'features' => $plan->features->map(function ($feature) {
                        return [
                            'feature_id'                       => $feature->id,
                            'plan_id'                          => $feature->plan_id,
                            'is_ads_free'                      => $feature->is_ads_free,
                            'number_of_articles'               => $feature->number_of_articles,
                            'number_of_stories'                => $feature->number_of_stories,
                            'number_of_e_papers_and_magazines' => $feature->number_of_e_papers_and_magazines,
                        ];
                    }),
                    'tenures'  => [ // Only include the selected tenure as a single item array
                        [
                            'tenure_id'      => $selectedTenure->id,
                            'plan_id'        => $selectedTenure->plan_id,
                            'tenure_name'    => $selectedTenure->name,
                            'duration'       => $selectedTenure->duration,
                            'price'          => $selectedTenure->price,
                            'discount_price' => $selectedTenure->discount_price,
                            'start_date'     => $selectedTenure->start_date,
                            'end_date'       => $selectedTenure->end_date,
                        ],
                    ],
                ];

                // Check if transaction already exists
                $existingTransaction = Transaction::where('transaction_id', $originalTransactionId)
                    ->where('user_id', $user->id)
                    ->first();

                if ($existingTransaction) {
                    return response()->json([
                        'error'   => true,
                        'message' => 'Transaction already processed',
                        'data'    => (object) [],
                    ], 400);
                }

                // Store transaction and subscription
                DB::transaction(function () use ($user, $plan, $selectedTenure, $start_date, $end_date, $responseData, $transactionId, $originalTransactionId, $productId, $environment, $autoRenewStatus, $plan_details) {
                    $feature_id = $plan->features->first()->id;

                    $transaction = Transaction::create([
                        'user_id'            => $user->id,
                        'order_id'           => $transactionId,
                        'transaction_id'     => $originalTransactionId,
                        'payment_gateway'    => 'applepay',
                        'amount'             => $selectedTenure->price,
                        'discount'           => session('discount', 0),
                        'plan_details'       => $plan_details, // Added structured plan details
                        'plan_tenure_id'     => $selectedTenure->id,
                        'start_date'         => $start_date,
                        'end_date'           => $end_date,
                        'signature_response' => json_encode([
                            'productId'             => $productId,
                            'transactionId'         => $transactionId,
                            'originalTransactionId' => $originalTransactionId,
                            'environment'           => $environment,
                            'autoRenewStatus'       => $autoRenewStatus,
                            'full_response'         => $responseData,
                        ]),
                        'status'             => 'success',
                    ]);

                    Subscription::create([
                        'user_id'        => $user->id,
                        'plan_id'        => $plan->id,
                        'feature_id'     => $feature_id,
                        'plan_tenure_id' => $selectedTenure->id,
                        'transaction_id' => $transaction->id,
                        'duration'       => $selectedTenure->duration,
                        'start_date'     => $start_date,
                        'end_date'       => $end_date,
                        'status'         => 'active',
                    ]);
                });

                // Return success response
                return response()->json([
                    'error'   => false,
                    'message' => 'Subscription verified successfully',
                    'data'    => [
                        'subscription' => [
                            'is_active'               => true,
                            'product_id'              => $productId,
                            'expires_at'              => $end_date->toISOString(),
                            'expires_at_ms'           => $end_date->getTimestamp() * 1000,
                            'auto_renew_status'       => $autoRenewStatus,
                            'environment'             => $environment,
                            'subscription_type'       => $subscriptionType,
                            'is_trial'                => $responseData['isUpgraded'] ?? false,
                            'original_transaction_id' => $originalTransactionId,
                            'transaction_id'          => $transactionId,
                        ],
                    ],
                ]);

            } catch (\Exception $e) {
                Log::error('Apple Receipt Verification Failed: ' . $e->getMessage(), [
                    'transaction_id' => $request->transaction_id ?? null,
                    'trace'          => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'error'   => true,
                    'message' => 'Failed to verify subscription: ' . $e->getMessage(),
                    'data'    => (object) [],
                ], 500);
            }
        } elseif ($request->ad_details_id && $request->amount) {
            $user = auth()->user();
            if (! $user) {
                Log::error('User not authenticated for Smart Ads purchase');
                return response()->json([
                    'error'   => true,
                    'message' => 'User not authenticated',
                    'data'    => (object) [],
                ], 401);
            }

            $adDetails = SmartAdsDetail::find($request->ad_details_id);
            if (! $adDetails) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Invalid ad details ID',
                    'data'    => (object) [],
                ], 400);
            }

            // Verify ad exists
            $ad = SmartAd::find($adDetails->smart_ad_id);
            if (! $ad) {
                return response()->json([
                    'error'   => true,
                    'message' => 'Ad not found',
                    'data'    => (object) [],
                ], 400);
            }

            try {
                // Generate a unique transaction ID
                $transactionId         = \Illuminate\Support\Str::uuid()->toString();
                $originalTransactionId = $transactionId;

                // Process payment and update user credits in a database transaction
                return DB::transaction(function () use ($user, $ad, $adDetails, $transactionId, $originalTransactionId, $request) {
                    // Check user credits
                    $userCredit      = UserCredits::where('user_id', $user->id)->first();
                    $requiredCredits = $request->amount;

                    if (! $userCredit || $userCredit->available_credits < $requiredCredits) {
                        Log::error('Insufficient credits for Smart Ads purchase', [
                            'user_id'           => $user->id,
                            'available_credits' => $userCredit ? $userCredit->available_credits : 0,
                            'required_credits'  => $requiredCredits,
                        ]);
                        return response()->json([
                            'error'   => true,
                            'message' => 'Insufficient credits for this purchase',
                            'data'    => (object) [],
                        ], 400);
                    }

                    // Prepare transaction details
                    $transaction_details = [
                        'transaction'     => [
                            'transaction_id'          => $transactionId,
                            'original_transaction_id' => $originalTransactionId,
                            'environment'             => 'Production', // Default environment since no Apple API call
                        ],
                        'contact_details' => [
                            'contact_name'  => $adDetails->contact_name,
                            'contact_email' => $adDetails->contact_email,
                            'contact_phone' => $adDetails->contact_phone,
                        ],
                        'price_summary'   => [
                            'total_price'   => $request->amount,
                            'daily_price'   => $adDetails->daily_price,
                            'price_summary' => $adDetails->price_summary,
                        ],
                    ];

                    // Create payment record
                    $payment = SmartAdsPayment::create([
                        'user_id'             => $user->id,
                        'smart_ad_id'         => $ad->id,
                        'transaction_id'      => $originalTransactionId,
                        'status'              => 'success',
                        'paid_at'             => now(),
                        'transaction_details' => $transaction_details,
                        'payment_gateway'     => 'credits', // Reflects credit-based payment
                        'amount'              => $request->amount,
                        'currency'            => 'INR', // Adjust currency as needed
                    ]);

                    // Update ad details
                    $adDetails->update([
                        'payment_status' => 'success',
                        'start_date'     => now(),
                        'end_date'       => now()->addDays($adDetails->total_days),
                    ]);

                    // Update user credits
                    $userCredit->credits_consumed  += $requiredCredits;
                    $userCredit->available_credits -= $requiredCredits;
                    $userCredit->save();

                    // Return success response
                    return response()->json([
                        'error'   => false,
                        'message' => 'Smart Ads purchase completed successfully',
                        'data'    => [
                            'payment' => [
                                'transaction_id'          => $transactionId,
                                'original_transaction_id' => $originalTransactionId,
                                'amount'                  => $request->amount,
                                'currency'                => 'INR',
                                'status'                  => 'success',
                                'paid_at'                 => $payment->paid_at->toISOString(),
                                'ad_details'              => [
                                    'ad_details_id' => $adDetails->id,
                                    'smart_ad_id'   => $ad->id,
                                    'total_price'   => $request->amount,
                                    'daily_price'   => $adDetails->daily_price,
                                    'total_days'    => $adDetails->total_days,
                                    'start_date'    => $adDetails->start_date->toISOString(),
                                    'end_date'      => $adDetails->end_date->toISOString(),
                                    'contact_name'  => $adDetails->contact_name,
                                    'contact_email' => $adDetails->contact_email,
                                    'contact_phone' => $adDetails->contact_phone,
                                ],
                                'user_credits'            => [
                                    'total_credits'     => $userCredit->total_credits,
                                    'available_credits' => $userCredit->available_credits,
                                    'credits_consumed'  => $userCredit->credits_consumed,
                                ],
                            ],
                        ],
                    ]);
                });
            } catch (\Exception $e) {
                Log::error('Smart Ads Purchase Failed: ' . $e->getMessage(), [
                    'ad_details_id' => $request->ad_details_id ?? null,
                    'amount'        => $request->amount ?? null,
                    'user_id'       => $user->id,
                    'trace'         => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'error'   => true,
                    'message' => 'Failed to complete Smart Ads purchase: ' . $e->getMessage(),
                    'data'    => (object) [],
                ], 500);
            }
        }
    }
}
