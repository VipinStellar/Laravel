<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function contactList(Request $request)
    {
        $query = Contact::select('*');
        return $this->_getPaginatedResult($query,$request);    
    }

   
}
