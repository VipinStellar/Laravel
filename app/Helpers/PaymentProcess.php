<?php 

namespace App\Helpers;
use Illuminate\Support\Facades\Mail;
use Spipu\Html2Pdf\Html2Pdf;
use Carbon\Carbon;
use App\Models\FinalPrice;
use App\Models\Media;
use App\Models\MediaPrice;
use App\Models\Branch;
use App\Models\State;
use App\Models\ServiceRequest;
use App\Models\ServicePayment;
use App\Models\ServiceInvoice;
use DB;
use Helper;
class PaymentProcess
{
    //GSTIN Based Business Details
    public static function getGstinData($gstin) {
        $gstin = strtoupper(trim($gstin));
        $api_result = "";
        $client_id = 'b31a1ee4-efd1-4ab5-affc-46cc2a999520';
        $client_secret = '526460cc-3662-4532-ac75-1d5c5a24eaff';
        
        if(!empty($gstin) && strlen($gstin) == 15){
            $query = http_build_query(array('email' => 'monu@stellarinfo.com', 'gstin' => $gstin));
            $url = 'https://api.mastergst.com/public/search?'. $query;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // Set the content type to application/json
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'client_id: '.$client_id, 
                'client_secret: '.$client_secret, 
                'Accept: application/json')
            );
            
            // Return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            // var_dump($ch);
            
            curl_close($ch);
        
            // Decode JSON response:
            $api_result = json_decode($result, true);
        }
        return $api_result;
    }

     //set IRN request JSON payload
     public static function setIrnDataJson($params=array()){
        $buyer_info = $params['buyer_info'];
        $invoice_info = $params['buyer_info']['invoice_info'];
        $seller_info = $params['seller_info'];
        $item_unit = $invoice_info['IsServc'] == 'N' ? 'NOS' : 'OTH';
        if(empty($invoice_info['IgstAmt']) || $invoice_info['IgstAmt'] ==0){
        if(!empty($invoice_info['UgstAmt']) && $invoice_info['UgstAmt'] !=0){
            $SgstAmt = $invoice_info['UgstAmt'];
        }else{
        $SgstAmt = $invoice_info['SgstAmt'];
        }
        }else{
            $SgstAmt = 0;
        }
    
        $json = array(
            array(
                "transaction"=> array(
                    "Version"=> "1.1",
                    "TranDtls"=> array(
                    "TaxSch"=> "GST",
                    "SupTyp"=> $invoice_info['SupTyp'],
                    "RegRev"=> "N",
                    "EcmGstin"=> null
                    ),
                    "DocDtls"=> array(
                    "Typ"=> $invoice_info['Type'],
                    "No"=> $invoice_info['InvNo'],
                    "Dt"=> $invoice_info['InvDate']
                    ),
                    "SellerDtls"=> array(
                    "Gstin"=> $seller_info['Gstin'],
                    "LglNm"=> $seller_info['Name'],
                    "TrdNm"=> null,
                    "Addr1"=> empty($seller_info['Address']) ? null : $seller_info['Address'],
                    "Addr2"=> null,
                    "Loc"=> empty($seller_info['City']) ? null : $seller_info['City'],
                    "Pin"=> empty($seller_info['PinCode']) ? null : $seller_info['PinCode'],
                    "Stcd"=> empty($seller_info['StateCode']) ? null : $seller_info['StateCode'],
                    "Ph"=> null,
                    "Em"=> empty($seller_info['Email']) ? null : $seller_info['Email']
                    ),
                    "BuyerDtls"=> array(
                    "Gstin"=> $buyer_info['Gstin'],
                    "LglNm"=> $buyer_info['Name'],
                    "TrdNm"=> null,
                    "Pos"=> empty($buyer_info['StateCode']) ? null : $buyer_info['StateCode'],
                    "Addr1"=> empty($buyer_info['Address']) ? null : $buyer_info['Address'],
                    "Addr2"=> null,
                    "Loc"=> empty($buyer_info['State']) ? null : $buyer_info['State'],
                    "Pin"=> empty($buyer_info['PinCode']) ? null : $buyer_info['PinCode'],
                    "Stcd"=> empty($buyer_info['StateCode']) ? null : $buyer_info['StateCode'],
                    "Ph"=> empty($buyer_info['Phone']) ? null : $buyer_info['Phone'],
                    "Em"=> empty($buyer_info['Email']) ? null : $buyer_info['Email']
                    ),
                    "ItemList"=> array(
                    array(
                        "SlNo"=> 1,
                        "PrdDesc"=> empty($invoice_info['ItemDesc']) ? 'null' : $invoice_info['ItemDesc'],
                        "IsServc"=> $invoice_info['IsServc'],
                        "HsnCd"=> empty($invoice_info['Hsn']) ? 'null' : $invoice_info['Hsn'],
                        "Qty"=> (int)$invoice_info['Qty'],
                        "Unit"=> $item_unit,
                        "UnitPrice"=> floatval($invoice_info['UnitPrice']),
                        "TotAmt"=> floatval($invoice_info['BaseAmt']),
                        "AssAmt"=> floatval($invoice_info['BaseAmt']),
                        "GstRt"=> floatval($invoice_info['GstRate']),
                        "IgstAmt"=> floatval($invoice_info['IgstAmt']),
                        "CgstAmt"=> floatval($invoice_info['CgstAmt']),
                        "SgstAmt"=> floatval($SgstAmt),
                        "CesRt"=> floatval($invoice_info['CesRt']),
                        "CesAmt"=> floatval($invoice_info['CesAmt']),
                        "TotItemVal"=> floatval($invoice_info['TotalAmt'])
                    )
                    ),
                    "ValDtls"=> array(
                    "AssVal"=> floatval($invoice_info['BaseAmt']),
                    "CgstVal"=> floatval($invoice_info['CgstAmt']),
                    "SgstVal"=> floatval($SgstAmt),
                    "IgstVal"=> floatval($invoice_info['IgstAmt']),
                    "CesVal"=> floatval($invoice_info['CesAmt']),
                    "TotInvVal"=> floatval($invoice_info['TotalAmt'])
                    )
                )
            )
        );
    
        return array(
                'seller_gstin' => $seller_info['Gstin'],
                'seller_ownerid' => $seller_info['OwnerId'],
                'invoice_data' => $json
            );
    }     
  
    //get IRN and QRcode data from cleartx api
    public static function getIrnData($req_data=array()) {
        $data = array ();
        $data['status'] = 0;
        $data['msg'] = 'Request Payload not correct';
        
        if (!empty($req_data) && $req_data !== null && $req_data !== false) {
            $e_invoice_data = json_encode($req_data['invoice_data']);		
         
            $seller_gstin = $req_data['seller_gstin'];
            $seller_ownerid = $req_data['seller_ownerid'];
            $api_url = env('GST_API_URL');
            $api_token = env('GST_API_TOKEN');
            
            if (!empty($e_invoice_data) && !empty($seller_gstin) && !empty($seller_ownerid)) {
                $curl = curl_init();
                
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    //CURLOPT_SSL_VERIFYPEER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 20,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => $e_invoice_data,
                    CURLOPT_HTTPHEADER => array(
                        'X-Cleartax-Auth-Token: '.$api_token.'',
                        'x-cleartax-product: EInvoice',
                        'Content-Type: application/json',
                        // 'owner_id: '.$seller_ownerid.'',
                        'gstin: '.$seller_gstin.''
                    ),
                ));
            
                $response = curl_exec($curl);
                $error = curl_error($curl);
            
                curl_close($curl);
                //Log Api data
                /*$log_data = array(
                    "log_name" => 'Cleartax E-Invoice API',
                    "log_request_url" => $api_url,
                    "log_request" => $e_invoice_data,
                    "log_response" => !empty($error) ? $error : $response
                );
                logApiCall($log_data);*/
                if($error) {
                    $data['status'] = 0;
                    $data['msg'] = $error;
                } else {
                    $response = json_decode($response, true);
                    if(!empty($response) && isset($response[0]) && array_key_exists("govt_response",$response[0]) && $response[0]['govt_response']['Success'] == 'Y' && !empty($response[0]['govt_response']['Irn'])){
                        $data['status'] = 1;
                        $data['msg'] = 'IRN Generated successfully';
                        $data['Irn'] = $response[0]['govt_response']['Irn'];
                        $data['IrnDate'] = $response[0]['govt_response']['AckDt'];
                        $data['SignedQRCode'] = $response[0]['govt_response']['SignedQRCode'];
                    } else{
                        $data['status'] = 0;
                        if(!empty($response) && isset($response[0]) && array_key_exists("govt_response",$response[0]) && !empty($response[0]['govt_response']['ErrorDetails'])){
                            $data['msg'] = $response[0]['govt_response']['ErrorDetails'][0]['error_message'];
                        } else{
                            $data['msg'] = 'Error in IRN generation. Please check request data';
                        }
                    }
                }
            }
            else {
                $data['status'] = 0;
                $data['msg'] = 'Request Payload not found';
            }
            
        }
        
        //echo json_encode($data);
        return $data;
    }

    public static function GetBranchDetails($branchId){
        $branchResult = Branch::find($branchId);
        return $branchResult;
    }

    public function getStateType($state_code){
        $state_result = DB::table('states')->select('state_type')->where('state_code', $state_code)->first();
        return $state_result->state_type;
    }

    public static function getMediaPriceList($media_id){
        $media_price = MediaPrice::join('job_service_plan', 'job_service_plan.plan_id', '=', 'media_price.plan_id')
                     ->join('media','media.id','=','media_price.media_id')
                     ->select('media_price.*','job_service_plan.plan_type','job_service_plan.zoho_plan_id','job_service_plan.service_type','media.tax_applicable','media.tax_rate as media_tax_rate')
                     ->where('media_price.media_id',$media_id)
                     ->where('media_price.is_visible',1)
                     ->orderBy('media_price.plan_id', 'asc')->get();
        return $media_price;
    }

    public static function getMediaFinalPrice($media_id){
        $result = FinalPrice::join('job_service_plan', 'job_service_plan.plan_id', '=', 'final_price.plan_id')
                  ->join('media','media.id','=','final_price.media_id')
                  ->leftjoin('media_price','media_price.media_id', '=', DB::raw("final_price.media_id and media_price.plan_id=final_price.plan_id"))
                  ->select('final_price.*','job_service_plan.plan_type','job_service_plan.zoho_plan_id','media_price.estimated_days','media_price.speed','media_price.support','job_service_plan.service_type','media.tax_applicable','media.tax_rate as media_tax_rate')
                  ->where('final_price.media_id',$media_id)->first();
        return $result;
    }

    public static function getCustomerDetails($media_id){
        $contact_data = Media::leftjoin('contact', 'contact.zoho_contact_id', '=', 'media.customer_id')
                            ->leftjoin('company','company.zoho_company_id', '=', 'contact.company_id')
                            ->select('media.id','media.deal_id','media.job_id','media.branch_id','media.media_type','media.tax_applicable','media.tax_rate','media.po_number','contact.customer_name','contact.gst_number as con_gst_number','contact.email','contact.mobile','contact.mailing_street','contact.mailing_region','contact.mailing_city','contact.mailing_state_ut','contact.mailing_state_code','contact.mailing_zip','contact.mailing_country','contact.use_billing_address',
                            'contact.billing_name as cus_billing_name','contact.gst_number as cus_gst_number','contact.billing_email as cus_billing_email','contact.billing_phone as cus_billing_phone','contact.billing_street as cus_billing_street','contact.billing_landmark as cus_billing_landmark','contact.billing_city as cus_billing_city','contact.billing_state as cus_billing_state','contact.billing_state_code as cus_billing_state_code',
                            'contact.billing_country as cus_billing_country','contact.billing_zip as cus_billing_zip','company.zoho_company_id','company.company_name','company.gst_number','company.billing_street','company.billing_landmark','company.billing_city','company.billing_state_ut','company.billing_state_code','company.billing_code','company.billing_country')
                            ->where('media.id',$media_id)->first();
        $data = array();
        if($contact_data){
            if($contact_data['use_billing_address'] =="Same as Account Billing Address"){
                $data['name'] = $contact_data['company_name'];
                $data['email'] = $contact_data['email'];
                $data['phone'] = $contact_data['mobile'];
                $data['address'] = $contact_data['billing_street'];
                $data['landmark'] = $contact_data['billing_landmark'];
                $data['city'] = $contact_data['billing_city'];
                $data['state'] = $contact_data['billing_state_ut'];
                $data['state_code'] = $contact_data['billing_state_code'];
                $data['country']= $contact_data['billing_country'];
                $data['pincode'] = $contact_data['billing_code'];
                $data['gst_no'] = $contact_data['gst_number'];
            }elseif($contact_data['use_billing_address'] =="Same as Contact Mailing Address"){
                $data['name'] = $contact_data['customer_name'];
                $data['email'] = $contact_data['email'];
                $data['phone'] = $contact_data['mobile'];
                $data['address'] = $contact_data['mailing_street'];
                $data['landmark']= $contact_data['mailing_region'];
                $data['city'] = $contact_data['mailing_city'];
                $data['state'] = $contact_data['mailing_state_ut'];
                $data['state_code'] = $contact_data['mailing_state_code'];
                $data['country']=$contact_data['mailing_country'];
                $data['pincode'] = $contact_data['mailing_zip'];
                $data['gst_no'] = $contact_data['cus_gst_number'];
            } else {
                $data['name'] = $contact_data['cus_billing_name'];
                $data['email'] = $contact_data['cus_billing_email'];
                $data['phone'] = $contact_data['cus_billing_phone'];
                $data['address'] = $contact_data['cus_billing_street'];
                $data['landmark']= $contact_data['cus_billing_landmark'];
                $data['city'] = $contact_data['cus_billing_city'];
                $data['state'] = $contact_data['cus_billing_state'];
                $data['state_code'] = $contact_data['cus_billing_state_code'];
                $data['country']=$contact_data['cus_billing_country'];
                $data['pincode'] = $contact_data['cus_billing_zip'];
                $data['gst_no'] = '';
            }
                $data['branch_id'] = $contact_data['branch_id'];
                $data['tax_applicable'] = $contact_data['tax_applicable'];
                $data['tax_rate'] = $contact_data['tax_rate'];
                $data['job_id'] = $contact_data['job_id'];
                $data['deal_id'] = $contact_data['deal_id'];
                $data['media_type'] = $contact_data['media_type'];
                $data['po_number']  = $contact_data['po_number'];
        }  
        return $data;
    }

    public static function AddPaymentRequest($request){
        $BranchData = self::GetBranchDetails($request->input('branch_id'));
        //set state for other territory
        $state = strip_tags($request->input('state'));
        $state_code = strip_tags($request->input('state_code'));
        
        if($state == '97'){
            $state      = $BranchData['state_name'];
            $state_code = $BranchData['state_code'];
        }

        //verify GSTIN
        $sez = 0;
        $gst_no = trim(strtoupper($request->input('gst_no')));
        if(isset($gst_no) && !empty($gst_no)){
            $gstinData = self::getGstinData($gst_no);
            if(!empty($gstinData) && $gstinData['status_cd'] == 1 && array_key_exists("data",$gstinData) && $gstinData['data']['sts'] == 'Active'){
                $taxpayer_type = strtoupper($gstinData['data']['dty']);
                if($taxpayer_type=='SEZ UNIT'){
                    $sez= 1 ;
                }
            }
            else{
                    $gst_no = "";
            }
        }
        $date = Carbon::now();
        // insert data in service_request table
            if(($request->input('payment_channel')=='Offline'|| $request->input('payment_channel')=='Online-Admin') && ($request->input('reqId') != '' && $request->input('reqId') != null))
            $ServiceRequest = ServiceRequest::where('id',$request->input('reqId'))->first();
            else
            $ServiceRequest = New ServiceRequest();
            $ServiceRequest->media_id  = strip_tags($request->input('media_id'));
            $ServiceRequest->firstname = strip_tags($request->input('firstname'));
            $ServiceRequest->email     = strip_tags($request->input('email'));
            $ServiceRequest->phone     = strip_tags($request->input('phone'));
            $ServiceRequest->address   = strip_tags($request->input('address'));
            $ServiceRequest->landmark  = strip_tags($request->input('landmark'));
            $ServiceRequest->city      = strip_tags($request->input('city'));
            $ServiceRequest->state     = $state;
            $ServiceRequest->state_code= $state_code;
            $ServiceRequest->zipcode   = strip_tags($request->input('pincode'));
            $ServiceRequest->gst_no    = $gst_no;
            $ServiceRequest->sez       = $sez;
            $ServiceRequest->job_id    = strip_tags($request->input('job_id'));
            $ServiceRequest->deal_id   = strip_tags($request->input('deal_id'));
            $ServiceRequest->plan_type = strip_tags($request->input('plan_type'));
            $ServiceRequest->branch_id = strip_tags($request->input('branch_id'));
            if($request->input('payment_channel') == 'Online' || empty($request->input('payment_date'))){
                $ServiceRequest->submit_timestamp = $date->format('Y-m-d H:i:s'); //Carbon::now()->toDateTimeString();
            } else {
                $ServiceRequest->submit_timestamp = ($request->input('existing_payment') == 'Credit') ? $date->format('Y-m-d H:i:s'): date("Y-m-d H:i:s", strtotime($request->input('payment_date')));
            }

            $ServiceRequest->save();
        //create Random Digit
            $random_digit   = rand(0000,9999);
            $order_no       = 'SVON-'.$random_digit.$ServiceRequest->id;
        /// Update the order number
            $ServiceRequest->order_no  = $order_no;
            $ServiceRequest->save();
        // Insert service_payments table data
            $ServicePayment = ServicePayment::where('request_id',$ServiceRequest->id)->first();
           if($ServicePayment == null || $ServicePayment == ''){
             $ServicePayment = New ServicePayment(); 
            }
            $ServicePayment->request_id    = $ServiceRequest->id;
            $ServicePayment->payment_item  = strip_tags($request->input('media_type'));
            $ServicePayment->total_amount  = $request->input('base_amount');
            $ServicePayment->total_tax     = $request->input('total_tax');
            $ServicePayment->tax_rate      = $request->input('tax_rate');
            $ServicePayment->payment_amount = $request->input('payment_amount');
            if($request->input('payment_channel') == 'Online'){
                $ServicePayment->payment_txnid = $request->input('txnid');
                $ServicePayment->payment_status = 'processed';
            }else{
                $ServicePayment->payment_txnid = ($request->input('existing_payment') == 'Credit')?$request->input('po_number').'/'.$ServiceRequest->id:$request->input('txn_no');
                $ServicePayment->payment_status = 'success';
                $ServicePayment->existing_payment = $request->input('existing_payment');
            }
            $ServicePayment->payment_timestamp = $ServiceRequest->submit_timestamp;
            $ServicePayment->payment_coupon = '';
            $ServicePayment->payment_channel = ($request->input('payment_channel') == 'Online-Admin')?'Online':$request->input('payment_channel');
            $ServicePayment->payment_category = 'service';
            $ServicePayment->payment_mode     = $request->input('payment_mode');
            $ServicePayment->payment_type   =  $request->input('pay_now');
            $ServicePayment->save();
        // Return Response
        return $ServiceRequest;
    }
    public static function GenrateInvoice($params = array()){
        // check customer state type
        //$params['sez'] == 1 ? 'SEZWP' : 'B2B'
        $state_type = (new self)->getStateType($params['state_code']);
        $base_amount= $params['total_amount'];
        $submit_timestamp = Carbon::now()->format('Y-m-d H:i:s');
        // Branch Details
        $sezcheck = $params['sez'] == 1 ? 'SEZWP' : 'B2B';
        $BranchData = self::GetBranchDetails($params['branch_id']);
        // Calculate tax
        if($params['tax_applicable'] == 0 && !empty($params['gst_no']) && $params['sez'] == 1){
            $tax_rate  = 18;
            $gst_rate  = 18;
            $integrated_calculation = ($base_amount * $tax_rate) / 100;
            $integrated_tax = round($integrated_calculation,2);
            $cess_rate = 0;
            $igst_rtax = 0;
            $ugst_rtax = 0;
            $sgst_rtax = 0; 
            $cgst_rtax = 0;
            $cess_rtax = 0;
            $layout = 2;
            $total_tax =0;
            $sezcheck = 'SEZWOP';
        } else {
            if($params['state_code'] == 'TEMPRORY-FOR-OTH-CESS' && $params['state_code'] == $BranchData['state_code'] && empty($params['gst_no']) ){
                $tax_rate = $params['tax_rate'];
                $gst_rate = round($tax_rate/2);
                $cess_rate = 1;
                
                $sgst_tax = ($base_amount * $gst_rate) / 100;
                $sgst_rtax = round($sgst_tax,2);
                
                $cgst_tax = ($base_amount * $gst_rate) / 100;
                $cgst_rtax = round($cgst_tax,2);
                
                $cess_tax = ($base_amount * $cess_rate) / 100;
                $cess_rtax = round($cess_tax,2);
            } else {
                $tax_rate = $params['tax_rate'];
                $gst_rate = round($tax_rate/2);
                $cess_rate = 0;
            
                $igst_tax = ($base_amount * $tax_rate) / 100;
                $igst_rtax = round($igst_tax,2);
            
                $ugst_tax = ($base_amount * $gst_rate) / 100;
                $ugst_rtax = round($ugst_tax,2);
                
                $sgst_tax = ($base_amount * $gst_rate) / 100;
                $sgst_rtax = round($sgst_tax,2);
                
                $cgst_tax = ($base_amount * $gst_rate) / 100;
                $cgst_rtax = round($cgst_tax,2);
                
                $cess_rtax = 0;
            }
            
            if($params['state_code'] == '97'){
                $scode="SGST/CGST";
                $ugst_rtax=0;
                $igst_rtax=0;
                $cess_rtax=0;
                $layout = 0;
            }
            elseif($params['state_code'] == 'TEMPRORY-FOR-OTH-CESS' && $params['state_code'] == $BranchData['state_code'] && empty($params['gst_no']) ){
                $scode="SGST/CGST";
                $ugst_rtax=0;
                $igst_rtax=0;
                $cess_rtax = $cess_rtax;
                $layout = 0;
            } 
            else{
                if(($state_type == "UT"|| $BranchData['state_type'] == "UT") && ($params['state_code'] == $BranchData['state_code']) && ($params['sez'] != 1 || (empty($params['gst_no']) && $params['sez'] == 1))){
                    $scode="UGST/CGST";
                    $sgst_rtax=0;
                    $igst_rtax=0;
                    $cess_rtax=0;
                    $layout = 0;
                }
                else if(($params['state_code'] == $BranchData['state_code']) && ($state_type != "UT"|| $BranchData['state_type'] != "UT") && ($params['sez'] != 1 || (empty($params['gst_no']) && $params['sez'] == 1)))
                {
                    $scode="SGST/CGST";
                    $ugst_rtax=0;
                    $igst_rtax=0;
                    $cess_rtax=0;
                    $layout = 0;
                }
                else {
                    if(!empty($params['gst_no']) && $params['sez'] == 1){
                        $layout = 1;
                    }else{
                        $layout = 0;
                    }
                    $scode="IGST";
                    $sgst_rtax=0;
                    $ugst_rtax=0;
                    $cgst_rtax=0;
                    $cess_rtax=0;
                }
            }
            $total_tax = round(($igst_rtax + $ugst_rtax + $sgst_rtax + $cgst_rtax + $cess_rtax),2);
            $integrated_tax = 0;
        }
        $payment_amount = $base_amount + $total_tax;
        $rounding_off = round(round($payment_amount) - $payment_amount,2);
        //Update Media Final Price Table Data
        if($params['media_id'] != null && $params['media_id'] !=0)
        $updateFinalPriceData=self::updateFinalPrice($params);
        
        //set financial year and invoice series
        $month = date("m", strtotime($submit_timestamp));
        $year = date("Y", strtotime($submit_timestamp));
        $year_short = date("y", strtotime($submit_timestamp));
        
        if($month > 03) {
        $financial_year = $year.'-'.($year + 1);
        $financial_year_short = $year_short.($year_short + 1);
        }
        else {
        $financial_year = ($year - 1).'-'.$year;
        $financial_year_short = ($year_short - 1).$year_short;
        }
        
        $invoice_format = 'SI/'.$BranchData['branch_code'].'/'.$financial_year_short.'/';
        $hsn = 998399;

        // ARn Number
        if($params['tax_applicable'] == 0 && !empty($params['gst_no']) && $params['sez']==1)
        $arn_no = $BranchData['arn_num'];
        else
        $arn_no = '';
        
        $ServiceInvoice = New ServiceInvoice();
        $ServiceInvoice->request_id = $params['id'];
        $ServiceInvoice->payment_id = $params['payment_id'];
        $ServiceInvoice->hsn        = $hsn;
        $ServiceInvoice->branch     = $params['branch'];
        $ServiceInvoice->invoice_branch_id     = $params['branch_id'];
        $ServiceInvoice->final_amount = $params['payment_amount'];
        $ServiceInvoice->base_amount = $params['total_amount'];
        $ServiceInvoice->igst = $igst_rtax;
        $ServiceInvoice->ugst = $ugst_rtax;
        $ServiceInvoice->sgst = $sgst_rtax;
        $ServiceInvoice->cgst = $cgst_rtax;
        $ServiceInvoice->gst_cess = $cess_rtax;
        $ServiceInvoice->rounding_off = $rounding_off;
        $ServiceInvoice->layout = $layout;
        $ServiceInvoice->arn_num = $arn_no;
        $ServiceInvoice->integrated_tax = $integrated_tax;
        $ServiceInvoice->financial_year = $financial_year;
        $ServiceInvoice->po_no = $params['po_number'];
        $ServiceInvoice->po_date = $submit_timestamp;
        $ServiceInvoice->created_on = $submit_timestamp;
        if($ServiceInvoice->save()){
            $inv_row_id = $ServiceInvoice->id;
            $maxIdRow = ServiceInvoice::select(DB::raw('MAX(invoice_id) as invoice_id'))->where('invoice_branch_id', $params['branch_id'])->where('financial_year',$financial_year)->first();
            $maxId = (int)$maxIdRow->invoice_id + 1;
            
            $invoice_created_on = $submit_timestamp;
            $invoice_no = str_pad($maxId,4,'0',STR_PAD_LEFT);
            $invoice_no = $invoice_format.$invoice_no;
            /// Update Service Invoice
            $ServiceInvoice->invoice_no = $invoice_no;
            $ServiceInvoice->invoice_id = $maxId;
            $ServiceInvoice->Save();
            
            if($maxIdRow && !empty($BranchData['branch_code'])){
            if(!empty($params['gst_no'])){
                $e_invoice_data = array(
                "buyer_info" => array(
                    "Id"    => $params['id'],
                    "Name"  => $params['firstname'],
                    "Email" => $params['email'],
                    "Phone" => $params['phone'],
                    "Address"  => $params['address'],
                    "Address2" => $params['landmark'],
                    "City" => $params['city'],
                    "State" => $params['state'],
                    "StateCode" => $params['state_code'],
                    "PinCode" => $params['zipcode'],
                    "Gstin" => $params['gst_no'],
                    "Sez" => $params['sez'],
                    "PaymentType" => 'service_invoice',
                    "PaymentDate" => date('d/m/Y',strtotime($submit_timestamp)),
                    "invoice_info" => array(
                        "Type" => 'INV',
                        "SupTyp" =>$sezcheck,
                        "IsServc" => "Y",
                        "IgstOnIntra" => ($params['sez'] == 1 && $params['state_code'] == $BranchData['state_code']) ? 'Y' : 'N',
                        "Hsn" => $hsn,
                        "InvNo" => $invoice_no,
                        "InvDate" => date('d/m/Y',strtotime($invoice_created_on)),
                        "ItemDesc" => $params['payment_item'],
                        "Qty" => 1,
                        "UnitPrice" => $base_amount,
                        "GstRate" => $tax_rate,
                        "CesRt" => $cess_rate,
                        "BaseAmt" => $base_amount,
                        "IgstAmt" => $igst_rtax,
                        "CgstAmt" => $cgst_rtax,
                        "SgstAmt" => $sgst_rtax,
                        "UgstAmt" => $ugst_rtax,
                        "CesAmt" => $cess_rtax,
                        "TotalAmt" => $params['payment_amount']
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
                $set_irn_data = self::setIrnDataJson($e_invoice_data);
                $get_irn_response = self::getIrnData($set_irn_data);
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
            
              
                $ServiceInvoice->irn_status     = $irn_status;
                $ServiceInvoice->irn_code       = $irn_no;
                $ServiceInvoice->signed_qrcode  = $irn_qrcode;
                $ServiceInvoice->irn_created_on = $irn_date;
                $ServiceInvoice->irn_msg        = $irn_msg;
                $ServiceInvoice->Save();
            }
          
            // For Sending Attachment Email
            if($ServiceInvoice->irn_status !=0){
                $invoicePdf=self::GenrateInvoicePDF($ServiceInvoice);
                if($params['media_id'] != null && $params['media_id'] !=0)
                {
                    $mediadata = Media::find($params['media_id']);
                    $mediadata->ServiceInvoice = $ServiceInvoice;
                    $mediadata->ServiceRequest = ServiceRequest::where('id',$ServiceInvoice->request_id)->first();
                    $mediadata->ServicePayment = ServicePayment::where('request_id',$ServiceInvoice->request_id)->first();
                    Helper::sendZohoCrmData($mediadata,'ADD-PAYMENT');
                }
            }
            return $ServiceInvoice;
        }
    }

    public static function updateFinalPrice($params)
    {
        $finalPriceData   = FinalPrice::where('media_id',$params['media_id'])->where('plan_id',$params['plan_id'])->orderBy('id','desc')->first();
        if($finalPriceData && $finalPriceData['id'] !=''){
            $FinalPrice = FinalPrice::find($finalPriceData['id']);
            $FinalPrice->paid_amount = ((int)$finalPriceData->paid_amount + round($params['amount']));
            $FinalPrice->balance_amount = ((int)$finalPriceData->balance_amount - round($params['amount']));
            $FinalPrice->save();
        } else {
            /// Check plan
            $mediaPriceData = MediaPrice::where('media_id',$params['media_id'])->where('plan_id', $params['plan_id'])->first();

            if($mediaPriceData && $mediaPriceData->id !=''){
                // Update media_price Plan
                $MediaPrice = MediaPrice::find($mediaPriceData->id);
                $MediaPrice->selected_plan = 1;
                $MediaPrice->save();
                // Insert final_price Plan
                $media_tax_rate = $params['tax_rate'];
                $media_tax_amount = round(($mediaPriceData->total_fee * $media_tax_rate) / 100);
                $media_total_price = $mediaPriceData->total_fee + $media_tax_amount;
                $media_balance_amount = ($media_total_price - round($params['amount']));
                $FinalPrice = new FinalPrice();
                $FinalPrice->media_id = $params['media_id'];
                $FinalPrice->plan_id  = $params['plan_id'];
                $FinalPrice->base_amount = $mediaPriceData->total_fee;
                $FinalPrice->total_amount = $media_total_price;
                $FinalPrice->paid_amount = round($params['amount']);
                $FinalPrice->balance_amount = $media_balance_amount;
                $FinalPrice->advance_percent= $mediaPriceData->advance_percent;
                $FinalPrice->tax_amount = $media_tax_amount;
                $FinalPrice->tax_rate   = $media_tax_rate;
                $FinalPrice->save();
            }
        }
        // Send Zoho Crm Data
        $mediadata = Media::find($params['media_id']);
        $mediadata->SelectedPlan = $FinalPrice;
        $mediadata->SelectdPlanType = $params['plan_type'];
       
       Helper::sendZohoCrmData($mediadata,'PAYMENT-UPDATE');
    }

    public static function GenrateInvoicePDF($Invoice,$preview=''){
            $Invoice->ServiceRequest = ServiceRequest::where('id',$Invoice->request_id)->first();
            $Invoice->ServicePayment = ServicePayment::where('request_id',$Invoice->request_id)->first();
            $Invoice->Branch = Branch::find($Invoice->ServiceRequest->branch_id);
            if($Invoice->ServiceRequest->media_id != null && $Invoice->ServiceRequest->media_id !=0)
            $Invoice->Media  = Media::find($Invoice->ServiceRequest->media_id);
           else
           $Invoice->Media = new Media();
            $Invoice->priceText = self::getIndianCurrencyText($Invoice->final_amount);
            // for SEZ table show
            if($Invoice->layout == 2 && !empty($Invoice->integrated_tax)){
                $Invoice->TaxPriceText = self::getIndianCurrencyText($Invoice->integrated_tax);
            }else{
                $Invoice->TaxPriceText = '';
            }
            $Invoice->preview =  preg_replace('/\s+/', '', trim($preview));
            $data['result'] = $Invoice;
            
            $certificate = 'file://'.base_path().'/public/cert/stellar2023.crt';
            $info = array(
                'Name' => 'Stellar Data Recovery',
                'Location' => 'India',
                'Reason' => 'Notify User',
                'ContactInfo' => 'https://www.stellarinfo.co.in',
            );
                
            //    ========== Spipu =========
            $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', 0);	
          //  $html2pdf->setDefaultFont('times');
            $html2pdf->addFont('opensans','','opensans.php');
            $html2pdf->setDefaultFont('opensans');	
            $html2pdf->pdf->setFontSubsetting(false);
            $html2pdf->pdf->SetCreator('');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->pdf->SetAuthor('Stellar Data Recovery');
            $html2pdf->pdf->SetTitle('Invoice Of Stellar Data Recovery');
            $html2pdf->pdf->SetSubject('');
            $html = view('invoice',$data);
                if($preview=='view'){
                    return $html;
                }else{
                    $html2pdf->writeHTML($html);
                // set document signature
                    $html2pdf->pdf->setPage(1);
                    $html2pdf->pdf->setSignature($certificate, $certificate, 'stellar@321', '', 2, $info);
                    // define active area for signature appearance
                    $html2pdf->pdf->setSignatureAppearance(137, 8, 24, 10);  
                    $html2pdf->clean();
                    //Generate invoice name
                    $InvoicePdfName = str_replace('/','-',$Invoice->invoice_no);
                    $html2pdf->Output(storage_path('Invoice/'.$InvoicePdfName.'.pdf'), 'F');
                    //Send Mail
                    $emailTo = trim($Invoice->ServiceRequest['email']); //"raj.kumar@stellarinfo.com"; 
                    if($Invoice->ServicePayment['payment_type']=='ADVC'){ 
                        $payment_type_text = 'Advance Recovery Fee'; 
                    }elseif($Invoice->ServicePayment['payment_type']=='RECV2'){ 
                        $payment_type_text = 'Final Recovery Fee'; 
                    }else{ 
                        $payment_type_text = 'Recovery Fee'; 
                    }
                    $emailSubject = "Invoice of ".$payment_type_text." payment for Transaction Id ".$Invoice->ServicePayment['payment_txnid'];
                    $emailMessage = "<style type='text/css' >
                                    @import url(http://fonts.googleapis.com/css?family=Arial, Helvetica, sans-serif);
                                    body, u, b { margin: 0px; padding: 0px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 25px; color: #222222; }
                                    a img { border: none; outline: none; }
                                    img { border: none; outline: none }
                                    a { text-decoration: none; color: #595959; outline: none; }
                                    a, a:link, a:visited { text-decoration: none !important; color: #000 }
                                    p { margin: 0px; padding: 0px; }
                                    img, tr, td { margin: 0px; padding: 0px; border-collapse: collapse; }
                                    table a { text-decoration: none; }
                                    </style>
                                    <table cellpadding='15' cellspacing='0' width='100%' align='center' style='background-color:#e3e3e3; line-height:25px;'>
                                    <tr>
                                        <td height='15'></td>
                                    </tr>
                                    <tr>
                                        <td><table cellpadding='0' cellspacing='0' width='600' align='center' style='background-color:#000000; border-radius:10px 10px 0 0;'>
                                            <tr>
                                            <td align='center' valign='middle' style='height:95px;'><img src='".url('public/images/logo.png')."' alt='Stellar Data Recovery' title='Stellar Data Recovery' /></td>
                                            </tr>
                                        </table>
                                        <table cellpadding='0' cellspacing='0' width='600' align='center' style='background-color:#ffffff;border-radius: 0 0 10px 10px;'>
                                            <tr>
                                            <td height='20' colspan='3'></td>
                                            </tr>
                                            <tr>
                                            <td width='4%'></td>
                                            <td align='left' valign='top' width='92%' style='color:#000000; padding-top:10px;'>Dear Valued Customer,</td>
                                            <td width='4%'></td>
                                            </tr>
                                            <tr>
                                            <td align='left' valign='top' height='20' style='background-color:#fff;' colspan='3'></td>
                                            </tr>
                                            <tr>
                                            <td width='4%'></td>
                                            <td align='left' valign='top' width='92%' ><table cellpadding='0' cellspacing='0' width='100%'>
                                                <tr>
                                                    <td colspan='3' valign='top' >Thank you for choosing us for your data recovery needs.<br>
                                                    Find attached your Invoice of ".$payment_type_text." payment for Transaction Id ".$Invoice->ServicePayment['payment_txnid']."</td>
                                                </tr>
                                                <tr>
                                                    <td align='left' valign='top' height='10'></td>
                                                </tr>
                                                </table></td>
                                            <td width='4%'></td>
                                            </tr>
                                            <tr>
                                            <td align='left' valign='top' height='10' colspan='3'></td>
                                            </tr>
                                            <tr>
                                            <td width='4%'></td>
                                            <td align='left' valign='top' >Thank You<br>
                                                Team Stellar,<br>
                                                <a href='https://www.stellarinfo.co.in/' target='_blank' style='color:#222222;'>www.stellarinfo.co.in</a></td>
                                            <td width='4%'></td>
                                            </tr>
                                            <tr>
                                            <td height='20' colspan='3'></td>
                                            </tr>
                                            <tr>
                                            <td width='4%'></td>
                                            <td valign='middle' align='left'> Please do not reply to this email. </td>
                                            <td width='4%'></td>
                                            </tr>
                                            <tr>
                                            <td align='left' valign='top' colspan='3' height='10'></td>
                                            </tr>
                                        </table>
                                        <table cellpadding='15' cellspacing='0' width='100%' align='center' style='background-color:#e3e3e3;'>
                                            <tr>
                                            <td height='20'>&nbsp;</td>
                                            </tr>
                                        </table>
                                        <table cellpadding='0' cellspacing='0' width='600' align='center' style='background-color:#fff; border-radius:10px;'>
                                            <tr>
                                            <td height='20' colspan='4'>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td width='4%'></td>
                                            <td align='left' valign='top' width='46%'><table cellpadding='0' cellspacing='0' width='100%'>
                                                <tr>
                                                    <td valign='top' style=' color:#000000' height='35'>For any enquiries:</td>
                                                </tr>
                                                <tr>
                                                    <td valign='top'><strong>Call us:</strong> ".(!empty($Invoice->Branch['phone_no'])?$Invoice->Branch['phone_no'] :'1800-102-3232')."  </td>
                                                </tr >
                                                <tr>
                                                    <td valign='top'><strong>Email us:</strong> ".(!empty($Invoice->Branch['branch_mail'])?$Invoice->Branch['branch_mail'] :'enquiry@stellarinfo.com')."  </td>
                                                </tr>
                                                </table></td>
                                            <td align='center' valign='top' width='46%'><table cellpadding='0' cellspacing='0' width='100%' align='center'>
                                                <tr>
                                                    <td valign='top' style='color:#000000' align='center' height='35'>Connect with us</td>
                                                </tr>
                                                <tr>
                                                    <td valign='top' align='center'><a href='https://www.facebook.com/stellardata' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/fb-icon.gif' align='absmiddle' alt='Stellar Data Recovery on Facebook' title='Stellar Data Recovery on Facebook' /></a> &nbsp; <a href='https://twitter.com/India_Stellar' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/twitter-icon.gif' align='absmiddle' alt='Stellar Data Recovery on Twitter' title='Stellar Data Recovery on Twitter' /></a> &nbsp; <a href='https://plus.google.com/u/0/b/113553898099807681250/113553898099807681250/about' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/gplus-icon.gif' align='absmiddle' alt='Stellar Data Recovery on G+' title='Stellar Data Recovery on G+' /></a> &nbsp; <a href='https://in.linkedin.com/company/stellar-information-systems-ltd' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/linkedin-icon.gif' align='absmiddle' alt='Stellar Data Recovery on LinkedIn' title='Stellar Data Recovery on LinkedIn' /></a></td>
                                                </tr>
                                                </table></td>
                                            <td width='4%'></td>
                                            </tr>
                                            <tr>
                                            <td height='15' colspan='4'></td>
                                            </tr>
                                        </table>
                                        <table cellpadding='0' cellspacing='0' width='600' align='center' style='background-color:#e3e3e3;'>
                                            <tr>
                                            <td height='10'></td>
                                            </tr>
                                            <tr>
                                            <td valign='middle' align='left' style='color:#595959; font-size:12px; text-align:center;'>Stellar Information Technology Pvt. Ltd. Leaders in Hard Drive Data Recovery Software & Services</td>
                                            </tr>
                                            <tr>
                                            <td valign='middle' align='left' style='color:#595959; font-size:12px; text-align:center;'>&nbsp;</td>
                                            </tr>
                                            <tr>
                                            <td height='10'></td>
                                            </tr>
                                        </table></td>
                                    </tr>
                                    <tr>
                                        <td height='20'></td>
                                    </tr>
                                    </table>";
                    
                    $sendmail=(new self)->AttachedInvoiceMail($emailTo,$emailSubject,$emailMessage,storage_path('Invoice/'.$InvoicePdfName.'.pdf'));
                    return $sendmail;
            }
            
    }
	//Pay Now method
    public static function AddPayNowRequest($request){
        $BranchData = Branch::where('zoho_branch_id',$request->input('zoho_branch_id'))->first();
        //set state for other territory
        $state      = strip_tags($request->input('state'));
        // Find State code
        $stateData = State::where('state_name',$state)->first();
        if($stateData){
            $state_code = $stateData['state_code'];
        }else{
            $state_code = strip_tags($request->input('state_code'));
        }
        
        if(isset($BranchData['state_name']) && $state == '97'){
            $state      = $BranchData['state_name'];
            $state_code = $BranchData['state_code'];
        }

        $date = Carbon::now();
        // insert data in service_request table
            $ServiceRequest = New ServiceRequest();
            $ServiceRequest->firstname = strip_tags(($request->input('user_type')=='individual')?$request->input('individualname'):$request->input('companyname'));
            $ServiceRequest->email     = strip_tags($request->input('email'));
            $ServiceRequest->phone     = strip_tags($request->input('phone'));
            $ServiceRequest->address   = strip_tags($request->input('address'));
            $ServiceRequest->landmark  = strip_tags($request->input('landmark'));
            $ServiceRequest->city      = strip_tags(($request->input('city')=='Other')?$request->input('other_city'):$request->input('city'));
            $ServiceRequest->state     = strip_tags($state);
            $ServiceRequest->state_code= $state_code;
            $ServiceRequest->zipcode   = strip_tags($request->input('zipcode'));
            $ServiceRequest->gst_no    = strip_tags(($request->input('user_type')=='company')?$request->input('gst_no'):'');
            $ServiceRequest->sez       = ($request->input('sez')=='yes')?1:0;
            $ServiceRequest->plan_type = strip_tags($request->input('plan_type'));
            $ServiceRequest->deal_id   = strip_tags($request->input('deal_id'));
            $ServiceRequest->branch_id = strip_tags($BranchData['id']);
            $ServiceRequest->submit_timestamp = $date->format('Y-m-d H:i:s');
            $ServiceRequest->save();
        //create Random Digit
            $random_digit   = rand(0000,9999);
            $order_no       = 'SVON-'.$random_digit.$ServiceRequest->id;
        /// Update the order number
            $ServiceRequest->order_no  = $order_no;
            $ServiceRequest->save();
        // Insert service_payments table data
            $ServicePayment = New ServicePayment(); 
            $ServicePayment->zoho_payment_id = strip_tags($request->input('id'));
            $ServicePayment->request_id      = $ServiceRequest->id;
            $ServicePayment->payment_item    = strip_tags($request->input('media_type'));
            $ServicePayment->total_amount    = strip_tags($request->input('base_amount'));
            $ServicePayment->total_tax       = strip_tags($request->input('tax_amount'));
            $ServicePayment->tax_rate        = ($request->input('tax_applicable')=='No' && $request->input('sez')=='yes' && $request->input('gst_api_status')=='Active')?0:18;
            $ServicePayment->payment_amount  = strip_tags($request->input('final_amount'));
            $ServicePayment->payment_txnid   = strip_tags($request->input('txnid'));
            $ServicePayment->payment_status  = 'processed';
            $ServicePayment->payment_timestamp = $ServiceRequest->submit_timestamp;
            $ServicePayment->payment_coupon = '';
            $ServicePayment->payment_channel = strip_tags($request->input('payment_channel'));
            $ServicePayment->payment_type   =  strip_tags($request->input('payment_type'));
            $ServicePayment->payment_category = strip_tags($request->input('payment_category'));
            $ServicePayment->payment_mode     = $request->input('payment_mode');
            $ServicePayment->save();
        // Return Response
        return $ServiceRequest;
    }

    // Generate Hsash
    public static function generateHash($params, $salt){
        // "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt_key";
        $hashString = $params["key"] . "|" . $params["txnid"] . "|" . $params["amount"] . "|" . $params["productinfo"] . "|" . $params["firstname"] . "|" . $params["email"] . "|".$params["udf1"]."|".$params["udf2"]."|".$params["udf3"]."|".$params["udf4"]."|".$params["udf5"]."||||||" . $salt;
        // Generate the hash
        $hash = hash("sha512", $hashString);
        return $hash;
    }
    public static function getIndianCurrencyText($number){
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'One', 2 => 'Two',
                        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
                        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
                        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
                        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
                        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
                        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
                        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    
        $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
        while( $i < $digits_length ) {
                        $divider = ($i == 2) ? 10 : 100;
                        $number = floor($no % $divider);
                        $no = floor($no / $divider);
                        $i += $divider == 10 ? 1 : 2;
                        if ($number) {
                                        $plural = (($counter = count($str)) && $number > 9) ? '' : null;
                                        $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
                                        $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                        } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal) ? " and " . ($decimal < 21 ? $words[$decimal] : ($words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10])) . ' Paisa' : '';
        return ($Rupees ? $Rupees . ' ' : '') . $paise;
    }
    public function AttachedInvoiceMail($to,$subject,$msg,$document=null)
    {
    $mail = Mail::html($msg, function($message) use ($msg,$to, $subject,$document){
        if($document!=null)
        $message->attach($document);
        $message->from(env('MAIL_USERNAME'));
        $message->to(is_array($to)?$to:\explode(",",$to))->subject($subject);
        });
        return $mail;
    }
}