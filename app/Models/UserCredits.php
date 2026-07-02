<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredits extends Model
{
    use HasFactory;

    protected $table = 'user_credits';

    protected $fillable = [
        'user_id',
        'credits_purchased',
        'credits_consumed',
        'total_credits',
        'available_credits',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBalanceAttribute()
    {
        return $this->total_credits - $this->credits_consumed;
    }
    
}
