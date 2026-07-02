<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdsPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_request_id',
        'user_id',
        'order_id',
        'amount',
        'currency',
        'payment_gateway',
        'transaction_id',
        'transaction_details',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'transaction_details' => 'array',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function adRequest()
    {
        return $this->belongsTo(CustomAdsRequest::class, 'ad_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
