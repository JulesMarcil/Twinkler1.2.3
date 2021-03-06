$(document).ready(function() { 

	$('#group-dropdown-span').hover(function() {
		$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideDown();
	}, function() {
		$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideUp()
	});

	// spinner when group clicked
	$('.group-link').on('click', function(e){
		var id = $(this).data('id');
		$('.group-spinner').fadeOut();
		$('#spinner-'+id).fadeIn();
	})

	//User actions
	$('#profile-actions').on('click', '#profile-gear', function(e){
    	e.preventDefault();
    });

});

$('#group-dropdown-span').hover(function() {
	$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).show();
}, function() {
	$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).hide()
});
