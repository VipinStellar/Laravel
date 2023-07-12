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
class MediaApiController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['preInspection']]);
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