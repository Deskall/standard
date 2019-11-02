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
	var products =[]; 
	var url = window.location.pathname;
	$.ajax({
		url: '/shop-functions/fetchPackages',
		dataType: 'Json'
	}).done(function(response){
		packages = response;
	});


	//Handle the product slider
	$(document).on("click",".category .uk-slider-items li",function(){
		var slider = $(this).parents('.uk-slider');
		var index = parseInt($(this).attr('data-index')) - 1;
		UIkit.slider(slider).show(index);
		UIkit.util.on(slider,'itemshown',function(){
			slider.parents('.category').find('[data-product-choice]').val($(this).attr('data-value')).trigger('change');
		});
	});

	$(document).on("change","[data-product-choice]",function(){
		UpdateOrder();
	});

	

	function UpdateOrder(){
		products = [];
		var package;

		var chosenPackage;
		$('.category .slider-products .uk-slider-items li.uk-active').each(function(){
			products.push($(this).attr('data-value'));
		});
		console.log(products);
		$.each(packages,function(i,v){
			console.log(v['Products']);

			if (v['Products'] == products){
				console.log(v);
				return false;
			}
	      // var same = v['Products'].every(function(element, index) {
	      //  return $.inArray(element,products) > -1; 
	      // });
	      // if (same){
	      //   chosenPackage = i;
	      //   return false;
	      // }
		});
		// console.log(chosenPackage);
	}
});
