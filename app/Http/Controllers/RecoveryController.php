<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use App\Models\Media;
use App\Models\Recovery;
use App\Models\MediaDirectory;
use App\Models\User;
use App\Models\FileUpload;
use App\Models\MediaOut;
use App\Models\MediaTransfer;
use DB;
use Helper;


class RecoveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function _getRecovery($media_id)
    {
        $recovery = Media::find($media_id);
        $recovery->recoveryObj = Recovery::where('media_id',$media_id)->first();
        $recovery->fileUpload = FileUpload::where('media_id',$media_id)->get();
        if($recovery->recoveryObj != null)
        {
            $recovery->recoveryObj['clone_required'] = json_decode($recovery->recoveryObj['clone_required']);
            $recovery->recoveryObj['clone_required_encrypted_data'] = json_decode($recovery->recoveryObj['clone_required_encrypted_data']);
            $recovery->recoveryObj['clone_required_recoverable_data'] = json_decode($recovery->recoveryObj['clone_required_recoverable_data']);
        }
        return response()->json($recovery);
    }

    public function getDirectory($media_id)
    {
       $MediaDirectory =  MediaDirectory::where('media_id',$media_id)->first();
       if($MediaDirectory != null)
       return response()->json($MediaDirectory);
       else
       return response()->json();
    }

    public function saveDirectory(Request $request)
    {
        $id = $request->input('id');
        if($id == null)
        $dir = New MediaDirectory();
        else
        $dir = MediaDirectory::find($id);
        $dir->media_id = $request->input('media_id');
        $dir->total_file = $request->input('total_file');
        $dir->total_data_size = $request->input('total_data_size');
        $dir->total_mail = $request->input('total_mail');
        $dir->total_mail_size = $request->input('total_mail_size');
        $dir->mail_data = $request->input('mail_data');
        $dir->data_store_media = $request->input('data_store_media');
        $dir->directory_listing = $request->input('directory_listing');
        $dir->data_delivery = $request->input('data_delivery');
        $dir->email_notification = $request->input('email_notification');
        $dir->total_data_size_format = $request->input('total_data_size_format');
        $dir->total_mail_size_format = $request->input('total_mail_size_format');
        $dir->recoverable_data = $request->input('recoverable_data');
        $dir->data_recovered = json_encode($request->input('data_recovered'));
        $dir->save();
        $media = Media::find($dir->media_id);
        $media->stage = 11;
        $media->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks, $request->input('type'),$media->stage);
        $media->DL = $dir;
        Helper::sendZohoCrmData($media,'DL');
        Helper::sendZohoCrmNotes($media,'INSPECTION',0,$request->input('remarks'));
        return response()->json($media);
    }


    public function recoverySave(Request $request)
    {
        $id = $request->input('id');
        if($id == null)
        $rec = New Recovery();
        else
        $rec = Recovery::find($id);
        $rec->media_id = $request->input('media_id');
        $rec->clone_creation = $request->input('clone_creation');
        $rec->cloned_done = $request->input('cloned_done');
        $rec->cloned_sectors = $request->input('cloned_sectors');
        $rec->data_encrypted = $request->input('data_encrypted');
        $rec->decryption_details = $request->input('decryption_details');
        $rec->decryption_details_send = $request->input('decryption_details_send');
        $rec->decryption_data = $request->input('decryption_data');
        $rec->decryption_data_details = $request->input('decryption_data_details');
        $rec->recoverable_data = $request->input('recoverable_data');
        $rec->shared_with_branch = $request->input('shared_with_branch');
        $rec->clone_branch = $request->input('clone_branch');
        $rec->clone_required_encrypted = $request->input('clone_required_encrypted');
        $rec->clone_required_encrypted_data = json_encode($request->input('clone_required_encrypted_data'));
        $rec->clone_required_recoverable = $request->input('clone_required_recoverable');
        $rec->clone_required_recoverable_data = json_encode($request->input('clone_required_recoverable_data'));
        $rec->clone_required = json_encode($request->input('clone_required'));
        $rec->start_date = $request->input('start_date');
        $rec->end_date = $request->input('end_date');
        $rec->partial_reason = $request->input('partial_reason');
        $rec->partial_reason_other = $request->input('partial_reason_other');
        $rec->save();
        $media = Media::find($rec->media_id);
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        if(($request->input('clone_creation') =='No') || ($request->input('recoverable_data') =='No' && $request->input('type')=='RECOVERABLE-DATA'))
            $media->stage = 14;
        $media->save();
        $media->Recovery = $rec;
        $media->ProcessType = $request->input('type');
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks, $request->input('type'),$media->stage);
        Helper::sendZohoCrmData($media,$request->input('type'));
        Helper::sendZohoCrmNotes($media,'INSPECTION',0,$request->input('remarks'));
        return response()->json($media);

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
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks,'allotJob',$media->stage);
    }

    public function updateBranchCloneUser(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        $media->user_id = $request->input('user_id');
        $media->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks,'CLONE-TRANSFER',$media->stage);
    }

    public function updateEextension(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        if($request->input('extension_day') !='Not Applicable')
        {
         $media->extension_day = $request->input('extension_day');
         $media->extension_approve = 1;
        }
        else
        {
         $media->extension_day  = $request->input('extension_day');
        }
        $remarks = "Extension requested for ".$request->input('extension_day')." days. <br>".$request->input('remarks');
        $media->save();
        $this->_insertMediaHistory($media,"edit",$remarks,$request->input('type'),$media->stage,'Pending');
        $media->extReason = $request->input('remarks');
        Helper::sendZohoCrmData($media,'EXTENSION-DAY');
    }

    public function updateDl(Request $request)
    {
        $id = $request->input('id');
        $dir = MediaDirectory::find($id);
        $dir->rework = $request->input('rework');
        $dir->save();
        $media = Media::find($dir->media_id);
        if($dir->rework == 'Yes')
        $media->stage = 22;
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        $media->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks,'DL-REWORK',$media->stage);

    }

    public function requsetmediaout(Request $request)
    {
            $type = "Finaly";
            $media = Media::find($request->input('media_id'));
            $userid = null;
            if($media->transfer_id == null || $media->media_out_type=="Partial")
            {

                $userid = DB::table('user_assign')->where('media_id',$media->id)->where('branch_id',$media->branch_id)->orderBy('id', 'asc')->first();
            }
            else if($media->transfer_id != null)
            {   
                $type = "Partial";
                $trans = MediaTransfer::find($media->transfer_id);
                $userid = DB::table('user_assign')->where('media_id',$media->id)->where('branch_id',$trans->new_branch_id)->orderBy('id', 'desc')->first();
            }
            $mediaout  = new MediaOut();
            $mediaout->media_id = $request->input('media_id');
            $mediaout->request_type = $request->input('request_type');
            $mediaout->remarks = $request->input('remarks');
            $mediaout->request_date = Carbon::now()->toDateTimeString();
            $mediaout->user_id_from = auth()->user()->id;
            $mediaout->user_id_to = $userid->user_id;
            $mediaout->save();
            $media->media_out_type = $type;
            $media->media_out_status = "0";
            $media->save();
            $this->_insertMediaHistory($media,"edit",$mediaout->remarks,'MEDIA-OUT',$media->stage);
    }

    public function responcemediaout(Request $request)
    {
        $mediaout = MediaOut::find($request->input('id'));
        $mediaout->approve_date = Carbon::now()->toDateTimeString();
        $mediaout->status_type = '1';
        $mediaout->save();
        $media = Media::find($mediaout->media_id);
         $media->media_out_status = null;
         $media->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'MEDIA-OUT',$media->stage);
    }
}