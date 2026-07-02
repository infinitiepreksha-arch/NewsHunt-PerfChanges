<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RssFeed extends Model
{
    use HasFactory;

    protected $fillable = ['channel_id', 'topic_id', 'feed_url', 'data_format', 'sync_interval', 'status', 'news_language_id', 'news_languages_image', 'description_type'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function newsLanguage()
    {
        return $this->belongsTo(NewsLanguage::class, 'news_language_id');
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($rssFeed) {
            if (! $rssFeed->news_language_id) {
                $rssFeed->news_language_id = DB::table('languages')->where('is_default', 1)->value('id');
            }
        });
    }
}
