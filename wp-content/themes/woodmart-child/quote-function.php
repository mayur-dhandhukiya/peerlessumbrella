<?php
add_shortcode('wc_repeter', 'wc_repeter_fun');
function wc_repeter_fun(){
	ob_start();
	?>
	<div class="wc_invoice_repeter wp-datatable">
		<table id="repeatable-fieldset-one" width="100%">
			<tbody>
				<tr>
					<th></th>
					<th>Item</th>
					<th>Description</th>
					<th>Unit Price</th>
					<th>Quantity</th>
					<th>Amount</th>
				</tr>
				<tr>
					<td>
						<button tabindex="-1" type="button" class="button remove-row btnDeleteRow"><i class="material-icons"></i></button>
						<!-- <a href="javascript:;" class="button remove-row"><i class="material-icons"></i></a> -->
					</td>
					<td>
						<select class="form-select" aria-label="Default select example" name="invoice_data[0][item]"> 
							<option selected=""></option>
							<option value="days">Days</option>
							<option value="hours">Hours</option>
							<option value="product">Product</option>
							<option value="service">Service</option>
							<option value="expense">Expense</option>
							<option value="discount">Discount</option>
						</select>
					</td>
					<td>
						<textarea name="invoice_data[0][description]" rows="2" cols="50" aria-required="true" aria-invalid="false" placeholder=""></textarea>
					</td>
					<td>
						<input type="number" name="invoice_data[0][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
					</td>
					<td>
						<input type="number" name="invoice_data[0][qty]" id="qty" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0.00">
					</td>
					<td>
						<input type="text" tabindex="-1" name="invoice_data[0][total]" id="total" class="lineTotal" placeholder="0.00" value="" readonly="">
					</td>
				</tr>
				<tr>
					<td>
						<button tabindex="-1" type="button" class="button remove-row btnDeleteRow"><i class="material-icons"></i></button>
						<!-- <a href="javascript:;" class="button remove-row"><i class="material-icons"></i></a> -->
					</td>

					<td>
						<select class="form-select" aria-label="Default select example" name="invoice_data[1][item]"> 
							<option selected=""></option>
							<option value="days">Days</option>
							<option value="hours">Hours</option>
							<option value="product">Product</option>
							<option value="service">Service</option>
							<option value="expense">Expense</option>
							<option value="discount">Discount</option>
						</select>
					</td>
					<td>
						<textarea name="invoice_data[1][description]" rows="2" cols="50" aria-required="true" aria-invalid="false" placeholder=""></textarea>
					</td>
					<td>
						<input type="number" name="invoice_data[1][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
					</td>
					<td>
						<input type="number" name="invoice_data[1][qty]" id="qty" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0.00">
					</td>
					<td>
						<input type="text" tabindex="-1" name="invoice_data[1][total]" id="total" class="lineTotal" placeholder="0.00" value="" readonly="">
					</td>
				</tr>
			</tbody>
		</table>
		<div class="button-row">
			<button type="button" class="btn btn-xs btnAddRow" id="add-row" data-count="2">
				<i class="icon-new"></i><span>New Line</span>
			</button>
		</div>
		<!-- <p><a  class="button btn btn-xs btnAddRow" href="javascript:;" data-count="2"><i class="icon-new"></i><span>New Line</span></a></p> -->
		<div class="wc_ship_repeter">
			<div style="display: none;">
				<table>
					<tr class="empty-row screen-reader-text">
						<td>
							<button tabindex="-1" type="button" class="button remove-row btnDeleteRow"><i class="material-icons"></i></button>
							<!-- <a href="javascript:;" class="button remove-row"><i class="material-icons"></i></a> -->
						</td>
						<td>
							<select class="form-select" aria-label="Default select example" name="invoice_data[rand_no][item]"> 
								<option selected=""></option>
								<option value="days">Days</option>
								<option value="hours">Hours</option>
								<option value="product">Product</option>
								<option value="service">Service</option>
								<option value="expense">Expense</option>
								<option value="discount">Discount</option>
							</select>
						</td>
						<td>
							<textarea name="invoice_data[rand_no][description]" rows="2" cols="50" aria-required="true" aria-invalid="false" placeholder=""></textarea>
						</td>
						<td>
							<input type="number" name="invoice_data[rand_no][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
						</td>
						<td>
							<input type="number" name="invoice_data[rand_no][qty]" id="qty" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0.00">
						</td>
						<td>
							<input type="text" tabindex="-1" name="invoice_data[rand_no][total]" id="total" class="lineTotal" placeholder="0.00" value="" readonly="">
						</td>
					</tr>
				</table>
			</div>

			<script type="text/javascript">
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
					return false;
				});

				jQuery(document).on('click','.wc_invoice_repeter .remove-row',function(){
					jQuery(this).parent().parent().remove();
					return false;
				});
			</script>

			<?php
			return ob_get_clean();
		}