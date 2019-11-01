$(document).ready(function(){
	if ($(".sidebar-menu").length > 0){
		var sidebarWidth = Math.ceil($(".sidebar-menu").width()) / 4;
		$(".sidebar-menu").css("transform-origin", 'calc(50% + '+sidebarWidth+'px + 4px) 0');
		$(".sidebar-menu").show();
	}

	//responsive height match
	if ($("[data-dk-height-match]").length > 0){
		$("[data-dk-height-match]").each(function(){
			UIkit.heightMatch($(this), {target: $(this).attr('data-dk-height-match')});
		});
	}

	//PLZ Modal
	if ($("#toggle-modal-postal-code").attr('data-active') == "false" && $("#toggle-modal-postal-code").attr('data-trigger') == "true"){
		UIkit.modal($("#modal-postal-code")).show();
	}
	UIkit.util.on("#modal-postal-code","shown",function(){
		$('input[name="plz-choice"]').focus();
	});

	//productblock
	$(document).on('click','.productblock .uk-card',function(){
		window.location.href = $(this).find('.btn-order').attr('href');
	});
});



//Product Choice Scripts
$(document).ready(function(){
	//we fetch the packages
	var packages = [];
	var url = window.location.pathname;
	$.ajax({
		url: '/shop-functions/fetchPackages',
		dataType: 'Json'
	}).done(function(response){
		console.log(response);
	});
});
