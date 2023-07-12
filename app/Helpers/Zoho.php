<?php 
namespace App\Helpers;
use DB;
use Carbon\Carbon; 
class Zoho
{

    public static function getZohoCrmAuthToken()
    {
        //sandbox
        $zoho_token_request = 'refresh_token=1000.860a9270502a6d0f574d7425ea2e7607.e3fdd21a4a6055f62914f44c35974fa4&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token';
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

      public static function  _insertMediaHistory($media,$type,$module,$user,$remarks)
      {
          DB::insert('insert into media_history (media_id,action_type,module_type,added_by,remarks,added_on,status) values (?,?,?,?,?,?,?)', array($media->id,
                        $type,$module,$user,$remarks,Carbon::now()->toDateTimeString(),$media->stage));
      }
}