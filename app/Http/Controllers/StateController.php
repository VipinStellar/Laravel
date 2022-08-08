<?php

namespace App\Http\Controllers;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    protected $state;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allState()
    {
        $states = State::all();
        return response()->json([$states]);
    }

}