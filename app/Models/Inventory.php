<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
   
    use HasFactory;
    public $timestamps = false;
    public $table = "inventory";
    protected $fillable = [
        'model_num',
        'serial_num',
        'pcb_num',
        'interface',
        'firmware',
        'date_purchase',
        'type',
        'rack_num',
        'inventory_type',
        'capacity'
    ];

}
