<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use App\Models\Media;
use App\Models\Job;
use DB;



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
        $select = 'media.*,transfer_media.media_id as transfer_media_id,transfer_media.new_branch_id as new_branch_id, branch.branch_name as branch_name,customer_detail.customer_name as customer_name,stage.stage_name as stage_name,job_status.status as jobStatus';
        $query = DB::table('media')->select(DB::raw($select));
        $query->leftJoin("transfer_media","media.id", "=", DB::raw("transfer_media.media_id and media.transfer_id=transfer_media.id"));
        $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
        $query->leftJoin('stage', 'stage.id', '=', 'media.stage');
        $query->leftJoin(DB::raw('(select * FROM job_status js where js.id IN (select max(js1.id) from job_status js1 where js1.media_id = js.media_id)) job_status'), function($join) { $join->on('job_status.media_id', '=', 'media.id');});
        $query->leftJoin('customer_detail','customer_detail.id', '=','media.customer_id');
        if(auth()->user()->role_id !=1)
        $query->whereRaw("(media.branch_id in ($branchId) or transfer_media.new_branch_id in ($branchId))");

        if($statusId != null && $statusId !='')
          $query->Where('job_status.status', '=', $statusId); 
        if($branchIdReq != null && $branchIdReq !='')
          $query->whereRaw("(media.branch_id in ($branchIdReq) or transfer_media.new_branch_id in ($branchIdReq))");

        $query->orderBy($request->input('orderBy'), $request->input('order'));
        $pageSize = $request->input('pageSize');
        $data = $query->paginate($pageSize,['*'],'page_no');
        $results = $data->items();
        $i = 0;
        foreach ($results as $result) {
          $result->jobStatusId = $result->jobStatus;
            if($result->new_branch_id !=null)
            $result->new_branch_id =$this->_getBranchName($result->new_branch_id);
            if($result->jobStatus !=null)
            $result->jobStatus =$this->_getStageName($result->jobStatus);
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
}