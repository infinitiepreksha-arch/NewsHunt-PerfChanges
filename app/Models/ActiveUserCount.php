<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveUserCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'count',
    ];
}
