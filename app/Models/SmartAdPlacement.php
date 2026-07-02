<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartAdPlacement extends Model
{
    protected $fillable = [
        'smart_ad_id',
        'user_id',
        'placement_key',
        'start_date',
        'end_date',
    ];
    
    public function smartAd()
    {
        return $this->belongsTo(SmartAd::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
