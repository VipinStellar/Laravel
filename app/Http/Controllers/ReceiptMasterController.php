<?php

namespace App\Http\Controllers;
use App\Models\ReceiptMaster;
use App\Models\ServiceInvoice;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
class ReceiptMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function receiptList(Request $request)
    {
        $term = trim($request->input('term'));
        $branchId = implode(',',$this->_getBranchId());
        $roleId = auth()->user()->role_id;
        $searchfieldName = $request->input('searchfieldName');
        $select = 'receipt_master.*, branch.branch_name as branch_name,media.id as MediaId,media.job_id,media.deal_id';
        $query = DB::table('receipt_master')->select(DB::raw($select))->leftJoin("media", "media.id", "=", "receipt_master.media_id")
            ->leftJoin("branch", "branch.id", "=", "receipt_master.branch_id");
        if($roleId ==10)
            $query->whereRaw("receipt_master.branch_id in ($branchId)");
        if($term !=null && $term !='' && $searchfieldName !=null && $searchfieldName !='' )
        $query->Where($searchfieldName, '=', $term);
        return $this->_getPaginatedResult($query,$request);    
    }

    public function getReceiptDetails($type,$id)
    {
        $details = null;
        if($type == 'invoice')
        {
            $details = DB::table('service_invoice')->select(DB::raw('service_invoice.*,service_request.branch_id as req_branch_id,service_request.firstname as firstname,media.deal_id,media.job_id,media.id as mediaId'))->leftJoin('service_request','service_request.id', '=','service_invoice.request_id')
                                ->leftJoin('media','media.id', '=','service_request.media_id')->where('service_invoice.id', '=',$id)->first();
            
        }
        elseif($type == 'receipt')
        {
            $details = DB::table('receipt_master')->select(DB::raw('receipt_master.*,media.deal_id,media.job_id,media.id as mediaId'))
                          ->leftJoin('media','media.id', '=','receipt_master.media_id')->where('receipt_master.id', '=',$id)->first();
            $details->Invoice = ServiceInvoice::where('invoice_no',$details->invoice_no)->first();
        }
        return response()->json($details);
    }

    public function addReceipt(Request $request)
    {
        $id = $request->input('id');
        if($id !=null && $id !=''){
            $existingAmount  = ReceiptMaster::select(DB::raw("SUM(received_amount + tds_amount) as total"))->where('media_id',$request->input('media_id'))->where('id','!=',$id)->get();
            $receipt = ReceiptMaster::find($id);
        }
        else
        {
            $existingAmount  = ReceiptMaster::select(DB::raw("SUM(received_amount + tds_amount) as total"))->where('media_id',$request->input('media_id'))->get();
            $receipt = new ReceiptMaster();
        } 
            if(count($existingAmount) > 0)
            {
                (int)$finalAmountRem = $existingAmount[0]['total'];
                (int)$newAmount  = ((int)$request->input('tds_amount') + (int)$request->input('received_amount'));
                
                if(($finalAmountRem + $newAmount) > (int)$request->input('invoice_total_amount'))
                  return response()->json(array("paid_amount"=>array('Current amount is grater than invoice total amount')),400);
            }            
            if($id == null || $id == '')
            {
                $receipt->media_id = $request->input('media_id');
                $receipt->branch_id = $request->input('branch_id');
                $receipt->invoice_no = $request->input('invoice_no');
                $receipt->invoice_id = $request->input('invoice_id');
                $branch = Branch::find($receipt->branch_id);
                $branch->receipt_num = $branch->receipt_num + 1;
                $branch->save();
                $receipt->receipt_num =$branch->branch_code."/R/".$branch->receipt_num;
            }
            $receipt->received_amount = $request->input('received_amount');
            $receipt->tds_amount = $request->input('tds_amount');            
            $receipt->payment_mode = $request->input('payment_mode');
            $receipt->transaction_id = $request->input('transaction_id');
            $receipt->cheque_dd = $request->input('cheque_dd');
            $receipt->payment_received_date = $request->input('payment_received_date');  
            $receipt->transaction_date = $request->input('transaction_date');
            $receipt->save();
            return response()->json($receipt);

    }
}