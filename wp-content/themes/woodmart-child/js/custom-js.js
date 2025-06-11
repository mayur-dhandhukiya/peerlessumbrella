jQuery(document).ready(function ($) {
	
	

	jQuery("table#repeatable-fieldset-one tbody select.wc-product").select2();
	jQuery("table#repeatable-fieldset-one tbody select.wc-varitions-product").select2();
	jQuery('.hidden-print .wp-options-wrapper .wc_top_logo').prop('checked', true);

	jQuery(document).ready(function () {
		jQuery(document).on('click', '.wc_invoice_repeter #add-row', function () {
			var count = jQuery(this).attr('data-count');
			count = parseInt(count) + 1;
			jQuery(this).attr('data-count', count);
			var row = jQuery('.empty-row.screen-reader-text').html().replace(/rand_no/g, count);
			if (jQuery('#repeatable-fieldset-one tbody>tr:last').length == 0) {
				jQuery('#repeatable-fieldset-one tbody').append('<tr>' + row + '</tr>');
			} else {
				jQuery('#repeatable-fieldset-one tbody>tr:last').after('<tr>' + row + '</tr>');
			}


			jQuery('table#repeatable-fieldset-one tbody select.wc-product').each(function () {
				jQuery(this).removeClass('select2-hidden-accessible');
				jQuery(this).next('.select2-container').remove();
				jQuery(this).select2();
			});

			jQuery('.wc-repete-varitions .wc-varitions-product').each(function () {
				jQuery(this).removeClass('select2-hidden-accessible');
				jQuery(this).next('.select2-container').remove();
				jQuery(this).select2();
			});


			//jQuery("table#repeatable-fieldset-one tbody select.wc-product").select2();
			//jQuery(".wc-repete-varitions .wc-varitions-product").select2();
			return false;
		});

		jQuery(document).on('click', '.wc_invoice_repeter .remove-row', function () {
			jQuery(this).parent().parent().remove();
			return false;
		});
		jQuery("body").on('click', '#btnToggleOptions', function () {
			jQuery("#wp-options").slideToggle();
		});

		jQuery(document).on('click', '.wp-tabs .tabs #preview', function () {
			wc_edit_preview_quotes_data();
			jQuery(this).toggleClass('active');
			if (!jQuery(this).hasClass('active')) {
				jQuery(this).text('Edit');
				jQuery('.wp-paper .wp-viewinvoice-form-wrapper').show();
				jQuery('.wp-paper .wp-quote-form-wrapper').hide();
			} else {
				jQuery(this).text('Preview');
				jQuery('.wp-paper .wp-viewinvoice-form-wrapper').hide();
				jQuery('.wp-paper .wp-quote-form-wrapper').show();
			}
		});
	});


	jQuery('form.wc_quote_form .wp-tabs .save-btn .wc-save.disabled').magnificPopup({
		type: 'inline',
		preloader: false,
		modal: true
	});

	jQuery(document).on('click', '.logged-modal-dismiss', function (e) {
		e.preventDefault();
		jQuery.magnificPopup.close();
	});

	jQuery(document).on("click", 'form.wc_quote_form .wp-tabs .save-btn .wc-save', function (e) {
		e.preventDefault();
		if (!jQuery(this).hasClass('disabled')) {
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

			if (from_company_name == '') {
				error += '<p class="wc_error"><strong>From:</strong> Please Enter Your Company Name</p>';
				wc_flag = 1;
			}

			if (from_your_name == '') {
				error += '<p class="wc_error"><strong>From:</strong> Please Enter Your Name</p>';
				wc_flag = 1;
			}

			if (from_email == '') {
				error += '<p class="wc_error"><strong>From:</strong> Email is required.</p>';
				wc_flag = 1;
			} else if (!IsEmail(from_email)) {
				error += '<p class="wc_error"><strong>From:</strong> Email is not valid.</p>';
				wc_flag = 1;
			}

			if (to_company_name == '') {
				error += '<p class="wc_error"><strong>To:</strong> Please Enter Your Customer Company Name</p>';
				wc_flag = 1;
			}

			if (to_your_name == '') {
				error += '<p class="wc_error"><strong>To:</strong> Please Enter Your Customer Name</p>';
				wc_flag = 1;
			}

			if (to_email == '') {
				error += '<p class="wc_error"><strong>To:</strong> Email is required.</p>';
				wc_flag = 1;
			} else if (!IsEmail(to_email)) {
				error += '<p class="wc_error"><strong>To:</strong> Email is not valid.</p>';
				wc_flag = 1;
			}

			if (wc_flag == 0) {
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
					url: myAjax.ajaxurl,
					contentType: false,
					processData: false,
					data: fd,
					success: function (response) {

						if (response) {
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

	jQuery(document).on("click", '.wc-list-table .wc-quotes-list tbody td.wc-action a.wc-delete', function (e) {
		e.preventDefault();
		var t_this = jQuery(this);
		var data_id = t_this.attr('data-id');
		t_this.addClass('active');

		jQuery.ajax({
			type: "post",
			dataType: "json",
			url: myAjax.ajaxurl,
			data: { action: 'wc_quote_list_delete_data', data_id: data_id },
			success: function (response) {
				t_this.removeClass('active');
				if (response.id) {
					if (response.id == data_id) {
						t_this.closest('tr').remove();
					}
				}
			}
		});
	});

	jQuery(document).on('change', '#repeatable-fieldset-one tbody td select.wc-product', function () {
		var t_this = jQuery(this);
		var value = t_this.find("option:selected").val();

		jQuery.ajax({
			type: "post",
			dataType: "json",
			url: myAjax.ajaxurl,
			data: { action: 'wc_quote_variation_prod_id', data_id: value },
			beforeSend: function (response) {
				t_this.closest("tr").find('td.wc-repete-varitions').addClass('active');
			},
			success: function (response) {
				t_this.removeClass('active');
				if (response.html) {
					t_this.closest('tr').find('.wc-varitions-product').html(response.html);
					var data_image = t_this.closest('tr').find('.wc-varitions-product option:selected').attr('data-image');
					if (data_image) {
						t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').show();
						t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', data_image);
						t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val(data_image);
					} else {
						t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').hide();
						t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', '');
						t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val('');
					}
					//jQuery("table#repeatable-fieldset-one tbody select.wc-varitions-product").select2();
					jQuery('.wc-repete-varitions .wc-varitions-product').each(function () {
						jQuery(this).removeClass('select2-hidden-accessible');
						jQuery(this).next('.select2-container').remove();
						jQuery(this).select2();
					});

					calculate_invoice(t_this);
					t_this.closest("tr").find('td.wc-repete-qty .lineQty').val(0);
				}
			},
			complete: function (response) {
				t_this.closest("tr").find('td.wc-repete-varitions').removeClass('active');
			}
		});
	});


	jQuery(document).on('change', 'table.wc-quotes-list tbody td.wc-remainder .wc_remainder', function () {
		var t_this = jQuery(this);
		var data_id = t_this.attr("data-id");
		if (t_this.prop('checked') == true) {
			t_this.addClass('active');

			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: myAjax.ajaxurl,
				data: { action: 'wc_quote_mail_remainder', data_id: data_id },
				success: function (response) {
					t_this.removeClass('active');
					/*if( response.data_id ){
						t_this.parents('.wc-quotes-list').find('tbody tr td[data-id="'+response.data_id+'"] .wc-remainder .wc_remainder').prop('checked', true);
					}*/
				}
			});
		} else {
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: myAjax.ajaxurl,
				data: { action: 'wc_quote_mail_remainder_unsend', data_id: data_id },
				success: function (response) {
					/*if( response.data_id ){
						t_this.parents('.wc-quotes-list').find('tbody tr td[data-id="'+response.data_id+'"] .wc-remainder .wc_remainder').prop('checked', false);
					}*/
				}
			});
		}
	});

	//jQuery('.wp-foot-left .invoice_notes').summernote();

	jQuery(document).on('change', '#repeatable-fieldset-one tbody td select.wc-varitions-product', function () {
		var t_this = jQuery(this);
		var data_image = t_this.find('option:selected').attr('data-image');
		if (data_image) {
			t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').show();
			t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', data_image);
			t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val(data_image);
		} else {
			t_this.closest('tr').find('td.wc-repete-desc .wc-var-image').hide();
			//t_this.closest('tr').find('.wc-repete-desc .wc-var-image img').attr('src', '');
			//t_this.closest('tr').find('.wc-repete-desc .wc-var-image .wc-hidden-var-image').val('');
		}
	});

	function calculate_invoice(t_this) {
		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price').each(function (key, val) {
			var price_this = jQuery(this);
			var price_val = price_this.val();

			if (price_this.prop('checked') == true) {
				var subtotal = 0;
				var net_price_check_total = 0;
				var product = t_this.closest("tr").find('td.wc-repete-selector');
				var active = t_this.closest("tr").find('.wc-repete-unit .wc-unit-price .linePrice');
				var net_price_check = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');
				if (price_val != 'eqp') {
					var price_list = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-variation');
				} else {
					var price_list = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');
				}

				var currency = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
				var qty = t_this.closest("tr").find('td.wc-repete-qty .lineQty').val();
				var price = 0;

				if (!active.hasClass('active')) {
					price = get_price_according_to_qty(qty, price_list, t_this, net_price_check);
				} else {
					price = t_this.closest("tr").find('.wc-repete-unit .wc-unit-price .linePrice').val();
				}

				if (isNaN(price)) {
					price = 0;
				}
				var amount = parseFloat(price) * qty;
				if (isNaN(amount)) {
					amount = 0;
				}
				subtotal = subtotal + parseFloat(amount);
				t_this.closest("tr").find('td.wc-repete-unit .linePrice').val(price);
				t_this.closest("tr").find('td.wc-repete-total .lineTotal').val(parseFloat(amount.toFixed(2)));
				t_this.closest("tr").find('td.wc-repete-total .wc-total-currency span').html(currency);
			}
		});
	}

	function calculate_eqp_net_price(t_this) {

		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price').each(function (key, val) {
			var price_this = jQuery(this);
			var price_val = price_this.val();

			if (price_this.prop('checked') == true) {

				jQuery('#repeatable-fieldset-one tbody tr').each(function (key, val) {
					var t_this = jQuery(this);
					var subtotal = 0;
					var product = t_this.find('td.wc-repete-selector');
					var active = t_this.find('td.wc-repete-unit .wc-unit-price .linePrice');
					var net_price_check = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');
					if (price_val != 'eqp') {
						price_list = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-variation');
					} else {
						price_list = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-price');
					}

					var currency = t_this.find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
					var qty = t_this.find('td.wc-repete-qty .lineQty').val();
					var price = 0;

					if (!active.hasClass('active')) {
						price = get_price_according_to_qty(qty, price_list, t_this, net_price_check);
					} else {
						price = t_this.find('.wc-repete-unit .wc-unit-price .linePrice').val();
					}

					if (isNaN(price)) {
						price = 0;
					}
					var amount = parseFloat(price) * qty;
					if (isNaN(amount)) {
						amount = 0;
					}
					subtotal = subtotal + parseFloat(amount);
					t_this.find('td.wc-repete-unit .linePrice').val(price);
					t_this.find('td.wc-repete-total .lineTotal').val(parseFloat(amount.toFixed(2)));
					t_this.find('td.wc-repete-total .wc-total-currency span').html(currency);

				});
			}
		});

	}

	function get_price_according_to_qty(qty, price_list, t_this, net_price_check) {
		var price = price_list;

		//var wc_eqp_price = jQuery('.wp-quote-form-wrapper .wc-eos-price input[name="wc_eqp_price"]:checked').val();
		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price').each(function (key, val) {
			var eqp_this = jQuery(this);
			var wc_eqp_price = eqp_this.val();

			if (eqp_this.prop('checked') == true) {

				if (wc_eqp_price != 'eqp') {

					var qty_arr = [];
					var min_qty_arr = [];
					var max_qty_arr = [];

					if (price_list !== undefined) {
						var variation = JSON.parse(price_list);
						price = variation[0].price;
						if (qty == 0 || qty == '') {
							price = variation[0].price;
						}
						jQuery(variation).each(function (key, value) {
							if ((parseFloat(value.min_qty) <= parseFloat(qty) && parseFloat(qty) <= parseFloat(value.max_qty)) || (parseFloat(value.min_qty) <= parseFloat(qty) && parseFloat(value.max_qty) == -1)) {
								price = value.price;
							}
							min_qty_arr.push(value.min_qty);
							max_qty_arr.push(value.max_qty);
						});
					}

					var prd_min_qty = Math.min.apply(Math, min_qty_arr);
					var prd_max_qty = Math.min.apply(Math, max_qty_arr);

					if (wc_eqp_price == 'net') {
						if (qty < prd_min_qty) {
							net_price = net_price_check;
						}
						net_price = price * 0.6;
					}

					/*if( prd_max_qty != -1 ){ t_this.closest("tr").find('td.wc-repete-qty .lineQty').removeAttr("max");} 
					t_this.closest("tr").find('td.wc-repete-qty .lineQty').attr("max", prd_max_qty);
					t_this.closest("tr").find('td.wc-repete-qty .lineQty').attr("min", prd_min_qty);*/

				}
			}

		});

		price = parseFloat(price).toFixed(2);
		net_price = parseFloat(net_price).toFixed(2);
		t_this.closest("tr").find('td.wc-repete-unit .linePrice').val(price);
		t_this.closest("tr").find('td.wc-repete-net .netPrice').val(net_price);
		var currency = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
		t_this.closest("tr").find('td.wc-repete-unit .wc-unit-currency span').html(currency);
		var price_obj = {
			'price': price,
			'net_price': net_price,
		};
		return price_obj;
	}


	jQuery(document).on("change", '.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price', function (e) {
		jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-unit .wc-unit-price .linePrice').removeClass('active');
		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price').each(function (key, val) {
			var t_this = jQuery(this);
			var value = t_this.val();

			if (t_this.prop('checked') == true) {
				if (value == 'net') {
					jQuery('#repeatable-fieldset-one .repeatable-net-price').show();
					jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-qty .lineQty').val('');
					jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-total .wc-total-price #total').val('');
					jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text('');
					jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text('');
					jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val('');
					jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val('');
					calculate_eqp_net_price(t_this);
				}

				if (value == 'eqp') {
					calculate_eqp_net_price(t_this);
				}

				sum_all_data(t_this.parents('.wp-quote-form-wrapper').find('#repeatable-fieldset-one tbody tr:first-child td:first-child'));
			} else {
				if (value == 'net') {
					jQuery('#repeatable-fieldset-one .repeatable-net-price').hide();
				}
			}
		});
	});

	/*var wc_edited = jQuery('.wp-quote-form-wrapper .wc-eos-price');
	if( !wc_edited.hasClass('wc-edit') ){
		jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price').prop( 'checked', true );	
	}*/

	/*jQuery(document).on("change", '.wp-quote-form-wrapper .wc-eos-price .wc_eqp_price', function(e) {
		var t_this = jQuery(this);
		var value = t_this.val();
		t_this.each(function( key ,val ) {
			console.log(jQuery(this).val());
		});
		
		jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-unit .wc-unit-price .linePrice').removeClass('active')
		if( value == 'net'){
			//jQuery('#repeatable-fieldset-one tbody tr .wc-unit-price #unit_price').removeAttr('readonly');
			//jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-qty .lineQty').removeAttr('readonly');
			jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-qty .lineQty').val('');
			//jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-unit .wc-unit-price .linePrice').val('');
			jQuery('#repeatable-fieldset-one tbody tr td.wc-repete-total .wc-total-price #total').val('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val('');
			jQuery('.wp-quote-form-wrapper .wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val('');
			calculate_eqp_net_price( t_this );
		}
		if( value == 'eqp'){
			calculate_eqp_net_price( t_this );
			//jQuery('#repeatable-fieldset-one tbody tr .wc-unit-price #unit_price').attr('readonly', 'readonly');
		}
		sum_all_data( t_this.parents('.wp-quote-form-wrapper').find('#repeatable-fieldset-one tbody tr:first-child td:first-child') );
	});*/

	jQuery(document).on("keyup change", ".wc_invoice_repeter #repeatable-fieldset-one tbody td #qty", function (e) {
		calculate_invoice(jQuery(this));
		sum_all_data(jQuery(this));
	});

	jQuery(document).on("keyup change", '.wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="from_email"]', function (e) {
		var email_val = jQuery(this).val();
		if (email_val) {
			jQuery('form#quotes-form .wc-email-form .wc-quotes-client_email input[name="client_email"]').val(email_val);
		}
	});

	jQuery(document).on("keyup change", ".wc_invoice_repeter #repeatable-fieldset-one tbody td .linePrice", function (e) {
		jQuery(this).addClass('active');
		this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
		calculate_invoice(jQuery(this));
		sum_all_data(jQuery(this));
	});

	function sum_all_data(t_this) {
		var total = 0;
		var currency = t_this.closest("tr").find('td.wc-repete-varitions .wc-varitions-product option:selected').attr('data-currency');
		jQuery('#repeatable-fieldset-one tbody tr').each(function () {
			var amount = jQuery(this).find('td #total').val();
			if (amount == undefined || amount == '' || amount == NaN) {
				amount = 0;
			}
			total = total + parseFloat(amount);
		});

		if (total) {
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text(total.toFixed(2));
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc-sub-currency').text(currency);
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text(total.toFixed(2));
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc-total-currency').text(currency);
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val(total.toFixed(2));
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val(total.toFixed(2));
		} else {
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text('');
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc-sub-currency').text('');
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text('');
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc-total-currency').text('');
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val('');
			t_this.parents('.wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val('');
		}
	}

	jQuery(document).on('change', '.wp-upload-logo input#logo_file_input', function () {
		var t_this = jQuery(this);
		var fd = new FormData();
		var files = jQuery('form.wc_quote_form .wp-paper .wp-meta-right #logo_file_input')[0].files[0];
		if (typeof files !== undefined) {
			fd.append('file', files);
		}
		fd.append('action', 'get_wc_priview_quotes');
		t_this.parents('.wp-upload-logo ').find('.wp-clearfix .right').addClass('active');

		jQuery.ajax({
			type: "post",
			url: myAjax.ajaxurl,
			data: fd,
			contentType: false,
			processData: false,
			success: function (response) {
				t_this.parents('.wp-upload-logo ').find('.wp-clearfix .right').removeClass('active');
				if (response.path) {
					jQuery('.wp-invoice-quete .wp-upload-logo input[name="image_url"]').val(response.path.url);
					jQuery('.wp-quote-form-wrapper .wp-meta-right #upload_area img').attr('src', response.path.url);
					jQuery('.wp-invoice-quete .wp-upload-logo').addClass('wc-hide-logo');
				} else {
					jQuery('.wp-invoice-quete .wp-upload-logo').removeClass('wc-hide-logo');
				}
				if (response.attach_id) {
					jQuery('.wp-invoice-quete .wp-upload-logo input[name="file_id"]').val(response.attach_id);
				}
			}
		});
	});

	jQuery(document).on('click', '.wp-paper .wp-quote-form-wrapper .wc-reset-wrap .wc-reset', function () {
		jQuery('.wp-quote-form-wrapper .wc-eos-price input[name="wc_eqp_price"]').prop('checked', false);
		calculate_eqp_net_price(jQuery(this));
	});

	/*jQuery(document).on('click','.wp-tabs .tabs li a.wc-pdf',function(){
		var doc = new jsPDF();
		var elementHTML = jQuery('.wp-paper .wp-viewinvoice-form-wrapper.print-html-hidden').html();
		var specialElementHandlers = {
			'#toPdf': function (element, renderer) {
				return true;
			}
		};
		doc.fromHTML(elementHTML, 5, 5, {
			'width': 170, 
			'elementHandlers': specialElementHandlers
		},
		function(bla){doc.save('sample-document.pdf');});
	});	*/

	jQuery(document).on('change', '#wp-options .wp-options-wrapper .wc_top_logo', function () {
		jQuery(this).toggleClass('active');
		if (jQuery(this).hasClass('active')) {
			jQuery('.wp-quote-form-wrapper .wp-meta .wp-meta-right .wp-invoice-quete .wp-upload-logo').show();
		} else {
			jQuery('.wp-quote-form-wrapper .wp-meta .wp-meta-right .wp-invoice-quete .wp-upload-logo').hide();
		}
	});

	jQuery(document).on('change', '#wp-options .wp-options-wrapper .wc_top_po', function () {
		jQuery(this).toggleClass('active');
		if (jQuery(this).hasClass('active')) {
			jQuery('.wp-quote-form-wrapper .wp-meta .wp-meta-right .wp-form-invoice-dates .po-number').css('display', 'flex');
		} else {
			jQuery('.wp-quote-form-wrapper .wp-meta .wp-meta-right .wp-form-invoice-dates .po-number').hide();
		}
	});

	jQuery(document).on('click', '.wp-tabs .tabs li a.wc-print', function () {
		wc_edit_preview_quotes_data();
		var table_html = '';
		table_html += jQuery('.wp-paper .wp-viewinvoice-form-wrapper.print-html-hidden').html();
		if (table_html) {
			printDiv(table_html);
		}
	});

	jQuery(document).on('click', '#quotes-form #wc-quotes-sub', function () {
		var t_this = jQuery(this);
		wc_edit_preview_quotes_data();
		var quote_html = jQuery('form.wc_quote_form .wp-paper .print-html-hidden')[0].outerHTML;
		var serialized = jQuery('#quotes-form').serialize();

		var name = jQuery(this).parents('.wc-email-form').find('.wc-quotes-fname #name').val();
		var lname = jQuery(this).parents('.wc-email-form').find('.wc-quotes-lname #lname').val();
		var wc_email = jQuery(this).parents('.wc-email-form').find('.wc-quotes-email #email').val();
		var client_email = jQuery(this).parents('.wc-email-form').find('.wc-quotes-client_email #client_email').val();
		wc_email = wc_email.trim();
		client_email = client_email.trim();
		var error = '';
		var wc_flag = 0;
		jQuery(this).parents('#quotes-form').find('.wc_error').remove();
		if (name == '') {
			error += '<p class="wc_error">please enter your Customer name</p>';
			wc_flag = 1;
		}

		/*if( lname == '' ){
			error += '<p class="wc_error">please enter your last name</p>'; 
			wc_flag= 1;
		}*/
		var errflag = 0;
		if (wc_email == '') {
			error += '<p class="wc_error">Email is required.</p>';
			wc_flag = 1;
		} else if (wc_email) {
			var emails = wc_email.split(',');
			jQuery(emails).each(function (key, val) {
				var temails = val.trim();
				if (!IsEmail(temails)) {
					errflag = 1;
				}
			});
			if (errflag == 1) {
				error += '<p class="wc_error">Email is not valid.</p>';
				wc_flag = 1;
			}
		}

		if (client_email == '') {
			error += '<p class="wc_error">From Email is required.</p>';
			wc_flag = 1;
		} else if (!IsEmail(client_email)) {
			error += '<p class="wc_error">From Email is not valid.</p>';
			wc_flag = 1;
		}

		if (wc_flag == 0) {
			t_this.addClass('active');
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: myAjax.ajaxurl,
				data: { action: 'wc_quote_send_data', quote_html: quote_html, serialized: serialized },
				success: function (response) {
					t_this.removeClass('active');

					jQuery('#quotes-form .wc-quotes-submit').before('<p class="wt-success">Your Form Successfully Done.</p>');
					jQuery('.wc-quotes-fname #name').val('');
					jQuery('.wc-quotes-client_email #client_email').val('');
					jQuery('.wc-quotes-lname #lname').val('');
					jQuery('.wc-quotes-email #email').val('');

					setTimeout(function () {
						jQuery.magnificPopup.close();
					}, 2000);

					/*if( response.msg ){
						if( response.type == 1 ){
							success_class =  'wc-success-mail';
						} else {
							success_class =  'wc-error-mail';
						}
						t_this.parents('form.wc_quote_form').find('.wp-tabs').before('<div class="wc-mail-msg"><span class="'+ success_class +'">'+ response.msg +'</span></div>');

						setTimeout(function(){ 
							t_this.parents('form.wc_quote_form').find('.wc-mail-msg .wc-success-mail').remove();
							t_this.parents('form.wc_quote_form').find('.wc-mail-msg .wc-error-mail').remove();
						}, 3000);
					}*/
				}
			});
		} else {
			jQuery('#quotes-form .wc-email-form').before(error);
		}
		return false;
	});

	jQuery(document).on('click', '#quotes-form .quotes-modal-dismiss', function (e) {
		e.preventDefault();
		jQuery.magnificPopup.close();
	});

	jQuery('.quotes-popup').magnificPopup({
		type: 'inline',
		preloader: false,
		callbacks: {
			open: function () {
				jQuery('#quotes-form .wt-success').remove();
			},
			close: function () {
			}
		}
	});


	/*jQuery(document).ready( function($) {
		jQuery(document).on("keyup", ".growTextarea:not(.focus)", function(e) {

			var value = jQuery(this).val();
			var contentAttr = jQuery(this).attr('name');

			jQuery('.'+contentAttr+'').html(value.replace(/\r?\n/g,'<br/>'));

		});
	});*/
});

function IsEmail(email) {
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!regex.test(email)) {
		return false;
	} else {
		return true;
	}
}

/*jQuery( function() {
	jQuery( document ).tooltip({
		my: "top center",
		at: "bottom center",
	});
} );*/


function printDiv(table_html, ord_table_html) {

	var newWin = window.open('', 'Print-Window');

	newWin.document.open();

	newWin.document.write('<html><body onload="window.print()">' + table_html + '</body></html>');

	newWin.document.close();

	setTimeout(function () { newWin.close(); }, 1000);

}

function wc_edit_preview_quotes_data() {
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
	var notes = jQuery('.wp-foot .wp-foot-left #invoice_notes').val();
	var sub_total = jQuery('.wp-foot .wp-foot-right tbody .wc-sub').text();
	var total = jQuery('.wp-foot .wp-foot-right tbody .wc-total').text();
	//var amnt_paid = jQuery('.wp-foot .wp-foot-right tbody .wc-amt-paid').text();
	//var balance = jQuery('.wp-foot .wp-foot-right tbody .wc-balance').text();

	var table_html = '';
	jQuery('#repeatable-fieldset-one tbody tr').each(function (index, value) {
		var wc_quotes_td = '';
		var prod_selected = jQuery(this).find('.wc-repete-selector select.wc-product option:selected').text();
		var variation_selected = jQuery(this).find('.wc-repete-varitions select.wc-varitions-product option:selected').text();
		var currency = jQuery(this).find('.wc-repete-varitions select.wc-varitions-product option:selected').attr('data-currency');
		var prod_desc = jQuery(this).find('.wc-repete-desc .wc-desc').val();
		var variation_img = jQuery(this).find('.wc-repete-desc .wc-var-image img').attr('src');
		var prod_price = jQuery(this).find('.wc-repete-unit #unit_price').val();
		var prod_qty = jQuery(this).find('.wc-repete-qty #qty').val();
		var prod_total = jQuery(this).find('.wc-repete-total #total').val();
		if (prod_desc === undefined) { prod_desc = ''; }
		if (variation_img === undefined) { variation_img = ''; }
		if (prod_selected === undefined) { prod_selected = ''; }
		if (variation_selected === undefined) { variation_selected = ''; }
		if (prod_price === undefined) { prod_price = ''; }
		if (prod_qty === undefined) { prod_qty = ''; }
		if (prod_total === undefined) { prod_total = ''; }
		if (currency === undefined) { currency = ''; }

		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + prod_selected + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + variation_selected + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8; position: relative;'>" + '<img style="width: 58px;height: 50px;display: block;object-fit: scale-down;margin: 0 auto;border: 1px solid #e6e6e6;padding: 5px;object-position: center; position: absolute; top: 10px; left: 50%; transform: translateX(-50%);" src="' + variation_img + '">' + ' <span style="display:block; margin-top: 65px;" class="wc-text-left">' + prod_desc + "</span></td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + currency + '' + prod_price + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + prod_qty + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + currency + '' + prod_total + "</td>";

		if (table_html !== undefined) {
			table_html += "<tr>" + wc_quotes_td + "</tr>";
		}
	});

	if (from_company_name) {
		jQuery('.wp-paper .invoice-from-address .wc-from-comapny-name').text(from_company_name);
	}

	if (from_your_name) {
		jQuery('.wp-paper .invoice-from-address .wc-from-name').text(from_your_name);
	}

	if (from_email) {
		jQuery('.wp-paper .invoice-from-address .wc-from-email').text(from_email);
	}

	if (to_company_name) {
		jQuery('.wp-paper .invoice-from-toname .wc-to-comapny-name').text(to_company_name);
	}

	if (to_your_name) {
		jQuery('.wp-paper .invoice-from-toname .wc-to-name').text(to_your_name);
	}

	if (to_email) {
		jQuery('.wp-paper .invoice-from-toname .wc-to-email').text(to_email);
	}

	if (to_telphone) {
		jQuery('.wp-paper .invoice-from-toname .wc-to-phone').text(to_telphone);
	}

	if (from_your_address) {
		jQuery('.wp-paper .invoice-from-address .wc-from-address').text(from_your_address);
	}

	if (to_your_address) {
		jQuery('.wp-paper .invoice-from-toname .wc-to-address').text(to_your_address);
	}

	if (image_url) {
		jQuery('.viewinvoice-address-logo .invoice-from-logo img').attr('src', image_url);
	}

	if (invoice_number) {
		jQuery('.viewinvoice-toname-table .invoice-table table tbody td.preview-invoice-id').text(invoice_number);
	}

	if (tex_number) {
		jQuery('.viewinvoice-toname-table .invoice-table table tbody .preview-po').text(tex_number);
	}

	if (trip_start == '') {
		jQuery('.viewinvoice-toname-table .invoice-table table tbody .preview-invoice-date').text('');
	} else {
		jQuery('.viewinvoice-toname-table .invoice-table table tbody .preview-invoice-date').text(trip_start);
	}

	if (trip_end == '') {
		jQuery('.viewinvoice-toname-table .invoice-table table tbody .preview-due-date').text('');
	} else {
		jQuery('.viewinvoice-toname-table .invoice-table table tbody .preview-due-date').text(trip_end);
	}

	if (table_html) {
		jQuery('.viewinvoice-dataTable table tbody tr.quotes-append-html').after(table_html);
		jQuery('.viewinvoice-dataTable table tbody tr.quotes-append-html').remove();
	}

	if (notes) {
		jQuery('.viewinvoice-dataTable tbody .invoice-notes').text(notes);
	}

	if (sub_total) {
		jQuery('.invoice-subtotal-table tbody .wc-preview-subtotal').text(sub_total);
	}

	if (total) {
		jQuery('.invoice-subtotal-table tbody .wc-preview-total').text(total);
	}
}

// product_image popup js
jQuery(document).ready(function ($) {
	jQuery(document).on('click','.woocommerce-product-gallery__image', function(){
			jQuery(document).find('.wp_popup_image .wd-carousel-inner .wd-carousel-item').click();
	});
});