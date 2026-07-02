<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ENewspaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'news_language_id',
        'date',
        'pdf_path',
        'type',
        'thumbnail',
        'added_by',
        'added_by_name',
        'topic_id',
        'background_image',
    ];

    /**
     * Get the channel that owns the ENewspaper.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Get the news language associated with the ENewspaper.
     */
    public function newsLanguage()
    {
        return $this->belongsTo(NewsLanguage::class);
    }

    /**
     * Get the topic that owns the ENewspaper.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
