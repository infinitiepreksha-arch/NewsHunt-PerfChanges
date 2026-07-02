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
        $status = self::latest()->first();
        return $status ? $status->status : 'inactive';
    }

    /**
     * Update the current status
     * 
     * @param string $status
     * @return self
     */
    public static function updateStatus($status)
    {
        return self::create([
            'status' => $status
        ]);
    }
    
}
