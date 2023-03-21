<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; 
use App\Models\Media;
use App\Models\Recovery;
use App\Models\User;
use DB;



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
        if($recovery->recoveryObj != null)
        {
            $recovery->recoveryObj['clone_required_encrypted_data'] = json_decode($recovery->recoveryObj['clone_required_encrypted_data']);
            $recovery->recoveryObj['clone_required_recoverable_data'] = json_decode($recovery->recoveryObj['clone_required_recoverable_data']);
        }
        return response()->json($recovery);
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
        $rec->clone_branch = $request->input('clone_branch');
        $rec->clone_required_encrypted = $request->input('clone_required_encrypted');
        $rec->clone_required_encrypted_data = json_encode($request->input('clone_required_encrypted_data'));
        $rec->clone_required_recoverable = $request->input('clone_required_recoverable');
        $rec->clone_required_recoverable_data = json_encode($request->input('clone_required_recoverable_data'));
        $rec->save();
        $media = Media::find($rec->media_id);
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        if(($request->input('clone_creation') =='No') || ($request->input('data_encrypted') =='Yes' && $request->input('decryption_data')=='No') 
            || ($request->input('recoverable_data') =='No' && $request->input('type')=='RECOVERABLE-DATA'))
            $media->stage = 11;
        $media->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks, $request->input('type'),$media->stage);
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
        $media->extension_day = $request->input('extension_day') + $media->extension_day;
        else
        $media->extension_day  = $request->input('extension_day');
        $media->save();
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),$request->input('type'),$media->stage);
    }
}