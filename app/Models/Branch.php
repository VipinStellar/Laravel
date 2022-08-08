<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    public $table = "branch";
    protected $fillable = [
        'branch_name',
        'country_id',
        'state_name',
        'address'
    ];
}
