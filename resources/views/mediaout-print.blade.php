<style>
  #parent{
    font-family: 'Open Sans';
    font-size: 13px;
    line-height: 1.4;
    text-align: center;
    width: 750px;
    padding: 15px 10px 10px 20px;
    border: none;
    margin: 0px;
  }
  .fs{font-size: 14px;}
  .divRow {
    display: table-row;
    width: auto;
  }
  .divCell1 {
    float: left;
    display: table-column;
    width: 450px;
    text-align: left;
    padding-right: 21px;
  }
  .divCell {
    float: left;
    display: table-column;
    width: 700px;
    text-align: left;
  }

  .divCellimg {
    float: left;
    display: table-column;
    width: 250px;
    text-align: center;
  }
  .table{
    font-size: 14px;
    margin-top: 5px;
    margin-bottom: 5px;
  }
  p{
    margin: 5px 0px 5px 0px;
  }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<div id="parent">
  <h1 style="margin-top: 0px;"><u>Media Out</u></h1>
  <div class="divRow">
    <div class="divCell1">
      <p><strong>Stellar Information Technology Private Ltd</strong><br>
      {{$result->branchData->address}}<br>
      Tel : {{$result->branchData->phone_no}}<br>
      Email : {{$result->branchData->branch_mail}}</p>
    </div>
    <div class="divCellimg">
      <img src="{{ url('public/images/logo-colored.png') }}" border="0"/>
    </div>
  </div>
  <hr>

  <div class="divRow">
    <div class="divCell1">
      <p style="margin-bottom: 0px;"><strong>Mr. / Ms  {{ $result->ContactData['name'] }}</strong><br>
      {{ $result->ContactData['address'] }}, <br>
      {{ $result->ContactData['landmark'] }}<br>
       {{ $result->ContactData['city'] }}, {{ $result->ContactData['state'] }}, {{ $result->ContactData['pincode'] }} <br>
      Phone : {{ $result->ContactData['phone'] }}
    </p>
    </div>
    <div class="divCellimg">
      <p><strong>Date : {{ !empty($result->added_onShowDate)?date('d-M-Y', strtotime($result->added_onShowDate)):'' }} </strong></p>
    </div>
  </div>

  <div class="divRow">
    <div class="divCell">
      <p>We would like to thank you for choosing our services for your data recovery needs. It is our 
         commitment to provide you with best and reliable data recovery system.<br>
         To explore more about our products and services, Please refer to our company website www.stellarinfo.co.in. 
         Again, I thank you for choosing Stellar Information Technology Private Ltd. for your data recovery needs, we always
         look forward to serve you.We promise your satisfaction and value for money on every transaction made with us.</p>
    </div>
  </div>
  <div class="divRow">
    <div class="divCell">
      <p><span style="float:left;"><strong>UserName : </strong></span>  <span style="float:right;padding-right:120px;"><strong>GatePassNo : </strong></span></p>
    </div>
  </div>
  <div class="divRow">
    <div class="divCell">
      <table class="table" width="100%">
        <tr>
          <td colspan="3"><strong>Media Details :</strong></td>
        </tr>
        <tr>
          <td><strong>Media</strong></td>
          <td><strong>Serial No.</strong></td>
          <td><strong>Model No.</strong></td>
          <td><strong>Size</strong></td>
        </tr>
        <tr>
          <td>{{ $result->media_type }}</td>
          <td>{{ $result->media_serial }}</td>
          <td>{{ $result->media_model }}</td>
          <td>{{ $result->media_capacity }}</td>
        </tr>
      </table>
    </div>
  </div>

  <div class="divRow">
    <div class="divCell">
     <p>
      <strong> Job Id : {{ empty($result->job_id) ? $result->deal_id : $result->job_id }}<br>
        Material In Date : {{ !empty($result->added_onShowDate)?date('d-M-Y', strtotime($result->added_onShowDate)):'' }}<br> </strong>
     </p>
     <p>
        <strong><u> Receive following peripherals along with media : </u></strong><br>
        Signature on this receipt acknowledges that you have received crashed Hard Disk from Stellar Information Technology Private Ltd. Please sign and date the media out receipt.
        <br>
      <strong>Client Name : </strong> {{ $result->ContactData['name'] }} <span style="float:right;padding-right:120px;"><strong>Signature :</strong></span>
     </p>
    <p>
      <strong><u>Customer Feedback : </u></strong><br>
      We appreciate positive and critical comments from all our customers and guarantee to respond
      to all comments we receive. <br>
      1. Did the service deliver the result that were promised?<br>
      Less than Promised {} As expected {} More than expected {} Consistently more {}<br>
      2. Was there proper and timely communication for the details?<br>
      Less than Promised {} As expected {} More than expected {} Consistently more {}
      <br>
      <strong>Feedback in detail : </strong> ----------------------------------------------------------------------------------------------------------------------------------------
      ---------------------------------------------------------------------------------------------------------------------------------------------------------
    </p>
    <p>
      <strong>Note : </strong><br>
      <strong>*</strong> Please share your grievences at complaints@stellarinfo.com to help us serve you better. <br>
      <strong>*</strong> Terms & Conditions same as accepted and signed in MAF for more details please visit www.stellarinfo.co.in
    </p>
    <table class="table" width="100%" style="padding-top:5px;">
        <tbody>
          <tr>
            <td align="left" width="33%">Doc. No : SITPL/IMS/DB(OP)/FM-14</td>
            <td align="center" width="33%">Ver. No : 1.0</td>
            <td align="right" width="33%">Effective Date : 01/09/2017</td>
          </tr>
        </tbody>
      </table>
      <p><strong>Stellar Confidential</strong></p>
    </div>
  </div>
</div>
<div id="printOption" style="display:block">
  <a href="javascript:void();" onclick="document.getElementById('printOption').style.visibility = 'hidden'; window.print(); return true;">Print</a>
</div>