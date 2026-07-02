<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdsRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'ad_type',
        'image',
        'url',
        'contact_name',
        'contact_email',
        'contact_phone',
        'total_price',
        'daily_price',
        'total_days',
        'price_summary', // Make sure this is here
        'web_ads_placement',
        'app_ads_placement',
        'ad_clicks',
        'ad_publish_status',
        'payment_status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'price_summary' => 'array',
        'start_date'    => 'date',
        'end_date'      => 'date',
        'web_ads_placement' => 'array',
        'app_ads_placement' => 'array',
    ];

    public function payments()
    {
        return $this->hasMany(CustomAdsPayment::class, 'ad_request_id');
    }

    public function tracking()
    {
        return $this->hasOne(CustomAdsTracking::class, 'ad_request_id');
    }
}
