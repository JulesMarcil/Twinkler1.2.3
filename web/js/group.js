/* --- Group members picture toggle --- */

$(document).ready(function() { 
	$("#second-row-big").on("click", "#toggle-button", function(){
		$(this).closest("#second-row-big").find("#pictures").slideToggle();
    	if($(this).text() === 'hide members'){
    		$(this).text('show members');	
    	}else{
    		$(this).text('hide members');
    	}
    })
});

/*--------CHARTS & TIMLINE SIZE--------*/
var activePage = document.URL.split("/").pop();
if (activePage!=="lists"){
var members_nb= balances.length;
};
var navbarHeight=$("#navbar-row").height();



window.onload =function() {
	$("#navbar-"+activePage).addClass("navbar-item");
	$('#timeline').height(Math.max($('#balance-expense-container').height(),$('#timeline-expense-container').height())-65+'px');
}

window.onresize =function() {
	$('#timeline').height(Math.max($('#balance-expense-container').height(),$('#timeline-expense-container').height())-65+'px');
}


/*--------CHARTS--------*/
if (activePage!=="lists"){
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

if (activePage!=="lists"){
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

};

var ctx = document.getElementById("balanceChart").getContext("2d");
new Chart(ctx).Bar(data,{
    scaleOverlay : false,
	scaleShowLabels : false
});
};

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


/*----chart scroll-----*/
$(document).ready(function() { 
    $("#balance-slimscroll").niceScroll();
});

$(document).ready(function() { 
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

    // --> expense modal scroll
	$(function(){
	    $('#expense-slimscroll').slimScroll({
	        height: Math.min('450',$(window).height()-120)+'px'
	    });
	});

});

/* --- AJAX FOR SELECTION MENU --- */
$(document).ready(function() {

// ---> ajax for going to settings
	$("#navbar-settings").on('click', 'a', function(e){
		e.preventDefault();
		$.get('/Twinkler1.2/web/app_dev.php/group/ajax/settings', function(response){
			$('#content-container').html(response);
			window.history.pushState("", "", 'settings');
			// rappeler les fonctions de mise en forme
			$("#balance-slimscroll").niceScroll();
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

			if (activePage!=="lists"){
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

			};

			var ctx = document.getElementById("balanceChart").getContext("2d");
			new Chart(ctx).Bar(data,{
			    scaleOverlay : false,
				scaleShowLabels : false
			});
		});
	});

// ---> ajax for going to expenses 
	$("#navbar-expenses").on('click', 'a', function(e){
		e.preventDefault();
		$.get('/Twinkler1.2/web/app_dev.php/group/ajax/expenses', function(response){
			$('#content-container').html(response);
			window.history.pushState("", "", 'expenses');
			// rappeler les fonctions de mise en forme
			$("#balance-slimscroll").niceScroll();
			$('#expense-slimscroll').slimScroll({
		        height: Math.min('450',$(window).height()-120)+'px'
		    });
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

			if (activePage!=="lists"){
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

			};

			var ctx = document.getElementById("balanceChart").getContext("2d");
			new Chart(ctx).Bar(data,{
			    scaleOverlay : false,
				scaleShowLabels : false
			});
		});
	});

// ---> ajax for going to lists 
	$("#navbar-lists").on('click', 'a', function(e){
		e.preventDefault();
		$.get('/Twinkler1.2/web/app_dev.php/group/ajax/lists', function(response){
			$('#content-container').html(response);
			window.history.pushState("", "", 'lists');
			// rappeler les fonctions de mise en forme
			// ajax create list form (copy paste from list.js (to factorize somewhere))
			$("#create-list-button").on('click','a', function(e){
				e.preventDefault();
				$.get('/Twinkler1.2/web/app_dev.php/group/new/lists', function(response){
					$('#list-menu').append(response).fadeIn();
				});
			});
			// rappel de listapp.js
			Appstart();
		});
	});

});

