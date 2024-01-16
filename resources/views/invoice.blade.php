      <style >
        .center { text-align: center; }
        strong { font-weight: bold; }
        table { margin:0; padding:0; border-collapse:collapse; font-size: 13px; color: #3a3a3a; font-family: 'Open Sans', arial, sans-serif;}
        th, td { border:1px solid #C6C4C4; vertical-align:top; }
        th { padding:3px 2px; line-height:1.2; }
        td { padding:2px; line-height:1.2; }
        .nobdr { border:none; }
        .desc-table th, .desc-table td { font-size: 12px }
      </style>
      <page backtop="5mm" backbottom="4mm" backleft="4mm" backright="4mm" style="<?php echo ($result['layout'] == 2)?'font-size:12px':'font-size:13px'; ?>">
        <table cellspacing="0" cellpadding="0" border="0" style="<?php echo ($result['preview']=='view')?'width:60%':'width:100%'; ?>">
          <tr>
            <td style="padding:8px;width:100%;border:none;">
              <table cellspacing="0" cellpadding="0" border="0" style="margin-bottom:15px;width:100%;">
                <tr>
                  <td valign="middle" class="nobdr" style="width:25%;">
                    <img src="http://www.stellardrs.com/TestCode/back/public/images/logo-colored.png" alt="Stellar Data Recovery" border="0" width="122"/>
                  </td>
                    <td valign="middle" align="center" class="nobdr" style="width:40%; font-weight:bold; font-size:22px;">TAX INVOICE</td>
                    <td valign="middle" class="nobdr" style="width:20%; padding:5px 0 0 8px;">
                    <img src="http://www.stellardrs.com/TestCode/back/public/images/signature.png" alt="Stellar Data Recovery"  width="92" >
                  </td>
                  <td valign="middle" align="center" class="nobdr" style="width:15%;font-size:15px;">Original for Recipient</td>
                </tr>
              </table>  
              <table cellspacing="0" cellpadding="0" style="<?php echo ($result['preview']=='view')?'margin-bottom:5px;':'margin-bottom:-5px;'; ?>width:100%;border-collapse:separate;">
                <tr>
                  <td class="nobdr" style="width: 65%;padding-right:25px;color:#8A8889;"><strong>Stellar Information Technology Private Ltd.</strong><br>GSTIN : {{ $result['Branch']['gst_no'] }} <br> State Code : {{ $result['Branch']['state_code'] }} <br> Address : {{ $result['Branch']['address'] }} <br> Tel : {{ $result['Branch']['phone_no'] }} <br> Email : {{ $result['Branch']['branch_mail'] }}</td>
                  <td class="nobdr" style="width: 35%;"><strong>Invoice No :</strong> {{ $result['invoice_no'] }} <br><strong>Issue Date :</strong> {{ date('d-M-Y',strtotime($result['created_on'])) }} <br> <strong>PO Number :</strong> {{ ($result['ServicePayment']['existing_payment']=='Credit')?$result['po_no']:'' }} <br> <strong>PO Date :</strong> {{ ($result['ServicePayment']['existing_payment']=='Credit' && $result['po_date'] !='')?date('d-M-Y',strtotime($result['po_date'])):'' }}</td>
                </tr>
              </table> 
              @if($result['irn_status'] == 1 && !empty($result['irn_code']))
                <table cellspacing="0" cellpadding="0" style="width:100%;margin-top:5px;margin-bottom:5px;border:none;">
                  <tr>
                    <td class="nobdr" style="width: 100%; text-align:center">IRN : {{ $result['irn_code'] }} </td>
                  </tr>
                </table>
              @endif
              <table cellspacing="0" cellpadding="0" style="width:100%;margin-bottom:10px;border-collapse:separate;">
                <tr>
                  <td class="nobdr" style="width: 100%;padding-bottom:12px; background:#ffffff; font-size:15px;" colspan="6"><strong>Customer Details :</strong></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>Billed To :</strong></td>
                  <td class="nobdr" style="width: 34%;"></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>Shipped To :</strong></td>
                  <td class="nobdr" style="width: 34%;"></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>Name :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['firstname']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>Name :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['firstname']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>GSTIN/UIN :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo (empty($result['ServiceRequest']['gst_no'])) ? 'NA' : $result['ServiceRequest']['gst_no']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>GSTIN/UIN :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo (empty($result['ServiceRequest']['gst_no'])) ? 'NA' :$result['ServiceRequest']['gst_no']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>Address :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo stripslashes($result['ServiceRequest']['address']); ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>Address :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo stripslashes($result['ServiceRequest']['address']); ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>City :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['city']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>City :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php  echo $result['ServiceRequest']['city'];?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>State/Place of supply :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['state'] ; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>State/Place of supply :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['state']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>State Code :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['state_code']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>State Code :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['state_code']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>Pin Code :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php  echo $result['ServiceRequest']['zipcode']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>Pin Code :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['zipcode']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>Email :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['email']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>Email :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['email']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <tr>
                  <td class="nobdr" style="width: 15%;"><strong>Phone :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php  echo $result['ServiceRequest']['phone'];  ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>Phone :</strong></td>
                  <td class="nobdr" style="width: 34%;"><?php echo $result['ServiceRequest']['phone']; ?></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr>
                <!-- <tr>
                  <td class="nobdr" style="width: 15%;"><strong>PAN No :</strong></td>
                  <td class="nobdr" style="width: 34%;"></td>
                  <td class="nobdr" style="width: 1%;"></td>
                  <td class="nobdr" style="width: 15%;"><strong>PAN No :</strong></td>
                  <td class="nobdr" style="width: 34%;"></td>
                  <td class="nobdr" style="width: 1%;"></td>
                </tr> -->
              </table>
              @if($result['layout']!=0 && $result['ServiceRequest']['sez'] == 1)
              <table cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-bottom:5px;">
                <tr>
                <td valign="top" class="nobdr" style="width:100%;text-align:center;">
                @if(!empty($result['arn_num']))
                  <strong>SUPPLY TO SEZ UNIT OR SEZ DEVELOPER FOR AUTHORISED OPERATIONS UNDER LUT WITHOUT PAYMENT OF  <br> INTEGRATED TAX (ARN : {{ $result['arn_num'] }})</strong>
                 @else
                 <strong>SUPPLY TO SEZ UNIT OR SEZ DEVELOPER FOR AUTHORISED OPERATIONS ON PAYMENT OF INTEGRATED TAX </strong>
                 @endif
                </td>
                </tr>
              </table>
             @endif
              <table cellspacing="0" cellpadding="0" border="0" class="desc-table" style="width:100%; margin-bottom:3px;">
                <tr>
                  <th align="center" style="width: 8%">Sr. No.</th>
                  <th align="left" style="width: 39%"><strong>Description</strong></th>
                  <th align="center" style="width: 13%">HSN / SAC</th>
                  <th style="width: 25%"></th>
                  <th align="center" style="width: 15%">Value</th>
                </tr>
                <tr>
                  <td align="center" style="width: 8%"><b>1</b></td>
                  <td style="width: 39%">
                   {{ ($result['Media']['job_id'] !='')?'Material In No : '.$result['Media']['job_id'] : 'Order No : '.$result['ServiceRequest']['order_no'] }}
                  </td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%"></td>
                  <td style="width: 15%"></td>
                </tr>
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%">{{ ($result['ServicePayment']['payment_type'] == 'ANLY') ? 'Analysis Charges' : 'Data Recovery Charges' }}</td>
                  <td style="width: 13%" align="center">{{ empty($result['hsn'])?'998399':$result['hsn'] }}</td>
                  <td style="width: 25%" align="right"></td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['base_amount'], 2, '.', '') }}</td>
                </tr>
              @if(!empty($result['igst']) && $result['igst'] != 0)
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">IGST @18%</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['igst'], 2, '.', '') }}</td>
                </tr>
              @endif
              @if(!empty($result['cgst']) && $result['cgst'] != 0)
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">CGST @9%</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['cgst'], 2, '.', '') }}</td>
                </tr>
              @endif
              @if(!empty($result['sgst']) && $result['sgst'] != 0)
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">SGST @9%</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['sgst'], 2, '.', '') }}</td>
                </tr>
              @endif
              @if(!empty($result['ugst']) && $result['ugst'] != 0)
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">UGST @9%</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['ugst'], 2, '.', '') }}</td>
                </tr>
              @endif
              @if(!empty($result['gst_cess']) && $result['gst_cess'] != 0)
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">Flood Cess @1%</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['gst_cess'], 2, '.', '') }}</td>
                </tr>
              @endif
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">Total</td>
                  <td style="width: 15%" align="right">{{ number_format((float)($result['base_amount'] + $result['gst_cess']+ $result['ugst']+$result['sgst']+$result['cgst']+$result['igst']), 2, '.', '') }}</td>
                </tr>
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">Rounding Off Value</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['rounding_off'], 2, '.', '') }}</td>
                </tr>
                <tr>
                  <td style="width: 8%"></td>
                  <td style="width: 39%"></td>
                  <td style="width: 13%"></td>
                  <td style="width: 25%" align="right">Grand Total (Rounding Off)</td>
                  <td style="width: 15%" align="right">{{ number_format((float)$result['final_amount'], 2, '.', '') }}</td>
                </tr>
              </table>
              <table cellspacing="0" cellpadding="0" border="0" class="desc-table" style="width:100%;">
                <tr>
                  <td style="width: 15%; padding:3px 5px;"><strong>Total in Words</strong></td>
                  <td style="width: 85%; padding:3px 5px">Rupees {{ $result['priceText'] }} Only</td>
                </tr>
              </table>
            @if($result['layout'] == 2)
              <table cellspacing="0" cellpadding="0" border="0" class="desc-table" style="width:100%; margin-top:3px; margin-bottom:3px;">
                <tr>
                  <th align="center" style="width:15%" rowspan="2">HSN / SAC</th>
                  <th align="left" style="width:25%" rowspan="2"><strong>Taxable Value </strong></th>
                  <th align="center" style="width:20%" colspan="2">Integrated Tax</th>
                  <th align="center" style="width:20%" rowspan="2">Total tax Amount</th>
                </tr>
                <tr>
                  <th align="left" style="width:20%">Rate </th>
                  <th align="left" style="width:20%">Amount</th>
                </tr>
                <tr>
                  <td align="center" style="width:15%"><strong>{{ empty($result['hsn'])?'998399':$result['hsn'] }}</strong></td>
                  <td align="left" style="width:25%">{{ number_format((float)$result['final_amount'], 2, '.', '') }}</td>
                  <td align="center" style="width:20%">18% </td>
                  <td align="right" style="width:20%">{{ number_format((float)$result['integrated_tax'], 2, '.', '') }}</td>
                  <td align="right" style="width:20%">{{ number_format((float)$result['integrated_tax'], 2, '.', '') }}</td>
                </tr>
                <tr>
                  <td align="center" style="width:15%"><strong>Total</strong></td>
                  <td align="left" style="width:25%">{{ number_format((float)$result['final_amount'], 2, '.', '') }}</td>
                  <td align="center" style="width:20%"></td>
                  <td align="right" style="width:20%">{{ number_format((float)$result['integrated_tax'], 2, '.', '') }}</td>
                  <td align="right" style="width:20%">{{ number_format((float)$result['integrated_tax'], 2, '.', '') }}</td>
                </tr>
              </table>
              
              <table cellspacing="0" cellpadding="0" border="0" class="desc-table" style="width:100%; background-color: #eee;">
                <tr>
                  <td style="width: 15%; padding:3px 5px;"><strong>Tax Amount (in Words)</strong></td>
                  <td style="width: 85%; padding:3px 5px">Rupees {{ $result['TaxPriceText'] }} Only</td>
                </tr>
              </table>
            @endif
              <table cellspacing="0" cellpadding="0" border="0" style="width: 100%; margin-top:10px;">
                <tr>
                  <td valign="top" class="nobdr" style="width: 48%;">
                  <strong>Amount of Tax Subject to Reverse Charge : NO</strong>
                  <br /><br />
                  <strong>E. & O.E.</strong></td>
                  <td valign="top" align="center" class="nobdr" style="width: 2%;"></td>
                  <td valign="top" align="right" class="nobdr" style="width: 50%;font-size: 15px;"><br></td>
                </tr>
              </table>
              <table cellspacing="0" cellpadding="0" border="0" style="width:100%;margin-top:10px;">
                <tr>
                  <td valign="top" class="nobdr" style="width: 65%;"><strong>Terms and Conditions</strong><br />
                  @if($result['ServicePayment']['payment_type'] == 'ANLY')
                      Same as accepted while processing analysis fee payment. <br />
                    @else
                      Same as accepted as signed in MAF <br />
                    @endif  
                    For more details please visit <a href="https://www.stellarinfo.co.in/pdf/Stellar_Terms_and_Conditions.pdf" target="_blank">www.stellarinfo.co.in</a><br/>
                   </td>
                  <td valign="top" align="center" class="nobdr" style="width:5%;"></td>
                  <td valign="top" class="nobdr" style="width:30%;text-align:center;padding-bottom:15px;">
                   @if($result['irn_status'] == 1 && $result['signed_qrcode']!=null && $result['signed_qrcode']!='') 
                    <qrcode value="{{$result['signed_qrcode']}}" ec="M" style="width:40mm;background-color:white;color:black;border:none;margin-top:-50px;"></qrcode><br/>
                   @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="3" valign="top" align="center" class="nobdr" style="width:100%;padding:0;color:#8A8889;">
                    <br/>
                      Regd.Office-205,Skipper Corner 88,Nehru Place,New Delhi-110019 | CIN-U72300DL2006PTC147288 | PAN.No: AALCS7470E <br/><br/>
                      Stellar Confidential
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </page>