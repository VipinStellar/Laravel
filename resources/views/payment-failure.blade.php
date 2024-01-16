<!doctype html>
<html lang="en">
<head>
@include('inc-meta-common')
<title>Transaction failed: Stellar Data Recovery</title>
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
</head>
<body>
    @include('header')
  @if($result)
  <section>
    <div class="container">
      <div style="border:1px solid #ddd; background:#F4F4F4; display:block; margin:0 auto 10px; padding:10px 30px; max-width:600px;">
        <h1 class="h3" style="text-align:center; padding:10px;font-size:22px;">Your transaction status is {{ ucfirst($result['status']) }}.<br>
          @if($result['err_msg'])
          <small>{{ $result['err_msg'] }}</small>
          @endif
        </h1>
        <p style="text-align:center !important; font-size:16px">Your transaction id for this transaction is {{ $result['txnid'] }} <br>
          You may try making the payment again by clicking the link below.</p>
        <p style="text-align:center !important; font-size:16px"><a class="btn btn-primary" href="{{ $result['payment_link'] }}">Try Again</a></p>
      </div>
    </div>
  </section>
  @endif
    @include('footer')
</body>
</html>