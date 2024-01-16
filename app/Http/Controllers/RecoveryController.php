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
use App\Models\MediaPrice;
use App\Models\FinalPrice;
use App\Models\Quotation;
use App\Models\JobServicePlan;
use App\Models\MediaClientOut;
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
        $oldMedia = $media->replicate();
        $media->stage = 11;
        $media->save();
        $this->StatusUpdateHistory($oldMedia,$media);
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks, $request->input('type'),$media->stage);
        $media->DL = $dir;
        $media->remarks = $remarks;
        Helper::sendZohoCrmData($media,'Directory-Listing');
        $sendMail = $this->_sendEmailtoISEUser($media);
        return response()->json($media);
    }

    public function updateClentdata(Request $request)
    {
        $id = $request->input('id');
        $dir = MediaDirectory::find($id);
        $media = Media::find($dir->media_id);
        $oldMedia = $media->replicate();
        if($request->input('dl_status') == 'Yes')
        {
            $dir->data_varification  = $request->input('data_varification');
            $dir->data_varification_approval  = $request->input('data_varification_approval');
            $dir->data_recovery_result  = $request->input('data_recovery_result');
            $dir->peripheral_details  = $request->input('peripheral_details');
            $dir->copyin_details  = $request->input('copyin_details');
            $dir->copyin  = $request->input('copyin');
            $media->stage = 12;       
        }
        elseif($request->input('dl_status') == 'No')
        {
            $dir->rework  = $request->input('rework');
            if($dir->rework == 'Yes')
            {
            $media->stage = 22;
            $dir->rework_possible = null;
            }
        }
        $dir->dl_status  = $request->input('dl_status');
        $dir->save();
        $media->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks, 'DIRECTORY-CONFIRM',$media->stage);
        $this->StatusUpdateHistory($oldMedia,$media);
        $media->DL = $dir;
        $media->remarks = $remarks;
        Helper::sendZohoCrmData($media,'DIRECTORY-CONFIRM');
        $sendMail = $this->_sendEmailtoTechnician($media);
        return response()->json($media);
    }

    public function updateRework(Request $request)
    {
        $id = $request->input('id');
        $dir = MediaDirectory::find($id);
        $media = Media::find($dir->media_id);
        $dir->rework_possible  = $request->input('rework_possible');
        $dir->save();
        $remarks = $request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks, 'DIRECTORY-CONFIRM',$media->stage);
        $media->DL = $dir;
        $media->remarks = $remarks;
        Helper::sendZohoCrmData($media,'REWORK');
        $sendMail = $this->_sendEmailtoISEUser($media);
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
        $oldMedia = $media->replicate();
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        if(($request->input('clone_creation') =='No') || ($request->input('recoverable_data') =='No' && $request->input('type')=='RECOVERABLE-DATA'))
        {
            $media->stage = 14;
            $media->wiping_request = '1';
            $media->wiping_date = $this->_getDueDate(date('Y-m-d'),1);
        }
        $media->save();
        $this->StatusUpdateHistory($oldMedia,$media);
        $media->Recovery = $rec;
        $remarks = $request->input('remarks');
        $media->remarks = $remarks;
        $this->_insertMediaHistory($media,"edit",$remarks, $request->input('type'),$media->stage);
        Helper::sendZohoCrmData($media,$request->input('type'));
        $sendMail = $this->_sendEmailtoISEUser($media);
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

    public function requestEextension(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        $media->extension_day = $request->input('extension_day');
        $media->extension_approve = 1;
        $remarks = "Extension requested for ".$request->input('extension_day')." days. <br>".$request->input('remarks');
        $media->save();
        $this->_insertMediaHistory($media,"edit",$remarks,$request->input('type'),$media->stage,'Pending');
        $sendMail = $this->_sendEmailExtension($media,'Pending');
    }

    public function updateEextension(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        if($media->stage == 4 || $media->stage == 5 || $media->stage == 6)
        {
                $assessment_due_date = ($media->assessment_due_date == null)?date('Y-m-d'):$media->assessment_due_date;
        }
        else
        {
            $duedate = ($media->due_date == null)?date('Y-m-d'):$media->due_date;
        }
        if($request->input('extension_type') =='Approved' && $media->extension_approve == 1)
        {
            if($media->stage == 4 || $media->stage == 5 || $media->stage == 6)
            $media->assessment_due_date = $this->_getDueDate($assessment_due_date,$media->extension_day);
            else
            $media->due_date = $this->_getDueDate($duedate,$media->extension_day);
        }     
        $media->extension_approve = '0';                       
        $media->save();
        if($request->input('extension_type') == 'Approved')
        Helper::sendZohoCrmData($media,'EXTENSION-UPDATE');
        $this->_insertMediaHistory($media,"edit", $request->input('remarks'),$request->input('type'),$media->stage,$request->input('extension_type'));
        $sendMail = $this->_sendEmailExtension($media,$request->input('extension_type'));
    }

    public function updateDl(Request $request)
    {
        $id = $request->input('id');
        $dir = MediaDirectory::find($id);
        $dir->rework = $request->input('rework');
        $dir->save();
        $media = Media::find($dir->media_id);
        $oldMedia = $media->replicate();
        if($dir->rework == 'Yes')
        $media->stage = 22;
        $media->no_recovery_reason = $request->input('no_recovery_reason');
        $media->no_recovery_reason_other = $request->input('no_recovery_reason_other');
        $media->save();
        $remarks = $request->input('remarks');
        $this->StatusUpdateHistory($oldMedia,$media);
        $this->_insertMediaHistory($media,"edit",$remarks,'DL-REWORK',$media->stage);

    }

    public function requsetmediaout(Request $request)
    {
         //   $type = "Finaly";
            $media = Media::find($request->input('media_id'));
            $userid = null;
            if($media->transfer_id != null)
            {   
              //  $type = "Partial";
                $trans = MediaTransfer::where('media_id',$media->id)->where('assets_type','Original Media')->orderBy('id', 'desc')->first();
                $userid = DB::table('user_assign')->where('media_id',$media->id)->where('branch_id',$trans->new_branch_id)->orderBy('id', 'desc')->first();
            }
            else
            {
                $userid = DB::table('user_assign')->where('media_id',$media->id)->where('branch_id',$media->branch_id)->orderBy('id', 'asc')->first();         
            }
            $mediaout  = new MediaOut();
            $mediaout->media_id = $request->input('media_id');
            $mediaout->request_type = $request->input('request_type');
            $mediaout->remarks = $request->input('remarks');
            $mediaout->request_date = Carbon::now()->toDateTimeString();
            $mediaout->user_id_from = auth()->user()->id;
            $mediaout->user_id_to = ($userid !=null)?$userid->user_id:null;
            $mediaout->save();
           // $media->media_out_type = $type;
            $media->media_out_status = "0";
            $media->save();
            $media->mediaOut = $mediaout;
            $this->_insertMediaHistory($media,"edit",$mediaout->remarks,'MEDIA-OUT',$media->stage);
            if($mediaout->user_id_to != null)
            $sendMail = $this->_sendEmailtoMediaOutRequest($media);
    }

    public function responcemediaout(Request $request)
    {
        $mediaout = MediaOut::find($request->input('id'));
        $mediaout->approve_date = Carbon::now()->toDateTimeString();
        $mediaout->status_type = '1';
        $mediaout->save();
        $media = Media::find($mediaout->media_id);
      //   $media->media_out_status = null;
         $media->save();
         $media->mediaOut = $mediaout;
        $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'MEDIA-OUT',$media->stage);
        if($mediaout->user_id_from != null)
        $sendMail = $this->_sendEmailtoMediaOutResponce($media);
    }

    public function sendMediaToclient(Request $request)
    {
            $MediaClientOut = new MediaClientOut();
            $MediaClientOut->media_id = $request->input('media_id');
            $MediaClientOut->media_out_Type = $request->input('media_out_Type');
            $MediaClientOut->media_out_mode = $request->input('media_out_mode');
            $MediaClientOut->courier_company_name = $request->input('courier_company_name');
            $MediaClientOut->same_as_address = $request->input('same_as_address');
            $MediaClientOut->ref_no = $request->input('ref_no');
            $MediaClientOut->ref_name = $request->input('ref_name');
            $MediaClientOut->ref_mobile = $request->input('ref_mobile');
            $MediaClientOut->id_proof = $request->input('id_proof');
            $MediaClientOut->courier_address = $request->input('courier_address');
            $MediaClientOut->save();
            $media = Media::find($MediaClientOut->media_id);
            $media->MediaClientOut = $MediaClientOut;
            $media->remarks = $request->input('remarks');
            $this->_insertMediaHistory($media,"edit",$request->input('remarks'),'MEDIA-OUT',$media->stage);
            Helper::sendZohoCrmData($media,'MEDIA-OUT-CUSTOMER');
    }

    public function updatePrice(Request $request)
    {
        $datasets = json_decode(json_encode($request->input('dataset'),TRUE));
        $priceCrmWrap = array();
        $media_id = null;
        $finalPrice = null;
        $SelectedPlanType = null;
        foreach($datasets as $data)
        {
            $priceCrm = [];
            $MediaPrice = MediaPrice::find($data->id);
            $MediaPrice->advance_percent = $data->advance_percent;
            $MediaPrice->additional_charges = $data->additional_charges;
            $MediaPrice->total_fee = $data->total_fee;
            if($data->is_visible == true || $data->is_visible == 1)
            $MediaPrice->is_visible = '1';
            else
            $MediaPrice->is_visible = '0';
            $MediaPrice->selected_plan = ($data->selected_plan == true)?'1':'0';
            $MediaPrice->save();
            $priceCrm['Plan_Type'] = $data->plan_type;
            $priceCrm['Advance_Percentage'] = $data->advance_percent;
            $priceCrm['Advance_Amount'] = round(($data->total_fee*$data->advance_percent)/100);
            $priceCrm['Total_Service_Fee'] = $data->total_fee;
            $priceCrm['Show_Plan'] =($MediaPrice->is_visible == '1')?true:false;
            $priceCrm['Estimated_Days'] =  strval($MediaPrice->estimated_days);
            $priceCrm['Speed'] =           $MediaPrice->speed;
            $priceCrm['Support'] =         $MediaPrice->support;
            if($MediaPrice->is_visible == '1')
            $priceCrmWrap[]=$priceCrm;
            $media_id =  $MediaPrice->media_id;
            if($MediaPrice->selected_plan == '1')
            {
                $SelectedPlanType = $data->plan_type;
                $finalPrice = FinalPrice::where('media_id',$media_id)->first();
                if($finalPrice == null || $finalPrice =='')
                {
                    $finalPrice = new FinalPrice();
                }
                $finalPrice->plan_id = $MediaPrice->plan_id;
                if($request->input('tax') == true)
                {
                    $taxAmount = ($MediaPrice->total_fee * 18)/100;
                    $finalPrice->total_amount = $MediaPrice->total_fee + $taxAmount;
                    $finalPrice->balance_amount =  $MediaPrice->total_fee + $taxAmount;
                    $finalPrice->tax_rate = '18';
                    
                    $finalPrice->tax_amount = $taxAmount;
                }
                else
                {
                    $finalPrice->tax_rate = 0;
                    $finalPrice->tax_amount = 0;
                    $finalPrice->total_amount = $MediaPrice->total_fee;
                    $finalPrice->balance_amount = $MediaPrice->total_fee;
                }
                $finalPrice->base_amount = $MediaPrice->total_fee;
                $finalPrice->advance_percent = $MediaPrice->advance_percent;
                $finalPrice->media_id = $media_id;
                $finalPrice->save();
            }
        }
        if(count($priceCrmWrap) > 0 && $media_id !=null)
        {
            $media =  Media::find($MediaPrice->media_id);
            $media->tax_applicable = ($request->input('tax')== true)?'1':'0';
            $media->save();
            $media->price = $priceCrmWrap;
            $media->SelectedPlan = $finalPrice;
            $media->SelectedPlanType = $SelectedPlanType;
            Helper::sendZohoCrmData($media,'PRICE-UPDATE');
        }
        return json_encode($datasets);
    }

    public function addQuotation(Request $request)
    {
        $quotation = new Quotation();
        $quotation->media_id = $request->input('media_id');
        $quotation->plan_id = $request->input('plan_id');
        $quotation->total_amount = $request->input('total_amount');
        $quotation->base_amount = $request->input('base_amount');
        $quotation->tax_amount = $request->input('tax_amount');
        $quotation->discount_amount = $request->input('discount_amount');
        $quotation->discount = $request->input('new_percentage');
        $quotation->new_total_amount = $request->input('new_total_price');
        $quotation->description = $request->input('description');
        $quotation->status = "Pending";
        $quotation->save();
        $media = DB::table('media')->select(DB::raw('media.*,contact.company_id as company_id'))->leftJoin('contact','contact.zoho_contact_id', '=','media.customer_id')->where('media.id', '=',$quotation->media_id)->first();
        $quotation->quotation_no = ($media->job_id == null?$media->deal_id:$media->job_id).'/Q/'.$quotation->id;
        $quotation->save();
        $media->Quotation = $quotation;
        $media->PlanDetails = JobServicePlan::where('plan_id',$quotation->plan_id)->first();
        Helper::sendZohoCrmData($media,'QUOTATION');
        return json_encode($media);
    }

    public function notifyTech(Request $request)
    {
        $media = Media::find($request->input('media_id'));
        $remarks = "<strong>".$request->input('title')."</strong><br>".$request->input('remarks');
        $this->_insertMediaHistory($media,"edit",$remarks,'NOTIFY-TECH',$media->stage);
        $media->Mailcontent = $remarks;
        if($media->user_id !=null)
        $sendMail = $this->_sendMailToNotifyTech($media);
    }
}