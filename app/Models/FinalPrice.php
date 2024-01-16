<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalPrice extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = "final_price";
}
