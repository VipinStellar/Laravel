
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>DRS | MAF Form</title>
    </head>
    <body>
        <div style="margin: 0px auto; width: 100%;">
            <div style="float:left; width: 100%;">                
				<div style="text-align: center;">
					<div style="float:right; font-size: 10px;">
						<!--SIT/DRS/FM-05(JC)/R01 <br/>-->
						<sub style="font-size: 10px; margin-left: 10px; text-decoration: underline;">www.stellarinfo.com</sub>
					</div> 
					<img src="{{ url('public/images/logo-colored.png') }}" style="float:left" />					
                    <h4>
                        Stellar Information Technology Pvt. Ltd.                         
                    </h4>	
					<div style="float:right; font-size: 11px;">
						<h2>Material In No.: {{ empty($result->job_id) ? $result->deal_id : $result->job_id }}</h2>
					</div> 
                </div>                
                <div style="float:right;"> 
                    <img title='Print' id="print" name="print" width='30'  height='18' src="{{ url('public/images/print.jpg') }}" style="float:left;" onClick="document.getElementById('print').style.visibility = 'hidden'; window.print(); document.getElementById('print').style.visibility = 'visible';">
                </div>
            </div>
            <div style=" margin: 0px auto;">
                <table border="1" style="font-size: 11px; border: solid; width: 100%; font-weight: bold;" >
                    <tr>
                        <td colspan="8" height="18">Teleperformance Global Services Private Limited</td>
                    </tr>
                    <tr height="18">
                        <td>Type Of Media</td>
                        <td>Make</td>
                        <td>Model</td>
                        <td>Sr. No. </td>
                        <td>Firmware</td>
                        <td colspan="3">Capacity</td>
                    </tr>
                    <tr height="18">
                        <td>{{ $result->media_type }}</td>
                        <td>{{ $result->media_make }}</td>
                        <td>{{ $result->media_model }}</td>
                        <td>{{ $result->media_serial }}</td>
                        <td></td>
                        <td colspan="3">{{ $result->media_capacity }}</td>
                    </tr>
                    <tr height="18">
                        <td rowspan="2">Media in date/Job in time/Assessment time</td>
                        <td>Media In Date</td>
                        <td>Job in time</td>
                        <td>Job started time</td>
                        <td colspan="4">Assessment date and time</td>
                    </tr>
                    <tr height="18">
                        <td></td>
                        <td></td>
                        <td colspan="6">&nbsp;</td>
                    </tr>
					<tr height="18">
                        <td colspan="1">Job Taken by</td>
                        <td colspan="7">&nbsp;</td>
                    </tr>
					<tr height="18">
                        <td rowspan="2">Is Imaging Required For Analysis (Yes/No)</td>
                        <td colspan="3">Imaging Start date/time</td>
						<td colspan="4">Imaging End date/time</td>
                    </tr>
					<tr height="18">
						<td colspan="3">&nbsp;</td>
						<td colspan="4">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td colspan="1">Extension Request Time for assessment if required (By Email)</td>
						<td colspan="7">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td colspan="1">Reason For Extension</td>
						<td colspan="7">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td rowspan="2">Cause of Crash/Problem</td>
						<td colspan="2">Logical</td>
						<td colspan="2">Physical</td>
						<td colspan="3">Logical Cum Physical</td>
					</tr>
					<tr height="18">
						<td colspan="2">&nbsp;</td>
                                                <td colspan="2">Tamper Permission <b>(Yes/No)</b></td>
                                                <td colspan="3">Tamper Permission<b> (Yes/No)</b></td>
					</tr>
					<tr height="18">
						<td colspan="1">Assessment Report (%) of data recovery Possible</td>
						<td colspan="7">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td colspan="1">Time required for the job to be recovered</td>
						<td colspan="7">&nbsp;</td>						
					</tr>
					<!--
                    <tr>
                        <td colspan="8">
                            <img src="Images/form1.JPG"  style="width: 100%; height: 180px;"/> 
                        </td>                   
                    </tr>
					-->
                    <tr height="18">
                        <td colspan="1">Reason If Not Possible :</td>
						<td colspan="7">&nbsp;</td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Charges : </td> 
						<td colspan="2">&nbsp;</td>
                        <td colspan="1">Remarks : </td>
						<td colspan="4">&nbsp;</td>
                    </tr>                   
                    <tr height="18">
                        <td rowspan="3">Confirmation Details </td>
                        <td colspan="3">Confirm &nbsp;<input type="checkbox" /> Yes &nbsp;<input type="checkbox" /> No</td>
                        <td colspan="4">Date</td>
                        
                    </tr>
                    <tr height="18">
						<td colspan="3">Confirmation Date received from the client for Quotation</td>
                        <td colspan="4">Commited date</td>
                    </tr>
                    <tr height="18">
                        <td colspan="3">&nbsp;</td>
                        <td colspan="4">&nbsp;</td>
                    </tr> 
					<tr height="18">
                        <td rowspan="2">Imaging Required For Recovery (Yes/No)</td>
                        <td colspan="3">Imaging Start date/time</td>
						<td colspan="4">Imaging End date/time</td>
                    </tr>
					<tr height="18">
						<td colspan="3">&nbsp;</td>
						<td colspan="4">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td colspan="1">Email notification for Request Given By Whom</td>
						<td colspan="7">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td colspan="1">Reason For Extension</td>
						<td colspan="7">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td rowspan="2">Total No. of File after recovery (Excluding Mail Files)</td>
						<td colspan="2">Total no. of Files</td>
						<td colspan="1">Total Size</td>
						<td colspan="2">Total Mail files</td>
						<td colspan="2">Remarks if Any</td>						
					</tr>
					<tr height="18">
						<td colspan="2">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="2">&nbsp;</td>
						<td colspan="2">&nbsp;</td>						
					</tr>
					<tr height="18">
						<td rowspan="2">Total No. of File after checking the data and checked by whom</td>
						<td colspan="2">Total no. of</td>
						<td colspan="1">Total Size</td>
						<td colspan="1">Ok Files</td>
						<td colspan="1" style="width:50px;">Bad</td>
						<td colspan="1">Total Mail</td>
						<td colspan="1">Mail files 100%</td>												
					</tr>
					<tr height="18">
						<td colspan="2">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>		
						<td colspan="1">&nbsp;</td>		
						<td colspan="1">&nbsp;</td>								
					</tr>
					<!--
                    <tr>
                        <td colspan="8">
                            <img src="Images/form2.JPG" style="width: 100%; height: 180px;"  /> 
                        </td>                   
                    </tr>
					-->
                    <tr height="18">
                        <td>Data Details</td>
                        <td>Checked By</td>
                        <td>Directory Sent By</td>
                        <td>Dir. Listing Confirmed Date</td>
                        <td>Data Copied Date</td>                        
                        <td colspan="2">Delivered</td>
                        <td>Data Dispatched Date</td>
                    </tr>
                    <tr height="18">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td  style="width:100px;" colspan="2"> <input type="checkbox" /> Yes  <input type="checkbox" /> No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td style="width:40px;">&nbsp;</td>
                    </tr>
					<tr height="18">
                        <td rowspan="2">Media Decryption on which data delivered with date</td>
                        <td colspan="2">CD Or DVD Total Nos.</td>
                        <td colspan="1">Make</td>
                        <td colspan="2">Model</td>
                        <td colspan="2">Sr. No.</td>                        
                    </tr>
					<tr height="18">
                        <td colspan="2">&nbsp;</td>
                        <td colspan="1">&nbsp;</td>
                        <td colspan="2">&nbsp;</td>
                        <td colspan="2">&nbsp;</td>                        
                    </tr>
					<tr height="18">
						<td rowspan="2">Data Destroyed date and by whom</td>
                        <td colspan="1">Image</td>
						<td colspan="1">Date</td>
						<td colspan="1">By Whom</td>
						<td colspan="2">Date</td>
						<td colspan="2">Sign</td>
                    </tr>
					<tr height="18">
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">&nbsp;</td>
						<td colspan="2">&nbsp;</td>
						<td colspan="2">&nbsp;</td>
                    </tr>
<!--					
                    <tr>
                        <td colspan="8">
                            <img src="Images/form3.JPG" style="width: 100%; height: 118px;" /> 
                        </td>                   
                    </tr>
-->    
                    <tr height="18">
                        <td colspan="8" style="font-size:18px;"><b>Details Provided By Client : </b></td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Important Data Required By Client</td>
                        <td colspan="7">case belongs to Gurgaon</td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Media Already Tempered : </td>
                        <td colspan="7">No</td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Problem As Per Client</td>
                        <td colspan="7">Media Problem : Others<br/>case belongs to Gurgaon</td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Software Details (OS/FS):</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Details Of Partition:</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Third Party Encryption Details:</td>
                        <td colspan="7">No &nbsp;  &nbsp;  &nbsp;  &nbsp; </td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Windows Encryption Details:</td>
                        <td colspan="7">No &nbsp;  &nbsp;  </td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Tempering Permission :</td>
                        <td colspan="7">Ask Me</td>
                    </tr>
                    <tr height="18">
                        <td colspan="1">Pheripheral Details :</td>
                        <td colspan="7"></td>
                    </tr>
                </table>
				<table width="100%">
                    <tr>
                        <td align="left" width="33%">Doc. No: SITPL/IMS/DB(OP)/FM-05</td>
                        <td align="center" width="33%">Ver. No: 1.0</td>
                        <td align="right" width="33%">Effective Date: 01/09/2017</td>
                    </tr>
                </table>
                      <h3>Stellar Confidential</h3>
            </div>
        </div>
    </body>
</html> 
                                        
                                        