<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use App\Models\Media;
use App\Models\BranchRelated;
use App\Models\MediaTeam;
use App\Models\MediaHistoty;
use App\Models\MediaTransfer;
use App\Models\Gatepass;
use App\Models\User;
use App\Models\Stage;
use App\Models\Branch;
use DB;
use App\Models\CustomerDetail;
use App\Models\FileUpload;
use App\Models\MediaDirectory;
class MediaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function medialist(Request $request)
    {
        $term = $request->input('term');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $status = $request->input('status');
        $searchType = $request->input('searchType');
        $searchfieldName = $request->input('searchfieldName');

        $branchId = implode(',',$this->_getBranchId());
        $select = 'media.*,transfer_media.media_id as transfer_media_id,transfer_media.new_branch_id as new_branch_id, branch.branch_name as branch_name,customer_detail.customer_name as customer_name,stage.stage_name as stage_name,transfer_media.client_media_send';
        $query = DB::table('media')->select(DB::raw($select));
        $query->leftJoin("transfer_media","media.id", "=", DB::raw("transfer_media.media_id and media.transfer_id=transfer_media.id"));
        $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
        $query->leftJoin('stage', 'stage.id', '=', 'media.stage');
        $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
        if(auth()->user()->role_id !=1 && ($searchType =='' || $searchType == null))
        $query->whereRaw("(media.branch_id in ($branchId) or transfer_media.new_branch_id in ($branchId) or transfer_media.old_branch_id in ($branchId))");
      
        if($term !=null && $term !='' && $searchfieldName !=null && $searchfieldName !='' )
        {
            if($searchfieldName == "customer_name")
                $query->where(DB::raw("customer_detail.customer_name"),'like','%'.$term.'%');
            else if($searchfieldName == "branch_id" && ($searchType =='' || $searchType == null))
               $query->whereRaw("(media.branch_id in ($term) or transfer_media.new_branch_id in ($term))");
            else if($searchfieldName == "branch_id" && $searchType !='' && $searchType != null)
               $query->whereRaw("((media.branch_id = $term AND media.transfer_id is null) OR transfer_media.new_branch_id = $term)");
            else
                $query->Where($searchfieldName, 'LIKE', '%'.$term.'%'); 
        }
        if($status != null && $status !='')
        {
            if($status == 4 && $searchType !='' && $searchType != null)
            $query->whereNotIn('media.stage', [1,2,3])->whereRaw('MONTH(media.created_on) = MONTH(CURRENT_DATE()) AND YEAR(media.created_on) = YEAR(CURRENT_DATE())');
            elseif($status == 7)
            $query->Where('media.stage', '=', 6)->Where('media.recovery_possibility','=','Yes'); 
            elseif($status == 8)
            $query->Where('media.stage', '=', 6)->Where('media.recovery_possibility','=','No');
            elseif($status == 1 || $status == 10)
            $query->WhereIn('media.stage',[1,10]);
            else
            $query->Where('media.stage', '=', $status); 
            
        }
        if($startDate != null && $endDate != null)
        {                    
                $startDate = date('Y-m-d', strtotime($startDate))." 00:00:00";            
                $endDate = date('Y-m-d', strtotime($endDate. ' + 1 days'))." 00:00:00";
                $query->whereBetween('media.created_on',[$startDate,$endDate]);
        }
        if($searchType !='' && $searchType != null){
            if($searchType == "my_assigned")
                $query->where('media.user_id',auth()->user()->id);
            else if($searchType == "assigned")
                $query->whereNotNull('user_id');
            else if($searchType == "unasigned")
                $query->whereNull('user_id');
        }
       
        $query->orderBy($request->input('orderBy'), $request->input('order'));
        $pageSize = $request->input('pageSize');
        $data = $query->paginate($pageSize,['*'],'page_no');
        $results = $data->items();
        $i = 0;
        foreach ($results as $result) {
            if($result->new_branch_id !=null)
            $result->new_branch_id =$this->_getBranchName($result->new_branch_id);
            $i++;
        }
        $count = $data->total();
        $data = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $count,
            "data" => $results
            ];
            return json_encode($data);
    }

    public function getAllHistory($mediaId)
    {
        $select = 'media_history.*,users.name as user_name';
        $query = DB::table('media_history')->select(DB::raw($select));
        $query->where('media_history.media_id', '=',$mediaId);
        $query->leftJoin('users', 'users.id', '=', 'media_history.added_by');
        $query->orderBy('id','asc');
        $history =  $query->get();
        return response()->json($history);
    }

    public function getdeptUser($deptId)
    {
        $teamUser = User::where('team_id',$deptId)->get();
        return response()->json($teamUser);
    }

    public function updateAllotJob(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        $media->user_id = $request->input('user_id');
        $media->team_id = $request->input('team_id');
        $media->save();
        $remarks = "<b>Department Name : </b>".$this->_getTeamName($media->team_id)."<br>"."<b>User Name : </b>".$this->_getUserName($media->user_id)."<br>"."<b>Reason : </b>".$request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks,'ASSIGN-CHANGE',$media->stage);
    }

    public function getTransferHistory($mediaId)
    {
        $select = 'transfer_media.*,old_branch.branch_name as oldName,new_branch.branch_name as newName';
        $query = DB::table('transfer_media')->select(DB::raw($select));
        $query->where('transfer_media.media_id', '=',$mediaId);
        $query->leftJoin('branch as old_branch', 'transfer_media.old_branch_id', '=', 'old_branch.id');
        $query->leftJoin('branch as new_branch', 'transfer_media.new_branch_id', '=', 'new_branch.id');
        $history =  $query->get();
        return response()->json($history);
    }

    protected function _getAllHistory($mediaId)
    {
        $select = 'media_history.*,users.name as user_name';
        $query = DB::table('media_history')->select(DB::raw($select));
        $query->where('media_history.media_id', '=',$mediaId);
        $query->leftJoin('users', 'users.id', '=', 'media_history.added_by');
        $query->orderBy('id','asc');
        $history =  $query->get();
        return $history;
    }

    public function getMedia($id)
    {
        $select = 'media.*,branch.branch_name as branch_name,customer_detail.customer_name as customer_name,transfer_media.transfer_code,transfer_media.new_branch_id,transfer_media.client_media_send,
                  recovery.recoverable_data as rec_recoverable_data,recovery.clone_branch as rec_clone_branch,stage.stage_name as stageName';
        $query = DB::table('media')->select(DB::raw($select));
        $query->where('media.id', '=',$id);
        $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
	    $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
        $query->leftJoin("transfer_media","transfer_media.id", "=", 'media.transfer_id');
        $query->leftJoin('recovery','recovery.media_id', '=','media.id');
        $query->leftJoin('stage','stage.id', '=','media.stage');
        $media =  $query->get();   
        if(count($media) > 0)
        {
            $media[0]->total_drive = json_decode($media[0]->total_drive);
            $media[0]->media_clone_detail = json_decode($media[0]->media_clone_detail);
            $media[0]->media_sapre_detail = json_decode($media[0]->media_sapre_detail);
            $media[0]->fileUpload = FileUpload::where('media_id',$media[0]->id)->get();
            $media[0]->Directory_Listing = MediaDirectory::where('media_id',$media[0]->id)->first();
            return response()->json($media[0]);
        }  
        else
        {
            return response()->json(null);
        }  
    }

    public function getMediaHistory($id,$type,$module)
    {
        $select = 'media_history.*,users.name as user_name';
        $query = DB::table('media_history')->select(DB::raw($select));
        $query->where('media_history.media_id', '=',$id);
        $query->where('media_history.action_type', '=',$type);
        $query->where('media_history.module_type', '=',$module);
        $query->leftJoin('users', 'users.id', '=', 'media_history.added_by');
        $query->orderBy('id','asc');
        $history =  $query->get();
        return response()->json($history);
    }

    private function _history($id,$type,$module)
    {
        $select = 'media_history.*,users.name as user_name';
        $query = DB::table('media_history')->select(DB::raw($select));
        $query->where('media_history.media_id', '=',$id);
        $query->where('media_history.action_type', '=',$type);
        $query->where('media_history.module_type', '=',$module);
        $query->leftJoin('users', 'users.id', '=', 'media_history.added_by');
        $query->orderBy('id','asc');
        $history =  $query->get();
        return $history;
    }

    public function _commanHistory($media_id)
    {
        $his = ['obserHis'=>$this->_history($media_id,'edit','observation'),
                'cloneCreation'=>$this->_history($media_id,'edit','cloneCreation'),
                'dataEncrypted'=>$this->_history($media_id,'edit','dataEncrypted'),
                'dataEncrypted'=>$this->_history($media_id,'edit','dataEncrypted'),
                'recoverableData'=>$this->_history($media_id,'edit','recoverableData'),                
                'allotJob'=>$this->_history($media_id,'edit','allotJob'),                
                'branchClone'=>$this->_history($media_id,'edit','branchClone'),                
                ];
        return response()->json($his);
    }

    public function getMediaUserList($id)
    {
        $media = Media::find($id);
        $branchId = $media->branch_id;
        $transfer_id = $media->transfer_id;
        if($branchId != null && $transfer_id !=null)
        {
            $transfer = MediaTransfer::find($transfer_id);
            $branchs = BranchRelated::whereIn('branch_id',[$branchId,$transfer->new_branch_id])->get();
        }
        else
        {
            $branchs = BranchRelated::where('branch_id',$branchId)->get();
        }        
        $userId = array();
        foreach($branchs as $branch)
        {
            $userId[] = $branch->user_id;
        }

        if($media->team_id !=0)
        {
            $teamUser = User::where('team_id',$media->team_id)->get();
            foreach($teamUser as $team)
            {
                $userId[] = $team->id;
            }
        }
        $userId = array_unique($userId);
        $users = User::whereIn('id',$userId)->get();
        return response()->json($users);
    }

    public function updateMediaTeam(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        $media->team_id = $request->input('team_id');
        $media->team_assign = 1;
        $media->save();
        $remarks = "Media Assign New Team ";
        $this->_insertMediaHistory($media,"transfer",$remarks,'media_in',$media->stage);
        return response()->json($media);
    }

    public function changeMediaAssign(Request $request)
    {
            $media = Media::find($request->input('media_id'));
            $oldMedia = $media;
            $media->user_id = $request->input('user_id');
            $media->save();
            $remarks = "Lab Technician changed to ".$this->_getUserName($media->user_id)." by ".$this->_getUserName(auth()->user()->id).".";
            $this->_insertMediaHistory($media,"assign",$remarks,$request->input('module_type'),$media->stage);
           // $this->_sendMailAssigneeChange($oldMedia,$media);
            return response()->json($media);
    }

    public function getMediaStatus($type)
    {
        if($type =="all")
        $stage = Stage::all();
        else if($type == 'jobconfirm')
        $stage = Stage::whereRaw("stage.id > 4 and stage.id !=10")->orderBy('stage_name','asc')->get();
        else
        $stage = Stage::where('type',$type)->get();
        return response()->json($stage);
    }

    public function updateMediaAnalysis(Request $request)
    {
        $id = $request->input('id');
        $media = Media::find($id);
        $oldMedia = $media;
        $media->branch_type = $request->input('branch_type');
        $media->media_type = $request->input('media_type');
        $media->drive_count = $request->input('drive_count');
        $media->media_interface = $request->input('media_interface');
        $media->media_casing = $request->input('media_casing');
        $media->service_mode = $request->input('service_mode');
        $media->service_type = $request->input('service_type');
        $media->media_make = $request->input('media_make');
        $media->media_model = $request->input('media_model');
        $media->media_serial = $request->input('media_serial');
        $media->media_capacity = $request->input('media_capacity');
        $media->tampered_status = $request->input('tampered_status');
        $media->media_condition = $request->input('media_condition');
        $media->media_status = $request->input('media_status');
        $media->model_storage = $request->input('model_storage');
        $media->stage = $request->input('stage');
        $media->media_category = $request->input('media_category');
        $media->total_drive = json_encode($request->input('total_drive'));
        $media->peripherals_details = $request->input('peripherals_details');
        $media->last_updated  = Carbon::now()->toDateTimeString();
        $media->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'PRE-ANALYSIS',$media->stage);
        //$this->_sendMailMediaStatusChanged($oldMedia,$media);
        return response()->json($media);
    }

    public function getAllBranch()
    {
        $branchs = Branch::all();
        return response()->json($branchs);
    }

    public function transferBranch()
    {
        $branchs = Branch::all();
        return response()->json($branchs);
    }

    public function sendMediatransfer(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        $oldbranchId = $media->branch_id;
        $mediaOldid = MediaTransfer::where('media_id', $media->id)->limit(1)->orderBy('id', 'DESC')->get();
        if(count($mediaOldid) > 0)
        $oldbranchId = $mediaOldid[0]->new_branch_id;
        $oldBranch = Branch::find($oldbranchId);
        if($request->input('branch_id') !='Client')
        {
            $newBranch = Branch::find($request->input('branch_id'));
            $transfer = new MediaTransfer();
            if(count($mediaOldid) > 0)
            $transfer->old_branch_id = $oldbranchId;
            else
            $transfer->old_branch_id = $media->branch_id;
            $transfer->new_branch_id = $request->input('branch_id');
            $transfer->reason = $request->input('reason');
            $transfer->media_id = $media->id;
            $transfer->created_on  = Carbon::now()->toDateTimeString();
            $transfer->save();
            $media->transfer_id = $transfer->id;
            $media->team_id = 0;
            $media->extension_required = $request->input('extension_required');
            $media->extension_day = $media->extension_day;
            $media->team_assign = 0;
            $media->user_id = null;
            $media->save();
            $remarks = "Media Transferred From ".$oldBranch->branch_name." to ".$newBranch->branch_name." by ".$this->_getUserName(auth()->user()->id).".";
        }
        else if($request->input('branch_id') =='Client')
        {
            $transfer = new MediaTransfer();
            $transfer->old_branch_id = $oldbranchId;
            $transfer->new_branch_id = $oldbranchId;
            $transfer->reason = $request->input('reason');
            $transfer->media_id = $media->id;
            $transfer->created_on  = Carbon::now()->toDateTimeString();
            $transfer->media_in_status = '1';
            $transfer->client_media_send = '1';
            $currentSerices =  MediaTransfer::where('new_branch_id', $transfer->new_branch_id)->max('transfer_series');
            $transfer->transfer_series = ($currentSerices == '')?1:$currentSerices+1;
            $transfer->save();
            $oldBranch = Branch::find($oldbranchId);
            MediaTransfer::where(['media_id'=>$transfer->media_id])->update(['client_media_send'=>'1']);
            $remarks = "Media Transferred From ".$oldBranch->branch_name." to Client by ".$this->_getUserName(auth()->user()->id).".";
        }
        // $sendMail = $this->_sendMailTransferMedia($transfer,$media);
           $this->_insertMediaHistory($media,"edit",$remarks,'TRANSFER-MEDIA',$media->stage);
        return response()->json($media);
    }

    function updateMediaAssessment(Request $request)
    {
        $id = $request->input('id');
        $media = Media::find($id);
        $oldMedia = $media;		
        $media->case_type = $request->input('case_type');
        $media->media_clone = $request->input('media_clone');
        $media->encryption_status = $request->input('encryption_status');
        $media->encryption_type = $request->input('encryption_type');
        $media->encryption_details_correct = $request->input('encryption_details_correct');
        $media->media_os = $request->input('media_os');
        $media->compression_status = $request->input('compression_status');
        $media->file_system_info = $request->input('file_system_info');
        $media->data_loss_reason = $request->input('data_loss_reason');
        $media->recoverable_data = $request->input('recoverable_data');
        $media->recovery_possibility = $request->input('recovery_possibility');
        $media->recovery_percentage = $request->input('recovery_percentage');
        $media->required_days = $request->input('required_days');
        $media->assessment_due_reason = $request->input('assessment_due_reason');
        $media->media_damage = $request->input('media_damage');
        $media->media_damage_physical = $request->input('media_damage_physical');
        $media->noise_type = $request->input('noise_type');
        $media->drive_electronics = $request->input('drive_electronics');
        $media->rotary_function = $request->input('rotary_function');
        $media->platters_condition = $request->input('platters_condition');
        $media->tampering_required = $request->input('tampering_required');
        $media->further_use = $request->input('further_use');
        $media->spare_required = $request->input('spare_required');
        $media->media_received = $request->input('media_received');
        $media->media_condition = $request->input('media_condition');
        $media->reading_process = $request->input('reading_process');
        $media->access_percentage = $request->input('access_percentage');
        $media->disk_type = $request->input('disk_type');
        $media->reading_process = $request->input('reading_process');
        $media->state_identified = $request->input('state_identified');
        $media->media_architecture = $request->input('media_architecture');
		$media->drive_count = $request->input('drive_count');
		$media->damage_drive = $request->input('damage_drive');
		$media->media_damage_physical_serve = $request->input('media_damage_physical_serve');
		$media->server_type = $request->input('server_type');
		$media->backup_software = $request->input('backup_software');
		$media->media_group = $request->input('media_group');
        $media->last_updated = Carbon::now()->toDateTimeString();
        $media->stage = $request->input('stage');
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        $media->encryption_name = $request->input('encryption_name');
        $media->extension_required = $request->input('extension_required');
        $media->extension_day =  $request->input('extension_day');
        $media->notes = $request->input('notes');
        $media->total_drive = json_encode($request->input('total_drive'));
        $media->media_clone_detail = json_encode($request->input('media_clone_detail'));
        $media->media_sapre_detail = json_encode($request->input('media_sapre_detail'));
        $media->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'INSPECTION',$media->stage);
        if($media->recovery_possibility == 'Yes' && $media->stage == 6)
        {
            $media->stage = 7;
            $media->save();
        }
        if($media->recovery_possibility == 'No' && $media->stage == 6)
        {
            $media->stage = 9;
            $media->save();
        }
        //$this->_sendMailMediaStatusChanged($oldMedia,$media);
        return response()->json($media);
    }

    public function updateGatePassRef(Request $request)
    {
            $mediaId =  $request->input('transfer_id');
            $gate = Gatepass::find($request->input('id'));
            $gate->ref_name_num = $request->input('ref_name_num');
            $gate->save();
             $sss = $this->generateMediaCode($mediaId);
    }
    

    protected function generateMediaCode($id)
    {
        $transfer = MediaTransfer::find($id);
        $media = Media::find($transfer->media_id);
        if($transfer->new_branch_id != "23")
        {           
            $transfer->transfer_code =  ($media->job_id ==null)?$media->zoho_id:$media->job_id;
        }
        else
        {
            $currentSerices =  MediaTransfer::where('new_branch_id', $transfer->new_branch_id)->max('transfer_series');
            if($currentSerices == '')
                $currentSerices = 1;
            else
                $currentSerices = $currentSerices+1;
    
             $code = str_pad($currentSerices,4,"0",STR_PAD_LEFT);
             $transfer->transfer_code = "HO/".$code;
             $transfer->transfer_series  = $currentSerices;
        }
         $transfer->media_in_status = "1";
         $transfer->save();
         $remarks = "Media In";
         $this->_insertMediaHistory($media,"edit",$remarks,'TRANSFER-MEDIA',$media->stage);
         return response()->json($transfer);

    }

    public function upload(Request $request)
    {
        if($request->hasfile('files'))
        {

            $file = $request->file('files');
            $media_id = $request->input('media_id');
            $path = $file->store('public/Upload/'.$media_id);
            $name = $file->getClientOriginalName();
            $save = new FileUpload();
            $save->name = $name;
            $save->media_id = $media_id;
            $save->store_path= url('/')."/storage/app/".$path;
            $save->save();

             return response()->json([
                        "success" => true,
                        "message" => "File successfully uploaded",
                        "data"=> FileUpload::where('media_id',$media_id)->get(),
                    ]);

        }
        else
        {
            return response()->json([
                        "success" => true,
                        "message" => "File Not uploaded",
                        "DDDD"=>$request
                    ]);
        }
              
    }

    public function deleteFile($id)
    {
       
        $file = FileUpload::find($id);
        $fileNameArray = explode('/',$file->store_path);
        $fileName = array_values(array_slice($fileNameArray, -1))[0];
        $destinationPath = storage_path('app\public\Upload').'/'.$file->media_id.'/'.$fileName;
        unlink($destinationPath);
        DB::table('file_uploads')->where('id', $id)->delete();
        return response()->json(["data"=>FileUpload::where('media_id',$file->media_id)->get()]);
    }

    // public function  sendmail()
    // {
    //     try {
    //         $messageBody = "DFGDGDFGD";
    //         Mail::raw($messageBody, function ($message) use ($messageBody) {
    //         $message->from('vipin.kumar@stellarinfo.com');
    //         $message->to('vipin.kumar@stellarinfo.com','raj.kumar@stellarinfo.com');
    //         $message->subject("Learning Laravel"); 
    //         });
    //     }
    //     catch(\Exception $e)
	// 		{
    //             echo $e->getMessage();die;
	// 			Log::error('Error Sending  Email :: '  . $e->getMessage());
	// 		}
    // }

    public function addDummyMedia(Request $request)
    {
        $cus = new CustomerDetail();
        $cus->customer_name = $this->nameGenerate();
        $cus->save();
        $media = new Media();
        $media->media_type = $request->input('media_type');
        $media->branch_id = $request->input('branch_id');
        $media->zoho_id = rand();
        $media->zoho_user = $this->_getUserName(auth()->user()->id);
        $media->created_on = Carbon::now()->toDateTimeString();
        $media->customer_id = $cus->id;
        $media->stage = 1;
        $media->save();
        $remarks = (!empty($media->zoho_user) ? "Case added by Zoho user ".$media->zoho_user : "Case added by Zoho user");
        $this->_insertMediaHistory($media,"edit",$remarks,'PRE-ANALYSIS',$media->stage);
        return response()->json($media);

    }

    public function updateDummyMedia(Request $request)
    {
        $media = Media::find($request->input('id'));
        $media->job_id = strtoupper(substr($request->input('branch_name'), 0, 3)).'/'.rand(10,100); 
        $media->zoho_job_id = rand();
        $media->stage = 4;
        $media->save();
        $remarks = (!empty($this->_getUserName(auth()->user()->id)) ? "Data updated by Zoho user ".$this->_getUserName(auth()->user()->id) : "Data updated by Zoho user");
        $this->_insertMediaHistory($media,"edit",$remarks,'INSPECTION',$media->stage);
        return response()->json($media);
    }

    function nameGenerate() {
        $key = '';
        $keys = array_merge(range('a', 'z'), range('A', 'Z'));
        for($i=0; $i < 6; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return strtoupper($key);
    }

    public function UpdateStausDummyMedia($id)
    {
        $media = Media::find($id);
        $media->stage = 8;
        if($media->required_days != null)
        $media->due_date = $this->_getDueDate(date('Y-m-d'),$media->required_days);
        $media->save();
        return response()->json($media);
    }

    public function UpdateStausDl($id)
    {
        $media = Media::find($id);
        $media->stage = 13;
        $media->save();
        $dl = MediaDirectory::where('media_id',$id)->first();
        $dl->dl_status = 'Yes';
        $dl->copyin = 'Online Transfer';
        $dl->save();
        return response()->json($media);
    }

    public function extensionUpdateDummy($id)
    {
        $media = Media::find($id);
        $media->extension_approve = 0;
        if($media->due_date != null)
        $media->due_date = $this->_getDueDate($media->due_date,$media->extension_day);
        else
        $media->due_date = $this->_getDueDate(date('Y-m-d'),$media->extension_day);
        $media->save();
        $this->_insertMediaHistory($media,"edit",'Extension Approved','EXTENSION-DAY',$media->stage,'Approved');
        return response()->json($media);
    }

}