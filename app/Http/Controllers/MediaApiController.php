<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Media;
use Carbon\Carbon; 
use DB;
use Helper;
use App\Models\Branch;
use App\Models\Stage;
use App\Models\MediaDirectory;
use App\Models\MediaOut;
use App\Models\UserAssign;
use App\Models\MediaWiping;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Quotation;
use App\Models\FinalPrice;
use App\Models\User;
use PaymentProcess;
use App\Models\ServiceRequest;
use App\Models\ServicePayment;

class MediaApiController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['preInspection','accountSave','contactSave','quoteUpdate','JobOwnerChange','DealNameChange','getMediaPrice','addAnalysisCharges']]);
    }

    public function addAnalysisCharges()
    {
        $res = Helper::getMimsCrmAuthToken();
        $MsgArray = array();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('firstname','email','phone','address','city','state','state_code','zipcode','order_no','branch_id'
                                         ,'zoho_payment_id','payment_item','total_amount','payment_status','payment_txnid','payment_timestamp','payment_channel','payment_type','payment_category','payment_mode','existing_payment');
                $validaion = Helper::validationMissingKey($required_fields,$res['data']);
                if(count($validaion) == 0)
                {
                    $MsgArray['status'] = "ERROR";
                    $validateGstdata = '';
                    $pinCheck = $this->_checkPinCode($res['data']['state_code'],$res['data']['zipcode']);
                    if(!$pinCheck)
                    {
                        $MsgArray['msg'] = "Invalid Pin Code";
                        return $MsgArray;
                    }
                    if($res['data']['payment_status'] !='Waiting for Invoice')
                    {
                        $MsgArray['msg'] = "Invoice can be generated only for payments completed and waiting for Invoice";
                        return $MsgArray;
                    }
                    if (array_key_exists("gst_no",$res['data']) && !empty($res['data']['gst_no']))
                    {
                        $gstTwoDigit = mb_substr($res['data']['gst_no'], 0, 2);
                        if($gstTwoDigit != $res['data']['state_code'])
                        {
                            $MsgArray['msg'] = "Not a valid GSTIN/UIN for the selected state.";
                            return $MsgArray;
                        }
                        else
                        {
                             $validateGstdata = PaymentProcess::getGstinData($res['data']['gst_no']);
                            if(empty($validateGstdata) || (!empty($validateGstdata) && ($validateGstdata['status_cd'] == 0)) ||  $validateGstdata['data']['sts'] != 'Active')
                            {
                                $MsgArray['msg'] = empty($validateGstdata)?"Invalid GSTIN / UID. Please check GSTIN / UID and try again.":$validateGstdata['error']['message'];
                                return $MsgArray;
                            }
                        }                        
                    }
                   if((empty($validateGstdata) && $res['data']['tax_applicable'] !=1) || 
                     (!empty($validateGstdata) && $res['data']['tax_applicable'] !=1 &&  strtoupper($validateGstdata['data']['dty']) !='SEZ UNIT'))
                   {
                    $MsgArray['msg'] = "Without tax invoice is applicable only for SEZ customers. Please check GSTIN for SEZ validity.";
                    return $MsgArray;
                   }
                   else
                   {
                    $insertdata = $this->saveInvoice($res['data'],$validateGstdata);
                    $MsgArray['msg'] = $insertdata[1];
                    $MsgArray['status'] = $insertdata[0];
                    $MsgArray['data'] = $insertdata[2];
                    return $MsgArray;
                   }

                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = 'Required field '.implode(', ',$validaion)." is empty";
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Input Fields Not Found";
            }

        }
        unset($res['Auth']);
        unset($res['data']);
        return $res;
    }

    protected function saveInvoice($data,$gstData)
    {
        $request = new ServiceRequest();
        $branch = Branch::where('zoho_branch_id',$data['branch_id'])->first();
        $request->firstname = $data['firstname'];
        $request->email = $data['email'];
        $request->phone = $data['phone'];
        $request->address = $data['address'];
        $request->city = $data['city'];
        $request->state = $data['state'];
        $request->state_code = $data['state_code'];
        $request->zipcode = $data['zipcode'];
        $request->plan_type = $data['plan_type']; 
        $request->gst_no = $data['gst_no'];
        $request->landmark = $data['landmark'];
        $request->sez = (!empty($gstData) && strtoupper($gstData['data']['dty']) =='SEZ UNIT')?'1':'0';
        $request->branch_id = $branch->id;
        $request->order_no = $data['order_no'];
        $request->submit_timestamp = date('Y-m-d H:i:s');
        $request->save();
        $servicePayment = new ServicePayment();
        $servicePayment->request_id = $request->id;
        $servicePayment->zoho_payment_id = $data['zoho_payment_id'];
        $servicePayment->payment_item = $data['payment_item'];
        $servicePayment->payment_status = 'success';
        $servicePayment->payment_txnid  = $data['payment_txnid'];
        $servicePayment->payment_timestamp =date('Y-m-d H:i:s', strtotime($data['payment_timestamp']));
        $servicePayment->payment_channel = $data['payment_channel'];
        $servicePayment->payment_type = $data['payment_type'];       
        $servicePayment->payment_category = $data['payment_category'];
        $servicePayment->payment_mode = $data['payment_mode'];
        $servicePayment->existing_payment = $data['existing_payment'];
        $servicePayment->tax_rate = $data['tax_rate'];
        if($data['tax_applicable'] != 1 && !empty($request->gst_no) && $request->sez == 1)
        {
            $servicePayment->total_tax = 0;
            $servicePayment->total_amount = $data['total_amount'];
            $servicePayment->payment_amount = $data['total_amount'];
       
        }
        else
        {
            $taxAmount = ($data['total_amount'] * $data['tax_rate']) / 100;
            $servicePayment->total_tax = round($taxAmount,2);
            $servicePayment->total_amount =$data['total_amount']; 
            $servicePayment->payment_amount = $data['total_amount'] + $taxAmount;
        }
        $servicePayment->save();
        $PaymentData = ServicePayment::join('service_request', 'service_request.id', '=', 'service_payments.request_id')
        ->select('service_request.*','service_payments.id as payment_id','service_payments.total_amount','service_payments.total_tax','service_payments.tax_rate','service_payments.payment_amount','service_payments.payment_status','service_payments.payment_txnid')
        ->where('service_payments.request_id', $request->id)->orderBy('service_payments.id','desc')->first();
        $PaymentData['tax_applicable'] = $data['tax_applicable'];
        $PaymentData['po_number'] = null;
        $PaymentData['amount'] = $PaymentData['payment_amount'];
        $PaymentData['branch'] = $this->_getBranchName($PaymentData['branch_id']);
        $ServiceInvoice = PaymentProcess::GenrateInvoice($PaymentData);
        $invdata = array('invoice_no'=>$ServiceInvoice->invoice_no,'payment_amount'=>$servicePayment->payment_amount,
                  'total_tax'=>$servicePayment->total_tax,'inv_url'=>'','sez_invoice'=>($request->sez ==1)?true:false,"Invoice_Status"=>'','invoice_date');
        if($ServiceInvoice->irn_status ==0)
        {
            $invdata['invoice_date'] = date("Y-m-d", strtotime($ServiceInvoice->created_on));
            $invdata['Invoice_Status'] = 'Invoice Generated Without IRN';
            return ['SUCCESS','Invoice Generated but IRN Not Generated',$invdata];
        }
        elseif($ServiceInvoice->irn_status !=0)
        {
            $invdata['Invoice_Status'] = 'Invoice Generated';
            $invdata['invoice_date'] = date("Y-m-d", strtotime($ServiceInvoice->created_on));
            $invdata['inv_url'] = env('MIMS_BASE_URL')."view-invoice/".$ServiceInvoice->id."/".$ServiceInvoice->request_id;
            return ['SUCCESS','Invoice Generated',$invdata];
        }
        return $ServiceInvoice;
    }

    public function getMediaPrice()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('media_type','media_capacity','tampered_status');
                if (array_key_exists("media_type",$res['data']) && array_key_exists("media_capacity",$res['data']) && array_key_exists("tampered_status",$res['data']))
                {
                    $validaion = Helper::validationInput($required_fields,$res['data']);
                    if(count($validaion) == 0)
                    {
                        $capacity = str_replace(' ','',$res['data']['media_capacity']);
                        $capaArray = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$capacity); 
                         if(count($capaArray) >= 2)
                         {
                                $size = array('KB','MB','GB','TB');
                                if(in_array(strtoupper($capaArray[1]),$size))
                                {
                                    $res['data']['media_capacity'] = $capaArray[0]." ".$capaArray[1];
                                    $res['data']['tampered_status'] = ($res['data']['tampered_status'] =='Yes')?'TAMPERED':'';
                                    $result = $this->setRecoveryPrice($res['data']);
                                    $price= array('amount'=>$result[0]['amount'],'advance_percent'=>$result[0]['advance_percent'],'tax_rate'=>$result[0]['tax_rate'],'advance_amount'=>$result[0]['advance_amount']);
                                    $tmp = array();
                                    $tmp['status'] = 'SUCCESS';
                                    $tmp['data'] = $price;
                                    return $tmp;
                                }
                                else
                                {
                                    $res['status'] = "ERROR";
                                    $res['msg'] = "Please verify Media Type, Capacity and Tampered status field and try again.";
                                }
                         }
                         else
                         {
                            $res['status'] = "ERROR";
                            $res['msg'] = "Please verify Media Type, Capacity and Tampered status field and try again.";
                         }
                       
                    }
                    else{
                        $res['status'] = "ERROR";
                        $res['msg'] = 'Required field '.implode(',',$validaion)." is empty";
                    }
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Input Fields Not Found";
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Input Fields Not Found";
            }
        }
        unset($res['Auth']);
        unset($res['data']);
        return $res;
    }

    public function DealNameChange()
    {
        $res = Helper::getMimsCrmAuthToken();
        
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            $deal_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'deal_id'));
            $deal_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'deal_name'));
            if($deal_id != null && $deal_id != '')
            {
                $media = Media::where('deal_id',$deal_id)->first();
                if($media !=null && $media !='')
                {
                    $media->deal_name = $deal_name;
                    $media->save();
                    $res['status'] = "SUCCESS";
                    $res['msg'] = "Record Updated";
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Record Not Found";
                }
            }
        }
        return $res;
    }

    public function JobOwnerChange()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            $deal_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'deal_id'));
            $Owner_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'Owner_id'));
            if($deal_id != null && $Owner_id != null)
            {
                $media = Media::where('deal_id',$deal_id)->first();
                $user = User::where('zoho_user_id',$Owner_id)->first();
                if($media !=null && $media !='' && $user !=null && $user !='')
                {
                    $media->ise_user_id = $user->id;
                    $media->save();
                    $res['status'] = "SUCCESS";
                    $res['msg'] = "Record Updated";
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Record Not Found";
                }
            }
        }
        return $res;
    }

    public function quoteUpdate()
    {
        $res = Helper::getMimsCrmAuthToken();
        
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            $status = Helper::sanitize_input(Helper::arrayIndex($res['data'],'quote_status'));
            $quote_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'quote_id'));
            $quotation = Quotation::where('zoho_quotation_id',$quote_id)->first();
            if($quotation !=null && $quotation !='')
            {
                $media = Media::find($quotation->media_id);
                $media['quotationNumber']  = $quotation->quotation_no;
                 if($status ==null || $status == '')
                 {
                        $mail = $this->_sendmailQuotation($media,'Rejected');
                        $quotation->status = 'Reject';
                        $quotation->save();
                 }
                 else if($status !=null && $status == 'Negotiation')
                 {
                    $mail = $this->_sendmailQuotation($media,'Approved');
                 }
                 else if($status !=null && $status == 'Confirmed')
                 {
                    $finalPrice = FinalPrice::where('media_id',$quotation->media_id)->first();
                    if(($finalPrice->paid_amount ==null || $finalPrice->paid_amount == 0) && $finalPrice->quotation_id == null )
                    {
                        $finalPrice->total_amount = $quotation->new_total_amount;
                        $finalPrice->tax_amount = $quotation->tax_amount;
                        $finalPrice->balance_amount = $quotation->new_total_amount;
                        $finalPrice->quotation_id = $quotation->id;
                        $finalPrice->base_amount = round($quotation->base_amount - $quotation->discount_amount);
                        $finalPrice->save();
                        $quotation->status = "Confirmed";
                        $quotation->save();
                        $mail = $this->_sendmailQuotation($media,'Confirmed');
                        $res['msg'] = 'Update Price';
                    }
                 }
            }
        }
        return $res;
    }
	
	public function contactSave()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            //$required_fields = array('zoho_contact_id','first_name','last_name','email','company_id','branch_id','mailing_street','mailing_city','mailing_state','mailing_zip','mailing_country','mailing_region','other_street','other_city','other_state','other_zip','other_country','description');
            $contact = Contact::where('zoho_contact_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_contact_id')))->first();
            if($contact == null || $contact =='')
              $contact = new Contact();
              $contact->zoho_contact_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_contact_id'));
              $contact->first_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'first_name'));
              $contact->last_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'last_name'));
              $contact->email = Helper::sanitize_input(Helper::arrayIndex($res['data'],'email'));
              $contact->mobile = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mobile'));
              $contact->company_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'company_id'));
              $contact->branch_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'branch_id'));
              $contact->mailing_street = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_street'));
              $contact->mailing_region = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_region'));
              $contact->mailing_city = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_city'));
              $contact->mailing_state_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_state_code'));
              $contact->mailing_country = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_country'));
              $contact->mailing_state_ut = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_state_ut'));
              $contact->mailing_zip = Helper::sanitize_input(Helper::arrayIndex($res['data'],'mailing_zip'));
              $contact->use_billing_address = Helper::sanitize_input(Helper::arrayIndex($res['data'],'use_billing_address'));
              $contact->billing_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_name'));
              $contact->billing_email = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_email'));
              $contact->billing_phone = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_phone'));
              $contact->billing_street = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_street'));
              $contact->billing_city = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_city'));
              $contact->billing_state = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_state'));
              $contact->billing_state_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_state_code'));
              $contact->billing_zip = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_zip'));
              $contact->gst_number = Helper::sanitize_input(Helper::arrayIndex($res['data'],'gst_number'));
              $contact->customer_name = $contact->first_name." ".$contact->last_name;
              $contact->save();
        }
         return $res;
    }

    public function accountSave()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
           // $required_fields = array('zoho_company_id','company_name','gst_number','billing_street','billing_city','billing_state','billing_code','billing_country','shipping_street','shipping_city','shipping_state','shipping_code','shipping_country','description_information');
            $company = Company::where('zoho_company_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_company_id')))->first();
            if($company == null || $company =='')
              $company = new Company();
              $company->zoho_company_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_company_id'));
              $company->company_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'company_name'));
              $company->gst_number = Helper::sanitize_input(Helper::arrayIndex($res['data'],'gst_number'));
              $company->billing_street = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_street'));
              $company->billing_landmark = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_landmark'));
              $company->billing_city = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_city'));
              $company->billing_state_ut = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_state_ut'));
              $company->billing_state_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_state_code'));
              $company->billing_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_code'));
              $company->billing_country = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_country'));
              $company->shipping_street = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_street'));
              $company->shipping_landmark = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_landmark'));
              $company->shipping_city = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_city'));
              $company->shipping_state_ut = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_state_ut'));
              $company->shipping_state_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_state_code'));
              $company->shipping_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_code'));
              $company->shipping_country = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_country'));
              $company->branch_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'branch_name'));
              $company->save();
        }
         return $res;
    } 

    public function preInspection(Request $request)
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
            $required_fields = array('service_type','service_mode','media_type','zoho_id','client_id','branch_id','zoho_user');
            $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
            if(count($res['validaion']) ==0 )
            {
                $update = false;
                $media = Media::where('zoho_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_id')))->first();
                if($media == null || $media =='')
                {
                    $media = new Media();
                    $update = true;
                }                
                $media->service_type = Helper::sanitize_input(Helper::arrayIndex($res['data'],'service_type'));
                $media->media_problem = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_problem'));
                $media->service_mode = Helper::sanitize_input(Helper::arrayIndex($res['data'],'service_mode'));
                $media->media_type = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_type'));
                $media->media_make = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_make'));
                $media->media_capacity = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_capacity'));
                $media->media_model = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_model'));
                $media->media_serial = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_serial'));
                $media->zoho_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_id'));
                $media->peripherals_details = Helper::sanitize_input(Helper::arrayIndex($res['data'],'peripherals_details'));
                $media->media_casing = Helper::sanitize_input(Helper::arrayIndex($res['data'],'peripherals_with_media'));
                $media->media_status = Helper::sanitize_input(Helper::arrayIndex($res['data'],'media_status'));
                $media->important_data = Helper::sanitize_input(Helper::arrayIndex($res['data'],'important_data'));
                $media->customer_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'client_id'));
                $media->deal_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'deal_id'));
                $media->deal_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'deal_name'));
                $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                $zoho_user_id =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user_id'));
                if($zoho_user_id != null && $zoho_user_id !='')
                {
                        $user = User::where('zoho_user_id',$zoho_user_id)->first();
                        if($user != null && $user !='')
                            $media->ise_user_id = $user->id;
                }
                
                $remarks = (!empty($zoho_user) ? "Case added by Zoho user ".$zoho_user : "Case added by Zoho user");
                if($update == true)
                {
                    $media->pre_due_date = $this->_getDueDate(date('Y-m-d'),1);
                    $media->stage = 1;
                    $branch = Branch::where('zoho_branch_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'branch_id')))->first();
                    $media->branch_id = $branch->id;
                    $media->created_on = Carbon::now()->toDateTimeString();
                    $media->save();   
                    Helper::_insertMediaHistory($media,"edit",'PRE-ANALYSIS',$zoho_user,$remarks);
                    $res['msg'] = "DATA INSERTED SUCCESSFULLY";
                    $res['data']['Pre_Inspection_Due_Date'] = $media->pre_due_date;
                }
                else if($update == false && $media->stage == 1)
                {
                    $media->save();  
                    $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                    Helper::_insertMediaHistory($media,"edit",'PRE-ANALYSIS',$zoho_user,$remarks); 
                    $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                    $res['data']['Pre_Inspection_Due_Date'] = $media->pre_due_date;
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Error!! Pre Inspection already done for this case";
                }        
            }
            else{
                $res['status'] = "ERROR";
                $res['msg'] = "Validation Error";
            }
        }
        else
        {
            $res['status'] = "ERROR";
            $res['msg'] = "Not a valid request!";
        }
        
    }
    //unset($res['data']);
    unset($res['Auth']);
        return $res;
    }
   
}