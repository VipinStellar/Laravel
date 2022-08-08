<?php

namespace App\Http\Controllers;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    protected $module;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allModule()
    {
        $module = Module::where('status',1)->get();
        return response()->json($module);
    }

}