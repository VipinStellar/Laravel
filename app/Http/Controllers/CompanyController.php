<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Helper;
use PaymentProcess;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function companyList(Request $request)
    {
        $term = trim($request->input('term'));
        $searchfieldName = $request->input('searchfieldName');
        $query = Company::select('*');
        if($term !=null && $term !='' && $searchfieldName !=null && $searchfieldName !='' )
        $query->Where($searchfieldName, '=', "".$term."");
        return $this->_getPaginatedResult($query,$request);    
    }

    public function updateCompany(Request $request)
    {
        $comapny = Company::find($request->input('id'));
        $comapny->company_name = $request->input('company_name');
        $comapny->gst_number = $request->input('gst_number');
        $comapny->billing_street = $request->input('billing_street');
        $comapny->shipping_street = $request->input('shipping_street');
        $comapny->billing_landmark = $request->input('billing_landmark');
        $comapny->shipping_landmark = $request->input('shipping_landmark');
        $comapny->billing_city = $request->input('billing_city');
        $comapny->shipping_city = $request->input('shipping_city');
        $comapny->billing_state_ut = $request->input('billing_state_ut');
        $comapny->shipping_state_ut = $request->input('shipping_state_ut');
        $comapny->billing_state_code = $request->input('billing_state_code');
        $comapny->shipping_state_code = $request->input('shipping_state_code');
        $comapny->billing_code = $request->input('billing_code');
        $comapny->shipping_code = $request->input('shipping_code');
        $comapny->billing_country = $request->input('billing_country');
        $comapny->shipping_country = $request->input('shipping_country');
        if($comapny->gst_number != null && $comapny->gst_number !='')
        {
            $gstCheck = PaymentProcess::getGstinData($comapny->gst_number);
            if(empty($gstCheck) || (!empty($gstCheck) && ($gstCheck['status_cd'] == 0)))
            {
                $error = array("gst_number"=>array($gstCheck['error']['message']));
                return response()->json($error, 400);
            }
            else if(!empty($gstCheck) && ($gstCheck['status_cd'] == 1))
            {
                $comapny->sez_unit_company = ($gstCheck['data']['dty']=='SEZ Unit')?1:0;
            } 
        }
        if($comapny->billing_code !=null && $comapny->billing_state_code !=null)
        {
                $pinCheck = $this->_checkPinCode($comapny->billing_state_code,$comapny->billing_code);
                if(!$pinCheck)
                {
                    $error = array("billing_code"=>array("Invalid Pin Code"));
                    return response()->json($error, 400);
                }

        }
        if($comapny->shipping_code !=null && $comapny->shipping_state_ut !=null)
        {
                $pinCheck = $this->_checkPinCode($comapny->shipping_state_ut,$comapny->shipping_code);
                if(!$pinCheck)
                {
                    $error = array("shipping_code"=>array("Invalid Pin Code"));
                    return response()->json($error, 400);
                }

        }
        $comapny->save();
        Helper::sendZohoCrmData($comapny,'COMPANY-EDIT');

    }

   
}
