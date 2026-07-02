<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'country_code',
        'phone_number',
        'message',
    ];

    /**
     * Get the full phone number.
     *
     * @return string
     */
    public function getFullPhoneNumberAttribute()
    {
        return $this->country_code . $this->phone_number;
    }
}
