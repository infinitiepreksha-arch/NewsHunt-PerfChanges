<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAdsDetail extends Model
{
    protected $table = 'smart_ads_details';

    protected $fillable = [
        'user_id',
        'smart_ad_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'total_price',
        'daily_price',
        'total_days',
        'price_summary',
        'web_ads_placement',
        'app_ads_placement',
        'ad_publish_status',
        'payment_status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'total_price' => 'float',
        'daily_price' => 'float',
        'total_days' => 'integer',
        'price_summary' => 'array',
        'web_ads_placement' => 'array',
        'app_ads_placement' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function smartAd()
    {
        return $this->belongsTo(SmartAd::class, 'smart_ad_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}