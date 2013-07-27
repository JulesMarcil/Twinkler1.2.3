$(document).ready(function() {

	// ajax create list form
	$("#create-list-button").on('click','a', function(e){
		e.preventDefault();
		$.get('/Twinkler1.2/web/app_dev.php/group/new/lists', function(response){
			$('#list-menu').append(response).fadeIn();
		});
	});

});
