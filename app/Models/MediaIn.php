<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaIn extends Model
{
    use HasFactory;
    public $table = "media_in";
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'zoho_id',
        'customer_id',
        'branch_id',
        'job_id',
        'recovery_possibility',
        'required_days',
        'recovery_percentage',
        'access_percentage',
        'tampering_required',
        'recoverable_data',
        'assessment_status',
        'stage',
        'media_type',
        'media_in_id',
        'assessment_on',
        'assessment_by'
    ];
}
