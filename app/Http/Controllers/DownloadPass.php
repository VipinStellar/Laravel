<?php

namespace App\Http\Controllers;
use App\Models\Branch;
use DB;
use Illuminate\Support\Arr;

class DownloadPass extends Controller
{
    public function index($id){
      $select = 'gatepass.*,transfer_media.new_branch_id,branch.branch_name as dispatched_to';
      $query  = DB::table('gatepass')->select(DB::raw($select));
      $query->leftjoin("gatepass_id","gatepass.id", "=", "gatepass_id.gatepass_id");
      $query->leftJoin("transfer_media","gatepass_id.transfer_id", "=", "transfer_media.id");
      $query->leftJoin('branch', 'gatepass.dispatch_branch_id', '=', 'branch.id');
      $query->where('gatepass.id', '=', $id);
      $results = $query->take(1)->get();
      $branch=Branch::find($results[0]->new_branch_id);
      $results[0]->transfer_address = $branch->address;
      $results[0]->gst_no = $branch->gst_no;
      $results[0]->phone_no = $branch->phone_no;
      $other_assets = json_decode($results[0]->other_assets);
      
      /// For gatepass Table data
      $select1 = 'media.media_type,media.job_id,media.deal_id,media.media_serial,media.media_make,media.media_model';
      $query1   = DB::table('gatepass_id')->select(DB::raw($select1));
      $query1->leftJoin("transfer_media","gatepass_id.transfer_id", "=", "transfer_media.id");
      $query1->leftJoin("media","transfer_media.media_id", "=", "media.id");
      $query1->where('gatepass_id.gatepass_id', '=', $id);
      $media_detail = $query1->get();
      /// Data Set For Print
      for($i=0; $i < count($other_assets); $i++){
             foreach($media_detail as $media){
              $matrial_job_id = ($media->job_id !='' && $media->job_id != null) ? $media->job_id : $media->deal_id;
            if($other_assets[$i]->only_media && ($other_assets[$i]->assets_job_id == $media->job_id || $other_assets[$i]->assets_job_id == $media->deal_id)){
                $other_assets[$i]->material_name = $media->media_type;
                $material_description =($matrial_job_id != '' && $matrial_job_id != null)? '<strong>Job ID - </strong>'.$matrial_job_id.', ':'';
                $material_description.=($media->media_serial !='' && $media->media_serial != null)?'<strong>Media Serial - </strong>'.$media->media_serial.', ':'';
                $material_description.=($media->media_make !='' && $media->media_make != null)?'<strong>Media Make - </strong>'.$media->media_make.', ':'';
                $material_description.=($media->media_model !='' && $media->media_model != null)?'<strong>Media Model - </strong>'.$media->media_model:'';
                $other_assets[$i]->material_description = $material_description;
            }
          }
      }
      $results[0]->other_assets = $other_assets;
      $data['result'] = $results[0];
      return view('downloadpass',$data);
    }
}
