<?php
/**
 * Admin quantity wish pricing option fields.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin/partials
 */
$wc_checked = false;
/*$default_term = array('bags','protective-gear');
$product_id = wp_get_post_parent_id($value_id);
$terms = get_the_terms( $product_id, 'product_cat' );
foreach ($terms as $key => $value) {
	if (in_array($value->slug, $default_term)){
		$wc_checked = true;
	}
}*/
if($wc_checked == true){
	if($display_blank_price == 1){
		$checked = 'checked';
	}else{
		$checked = '';
	}
}else{
	if($display_blank_price == 1){
		$checked = 'checked';
	}
}


?>

<tr class="as-fileds-row-<?php echo esc_attr( $number ); ?> as-fileds-row" row-number="<?php echo esc_attr( $number ); ?>" >
	<td>
		<input class="wqap-display-blank-price" type="checkbox" value='1'
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][display_blank_price]"  <?php echo $checked; ?>
		 />
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Min Quantity', 'woo-products-quantity-range-pricing' ); ?>"
		       value="<?php echo esc_attr( $min_quantity ); ?>" class="wqap-min-quantity" type="number"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][min_qty]"
		       value="" min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Max Quantity', 'woo-products-quantity-range-pricing' ); ?>"
		       value="<?php echo esc_attr( $max_quantity ); ?>" class="wqap-max-quantity" type="number"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][max_qty]"
		       value="" min="-1"/>
	</td>
	<td>
    <select class="pricing-type wpqrp-pricing-type-<?php echo esc_attr( $number ); ?> wpqrp_chosen_select"
		        name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][type]"
				onload="function(){ alert('ff'); $('.wpqrp-pricing-type-<?php echo esc_attr( $number ); ?>').select2(); }" >

			<?php
			foreach ( $type_values as $key => $value ) :
				?>
				<option
					value="<?php echo esc_attr( $value ); ?>" <?php if ( $type == $value ) { echo 'selected'; } ?> ><?php echo esc_attr( $key ); ?></option>
			<?php endforeach; ?>
		</select>
		
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Enter Price ', 'woocommerce' ); ?>" class="as_pricing wc_input_price"
		       value="<?php echo esc_attr( $regular_price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][regular_price]"
		       min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Enter Price ', 'woocommerce' ); ?>" class="as_pricing wc_input_price"
		       value="<?php echo esc_attr( $sales_price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][sales_price]"
		       min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Enter Price ', 'woocommerce' ); ?>" class="as_pricing wc_input_price"
		       value="<?php echo esc_attr( $special_price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][special_price]"
		       min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'Enter Price ', 'woocommerce' ); ?>" class="as_pricing wqap-pricing wc_input_price"
		       value="<?php echo esc_attr( $price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][price]"
		       min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( 'CND Price ', 'woocommerce' ); ?>" class="as_pricing wqap-pricing wc_input_price"
		       value="<?php echo esc_attr( $cnd_price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][cnd_price]"
		       min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( '2nd Price ', 'woocommerce' ); ?>" class="as_pricing wqap-pricing wc_input_price"
		       value="<?php echo esc_attr( $sec_price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][sec_price]"
		       min="0"/>
	</td>
	<td>
		<input placeholder="<?php esc_attr_e( '2nd CND Price ', 'woocommerce' ); ?>" class="as_pricing wqap-pricing wc_input_price"
		       value="<?php echo esc_attr( $sec_cnd_price ); ?>" type="text"
		       name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][sec_cnd_price]"
		       min="0"/>
	</td>
	<td>
			<select class="wpqrp-select-role wpqrp-role-<?php echo esc_attr( $number ); ?> wpqrp_chosen_select"
        name="as_woo_pricing_<?php echo esc_attr( $value_id ); ?>[<?php echo esc_attr( $number ); ?>][role][]"
        multiple>
    <?php foreach ( $wp_roles->roles as $key => $value ): ?>
        <option value="<?php echo esc_attr( $key ); ?>"
            <?php echo in_array( $key, $wpqrp_roles ) ? 'selected' : ''; ?>>
            <?php echo esc_html( $value['name'] ); ?>
        </option>
    <?php endforeach; ?>
</select>
	</td>
	<td>
		<?php if ( $number > 0 ) { ?>
			<a type="button" class="button dashicons dashicons-trash delete-quantity"
			   data-id="<?php echo esc_attr( $number ); ?>"
			   value-id="<?php echo esc_attr( $value_id ); ?>"></a>
		<?php } ?>
	</td>
</tr>