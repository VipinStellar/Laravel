<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchRelated extends Model
{
    use HasFactory;
    public $table = "branch_related";
    protected $fillable = [
        'user_id',
        'branch_id',
    ];
}
