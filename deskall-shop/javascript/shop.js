$(document).ready(function(){
	
	//if form not valid we go to correct tab
	if ($("#Form_CheckoutForm").length > 0 && $("#Form_CheckoutForm").find('.message.validation').length > 0){
		//search for errors
		var switcher = $('#tab-switcher');
		var errorMessage = $("#Form_CheckoutForm").find('.message.validation');
		var index = parseInt(errorMessage.parents('li.account-tab').attr('data-index'));
		//Update Package
		$("#summary-package-"+$("#Form_CheckoutForm_ProductID").val()).attr('hidden',false);
		UIkit.tab(switcher).show(index);
	}

	$(document).on("change","input[name='DeliverySameAddress']",function(){
		if ($(this).is(':checked')){
			$("#delivery-form-container").attr('hidden',false);
		}
		else{
			$("#delivery-form-container").attr('hidden','hidden');
		}
	});

	$(document).on("click",".add-to-cart",function(){
		UpdateOrderPreview($(this).attr('data-product-id'),1,'webshop');
		$("#link-shop").attr("hidden","hidden");
		$("#toggle-cart").attr("hidden",false);
		var count = parseInt($("#cart-articles-count").text()) + 1;
		$("#cart-articles-count").text(count);
	});

	$(document).on("change","input[data-quantity]",function(){
		UpdateOrderPreview($(this).attr('data-quantity'),$(this).val(), 'checkout');
		UpdateCartSummary();
	});

	$(document).on("click","[data-remove-product]",function(){
		productID = $(this).attr('data-remove-product');
		$.ajax({
			url: '/shop/removeFromCart',
			method: 'POST',
			dataType: 'html',
			data: {productID: productID}
		}).done(function(response){
			$(".order-preview").empty().append(response);
		});
	});

	$(document).on("click","[data-step='forward']",function(){
		UpdateCartStep();
	});

	
		
	
		
		
	


	function UpdateOrderPreview(productID,quantity,context = null){
		//ici ajouter un
		$.ajax({
			url: '/shop/updateCart',
			method: 'POST',
			dataType: 'html',
			data: {productID: productID,quantity: quantity, context: context}
		}).done(function(response){
			if (context == "checkout"){
				$(".order-preview").each(function(){
					$(this).empty().append(response);
				});
			}
			else{
				$("#offcanvas-usage-cart .order-preview").empty().append(response);
				$("#cart-container").attr("hidden",false);
			}
		});
	}

	function UpdateOrderSummary(){
		//ici ajouter un
		$.ajax({
			url: '/shop/updateCartSummary',
			method: 'POST',
			dataType: 'html'
		}).done(function(response){
			$(".summary-products").replaceWith(response);
		});
	}

	function UpdateCartStep(){
		var form = $("#Form_CheckoutForm");
		$.ajax({
			url: '/shop/updateCartData',
			method: 'POST',
			dataType: 'html',
			data: {form: form.serialize()}
		}).done(function(response){
			$(".summary-products").each(function(){
				$(this).empty().append(response);
			});
		});
	}


	//Steps
	$(document).on("click","[data-step]",function(){
		var switcher = $('#tab-switcher');
		var tab = $(this).parents('li.account-tab');
		var form = $(this).parents('form');
		var index = parseInt(tab.attr('data-index'));
		if ($(this).attr('data-step') == "backward"){
			UIkit.tab(switcher).show(index-1);
		}
		if (form.valid() && $(this).attr('data-step') == "forward"){
			UIkit.tab(switcher).show(index+1);
		}
	});
	

	//Pyement Method Fields
	$(document).on("change","input[name='PaymentMethod']",function(){
		if ($("input[name='PaymentMethod']:checked").val() == "bill"){
			$("#card-form-container").attr('hidden','hidden');
			$("#Form_CheckoutForm_DeliverySameAddress_Holder").attr("hidden",false);
			$("#summary-bill-container").attr('hidden',false);
			$("#Form_CheckoutForm_action_payBill").attr('hidden',false);
		}
		else if ( $("input[name='PaymentMethod']:checked").val() == "cash"){
			$("#Form_CheckoutForm_DeliverySameAddress_Holder").attr("hidden","hidden");
			$("#card-form-container").attr('hidden','hidden');
			$("#summary-bill-container").attr('hidden',false);
			$("#Form_CheckoutForm_action_payBill").attr('hidden',false);
		}
		else{
			$("#Form_CheckoutForm_DeliverySameAddress_Holder").attr("hidden","hidden");
			$("#Form_CheckoutForm_action_payBill").attr('hidden','hidden');
			$("#summary-bill-container").attr('hidden','hidden');
			$("#card-form-container").attr('hidden',false);
		}
	
		$("#Form_CheckoutForm_PaymentType").val($("input[name='PaymentMethod']:checked").val());
	});

	//Voucher
	$(document).on("click","[data-check-voucher]",function(){
		$.post({
			url: cleanUrl(window.location.pathname)+'VoucherForm',
			data:{code: $("input[name='voucher']").val(), package: $("#Form_CheckoutForm_ProductID").val() },
	        dataType: 'Json'
		}).done(function(response){
			if (response.status == "OK"){
				UIkit.modal.alert(response.message).then(function() {
					UpdateOrderSummary();
				});
			}
			else{
				if (response.message){
					UIkit.modal.alert(response.message);
				}
			}
		}).fail(function(){
			UIkit.modal.alert('Ein Fehler ist aufgetreten').then(function() {
				window.location.reload();
			});
		});
	});

	function getPrice(){
		return $("#full-total-price").attr('data-price');
	}

	function cleanUrl(url){
		url = (url.substr(url.length-1,1) == "/") ? url : url+"/";
		return url;
	}

	//PayPal
	if ($('#paypal-button-container').length > 0){
		var paypaloptions = {
	      	intent: 'CAPTURE',
	  	    payer: {
	  	        name: {
	  	          given_name: $("input[name='FirstName']").val(),
	  	          surname: $("input[name='Name']").val()
	  	        },
	  	        address: {
	  	          address_line_1: $("input[name='Street']").val(),
	  	          address_line_2: $("input[name='Address']").val(),
	  	          admin_area_2: $("input[name='City']").val(),
	  	          admin_area_1: $("input[name='Region']").val(),
	  	          postal_code: $("input[name='PostalCode']").val(),
	  	          country_code: $("select[name='Country']").val().toUpperCase()
	  	        },
	  	        email_address: $("input[name='Email']").val(),
	  	        phone: {
	  	          phone_number: {
	  	            national_number: $("input[name='Phone']").val()
	  	          }
	  	        }
	  	    },
	  	    purchase_units: [
	  	        {
	  	          amount: {
	  	            value: getPrice(),
	  	            currency_code: 'CHF'
	  	          }
	  	        }
	  	    ]
	    };
	    // if ($("input[name='DeliverySameAddress']").is(':checked')){
	    // 	shippingoptions = {
	  	 //        // shipping_type: shipping_type,
	  	 //        address: {
	  	 //            address_line_1: $("input[name='DeliveryStreet']").val(),
		  	//         address_line_2: $("input[name='DeliveryAddress']").val(),
		  	//         admin_area_2: $("input[name='DeliveryCity']").val(),
		  	//         admin_area_1: $("input[name='DeliveryRegion']").val(),
		  	//         postal_code: $("input[name='DeliveryPostalCode']").val(),
		  	//         country_code: $("select[name='DeliveryCountry']").val().toUpperCase()
	  	 //        }
	  	 //    };
	  	 //    paypaloptions.purchase_units[0].shipping = shippingoptions;
	    // }
		paypal.Buttons({
	    createOrder: function(data, actions) {
	      return actions.order.create(paypaloptions);
	    },
	    onApprove: function(data, actions) {
	    	$('#paypal-button-container').hide().after('<p>Zahlung in Bearbeitung, bitte haben Sie einen Moment Geduld.</p><p>Bitte schließen Sie die Seite nicht und laden Sie sie nicht erneut.</p>');
	      return actions.order.capture().then(function(details) {
	        UIkit.modal.alert('Ihre Zahlung wurde berücksichtigt. Sie werden in wenigen Augenblicken weitergeleitet ...');
	        // Call your server to save the transaction
	        $.ajax({
	        	url: cleanUrl(window.location.pathname)+'transaktion-abgeschlossen',
	          	method: 'post',
	          	data: {
	            	orderID: data.orderID
	          	},
	          	dataType: 'Json'
	        }).done(function(response){
	        	if (response.status == "OK"){
	        		window.location.href = response.redirecturl;
	        	}
	        	else{
	        		 UIkit.modal.alert('Ein Fehler ist aufgetreten').then(function() {
	        		 	window.location.reload();
	        		 });
	        	}
	        }).fail(function(data){
	        	UIkit.modal.alert('Ein Fehler ist aufgetreten').then(function() {
	        		 	window.location.reload();
	        		 });
	        });
	      });
	    }
	  }).render('#paypal-button-container');
	}
});