<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_in_english',
        'code',
        'rtl',
        'image',
    ];
    protected $casts = [
        'admin_panel_files' => 'array',
        'web_files'         => 'array',
    ];

    public function getRtlAttribute($rtl)
    {
        return $rtl != 0;
    }

    public function getImageAttribute($value)
    {
        if (! empty($value)) {
            if ($this->code == "en") {
                return asset("/assets/images/" . $value);
            }
            return url(Storage::url($value));
        }
        return "";
    }
}
