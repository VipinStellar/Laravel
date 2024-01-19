<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use App\Models\ServicePayment;
use PaymentProcess;

class PaymentController extends Controller
{

    public function index($id){
        $data = array();
        if(isset($id) && (!empty($id))){
            $data['id']     = $id;
            $data['action'] = url('payments/proceed');
        }
        $data['result'] = $data;
        return view('payment',$data);
    }
    
    public function paymentsProceed(Request $request){
        if($request->isMethod('post') && ($request->input('id')) && ($request->input('proceed_pay') =='proceed') && ($request->input('agree') =='on')){
            $enc_id = preg_replace('/\s+/', '', trim($request->input('id')));
            $id = $this->idDecodeAndEncode('decrypt',$enc_id);
            $request['media_id'] = $id;
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'phone'     => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'email'     => 'required|email',
                'address'   => 'required|string',
                'pincode'   => 'required',
                'state_code'=> 'required',
                'branch_id' => 'required',
                'plan_type' => 'required',
                'payment_channel'=>'required',
                'media_type'=> 'required'
            ]);

            if($validator->fails()){
                $errors = $validator->errors();
                return redirect()->back()->withErrors($errors);
            }
            $plan_type = $request->input('plan_type');
            $pay_now   = $request->input('pay_now');
            $plan_details = array();
            if($plan_type == "Standard" && session()->has('plan_standard')){
                $plan_details = session('plan_standard');
            }
            else if($plan_type == "Economy" && session()->has('plan_economy')){
                $plan_details = session('plan_economy');
            }
            else if($plan_type == "Priority" && session()->has('plan_priority')){
                $plan_details = session('plan_priority');
            }else{
                $plan_details = array();
            }
            
          if($plan_details && count($plan_details) > 0 && ($plan_details['plan_type'] == $plan_type)){
            $request['plan_id'] = 1;
            $request['base_amount'] = 0;
            $request['total_tax'] = 0;
            $request['tax_rate'] = 0;
            $request['payment_amount'] = 0;
            if($pay_now == 'ADVC' && $plan_details['advance_amount_type'] == $pay_now ){
                $request['plan_id']       = $plan_details['plan_id'];
                $request['base_amount']   = $plan_details['advance_amount'];
                $request['total_tax']     = $plan_details['advance_tax'];
                $request['tax_rate']      = $plan_details['tax_rate'];
                $request['payment_amount']= $plan_details['advance_total'];
            }else if($pay_now == 'RECV' && $plan_details['full_amount_type'] == $pay_now){
                $request['plan_id']       = $plan_details['plan_id'];
                $request['base_amount']   = $plan_details['subtotal_amount'];
                $request['total_tax']     = $plan_details['total_tax'];
                $request['tax_rate']      = $plan_details['tax_rate'];
                $request['payment_amount']= $plan_details['final_amount'];
            } else if($pay_now == 'RECV2' && $plan_details['full_amount_type'] == $pay_now){
                $request['plan_id']       = $plan_details['plan_id'];
                $request['base_amount']   = $plan_details['subtotal_amount'];
                $request['total_tax']     = $plan_details['total_tax'];
                $request['tax_rate']      = $plan_details['tax_rate'];
                $request['payment_amount']= $plan_details['final_amount'];
            }

            //create transaction id
            $txnid       = substr(hash('sha256', mt_rand() . microtime()), 0, 22);
            $request['txnid'] = $txnid;
            $request['payment_mode']= 'Payu';

            $addPayment = PaymentProcess::AddPaymentRequest($request);
            if($addPayment!='' && $addPayment['id'] !=''){
            // Create a map of parameters to pass to the PayU API
                $params = array(
                    "key"       => env('PAYU_MERCHANT_KEY'),
                    "txnid"     => $txnid,
                    "amount"    =>  $request->input('payment_amount'),
                    "productinfo"=> $request->input('media_type'),
                    "firstname"  => $request->input('firstname'),
                    "email"      => $request->input('email'),
                    "phone"      => $request->input('phone'),
                    "surl"       => url('payment/status/success'),
                    "furl"       => url('payment/status/failure'),
                    "udf1"       => $id,
                    "udf2"       => $addPayment['order_no'],
                    "udf3"       => $plan_type,
                    "udf4"       => $request->input('plan_id'),
                    "udf5"       => $request->input('tax_applicable')
                );
                // Generate the hash
                    $hash = $this->generateHash($params, env('PAYU_SALT_KEY'));
                // Add the hash to the parameter map
                    $params["hash"] = $hash;
                // Build the URL for the PayU API request
                    $params["action"] = env('PAYU_BASE_URL')."/_payment";
                // Output the URL for the PayU API request
                // echo $url;
                $params['result'] = $params;

                return view('payment-proceed',$params);
            }else{
                echo "<h1 class=\"hd-sml\">Error!!! Generating your request . Please <a href='".url('payment/'.$id)."'>Try again</a></h1>";
            }
          }else{
            echo "<h1 class=\"hd-sml\">Error!!! No Service details found. Please contact your related branch.</h1>";
          }   
        } else {
            echo "<h1 class=\"hd-sml\">Error!!! Something went wrong. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
        }
    }
    // Payment Success
    public function paymentSuccess(Request $request){
        if($request->isMethod('post') && ($request->input('status') == 'success') && $request->input('txnid')!=''){
            /// Varified Payment Status
            $verifiedStatus='';
            $command = "verify_payment";
            $v_hash_str = env('PAYU_MERCHANT_KEY') . '|' . $command . '|' . $request->input('txnid') . '|' . env('PAYU_SALT_KEY') ;
            $v_hash = strtolower(hash('sha512', $v_hash_str));
            $v_data = array('key' => env('PAYU_MERCHANT_KEY') , 'hash' =>$v_hash , 'var1' => $request->input('txnid'), 'command' => $command);
            $v_data= http_build_query($v_data);
            $PayuAPIUrl = env('PAYU_BASE_URL')."/merchant/postservice?form=2";
            // Curl Start
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $PayuAPIUrl);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, $v_data);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            $ch_response = curl_exec($c);
            if (curl_errno($c)) {
            $sad = curl_error($c);
            //throw new Exception($sad);
            }else{
                $ch_response = json_decode($ch_response, true);	
            }
            if($ch_response && $ch_response['transaction_details']){
                foreach($ch_response['transaction_details'] as $transactionId => $transactionData){
                    if($transactionId == $request->input('txnid')){
                        $verifiedStatus = $transactionData['status'];
                    }
                }
            }else{
                    $verifiedStatus = '';
            }
           
            if($ch_response && ($ch_response['status'] == 1) && $verifiedStatus =='success' && count($ch_response['transaction_details']) > 0){
                
                $data = array();
                // Verify Payment Data in ServicePayment
                $VerifiedPayment = ServicePayment::join('service_request', 'service_request.id', '=', 'service_payments.request_id')
                                ->select('service_request.*','service_payments.id as payment_id','service_payments.total_amount','service_payments.total_tax','service_payments.tax_rate','service_payments.payment_amount','service_payments.payment_type','service_payments.payment_status','service_payments.payment_txnid','service_payments.existing_payment','service_payments.payment_mode','service_payments.payment_timestamp')
                                ->where('service_payments.payment_txnid', $request->input('txnid'))
                                ->where('service_request.media_id', $request->input('udf1'))
                                ->orderBy('service_payments.id','desc')->first();
                
                if($VerifiedPayment && $VerifiedPayment['payment_status'] != 'success'){
                    $VerifiedPayment['plan_id']        = $request->input('udf4');
                    $VerifiedPayment['tax_applicable'] = $request->input('udf5');
                    $VerifiedPayment['amount'] = $request->input('amount');
                    // Branch Data
                    $BranchData = PaymentProcess::GetBranchDetails($VerifiedPayment['branch_id']);
                    $VerifiedPayment['branch'] = $BranchData['branch_name'];
                    $VerifiedPayment['po_number'] = '';
                    //Generate ServicePayment
                    $ServicePayment =  ServicePayment::find($VerifiedPayment['payment_id']);
                    $ServicePayment->payment_status = $request->input('status');
                    $ServicePayment->payment_timestamp = Carbon::now()->format('Y-m-d H:i:s');
                    if($ServicePayment->save()){
                        $ServiceInvoice = PaymentProcess::GenrateInvoice($VerifiedPayment);
                        if($ServiceInvoice && $ServiceInvoice['id'] != ''){
                         $payment_type_mail = ($VerifiedPayment['payment_type']=='ADVC')? 'Advance Data Recovery Fee':'Data Recovery Fee';
                            //Send Mail
                            $MailHtml = "<style type='text/css'>
                            @import url(http://fonts.googleapis.com/css?family=Arial, Helvetica, sans-serif);
                            body , u , b{
                                margin:0px;
                                padding:0px;
                                font-family:Arial, Helvetica, sans-serif;
                                font-size:14px;
                                line-height:25px;
                                color:#222222;
                            }
                            a img {
                                border:none;
                                outline:none;
                            }
                            img {
                                border:none;
                                outline:none
                            }
                            a {
                                text-decoration:none;
                                color:#595959;
                                outline:none;
                            }
                            a, a:link, a:visited {
                                text-decoration: none !important;
                                color: #000
                            }
                            p {
                                margin:0px;
                                padding:0px;
                            }
                            img, tr, td {
                                margin:0px;
                                padding:0px;
                                border-collapse:collapse;
                            }
                            table a { text-decoration:none; }
                            
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
                                      <td align='left' valign='top' width='92%' style='color:#000000; font-size:16px; padding-top:10px;'>Dear ".$VerifiedPayment['firstname'].",</td>
                                      <td width='4%'></td>
                                    </tr>
                                    <tr>
                                      <td align='left' valign='top' height='20' style='background-color:#fff;' colspan='3'></td>
                                    </tr>
                                    <tr>
                                      <td width='4%'></td>
                                      <td align='left' valign='top' width='92%' ><table cellpadding='0' cellspacing='0' width='100%'>
                                          <tr>
                                            <td colspan='3' valign='top' >Thank you - We\'ve received your payment. </td>
                                           
                                          </tr>
                                          <tr>
                                            <td align='left' valign='top' height='10'></td>
                                          </tr>
                                         <tr>
                                            <td colspan='3' valign='top' >Your transaction status is <strong>".$request->input('status')."</strong></td>
                                         </tr>
                                          <tr>
                                            <td align='left' valign='top' height='10'></td>
                                          </tr>
                                         <tr>
                                            <td colspan='3' valign='top' >Here  are your Order details::</td>
                                           
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
                                      <td align='left' valign='top' width='92%'><table cellpadding='0' cellspacing='0' width='100%' style='border-collapse:collapse'>
                                          <tr>
                                            <td valign='middle' width='50%' height='60' style=' border:1px solid #e3e3e3; padding-left:6%;'>Order no </td>
                                            <td valign='middle' width='50%' height='60' style=' border:1px solid #e3e3e3; padding-left:6%; color:#000000;'><strong>".$request->input('udf2')."</strong></td>
                                          </tr>
                                          <tr>
                                          
                                            <td valign='middle' width='50%' height='60' style=' border:1px solid #e3e3e3; padding-left:6%;'> Transaction ID </td>
                                            <td valign='middle' width='50%' height='60' style=' border:1px solid #e3e3e3; padding-left:6%; color:#000000;'><strong>".$request->input('txnid')."</strong></td>
                                          </tr>
                                          <tr>
                                          
                                            <td valign='middle' width='50%' height='60' style=' border:1px solid #e3e3e3; padding-left:6%;'> ".$payment_type_mail."</td>
                                            <td valign='middle' width='50%' height='60' style=' border:1px solid #e3e3e3; padding-left:6%; color:#000000;'><strong>Rs ".number_format($request->input('amount'))."</strong></td>
                                          </tr>
                                          
                                        </table></td>
                                      <td width='4%'></td>
                                    </tr>
                                    <tr>
                                      <td align='left' valign='top' colspan='3' height='10'></td>
                                    </tr>
                                    <tr>
                                        <td valign='top'>&nbsp;</td>
                                        <td valign='top' style='line-height:20px'>Your invoice for this transaction has been sent on your provided email.</td>
                                        <td width='4%'></td>
                                    </tr>
                                    <tr>
                                      <td align='left' valign='top' colspan='3' height='30'></td>
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
                                      <td align='left' valign='top' ><span style='color:#ff0000;'>Special Offer</span> - Now you can buy Western Digital <u>1 TB Hard disk</u> with <u>2 Year Data Care plan</u>  from Stellar at a discounted rate. <a target='_blank' href='https://www.stellarinfo.co.in/mailers/payment/?utm_source=Payment-Mail'><b style='color:#ff0000;'>Buy Now</b> </a></td>
                                      <td width='4%'></td>
                                    </tr>
                                    <tr>
                                      <td height='20' colspan='3'></td>
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
                                            <td valign='top' style=' color:#000000' height='35'>For any enquiries related to your order:</td>
                                          </tr>
                                          <tr>
                                            <td valign='top'><strong>Call us:</strong> ".$BranchData['phone_no']." </td>
                                          </tr
                                          ><tr>
                                            <td valign='top'><strong>Email us:</strong> ".$BranchData['branch_mail']." </td>
                                          </tr>
                                        </table></td>
                                      <td align='center' valign='top' width='46%'><table cellpadding='0' cellspacing='0' width='100%' align='center'>
                                          <tr>
                                            <td valign='top' style='color:#000000' align='center' height='35'>Connect with us</td>
                                          </tr>
                                          <tr>
                                            <td valign='top' align='center'><a href='https://www.facebook.com/stellardata' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/fb-icon.gif' align='absmiddle' alt='Stellar Data Recovery on Facebook' title='Stellar Data Recovery on Facebook' /></a> &nbsp; <a href='https://twitter.com/India_Stellar' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/twitter-icon.gif' align='absmiddle' alt='Stellar Data Recovery on Twitter' title='Stellar Data Recovery on Twitter' /></a> &nbsp; <a href='https://plus.google.com/u/0/b/113553898099807681250/113553898099807681250/about' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/gplus-icon.gif' align='absmiddle' alt='Stellar Data Recovery on G+' title='Stellar Data Recovery on G+' /></a> &nbsp; <a href='https://in.linkedin.com/company/stellar-information-systems-ltd' target='_blank'><img src='https://www.stellarinfo.co.in/images/auto-responder/linkedin-icon.png' align='absmiddle' alt='Stellar Data Recovery on LinkedIn' title='Stellar Data Recovery on LinkedIn' /></a></td>
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
                                      <td valign='middle' align='left' style='color:#595959; font-size:12px; text-align:center;'>D16, Sector-33,
                                        Infocity Phase II,
                                        Gurugram-122001</td>
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
                            
                            $subject = $payment_type_mail.' payment for Order no '.$request->input("udf2").' Payment Received';
                            $this->_sendMail($MailHtml,$subject,trim($VerifiedPayment['email']));

                            //Success data show
                            $data['payment_amount'] = $request->input('amount');
                            $data['order_no']       = $request->input('udf2');
                            $data['payment_txnid']  = $request->input('txnid');
                            $data['result'] = $data;
                            return view('payment-success',$data);
                        } else{
                            //Error in Invoice Generation
                            echo "<h1 class=\"hd-sml\">Error!!! Invoice Generation failed. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
                        }
                    }else {
                        // redirect for payment status not updated
                        echo "<h1 class=\"hd-sml\">Error!!! Transaction verification failed. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
                    } 
                } else{
                   echo "<h1 class=\"hd-sml\">Error!!! Transaction verification failed. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
                }
            } else {
                //check Api Response
                echo "<h1 class=\"hd-sml\">Error!!! Transaction verification failed. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
            } 
        }else {
            // Error - When Url access Directly 
            echo "<h1 class=\"hd-sml\">Error!!! Transaction verification failed. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
        }
    }
    public function paymentFailure(Request $request){
        if($request->isMethod('post') && ($request->input('status') == 'failure') && $request->input('txnid')!=''){
            $failure_payment_update =  ServicePayment::where('payment_txnid',$request->input('txnid'))->where('payment_status','!=','success')->update(['payment_status' => $request->input('status'),'payment_timestamp' => Carbon::now()->format('Y-m-d H:i:s')]);
           
            $data = array();
             if($failure_payment_update){
                 $data['status']       = $request->input('status');
                 $data['txnid']        = $request->input('txnid');
                 $data['err_msg']      = $request->input('field9');
                 $data['payment_link'] = url('payment/'.$this->idDecodeAndEncode('encrypt',$request->input('udf1')));
             }
             $data['result'] = $data;
             return view('payment-failure',$data);
         } else {
            echo "<h1 class=\"hd-sml\">Error!!! Transaction verification failed. Please contact <a href='https://www.stellarinfo.co.in/company/contact.php'>support</a></h1>";
        }
    }

    // Generate Hsash
    public function generateHash($params, $salt){
        // "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt_key";
        $hashString = $params["key"] . "|" . $params["txnid"] . "|" . $params["amount"] . "|" . $params["productinfo"] . "|" . $params["firstname"] . "|" . $params["email"] . "|".$params["udf1"]."|".$params["udf2"]."|".$params["udf3"]."|".$params["udf4"]."|".$params["udf5"]."||||||" . $salt;
        // Generate the hash
        $hash = hash("sha512", $hashString);
        return $hash;
    }

}
