$(document).ready(function() {

	/* --- Feedback Modal--- */
	$("a[data-target=feedbackModal]").on("click", function(e){
		e.preventDefault();
		$.get('/Twinkler1.2.3/web/app_dev.php/feedback', function(response){
			$("#feedbackModal").html(response).modal('show');
			feedbackActions();
		});
		$.get('/feedback', function(response){
			$("#feedbackModal").html(response).modal('show');
			feedbackActions();
		});
	});

});