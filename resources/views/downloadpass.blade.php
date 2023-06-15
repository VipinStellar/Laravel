<!DOCTYPE html>
    <html>
        <head>
            <title> Material Gate Pass </title>
            <style>
                /* reset */
                *{
                    border: 0;
                    box-sizing: content-box;
                    color: inherit;
                    font-family: inherit;
                    font-size: inherit;
                    font-style: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    text-decoration: none;
                    vertical-align: top;
                }
                /* table */
                table { font-size: 75%; table-layout: fixed; width: 100%; }
                table { border-collapse: collapse; border-spacing: 2px; }

                /* page */
                html { font: 16px/1 'Montserrat',arial,sans-serif; overflow: auto; padding: 0.5in; }
                html { background: #999; cursor: default; }
                body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.25in; width: 8.5in; }
                body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }
               
                /* header */
                header { margin: 0 0 1em; }
                header:after { clear: both; content: ""; display: table; }
                header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
                header address { font-size: 75%; font-style: normal; line-height: 1.2; margin: 0 1em 1em 0; }
                header address p { margin: 0 0 0.25em; }
                header span, header img { display: block; float: left; }
                header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
                header img { max-height: 100%; max-width: 100%; margin-right:2.5em; }
                header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

                /* article */
                article:after { clear: both; content: ""; display: table; }
                h2 { text-align: center; font-weight: bold; }
                .text-uppercase { text-transform: uppercase !important; }
                article address { font-size: 125%; font-weight: bold; }

                /* table name-data */
                article .name-data{ margin: 1em 0 1em; }
                article .name-data td{font-weight: bold; padding: 0.25em; font-size: 12px;}
                article .name-data td span { font-weight: lighter; margin-left: 0.5em; }
                table.name-data:after{ clear: both; content: ""; display: table; }
                article td { position: relative; text-align: left; }

                /* table items */
                table.inventory {margin: 1em 0 0}
                table.inventory tr th,table.inventory tr td { border: 1px solid #212529; }
                table.inventory { clear: both; width: 100%; }
                table.inventory th { font-weight: bold; padding: 0.5em; text-align: center; }
                table.inventory td { padding: 0.75em; }
                table.inventory td:nth-child(1) { text-align: center; }
                table.inventory td:nth-child(2) { text-align: left; }
                table.inventory td:nth-child(3) { text-align: left; }
                table.inventory td:nth-child(4) { text-align: center; }
                table.inventory td:nth-child(5) { text-align: left; }

                /* Inventory check table */
                table.inventory-check tr th{ border: 1px solid #212529; text-align: left; line-height: 2; }
                table.inventory-check th{ padding: 1em; font-weight: bold; }
                table th .underline_text{text-decoration: underline;}
                table.inventory-check th p span{margin-left: 0.5em;}
                /* Instruction table */
                table.inst-table tr th { border: 1px solid #212529; text-align: left; line-height: 2; padding: 1em; font-weight: bold; }
                /* Button */
                .button{ cursor: pointer;color: #ffffff; background-color: #007bff;border-color: #007bff;box-shadow: none;padding: 0.25rem 0.5rem;font-size: 0.875rem;line-height: 1.5; border-radius: 0.2rem;}
                table strong{ font-weight: bold; }
                
                @media print {
                    * { -webkit-print-color-adjust: exact; }
                    html { background: none; padding: 0; }
                    body { box-shadow: none; margin: 0; }
                    span:empty { display: none; }
                    .print-button{display: none;}
                }

                @page { margin: 0; }
            </style>
        </head>
        <body>
        <header>
            <img src="https://www.stellarinfo.co.in/v3/images/logo-colored.png" alt="Stellar Data Recovery Logo">
            <address>
                <p>{{ $result->transfer_address }}</p>
                <p>{{ $result->phone_no }} | www.stellarinfo.co.in</p>
                <p>GSTIN: {{ $result->gst_no }}</p>
			</address>
		</header>
            <article>
                <h2><span style="text-transform: uppercase;"> MATERIAL GATE PASS -</span> @if($result->gatepass_type != '') {{ $result->gatepass_type }} @endif </h2>
                <table class="name-data">
                    <tr>
                        <td width="50%">Gate Pass No.: <span> {{ $result->gatepass_no }} </span></td>
                        <td width="50%">Date: <span>{{ date('d/m/Y', strtotime($result->created_on)) }}</span></td>
                    </tr>
                    @if($result->expected_return_date !='' && $result->expected_return_date != null)
                    <tr>
                        <td colspan="2">Expected Return Date: <span> {{ date('d/m/Y', strtotime($result->expected_return_date)) }} </span></td>
                    </tr>
                    @endif
                    <tr>
                        <td>Requesting Deptt.: <span> {{ $result->requester_deptt }} </span></td>
                        <td>Name: <span>{{ $result->sender_name }}</span></td>
                    </tr>
                    <tr>
                        <td>Dispatched to: <span> {{ ($result->dispatched_to !='' && $result->dispatched_to != null && $result->dispatch_branch_id != 0)?$result->dispatched_to:'Client' }} </span></td>
                        <td>Name: <span>{{ $result->dispatch_name }}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2">Address: <span> {{ $result->dispatch_address }} </span></td>
                    </tr>
                </table>
                <h2>Material Details</h2>
                <table class="inventory">
                <thead>
                    <tr>
                        <th width="5%" >SN</th>
                        <th width="35%">Asset Type</th>
                        <th width="50%">Description</th>
                        <th width="10%">Quantity</th>
                    </tr>
                  </thead>
                  <tbody>
                   @php  $count=1; @endphp 
                    @if($result->other_assets)
                    @foreach ($result->other_assets as $other_asset)
                      @if($other_asset->only_media)
                        @if($other_asset->material_name !='')
                            <tr>
                                <td>@php echo str_pad($count,2,"0",STR_PAD_LEFT); @endphp</td>
                                <td>{{ $other_asset->material_name }}</td>
                                <td>{!! $other_asset->material_description !!}</td>
                                <td></td>
                            </tr>
                            @php  $count++; @endphp
                        @endif
                      @endif
                      @if($other_asset->only_assets)
                        <tr>
                            <td>@php echo str_pad($count,2,"0",STR_PAD_LEFT); @endphp</td>
                            <td>{!! $other_asset->assets_name.'<br><strong>(Job ID - '.$other_asset->assets_job_id.')</strong>' !!}</td>
                            <td>{!! $other_asset->assets_description !!}</td>
                            <td></td>
                        </tr>
                        @php  $count++; @endphp
                      @endif
                    @endforeach
                    @endif
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:left;height:40px;"><span style="font-weight:bold;">Remarks : </span>{{ $result->remarks }}</td>
                    </tr>
                </table>
                <table class="inventory-check">
                    <tr>
                        <th>
                            <p class="underline_text">Prepared by:</p>
                            <p>Name:<span>___________________________</span></p>
                            <p>Emp ID:<span>_________________________</span></p>
                            <p>Signature:<span>_______________________</span></p>
                        </th>
                        <th>
                            <p class="underline_text">Approved by:</p>
                            <p>Name:<span>___________________________</span></p>
                            <p>Emp ID:<span>__________________________</span></p>
                            <p>Signature:<span>________________________</span></p>
                        </th>
                        <th>
                            <p class="underline_text">Carried by:</p>
                            <p>Courier:<span>___________________________</span></p>
                            <p>A/W Bill No.:<span>______________________</span></p>
                            <p>Signature:<span>________________________</span></p>
                        </th>
                    </tr>
                </table>
                <table class="inst-table">
                    <tr>
                        <th>
                        <p><span class="underline_text">Consignee/Carrier Receipt:</span> Received the above material in good condition</p>
                        <p>Name/Company <span>________________________</span> Date/Time <span>______________________</span> Signature <span>_______________________</span></p>
                        </th>
                    </tr>
                </table>
                <p style="margin-top: 1em; font-size: 9px; font-weight: bold;">Ahmedabad | Bengaluru | Chandigarh | Chennai | Coimbatore | Delhi NP | Delhi CP | Gurugram | Hyderabad | Kochi | Kolkata | Mumbai | Noida | Pune | Vashi</p>
                <h5 style="margin-top: 1em; font-weight: bold; text-align: center;">Stellar Internal</h5>
                <p class="print-button" style="text-align: center; margin-top: 2rem;"><button class="button" onclick="window.print();">Print this page</button></p>
            </article>
        </body>
    </html>