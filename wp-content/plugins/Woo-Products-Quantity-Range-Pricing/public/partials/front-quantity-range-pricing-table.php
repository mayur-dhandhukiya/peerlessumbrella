<?php
/**
 * Admin quantity wish pricing fronted table.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/public/partials
 */
$tab_header = '';
$tab_price_tab_one = '';
$tab_price_tab_two = '';
$tab_price_tab_one_sec = '';
$tab_price_tab_three = '';
$tab_price_tab_three_sec = '';
$tab_price = 0;
$code_as = $regular_price = $sales_price = $regular_price_td = $sales_price_td = '';

?>

<table class='variation_quantity_table' style="display: none;">
	<thead>
	<tr>
		<th><?php echo $heading_quantity; ?></th>
		<th><?php echo $heading_price; ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order( $as_quantity_rage_values );
	// Display all quantity price values in table.
	$valid_data_count = 0;
	foreach ( $as_quantity_rage_values as $as_quantity_rage_v ) {
		$as_quantity_rage_value = '';
		if ( is_array( $as_quantity_rage_v ) ) {
			$as_quantity_rage_value = $as_quantity_rage_v;
		} else {
			$as_quantity_rage_value = array(
				'min_qty' => $as_quantity_rage_v->min_qty,
				'max_qty' => $as_quantity_rage_v->max_qty,
				'role' => $as_quantity_rage_v->role,
				'regular_price' => $as_quantity_rage_v->regular_price,
				'sales_price' => $as_quantity_rage_v->sales_price,
				'price' => $as_quantity_rage_v->price,
				'cnd_price' => $as_quantity_rage_v->cnd_price,
				'sec_price' => $as_quantity_rage_v->sec_price,
				'type' => $as_quantity_rage_v->type,
				);	
		}
		$data_show = false;
		if( empty( $as_quantity_rage_value['role'] ) ) { $as_quantity_rage_value['role'] = ''; }
		$roles = $as_quantity_rage_value['role'];
		if( ! empty( $roles ) ) {
			if( is_user_logged_in() ) {
				$user_info = get_userdata( get_current_user_id() );
				foreach ( $user_info->roles as $role ) {
					if( in_array( $role, $roles ) ) {
						$data_show = true;
						$valid_data_count++;
					}
				}
			}else{
				$user_roles = get_option( 'as_wpqrp_user_role', '' );
				foreach ( $user_roles as $role ) {
					if( in_array( $role, $roles ) ) {
						$data_show = true;
						$valid_data_count++;
					}
				}
			}
		} else {
			$data_show = true;
			$valid_data_count++;
		}

		if ( ! empty( $as_quantity_rage_value['min_qty'] ) && $as_quantity_rage_value['max_qty'] && $as_quantity_rage_value['price'] && $data_show != false ) {

			$type  = $as_quantity_rage_value['type'];
			$regular_price = $as_quantity_rage_value['regular_price'];
			$sales_price = $as_quantity_rage_value['sales_price'];
			$price = $as_quantity_rage_value['price'];
			$cad_price = $as_quantity_rage_value['cnd_price'];
			$sec_price = $as_quantity_rage_value['sec_price']; ?>
			<tr>
				<td>
					<?php
					$tab_header .= '<th>'; 
					echo esc_attr( $as_quantity_rage_value['min_qty'] );
					$tab_header .= esc_attr( $as_quantity_rage_value['min_qty'] );
					if ( $as_quantity_rage_value['max_qty'] == - 1 ) {
						//echo ' ' . $label_or_more;
						//$tab_header .= ' ' . $label_or_more;
					} else {
						//echo esc_attr( ' - ' . $as_quantity_rage_value['max_qty'] );
						//$tab_header .= esc_attr( ' - ' . $as_quantity_rage_value['max_qty'] );
					}
					$tab_header .= '</th>';
					?>
				</td>
				<td>

					<?php
					// get woocommerce price decimal separator.
					$decimal_separator = wc_get_price_decimal_separator();

					// get woocommerce price thousand separator.
					$thousand_separator = wc_get_price_thousand_separator();

					// get woocommerce price decimals.
					$decimals = wc_get_price_decimals();

					$price = str_replace( $decimal_separator, ".", $price );
					$price = str_replace( $thousand_separator, "", $price );

					$cad_price = str_replace( $decimal_separator, ".", $cad_price );
					$cad_price = str_replace( $thousand_separator, "", $cad_price );

					$sec_price = str_replace( $decimal_separator, ".", $sec_price );
					$sec_price = str_replace( $thousand_separator, "", $sec_price );

					



					$price_display = '';
					$cad_price_display = '';
					$sec_price_display = '';
					$sec_cad_price_display = '';
					switch ( $type ) {

						case 'percentage':
							$label_discount 		= apply_filters( 'wpqrp_label_discount',  ' Discount ' );
							$price_display 			= number_format( ( $final_price - ( ( $final_price * $price ) / 100 ) ), $decimals, $decimal_separator, $thousand_separator );
							$cad_price_display 			= number_format( ( $final_price - ( ( $final_price * $cad_price ) / 100 ) ), $decimals, $decimal_separator, $thousand_separator );
							$sec_price_display 			= number_format( ( $final_price - ( ( $final_price * $sec_price ) / 100 ) ), $decimals, $decimal_separator, $thousand_separator );
							$sec_cad_price_display		= number_format( ( $final_price - ( ( $final_price * $sec_cnd_price ) / 100 ) ), $decimals, $decimal_separator, $thousand_separator );

							
							echo esc_attr( $currency . $price_display . ' ( ' . $price . ' % ' . $label_discount .' )' );

							break;

						case 'price':
							$label_discount 		= apply_filters( 'wpqrp_label_discount',  ' Discount ' );
							$price_display 			= number_format( ( $final_price - $price ), $decimals, $decimal_separator, $thousand_separator );
							$cad_price_display 			= number_format( ( $final_price - $cad_price ), $decimals, $decimal_separator, $thousand_separator );
							$sec_price_display 			= number_format( ( $final_price - $sec_price ), $decimals, $decimal_separator, $thousand_separator );
							$sec_cad_price_display 			= number_format( ( $final_price - $sec_cnd_price ), $decimals, $decimal_separator, $thousand_separator );

							echo esc_attr( $currency . $price_display . ' ( ' . $currency . $price . $label_discount . ' ) ' );
							
							break;

						case 'fixed':
							$label_selling_pricing 	= apply_filters( 'wpqrp_label_selling_pricing', ' ( Selling price )' );
							$price_display 			= number_format( $price, $decimals, $decimal_separator, $thousand_separator );
							$cad_price_display 		= number_format( $cad_price, $decimals, $decimal_separator, $thousand_separator );
							$sec_price_display 		= number_format( $sec_price, $decimals, $decimal_separator, $thousand_separator );
							$sec_cad_price_display 	= number_format( $sec_cnd_price, $decimals, $decimal_separator, $thousand_separator );
							echo $currency . $price_display . $label_selling_pricing;
							
							break;		

					}

					if(!empty($regular_price)){
						$regular_price_td   .= '<td> ' . $regular_price . ' </td>';						
					}

					if(!empty($sales_price)){
						$sales_price_td   .= '<td> ' . $sales_price . ' </td>';						
					}

					$tab_price++;
					$price_d = ( $cad_price_display * 1.32 );
					$price_d_sec = ( $sec_price_display * 1.32 );
					$tab_price_tab_one .= '<td> ' . $price_display . ' </td>';
					$tab_price_tab_three .= '<td> ' . number_format((float)$price_d, 2, '.', '') . ' </td>';
					
					$tab_price_tab_two .= '<td> ' . $cad_price_display . ' </td>';

					$data_load = false;
					$terms = get_the_terms( $item_id, 'product_cat' );
					if (  $terms  ) {
						foreach (  $terms as $term ) {
							if ( $term->name == 'bags' ) {
								$data_load = true;
							}
						}
					}
					$code_a = '(P)';
        			if ( $data_load ) {
						$tab_price_tab_one_sec .= '<td> ' . $sec_price_display . ' </td>';
						$tab_price_tab_three_sec .= '<td> ' . number_format((float)$price_d_sec, 2, '.', '') . ' </td>';
						$tab_price_tab_two_sec .= '<td> ' . $sec_cad_price_display . ' </td>';
						$code_a = '(C)';
					}
					if ( $as_woo_qrp_label ) {
						$code_a = '('.$as_woo_qrp_label.')';
					}
					if ( $code_a == '(C)' ) {
						$code_as = '(C)';
                    }elseif ( $code_a == '(P)' ) {
						$code_as = '(A)';
                    }
					?>

				</td>
			</tr>

			<?php
		}
	}
	// End foreach.
	if( $valid_data_count == 0 ) {
		?>
		<style>
		.variation_quantity_table{ display: none; }
		</style>
		<?php
	}
	?>

	</tbody>
</table>
<?php $regular_price_td = $sales_price_td = ''; ?>
<script type="text/javascript">
	jQuery( document ).ready( function() {
        if ( readCookie('lang') == 'canada' ) {
            jQuery('.langvage a[title="Canada"]').click();
        } else {
            jQuery('.langvage a[title="English"]').click();
        }

    } );
</script>
<div class="as-tabs tabs variation_quantity_table">
   <ul class="nav nav-tabs ">
      <li class="nav-item english-nav active english-nav" ><a href="#tab-one" id="tab-one-title" class="nav-link" data-toggle="tab">US Pricing</a></li>
      <li class="nav-item "><a href="#tab-two" id="tab-two-title" class="nav-link" data-toggle="tab">FOB Toronto ( USD )</a></li>
      <li class="nav-item franch-nav" ><a href="#tab-three" id="tab-three-title" class="nav-link" data-toggle="tab">CND Currency</a></li>
   </ul>
   <div class="tab-content">
      <div id="tab-one" class="tab-pane active">
         <div class="wpb_text_column wpb_content_element ">
            <div class="wpb_wrapper">
               <table class="table" width="100px">
                  <tbody>
                     <tr>
                        <th>Qty</th>
                        <?php echo $tab_header; ?>
                     </tr>
                     <tr>
                        <td><?php echo $tab_price.$code_a; ?></td>
                        <?php echo $tab_price_tab_one; ?>
                     </tr>
                     <?php 
                        if(!empty($regular_price_td)){
                        	?>
                        		<tr>
                        			<td>Regular Price</td>
		                        	<?php echo $regular_price_td; ?></td>
		                        </tr>
                        	<?php
                        }
                        ?>
                        <?php 
                        if(!empty($sales_price_td)){
                        	?>	
	                        	<tr>
	                        		<td>Sales Price</td>
		                        	<td><?php echo $sales_price_td; ?></td>
		                        </tr>
                        	<?php
                        }
                        ?> 
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <div id="tab-two" class="tab-pane">
         <div class="wpb_text_column wpb_content_element 444444444">
            <div class="wpb_wrapper">
                <table class="table" width="100px" style="margin-bottom: 10px;" >
                  <tbody>
                     <tr>
                        <th>Qty</th>
                        <?php echo $tab_header; ?>
                     </tr>
                     <tr>
                        <td><?php echo $tab_price.$code_as; ?></td>
                        <?php echo $tab_price_tab_two; ?>
                     </tr>
                    <?php 
                        if(!empty($regular_price_td)){
                        	?>
                        		<tr>
                        			<td>Regular Price</td>
		                        	<?php echo $regular_price_td; ?></td>
		                        </tr>
                        	<?php
                        }
                        ?>
                        <?php 
                        if(!empty($sales_price_td)){
                        	?>	
	                        	<tr>
	                        		<td>Sales Price</td>
		                        	<td><?php echo $sales_price_td; ?></td>
		                        </tr>
                        	<?php
                        }
                        ?> 
                  </tbody>
               </table>
                <p>Canadian Program is billed in US$. FOB: Toronto. Duty & Brokerage is included.</p>
            </div>
         </div>
      </div>
      <div id="tab-three" class="tab-pane ddddddd">
         <div class="wpb_text_column wpb_content_element 555555">
            <div class="wpb_wrapper">
                <table class="table" width="100px" style="margin-bottom: 10px;" >
                  <tbody>
                     <tr>
                        <th>Qty</th>
                        <?php echo $tab_header; ?>
                     </tr>
                     <tr>
                        <td><?php echo $tab_price.$code_as; ?></td>
                        <?php echo $tab_price_tab_three; ?>
                     </tr>
                     <?php 
                        if(!empty($regular_price_td)){
                        	?>
                        		<tr>
                        			<td>Regular Price</td>
		                        	<?php echo $regular_price_td; ?></td>
		                        </tr>
                        	<?php
                        }
                        ?>
                        <?php 
                        if(!empty($sales_price_td)){
                        	?>	
	                        	<tr>
	                        		<td>Sales Price</td>
		                        	<td><?php echo $sales_price_td; ?></td>
		                        </tr>
                        	<?php
                        }
                        ?> 
                  </tbody>
               </table>
                <p>Pricing above is in Canadian dollars and is provided for information purposes only – pricing changes daily based on currency exchange rates. Peerless will BILL at the Canadian program price in USA dollars.</p>
            </div>
         </div>
      </div>
   </div>
</div>