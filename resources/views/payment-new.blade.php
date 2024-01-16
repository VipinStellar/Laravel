<!doctype html>
<html lang="en">
    <head>
        @include('inc-meta-common')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Pay Media Device Analysis Fee Online - Stellar</title>
        <meta name="description" content="Stellar offers hassle free online payment facility. You can directly pay media analysis fees online over secure network." />
        <meta name="keywords" content="Pay analysis fee online, online payment of analysis fee, Stellar online payment, online analysis fee payment" />
        <!-- Bootstrap core CSS -->
        <link href="{{ url('public/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ url('public/css/custom.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ url('public/css/contact.css') }}" rel="stylesheet" type="text/css">
        <script type="text/javascript">
        performance.mark("scriptStart");
        WebFontConfig = {
        google: { families: [ 'Open Sans:400,600,700', 'Montserrat:500,600,700&display=swap' ] }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
        </script>
        <style>
        .loading::before { background-position: 50% 25%; width: 100%; height: 100%; top: 0; left: 0; background-color: rgba( 0, 0, 0, .8 ); z-index: 3;  backdrop-filter: blur(10px);}
        .table-borderless td, .table-borderless th { padding: 5px 10px;}
        .table-borderless tr > td:first-child, .table-borderless tr > th:first-child { padding-left:0px;}
        </style>
    </head>
<body>
    @include('header')
    <section class="py-5" id="form-error" style="display:none">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-9 col-sm-12 col-xs-4">
          <div class="alert alert-danger mx-auto py-2 fs14 mt-3 text-center" style="max-width:600px;"><strong id="main_msg"></strong> </div>
          </div>
        </div>
      </div>
    </section>
    <section class="pt-1 pb-4 job-form" id="job-form">
        <div class="container pt-4 pb-2">
          <div class="row justify-content-center">
            <div class="col-md-9 col-sm-12 col-xs-4 pb-3">
                <!-- <div class="alert alert-danger submission-alert mx-auto py-2 fs14 mt-2 mb-4 text-center alert-dismissible" style="max-width:500px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Transaction cancelled as page was refreshed. Please fill all required fields properly and try again.</strong> 
                </div> -->
                <!-- <div class="alert alert-danger submission-alert mx-auto py-2 fs14 mt-2 mb-4 text-center alert-dismissible" style="max-width:600px;"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Transaction cancelled. Please fill all required fields properly and try again.</strong> </div> -->
                <!-- <div class="clearfix"></div> -->
                <ol class="job-steps">
                  <li class="active" id="step1">
                    <div class="step">1</div>
                    <div class="caption">Service Type</div>
                  </li>
                  <li id="step2">
                      <div class="step">2</div>
                      <div class="caption">Verify Details</div>
                  </li>
                  <li id="step3">
                      <div class="step">3</div>
                      <div class="caption">Make Payment </div>
                  </li>
                </ol>
            </div>
          </div>
        </div>
        @if(isset($result) && $result['id'] !='')
        <form action="{{ $result['action'] }}" method="post" name="payuForm" id="payuForm" onSubmit="return validateProceedPayment();">
          @csrf  
         
        <div class="container">
          <div class="row mb-3 pt-2">
            <div class="col-md-12 text-center">
              <p class="fmon text-red mb-0 text-uppercase fs16" id="step-subtitle"><strong>Pay Your Data Recovery Fee Now</strong></p>
              <h3 class="section-title mb-0" id="step-title">Select Your Service Type</h3>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-sm-12 col-md-12 col-lg-11 col-xl-10 pb-4">
              <div class="row">
              <!-- For Step 1 Start-->
                <div class="col-sm-7 col-md-8 pr-xl-5 pl-md-5 pl-lg-3 order-2 order-sm-1" id="step-plan" data-title="Select Your Service Type">
                  @foreach($result['plan_types'] as $plan_type)
                   @if($plan_type['plan_type'] == 'Standard')
                    <div class="row service-type-box mx-0 mr-lg-3 mb-4 select-box" data-value="Standard" id="plan_standard" data-total-value="@json($plan_type)">
                      <div class="col-2 blue-bg d-flex align-items-center text-center" style="border-radius: 14px 0 0 14px;"> <img src="{{ url('public/images/job/standard-icon.png') }}" class="img-fluid mx-auto" alt="Standard Service"> </div>
                      <div class="col-10 p-3 pl-xl-4">
                        <h4><strong>Standard Service</strong></h4>
                        <ul class="bullet bullet-black">
                          <li>Standard data recovery service charges</li>
                          <li>Estimated data recovery time <span class="recovery-time">2-4 weeks</span> </li>
                          <li>Weekly Job Update</li>
                          <li>Support via Email and Phone</li>
                        </ul>
                      </div>
                    </div>
                    @endif
                    @if($plan_type['plan_type'] == 'Economy')
                      <div class="row service-type-box mx-0 mr-lg-3 mb-4 select-box" data-value="Economy" id="plan_economy">
                        <div class="col-2 slate-bg d-flex align-items-center text-center" style="border-radius: 14px 0 0 14px;"> <img src="{{ url('public/images/job/economy-icon.png') }}" class="img-fluid mx-auto" alt="Economy Service"> </div>
                        <div class="col-10 p-3 pl-xl-4">
                          <h4><strong>Economy Service</strong></h4>
                          <ul class="bullet bullet-black">
                            <li>Most economical data recovery service </li>
                            <li>Estimated data recovery time <span class="recovery-time">4-6 weeks</span> </li>
                            <li>Job Update on Job Completion</li>
                            <li>Support via Email and Phone</li>
                          </ul>
                        </div>
                      </div>
                    @endif
                    @if($plan_type['plan_type'] == 'Priority')
                    <div class="row service-type-box mx-0 mr-lg-3 mb-4 select-box" data-value="Priority" id="plan_express">
                      <div class="col-2 red-bg d-flex align-items-center text-center" style="border-radius: 14px 0 0 14px;"> <img src="{{ url('public/images/job/priority-icon.png') }}" class="img-fluid mx-auto" alt="Priority Service"> </div>
                      <div class="col-10 p-3 pl-xl-4">
                        <h4><strong>Priority Service</strong></h4>
                        <ul class="bullet bullet-black">
                          <li>Priority data recovery service charges </li>
                          <li>Estimated data recovery time <span class="recovery-time">1-2 weeks</span> </li>
                          <li>Daily Job Update</li>
                          <li>Support via Email and Phone</li>
                        </ul>
                      </div>
                    </div>
                    @endif
                  @endforeach
                  <div class="" id="verify_service" data-title="Your Service Fee Details">
                  <p class="fs16 fw600">Now pay your Data Recovery Fee online using hassle free fast & secure payment methods.</p>
                    <table class="table table-borderless table-sm table_verify_service fs14">
                      <thead>
                      </thead>
                      <tbody>
                        <tr class="data_row_job_id">
                          <td width="35%">Job ID</td>
                          <td width="5">:</td>
                          <td class="data_value"></td>
                        </tr>
                        <tr class="data_row_plan">
                          <td width="35%">Service Type</td>
                          <td width="5">:</td>
                          <td class="data_value"></td>
                        </tr>
                        <tr class="data_row_total_amt">
                          <td>Final Data Recovery Fee</td>
                          <td width="5">:</td>
                          <td class="data_value"></td>
                        </tr>
                        <tr class="data_row_paid_amt">
                          <td>Paid Amount</td>
                          <td width="5">:</td>
                          <td class="data_value"></td>
                        </tr>
                        <tr class="data_row_bal_amt">
                          <td>Balance Amount</td>
                          <td width="5">:</td>
                          <td class="data_value"></td>
                        </tr>
                        <tr>
                          <td colspan="3" class="fs13 pt-3">* All amount above are inclusive of all taxes.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="alert alert-danger details-alert py-2 fs14" style="display:none; max-width:500px;"></div>
                  <input type="hidden" name="plan_type" id="plan_type" value="<?php echo (empty($posted['plan_type'])) ? '' : $posted['plan_type']; ?>" />
                  <button type="button" id="GoToStepInfo" class="btn btn-primary pl-md-5 pr-md-5 pl-4 pr-4 fs16 mb-3 mt-3">Verify Details <i class="bi bi-arrow-right"></i></button>
                </div>
              <!-- For Step 1 End-->
              <!-- For Step 2 Start -->
                <div class="col-sm-7 col-md-8 mb-3 order-2 order-sm-1" id="step-info" data-title="Verify Your Details" style="display:none">
                    <div class="card bill-info">
                    <div class="card-header text-left">
                        <p class="mb-0 fmon fs16"> Your Billing Details</p>
                    </div>
                    <div class="card-body pr-xl-2">
                        <p class="fs14">Kindly verify your details for invoice purpose. Please contact respective branch to make any changes in your billing details before making payment. Your billing details can not be change after payment.</p>
                        <table class="table table-borderless table-sm table_verify_data fs14">
                        <thead>
                        </thead>
                        <tbody>
                            <tr class="data_row_name">
                              <td width="25%">Name</td>
                              <td width="5">:</td>
                              <td class="data_value">Raj Kumar Pal</td>
                            </tr>
                            <tr class="data_row_email">
                              <td>Email</td>
                              <td width="5">:</td>
                              <td class="data_value">palrajkumar999@gmail.com</td>
                            </tr>
                            <tr class="data_row_phone">
                              <td>Phone No.</td>
                              <td width="5">:</td>
                              <td class="data_value">8707319535</td>
                            </tr>
                            <tr class="data_row_address">
                              <td>Address</td>
                              <td width="5">:</td>
                              <td class="data_value">PLOT NO.A2 MIDC INDUSTRIAL AREA RANJANGAON</td>
                            </tr>
                            <tr class="data_row_city">
                              <td>City</td>
                              <td width="5">:</td>
                              <td class="data_value">New Delhi</td>
                            </tr>
                            <tr class="data_row_state">
                              <td>State</td>
                              <td width="5">:</td>
                              <td class="data_value">Delhi</td>
                            </tr>
                            <tr class="data_row_zipcode">
                              <td>Pincode</td>
                              <td width="5">:</td>
                              <td class="data_value">110034</td>
                            </tr>
                            <tr class="data_row_gst_no">
                              <td>GSTIN/UIN</td>
                              <td width="5">:</td>
                              <td class="data_value"></td>
                            </tr>
                            <tr class="data_row_jobid">
                              <td>Job ID </td>
                              <td width="5">:</td>
                              <td class="data_value">GGN/29</td>
                            </tr>
                        </tbody>
                        </table>
                        
                        <div class="form-group">
                        <div class="form-check for-validation">
                            <input class="form-check-input mt-2" type="checkbox" name="agree" id="agree">
                            <label class="form-check-label fs14 pt-1 fw600" for="agree"> I have read and agreed to Stellar <a href="javascript:void(0);" data-toggle="modal" data-target="#modal_terms">Terms and Conditions</a>. </label>
                        </div>
                        </div>
                        <div class="alert alert-danger details-alert py-2 fs14" style="display:none; max-width:500px;"></div>
                        <button type="submit" class="btn btn-primary pl-md-5 pr-md-5 pl-4 pr-4 mt-1 mb-2 fs16"  name="pay_now" value="pay_now">Proceed To Pay <i class="bi bi-arrow-right"></i></button>
                        <!-- <p><a href="javascript:void(0);" id="BackToStepPlan" class="text-muted"><strong><i class="bi bi-arrow-left"></i> Go Back</strong></a></p> -->
                     </div>
                    </div>
                </div>
                <!-- For Step 2 End-->
                <div class="col-sm-5 col-md-4 pl-sm-0 pl-md-3 pl-xl-4 mb-5 order-1 order-sm-2" id="preview-box">
                  <div class="sticky">
                    <div class="border dark-bg px-3 pt-4 pb-3 preview_fee">
                      <p class="h6"><span class="text_fee_type">Data Recovery Fee</span>: <span class="float-right"><em class="rupee">`</em> <strong class="subtotal_amount"></strong></span></p>
                      <p class="h6">GST @<span class="tax_rate">18</span>%: <span class="float-right"><em class="rupee">`</em> <strong class="tax_amount"></strong></span></p>
                      <hr>
                      <p class="h6"><strong>Total Amount:</strong> <strong class="float-right h4 fw600"><em class="rupee">`</em> <span class="final_amount"></span></strong></p>
                    </div>
                    <div class="border px-3 pt-3 pb-2  preview_fee">
                      <div class="form-group for-validation">
                        <p class="h6"><strong>I want to pay now:</strong></p>
                        <div class="custom-control custom-radio">
                          <input type="radio" name="pay_now" id="pay_now_advance" value="ADVC" class="custom-control-input">
                          <label class="custom-control-label" for="pay_now_advance"> <span class="text_advc_label">Advance Only</span> (<em class="rupee">`</em> <span class="advance_total"></span>)</label>
                        </div>
                        <div class="custom-control custom-radio">
                          <input type="radio" name="pay_now" id="pay_now_full" value="RECV" class="custom-control-input">
                          <label class="custom-control-label" for="pay_now_full"><span class="text_recv_label">Full Amount</span> (<em class="rupee">`</em> <span class="final_amount"></span>)</label>
                        </div>
                      </div>
                    </div>
                    <div class="border px-3 pt-4 pb-3 preview_payable" style="display:none">
                      <p class="h6"><strong>Amount Payable:</strong> <strong class="float-right h4 fw600"><em class="rupee">`</em> <span class="payable_amount"></span></strong></p>
                    </div>
                    <div class="border dark-bg px-3 pt-3 pb-2 preview_conditions">
                      <p class="h6 mb-2"><strong>Your Selection:</strong></p>
                      <ul class="bullet bullet-black">
                        <li class="preview_plan_name">Service Type : <strong>Standard Service</strong> </li>
                        <li class="preview_pay_now">Pay Now : <strong>Advance Only</strong></li>
                      </ul>
                    </div>
                    
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </form>
      @else
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-9 col-sm-12 col-xs-12 mt-4">
                <p class="fs16 text-center"><strong>Your Payment Details was Not Found!</strong></p>
            </div>
          </div>
        </div>    
      @endif
</section>
<section class="grey-bg">
  <div class="container">
    @include('why-stellar')
  </div>
</section>
<!--Popup TnC-->
<div class="modal fade custom_modal" id="modal_terms" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Stellar Terms and Conditions</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body" style="height:350px; overflow-y: auto; margin-bottom:10px; font-size:12px; line-height:1.4;">
          <p><strong>PLEASE READ THIS SERVICE AGREEMENT CAREFULLY BEFORE SIGNING AS IT CONTAINS IMPORTANT RIGHTS AND OBLIGATIONS BETWEEN THE STELLAR AND CUSTOMER</strong></p>
<p>TERMS AND CONDITIONS FOR DATA RECOVERY AND DATA ERASURE</p>
<p>By ordering and/or availing services from Stellar Information Technology Pvt. Ltd., Customer agrees to the following terms and conditions:</p>
<p><strong>1. INTERPRETATION</strong></p>
<p>In these Terms the following definitions will apply:</p>
<p>1.1   <strong>"Customer"</strong> means a person, body or corporate who avails the services of Stellar including, without limitation, an individual, sole trader, partnership, limited company or public authority. All Stellar appointed channel partners are also included in the definition of Customer.</p>
<p>1.2  <strong>&ldquo;Confidential Information&rdquo;</strong> means all confidential information (however recorded or preserved) disclosed by either party to the other party in connection with the Services, including but not limited to any information that would be regarded as confidential by either party;</p>
<p>1.3   <strong>"Contract/Agreement"</strong> means a legally binding contract/agreement which will come into existence between Customer and Stellar, governed by the Terms contained herein.</p>
<p>1.4   <strong>"Data"</strong> means information created in electronic form of any description stored and transmitted in the form of electrical signals and recorded on magnetic, optical, flash or mechanical recording media but does not include the installed operating system files, application/software program files, default download folder files, data files under bad sectors , download history, internet surfing history installed on the media.</p>
<p>1.5   <strong>"Data Recovery"</strong> means the complete or partial data recovery/restoration of the specific files and folders only as are indicated by the client in the MAF to be completely or partially lost, damage, or deleted data from Media, to which damage has been caused by technical defects, human error or other causes. In the event the specific files and folders  are not indicated upfront in the MAF by the client, then the client will have to unconditionally accept the recovered data as presented by Stellar and will be liable to pay the complete recovery amount to Stellar.</p>
<p>1.6   <strong>"Service Charges"</strong> means the charges for the Services payable by Customer, as set out in the Inspection Report/quotation for the services.</p>
<p>1.7   <strong>&ldquo;Inspection&rdquo;</strong> means preliminary check-up of the customer storage media to determine the possibility of data recovery.</p>
<p>1.8   <strong>&ldquo;Inspection Charges&rdquo;</strong> means the charges payable by Customer to Stellar when Customer handsover the media to Stellar for Inspection.</p>
<p>1.9   <strong>&ldquo;Inspection Report&rdquo;</strong> means the formal report or quotation generated by Stellar and sent to the customer estimating the possibility of recovery of specified data as outlined in the MAF, with costs and time involved therein, along-with the validity period of this report. The eventual outcome may be different from what is estimated in the Inspection Report . Customer agrees not to hold Stellar liable for any deviation viz a viz the final recovery outcome and the data recovery estimation mentioned in the Inspection Report.</p>
<p>1.10   <strong>&ldquo;Media"</strong> means Customer&rsquo;s storage media on which data recovery has to be performed such as hard-drives, USB Flash drive, external hard drive,  laptop hard drive, computer hard drive, server hard drive, storage box hard drive, solid state drive, tape media or other electronic, magnetic or electro-mechanical storage devices.</p>
<p>1.11   <strong>"Job ID"</strong> means the unique identification number assigned by Stellar to the  media received from the customer to be used and quoted in all communication concerning the media.</p>
<p>1.12   <strong>&ldquo;Defects&rdquo;</strong> means and be classified into three categories viz.</p>
<p>1.12.1.1.1   Physical defects means the defect in the media on account of slow reading , bad sectors or due to  failure of one or more components.</p>
<p>1.12.1.1.2   Logical defects means the defect in the media arising out of deletion or corruption or overwriting of data, formatting, data restoration issue.</p>
<p>1.12.1.1.3   Logical-cum-Physical defects means the defects arising as a result of combination of Physical and Logical defect/s.</p>
<p>1.13   <strong>"Services"</strong> means the Data Recovery Services, the Data Erasure/Data Wiping Services , Degaussing service, Data Repair services , Data Migration services and/or Data Restoration Services etc.</p>
<p>1.14   <strong>"Website"</strong> means website of Stellar at https://www.stellarinfo.co.in, or such other website as Stellar uses to operate its business from time to time.</p>
<p>1.15   <strong>&ldquo;Additional Storage Media&rdquo;</strong> means working and empty storage media of capacity equal to or greater than to be provided by the Customer for the purpose of collecting the recovered data from Stellar</p>
<p>1.16   <strong>&ldquo;Spare Storage Media&rdquo;</strong> means new/working storage media of same/similar technical specification/configuration such as make, model, firmware, PCB number etc. to be provided by the customer to Stellar for the purpose of facilitating data recovery for their provided job from their storage media</p>
<p>1.17   <strong>&ldquo;Order&rdquo;</strong> means any written or oral request made by the Customer to Stellar to undertake Inspection or Data Recovery or any other service as the case maybe. Upon receipt of payment and necessary documents as per agreed terms mentioned in Inspection Report/Quotation, Stellar will proceed ahead for requisite service accordingly</p>
<p><strong>2. ORDER PROCESS</strong></p>
<p>2.1   Following an initial telephone consultation, or submission of an online form/ request for call back via our Website, or Chat, or sms, or e-mail, or through any social media platform, Customer will personally hand over their Media to Stellar for Stellar In-lab inspection at Stellar office ,or else utilize the services of 3rd party logistics provider to transport the media from the origin to Stellar office at the Customer&rsquo;s own risk and cost. The client can also request Stellar for media pick up from their premises, in such case Stellar will not be liable for any transit issue or loss or damage to client media what-so-ever it maybe. Upon receipt of Inspection charges /Analysis charges which are non-refundable and Order, Stellar will inspect Customer&rsquo;s Media and, as soon as reasonably possible, provide Customer with an inspection report. In the event it is found that the actual condition of media received by Stellar is different in any manner whatsoever from the information provided by customer as stated herein above, then Stellar retains the right to forfeit the inspection charges and at its own discretion to<br>
  (i)   Reject this media of customer for any required service<br>
  (ii)  Modify the inspection Charge as the case maybe<br>
  (iii) Modify terms of service<br>
  The decision of Stellar in this regard will be final and binding on the Customer . </p>
<p>2.2   In some cases to perform Inspection, Stellar may need permission for media tampering or certain inputs from Customer such as user names, passwords, access codes, encryption credentials and or specific hardware along-with their configuration information etc.. If Customer do not provide such permission and/or input within 7 days of the Stellar request, or if Customer provides incomplete or incorrect input, then Stellar will forfeit the inspection charges and will not be responsible for supplying the Inspection Report late or else not supplying the inspection report at all.<br>
  Inspection report or Quotation will be void in the event the customer takes back the media without getting the requisite services done from Stellar as mentioned in the Inspection Report. In event of subsequent return by customer at a  later date; this will then be treated as a fresh/new case by Stellar</p>
<p>2.3   In case of onsite inspection at customer site, upon such specific request made by the customer to Stellar ; upon receipt of Inspection Charges along-with receipt of charges for additional expenses such as travel and per diem expenses for on-site work, boarding and  lodging, shipping and insurance (both ways), and any other actual expenses during transit and/or during operation if any, for parts, storage media, and/or off-the-shelf software used to perform the Inspection . Stellar will inspect Customer&rsquo;s Media/its clone at Customer Site or else at Stellar In-Lab as the case maybe, and, as soon as reasonably possible, provide Customer with an inspection report;  setting out the scope of Services and applicable Service charges via e-mail or Statement of Work, which will typically include applicable Service Charges, estimated time required and percentage of recoverable data from the customer specified required data as outlined in MAF. In case the Customer has not specified the exact data/files/folders to be recovered as mentioned under the important files and folders section of the MDF , then the customer will be liable to make complete/ full payment of requisite service charges as outlined in the Inspection Report/Quotation and also to accept the data recovered by Stellar as final and binding.</p>
<p>2.4   Following receipt of our Inspection Report, Customer may at their option either: (i) accept and sign service request or statement of work or Provide PO to submit an order for Stellar Services ("Order") along with the fulfilment of the payment terms as specified in the Inspection report; or (ii) submit a request to Stellar to return their Media. Herein customer will need to collect the media from Stellar office premises failing which Stellar will dispatch the media at the cost and risk of the customer to the place assigned by customer, the delivery cost of which Customer agree to pay; or (iii) If Stellar does not receive an Order or request to return customer media within 60 (sixty) calendar days of the date of the Quotation, Stellar may consider the customer&rsquo;s media as abandoned and dispose of customer media or take other such actions as described in Section 12.11 regarding abandoned media. Of the above 60 day period, the first 30 days will be kept free of charge and thereafter Stellar will determine and levy a demurrage charge for keeping the media in safe and private condition within its office premises.</p>
<p>2.5   Stellar acceptance of customer Order will take place when Stellar sends Customer email confirmation of their acceptance, which will be subsequent to the fulfilment of payment terms as specified in the inspection report.</p>
<p><strong>3. OUR PERFORMANCE OF THE SERVICES</strong></p>
<p>3.1  In consideration of customer payment of the Service Charges, Stellar will provide the Services in accordance with these Terms and Conditions and with commercially reasonable care and skill. The time and cost specified in the Inspection Report is only an estimation and the same may vary and the customer will be updated accordingly by Stellar.</p>
<p>3.2   For some Services, Stellar may need certain information from Customer such as user names, passwords and/or access codes. If Customer do not provide this information within a reasonable time of our request, or if Customer provide incomplete or incorrect information, Stellar may make an additional charge of a reasonable sum to compensate us for any extra work that is required as a result. Stellar will not be responsible for supplying the Services late or not supplying any part of them if this is caused by Customer not giving us the requisite information Stellar need. <br>
  If correct data, accurate information and/or requirements necessary for execution of the agreement are not provided by the customer within 7 days of request made by Stellar and/or not provided in accordance with the agreement, or if Customer fails to meet its obligations in any other way; then Stellar has in any case the right to terminate or dissolve the agreement or to suspend execution of the agreement and Stellar has the right to charge the costs incurred at its usual rates without any obligation to provide the data.</p>
<p>3.3   Stellar reserves the right to suspend its Services to (i) deal with technical problems or make technical changes as and when required; (ii) update the Services to reflect changes in relevant laws and regulatory requirements; (iii) to accomodate changes to the Services as requested by Customer. Stellar may also suspend supply of the Services if Customer does not pay.</p>
<p><strong>4. OUR SERVICES</strong></p>
<p>4.1   Data Recovery Services.<br>
  Stellar will use commercially reasonable endeavours for :</p>
<p>4.1.1   Inspection :<br>
  Following an Order for inspection, examine/inspect the Media to determine:</p>
<p>4.1.1.1   Estimated Recovery Percentage from Required Data (as outlined by customer to Stellar in writing at the outset) accessible and/or recoverable from the media.</p>
<p>4.1.1.2   Identification of the type of damage to the Media;</p>
<p>4.1.2   Inspection Report: communicate the results of Inspection to Customer</p>
<p>4.1.3   Recovery Process : Following an Order for data recovery after the inspection report; retrieve, recover, replicate data and thereafter communicate the directory listing of recovered data to customer or invite customer directly for verification of recovered data. Following the confirmation of Directory Listing or verification of recovered data by customer, provide access to and/or return Data on Additional Storage media provided by customer to Stellar</p>
<p>4.2   Data Erasure and/or Data Degaussing Services</p>
<p>4.3   Data Migration and/or Data Conversion Services</p>
<p>4.4   Data Repair Services</p>
<p>4.5   Data Encryption Services</p>
<p>4.6   Other/Additional Services : Carry out any other related services duly agreed upon in writing subject to payment of additional charge.</p>
<p>4.7   Stellar provides at its discretion, taking into account the feasibility, the above services on onsite basis viz. Customer Site and/or on offsite basis viz. Remote Online and  Stellar In-Lab. On-Site services, if any, will be subject to additional charges.</p>
<p>4.8   Stellar after performance of any service/s mentioned herein above; will securely erase beyond recovery,  the customer data available with Stellar within 7 days upon receipt of Payment for that particular job without any intimation to the customer.</p>
<p>4.9   While Stellar uses approved original equipment manufacturer repairs, Stellar offers no guarantee that the Services will be consistent with any warranty offered by the original equipment manufacturer.  Our performance of the Services should, under no circumstances, be taken as a guarantee that the Services will be successful, that all or any of customer Data is recoverable or will be useable, that the Media will be capable of being used or that Stellar will achieve any other particular result.</p>
<p><strong>5. TERMINATION</strong></p>
<p>5.1   Stellar may terminate the Contract with immediate effect by giving written notice if Customer commits a material breach of any term of the Contract in which breach is irremediable or (if such breach is remediable) fails to remedy that breach within a period of 7 days after being notified in writing to do so or repeatedly breach these Terms.  A failure to pay a Service charges due to Stellar shall constitute a material breach.</p>
<p>5.2   It is acknowledged that Stellar may suspend performance of the Services in the event of non-payment of any Service charges by the Customer.</p>
<p>5.3   Following termination, Customer shall be responsible for all sums owing to Stellar which shall become payable immediately.</p>
<p><strong>6.   CUSTOMER ACKNOWLEDGEMENTS &amp; OBLIGATIONS</strong></p>
<p>6.1   Customer hereby acknowledge and warrant to Stellar that :</p>
<p>6.1.1   Customer is in need of services and hence is approaching / re-approaching on his own accord with a free mind without any pressure</p>
<p>6.1.2   Customer is legally capable of entering into binding contracts and has the full authority, power and capacity to agree to these Terms. If Customer, is a Business Customer, it has the appropriate legal authority to enter into the Contract as an agent of the entity which Customer represent;</p>
<p>6.1.3   Customer is legally permitted to grant access to the Data and/or any password, software, or codes required to perform the Services and all the information provided by Customer to Stellar in connection with the Order is true, accurate, complete and not misleading; </p>
<p>6.1.4   Customer is the owner of the Media and/or have the permission from the owner of the Media for Stellar to perform the Services;</p>
<p>6.1.5   Supply of the Media and/or Data for the services to Stellar will neither breach any obligations or rights of any third parties nor will breach any applicable law;</p>
<p>6.1.6   Media does not contain any material (including without limitation any Data) which may infringe the Intellectual Property Rights of any third party; </p>
<p>6.1.7   Media does not contain any material and/or information which will breach applicable law;</p>
<p>6.1.8   Media does not contain any Data that is subject to preservation requirements, whether due to litigation, bankruptcy proceedings, creditor&rsquo;s rights, or statutory or regulatory requirements;</p>
<p>6.1.9   Data inside the media submitted for Services does not have any commercial value nor it is readily saleable in the open market;</p>
<p>6.1.10   Customer shall not use any forms/receipts issued by Stellar, reports shared by Stellar and data recovered, migrated, degaussed or erased by it in any court of law for any legal proceedings as an evidence or for any other purpose to any law enforcement agency or court of law;</p>
<p>6.1.11   All the risks and liabilities on account of action of any eavesdropping, tapping or similar kind of activities done by the Customer, vests with him/her/them only and Stellar is in no way responsible or associated with it and the Customer agrees to indemnify Stellar fully for any action initiated against them</p>
<p>6.1.12   Customer agrees and acknowledges not to hold Stellar responsible and liable for any deviation vis a vis the Inspection Report and the final outcome.</p>
<p>6.1.13   Customer acknowledges to make available additional storage media to Stellar and also acknowledges that they will not raise any claim for lost data in the additional storage media supplied by them for their respective job to Stellar for copying of recovered data</p>
<p>6.1.14   Customer acknowledges that the Spare Storage Media supplied to Stellar will be consumed for the purpose of Data Recovery for their respective job and they will not make any claim of any sort to Stellar for the same and/or towards the cost of the same. Customer also acknowledges that the Spare Storage Media will not be received back in working condition and hence any data on the said media will be lost forever.</p>
<p>6.1.15   Customer acknowledges that the correct media and information/inputs has been submitted to Stellar on which requisite service has to be performed by Stellar</p>
<p>6.2   Stellar reserve the right to request documentary evidence of customer ownership or legal right to authorise the Services and to suspend or not commence the Services without receipt of such evidence.</p>
<p>6.3   Customer understands and acknowledge that his/her Media and/or Data may already be damaged prior to its receipt by Stellar, and that efforts to complete the Services by Stellar may result in the destruction of, or further damage to, customer Media and/or Data. Customer acknowledges that Stellar will not assume any responsibility for any damage of whatsoever nature that may occur to the Customer's device, media and/or data.</p>
<p>6.4   Customer hereby acknowledge that Stellar is only committing to making reasonable efforts with its existing technology and techniques but Stellar cannot promise or guarantee any particular results/outcomes.</p>
<p>6.5   Customer hereby acknowledges and accept that there could be partial recovery or no recovery outcome which may be different from the estimation made upfront in the Inspection Report  and customer will not hold Stellar liable for the same </p>
<p>6.6   Customer hereby acknowledge that it is not possible for Stellar to ascertain the true condition of the media when the same is collected / delivered to Stellar. Customer acknowledges and accepts that the determination by the Stellar of the condition of media (viz. Whether the same is damaged/non-damaged or tampered/non-tampered, or opened/unopened) after examining the same at the Stellar In-Lab shall be final and binding on the Customer.  Customer acknowledges and accepts that the determination of Stellar regarding the condition of media will not be challenged by them under any circumstance whatsoever.</p>
<p>6.7   Customer hereby acknowledges and accepts that the determination as to whether the media is encrypted or not by Stellar shall be final and binding on them.</p>
<p>6.8   Customer hereby acknowledges and accepts that the determination by Stellar on the accuracy of decryption credentials provided by the customer shall be final and binding and the same will not be challenged by them under any circumstance whatsoever.</p>
<p>6.9   Customer acknowledges and agrees to make the payment for the services (even when the same are required or not) provided by Stellar within stipulated time.</p>
<p>6.10   If the customer does not go ahead for the data recovery process and takes the media out, then the case is considered as closed. In case the Customer chooses to come back, then the media will be re-entered   as a fresh case and new inspection fees based on condition of media will be applicable and new quotation will be shared with the customer.</p>
<p>6.11   In case the customer knowingly or unknowingly, hands over wrong media / hard disk for recovery and later on after seeing directory listing realizes the same, then also the customer shall be bound to pay the entire invoice amount for Stellar services irrespective of whether or not he/she takes the data / hard disk / media as the case maybe.</p>
<p>6.12   Customer acknowledges that in order to protect customer confidentiality Stellar reserves all rights to destroy, dispose of and/or  junk the media  in the following situations :-</p>
<p>6.12.1   Customer has given the media for Inspection but not sharing the required correct inputs</p>
<p>6.12.2   Customer has given the media for Inspection and after getting the Inspection Report subsequent order/instruction is not issued to Stellar</p>
<p>6.12.3   Customer has placed order on Stellar for data recovery however not sharing the requisite correct inputs or has received the Directory List for same from Stellar but now Customer is not responding</p>
<p>6.12.4   Customer has taken the data after recovery however not collecting his/her original media from Stellar Office</p>
<p>6.13   Customer acknowledges that he is not relying on any descriptions, statements, specifications, or illustrations representing the Services.</p>
<p>6.14   Customer acknowledge that no employee of stellar is authorized to make any representation or warranty on behalf of stellar that is not in these terms.</p>
<p>6.15   Customer acknowledges that he is in need of data service and is approaching / re-approaching on his own accord of his own volition with a free mind without any pressure and influence.</p>
<p><strong>7.   PRICE AND PAYMENT</strong></p>
<p>7.1   The customer has to pay the Inspection Charges and Service Charges, as the case may be.</p>
<p>7.2   The price payable for the inspection is termed as Inspection Charges and price payable for services is termed as Service Charges which will be more specifically set out in the relevant Quotation/Inspection Report.</p>
<p>7.3   The Inspection Charges and Service charges shall be payable as specified in the inspection report/quotation prior to commencing of Services by Stellar.</p>
<p>7.4   In cases, where the Corporate Customer places a work order/purchase order with Stellar, Stellar will invoice such Corporate Customer for the Inspection Charges and Service Charges which shall be paid by the Corporate Customer within 15 calendar days of the date of the invoice. Order once placed on Stellar is  irrevocable. </p>
<p>7.5   Customer shall pay the entire amount in the mode, manner and terms  specified in Inspection Report/Quotation.</p>
<p>7.6   Customer will be responsible for and indemnify Stellar against all taxes &amp; levies as applicable  imposed with respect to these Terms and any Services provided hereunder, except for taxes based on the net income of Stellar.</p>
<p>7.7   Payment by any credit card or debit card is subject to authorisation by the card issuer. If such authorisation is refused to Stellar, Stellar will not be liable for any delay or non-delivery of the Services and the Order will be deemed to be cancelled.</p>
<p>7.8   If Customer fail to pay to Stellar any amount due under these Terms in accordance with the provisions of these Terms Stellar, may retain the Equipment and Data until Customer make full payment.  If Customer do not make full payment within 90 (ninety) calendar days of the due date, Stellar will notify Customer that Stellar consider the Equipment and Data to be abandoned.</p>
<p><strong>8. WARRANTY &amp; EXCLUSIONS.</strong></p>
<p>8.1   The services are provided "as is" without any warranties  what-so-ever.</p>
<p>8.2   Stellar do not warrant that the services provided will meet requirements of the Customer or that the services will be provided error free, securely, timely, and in an uninterrupted manner.</p>
<p>8.3   Stellar hereby expressly disclaim all warranties, whether express, implied, or statutory, including but not limited to any warranty of title, accuracy, merchantability, fitness for a particular purpose or non-infringement.</p>
<p>8.4   Stellar hereby expressly disclaim all warranties whether express, implied or otherwise related to subsequent use of recovered data in any software or hardware.</p>
<p>8.5   Stellar do not warrant to the authenticity of the data recovered and/or its correctness and/or fitness for use</p>
<p>8.5   Stellar do not warrant that the estimations provided in inspection report is final or that the same is not likely to change at the time of invoicing</p>
<p><strong>9. RESPONSIBILITY FOR LOSS OR DAMAGE/LIMITATION OF LIABILITY.</strong></p>
<p>9.1   Stellar do not accept responsibility for loss, damage, destruction or corruption of data, or media, or equipment whether physical or otherwise.</p>
<p>9.2   Stellar do not accept any responsibility for any loss or damage caused to customer media, data either in transit or prior to our receiving customer media, data, or other equipment; or in the course of our providing the services where such damage, destruction, corruption or invalidation arises from our performing the services in accordance with these terms.</p>
<p>9.3   Subject to the provisions of this clause, Stellar&rsquo;s total liability to the customer, whether in contract, tort (including negligence), for breach of statutory duty, or otherwise, arising under or in connection with a contract shall be limited to the value of the Service Charges paid by the Customer  for the particular job or the cost of the media, whichever is lesser.</p>
<p>9.4   Stellar shall not be liable to the customer, whether in contract, tort (including negligence), for breach of statutory duty, or otherwise, arising under or in connection with these terms or any contract for any indirect or consequential loss, pecuniary loss, loss of data, loss or damage during transit (including both inward logistics to stellar and outward logistics from stellar to customer), business interruption, loss of profits or loss of sales or business, or the procurement of substitute goods or services or the cost thereof even if the stellar has been advised of the possibility of such damages.</p>
<p>9.5   Use of couriers.  In collecting customer equipment and/or media prior to the commencement of the services, or in delivering the recovered data, media, original equipment, stellar outsource such service to courier companies.  By agreeing to stellar using them for the services, customer agree that any loss or damage to the media, equipment or data shall be expressly be subject to the terms and conditions provided by the applicable courier company, including limitations of liability and compensation limits.  Customer hereby waive all right to bring any claim against stellar for any loss or damage to data or media or equipment arising from negligence and/or breach of contract by the courier company beyond any compensation scheme set out by them.  Stellar is not responsible for loss, damage or theft of media and/or data while in transit irrespective of the situation of that customer has availed Stellar's free media/data pickup or drop service between stellar and customer location or any movement of media/data between Stellar's designated data recovery facilities and stellar customer service location. This limitation remains irrespective of the fact whether the media , the equipment and/or data is handled by Stellar's employees or an outsourced agency. </p>
<p><strong>10. INDEMNITY.</strong></p>
<p>10.1 Customer shall indemnify Stellar, its employees, its associates, its channel partners and consultants in full against and hold them harmless from all claims, costs, damages, losses, liabilities, expenses (including without limitation legal expenses) demands, settlements, and judgments awarded against or incurred or paid by them (collectively &ldquo;Losses&rdquo;) as a result of or in connection with any and all of customer acts, inactions and/or omissions connected with the Contract and these Terms.</p>
<p>10.2   All correspondence including inspection report/quotation are confidential and the same cannot be shared by the customer on any platform including but not limited to using the same for Legal purposes and the customer shall indemnify any loss or damages or costs incurred by Stellar on account of the breach of the afore-said by the customer.</p>
<p>10.3  Equitable Relief. It is agreed that money damages would not be a sufficient remedy for any breach of this Agreement by the customer or by its Representatives. Accordingly, Stellar shall be entitled to seek specific performance, injunctive relief, or any other forms of equitable relief as a remedy for any breach of this Agreement by the Customer or its  Representatives; provided however, that such remedy(ies) shall not be deemed to be the exclusive remedy(ies) for a breach of this Agreement, but shall be in addition to all other remedies available at law or equity.</p>
<p>10.4  Customer certifies to Stellar that it is the legal owner of, and/or has the right to be in possession of the device , media and/or data furnishing to Stellar for data recovery and its collection, processing and transfer of such device and/or media and/or data is in compliance with the data protection laws. The Customer/ is subject and customer will defend on his own expense, indemnify and hold Stellar harmless against any damages and expenses that may incur including attorney fees and pay any cost , damages attorney fees declared against Stellar resulting from customer breach of this section.</p>
<ul data-type="component-text">
</ul>
<p><strong>11.   CONFIDENTIAL INFORMATION</strong></p>
<p>11.1  Stellar may disclose Confidential Information, including customer Data, where so ever required by law, to cooperate with any law enforcement authorities, governmental agencies, or court orders requesting or directing such disclosure.</p>
<p>11.2  Stellar will use any information contained in the media only for the intended purpose and will otherwise keep such information disclosed by the Customer under this agreement in the strict confidence. Stellar will ensure reasonable measures to prevent unauthorized disclosure of Customer's data of the same degree as ensured by Stellar in protecting its own confidential information. </p>
<p>11.3  Stellar will not disclose this information to any person(s) except to the authorized representative/contact person of the Customer or as required by law. Stellar being global organization, Customer hereby agrees to the transfer of information and/or media to its other locations for the sole purpose of fulfilling the agreement.</p>
<p>11.4  Upon realization of service charges from Customer, Stellar will wipe customer data beyond recovery within 7 working days, which will release Stellar from all its obligations towards customer including that of data confidentiality.</p>
<p>11.5  Stellar will use the personal data of Customer which is provided to Stellar at the time of job/enquiry submission to supply the Services to Customer and to process the payment of the Customer for the Services irrespective of the fact whether the customer has put his/her phone on DND mode. Stellar may also use personal data of the Customer for marketing purposes until and unless it being opposed in writing by the customer not to do so or Stellar and Customer has agreed to do so. By agreeing to these Terms, Customer is also agreeing to the storage and use of it&rsquo;s personal data pursuant to the terms of our Privacy Policy, which is available at              Privacy Policy - Stellar Data Recovery (stellarinfo.co.in)</p>
<p>11.6  Stellar is authorized to place the name and logo of Customer or Customer's clients that have received Services from Stellar on the Stellar website and/or reference list</p>
<p><strong>12. OTHER IMPORTANT TERMS</strong></p>
<p>12.1  This Contract is between Customer and Stellar. No other person shall have any rights to enforce any of its terms.  Each of the paragraphs of these Terms operates separately. If any court or relevant authority decides that any of them are unlawful and/or unenforceable, the remaining paragraphs will remain in full force and effect.</p>
<p>12.2  If Stellar delay in taking steps against Customer in respect of  breaking this contract, this will not prevent Stellar taking steps against Customer at a later date.</p>
<p>12.3  Stellar may change the Services to reflect changes in relevant laws and regulatory requirements and to implement minor technical adjustments and improvements, for example to address a security threat. In addition, Stellar may make more material changes to these Terms or the Services.</p>
<p>12.4  This Agreement shall be governed by laws of state of Delhi, India.</p>
<p>12.5  All disputes between the parties relating to this agreement or the rights or obligations of the parties hereto or arising out of or in relation to this agreement, shall be referred to a Sole Arbitrator duly appointed by Stellar, for decision whose award shall be final and binding on both the parties in accordance with the provisions of Arbitration and Conciliation Act, 1996.<br>
  Subject to the Arbitration Clause, any dispute between the parties arising out of this Agreement shall be subject to the exclusive jurisdiction of competent courts in Delhi, India alone and each party irrevocably submits itself to the jurisdiction of such courts for all purposes. </p>
<p>12.6  Except for Customer&rsquo;s obligation to make payments, Stellar&rsquo;s  performance shall be excused to the extent performance is hindered, delayed or made impractical due to causes beyond it's reasonable control.</p>
<p>12.7  These Terms, together with any exhibits or other attachments provided by Stellar, constitutes the entire Agreement between the parties in relation to this subject matter, unless the parties have entered into a previously written master services agreement, in which case the master agreement/Compliance Agreement/ Non-Disclosure Agreement/ Confidentiality Agreement  shall govern with respect to any conflicting terms hereunder.  The terms and conditions of any Customer issued Purchase Order, or terms contained on the Website, are specifically excluded from and superseded by the terms and conditions contained in this Service Agreement.</p>
<p>12.8  The termination of these Terms for any reason shall not affect: a) except as set forth herein, the obligations of Customer to account for and pay to Stellar any amounts for which Customer is obligated by virtue of  transactions or events which occurred prior to the effective date of termination; or b) any other obligation or liability which either party has to the other under these Terms and which, by its nature, would reasonably be expected to survive termination, such as customer indemnification obligations.</p>
<p>12.9  Customer ceases the right of ownership of his/her media;</p>
<p>12.9.1   if the media is left unclaimed at Stellar premises for 30 days from the date of receipt of media for the inspection;</p>
<p>12.9.2   if the media is left unclaimed at Stellar premises for 60 days post the inspection report or from the date of listing whichever is later</p>
<p>12.10  Customer shall pay demurrage charges of Rs.200 per media per day after the 15 days from the date of receipt of media by Stellar in the event of unclaimed media as stated herein above.</p>
<p>12.11  Abandoned Media : following the cessation of ownership of the customer in the media, Stellar to ensure data confidentiality of the customer data for all the media which are not claimed by the customer irrespective of the recovery results the media would be sent to our Central Depository for physical destruction of the platter thus ensuring data confidentiality which in turn are send to E-waste zone periodically as a part of contribution to safer and green environment.</p>
<p>Customer confirms that s/he has read and understood terms and conditions set out in this agreement with Stellar and agrees to abide by it.</p>
	  </div>
      <div class="modal-footer justify-content-between"> <a class="btn btn-primary btn-sm" href="https://www.stellarinfo.co.in/pdf/Stellar_Terms_and_Conditions.pdf" target="_blank">Download PDF</a>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@include('footer')
<script>
  var price_quote_url = "{{ url('price-quote') }}";
</script>
<!-- <script defer src="{{ url('public/js/payments/pay-now.js?v=3') }}"></script> -->
<script>
  //  function validateProceedPayment(){
  //   if($('#agree').is(':checked') != true){
  //     $('.details-alert').html('You must agree with the terms and conditions.').show();
  //     return false;
  //   } else {
  //     $('.details-alert').addClass("alert-danger").removeClass("alert-success").hide().html('');
  //   }
  // }
  $(function(){
    var loadscript=new load_script();
    loadscript.countNumber();
   // morphPrice("<?php echo !empty($result['id']) ? $result['id'] : '' ?>","qid");
  });
</script>
<script>
$(window).on('load', function() {
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
});
</script>
<style>
    #modal_terms p { margin-bottom:7px;}
</style>
</body>
</html>
