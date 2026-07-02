<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'parent_id', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function reports()
    {
        return $this->hasMany(ReportComment::class, 'comment_id');
    }

    public function fetchComments($postId)
    {
        $comments = Comment::with('user', 'replies.user')
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->get();

        return response()->json($comments);
    }
}
