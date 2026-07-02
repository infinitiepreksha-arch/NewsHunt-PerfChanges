<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartAd extends Model
{
    protected $table = 'smart_ads';

    protected $fillable = [
        'name',
        'slug',
        'body',
        'adType',
        'imageUrl',
        'imageAlt',
        'views',
        'clicks',
        'enabled',
        'vertical_image',
        'horizontal_image',
        'placements',
    ];

    protected $casts = [
        'placements' => 'array',
        'views'      => 'integer',
        'clicks'     => 'integer',
        'enabled'    => 'boolean',
    ];

    public function smartAdsDetail()
    {
        return $this->hasOne(SmartAdsDetail::class, 'smart_ad_id');
    }

    public function smartAdsTracking()
    {
        return $this->hasOne(SmartAdTracking::class, 'smart_ad_id');
    }

    public function placements()
    {
        return $this->hasMany(SmartAdPlacement::class, 'smart_ad_id');
    }

}
