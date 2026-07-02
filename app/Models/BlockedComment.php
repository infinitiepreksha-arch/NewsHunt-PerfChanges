<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedComment extends Model
{
    use HasFactory;
    protected $table = 'blocked_comments';

    protected $fillable = [
        'blocker_user_id',
        'comment_id',
        'owner_user_id',
        'status',
        'block_reason',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /*
    |----------------------------------
    | Relationships
    |----------------------------------
    */

    // Who blocked
    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_user_id');
    }

    // Comment that is blocked
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    // Owner of comment
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
