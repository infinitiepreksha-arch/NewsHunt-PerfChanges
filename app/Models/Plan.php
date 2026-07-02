<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'status',    
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the features associated with the plan.
     */
    public function features_plan()
    {
        return $this->hasOne(Feature::class);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    /**
     * Get the tenures for the plan.
     */
    public function planTenures()
    {
        return $this->hasMany(PlanTenure::class);
    }

    /**
     * Get the subscriptions for the plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if the plan has any active subscriptions.
     */
    public function hasActiveSubscriptions()
    {
        return $this->subscriptions()
            ->where('status', '!=', 'expired')
            ->where('end_date', '>=', now()->toDateString())
            ->exists();
    }
}
