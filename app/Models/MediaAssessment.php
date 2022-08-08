<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAssessment extends Model
{
    use HasFactory;
    public $table = "media_assessment";
    public $timestamps = false;
    // protected $fillable = [
    //     'zoho_id',
    //     'client_id',
    //     'branch_id',
    //     'media_type',
    //     'assessment_status',
    //     'media_make',
    //     'media_model',
    //     'media_serial',
    //     'media_capacity',
    //     'tampered_status',
    //     'media_condition',
    //     'media_os',
    //     'media_firmware',
    //     'encryption_status',
    //     'peripherals_details',
    //     'created_on',
    //     'last_updated'
    // ];
}
