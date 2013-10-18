$(document).ready(function() { 

	$('#group-dropdown-span').hover(function() {
		$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideDown();
	}, function() {
		$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).slideUp()
	});

	// spinner when group clicked
	$('.group-link').on('click', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		$('.group-spinner').fadeOut();
		$('#spinner-'+id).fadeIn();
	})
});

$('#group-dropdown-span').hover(function() {
	$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).show();
}, function() {
	$('.navbar .dropdown').find('.dropdown-menu').first().stop(true, true).delay(0).hide()
});


$("select").selectpicker({style: 'btn', menuStyle: 'dropdowN'});
