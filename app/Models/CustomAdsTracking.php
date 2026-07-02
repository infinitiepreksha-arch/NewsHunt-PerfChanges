<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdsTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_request_id',
        'ad_clicks',
    ];

    protected $casts = [
        'ad_clicks' => 'array',
    ];

    // Relationships
    public function adRequest()
    {
        return $this->belongsTo(CustomAdsRequest::class, 'ad_request_id');
    }
}
