<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppleCreditTransaction extends Model
{
    protected $table = 'apple_credit_transactions'; // custom table name

    protected $fillable = [
        'user_id',
        'transaction_id',
        'product_id',
        'credits_awarded',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
