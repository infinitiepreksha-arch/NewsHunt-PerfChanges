<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAdTracking extends Model
{
    protected $table = 'smart_ads_tracking';

    protected $fillable = [
        'smart_ad_id',
        'ad_clicks',
        'totalClicks',
    ];

    protected $casts = [
        'ad_clicks' => 'array',
        'totalClicks' => 'integer',
    ];

    public function smartAd()
    {
        return $this->belongsTo(SmartAd::class, 'smart_ad_id');
    }

    
}