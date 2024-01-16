<!doctype html>
<html lang="en">
<head>
@include('inc-meta-common')
<title>Payment Successful - Stellar Data Recovery</title>
<!-- Bootstrap core CSS -->
<link href="{{url('public/css/bootstrap.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('public/css/custom.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('public/css/contact.css')}}" rel="stylesheet" type="text/css">
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
.box-holder { border: 1px solid #ddd; background: #fff; padding: 10px; min-height: 230px; margin-bottom: 10px; }
.box-holder:last-child { margin-right: none }
.big-text3 { color: #0066cc; font-size: 40px; font-weight: 600; text-align: center; margin-bottom: 10px; line-height: 1.5;}
.text-cnt { font-size: 16px; text-align: left; }
</style>
</head>
<body>
 @include('header')
@if(isset($result) && count($result) > 0)
<section class="pt-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="fmon text-red mb-0 text-uppercase fs16" id="step-subtitle"><strong>Payment Completed</strong></p>
                <h3 class="section-title mb-0" id="step-title">Your Transaction is Successful</h3>
            </div>
        </div>
        <div style="border:1px solid #ddd; background:#F4F4F4; display:block; margin:0 auto 30px; padding:10px 30px; max-width:750px;"> <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
            <h1 class="h3" style="text-align:center; padding:10px;font-size:22px;">Thank you for your payment of <strong>Rs. {{ number_format($result['payment_amount'],2) }} </strong></h1>
            <p style="text-align:center !important; font-size:16px">Your Transaction Id for your Order no. <strong>{{ $result['order_no'] }}</strong> is <strong> {{ $result['payment_txnid'] }}</strong><br>
                You will soon receive your transaction details on mail.</p>
        </div>
    </div>
</section>
@endif
@include('footer')
<script>
$(window).on('load', function() {
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();
});
</script> 

</body>
</html>