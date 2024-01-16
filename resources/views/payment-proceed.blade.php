<!DOCTYPE html>
    <html>
        <head>
            <title>Payment Procced</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <script>
                
                var hash = "<?php if($result && $result['hash']){ echo $result['hash'];  } ?>";
                function submitPayuForm() {
                if(hash == '') {
                    return;
                }
                //return;
                var payuForm = document.forms.payuForm;
                payuForm.submit();
                }
            </script>
        </head>
        <body onLoad="submitPayuForm();">
            <?php //echo"<pre>"; print_r($result); die; ?>
            @if(isset($result) && $result['hash'] !='' && $result['txnid'] !='')
                <form action="{{ $result['action'] }}" method="post" id="payuForm" name="payuForm"> 
                    <input type="hidden" name="hash" value="{{ $result['hash'] }}"/>
                    <input type="hidden" name="txnid" value="{{ $result['txnid'] }}" />
                    <input type="hidden" name="key" value="{{ $result['key'] }}" />
                    <input type="hidden" name="firstname" value="{{ $result['firstname'] }}" />
                    <input type="hidden" name="phone" value="{{ $result['phone'] }}" />
                    <input type="hidden" name="email" value="{{ $result['email'] }}" />
                    <input type="hidden" name="productinfo" value="{{ $result['productinfo'] }}" />
                    <input type="hidden" name="amount" value="{{ $result['amount'] }}" />
                    <input type="hidden" name="surl" value="{{ $result['surl'] }}" />
                    <input type="hidden" name="furl" value="{{ $result['furl'] }}" />
                    <input type="hidden" name="udf1" value="{{ $result['udf1'] }}" />
                    <input type="hidden" name="udf2" value="{{ $result['udf2'] }}" />
                    <input type="hidden" name="udf3" value="{{ $result['udf3'] }}" />
                    <input type="hidden" name="udf4" value="{{ $result['udf4'] }}" />
                    <input type="hidden" name="udf5" value="{{ $result['udf5'] }}" />
                </form>
            @endif
        </body>
    </html>