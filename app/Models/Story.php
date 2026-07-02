<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'topic_id','animation_details','story_count','news_language_id', 'image_size_type'];
    
    /**
     * Get the slides associated with the story.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // In your Story model (app/Models/Story.php)
    public function story_slides()
    {
        return $this->hasMany(StorySlide::class)->orderBy('order', 'asc');
    }

    public function news_language()
    {
        return $this->belongsTo(NewsLanguage::class);
    }

}
