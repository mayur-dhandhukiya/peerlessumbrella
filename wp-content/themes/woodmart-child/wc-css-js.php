<?php
add_action( 'wp_footer', 'wc_add_css_js_fun' );
function wc_add_css_js_fun(){
	if( is_page(  array( 3423440248 , 3423440255 ) ) ){
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {

				jQuery("table#repeatable-fieldset-one tbody select.wc-product").select2();
				jQuery("table#repeatable-fieldset-one tbody select.wc-varitions-product").select2(); 
				jQuery('.hidden-print .wp-options-wrapper .wc_top_logo').prop('checked', true);

				jQuery( document ).on('click','.wc_invoice_repeter #add-row', function() {
					var count = jQuery(this).attr('data-count');
					count = parseInt(count) + 1;
					jQuery(this).attr('data-count',count);
					var row = jQuery( '.empty-row.screen-reader-text' ).html().replace(/rand_no/g, count);      
					if( jQuery('#repeatable-fieldset-one tbody>tr:last').length == 0  )  {    
						jQuery('#repeatable-fieldset-one tbody').append('<tr>'+row+'</tr>');
					} else {
						jQuery('#repeatable-fieldset-one tbody>tr:last').after('<tr>'+row+'</tr>');
					}


					jQuery('table#repeatable-fieldset-one tbody select.wc-product').each(function(){              
						jQuery(this).removeClass('select2-hidden-accessible');                    
						jQuery(this).next('.select2-container').remove();
						jQuery(this).select2();
					});    

					jQuery('.wc-repete-varitions .wc-varitions-product').each(function(){              
						jQuery(this).removeClass('select2-hidden-accessible');                    
						jQuery(this).next('.select2-container').remove();
						jQuery(this).select2();
					});  

			//jQuery("table#repeatable-fieldset-one tbody select.wc-product").select2();
			//jQuery(".wc-repete-varitions .wc-varitions-product").select2();
			return false;
		});

				jQuery(document).on('click','.wc_quote_form .wc_invoice_repeter .remove-row',function(){
					jQuery(this).parent().parent().remove();
					return false;
				});
				jQuery("body").on('click', '.wc_quote_form .wc_quote_form #btnToggleOptions', function () {
					jQuery("#wp-options").slideToggle();
				});

				jQuery(document).on('click','.wc_quote_form .wp-tabs .tabs .wc-preview',function(){
					wc_edit_preview_quotes_data();
					jQuery('.wc_quote_form .wp-tabs .tabs .wc-preview').toggleClass('active');
					if( !jQuery(this).hasClass('active') ){
						jQuery('.wc_quote_form .wp-tabs .tabs .wc-preview').text('Edit');
						jQuery('.wc_quote_form .wp-paper .wp-viewinvoice-form-wrapper').show();
						jQuery('.wc_quote_form .wp-paper .wp-quote-form-wrapper').hide();
					} else{
						jQuery('.wc_quote_form .wp-tabs .tabs .wc-preview').text('Preview');
						jQuery('.wc_quote_form .wp-paper .wp-viewinvoice-form-wrapper').hide();
						jQuery('.wc_quote_form .wp-paper .wp-quote-form-wrapper').show();
					}
				});

				jQuery('form.wc_quote_form .wp-tabs .save-btn .wc-save.disabled').magnificPopup({
					type: 'inline',
					preloader: false,
					modal: true
				});

				jQuery(document).on( 'click', '.logged-modal-dismiss', function(e) {
					e.preventDefault();
					jQuery.magnificPopup.close();
				});

				jQuery(document).on("click", 'form.wc_quote_form .wp-tabs .save-btn .wc-save', function(e) {
					e.preventDefault();
					if(!jQuery(this).hasClass('disabled')){
						jQuery('.wc_quote_form .comman_editor').each(function(){
							var myEditor = jQuery(this);
							var html = myEditor.find('.ql-editor').html();
							jQuery(this).siblings('.comman_editor_val').val(html);
						});
						var t_this = jQuery(this);
						var serialized = jQuery('.wc_quote_form').serialize();

						var to_your_name = jQuery('form.wc_quote_form .wp-paper .wp-meta-from input[name="to_your_name"]').val();
						var from_your_name = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="from_your_name"]').val();
						var from_company_name = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="from_company_name"]').val();
						var to_company_name = jQuery('.wp-quote-form-wrapper .wp-meta-from.wp-meta-to input[name="to_comapny_name"]').val();
						var from_email = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="from_email"]').val();
						var to_email = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="to_email"]').val();

						to_your_name = to_your_name.trim();
						from_your_name = from_your_name.trim();
						from_company_name = from_company_name.trim();
						to_company_name = to_company_name.trim();
						from_email = from_email.trim();
						to_email = to_email.trim();
						var error = '';
						var wc_flag = 0;
						jQuery('form.wc_quote_form .wc_error').remove();

						if( from_company_name == '' ){
							error += '<p class="wc_error"><strong>From:</strong> Please Enter Your Company Name</p>'; 
							wc_flag= 1;
						}

						if( from_your_name == '' ){
							error += '<p class="wc_error"><strong>From:</strong> Please Enter Your Name</p>'; 
							wc_flag= 1;
						}

						if( from_email == '' ){
							error += '<p class="wc_error"><strong>From:</strong> Email is required.</p>';      
							wc_flag = 1;
						}else if( !IsEmail(from_email) ){
							error += '<p class="wc_error"><strong>From:</strong> Email is not valid.</p>';      
							wc_flag = 1;
						}

						if( to_company_name == '' ){
							error += '<p class="wc_error"><strong>To:</strong> Please Enter Your Customer Company Name</p>'; 
							wc_flag = 1;
						}

						if( to_your_name == '' ){
							error += '<p class="wc_error"><strong>To:</strong> Please Enter Your Customer Name</p>'; 
							wc_flag = 1;
						}

						if( to_email == '' ){
							error += '<p class="wc_error"><strong>To:</strong> Email is required.</p>';      
							wc_flag = 1;
						}else if( !IsEmail(to_email) ){
							error += '<p class="wc_error"><strong>To:</strong> Email is not valid.</p>';      
							wc_flag = 1;
						}

						if( wc_flag == 0 ){
							t_this.addClass('active');

							var fd = new FormData();
							var files = jQuery('form.wc_quote_form .wp-paper .wp-meta-right #logo_file_input')[0].files[0];
							if (typeof files !== undefined) {
								fd.append('file', files);
							}
							fd.append('serialized', serialized);
							fd.append('action', 'wc_quote_user_data');

							jQuery.ajax({
								type: 'POST',
								url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
								contentType: false,
								processData: false,
								data: fd,
								success: function(response) {

									if( response ){
										t_this.removeClass('active');
										window.location.reload();	
									}
								}
							});
						} else {
							jQuery('form.wc_quote_form .wp-tabs').before(error);
						}
					}
				});

				jQuery(document).on( "click", '.wc-list-table .wc-quotes-list tbody td.wc-action a.wc-delete', function(e){
					e.preventDefault();
					var t_this = jQuery(this);
					var data_id = t_this.attr('data-id');
					t_this.addClass('active');

					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						data : { action: 'wc_quote_list_delete_data', data_id : data_id },
						success: function(response) {
							t_this.removeClass('active');
							if( response.id ){
								if( response.id == data_id ){
									t_this.closest('tr').remove();
								}
							}
						}
					});
				});

				jQuery(document).on('change','#repeatable-fieldset-one tbody td select.wc-product',function(){
					var t_this = jQuery(this);				
					var value = t_this.find("option:selected").val();	

					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						data : { action: 'wc_quote_variation_prod_id', data_id : value },
						beforeSend: function(response) {
							t_this.closest("tr").find('td.wc-repete-varitions').addClass('active');
						},
						success: function(response) {
							t_this.removeClass('active');
							if( response.html ){
								t_this.closest('tr').find('.wc-varitions-product').html(response.html);
								var data_image = t_this.closest('tr').find('.wc-varitions-product option:selected').attr('data-image');
								if( data_image ){
									t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').show();
									t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', data_image);
									t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val(data_image);
								} else {
									t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').hide();
									t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', '');
									t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val('');
								}
								jQuery('.wc-repete-varitions .wc-varitions-product').each(function(){              
									jQuery(this).removeClass('select2-hidden-accessible');                    
									jQuery(this).next('.select2-container').remove();
									jQuery(this).select2();
								});  

								calculate_invoice( t_this );
								t_this.closest("tr").find('td.wc-repete-qty .lineQty').val(0);
							}
						},
						complete: function(response) {
							t_this.closest("tr").find('td.wc-repete-varitions').removeClass('active');
						}
					});
				});

				jQuery(document).on('change','table.wc-quotes-list tbody td.wc-remainder .wc_remainder',function(){
					jQuery('.wc_quote_form .comman_editor').each(function(){
						var myEditor = jQuery(this);
						var html = myEditor.find('.ql-editor').html();
						jQuery(this).siblings('.wc_quote_form .comman_editor_val').val(html);
					});
					var t_this = jQuery(this);	
					var data_id = t_this.attr("data-id");	
					if( t_this.prop('checked') == true ){			
						t_this.parents('tr').find('.wc-remainder').addClass('active');
						t_this.hide();
						jQuery.ajax({
							type : "post",
							dataType : "json",
							url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
							data : { action: 'wc_quote_mail_remainder', data_id : data_id },
							success: function(response) {
								t_this.parents('tr').find('.wc-remainder').removeClass('active');
								t_this.show();
								if( response.type  == 'success' ){
									jQuery('.wc-quote-table-wrap .wc-list-table').before('<p class="wc-remainder-success">Quote Reminder Set!!</p>');
								}
								setTimeout(function(){
									jQuery('.wc-quote-table-wrap .wc-remainder-success').remove();
								},2500 );
							}
						});
					} else {
						t_this.parents('tr').find('.wc-remainder').addClass('active');
						t_this.hide();
						jQuery.ajax({
							type : "post",
							dataType : "json",
							url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
							data : { action: 'wc_quote_mail_remainder_unsend', data_id : data_id },
							success: function(response) {
								t_this.parents('tr').find('.wc-remainder').removeClass('active');
								t_this.show();
								if( response.type  == 'success' ){
									jQuery('.wc-quote-table-wrap .wc-list-table').before('<p class="wc-remainder-success">Quote Reminder Reset!!</p>');
								}
								setTimeout(function(){
									jQuery('.wc-quote-table-wrap .wc-remainder-success').remove();
								},2500 );
							}
						});
					}
				});

	//jQuery('.wp-foot-left .invoice_notes').summernote();

	jQuery(document).on('change','#repeatable-fieldset-one tbody td select.wc-varitions-product',function(){
		var t_this = jQuery(this);
		var data_image = t_this.find('option:selected').attr('data-image');
		if( data_image ){
			t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').show();
			t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', data_image);
			t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val(data_image);
		} else {
			t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').hide();
			//t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', '');
			//t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val('');
		}
	});

	function numberWithCommas(number) {
		number = parseFloat(number).toFixed(2);
		var parts = number.toString().split(".");
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		return parts.join(".");
	}

	function calculate_invoice( t_this ){

		var price_type = [];
		jQuery('.wc_eqp_price:checked').each(function(){
			price_type.push(jQuery(this).val());
		});
		var subtotal = 0;
		var net_price_check_total = 0;
		var product = t_this.closest("tr").find('td.wc-repete-selector');
		var active = t_this.closest("tr").find('.wc-repete-unit .wc-unit-price .linePrice');
		var net_price = jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_checkbox:checked').val();
		var net_price_check = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');
		if( price_type.indexOf('eqp') == -1 ){
			var price_list = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-variation');	
		}  else {
			var price_list = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');			
		}

		var currency = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
		var qty = t_this.closest("tr").find('td.wc-repete-qty .lineQty').val();
		//var linePrice = t_this.closest("tr").find('.wc-repete-unit .wc-unit-price .linePrice').val();
		var price = 0;
		/*if( isNaN(linePrice) ){ linePrice = 0; }*/

		if( !active.hasClass('active') ){
			price = get_price_according_to_qty(qty, price_list, t_this, net_price_check);	
		} else {
			price = t_this.closest("tr").find('.wc-repete-unit .wc-unit-price .linePrice').val();
		}

		if( isNaN( price ) ){
			price = 0;
		}
		var amount = parseFloat(price) * qty;
		if( isNaN(amount) ){
			amount = 0;
		}
		subtotal = subtotal + parseFloat(amount);
		t_this.closest("tr").find('td.wc-repete-unit .linePrice').val(price);	
		//t_this.closest("tr").find('td.wc-repete-total .lineTotal').val(parseFloat(amount.toFixed(2)));
		t_this.closest("tr").find('td.wc-repete-total .lineTotal').val(numberWithCommas(amount.toFixed(2)));
		t_this.closest("tr").find('td.wc-repete-total .wc-total-currency span').html(currency);
	}

	function calculate_eqp_net_price( t_this ){
		jQuery('#repeatable-fieldset-one tbody tr').each(function( key, val ) {

			var price_type = [];
			jQuery('.wc_eqp_price:checked').each(function(){
				price_type.push(jQuery(this).val());
			});
			var t_this = jQuery(this);
			var subtotal = 0;
			var product = t_this.find('td.wc-repete-selector');
			var active = t_this.find('td.wc-repete-unit .wc-unit-price .linePrice');
			var net_price_check = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');
			var net_price = jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_checkbox:checked').val();
			if( price_type.indexOf('eqp') == -1 ){
				price_list = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-variation');
			} else {
				price_list = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');			
			}

			var currency = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
			var qty = t_this.find('td.wc-repete-qty .lineQty').val();
			//var linePrice = t_this.find('.wc-repete-unit .wc-unit-price .linePrice').val();
			var price = 0;
			/*if( isNaN(linePrice) ){
				linePrice = 0;
			}*/

			if( !active.hasClass('active') ){
				price = get_price_according_to_qty(qty, price_list, t_this, net_price_check);	
			} else {
				price = t_this.find('.wc-repete-unit .wc-unit-price .linePrice').val();
			}

			if( isNaN( price ) ){
				price = 0;
			}
			var amount = parseFloat(price) * qty;
			if( isNaN(amount) ){
				amount = 0;
			}
			subtotal = subtotal + parseFloat(amount);

			t_this.find('td.wc-repete-unit .linePrice').val(price);	
			//t_this.find('td.wc-repete-total .lineTotal').val(parseFloat(amount.toFixed(2)));
			t_this.find('td.wc-repete-total .lineTotal').val(numberWithCommas(amount.toFixed(2)));
			t_this.find('td.wc-repete-total .wc-total-currency span').html(currency);

		});
		
	}

	function get_price_according_to_qty(qty, price_list, t_this, net_price_check){
		var price = price_list;

		//var wc_eqp_price = jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_checkbox:checked').val();
		var price_type = [];
		jQuery('.wc_checkbox:checked').each(function(){
			price_type.push(jQuery(this).val());
		});	
		if( price_type.indexOf('eqp') == -1){

			var qty_arr = [];
			var min_qty_arr = [];
			var max_qty_arr = [];

			if( price_list !== undefined ){
				var variation = JSON.parse(price_list);
				price = variation[0].price;
				if( qty == 0 || qty == '' ){
					price = variation[0].price;
				}
				jQuery(variation).each(function(key, value) {
					if ((parseFloat(value.min_qty) <= parseFloat(qty) && parseFloat(qty) <= parseFloat(value.max_qty)) || (parseFloat(value.min_qty) <= parseFloat(qty) && parseFloat(value.max_qty) == -1)) {
						price = value.price;
					}
					min_qty_arr.push(value.min_qty);
					max_qty_arr.push(value.max_qty);
				});							
			}

			var prd_min_qty = Math.min.apply(Math, min_qty_arr);
			var prd_max_qty = Math.min.apply(Math, max_qty_arr);

			if( price_type.indexOf('net') !== -1 ){ 
				if( qty < prd_min_qty ){
					price = net_price_check;  	
				}
				price = price * 0.6;
			}

		/*if( prd_max_qty != -1 ){ t_this.closest("tr").find('td.wc-repete-qty .lineQty').removeAttr("max");} 
		t_this.closest("tr").find('td.wc-repete-qty .lineQty').attr("max", prd_max_qty);
		t_this.closest("tr").find('td.wc-repete-qty .lineQty').attr("min", prd_min_qty);*/

	}

	if( price_type.indexOf('net') !== -1 && price_type.indexOf('eqp') !== -1 ){
		if( isNaN( price ) ){
			price = 0;
		}
		price = price * 0.6;
	}

	price = parseFloat(price).toFixed(2);
	t_this.closest("tr").find('td.wc-repete-unit .linePrice').val(price);
	var currency = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
	t_this.closest("tr").find('td.wc-repete-unit .wc-unit-currency span').html(currency);

	return price;
}

	/*var wc_edited = jQuery('.wp-quote-form-wrapper .wc-eos-price');
	if( !wc_edited.hasClass('wc-edit') ){
		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price').prop( 'checked', true );	
	}*/
	
	jQuery(document).on("change", '.wp-quote-form-wrapper .wc-eos-price .wc_checkbox', function(e) {
		var t_this = jQuery(this);
		var price_type = [];
		jQuery('.wc_checkbox:checked').each(function(){
			price_type.push(jQuery(this).val());

		});	
		jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-unit .wc-unit-price .linePrice').removeClass('active');

		if( price_type.indexOf('net') !== -1 ){
			jQuery('#repeatable-fieldset-one thead tr th.wc-text-label').text('Net Price');
			jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-qty .lineQty').val('');
			jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-total .wc-total-price #total').val('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val('');
			calculate_eqp_net_price( t_this );
		} else {
			jQuery('#repeatable-fieldset-one thead tr th.wc-text-label').text('Unit Price');
		}

		if( price_type.indexOf('eqp') !== -1 ){
			calculate_eqp_net_price( t_this );
			jQuery('#repeatable-fieldset-one thead tr th.wc-text-label').text('Eqp Price');
			//jQuery('#repeatable-fieldset-one tbody tr .wc-unit-price #unit_price').attr('readonly', true);
		} 

		if( price_type.indexOf('net') !== -1 && price_type.indexOf('eqp') !== -1 ){
			jQuery('#repeatable-fieldset-one thead tr th.wc-text-label').text('Eqp and Net Price');
		}

		sum_all_data( t_this.parents('.wp-quote-form-wrapper').find('#repeatable-fieldset-one tbody tr:first-child td:first-child') );

		if( jQuery('.wc_checkbox:checked').length == 0 ){
			jQuery('.wp-quote-form-wrapper .wc-reset-wrap button.wc-reset').click();
		}
	});

	jQuery(document).on("keyup change", ".wc_invoice_repeter #repeatable-fieldset-one tbody td #qty", function(e) {
		calculate_invoice( jQuery(this) );
		sum_all_data( jQuery(this) );
	});

	wc_customer_email_autofill();
	jQuery(document).on("keyup change", '.wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="from_email"], .wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="to_your_name"]', function(e) {
		wc_customer_email_autofill();
	});
	
	jQuery(document).on("keyup change", ".wc_invoice_repeter #repeatable-fieldset-one tbody td .linePrice", function(e) {	
		jQuery(this).addClass('active');
		this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
		calculate_invoice( jQuery(this)  );
		sum_all_data( jQuery(this) );
	});

	function sum_all_data( t_this ){
		var total = 0;
		var currency = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
		jQuery('#repeatable-fieldset-one tbody tr').each( function() {
			var amount = jQuery(this).find('td #total').val();					
			if( amount == undefined || amount == '' || amount == NaN ){
				amount = 0;						
			}
			if( amount ){
				amount = amount.replace(",", "");				
			}
			total = total + parseFloat(amount);
		});

		if( total ){
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text(numberWithCommas(total));
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc-sub-currency').text(currency);
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text(numberWithCommas(total));
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc-total-currency').text(currency);
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val(numberWithCommas(total));
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val(numberWithCommas(total));
		} else {
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text('');
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc-sub-currency').text('');	
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text('');
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc-total-currency').text('');
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val('');
			t_this.parents('.wc_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val('');
		}
	}

	jQuery(document).on('change','.wc_quote_form .wp-upload-logo input#logo_file_input',function(){
		var t_this = jQuery(this);
		var fd = new FormData();
		var files = jQuery('form.wc_quote_form .wp-paper .wp-meta-right #logo_file_input')[0].files[0];
		if(typeof files !== undefined) {
			fd.append('file', files);
		}
		fd.append('action', 'get_wc_priview_quotes');
		t_this.parents('.wp-upload-logo').find('.wp-clearfix .right').addClass('active');

		jQuery.ajax({
			type: "post",
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			data: fd,
			contentType: false,
			processData: false,
			success: function (response) {
				t_this.parents('.wp-upload-logo').find('.wp-clearfix .right').removeClass('active');
				if( response.path ){
					jQuery('.wc_quote_form .wp-invoice-quete .wp-upload-logo input[name="image_url"]').val(response.path.url);
					jQuery('.wc_quote_form .wp-quote-form-wrapper .wp-meta-right #upload_area img').attr('src', response.path.url);
					//jQuery('.wp-invoice-quete .wp-upload-logo').addClass('wc-hide-logo');
				} else {
					//jQuery('.wp-invoice-quete .wp-upload-logo').removeClass('wc-hide-logo');
				}
				if( response.attach_id ){
					jQuery('.wc_quote_form .wp-invoice-quete .wp-upload-logo input[name="file_id"]').val(response.attach_id);
				}
			}
		});
	});

	jQuery(document).on('click','.wc_quote_form .wp-tabs .tabs li a.wc-pdf',function(e){
		var t_this = jQuery(this);
		wc_edit_preview_quotes_data();
		jQuery('form.wc_quote_form .wp-paper .print-html-hidden').show();
		var html = jQuery('form.wc_quote_form .wp-paper .print-html-hidden')[0].outerHTML;
		t_this.addClass('active');

		jQuery.ajax({
			type : "post",
			url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			data : { action: 'wc_create_pdf_html', html : html },
			success: function(response) {
				t_this.removeClass('active');
				var byteCharacters = atob(response);
				var byteNumbers = new Array(byteCharacters.length);
				for (var i = 0; i < byteCharacters.length; i++) {
					byteNumbers[i] = byteCharacters.charCodeAt(i);
				}
				var a = document.createElement('a')
				var byteArray = new Uint8Array(byteNumbers);
				var file = new Blob([byteArray], { type: 'application/pdf;base64' });
				var fileURL = URL.createObjectURL(file);
				a.href = fileURL;
				a.download = 'quotes.pdf';
				document.body.append(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(fileURL);
			}
		});
	});

	jQuery(document).on('click','.wc_quote_form .wp-paper .wp-quote-form-wrapper .wc-reset-wrap .wc-reset',function(){
		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_checkbox').prop('checked', false);
		jQuery('#repeatable-fieldset-one thead tr th.wc-text-label').text('Unit Price');
		calculate_eqp_net_price( jQuery(this) );
	});

	jQuery(document).on('change','.wc_quote_form #wp-options .wp-options-wrapper .wc_top_logo',function(){
		jQuery(this).toggleClass('active');
		if( jQuery(this).hasClass('active') ){
			jQuery('.wc_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-invoice-quete .wp-upload-logo').show();
		} else {
			jQuery('.wc_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-invoice-quete .wp-upload-logo').hide();
		}
	});	

	jQuery(document).on('change','.wc_quote_form #wp-options .wp-options-wrapper .wc_top_po',function(){
		jQuery(this).toggleClass('active');
		if( jQuery(this).hasClass('active') ){
			jQuery('.wc_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-form-invoice-dates .po-number').css('display', 'flex');
		} else {
			jQuery('.wc_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-form-invoice-dates .po-number').hide();
		}
	});	

	jQuery(document).on('click','.wc_quote_form .wp-tabs .tabs li a.wc-print',function(){
		wc_edit_preview_quotes_data();
		var table_html  = '';
		table_html += jQuery('.wc_quote_form .wp-paper .wp-viewinvoice-form-wrapper.print-html-hidden').html();
		if( table_html ) {
			printDiv(table_html);
		}
	});

	jQuery(document).on('click','#quotes-form #wc-quotes-sub',function(){
		var t_this = jQuery(this);
		wc_edit_preview_quotes_data();
		var quote_html = jQuery('form.wc_quote_form .wp-paper .print-html-hidden')[0].outerHTML;
		var serialized = jQuery( '#quotes-form' ).serialize();

		var name = jQuery(this).parents('.wc-email-form').find('.wc-quotes-fname #name').val();
		var lname = jQuery(this).parents('.wc-email-form').find('.wc-quotes-lname #lname').val();
		var wc_email = jQuery(this).parents('.wc-email-form').find('.wc-quotes-email #email').val();
		var client_email = jQuery(this).parents('.wc-email-form').find('.wc-quotes-client_email #client_email').val();
		wc_email = wc_email.trim();
		client_email = client_email.trim();
		var error = '';
		var wc_flag = 0;
		jQuery(this).parents('#quotes-form').find('.wc_error').remove();
		if( name == '' ){
			error += '<p class="wc_error">please enter your Customer name</p>'; 
			wc_flag = 1;
		}

		var errflag = 0;
		if( wc_email == '' ){
			error += '<p class="wc_error">Email is required.</p>';      
			wc_flag = 1;
		}else if( wc_email ){
			var emails = wc_email.split(',');
			jQuery(emails).each(function( key, val ) {
				var temails = val.trim();
				if( !IsEmail(temails) ){
					errflag = 1;
				}
			});		
			if( errflag == 1 ){
				error += '<p class="wc_error">Email is not valid.</p>';
				wc_flag = 1;
			}	
		}

		if( client_email == '' ){
			error += '<p class="wc_error">From Email is required.</p>';      
			wc_flag = 1;
		}else if( !IsEmail(client_email) ){
			error += '<p class="wc_error">From Email is not valid.</p>';      
			wc_flag = 1;
		}

		if( wc_flag == 0 ){
			t_this.addClass('active');
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
				data : { action: 'wc_quote_send_data', quote_html : quote_html, serialized : serialized},
				success: function(response) {
					t_this.removeClass('active');

					jQuery('#quotes-form .wc-quotes-submit').before('<p class="wt-success">Your Form Successfully Done.</p>');
					jQuery('.wc-quotes-fname #name').val('');
					jQuery('.wc-quotes-client_email #client_email').val('');
					jQuery('.wc-quotes-lname #lname').val('');
					jQuery('.wc-quotes-email #email').val('');

					setTimeout(function(){ 
						jQuery.magnificPopup.close(); 
					}, 2000);
				}
			});
		} else {
			jQuery('#quotes-form .wc-email-form').before(error);
		}
		return false;
	});

	jQuery(document).on( 'click', '#quotes-form .quotes-modal-dismiss', function(e) {
		e.preventDefault();
		jQuery.magnificPopup.close();
	});

	jQuery('.quotes-popup').magnificPopup({
		type: 'inline',
		preloader: false,
		callbacks: {
			open: function() {
				jQuery('#quotes-form .wt-success').remove();
			},
			close: function() {

			}
		}
	});	

	var toolbarOptions = [
	['bold', 'italic', 'underline', 'strike'],        
	['blockquote', 'code-block'],

	[{ 'header': 1 }, { 'header': 2 }],               
	[{ 'list': 'ordered'}, { 'list': 'bullet' }],
	[{ 'script': 'sub'}, { 'script': 'super' }],     
	[{ 'indent': '-1'}, { 'indent': '+1' }],         
	[{ 'direction': 'rtl' }],                        

	[{ 'size': ['small', false, 'large', 'huge'] }], 
	[{ 'header': [1, 2, 3, 4, 5, 6, false] }],

	[{ 'color': [] }, { 'background': [] }],         
	[{ 'font': [] }],
	[{ 'align': [] }],

	['clean']                                        
	];

	var options = {
		//debug: 'info',
		modules: {
			toolbar:  toolbarOptions
		},
		theme: 'snow'

	};
	
	if( jQuery('.wc_quote_form #editor').length > 0 ){
		var editor = new Quill('.wc_quote_form #editor', options);
	}
	if( jQuery('.wc_quote_form #extra_editor').length > 0 ){
		var editor = new Quill('.wc_quote_form #extra_editor', options);
	}

});

function IsEmail(email){
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!regex.test(email)) {
		return false;
	}else{
		return true;
	}
}

function printDiv(table_html, ord_table_html) {

	var newWin=window.open('','Print-Window');

	newWin.document.open();

	newWin.document.write('<html><body onload="window.print()">'+table_html+'</body></html>');

	newWin.document.close();

	setTimeout(function(){newWin.close();},1000);

}

function wc_edit_preview_quotes_data() {
	jQuery('.wc_quote_form .comman_editor').each(function(){
		var myEditor = jQuery(this);
		var html = myEditor.find('.ql-editor').html();
		jQuery(this).siblings('.wc_quote_form .comman_editor_val').val(html);
	});

	var header_text = jQuery('#repeatable-fieldset-one thead tr th.wc-text-label').text();
	var from_your_name = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="from_your_name"]').val();
	var to_your_name = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="to_your_name"]').val();
	var from_your_address = jQuery('.wp-quote-form-wrapper .wp-meta-from .from_your_address').val();
	var to_your_address = jQuery('.wp-quote-form-wrapper .wp-meta-from .to_your_address').val();
	var from_company_name = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="from_company_name"]').val();
	var to_company_name = jQuery('.wp-quote-form-wrapper .wp-meta-from.wp-meta-to input[name="to_comapny_name"]').val();
	var from_email = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="from_email"]').val();
	var to_email = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="to_email"]').val();
	var to_telphone = jQuery('.wp-quote-form-wrapper .wp-meta-from input[name="to_telphone"]').val();
	var image_url = jQuery('.wp-meta-right .wp-upload-logo input[name="image_url"]').val();
	var invoice_number = jQuery('.wp-meta-right .wp-form-invoice-dates input[name="invoice_number"]').val();
	var tex_number = jQuery('.wp-meta-right .wp-form-invoice-dates input[name="tex_number"]').val();
	var trip_start = jQuery('.wp-meta-right .wp-form-invoice-dates input[name="trip-start"]').val();
	var trip_end = jQuery('.wp-meta-right .wp-form-invoice-dates input[name="trip-end"]').val();
	var notes = jQuery('.wp-foot .wp-foot-left #invoice_notes').text();
	var extra_notes = jQuery('.wp-foot .wp-foot-left .wc_extra_comments').val();
	var sub_total = jQuery('.wp-foot .wp-foot-right tbody .wc-sub').text();
	var total = jQuery('.wp-foot .wp-foot-right tbody .wc-total').text();
	//var amnt_paid = jQuery('.wp-foot .wp-foot-right tbody .wc-amt-paid').text();
	//var balance = jQuery('.wp-foot .wp-foot-right tbody .wc-balance').text();
	//var wc_quotes_td +=  <td style='border: 1px solid #00abc8; padding:0; position: relative;'><table width='100%' align='center' style='border:0; text-align:center; width:100%; 'border='0'><tr><td style='text-align: center;padding: 10px;position: relative;height:50px;width:100%;' width='100%' align='center'>" + '<img width="50" height="50" style="width: 50px;height:50px; display: block;margin: 0 auto;border: 1px solid #e6e6e6;padding: 5px; object-position: center;position:absolute;top:10px;left: 50%; transform: translateX(-50%);" src="'+variation_img+'"></td></tr>'+' <tr><td style="text-align:center;padding:5px 10px;" align="center"><span style="display:block; text-align:center;margin-top: 15px;" class="wc-text-left">'+prod_desc+ "</span></td></tr></table></td>;

	var table_html = '';
	jQuery('#repeatable-fieldset-one tbody tr').each( function( index,value ) {
		var wc_quotes_td = '';
		var prod_selected = jQuery(this).find('.wc-repete-selector select.wc-product option:selected').text();
		var prod_link = jQuery(this).find('.wc-repete-selector select.wc-product option:selected').attr('data-link');
		var variation_selected = jQuery(this).find('.wc-repete-varitions select.wc-varitions-product option:selected').text();
		var currency = jQuery(this).find('.wc-repete-varitions select.wc-varitions-product option:selected').attr('data-currency');
		var prod_desc = jQuery(this).find('.wc-repete-desc .wc-desc').val();
		var variation_img = jQuery(this).find('.wc-repete-desc .wc-var-image img').attr('src');
		var prod_price = jQuery(this).find('.wc-repete-unit .linePrice').val();
		var prod_qty = jQuery(this).find('.wc-repete-qty .lineQty').val();
		var prod_total = jQuery(this).find('.wc-repete-total .lineTotal').val();
		if( prod_desc === undefined ){ prod_desc = ''; }
		if( variation_img === undefined ){ variation_img = ''; }
		if( prod_selected === undefined ){ prod_selected = ''; }
		if( variation_selected === undefined  ){ variation_selected = ''; }
		if( prod_price === undefined ){ prod_price = ''; }
		if( prod_qty === undefined ){ prod_qty = ''; }
		if( prod_total === undefined ){ prod_total = ''; }
		if( currency === undefined ){ currency = ''; }
		if( prod_link === undefined ){ prod_link = ''; }
		
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'><a target='_blank' href='"+ prod_link +"' style='text-decoration:underline!important;color: #333333;'>" + prod_selected + "</a></td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + variation_selected + "</td>";
		wc_quotes_td += "<td style='border: 1px solid #00abc8; padding:0; position: relative;'><table width='100%' align='center' style='border:0; text-align:center; width:100%; 'border='0'><tr><td style='text-align: center;padding: 10px;position: relative;height:50px;width:100%;' width='100%' align='center'><img width='50' height='50' style='width: 50px;height:50px; display: block;margin: 0 auto;border: 1px solid #e6e6e6;padding: 5px; object-position: center;position:absolute;top:10px;left: 50%; transform: translateX(-50%);' src="+variation_img+"></td></tr><tr><td style='text-align:center;padding:5px 10px;' align='center'><span style='display:block; text-align:center;margin-top:15px;' class='wc-text-left'>"+prod_desc+"</span></td></tr></table></td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + currency+''+prod_price + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + prod_qty + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8; color: #00abc8; font-weight: 700;'>" + currency+''+prod_total + "</td>";
		
		if( table_html !== undefined && prod_selected != ''  ){
			table_html += "<tr class='wc-sub-tr'>"+ wc_quotes_td +"</tr>";
		}	
	});

	if( header_text ){
		jQuery('.wc_quote_form .viewinvoice-dataTable tbody tr.wc-notes-pricing .wc-text').html('<strong>'+header_text+'</strong>');
		jQuery('.wc_quote_form .viewinvoice-dataTable thead th.wc-hide-price').html(header_text);
	}

	if( header_text == 'Unit Price' ){
		jQuery('.wc_quote_form .viewinvoice-dataTable tbody tr.wc-notes-pricing .wc-text').html('<strong>(C)</strong>');	
	}

	if( from_company_name ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-address .wc-from-comapny-name').text(from_company_name);
	}

	if( from_your_name ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-address .wc-from-name').text(from_your_name);
	} 

	if( from_email ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-address .wc-from-email').text(from_email);
	}	

	if( to_company_name ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-toname .wc-to-comapny-name').text(to_company_name);
	} 

	if( to_your_name ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-toname .wc-to-name').text(to_your_name);
	} 

	if( to_email ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-toname .wc-to-email').text(to_email);
	} 

	if( to_telphone ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-toname .wc-to-phone').text(to_telphone);
	} 

	if( from_your_address ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-address .wc-from-address').text(from_your_address);
	} 

	if( to_your_address ){
		jQuery('.wc_quote_form .wp-paper .invoice-from-toname .wc-to-address').text(to_your_address);
	} 

	if( image_url ){
		jQuery('.viewinvoice-address-logo .invoice-from-logo img').attr('src', image_url);
	} 

	if( invoice_number ){
		jQuery('.wc_quote_form .viewinvoice-toname-table .invoice-table table tbody td.preview-invoice-id').text(invoice_number);
	} 

	if( tex_number ){
		jQuery('.wc_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-po').text(tex_number);
	} 


	if( trip_start == ''){
		jQuery('.wc_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-invoice-date').text('');
	} else {
		var string_date = '';
		var trip_result = trip_start.split('-');
		if( trip_result[1] ) {
			string_date +=  trip_result[1];
		}
		if( trip_result[2] ) {
			string_date += '/'+ trip_result[2];
		}
		if( trip_result[0] ){
			string_date += '/'+ trip_result[0];
		}
		jQuery('.wc_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-invoice-date').text(string_date);
	}

	if( trip_end == '' ){
		jQuery('.wc_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-due-date').text('');
	} else {
		var string_end_date = '';
		var trip_end_result = trip_end.split('-');
		if( trip_end_result[1] ) {
			string_end_date +=  trip_end_result[1];
		}
		if( trip_end_result[2] ) {
			string_end_date += '/'+ trip_end_result[2];
		}
		if( trip_end_result[0] ){
			string_end_date += '/'+ trip_end_result[0];
		}
		jQuery('.wc_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-due-date').text(string_end_date);
	} 

	if( table_html ){
		jQuery('.wc_quote_form .viewinvoice-dataTable table tbody tr.wc-sub-tr').remove();
		jQuery('.wc_quote_form .viewinvoice-dataTable table tbody tr.quotes-append-html').after(table_html);
		jQuery('.wc_quote_form .viewinvoice-dataTable table tbody tr.quotes-append-html').hide();
	}

	if( notes ){
		jQuery('.wc_quote_form .viewinvoice-dataTable tbody .invoice-notes').html(notes);
	} 
	
	if( extra_notes ){
		jQuery('.wc_quote_form .viewinvoice-dataTable tbody .wc-extra-notes .wc-extra-text').html(extra_notes);
	} 

	if( sub_total ){
		jQuery('.wc_quote_form .invoice-subtotal-table tbody .wc-preview-subtotal').text(sub_total);
	} 

	if( total ){
		jQuery('.wc_quote_form .invoice-subtotal-table tbody .wc-preview-total').text(total);
	} 
}

function wc_customer_email_autofill() {
	var email_val = jQuery('.wc_quote_form .wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="from_email"]').val();
	var customer_name = jQuery('.wc_quote_form .wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="to_your_name"]').val();
	if( email_val ){
		jQuery('form#quotes-form .wc-email-form .wc-quotes-client_email input[name="client_email"]').val(email_val);
	}
	if( customer_name ){
		jQuery('form#quotes-form .wc-email-form .wc-quotes-fname input[name="fname"]').val(customer_name);	
	}
}
</script>
<?php
}
}