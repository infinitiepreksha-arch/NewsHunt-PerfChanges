<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLanguageSubscriber extends Model
{
    use HasFactory;
    protected $table = 'news_languages_subscribers';

    protected $fillable = ['news_language_id', 'user_id','is_selected'];

    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newsLanguage()
    {
        return $this->belongsTo(NewsLanguage::class, 'news_language_id');
    }

}
