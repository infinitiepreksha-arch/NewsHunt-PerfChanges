<?php
namespace App\Http\Controllers;

use App\Models\CustomAdsRequest;
use App\Models\CustomAdsTracking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SmartAdsFrontController extends Controller
{

    public function index(Request $request)
    {
        $theme             = getTheme();
        $title             = "Smart Ads";
        $customAdsSettings = $this->customAdsSettings();
        $data              = [
            'theme'             => $theme,
            'title'             => $title,
            'customAdsSettings' => $customAdsSettings,
        ];

        return view("front_end.$theme.pages.smart_ads_request", $data);
    }

    public function store(Request $request)
    {

        try {
            // First, let's do basic validation without complex rules
            $request->validate([
                'title'         => 'required|string|max:255',
                'contact_name'  => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'start_date'    => 'required|date',
                'end_date'      => 'required|date',
                'total_price'   => 'required|numeric|min:0',
                'daily_price'   => 'required|numeric|min:0',
                'total_days'    => 'required|integer|min:1',
            ]);

            // Check if at least one placement is selected
            $appPlacements = $request->input('app_ads_placement', []);
            $webPlacements = $request->input('web_ads_placement', []);

            if (empty($appPlacements) && empty($webPlacements)) {
                return back()->withErrors(['placement' => 'Please select at least one placement option.'])->withInput();
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                try {
                    $image     = $request->file('image');
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                    // Make sure the directory exists
                    if (! Storage::disk('public')->exists('ads/images')) {
                        Storage::disk('public')->makeDirectory('ads/images');
                    }

                    $imagePath = $image->storeAs('ads/images', $imageName, 'public');
                    Log::info('Image uploaded successfully', ['path' => $imagePath]);
                } catch (Exception $e) {
                    Log::error('Image upload failed', ['error' => $e->getMessage()]);
                    return back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                }
            } else {
                return back()->withErrors(['image' => 'Please upload an advertisement image.'])->withInput();
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Get custom ads settings for price summary
                $customAdsSettings = $this->customAdsSettings();

                // Build price summary
                $priceSummary = $this->buildPriceSummary($appPlacements, $webPlacements, $customAdsSettings);

                // Create the ad request with JSON data
                $adRequest = CustomAdsRequest::create([
                    'user_id'           => Auth::user()->id,
                    'title'             => $request->input('title'),
                    'slug'              => Str::slug($request->input('title')) . '-' . time(),
                    'description'       => $request->input('description'),
                    'ad_type'           => $request->input('ad_type', 'image'),
                    'image'             => $imagePath,
                    'url'               => $request->input('url'),
                    'contact_name'      => $request->input('contact_name'),
                    'contact_email'     => $request->input('contact_email'),
                    'contact_phone'     => $request->input('phone'),
                    'total_price'       => (float) $request->input('total_price'),
                    'daily_price'       => (float) $request->input('daily_price'),
                    'total_days'        => (int) $request->input('total_days'),
                    'price_summary'     => $priceSummary,
                    'web_ads_placement' => ! empty($webPlacements) ? $webPlacements : null,
                    'app_ads_placement' => ! empty($appPlacements) ? $appPlacements : null,
                    'ad_clicks'         => 0,
                    'ad_publish_status' => 'pending',
                    'payment_status'    => 'pending',
                    'start_date'        => $request->input('start_date'),
                    'end_date'          => $request->input('end_date'),
                ]);

                // Create tracking record
                CustomAdsTracking::create([
                    'ad_request_id' => $adRequest->id,
                    'ad_clicks'     => json_encode([]),
                ]);

                // Commit the transaction
                DB::commit();

                // Log success
                Log::info('Smart Ad request submitted successfully', [
                    'ad_request_id' => $adRequest->id,
                    'title'         => $adRequest->title,
                    'contact_email' => $adRequest->contact_email,
                    'total_price'   => $adRequest->total_price,
                ]);

                // Redirect with success message
                return redirect()->route('smart-ads.index')->with('success',
                    'Your advertisement request has been submitted successfully! Request ID: #' . $adRequest->id
                );

            } catch (Throwable $e) {
                // Rollback transaction
                DB::rollback();

                // Delete uploaded image if it exists
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                throw $e; // Re-throw to be caught by outer catch
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error in Smart Ads', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();

        } catch (Throwable $e) {
            Log::error('Error submitting smart ad request', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Build price summary array from selected placements
     */
    protected function buildPriceSummary($appPlacements, $webPlacements, $customAdsSettings)
    {
        $priceSummary = [];

        // Process app placements
        if (! empty($appPlacements)) {
            foreach ($appPlacements as $placementGroup) {
                $placements = is_string($placementGroup)
                ? array_map('trim', explode(',', $placementGroup))
                : [$placementGroup];

                foreach ($placements as $placement) {
                    $priceKey = $this->getAppPlacementPriceKey($placement);
                    if ($priceKey) {
                        $price          = isset($customAdsSettings[$priceKey]) ? (float) $customAdsSettings[$priceKey] : 0;
                        $priceSummary[] = [
                            'placement'    => $placement,
                            'type'         => 'app',
                            'display_name' => $this->getPlacementDisplayName($placement),
                            'daily_price'  => $price,
                        ];
                    }
                }
            }
        }

        // Process web placements
        if (! empty($webPlacements)) {
            foreach ($webPlacements as $placementGroup) {
                $placements = is_string($placementGroup)
                ? array_map('trim', explode(',', $placementGroup))
                : [$placementGroup];

                foreach ($placements as $placement) {
                    $priceKey = $this->getWebPlacementPriceKey($placement);
                    if ($priceKey) {
                        $price          = isset($customAdsSettings[$priceKey]) ? (float) $customAdsSettings[$priceKey] : 0;
                        $priceSummary[] = [
                            'placement'    => $placement,
                            'type'         => 'web',
                            'display_name' => $this->getPlacementDisplayName($placement),
                            'daily_price'  => $price,
                        ];
                    }
                }
            }
        }

        return $priceSummary;
    }

    protected function getAppPlacementPriceKey($placement)
    {
        $mapping = [
            'app_category_news_page'         => 'category_news_page_price',
            'topics_page'           => 'topics_page_price',
            'under weather card'    => 'under_weather_card_price',
            'above_recommendations' => 'above_recommendations_section_price',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getWebPlacementPriceKey($placement)
    {
        $mapping = [
            'header'        => 'header_price',
            'left_sidebar'  => 'left_sidebar_price',
            'footer'        => 'footer_price',
            'right_sidebar' => 'right_sidebar_price',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getAppPlacementStatusKey($placement)
    {
        $mapping = [
            'app_category_news_page'         => 'category_news_page_placement_status',
            'topics_page'           => 'topic_placement_status',
            'under weather card'    => 'after_weather_section_status',
            'above_recommendations' => 'above_recommendations_section_status',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getWebPlacementStatusKey($placement)
    {
        $mapping = [
            'header'        => 'header_placement_status',
            'left_sidebar'  => 'left_sidebar_status',
            'footer'        => 'footer_placement_status',
            'right_sidebar' => 'right_sidebar_status',
        ];

        return $mapping[$placement] ?? null;
    }

    protected function getPlacementDisplayName($placement)
    {
        $displayNames = [
            'app_category_news_page'         => 'Splash Screen',
            'topics_page'           => 'Topics Page',
            'under weather card'    => 'After Weather Section',
            'above_recommendations' => 'Above Recommendations Section',
            'header'                => 'Header',
            'left_sidebar'          => 'Left Sidebar',
            'footer'                => 'Footer',
            'right_sidebar'         => 'Right Sidebar',
        ];

        return $displayNames[$placement] ?? ucwords(str_replace('_', ' ', $placement));
    }

    protected function customAdsSettings()
    {
        try {
            $customAdsSettings = Setting::whereIn('name', [
                'above_recommendations_section_status',
                'above_recommendations_section_price',
                'topic_placement_status',
                'topics_page_price',
                'footer_placement_status',
                'footer_price',
                'right_sidebar_status',
                'right_sidebar_price',
                'left_sidebar_status',
                'left_sidebar_price',
                'header_placement_status',
                'header_price',
                'after_weather_section_status',
                'after_weather_section_price',
                'category_news_page_placement_status',
                'category_news_page_price',
                'enable_custom_ads_status',
            ])->pluck('value', 'name');

            return [
                'above_recommendations_section_status' => $customAdsSettings['above_recommendations_section_status'] ?? '0',
                'above_recommendations_section_price'  => $customAdsSettings['above_recommendations_section_price'] ?? '0',
                'topic_placement_status'               => $customAdsSettings['topic_placement_status'] ?? '0',
                'topics_page_price'                    => $customAdsSettings['topics_page_price'] ?? '0',
                'footer_placement_status'              => $customAdsSettings['footer_placement_status'] ?? '0',
                'footer_price'                         => $customAdsSettings['footer_price'] ?? '0',
                'right_sidebar_status'                 => $customAdsSettings['right_sidebar_status'] ?? '0',
                'right_sidebar_price'                  => $customAdsSettings['right_sidebar_price'] ?? '0',
                'left_sidebar_status'                  => $customAdsSettings['left_sidebar_status'] ?? '0',
                'left_sidebar_price'                   => $customAdsSettings['left_sidebar_price'] ?? '0',
                'header_placement_status'              => $customAdsSettings['header_placement_status'] ?? '0',
                'header_price'                         => $customAdsSettings['header_price'] ?? '0',
                'after_weather_section_status'         => $customAdsSettings['after_weather_section_status'] ?? '0',
                'after_weather_section_price'          => $customAdsSettings['after_weather_section_price'] ?? '0',
                'category_news_page_placement_status'                 => $customAdsSettings['category_news_page_placement_status'] ?? '0',
                'category_news_page_price'                  => $customAdsSettings['category_news_page_price'] ?? '0',
                'enable_custom_ads_status'             => $customAdsSettings['enable_custom_ads_status'] ?? '0',
            ];
        } catch (Throwable $e) {
            Log::error('Error getting custom ads settings: ' . $e->getMessage());
            return [
                'above_recommendations_section_status' => '0',
                'above_recommendations_section_price'  => '0',
                'topic_placement_status'               => '0',
                'topics_page_price'                    => '0',
                'footer_placement_status'              => '0',
                'footer_price'                         => '0',
                'right_sidebar_status'                 => '0',
                'right_sidebar_price'                  => '0',
                'left_sidebar_status'                  => '0',
                'left_sidebar_price'                   => '0',
                'header_placement_status'              => '0',
                'header_price'                         => '0',
                'after_weather_section_status'         => '0',
                'after_weather_section_price'          => '0',
                'category_news_page_placement_status'                 => '0',
                'category_news_page_price'                  => '0',
                'enable_custom_ads_status'             => '0',
            ];
        }
    }
}
