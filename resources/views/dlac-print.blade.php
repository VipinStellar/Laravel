<style>
  #parent{
    font-family: 'Open Sans';
    font-size: 13px;
    line-height: 1.4;
    text-align: center;
    width: 750px;
    padding: 30px 10px 10px 25px;
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
    float: right;
    display: table-column;
    width: 250px;
    text-align: center;
  }
  .divFrom{
    width: 250px;
    height: 150px;
    border: 1px solid black;
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
  <div class="divRow">
    <div class="divCell1"></div>
    <div class="divCellimg">
      <div class="divFrom"><span style="float:left;padding:5px;"><strong>From : </strong></span></div>
    </div>
  </div>
  <div class="divRow" style="margin-top:10px;">
    <div class="divCell1">
      <p><strong>To :</strong></p>
      <p><strong>Stellar Information Technology Private Ltd</strong><br>
      {{$result->branchData->address}}<br>
      Tel : {{$result->branchData->phone_no}}<br>
      Email : {{$result->branchData->branch_mail}}</p>
    </div>
    <div class="divCellimg"></div>
  </div>
  <div class="divRow">
    <div class="divCell">
      <h3 style="text-align: center;">Data Acceptance & Credit Facility</h3>
    </div>
  </div>
  <div class="divRow">
    <div class="divCell">
      <p>We have sent a media for data recovery with following details :</p>
    </div>
  </div>
  <div class="divRow">
    <div class="divCell">
      <p><strong> Job Id : {{ empty($result->job_id) ? $result->deal_id : $result->job_id }}</strong></p>
      <p><strong>Media Details : </strong></p>
      <table class="table" width="100%">
        <tr>
          <td colspan="3"><strong>Media Type :</strong></td>
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
     <p style="padding-top:5px;"><strong>Data Acceptance Certification : </strong></p>
     <p style="padding-bottom:5px;"><strong>This is to certify that ................ (Company /Individual name) has verified the data and the data
      recovered by (Stellar) fully meets our expectation. Abiding Stellar terms &
      conditions, we also confirm that no rework or any additional recovery exercise is required on work order/PO
      number ............ </strong></p>
      <p><strong>Credit Agreement : </strong></p>
      <ol style="font-weight:bold;">
            <li>.................. presently owes Stellar the sum of ............., said sum being
                presently due on data delivery and payable on (date).</li>
            <li>In the event We/I fail to make any payments punctually on the agreed extended terms, Stellar
                shall have full rights to charge penalty as mentioned in MAF.</li>
            <li>This agreement shall be binding upon both the parties.</li>
     </ol>
     <table class="table" width="100%" style="padding-top:5px;">
        <tbody>
            <tr>
                <th align="left" width="50%">Creditor : ....................................................</th>
                <th align="left" width="50%">Debitor : .....................................................</th>
            </tr>
            <tr>
                <th align="left" width="50%">Company : ..................................................</th>
                <th align="left" width="50%">Company : ..................................................</th>
            </tr>
            <tr>
                <th align="left" width="50%">Signature : ..................................................</th>
                <th align="left" width="50%">Signature : ..................................................</th>
            </tr>
        </tbody>
      </table>
    <table class="table" width="100%" style="padding-top:5px;">
        <tbody>
          <tr>
            <td align="left" width="33%">Doc. No : SITPL/IMS/DB(OP)/FM-12</td>
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
    <button onclick="document.getElementById('printOption').style.visibility = 'hidden'; window.print(); return true;" style="cursor:pointer;margin-bottom:20px;border-radius:5px;padding:7px 15px 7px 15px;">Print</button>
</div>