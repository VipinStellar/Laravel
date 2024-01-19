<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceRequest;
use App\Models\ServicePayment;
use App\Models\ServiceInvoice;
use App\Models\MediaPrice;
use App\Models\FinalPrice;
use App\Models\Media;
use Carbon\Carbon;
use Helper;
use DB;
use PaymentProcess;


class PaymentApiController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function updatePoNumber(Request $request)
    {
            $media = Media::find($request->input('id'));
            $media->po_number = $request->input('po_number');
            $media->po_date = $request->input('po_date');
            $media->save();
            return response()->json($media);
    }

    public function addPayment(Request $request)
    {
       // Get Customer Details
       $contact_data =  PaymentProcess::getCustomerDetails($request->input('media_id'));

        $request['firstname']= $contact_data['name'];
        $request['email']   = $contact_data['email'];
        $request['phone']   = $contact_data['phone'];
        $request['address'] = $contact_data['address'];
        $request['landmark']= $contact_data['landmark'];
        $request['city'] = $contact_data['city'];
        $request['state'] = $contact_data['state'];
        $request['state_code'] = $contact_data['state_code'];
        $request['pincode']   = $contact_data['pincode'];
        $request['branch_id'] = $contact_data['branch_id'];
        $request['gst_no']  = $contact_data['gst_no'];
        $request['tax_applicable'] = $contact_data['tax_applicable'];
        $request['job_id']  = $contact_data['job_id'];
        $request['deal_id'] = $contact_data['deal_id'];
        $request['media_type'] = $contact_data['media_type'];
        $request['branch'] = $this->_getBranchName($contact_data['branch_id']);
        // validation
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'phone'     => 'required',
            'email'     => 'required|email',
            'address'   => 'required',
            'pincode'   => 'required',
            'state_code'=> 'required',
            'branch_id' => 'required',
            'plan_type' => 'required',
            'payment_channel'=>'required',
            'media_type'  => 'required',
        ],
        [
            'firstname.required' => 'Name',
            'phone.required' => 'Phone no',
            'email.required' => 'Email',
            'address.required' => 'Address',
            'pincode.required' => 'Pincode',
            'state_code.required' => 'State Code',
            'branch_id.required' => 'Name',
            'plan_type.required' => 'Phone no',
            'payment_channel.required' => 'Payment Channel',
            'media_type.required' => 'Media Type',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        /// Calculate tax amount
        if($contact_data['tax_applicable'] == 1){
            $tax_rate        = (isset($contact_data['tax_rate']) && $contact_data['tax_rate'] != '') ? (int)$contact_data['tax_rate'] : 18;
            $request["tax_rate"] = $tax_rate;
        }else{
            $tax_rate        = 0;
            $request["tax_rate"]  = $tax_rate;
        }
        $paid_amount = $request->input('paid_amount');
        $base_amount = (($paid_amount * 100)/(100 + $tax_rate));
        $request['base_amount'] = round($base_amount);
        $tax_amount  = (($base_amount * $tax_rate) / 100);
        $request['total_tax'] = round($tax_amount);
        $request['payment_amount'] = $paid_amount;
        $request['pay_now']        = "RECV";
        // Add Payment Request
        $addPayment = PaymentProcess::AddPaymentRequest($request);
        if($addPayment && $addPayment['id']){
            return json_encode($addPayment);
        }
        
    }

    public function paymentList(Request $request)
    {
            $term = trim($request->input('term'));
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');
            $searchfieldName = $request->input('searchfieldName');
            $branchId = implode(',',$this->_getBranchId());
            
            $select = 'service_request.*,service_payments.payment_item,service_payments.total_amount,service_payments.total_tax,
                      service_payments.payment_amount,service_payments.payment_status,service_payments.payment_type,service_payments.payment_txnid,
                      service_payments.payment_timestamp,service_invoice.invoice_no,service_invoice.id as invoiceId,service_invoice.irn_status,
                      branch.branch_name';
            $query = DB::table('service_request')->select(DB::raw($select));
            $query->leftJoin("service_payments","service_payments.request_id", "=", "service_request.id");
            $query->leftJoin("service_invoice",function($join)
                        {
                            $join->on("service_invoice.request_id", "=", "service_request.id");
                            $join->on("service_invoice.payment_id", "=", "service_payments.id");
                        });
            $query->leftJoin("media","media.id", "=", "service_request.media_id");
            $query->leftJoin('branch', 'branch.id', '=', 'media.branch_id');
            $query->where("service_payments.payment_status","=", "success");
            if(auth()->user()->role_id !=1)
            $query->whereRaw("(service_request.branch_id in ($branchId))");
        
            if($term !=null && $term !='' && $searchfieldName !=null && $searchfieldName !='' )
            {
                if($searchfieldName == "branch_id")
                    $query->whereRaw("service_request.branch_id in ($term)");
                elseif(($searchfieldName == "invoice_no"))
                    $query->Where('service_invoice.'.$searchfieldName, '=', "".$term."");
                elseif(($searchfieldName == "payment_txnid"))
                    $query->Where('service_payments.'.$searchfieldName, '=', "".$term."");
                else
                    $query->Where('service_request.'.$searchfieldName, 'LIKE', '%'.$term.'%'); 
            }
            if($startDate != null && $endDate != null)
            {                    
                $startDate = date('Y-m-d', strtotime($startDate))." 00:00:00";            
                $endDate = date('Y-m-d', strtotime($endDate. ' + 1 days'))." 00:00:00";
                $query->whereBetween('service_payments.payment_timestamp',[$startDate,$endDate]);
            }
        
            $query->orderBy($request->input('orderBy'), $request->input('order'));
            $pageSize = $request->input('pageSize');
            $data = $query->paginate($pageSize,['*'],'page_no');
            $results = $data->items();
            $count = $data->total();
            $data = [
                "draw" => $request->input('draw'),
                "recordsTotal" => $count,
                "data" => $results
                ];
                return json_encode($data);
    }

    public function generateInvoice($paymentId)
    {   
        // Get Payment Details
        $PaymentData = ServicePayment::join('service_request', 'service_request.id', '=', 'service_payments.request_id')
                                ->select('service_request.*','service_payments.id as payment_id','service_payments.total_amount','service_payments.total_tax','service_payments.tax_rate','service_payments.payment_amount','service_payments.payment_status','service_payments.payment_txnid','service_payments.existing_payment','service_payments.payment_mode','service_payments.payment_timestamp')
                                ->where('service_payments.request_id', $paymentId)->orderBy('service_payments.id','desc')->first();
    
        if($PaymentData){
            $PaymentData['plan_id'] = $this->getPlanId($PaymentData['plan_type']);
            $Media = Media::find($PaymentData['media_id'],['tax_applicable','po_number']);
            $PaymentData['tax_applicable'] = $Media['tax_applicable'];
            $PaymentData['po_number'] = $Media['po_number'];
            $PaymentData['amount'] = $PaymentData['payment_amount'];
            $PaymentData['branch'] = $this->_getBranchName($PaymentData['branch_id']);
            // return json_encode($Media);
            // Generate Invoice
            $ServiceInvoice = PaymentProcess::GenrateInvoice($PaymentData);
            if($ServiceInvoice){
                return json_encode($ServiceInvoice);   
            }
        }            
    }
    public function generateIrn($invoiceId){
        // Get Invoice Details
        $payDetail = ServiceInvoice::join('service_request', 'service_request.id', '=', 'service_invoice.request_id')
                         ->join('service_payments', 'service_payments.id', '=', 'service_invoice.payment_id')
                         ->select('service_request.*','service_invoice.*','service_payments.payment_item','service_payments.tax_rate','service_payments.total_tax','service_payments.zoho_payment_id')
                         ->where('service_invoice.id', $invoiceId)->first();

        // Get Branch Details
        $BranchData = PaymentProcess::GetBranchDetails($payDetail['branch_id']);

        // check cess_rate
        if($payDetail['state_code'] == 'TEMPRORY-FOR-OTH-CESS' && $payDetail['state_code'] == $BranchData['state_code'] && empty($payDetail['gst_no']))
            $cess_rate = 1;
        else
            $cess_rate = 0;
       
        // Generate IRN
        if(!empty($payDetail['gst_no'])){

            $sezcheck = 'B2B';
            if($payDetail['sez'] == 1 && $payDetail['total_tax'] ==0)
             $sezcheck = 'SEZWOP';
            elseif($payDetail['sez'] == 1 && $payDetail['total_tax'] !=0 && !empty($payDetail['total_tax']))
             $sezcheck = 'SEZWP';
            
            $e_invoice_data = array(
            "buyer_info" => array(
                "Id"    => $payDetail['request_id'],
                "Name"  => $payDetail['firstname'],
                "Email" => $payDetail['email'],
                "Phone" => $payDetail['phone'],
                "Address"  => $payDetail['address'],
                "Address2" => $payDetail['landmark'],
                "City" => $payDetail['city'],
                "State" => $payDetail['state'],
                "StateCode" => $payDetail['state_code'],
                "PinCode" => $payDetail['zipcode'],
                "Gstin" => $payDetail['gst_no'],
                "Sez" => $payDetail['sez'],
                "PaymentType" => 'service_invoice',
                "PaymentDate" => date('d/m/Y',strtotime($payDetail['created_on'])),
                "invoice_info" => array(
                    "Type" => 'INV',
                    "SupTyp" => $sezcheck,
                    "IsServc" => "Y",
                    "IgstOnIntra" => ($payDetail['sez'] == 1 && $payDetail['state_code'] == $BranchData['state_code']) ? 'Y' : 'N',
                    "Hsn" => $payDetail['hsn'],
                    "InvNo" => $payDetail['invoice_no'],
                    "InvDate" => date('d/m/Y',strtotime($payDetail['created_on'])),
                    "ItemDesc" => $payDetail['payment_item'],
                    "Qty" => 1,
                    "UnitPrice" => $payDetail['base_amount'],
                    "GstRate" => $payDetail['tax_rate'],
                    "CesRt" => $cess_rate,
                    "BaseAmt" => $payDetail['base_amount'],
                    "IgstAmt" => $payDetail['igst'],
                    "CgstAmt" => $payDetail['cgst'],
                    "SgstAmt" => $payDetail['sgst'],
                    "UgstAmt" => $payDetail['ugst'],
                    "CesAmt" => $payDetail['gst_cess'],
                    "TotalAmt" => $payDetail['final_amount']
                )
            ),
            "seller_info" => array(
                "Name" => $BranchData['branch_name'],
                "Email" => $BranchData['branch_mail'],
                "Phone" => '',
                "Address" => $BranchData['address_short'],
                "City" => $BranchData['city_name'],
                "State" => $BranchData['state_name'],
                "StateCode" => $BranchData['state_code'],
                "PinCode" => $BranchData['pincode'],
                "Gstin" => $BranchData['gst_no'],
                "OwnerId" => $BranchData['branch_owner_id'],
              )
            );
            $set_irn_data = PaymentProcess::setIrnDataJson($e_invoice_data);
            $get_irn_response = PaymentProcess::getIrnData($set_irn_data);
           
            //echo '<pre>';print_r($get_irn_response);echo '</pre>';
            if($get_irn_response['status']==1){
                $irn_status = $get_irn_response['status'];
                $irn_msg = trim($get_irn_response['msg']);
                $irn_no = $get_irn_response['Irn'];
                $irn_date = $get_irn_response['IrnDate'];
                $irn_qrcode = $get_irn_response['SignedQRCode'];
            }else{
                $irn_status = $get_irn_response['status'];
                $irn_msg = trim($get_irn_response['msg']);
                $irn_no = '';
                $irn_date = '';
                $irn_qrcode = '';
            }
        }
        else{
                $irn_status = 2;
                $irn_msg = NULL;
                $irn_no = NULL;
                $irn_date = NULL;
                $irn_qrcode = NULL;
        }
            $ServiceInvoice = ServiceInvoice::find($invoiceId);
            $ServiceInvoice->irn_status     = $irn_status;
            $ServiceInvoice->irn_code       = $irn_no;
            $ServiceInvoice->signed_qrcode  = $irn_qrcode;
            $ServiceInvoice->irn_created_on = $irn_date;
            $ServiceInvoice->irn_msg        = $irn_msg;
            $ServiceInvoice->Save();
            if($ServiceInvoice->irn_status !=0){
                PaymentProcess::GenrateInvoicePDF($ServiceInvoice);
                if($payDetail['media_id'] !=0 && $payDetail['media_id'] !=null &&  empty($payDetail['zoho_payment_id']))
                {
                    $mediadata = Media::find($payDetail['media_id']);
                    $mediadata->ServiceInvoice = $ServiceInvoice;
                    $mediadata->ServiceRequest = ServiceRequest::find($ServiceInvoice->request_id);
                    $mediadata->ServicePayment = ServicePayment::find($ServiceInvoice->payment_id);
                    Helper::sendZohoCrmData($mediadata,'ADD-PAYMENT');
                }
                elseif(($payDetail['media_id'] ==0 || $payDetail['media_id'] == null) && !empty($payDetail['zoho_payment_id']))
                {
                    $ServiceInvoice->ServiceRequest = ServiceRequest::find($ServiceInvoice->request_id);
                    $ServiceInvoice->ServicePayment = ServicePayment::find($ServiceInvoice->payment_id);
                    Helper::sendZohoCrmData($ServiceInvoice,'INVOICE-PAYMENT-UPDATE');
                }
            }
            return json_encode($ServiceInvoice); 
    }
}
