<?php

namespace App\Http\Controllers;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    protected $country;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allCountry()
    {
        $country = Country::all();
        return response()->json([$country]);
    }

}