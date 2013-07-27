$(document).ready(function() {

	// overwrite contains function
	$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    	return function( elem ) {
        	return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    	};
	});

	// show only facebook friends matching input
	$("#friend-input").keyup(function() {
		input_content = $(this).val();
		$('#friend-table .name').closest("tr").show().find(".name").not(':contains(' + input_content + ')').closest("tr").hide();
	});
	// Scroll in friend table
	$("#friend-table").slimScroll({
	    height: '160px'
	});

	// ajax add member
	$(".add-members").find('form').on('submit', function(e){
		e.preventDefault();
		var form = $(this);
		var name = form.find('#form_name').val();
		var email = form.find('#form_email').val();
		$.ajax(form.attr('action'),{
			type: form.attr('method'),
			data: form.serialize(),
			success: function(response){				
				$('#group').append(response).fadeIn();
				if(email === ''){
					$("#flash-message-block").html('<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>You added <b>'+ name +'</b> to the group<p></div>');
				}else{
					$("#flash-message-block").html('<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>You added <b>'+ name +'</b> to the group<p><p>We will send him an invitation email to <b>'+ email +'</b> when you validate</p></div>');
				}
				form.trigger('reset').find('#form_name').focus();
			}
		});
	});

	// ajax add friend
	$("#friend-table").find('button').on('click', function(){
		$(this).closest('tr').fadeOut();
		var id = $(this).data('id');
		$.get('/Twinkler1.2/web/app_dev.php/group/add/friend/'+id+'', function(response){
			$('#group').append(response).fadeIn();
			var name = $('#group').find('p').last().data('name');
			var email = $('#group').find('p').last().data('email');
			$("#flash-message-block").html('<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><p>You added <b>'+ name +'</b> to the group<p><p>We will send him an invitation email to <b>'+ email +'</b> when you validate</p></div>');
		});
	});

	// ajax remove added member
	$("#group").on('click', '.remove-added-member', function(){
		var id = $(this).data('id');
		var user_id = $(this).data('user');
		var name = $(this).closest('.member').find('p').text();
		$(this).closest('.member').remove();
		$("#flash-message-block").hide().html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><p>You removed <b>'+ name +'</b> from the group<p></div>').show();;
		$('#tr-user-'+user_id).fadeIn();
		$.get('/Twinkler1.2/web/app_dev.php/group/add/remove/member/'+id+'', function(response){
		});
	});
});


