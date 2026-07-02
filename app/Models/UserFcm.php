<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFcm extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'fcm_id', 'platform', 'news_language_id'];

}
