<?php 
namespace App\Helpers;
use DB;
class Zoho
{
    
    public static function getZohoCrmAuthToken(){
        //sandbox
        $zoho_token_request = 'refresh_token=1000.860a9270502a6d0f574d7425ea2e7607.e3fdd21a4a6055f62914f44c35974fa4&client_id=1000.T6JSHXG93T9Y4FTURO5RV6UMG23GBE&client_secret=c45808c9c34377da420952f2c54f8696663f49011f&grant_type=refresh_token';
        //production
        $zoho_header = array(
        'Content-Type: application/x-www-form-urlencoded'
        );
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
          Zoho::logApiCall($log_data);
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
                Zoho::logApiCall($log_data);
                return false;
            }
        }
    }

    public static function logApiCall($log_data = array()){
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
}