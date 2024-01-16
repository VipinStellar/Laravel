var load_script=function(){
	this.go_top=function(){
		//go to top button
		$(window).scroll(function() {
			if ($(this).scrollTop() >= 500) {	// If page is scrolled more than 50px
				$('#goTop').fadeIn(200);	// Fade in the arrow
			} else {
				$('#goTop').fadeOut(200);	// Else fade out the arrow
			}
		});
		$('#goTop').on('click',function() {	// When arrow is clicked
			$('body,html').animate({
				scrollTop : 0	// Scroll to top of body
			}, 500);
			$(window).scroll(function() {
			if ($(this).scrollTop() >= 500) {	// If page is scrolled more than 50px
				$('#goTop').fadeIn(200);	// Fade in the arrow
			} else {
				$('#goTop').fadeOut(200);	// Else fade out the arrow
			}
			});
		});
	};
	this.goToSection=function(){
		$('.go_ahead').on('click',function(e){
			e.preventDefault();
			var elem=$(this).attr('data-where');
			var top=$('#'+elem).offset().top;
			$('body,html').animate({ scrollTop:top - 75},600);
			return false;
		});
	};
	this.accordion=function(){
		//accordion icon toggle
		var acc = $(".accordion");
		var i;
		for (i = 0; i < acc.length; i++) {
		  acc[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			$(panel).slideToggle("fast");
		  });
		}
	};
	this.scrollMenu=function(){
		//on scroll actionbar update
		var top=$('.actionbar-scroll-limit').position().top + $('.actionbar-scroll-limit').outerHeight(true);
		$(window).scroll(function() {
		  if ($(this).scrollTop() >= top) { // If page is scrolled more than this
			  $('#actionbar').addClass('actionbar-fixed');    // add compact navbar class
		  } else {
			  $('#actionbar').removeClass('actionbar-fixed');  // else remove compact navbar class
		  }
		});
	};
	this.screenTabAnimate=function(){
		// Tab-Pane change function
		var tabChange = function(){
			var tabs = $('.screen_tab .nav-tabs > a');
			var active = tabs.filter('.active');
			var next = active.next('a').length? active.next('a') : tabs.filter(':first-child');
			// Use the Bootsrap tab show method
			next.tab('show')
		}
		// Tab Cycle function
		var tabCycle = setInterval(tabChange, 3000);
		var t;
		// Tab click event handler
		$(function(){
			$('.screen_tab .nav-tabs a').on('click',function(e) {
				clearTimeout(t);
				e.preventDefault();
				// Stop the cycle
				clearInterval(tabCycle);
				// Show the clicked tabs associated tab-pane
				$(this).tab('show')
				// Start the cycle again in a predefined amount of time
				t =setTimeout(function(){
					tabCycle = setInterval(tabChange, 3000)
				}, 10000);
			});
		});
	};
	this.countNumber=function(){
		var a = 0;
		$(window).scroll(function() {
		  if( $('#counter').length ){
		   var oTop = $('#counter').offset().top - window.innerHeight;
		   if (a == 0 && $(window).scrollTop() > oTop) {
			   $('.counter-value').each(function() {
				  var $this = $(this),
				  countTo = $this.attr('data-count');
				  $({countNum: $this.text()}).animate({ countNum: countTo},
				  {
				  duration: 3000, easing: 'swing', step: function() {
				  $this.text(Math.floor(this.countNum));},
				  complete: function() {
				  $this.text(this.countNum);
				  }});
			   });
		   a = 1;
		   }
		  }
		});
	};
	this.incrementCounter=function(){
		var trigger_executed = false;
		$(window).scroll(function() {
			if($('.trigger-counter').length && !trigger_executed){if ($(this).scrollTop() >= $('.trigger-counter').position().top) {	// If page is scrolled to this div
				$(".incremental-counter").incrementalCounter({
					"digits": 7
				});
				$('.trigger-counter').removeClass('trigger-counter');
				trigger_executed = true;
			}}
		});
		
	};
	this.multiItemCarousel=function(carouselId,items=4){
		//multi item carousel
		var carouselId = carouselId.trim();
		if($('#'+carouselId).length){ 
			$('#' + carouselId + ' .carousel-item').eq(0).addClass('active');
			$('#'+carouselId).on('slide.bs.carousel', function (e) {
				var direction = e.direction;
				var $e = $(e.relatedTarget);
				var idx = $e.index();
				var itemsPerSlide = direction == 'right' ? items + 1 : items;
				var totalItems = $('#' + carouselId + ' .carousel-item').length;
			 
				if (idx >= totalItems-(itemsPerSlide-1)) {
					var it = itemsPerSlide - (totalItems - idx);
					for (var i=0; i<it; i++) {
						// append slides to end
						if (e.direction=="left") {
							$('#' + carouselId + ' .carousel-item').eq(i).appendTo('#' + carouselId + ' .carousel-inner');
						}
						else {
							$('#' + carouselId + ' .carousel-item').eq(0).appendTo('#' + carouselId + ' .carousel-inner');
						}
					}
				}
			});
		}
	};
	this.dpSlider=function(dpSliderId){
		//dp slider
		var dpSliderId = dpSliderId.trim();
		function detect_active(dpSliderId){
			// get active
			var get_active = $('#' + dpSliderId + ' .dp-item:first-child').data('class');
			$('#' + dpSliderId + ' .dp-dots li').removeClass('active');
			$('#' + dpSliderId + ' .dp-dots li[data-class='+ get_active +']').addClass('active');
		}
		
		$('#' + dpSliderId + ' .dp-next').on('click', function(){
			var total = $('#' + dpSliderId + ' .dp-item').length;
			$('#' + dpSliderId + ' .dp-item:first-child').hide().appendTo('#' + dpSliderId + ' .dp-slider').fadeIn();
			$.each($('#' + dpSliderId + ' .dp-item'), function (index, dp_item) {
				$(dp_item).attr('data-position', index + 1);
			});
			detect_active(dpSliderId);
		});
		$('#' + dpSliderId + ' .dp-prev').on('click', function(){
			var total = $('#' + dpSliderId + ' .dp-item').length;
			$('#' + dpSliderId + ' .dp-item:last-child').hide().prependTo('#' + dpSliderId + ' .dp-slider').fadeIn();
			$.each($('#' + dpSliderId + ' .dp-item'), function (index, dp_item) {
				$(dp_item).attr('data-position', index + 1);
			});
			detect_active(dpSliderId);
		});
		$('#' + dpSliderId + ' .dp-dots li').on('click', function(){
			$('#' + dpSliderId + ' .dp-dots li').removeClass('active');
			$(this).addClass('active');
			var get_slide = $(this).attr('data-class');
			//console.log(get_slide);
			$('#' + dpSliderId + ' .dp-item[data-class=' + get_slide + ']').hide().prependTo('#' + dpSliderId + ' .dp-slider').fadeIn();
			$.each($('#' + dpSliderId + ' .dp-item'), function (index, dp_item) {
				$(dp_item).attr('data-position', index + 1);
			});
		});
	
		$('body').on("click", '#' + dpSliderId + ' .dp-item:not(:first-child)', function(){
			var get_slide = $(this).attr('data-class');
			//console.log(get_slide);
			$('#' + dpSliderId + ' .dp-item[data-class=' + get_slide + ']').hide().prependTo('#' + dpSliderId + ' .dp-slider').fadeIn();
			$.each($('#' + dpSliderId + ' .dp-item'), function (index, dp_item) {
				$(dp_item).attr('data-position', index + 1);
			});
			detect_active(dpSliderId);
		});
	};
	this.tabPanel=function(active=0){
		//Tab panel without id
		$('.custom_tab .nav-tabs > .nav-link').on('click', function(e){
		  var tab  = $(this),
			  tabIndex = tab.index(),
			  tabPanel = $(this).closest('.custom_tab'),
			  tabPane = tabPanel.find('.tab-pane').eq(tabIndex);
		  tabPanel.find('.nav-link.active').removeClass('active');
		  tabPanel.find('.tab-pane.active.show').removeClass('active show')
		  tab.addClass('active');
		  tabPane.addClass('active show');
		});
	};
	this.tabPanelDefault=function(tabId='s',active=1){
		//Set Default Tab panel with tab index
		var tabId = $.trim(tabId);
		if($('#'+tabId).length){ 
			var tab = $('#' + tabId + ' > .nav-tabs > .nav-link').eq(active - 1),
			tabIndex = tab.index(),
			tabPanel = $(tab).closest('.custom_tab'),
			tabPane = tabPanel.find('.tab-pane').eq(tabIndex);
			tabPanel.find('.nav-link.active').removeClass('active');
		  tabPanel.find('.tab-pane.active.show').removeClass('active show')
		  tab.addClass('active');
		  tabPane.addClass('active show');
		}
	};
	this.cycleText=function(text,elem,textDuration){
		//cycleText function
        var i = 0, $div = $(elem);
		setInterval(function ()
		{
			$div.fadeOut(500,function ()
			{
				$div.html(text[i++ % text.length]).fadeIn(1000);
			});
		}, textDuration);
	};
	
	this.loadSideButtons=function(){
		$(".handle").mouseover(function() {
			$(this).css({
				"right": "0"
			});
			$(this).removeClass("toggled");
		});
		$(".handle").mouseout(function() {
			$(this).css({
				"right": "-200px"
			});
			$(this).addClass("toggled");
		});	
	
	};
	this.hoverSlideBox=function(){
		//Infobox Hover Slide toggle 
		$(".vc-infobox-wrap").hover(function(){
		$(".vc-infobox-cont",this).animate({bottom: '0'}, "fast");
		},function(){
		$(".vc-infobox-cont",this).animate({bottom: '-100%'}, "fast");
		});
	};
	this.visibleSlideBox=function(){
		//Infobox Hover Slide toggle 
		$(".slidebox-wrap").hover(function(){
		$(".slidebox-cont",this).animate({bottom: '0'}, "fast");
		},function(){
		var h = $(this).height();
		h = 50 - h;
		$(".slidebox-cont",this).animate({bottom: h}, "fast");
		});
	};
	this.sendExeMail=function(){
		$('#send_exe_mail').on('click',function(){
			
			$("#form_exe_mail_cont #exe_mail_error").html('').hide();
			var ref=$("#form_exe_mail #ref").val();
			var email=$("#form_exe_mail #exe_email").val();
			email = email.replace(/\s+/g, '');
			var atpos=email.indexOf("@");
			var dotpos=email.lastIndexOf(".");
			var dataString = {"qemail": email, "ref": ref, "request": "send_exe_mail" };
			if(email != ''){
			if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length){
				$("#form_exe_mail_cont #exe_mail_error").show().html("Please fill a valid Email Id");
				return false;
			}else{
			$('#send_exe_mail').attr('disabled', true);
			$.ajax({
				 type: "POST",
				 dataType: "json",
				 url: "/submit_ppc-download.php",			 
				 data: dataString,
				 cache: false,
				 
				 success: function(data) {	
				 var status = data['status'];
				 var msg = data['message'];
				 if(status == 'success'){
					 $('#form_exe_mail_cont').html(msg);
				 }
				 else if(status == 'error'){
					$("#form_exe_mail_cont #exe_mail_error").show().html("Error!!! Please try again");
				 }
				 else {
					alert(status);
				 }
				 },
				 error: function (jqXHR, status, err) {
					alert(err);
				  },
				  complete: function (jqXHR, status) {
					$('#send_exe_mail').attr('disabled', false);
				  }
			 });
			}
			}else{
				$("#form_exe_mail_cont #exe_mail_error").show().html("Please fill a valid Email Id");
				return false;
			}
    
		});
	};
	this.saveJourney=function(){
		var dataString = {"request": "save_journey" };

		$.ajax({
			 type: "POST",
			 dataType: "json",
			 url: "/load_more.php",			 
			 data: dataString,
			 cache: false,
			 
			 success: function(data) {
			 },
			 error: function (jqXHR, status, err) {
				alert(err);
			 },
			 complete: function (jqXHR, status) {
				
			 }
		 });
	};
};

//Load Global Functions Scripts
$(function(){
	"use strict";
	var loadscript=new load_script();
	loadscript.goToSection();
	loadscript.accordion();
	loadscript.tabPanel();
});
