<?php 

namespace App\Helpers;
use DB;
use Carbon\Carbon; 
use App\Models\CustomerDetail;
use App\Models\User;
use App\Models\Stage;
use DateTime;
class Zoho 
{

    public  function getZohoCrmAuthToken()
    {
        //sandbox
        $zoho_token_request = 'refresh_token=1000.63092452ac4bfa9bc4766e1efc68c75b.2964edecf2f227bdebeb4c3088173c2f&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token';
        //production
        $zoho_header = array('Content-Type: application/x-www-form-urlencoded');
        $api_url = 'https://accounts.zoho.com/oauth/v2/token';
        $zch = curl_init($api_url);                                                      
        curl_setopt($zch, CURLOPT_CUSTOMREQUEST, "POST");           
        curl_setopt($zch, CURLOPT_POSTFIELDS, $zoho_token_request);    
        curl_setopt($zch, CURLOPT_MAXREDIRS, 5);  
        curl_setopt($zch, CURLOPT_TIMEOUT, 10);                   
        curl_setopt($zch, CURLOPT_RETURNTRANSFER, true);   
        curl_setopt($zch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($zch, CURLOPT_HTTPHEADER, $zoho_header); 
        
        $zoho_token_result = curl_exec($zch);
        $error = curl_error($zch);
        curl_close($zch);       
        if ($error) {
          //Log Api data
          $log_data = array(
              "log_name" => 'Zoho Auth API',
              "log_request_url" => $api_url,
              "log_request" => $zoho_token_request,
              "log_response" => !empty($error) ? $error : $zoho_token_result
          );
          (new self)->logApiCall($log_data);
          return false;
        } else {
            $json_decode =  json_decode($zoho_token_result, true);
            if(array_key_exists("access_token",$json_decode)){
                $tokenvalue= $json_decode['access_token'];
                return $tokenvalue;
            }
            else{
                //Log Api data
                $log_data = array(
                    "log_name" => 'Zoho Auth API',
                    "log_request_url" => $api_url,
                    "log_request" => $zoho_token_request,
                    "log_response" => !empty($error) ? $error : $zoho_token_result
                );
                (new self)->logApiCall($log_data);
                return false;
            }
        }
    }

   public  function logApiCall($log_data = array()){
        $log_date = date('Y-m-d H:i:s');
        $log_name = '';
        $log_request = '';
        $log_response = '';
        $log_request_url = '';
        $log_referrer = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        
        if (isset($log_data['log_name']) && !empty($log_data['log_name'])) {
            $log_name = $log_data['log_name'];
        }
        
        if (isset($log_data['log_request']) && !empty($log_data['log_request'])) {
            $log_request = $log_data['log_request'];
        }
        
        if (isset($log_data['log_response']) && !empty($log_data['log_response'])) {
            $log_response = $log_data['log_response'];
        }
        
        if (isset($log_data['log_request_url']) && !empty($log_data['log_request_url'])) {
            $log_request_url = $log_data['log_request_url'];
        }
        
        if (isset($log_data['log_referrer']) && !empty($log_data['log_referrer'])) {
            $log_referrer = $log_data['log_referrer'];
        }
        DB::insert('insert into api_log (log_date, log_name, log_request, log_response, log_request_url, log_referrer) values (?,?,?,?,?,?)', array($log_date, $log_name,
            $log_request,$log_response,$log_request_url,$log_referrer));
    }

    public static function getMimsCrmAuthToken()
    {
            header('Content-Type: application/json');
            $formError = 0;
            $response = array();
            $response['status'] = 'ERROR';
            $response['msg'] = 'Error!!! Not a valid request';
            $response['data'] = null;
            $response['Auth'] = null;
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
            if(strcasecmp($contentType, 'application/x-www-form-urlencoded') == 0 && $formError == 0){
                $rqst = $_POST;
                $formError = 0;
                $response['status'] = 'SUCCESS';
                $response['data'] = $rqst;
                $response['Auth'] = 1;
            }elseif(strcasecmp($contentType, 'application/json') == 0 && $formError == 0){
                $content = trim(file_get_contents("php://input"));
                $rqst = json_decode($content, true);
                $formError = 0;
                $response['data'] = $rqst;
                $response['status'] = 'SUCCESS';
                $response['Auth'] = 1;
            }

            return $response;
    }

    public static function sanitize_input($inputs) {
        $inputs = trim($inputs);
        $inputs = stripslashes($inputs);
        $inputs = htmlspecialchars($inputs);
        return $inputs;
      }

      public static function validationInput($required_fields,$rqst)
      {
        $response = array();
        foreach($required_fields as $field) {
            if (isset($rqst[$field]) && empty($rqst[$field])) 
                 $response[]= 'Required field '.$field. ' is empty';
        }
         return $response;
      }

      public static function arrayIndex($rqst,$key)
      {
          if(isset($rqst[$key]))
             return $rqst[$key];
          else
              return null;
      }

      public static function  _insertMediaHistory($media,$type,$module,$user,$remarks,$extStatus=null)
      {
          DB::insert('insert into media_history (media_id,action_type,module_type,added_by,remarks,added_on,status,ext_status) values (?,?,?,?,?,?,?,?)', array($media->id,
                        $type,$module,$user,$remarks,Carbon::now()->toDateTimeString(),$media->stage,$extStatus));
      }
	  
	 public static function sendZohoCrmNotes($params = '', $request_type, $limit = 1,$remarks){
		    $zoho_tokeninfo = (new self)->getZohoCrmAuthToken();
        if($zoho_tokeninfo == false){
            $zoho_tokeninfo =(new self)->getZohoCrmAuthToken();
        }
		if($request_type == 'PRE-ANALYSIS'){
		$api_url= 'https://crmsandbox.zoho.com/crm/v2/Media_Pickup/'.$params->zoho_id.'/Notes';
		$zoho_crm_data = json_encode((new self)->dataFormateNotes($params,$remarks,'Media_Pickup'));
		$api_name= 'Zoho CRM Notes API';
		$push_method = "POST";
		}
        elseif($request_type == 'INSPECTION'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID/'.$params->zoho_job_id.'/Notes';
            $zoho_crm_data = json_encode((new self)->dataFormateNotes($params,$remarks,'Job_ID'));
            $api_name= 'Zoho CRM Notes API';
            $push_method = "POST";
        }
		  if($zoho_tokeninfo && !empty($request_type)){
            $zoho_header = array(
            'Content-Type: application/json',
            'Authorization: Zoho-oauthtoken '.$zoho_tokeninfo.''
            );                                                             
            $zch = curl_init($api_url);
            curl_setopt($zch, CURLOPT_CUSTOMREQUEST, $push_method);           
            curl_setopt($zch, CURLOPT_POSTFIELDS, $zoho_crm_data);    
            curl_setopt($zch, CURLOPT_MAXREDIRS, 5);  
            curl_setopt($zch, CURLOPT_TIMEOUT, 10);                   
            curl_setopt($zch, CURLOPT_RETURNTRANSFER, true);   
            curl_setopt($zch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($zch, CURLOPT_HTTPHEADER, $zoho_header);
            
            $zoho_result = curl_exec($zch);
            $error = curl_error($zch);
            curl_close($zch);
            
            //Log Api data
            $log_data = array(
                "log_name" => $api_name,
                "log_request_url" => $api_url,
                "log_request" => $zoho_crm_data,
                "log_response" => !empty($error) ? $error : $zoho_result
            );
            (new self)->logApiCall($log_data);
              
            //retry if error
            if ($error && $limit == 1) {
              sendZohoCrmNotes($params,$request_type,0,$remarks);
            } 
            else 
            {
                $json_decode =  json_decode($zoho_result, true);
                if(array_key_exists("status",$json_decode['data'][0]) && $json_decode['data'][0]['status'] == 'error' && $limit == 1){
                    sendZohoCrmNotes($params,$request_type,0,$remarks);
                }
            }
        } else{
            //Log Api data
            $log_data = array(
                "log_name" => $api_name,
                "log_request_url" => $api_url,
                "log_request" => $zoho_crm_data,
                "log_response" => 'Authentication Token Failure'
            );
            (new self)->logApiCall($log_data);
        }
	}

    public static function sendAttachmentZoho($params = '', $request_type, $limit = 1)
    {
        $zoho_tokeninfo = (new self)->getZohoCrmAuthToken();
        if($zoho_tokeninfo == false){
            $zoho_tokeninfo =(new self)->getZohoCrmAuthToken();
        }
        if($zoho_tokeninfo && !empty($request_type)){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID/'.$params->zoho_job_id.'/Attachments';
            $api_name= 'Attachment';
            $zoho_crm_data = array("attachmentUrl"=>$params->url);
            $push_method = "POST";
            $zoho_header = array(
                'Content-Type: multipart/form-data; boundary=<calculated when request is sent>',
                'Authorization: Zoho-oauthtoken '.$zoho_tokeninfo
                );                                                             
            $zch = curl_init($api_url);
            curl_setopt($zch, CURLOPT_CUSTOMREQUEST, $push_method);           
            curl_setopt($zch, CURLOPT_POSTFIELDS, $zoho_crm_data);    
            curl_setopt($zch, CURLOPT_MAXREDIRS, 5);  
            curl_setopt($zch, CURLOPT_TIMEOUT, 10);                   
            curl_setopt($zch, CURLOPT_RETURNTRANSFER, true);   
            curl_setopt($zch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($zch, CURLOPT_HTTPHEADER, $zoho_header);            
            $zoho_result = curl_exec($zch);
            $error = curl_error($zch);
            curl_close($zch);
            //Log Api data
            $log_data = array(
                "log_name" => $api_name,
                "log_request_url" => $api_url,
                "log_request" => json_encode($zoho_crm_data),
                "log_response" => !empty($error) ? $error : $zoho_result
            );
            (new self)->logApiCall($log_data);
        }
    }

      public static function sendZohoCrmData($params = '', $request_type, $limit = 1){
        
        $zoho_tokeninfo = (new self)->getZohoCrmAuthToken();
        if($zoho_tokeninfo == false){
            $zoho_tokeninfo =(new self)->getZohoCrmAuthToken();
        }
        if($request_type == 'PRE-ANALYSIS'){
            $zoho_crm_data = json_encode((new self)->dataFormatePreAnalysis($params));
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Media_Pickup';
            $api_name= 'Zoho CRM Pre Analysis API';
            $push_method = "PUT";
        }
        elseif($request_type == 'INSPECTION'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM Inspection API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateInspection($params));
        }
        elseif($request_type == 'EXTENSION-DAY'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM Inspection API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateExtension($params));
        }
        elseif($request_type == 'NOT-DONE'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM Recovery API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateNotDone($params));
        }
        elseif($request_type == 'OBSERVATION'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM OBSERVATION API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateOvervation($params));
        }
        elseif($request_type == 'CLONECREATION' || $request_type == 'DATA-ENCRYPTED' || $request_type == 'RECOVERABLE-DATA'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM '.$request_type.' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateRecovery($params));
        }
        elseif($request_type == 'DL'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM DL API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateDL($params));
        }
        elseif($request_type == 'DATAOUT'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM DATAOUT API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateDataOut($params));
        }
        elseif($request_type == 'MEDIA_OUT'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM MEDIA OUT API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateMediaOut($params));
        }
        elseif($request_type == 'WIPING'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM MEDIA OUT API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateWiping($params));
        }
        elseif($request_type == 'GATEPASS_UPDATE'){
            $api_url= 'https://crmsandbox.zoho.com/crm/v2/Job_ID';
            $api_name= 'Zoho CRM GATEPASS UPDATE API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateGatePass($params));
        }
        if($zoho_tokeninfo && !empty($request_type)){
            $zoho_header = array(
            'Content-Type: application/json',
            'Authorization: Zoho-oauthtoken '.$zoho_tokeninfo.''
            );                                                             
            $zch = curl_init($api_url);
            curl_setopt($zch, CURLOPT_CUSTOMREQUEST, $push_method);           
            curl_setopt($zch, CURLOPT_POSTFIELDS, $zoho_crm_data);    
            curl_setopt($zch, CURLOPT_MAXREDIRS, 5);  
            curl_setopt($zch, CURLOPT_TIMEOUT, 10);                   
            curl_setopt($zch, CURLOPT_RETURNTRANSFER, true);   
            curl_setopt($zch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($zch, CURLOPT_HTTPHEADER, $zoho_header);
            
            $zoho_result = curl_exec($zch);
            $error = curl_error($zch);
            curl_close($zch);
            
            //Log Api data
            $log_data = array(
                "log_name" => $api_name,
                "log_request_url" => $api_url,
                "log_request" => $zoho_crm_data,
                "log_response" => !empty($error) ? $error : $zoho_result
            );
            (new self)->logApiCall($log_data);
              
            //retry if error
            if ($error && $limit == 1) {
              sendZohoCrmData($params,$request_type,0);
            } 
            else 
            {
                $json_decode =  json_decode($zoho_result, true);
                if(array_key_exists("status",$json_decode['data'][0]) && $json_decode['data'][0]['status'] == 'error' && $limit == 1){
                    sendZohoCrmData($params,$request_type,0);
                }
            }
        } else{
            //Log Api data
            $log_data = array(
                "log_name" => $api_name,
                "log_request_url" => $api_url,
                "log_request" => $zoho_crm_data,
                "log_response" => 'Authentication Token Failure'
            );
            (new self)->logApiCall($log_data);
        }
       
    }

        public  function dataFormatePreAnalysis($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_id,
					"Name" => (new self)->_getClientName($params->customer_id),
					"Make" => $params->media_make,
					"Model" => $params->media_model,
					"Media_Type" =>$params->media_type,
					"Serial_Number" => $params->media_serial,
					"Capacity" => $params->media_capacity,
					"Tampered_Status" => $params->tampered_status,
					"Media_Condition" => $params->media_condition,
					"Peripherals_Details" => !empty($params->peripherals_details) ? strip_tags($params->peripherals_details) : "",
					"Media_Checking_Done" => ($params->stage == '3') ? true : false,
                    "Peripherals_With_Media" => $params->media_casing,
                    "Media_Status" => $params->media_status
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);

            return $zoho_crm_data;
        }

        public function dataFormateExtension($params = array())
        {
            $objDateTime = new DateTime();
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Extension_Day" => $params->extension_day,
					"Extension_Reason" => $params->extReason
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);

            return $zoho_crm_data;
        }

        public function dataFormateGatePass($params = array())
        {
            if($params->mediaOutType == 1)
            {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Mode_of_Data_Out" =>$params->transfer_mode,
					"Data_Out_Name" =>$params->ref_name_num,
                    )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
        }
        elseif($params->mediaOutType == 2)
            {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Mode_of_Media_Out" =>$params->transfer_mode,
					"Media_Out_Name" =>$params->ref_name_num,
                    )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
        }
            return $zoho_crm_data;
        }

        public function dataFormateWiping($params = array())
        {
            $Wiping_Date = DateTime::createFromFormat('Y-m-d h:i:s', $params->WipingDate);
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Wiping_Date"=>$Wiping_Date->format('Y-m-d'),
					"Wiping_Done_Status"=>$params->Wipingstatus,
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            return $zoho_crm_data;
        }

        public function dataFormateMediaOut($params = array())
        {
            $stege = Stage::find($params->stage);
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Current_Status"=>$stege->stage_name,
					"Job_Stage"=>$stege->stage_name,
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            return $zoho_crm_data;
        }

        public function dataFormateDataOut($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Data_Copy_Done" =>"Yes",
					"Data_Out1" =>"Yes",
					"Current_Status"=>"Data out",
					"Job_Stage"=>"Data out",
                    "Client_Serial_No"=>($params["DL"]->copyin_details !=null)?$params["DL"]['copyin_details'][0]['media_sn']:null,
                    "Client_Make_Model"=>($params["DL"]->copyin_details !=null)?$params["DL"]['copyin_details'][0]['media_model']:null
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            return $zoho_crm_data;
        }

        public function dataFormateDL($params = array())
        {
           $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Type_of_Data_Recovered" =>str_replace(["[","]",'"'],'','"'.$params['DL']['data_recovered'].'"'),
					"Recovered_Data_Size" => $params['DL']['recoverable_data'],
					"Total_Number_of_Files" => $params['DL']['total_file'],
					"Uplaod_Directory_Listing" => $params['DL']['directory_listing'],
					"Total_Data_Size" => $params['DL']['total_data_size']." ".$params['DL']['total_data_size_format'],
                    "Current_Status"=>"Directory Listing Submitted"
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            return $zoho_crm_data;
        }

        public function dataFormateRecovery($params = array())
        {
            $caseNotPossible = null;
            if($params->recovery_possibility == 'No')
            {
                $caseNotPossible = implode(",",$params->no_recovery_reason);
                if($params->no_recovery_reason_other != null)
                $caseNotPossible = $caseNotPossible.",".$params->no_recovery_reason_other;
            }

            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Clone_Creation_Completed" => $params['Recovery']['clone_creation'],
					"Data_Encrypted" => $params['Recovery']['data_encrypted'],
					"Decryption_Details_Received" => $params['Recovery']['decryption_details'],
					"Data_Decrytion_Successful" => $params['Recovery']['decryption_data'],
					"Request_for_Correct_Encryption_details" => $params['Recovery']['decryption_details_send'],
					"Data_Verifcation" => $params['Recovery']['recoverable_data'],
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            if($params->stage == 14)
            {
                $zoho_crm_data['data'][0]['Current_Status'] ="Not Done";
                $zoho_crm_data['data'][0]['Not_Done_Reason'] =$caseNotPossible;
            }

            return $zoho_crm_data;
        }

        public function dataFormateNotDone($params = array())
        {
            $caseNotPossible = null;
            if($params->recovery_possibility == 'No')
            {
                $caseNotPossible = implode(",",$params->no_recovery_reason);
                if($params->no_recovery_reason_other != null)
                $caseNotPossible = $caseNotPossible.",".$params->no_recovery_reason_other;
            }

            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Recovery_Possibility" =>'Not Possible',
					"Current_Status" => 'Not Done',
                    "Not_Done_Reason" => $caseNotPossible
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);

            return $zoho_crm_data;
        }

        public function dataFormateOvervation($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"Assessment_By_Username" => !empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id) : "MIMS User",
					"Recovery_Possibility" => ($params->recovery_possibility == 'Yes')?'Possible':'Not Possible',
					"Recovery_Percentage" => str_replace('%%', '%', $params->recovery_percentage),
					"Recoverable_Data" => str_replace('%%', '%', $params->recoverable_data),
					"Assessment_Remarks" => str_replace('%%', '%', $params->notes),
					"Media_Similar_Spare" => $params->spare_required,
					"Media_Similar_Spare_Details" =>($params->spare_required == 'Yes')?(new self)->spareDetailDataset($params):null,

				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);

            return $zoho_crm_data;
        }

        public function dataFormateInspection($params = array())
        {
            $objDateTime = new DateTime();
            $caseNotPossible = null;
            $assemntDuereason = null;
            if($params->recovery_possibility == 'No')
            {
                $caseNotPossible = implode(",",$params->no_recovery_reason);
                if($params->no_recovery_reason_other != null)
                $caseNotPossible = $caseNotPossible.",".$params->no_recovery_reason_other;
            }
            if($params->stage == 5)
            {
                $assemntDuereason = implode(",",$params->assessment_due_reason);
                if($params->assessment_due_reason_other != null)
                $assemntDuereason = $assemntDuereason.",".$params->assessment_due_reason_other;
            }
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_job_id,
					"MIMS_Data_Received" => ($params->stage == 8) ? true : "",
					"Assessment_By_Username" => !empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id) : "MIMS User",
					"Case_Type" => $params->case_type,
					"Tampering_Required" => $params->tampering_required,
					"Recovery_Possibility" => ($params->recovery_possibility == 'Yes')?'Possible':'Not Possible',
					"Required_Days" => $params->required_days,
					"Recovery_Percentage" => str_replace('%%', '%', $params->recovery_percentage),
					"Recoverable_Data" => str_replace('%%', '%', $params->recoverable_data),
					"Assessment_Remarks" => str_replace('%%', '%', $params->notes),
					"Assessment_Status" => 'Assessment Done',
					"Assessment_Date_Time" => $objDateTime->format('c'),
					"Current_Status" => ($params->stage == 8)?'Waiting for Confirmation':($params->stage == 7?'Not Possible':''),
//					"Job_Stage" => ($params->stage == 8)?'Waiting for Confirmation':($params->stage == 7?'Not Possible':''),
					"Operating_System_Details" => $params->media_os,
					"Encryption_Status" => $params->encryption_status,
					"Software_Name" => $params->encryption_name,
					"Encryption_Level" => $params->encryption_type,
					"Accuracy_of_Provided" => $params->encryption_details_correct,
					"Further_use_of_media" => $params->further_use,
                    "Not_Done_Reason" => $caseNotPossible,
                    "Assessment_Due_Reason" => $assemntDuereason,
                    "Extension_Day" => $params->extension_day,
					"Extension_Reason" => $params->extReason,
					"Media_Similar_Spare" => $params->spare_required,
					"Media_Similar_Spare_Details" =>($params->media_sapre_detail == null)?null:(new self)->spareDetailDataset($params),

				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);

            return $zoho_crm_data;
        }

        public function spareDetailDataset($media)
        {
            $details = '';
            $spareDetails =  json_decode($media->media_sapre_detail,true);
            if($spareDetails != null && ($media->media_type == "Hard Drive" || $media->type == "External Hard Drive"))
            {
                for ($row = 0; $row < count($spareDetails); $row++) { 
                    $details.="Media Make :".$spareDetails[$row]['media_make']."\n Media Model :".$spareDetails[$row]['media_make']."\nMedia Capacity :".$spareDetails[$row]['media_capacity'].
                    "\nFirmware :".$spareDetails[$row]['firmware']."\nSite Code :".$spareDetails[$row]['site_code']."\nPCB No :".$spareDetails[$row]['pcb_num']."\n\n";
                }
            }
            else if($spareDetails != null && ($media->media_type == "Solid State Drive" || $media->type == "External Solid State Drive"))
            {
                for ($row = 0; $row < count($spareDetails); $row++) { 
                    $details.="Media Make: :".$spareDetails[$row]['media_make']."\nMedia Model :".$spareDetails[$row]['media_make']."\nMedia Capacity :".$spareDetails[$row]['media_capacity'].
                    "\nController Model No :".$spareDetails[$row]['controller_model']."\nNo Of Data CHIP :".$spareDetails[$row]['nom_of_data']."\nController Make :".$spareDetails[$row]['controller_make']."\n\n";
                }
            }
            return $details;
        }

         public function _getClientName($id)
        {
           $client = CustomerDetail::find($id);
           return $client->customer_name;
        }

        public function _getUserName($userId)
        {
          if($userId != null)
          {
            $user = User::find($userId);
            return $user->name;
          }
          else
          return null;
      
        }
		
		public function dataFormateNotes($media,$remarks,$type)
		{
			$zoho_crm_data = array(
				"data" => array(
				  array(
					"Note_Title" => !empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
					"Parent_Id" => ($type == 'Job_ID')?$media->zoho_job_id:$media->zoho_id,
					"Note_Content" => strip_tags($remarks),
					'se_module' => $type
				  )
				)
			);
			
			return $zoho_crm_data;
		}
}