<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'slug',
        'post_count',
        'layout_width',
        'html_content',
        'status',
        'subject',
        'logo',
        'image',
        'type',
        'closing',
        'signature',
        'footer_text',
    ];
}
