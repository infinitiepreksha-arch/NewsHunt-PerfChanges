<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLanguageStatus extends Model
{
    use HasFactory;

    protected $table = 'news_language_status';

    protected $fillable = [
        'status'
    ];

    /**
     * Get the current status
     * 
     * @return string
     */
    public static function getCurrentStatus()
    {
        return \Illuminate\Support\Facades\Cache::remember('news_language_current_status', 600, function () {
            $status = self::latest()->first();
            return $status ? $status->status : 'inactive';
        });
    }

    /**
     * Update the current status
     * 
     * @param string $status
     * @return self
     */
    public static function updateStatus($status)
    {
        \Illuminate\Support\Facades\Cache::forget('news_language_current_status');
        try {
            if (!\Illuminate\Support\Facades\Cache::has('view_composer_cache_buster')) {
                \Illuminate\Support\Facades\Cache::forever('view_composer_cache_buster', 1);
            } else {
                \Illuminate\Support\Facades\Cache::increment('view_composer_cache_buster');
            }
        } catch (\Throwable $e) {}

        return self::create([
            'status' => $status
        ]);
    }
    
}
