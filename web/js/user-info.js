$(document).ready(function() { 

	/* --- Feedback Modal--- */
	$("a[data-target=feedbackModal]").on("click", function(e){
		e.preventDefault();
		$.get('feedback', function(response){
			$("#feedbackModal").html(response).modal('show');
			feedbackActions();
		});
	});


	$('#group-dropdown-span').hover(function() {
		$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideDown();
	}, function() {
		$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideUp()
	});
});




function feedbackActions(){

}