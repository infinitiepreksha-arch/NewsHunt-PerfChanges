<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsLanguage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'image', 'is_active', 'position', 'status'];

    public function rssFeeds()
    {
        return $this->hasMany(RssFeed::class, 'news_language_id');
    }

    public function topic()
    {
        return $this->hasMany(Topic::class, 'news_language_id');
    }

    public function channel()
    {
        return $this->hasMany(Channel::class, 'news_language_id');
    }

    public function post()
    {
        return $this->hasMany(Post::class, 'news_language_id');
    }

    public function enewspapers()
    {
        return $this->hasMany(ENewspaper::class, 'news_language_id');
    }

    public function status()
    {
        return $this->hasOne(NewsLanguageStatus::class, 'news_language_id'); // Foreign key
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'news_languages_subscribers', 'news_language_id', 'user_id');
    }

    public function isFollowedByUser()
    {
        return auth()->check() ? $this->followers()->where('user_id', auth()->id())->exists() : false;
    }
    // In NewsLanguage model
    public function subscribers()
    {
        return $this->hasMany(NewsLanguageSubscriber::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'news_language_id');
    }

    public function statusRecord()
    {
        return $this->hasOne(NewsLanguageStatus::class, 'news_language_id', 'id');
    }
}
