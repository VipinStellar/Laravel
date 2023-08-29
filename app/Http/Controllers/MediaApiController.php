<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Media;
use App\Models\CustomerDetail;
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

class MediaApiController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['preInspection','mediaIn','changeStatus','extensionUpdate','mediaDlConfirm','requestMediaOut','requestMediaWiping','accountSave']]);
    }

    public function accountSave()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            $required_fields = array('zoho_company_id','company_name','gst_number','billing_street','billing_city','billing_state','billing_code','billing_country','shipping_street','shipping_city','shipping_state','shipping_code','shipping_country','description_information');
            $company = Company::where('zoho_company_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_company_id')))->first();
            if($company == null || $company =='')
              $company = new Company();
              $company->zoho_company_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_company_id'));
              $company->company_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'company_name'));
              $company->gst_number = Helper::sanitize_input(Helper::arrayIndex($res['data'],'gst_number'));
              $company->billing_street = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_street'));
              $company->billing_city = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_city'));
              $company->billing_state = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_state'));
              $company->billing_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_code'));
              $company->billing_country = Helper::sanitize_input(Helper::arrayIndex($res['data'],'billing_country'));
              $company->shipping_street = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_street'));
              $company->shipping_city = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_city'));
              $company->shipping_state = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_state'));
              $company->shipping_code = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_code'));
              $company->shipping_country = Helper::sanitize_input(Helper::arrayIndex($res['data'],'shipping_country'));
              $company->description_information = Helper::sanitize_input(Helper::arrayIndex($res['data'],'description_information'));
              $company->save();
        }
         return $res;
    }

    public function requestMediaWiping()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('zoho_job_id','zoho_user','Wiping_Check_Mark');
                $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
                if(count($res['validaion']) ==0 )
                {
                    $media = Media::where('zoho_job_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_job_id')))->first();
                    if($media == null || $media =='')
                    {
                        $res['status'] = "ERROR";
                        $res['msg'] = "Record Not Found";
                    }
                    else
                    {
                            $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                            $user     =  UserAssign::where('media_id',$media->id)->orderBy('id','asc')->first();
                            $wipe     =  new MediaWiping();
                            $wipe->user_id = $user->user_id;
                            $wipe->branch_id = $media->branch_id;
                            $wipe->media_id = $media->id;
                            $wipe->request_wiping_date = Carbon::now()->toDateTimeString();
                            $wipe->request_type = "CRM";
                            $wipe->expected_wiping_date  = $this->_getDueDate(date('Y-m-d'),7);
                            $wipe->save();                    
                        $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                        Helper::_insertMediaHistory($media,"edit",'WIPING',$zoho_user,$remarks);
                        $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                        $res['status'] = 'SUCCESS';
                    }
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Validation Error";
                    unset($res['data']);
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Not a valid request!";
               unset($res['data']);
            }
        }
        unset($res['Auth']);
        return $res;
    }

    public function requestMediaOut()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('zoho_job_id','zoho_user','Request_for_Media_Out');
                $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
                if(count($res['validaion']) ==0 )
                {
                    $media = Media::where('zoho_job_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_job_id')))->first();
                    if($media == null || $media =='')
                    {
                        $res['status'] = "ERROR";
                        $res['msg'] = "Record Not Found";
                    }
                    else
                    {
                        $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                        $mediaout  = new MediaOut();
                        $mediaout->media_id = $media->id;
                        $mediaout->request_type = Helper::sanitize_input(Helper::arrayIndex($res['data'],'Request_for_Media_Out'));
                        $mediaout->remarks = "Request For Media Out";
                        $mediaout->request_date = Carbon::now()->toDateTimeString();
                        $mediaout->user_id_from = $zoho_user." CRM USER";
                        $mediaout->user_id_to = null;
                        $mediaout->save();  
                        $media->crm_request_media_out = "1";
                        $media->save();                     
                        $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                        Helper::_insertMediaHistory($media,"edit",'MEDIA-OUT',$zoho_user,$remarks);
                        $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                        $res['status'] = 'SUCCESS';
                    }
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Validation Error";
                    unset($res['data']);
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Not a valid request!";
               unset($res['data']);
            }
        }
        unset($res['Auth']);
        return $res;
        
    }

    public function mediaDlConfirm()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('zoho_job_id','zoho_user','Mode_of_Data_Verification','Mode_of_Data_verification_approval','Data_Recovery_Results','Data_Delivery_Mode');
                $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
                if(count($res['validaion']) ==0 )
                {
                    $media = Media::where('zoho_job_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_job_id')))->first();
                    if($media == null || $media =='')
                    {
                        $res['status'] = "ERROR";
                        $res['msg'] = "Record Not Found";
                    }
                    else
                    {
                        $media->stage = 12;
                        $media->save();
                        $dl = MediaDirectory::where('media_id',$media->id)->first();
                        $dl->copyin = Helper::sanitize_input(Helper::arrayIndex($res['data'],'Data_Delivery_Mode'));
                        $dl->dl_status = "Yes";
                        $dl->copyin_details = '[{"media_sn":"'.Helper::sanitize_input(Helper::arrayIndex($res['data'],'Client_Serial_No')).'","media_model":"'.Helper::sanitize_input(Helper::arrayIndex($res['data'],'Client_Make_Model')).'","capacity":"","media_make":"","inventry_num":null}]';
                        $dl->save();
                        $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                        $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                        Helper::_insertMediaHistory($media,"edit",'DIRECTORY-LISTING',$zoho_user,$remarks);
                        $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                        $res['status'] = 'SUCCESS';
                    }
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Validation Error";
                    unset($res['data']);
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Not a valid request!";
               unset($res['data']);
            }
        }
        unset($res['Auth']);
        return $res;
    }

    public function extensionUpdate()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('zoho_job_id','zoho_user','extension_day','extension_approval');
                $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
                if(count($res['validaion']) ==0 )
                {
                    $media = Media::where('zoho_job_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_job_id')))->first();
                    if($media == null || $media =='')
                    {
                        $res['status'] = "ERROR";
                        $res['msg'] = "Record Not Found";
                    }
                    else
                    {
                            $extStatus = Helper::sanitize_input(Helper::arrayIndex($res['data'],'extension_approval'));
                            $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                            if($media->stage == 4 || $media->stage == 5 || $media->stage == 6)
                            {
                                    $assessment_due_date = ($media->assessment_due_date == null)?date('Y-m-d'):$media->assessment_due_date;
                            }
                            else
                            {
                                $duedate = ($media->due_date == null)?date('Y-m-d'):$media->due_date;
                            }
                            
                            if($extStatus =='Approved' && $media->extension_approve == 1)
                            {
                                if($media->stage == 4 || $media->stage == 5 || $media->stage == 6)
                                $media->assessment_due_date = $this->_getDueDate($assessment_due_date,$media->extension_day);
                                else
                                $media->due_date = $this->_getDueDate($duedate,$media->extension_day);
                            }     
                            $media->extension_approve = 0;                       
                            $media->save();
                            $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                            Helper::_insertMediaHistory($media,"edit",'EXTENSION-DAY',$zoho_user,$remarks,$extStatus);
                            $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                            $res['status'] = 'SUCCESS';
                            $res['data']['Recovery_Due_Date'] = $media->due_date;
                            $res['data']['Assessment_Due_Date'] = $media->assessment_due_date;

                    }
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Validation Error";
                    unset($res['data']);
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Not a valid request!";
               unset($res['data']);
            }
        }
        unset($res['Auth']);
        return $res;
    }

    public function changeStatus()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            $res['data']['Recovery_Due_Date'] = null;
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('zoho_job_id','job_id','zoho_user','job_status');
                $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
                if(count($res['validaion']) ==0 )
                {
                    $media = Media::where('zoho_job_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_job_id')))->first();
                    if($media == null || $media =='')
                    {
                        $res['status'] = "ERROR";
                        $res['msg'] = "Record Not Found";
                    }
                    else
                    {
                            $job_status = Helper::sanitize_input(Helper::arrayIndex($res['data'],'job_status'));
                            $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                            if($job_status == 'Confirm')
                            {
                              $media->due_date = $this->_getDueDate(date('Y-m-d'),$media->required_days);
                              $res['data']['Recovery_Due_Date'] = $media->due_date;
                            }
                           if($job_status == 'Confirm')
                                $job_status = 'Confirmed';
                            else if($job_status == 'Not Confirm')
                                $job_status = 'Not Confirmed';
                            $stage = Stage::where('stage_name',$job_status)->first();
                            $media->stage = $stage->id;
                            $media->save();
                            $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                            Helper::_insertMediaHistory($media,"edit",'INSPECTION',$zoho_user,$remarks);
                            $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                            $res['status'] = 'SUCCESS';

                    }
                }
                else
                {
                    $res['status'] = "ERROR";
                    $res['msg'] = "Validation Error";
                    unset($res['data']);
                }
            }
            else
            {
                $res['status'] = "ERROR";
                $res['msg'] = "Not a valid request!";
               unset($res['data']);
            }
        }
        unset($res['Auth']);
        return $res;
    }

    public function mediaIn()
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
                $required_fields = array('zoho_id','job_id','zoho_user','zoho_job_id');
                $res['validaion'] = Helper::validationInput($required_fields,$res['data']);
                if(count($res['validaion']) ==0 )
                {
                    $media = Media::where('zoho_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_id')))->first();
                    if($media == null || $media =='')
                    {
                        $res['status'] = "ERROR";
                        $res['msg'] = "Please send the record for Pre Inspection";
                    }
                    else
                    {
                        if($media->job_id == null)
                        {
                            $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                            $media->assessment_due_date = $this->_getDueDate(date('Y-m-d'),2);
                            $media->job_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'job_id'));
                            $media->stage = 4;
                            $media->zoho_job_id = Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_job_id'));
                            $media->save();
                            $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                            Helper::_insertMediaHistory($media,"edit",'INSPECTION',$zoho_user,$remarks);
                            $res['msg'] = "DATA UPDATED SUCCESSFULLY";
                            $res['status'] = 'SUCCESS';
                            $res['data']['Assessment_Due_Date'] = $media->assessment_due_date;
                        }
                        else
                        {
                            $res['status'] = "ERROR";
                            $res['msg'] = "Error!! Inspection already done for this case";
                        }
                    }
                }
                else
                {
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
        unset($res['Auth']);
        return $res;
    }

    public function preInspection(Request $request)
    {
        $res = Helper::getMimsCrmAuthToken();
        if($res['Auth'] == '1' && $res['status'] == 'SUCCESS')
        {
            if($res['data'] != null && $res['data'] !='')
            {
            $required_fields = array('service_type','service_mode','media_type','media_make','media_capacity','media_model','media_serial','zoho_id','client_name','branch_id','zoho_user');
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
                $zoho_user =  Helper::sanitize_input(Helper::arrayIndex($res['data'],'zoho_user'));
                $remarks = (!empty($zoho_user) ? "Case added by Zoho user ".$zoho_user : "Case added by Zoho user");
                if($update == true)
                {
                    $media->pre_due_date = $this->_getDueDate(date('Y-m-d'),1);
                    $media->stage = 1;
                    $cus = new CustomerDetail();
                    $cus->customer_name = Helper::sanitize_input(Helper::arrayIndex($res['data'],'client_name'));
                    $cus->save();
                    $branch = Branch::where('zoho_branch_id', Helper::sanitize_input(Helper::arrayIndex($res['data'],'branch_id')))->first();
                    $media->customer_id = $cus->id;
                    $media->branch_id = $branch->id;
                    $media->created_on = Carbon::now()->toDateTimeString();
                    $media->save();   
                    Helper::_insertMediaHistory($media,"edit",'PRE-ANALYSIS',$zoho_user,$remarks);
                    $res['msg'] = "DATA INSERTED SUCCESSFULLY";
                }
                else if($update == false && $media->stage == 1)
                {
                    $media->save();  
                    $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
                    Helper::_insertMediaHistory($media,"edit",'PRE-ANALYSIS',$zoho_user,$remarks); 
                    $res['msg'] = "DATA UPDATED SUCCESSFULLY";
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
    unset($res['data']);
    unset($res['Auth']);
        return $res;
    }



   
}