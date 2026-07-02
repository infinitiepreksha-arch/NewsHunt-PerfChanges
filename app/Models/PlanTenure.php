<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTenure extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'plan_id',
        'duration',
        'product_id',
        'price',
        'discount_price',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'duration' => 'integer',
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the plan that owns the tenure.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
