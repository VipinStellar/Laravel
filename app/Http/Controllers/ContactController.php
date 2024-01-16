<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Helper;
use App\Models\Company;
use PaymentProcess;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function contactList(Request $request)
    {
        $term = trim($request->input('term'));
        $searchfieldName = $request->input('searchfieldName');
        $select = 'contact.*, branch.branch_name as branch_name,company.company_name,company.gst_number as com_gst_number,company.billing_street as com_billing_street,company.billing_landmark as com_billing_landmark,company.billing_city as com_billing_city,company.billing_state_ut as com_billing_state_ut,
                   company.billing_state_code as com_billing_state_code,company.billing_code as com_billing_code,company.billing_country as com_billing_country,company.id as com_id,company.gst_number as com_gst_number,company.zoho_company_id as zoho_company_id';
        $query = DB::table('contact')->select(DB::raw($select))->leftJoin("company", "company.zoho_company_id", "=", "contact.company_id")
        ->leftJoin("branch", "branch.zoho_branch_id", "=", "contact.branch_id");
        if($term !=null && $term !='' && $searchfieldName !=null && $searchfieldName !='' )
        {
            if($searchfieldName == 'company_name')
            $query->Where('company.'.$searchfieldName, '=', "".$term."");
            elseif($searchfieldName == 'branch_name')
            $query->Where('branch.'.$searchfieldName, '=', "".$term."");
            else 
            $query->Where('contact.'.$searchfieldName, '=', "".$term."");
        }
      //  $query = Contact::select('contact.*,company.company_name,branch.branch_name')
        return $this->_getPaginatedResult($query,$request);    
    }

    public function updateContact(Request $request)
    {
        $contact = Contact::find($request->input('id'));
        $sez_unit_company = 0;
        $contact->first_name = $request->input('first_name');
        $contact->last_name = $request->input('last_name');
        $contact->customer_name = $request->input('first_name')." ".$request->input('last_name');
        $contact->email = $request->input('email');
        $contact->mobile = $request->input('mobile');
        $contact->mailing_street = $request->input('mailing_street');
        $contact->mailing_region = $request->input('mailing_region');
        $contact->mailing_city = $request->input('mailing_city');
        $contact->mailing_state_code = $request->input('mailing_state_code');
        $contact->mailing_country = $request->input('mailing_country');
        $contact->mailing_state_ut = $request->input('mailing_state_ut');
        $contact->mailing_zip = $request->input('mailing_zip');
        $contact->use_billing_address = $request->input('use_billing_address');
        $contact->billing_name = $request->input('billing_name');
        $contact->billing_email = $request->input('billing_email');
        $contact->billing_phone = $request->input('billing_phone');
        $contact->billing_street = $request->input('billing_street');
        $contact->billing_city = $request->input('billing_city');
        $contact->billing_state = $request->input('billing_state');
        $contact->billing_state_code = $request->input('billing_state_code');
        $contact->billing_zip = $request->input('billing_zip');  
        $contact->billing_country = $request->input('billing_country');  
        $contact->billing_landmark = $request->input('billing_landmark');  
        $contact->gst_number = $request->input('gst_number'); 
        if($contact->use_billing_address == 'Same as Account Billing Address' && $request->input('com_gst_number') !=null && $request->input('com_gst_number') !='')
        {
            $gstCheck = PaymentProcess::getGstinData($request->input('com_gst_number'));
            if(empty($gstCheck)||(!empty($gstCheck) && ($gstCheck['status_cd'] == 0)))
            {
                $error = array("com_gst_number"=>array($gstCheck['error']['message']));
                return response()->json($error, 400);
            }
            else if(!empty($gstCheck) && ($gstCheck['status_cd'] == 1))
            {
                $sez_unit_company = ($gstCheck['data']['dty']=='SEZ Unit')?1:0;
            } 
        } 
        elseif($contact->use_billing_address == 'Same as Contact Mailing Address' && $request->input('gst_number') !=null && $request->input('gst_number') !='')
        {           
            $gstCheck = PaymentProcess::getGstinData($request->input('gst_number')); 
            if(empty($gstCheck)||(!empty($gstCheck) && ($gstCheck['status_cd'] == 0)))
            {
                $error = array("gst_number"=>array($gstCheck['error']['message']));
                return response()->json($error, 400);
            }
            else if(!empty($gstCheck) && ($gstCheck['status_cd'] == 1))
            {
                $contact->sez_unit_contact = ($gstCheck['data']['dty']=='SEZ Unit')?1:0;
            } 
        }
        if($contact->use_billing_address == 'Same as Account Billing Address' && $request->input('com_billing_code') !=null && $request->input('com_billing_state_code') !=null)
        {
            $pinCheck = $this->_checkPinCode($request->input('com_billing_state_code'),$request->input('com_billing_code'));
            $error = array("com_billing_code"=>array("Invalid Pin Code"));
            if(!$pinCheck)
            return response()->json($error, 400);
        }
        if($contact->use_billing_address == 'Same as Contact Mailing Address' && $request->input('mailing_state_code') !=null && $request->input('mailing_zip') !=null)
        {
            $pinCheck = $this->_checkPinCode($request->input('mailing_state_code'),$request->input('mailing_zip'));
            $error = array("mailing_zip"=>array("Invalid Pin Code"));
            if(!$pinCheck)
            return response()->json($error, 400);
        }
        if($contact->use_billing_address == 'Custom Billing Address' && $request->input('billing_state_code') !=null && $request->input('billing_zip') !=null)
        {
            $pinCheck = $this->_checkPinCode($request->input('billing_state_code'),$request->input('billing_zip'));
            $error = array("billing_zip"=>array("Invalid Pin Code"));
            if(!$pinCheck)
            return response()->json($error, 400);
        }
        $contact->save();
        if($contact->use_billing_address == 'Same as Account Billing Address' && $request->input('com_id') !=null && $request->input('com_id') !='')
        {
            $comapny = Company::find($request->input('com_id'));
            $comapny->billing_street = $request->input('com_billing_street');
            $comapny->billing_landmark = $request->input('com_billing_landmark');
            $comapny->billing_city = $request->input('com_billing_city');
            $comapny->billing_state_ut = $request->input('com_billing_state_ut');
            $comapny->billing_state_code = $request->input('com_billing_state_code');
            $comapny->billing_code = $request->input('com_billing_code');
            $comapny->billing_country = $request->input('com_billing_country');
            $comapny->gst_number = $request->input('com_gst_number');
            $comapny->sez_unit_company = $sez_unit_company;
            $comapny->save();
            Helper::sendZohoCrmData($comapny,'COMPANY-EDIT');    
        }
        Helper::sendZohoCrmData($contact,'CONTACT-EDIT');
        return response()->json($contact);
    }

    public function getContact($id)
    {
        $contact = DB::table('contact')->select(DB::raw('contact.*,company.company_name,company.gst_number as com_gst_number,company.billing_street as com_billing_street,company.billing_landmark as com_billing_landmark,company.billing_city as com_billing_city,company.billing_state_ut as com_billing_state_ut,
        company.billing_state_code as com_billing_state_code,company.billing_code as com_billing_code,company.billing_country as com_billing_country,company.id as com_id,company.gst_number as com_gst_number'))->leftJoin('company','company.zoho_company_id', '=','contact.company_id')->where('contact.id', '=',$id)->first();
        return response()->json($contact);
    }

   
}
