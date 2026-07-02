<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAdsPayment extends Model
{
    use HasFactory;

    protected $table    = 'smart_ads_payments';
    protected $fillable = [
        'smart_ad_id',
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
        'transaction_details' => 'array', // longtext, store JSON if applicable
        'paid_at'             => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    // Relationship
    public function smartAd()
    {
        return $this->belongsTo(SmartAd::class, 'smart_ad_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
