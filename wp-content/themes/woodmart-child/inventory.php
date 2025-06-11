<?php
/**
* Template Name: Inventory Page
*
* @package WordPress
* @subpackage Twenty_Fourteen
* @since Twenty Fourteen 1.0
*/


get_header(); ?>

<style type="text/css">
	.page-template-inventory .row.content-layout-wrapper {
		display: block;
	}
	form.wc_inventory {
	    grid-column: span 12;
		margin: 0 0px !important; 
	}
	form.wc_inventory .wc_form_field {
		display: inline-block;
		text-align: left;
		width: 48%;
	}
	form.wc_inventory .form-submit {
		text-align: left;
		margin-top: 30px;
		margin-bottom: 50px;
	}
	.page-template-inventory .wc-inventory-table{
		margin-bottom: 80px;
	}
	.select2-container textarea{
		min-height: 0;
	}
	.select2-container--default .select2-selection--multiple .select2-selection__choice:last-child{
		margin-bottom: var(--li-mb);
	}
	table.wc-inventory-table a {
		text-decoration: underline !important;
	}
	a#wc_share_result {
		padding: 12px 20px;
		font-size: 13px;
		line-height: 20px;
		background-color: #F3F3F3;
		color: #3E3E3E;
		position: relative;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		outline: none;
		border-width: 0;
		border-style: solid;
		border-color: transparent;
		border-radius: 0;
		box-shadow: none;
		vertical-align: middle;
		text-align: center;
		text-decoration: none;
		text-transform: uppercase;
		text-shadow: none;
		letter-spacing: .3px;
		font-weight: 600;
		cursor: pointer;
		transition: color .25s ease, background-color .25s ease, border-color .25s ease, box-shadow .25s ease, opacity .25s ease;
	}
	a#wc_share_result:hover {
		color: #3E3E3E;
		box-shadow: inset 0 0 200px rgb(0 0 0 / 10%);
		text-decoration: none;
	}
	.inventory-table-container {
		text-align: center;
	}
	.wc_result_email{
		display: none;
	}
	form.wc_result_email.active {
		display: block !important;
		margin-top: 25px;
		width: 60%;
		margin: auto;
	}
	input[name="wc_email"]{
		margin-top: 25px;
		margin-bottom: 10px;
	}
	.share_result_wrapper {
		text-align: center;
	}
	/* @media only screen and (min-width: 480px) {
		form.wc_inventory {
	    	margin: 0 0px;
		}
	} */
</style>
<?php

$curl = curl_init('https://job.peerlessumbrellamedia.com/api/getAllProducts');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, array(
	'token' => 'A5H#Fs^d8t7U',
	'product_id' => '',
	'color' => '',
	'quantity' => '',
	'in_stock' => '',
	'search' => '',
));
$inventory ='';
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
if ($response) {
	$inventory = json_decode($response);
}
$item_id_arr = array();
$color_arr = array(); 
if( $inventory->data ){
	if( isset($inventory->data) && !empty($inventory->data) ){
		foreach( $inventory->data as $inventory_data ){

			if( !in_array($inventory_data->item_id, $item_id_arr) ){
				$item_id_arr[] = $inventory_data->item_id;	
			}
			if( !in_array($inventory_data->name, $color_arr) ){
				$color_arr[] = $inventory_data->name;
				

			}
		}
	}
	/**foreach ($response->data as $inventory) {
		print($inventory);
		
	}*/
}
//print_r($color_arr);
//print_r($item_id_arr);
// $curl = curl_init('http://job.peerlessumbrellamedia.com/api/getAllProducts');
// curl_setopt($curl, CURLOPT_POST, true);
// curl_setopt($curl, CURLOPT_POSTFIELDS, array( 'token' => 'A5H#Fs^d8t7U' ));
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// $getAllColors = curl_exec($curl);
// curl_close($curl);

// if ($getAllColors) {
// 	$getAllColors = json_decode($getAllColors);
// }
// $colors = array();
// if ( $getAllColors ){

// 	foreach( $getAllColors->data as $getAllColor ) {
// 		$getAllColor = (array)$getAllColor;
// 		if ( $getAllColor['Colors']  ) {
// 			$color_data = explode( ',', $getAllColor['Colors'] );
// 			if ( $color_data ) {
// 				foreach(  $color_data as $color_datas ) {
// 					$colors[$color_datas]=$color_datas;
// 				}
// 			}
// 		}		
// 	}
// }
?>
<form action="" method="post" name="wc_inventory" class="wc_inventory">

	<div class="wc_form_field" style="width: calc( 100% - 80px);">
		<label>Search by Style #</label>
		<select  class="form-field wc-select-2 product_id" name="product_id[]" multiple="multiple">
			<?php if( $item_id_arr ){
				foreach($item_id_arr as $item_ids) { if ( $item_ids != '2351MMP' ) {  if ( $item_ids == '2351MM' ) { $item_ids .= ',2351MMP'; }?>
				<option value="<?php echo $item_ids; ?>"><?php echo $item_ids; ?></option>
			<?php } }
		} ?>
	</select>
	<span class="input_desc"><b>Enter your style # in field above or use our drop down menu, hit submit and all qtys will be displayed for this style</b></span>
</div>

<div class="wc_form_field" style="width: calc( 100% - 80px); display: center;">
	<label><br>Search by Color</label>
	<select  class="form-field wc-select-2 inventory_color" name="inventory_color[]" multiple="multiple">
		<?php if( $color_arr ){
			foreach ($color_arr as $color) { ?>
				<option value="<?php echo trim($color); ?>"><?php echo trim($color); ?></option>
			<?php }
		} ?>
	</select>

	<span class="input_desc"><b>Enter the color you are looking for in the field above or use the drop down menu, hit submit and all styles that are affiliated with that color will be displayed</b></span>
</div>


<div class="wc_form_field" style="width: calc( 100% - 80px); display: center;">
	<label><br>Search by Quantity</label>
	<input type="number" class="form-field wc_qty" name="wc_qty">
	<span class="input_desc"><b>Enter a quantity in the quantity field above and all styles that match, by color, will be displayed</b></span>
</div>
<div class="wc_form_field" style="width: calc( 100% - 80px); display: center; margin-top: 20px;">
	<label>Search by keywords</label>
	<input type="text" class="form-field wc_keyword" name="wc_keyword">
	<span class="input_desc">
		<b>
			Enter your keywords in field above or use our input menu, hit submit and all qtys will be displayed for this keywords<br><br>Enter a combination of the above for best results
		</b>
	</span>
</div>
	<!-- <div class="wc_form_field"><hr>
		<label>Stock</label>
		<input type="number" class="form-field stock" name="stock">
	</div> -->
	<!--<div class="wc_form_field search_form_field">
		<label><br><br>Advanced Search</label>
		<input type="search" class="form-field wc_inventory_search" name="wc_inventory_search">
		
		<span class="input_desc">
			
			Enter a combination of the above for best match results
			(example. Black , 250 units)
		</span>
	
	</div>-->
	<br/>
	
	<div class="form-submit">
		<input type="submit" name="submit" value="submit">
		<input style="margin-left:10px;" class="button" type="reset" name="reset" value="reset">
	</div>
</form>

<div class="share_result_wrapper">
	<a href="javascript:;" id="wc_share_result" class="wc_share_result" style="display: none;">Email Results</a>
	<form action="" method="post" class="wc_result_email">
		<div class="share_result_msg" style="text-align: center;"></div>
		<input type="text" placeholder="Please enter email" class="wc_email" name="wc_email">	
		<div class="wc-inventory-loader">
			<input type="submit" class="wc-inventory-email form-submit">
			<span class="wc-loader"></span>
		</div>
	</form>	
</div>
<br/><br/>
<div class="inventory-table-container">
	<span class="wc-loader"></span>
	<table class="wc-inventory-table" style="float:right"> 
		<thead>
			<tr>
				<th>Product Id</th>
				<th>Thumbnail</th>
				<th>Color</th>
				<th>Quantity</th>
				<th>Description</th>
				<th>In Stock</th>
				<th class="wc-inventory-currency-th" data-sort="float" data-currency="<?php echo get_woocommerce_currency_symbol(); ?>"><a href="javascript:;" class="wc-inventory-sorting">AS LOW AS<span class="wd-icon"></span></a></th>
			</tr>
		</thead>
		<tbody>
			
		</tbody>
	</table>
</div>
<style>
	select.form-field.product_id,
	select.form-field.inventory_color{
		height: 100px;
	}
	.wc-inventory-table thead tr th a.wc-inventory-sorting .wd-icon{
		font-size: 12px;
		margin-left: 5px;
	}
	.wc-inventory-table thead tr th a.wc-inventory-sorting .wd-icon:before {
		content: "\f119";
		font-family: woodmart-font;
	}
	.wc-inventory-table thead tr th a.wc-inventory-sorting {
		text-decoration: none !important; 
	}
	.wc_result_email .wc_error {
		border-color: #dd4343;
	}
	.wc-loader {
		border: 16px solid #f3f3f3;
		border-radius: 50%;
		border-top: 16px solid #3498db;
		width: 120px;
		height: 120px;
		-webkit-animation: spin 2s linear infinite;
		animation: spin 2s linear infinite;
		display: none;
	}
	.inventory-table-container{
		position: relative;
		padding-bottom: 80px;
	}
	.inventory-table-container .wc-inventory-table{
		float: none !important;
		margin-bottom: 0 !important;
	}
	.inventory-table-container .wc-loader{
		position: absolute;
		top: 80px;
		left: 50%;
		transform: translate(-50%, 0);
		width: 50px;
		height: 50px;
		border-width: 6px;
	}
	.inventory-table-container.active .wc-loader{
		display: block;
	}
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
	.wc-inventory-loader {
		position: relative;
		background: #000;
		width: auto;
		display: inline-block;
	}
	.wc-inventory-loader input[type="submit"].active {
		opacity: 0.2;
	}
	.wc-inventory-loader input[type="submit"].active + .wc-loader {
		display: inline-block;
		width: 22px;
		height: 22px;
		border: 3px solid #f3f3f3;
		border-top: 3px solid #00abc8;
		vertical-align: middle;
		position: absolute;
		left: 34px;
		top: 11px;
		transform: translate(-50%, -50%);
		opacity: 1;
		z-index: 1;
	}
</style>
<script type="text/javascript">
	jQuery(document).ready(function () {

		if( jQuery('.wc-select-2').length > 0 ){
			jQuery('.wc-select-2').select2();
		}

		jQuery(document).on('click','input[type="reset"]',function(){
			jQuery('select,input[type="number"]').val('').change();
		});

		setTimeout(function(){
			jQuery( 'form.wc_inventory' ).submit();
		},1000);

		jQuery( 'a#wc_share_result' ).click(function() {
			jQuery('form.wc_result_email').toggleClass('active');
		});

		jQuery(document).on('click','form.wc_result_email .wc-inventory-email',function(e) {
			e.preventDefault();
			var t_this = jQuery(this);

			var result = jQuery('.inventory-table-container').html();
			var product_id = jQuery( '.product_id').val().join(",");
			var color = jQuery( '.inventory_color').val().join(",");
			var qty = jQuery( '.wc_qty').val();
			var stock = jQuery( '.stock').val();
			var search = jQuery( '.wc_inventory_search').val();

			var email_add = jQuery(this).parents('form.wc_result_email').find('input[name="wc_email"]').val();
			email_add = email_add.trim();
			var error = '';
			var wc_flag = 0;
			jQuery('form.wc_result_email input').removeClass('wc_error');

			if( email_add == '' ){
				error += 'wc_error';
				wc_flag = 1;
				t_this.parents('form.wc_result_email').find('input[name="wc_email"]').addClass('wc_error');
			}else if( !IsEmail(email_add) ){
				error += 'wc_error';
				wc_flag = 1;
				t_this.parents('form.wc_result_email').find('input[name="wc_email"]').addClass('wc_error');
			}

			if( wc_flag == 0 ){
				t_this.addClass('active');
				jQuery.ajax({
					type : "POST",
					dataType : "json",
					url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data : {action: "wc_share_inventory_result", result: result, email_add: email_add,  product_id: product_id, color:color, qty:qty, stock:stock, search: search },
					success: function(response) {
						t_this.removeClass('active');
						if( response ){
							setTimeout(function(){
								jQuery('.share_result_wrapper .share_result_msg').html('<span class="wc-success">'+response+'</span>');
							},2000);
							setTimeout(function(){
								jQuery('.share_result_wrapper .share_result_msg').html('');
								jQuery('form.wc_result_email').trigger("reset");
								jQuery('form.wc_result_email').removeClass('active');
							},4000);
						}
					}
				});
			} 
			return false;
		});

		jQuery(document).on('submit','form.wc_inventory', function() {
			//jQuery('a#wc_share_result').hide();
			var t_this = jQuery(this);
			jQuery('.wc_result_email').removeClass('active');

			var product_id = jQuery( this ).find('.product_id').val().join(",");
			var color = jQuery( this ).find('.inventory_color').val().join(",");
			var qty = jQuery( this ).find('.wc_qty').val();
			var keyword = jQuery( this ).find('.wc_keyword').val();
			var stock = jQuery( this ).find('.stock').val();
			var search = jQuery( this ).find('.wc_inventory_search').val();
			jQuery('.inventory-table-container').addClass('active');
			jQuery.ajax({
				type : "POST",
				dataType : "json",
				url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data : {action: "wp_sync_status", product_id: product_id, color:color, qty:qty, keyword:keyword, stock:stock, search: search},
				success: function(response) {
					jQuery('.inventory-table-container').removeClass('active');
					jQuery( 'table.wc-inventory-table tbody' ).html( response ).hide();
					jQuery(".wc-inventory-table").stupidtable();
					jQuery(".wc-inventory-table .wc-inventory-currency span").text(jQuery( 'table.wc-inventory-table .wc-inventory-currency-th' ).attr('data-currency'));
					jQuery( 'table.wc-inventory-table tbody' ).show();
					jQuery('a#wc_share_result').show();
				}
			});
			return false;
		}); 

		function IsEmail(email){
			var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(!regex.test(email)) {
				return false;
			}else{
				return true;
			}
		}
	}); 
</script>
<?php get_footer(); ?>
