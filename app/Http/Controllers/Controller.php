<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use App\Models\Branch;
use App\Models\User;
use App\Models\Stage;
use App\Models\BranchRelated;
use Carbon\Carbon; 
use DB;
use App\Models\MediaTeam;
use App\Models\JobBasePrice;
use App\Models\JobPriceRate;
use App\Models\JobServicePlan;
use App\Models\Pincode;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected  function  idDecodeAndEncode($action, $string) {
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

  protected function calculateEstimatedDays($type,$days)
  {
      $finalDay = null;
      if($type == '1')
      {
          $finalDay = $days;
      }
      elseif($type == '2')
      {
          $finalDay = $days *2;
      }
      elseif($type == '3')
      {
          $finalDay = $days/2;
      }
      return ceil($finalDay);
  }

  protected function _checkPinCode($stateCode,$pinCode)
  {
      $pin_state = Pincode::where('pin_code',$pinCode)->first();
      if(($pin_state == null) || ($stateCode == $pin_state->state_code))
        return true;
      else
      return false;
  }

    protected function setRecoveryPrice($data = array()){
      $media_type = isset($data['media_type']) ? trim($data['media_type']) : '';
      $media_capacity = isset($data['media_capacity']) ? trim($data['media_capacity']) : '';
      $tampered_status = isset($data['tampered_status']) ? trim(strtoupper($data['tampered_status'])) : '';
      $encryption_status = isset($data['encryption_status']) ? trim(strtoupper($data['encryption_status'])) : '';
      $case_type = isset($data['case_type']) ? trim(strtoupper($data['case_type'])) : '';	
      
      $filter_case_type = array(
        'case_type'   => $case_type,
        'tampered_status'   => $tampered_status,
      );
      $case_type_code = $this->getCaseTypeCode($filter_case_type);//TMP
 
      $device_code = $this->getDeviceCode($media_type);
 
      $capacity_units = array('KB','MB','GB','TB');
      $capacity_array = explode(' ', strtoupper($media_capacity));
      $capacity_size = $capacity_unit = '';
      foreach ($capacity_array as $value) {
          if(is_numeric($value)){
          $capacity_size = $value;
        }
        if(in_array($value, $capacity_units)){
          $capacity_unit = $value;
        }
      }
      
      $filter_price = array(
        'device_code' => $device_code,
        'capacity_size' => $capacity_size,
        'capacity_unit' => $capacity_unit,
        'case_type_code' => $case_type_code,
        'encryption' => $encryption_status,
      );

      $recovery_price_details = $this->getRecoveryPrice($filter_price);
      $recovery_plan_details = $this->getServicePlans($recovery_price_details);
      return $recovery_plan_details;
    }

    protected function getServicePlans($data = array()){
      $recovery_price = isset($data['recovery_price']) ? trim($data['recovery_price']) : '';
      $tax_rate = isset($data['tax_rate']) ? trim($data['tax_rate']) : '';
      $device_code = isset($data['device_code']) ? trim($data['device_code']) : '';
      $case_type_code = isset($data['case_type_code']) ? trim($data['case_type_code']) : '';
      $capacity_size = isset($data['capacity_size']) ? trim($data['capacity_size']) : '';
      $capacity_unit = isset($data['capacity_unit']) ? trim($data['capacity_unit']) : '';
      $encryption = isset($data['encryption']) ? trim($data['encryption']) : '';
      $plans = array();
      
      $device_for_no_plan = array('OTHHDD','IPHONE','ANDROID','CD','AUDREC','FLOPPY','VCR','BLURAY');
      $case_for_no_plan = array('TMP','LG','LGMCX','PH','PHHF');
      if(!in_array($device_code, $device_for_no_plan) && !in_array($case_type_code, $case_for_no_plan)){
         $plan_result = JobServicePlan::where('service_type','recovery')->whereIn('plan_type',['Standard','Economy','Priority'])->get();
      }
      else{
         $plan_result = JobServicePlan::where('service_type','recovery')->Where('plan_type','Standard')->get();
      }
      
        foreach($plan_result as $plan_details){
          if($case_type_code == 'LC'){
            $plan_rate = $plan_details['plan_rate_lc'];
          } else if($case_type_code == 'PH'){
          $plan_rate = $plan_details['plan_rate_ph'];
          } else if($case_type_code == 'PC'){
          $plan_rate = $plan_details['plan_rate_pc'];
          } else if($case_type_code == 'LGMCX'){
          $plan_rate = $plan_details['plan_rate_lgmcx'];
          } else if($case_type_code == 'PHHF'){
          $plan_rate = $plan_details['plan_rate_phhf'];
          } else {
          $plan_rate = $plan_details['plan_rate'];
          }
          
          if($encryption == 'YES' && in_array($case_type_code, array('LC','PC','LGMCX','PHHF'))){
            if($capacity_unit == 'TB' || ($capacity_unit == 'GB' && $capacity_size > 500)){
            $encryption_fee = 5000;
            } else {
              $encryption_fee = 2500;
            }
            } else{
            $encryption_fee = 0;
          }
          $plan_price = $recovery_price + (($recovery_price * $plan_rate)/100);
          $plan_details['amount'] = (int)(round( $plan_price / 100 ) * 100) + $encryption_fee;
          $plan_details['advance_percent'] = 50;
          $plan_details['advance_amount'] = round(($plan_details['amount'] * $plan_details['advance_percent'])/100);
          $plan_details['tax_rate'] = $tax_rate;
          $plan_details['case_type_code'] = $case_type_code;
          $plan_details['capacity_size'] = $capacity_size;
          $plan_details['capacity_unit'] = $capacity_unit;
          $plans[] = $plan_details;
        }
            
      return $plans;
    }

    protected function getCaseTypeColumn($case_type){
      $case_type_code = isset($case_type) ? trim($case_type) : '';
      $case_type_column = '';
      $cases = array(
        "LG"   => 'simple_logical',
        "LC"   => 'complex_logical',
        "LGMCX" => 'mcx_logical',				
        "PH"   => 'simple_physical',
        "PC"   => 'complex_physical',
        "PHHF" => 'hf_physical',
        "TMP"  => 'tampered',
        );
      
      if(array_key_exists($case_type_code,$cases) && !empty($case_type_code)){
        $case_type_column = $cases[$case_type_code];
      }
      return $case_type_column;
    }
  protected function getRecoveryPrice($data = array()){
	$base_price = 0;
	$recovery_price = 0;
	$tax_rate = 0;
	$device_code = isset($data['device_code']) ? trim($data['device_code']) : '';
	$capacity_size = isset($data['capacity_size']) ? trim($data['capacity_size']) : '';
	$capacity_unit = isset($data['capacity_unit']) ? trim($data['capacity_unit']) : '';
	$case_type_code = isset($data['case_type_code']) ? trim($data['case_type_code']) : '';
	$encryption = isset($data['encryption']) ? trim($data['encryption']) : '';
	$case_type_column = $this->getCaseTypeColumn($case_type_code);
  if($capacity_unit == 'TB'){
    $capacity_size_gb = round($capacity_size*1024);
 }
 else{
     $capacity_size_gb = $capacity_size;
 }
	$device_for_no_capacity = array('IPHONE','ANDROID','CD','AUDREC','FLOPPY','VCR','BLURAY');
	if(in_array($device_code, $device_for_no_capacity)){
	  $base_price_row = JobBasePrice::where('service_type','recovery')->where('device_type',$device_code)->first();
	}
	else{
    $base_price_row = JobBasePrice::where('service_type','recovery')->where('device_type',$device_code)->where('capacity_size',$capacity_size)->where('capacity_unit',$capacity_unit)->first();
	}
	if($base_price_row !=null && $base_price_row !='' && !empty($case_type_column)){
	  $base_price = $base_price_row['base_price'];
	}
else{ 
    $base_price_row = JobBasePrice::where('service_type','recovery')->where('device_type',$device_code)->get();
    $base_price_data= array();
    if(count($base_price_row) > 0)
    {
      foreach($base_price_row as $price_details)
      {
         $base_price= array();
         $base_price['size']=$price_details['capacity_size'];
			   $base_price['unit'] = $price_details['capacity_unit'];
         if($price_details['capacity_unit'] == 'TB'){
			  	$base_price['size_gb']=round($price_details['capacity_size']*1024);
			  }
			  else{
			  	$base_price['size_gb']=$price_details['capacity_size'];
			  }
			    $base_price_data[] = $base_price;
      }
    }
    $sort_by = array_column($base_price_data, 'size_gb');
		array_multisort($sort_by, SORT_ASC, $base_price_data);
    $closest_size =  $this->getClosest($capacity_size_gb,$base_price_data);
		$key = array_search($closest_size, array_column($base_price_data, 'size_gb'));
		$base_size =  $base_price_data[$key]['size'].$base_price_data[$key]['unit'];
    $base_price_row = JobBasePrice::where('service_type','recovery')->where('device_type',$device_code)->where('capacity_size',$base_price_data[$key]['size'])->where('capacity_unit',$base_price_data[$key]['unit'])->first();

    if($base_price_row !=null && $base_price_row !='')
    {   
      $base_price = $base_price_row['base_price'];
    }
}

if($base_price !=null && $base_price !=0)
{
   $rate_num_row = JobPriceRate::where('service_type','recovery')->where('device_type',$device_code)->first();
	if($rate_num_row !=null && $rate_num_row !=''){
		 $rate = $rate_num_row[$case_type_column];
		$recovery_price = $base_price + (($base_price * $rate)/100);
		$recovery_price = round( $recovery_price / 100 ) * 100;
		$tax_rate = $rate_num_row['tax_rate'];
	  }
  }
	
	$recovery_price_details = array(
		'recovery_price' => $recovery_price,
		'tax_rate' => $tax_rate,
		'device_code' => $device_code,
		'case_type_code' => $case_type_code,
		'capacity_size' => $capacity_size,
		'capacity_unit' => $capacity_unit,
		'encryption' => $encryption,
	);
	
	return $recovery_price_details;
}

protected function getClosest($search, $arr) {
  $count = count($arr);
  $closest = null;
  foreach ($arr as $a=>$item) {
  if($search == $item['size_gb']){
      $closest = $item['size_gb'];
  }
  else if ($closest === null || ((abs($search - $closest) > abs($item['size_gb'] - $search) || abs($search - $closest) == abs($item['size_gb'] - $search)) && $item['size_gb'] > $search) || ((abs($search - $closest) < abs($item['size_gb'] - $search)) && $search > $closest)) {
   $closest = $item['size_gb'];
  }
 else if($count == $a+1 && $item['size_gb'] < $search) {
   $closest = $item['size_gb'];
  }
  }
  return $closest;
}

    protected function getCaseTypeCode($data = array()){
      $case_type = isset($data['case_type']) ? trim($data['case_type']) : '';
      $tampered_status = isset($data['tampered_status']) ? trim($data['tampered_status']) : '';
      $case_type_code = '';
      
      if($tampered_status == 'TAMPERED' || $tampered_status == 'NOT DETERMINED AT PRESENT STAGE'){
        $case_type_code = 'TMP';
      }
      else if($case_type == 'LOGICAL'){
        $case_type_code = 'LG';
      }
      else if($case_type == 'LOGICAL COMPLEX'){
        $case_type_code = 'LC';
      }
      else if($case_type == 'MOST COMPLEX'){
        $case_type_code = 'LGMCX';
      } 
      else if($case_type == 'PHYSICAL'){
        $case_type_code = 'PH';
      }
      else if($case_type == 'PHYSICAL COMPLEX' || $case_type == 'LOGICAL CUM PHYSICAL'){
        $case_type_code = 'PC';
      }
      else{
        $case_type_code = 'LG';
      }
      
      return $case_type_code;
  }
  protected function getDeviceCode($device){
    $device = trim(strtoupper($device));
    $device_code = '';
    $devices = array(
      "HARD DRIVE" => 'HDD',
      "EXTERNAL HARD DRIVE" => 'HDD',
      "EXTERNAL SOLID STATE DRIVE" => 'SSD',
      "SOLID STATE DRIVE" => 'INTSSD',
      "LAPTOP HDD" => 'HDD',      
      "EXTERNAL HARD DISK" => 'HDD',
      "SD CARD" => 'FLASH',
      "DEAD ANDROID PHONE" => 'ANDROID',
      "DEAD IPHONE" => 'IPHONE',
      "MEMORY CARD" => 'FLASH',
      "DVR" => 'DVR',
      "PEN DRIVE" => 'FLASH',
      "RAID SERVER" => '',
      "USB BOX" => '',
      "NAS BOX" => '',
      "TAPE DRIVE" => '',
      "VIRTUAL SERVER" => '',
      "CD/DVD" => 'CD',
      "FLOPPY DRIVE" => 'FLOPPY',
      "FLASH MEDIA" => 'FLASH',
      "SAN BOX" => '',
      "SAS DRIVE" => 'SAS',
      "SCSI DRIVE" => 'SCSI',
      "CLOUD" => '',
      "OTHER" => 'OTHHDD',		
      );
    
    if(array_key_exists($device,$devices) && !empty($device)){
      $device_code = $devices[$device];
    }
    return $device_code;
  }


  protected function _getPaginatedResult($query,$request)
  {
    $query->orderBy($request->input('orderBy'), $request->input('order'));
     $pageSize = $request->input('pageSize');
     $data = $query->paginate($pageSize,['*'],'page_no');
     $results = $data->items();
    $count = $data->total();
    $data = [
      "draw" => $request->input('draw'),
      "recordsTotal" => $count,
      "data" => $results
    ];
    return json_encode($data);
  }

  protected function _getBranchName($id)
  {
    $branch = Branch::find($id);
    return $branch->branch_name ;
  }

  protected function _getUserName($userId)
  {
    if($userId != null)
    {
      $user = User::find($userId);
      return $user->name;
    }
    else
    return null;

  }

  protected function _getUserTeamId($userId)
  {
      $user = User::find($userId);
      return $user->team_id;
  }

  protected function _getUserDetails($userId)
  {
      $user = User::find($userId);
      return $user;
  }

  protected function _getTeamName($id)
  {
      $team = MediaTeam::find($id);
      return $team->team_name;
  }

  protected function _getStageName($id)
  {
      $stage = Stage::find($id);
      return $stage->stage_name;
  }

  protected function StatusUpdateHistory($oldBoj,$media)
  {
    if($oldBoj->stage != $media->stage)
    {
        $content = "Media Status has been Changed From ".$this->_getStageName($oldBoj->stage)." to ".$this->_getStageName($media->stage);
        $this->_insertMediaHistory($media,"edit",$content,'STATUS-UPDATE',$media->stage);
    }
  }

  protected function  _insertMediaHistory($media,$type,$remarks,$module,$status,$extStatus=null)
    {
        DB::insert('insert into media_history (media_id,added_by,action_type,remarks,module_type,added_on,status,ext_status) values (?,?,?,?,?,?,?,?)', array($media->id, auth()->user()->id,
        $type,$remarks,$module,Carbon::now()->toDateTimeString(),$status,$extStatus));
    }

  protected function _getBranchId()
  {
      $branchs = BranchRelated::where('user_id',auth()->user()->id)->get();
      $branchId = array();
      foreach($branchs as $branch)
      {
          $branchId[] = $branch->branch_id;
      }

      return $branchId;
  }

  protected function getUserBranchid($userid)
  {
    $branchs = BranchRelated::where('user_id',$userid)->first();
    return $branchs->branch_id;
  }

  protected function _userCheckBrnach($bId)
  {
      $currnetBrnach = $this->_getBranchId();
      if(in_array($bId,$currnetBrnach))
      return true;
      else
      return false;
  }

  protected function _getUserBranchId($userId)
  {
    $branch = BranchRelated::where('user_id',$userId)->get();
    return $this->_getBranchName($branch[0]->branch_id);
  }

  protected function _getFrontDeskId($branchId)
  {
      $curUser = null;
      $userId = array();
      $branchs = BranchRelated::where('branch_id',$branchId)->get();
      foreach($branchs as $branch)
      {
          $userId[] = $branch->user_id;
      }
      if(count($userId) > 0)
      {
        $users = User::whereIn('id',$userId)->where('team_id',10)->first();
        if($users != null)
          $curUser = $users->id;
      }

      return $curUser;
  }

  protected function get_query()
  {
    return 'SELECT COUNT(id) AS count_id FROM media WHERE 1';
  }

  protected function _getDueDate($startDate,$day)
  {
    $endDate = date('Y-m-d', strtotime($startDate. ' +'.$day.' days'));
    $finalDate = $this->dueDateNonWorking($startDate,$endDate);
    return $finalDate;
  }

  protected function dueDateNonWorking($startDate,$endDate)
  {
    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);
    for($i=$startTimestamp; $i<=$endTimestamp; $i = $i+(60*60*24) ){
      if(date("N",$i) ==7) 
         $endDate =  date('Y-m-d', strtotime($endDate. ' + 1 days'));
      }
      return $endDate;

  }

  protected function getPlanId($plan_type)
  {
    $result = JobServicePlan::where('plan_type','=',$plan_type)->select('plan_id')->first();
    return $result['plan_id'];
  }

  protected function _sendMail($msg,$subject,$to,$bcc=null)
  {
    Mail::html($msg, function($message) use ($msg,$to, $subject,$bcc){
      $message->from(env('MAIL_USERNAME'));
      if($bcc !=null)
      $message->bcc($bcc);
      $message->to(is_array($to)?$to:\explode(",",$to))->subject($subject);
    });
     
  }

  protected function _sendEmailtoISEUser($media)
  {
    if($media->ise_user_id != null)
    {
      $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
      $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;      
      $user = $this->_getUserDetails($media->ise_user_id);
      $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId." by user ".auth()->user()->name.".<br><br><strong>Current Status : ".$this->_getStageName($media->stage)." </strong>.<br><br> Please login into MIMS/CRM to check updates. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email));
    }
  }

  protected function _sendEmailtoTechnician($media)
  {
    if($media->user_id != null)
    {
      $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
      $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;         
      $user = $this->_getUserDetails($media->user_id);
      $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId." by user ".auth()->user()->name.".<br><br><strong>Current Status : ".$this->_getStageName($media->stage)." </strong>.<br><br> Please login into MIMS/CRM to check updates. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email));
    }
  }

  protected function _sendEmailStatusChange($media)
  {
    if($media->user_id != null)
    {
      $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
      $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;        
      $user = $this->_getUserDetails($media->user_id);
      $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId." by user ".auth()->user()->name.".<br><br><strong>Current Status : ".$this->_getStageName($media->stage)." </strong>.<br><br> Please login into MIMS/CRM to check updates. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email));
    }
  }

  protected function _sendEmailExtension($media,$status)
  {

      $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
      $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;    
      if($status == 'Pending')
      { 
        $user = $this->_getUserDetails($media->ise_user_id);
        $text = "<strong>Extension Requested : ".$media->extension_day." days<br>Extension Status : ".$status."</strong>";
      }
      else
      {
      $user = $this->_getUserDetails($media->user_id);
      $text = "<strong><br>Extension Status : ".$status."</strong>";
      }
      $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId." by user ".auth()->user()->name.".<br><br>".$text."<br><br> Please login into MIMS/CRM to check updates. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email));
  }

  protected function _sendMailAssignChange($media)
  {
    if($media->user_id != null)
    {
      $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
      $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;       
      $user = $this->_getUserDetails($media->user_id);
      $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId." by user. <br><strong>".auth()->user()->name." assigned the Task</strong><br><br> Please login into MIMS/CRM to check updates. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email));
    }
  }

  protected function _sendmailQuotation($media,$status)
  {
    if($media->ise_user_id !=null)
    {
      $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
      $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;       
      $user = $this->_getUserDetails($media->ise_user_id);
      $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId.".<br><br><strong>Quotation number ".$media->quotationNumber." has been ".$status."</strong>.<br><br> Please login into MIMS/CRM to check updates. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email));
    }
  }

  protected function _sendMailForgotPassword($user){
    if($user->email != null)
    {
      $subject = "Forgot Password ";      
      $msg = "Dear User,<br><br>"."Please login into MIMS.After login, you can change your password in profile setting.<br><br> New password is <strong>".$user->newPas."</strong><br><br> Please login into MIMS. 
              <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
      $this->_sendMail($msg,$subject,trim($user->email),'vivek@stellarinfo.com');
    }
  }

  protected function _sendEmailtoMediaOutRequest($media)
  {
    $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
    $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;       
    $user = $this->_getUserDetails($media['mediaOut']['user_id_to']);
    $iseName = $this->_getUserDetails($media['mediaOut']['user_id_from']);
    $msg = "Dear User,<br><br>"."Media Out for Job ID ".$recordId." has been request by ".$iseName->name."<br><br> Please login into MIMS/CRM to check updates. 
            <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
    $this->_sendMail($msg,$subject,trim($user->email));

  }

  protected function _sendEmailtoMediaOutResponce($media)
  {
    $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
    $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;       
    $user = $this->_getUserDetails($media['mediaOut']['user_id_from']);
    $iseName = $this->_getUserDetails($media['mediaOut']['user_id_to']);
    $msg = "Dear User,<br><br>"."Media handover has been done by Technician ".$iseName->name." To  ".$user->name."<br><br> Please login into MIMS/CRM to check updates. 
            <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
    $this->_sendMail($msg,$subject,trim($user->email));
  }

  protected function _sendEmailtoMediaWipingRequest($media)
  {
    $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
    $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;       
    $user = $this->_getUserDetails($media['Wiping']['requested_to']);
    $iseName = $this->_getUserDetails($media['Wiping']['requested_by']);
    $msg = "Dear User,<br><br>"."Request for Media Wiping for Job ID ".$recordId." has been request by ".$iseName->name."<br><br> Please login into MIMS/CRM to check updates. 
            <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
    $this->_sendMail($msg,$subject,trim($user->email));

  }


  protected function _sendEmailtoMediaWipingDone($media)
  {
    $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
    $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;       
    $iseName = $this->_getUserDetails($media['Wiping']['requested_to']);
    $user = $this->_getUserDetails($media['Wiping']['requested_by']);
    $msg = "Dear User,<br><br>"."Media Wiping Done for Job ID ".$recordId."  by ".$iseName->name."<br><br> Please login into MIMS/CRM to check updates. 
            <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
    $this->_sendMail($msg,$subject,trim($user->email));

  }

  protected function _sendMailToNotifyTech($media)
  {
    $recordId = $media->job_id == null?$media->deal_id:$media->job_id;
    $subject = "Update for record ".($media->job_id == null)?$media->deal_name:$recordId;         
    $user = $this->_getUserDetails($media->user_id);
    $msg = "Dear User,<br><br>"."Following action has been performed on record ".$recordId." by user ".auth()->user()->name.".<br><br><strong>Current Status : ".$this->_getStageName($media->stage)." </strong>.<br>".$media->Mailcontent."<br><br> Please login into MIMS/CRM to check updates. 
            <br><br>Regards,<br>Stellar Data Recovery<br>www.stellarinfo.co.in<br><br>Please do not reply to this auto-generated message.";
    $this->_sendMail($msg,$subject,trim($user->email));
  }

}
