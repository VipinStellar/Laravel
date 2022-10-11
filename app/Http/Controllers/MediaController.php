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
use App\Models\User;
use App\Models\Stage;
use App\Models\Branch;
use DB;
use App\Models\FileUpload;
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
        $select = 'media.*,transfer_media.media_id as transfer_media_id,transfer_media.new_branch_id as new_branch_id, branch.branch_name as branch_name,customer_detail.customer_name as customer_name,stage.stage_name as stage_name';
        $query = DB::table('media')->select(DB::raw($select));
        $query->leftJoin("transfer_media","media.id", "=", DB::raw("transfer_media.media_id and media.transfer_id=transfer_media.id"));
        $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
        $query->leftJoin('stage', 'stage.id', '=', 'media.stage');
        $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
        if(auth()->user()->role_id !=1 && ($searchType =='' || $searchType == null))
        $query->whereRaw("(media.branch_id in ($branchId) or transfer_media.new_branch_id in ($branchId))");
      
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
            if($status == 3 && $searchType !='' && $searchType != null)
            $query->whereNotIn('media.stage', [1,2])->whereRaw('MONTH(media.created_on) = MONTH(CURRENT_DATE()) AND YEAR(media.created_on) = YEAR(CURRENT_DATE())');
            elseif($status == 6)
            $query->Where('media.stage', '=', 5)->Where('media.recovery_possibility','=','Yes'); 
            elseif($status == 7)
            $query->Where('media.stage', '=', 5)->Where('media.recovery_possibility','=','No');
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

    public function getMedia($id)
    {
        $select = 'media.*,branch.branch_name as branch_name,customer_detail.customer_name as customer_name';
        $query = DB::table('media')->select(DB::raw($select));
        $query->where('media.id', '=',$id);
        $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
	    $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
        $media =  $query->get();   
        if(count($media) > 0)
        {
            $media[0]->total_drive = json_decode($media[0]->total_drive);
            $media[0]->media_clone_detail = json_decode($media[0]->media_clone_detail);
            $media[0]->media_sapre_detail = json_decode($media[0]->media_sapre_detail);
            $media[0]->fileUpload = FileUpload::where('media_id',$media[0]->id)->get();
            if($media[0]->transfer_id != null)
            {
                $media[0]->transferMedia =  MediaTransfer::find($media[0]->transfer_id);
            }
            $media[0]->preHis = null;
            $media[0]->assHis = null;
            $select1 = 'media_history.*,users.name as user_name,stage.stage_name as stage_name';
            $query1 = DB::table('media_history')->select(DB::raw($select1));
            $query1->where('media_history.media_id', '=',$id);
            $query1->where('media_history.module_type', '=',"media_in");
            $query1->where('media_history.action_type', '=',"edit");
            $query1->leftJoin('users', 'users.id', '=', 'media_history.added_by');
            $query1->leftJoin('stage', 'stage.id', '=', 'media_history.status');
            $query1->orderBy('id','desc');
            $query1->limit(1);
            $preHistory =  $query1->get();
            if(count($preHistory) > 0)
            {
                $media[0]->preHis = $preHistory;
            }
            $select2 = 'media_history.*,users.name as user_name,stage.stage_name as stage_name';
            $query2 = DB::table('media_history')->select(DB::raw($select2));
            $query2->where('media_history.media_id', '=',$id);
            $query2->where('media_history.module_type', '=',"assessment");
            $query2->where('media_history.action_type', '=',"edit");
            $query2->leftJoin('users', 'users.id', '=', 'media_history.added_by');
            $query2->leftJoin('stage', 'stage.id', '=', 'media_history.status');
            $query2->orderBy('id','desc');
            $query2->limit(1);
            $assHistory =  $query2->get();
            if(count($assHistory) > 0)
            {
                $media[0]->assHis = $assHistory;
            }
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
        else
        $stage = Stage::where('type',$type)->get();
        return response()->json($stage);
    }

    public function updateMediaAnalysis(Request $request)
    {
        $id = $request->input('id');
        $media = Media::find($id);
        $oldMedia = $media;
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
        $media->stage = $request->input('stage');
        $media->media_category = $request->input('media_category');
        $media->total_drive = json_encode($request->input('total_drive'));
        $media->peripherals_details = $request->input('peripherals_details');
        $media->last_updated  = Carbon::now()->toDateTimeString();
        $media->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'media_in',$media->stage);
        //$this->_sendMailMediaStatusChanged($oldMedia,$media);
        return response()->json($media);
    }

    public function getAllBranch()
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
        $newBranch = Branch::find($request->input('branch_id'));
        $transfer = new MediaTransfer();
        $transfer->old_branch_id = $media->branch_id;
        $transfer->new_branch_id = $request->input('branch_id');
        $transfer->reason = $request->input('reason');
        $transfer->media_id = $media->id;
        $transfer->created_on  = Carbon::now()->toDateTimeString();
        $transfer->save();
        $media->transfer_id = $transfer->id;
        $media->team_id = 0;
        $media->extension_required = $request->input('extension_required');
        $media->extension_day = $request->input('extension_day');
        $media->team_assign = 0;
        $media->save();
       // $sendMail = $this->_sendMailTransferMedia($transfer,$media);
        $remarks = "Media Transferred ".$oldBranch->branch_name." to ".$newBranch->branch_name." by ".$this->_getUserName(auth()->user()->id).".";
        $this->_insertMediaHistory($media,"transfer",$remarks,'media_in',$media->stage);
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
		$media->media_group = $request->input('media_group');
        $media->last_updated = Carbon::now()->toDateTimeString();
        $media->stage = $request->input('stage');
        $media->encryption_name = $request->input('encryption_name');
        $media->extension_required = $request->input('extension_required');
        $media->extension_day = $request->input('extension_day');
        $media->total_drive = json_encode($request->input('total_drive'));
        $media->media_clone_detail = json_encode($request->input('media_clone_detail'));
        $media->media_sapre_detail = json_encode($request->input('media_sapre_detail'));
        $media->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'assessment',$media->stage);
        //$this->_sendMailMediaStatusChanged($oldMedia,$media);
        return response()->json($media);
    }

    public function generateMediaCode($id)
    {
        $transfer = MediaTransfer::find($id);
        $media = Media::find($transfer->media_id);
        if($transfer->new_branch_id != "23")
        {           
            $transfer->transfer_code =  $media->job_id;
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
         $remarks = "Media In by ".$this->_getUserName(auth()->user()->id);
         $this->_insertMediaHistory($media,"transfer",$remarks,'media_in',$media->stage);
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

}