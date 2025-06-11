<?php
add_action( 'wp_footer', 'wc_add_custom_css_js_fun' );
function wc_add_custom_css_js_fun(){
	if( is_page(  array( 3423440248 , 3423440255 ) ) ){
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {

				jQuery('.hidden-print .wp-options-wrapper .wc_top_logo').prop('checked', true);

				jQuery( document ).on('click','.wc_custom_invoice_repeter #add-custom-row', function() {
					var count = jQuery(this).attr('data-count');
					count = parseInt(count) + 1;
					jQuery(this).attr('data-count',count);
					var row = jQuery( '.empty-row.custom-screen-reader-text' ).html().replace(/rand_no/g, count);      
					if( jQuery('#custom-repeatable-fieldset-one tbody>tr:last').length == 0  )  {    
						jQuery('#custom-repeatable-fieldset-one tbody').append('<tr>'+row+'</tr>');
					} else {
						jQuery('#custom-repeatable-fieldset-one tbody>tr:last').after('<tr>'+row+'</tr>');
					}
					return false;
				});

				jQuery(document).on('click','.wc_custom_invoice_repeter .custom-remove-row',function(){
					jQuery(this).parent().parent().remove();
					return false;
				});
				jQuery("body").on('click', '.wc_custom_quote_form #btnToggleOptions', function () {
					jQuery(".wc_custom_quote_form #wp-options").slideToggle();
				});

				jQuery(document).on('click','.wc_custom_quote_form .wp-tabs .tabs .wc-preview',function(){
					wc_custom_edit_preview_quotes_data();
					jQuery('.wc_custom_quote_form .wp-tabs .tabs .wc-preview').toggleClass('active');
					if( !jQuery(this).hasClass('active') ){
						jQuery('.wc_custom_quote_form .wp-tabs .tabs .wc-preview').text('Edit');
						jQuery('.wc_custom_quote_form .wp-paper .wp-viewinvoice-form-wrapper').show();
						jQuery('.wc_custom_quote_form .wp-paper .wp-quote-form-wrapper').hide();
					} else{
						jQuery('.wc_custom_quote_form .wp-tabs .tabs .wc-preview').text('Preview');
						jQuery('.wc_custom_quote_form .wp-paper .wp-viewinvoice-form-wrapper').hide();
						jQuery('.wc_custom_quote_form .wp-paper .wp-quote-form-wrapper').show();
					}
				});

				jQuery('form.wc_custom_quote_form .wp-tabs .save-btn .wc-save.disabled').magnificPopup({
					type: 'inline',
					preloader: false,
					modal: true
				});

				jQuery(document).on( 'click', '.logged-modal-dismiss', function(e) {
					e.preventDefault();
					jQuery.magnificPopup.close();
				});

				jQuery(document).on("click", 'form.wc_custom_quote_form .wp-tabs .save-btn .wc-save', function(e) {
					e.preventDefault();
					if( (!jQuery(this).hasClass('disabled'))  && (!jQuery(this).hasClass('wc-disabled')) ){
						jQuery('.wc_custom_quote_form .comman_editor').each(function(){
							var myEditor = jQuery(this);
							var html = myEditor.find('.ql-editor').html();
							jQuery(this).siblings('.wc_custom_quote_form .comman_editor_val').val(html);
						});
						var t_this = jQuery(this);
						var serialized = jQuery('.wc_custom_quote_form').serialize();

						var to_your_name = jQuery('form.wc_custom_quote_form .wp-paper .wp-meta-from input[name="to_your_name"]').val();
						var from_your_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="from_your_name"]').val();
						var from_company_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="from_company_name"]').val();
						var to_company_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from.wp-meta-to input[name="to_comapny_name"]').val();
						var from_email = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="from_email"]').val();
						var to_email = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="to_email"]').val();

						to_your_name = to_your_name.trim();
						from_your_name = from_your_name.trim();
						from_company_name = from_company_name.trim();
						to_company_name = to_company_name.trim();
						from_email = from_email.trim();
						to_email = to_email.trim();
						var error = '';
						var wc_flag = 0;
						jQuery('form.wc_custom_quote_form .wc_error').remove();

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
							var files = jQuery('form.wc_custom_quote_form .wp-paper .wp-meta-right #logo_file_input')[0].files[0];
							if (typeof files !== undefined) {
								fd.append('file', files);
							}
							fd.append('serialized', serialized);
							fd.append('action', 'wc_custom_quote_user_data');

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
							jQuery('form.wc_custom_quote_form .wp-tabs').before(error);
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

				jQuery(document).on('change','.wc_custom_quote_form #custom-repeatable-fieldset-one .wc-repete-image .wc-product-image',function(){
					var t_this = jQuery(this);
					var fd = new FormData();
					var files = t_this[0].files[0];
					if(typeof files !== undefined) {
						fd.append('file', files);
					}
					fd.append('action', 'get_wc_custom_product_image');
					t_this.addClass('active');
					t_this.parent('.wc-repete-image').addClass('loading');
					t_this.parents('.wc_custom_quote_form').find('.wc-save').addClass('wc-disabled');

					jQuery.ajax({
						type: "post",
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						data: fd,
						contentType: false,
						processData: false,
						success: function (response) {
							t_this.removeClass('active');
						//t_this.hide();
						t_this.parent('.wc-repete-image').removeClass('loading');
						t_this.parent('.wc-repete-image').find('.wc-pro-image').addClass('active');
						t_this.parents('.wc_custom_quote_form').find('.wc-save').removeClass('wc-disabled');
						if( response.path ){
							t_this.parent().find('.wc-pro-image .wc-hidden-pro-image').val(response.path);
							t_this.parent().find('.wc-pro-image img').attr('src', response.path);
							t_this.parent().find('.wc-pro-image img').attr('width', '300');
							t_this.parent().find('.wc-pro-image img').attr('height', '300');
							t_this.parent().find('.wc-pro-image .product_file_id').val('');
						} 
						/*if( response.attach_id ){
							t_this.parent().find('.wc-pro-image .product_file_id').val(response.attach_id);
						}*/
					}
				});
				});

			/*jQuery(document).on('click','.wc_custom_quote_form #custom-repeatable-fieldset-one .wc-repete-image .wc-pro-image a',function(){
				var t_this = jQuery(this);
				t_this.parents('.wc-repete-image').find('.wc-pro-image').hide();
				t_this.parents('.wc-repete-image').find('.wc-product-image').show();

				if( t_this.parent('.wc-repete-image').find('.wc-pro-image').hasClass('active') ){
					t_this.parents('.wc-repete-image').find('.wc-product-image').hide();
				}
			});*/


			function NumWithCommas(number) {
				number = parseFloat(number).toFixed(2);
				var parts = number.toString().split(".");
				parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				return parts.join(".");
			}

			jQuery(document).on("keyup change", ".wc_custom_quote_form #custom-repeatable-fieldset-one tbody td .linePrice", function(e) {	
				var t_this = jQuery(this); 
				wc_unit_price( t_this );
			});

			jQuery(document).on("keyup change", ".wc_custom_quote_form  #custom-repeatable-fieldset-one tbody td #qty", function(e) {
				var t_this = jQuery(this);
				wc_unit_price( t_this );
				sum_all_data( t_this );
			});

			function wc_unit_price( t_this ) {
				var linePrice = t_this.closest("tr").find('.wc-repete-unit .wc-unit-price .linePrice').val();
				var qty = t_this.closest("tr").find('td.wc-repete-qty .lineQty').val();
				var price = 0;

				if( linePrice && qty ){
					price = parseFloat(linePrice) * qty;
				}

				if( isNaN( price ) ){
					price = 0;
				}

				t_this.closest("tr").find('td.wc-repete-total .lineTotal').val(NumWithCommas(price.toFixed(2)));
			}

			function sum_all_data( t_this ) {
				var total = 0;
				var currency = t_this.closest("tr").find('td.wc-repete-unit .wc-unit-price .wc-unit-currency span').text();
				jQuery('#custom-repeatable-fieldset-one tbody tr').each( function() {
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
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text(NumWithCommas(total));
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc-sub-currency').text(currency);
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text(NumWithCommas(total));
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc-total-currency').text(currency);
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val(NumWithCommas(total));
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val(NumWithCommas(total));
				} else {
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc_value').text('');
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub span.wc-sub-currency').text('');	
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc_value').text('');
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total span.wc-total-currency').text('');
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-sub input[name="foot_sub_total"]').val('');
					t_this.parents('.wc_custom_quote_form .wp-quote-form-wrapper').find('.wp-foot .wp-foot-right tbody td.wc-total input[name="foot_total"]').val('');
				}
			}

			wc_custom_customer_email_autofill();
			jQuery(document).on("keyup change", '.wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="from_email"], .wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="to_your_name"]', function(e) {
				wc_custom_customer_email_autofill();
			});

			jQuery(document).on('change','.wc_custom_quote_form .wp-upload-logo input#logo_file_input',function(){
				var t_this = jQuery(this);
				var fd = new FormData();
				var files = jQuery('form.wc_custom_quote_form .wp-paper .wp-meta-right #logo_file_input')[0].files[0];
				if(typeof files !== undefined) {
					fd.append('file', files);
				}
				fd.append('action', 'get_wc_custom_priview_quotes');
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
							jQuery('.wc_custom_quote_form .wp-invoice-quete .wp-upload-logo input[name="image_url"]').val(response.path.url);
							jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-right #upload_area img').attr('src', response.path.url);
						} 

						if( response.attach_id ){
							jQuery('.wc_custom_quote_form .wp-invoice-quete .wp-upload-logo input[name="file_id"]').val(response.attach_id);
						}
					}
				});
			});

			jQuery(document).on('click','.wc_custom_quote_form .wp-tabs .tabs li a.wc-pdf',function(e){
				var t_this = jQuery(this);
				wc_custom_edit_preview_quotes_data();
				jQuery('form.wc_custom_quote_form .wp-paper .print-html-hidden').show();
				var html = jQuery('form.wc_custom_quote_form .wp-paper .print-html-hidden')[0].outerHTML;
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

			jQuery(document).on('click','.wc_custom_quote_form .wp-paper .wp-quote-form-wrapper .wc-reset-wrap .wc-reset',function(){
				jQuery('.wp-quote-form-wrapper .wc-eos-price .wc_checkbox').prop('checked', false);
				jQuery('#custom-repeatable-fieldset-one thead tr th.wc-text-label').text('Unit Price');
				calculate_eqp_net_price( jQuery(this) );
			});

			jQuery(document).on('change','.wc_custom_quote_form #wp-options .wp-options-wrapper .wc_top_logo',function(){
				jQuery(this).toggleClass('active');
				if( jQuery(this).hasClass('active') ){
					jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-invoice-quete .wp-upload-logo').show();
				} else {
					jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-invoice-quete .wp-upload-logo').hide();
				}
			});	

			jQuery(document).on('change','.wc_custom_quote_form #wp-options .wp-options-wrapper .wc_top_po',function(){
				jQuery(this).toggleClass('active');
				if( jQuery(this).hasClass('active') ){
					jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-form-invoice-dates .po-number').css('display', 'flex');
				} else {
					jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-right .wp-form-invoice-dates .po-number').hide();
				}
			});	

			jQuery(document).on('click','.wc_custom_quote_form .wp-tabs .tabs li a.wc-print',function(){
				wc_custom_edit_preview_quotes_data();
				var table_html  = '';
				table_html += jQuery('.wc_custom_quote_form .wp-paper .wp-viewinvoice-form-wrapper.print-html-hidden').html();
				if( table_html ) {
					printDiv(table_html);
				}
			});

			jQuery(document).on('click','#quotes-form #wc-quotes-sub',function(){
				var t_this = jQuery(this);
				wc_custom_edit_preview_quotes_data();
				var quote_html = jQuery('form.wc_custom_quote_form .wp-paper .print-html-hidden')[0].outerHTML;
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

			jQuery('.wc_custom_quote_form .quotes-popup').magnificPopup({
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
	
	if( jQuery('.wc_custom_quote_form #editor').length > 0 ){
		var editor = new Quill('.wc_custom_quote_form #editor', options);
	}
	if( jQuery('.wc_custom_quote_form #extra_editor').length > 0 ){
		var editor = new Quill('.wc_custom_quote_form #extra_editor', options);
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

function printDiv(table_html) {

	var newWin = window.open('','Print-Window');

	newWin.document.open();

	newWin.document.write('<html><body onload="window.print()">'+table_html+'</body></html>');

	newWin.document.close();

	setTimeout(function(){newWin.close();},1000);

}

function wc_custom_edit_preview_quotes_data() {
	jQuery('.wc_custom_quote_form .comman_editor').each(function(){
		var myEditor = jQuery(this);
		var html = myEditor.find('.ql-editor').html();
		jQuery(this).siblings('.wc_custom_quote_form .comman_editor_val').val(html);
	});

	var header_text = jQuery('.wc_custom_quote_form #custom-repeatable-fieldset-one thead tr th.wc-text-label').text();
	var from_your_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="from_your_name"]').val();
	var to_your_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="to_your_name"]').val();
	var from_your_address = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from .from_your_address').val();
	var to_your_address = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from .to_your_address').val();
	var from_company_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="from_company_name"]').val();
	var to_company_name = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from.wp-meta-to input[name="to_comapny_name"]').val();
	var from_email = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="from_email"]').val();
	var to_email = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="to_email"]').val();
	var to_telphone = jQuery('.wc_custom_quote_form .wp-quote-form-wrapper .wp-meta-from input[name="to_telphone"]').val();
	var image_url = jQuery('.wc_custom_quote_form .wp-meta-right .wp-upload-logo input[name="image_url"]').val();
	var invoice_number = jQuery('.wc_custom_quote_form .wp-meta-right .wp-form-invoice-dates input[name="invoice_number"]').val();
	var tex_number = jQuery('.wc_custom_quote_form .wp-meta-right .wp-form-invoice-dates input[name="tex_number"]').val();
	var trip_start = jQuery('.wc_custom_quote_form .wp-meta-right .wp-form-invoice-dates input[name="trip-start"]').val();
	var trip_end = jQuery('.wc_custom_quote_form .wp-meta-right .wp-form-invoice-dates input[name="trip-end"]').val();
	var notes = jQuery('.wc_custom_quote_form .wp-foot .wp-foot-left #invoice_notes').text();
	var extra_notes = jQuery('.wc_custom_quote_form .wp-foot .wp-foot-left .wc_extra_comments').val();
	var sub_total = jQuery('.wc_custom_quote_form .wp-foot .wp-foot-right tbody .wc-sub').text();
	var total = jQuery('.wc_custom_quote_form .wp-foot .wp-foot-right tbody .wc-total').text();

	var table_html = '';
	jQuery('.wc_custom_quote_form #custom-repeatable-fieldset-one tbody tr').each( function( index,value ) {
		var wc_quotes_td = '';
		var prod_img = jQuery(this).find('.wc-repete-image .wc-pro-image img').attr('src');
		var currency = jQuery(this).find('td.wc-repete-unit .wc-unit-price .wc-unit-currency span').text();
		var prod_desc = jQuery(this).find('.wc-repete-desc .wc-desc').val();
		var prod_price = jQuery(this).find('.wc-repete-unit .linePrice').val();
		var prod_qty = jQuery(this).find('.wc-repete-qty .lineQty').val();
		var prod_total = jQuery(this).find('.wc-repete-total .lineTotal').val();
		if( prod_desc === undefined ){ prod_desc = ''; }
		if( prod_img === undefined ){ prod_img = ''; }
		if( prod_price === undefined ){ prod_price = ''; }
		if( prod_qty === undefined ){ prod_qty = ''; }
		if( prod_total === undefined ){ prod_total = ''; }
		if( currency === undefined ){ currency = ''; }

		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'><img src="+prod_img+" style='width: 300px;object-fit: cover;'></td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8; padding:0; position: relative;'>"+prod_desc+"</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + currency+''+prod_price + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8;'>" + prod_qty + "</td>";
		wc_quotes_td += "<td style='text-align: center;padding: 10px;border: 1px solid #00abc8; color: #00abc8; font-weight: 700;'>" + currency+''+prod_total + "</td>";
		
		if( table_html !== undefined && prod_img != ''  ){
			table_html += "<tr class='wc-sub-tr'>"+ wc_quotes_td +"</tr>";
		}	
	});

	if( header_text ){
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable tbody tr.wc-notes-pricing .wc-text').html('<strong>'+header_text+'</strong>');
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable thead th.wc-hide-price').html(header_text);
	}

	if( header_text == 'Unit Price' ){
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable tbody tr.wc-notes-pricing .wc-text').html('<strong>(C)</strong>');	
	}

	if( from_company_name ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-address .wc-from-comapny-name').text(from_company_name);
	}

	if( from_your_name ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-address .wc-from-name').text(from_your_name);
	} 

	if( from_email ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-address .wc-from-email').text(from_email);
	}	

	if( to_company_name ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-toname .wc-to-comapny-name').text(to_company_name);
	} 

	if( to_your_name ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-toname .wc-to-name').text(to_your_name);
	} 

	if( to_email ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-toname .wc-to-email').text(to_email);
	} 

	if( to_telphone ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-toname .wc-to-phone').text(to_telphone);
	} 

	if( from_your_address ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-address .wc-from-address').text(from_your_address);
	} 

	if( to_your_address ){
		jQuery('.wc_custom_quote_form .wp-paper .invoice-from-toname .wc-to-address').text(to_your_address);
	} 

	if( image_url ){
		jQuery('.viewinvoice-address-logo .invoice-from-logo img').attr('src', image_url);
	} 

	if( invoice_number ){
		jQuery('.wc_custom_quote_form .viewinvoice-toname-table .invoice-table table tbody td.preview-invoice-id').text(invoice_number);
	} 

	if( tex_number ){
		jQuery('.wc_custom_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-po').text(tex_number);
	} 


	if( trip_start == ''){
		jQuery('.wc_custom_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-invoice-date').text('');
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
		jQuery('.wc_custom_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-invoice-date').text(string_date);
	}

	if( trip_end == '' ){
		jQuery('.wc_custom_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-due-date').text('');
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
		jQuery('.wc_custom_quote_form .viewinvoice-toname-table .invoice-table table tbody .preview-due-date').text(string_end_date);
	} 

	if( table_html ){
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable table tbody tr.wc-sub-tr').remove();
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable table tbody tr.quotes-append-html').after(table_html);
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable table tbody tr.quotes-append-html').hide();
	}

	if( notes ){
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable tbody .invoice-notes').html(notes);
	} 
	
	if( extra_notes ){
		jQuery('.wc_custom_quote_form .viewinvoice-dataTable tbody .wc-extra-notes .wc-extra-text').html(extra_notes);
	} 

	if( sub_total ){
		jQuery('.wc_custom_quote_form .invoice-subtotal-table tbody .wc-preview-subtotal').text(sub_total);
	} 

	if( total ){
		jQuery('.invoice-subtotal-table tbody .wc-preview-total').text(total);
	} 
}

function wc_custom_customer_email_autofill() {
	var email_val = jQuery('.wc_custom_quote_form .wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="from_email"]').val();
	var customer_name = jQuery('.wc_custom_quote_form .wp-paper .wp-quote-form-wrapper .wp-meta-from input[name="to_your_name"]').val();
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

add_action('wp_head','wc_custom_quote_css_func');
function wc_custom_quote_css_func(){
	?>
	<style>
		.wc_custom_quote_form .wc-repete-image.loading .wc-loader {
			display: block;
		}
		.wc_custom_quote_form .wc-repete-image .wc-loader {
			border: 5px solid #f3f3f3;
			border-top: 5px solid #3498db;
			width: 40px;
			height: 40px;
			margin: 0 auto 10px;
		}
		.wc_custom_quote_form input.wc-product-image {
			margin-top: 10px;
		}
		.wc-repete-image.loading .wc-pro-image {
			display: none;
		}
		.wp-custom-datatable.wc_custom_invoice_repeter .wc-unit-price, .wp-custom-datatable.wc_custom_invoice_repeter .wc-total-price {
			display: flex;
			align-items: center;
		} 
		.wc_custom_quote_form .wc-save.wc-disabled{
			opacity: 0.6;
			cursor: not-allowed;
		}
	</style>
	<?php
}