<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class StorySlide extends Model
{
    use HasFactory;

    protected $fillable = ['story_id', 'image', 'title', 'description', 'order','animation_details'];

    /**
     * Get the story that owns the slide.
     */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
    
    protected $casts = [
        'animation_details' => 'array',
    ];
}
