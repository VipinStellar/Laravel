<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\ServicePayment;
use App\Models\ServiceInvoice;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Media;
use Spipu\Html2Pdf\Html2Pdf;
use App\Models\MediaHistoty;
use \stdClass ;
use Carbon\Carbon;
use DB;
use PaymentProcess;
use App\Models\MediaDirectory;

class PDFController extends Controller
{
    
    public function generatePDF($id,$preview)
    {
        if($preview == 'view')
        {
            $Invoice = new ServiceInvoice();               
            $Invoice->ServiceRequest = ServiceRequest::where('id',$id)->first();
            if($Invoice->ServiceRequest != null && $Invoice->ServiceRequest !='')
            {
            $Invoice->ServicePayment = ServicePayment::where('request_id',$id)->first();
            $Invoice->Branch = Branch::find($Invoice->ServiceRequest->branch_id);
            $Invoice->Media = Media::find($Invoice->ServiceRequest->media_id);
            $Invoice->priceText = $this->getIndianCurrencyText($Invoice->ServicePayment->payment_amount);
            $Invoice->layout = null;     
            $Invoice['po_no'] = $Invoice->Media->po_number;
            $Invoice['po_date'] = date('Y-m-d');
            $submit_timestamp = Carbon::now()->format('Y-m-d H:i:s');
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
            
            $invoice_format = 'SI/'.$Invoice->Branch->branch_code.'/'.$financial_year_short.'/';
            $maxIdRow = ServiceInvoice::select(DB::raw('MAX(invoice_id) as invoice_id'))->where('branch', $Invoice->Branch->branch_name)->where('financial_year',$financial_year)->first();
            $maxId = (int)$maxIdRow->invoice_id + 1;
            $Invoice->created_on = $submit_timestamp;
            $invoice_no = str_pad($maxId,4,'0',STR_PAD_LEFT);
            $Invoice->invoice_no = $invoice_format.$invoice_no;
            if($Invoice->layout == 2 && !empty($Invoice->integrated_tax)){
                $Invoice->TaxPriceText = $this->getIndianCurrencyText($Invoice->integrated_tax);
            }else{
                $Invoice->TaxPriceText = '';
            }
            $Invoice->preview =  preg_replace('/\s+/', '', trim($preview));
            $base_amount=$Invoice->ServicePayment['total_amount'];
            $state_type = PaymentProcess::getStateType($Invoice->ServiceRequest['state_code']);
            /////////// Invoice Calculation ////////////
            if($Invoice->Media['tax_applicable'] == 0 && !empty($Invoice->ServiceRequest['gst_no']) && $Invoice->ServiceRequest['sez'] == 1){
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
            } else {
                if($Invoice->ServiceRequest['state_code'] == 'TEMPRORY-FOR-OTH-CESS' && $Invoice->ServiceRequest['state_code'] == $Invoice->Branch['state_code'] && empty($Invoice->ServiceRequest['gst_no']) ){
                    $tax_rate = $Invoice->ServicePayment['tax_rate'];
                    $gst_rate = round($tax_rate/2);
                    $cess_rate = 1;
                    
                    $sgst_tax = ($base_amount * $gst_rate) / 100;
                    $sgst_rtax = round($sgst_tax,2);
                    
                    $cgst_tax = ($base_amount * $gst_rate) / 100;
                    $cgst_rtax = round($cgst_tax,2);
                    
                    $cess_tax = ($base_amount * $cess_rate) / 100;
                    $cess_rtax = round($cess_tax,2);
                } else {
                    $tax_rate = $Invoice->ServicePayment['tax_rate'];
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
                
                if($Invoice->ServiceRequest['state_code'] == '97'){
                    $scode="SGST/CGST";
                    $ugst_rtax=0;
                    $igst_rtax=0;
                    $cess_rtax=0;
                    $layout = 0;
                }
                elseif($Invoice->ServiceRequest['state_code'] == 'TEMPRORY-FOR-OTH-CESS' && $Invoice->ServiceRequest['state_code'] == $Invoice->Branch['state_code'] && empty($Invoice->ServiceRequest['gst_no']) ){
                    $scode="SGST/CGST";
                    $ugst_rtax=0;
                    $igst_rtax=0;
                    $cess_rtax = $cess_rtax;
                    $layout = 0;
                } 
                else{
                    if(($state_type == "UT"|| $Invoice->Branch['state_type'] == "UT") && ($Invoice->ServiceRequest['state_code'] == $Invoice->Branch['state_code']) && ($Invoice->ServiceRequest['sez'] != 1 || (empty($Invoice->ServiceRequest['gst_no']) && $Invoice->ServiceRequest['sez'] == 1))){
                        $scode="UGST/CGST";
                        $sgst_rtax=0;
                        $igst_rtax=0;
                        $cess_rtax=0;
                        $layout = 0;
                    }
                    else if(($Invoice->ServiceRequest['state_code'] == $Invoice->Branch['state_code']) && ($state_type != "UT"|| $Invoice->Branch['state_type'] != "UT") && ($Invoice->ServiceRequest['sez'] != 1 || (empty($Invoice->ServiceRequest['gst_no']) && $Invoice->ServiceRequest['sez'] == 1)))
                    {
                        $scode="SGST/CGST";
                        $ugst_rtax=0;
                        $igst_rtax=0;
                        $cess_rtax=0;
                        $layout = 0;
                    }
                    else {
                        if(!empty($Invoice->ServiceRequest['gst_no']) && $Invoice->ServiceRequest['sez'] == 1){
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
            $Invoice->rounding_off = round(round($payment_amount) - $payment_amount,2);
            $Invoice->igst = $igst_rtax;
            $Invoice->cgst = $cgst_rtax;
            $Invoice->sgst = $sgst_rtax;
            $Invoice->ugst = $ugst_rtax;
            $Invoice->gst_cess = $cess_rtax;
            $Invoice->base_amount = $base_amount;
            $Invoice->final_amount = $Invoice->ServicePayment['payment_amount'];
            $data['result'] = $Invoice;           
            $html = view('invoice',$data);
            return $html;
        }else{
            abort(404, 'File not found!');
        }
        }
        elseif($preview == 'preview')
        {
            $file = storage_path('Invoice/') . $id . '.pdf';
            if (file_exists($file)) {

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];
        
                return response()->file($file, $headers);
            } else {
                abort(404, 'File not found!');
            }
        }
        else{
            abort(404, 'File not found!');
        }
    }

    protected function getIndianCurrencyText($number){
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

    public function viewPDF($invId,$reqId)
    {
        $Invoice =  ServiceInvoice::where('id',$invId)->where('request_id',$reqId)->first();  
        if($Invoice->irn_status == 0) 
        {
            abort(404, 'File not found!');
        }
        if($Invoice != null && $Invoice !='')
        {
        $Invoice->ServiceRequest = ServiceRequest::where('id',$Invoice->request_id)->first();
        $Invoice->ServicePayment = ServicePayment::where('request_id',$Invoice->request_id)->first();
        $Invoice->Branch = Branch::find($Invoice->ServiceRequest->branch_id);
        if($Invoice->ServiceRequest->media_id !=null && $Invoice->ServiceRequest->media_id !=0)
        $Invoice->Media  = Media::find($Invoice->ServiceRequest->media_id);
        else
        $Invoice->Media = new Media();        
        $Invoice->priceText = self::getIndianCurrencyText($Invoice->final_amount);
        if($Invoice->layout == 2 && !empty($Invoice->integrated_tax)){
            $Invoice->TaxPriceText = self::getIndianCurrencyText($Invoice->integrated_tax);
        }else{
            $Invoice->TaxPriceText = '';
        }
        $data['result'] = $Invoice;
        $certificate = 'file://'.base_path().'/public/cert/stellar2023.crt';
            $info = array(
                'Name' => 'Stellar Data Recovery',
                'Location' => 'India',
                'Reason' => 'Notify User',
                'ContactInfo' => 'https://www.stellarinfo.co.in',
            );
        $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', 0);	
       // $html2pdf->setDefaultFont('times');
       $html2pdf->addFont('opensans','','opensans.php');
        $html2pdf->setDefaultFont('opensans');	
        $html2pdf->pdf->setFontSubsetting(false);
        $html2pdf->pdf->SetCreator('');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->SetAuthor('Stellar Data Recovery');
        $html2pdf->pdf->SetTitle('Invoice Of Stellar Data Recovery');
        $html2pdf->pdf->SetSubject('');
        $html = view('invoice_print',$data);
        $html2pdf->writeHTML($html);
        $html2pdf->pdf->setPage(1);
        $html2pdf->pdf->setSignature($certificate, $certificate, 'stellar@321', '', 2, $info);
        $html2pdf->pdf->setSignatureAppearance(137, 8, 24, 10);  
        $html2pdf->clean();
        $InvoicePdfName = str_replace('/','-',$Invoice->invoice_no);
        $html2pdf->Output($InvoicePdfName.'.pdf', 'I');
        }
        else{
            abort(404, 'File not found!');
        }
    }

    public function printDocument($type,$id)
    {
        $media = Media::find($id);
        if($media != null && $media !='')
        {
            $media->branchData = Branch::find($media->branch_id);
            $media->ContactData = PaymentProcess::getCustomerDetails($media->id);
            $query = DB::table('media_history')->where('media_id', $media->id);
            $media->added_onShowDate = null;
            $data['result'] = $media;  
            if($type == 'print-mediain')
            {
                $HisMedia = $query->where('status','4')->limit(1)->orderBy('id', 'DESC')->first();
                if(!empty($HisMedia))
                $media->added_onShowDate =$HisMedia->added_on;
                $html =  view('media-in-print',$data);
            } 
            elseif($type == 'print-dataout')
            {                
                $HisMedia = $query->where('status','16')->limit(1)->orderBy('id', 'DESC')->first();
                $media->datOut = MediaDirectory::where('media_id', $media->id)->first();
                if(!empty($HisMedia))
                $media->added_onShowDate =$HisMedia->added_on;
                $html =  view('dataout-print',$data); 
            }
            elseif($type == 'print-mediaout')
            {
                 $HisMedia = $query->where('status','21')->limit(1)->orderBy('id', 'DESC')->first();
                 if(!empty($HisMedia))
                 $media->added_onShowDate =$HisMedia->added_on;
                 $html =  view('mediaout-print',$data);
            }
            elseif($type == 'print-dlacl')
            {
                $html =  view('dlacl-print',$data);
            }
            elseif($type == 'print-dlac')
            {
                $html =  view('dlac-print',$data);
            }
            elseif($type == 'print-jobcard')
            {
                $html =  view('jobcard-print',$data);
            }
            else 
            abort(404, 'File not found!');
            return $html;
        }
        else
        {
            abort(404, 'File not found!');
        }
    }
}