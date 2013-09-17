$(document).ready(function() { 

	/* --- Feedback Modal--- */
	$("a[data-target=feedbackModal]").on("click", function(e){
		e.preventDefault();
		$.get('feedback', function(response){
			$("#feedbackModal").html(response).modal('show');
			feedbackActions();
		});
	});
});

function feedbackActions(){

}