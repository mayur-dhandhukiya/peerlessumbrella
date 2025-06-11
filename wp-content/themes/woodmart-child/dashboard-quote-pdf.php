<?php
use Dompdf\Dompdf;
add_action( 'wp_ajax_nopriv_wc_create_pdf_html', 'wc_create_pdf_html_fun' );
add_action( 'wp_ajax_wc_create_pdf_html', 'wc_create_pdf_html_fun' );
function wc_create_pdf_html_fun() {
	$html = isset( $_POST['html'] ) ? $_POST['html'] : '';
	wc_pdf_file_genrate_fun( $html, 0 );
}

function wc_pdf_file_genrate_fun( $html = '', $save_file = 0 ){

	$html = $html;
	$html_str = str_replace('500px','800px', $html);

	//$html_str = str_replace('padding-top: 10px;text-align: inherit;','padding-top: 100px;text-align: inherit;', $html_str);
	//$html_str = str_replace('style="display:block;" class="wc-text-left"','style="display:block;padding-top:50px;" class="wc-text-left"', $html_str);
	
	require_once get_stylesheet_directory().'/dompdf_1/autoload.inc.php';
	//echo $html;
	//$html = str_replace('\\','',$html);
	$html = stripslashes($html_str);
	$html = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
	<style>*{ font-family: "Roboto", sans-serif; }</style></head><body>'.$html.'</body></html>';

	//echo $html;
	$dompdf = new Dompdf();
	$dompdf->loadHtml($html,'UTF-8');

	$dompdf->setPaper('A4', 'portrait');
	$dompdf->set_option('isRemoteEnabled', true);

	$dompdf->render();

    //$dompdf->stream("file.pdf");
	$output = $dompdf->output();

	if( $save_file == 1 ){
		$upload = wp_upload_dir();
		$upload_url = $upload['basedir'];
		$upload_url = $upload_url . '/pdf/';

		file_put_contents($upload_url.time().''.".pdf", $output);	
		$url = $upload['basedir']. '/pdf/'.time().''.".pdf";
		return $url;  
	}else{
		echo base64_encode($output);
		die;
	}
}

function wc_quotes_data_mail_remainder_fun($edit_id){
	ob_start();
	$uer_id = get_current_user_id();
	$edit_id = $edit_id;
	$wc_logo_hide = ( $edit_id ) ? 'wc-hide-logo' : '';
	$post =  get_post( $edit_id );
	$id = isset($post->ID) ? $post->ID : '';
	$image_id = get_post_meta( $id, 'logo_id', true );
	$quotes_extra_data = get_post_meta( $id, 'quotes_extra_data', true );
	$wc_eqp_price = isset($quotes_extra_data['wc_eqp_price']) ? $quotes_extra_data['wc_eqp_price'] : '';
	$customization_logo  = isset( $quotes_extra_data['customization_logo'] ) ? $quotes_extra_data['customization_logo'] : '';
	$customization_poNumber  = isset( $quotes_extra_data['customization_poNumber'] ) ? $quotes_extra_data['customization_poNumber'] : '';
	?>
	<div class="wp-viewinvoice-form-wrapper print-html-hidden" style="max-width: 100%; width: 100%;">
		<table width="100%" style="caption-side: bottom;border-collapse: collapse;">
			<tr>
				<td>
					<div class="viewinvoice-address-logo" style="width:100%;">
						<table style="width:100%;">
							<tr>
								<td style="text-align:left;">
									<div class="invoice-from-address">
										<h4>From</h4>
										<span class="wc-from-comapny-name"><?php echo isset($quotes_extra_data['from_company_name']) ? $quotes_extra_data['from_company_name'] : '';  ?></span><br/>
										<span class="wc-from-name"><?php echo isset($quotes_extra_data['from_your_name']) ? $quotes_extra_data['from_your_name'] : '';  ?></span><br/>
										<span class="wc-from-email"><?php echo isset($quotes_extra_data['from_email']) ? $quotes_extra_data['from_email'] : '';  ?></span><br/>
										<span class="wc-from-address"></span>
									</div>
								</td>
								<td  style="text-align:right;">
									<div class="invoice-from-logo">
										<?php
										$image_url = '';
										if( $image_id !== 0 ){
											$image_attributes = wp_get_attachment_image_src($image_id, 'full');
											if($image_attributes){
												$image_url = $image_attributes[0];
											} else {
												$upload_dir = wp_upload_dir();
												$baseurl = $upload_dir['baseurl'] . '/2022/01/peerless-cyan-logo-web-3-1.png';
												$image_url = $baseurl;
											}
										}
										?>
										<img src="<?php echo $image_url; ?>" alt="" style="width: 100px;height: auto;">
									</div>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="viewinvoice-toname-table" style="width:100%;clear: both;margin: 0;display: block; ">
						<table style="width:100%;" style="caption-side: bottom;border-collapse: collapse;">
							<tr class="invoice-from-toname">
								<th style="text-align:left;"><h4>To</h4></th>
								<th></th>
								<th style="text-align:right;"><h4>Quotes</h4></th>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-comapny-name"><?php echo isset( $quotes_extra_data['to_comapny_name'] ) ? $quotes_extra_data['to_comapny_name'] : ''; ?></span></td>
								<td style="text-align:right;">Quotes #</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-invoice-id" style="text-align:right;"><?php echo isset($quotes_extra_data['invoice_number']) ? $quotes_extra_data['invoice_number'] : '';  ?></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-name"><?php echo isset( $quotes_extra_data['to_your_name'] ) ? $quotes_extra_data['to_your_name'] : ''; ?></span></td>
								<td style="text-align:right;">P.O. #</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-po" style="text-align:right;"><?php echo isset($quotes_extra_data['tex_number']) ? $quotes_extra_data['tex_number'] : '';  ?></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-email"><?php echo isset($quotes_extra_data['to_email']) ? $quotes_extra_data['to_email'] : '';  ?></span></td>
								<td style="text-align:right;">Quote Date</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-invoice-date" style="text-align:right;"><?php echo isset( $quotes_extra_data['trip-start'] ) ? $quotes_extra_data['trip-start'] : ''; ?></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-phone"><?php echo isset($quotes_extra_data['to_telphone']) ? $quotes_extra_data['to_telphone'] : '';  ?></span></td>
								<td style="text-align:right;">Expiration Date</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-due-date" style="text-align:right;"><?php echo isset( $quotes_extra_data['trip-end'] ) ? $quotes_extra_data['trip-end'] : ''; ?></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-address"></span></td>
								<td style="text-align:right;"></td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="" style="text-align:right;"></td></tr></table></span></td>
							</tr>
						</table>
					</span>
				</td>
			</tr>
			<tr>
				<td style="padding-top: 10px;">
					<div class="viewinvoice-dataTable">
						<table style="border: 1px solid #00abc8; border-collapse: collapse; border-spacing: 0;width: 100%; caption-side: bottom;border-collapse: collapse;">
							<thead>
								<tr>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Item</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Varition</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Description</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Unit Price</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Quantity</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff;  text-align: center;">Amount</th>
								</tr>
							</thead>
							<tbody>	

								<?php
								$invoice_data  = isset($quotes_extra_data['invoice_data']) ? $quotes_extra_data['invoice_data'] : '';
								if( $invoice_data ){
									foreach ($invoice_data as $value) {
										$item_data = isset($value['item']) ? $value['item'] : '';
										$unit_data = isset($value['unit']) ? $value['unit'] : '';
										$wc_hidden_var_image = isset($value['wc-hidden-var-image']) ? $value['wc-hidden-var-image'] : '';
										$description = isset($value['description']) ? $value['description'] : '';
										$qty_data = isset($value['qty']) ? $value['qty'] : '';
										$varitions = isset($value['varitions']) ? $value['varitions'] : '';
										$total_data = isset($value['total']) ? $value['total'] : '';
										$product = wc_get_product( $item_data );
										if( $product ){
											$variations = $product->get_available_variations(); 
											$product_title = $product->get_title();
										}
										?>
										<tr class="quotes-append-html" style="text-align: center; border: 1px solid #000;">
											<td style="text-align: center;padding: 10px;border: 1px solid #00abc8;"><a target="_blank" href="<?php echo get_permalink( $item_data ); ?>" style="text-decoration:underline!important;color: #333333;"><?php echo $product_title; ?></a></td>
											<td style="text-align: center;padding: 10px;border: 1px solid #00abc8;">
												<?php
												if( $variations ){
													$var_prod_id = wp_list_pluck( $variations, 'variation_id' );
													if( $var_prod_id ){         
														foreach ($var_prod_id as $values) {
															$variation = new WC_Product_Variation($values);
															$variationName = implode(" / ", $variation->get_variation_attributes());
                                                        //$variations_title = get_the_title( $values );
															if( $varitions == $values ){
																echo $variationName; 
															}
														}
													}
												}
												?>
											</td>
											<td style="text-align: center;padding: 10px;border: 1px solid #00abc8; position: relative;"><img style="width: 58px;height: 50px;display: block;object-fit: scale-down;margin: 0 auto;border: 1px solid #e6e6e6;padding: 5px;object-position: center; position: absolute; top: 10px; left: 50%; transform: translateX(-50%);" src="<?php echo $wc_hidden_var_image; ?>"><span style="display:block; margin-top: 65px;" class="wc-text-left"><?php echo $description; ?></span>
												<td style="text-align: center;padding: 10px;border: 1px solid #00abc8;"><?php echo get_woocommerce_currency_symbol().''.$unit_data;?></td>
												<td style="text-align: center;padding: 10px;border: 1px solid #00abc8;"><?php echo $qty_data;?></td>
												<td style="text-align: center;padding: 10px;border: 1px solid #00abc8;"><?php echo get_woocommerce_currency_symbol().''.$total_data;?></td>
											</tr>
											<?php
										}
									}
									?>	
									<tr class="wc-notes-pricing">
										<td class="wc-text" colspan="6" style="border-bottom: 0; text-align: right; padding: 10px 10px!important;">
											<?php 
											$txt_string = 'Unit Price';
											if( !empty( $wc_eqp_price ) && in_array( "eqp", $wc_eqp_price ) ) {
												$txt_string = 'Eqp Price';	
											}
											if( !empty( $wc_eqp_price ) && in_array( "net", $wc_eqp_price ) ) {
												$txt_string = 'Net Price';
											}
											if( !empty( $wc_eqp_price ) && in_array( "eqp", $wc_eqp_price ) && in_array( "net", $wc_eqp_price ) ){
												$txt_string = 'EQP And NET Price';
											}
											if( $txt_string ){ echo '<strong>'.$txt_string.'</strong>'; }
											?>
										</td>
									</tr>
									<tr class="wc-extra-notes">
										<td class="wc-extra-text" colspan="6" style="border-bottom: 0;  padding-top: 50px; padding-left: 12px; margin-bottom:5px;"><?php echo isset( $quotes_extra_data['wc_extra_comments'] ) ? $quotes_extra_data['wc_extra_comments'] : ''; ?></td>
									</tr>							
									<tr>
										<td class="invoice-notes" colspan="6" style="border-bottom: 0; padding-top: 20px; padding-left: 12px; margin-bottom:5px;"><?php echo isset($quotes_extra_data['invoice_notes']) ? $quotes_extra_data['invoice_notes'] : 'NOTE: Pricing shown is valid for 30 days.<br/>Production time varies by product.<br/>FOB Newark, NJ. Freight Estimate Available by Request.<br/>Visit peerlessumbrella.com/inventory<br/>for real-time inventory status and please contact your rep for virtuals, questions, additional assistance.';?></td>
									</tr>
									<tr class="invoice-row" style="border-top: 1px solid #00abc8!important;">
										<td colspan="5" style="border-bottom: 0; border-top: 1px solid #00abc8!important;"></td>
										<td style="border-top: 1px solid #00abc8!important;">
											<div class="invoice-subtotal-table">
												<table style="margin-bottom: 0; border: 0; border-left: 1px solid #00abc8; border-collapse: collapse; border-spacing: 0; caption-side: bottom;border-collapse: collapse;">
													<tbody>
														<tr style="display:none;">
															<td style="font-weight: bold; color: #000000; padding: 10px 12px; border-bottom: 0;">Subtotal</td>
															<td class="wc-preview-subtotal" style="padding: 10px 12px; border-bottom: 0;"><?php echo isset($quotes_extra_data['foot_sub_total']) ? get_woocommerce_currency_symbol().''.$quotes_extra_data['foot_sub_total'] : '0.00';  ?></td>
														</tr>
														<tr class="invoice-sum-tax" style="display:none;">
															<td style="color: #777777 !important; padding: 0px 12px 10px !important; font-weight: normal !important; border-bottom: 1px solid #ccc !important;"><span></span></td>
															<td style="color: #777777 !important; padding: 0px 12px 10px !important; font-weight: normal !important; border-bottom: 1px solid #ccc !important;"><span></span></td>
														</tr>
														<tr style="display:none;">
															<td style="font-weight: bold; color: #000000; padding: 10px 12px; border-bottom: 0;">Total</td>
															<td class="wc-preview-total" style="padding: 10px 12px; border-bottom: 0;"><?php echo isset($quotes_extra_data['foot_total']) ? get_woocommerce_currency_symbol().''.$quotes_extra_data['foot_total'] : '0.00';  ?></td>
														</tr>
													</tbody>
												</table>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</div>


		<?php
		return ob_get_clean();
	}
