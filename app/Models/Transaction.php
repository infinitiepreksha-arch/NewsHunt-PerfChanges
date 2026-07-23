<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaction';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'order_id',
        'feature_id',
        'transaction_id',
        'payment_gateway',
        'amount',
        'discount',
        'plan_details',
        'status'
    ];

    protected $casts = [
        'plan_details' => 'array',
    ];

    /**
     * Get the plan name from the serialized plan_details attribute.
     */
    public function getPlanNameAttribute()
    {
        return $this->plan_details['plan']['plan_name'] ?? 'N/A';
    }
    /**
     * Get the user that owns the transaction.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function planTenure()
{
    return $this->belongsTo(PlanTenure::class, 'plan_tenure_id');
}
}
