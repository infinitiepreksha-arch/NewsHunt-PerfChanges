<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'message',
        'image',
        'item_id',
        'user_id',
        'send_to',
        'url',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

}
