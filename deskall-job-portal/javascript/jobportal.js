$(document).ready(function(){
	$(document).on("click","[data-edit-offer]",function(){
		var id = $(this).attr('data-edit-offer');
		$.ajax({
			url: window.location.pathname+'EditJobOffer',
			dataType: 'html',
			data: {OfferId: id}
		}).done(function(response){
			$("#Form_JobOfferForm").load(window.location.pathname+'EditJobOffer #Form_JobOfferForm',function(){
				Uikit.toggle($(".toggle-new-offer")).toggle();
			});
		});
	});
	$(document).on("click","[data-delete]",function(){
		var id = $(this).attr('data-delete');
		var modal = $("#delete-modal");
		modal.find('a').attr('href',window.location.pathname+'/bewerbung-loeschen/'+id);
		UIkit.modal(modal).show();
	});
});