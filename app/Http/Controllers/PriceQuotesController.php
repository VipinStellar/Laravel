<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use App\Models\MediaPrice;
use App\Models\Media;
use App\Models\FinalPrice;
use PaymentProcess;


class PriceQuotesController extends Controller
{
    //Price Quote
    public function index(Request $request){
       
        //get job id details from Zoho crm
        if ($request->input('request') == 'quote_data' && $request->input('id')){
            $data = array();
            $data['status'] = 'error';
            session()->forget('plan_standard');
            session()->forget('plan_economy');
            session()->forget('plan_priority');
            session()->forget('plan_selected');
            
            $id = preg_replace('/\s+/', '', trim($request->input('id')));
            $id = $this->idDecodeAndEncode('decrypt',$id);
            
        if((!empty($id))){
              
            $plan_types = array ();
            $plan_active ='';
            // check final Price Plan
            $check_final_plan = PaymentProcess::getMediaFinalPrice($id);

            if($check_final_plan){
               if(($check_final_plan['paid_amount'] == $check_final_plan['total_amount']) && $check_final_plan['balance_amount'] == 0){
                    $data['status'] = 'success';
                    $data['msg'] = 'No Balance amount found to be paid.';
                    return json_encode($data);
               }
               elseif($check_final_plan['paid_amount'] == 0 && ($check_final_plan['total_amount'] == $check_final_plan['balance_amount'])){
                    $single_plan = array();
                    $single_plan["plan_type"]       = $check_final_plan['plan_type'];
                    $single_plan["plan_id"]         = $check_final_plan['plan_id'];
                    $single_plan["plan_total_amount"]= $check_final_plan['total_amount'];
                    $single_plan["list_total"]      = $check_final_plan['base_amount'];
                    $single_plan["discount"]        = 0;
                    $single_plan["subtotal_amount"] = $single_plan["list_total"] - $single_plan["discount"];
                    //tax rate
                    $single_plan['tax_applicable']  = $check_final_plan['tax_applicable'];
                    if($single_plan['tax_applicable'] == 1){
                        $single_plan["tax_rate"]  = (isset($check_final_plan['media_tax_rate']) && $check_final_plan['media_tax_rate'] != '') ? (int)$check_final_plan['media_tax_rate'] : 18;
                    }else{
                        $single_plan["tax_rate"]  = 0;
                    }
                    $single_plan["total_tax"]       = round($single_plan["tax_rate"] * $single_plan["subtotal_amount"] / 100);
                    $single_plan["final_amount"]    = $single_plan["total_tax"] + $single_plan["subtotal_amount"];
                    $single_plan["full_amount_type"]= "RECV";
                    $single_plan['advance_percent'] = $check_final_plan['advance_percent'];
                    $single_plan['advance_amount']  = round(($single_plan["subtotal_amount"] * $single_plan['advance_percent'])/100);
                    $single_plan["advance_tax"]     = round($single_plan["tax_rate"] * $single_plan['advance_amount'] / 100);
                    $single_plan["advance_total"]   = $single_plan["advance_tax"] + $single_plan["advance_amount"];
                    $single_plan["advance_amount_type"] = "ADVC";
                    $single_plan["job_speed"]      = $check_final_plan['speed'];
                    $single_plan["job_support"]     = $check_final_plan['support'];
                    /// Check active plan
                    $plan_active =      $check_final_plan['plan_type'];
                    
                    // check which type of plan selected
                    if($single_plan["plan_type"] == "Standard"){
                        $single_plan["working_days"]    = !empty($check_final_plan['estimated_days']) ? $check_final_plan['estimated_days'] : '10';
                        session(["plan_standard" => $single_plan]);
                    }
                    else if($single_plan["plan_type"] == "Economy"){
                        // calculate Working Days for Economy
                        $single_plan["working_days"]    = !empty($check_final_plan['estimated_days']) ? $check_final_plan['estimated_days'] : '20';
                        session(["plan_economy" => $single_plan]);
                    }
                    else if($single_plan["plan_type"] == "Priority"){
                        // calculate Working Days for Priority
                        $single_plan["working_days"]    = !empty($check_final_plan['estimated_days']) ? $check_final_plan['estimated_days'] : '5';
                        session(["plan_priority" => $single_plan]);
                    }
                    
                    $plan_types[] = $single_plan;
               } elseif($check_final_plan['paid_amount'] > 0 && $check_final_plan['paid_amount'] < $check_final_plan['total_amount']) {
                    $single_plan = array();
                    $single_plan["plan_type"]   = $check_final_plan['plan_type'];
                    $single_plan["plan_id"]     = $check_final_plan['plan_id'];
                    $single_plan["plan_total_amount"] = $check_final_plan['total_amount'];
                    $single_plan['tax_applicable']  = $check_final_plan['tax_applicable'];
                    if($single_plan['tax_applicable'] == 1){
                        $single_plan["tax_rate"]        = (isset($check_final_plan['media_tax_rate']) && $check_final_plan['media_tax_rate'] != '') ? (int)$check_final_plan['media_tax_rate'] : 18;
                    }else{
                        $single_plan["tax_rate"]        = 0;
                    }
                    $single_plan["balance_amount"] = $check_final_plan['balance_amount'];
                    $single_plan["paid_amount"]  = $check_final_plan['paid_amount'];
                    $subtotal_amount = (($single_plan["balance_amount"] * 100)/(100 + $single_plan["tax_rate"]));
                    $single_plan["subtotal_amount"] = round($subtotal_amount);
                    $total_tax  = (($single_plan["tax_rate"] * $subtotal_amount) / 100);
                    $single_plan["total_tax"] = round($total_tax);
                    $single_plan["final_amount"] = ($single_plan["total_tax"] + $single_plan["subtotal_amount"]);
                    $single_plan["full_amount_type"] = "RECV2";
                    $plan_active    =   $check_final_plan['plan_type'];
                    if($single_plan["plan_type"] == "Standard"){
                        session(["plan_standard" => $single_plan]);
                    }
                    else if($single_plan["plan_type"] == "Economy"){
                        session(["plan_economy" => $single_plan]);
                    }
                    else if($single_plan["plan_type"] == "Priority"){
                        session(["plan_priority" => $single_plan]);
                    }
                    
                    $plan_types[] = $single_plan;
                    //echo "active Plan false condition";
               }
            }
            else{
              
            $quote_datas  = PaymentProcess::getMediaPriceList($id);
           
            if($quote_datas && count($quote_datas) > 0){
                foreach($quote_datas as $quote_data){
                    $single_plan = array();
                    $single_plan["plan_type"]       = $quote_data['plan_type'];
                    $single_plan["plan_id"]         =  $quote_data['plan_id'];
                    $single_plan["list_total"]      = $quote_data['total_fee'];
                    $single_plan["discount"]        = 0;
                    $single_plan["subtotal_amount"] = $single_plan["list_total"] - $single_plan["discount"];
                    $single_plan['tax_applicable']  = $quote_data['tax_applicable'];
                    if($single_plan['tax_applicable'] == 1){
                        $single_plan["tax_rate"]        = (isset($quote_data['media_tax_rate']) && $quote_data['media_tax_rate'] != '') ? (int)$quote_data['media_tax_rate'] : 18;
                    }else{
                        $single_plan["tax_rate"]        = 0;
                    }
                    $single_plan["total_tax"]       = round($single_plan["tax_rate"] * $single_plan["subtotal_amount"] / 100);
                    $single_plan["final_amount"]    = $single_plan["total_tax"] + $single_plan["subtotal_amount"];
                    $single_plan["plan_total_amount"] = $single_plan["final_amount"];
                    $single_plan["full_amount_type"]= "RECV";
                    $single_plan['advance_percent'] = $quote_data['advance_percent'];
                    $advance_amount     = (($single_plan["subtotal_amount"] * $single_plan['advance_percent'])/100);
                    $single_plan['advance_amount']  = round($advance_amount);
                    $single_plan["advance_tax"]     = round($single_plan["tax_rate"] * $advance_amount / 100);
                    $single_plan["advance_total"]   = $single_plan["advance_tax"] + $single_plan["advance_amount"];
                    $single_plan["advance_amount_type"] = "ADVC";
                    $single_plan["job_speed"]      = $quote_data['speed'];
                    $single_plan["job_support"]     = $quote_data['support'];
                    // check active plan
                    if($quote_data['selected_plan'] == 1){
                        $plan_active =$single_plan["plan_type"];
                    }
                    
                    // check which type of plan selected
                    if($single_plan["plan_type"] == "Standard"){
                        $single_plan["working_days"]    = !empty($quote_data['estimated_days']) ? $quote_data['estimated_days'] : '10';
                        session(["plan_standard" => $single_plan]);
                    }
                    else if($single_plan["plan_type"] == "Economy"){
                        // calculate Working Days for Economy
                        $single_plan["working_days"]    = !empty($quote_data['estimated_days']) ? $quote_data['estimated_days'] : '20';
                        session(["plan_economy" => $single_plan]);
                    }
                    else if($single_plan["plan_type"] == "Priority"){
                        // calculate Working Days for Priority
                        $single_plan["working_days"]    = !empty($quote_data['estimated_days']) ? $quote_data['estimated_days'] : '5';
                        session(["plan_priority" => $single_plan]);
                    }
                    
                    $plan_types[] = $single_plan;
                    }
                }
            }
           
            $data['plan_types'] = $plan_types;
            if($plan_active!=''){
                $data["plan_active"] = $plan_active;
            } else {
                if(session()->has('plan_standard') && !empty(session('plan_standard'))){
                    $data["plan_active"] = "Standard";
                }
                else {
                    if(session()->has('plan_economy') && !empty(session('plan_economy'))){
                    $data["plan_active"] = "Economy";
                    }
                    else if(session()->has('plan_priority') && !empty('plan_priority')){
                    $data["plan_active"] = "Priority";
                    }
                    else{
                    $data["plan_active"] ="";
                    }
                }
            }
            
            if(!empty($plan_types)){
                $plan_active_index = array_search($data["plan_active"], array_column($plan_types, 'plan_type'));
                $plan_selected = $plan_types[$plan_active_index];
                if($plan_selected['final_amount'] > 0 ){					
                    $data['plan_selected'] = $plan_selected;
                    $data['plan_types'] = $plan_types;
                    // Customer Contact Data
                    $contact_data     = PaymentProcess::getCustomerDetails($id);
                    if($contact_data){
                        $data['name'] = $contact_data['name'];
                        $data['email'] = $contact_data['email'];
                        $data['phone'] = $contact_data['phone'];
                        $data['address'] = $contact_data['address'];
                        $data['landmark'] = $contact_data['landmark'];
                        $data['city'] = $contact_data['city'];
                        $data['state'] = $contact_data['state'];
                        $data['state_code'] = $contact_data['state_code'];
                        $data['country']= $contact_data['country'];
                        $data['pincode'] = $contact_data['pincode'];
                        $data['branch_id'] = $contact_data['branch_id'];
                        $data['gst_no'] = $contact_data['gst_no'];
                        $data['tax_applicable'] = $contact_data['tax_applicable'];
                        $data['job_id'] = $contact_data['job_id'];
                        $data['deal_id'] = $contact_data['deal_id'];
                        $data['media_type'] = $contact_data['media_type'];
                        $data['branch'] = $this->_getBranchName($contact_data['branch_id']);
                        $data['status'] = 'success';
                        session(["plan_selected" => $plan_selected]);
                    }  
                }
            }
            else{
				$data['status'] = 'success';
				$data['msg'] = 'No Service fee details found. Please contact your related branch.';
			  }
            echo json_encode($data);
            
        }else{
            $data['msg'] = 'No Service fee details found. Please contact your related branch.';
            echo json_encode($data);
        }
    } elseif ($request->input('request') == 'plan_data' && $request->input('plan')){
            $data = array ();
            $data['status'] = 'error';
            $data["plan_selected"] = "";
            session(['plan_selected' => '']);
            $plan = trim($request->input('plan'));
            $plan = preg_replace('/\s+/', '', $plan);
            if(!empty($plan)){
              if($plan == "Standard"){
                $data["plan_selected"] = (session()->has('plan_standard')) ? session('plan_standard') : '';
              }
              else if($plan == "Economy"){
                $data["plan_selected"] = (session()->has('plan_economy')) ? session('plan_economy') : '';
              }
              else if($plan == "Priority"){
                $data["plan_selected"] = (session()->has('plan_priority')) ? session('plan_priority') : '';
              }
              $data['status'] = 'success';
              session(['plan_selected' => $data["plan_selected"]]);
            }
            sleep(1);
            echo json_encode($data);
        } 
    }
    
}
