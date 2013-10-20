/*-----Function definition-------*/
var activePage = document.URL.split("/").pop(); /*get page name*/
var activePageHighlight=function(){  // add top and bottom border on the active page name
	$("#navbar-"+activePage).parent().addClass("navbar-item");
};
var deactivePageHighlight=function(){  // add top and bottom border on the active page name
	$("#navbar-"+activePage).parent().removeClass("navbar-item");
};
var addScrollOnChart=function(){
	$("#balance-slimscroll").niceScroll();
};
/*
$('#group-dropdown-span').hover(function() {
	$(this).find('.dropdown-menu').first().stop(true, true).delay(0).show();
}, function() {
	$(this).find('.dropdown-menu').first().stop(true, true).delay(0).hide();
});
*/

var expenseSlimscroll = function(height){
	$('#expense-slimscroll').slimScroll({
		height: Math.min(height,$(window).height()-120)+'px'
	});
}

/*--------CHARTS--------*/

var getChart=function(){
	var max=Math.max.apply( Math, balances );
	var min=Math.min.apply( Math, balances );
	max=Math.max(max,-min);

	for (var i=0;i<members.length;i++){


		var newRow = document.createElement("tr");
		newRow.setAttribute("id", i);
		if (balances[i]<0){
			$('#'+i).html('<td id="balance-owner"></td><td class="negative-balance"><div style="position:relative"><div class="neg-bar"></div></div></td><td class="positive-balance"><div></div></td>');
			$('#'+i+' #balance-owner').html(members[i] +'</br>'+ currency +' '+ balances[i]);
			$('#'+i+' #balance-owner').addClass('neg');
			$('#'+i+' .pos-bar').width(0+'px');
			$('#'+i+' .neg-bar').animate(
				{ width: balances[i]/max*(-90)+'%' }, {
					duration: 800,
				}); 
		}else{
			$('#'+i).html('<td id="balance-owner"></td><td class="negative-balance"><div></div></td><td class="positive-balance"><div style="position:relative"><div class="pos-bar"></div></div></td>');
			$('#'+i+' #balance-owner').html(members[i] +'</br>'+ currency +' '+  balances[i]);
			$('#'+i+' #balance-owner').addClass('pos');
			$('#'+i+' .neg-bar').width(0+'px');
			$('#'+i+' .pos-bar').animate(
				{ width: balances[i]/max*90+'%' }, {
					duration: 800,
				}); 
		};

		if((i % 2 ==0)){
			$('#'+i+' .positive-balance').css({"background-color":"rgb(250,250,250)"});
			$('#'+i+' .negative-balance').css({"background-color":"rgb(250,250,250)"});
		}

		$('#balance-table').append(newRow);
	};
	$('#blce-tbl').height($('#balance-table').height()+10);
}


/* HEADER ACTIONS */

$(document).ready(function() { 

	/* --- Group members picture toggle --- */
	$("#toggle-button").click(function(){

		$('.tooltip').hide();
		$("#second-row-big").slideToggle();
		if($(this).hasClass("icon-collapse-top")){
			$(this).removeClass("icon-collapse-top");
			$(this).addClass("icon-collapse");	
		}else{
			$(this).addClass("icon-collapse-top");
			$(this).removeClass("icon-collapse");	
		}
	});

	/* --- Group members show profile in modal --- */
	$("a[data-target=memberProfileModal]").on("click", function(e){
		e.defaultPrevented;
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

    // ---> Expense filter
    $('#filter-button-group').on('click', '#filter-button', function(e){
    	e.preventDefault();
    });

    $('#unread-2').hide();
    $("#show-all-button").on("click", function(e){
    	e.preventDefault();
    	$('.navbar-unread').hide();
    	$('#unread-1').show();
    	$(".expense-row").fadeIn();
    	$("#only-mine-button").removeClass("active");
    	$(this).addClass("active");
    });
    $("#only-mine-button").on("click", function(e){
    	e.preventDefault();
    	$('.navbar-unread').hide();
    	$('#unread-2').show();
    	$(".expense-row").filter(".nottagged").fadeOut();
    	$("#show-all-button").removeClass("active");
    	$(this).addClass("active");
    });

    // ---> Edit/Remove button
    $('.edit-button').on('click', function(e){
    	e.defaultPrevented;
    	console.log('ok1');
    	var expenseId = $(this).data('id');
    	var editBox = $('#expense-edit-'+expenseId);


    	if($('#edit-expense-'+expenseId).css("display") == "none"){
    		console.log('ok3: '+expenseId);
    		console.log('ok3: '+editBox);
    		$('#edit-expense-'+expenseId).removeClass('hidden');
    		$.get('expenses/edit/'+expenseId, function(response){
    			$('#edit-expense-'+expenseId).html(response).hide().fadeIn();
    			editBox.addClass('shown');
    		});
    	} else {
    		console.log('ok2');
    		$('#edit-expense-'+expenseId).slideToggle();
    	}
    })

    expenseSlimscroll(445);

   
    $('[data-toggle="checkbox"]').each(function () {
    	$(this).checkbox();
    });

     $("#select-all-button").click(function(){

    	$(".user-checkbox").prop("checked", "checked");
    });


    $("#deselect-all-button").click(function(){
    	$(".user-checkbox").prop("checked", false);
    });
}

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! CHAT START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

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
		e.defaultPrevented;
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
	getChart();

	$('#action-bar-first-child').hover(
		function()
		{
			$('.group-edit').addClass('group-edit-hover');
		},
		function(){
			$('.group-edit').removeClass('group-edit-hover');
		}
		);

	// --> hover and click on payback table

	$('#my-settlement .payback-table tr').hover(
		function(){
			$(this).css({"background-color":"rgb(250,250,250)"});
			$(this).find('#payback-cta').animate(
				{ left: 0 }, {
					duration: 100,
				}); 
		},
		function(){
			$(this).css({"background-color":"#fff"});
			$(this).find('#payback-cta').animate(
				{ left: -300 }, {
					duration: 100,
				}); 
		}
		);

	/* --- Prefilled payback form in modal --- */
	$(".payback-table tr").on("click", function(e){
		e.defaultPrevented;
		var id1 = $(this).data('payer');
		var amount = $(this).data('amount');
		var id2 = $(this).data('paid');
		$.get('payback/new/'+id1+'/'+amount+'/'+id2, function(response){
			$("#modal").html(response).modal('show');
		});
	});

	/* --- New payback form in modal --- */
	$("#new-payback").on("click", function(e){
		e.defaultPrevented;
		$.get('payback/new/0/0/0', function(response){
			$("#modal").html(response).modal('show');
		});
	});

	/* --- Send summary modal --- */
	$("#send-summary").on("click", function(e){
		e.defaultPrevented;
		$.get('summary/modal', function(response){
			$("#modal").html(response).modal('show');
		});
	});

    // Height set
    $('#page-body').height(Math.max($('#page-body').height(),$('#my-settlement').height()+$('#settlement').height()+$('header').height()+60));  

    $('#remove-member').on('click', function(e){
    	$('#remove-member-box').slideToggle();
    	$('#edit-group-box').slideUp();
    	$('#close-group-box').slideUp();
    });
    $('#edit-group').on('click', function(e){
    	$('#remove-member-box').slideUp();
    	$('#edit-group-box').slideToggle();
    	$('#close-group-box').slideUp();
    });
    $('#close-group').on('click', function(e){
    	$('#remove-member-box').slideUp();
    	$('#edit-group-box').slideUp();
    	$('#close-group-box').slideToggle();
    });
    $('.cancel-action').on('click', function(e){
    	$('#remove-member-box').slideUp();
    	$('#edit-group-box').slideUp();
    	$('#close-group-box').slideUp();
    });
}

/*!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! LOAD FUNCTION ON START !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

/* actually call the function when page is loaded for the first time */
$(document).ready(function() { 
	if (activePage==="expenses"){
		expenseStart();
		getChart();
	}else if(activePage==="chat"){
		chatStart();
	}else if(activePage==="dashboard"){
		dashboardStart();
		getChart();
	};

	$('#hide-tooltip').tooltip();
});

/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

/* --- AJAX FOR SELECTION MENU --- */
$(document).ready(function() {

	// ---> ajax for going to expenses 
	$("#navbar-expenses-link").on('click', function(e){
		e.preventDefault();
		$('#content-container').fadeOut();
		$('#spinner').fadeIn();
		$.get('ajax/expenses', function(response){
			$('#spinner').fadeOut();
			$('#content-container').html(response).fadeIn();
			window.history.pushState("", "", 'expenses');
			// rappeler les fonctions de mise en forme
			addScrollOnChart();
			expenseStart();
			getChart();
		});
	});

	// ---> ajax for going to chat 
	$("#navbar-chat-link").on('click', function(e){
		e.defaultPrevented;
		$('#content-container').fadeOut();
		$('#spinner').fadeIn();
		$.get('ajax/chat', function(response){
			$('#spinner').fadeOut();
			$('#content-container').html(response).fadeIn();
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
	$("#navbar-dashboard-link").on('click', function(e){
		e.defaultPrevented;
		$('#content-container').fadeOut();
		$('#spinner').fadeIn();
		$.get('ajax/dashboard', function(response){
			$('#spinner').fadeOut();
			$('#content-container').html(response).fadeIn();
			window.history.pushState("", "", 'dashboard');
			// rappeler les fonctions de mise en forme
			// rappel de listapp.js

			deactivePageHighlight();
			activePage = document.URL.split("/").pop(); /*get page name*/
			activePageHighlight();

			dashboardStart();
			getChart();
		});
	});
});

