<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo', 'status', 'news_language_id', 'categorie_order'];

    public function rssFeeds()
    {
        return $this->hasMany(RssFeed::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function webStories()
    {
        return $this->hasMany(StorySlide::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function newsLanguage()
    {
        return $this->belongsTo(NewsLanguage::class);
    }

    public function eNewspapers()
    {
        return $this->hasMany(ENewspaper::class);
    }
}
