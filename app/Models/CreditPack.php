<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_id',
        'credits',
        'price',
        'currency',
        'savings_percent',
        'tagline',
        'is_popular',
        'is_best_value',
    ];
}
