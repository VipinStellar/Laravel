<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function companyList(Request $request)
    {
        $query = Company::select('*');
        return $this->_getPaginatedResult($query,$request);    
    }

   
}
