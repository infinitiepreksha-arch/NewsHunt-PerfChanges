<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    
    /**
     * Get all comments associated with this report type
     */
    public function reportComments()
    {
        return $this->hasMany(ReportComment::class, 'report_type_id');
    }
}
