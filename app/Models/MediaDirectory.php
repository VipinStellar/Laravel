<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaDirectory extends Model
{
    use HasFactory;
    public $table = "media_directory";
    public $timestamps = false;
}
