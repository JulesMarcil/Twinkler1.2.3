$(document).ready(function() {

	$("#fb-root").bind("facebook:friends", function() {
		var friends = window.friends;
		for (var i=0; i<friends.length; i++){
			var friend = friends[i];
			$('#friend-table').find('tbody').append('<tr><td><img src="http://graph.facebook.com/'+friend['id']+'/picture?width=30&height=30" alt="friend" width="30px"></td><td class="name">'+friend['name']+'</td><td class="add-button"><button data-id="'+friend['id']+'" data-name="'+friend['name']+'" data-username="'+friend['username']+'" class="btn btn-small">Add</button></td></tr>');
		}
	});

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
	    height: '236px'
	});

	// ajax add friend
	$("#friend-table").on('click', 'button', function(){
		console.log('add friend');
		$(this).closest('tr').fadeOut();
		var id   = $(this).data('id');
		var name   = $(this).data('name');
		var username   = $(this).data('username');
		$. ajax ({
             type: "GET",
             url: "add/friend",
             dataType: 'json',
             data: {'id': id, 'name': name, 'username': username },
             success: function (response) {
                  	console.log(response);//iterate here the object
                  	if (response.error){
                  		$("#flash-message-block").html('<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button><p>'+response.error+'</p></div>');
                  	} else {
                  		$('#group').find('tr').first().append('<td><div class="member"><img src="http://graph.facebook.com/'+response.id+'/picture?width=100&height=100" alt="friend" class="img-circle" style="position: relative"></div></td>').fadeIn();
                  		$('#group').find('tr').last().append('<td class="member-name-row"><div class="member"><p>'+response.name+'</p></div></td>').fadeIn();
						$("#flash-message-block").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><p>You added <b>'+ response.name +'</b> to the group<p><p>Tell her/him to simply login to Twinkler to access the group</p></div>');
                  	}                  	
             }
         });
	});

	// ajax remove added member
	$("#group").on('click', '.remove-added-member', function(){
		var id = $(this).data('id');
		var user_id = $(this).data('user');
		var name = $(this).closest('.member').find('p').text();
		$(this).closest('.member').remove();
		$.get('add/remove/member/'+id+'', function(response){
			$("#flash-message-block").hide().html('<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button><p>You removed <b>'+ name +'</b> from the group<p></div>').show();;
			$('#tr-user-'+user_id).fadeIn();
		});
	});
});


