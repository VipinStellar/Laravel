<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatepassId extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "gatepass_id";
}
