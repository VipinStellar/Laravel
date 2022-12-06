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

class MediaApiController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['mediaAnalysis','mediaAssessment','changeStatus']]);
    }

    function arrayIndex($rqst,$key)
    {
        if(isset($rqst[$key]))
           return $rqst[$key];
        else
            return null;
    }

    public function changeStatus(Request $request)
    {
        $formError = 0;
        $response = array();
        $response['status'] = 'ERROR';
        $response['msg'] = '';
        $checkApi = $this->validApiKey($response,$formError);
        if($checkApi[0]['msg'] !='' && $checkApi[1] != 0)
       {
            return $checkApi[0];
       }
       else if($checkApi[0]['msg'] =='' && $checkApi[1] == 0)
       {
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
            if(strcasecmp($contentType, 'application/x-www-form-urlencoded') == 0 && $formError == 0){
                $rqst = $_POST;
            }elseif(strcasecmp($contentType, 'application/json') == 0 && $formError == 0){
                $content = trim(file_get_contents("php://input"));
                $rqst = json_decode($content, true);
            }
            if(empty($rqst) && $formError == 0){
                $formError = 1;
                $response['msg'] = 'Required data is empty';
            }
            $required_fields = array('zoho_id','job_id','zoho_user','stage');
            $fields = '';
            foreach($required_fields as $field) {
                if (isset($rqst[$field]) && empty($rqst[$field])) {
                    $formError = 1;
                    $fields .= $field.", ";
                    $response['msg'] = 'Required field '.$fields. ' is empty';
                }
            }
            if($formError == 0 && !empty($rqst['job_id'])){
                $media = Media::where('zoho_id', $rqst['zoho_id'])->where('job_id',$rqst['job_id'])->first();
                if($media != '' || $media != null )
                 {
                    $media->stage = $rqst['stage'];
                    $media->save();
                    $history = $this->_insertHistory("Media Update by Zoho user",$media->id,'assessment',$rqst['zoho_user'],$media->stage);
                    $response['status'] = 'SUCCESS';
                    $response['msg'] = 'Media Status Changed';
                 } 
                 else{
                    $response['msg'] = 'Media Not Found';
                 }
           }
          return $response;
       }
    }
    
    public function mediaAssessment(Request $request)
    {
        $formError = 0;
        $response = array();
        $response['status'] = 'ERROR';
        $response['msg'] = '';
        $checkApi = $this->validApiKey($response,$formError);
       if($checkApi[0]['msg'] !='' && $checkApi[1] != 0)
       {
            return $checkApi[0];
       }
       else if($checkApi[0]['msg'] =='' && $checkApi[1] == 0)
       {
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
            if(strcasecmp($contentType, 'application/x-www-form-urlencoded') == 0 && $formError == 0){
                $rqst = $_POST;
            }elseif(strcasecmp($contentType, 'application/json') == 0 && $formError == 0){
                $content = trim(file_get_contents("php://input"));
                $rqst = json_decode($content, true);
            }
            if(empty($rqst) && $formError == 0){
                $formError = 1;
                $response['msg'] = 'Required data is empty';
            }
            $required_fields = array('zoho_id', 'zoho_job_id','job_id','client_name','branch_id');
            $fields = '';
            foreach($required_fields as $field) {
                if (isset($rqst[$field]) && empty($rqst[$field])) {
                    $formError = 1;
                    $fields .= $field.", ";
                    $response['msg'] = 'Required field '.$fields. ' is empty';
                }
            }
            if($formError == 0 && !empty($rqst['job_id'])){
                $saveData =  $this->saveAssessment($rqst);
                return $saveData;
           }
          return $response;
       }
    }

    public function mediaAnalysis(Request $request)
    {
        $formError = 0;
        $response = array();
        $response['status'] = 'ERROR';
        $response['msg'] = '';
        $checkApi = $this->validApiKey($response,$formError);
       if($checkApi[0]['msg'] !='' && $checkApi[1] != 0)
       {
            return $checkApi[0];
       }
       else if($checkApi[0]['msg'] =='' && $checkApi[1] == 0)
       {
           $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
            if(strcasecmp($contentType, 'application/x-www-form-urlencoded') == 0 && $formError == 0){
                $rqst = $_POST;
            }elseif(strcasecmp($contentType, 'application/json') == 0 && $formError == 0){
                $content = trim(file_get_contents("php://input"));
                $rqst = json_decode($content, true);
            }
            if(empty($rqst) && $formError == 0){
                $formError = 1;
                $response['msg'] = 'Required data is empty';
            }
            $required_fields = array('zoho_id', 'media_type','client_name','branch_id');
            $fields = '';
            foreach($required_fields as $field) {
                if (isset($rqst[$field]) && empty($rqst[$field])) {
                    $formError = 1;
                    $fields .= $field.", ";
                    $response['msg'] = 'Required field '.$fields. ' is empty';
                }
            }
            if($formError == 0 && !empty($rqst['zoho_id'])){
                $saveData =  $this->savePreAnalysis($rqst);
                return $saveData;
            }
            else
            {
            $response['msg'] = 'Error!! Media Pickup/Receiving entry is empty for this record.';
            $response['status'] = 'SUCCESS';
            }
            return $response;
       }
    }

    function _insertHistory($remarks,$id,$module,$user,$status)
    {
        $response =array();
        $id = DB::table('media_history')->insertGetId(['media_id' => $id,'added_by' => $user,'action_type'=>'edit','remarks'=>$remarks,'module_type'=>$module,'added_on'=>Carbon::now()->toDateTimeString(),'status'=>$status]);
        if($id){
            $response['msg'] = 'Data Submitted Successfully in MIMS';
            $response['status'] = 'SUCCESS';
        }
        else
        {
            $response['msg'] = 'Error!! Data Not Submitted in MIMS';
            $response['status'] = "ERROR";
        }
        return $response;
    }

    function validApiKey($response,$formError)
    {
        if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0 && $formError == 0){
            $formError = 1;
            $response['msg'] = 'Not a valid request!';
        }
        $API_KEY = isset($_SERVER["HTTP_API_KEY"]) ? trim($_SERVER["HTTP_API_KEY"]) : '';
        if($API_KEY != 'e2c_dase4a3df531e5de0jkly6u65sdeb0c' && $formError == 0){
        $formError = 1;
        $response['msg'] = 'Invalid API KEY';
        }
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if(($contentType != 'application/x-www-form-urlencoded' && $contentType != 'application/json') && $formError == 0){
            $formError = 1;
            $response['msg'] = 'Not a valid content';
        }
        return [$response,$formError];
    }

    function sanitize_input($inputs) {
            $inputs = trim($inputs);
            $inputs = stripslashes($inputs);
            $inputs = htmlspecialchars($inputs);
            return $inputs;
        }

    function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }

    function saveAssessment($rqst)
    {
        $dataLog = array();
        $zoho_id = $this->sanitize_input($this->arrayIndex($rqst,'zoho_id'));
        $zoho_job_id = $this->sanitize_input($this->arrayIndex($rqst,'zoho_job_id'));
        $job_id = $this->sanitize_input($this->arrayIndex($rqst,'job_id'));
        $job_status = (!empty($this->arrayIndex($rqst,'job_status')) ? $this->sanitize_input($this->arrayIndex($rqst,'job_status')) : 'Waiting For Assessment');
        $client_name = $this->sanitize_input($this->arrayIndex($rqst,'client_name'));
        $media_type = $this->sanitize_input($this->arrayIndex($rqst,'media_type'));
        $media_make = $this->sanitize_input($this->arrayIndex($rqst,'media_make'));
        $media_model = $this->sanitize_input($this->arrayIndex($rqst,"media_model"));
        $media_serial = $this->sanitize_input($this->arrayIndex($rqst,"media_serial"));
        $media_capacity = $this->sanitize_input($this->arrayIndex($rqst,'media_capacity'));
        $media_os = $this->sanitize_input($this->arrayIndex($rqst,'media_os'));
        $tampered_status = $this->sanitize_input($this->arrayIndex($rqst,'tampered_status'));
        $media_condition = $this->sanitize_input($this->arrayIndex($rqst,'media_condition'));
        $media_problem = $this->sanitize_input($this->arrayIndex($rqst,'media_problem'));
        $media_firmware = $this->sanitize_input($this->arrayIndex($rqst,'media_firmware'));
        $peripherals_details = $this->sanitize_input($this->arrayIndex($rqst,'peripherals_details'));
        $important_data = $this->sanitize_input($this->arrayIndex($rqst,'important_data'));
        $encryption_status = $this->sanitize_input($this->arrayIndex($rqst,'encryption_status'));
        $encryption_software = $this->sanitize_input($this->arrayIndex($rqst,'encryption_software'));
        $encryption_version = $this->sanitize_input($this->arrayIndex($rqst,'encryption_version'));
        $encryption_username = $this->sanitize_input($this->arrayIndex($rqst,'encryption_username'));
        $encryption_password = $this->sanitize_input($this->arrayIndex($rqst,'encryption_password'));
        $zoho_user = $this->sanitize_input($this->arrayIndex($rqst,'zoho_user'));
        $branch_id = $this->sanitize_input($this->arrayIndex($rqst,'branch_id'));

        /////Check database 
        $assessment = Media::where('zoho_id', $zoho_id)->first();
        if($assessment == '' || $assessment == null )
        {
            $cus = new CustomerDetail();
            $cus->customer_name = $client_name;
            $cus->save();
            $assessment->job_id = $job_id;
            $assessment->zoho_id = $zoho_id;
            $assessment->zoho_job_id = $zoho_job_id;
            $assessment->media_type = $media_type;
            $assessment->branch_id = $branch_id;
            $assessment->media_os = $media_os;
            $assessment->media_firmware = $media_firmware;
            $assessment->encryption_status = $encryption_status;
            $assessment->job_status = $job_status;
            $assessment->media_make = $media_make;
            $assessment->media_model = $media_model;
            $assessment->media_serial = $media_serial;
            $assessment->media_capacity = $media_capacity;
            $assessment->media_condition = $media_condition;
            $assessment->media_problem = $media_problem;
            $assessment->tampered_status = $tampered_status;
            $assessment->peripherals_details = $peripherals_details;
            $assessment->important_data = $important_data;
            $assessment->encryption_software = $encryption_software;
            $assessment->encryption_version = $encryption_version;
            $assessment->encryption_username = $encryption_username;
            $assessment->encryption_password = $encryption_password;
            $assessment->zoho_user = $zoho_user;
            $assessment->stage = 1;
            $assessment->customer_id = $cus->id;
            $assessment->save();
            $remarksPre = (!empty($zoho_user) ? "Media In by Zoho user ".$zoho_user : "Media In by Zoho user");
            $history = $this->_insertHistory($remarksPre,$assessment->id,'media_in',$zoho_user,$preAnalysis->stage);
            return $history;
        }
        else if($assessment != '' && $assessment != null)
        {
            if($assessment->stage  == "1")
            {
                $dataLog['msg'] = 'Error!! Media Pre-Analysis is not completed for this case.';
                $dataLog['status'] = 'SUCCESS';
                return $dataLog;
            }
            if($assessment->job_id == null)
            {
                $assessment->stage = 3;
            }
            $assessment->job_id = ($job_id == '' || $job_id == null)?$assessment->job_id:$job_id;
            $assessment->zoho_id = ($zoho_id == '' || $zoho_id == null)?$assessment->zoho_id:$zoho_id;
            $assessment->zoho_job_id = ($zoho_job_id == '' || $zoho_job_id == null)?$assessment->zoho_job_id:$zoho_job_id;
            $assessment->media_type = ($media_type == '' || $media_type == null)?$assessment->media_type:$media_type;
            $assessment->branch_id = $branch_id;
            $assessment->media_os =  ($media_os == '' || $media_os == null)?$assessment->media_os:$media_os;
            $assessment->media_firmware = ($media_firmware == '' || $media_firmware == null)?$assessment->media_firmware:$media_firmware;
            $assessment->encryption_status = ($encryption_status == '' || $encryption_status == null)?$assessment->encryption_status:$encryption_status;
            $assessment->job_status = ($job_status == '' || $job_status == null)?$assessment->job_status:$job_status;
            $assessment->media_make = ($media_make == '' || $media_make == null)?$assessment->media_make:$media_make;
            $assessment->media_model =($media_model == '' || $media_model == null)?$assessment->media_model:$media_model;
            $assessment->media_serial =($media_serial == '' || $media_serial == null)?$assessment->media_serial:$media_serial;
            $assessment->media_capacity = ($media_capacity == '' || $media_capacity == null)?$assessment->media_capacity:$media_capacity;
            $assessment->media_condition = ($media_condition == '' || $media_condition == null)?$assessment->media_condition:$media_condition;
            $assessment->tampered_status = ($tampered_status == '' || $tampered_status == null)?$assessment->tampered_status:$tampered_status;
            $assessment->media_problem = ($media_problem == '' || $media_problem == null)?$assessment->media_problem:$media_problem;
            $assessment->peripherals_details = ($peripherals_details == '' || $peripherals_details == null)?$assessment->peripherals_details:$peripherals_details;
            $assessment->important_data = ($important_data == '' || $important_data == null)?$assessment->important_data:$important_data;
            $assessment->encryption_software = ($encryption_software == '' || $encryption_software == null)?$assessment->encryption_software:$encryption_software;
            $assessment->encryption_version = ($encryption_version == '' || $encryption_version == null)?$assessment->encryption_version:$encryption_version;
            $assessment->encryption_username = ($encryption_username == '' || $encryption_username == null)?$assessment->encryption_username:$encryption_username;
            $assessment->encryption_password = ($encryption_password == '' || $encryption_password == null)?$assessment->encryption_password:$encryption_password;
            $assessment->zoho_user = ($zoho_user == '' || $zoho_user == null)?$assessment->zoho_user:$zoho_user;
            $assessment->save();
            $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");
            $history = $this->_insertHistory($remarks,$assessment->id,'assessment',$zoho_user,$assessment->stage);
           // $history = $this->_insertHistory($remarks,$assessment->id,'media_in',$zoho_user,$assessment->stage);
            return $history;

        }
        
        
    }

    function savePreAnalysis($rqst)
    {
        $dataLog = array();
        $zoho_id = $this->sanitize_input($this->arrayIndex($rqst,'zoho_id'));
        $client_name = $this->sanitize_input($this->arrayIndex($rqst,'client_name'));
        $media_type = $this->sanitize_input($this->arrayIndex($rqst,'media_type'));
        $media_make = $this->sanitize_input($this->arrayIndex($rqst,'media_make'));
        $media_model = $this->sanitize_input($this->arrayIndex($rqst,'media_model'));
        $media_serial = $this->sanitize_input($this->arrayIndex($rqst,'media_serial'));
        $media_capacity = $this->sanitize_input($this->arrayIndex($rqst,'media_capacity'));
        $media_os =       $this->sanitize_input($this->arrayIndex($rqst,'media_os'));
        $tampered_status = $this->sanitize_input($this->arrayIndex($rqst,'tampered_status'));
        $media_condition = $this->sanitize_input($this->arrayIndex($rqst,'media_condition'));
        $media_problem = $this->sanitize_input($this->arrayIndex($rqst,'media_problem'));
        $media_firmware = $this->sanitize_input($this->arrayIndex($rqst,'media_firmware'));
        $peripherals_details = $this->sanitize_input($this->arrayIndex($rqst,'peripherals_details'));
        $important_data = $this->sanitize_input($this->arrayIndex($rqst,'important_data'));
        $encryption_status = $this->sanitize_input($this->arrayIndex($rqst,'encryption_status'));
        $encryption_software = $this->sanitize_input($this->arrayIndex($rqst,'encryption_software'));
        $encryption_version =$this->sanitize_input($this->arrayIndex($rqst,'encryption_version'));
        $encryption_username = $this->sanitize_input($this->arrayIndex($rqst,'encryption_username'));
        $encryption_password = $this->sanitize_input($this->arrayIndex($rqst,'encryption_password'));
        $zoho_user =$this->sanitize_input($this->arrayIndex($rqst,'zoho_user'));
        $branch_id = $this->sanitize_input($this->arrayIndex($rqst,'branch_id'));
        ///////Check database 
        $preAnalysis = Media::where('zoho_id', $zoho_id)->first();
        if($preAnalysis != '' && $preAnalysis != null && ($preAnalysis->stage == 1 || $preAnalysis->stage == 2))
        {
            $dataLog['msg'] = 'Error!! Pre-Analysis already done for this case';
            $dataLog['status'] = 'SUCCESS';
            return $dataLog;
        }
        else if($preAnalysis != '' &&  $preAnalysis != null)
        {
            $remarks = (!empty($zoho_user) ? "Data updated by Zoho user ".$zoho_user : "Data updated by Zoho user");  
        }
        else
        {
            $preAnalysis = new Media();
            $preAnalysis->stage = 1;
            $remarks = (!empty($zoho_user) ? "Case added by Zoho user ".$zoho_user : "Case added by Zoho user");
        }

            $preAnalysis->zoho_id = $zoho_id;
            $preAnalysis->media_type = $media_type;
            $preAnalysis->media_make = $media_make;
            $preAnalysis->media_model = $media_model;
            $preAnalysis->media_serial = $media_serial;
            $preAnalysis->media_capacity = $media_capacity;
            $preAnalysis->tampered_status = $tampered_status;
            $preAnalysis->media_condition = $media_condition;
            $preAnalysis->media_problem = $media_problem;
            $preAnalysis->peripherals_details = $peripherals_details;
            $preAnalysis->important_data = $important_data;
            $preAnalysis->encryption_software = $encryption_software;
            $preAnalysis->encryption_version = $encryption_version;
            $preAnalysis->encryption_username = $encryption_username;
            $preAnalysis->encryption_password = $encryption_password;
            $preAnalysis->zoho_user = $zoho_user;
            $preAnalysis->branch_id = $branch_id;
            $preAnalysis->created_on = Carbon::now()->toDateTimeString();
            $preAnalysis->save(); 
            $cus = new CustomerDetail();
            $cus->customer_name = $client_name;
            $cus->save();
            $preAnalysis->customer_id = $cus->id;
            $preAnalysis->save();
            $history = $this->_insertHistory($remarks,$preAnalysis->id,'media_in',$zoho_user,$preAnalysis->stage);
            return $history;
    }
}