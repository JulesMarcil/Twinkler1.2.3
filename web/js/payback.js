// Loaded on payback page only, to be merged with group.js

$(document).ready(function() {

	/* --- Prefilled payback form in modal --- */
	$(".payback-table tr").on("click", function(e){
		e.preventDefault();
		var id1 = $(this).data('payer');
		var amount = $(this).data('amount');
		var id2 = $(this).data('paid');
		console.log(id1);
		console.log(amount);
		console.log(id2);
		$.get('payback/prefilled/'+id1+'/'+amount+'/'+id2, function(response){
			$("#prefilledPaybackModal").html(response).modal('show');
		});
	});

});