<?php

namespace App\Http\Controllers;
use App\Models\Gatepass;
use App\Models\MediaTransfer;
use Illuminate\Http\Request;
use DB;
class DownloadPass extends Controller
{
    //
    public function index($id,$pass_no){
        $pass_no=str_replace('-','/',$pass_no);
        $select = 'gatepass.*,media.zoho_id,media.media_type,media.case_type,branch.branch_name as dispatched_to, branch.address';
        $query = DB::table('gatepass')->select(DB::raw($select));
        $query->leftJoin("transfer_media","gatepass.transfer_id", "=", "transfer_media.id");
        $query->leftJoin("media","transfer_media.media_id", "=", "media.id");
        // $query->leftJoin('customer_detail','media.customer_id', '=','customer_detail.id');
        $query->leftJoin('branch', 'gatepass.dispatch_branch_id', '=', 'branch.id');
        $query->where('gatepass.id', '=', $id);
        $query->where('gatepass.gatepass_no', '=', $pass_no);
        $results = $query->get();
        
        return view('downloadpass');
    }
}
