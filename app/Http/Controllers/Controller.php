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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
      $user = User::find($userId);
      return $user->name;
  }

  protected function _getUerEmail($userId)
  {
      $user = User::find($userId);
      return $user->email;
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

  protected function _sendMail($msg,$subject,$to)
  {
    Mail::html($msg, function($message) use ($msg,$to, $subject){
      $message->from('vipin.kumar@stellarinfo.com');
      $message->to(is_array($to)?$to:\explode(",",$to))->subject($subject);
    });
     
  }

  protected function _getUserIdEmail($branchId)
  {
     $userId = array();
     $userEmail = array();
     $branchs = BranchRelated::where('branch_id',$branchId)->get();
     foreach($branchs as $branch)
        {
            $userId[] = $branch->user_id;
        }
        if(count($userId) > 0)
        {
          $users = User::whereIn('id',$userId)->get();
          foreach($users as $user)
          {
              $userEmail[$user->id] = $user->email;
          }
        }
        return $userEmail;
  }

  protected function _sendMailTransferMedia($transfer,$media)
  {
          $userEmail =$this->_getUserIdEmail($transfer->new_branch_id);
          $content = "Media Transferred ".$this->_getBranchName($transfer->old_branch_id)." to ".$this->_getBranchName($transfer->new_branch_id)." by ".$this->_getUserName(auth()->user()->id).".";
          if(count($userEmail) > 0)
          $sendmail = $this->_sendMail($content,"Media Transfer",$userEmail);
  }

  protected function _sendMailMediaIn($media)
  {   
            $userEmail =$this->_getUserIdEmail($media->branch_id);
            $content = "New Media Added ".$this->_getBranchName($media->branch_id);
            if(count($userEmail) > 0)
            $sendmail = $this->_sendMail($content,"New Media",$userEmail);
  }

  protected function _sendMailAssigneeChange($oldMedia,$newMedia)
  {
       $userEmail =$this->_getUserIdEmail($newMedia->branch_id);
       if($oldMedia->user_id != null)
         $content = "Media Assignee has been Changed ".$this->_getUserName($oldMedia->user_id)." to ".$this->_getUserName($newMedia->user_id);
         else
         $content = "Media Assignee has been Changed ".$this->_getUserName($newMedia->user_id);
         if(count($userEmail) > 0)
            $sendmail = $this->_sendMail($content,"Media Assignee Change",$userEmail);
  }

  protected function _sendMailMediaStatusChanged($oldMedia,$newMedia)
  {
          $userEmail =$this->_getUserIdEmail($newMedia->user_id);
          $content = "Media Status has been Changed ".$this->_getStageName($oldMedia->stage)." to ".$this->_getStageName($newMedia->stage);
          if(count($userEmail) > 0)
            $sendmail = $this->_sendMail($content,"Media Status Change",$userEmail);
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

}
