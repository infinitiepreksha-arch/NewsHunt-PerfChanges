<?php
namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SplashAndWeatherAdsController extends Controller
{
    protected function formatAd($ad, $placement)
    {
        return [
            "id"               => "ad_" . $ad->smart_ad_id,
            "smart_ad_id"      => $ad->smart_ad_id,
            "type"             => 'ad',
            "name"             => $ad->name,
            "title"            => $ad->name,
            "description"      => $ad->body,
            "body"             => $ad->body,
            "image"            => $ad->vertical_image ? url('storage/' . $ad->vertical_image) : null,
            "horizontal_image" => $ad->horizontal_image ? url('storage/' . $ad->horizontal_image) : null,
            "image_alt"        => $ad->image_alt,
            "imageUrl"         => $ad->imageUrl,
            "ad_type"          => $ad->ad_type,
            "slug"             => $ad->slug,
            "views"            => $ad->views + 1,
            "clicks"           => $ad->clicks,
            "contact_info"     => [
                "name"  => $ad->contact_name,
                "email" => $ad->contact_email,
                "phone" => $ad->contact_phone,
            ],
            "created_at"       => $ad->created_at,
            "publish_date"     => Carbon::parse($ad->created_at)->diffForHumans(),
        ];
    }

    protected function getAdByPlacement($placement)
    {
        $ads = DB::table('smart_ad_placements as sap')
            ->join('smart_ads as sa', 'sap.smart_ad_id', '=', 'sa.id')
            ->join('smart_ads_details as sad', 'sap.smart_ad_id', '=', 'sad.smart_ad_id')
            ->where('sap.placement_key', $placement)
            ->where('sad.ad_publish_status', 'approved')
            ->where('sad.payment_status', 'success')
            ->where('sap.start_date', '<=', now())
            ->where('sap.end_date', '>=', now())
            ->inRandomOrder()
            ->select(
                'sa.id as smart_ad_id',
                'sa.name',
                'sa.slug',
                'sa.body',
                'sa.adType as ad_type',
                'sa.vertical_image',
                'sa.horizontal_image',
                'sa.imageUrl',
                'sa.imageAlt as image_alt',
                'sa.views',
                'sa.clicks',
                'sa.created_at',
                'sad.contact_name',
                'sad.contact_email',
                'sad.contact_phone',
                'sap.start_date',
                'sap.end_date'
            )
            ->get();

        if ($ads->isNotEmpty()) {
            return $ads->map(function ($ad) use ($placement) {
                // increment views for each ad
                DB::table('smart_ads')->where('id', $ad->smart_ad_id)->increment('views');
                return $this->formatAd($ad, $placement);
            });
        }

        return collect(); // return empty collection if no ads found
    }

    public function splashScreenAd()
    {
        $ad = $this->getAdByPlacement('splash_screen ');
        return response()->json([
            "status"  => $ad ? true : false,
            "message" => $ad ? "Splash Screen Ad fetched successfully." : "No ad found.",
            "data"    => $ad,
        ]);
    }

    public function afterWeatherCardAd()
    {
        $ads = $this->getAdByPlacement('after_weather_card');
        return response()->json([
            "status"  => $ads->isNotEmpty(),
            "message" => $ads->isNotEmpty() ? "After Weather Card Ads fetched successfully." : "No ads found.",
            "data"    => $ads,
        ]);
    }
}
