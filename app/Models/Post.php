<?php
namespace App\Models;

use DevDojo\LaravelReactions\Contracts\ReactableInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

class Post extends Model implements ReactableInterface
{
    use HasFactory;

    protected $fillable = ['channel_id', 'type', 'audio', 'video_thumb', 'video', 'topic_id', 'title', 'resource', 'slug', 'image', 'description', 'status', 'post_count', 'pubdate', 'publish_date', 'likes_count', 'news_language_id', 'video_url',
        'video_embed',
        'video_type','view_count',
        'is_video', 'is_custom_post','is_short_video'];

    public function channel()
    {
        return $this->belongsTo(Channel::class)->select('id', 'name', 'logo', 'slug');
    }
    public function subscriptions()
    {
        return $this->hasManyThrough(Subscription::class, Plan::class, 'id', 'plan_id');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class)->select('id', 'name', 'slug');
    }

    public function isFavoritedByUser($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function newsLanguage()
    {
        return $this->belongsTo(NewsLanguage::class);
    }
    
    public function images()
    {
        return $this->hasMany(PostImage::class, 'post_id');
    }

    public function links()
    {
        return $this->hasMany(PostLink::class, 'post_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getReactionsSummary()
    {
        return $this->reactions()
            ->getQuery()
            ->select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->get();
    }
    public function reactions(): MorphToMany
    {
        /** @var $this Model */
        return $this->morphToMany('DevDojo\\LaravelReactions\\Models\\Reaction', 'reactable')
            ->withPivot(['responder_id', 'responder_type']);
    }
    public function reacted($responder = null)
    {
        if (is_null($responder)) {
            $responder = auth()->user();
        }

        return $this->reactions()
            ->where('responder_id', $responder->id)
            ->where('responder_type', get_class($responder))->exists();
    }
}
