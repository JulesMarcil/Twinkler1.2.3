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
};


$('#group-dropdown-span').hover(function() {
	$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideDown();
}, function() {
	$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideUp()
});

/*--------CHARTS--------*/

var getChart=function(){
	var max=Math.max.apply( Math, balances );
	console.log(max);
	for (var i=0;i<members.length;i++){


            var newRow = document.createElement("tr");
		    newRow.setAttribute("id", members[i]);
            if (balances[i]<0){
            $('#'+members[i]).html('<td id="balance-owner"></td><td class="negative-balance"><div><div class="neg-bar"></div></div></td><td class="positive-balance"><div></div></td>');
            	$('#'+members[i]+' #balance-owner').html(members[i] +'</br>'+ currency +' '+ balances[i]);
            	$('#'+members[i]+' #balance-owner').addClass('neg');
            	$('#'+members[i]+' .pos-bar').width(0+'px');
		        $('#'+members[i]+' .neg-bar').animate(
		            { width: balances[i]/max*(-90)+'%' }, {
		             duration: 800,
		         }); 
            }else{
            	$('#'+members[i]).html('<td id="balance-owner"></td><td class="negative-balance"><div></div></td><td class="positive-balance"><div><div class="pos-bar"></div></div></td>');
            	$('#'+members[i]+' #balance-owner').html(members[i] +'</br>'+ currency +' '+  balances[i]);
            	$('#'+members[i]+' #balance-owner').addClass('pos');

            	$('#'+members[i]+' .neg-bar').width(0+'px');
		        $('#'+members[i]+' .pos-bar').animate(
		            { width: balances[i]/max*90+'%' }, {
		             duration: 800,
		         }); 
            };

            if((i % 2 ==0) && (i!=0)){
            	$('#'+members[i]+' .positive-balance').css({"background-color":"rgb(250,250,250)"});
		        $('#'+members[i]+' .negative-balance').css({"background-color":"rgb(250,250,250)"});
            }

		$('#balance-table').append(newRow);
	};
	$('#blce-tbl').height($('#balance-table').height()+10);
}


/* HEADER ACTIONS */

$(document).ready(function() { 

	/* --- Group members picture toggle --- */
	$("#toggle-button").click(function(){
		$("#second-row-big").slideToggle();
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

/* settingsStart, expenseStart, listsStart define the funciton that needs to be
/* called on page load and in response for ajax request

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! EXPENSE START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

var expenseStart = function(){

	deactivePageHighlight();
	activePage = document.URL.split("/").pop(); /*get page name*/
	activePageHighlight();

	addScrollOnChart();
	getChart();

	/*--------CHARTS & TIMLINE SIZE--------*/
	if (activePage!=="lists"){
		var members_nb= balances.length;
	};
	var navbarHeight=$("#navbar-row").height();

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

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! chat START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

var chatStart = function(){

	deactivePageHighlight();
	activePage = document.URL.split("/").pop(); /*get page name*/
	activePageHighlight();

	var input = $("#send-box").find('input');

	function refreshMessages() {
		rowCount=$('#chat-table tr').length
		if(activePage=="chat"){
			$.get('messages', function(response){
				$('#message-list').html(response);
			// rappeler les fonctions de mise en forme
			if(rowCount!=$('#chat-table tr').length){
				$("#message-list").animate({ scrollTop: 100000 }, "slow");
			}
		});
		}
	}
	window.setInterval(refreshMessages, 10000);

	$(document).keypress(function(e) {
		if(e.which == 13) {
			var message = $("#send-box").find('input').val();
			$.get('ajax/message/new?new_message='+message, function(response){
				$('#message-list').html(response);
				input.val('');
				input.focus();
				// rappeler les fonctions de mise en forme

				var rowCount=$('#chat-table tr').length

				if(rowCount < 7){		
					$('#message-list').slimScroll({
						height: (rowCount+1)*($('#chat-table tr').height()+2),
					});
					$('#chat-container').height(40+(rowCount)*($('#chat-table tr').height()+2)+'px');
					$('#chat-container .slimScrollDiv').height((rowCount)*($('#chat-table tr').height()+2)+'px');
					$('#message-list').height((rowCount)*($('#chat-table tr').height()+2)+'px');

				}

				$("#message-list").animate({ scrollTop: 100000 }, "slow");
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
		height: Math.min('280',$('#message-list').height())+'px',
		start: 'bottom'
	});
}

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! DASHBOARD START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

var dashboardStart = function(){

	deactivePageHighlight();
	activePage = document.URL.split("/").pop(); /*get page name*/
	activePageHighlight();

	$('#action-bar-first-child').hover(
		function()
		{
			$('.group-edit').addClass('group-edit-hover');
		},
		function(){
			$('.group-edit').removeClass('group-edit-hover');
		}
	);
}


/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! LOAD FUNCTION ON START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

/* actually call the function when page is loaded for the first time */
$(document).ready(function() { 

	jQuery(function($) {
		function fixDiv() {

			var oritop=55;
			if($("#toggle-button").text() === 'hide members'){
				oritop=55;	
			}else{
				oritop=0;
			}
			
			var $cache = $('#navbar-row'); 
			var $cacheItem = $('#navbar-expenses');
			if ($(window).scrollTop() > oritop && ($(document).height()-32>$(window).height())) {
				$("#navbar-"+activePage).css({'border-top-left-radius':'0px','border-top-right-radius':'0px'});
				$cache.addClass("navbar-scroll");
				$cache.css({'position': 'fixed', 'top': '40px','z-index':'1'}); 
				$( "#toggle-button" ).hide();
			}
			else{
				$cache.css({'position': 'relative', 'top': 'auto','z-index':'0'});
				$cache.removeClass("navbar-scroll");
				$("#navbar-"+activePage).css({'border-top-left-radius':'4px','border-top-right-radius':'4px'});
				$( "#toggle-button" ).show();
			}
		}
		$(window).scroll(fixDiv);
		fixDiv();
	}); 

	if (activePage==="expenses"){
		expenseStart();
		getChart();
	}else if(activePage==="chat"){
		chatStart();
	}else if(activePage==="dashboard"){
		dashboardStart();
	}
});

$(window).load(function() {
	if (activePage==="expenses"){
		getChart();
	}else if(activePage==="dashboard"){
		
	}
});
/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

/* --- AJAX FOR SELECTION MENU --- */
$(document).ready(function() {

	// ---> ajax for going to expenses 
	$("#navbar-expenses").on('click', function(e){
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
			getChart();
		});
	});

	// ---> ajax for going to chat 
	$("#navbar-chat").on('click', function(e){
		e.preventDefault();
		$.get('ajax/chat', function(response){
			$('#content-container').html(response);
			window.history.pushState("", "", 'chat');
			// rappeler les fonctions de mise en forme
			// rappel de listapp.js

			deactivePageHighlight();
			activePage = document.URL.split("/").pop(); /*get page name*/
			activePageHighlight();

			chatStart();
		});
	});

	// ---> ajax for going to dashboard
	$("#navbar-dashboard").on('click', function(e){
		e.preventDefault();
		$.get('ajax/dashboard', function(response){
			$('#content-container').html(response);
			window.history.pushState("", "", 'dashboard');
			// rappeler les fonctions de mise en forme
			// rappel de listapp.js

			deactivePageHighlight();
			activePage = document.URL.split("/").pop(); /*get page name*/
			activePageHighlight();

			dashboardStart();
		});
	});
});

