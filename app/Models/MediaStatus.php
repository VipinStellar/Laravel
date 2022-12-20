<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaStatus extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "media_status";
}
