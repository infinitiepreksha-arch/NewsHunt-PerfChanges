<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'report_type_id',
        'is_other',
        'other_type',
        'status',
        'action_taken_by',
        'action_at',
        'report',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function reportType()
    {
        return $this->belongsTo(ReportType::class, 'report_type_id');
    }

    
    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_taken_by');
    }
}
