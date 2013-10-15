$(document).ready(function() {

	// ---> Get token
	$("#get-token").on('click', function(e){
		e.preventDefault();
		$('#token').html('<p>Access token = ... </p>');
		$.getJSON('s-money/oauth/token', function(response){
			console.log(response);
			$('#access_token_1').attr('value', response.access_token);
			$('#access_token_2').attr('value', response.access_token);
			$('#access_token_3').attr('value', response.access_token);
			$('#access_token_4').attr('value', response.access_token);
		});
	});

	// ---> Send Money
	$("#send-money").on('click', function(e){
	});

});