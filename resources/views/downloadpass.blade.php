<!DOCTYPE html>
    <html>
        <head>
            <title> Material Gate Pass </title>
            <style>
                /* reset */
                *
                {
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
                body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.5in; width: 8.5in; }
                body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }
               
                /* header */
                header { margin: 0 0 2em; }
                header:after { clear: both; content: ""; display: table; }
                header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
                header address { font-size: 75%; font-style: normal; line-height: 1.2; margin: 0 1em 1em 0; }
                header address p { margin: 0 0 0.25em; }
                header span, header img { display: block; float: left; }
                header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
                header img { max-height: 100%; max-width: 100%; margin-right: 4rem; }
                header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

                /* article */
                article, article address, table.inventory { margin: 0 0 3em; }
                article:after { clear: both; content: ""; display: table; }
                h2 {text-align: center; font-size: 20px; font-weight: bold;}
                .text-uppercase { text-transform: uppercase !important; }
                article address { font-size: 125%; font-weight: bold; }

                /* table name-data */
                article .name-data{margin: 2em 0 2em;}
                article .name-data th{font-weight: bold; padding: 0.5em; font-size: 14px;}
                article .name-data th span { font-weight: lighter; margin-left: 0.5em; }
                table.name-data:after{ clear: both; content: ""; display: table; }
                .name-data th, .name-data td { padding: 0.5em; position: relative; text-align: left; }

                /* table items */
                table.inventory {margin: 2em 0 0}
                table.inventory tr th,table.inventory tr td { border: 1px solid #212529; }
                table.inventory { clear: both; width: 100%; }
                table.inventory th { font-weight: bold; padding: 1em; text-align: center; }
                table.inventory td { padding: 1em; }
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
                .button{
                    cursor: pointer;
                    color: #ffffff;
                    background-color: #007bff;
                    border-color: #007bff;
                    box-shadow: none;
                    padding: 0.25rem 0.5rem;
                    font-size: 0.875rem;
                    line-height: 1.5;
                    border-radius: 0.2rem;
                }
                
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
                <p>D-16, Sector-33, Infocity Phase II, Gurugram Haryana 122001</p>
                <p>[0124] 4326700 | www.stellarinfo.co.in</p>
                <p>GSTIN: 06AALCS7470E1Z5</p>
			</address>
		</header>
            <article>
                <h2><span style="text-transform: uppercase;"> MATERIAL GATE PASS -</span> Non-Returnable </h2>
                <table class="name-data">
                    <tr>
                        <th width="50%">Gate Pass No.: <span> R/GGN/6901 </span></th>
                        <th width="50%">Date: <span>29/09/2021</span></th>
                    </tr>
                    <tr>
                        <th colspan="2">Expected Return Date: <span> 06/10/2022 </span></th>
                    </tr>
                    <tr>
                        <th>Requesting Deptt.: <span> DRS </span></th>
                        <th>Name: <span>Vipin Kumar Chaurasia</span></th>
                    </tr>
                    <tr>
                        <th>Dispatched to: <span> Hyderabad </span></th>
                        <th>Name: <span>Vivek Singh</span></th>
                    </tr>
                    <tr>
                        <th colspan="2">Address: <span> 509, 5th Floor, Aditya Trade Center, Ameerpet, Hyderabad- 500038, Telangana India </span></th>
                    </tr>
                </table>
                <h2>Material Details</h2>
                <table class="inventory">
                <thead>
                    <tr>
                        <th width="5%" >SN</th>
                        <th width="25%">Asset Type</th>
                        <th width="30%">Description</th>
                        <th width="10%">Quantity</th>
                        <th width="30%">Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td>01</td>
                        <td>HDD</td>
                        <td>I have Recived External Hard Drive</td>
                        <td>1</td>
                        <td>Toshiba Hard Drive</td>
                    </tr>
                    <tr>
                        <td></td>
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
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
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
                        <td></td>
                    </tr>
                </table>
                <table class="inventory-check">
                    <tr>
                        <th>
                            <p class="underline_text">Prepared by:</p>
                            <p>Name:<span>______________________</span></p>
                            <p>Emp ID:<span>_____________________</span></p>
                            <p>Signature:<span>___________________</span></p>
                        </th>
                        <th>
                            <p class="underline_text">Approved by:</p>
                            <p>Name:<span>______________________</span></p>
                            <p>Emp ID:<span>_____________________</span></p>
                            <p>Signature:<span>___________________</span></p>
                        </th>
                        <th>
                            <p class="underline_text">Carried by:</p>
                            <p>Courier:<span>______________________</span></p>
                            <p>A/W Bill No.:<span>_________________</span></p>
                            <p>Signature:<span>____________________</span></p>
                        </th>
                    </tr>
                </table>
                <table class="inst-table">
                    <tr>
                        <th>
                        <p><span class="underline_text">Consignee/Carrier Receipt:</span> Received the above material in good condition</p>
                        <p>Name/Company <span>______________________</span> Date/Time <span>____________________</span> Signature <span>____________________</span></p>
                        </th>
                    </tr>
                </table>
                <p style="margin-top: 1em; font-size: 9px; font-weight: bold;">Ahmedabad | Bengaluru | Chandigarh | Chennai | Coimbatore | Delhi NP | Delhi CP | Gurugram | Hyderabad | Kochi | Kolkata | Mumbai | Noida | Pune | Vashi</p>
                <h5 style="margin-top: 1em; font-weight: bold; text-align: center;">Stellar Internal</h5>
                <p class="print-button" style="text-align: center; margin-top: 2rem;"><button class="button" onclick="window.print();">Print this page</button></p>
            </article>
        </body>
    </html>