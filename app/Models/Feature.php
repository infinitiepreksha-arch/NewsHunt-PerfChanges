<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'plan_id',
        'is_ads_free',
        'number_of_articles',
        'number_of_stories',
        'number_of_e_papers_and_magazines'
    ];

    protected $casts = [
        'is_ads_free' => 'boolean',
        'number_of_articles' => 'integer',
        'number_of_stories' => 'integer',
        'number_of_e_papers_and_magazines' => 'integer'
    ];

    /**
     * Get the plan that owns the feature.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }   

    
}
