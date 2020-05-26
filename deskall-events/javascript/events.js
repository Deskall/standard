$(document).ready(function(){

	//if form not valid we go to correct tab
	if ($("#Form_RegisterForm").length > 0 && $("#Form_RegisterForm").find('.message.validation').length > 0){
		//search for errors
		var switcher = $('#tab-switcher');
		var errorMessage = $("#Form_RegisterForm").find('.message.validation');
		var index = parseInt(errorMessage.parents('li.account-tab').attr('data-index'));
		//Update Package
		$("#summary-package-"+$("#Form_RegisterForm_ProductID").val()).attr('hidden',false);
		UIkit.tab(switcher).show(index);
		$('html, body').animate({scrollTop: switcher.offset().top }, 100);
	}

	$("#Form_RegisterForm_CustomerFields_Holder input,#Form_RegisterForm_CustomerFields_Holder select").on("change",function(){
		UpdateAddress();
	});
		
	function UpdateAddress(){
		var html = '<p>';
		if ($("input[name='Company']").val()){
			html += $("input[name='Company']").val()+'<br>';
		}
		html += $("select[name='Gender']").val()+' '+$("input[name='Vorname']").val()+' '+$("input[name='Name']").val()+'<br>';
		html += $("input[name='Address']").val()+'<br>';
		html += $("input[name='PostalCode']").val()+' '+$("input[name='City']").val()+'<br>';
		html += $("select[name='Country'] option:selected").text();
		html += '</p>';

		html += '<p>'+$("input[name='Email']").val()+'<br>';
		html += $("input[name='Phone']").val()+'</p>';

		$("#customer-address").html(html);
	}

	//Steps
	$(document).on("click","[data-step]",function(){
		var switcher = $('#tab-switcher');
		var tab = $(this).parents('li.account-tab');
		var form = $(this).parents('form');
		var index = parseInt(tab.attr('data-index'));
		if ($(this).attr('data-step') == "backward"){
			UIkit.tab(switcher).show(index-1);
			$('html, body').animate({scrollTop: switcher.offset().top }, 100);
		}
		if (form.valid() && $(this).attr('data-step') == "forward"){
			if (index == 0){
				UpdateAddress();
			}
			var li = index+2;
			switcher.find('li:nth-child('+li+')').removeClass('uk-disabled');
			UIkit.tab(switcher).show(index+1);
			$('html, body').animate({scrollTop: switcher.offset().top }, 100);
		}
	});
	

	//Pyement Method Fields
	$(document).on("change","input[name='PaymentMethod']",function(){
		if ($("input[name='PaymentMethod']:checked").val() == "bill"){
			$("#card-form-container").attr('hidden','hidden');
			$("#summary-bill-container").attr('hidden',false);
			$("#Form_RegisterForm_action_doRegisterBill").attr('hidden',false);
			$("#payment-type").html('<p>'+$("label[for='bill-choice']").html()+'</p>');
		}
		else if ( $("input[name='PaymentMethod']:checked").val() == "cash"){
			$("#card-form-container").attr('hidden','hidden');
			$("#summary-bill-container").attr('hidden',false);
			$("#Form_RegisterForm_action_doRegisterBill").attr('hidden',false);
			$("#payment-type").html('<p>'+$("label[for='cash-choice']").html()+'</p>');
		}
		else{
			$("#Form_RegisterForm_action_doRegisterBill").attr('hidden','hidden');
			$("#summary-bill-container").attr('hidden','hidden');
			$("#card-form-container").attr('hidden',false);
			$("#payment-type").html('<p>'+$("label[for='online-choice']").html()+'</p>');
		}
	
		$("#Form_RegisterForm_PaymentType").val($("input[name='PaymentMethod']:checked").val());
	});


	function UpdateOrderSummary(){
		var coursePrice = parseFloat($("#event").attr('data-price'));
		var voucherPrice = parseFloat($("#voucher-price").attr('data-price'));
		if (voucherPrice){
			coursePrice -= voucherPrice;
		}
		var mwstPrice = coursePrice * 0.0707;
		$("#event-mwst-price").html("CHF "+mwstPrice.toFixed(2));
		$("#event-total-price").html("CHF "+coursePrice.toFixed(2)).attr('data-price',coursePrice);
	}


	//Voucher
	$(document).on("click","[data-check-voucher]",function(){
		$.post({
			url: cleanUrl($("#event").attr('data-url'))+'VoucherForm',
			data:{code: $("input[name='voucher']").val(), event: $("#Form_RegisterForm_DateID").val() },
	        dataType: 'Json'
		}).done(function(response){
			if (response.status == "OK"){
				UIkit.modal.alert(response.message).then(function() {
					$("input[name='VoucherID']").val(response.voucherID);
					$("#voucher-price").html(response.discountPrice);
					$("#voucher-price").attr('data-price',response.discount);
					$("#voucher-row").attr('hidden',false);
					UpdateOrderSummary();
				});
			}
			else{
				if (response.message){
					UIkit.modal.alert(response.message);
					$("#voucher-price").empty();
					$("#voucher-row").attr('hidden','hidden')
				}
			}
		}).fail(function(){
			UIkit.modal.alert('Ein Fehler ist aufgetreten').then(function() {
				window.location.reload();
			});
		});
	});

	function getPrice(){
		return $("#event-total-price").attr('data-price');
	}

	function cleanUrl(url){
		url = (url.substr(url.length-1,1) == "/") ? url : url+"/";
		return url;
	}

	//PayPal
	if ($('#paypal-button-container').length > 0){
		
		paypal.Buttons({
	    createOrder: function(data, actions) {
	    		var paypaloptions = {
	    	      	intent: 'CAPTURE',
	    	  	    payer: {
	    	  	        name: {
	    	  	          given_name: $("input[name='Vorname']").val(),
	    	  	          surname: $("input[name='Name']").val()
	    	  	        },
	    	  	        address: {
	    	  	          address_line_1: $("input[name='Address']").val(),
	    	  	          address_line_2: $("input[name='Address2']").val(),
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
	      return actions.order.create(paypaloptions);
	    },
	    onApprove: function(data, actions) {
	    	$('#paypal-button-container').hide().after('<p>Zahlung in Bearbeitung, bitte haben Sie einen Moment Geduld.</p><p>Bitte schließen Sie die Seite nicht und laden Sie sie nicht erneut.</p>');
	      	$('html, body').animate({scrollTop: $('#paypal-button-container').offset().top -50 }, 100);
	      return actions.order.capture().then(function(details) {
	        UIkit.modal.alert('Ihre Zahlung wurde berücksichtigt. Sie werden in wenigen Augenblicken weitergeleitet ...');
	        // Call your server to save the transaction
	        $.ajax({
	        	url: cleanUrl($("#event").attr('data-url'))+'transaktion-abgeschlossen',
	          	method: 'post',
	          	data: {
	            	orderID: data.orderID,
	            	dateID: $("input[name='DateID']").val(),
	            	voucherID: $("input[name='VoucherID']").val()
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