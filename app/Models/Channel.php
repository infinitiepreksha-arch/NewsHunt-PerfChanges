<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = ['country_id', 'news_language_id', 'description', 'name', 'logo', 'slug', 'status'];

    public function rssFeeds()
    {
        return $this->hasMany(RssFeed::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'channel_subscribers', 'channel_id', 'user_id');
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
