function setSectionTitle(id){
	if(id){
		var title = $('#pay-form #'+id).attr("data-title");
		$('#pay-form #step-title').text(title);
	}
	moveToTop();
}

function moveToTop(){
	$('html, body').animate({
		scrollTop: $(".job-steps").offset().top-10
	}, 200);
}

function hasData(elm) {
    if (!$(elm).val() || $(elm).val() == null || $(elm).val() == " " || $(elm).val() == "") {
        return false;
    }
}

function showError(msg){
	if(msg){
		if(msg == 'none'){
			$('.details-alert').addClass("alert-danger").removeClass("alert-success").hide().html('');
		}else{
			$('.details-alert').html(msg).show();
		}
	}else{
		$('.details-alert').html('Please fill all required fields properly then proceed ahead.').show();
	}
}

function resetOnReload(){
	$('#paymentForm input[name=agree]').prop('checked', false);
}


$(document).ready(function(){
  resetOnReload();
  $('#paymentForm').on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) { 
		e.preventDefault();
		return false;
	  }
  });

  $('#pay-form #BackToStepInfo').on('click',function (e) {
	  showError("none");
	  setSectionTitle('step-info');
	  $("#pay-form #step-details").fadeOut('fast',function() {
		  $("#pay-form #step-info").fadeIn('fast');
		  $('.job-steps li#step2').removeClass('active');
	  });
  });
  
  //form next navigation
  $('#pay-form #GoToStepDetails').on('click',function () {
	  if(validateStepInfo() === true){
		showError("none");
	  	setSectionTitle('step-details');
		$("#pay-form #step-info").fadeOut('fast',function() {
			$('.preview_fee').show();
			$("#pay-form #step-details").fadeIn('fast',function() {
				$('.job-steps li#step2').addClass('active');
			});
		});
	  }
  });
  

  // Hide Error Input and Select
  $('#paymentForm input, #paymentForm select').on('change', function () {
	$(this).closest('.for-validation').removeClass('has-error');
	showError("none");
  });

//Media Details change
   
  $('.numeric').on('input', function (event) {
	  this.value = this.value.replace(/[^0-9.,]/g, '');
  });
  $('.numericint').on('input', function (event) {
	  this.value = this.value.replace(/[^0-9,]/g, '');
  });
     
});

//end document ready

function otherCity(){
	var city = $('#city').val();

	if(city == 'Other'){
		$('#cityname').show();
	} 
	else {
		$('#cityname').hide();
	}
}

function validateStepInfo(){
	showError("none");
    var a = "#paymentForm input[name='name']";
    var b = "#paymentForm input[name='email']";
	var c = "#paymentForm input[name='phone']";
	var d = "#paymentForm input[name='address']";
    var f = "#paymentForm select[name='city']";
	var oc = "#paymentForm input[name='other_city']";
	var g = "#paymentForm select[name='state']";
	var h = "#paymentForm input[name='zipcode']";
	var gst = "#paymentForm input[name='gst_no']";
	var sc = "#paymentForm input[name='state_code']";
	var vzc = false;
	// s
	var atpos = $(b).val().indexOf("@");
	var dotpos = $(b).val().lastIndexOf(".");
	
	var regExp = /^[ A-Za-z0-9,():./-]*$/;	
	
	if(hasData(a) === false || $(a).val().length < 3){
		showError("Please fill a valid Name in billing information");
		$(a).closest('.for-validation').addClass('has-error');
		$(a).focus();
		return false;
	}
	else {
	  if(regExp.test($(a).val()) == false){
		showError("Only alphabets and numeric characters allowed");
		$(a).closest('.for-validation').addClass('has-error');
		$(a).focus();
		return false;
	  }
	  else {
		showError("none");
		$(a).closest('.for-validation').removeClass('has-error');
	  }
	}
	
	if (atpos<2 || dotpos<atpos+2 || dotpos+2>=$(b).val().length){
		showError("Please provide a valid E-mail address");
		$(b).closest('.for-validation').addClass('has-error');
		$(b).focus();
		return false;
    }
	else {
		showError("none");
		$(b).closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(c) === false || isNaN($(c).val()) || $(c).val().length !== 10){
		showError("Please fill a 10 digit Mobile number");
		$(c).closest('.for-validation').addClass('has-error');
		$(c).focus();
		return false;
	}
	else {
		showError("none");
		$(c).closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(d) === false || $(d).val().length < 3){
		showError("Please fill your Address properly");
		$(d).closest('.for-validation').addClass('has-error');
		$(d).focus();
		return false;
	}
	else {
		showError("none");
		$(d).closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(g) === false || $(g).val() == 'select'){
		showError("Please select your State");
		$(g).closest('.for-validation').addClass('has-error');
		$(g).focus();
		return false;
	}
	else {
		showError("none");
		$(g).closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(f) === false || $(f).val() == 'select'){
		showError("Please select your City");
		$(f).closest('.for-validation').addClass('has-error');
		$(f).focus();
		return false;
	}
	else {
		showError("none");
		$(f).closest('.for-validation').removeClass('has-error');
	}
	
	if($(f).val() == 'Other'){
		if(hasData(oc) === false || $(oc).val().length < 3){
			showError("Please enter your other City");
			$(oc).closest('.for-validation').addClass('has-error');
			$(oc).focus();
			return false;
		}
		else {
			showError("none");
			$(oc).closest('.for-validation').removeClass('has-error');
		}
	}
	
	if(hasData(h) === false || isNaN($(h).val()) || $(h).val().length !== 6){
		showError("Please enter your Zipcode/Pincode properly");
		$(h).closest('.for-validation').addClass('has-error');
		$(h).focus();
		return false;
	}
	else {
		showError("none");
		$(h).closest('.for-validation').removeClass('has-error');
	}
	
	//gstin validate
	if(($(gst).val() !=undefined) && ($(gst).val()!=null) && ($(gst).val()!='')){
	  var regExp = /\d{2}[a-zA-z]{5}\d{4}[a-zA-Z]{1}\d{1}[0-9a-zA-Z]{2}/;
	  var gstval = $(gst).val().substring(0, 2); // 0,1 == 2 num
	  var state_code = $('#paymentForm #state_code').val();
	  if($(gst).val().length == 15 ){
		if( !$(gst).val().match(regExp) ){
		  showError("Not a valid GSTIN/UIN");
		  $(gst).closest('.for-validation').addClass('has-error');
		  $(gst).focus();
		  return false;
		}
		else if(state_code != gstval) {
		  showError("Not a valid GSTIN/UIN for the State you selected");
		  $(gst).closest('.for-validation').addClass('has-error');
		  $(gst).focus();
		  return false;
		} 
		else {
		  showError("none");
		  $(gst).closest('.for-validation').removeClass('has-error');
		}
	  } 
	  else {
		showError("Please enter 15 digits GSTIN/UIN");
		$(gst).closest('.for-validation').addClass('has-error');
		$(gst).focus();
		return false;
	  } 
	}
	else { 
		showError("none");
		$(gst).closest('.for-validation').removeClass('has-error');
	}
	
	
	if(!$('#agree').is(':checked')){
		showError("You must agree with the terms and conditions.");
		return false;
	}
	else {
		showError("none");
	}

	if((($(sc).val!=undefined) && ($(sc).val!=null) && ($(sc).val!=''))){
		$.ajax({
			type: "POST",
			async : false,
			dataType: "json",
			url: verify_pincode_url,
			data: {'state_code':$(sc).val(), 'zipcode': $(h).val()},
			cache: false,
			beforeSend: function(){
			   $(".job-form").addClass('loading');
			 },
			success: function(data) {
				if(data.status=='success'){
					if(data.message==true){
						vzc = data.message;
					}
				}
			},
			error: function (jqXHR, status, err) {
			   //alert(err);
			 },
			complete: function (jqXHR, status) {
			   //console.log(jqXHR['responseText']);
				$(".job-form").removeClass('loading');
				
			 }
		});
	}
	
	if(vzc==false){
		console.log(vzc);
		showError("you have enter wrong Zipcode/Pincode");
		$(h).closest('.for-validation').addClass('has-error');
		$(h).focus();
		return false;
	}else{
		showError("none");
	}

	setInfoDetails();
	//$('#verifyDetailsModal').modal({backdrop: 'static',keyboard: false});
	return true;	
}

function getstateId(elem){
	$('#state_code').val($(elem).find('option:selected').attr('data-state'));
}

function setInfoDetails(){
	$('.table_verify_data tr > td.data_value').text('');
	var name= $("#paymentForm input[name='name']").val();
    var email= $("#paymentForm #email").val();
	var phone= $("#paymentForm #phone").val();
	var address= $("#paymentForm #address").val() +' '+ $("#paymentForm #landmark").val();
    var city= $("#paymentForm #city").val();
	var state= $("#paymentForm #state").val();
	var zipcode= $("#paymentForm #zipcode").val();
	var otherCity= $("#paymentForm #other_city").val();
	var gst= $("#paymentForm #gst_no").val();
	var cityName= (city=='Other' && otherCity!='' ? otherCity : city);
	$('.table_verify_data tr.data_row_name > td.data_value').text(name);
	$('.table_verify_data tr.data_row_email > td.data_value').text(email);
	$('.table_verify_data tr.data_row_phone > td.data_value').text(phone);
	$('.table_verify_data tr.data_row_address > td.data_value').text(address);
	$('.table_verify_data tr.data_row_city > td.data_value').text(cityName);
	$('.table_verify_data tr.data_row_state > td.data_value').text(state);
	$('.table_verify_data tr.data_row_zipcode > td.data_value').text(zipcode);
	if(gst!=''){
		$('.table_verify_data tr.data_row_gst_no').show();
		$('.table_verify_data tr.data_row_gst_no > td.data_value').text(gst);
	}else{
		$('.table_verify_data tr.data_row_gst_no').hide();
	}
}