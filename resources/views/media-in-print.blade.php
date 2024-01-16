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
  <h1 style="margin-top: 0px;"><u>Material In</u></h1>
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
      <p>
        <strong>{{ ($result->job_id)?$result->deal_id:$result->job_id; }}</strong>
      </p>
      <p style="margin-bottom: 0px;">
      <strong>Mr. / Ms  {{ $result->ContactData['name'] }}</strong><br>
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
      <p>Received Media from {{ $result->ContactData['name'] }} with following detail for recovery / handling / mirroring / Analysis.</p>
    </div>
  </div>
  <div class="divRow">
    <div class="divCell">
      <p><span style="float: left;"><strong>UserName : </strong> </span>  <span style="float: right;padding-right:120px;"><strong>GatePassNo : </strong></span></p>
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
        <strong><u> Receive following peripherals along with media : </u></strong><br>
        Thank you for choosing Stellar Information Technology Private Ltd. for your data recovery needs. At Stellar we
        assure you of accurate, timely & confidential recovery services. We would revert back to you with an
        Assessment / Quotation Report on {{ date('d-M-Y',strtotime($result->assessment_due_date)) }} before . 
     </p>
     <p>
        <strong>Please Note : <i>NO RECOVERY NO CHARGE POLICY </strong></i> is extended in case of only those files and folders mentioned by the customer in MAF Form.
        <strong>'ALL DATA' </strong> or  <strong> 'FULL DATA' </strong> or similar type of notification in MAF Form shall not be covered under this policy.
     </p>
     
    <p style="padding-top: 15px;">
      <strong>Stellar Information Technology Private Ltd</strong><br>
      <strong>(Signature & Seal)</strong><br>
      Terms & Conditions<br>
      Same as accepted and signed in MAF for more details please visit www.stellarinfo.com<br>
    </p>
    <table class="table" width="100%" style="padding-top: 5px;">
        <tbody>
          <tr>
            <td align="left" width="33%">Doc. No: SITPL/IMS/DB(OP)/FM-04</td>
            <td align="center" width="33%">Ver. No: 1.0</td>
            <td align="right" width="33%">Effective Date: 01/09/2017</td>
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