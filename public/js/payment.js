var subtotal_amount = "";
var tax_amount = "";
var final_amount = "";
var advance_total = "";
function QuotePrice(id){
	var id = id.trim();
	var dataString = {"request":"quote_data", "id":id};
	$("#plan_standard").hide();
	$("#plan_economy").hide();
	$("#plan_express").hide();
	
	if(id != ''){
		$.ajax({
		 type: "POST",
		 dataType: "json",
		 url: price_quote_url,
		 data: dataString,
		 cache: false,
		 beforeSend: function(){
			$("#job-form").addClass('loading');
		  },
		 success: function(data) {
			
			if(data['plan_types'] && data['plan_types'] != '' && data['status'] == 'success'){
				var plan_types = data['plan_types'];
				var plan_active = data['plan_active'];
				var plan_selected = data['plan_selected'];
				if(plan_selected['full_amount_type'] == 'RECV1' || plan_selected['full_amount_type'] == 'RECV2'){
					setSectionTitle('verify_service');
					$("#job-form #verify_service").show();
					$("#job-form #step-plan").attr("data-title",$('#job-form #verify_service').attr("data-title"));
					if(data['job_id']!= null && data['job_id']!=''){
						$('.table_verify_service tr.data_row_job_id > td.data_value').text(data['job_id']);
					}else{
						$('.table_verify_service tr.data_row_job_id > td').css('display','none');
					}	
					$('.table_verify_service tr.data_row_plan > td.data_value').text(plan_selected['plan_type']);
					$('.table_verify_service tr.data_row_total_amt > td.data_value').html('<em class="rupee">`</em> '+(Number(plan_selected["plan_total_amount"])).toLocaleString("en"));
					$('.table_verify_service tr.data_row_paid_amt > td.data_value').html('<em class="rupee">`</em> '+(Number(plan_selected['paid_amount'])).toLocaleString("en"));
					$('.table_verify_service tr.data_row_bal_amt > td.data_value').html('<em class="rupee">`</em> '+(Number(plan_selected['balance_amount'])).toLocaleString("en"));
					if(plan_selected['tax_applicable'] == 0){
						$('.table_verify_service tr.tax_text > td').css('display','none');
					}else{
						$('.table_verify_service tr.tax_text > td').css('display','');
					}
					
					$(".text_fee_type").text('Balance Amount');
					$(".text_advc_label").text('Partial Amount');
					$(".text_recv_label").text('Full Balance Amount');
				}
				else{
				  $("#job-form #verify_service").hide();
				  $.each(plan_types, function(key, value){
					  if(value.plan_type == "Standard"){
						  $("#plan_standard").show();
						  if(value.working_days){
						  	$("#plan_standard .recovery-time").html(value.working_days + ' days');
						  }
						  if((value.job_support !=null && value.job_support !='')){
							$("#plan_standard .support-via").html(value.job_support);
						  }
						  if(value.job_speed !=null && value.job_speed !=''){
							$("#plan_standard .speed-lab").html(value.job_speed);
						  }
					  }
					  if(value.plan_type == "Economy"){
						  $("#plan_economy").show();
						  if(value.working_days){
						  	$("#plan_economy .recovery-time").html(value.working_days + ' days');
						  }
						  if(value.job_support !=null && value.job_support !=''){
							$("#plan_economy .support-via").html(value.job_support);
						  }
						  if(value.job_speed !=null && value.job_speed !=''){
							$("#plan_economy .speed-lab").html(value.job_speed);
						  }
					  }
					  if(value.plan_type == "Priority"){
						  $("#plan_express").show();
						  if(value.working_days){
						  	$("#plan_express .recovery-time").html(value.working_days + ' days');
						  }
						  if(value.job_support !=null && value.job_support !=''){
							$("#plan_express .support-via").html(value.job_support);
						  }
						  if(value.job_speed !=null && value.job_speed !=''){
							$("#plan_express .speed-lab").html(value.job_speed);
						  }
					  }	
				  });
				}
				
				if(plan_selected && plan_selected != '' && plan_selected['plan_type'] == plan_active){
					subtotal_amount = plan_selected['subtotal_amount'];
					tax_amount = plan_selected['total_tax'];
					final_amount = plan_selected['final_amount'];
					advance_total = plan_selected['advance_total'];
				}
				$(".subtotal_amount").html((subtotal_amount).toLocaleString("en"));
				$(".tax_amount").html((tax_amount).toLocaleString("en"));
				if(plan_selected['tax_rate'] != 0){
					$(".tax_rate").html(plan_selected['tax_rate']);
				}else{
					$($('.tax_rate').parents()[0]).hide();
				}
				
				$(".final_amount").html((final_amount).toLocaleString("en"));
				if(advance_total){
					$(".advance_total").show().html((advance_total).toLocaleString("en"));
					$(".payable_amount").html((advance_total).toLocaleString("en"));
					$("#pay_now_advance").parent().show();
					$("#pay_now_advance").val(plan_selected['advance_amount_type']);
					$("#pay_now_advance").attr('checked', true).trigger('click');
					$('#preview-box .preview_pay_now').show().find('strong').html($("#pay_now_advance").next('label').html());
				}else{
					$(".advance_total").hide().html("");
					$("#pay_now_advance").parent().hide();
					$(".payable_amount").html((final_amount).toLocaleString("en"));
					$("#pay_now_full").attr('checked', true).trigger('click');
					$('#preview-box .preview_pay_now').show().find('strong').html($("#pay_now_full").next('label').html());
				}
				$("#pay_now_full").val(plan_selected['full_amount_type']);
				$("#paymentProceedForm #plan_type").val(plan_active).trigger('change');
				$(".service-type-box[data-value='" + plan_active +"']").addClass('active');
				$('#preview-box .preview_plan_name').show().find('strong').text(plan_selected['plan_type']);
				$("#job-form").removeClass('loading');
				
				$("#paymentProceedForm #firstname").val(data['name']);
    			$("#paymentProceedForm #email").val(data['email']);
				$("#paymentProceedForm #phone").val(data['phone']);				
				$("#paymentProceedForm #address").val(data['address']);
				$("#paymentProceedForm #landmark").val(data['landmark']);
				$("#paymentProceedForm #city").val(data['city']);
				$("#paymentProceedForm #state").val(data['state']);
				$("#paymentProceedForm #state_code").val(data['state_code']);
				$("#paymentProceedForm #pincode").val(data['pincode']);
				$("#paymentProceedForm #gst_no").val(data['gst_no']);				
				$("#paymentProceedForm #jobid").val(data['job_id']);
				$("#paymentProceedForm #branch").val(data['branch']);
				$("#paymentProceedForm #branch_id").val(data['branch_id']);
				$("#paymentProceedForm #media_type").val(data['media_type']);
				$("#paymentProceedForm #tax_applicable").val(data['tax_applicable']);
				$("#paymentProceedForm #deal_id").val(data['deal_id']);
				// Set table 
				$('.table_verify_data tr > td.data_value').text('');
				if(data['job_id'] !='' && data['job_id'] != null ){
					$('.table_verify_data tr.data_row_jobid > td.data_value').text(data['job_id']);
				}else{
					$('.table_verify_data tr.data_row_jobid > td').css('display','none');
				}
				$('.table_verify_data tr.data_row_name > td.data_value').text(data['name']);
				$('.table_verify_data tr.data_row_email > td.data_value').text(data['email']);
				$('.table_verify_data tr.data_row_phone > td.data_value').text(data['phone']);
				$('.table_verify_data tr.data_row_address > td.data_value').text(data['address']);
				$('.table_verify_data tr.data_row_city > td.data_value').text(data['city']);
				$('.table_verify_data tr.data_row_state > td.data_value').text(data['state']);
				$('.table_verify_data tr.data_row_zipcode > td.data_value').text(data['pincode']);
				if(data['gst_no'] !='' && data['gst_no'] != null ){
					$('.table_verify_data tr.data_row_gst_no > td.data_value').text(data['gst_no']);
				}else{
					$('.table_verify_data tr.data_row_gst_no').css('display','none');
				}
			}
			else {
				if(data['msg'] && data['msg'] !=''){
					$("#main_msg").text(data['msg']);
				}
				else{
					$("#main_msg").text('OOPS!! Requested URL is not valid.');
				}
				$("#form-error").show();
				$("#job-form").hide();
				moveToTop();
			}
				
		 },
		 error: function (jqXHR, status, err) {
			//alert(err);
		  },
		 complete: function (jqXHR, status) {
			//console.log(jqXHR['responseText']);
		  }
	 });
	 
	}
	else{
		$("#main_msg").text('OOPS!! Requested URL is not valid.');
		$("#form-error").show();
		$("#job-form").hide();
		moveToTop();
	}
	
}

//Service Type Select
$('.service-type-box').on('click',function() {
	  var plan_value = $(this).attr("data-value");
	  var plan_name = $(this).find("h4").text();
	  $('.service-type-box').removeClass('active');
	  $(this).addClass("active");
	  //focusBtn('#GoToStepInfo');
	  $('#preview-box .preview_plan_name').show().find('strong').text(plan_name);
	  $('#paymentProceedForm #plan_type').val(plan_value).trigger('change');
	  showError("none");
	  
	  var dataString = {"request":"plan_data", "plan":plan_value};
	  //ajax
	  if(plan_value != ''){
		$.ajax({
		 type: "POST",
		 dataType: "json",
		 url: price_quote_url,
		 data: dataString,
		 cache: false,
		 beforeSend: function(){
		   $("#job-form").addClass('loading');
		 },
		 success: function(data) {
			
			if(data['plan_selected'] && data['plan_selected'] != '' && data['status'] == 'success'){
				var plan_selected = data['plan_selected'];
				
				if(plan_selected && plan_selected != ''){
					subtotal_amount = plan_selected['subtotal_amount'];
					tax_amount = plan_selected['total_tax'];
					final_amount = plan_selected['final_amount'];
					advance_total = plan_selected['advance_total'];
				}

				$(".subtotal_amount").html((subtotal_amount).toLocaleString("en"));
				$(".tax_amount").html((tax_amount).toLocaleString("en"));
				$(".tax_rate").html(plan_selected['tax_rate']);
				$(".final_amount").html((final_amount).toLocaleString("en"));
				if(advance_total){
					$(".advance_total").show().html((advance_total).toLocaleString("en"));
					$(".payable_amount").html((advance_total).toLocaleString("en"));
					$("#pay_now_advance").parent().show();
					$("#pay_now_advance").attr('checked', true).trigger('click');
					$('#preview-box .preview_pay_now').show().find('strong').html($("#pay_now_advance").next('label').html());
				}else{
					$(".advance_total").hide().html("");
					$("#pay_now_advance").parent().hide();
					$(".payable_amount").html((final_amount).toLocaleString("en"));
					$("#pay_now_full").attr('checked', true).trigger('click');
					$('#preview-box .preview_pay_now').show().find('strong').html($("#pay_now_full").next('label').html());
				}
				$(".advance_total").html((advance_total).toLocaleString("en"));
				$(".payable_amount").html((advance_total).toLocaleString("en"));
				$('#paymentProceedForm #pay_now_advance').prop('checked', true).trigger('change');
			}
			else {
				if(data['msg'] && data['msg'] !=''){
					$("#main_msg").text(data['msg']);
				}
				else{
					$("#main_msg").text('Error!! Please refresh and try again');
				}
				$("#form-error").show();
				$("#job-form").hide();
				moveToTop();
			}
				
		 },
		 error: function (jqXHR, status, err) {
			//alert(err);
		  },
		 complete: function (jqXHR, status) {
			$("#job-form").removeClass('loading');
		  }
	 });
	 
	}
  });

function setSectionTitle(id){
	if(id){
		var title = $('#job-form #'+id).attr("data-title");
		$('#job-form #step-title').text(title);
	}
	moveToTop();
}

function moveToTop(){
	$('html, body').animate({
		scrollTop: $(".job-steps").offset().top-10
	}, 200);
}

function hasData(elm) {
    if (!elm.val() || elm.val() == null || elm.val() == " " || elm.val() == "") {
        return false;
    }
	else{
		return true;
	}
}

function trim(str){
	if( typeof str !== 'undefined' ) {
		var str=str.replace(/^\s+|\s+$/,'');
   		return str;
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
	if ($('.submission-alert').length) {
		$('.submission-alert').hide();
	}
}

function resetOnReload(){
	$('#paymentProceedForm input[name=agree]').prop('checked', false);
}

//Validate form
function validateStepPlan() {
    if (hasData($('#paymentProceedForm #plan_type')) === false){
		return false;
	}
	if(!$('#paymentProceedForm input[name=pay_now]:checked').val()){
		$('#paymentProceedForm input[name=pay_now]').closest('.for-validation').addClass('has-error');
		return false;
	}
}

function validateProceedPayment(){
	showError("none");
    var b = $("#paymentProceedForm #firstname");
    var c = $("#paymentProceedForm #email");
	var d = $("#paymentProceedForm #phone");
	var e = $("#paymentProceedForm #address");
	var f = $("#paymentProceedForm #city");
	var g = $("#paymentProceedForm #state");
	var h = $("#paymentProceedForm #state_code");
	var i = $("#paymentProceedForm #pincode");
	var j = $("#paymentProceedForm #gst_no");

	var atpos = c.val().indexOf("@");
	var dotpos = c.val().lastIndexOf(".");
	
	var regExp = /^[ A-Za-z0-9,():./-]*$/;	
	
	if(hasData(b) === false || b.val().length < 3){
		showError("Not a valid Name");
		//b.closest('.for-validation').addClass('has-error');
		return false;
	}
	else {
	  if(regExp.test(b.val()) == false){
		showError("Only alphabets and numeric characters allowed in Name");
		//b.closest('.for-validation').addClass('has-error');
		return false;
	  }
	  else {
		showError("none");
		//b.closest('.for-validation').removeClass('has-error');
	  }
	}
	
	if (atpos<2 || dotpos<atpos+2 || dotpos+2 >= c.val().length){
		showError("Not a valid E-mail address");
		//c.closest('.for-validation').addClass('has-error');
		return false;
    }
	else {
		showError("none");
		//c.closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(d) === false || isNaN(d.val())){
		showError("Not a valid Mobile number");
		//d.closest('.for-validation').addClass('has-error');
		return false;
	}
	else {
		showError("none");
		//d.closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(e) === false || e.val().length < 3){
		showError("Not a valid Address");
		//e.closest('.for-validation').addClass('has-error');
		return false;
	}
	else {
		showError("none");
		//e.closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(g) === false || g.val() == 'select'){
		showError("Not a valid State");
		//g.closest('.for-validation').addClass('has-error');
		return false;
	}
	else {
		showError("none");
		//g.closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(f) === false || f.val() == 'select'){
		showError("Not a valid City");
		//f.closest('.for-validation').addClass('has-error');
		return false;
	}
	else {
		showError("none");
		//f.closest('.for-validation').removeClass('has-error');
	}
	
	if(hasData(i) === false || isNaN(i.val()) || i.val().length !== 6){
		showError("Not a valid Pincode");
		//i.closest('.for-validation').addClass('has-error');
		return false;
	}
	else {
		showError("none");
		//i.closest('.for-validation').removeClass('has-error');
	}
	
	//gstin validate
	if(hasData(j) === true){
	  var regExp = /\d{2}[a-zA-z]{5}\d{4}[a-zA-Z]{1}\d{1}[0-9a-zA-Z]{2}/;
	  var gstval = j.val().substring(0, 2); // 0,1 == 2 num
	  var state_code = h.val();
	  if(j.val().length == 15 ){
		if( !j.val().match(regExp) ){
		  showError("Not a valid GSTIN/UIN");
		  //j.closest('.for-validation').addClass('has-error');
		  return false;
		}
		else if(state_code != gstval) {
		  showError("Not a valid GSTIN/UIN for the selected State");
		  //j.closest('.for-validation').addClass('has-error');
		  return false;
		} 
		else {
		  showError("none");
		  //j.closest('.for-validation').removeClass('has-error');
		}
	  } 
	  else {
		showError("Not a valid GSTIN/UIN");
		//j.closest('.for-validation').addClass('has-error');
		return false;
	  } 
	}
	else { 
		showError("none");
		//j.closest('.for-validation').removeClass('has-error');
	}
	
	if(!$('#agree').is(':checked')){
		showError("You must agree with the terms and conditions.");
		return false;
	}
	else {
		showError("none");
	}
	
	//setDetailsTable();
	return true;	
}


$(document).ready(function(){
  resetOnReload();
  
  $('#paymentProceedForm').on('keyup keypress', function(e) {
	  var keyCode = e.keyCode || e.which;
	  if (keyCode === 13) { 
		e.preventDefault();
		return false;
	  }
  });
  
  //form back navigation
  $('#job-form #BackToStepPlan').on('click',function (e) {
	  showError("none");
	  setSectionTitle('step-plan');
	  $("#job-form #step-info").fadeOut('fast',function() {
		  $('.preview_payable').hide();
		  $('.preview_fee').show();
		  $("#job-form #step-plan").fadeIn('fast',function() {
			  $('.job-steps li#step2').removeClass('active');
		  });
	  });
  });
  $('#job-form #BackToStepInfo').on('click',function (e) {
	  showError("none");
	  setSectionTitle('step-info');
	  $("#job-form #step-payment").fadeOut('fast',function() {
		  $("#job-form #step-info").fadeIn('fast');
		  $('.job-steps li#step3').removeClass('active');
	  });
  });
  
  //form next navigation
  $('#job-form #GoToStepInfo').on('click',function () {
	  if(validateStepPlan() === false){
		showError("Please fill all required fields properly");
	  }
	  else{
		showError("none");
	  	setSectionTitle('step-info');
		$("#job-form #step-plan").fadeOut('fast',function() {
			$('.preview_payable').show();
			$('.preview_fee').hide();
			$("#job-form #step-info").fadeIn('fast',function() {
				$('.job-steps li#step2').addClass('active');
			});
		});
	  }
  });
  $('#GoToStepPayment').on('click',function () {
	  if(validateStepInfo() === true){
		showError("none");
	  	setSectionTitle('step-payment');
		$("#job-form #step-info").fadeOut('fast',function() {
			$("#job-form #step-payment").fadeIn('fast',function() {
				$('.job-steps li#step3').addClass('active');
			});
		});
	  }
  });

  


//Media Details change
  $('#paymentProceedForm input, #paymentProceedForm select').on('change', function () {
	  $(this).closest('.for-validation').removeClass('has-error');
	  showError("none");
  });

  $('#paymentProceedForm input[name=pay_now]').on('change', function () {
	  if ($("#pay_now_advance").prop("checked")) {
		  $(".payable_amount").html((advance_total).toLocaleString("en"));
	  } else{
		   $(".payable_amount").html((final_amount).toLocaleString("en"));
	  }
	  $('#preview-box .preview_pay_now').show().find('strong').html($(this).next('label').html());
  });
  
  $('.numeric').on('input', function (event) {
	  this.value = this.value.replace(/[^0-9.,]/g, '');
  });
  $('.numericint').on('input', function (event) {
	  this.value = this.value.replace(/[^0-9,]/g, '');
  });
  
  
  $(document).on('click','#apply_coupon',function(){
	morphPrice();
  });
  
  $(document).on('click','.btn-use-coupon',function(){
	  var offer_coupon = $(".offer-coupon").text().trim();
	  $("#coupon").val(offer_coupon);
	  $("#popup_offer").modal("hide");
	  $("#coupon").focus();
	  morphPrice();
  });
  
  $(document).on('click','#remove_coupon',function(){
	  $(this).attr("id","apply_coupon").promise().done(function(){
		$(this).html("Apply");
		$("#coupon").val('').promise().done(function(){
		  $(this).attr('readonly', false);
		  morphPrice();
		});
	  });
  });
  
  $('#coupon').on('change',function(){
	  $("#coupon_msg").hide().html('');
	  showError("none");
  });
  
});
//end document ready

//payment wallet validations
function select_wallet(e){
	$("#bankcode").val(e);
}
	
function select_cc(e){
	$("#bankcode").val(e);
	if(e=='AMEX'){
		$("#cc_cvv").attr('maxlength', 4);
	}
	else{
		$("#cc_cvv").attr('maxlength', 3);
	}
}
	
function select_dc(e){
	$("#bankcode").val(e);
}

function select_exp_mo(e){
	$("#ccexpmon").val(e);
}
	
function select_exp_yr(e){
	$("#ccexpyr").val(e);
}
	
function reset_fields(){
	$("#bankcode").val('');
	$("#ccexpmon").val('');
	$("#ccexpyr").val('');
	$("#ccname").val('');
	$("#ccnum").val('');
	$("#ccvv").val('');
	$('#cc_type').val('').trigger('change');
	$('#cc_exp_mo').val('').trigger('change');
	$('#cc_exp_yr').val('').trigger('change');
	$("#cc_num").val('');
	$("#cc_name").val('');
	$("#cc_cvv").val('');
	$('#dc_type').val('').trigger('change');
	$('#dc_exp_mo').val('').trigger('change');
	$('#dc_exp_yr').val('').trigger('change');
	$("#dc_num").val('');
	$("#dc_name").val('');
	$("#dc_cvv").val('');
	$('#wallet_type').val('').trigger('change');
	$("#store_card").val('');
	$("#one_click_checkout").val('');
	$("#store_card").prop("checked", false);
	$("#one_click_checkout").prop("checked", false);
	$(".cardlabel_dc").hide();
	$(".cardlabel_cc").hide();
}
	
	
//Payment Form change
$( document ).ready(function() {
	//set pg value
	$(".payment-method .nav-link").on('click', function(){
		
		reset_fields();
		$(".alert").hide();	
		
		var pg = $(this).attr('id');
		$("#pg").val(pg);
		
		if((pg=='NB') || (pg=='CASH') || (pg=='UPI') || (pg=='EMI') || (pg=='NOCOSTEMI') || (pg=='CC') || (pg=='DC')){
		$("#bankcode").val('');	
		}
		
		if(pg=='WALLET'){
		$("#bankcode").val('payuw');	
		}
		
		if(pg=='AMEXZ'){
		$("#bankcode").val('AMEXZ');	
		}
		
		if(pg=='PAYTM'){
		$("#pg").val('CASH');
		$("#bankcode").val('paytm');	
		}
		
	});
	
	$('#cc_name, #dc_name').on('keyup blur',function(){
		var d =$(this).val();
		$("#ccname").val(d);
	});

	$('#cc_num, #dc_num').on('keyup blur',function(){
		var d =$(this).val();
		$("#ccnum").val(d);
	});

	$('#cc_cvv, #dc_cvv').on('keyup blur',function(){
		var d =$(this).val();
		$("#ccvv").val(d);
	});
	
	$('#savecc').on('change',function() {
        if ($(this).is(':checked')) {
           $(".cardlabel_cc").show();
		   $("#store_card").val('1');
		   $("#one_click_checkout").val('1');
        }
		else {
			$(".cardlabel_cc").hide();
			$("#store_card").val('');
			$("#one_click_checkout").val('');
		}
    });
	
	$('#savedc').on('change',function() {
        if ($(this).is(':checked')) {
           $(".cardlabel_dc").show();
		   $("#store_card").val('1');
		   $("#one_click_checkout").val('1');
        }
		else {
			$(".cardlabel_dc").hide();
			$("#store_card").val('');
			$("#one_click_checkout").val('');
		}
    });

});

$('.focusBtn').on('click',function(){
	focusBtn();
});

function focusBtn(target) {
	var target = target;
	if($(target).length){ 
		target =  target;
	}else {
		target =  "#submit_payment";
	}
	
	$(target).removeClass("highlight-btn").addClass("highlight-btn").delay(1500).queue(function(){
    	$(this).removeClass("highlight-btn").dequeue();
	});
	var top=$(target).offset().top;
	$('body,html').delay(600).animate({ scrollTop:top - 150},300);
}