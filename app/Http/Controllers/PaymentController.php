<?php
namespace App\Http\Controllers;

use App\Models\PaymentSetting;
use App\Models\Plan;
use App\Models\SmartAd;
use App\Models\SmartAdsDetail;
use App\Models\SmartAdsPayment;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{

    public function showForm(Request $request)
    {
        $theme           = getTheme();
        $title           = __('frontend-labels.payment_gateway.title');
        $payment_setting = PaymentSetting::where('status', true)->get()->keyBy('gateway');

        // Membership values
        $tenure_id = $request->input('tenure_id', null);
        $plan      = $request->input('plan', null);

        // Smart Ad values
        $smart_ad_id = $request->input('smart_ad_id', null);
        $amount      = $request->input('amount', null);

        return view("front_end.{$theme}.pages.payment", compact(
            'theme',
            'title',
            'payment_setting',
            'tenure_id',
            'plan',
            'smart_ad_id',
            'amount'
        ));
    }

    public function createStripeSession(Request $request)
    {
        $setting = PaymentSetting::where('gateway', 'stripe')->where('status', true)->first();
        if (! $setting) {
            return redirect()->route('payment.cancel')->with('error', 'Stripe is not enabled.');
        }

        // Webhook check
        if (empty($setting->stripe_webhook_secret)) {
            return redirect()->route('payment.cancel')->with('error', 'Stripe webhook is not configured. Please contact support.');
        }

        $currency   = strtolower($setting->currency ?? 'inr');
        $amount     = floatval($request->amount);
        $unitAmount = intval(round($amount * 100));

        $minUnitAmount = match ($currency) {
            'inr'   => 50,
            'usd'   => 50,
            'amd'   => 130,
            default => 50
        };

        if ($unitAmount < $minUnitAmount) {
            return redirect()->route('payment.cancel')->with('error', "Minimum amount required for $currency is $minUnitAmount units.");
        }

        Stripe::setApiKey($setting->stripe_secret);
        if ($request->tenure_id) {
            try {
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => [[
                        'price_data' => [
                            'currency'     => $currency,
                            'unit_amount'  => $unitAmount,
                            'product_data' => [
                                'name' => $request->plan . ' Plan',
                            ],
                        ],
                        'quantity'   => 1,
                    ]],
                    'mode'                 => 'payment',
                    'success_url'          => route('payment.success', [
                        'user_id'   => Auth::id(),
                        'tenure_id' => $request->tenure_id,
                    ]),
                    'cancel_url'           => route('payment.cancel'),
                    'metadata'             => [
                        'user_id'   => Auth::id(),
                        'tenure_id' => $request->tenure_id,
                    ],
                ]);

                return redirect($session->url);
            } catch (\Exception $e) {
                return redirect()->route('payment.cancel')->with('error', 'Stripe Error: ' . $e->getMessage());
            }
        } elseif ($request->smart_ad_id) {
            try {
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => [[
                        'price_data' => [
                            'currency'     => $currency,
                            'unit_amount'  => $unitAmount,
                            'product_data' => [
                                'name' => ' Sponsor Ads Plan',
                            ],
                        ],
                        'quantity'   => 1,
                    ]],
                    'mode'                 => 'payment',
                    'success_url'          => route('payment.success', [
                        'ad_details_id' => $request->ad_details_id,
                        'smart_ad_id'   => $request->smart_ad_id,
                    ]),
                    'cancel_url'           => route('payment.cancel'),
                    'metadata'             => [
                        'user_id'       => Auth::id(),
                        'smart_ad_id'   => $request->smart_ad_id ?? null,
                        'ad_details_id' => $request->ad_details_id ?? null,

                    ],
                ]);

                return redirect($session->url);
            } catch (\Exception $e) {
                return redirect()->route('payment.cancel')->with('error', 'Stripe Error: ' . $e->getMessage());
            }
        }
    }

    public function stripeWebhook(Request $request)
    {
        $setting = PaymentSetting::where('gateway', 'stripe')->where('status', true)->first();
        if (! $setting) {
            Log::error('Stripe settings not found');
            return response()->json(['status' => 'error', 'message' => 'Stripe settings not found'], 400);
        }

        Stripe::setApiKey($setting->stripe_secret);

        $payload    = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        // Log for debugging
        Log::info('Stripe Webhook Received', [
            'payload'        => $payload,
            'signature'      => $sig_header,
            'webhook_secret' => $setting->stripe_webhook_secret,
        ]);

        if (empty($sig_header)) {
            Log::error('Stripe Webhook: Missing Stripe-Signature header');
            return response()->json(['status' => 'error', 'message' => 'Missing signature header'], 400);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $setting->stripe_webhook_secret
            );

            if ($event->type === 'checkout.session.completed') {
                $session     = $event->data->object;
                $user_id     = $session->metadata->user_id ?? null;
                $tenure_id   = $session->metadata->tenure_id ?? null;
                $smart_ad_id = $session->metadata->smart_ad_id ?? null;

                // -------------------------
                // CASE 1: Membership Plan
                // -------------------------
                if ($tenure_id) {
                    // Validate metadata
                    if (! $user_id || ! $tenure_id) {
                        Log::error('Stripe Webhook: Missing user_id or tenure_id in metadata', [
                            'user_id'   => $user_id,
                            'tenure_id' => $tenure_id,
                        ]);
                        return response()->json(['status' => 'error', 'message' => 'Invalid metadata'], 400);
                    }

                    // Fetch plan
                    $plan = Plan::with(['features', 'planTenures'])->find($tenure_id);
                    if (! $plan || $plan->features->isEmpty() || $plan->planTenures->isEmpty()) {
                        Log::error("Stripe Webhook: Invalid plan or missing features/tenures for plan_id: $tenure_id");
                        return response()->json(['status' => 'error', 'message' => 'Invalid plan'], 400);
                    }

                    // Calculate subscription dates
                    $start_date  = now();
                    $duration    = $plan->planTenures->first()->duration;
                    $tenure_name = strtolower($plan->planTenures->first()->name);
                    $end_date    = str_contains($tenure_name, 'month')
                        ? now()->addMonths($duration)
                        : now()->addMonths($duration);

                    // Prepare plan details
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
                        })->toArray(),
                        'tenures'  => $plan->planTenures->map(function ($tenure) {
                            return [
                                'tenure_id'      => $tenure->id,
                                'plan_id'        => $tenure->plan_id,
                                'tenure_name'    => $tenure->name,
                                'duration'       => $tenure->duration,
                                'price'          => $tenure->price,
                                'discount_price' => $tenure->discount_price,
                            ];
                        })->toArray(),
                    ];

                    // Prevent duplicate transactions
                    if (Transaction::where('transaction_id', $session->payment_intent)->exists()) {
                        Log::info('Stripe Webhook: Transaction already processed', ['payment_intent' => $session->payment_intent]);
                        return response()->json(['status' => 'success', 'message' => 'Transaction already processed']);
                    }

                    // Create transaction and subscription
                    DB::transaction(function () use ($session, $user_id, $plan, $start_date, $end_date, $plan_details) {
                        $transaction = Transaction::create([
                            'user_id'         => $user_id,
                            'transaction_id'  => $session->payment_intent,
                            'payment_gateway' => 'stripe',
                            'amount'          => $session->amount_total / 100,
                            'discount'        => 0, // Replace with actual discount logic if available
                            'plan_details'    => $plan_details,
                            'start_date'      => $start_date,
                            'end_date'        => $end_date,
                            'status'          => 'success',
                        ]);

                        Subscription::create([
                            'user_id'        => $user_id,
                            'plan_id'        => $plan->id,
                            'plan_tenure_id' => $plan->planTenures->first()->id,
                            'feature_id'     => $plan->features->first()->id,
                            'transaction_id' => $transaction->id,
                            'duration'       => $plan->planTenures->first()->duration,
                            'start_date'     => $start_date,
                            'end_date'       => $end_date,
                            'status'         => 'active',
                        ]);
                    });

                    Log::info('Stripe Webhook: Payment processed successfully', [
                        'user_id'        => $user_id,
                        'plan_id'        => $plan_id,
                        'payment_intent' => $session->payment_intent,
                    ]);
                    return response()->json(['status' => 'success']);
                } elseif ($smart_ad_id) {

                    // Validate metadata
                    if (! $user_id || ! $smart_ad_id) {
                        Log::error('Stripe Webhook: Missing user_id or smart_ad_id in metadata', [
                            'user_id'     => $user_id,
                            'smart_ad_id' => $smart_ad_id,
                        ]);
                        return response()->json(['status' => 'error', 'message' => 'Invalid metadata'], 400);
                    }

                    // Prevent duplicate
                    if (SmartAdsPayment::where('transaction_id', $session->payment_intent)->exists()) {
                        Log::info('Stripe Webhook: Smart Ad Transaction already processed', [
                            'payment_intent' => $session->payment_intent,
                        ]);
                        return response()->json(['status' => 'success', 'message' => 'Smart Ad already processed']);
                    }

                    $adDetails = SmartAdsDetail::find($session->metadata->ad_details_id ?? null);
                    if (! $adDetails) {
                        Log::error("Stripe Webhook: Invalid ad_details_id for smart_ad_id: $smart_ad_id");
                        return response()->json(['status' => 'error', 'message' => 'Invalid ad details'], 400);
                    }

                    $ad = SmartAd::find($smart_ad_id);
                    if (! $ad) {
                        Log::error("Stripe Webhook: Smart Ad not found for id: $smart_ad_id");
                        return response()->json(['status' => 'error', 'message' => 'Smart Ad not found'], 400);
                    }
                    // Transaction details structure
                    $transaction_details = [
                        'transaction'     => [
                            'payment_id'     => $session->payment_intent,
                            'transaction_id' => $session->payment_intent,
                            'amount'         => $session->amount_total / 100,
                            'currency'       => strtoupper($session->currency),
                            'status'         => $session->payment_status,
                            'payment_method' => $session->payment_method_types[0] ?? 'stripe',
                            'receipt_email'  => $session->customer_email ?? $adDetails->contact_email,
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

                    // Save Payment + Update Ad details
                    DB::transaction(function () use ($session, $user_id, $ad, $adDetails, $transaction_details) {
                        SmartAdsPayment::create([
                            'user_id'             => $user_id,
                            'smart_ad_id'         => $ad->id,
                            'transaction_id'      => $session->payment_intent,
                            'status'              => 'success',
                            'paid_at'             => now(),
                            'transaction_details' => $transaction_details,
                            'payment_gateway'     => 'stripe',
                            'amount'              => $session->amount_total / 100,
                            'currency'            => strtoupper($session->currency),
                        ]);

                        $adDetails->update([
                            'payment_status' => 'success',
                            'start_date'     => now(),
                            'end_date'       => now()->addDays($adDetails->total_days),
                        ]);
                    });

                    Log::info('Stripe Webhook: Smart Ad Payment processed successfully', [
                        'user_id'        => $user_id,
                        'smart_ad_id'    => $smart_ad_id,
                        'payment_intent' => $session->payment_intent,
                    ]);
                    return response()->json(['status' => 'success']);
                }
            }

            Log::info('Stripe Webhook: Event type ignored', ['event_type' => $event->type]);
            return response()->json(['status' => 'ignored', 'message' => 'Event type not handled']);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook Signature Verification Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Webhook error: ' . $e->getMessage()], 400);
        }
    }

    public function createRazorpayOrder(Request $request)
    {
        $setting = PaymentSetting::where('gateway', 'razorpay')->where('status', true)->first();

        $api    = new Api($setting->razorpay_key, $setting->razorpay_secret);
        $amount = $request->amount * 100;

        $orderData = [
            'receipt'  => 'rcptid_' . time(),
            'amount'   => $amount,
            'currency' => $setting->currency,
        ];

        $razorpayOrder = $api->order->create($orderData);

        return response()->json([
            'order_id' => $razorpayOrder->id,
            'amount'   => $razorpayOrder->amount,
            'currency' => $razorpayOrder->currency,
        ]);
    }

    public function razorpayProcess(Request $request)
    {
        // Get Razorpay settings
        $setting = PaymentSetting::where('gateway', 'razorpay')->where('status', true)->first();
        if (! $setting) {
            abort(403, 'Razorpay not enabled');
        }

        // Common inputs
        $amount        = $request->input('amount');
        $plan          = $request->input('plan');
        $plan_id       = $request->input('plan_id');
        $smart_ad_id   = $request->input('smart_ad_id');
        $ad_details_id = $request->input('ad_details_id');
        $tenure_id     = $request->input('tenure_id', null);
        $theme         = getTheme();

        // If plan_id is missing, try to get it from the plan name
        if (! $plan_id && $plan) {
            $planModel = Plan::where('name', $plan)->first();
            if ($planModel) {
                $plan_id = $planModel->id;
            } else {
                return redirect()->back()->with('error', 'Invalid plan name.');
            }
        }

        $title = __('frontend-labels.payment_with_razorpay.title');
        $data  = [
            'theme'         => $theme,
            'title'         => $title,
            'amount'        => $amount,
            'plan'          => $plan,
            'plan_id'       => $plan_id,
            'tenure_id'     => $tenure_id,
            'smart_ad_id'   => $smart_ad_id,
            'ad_details_id' => $ad_details_id,
            'setting'       => $setting,
        ];

        return view("front_end.{$theme}.pages.razorpay_checkout", $data);
    }

    public function razorpayCallback(Request $request)
    {
        $data = $request->all();

        if (empty($data['razorpay_payment_id'])) {
            return response()->json(['status' => 'error', 'message' => 'Payment ID missing'], 400);
        }

        // Get Razorpay settings
        $setting = PaymentSetting::where('gateway', 'razorpay')->where('status', true)->first();

        if (! $setting) {
            return response()->json(['status' => 'error', 'message' => 'Razorpay not configured'], 500);
        }

        $api = new Api($setting->razorpay_key, $setting->razorpay_secret);

        // ✅ STEP 1: VERIFY PAYMENT SIGNATURE (SECURITY)
        if (! empty($data['razorpay_order_id']) && ! empty($data['razorpay_signature'])) {
            try {
                $attributes = [
                    'razorpay_order_id'   => $data['razorpay_order_id'],
                    'razorpay_payment_id' => $data['razorpay_payment_id'],
                    'razorpay_signature'  => $data['razorpay_signature'],
                ];

                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $e) {
                Log::error('Razorpay signature verification failed', [
                    'error' => $e->getMessage(),
                    'data'  => $data,
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Payment verification failed',
                ], 400);
            }
        }

        // ✅ STEP 2: FETCH PAYMENT AND CHECK STATUS
        try {
            $payment = $api->payment->fetch($data['razorpay_payment_id']);

            Log::info('Payment Status', [
                'payment_id' => $payment->id,
                'status'     => $payment->status,
                'captured'   => $payment->captured,
            ]);

        try {
            // Fetch latest payment from Razorpay
            $payment = $api->payment->fetch($payment->id);

            // Only capture if authorized
            if ($payment->status === 'authorized') {

                $payment->capture(['amount' => $payment->amount]);

                Log::info('Payment captured successfully', [
                    'payment_id' => $payment->id
                ]);

            } elseif ($payment->status === 'captured') {

                Log::info('Payment already captured', [
                    'payment_id' => $payment->id
                ]);

                // treat as success instead of error
            } else {

                Log::warning('Payment not in capturable state', [
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not authorized for capture',
                ], 400);
            }

        } catch (\Exception $e) {

            Log::error('Payment capture failed', [
                'payment_id' => $payment->id ?? null,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Payment capture failed: ' . $e->getMessage(),
            ], 500);
        }

        } catch (\Exception $e) {
            Log::error('Failed to fetch payment', [
                'payment_id' => $data['razorpay_payment_id'],
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to verify payment',
            ], 500);
        }

        // ✅ STEP 4: PROCESS SUBSCRIPTION OR SMART ADS
        if ($request->plan_id) {
            // PLAN SUBSCRIPTION LOGIC
            if (empty($data['plan_id'])) {
                return response()->json(['status' => 'error', 'message' => 'Plan ID is missing'], 400);
            }

            $plan = Plan::with(['features', 'planTenures', 'subscriptions'])->find($data['plan_id']);
            if (! $plan) {
                return response()->json(['status' => 'error', 'message' => 'Invalid plan ID'], 400);
            }

            if ($plan->features->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'No features available for this plan'], 400);
            }

            $selectedTenure = $plan->planTenures->where('id', $data['tenure_id'])->first();
            if (! $selectedTenure) {
                return response()->json(['status' => 'error', 'message' => 'Invalid tenure ID'], 400);
            }

            $start_date  = now();
            $duration    = $selectedTenure->duration;
            $tenure_name = strtolower($selectedTenure->name);

            $end_date = str_contains($tenure_name, 'month')
                ? now()->addMonths($duration)
                : now()->addMonths($duration);

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
                'tenures'  => [
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

            try {
                $transaction = DB::transaction(function () use ($data, $plan, $selectedTenure, $start_date, $end_date, $plan_details) {
                    $userId     = Auth::id();
                    $feature_id = $plan->features->first()->id;
                    $duration   = $selectedTenure->duration;

                    $subscriptionData = [
                        'user_id'        => $userId,
                        'plan_id'        => $plan->id,
                        'feature_id'     => $feature_id,
                        'plan_tenure_id' => $selectedTenure->id,
                        'duration'       => $duration,
                        'start_date'     => $start_date,
                        'end_date'       => $end_date,
                        'status'         => 'active',
                    ];

                    $transaction = Transaction::create([
                        'user_id'         => $userId,
                        'transaction_id'  => $data['razorpay_payment_id'],
                        'payment_gateway' => 'razorpay',
                        'amount'          => $data['amount'],
                        'discount'        => session('discount', 0),
                        'plan_details'    => $plan_details,
                        'start_date'      => $start_date,
                        'end_date'        => $end_date,
                        'status'          => 'success',
                    ]);

                    $subscriptionData['transaction_id'] = $transaction->id;
                    Subscription::create($subscriptionData);

                    return $transaction;
                });

                return response()->json([
                    'status'              => 'success',
                    'transaction_id'      => $transaction->id,
                    'message'             => 'Transaction and subscription created successfully',
                    'subscription_period' => [
                        'start_date' => $start_date->format('Y-m-d H:i:s'),
                        'end_date'   => $end_date->format('Y-m-d H:i:s'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Subscription creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to create subscription: ' . $e->getMessage(),
                ], 500);
            }

        } elseif ($request->smart_ad_id && $request->ad_details_id) {
            // SMART ADS LOGIC
            $adDetails = SmartAdsDetail::find($data['ad_details_id']);

            if (! $adDetails) {
                return response()->json(['status' => 'error', 'message' => 'Invalid ad details ID'], 400);
            }

            $ad = SmartAd::find($adDetails->smart_ad_id);
            if (! $ad) {
                return response()->json(['status' => 'error', 'message' => 'Ad not found'], 400);
            }

            $existingPayment = SmartAdsPayment::where('transaction_id', $data['razorpay_payment_id'])->first();
            if ($existingPayment) {
                return response()->json(['status' => 'error', 'message' => 'Transaction already exists'], 400);
            }

            try {
                $paymentRecord = DB::transaction(function () use ($data, $ad, $adDetails) {
                    $transaction_details = [
                        'transaction'     => [
                            'payment_id'     => $data['razorpay_payment_id'],
                            'order_id'       => $data['razorpay_order_id'] ?? null,
                            'signature'      => $data['razorpay_signature'] ?? null,
                            'amount'         => $adDetails->total_price,
                            'transaction_id' => $data['razorpay_payment_id'],
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

                    $paymentRecord = SmartAdsPayment::create([
                        'user_id'             => auth()->id() ?? $ad->user_id,
                        'smart_ad_id'         => $ad->id,
                        'transaction_id'      => $data['razorpay_payment_id'],
                        'status'              => 'success',
                        'paid_at'             => now(),
                        'transaction_details' => $transaction_details,
                        'payment_gateway'     => 'razorpay',
                        'amount'              => $adDetails->total_price,
                        'currency'            => 'INR',
                    ]);

                    $adDetails->update([
                        'payment_status' => 'success',
                        'start_date'     => now(),
                        'end_date'       => now()->addDays($adDetails->total_days),
                    ]);

                    return $paymentRecord;
                });

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Payment verified and captured successfully',
                    'error'   => false,
                    'data'    => [
                        'transaction_id' => $paymentRecord->id,
                        'payment_id'     => $data['razorpay_payment_id'],
                        'ad_period'      => [
                            'start_date' => $adDetails->start_date->format('Y-m-d H:i:s'),
                            'end_date'   => $adDetails->end_date->format('Y-m-d H:i:s'),
                        ],
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Smart Ads payment failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to create payment: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid request - no plan_id or smart_ad_id provided',
        ], 400);
    }

    public function success(Request $request)
    {
        $theme = getTheme();

        $smartAdsPayments   = null;
        $membershipPayments = null;

        if ($request->filled('smart_ad_id')) {
            $smart_ad_id = $request->smart_ad_id;

            $smartAdsPayments = SmartAdsPayment::where('smart_ad_id', $smart_ad_id)
                ->where('user_id', auth()->id())
                ->first();
        } elseif ($request->filled('user_id') && $request->filled('tenure_id')) {
            $user_id            = $request->user_id;
            $membershipPayments = Transaction::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        $title = __('frontend-labels.payment_success.title');
        $data  = [
            'theme'              => $theme,
            'title'              => $title,
            'smartAdsPayments'   => $smartAdsPayments,
            'membershipPayments' => $membershipPayments,
        ];

        return view('front_end.' . $theme . '.pages.success', $data);
    }

    public function cancel()
    {
        $theme = getTheme();
        $title = __('frontend-labels.payment_cancel.title');
        $data  = [
            'theme' => $theme,
            'title' => $title,
        ];
        return view('front_end/' . $theme . '/pages/cancel', $data);

    }

}
