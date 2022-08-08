<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaTransfer extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "transfer_media";

}
