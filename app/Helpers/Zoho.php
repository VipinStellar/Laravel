<?php 

namespace App\Helpers;
use DB;
use Carbon\Carbon; 
use App\Models\Contact;
use App\Models\User;
use App\Models\Stage;
use App\Models\Quotation;
use DateTime;
use App\Models\ServicePayment;

class Zoho 
{

    public  function getZohoCrmAuthToken()
    {
        //sandbox
        $zoho_token_request = env('MIMS_REQUEST_TOKEN');
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

    public  function  idDecodeAndEncode($action, $string) {
        $output = false;
    
        $encrypt_method = "AES-256-CBC";
        //pls set your unique hashing key
        $secret_key = 'mims';
        $secret_iv = 'mims123';
    
        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
    
        return $output;
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

            $arrayResponce = json_decode($log_response, true);
            if($log_name == 'Zoho CRM Quotes Send API' && $arrayResponce["data"][0]['status'] == 'success')
            {
                $requestData = json_decode($log_request, true);
                $Quotation = Quotation::find($requestData["data"][0]['Quotation_Primary_Id']);
                $Quotation->zoho_quotation_id = $arrayResponce["data"][0]['details']['id'];
                $Quotation->save();
            }
            elseif($log_name == 'Zoho CRM Add Price Send API' && $arrayResponce["data"][0]['status'] == 'success')
            {
                $requestData = json_decode($log_request, true);
                $serivcePayment = ServicePayment::find($requestData["data"][0]['ServicePaymentId']);
                $serivcePayment->zoho_payment_id = $arrayResponce["data"][0]['details']['id'];
                $serivcePayment->save();
            }
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
                 $response[]= $field;
                // $response[]= 'Required field '.$field. ' is empty';
        }
         return $response;
      }

      public static function validationMissingKey($required_fields,$rqst)
      {
        $response = array();
        foreach($required_fields as $field) {
        if(!isset($rqst[$field]))
            $response[]= $field;
        else if (isset($rqst[$field]) && empty($rqst[$field])) 
                 $response[]= $field;
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
	
      public static function sendZohoCrmData($params = '', $request_type, $limit = 1){
        
        $zoho_tokeninfo = (new self)->getZohoCrmAuthToken();
        if($zoho_tokeninfo == false){
            $zoho_tokeninfo =(new self)->getZohoCrmAuthToken();
        }
        if($request_type == 'PRE-ANALYSIS'){
            $zoho_crm_data = json_encode((new self)->dataFormatePreAnalysis($params));
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM Pre Analysis API';
            $push_method = "PUT";
        }
        elseif($request_type == 'STATUS-CHANGE'){
            $zoho_crm_data = json_encode((new self)->datastatusupdate($params));
            $api_url=  env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM STATUS CHANGE';
            $push_method = "PUT";
        }
        elseif($request_type == 'EXTENSION-UPDATE'){
            $zoho_crm_data = json_encode((new self)->dataExtensionUpdate($params));
            $api_url=  env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Extension Update';
            $push_method = "PUT";
        }
        elseif($request_type == 'INSPECTION'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM Inspection API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateInspection($params));
        }
        elseif($request_type == 'PRICE-UPDATE'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM PRICE UPDATE API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormatePriceUpdate($params));
        }
        elseif($request_type == 'PAYMENT-UPDATE'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM PAYMENT UPDATE API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormatePaymentUpdate($params));
        }
        elseif($request_type == 'CONTACT-EDIT'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Contacts';
            $api_name= 'Zoho CRM CONTACT UPDATE API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateContact($params));
        }
        elseif($request_type == 'COMPANY-EDIT'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Accounts';
            $api_name= 'Zoho CRM COMPANY UPDATE API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateCompany($params));
        }
        elseif($request_type == 'QUOTATION'){
            $api_url= env('MIMS_API_URL').'/crm/v2.1/Quotes';
            $api_name= 'Zoho CRM Quotes Send API';
            $push_method = "POST";
            $zoho_crm_data = json_encode((new self)->dataFormateQuotes($params));
        }
        elseif($request_type == 'ADD-PAYMENT'){
            $api_url= env('MIMS_API_URL').'/crm/v2.1/Customer_Payments';
            $api_name= 'Zoho CRM Add Price Send API';
            $push_method = "POST";
            $zoho_crm_data = json_encode((new self)->dataFormatPaymentAdd($params));
        }
        elseif($request_type == 'INVOICE-PAYMENT-UPDATE'){
            $api_url= env('MIMS_API_URL').'/crm/v2.1/Customer_Payments';
            $api_name= 'Zoho CRM Update Price Send API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormatPaymentUpdate($params));
        }
        elseif($request_type == 'OBSERVATION'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM Add Observation Send API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormatObservation($params));
        }
        elseif($request_type == 'CLONECREATION' || $request_type == 'DATA-ENCRYPTED' || $request_type == 'RECOVERABLE-DATA'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM '.$request_type.' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateRecovery($params));
        }
        elseif($request_type == 'Directory-Listing'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM DL API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateDL($params));
        }
        elseif($request_type == 'DIRECTORY-CONFIRM'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM DL API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateDLConfirm($params));
        }
        elseif($request_type == 'REWORK'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM DL API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateRework($params));
        }
        elseif($request_type == 'DATA-OUT-TECH' || $request_type == 'DATA-OUT-ISE'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM '. $request_type. ' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateDatOut($params));
        }
        elseif($request_type == 'DAILY-STATUS'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM '. $request_type. ' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateDailyStatus($params));
        }
        elseif($request_type == 'MEDIA-OUT-CUSTOMER'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM '. $request_type. ' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->dataFormateMediaOutClient($params));
        }
        elseif($request_type == 'WIPING-REQUEST'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM '. $request_type. ' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->WipingRequest($params));
        }
        elseif($request_type == 'WIPING-REQUEST-UPDATE'){
            $api_url= env('MIMS_API_URL').'/crm/v2/Deals';
            $api_name= 'Zoho CRM '. $request_type. ' API';
            $push_method = "PUT";
            $zoho_crm_data = json_encode((new self)->WipingRequestUpdate($params));
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
	
	 // Get Zoho CRM DATA
    public static function GetZohoCrmData($params = array(), $request_type, $limit = 1){
        
        $zoho_tokeninfo = (new self)->getZohoCrmAuthToken();
        if($zoho_tokeninfo == false){
            $zoho_tokeninfo =(new self)->getZohoCrmAuthToken();
        }

        if($request_type == 'CUSTOMER-PAYMENTS' && $params['id']!=''){
            $api_url=  env('MIMS_API_URL').'/crm/v2/Customer_Payments/'.$params['id'];
            $api_name= 'Zoho CRM Customer Payments API';
            $push_method = "GET";
        }

        if($zoho_tokeninfo && !empty($request_type)){
            $zoho_header = array(
            'Content-Type: application/json',
            'Authorization: Zoho-oauthtoken '.$zoho_tokeninfo.''
            );                                                             
            $zch = curl_init($api_url);
            curl_setopt($zch, CURLOPT_CUSTOMREQUEST, $push_method);  
            curl_setopt($zch, CURLOPT_MAXREDIRS, 5);  
            curl_setopt($zch, CURLOPT_TIMEOUT, 10);                   
            curl_setopt($zch, CURLOPT_RETURNTRANSFER, true);   
            curl_setopt($zch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($zch, CURLOPT_HTTPHEADER, $zoho_header);
            
            $zoho_result = curl_exec($zch);
            $error = curl_error($zch);
            curl_close($zch);
            $result_decode = json_decode($zoho_result,true);
            //return $zoho_result;
            if(!isset($result_decode)){
                $log_data = array(
                    "status" => 'error',
                    "result" =>  'Customer Details Not Found'
                );
            }else if(array_key_exists("status",$result_decode) && $result_decode['status']=='error'){
                $log_data = array(
                    "status" => 'error',
                    "result" =>  $result_decode['message']
                );
            }else{
                $log_data = array(
                    "status" => 'success',
                    "result" =>  $result_decode
                );
            }
            //Log Api data
            return $log_data;
        } else {
            //Log Api data
            $log_data = array(
                "status" => 'error',
                "result" => 'Authentication Token Failure'
            );
            return $log_data;
        }
    }

    public  function dataFormatePreAnalysis($params = array())
        {
            $stege = Stage::find($params->stage);
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->deal_id,
					"Current_Status" =>$stege->stage_name,
					"Job_Number" => $params->job_id,
                    "Media_Type" =>$params->media_type,
                    "Media_Category" =>$params->media_category,
                    "Media_Make" =>$params->media_make,
                    "Media_Model" =>$params->media_model,
                    "Media_Serial" =>$params->media_serial,
                    "Type_of_Interface" =>$params->media_interface,
                    "Media_Capacity" =>$params->media_capacity,
                    "Media_Status" =>$params->media_status,
                    "Media_Condition" =>$params->media_condition,
                    "Peripherals_Details" =>$params->peripherals_details,
                    "Problem_Type" =>$params->media_problem,
                    "MIMS_Notes" =>$params->remarks,
                    "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            if($params->stage == 3 && count($params->price) > 0)
            {
              $zoho_crm_data['data'][0]['Recovery_Charges'] = $params->price;
              $zoho_crm_data['data'][0]['Payment_Link'] = url('payment')."/".(new self)->idDecodeAndEncode('encrypt',$params->id);
            }
            return $zoho_crm_data;
        }

    public function datastatusupdate($params = array())
    {
        $stege = Stage::find($params->stage);
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "Current_Status" =>$stege->stage_name,
                "Job_Number" => $params->job_id,
                "Not_Interested_Reason" =>($params->stage ==15)?$params->remarks:'',
                "Not_Done_Reason" =>($params->stage ==14)?$params->remarks:'',
               // "Inspection_Due_Date" =>($params->stage ==4)?$params->assessment_due_date:'',
              )
            ),
            "trigger" => array(
                "approval",
                "workflow",
                "blueprint",
            )
        );
        if($params->stage ==4)
            $zoho_crm_data['data'][0]['Inspection_Due_Date'] = $params->assessment_due_date;
        return $zoho_crm_data;
    }

    public function dataExtensionUpdate($params = array())
    {
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "Inspection_Due_Date" =>$params->assessment_due_date,
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
            $dueReson = null;
            if($params->recovery_possibility == 'No')
            {
                $caseNotPossible = implode(", ",$params->no_recovery_reason);
                if($params->no_recovery_reason_other != null)
                $caseNotPossible = $caseNotPossible.",".$params->no_recovery_reason_other;
            }
            if($params->stage == 5 && $params->assessment_due_reason !=null)
            {
                $dueReson = implode(", ",$params->assessment_due_reason);
                if($params->assessment_due_reason_other != null)
                $dueReson = $dueReson.", ".$params->assessment_due_reason_other;
            }
            $stege = Stage::find($params->stage);
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->deal_id,
					"Current_Status" =>$stege->stage_name,
					"Job_Number" => $params->job_id,
					"Case_Type" => $params->case_type,
					"Tamper_Open_Permission" => $params->tampering_required,
					"Further_Use_of_Media" => $params->further_use,
					"Media_Os" => $params->media_os,
					"Media_Found_Encrypted" => $params->encryption_status,
					"Encryption_Name" => $params->encryption_name,
					"Encryption_Level_Identified" => $params->encryption_type,
					"Accuracy_of_Provided_Decryption_Details" => $params->encryption_details_correct,
					"Recovery_Possibility" => $params->recovery_possibility,
					"Media_Similar_Spare" => $params->spare_required,
					"Notes_for_Customer" => $params->notes,
					"Case_Not_Possible_Reason" => $caseNotPossible,
					"Days_Required_for_Recovery" => $params->required_days,
					"Recovered_Data_Files_and_Folder_Structure" => $params->recoverable_data,
					"Recovery_Percentage" => $params->recovery_percentage,
					"Inspection_due_reason" => $dueReson,
					"Extension_Required" => $params->extension_required,
					"Extension_Required_Days" => $params->extension_day,
                    "MIMS_Notes" =>$params->remarks,
                    "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            if($params->stage !='4' && $params->stage !='5')
            {
                $zoho_crm_data['data'][0]['Inspection_Status'] ='Inspection Done';
                $zoho_crm_data['data'][0]['Stage'] ='Assessment/Data Recovery Quote';
                $zoho_crm_data['data'][0]["Inspection_By_User"]= !empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id) : "MIMS User";
                $zoho_crm_data['data'][0]["Inspection_Done_On"]= $objDateTime->format('c');
            }
            if($params->stage == 6 && count($params->countMediaPrice) == 0)
            {
                $zoho_crm_data['data'][0]['Recovery_Charges'] = $params->price;
                $zoho_crm_data['data'][0]['Payment_Link'] = url('payment')."/".(new self)->idDecodeAndEncode('encrypt',$params->id);
            }
            return $zoho_crm_data;
        }

        public function dataFormatePriceUpdate($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->deal_id,
                    "Recovery_Charges"=>$params->price
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            if($params->SelectedPlan != null)
            {
                $zoho_crm_data['data'][0]['Total_Service_Fee'] = (int)$params->SelectedPlan->total_amount;
                $zoho_crm_data['data'][0]['Amount_Paid'] = (int)$params->SelectedPlan->paid_amount;
                $zoho_crm_data['data'][0]['Tax_Applicable'] = strval($params->SelectedPlan->tax_amount);
                $zoho_crm_data['data'][0]['Balance_Amount'] =(int) $params->SelectedPlan->balance_amount;
                $zoho_crm_data['data'][0]['Selected_Plan'] =$params->SelectedPlanType;
            }
            return $zoho_crm_data;

        }

        public function dataFormatePaymentUpdate($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->deal_id,
                    "Total_Service_Fee"=>(int)$params->SelectedPlan->total_amount,
                    "Amount_Paid"=>(int)$params->SelectedPlan->paid_amount,
                    "Tax_Applicable"=>strval($params->SelectedPlan->tax_amount),
                    "Balance_Amount"=>(int)$params->SelectedPlan->balance_amount,
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            if($params->SelectdPlanType != null)
            {
                $zoho_crm_data['data'][0]['Selected_Plan'] =$params->SelectdPlanType;
            }
            return $zoho_crm_data;
        }

        public function dataFormateContact($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_contact_id,
                    "First_Name"=>$params->first_name,
                    "Last_Name"=>$params->last_name,
                    "Email"=>$params->email,
                    "Mobile"=>$params->mobile,
                    "Mailing_Street"=>$params->mailing_street,
                    "Mailing_Region"=>$params->mailing_region,
                    "Mailing_City"=>$params->mailing_city,
                    "Mailing_State_Code"=>$params->mailing_state_code,
                    "Mailing_Country"=>$params->mailing_country,
                    "Mailing_State_UT"=>$params->mailing_state_ut,
                    "Mailing_Zip"=>$params->mailing_zip,
                    "Use_Billing_Address"=>$params->use_billing_address,
                    "Billing_Name"=>$params->billing_name,
                    "Billing_Email"=>$params->billing_email,
                    "Billing_Phone"=>$params->billing_phone,
                    "Billing_Street"=>$params->billing_street,
                    "Billing_City"=>$params->billing_city,
                    "Billing_State"=>$params->billing_state,
                    "Billing_State_Code"=>$params->billing_state_code,
                    "Billing_Zip"=>$params->billing_zip,
                    "GST_Number"=>$params->gst_number,
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

        public function dataFormateCompany($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->zoho_company_id,
                    "Account_Name"=>$params->company_name,
                    "GST_Number"=>$params->gst_number,
                    "Billing_Street"=>$params->billing_street,
                    "Billing_Landmark"=>$params->billing_landmark,
                    "Billing_City"=>$params->billing_city,
                    "Billing_State_UT"=>$params->billing_state_ut,
                    "Billing_State_Code"=>$params->billing_state_code,
                    "Billing_Code"=>$params->billing_code,
                    "Billing_Country"=>$params->billing_country,
                    "Shipping_Street"=>$params->shipping_street,
                    "Shipping_Landmark"=>$params->shipping_landmark,
                    "Shipping_City"=>$params->shipping_city,
                    "Shipping_State_UT"=>$params->shipping_state_ut,
                    "Shipping_State_Code"=>$params->shipping_state_code,
                    "Shipping_Code"=>$params->shipping_code,
                    "Shipping_Country"=>$params->shipping_country,
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

        public function dataFormateQuotes($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"Account_Name" => $params->company_id,
					"Deal_Name" => $params->deal_id,
					"Contact_Name" => $params->customer_id,
					"Valid_Till" => date("Y-m-d", strtotime("+ 7 day")),
					"Subject" => $params->Quotation->quotation_no,
					"Description" => $params->Quotation->description,
					"Discount_Percent" => (float)$params->Quotation->discount,
					"Quote_Stage" => "Draft",
                    "Discount" => (int)$params->Quotation->discount_amount,
                    "Tax_Amount" => (int)$params->Quotation->tax_amount,
                    "Quotation_Primary_Id" => (int)$params->Quotation->id,
                    "Owner" => (new self)->getzohouserid($params->ise_user_id),
					"Quoted_Items" => array(array('Service_Fee_Type'=>$params->PlanDetails->plan_type,"Quantity"=>1,'Product_Name'=>$params->PlanDetails->zoho_plan_id,'List_Price'=>(int)$params->Quotation->base_amount,"Total"=>(int)$params->Quotation->base_amount)),
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

        public function dataFormatPaymentAdd($params = array())
        {
            $objDateTime = new DateTime($params->ServiceInvoice->created_on);
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"Name" => $params->ServiceRequest->firstname,
					"Payment_Type" => ($params->ServicePayment->payment_type =='ADVC')?'Advance Payment':'Other',
					"Amount_Type" => ($params->ServicePayment->payment_type =='ADVC')?'Advance Recovery Fee':'Final Recovery Fee',
					//"Valid_Till" => date("Y-m-d", strtotime("+ 7 day")),
					"Payment_Channel" => $params->ServicePayment->payment_channel,
					"Create_Invoice_For" => ($params->ServicePayment->existing_payment==null)?'Existing Payment':$params->ServicePayment->existing_payment,
					"Source" => $params->ServicePayment->payment_mode,
					"Base_Amount" => (int)$params->ServiceInvoice->base_amount,
                    "Transaction_ID" => $params->ServicePayment->payment_txnid,
                    "Tax_Applicable" =>  ($params->ServicePayment->tax_rate==null || $params->ServicePayment->tax_rate==0)?'Without GST':'With GST',
                    "Status" => 'success',
                    "Invoice_Status" => 'Invoice Generated',
					"Payment_Time_Stamp" => $objDateTime->format('c'),
                    "Account_Type" =>"Service",
                    "Amount" =>(int)$params->ServiceInvoice->final_amount,
                    "GST" =>(int)$params->ServicePayment->total_tax,
                    "ServicePaymentId" =>(int)$params->ServicePayment->id,
                    "Invoice_ID" =>$params->ServiceInvoice->invoice_no,
                    "Invoice_Link" =>env('MIMS_BASE_URL')."view-invoice/".$params->ServiceInvoice->id."/".$params->ServiceInvoice->request_id,
                    "ARN_Number" =>$params->ServiceInvoice->arn_num,
                    "IRN_Number"=>$params->ServiceInvoice->irn_code,
                    "Customer_Type"=>($params->ServiceInvoice->irn_code == null)?'B2C':'B2B',
                    "GST_IN" =>$params->ServiceRequest->gst_no,
                    "Email" =>$params->ServiceRequest->email,
                    "Customer_Mobile" =>$params->ServiceRequest->phone,
                    "Deal_Name"=>$params->deal_id,
                    "Contact_Name"=>$params->customer_id,
                    "Order_ID" =>$params->ServiceRequest->order_no,
                    "SEZ_Invoice"=>($params->ServiceRequest->sez ==1)?true:false,
                  //  "Branch"=>$params->ServiceInvoice->branch,
                    "City"=>$params->ServiceRequest->city,
                    "State"=>$params->ServiceRequest->state,
                    "Country"=>"India",
                    "Address1"=>$params->ServiceRequest->address,
                    "Address2"=>$params->ServiceRequest->landmark,
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

        public function dataFormatPaymentUpdate($params = array())
        {
            $zoho_crm_data = array(
				"data" => array(
				  array(
                    "id" => $params->ServicePayment->zoho_payment_id,
					"Name" => $params->ServiceRequest->firstname,
                    "Email" => $params->ServiceRequest->email,
                    "Customer_Mobile" => $params->ServiceRequest->phone,
                    "Address1"  => $params->ServiceRequest->address,
                    "Address2"  => $params->ServiceRequest->landmark,
                    "City"  => $params->ServiceRequest->city,
                    "State" => $params->ServiceRequest->state,
                    "State_Code" => $params->ServiceRequest->state_code,
                    "Pincode"   => strval($params->ServiceRequest->zipcode),
					"Payment_Channel" => $params->ServicePayment->payment_channel,
                    "Payment_Type" => ($params->ServicePayment->payment_channel=="Online" && $params->ServicePayment->payment_mode =="Payu")?'Online Payment Received':'Offline Payment Received',
					"Source" => $params->ServicePayment->payment_mode,
                    "Invoice_Status" => ($params->irn_status==0)?'Invoice Generated Without IRN':'Invoice Generated',
                    "Status" => 'success',
                    "Amount" => $params->final_amount,
                    "GST"    => round(($params->igst + $params->ugst + $params->sgst + $params->cgst + $params->gst_cess)),
                    "Transaction_ID" => $params->ServicePayment->payment_txnid,
                    "Payment_Time_Stamp"=>date(DATE_ATOM, strtotime($params->ServicePayment->payment_timestamp)),
                    "Invoice_Date" => date("Y-m-d", strtotime($params->created_on)),
                    "Invoice_ID"   => $params->invoice_no,
                    "Invoice_Link" => env('MIMS_BASE_URL').'view-invoice/'.$params->id.'/'.$params->request_id,
                    "Customer_Invoice_Links"=> env('MIMS_BASE_URL').'view-invoice/'.$params->id.'/'.$params->request_id,
                    'SEZ_Invoice' => ($params->ServiceRequest->sez==1)?true:false,
                    "ARN_Number" => $params->arn_num,
                    "IRN_Number"=> $params->irn_code,
                    "GST_IN" => $params->ServiceRequest->gst_no,
                    "Order_ID" => $params->ServiceRequest->order_no,
                    "Product_Info" => $params->ServicePayment->payment_item,
                    //"Branch" => $params->branch,
                    "Country" =>"India"
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
        public function dataFormatObservation($params = array())
        {
            $caseNotPossible = null;
            $Not_Done_Remarks = null;
            if($params->recovery_possibility == 'No')
            {
                $caseNotPossible = implode(", ",$params->no_recovery_reason);
                if($params->no_recovery_reason_other != null)
                $caseNotPossible = $caseNotPossible.",".$params->no_recovery_reason_other;
                $Not_Done_Remarks = $params['remarks'];
            }
            $stege = Stage::find($params->stage);
            $zoho_crm_data = array(
				"data" => array(
				  array(
					"id" => $params->deal_id,					
					"Notes_for_Customer" => $params->notes,
					"Case_Not_Done_Reason" => $caseNotPossible,
                    "Not_Done_Remarks" => $Not_Done_Remarks,
                    "MIMS_Notes" =>$params->remarks,
                    "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
				  )
				),
				"trigger" => array(
					"approval",
					"workflow",
					"blueprint",
				)
			);
            if($params->recovery_possibility == 'No')
            {
                $zoho_crm_data['data'][0]['Current_Status'] =$stege->stage_name;
            }
            return $zoho_crm_data;
        }

        public function dataFormateRecovery($params = array())
        {
            $caseNotPossible = null;
            $Not_Done_Remarks = null;
            if($params->stage == 14)
            {
                $caseNotPossible = implode(",",$params->no_recovery_reason);
                if($params->no_recovery_reason_other != null)
                $caseNotPossible = $caseNotPossible.",".$params->no_recovery_reason_other;
                $Not_Done_Remarks = $params['remarks'];
            }

            $zoho_crm_data = array(
				"data" => array(
				  array(
                    "id" => $params->deal_id,
					"Clone_Creation_Completed" => $params['Recovery']['clone_creation'],
					"Data_Encrypted" => $params['Recovery']['data_encrypted'],
					"Decryption_Details_Received" => $params['Recovery']['decryption_details'],
					"Data_Decryption_Successful" => $params['Recovery']['decryption_data'],
					"Request_for_Correct_Encryption_details" => $params['Recovery']['decryption_details_send'],
					"Data_Verification" => $params['Recovery']['recoverable_data'],
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
                $zoho_crm_data['data'][0]['Case_Not_Done_Reason'] =$caseNotPossible;
                $zoho_crm_data['data'][0]['Not_Done_Remarks'] =$Not_Done_Remarks;
            }

            return $zoho_crm_data;
        }

        public function dataFormateDL($params = array())
        {
           $zoho_crm_data = array(
                "data" => array(
                  array(
                    "id" => $params->deal_id,
                    "Type_of_Data_Recovered" =>str_replace(["[","]",'"'],'','"'.$params['DL']['data_recovered'].'"'),
                    "Recovered_Data_Size" => $params['DL']['recoverable_data'],
                    "Total_Number_of_Files" => $params['DL']['total_file'],
                    "Total_Data_Size" => $params['DL']['total_data_size']." ".$params['DL']['total_data_size_format'],
                    "Current_Status"=>"Directory Listing Submitted",
                    "Upload_Directory_Listing" =>$params['DL']['directory_listing'],
                    "MIMS_Notes" =>$params->remarks,
                    "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
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

    public function dataFormateDLConfirm($params = array())
    {
        $stege = Stage::find($params->stage);
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "Mode_of_Data_Verification" =>$params['DL']['data_varification'],
                "Mode_of_Data_verification_approval" => $params['DL']['data_varification_approval'],
                "Data_Recovery_Results" => $params['DL']['data_recovery_result'],
                "Data_Delivery_Mode" => $params['DL']['copyin'],
                "Peripheral_Details" =>$params['DL']['peripheral_details'],
                "Current_Status" =>$stege->stage_name,
                "Rework_Required" =>$params['DL']['rework'],
                "MIMS_Notes" =>$params->remarks,
                "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
              )
            ),
            "trigger" => array(
                "approval",
                "workflow",
                "blueprint",
            )
        );
        if($params['DL']['copyin_details']  !=null && $params['DL']['rework'] !='Yes')
        {
            $datas = $params['DL']['copyin_details'];
            if(array_key_exists('media_sn',$datas))
            $zoho_crm_data['data'][0]['Client_Make_Model'] = $datas[0]['media_sn'];
            if(array_key_exists('media_model',$datas))
            $zoho_crm_data['data'][0]['Client_Serial_No'] = $datas[0]['media_model'];
            if(array_key_exists('cdSize',$datas))
            $zoho_crm_data['data'][0]['CD_DVD_Size'] = $datas[0]['cdSize'];
        }
        return $zoho_crm_data;
    }

    public function dataFormateRework($params = array())
    {
        $stege = Stage::find($params->stage);
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "Rework_Possible" =>$params['DL']['rework_possible'],
                "Enter_Remark" => $params->remarks,
                "Current_Status" =>$stege->stage_name,
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

    public function dataFormateDatOut($params = array())
    {
        $stege = Stage::find($params->stage);
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id, 
                "Data_Copy_Done" =>$params['DL']['data_copy_status'],
                "Data_Delivery_Mode" => $params['DL']['copyin'],
                "Mode_of_Data_Out" => $params['DL']['data_out_mode'],
                "Data_Out_Name" => $params['DL']['ref_name'],
                "Data_Out_Mobile" => $params['DL']['ref_mobile'],
                "ID_Proof" => $params['DL']['id_proof'],
                "Document_Number" => $params['DL']['ref_no'],
                "Courier_Company_Name" => $params['DL']['courier_company_name'],
                "Courier_Address" => $params['DL']['courier_address'],
                "Address_Same_As_Media_In" => $params['DL']['address_same_as_mediain'],
                "Current_Status" =>$stege->stage_name,
                "MIMS_Notes" =>$params->remarks,
                "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
              )
            ),
            "trigger" => array(
                "approval",
                "workflow",
                "blueprint",
            )
        );
        if($params['DL']['copyin_details']  !=null && $params['DL']['copyin'] !='Online Transfer' && $params['actionType'] =='DATA-OUT-TECH')
        {
            $datas = $params['DL']['copyin_details'];
            if(array_key_exists('media_sn',$datas))
            $zoho_crm_data['data'][0]['Client_Make_Model'] = $datas[0]['media_sn'];
            if(array_key_exists('media_model',$datas))
            $zoho_crm_data['data'][0]['Client_Serial_No'] = $datas[0]['media_model'];
            if(array_key_exists('cdSize',$datas))
            $zoho_crm_data['data'][0]['CD_DVD_Size'] = $datas[0]['cdSize'];
        }
        return $zoho_crm_data;
    }
   
    public function dataFormateDailyStatus($params = array())
    {
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "MIMS_Notes" =>$params['DAILY']['status'],
                "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
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

    public function dataFormateMediaOutClient($params = array())
    {
        $stege = Stage::find($params->stage);
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "Current_Status" =>$stege->stage_name,
                "Request_for_Media_Out" =>$params['MediaClientOut']['media_out_Type'],
                "Mode_of_Media_Out" => $params['MediaClientOut']['media_out_mode'],
                "Media_Out_Name" => $params['MediaClientOut']['ref_name'],
                "Media_Out_Mobile" => $params['MediaClientOut']['ref_mobile'],
                "MO_Courier_Company_Name" =>$params['MediaClientOut']['courier_company_name'],
                "MO_Document_Number" =>$params['MediaClientOut']['ref_no'],
                "MO_Address_Same_As_Media_In" =>$params['MediaClientOut']['same_as_address'],
                "MO_Courier_Address" =>$params['MediaClientOut']['courier_address'],
                "Media_Out_ID_Proof" =>$params['MediaClientOut']['id_proof'],
                "MIMS_Notes" =>$params->remarks,
                "MIMS_Notes_Title" =>!empty((new self)->_getUserName(auth()->user()->id)) ? (new self)->_getUserName(auth()->user()->id).' (MIMS)' : "MIMS User",
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

    public function WipingRequest($params = array())
    {
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "Data_Wiping_Requested" =>"Yes",
                "Wiping_Request_Date" =>date('Y-m-d',strtotime($params['Wiping']['request_wiping_date'])),
                "Wiping_Done" => "Pending",
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

    public function WipingRequestUpdate($params = array())
    {
        $zoho_crm_data = array(
            "data" => array(
              array(
                "id" => $params->deal_id,
                "No_Wiping_Reason" =>$params->remarks,
                "Wiping_Date" =>date('Y-m-d',strtotime($params['Wiping']['approve_wiping_date'])),
                "Wiping_Done" => $params->Wiping->wiping_status
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
        
        public function _getClientName($id)
        {
           $client = Contact::where('zoho_contact_id',$id)->first();
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

        public function getzohouserid($userId)
        {
            if($userId != null)
            {
              $user = User::find($userId);
              return $user->zoho_user_id;
            }
            else
            return null;
        }
}