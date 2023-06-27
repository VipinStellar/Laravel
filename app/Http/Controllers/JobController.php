<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use App\Models\Media;
use App\Models\Job;
use App\Models\Observation;
use App\Models\MediaStatus;
use App\Models\Gatepass;
use App\Models\MediaTransfer;
use App\Models\Branch;
use DB;
use App\Models\GatepassId;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function jobconfirm(Request $request)
    {
      $statusInput = $request->input('statusId');
      $branchInput = $request->input('branchId');
      $branchId = implode(',',$this->_getBranchId());
      $select = 'media.*,transfer_media.media_id as transfer_media_id,transfer_media.new_branch_id as new_branch_id, branch.branch_name as branch_name,customer_detail.customer_name as customer_name,stage.stage_name as stage_name';
      $query = DB::table('media')->select(DB::raw($select));
      $query->leftJoin("transfer_media","media.id", "=", DB::raw("transfer_media.media_id and media.transfer_id=transfer_media.id"));
      $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
      $query->leftJoin('stage', 'stage.id', '=', 'media.stage');
      $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
      if(auth()->user()->role_id !=1)
      $query->whereRaw("(media.branch_id in ($branchId) or transfer_media.new_branch_id in ($branchId))");
      $query->whereRaw("media.stage > 4 and media.stage !=10");
     // DB::enableQueryLog();
     if($branchInput != null && $branchInput !='')
      $query->whereRaw("(media.branch_id in ($branchInput) or transfer_media.new_branch_id in ($branchInput))");
     if($statusInput != null && $statusInput !='')
       $query->Where('media.stage', '=', $statusInput); 
      $query->orderBy($request->input('orderBy'), $request->input('order'));
      $pageSize = $request->input('pageSize');
      $data = $query->paginate($pageSize,['*'],'page_no');
      //$queries = DB::getQueryLog();print_r($queries);die;
      $results = $data->items();
      $count = $data->total();
        $data = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $count,
            "data" => $results
            ];
            return json_encode($data);
    }

    public function joblist(Request $request)
    {
        $statusId = $request->input('statusId');
        $branchIdReq = $request->input('branchId');
        $branchId = implode(',',$this->_getBranchId());
        $select = 'transfer_media.id as transPrimaryId,transfer_media.media_id as transfer_media_id,transfer_media.media_in_status as media_in_status,transfer_media.media_in_date as media_in_date,transfer_media.assets_type,transfer_media.id as transferId,transfer_media.gatepass_status as getpasStatus,gatepass_id.gatepass_id as getpassId,
                   transfer_media.new_branch_id as new_branch_id,transfer_media.old_branch_id,transfer_media.transfer_code as transfer_code,transfer_media.client_media_send,
                   branch.branch_name as branch_name,customer_detail.customer_name as customer_name,stage.stage_name as stage_name,media.*';
        $query = DB::table('transfer_media')->select(DB::raw($select));
        $query->leftJoin("media",'transfer_media.media_id', "=","media.id");
        $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
        $query->leftJoin('stage', 'stage.id', '=', 'media.stage');
        $query->leftJoin("gatepass_id","transfer_media.id", "=", "gatepass_id.transfer_id");
        $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
        if(auth()->user()->role_id !=1)
        $query->whereRaw("(transfer_media.new_branch_id in ($branchId) or transfer_media.old_branch_id in ($branchId))");

        if($branchIdReq != null && $branchIdReq !='')
          $query->whereRaw("(transfer_media.old_branch_id in ($branchIdReq) or transfer_media.new_branch_id in ($branchIdReq))");

        $query->orderBy($request->input('orderBy'), $request->input('order'));
        $pageSize = $request->input('pageSize');
        $data = $query->paginate($pageSize,['*'],'page_no');
        $results = $data->items();
        $i = 0;
        foreach ($results as $result) {
          $result->ref_name = null;
          $Gatequery = DB::table('gatepass')->whereRaw('FIND_IN_SET(?, media_id)', [$result->transfer_media_id])->orderBy('id','DESC')->limit(1)->get();
          if(count($Gatequery) > 0)
            $result->ref_name = $Gatequery[0]->ref_name_num;;
            $result->genPassCheck = $this->_userCheckBrnach($result->old_branch_id);
            $result->mediaInCheck = $this->_userCheckBrnach($result->new_branch_id);
            $result->mediaTransCheck = $this->_userCheckBrnach(($result->transferId == null)?$result->branch_id:$result->new_branch_id);
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

    public function updateJobStatus(Request $request)
    {
        $job = new Job();
        $media = Media::find($request->input('media_id'));
        $job->status = $request->input('status');
        $job->remarks = $request->input('remarks');
        $job->media_id = $request->input('media_id');
        $job->created_at  = Carbon::now()->toDateTimeString();
        $job->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'assessment',$media->stage);
        return response()->json($job);
    }

    public function updateMediaStatus(Request $request)
    {
        $media = new MediaStatus();
        $media->media_id = $request->input('media_id');
        $media->status = $request->input('status');
        $media->user_id = auth()->user()->id;
        $media->save();
        return response()->json($media);
    }

    public function getStatusHistory($id)
    {
      $select = 'media_status.*,users.name as Username';
      $query = DB::table('media_status')->select(DB::raw($select));
      $query->where('media_status.media_id', '=',$id);
      $query->leftJoin('users','users.id', '=','media_status.user_id');
      $query->orderBy('id','asc');
      $media =  $query->get();
      return response()->json($media);
    }

    public function getMediaObservation($media_id)
    {
      $select = 'media.id as mediaId,media.case_type as caseType,media.recovery_possibility as recovery_possibility,media.no_recovery_reason as no_recovery_reason,media.no_recovery_reason_other as no_recovery_reason_other,
                media.media_type as media_type,media.notes as notes,media.required_days as required_days,media.recovery_percentage as recovery_percentage,media.recoverable_data as recoverable_data,observation.*';
      $query = DB::table('media')->select(DB::raw($select));
      $query->where('media.id', '=',$media_id);
      $query->leftJoin('observation','observation.media_id', '=','media.id');
      $media =  $query->get();
      return response()->json($media[0]);
    }

    public function updateObservation(Request $request)
    {
        $id = $request->input('id');
        if($id == null)
        $Obser = New Observation();
        else
        $Obser = Observation::find($id);
        $Obser->media_id = $request->input('media_id');
        $Obser->media_seal_condition = $request->input('media_seal_condition');
        $Obser->p_c_b_found_faulty = $request->input('p_c_b_found_faulty');
        $Obser->unique_rom_chip = $request->input('unique_rom_chip');
        $Obser->p_c_b_original = $request->input('p_c_b_original');
        $Obser->motor_found_faulty = $request->input('motor_found_faulty');
        $Obser->p_c_b_found_tempered = $request->input('p_c_b_found_tempered');
        $Obser->head_stack_assembly = $request->input('head_stack_assembly');
        $Obser->found_foreign_particles_on_platters = $request->input('found_foreign_particles_on_platters');
        $Obser->total_numbers_of_heads = $request->input('total_numbers_of_heads');
        $Obser->total_number_of_platters = $request->input('total_number_of_platters');
        $Obser->number_of_working_heads = $request->input('number_of_working_heads');
        $Obser->number_of_non_working_heads = $request->input('number_of_non_working_heads');
        $Obser->condition_of_platter_surface = $request->input('condition_of_platter_surface');
        $Obser->condition_of_multiple_platter_surface = $request->input('condition_of_multiple_platter_surface');
        $Obser->platter_cleaning_required_at_initial_stage = $request->input('platter_cleaning_required_at_initial_stage');
        $Obser->p_c_b_rom_is_corrupted = $request->input('p_c_b_rom_is_corrupted');
        $Obser->service_area_are_corrupted = $request->input('service_area_are_corrupted');
        $Obser->imaging_process_at_initial_stage = $request->input('imaging_process_at_initial_stage');
        $Obser->spare_required = $request->input('spare_required');
        $Obser->label1 = $request->input('label1');
        $Obser->architacture = $request->input('architacture');
        $Obser->internal_damage = $request->input('internal_damage');
        $Obser->controller_name = $request->input('controller_name');
        $Obser->encryption = $request->input('encryption');
        $Obser->virtual_translater = $request->input('virtual_translater');
        $Obser->media_interface = $request->input('media_interface');
        $Obser->save();
        $media = Media::find($Obser->media_id);
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        $media->recovery_possibility = $request->input('recovery_possibility');
        $media->recoverable_data = $request->input('recoverable_data');
        $media->recovery_percentage = $request->input('recovery_percentage');
        $media->required_days = $request->input('required_days');
        $media->notes = $request->input('notes');
        if($request->input('recovery_possibility') == 'No')
            $media->stage = 14;
        $media->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks,'OBSERVATION',$media->stage);
        return response()->json($Obser);
    }

    public function getMediaJob($id)
    {
            $select = 'media.*,customer_detail.customer_name as customer_name';
            $query = DB::table('media')->select(DB::raw($select));
            $query->where('media.id', '=',$id);
            $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
            $media =  $query->get(); 
            if(count($media) > 0)
            {
              $media[0]->allHistory = null;
              $Sel = $select = 'job_status.*,stage.stage_name as stage_name';
              $query1 = DB::table('job_status')->select(DB::raw($Sel));
              $query1->where('job_status.media_id', '=',$media[0]->id);
              $query1->leftJoin('stage','stage.id', '=','job_status.status');
              $jobHis =  $query1->get(); 
              if(count($jobHis) > 0)
              {
                 $media[0]->allHistory = $jobHis;
              }
               return response()->json($media[0]);
            }
            else
            {
            return response()->json(null);
            }
    }
   
    public function getObvertationDetails($mediaId)
    {
      $media = Media::find($mediaId);
      $media->Obser = Observation::where('media_id',$mediaId)->first();
      return response()->json($media);
    }
       
    public function GatePassList(Request $request){
      $search_passType = $request->input('passType');
      $search_branchId = $request->input('branchId');
      $term = $request->input('term');
      $branchId = implode(',',$this->_getBranchId());
      $select = 'transfer_media.*,media.zoho_id,media.media_type,media.case_type,media.stage as stage_id,media.job_id,customer_detail.customer_name,branch.branch_name as new_branch_name,stage.stage_name as stage_name,gatepass.id as gatepass_id,gatepass.gatepass_no,gatepass.created_on as createdon';
      $query = DB::table('transfer_media')->select(DB::raw($select));
      $query->leftJoin("media","transfer_media.media_id", "=", "media.id");
      $query->leftJoin("gatepass_id","transfer_media.id", "=", "gatepass_id.transfer_id");
      $query->leftJoin("gatepass","gatepass_id.gatepass_id", "=", "gatepass.id");
      $query->leftJoin("stage", "media.stage", "=", "stage.id");
      $query->leftJoin("customer_detail","media.customer_id", "=","customer_detail.id");
      $query->leftJoin("branch","transfer_media.old_branch_id", "=", "branch.id");
      $query->where("transfer_media.gatepass_status", "=","1");
      if(auth()->user()->role_id !=1)
      $query->whereRaw("transfer_media.old_branch_id in ($branchId)");

      if($search_passType !=null && $search_passType !=''){
        $query->where("gatepass.gatepass_type", "=", "".$search_passType."");
      }
      if($search_branchId !=null && $search_branchId !=''){
        $query->whereRaw("(transfer_media.old_branch_id = ".$search_branchId.")");
      }
      if($term != null && $term !='')
      {
        $query->whereRaw("(media.zoho_id = '".$term."' or media.job_id = '".$term."')");
      }
      $query->orderBy($request->input('orderBy'), $request->input('order'));
      $pageSize = $request->input('pageSize');
      $data = $query->paginate($pageSize,['*'],'page_no');
      $results = $data->items();  
      $count = $data->total();
      $data = [
        "draw"         => $request->input('draw'),
        "recordsTotal" => $count,
        "data"         => $results
        ];
        return json_encode($data);
    }
    
    public function addGatePass(Request $request){
      $expected_return_date = $request->input('expected_return_date');
      $address = '';
      $branch_code = '';
      if($request->input('dispatch_branch_id') == 0){
        $address = $request->input('client_address');
        $branch_code = '';
      }else{
        $branch = Branch::find($request->input('dispatch_branch_id'));
        $address = $branch->address;
        $branch_code = $branch->branch_code;
      }
      
      $gatepass = new Gatepass();
      $gatepass->gatepass_type         = $request->input('gatepass_type');
      $gatepass->ref_name_num         = $request->input('ref_name_num');
      $gatepass->transfer_mode         = $request->input('transfer_mode');
      $gatepass->expected_return_date  = ($expected_return_date !=null && $expected_return_date !='' ? date('Y-m-d', strtotime($expected_return_date)):'');
      $gatepass->requester_deptt       = $request->input('requester_deptt');
      $gatepass->sender_name           = $request->input('sender_name');
      $gatepass->dispatch_branch_id    = $request->input('dispatch_branch_id');
      $gatepass->dispatch_name         = $request->input('dispatch_name');
      $gatepass->dispatch_address      = $address;
      $gatepass->other_assets          = (count($request->input('otherAssets')) > 0) ? json_encode($request->input('otherAssets')):'';
      $gatepass->remarks               = $request->input('remarks');
      $gatepass->created_on            = Carbon::now()->toDateTimeString();
      $gatepass->media_id              = implode(',',$request->input('media_id'));
      $gatepass->save();
      $pass_no ='';
      if($gatepass->gatepass_type !='' && $gatepass->gatepass_type =='Returnable'){
        $pass_no ='R/';
      }else{
        $pass_no ='NR/';
      }

      $pass_no.= (($branch_code != '')? $branch_code.'/':'').str_pad($gatepass->id,4,"0",STR_PAD_LEFT);
      $gatepass = Gatepass::find($gatepass->id);
      $gatepass->gatepass_no = $pass_no;
      $gatepass->save();

      $transfer_id = $request->input('transfer_id');
      for($i=0; $i < count($transfer_id); $i++){
        // insert for gatepass id table
        $gatepass_id = new GatepassId();
        $gatepass_id->transfer_id = $transfer_id[$i];
        $gatepass_id->gatepass_id = $gatepass->id;
        $gatepass_id->save();
        // For Change Status
        $media_transfer = MediaTransfer::find($transfer_id[$i]);
        $media_transfer-> gatepass_status = '1';
        $media_transfer->save();
        // For Media History
        $media = Media::find($media_transfer->media_id);
        $remarks = "GatePass Created";
        $this->_insertMediaHistory($media,"add",$remarks,'GATEPASS-CREATED',$media->stage);
      }
      return response()->json('success');
    }
    
    public function downloadPass($id){

      $select = 'gatepass.*,transfer_media.new_branch_id,branch.branch_name as dispatched_to';
      $query  = DB::table('gatepass')->select(DB::raw($select));
      $query->leftjoin("gatepass_id","gatepass.id", "=", "gatepass_id.gatepass_id");
      $query->leftJoin("transfer_media","gatepass_id.transfer_id", "=", "transfer_media.id");
      $query->leftJoin('branch', 'gatepass.dispatch_branch_id', '=', 'branch.id');
      $query->where('gatepass.id', '=', $id);
      $results = $query->take(1)->get();
      $branch=Branch::find($results[0]->new_branch_id);
      $results[0]->transfer_address = $branch->address;
      $results[0]->other_assets = json_decode($results[0]->other_assets);
      /// For gatepass Table data
      
      $select1 = 'media.media_type,media.job_id,media.media_serial,media.media_make,media.media_model';
      $query1   = DB::table('gatepass_id')->select(DB::raw($select1));
      $query1->leftJoin("transfer_media","gatepass_id.transfer_id", "=", "transfer_media.id");
      $query1->leftJoin("media","transfer_media.media_id", "=", "media.id");
      $query1->where('gatepass_id.gatepass_id', '=', $id);
      $results[0]->material_detail = $query1->get();
      return json_encode($results[0]);
    }
}