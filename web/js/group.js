	/*-----Function definition-------*/
	var activePage = document.URL.split("/").pop(); /*get page name*/
	var activePageHighlight=function(){  // add top and bottom border on the active page name
		$("#navbar-"+activePage).addClass("navbar-item");
	};
	var deactivePageHighlight=function(){  // add top and bottom border on the active page name
		$("#navbar-"+activePage).removeClass("navbar-item");
	};
	var addScrollOnChart=function(){
		$("#balance-slimscroll").niceScroll();
	}


	$.scrollTo = $.fn.scrollTo = function(x, y, options){
	    if (!(this instanceof $)) return $.fn.scrollTo.apply($('html, body'), arguments);

	    options = $.extend({}, {
	        gap: {
	            x: 0,
	            y: 0
	        },
	        animation: {
	            easing: 'swing',
	            duration: 600,
	            complete: $.noop,
	            step: $.noop
	        }
	    }, options);

	    return this.each(function(){
	        var elem = $(this);
	        elem.stop().animate({
	            scrollLeft: !isNaN(Number(x)) ? x : $(y).offset().left + options.gap.x,
	            scrollTop: !isNaN(Number(y)) ? y : $(y).offset().top + options.gap.y
	        }, options.animation);
	    });
	};

	/*--------CHARTS--------*/

	var graphColor=function(graphData){
		var chartColor=[];
		for(var i=0; i<graphData.length;i++){
			if(graphData[i]>=0){
				chartColor.push("rgba(168,189,68,0.5)");
			}else{			
				chartColor.push("rgba(249,126,118,0.5)");
			}
		};
		return chartColor
	}

	var members_chart=[];
	for (var i = 0; i < balances.length; i++) {
		members_chart[i]='';
	}

	var colorFill=graphColor(balances);
	var data = {
		labels : members_chart,
		datasets : [
					{
						fillColor : colorFill,
						strokeColor : "rgba(220,220,220,1)",
						data : balances
					}
					]
		}


	var loadChart=function(){

		var graphColor=function(graphData){
			var chartColor=[];
			for(var i=0; i<graphData.length;i++){
				if(graphData[i]>=0){
					chartColor.push("rgba(168,189,68,0.5)");
				}else{			
					chartColor.push("rgba(249,126,118,0.5)");
				}
			};
			return chartColor
		}
		
		var members_chart=[];
		for (var i = 0; i < balances.length; i++) {
			members_chart[i]='';

		}

		var colorFill=graphColor(balances);
		var data = {
					labels : members_chart,
					datasets : [
								{
									fillColor : colorFill,
									strokeColor : "rgba(220,220,220,1)",
									data : balances
								}
								]
					}

		var ctx = document.getElementById("balanceChart").getContext("2d");
		new Chart(ctx).Bar(data,{
		    scaleOverlay : false,
			scaleShowLabels : false
		});
	}

	/* HEADER ACTIONS */

	$(document).ready(function() { 

		/* --- Group members picture toggle --- */
		$("#second-row-big").on("click", "#toggle-button", function(){
			$(this).closest("#second-row-big").find("#pictures").slideToggle();
	    	if($(this).text() === 'hide members'){
	    		$(this).text('show members');	
	    	}else{
	    		$(this).text('hide members');
	    	}
	    });

		/* --- Group members show profile in modal --- */
		$("a[data-target=memberProfileModal]").on("click", function(e){
			e.preventDefault();
			$.get('modal/profile/'+$(this).data('id'), function(response){
				$("#memberProfileModal").html(response).modal('show');
			});
		});
	});

	window.onresize =function() {
		$('#timeline').height(Math.max($('#balance-expense-container').height(),$('#timeline-expense-container').height())-100+'px');
	}

	/* settingsStart, expenseStart, listsStart define the funciton that needs to be
	/* called on page load and in response for ajax request

	/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!SETTINGS START!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

	/*-------Settings Start----*/
	var settingsStart = function(){

		deactivePageHighlight();
		activePage = document.URL.split("/").pop(); /*get page name*/
		activePageHighlight();

		addScrollOnChart();
		loadChart();

		$('#action-bar-first-child').hover(
			function()
			{
				$('.group-edit').addClass('group-edit-hover');
			},
			function(){
				$('.group-edit').removeClass('group-edit-hover');
			}
		);

		/*--------CHARTS & TIMLINE SIZE--------*/
		if (activePage!=="lists"){
		var members_nb= balances.length;
		};
		var navbarHeight=$("#navbar-row").height();
		

	}

	/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! EXPENSE START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
	/*-------expenseStart-----*/
	var expenseStart = function(){

		deactivePageHighlight();
		activePage = document.URL.split("/").pop(); /*get page name*/
		activePageHighlight();

		addScrollOnChart();
		loadChart();

		/*--------CHARTS & TIMLINE SIZE--------*/
		if (activePage!=="lists"){
		var members_nb= balances.length;
		};
		var navbarHeight=$("#navbar-row").height();


		$('#timeline').height(Math.max($('#balance-expense-container').height(),$('#timeline-expense-container').height())-65+'px');


		/*-------TOOLTIPS--------*/
		    $(function () {
		        $("[rel='tooltip']").tooltip({placement: 'top'});
		    });


		/*-------Pinpoint buttons on timeline (date)--------*/

		$('.pinpoint-button').hover(function () {
		    this.src = 'http://twinkler.co/img/Frame/tmln-btn-hover.png';
		}, function () {
		    this.src = 'http://twinkler.co/img/Frame/tmln-btn.png';
		});

		var today=new Date();
		var dd=today.getDate();
		var mm=today.getMonth()+1;

		if(dd<10){dd='0'+dd};
		 if(mm<10){mm='0'+mm};

		$('#today-pinpoint').attr('title', dd +"/"+mm);

	    // --> expense modal scroll
		$(function(){
		    $('#expense-slimscroll').slimScroll({
		        height: Math.min('450',$(window).height()-120)+'px'
		    });
		});

	    // ---> Expense filter
	    $("#show-all-button").on("click", function(){
	    	$(".expense-block").fadeIn();
			$("#only-mine-button").removeClass("active");
			$(this).addClass("active");
	    });
	    $("#only-mine-button").on("click", function(){
	    	$(".expense-block").filter(".nottagged").fadeOut();
			$("#show-all-button").removeClass("active");
	    	$(this).addClass("active");
	    });

	    $('.edit-button').on('click', function(e){
	    	e.preventDefault();
	    	var expenseId = $(this).data('id');
	    	var editBox = $('#expense-edit-'+expenseId);
	    	if(editBox.hasClass('shown')){
	    		editBox.fadeOut();
	    		editBox.removeClass('shown');
	    	} else {
		    	$.get('expenses/edit/'+expenseId, function(response){
					editBox.html(response).hide().fadeIn();
					editBox.addClass('shown');
				});
		    }
	    })

	}

	/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! TIMELINE START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

	var timelineStart = function(){

		deactivePageHighlight();
		activePage = document.URL.split("/").pop(); /*get page name*/
		activePageHighlight();

		var input = $("#send-box").find('input');
		input.focus();

		$(document).keypress(function(e) {
		    if(e.which == 13) {
		        var message = $("#send-box").find('input').val();
				$.get('ajax/message/new?new_message='+message, function(response){
					$('#message-list').html(response);
					input.val('');
					input.focus();
					// rappeler les fonctions de mise en forme

					$("#message-list").animate({ scrollTop: 100000 }, "fast");
				});
		    }
		});

		$("#more-expenses").on('click', 'a', function(e){
			e.preventDefault();
			$.get('ajax/expenses', function(response){
				$('#content-container').html(response);
				window.history.pushState("", "", 'expenses');
				// rappeler les fonctions de mise en forme
				addScrollOnChart();
				$('#expense-slimscroll').slimScroll({
			        height: Math.min('450',$(window).height()-120)+'px'
			    });
				expenseStart();
			});
		});

		$('#message-list').slimScroll({
	        height: '280px',
	        start: 'bottom'
	    });


        function refreshMessages() { 
        	$.get('messages', function(response){
					$('#message-list').html(response);
					// rappeler les fonctions de mise en forme
					$("#message-list").animate({ scrollTop: 100000 }, "fast");
				});
        }
	    window.setInterval(refreshMessages, 30000);

	}

	/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! LOAD FUNCTION ON START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

	/* actually call the function when page is loaded for the first time */
	$(document).ready(function() { 
		if (activePage==="expenses"){
			expenseStart();
		}else if(activePage==="timeline"){
			timelineStart();
		}
	});

	/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

	/* --- AJAX FOR SELECTION MENU --- */
	$(document).ready(function() {

	// ---> ajax for going to expenses 
		$("#navbar-expenses").on('click', 'a', function(e){
			e.preventDefault();
			$.get('ajax/expenses', function(response){
				$('#content-container').html(response);
				window.history.pushState("", "", 'expenses');
				// rappeler les fonctions de mise en forme
				addScrollOnChart();
				$('#expense-slimscroll').slimScroll({
			        height: Math.min('450',$(window).height()-120)+'px'
			    });
				expenseStart();
			});
		});

	// ---> ajax for going to timeline 
		$("#navbar-timeline").on('click', 'a', function(e){
			e.preventDefault();
			$.get('ajax/timeline', function(response){
				$('#content-container').html(response);
				window.history.pushState("", "", 'timeline');
				// rappeler les fonctions de mise en forme
				// rappel de listapp.js

				deactivePageHighlight();
				activePage = document.URL.split("/").pop(); /*get page name*/
				activePageHighlight();

				timelineStart();
			});
		});

	});

