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

	//Configurator Page
	if ($('body').hasClass('ConfiguratorPage')){
		//set up the products
		var products = [];
		//we fetch the packages
		var packages = [];
		var productsOfPackages = [];
		var url = window.location.pathname;
		$.ajax({
			url: '/shop-functions/fetchPackages',
			dataType: 'Json'
		}).done(function(response){
			packages = response;
			//Init Order
			UpdateOrder();
		});

		//Handle the product slider
		$(document).on("click",".category:not(.disabled) .uk-slider-items li",function(){
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

		$(document).on("change",".no-category",function(){
			if ($(this).is(':checked')){
				$(this).parents('.category').addClass('disabled');
			}
			else{
				$(this).parents('.category').removeClass('disabled');
			}
			UpdateOrder();
		});
	}

	//Shop Page
	if ($('body').hasClass('ShopPage')){
		UpdateOrderPreview();
	}




	function UpdateOrder(){
		productsOfPackages = [];
		products = [];
		var package;
		var chosenPackageID = 0;
		$('.category:not(.disabled) .slider-packages .uk-slider-items li.uk-active').each(function(){
			productsOfPackages.push($(this).attr('data-value'));
		});
		$('.category:not(.disabled) .slider-products .uk-slider-items li.uk-active').each(function(){
			products.push($(this).attr('data-value'));
		});
		//Compare to see if any package matches the selected products
		$.each(packages,function(i,v){
			if (compareArrays(v['Products'],productsOfPackages)){
				chosenPackageID = v['ID'];
				return false;
			}
		});

		UpdateOrderPreview(chosenPackageID, products);
	}

	function compareArrays(arr1, arr2) {
	    return $(arr1).not(arr2).length == 0 && $(arr2).not(arr1).length == 0
	};

	function UpdateOrderPreview(packageID, products){
		$.ajax({
			url: '/shop-functions/fetchCart',
			method: 'POST',
			dataType: 'html',
			data: {packageID: packageID, products: products}
		}).done(function(response){
			$(".order-preview").each(function(){
				$(this).empty().append(response);
			});
		});
	}

});