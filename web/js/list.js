$(document).ready(function() {

	// ajax create list form
	$("#create-list-button").on('click','a', function(e){
		e.preventDefault();
		$.get('/Twinkler1.2.3/web/app_dev.php/group/new/lists', function(response){
			$('#list-menu').append(response).fadeIn();
		});
	});
});



window.onload = (function(){
	$('.icon-remove-sign').mouseover(function(){
		$(this).css('color','#fb786b');
	});

	$('.icon-remove-sign').mouseleave(function(){
		$(this).css('color','#5486C6');
	});

	$('.item').click(function(){
		$(this).css('border-top','1px solid #A8BD44');
		$(this).css('border-bottom','1px solid #A8BD44');
		$(this).animate({backgroundColor: 'rgb(214,222,155)'}, 'fast');
		$(this).animate({backgroundColor: '#fff'}, 'fast');
		$(this).css('border-top','1px solid #DED8D6');
		$(this).css('border-bottom','1px solid #DED8D6');
	});

		$('.item').mouseover(function(){
	$(this).find('.remove').stop().show();
	});

	$('.item').mouseleave(function(){
	$(this).find('.remove').hide();
	});

});

