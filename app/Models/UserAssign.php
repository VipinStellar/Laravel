<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssign extends Model
{
    use HasFactory;
    public $table = "user_assign";
    public $timestamps = false;
}
