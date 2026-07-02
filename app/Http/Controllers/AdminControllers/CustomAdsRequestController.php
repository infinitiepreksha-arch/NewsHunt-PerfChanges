<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Mail\SmartAdStatusMail;
use App\Models\Setting;
use App\Models\SmartAd;
use App\Models\SmartAdsDetail;
use App\Models\SmartAdsPayment;
use App\Services\ResponseService;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomAdsRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        ResponseService::noAnyPermissionThenRedirect(['list-CustomAds', 'view-details-CustomAds', 'change-status-CustomAds']);

        $title = __('page.CUSTOM_ADS');

        $approval_limit = Setting::where('name', 'approval_limit_for_admin')->value('value') ?? 3;

        // Fetch current number of approved ads
        $current_approved = SmartAd::whereHas('smartAdsDetail', function ($query) {
            $query->where('ad_publish_status', 'approved');
        })->count();

        if (request()->ajax()) {
            // Get SmartAd data with related models
            $data = SmartAd::with(['smartAdsDetail.user', 'smartAdsTracking'])
                ->select(
                    'id',
                    'name as title',
                    'slug',
                    'adType as ad_type',
                    'vertical_image',
                    'horizontal_image',
                    'body as description',
                    'imageUrl as url',
                    'views',
                    'clicks',
                    'enabled',
                    'created_at',
                    'updated_at'
                )
                ->get()
                ->map(function ($smartAd) {
                    // Get related data
                    $detail   = $smartAd->smartAdsDetail;
                    $tracking = $smartAd->smartAdsTracking;
                    $payment  = SmartAdsPayment::where('smart_ad_id', $smartAd->id)->first();

                    return [
                        'id'                  => $smartAd->id,
                        'user_id'             => $detail->user_id ?? null,
                        'title'               => $smartAd->title,
                        'slug'                => $smartAd->slug,
                        'ad_type'             => $smartAd->ad_type,
                        'vertical_image'      => $smartAd->vertical_image,
                        'horizontal_image'    => $smartAd->horizontal_image,
                        'description'         => $smartAd->description,
                        'url'                 => $smartAd->url,
                        'enabled'             => $smartAd->enabled,
                        'views'               => $smartAd->views,
                        'clicks'              => $smartAd->clicks,

                        // From SmartAdsDetail
                        'contact_name'        => $detail->contact_name ?? '',
                        'contact_email'       => $detail->contact_email ?? '',
                        'contact_phone'       => $detail->contact_phone ?? '',
                        'total_price'         => $detail->total_price ?? 0,
                        'daily_price'         => $detail->daily_price ?? 0,
                        'total_days'          => $detail->total_days ?? 0,
                        'price_summary'       => $detail->price_summary ?? [],
                        'web_ads_placement'   => $detail->web_ads_placement ?? [],
                        'app_ads_placement'   => $detail->app_ads_placement ?? [],
                        'ad_publish_status'   => $detail->ad_publish_status ?? 'pending',
                        'payment_status'      => $detail->payment_status ?? 'pending',
                        'start_date'          => $detail->start_date ?? null,
                        'end_date'            => $detail->end_date ?? null,

                        // From SmartAdTracking
                        'ad_clicks'           => $tracking->ad_clicks ?? [],
                        'total_clicks'        => $tracking->totalClicks ?? 0,

                        // From SmartAdsPayment
                        'order_id'            => $payment->order_id ?? null,
                        'amount'              => $payment->amount ?? 0,
                        'currency'            => $payment->currency ?? 'USD',
                        'payment_gateway'     => $payment->payment_gateway ?? null,
                        'transaction_id'      => $payment->transaction_id ?? null,
                        'transaction_details' => $payment->transaction_details ?? [],
                        'paid_at'             => $payment->paid_at ?? null,

                        'created_at'          => $smartAd->created_at,
                        'updated_at'          => $smartAd->updated_at,
                    ];
                });

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-info btn-sm preview-btn" data-id="' . $row['id'] . '" data-bs-toggle="modal" data-bs-target="#previewModal">Preview</button>';
                })
                ->addColumn('vertical_image', function ($row) {
                    return $row['vertical_image'] ? asset('storage/' . $row['vertical_image']) : '';
                })
                ->addColumn('horizontal_image', function ($row) {
                    return $row['horizontal_image'] ? asset('storage/' . $row['horizontal_image']) : '';
                })
                ->addColumn('ad_publish_status', function ($row) {
                    return $row['ad_publish_status'] ?? 'pending';
                })
                ->addColumn('payment_status', function ($row) {
                    return $row['payment_status'] ?? 'pending';
                })
                ->addColumn('user', function ($row) {
                    return $row['contact_name'] ?? 'N/A';
                })
                ->addColumn('pricing', function ($row) {
                    $totalPrice = $row['total_price'] ?? 0;
                    $dailyPrice = $row['daily_price'] ?? 0;
                    $totalDays  = $row['total_days'] ?? 0;

                    return '<strong>' . number_format($totalPrice, 2) . '</strong>' .
                    '<br><small>' . number_format($dailyPrice, 2) . '/day × ' . $totalDays . ' days</small>';
                })
                ->rawColumns(['action', 'status', 'pricing', 'performance'])
                ->editColumn('created_at', function ($row) {
                    return $row['created_at'] ? date('Y-m-d H:i:s', strtotime($row['created_at'])) : '';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row['updated_at'] ? date('Y-m-d H:i:s', strtotime($row['updated_at'])) : '';
                })
                ->editColumn('start_date', function ($row) {
                    return $row['start_date'] ? date('Y-m-d H:i:s', strtotime($row['start_date'])) : '';
                })
                ->editColumn('end_date', function ($row) {
                    return $row['end_date'] ? date('Y-m-d H:i:s', strtotime($row['end_date'])) : '';
                })
                ->make(true);
        }

        return view('admin.ads_requests.index', compact('title', 'approval_limit', 'current_approved'));
    }

    public function updateStatus($id)
    {
        try {
            $smartAd = SmartAd::findOrFail($id);
            $detail  = $smartAd->smartAdsDetail;

            if (! $detail) {
                return response()->json(['success' => false, 'message' => 'Smart ad detail not found']);
            }

            $oldStatus = $detail->ad_publish_status;
            $newStatus = request('status');

            // Validate email address
            if (! $detail->contact_email || ! filter_var($detail->contact_email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['success' => false, 'message' => 'Invalid email address']);
            }

            // Update the detail
            $detail->update([
                'ad_publish_status' => $newStatus,
                'payment_status'    => request('payment_status', $detail->payment_status),
            ]);

            // Apply mail settings
            applyMailSettingsFromDb();

            // Send mail/notification if status has changed
            if ($oldStatus !== $newStatus) {
                try {
                    // Log the email attempt
                    Log::info('Attempting to send status change email', [
                        'ad_id'      => $id,
                        'email'      => $detail->contact_email,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                    ]);

                    // Check if we should queue or send immediately
                    if (config('queue.default') !== 'sync') {
                        // Queue the email
                        Mail::to($detail->contact_email)->queue(
                            new SmartAdStatusMail($detail, $newStatus)
                        );
                    } else {
                        // Send immediately
                        Mail::to($detail->contact_email)->send(
                            new SmartAdStatusMail($detail, $newStatus)
                        );
                    }

                    Log::info('Email queued/sent successfully for ad status change', ['ad_id' => $id]);

                } catch (\Exception $mailException) {
                    Log::error('Failed to send status change email', [
                        'ad_id' => $id,
                        'email' => $detail->contact_email,
                        'error' => $mailException->getMessage(),
                    ]);
                    // Don't return error - the status update should still succeed
                }
            }

            // Update the main smart ad
            $smartAd->update([
                'enabled' => $newStatus === 'active',
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'Status updated successfully',
                'email_sent' => $oldStatus !== $newStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in updateStatus method', [
                'ad_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

}
