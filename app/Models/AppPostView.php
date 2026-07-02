<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppPostView extends Model
{
    use HasFactory;

    protected $table = 'app_post_views';

    protected $fillable = [
        'device_id',
        'post_id',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
