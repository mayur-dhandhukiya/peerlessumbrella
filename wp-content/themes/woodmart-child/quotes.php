<?php
use Dompdf\Dompdf;
add_shortcode( 'wc_quotes_list_data', 'wc_quotes_list_data_fun' );
function wc_quotes_list_data_fun(){
	ob_start();
	?>
	<div class="wc-quotes-flex">
		<div class="wc-add-new">
			<a href="<?php echo site_url('/new-quotes/?type=new')?>">Create New Quote<span class="wd-icon"></span></a>
		</div>
		<div class="wc-add-new wc-padding">
			<a href="<?php echo site_url('/new-quotes/?type=custom')?>">Create Custom Quote<span class="wd-icon"></span></a>
		</div>
	</div>
	<?php
	if( is_user_logged_in() ){
		$user_id = get_current_user_id();
		//$paged = ( $_GET['pa'] ? $_GET['pa'] : 1);
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		$post_per_page = 10;
		$args = array(
			'post_type'   => 'quotes',
			'post_status' => 'publish',
			'posts_per_page'=> $post_per_page,
			'author' => $user_id,
			'paged' => $paged,
		);

		$the_query = new WP_Query( $args ); 
		if ( $the_query->have_posts() ) { 
			$main_page_number = 1;
			$page = 1;
			$req_uri = array_reverse(explode('/', $_SERVER['REQUEST_URI']));
			foreach ($req_uri as $value) {
				if(is_numeric($value)) {
					$page = $value;
					break;
				}
			}
			if( $page > 1 ){
				$main_page_number = ( $page - $main_page_number ) * $post_per_page + 1;
			}
			?>
			<div class="wc-quote-table-wrap">
				<div class="wc-list-table">
					<table class="wc-quotes-list">
						<thead>	
							<tr>
								<th>No</th>
								<th>Company Name</th>
								<th>Customer Name</th>
								<th colspan="3">Items</th>
								<th>Quote Number</th>
								<th>Action</th>
								<th>Reminder</th>
							</tr>
						</thead>
						<tbody>
							<?php
							while ( $the_query->have_posts() ) { $the_query->the_post(); 
								$psot_id = get_the_ID();
								$quotes_extra_data = get_post_meta( $psot_id, 'quotes_extra_data', true );
								$quote_mail_remainder = get_post_meta( $psot_id, 'quote_mail_remainder', true );
								$quote_mail_remainder = get_post_meta( $psot_id, 'quote_mail_remainder', true );
								$quotes_type = get_post_meta( $psot_id, 'quotes_type', true );
								$invoice_data  = isset($quotes_extra_data['invoice_data']) ? $quotes_extra_data['invoice_data'] : '';
								?>
								<tr data-id="<?php echo $psot_id; ?>">
									<td class="wc-number"><?php echo $main_page_number; ?></td>
									<td class="wc-title-to-company"><span><?php echo isset($quotes_extra_data['to_comapny_name']) ? $quotes_extra_data['to_comapny_name'] : ''; ?></span></td>
									<td class="wc-title"><span><?php echo isset($quotes_extra_data['to_your_name']) ? $quotes_extra_data['to_your_name'] : ''; ?></span></td>
									<td colspan="3">
										<table style="width: 100%;" class="wc-item-table">
											<thead >
												<?php
												if($quotes_type == 'custom'){ ?>
													<th>Image</th>
													<th>Description</th>
												<?php } else { ?>
													<th>Item</th>
													<th>Color</th>
												<?php } ?>
											</thead>
											<tbody>
												<?php
												if($quotes_type == 'custom'){
													$custom_invoice_data  = isset($quotes_extra_data['custom_invoice_data']) ? $quotes_extra_data['custom_invoice_data'] : '';
													if( $custom_invoice_data ){
														foreach ($custom_invoice_data as $value) {
															$product_file_id = isset($value['product_file_id']) ? $value['product_file_id'] : '';
															//$attch_ids = get_post_meta( $product_file_id, 'p_attach_ids', true );
															$convert_ids = isset($value['convert_ids']) ? $value['convert_ids'] : '';
															$wc_hidden_pro_image = isset($value['wc-hidden-pro-image']) ? $value['wc-hidden-pro-image'] : '';
															$img_src_string = ( $convert_ids ) ? 'https://drive.google.com/uc?id='.$convert_ids : $wc_hidden_pro_image;
															$description = isset($value['description']) ? $value['description'] : '';
															?>
															<tr>
																<td class="wc-invo-prod-title"><img src="<?php echo $img_src_string; ?>" width="100" height="100"></td>
																<td class="wc-invo-prod-title"><?php echo $description; ?></td>
															</tr>
															<?php
														}
													}
												} else {
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
															$variations = array();
															if( is_object($product) ){
																$variations = $product->get_available_variations(); 
																$product_title = $product->get_title();
															}
															?>
															<tr>
																<td class="wc-invo-prod-title"><?php echo $product_title; ?></td>
																<td class="wc-invo-prod-title">
																	<?php
																	if( $variations ){
																		$var_prod_id = wp_list_pluck( $variations, 'variation_id' );
																		if( $var_prod_id ){         
																			foreach ($var_prod_id as $values) {
																				$variation = new WC_Product_Variation($values);
																				$variationName = implode(" / ", $variation->get_variation_attributes());
																				if( $varitions == $values ){
																					echo $variationName; 
																				}
																			}
																		}
																	}
																	?>
																</td>
															</tr>
															<?php
														}
													}
												}
												?>
											</tbody>
										</table>
									</td>
									<td class="wc-invgoice-num"><span><?php echo isset($quotes_extra_data['invoice_number']) ? $quotes_extra_data['invoice_number']: '-'; ?></span></td>
									<td class="wc-action">
										<div class="wc-btn-box">
											<a href="<?php echo site_url('/new-quotes/?edit='.$psot_id.'&type='.$quotes_type); ?>" class="wc-edit" data-id="<?php echo $psot_id; ?>"><span class="wd-icon"></span></a>
											<a href="javascript:;" class="wc-delete" data-id="<?php echo $psot_id; ?>"><span class="wd-icon"></span><span class="wc-loader"></span></a>
										</div>
									</td>
									<td class="wc-remainder">
										<span class="wc-loader"></span>
										<input type="checkbox" class="wc_remainder wc_custom_checkbox" <?php echo ( $quote_mail_remainder == 'yes' ) ? 'checked' : ''; ?> data-id="<?php echo $psot_id; ?>" name="wc_remainder" value="">
									</td>
								</tr>
								<?php 
								$main_page_number++;
							}
							wp_reset_postdata(); 
							?>
						</tbody>
						<?php 
						$total_pages = $the_query->max_num_pages;
						if( $total_pages > 1 ){
							?>
							<tfoot>
								<tr>
									<td colspan="5" class="wc-pagination">
										<?php
										$current_page = max(1, $paged);
										echo paginate_links(array(
											'base' => get_pagenum_link(1) . '%_%',
											'format' => '?paged=%#%',
											'current' => $current_page,
											'total' => $total_pages,
											'prev_text'    => __('« Prev'),
											'next_text'    => __('Next »'),
											'add_args'  => array()
										));
										?>
									</td>
								</tr>
							</tfoot>
						<?php } ?>
					</table>
				</div>
			</div>
		<?php } else { ?>
			<p class="wc-no-posts"><?php _e( 'Sorry, no quotes matched your criteria.' ); ?></p>
			<?php
		}
	}
	return ob_get_clean();
}


add_shortcode( 'wc_add_new_quotes', 'wc_add_new_quotes_fun' );
function wc_add_new_quotes_fun(){
	ob_start();
	?>
	<div class="wp-Free-quote">
		<div class="wp-Free-quote-form">
			<?php  if( $_GET['type'] && !empty($_GET['type']) && $_GET['type'] == 'new' ){  ?>
				<form method="post" enctype="multipart/form-data" class="wc_quote_form">
					<?php
					$uer_id = get_current_user_id();
					$edit_id = isset( $_GET['edit'] ) ? $_GET['edit'] : '';
					$wc_logo_hide = ( $edit_id ) ? 'wc-hide-logo' : '';
					$post =  get_post( $edit_id );
					$id = isset($post->ID) ? $post->ID : '';
					$image_id = get_post_meta( $id, 'logo_id', true );
					$quotes_extra_data = get_post_meta( $id, 'quotes_extra_data', true );
					$wc_eqp_price = isset($quotes_extra_data['wc_eqp_price']) ? $quotes_extra_data['wc_eqp_price'] : '';
					$customization_logo  = isset( $quotes_extra_data['customization_logo'] ) ? $quotes_extra_data['customization_logo'] : '';
					$customization_poNumber  = isset( $quotes_extra_data['customization_poNumber'] ) ? $quotes_extra_data['customization_poNumber'] : '';
					?>
					<div class="wc-backto-quotes">
						<a href="<?php echo site_url('/quotes')?>"><span class="wd-icon"></span>Back To Quotes</a>
					</div>
					<div class="alert alert-info">
						<span class="hidden-small">Want to customize your quote?</span>
						<a href="javascript:;" id="btnToggleOptions">Show Customization Options</a>
					</div>
					<div id="wp-options" class="hidden-print open">
						<div class="wp-options-wrapper">
							<label class="option label-checkbox">
								<?php
								$logo_class = $logo_checked = '';
								if( $customization_logo == 'on' ){
									$logo_checked = 'checked';
								//$logo_class = "active";
								}
								?>
								<input type="checkbox" onclick="" <?php echo $logo_checked; ?> class="wc_top_logo active <?php //echo $logo_class; ?> wc_custom_checkbox " name="customization_logo" id="customization_logo">
								<span>Logo</span> 
							</label>
							<label class="option label-checkbox">
								<?php
								$po_class = $po_checked = '';
								if( $customization_poNumber == 'on' ){
									$po_checked = 'checked';
									$po_class = "active";
								}
								?>
								<input type="checkbox" name="customization_poNumber" <?php echo $po_checked; ?> class="wc_top_po <?php echo $po_class; ?> wc_custom_checkbox" id="customization_poNumber">
								<span>P.O. #</span> 
							</label>
						</div>
					</div>
					<div class="wp-tabs">
						<ul class="nav tabs">
							<li class="nav-item tab-link current" data-tab="tab-1">
								<a id="preview" class="wc-preview nav-link active" aria-current="page" href="javascript:;">Preview</a>
							</li>
							<li class="nav-item tab-link" data-tab="tab-2">
								<a id="age" title="" class="nav-link wc-print" href="javascript:;">Print</a>
							</li>
							<li class="nav-item tab-link" data-tab="tab-3">
								<a id="age" title="" class="nav-link wc-pdf" href="javascript:;"><span class="wc-pdf-name">PDF</span><span class="wc-loader"></span></a>
							</li>
							<li class="nav-item tab-link" data-tab="tab-4">
								<a id="age" title="" class="nav-link wc-send quotes-popup" data-id="<?php echo $uer_id; ?>" href="#quotes-form"><span class="wc-send-name">Send</span><span class="wc-loader"></span></a>
							</li>
						</ul>
						<?php  
						$edit_id = isset( $_GET['edit'] ) ? $_GET['edit'] : ''; 
						$title_html = ( $edit_id ) ? 'Update' : 'Save';
						?>
						<div class="save-btn">
							<?php $login_class = ( !is_user_logged_in()  ) ? 'disabled' : ''; ?>
							<a href="#logged-in-modal" class="wc-save <?php echo $login_class; ?>"><span class="wc-save-title"><?php echo $title_html; ?></span><span class="wc-loader"></span></a>
							<input type="hidden" name="update_data" value="<?php echo base64_encode($edit_id); ?>">
						</div>
					</div>
					<div id="logged-in-modal" class="mfp-hide white-popup-block">
						<h1>Quote</h1>
						<div class="wc-loged-in-popup">
							<span>To save your quote, you can login first.</span>
							<a href="<?php echo site_url('/my-account');?>" class="wc-logged-in-save">Click Here</a>
						</div>
						<p><a class="logged-modal-dismiss" href="javascript:;">X</a></p>
					</div>
					<div class="wp-paper">
						<div class="wp-quote-form-wrapper">
							<div class="wp-meta">
								<div class="wp-meta-left">
									<div class="wp-meta-from">
										<label>From</label>
										<input type="text" name="from_company_name" value="<?php echo isset($quotes_extra_data['from_company_name']) ? $quotes_extra_data['from_company_name'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Company Name">
										<input type="text" name="from_your_name" value="<?php echo isset($quotes_extra_data['from_your_name']) ? $quotes_extra_data['from_your_name'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Name">
										<input type="email" name="from_email" value="<?php echo isset($quotes_extra_data['from_email']) ? $quotes_extra_data['from_email'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Email">
										<?php /*<textarea class="from_your_address" name="from_your_address" cols="40" rows="10" aria-required="true" aria-invalid="false" placeholder="Your address"><?php echo isset($quotes_extra_data['from_your_address']) ? $quotes_extra_data['from_your_address'] : '';  ?></textarea> */ ?>
									</div>
									<div class="wp-meta-from wp-meta-to">
										<label>To</label>
										<input type="text" name="to_comapny_name" value="<?php echo isset( $quotes_extra_data['to_comapny_name'] ) ? $quotes_extra_data['to_comapny_name'] : ''; ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Customer (Company) Name">
										<input type="text" name="to_your_name" value="<?php echo isset( $quotes_extra_data['to_your_name'] ) ? $quotes_extra_data['to_your_name'] : ''; ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Customer name">
										<input type="email" name="to_email" value="<?php echo isset($quotes_extra_data['to_email']) ? $quotes_extra_data['to_email'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Email">
										<input type="tel" name="to_telphone" placeholder="(optional) Phone Number" value="<?php echo isset($quotes_extra_data['to_telphone']) ? $quotes_extra_data['to_telphone'] : '';  ?>" pattern="[0-9]{3} [0-9]{3} [0-9]{4}">    

										<?php /*<textarea class="to_your_address"  name="to_your_address" cols="40" rows="10" aria-required="true" aria-invalid="false" placeholder="Customer address"><?php echo isset($quotes_extra_data['to_your_address']) ? $quotes_extra_data['to_your_address'] : '';  ?></textarea>*/ ?>
									</div>
								</div>
								<div class="wp-meta-right">
									<div class="wp-invoice-quete">
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
											//$image_url = 'https://www.peerlessumbrella.com/wp-content/uploads/2022/01/peerless-cyan-logo-web-3-1.png';
											}
										}
										?>	
										<div class="wp-upload-logo wc-hide-logo <?php //echo $wc_logo_hide; ?>" <?php echo ($customization_logo == 'on') ? 'style="display: block"' : 'style="display: block"'; ?> >
											<div id="upload_area" style="" class="empty yournamehere">
												<?php echo '<img src="'.$image_url.'" />'; ?>
											</div>
											<div class="wp-clearfix">
												<label id="btnUploadLogo" class="input-file" style="">
													<b class="btn btn-xs right"><span class="wc-browse">Browse...</span><span class="wc-loader"></span></b>
													<input name="filename" type="file" id="logo_file_input" accept=".jpg, .jpeg, .gif, .bmp, .png">
													<input type="hidden" name="file_id" value="<?php echo ( $image_id ) ? $image_id : 3423439574; ?>">
													<input type="hidden" name="image_url" value="<?php echo $image_url; ?>">
												</label>
											</div>
										</div>
										<input type="text" name="quote_name" value="<?php echo isset($quotes_extra_data['quote_name']) ? $quotes_extra_data['quote_name'] : ''; ?>" size="40" aria-required="true" aria-invalid="false" placeholder="QUOTE">
									</div>
									<div class="wp-form-invoice-dates">
										<div class="row align-items-center">
											<div class="col-sm-3">
												<label for="inputtext" class="col-form-label">Quote #</label>
											</div>
											<div class="col-sm-9">
												<input type="text" name="invoice_number" class="form-control" value="<?php echo isset($quotes_extra_data['invoice_number']) ? $quotes_extra_data['invoice_number'] : '';  ?>" placeholder="0000001">
											</div>
										</div>
										<div class="row align-items-center po-number" <?php echo ($customization_poNumber == 'on') ? 'style="display: flex;"' : ''; ?>>
											<div class="col-sm-3">
												<label for="inputtext" value="<?php echo isset($quotes_extra_data['po_number']) ? $quotes_extra_data['po_number'] : '';  ?>" name="po_number" class="col-form-label">P.O. #</label>
											</div>
											<div class="col-sm-9">
												<input type="text" name="tex_number"  value="<?php echo isset($quotes_extra_data['tex_number']) ? $quotes_extra_data['tex_number'] : '';  ?>" class="form-control" placeholder="">
											</div>
										</div>
										<div class="row align-items-center">
											<div class="col-sm-3">
												<label for="inputPassword" class="col-form-label">Quote Date</label>
											</div>
											<div class="col-sm-9">
												<input type="date" id="start" name="trip-start" value="<?php echo isset( $quotes_extra_data['trip-start'] ) ? $quotes_extra_data['trip-start'] : ''; ?>">
											</div>
										</div>
										<div class="row align-items-center">
											<div class="col-sm-3">
												<label for="trip-end" class="col-form-label">Expiration Date</label>
											</div>
											<div class="col-sm-9">
												<input type="date" id="end" name="trip-end" value="<?php echo isset( $quotes_extra_data['trip-end'] ) ? $quotes_extra_data['trip-end'] : ''; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="wc-eos-price <?php //echo ( $edit_id ) ? 'wc-edit' : ''; ?>">
							<?php /*<div class="price-eqp-checkbox">
								<?php
								$eqp_checked = '';
								if( $wc_eqp_price[0] == 'eqp' ){ $eqp_checked = 'checked'; }
								?>
								<input type="checkbox" <?php echo $eqp_checked; ?> class="wc_eqp_price check_box wc_custom_checkbox" name="wc_eqp_price[]" value="eqp"> 
								<label class="checkmark-lable" for="wc_eqp_price">EQP </label>
							</div>
							<div class="price-rp-checkbox">
								<?php
								$net_checked = '';
								if( $wc_eqp_price[1] == 'net' ){ $net_checked = 'checked'; }
								?>
								<input type="checkbox" <?php echo $net_checked; ?> class="wc_eqp_price wc_custom_checkbox" name="wc_eqp_price[]" value="net">
								<label class="checkmark-lable" for="wc_eqp_price">NET</label>
								</div> */ ?>
								<div class="price-eqp-checkbox">
									<?php
									$eqp_checked = '';
									if( $wc_eqp_price[0] == 'eqp' ){ $eqp_checked = 'checked'; }
									?>

									<label class="checkmark-lable" for="wc_eqp_price">
										<input type="checkbox" id="wc_eqp_price" <?php echo $eqp_checked; ?> class="wc_eqp_price wc_checkbox" name="wc_eqp_price[]" value="eqp"> 
										EQP 
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="price-rp-checkbox">
									<?php
									$net_checked = '';
									if( $wc_eqp_price[1] == 'net' ){ $net_checked = 'checked'; }
									?>

									<label class="checkmark-lable" for="wc_rp_price">
										<input type="checkbox" id="wc_rp_price" <?php echo $net_checked; ?> class="wc_rp_price wc_checkbox" name="wc_eqp_price[]" value="net">
										NET
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="wc-reset-wrap">
									<button type="button" class="wc-reset">Clear</button>
								</div>
							</div>
							<div class="wp-datatable wc_invoice_repeter">
								<table id="repeatable-fieldset-one">
									<thead>
										<tr>
											<?php 
											$txt_string = 'Unit Price';
											if( !empty( $wc_eqp_price ) && in_array( "eqp", $wc_eqp_price )  ){
												$txt_string = 'EQP Price';	
											}
											if( !empty( $wc_eqp_price ) && in_array( "net", $wc_eqp_price ) ){
												$txt_string = 'NET Price';
											}	
											if( !empty( $wc_eqp_price ) && in_array( "eqp", $wc_eqp_price ) && in_array( "net", $wc_eqp_price ) ){
												$txt_string = 'EQP and NET Price';
											}
											?>
											<th></th>
											<th>Item</th>
											<th>Color</th>
											<th>Description</th>
											<th class="wc-text-label"><?php echo $txt_string; ?></th>
											<?php /*<th class="repeatable-net-price" <?php echo ( $wc_eqp_price[1] == 'net' ) ? 'style="display: table-cell"' : ''; ?> >Net Price</th> */ ?>
											<th>Quantity</th>
											<th>Amount</th>	
										</tr>
									</thead>
									<tbody>
										<?php
										$cnt = 2;
										$prod_data = isset($quotes_extra_data['invoice_data']) ? $quotes_extra_data['invoice_data'] : '';
										if( $prod_data ){
											$cnt = count($prod_data);
											foreach ($prod_data as $key => $value) {
												$item_id = isset($value['item']) ? $value['item'] : '';
												$description = isset($value['description']) ? $value['description'] : '';
												$unit = isset($value['unit']) ? $value['unit'] : '';
												$net = isset($value['net']) ? $value['net'] : '';
												$qty = isset($value['qty']) ? $value['qty'] : '';
												$varitions_id  = isset( $value['varitions'] ) ? $value['varitions'] : '';
												$tax = isset($value['tax']) ? $value['tax'] : '';
												$total = isset($value['total']) ? $value['total'] : '';
												$varitions_image_url = isset($value['wc-hidden-var-image']) ? $value['wc-hidden-var-image'] : '';
												?>
												<tr>
													<td>
														<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
															<i class="wd-tools-icon close-icon"></i>
														</button>
													</td>
													<td class="wc-repete-selector">
														<select class="form-select wc-product" aria-label="Default select example" name="invoice_data[<?php echo $key; ?>][item]">
															<?php echo quote_product_list_loop( $item_id ); ?> 
														</select>
													</td>
													<td class="wc-repete-varitions">
														<div class="wc-var-wrap">
															<span class="wc-loader"></span>
															<select class="form-select wc-varitions-product" aria-label="Default select example" name="invoice_data[<?php echo $key; ?>][varitions]">
																<?php
																$variations = array();
																$product = wc_get_product( $item_id );
																if( is_object($product) ){
																	$variations = $product->get_available_variations();	
																}
																
																if( $variations ){
																	$var_prod_id = wp_list_pluck( $variations, 'variation_id' );
																	if( $var_prod_id ){			
																		foreach ($var_prod_id as $values) {
																			$variation = new WC_Product_Variation($values);
																			$regular_price = $variation->get_price();
																			$variationName = implode(" / ", $variation->get_variation_attributes());
																			$pricing_values  = get_post_meta( $values, '_as_quantity_range_pricing_values', true );
																			$thumbnail_id  = get_post_meta( $values, '_thumbnail_id', true );
																			$image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');
																			if( !empty($image_attributes) ){
																				$image = $image_attributes[0];
																			} else {
																				$image = wc_placeholder_img_src( 'woocommerce_thumbnail' );
																			}
																			$select = '';
																			if( $varitions_id == $values ){
																				$select = 'selected';
																			}
																			?>
																			<option value="<?php echo $values; ?>" data-price="<?php echo $regular_price; ?>" data-currency="<?php echo get_woocommerce_currency_symbol()?>" data-image="<?php echo $image; ?>" data-variation='<?php echo json_encode($pricing_values); ?>' <?php echo $select; ?> ><?php echo $variationName; ?></option>
																			<?php
																		}
																	}
																}
																?>
															</select>
														</div>
													</td>
													<td class="wc-repete-desc">
														<div class="wc-var-image" <?php echo ( $varitions_image_url ) ? 'style="display: block;"' : '';  ?> ><img src="<?php echo $varitions_image_url; ?>"><input type="hidden" class="wc-hidden-var-image" name="invoice_data[<?php echo $key; ?>][wc-hidden-var-image]" value="<?php echo $varitions_image_url; ?>"></div>
														<textarea name="invoice_data[<?php echo $key; ?>][description]" class="wc-desc"  aria-required="true" aria-invalid="false" placeholder="Additional Information"><?php echo $description; ?></textarea>
													</td>
													<td class="wc-repete-unit">
														<div class="wc-unit-price">
															<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
															<input type="text" name="invoice_data[<?php echo $key; ?>][unit]" id="unit_price" class="text-right linePrice inputNumber" value="<?php echo $unit; ?>" autocomplete="nope" placeholder="0.00" >
														</div>
													</td>
													<?php /*<td class="wc-repete-net repeatable-net-price" <?php echo ( $wc_eqp_price[1] == 'net' ) ? 'style="display: table-cell"' : ''; ?>>
														<div class="wc-net-price">
															<div class="wc-net-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
															<input type="text" name="invoice_data[<?php echo $key; ?>][net]" id="net_price" class="text-right netPrice inputNumber" value="<?php echo $net; ?>" autocomplete="nope" placeholder="0.00" >
														</div>
														</td> */ ?>
														<td class="wc-repete-qty">
															<input type="number" name="invoice_data[<?php echo $key; ?>][qty]" id="qty" min="0" class="text-right lineQty inputNumber" value="<?php echo $qty; ?>" autocomplete="nope" placeholder="0" step="1">
														</td>
														<td class="wc-repete-total">
															<div class="wc-total-price">
																<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
																<input type="text" tabindex="-1" name="invoice_data[<?php echo $key; ?>][total]" id="total" class="lineTotal" placeholder="0" value="<?php echo $total; ?>" readonly="">
															</div>
														</td>
													</tr>
													<?php
												} 
											} else {
												?>
												<tr>
													<td>
														<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
															<i class="wd-tools-icon close-icon"></i>
														</button>
													</td>
													<td class="wc-repete-selector">
														<select class="form-select wc-product" aria-label="Default select example" name="invoice_data[0][item]">
															<?php echo quote_product_list_loop(); ?>
														</select>
													</td>
													<td class="wc-repete-varitions">
														<div class="wc-var-wrap">
															<span class="wc-loader"></span>
															<select class="form-select wc-varitions-product" aria-label="Default select example" name="invoice_data[0][varitions]"></select>
														</div>
													</td>
													<td class="wc-repete-desc">
														<div class="wc-var-image"><img src=""><input type="hidden" class="wc-hidden-var-image" name="invoice_data[0][wc-hidden-var-image]" value=""></div>
														<textarea name="invoice_data[0][description]" class="wc-desc" aria-required="true" aria-invalid="false" placeholder="Additional Information"></textarea>
													</td>
													<td class="wc-repete-unit">
														<div class="wc-unit-price">
															<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
															<input type="text" name="invoice_data[0][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00" >
														</div>
													</td>
												<?php /*<td class="wc-repete-net repeatable-net-price" <?php echo ( $wc_eqp_price[1] == 'net' ) ? 'style="display: table-cell"' : ''; ?>>
													<div class="wc-net-price">
														<div class="wc-net-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
														<input type="text" name="invoice_data[0][net]" id="net_price" class="text-right netPrice inputNumber" value="" autocomplete="nope" placeholder="0.00" >
													</div>
													</td> */ ?>
													<td class="wc-repete-qty">
														<input type="number" name="invoice_data[0][qty]" id="qty" class="text-right lineQty inputNumber" min="0" value="" autocomplete="nope" placeholder="0" step="1" >
													</td>
													<td class="wc-repete-total">
														<div class="wc-total-price">
															<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
															<input type="text" tabindex="-1" name="invoice_data[0][total]" id="total" class="lineTotal" placeholder="0" value="" readonly="">
														</div>
													</td>

												</tr>
												<tr>
													<td>
														<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
															<i class="wd-tools-icon close-icon"></i>
														</button>
													</td>
													<td class="wc-repete-selector">
														<select class="form-select wc-product" aria-label="Default select example" name="invoice_data[1][item]"> 
															<?php echo quote_product_list_loop(); ?>
														</select>
													</td>
													<td class="wc-repete-varitions">
														<div class="wc-var-wrap">
															<span class="wc-loader"></span>
															<select class="form-select wc-varitions-product" aria-label="Default select example" name="invoice_data[1][varitions]"></select>
														</div>
													</td>
													<td class="wc-repete-desc">
														<div class="wc-var-image"><img src=""><input type="hidden" class="wc-hidden-var-image" name="invoice_data[1][wc-hidden-var-image]" value=""></div>
														<textarea name="invoice_data[1][description]" class="wc-desc" aria-required="true" aria-invalid="false" placeholder="Additional Information"></textarea>
													</td>
													<td class="wc-repete-unit">
														<div class="wc-unit-price">
															<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
															<input type="text" name="invoice_data[1][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
														</div>
													</td>
													<?php /*<td class="wc-repete-net repeatable-net-price" <?php echo ( $wc_eqp_price[1] == 'net' ) ? 'style="display: table-cell"' : ''; ?>>
														<div class="wc-net-price">
															<div class="wc-net-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
															<input type="text" name="invoice_data[1][net]" id="net_price" class="text-right netPrice inputNumber" value="" autocomplete="nope" placeholder="0.00" >
														</div>
														</td>*/?>
														<td class="wc-repete-qty">
															<input type="number" name="invoice_data[1][qty]" id="qty" min="0" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0" step="1" >
														</td>
														<td class="wc-repete-total">
															<div class="wc-total-price">
																<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
																<input type="text" tabindex="-1" name="invoice_data[1][total]" id="total" class="lineTotal" placeholder="0" value="" readonly="">
															</div>
														</td>
													</tr>
													<?php
												}
												?>
											</tbody>
										</table>
										<div class="button-row">
											<button type="button" class="btn btn-xs btnAddRow" id="add-row" data-count="<?php echo $cnt; ?>">
												<i class="icon-new"></i><span>New Line</span>
											</button>
										</div>
									</div>
									<div class="wp-foot">
										<div class="wp-foot-left">
											<div class="wc-extra-comment">
												<label class="input-label">Setup Fee And Run Charge Info</label>
												<div id="extra_editor" class="comman_editor">
													<?php echo isset($quotes_extra_data['wc_extra_comments']) ? $quotes_extra_data['wc_extra_comments'] : ''; ?>
												</div>
												<textarea style="display:none" name="wc_extra_comments" id="wc_extra_comments" rows="4" class="growTextarea wc_extra_comments comman_editor_val"><?php echo isset($quotes_extra_data['wc_extra_comments']) ? $quotes_extra_data['wc_extra_comments'] : '';
											?></textarea> 
										</div>
										<label class="input-label notes">Notes</label>
										<div id="editor" class="comman_editor">
											<?php
											echo isset($quotes_extra_data['invoice_notes']) ? $quotes_extra_data['invoice_notes'] : 'NOTE:<br/>FOB Newark, NJ.<br/>Freight Estimate Available by Request.<br/>Visit peerlessumbrella.com/inventory for real-time inventory status<br/>Please contact your rep for virtuals, questions or additional assistance.';
											?>
										</div>
										<textarea name="invoice_notes" id="invoice_notes" rows="4" class="growTextarea comman_editor_val" style="display:none"><?php
										echo isset($quotes_extra_data['invoice_notes']) ? $quotes_extra_data['invoice_notes'] : 'NOTE:<br/>FOB Newark, NJ.<br/>Freight Estimate Available by Request.<br/>Visit peerlessumbrella.com/inventory for real-time inventory status<br/>Please contact your rep for virtuals, questions, additional assistance.';
									?></textarea> 
								</div>
								<div class="wp-foot-right" style="display:none;">
									<table>
										<tbody>
											<tr>
												<td class="sum-label">Subtotal</td>
												<td class="wc-sub"><span class="wc-sub-currency"><?php echo  get_woocommerce_currency_symbol();?></span><span class="wc_value"><?php echo isset($quotes_extra_data['foot_sub_total']) ? $quotes_extra_data['foot_sub_total'] : '0.00';  ?></span><input type="hidden" name="foot_sub_total" value="<?php echo isset($quotes_extra_data['foot_sub_total']) ? $quotes_extra_data['foot_sub_total'] : '0.00';  ?>"></td>
											</tr>
											<tr>
												<td class="sum-label">Total</td>
												<td class="wc-total"><span class="wc-total-currency"><?php echo get_woocommerce_currency_symbol();?></span><span class="wc_value"><?php echo isset($quotes_extra_data['foot_total']) ? $quotes_extra_data['foot_total'] : '0.00';  ?></span><input type="hidden" name="foot_total" value="<?php echo isset($quotes_extra_data['foot_total']) ? $quotes_extra_data['foot_total'] : '0.00';  ?>"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="wp-viewinvoice-form-wrapper">
							<div class="viewinvoice-address-logo">
								<div class="invoice-from-address">
									<h4 style="margin-bottom: 0;">From</h4>
									<span class="wc-from-comapny-name"></span><br/>
									<span class="wc-from-name"></span><br/>
									<span class="wc-from-email"></span><br/>
									<span class="wc-from-address"></span>
								</div>
								<div class="invoice-from-logo">
									<img src="" alt="">
								</div>
							</div>
							<div class="viewinvoice-toname-table">
								<div class="invoice-from-toname">
									<h4 style="margin-bottom: 0;">To</h4>
									<span class="wc-to-comapny-name"></span><br/>
									<span class="wc-to-name"></span><br/>
									<span class="wc-to-email"></span><br/>
									<span class="wc-to-phone"></span><br/>
									<span class="wc-to-address"></span>
								</div>
								<div class="invoice-table">
									<h4 style="margin-bottom:0px;">Quotes</h4>
									<table>
										<tbody>
											<tr>
												<td>Quotes #</td>
												<td class="preview-invoice-id"></td>
											</tr>
											<tr>
												<td>P.O. #</td>
												<td class="preview-po"></td>
											</tr>
											<tr>
												<td>Quote Date</td>
												<td class="preview-invoice-date"></td>
											</tr>
											<tr>
												<td>Expiration Date</td>
												<td class="preview-due-date"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="viewinvoice-dataTable">
								<table>
									<thead>
										<tr>
											<th>Item</th>
											<th>Color</th>
											<th>Description</th>
											<th class="wc-hide-price">Unit Price</th>
											<th>Quantity</th>
											<th>Amount</th>
										</tr>
									</thead>
									<tbody>									
										<tr class="quotes-append-html">
											<td colspan="6"></td>
										</tr>	
										<tr class="wc-notes-pricing">
											<td class="wc-text" colspan="6" style="text-align: right;padding: 10px 10px;"></td>
										</tr>
										<tr class="wc-extra-notes">
											<td class="wc-extra-text" colspan="6"></td>
										</tr>
										<tr>
											<td class="invoice-notes" colspan="6"><u>NOTES:</u></td>
										</tr>
										<tr class="invoice-row" style="display:none;">
											<td colspan="5"></td>
											<td>
												<div class="invoice-subtotal-table">
													<table>
														<tbody>
															<tr>
																<td>Subtotal</td>
																<td class="wc-preview-subtotal"></td>
															</tr>
															<tr class="invoice-sum-tax" style="display:none;">
																<td><span></span></td>
																<td><span></span></td>
															</tr>
															<tr>
																<td>Total</td>
																<td class="wc-preview-total"></td>
															</tr>
														</tbody>
													</table>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>	
						</div>
						<!------------------- ||  Print Html Start || --------------------->
						<!-- ------------------||***************||-------------------->

						<div class="wp-viewinvoice-form-wrapper print-html-hidden" style="max-width: 500px; width: 100%;">
							<table width="100%" style="caption-side: bottom;border-collapse: collapse;">
								<tr>
									<td>
										<div class="viewinvoice-address-logo" style="width:100%;">
											<table style="width:100%;">
												<tr>
													<td style="text-align:left;">
													</td>
													<td  style="text-align:right; vertical-align: top;">
														<div class="invoice-from-logo">
															<img src="" alt="" style="width: 100px;height: auto;">
														</div>
													</td>
												</tr>
												<tr>
													<td style="text-align:left;">
														<div class="invoice-from-address">
															<h4 style="margin: 0;">From</h4>
															<span class="wc-from-comapny-name"></span><br/>
															<span class="wc-from-name"></span><br/>
															<span class="wc-from-email"></span><br/>
															<span class="wc-from-address"></span>
														</div>
													</td>
													<td  style="text-align:right; vertical-align: top;"></td>
												</tr>
											</table>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<span class="viewinvoice-toname-table" style="width:100%;clear: both;margin: 0;display: block; ">
											<table style="width:100%;" style="caption-side: bottom;border-collapse: collapse;">
												<tr class="invoice-from-toname"><br/>
													<th style="text-align:left;"><h4 style="margin-bottom: -10px;">To</h4></th>
													<th></th>
													<th style="text-align:right;"><h4 style="margin-bottom: -10px;">Quotes</h4></th>
												</tr>
												<tr>
													<td class="invoice-from-toname"><span class="wc-to-comapny-name"></span></td>
													<td style="text-align:right;">Quotes #</td>
													<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-invoice-id" style="text-align:right;"></td></tr></table></span></td>
												</tr>
												<tr>
													<td class="invoice-from-toname"><span class="wc-to-name"></span></td>
													<td style="text-align:right;">P.O. #</td>
													<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-po" style="text-align:right;"></td></tr></table></span></td>
												</tr>
												<tr>
													<td class="invoice-from-toname"><span class="wc-to-email"></span></td>
													<td style="text-align:right;">Quote Date</td>
													<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-invoice-date" style="text-align:right;"></td></tr></table></span></td>
												</tr>
												<tr>
													<td class="invoice-from-toname"><span class="wc-to-phone"></span></td>
													<td style="text-align:right;">Expiration Date</td>
													<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-due-date" style="text-align:right;"></td></tr></table></span></td>
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
									<td style="padding-top: 5px;">
										<div class="viewinvoice-dataTable">
											<table style="border: 1px solid #00abc8; border-collapse: collapse; border-spacing: 0;width: 100%; caption-side: bottom;border-collapse: collapse;">
												<thead>
													<tr>
														<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Item</th>
														<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Color</th>
														<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Description</th>
														<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Unit Price</th>
														<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Quantity</th>
														<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff;  text-align: center;">Amount</th>
													</tr>
												</thead>
												<tbody>									
													<tr class="quotes-append-html" style="text-align: center; border: 1px solid #000;">
														<td colspan="6" style="border-bottom: 0; text-align: center;"></td>
													</tr>
													<tr class="wc-notes-pricing">
														<td class="wc-text" colspan="6" style="padding: 10px 10px;border-bottom: 0; text-align: right!important;"></td>
													</tr>
													<tr class="wc-extra-notes">
														<td class="wc-extra-text" colspan="6" style="border-bottom: 0;  padding-top: 10px; padding-left: 12px; margin-bottom:5px;"></td>
													</tr>
													<tr>
														<td class="invoice-notes" colspan="6" style="border-bottom: 0;  padding-top: 20px; padding-left: 12px; margin-bottom:5px;"><u>NOTES:</u></td>
													</tr>
													<tr class="invoice-row">
														<td colspan="5" style="border-bottom: 0;"></td>
														<td style="border-bottom: 0;">
															<div class="invoice-subtotal-table">
																<table style="margin-bottom: 0; border: 0; border-left: 1px solid #00abc8; border-collapse: collapse; border-spacing: 0; caption-side: bottom;border-collapse: collapse;">
																	<tbody>
																		<tr style="display:none;">
																			<td style="font-weight: bold; color: #000000; padding: 10px 12px; border-bottom: 0;">Subtotal</td>
																			<td class="wc-preview-subtotal" style="padding: 10px 12px; border-bottom: 0;"></td>
																		</tr>
																		<tr class="invoice-sum-tax" style="display:none;">
																			<td style="color: #777777 !important; padding: 0px 12px 10px !important; font-weight: normal !important; border-bottom: 1px solid #ccc !important;"><span></span></td>
																			<td style="color: #777777 !important; padding: 0px 12px 10px !important; font-weight: normal !important; border-bottom: 1px solid #ccc !important;"><span></span></td>
																		</tr>
																		<tr style="display:none;">
																			<td style="font-weight: bold; color: #000000; padding: 10px 12px; border-bottom: 0;">Total</td>
																			<td class="wc-preview-total" style="padding: 10px 12px; border-bottom: 0;"></td>
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
						<!------------------- ||  Print Html End || --------------------->
						<!-- ------------------||***************||-------------------->
					</div>

					<div class="wp-tabs">
						<ul class="nav tabs">
							<li class="nav-item tab-link current" data-tab="tab-1">
								<a id="preview" class="wc-preview nav-link active" aria-current="page" href="javascript:;">Preview</a>
							</li>
							<li class="nav-item tab-link" data-tab="tab-2">
								<a id="age" title="" class="nav-link wc-print" href="javascript:;">Print</a>
							</li>
							<li class="nav-item tab-link" data-tab="tab-3">
								<a id="age" title="" class="nav-link wc-pdf" href="javascript:;"><span class="wc-pdf-name">PDF</span><span class="wc-loader"></span></a>
							</li>
							<li class="nav-item tab-link" data-tab="tab-4">
								<a id="age" title="" class="nav-link wc-send quotes-popup" data-id="<?php echo $uer_id; ?>" href="#quotes-form"><span class="wc-send-name">Send</span><span class="wc-loader"></span></a>
							</li>
						</ul>
						<?php  
						$edit_id = isset( $_GET['edit'] ) ? $_GET['edit'] : ''; 
						$title_html = ( $edit_id ) ? 'Update' : 'Save';
						?>
						<div class="save-btn">
							<?php $login_class = ( !is_user_logged_in()  ) ? 'disabled' : ''; ?>
							<a href="#logged-in-modal" class="wc-save <?php echo $login_class; ?>"><span class="wc-save-title"><?php echo $title_html; ?></span><span class="wc-loader"></span></a>
							<input type="hidden" name="update_data" value="<?php echo base64_encode($edit_id); ?>">
						</div>
					</div>
					<?php wp_nonce_field('quotesSave', 'formType'); ?>
				</form>
				<div class="wc_ship_repeter">
					<div style="display: none;">
						<table>
							<tr class="empty-row screen-reader-text">
								<td>
									<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
										<i class="wd-tools-icon close-icon"></i>
									</button>
								</td>
								<td class="wc-repete-selector">
									<select class="form-select wc-product" aria-label="Default select example" name="invoice_data[rand_no][item]"> 
										<?php echo quote_product_list_loop(); ?>
									</select>
								</td>
								<td class="wc-repete-varitions">
									<div class="wc-var-wrap">
										<span class="wc-loader"></span>
										<select class="form-select wc-varitions-product" aria-label="Default select example" name="invoice_data[rand_no][varitions]"></select>
									</div>
								</td>
								<td class="wc-repete-desc">
									<div class="wc-var-image"><img src=""><input type="hidden" class="wc-hidden-var-image" name="invoice_data[rand_no][wc-hidden-var-image]" value=""></div>
									<textarea name="invoice_data[rand_no][description]" class="wc-desc" aria-required="true" aria-invalid="false" placeholder="Additional Information"></textarea>
								</td>
								<td class="wc-repete-unit">
									<div class="wc-unit-price">
										<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
										<input type="text" name="invoice_data[rand_no][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
									</div>
								</td>
							<?php /* <td class="wc-repete-net repeatable-net-price" <?php echo ( $wc_eqp_price[1] == 'net' ) ? 'style="display: table-cell"' : ''; ?>>
								<div class="wc-net-price">
									<div class="wc-net-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
									<input type="text" name="invoice_data[rand_no][net]" id="net_price" class="text-right netPrice inputNumber" value="" autocomplete="nope" placeholder="0.00" >
								</div>
								</td>*/ ?>
								<td class="wc-repete-qty">
									<input type="number" name="invoice_data[rand_no][qty]" id="qty" class="text-right lineQty inputNumber" min="0" value="" autocomplete="nope" placeholder="0" step="1" >
								</td>
								<td class="wc-repete-total">
									<div class="wc-total-price">
										<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
										<input type="text" tabindex="-1" name="invoice_data[rand_no][total]" id="total" class="lineTotal" placeholder="0" value="" readonly="">
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>

			<?php  }  if( $_GET['type'] && !empty($_GET['type']) && $_GET['type'] == 'custom' ){   ?>
<!-- 
//-----------------------------------------------------
// New Custom Quotes Start 
//----------------------------------------------------- 
-->
<form method="post" enctype="multipart/form-data" class="wc_custom_quote_form">
	<?php
	$uer_id = get_current_user_id();
	$edit_id = isset( $_GET['edit'] ) ? $_GET['edit'] : '';
	$wc_logo_hide = ( $edit_id ) ? 'wc-hide-logo' : '';
	$post =  get_post( $edit_id );
	$id = isset($post->ID) ? $post->ID : '';
	$image_id = get_post_meta( $id, 'logo_id', true );
	$quotes_extra_data = get_post_meta( $id, 'quotes_extra_data', true );
	$wc_eqp_price = isset($quotes_extra_data['wc_eqp_price']) ? $quotes_extra_data['wc_eqp_price'] : '';
	$customization_logo  = isset( $quotes_extra_data['customization_logo'] ) ? $quotes_extra_data['customization_logo'] : '';
	$customization_poNumber  = isset( $quotes_extra_data['customization_poNumber'] ) ? $quotes_extra_data['customization_poNumber'] : '';
	?>
	<div class="wc-backto-quotes">
		<a href="<?php echo site_url('/quotes')?>"><span class="wd-icon"></span>Back To Quotes</a>
	</div>
	<div class="alert alert-info">
		<span class="hidden-small">Want to customize your quote?</span>
		<a href="javascript:;" id="btnToggleOptions">Show Customization Options</a>
	</div>
	<div id="wp-options" class="hidden-print open">
		<div class="wp-options-wrapper">
			<label class="option label-checkbox">
				<?php
				$logo_class = $logo_checked = '';
				if( $customization_logo == 'on' ){
					$logo_checked = 'checked';
					//$logo_class = "active";
				}
				?>
				<input type="checkbox" onclick="" <?php echo $logo_checked; ?> class="wc_top_logo active <?php //echo $logo_class; ?> wc_custom_checkbox " name="customization_logo" id="customization_logo">
				<span>Logo</span> 
			</label>
			<label class="option label-checkbox">
				<?php
				$po_class = $po_checked = '';
				if( $customization_poNumber == 'on' ){
					$po_checked = 'checked';
					$po_class = "active";
				}
				?>
				<input type="checkbox" name="customization_poNumber" <?php echo $po_checked; ?> class="wc_top_po <?php echo $po_class; ?> wc_custom_checkbox" id="customization_poNumber">
				<span>P.O. #</span> 
			</label>
		</div>
	</div>
	<div class="wp-tabs">
		<ul class="nav tabs">
			<li class="nav-item tab-link current" data-tab="tab-1">
				<a id="preview" class="wc-preview nav-link active" aria-current="page" href="javascript:;">Preview</a>
			</li>
			<li class="nav-item tab-link" data-tab="tab-2">
				<a id="age" title="" class="nav-link wc-print" href="javascript:;">Print</a>
			</li>
			<li class="nav-item tab-link" data-tab="tab-3">
				<a id="age" title="" class="nav-link wc-pdf" href="javascript:;"><span class="wc-pdf-name">PDF</span><span class="wc-loader"></span></a>
			</li>
			<li class="nav-item tab-link" data-tab="tab-4">
				<a id="age" title="" class="nav-link wc-send quotes-popup" data-id="<?php echo $uer_id; ?>" href="#quotes-form"><span class="wc-send-name">Send</span><span class="wc-loader"></span></a>
			</li>
		</ul>
		<?php  
		$edit_id = isset( $_GET['edit'] ) ? $_GET['edit'] : ''; 
		$title_html = ( $edit_id ) ? 'Update' : 'Save';
		?>
		<div class="save-btn">
			<?php $login_class = ( !is_user_logged_in()  ) ? 'disabled' : ''; ?>
			<a href="#logged-in-modal" class="wc-save <?php echo $login_class; ?>"><span class="wc-save-title"><?php echo $title_html; ?></span><span class="wc-loader"></span></a>
			<input type="hidden" name="update_data" value="<?php echo base64_encode($edit_id); ?>">
		</div>
	</div>
	<div id="logged-in-modal" class="mfp-hide white-popup-block">
		<h1>Quote</h1>
		<div class="wc-loged-in-popup">
			<span>To save your quote, you can login first.</span>
			<a href="<?php echo site_url('/my-account');?>" class="wc-logged-in-save">Click Here</a>
		</div>
		<p><a class="logged-modal-dismiss" href="javascript:;">X</a></p>
	</div>
	<div class="wp-paper">
		<div class="wp-quote-form-wrapper">
			<div class="wp-meta">
				<div class="wp-meta-left">
					<div class="wp-meta-from">
						<label>From</label>
						<input type="text" name="from_company_name" value="<?php echo isset($quotes_extra_data['from_company_name']) ? $quotes_extra_data['from_company_name'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Company Name">
						<input type="text" name="from_your_name" value="<?php echo isset($quotes_extra_data['from_your_name']) ? $quotes_extra_data['from_your_name'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Name">
						<input type="email" name="from_email" value="<?php echo isset($quotes_extra_data['from_email']) ? $quotes_extra_data['from_email'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Email">
					</div>
					<div class="wp-meta-from wp-meta-to">
						<label>To</label>
						<input type="text" name="to_comapny_name" value="<?php echo isset( $quotes_extra_data['to_comapny_name'] ) ? $quotes_extra_data['to_comapny_name'] : ''; ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Customer (Company) Name">
						<input type="text" name="to_your_name" value="<?php echo isset( $quotes_extra_data['to_your_name'] ) ? $quotes_extra_data['to_your_name'] : ''; ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Customer name">
						<input type="email" name="to_email" value="<?php echo isset($quotes_extra_data['to_email']) ? $quotes_extra_data['to_email'] : '';  ?>" size="40" aria-required="true" aria-invalid="false" placeholder="Email">
						<input type="tel" name="to_telphone" placeholder="(optional) Phone Number" value="<?php echo isset($quotes_extra_data['to_telphone']) ? $quotes_extra_data['to_telphone'] : '';  ?>" pattern="[0-9]{3} [0-9]{3} [0-9]{4}">    
					</div>
				</div>
				<div class="wp-meta-right">
					<div class="wp-invoice-quete">
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
						<div class="wp-upload-logo wc-hide-logo" <?php echo ($customization_logo == 'on') ? 'style="display: block"' : 'style="display: block"'; ?> >
							<div id="upload_area" style="" class="empty yournamehere">
								<?php echo '<img src="'.$image_url.'" />'; ?>
							</div>
							<div class="wp-clearfix">
								<label id="btnUploadLogo" class="input-file" style="">
									<b class="btn btn-xs right"><span class="wc-browse">Browse...</span><span class="wc-loader"></span></b>
									<input name="filename" type="file" id="logo_file_input" accept=".jpg, .jpeg, .gif, .bmp, .png">
									<input type="hidden" name="file_id" value="<?php echo ( $image_id ) ? $image_id : 3423439574; ?>">
									<input type="hidden" name="image_url" value="<?php echo $image_url; ?>">
								</label>
							</div>
						</div>
						<input type="text" name="quote_name" value="<?php echo isset($quotes_extra_data['quote_name']) ? $quotes_extra_data['quote_name'] : ''; ?>" size="40" aria-required="true" aria-invalid="false" placeholder="QUOTE">
					</div>
					<div class="wp-form-invoice-dates">
						<div class="row align-items-center">
							<div class="col-sm-3">
								<label for="inputtext" class="col-form-label">Quote #</label>
							</div>
							<div class="col-sm-9">
								<input type="text" name="invoice_number" class="form-control" value="<?php echo isset($quotes_extra_data['invoice_number']) ? $quotes_extra_data['invoice_number'] : '';  ?>" placeholder="0000001">
							</div>
						</div>
						<div class="row align-items-center po-number" <?php echo ($customization_poNumber == 'on') ? 'style="display: flex;"' : ''; ?>>
							<div class="col-sm-3">
								<label for="inputtext" value="<?php echo isset($quotes_extra_data['po_number']) ? $quotes_extra_data['po_number'] : '';  ?>" name="po_number" class="col-form-label">P.O. #</label>
							</div>
							<div class="col-sm-9">
								<input type="text" name="tex_number"  value="<?php echo isset($quotes_extra_data['tex_number']) ? $quotes_extra_data['tex_number'] : '';  ?>" class="form-control" placeholder="">
							</div>
						</div>
						<div class="row align-items-center">
							<div class="col-sm-3">
								<label for="inputPassword" class="col-form-label">Quote Date</label>
							</div>
							<div class="col-sm-9">
								<input type="date" id="start" name="trip-start" value="<?php echo isset( $quotes_extra_data['trip-start'] ) ? $quotes_extra_data['trip-start'] : ''; ?>">
							</div>
						</div>
						<div class="row align-items-center">
							<div class="col-sm-3">
								<label for="trip-end" class="col-form-label">Expiration Date</label>
							</div>
							<div class="col-sm-9">
								<input type="date" id="end" name="trip-end" value="<?php echo isset( $quotes_extra_data['trip-end'] ) ? $quotes_extra_data['trip-end'] : ''; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="wc-eos-price">

				<div class="price-eqp-checkbox">
					<?php
					$eqp_checked = '';
					if( $wc_eqp_price[0] == 'eqp' ){ $eqp_checked = 'checked'; }
					?>

					<label class="checkmark-lable" for="wc_eqp_price">
						<input type="checkbox" id="wc_eqp_price" <?php echo $eqp_checked; ?> class="wc_eqp_price wc_checkbox" name="wc_eqp_price[]" value="eqp"> 
						EQP 
						<span class="checkmark"></span>
					</label>
				</div>
				<div class="price-rp-checkbox">
					<?php
					$net_checked = '';
					if( $wc_eqp_price[1] == 'net' ){ $net_checked = 'checked'; }
					?>

					<label class="checkmark-lable" for="wc_rp_price">
						<input type="checkbox" id="wc_rp_price" <?php echo $net_checked; ?> class="wc_rp_price wc_checkbox" name="wc_eqp_price[]" value="net">
						NET
						<span class="checkmark"></span>
					</label>
				</div>
				<div class="wc-reset-wrap">
					<button type="button" class="wc-reset">Clear</button>
				</div>
			</div>
			<div class="wp-custom-datatable wc_custom_invoice_repeter">
				<table id="custom-repeatable-fieldset-one">
					<thead>
						<tr>
							<?php 
							$txt_string = 'Unit Price';
							if( !empty( $wc_eqp_price ) && in_array( "eqp", $wc_eqp_price )  ){
								$txt_string = 'EQP Price';	
							}
							if( !empty( $wc_eqp_price ) && in_array( "net", $wc_eqp_price ) ){
								$txt_string = 'NET Price';
							}	
							if( !empty( $wc_eqp_price ) && in_array( "eqp", $wc_eqp_price ) && in_array( "net", $wc_eqp_price ) ){
								$txt_string = 'EQP and NET Price';
							}
							?>
							<th></th>
							<th>Image</th>
							<th>Description</th>
							<th class="wc-text-label"><?php echo $txt_string; ?></th>
							<th>Quantity</th>
							<th>Amount</th>	
						</tr>
					</thead>
					<tbody>
						<?php
						$cnt = 2;
						$prod_data = isset($quotes_extra_data['custom_invoice_data']) ? $quotes_extra_data['custom_invoice_data'] : '';
						if( $prod_data ){
							$cnt = count($prod_data);
							foreach ($prod_data as $key => $value) {
								$item_id = isset($value['item']) ? $value['item'] : '';
								$description = isset($value['description']) ? $value['description'] : '';
								$unit = isset($value['unit']) ? $value['unit'] : '';
								$net = isset($value['net']) ? $value['net'] : '';
								$qty = isset($value['qty']) ? $value['qty'] : '';
								$varitions_id  = isset( $value['varitions'] ) ? $value['varitions'] : '';
								$tax = isset($value['tax']) ? $value['tax'] : '';
								$total = isset($value['total']) ? $value['total'] : '';
								$varitions_image_url = isset($value['wc-hidden-pro-image']) ? $value['wc-hidden-pro-image'] : '';
								$product_file_id = isset($value['product_file_id']) ? $value['product_file_id'] : '';
								$convert_ids = isset($value['convert_ids']) ? $value['convert_ids'] : '';
								//$p_attach_ids = get_post_meta( $product_file_id, 'p_attach_ids', true);
								$img_src_string = ( $convert_ids ) ? 'https://drive.google.com/uc?id='.$convert_ids : $varitions_image_url;
								?>
								<tr>
									<td>
										<button tabindex="-1" type="button" class="button custom-remove-row btnDeleteRow">
											<i class="wd-tools-icon close-icon"></i>
										</button>
									</td>
									<td class="wc-repete-image">
										<span class="wc-loader"></span>
										<div class="wc-pro-image <?php echo ( $convert_ids ) ? 'active' : '';?>">
											<a href="javascript:;"><span class="wd-icon"></span></a>
											<img src="<?php echo $img_src_string; ?>" width="300" height="300">
											<input type="hidden" class="wc-hidden-pro-image" name="custom_invoice_data[<?php echo $key; ?>][wc-hidden-pro-image]" value="<?php echo $varitions_image_url; ?>">
											<input type="hidden" class="product_file_id" name="custom_invoice_data[<?php echo $key; ?>][product_file_id]" value="<?php echo $convert_ids; ?>"></div>
											<input type="file" name="custom_invoice_data[0][custom-product-image]" accept="image/*" class="wc-product-image"/>
											<br>
										</td>
										<td class="wc-repete-desc">
											<textarea name="custom_invoice_data[<?php echo $key; ?>][description]" class="wc-desc"  aria-required="true" aria-invalid="false" placeholder="Additional Information"><?php echo $description; ?></textarea>
										</td>
										<td class="wc-repete-unit">
											<div class="wc-unit-price">
												<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
												<input type="text" name="custom_invoice_data[<?php echo $key; ?>][unit]" id="unit_price" class="text-right linePrice inputNumber" value="<?php echo $unit; ?>" autocomplete="nope" placeholder="0.00" >
											</div>
										</td>
										<td class="wc-repete-qty">
											<input type="number" name="custom_invoice_data[<?php echo $key; ?>][qty]" id="qty" min="0" class="text-right lineQty inputNumber" value="<?php echo $qty; ?>" autocomplete="nope" placeholder="0" step="1">
										</td>
										<td class="wc-repete-total">
											<div class="wc-total-price">
												<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
												<input type="text" tabindex="-1" name="custom_invoice_data[<?php echo $key; ?>][total]" id="total" class="lineTotal" placeholder="0" value="<?php echo $total; ?>" readonly="">
											</div>
										</td>
									</tr>
									<?php
								} 
							} else {
								?>
								<tr>
									<td>
										<button tabindex="-1" type="button" class="button custom-remove-row btnDeleteRow">
											<i class="wd-tools-icon close-icon"></i>
										</button>
									</td>
									<td class="wc-repete-image">
										<span class="wc-loader"></span>
										<div class="wc-pro-image">
											<a href="javascript:;"><span class="wd-icon"></span></a>
											<img src="">
											<input type="hidden" class="wc-hidden-pro-image" name="custom_invoice_data[0][wc-hidden-pro-image]" value=""><input type="hidden" class="product_file_id" name="custom_invoice_data[0][product_file_id]" value="">
										</div>
										<input type="file" name="custom_invoice_data[0][custom-product-image]" accept="image/*" class="wc-product-image"/>
										<br>
									</td>
									<td class="wc-repete-desc">
										<textarea name="custom_invoice_data[0][description]" class="wc-desc" aria-required="true" aria-invalid="false" placeholder="Additional Information"></textarea>
									</td>
									<td class="wc-repete-unit">
										<div class="wc-unit-price">
											<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
											<input type="text" name="custom_invoice_data[0][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00" >
										</div>
									</td>
									<td class="wc-repete-qty">
										<input type="number" name="custom_invoice_data[0][qty]" id="qty" class="text-right lineQty inputNumber" min="0" value="" autocomplete="nope" placeholder="0" step="1" >
									</td>
									<td class="wc-repete-total">
										<div class="wc-total-price">
											<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
											<input type="text" tabindex="-1" name="custom_invoice_data[0][total]" id="total" class="lineTotal" placeholder="0" value="" readonly="">
										</div>
									</td>

								</tr>
								<tr>
									<td>
										<button tabindex="-1" type="button" class="button custom-remove-row btnDeleteRow">
											<i class="wd-tools-icon close-icon"></i>
										</button>
									</td>
									<td class="wc-repete-image">
										<span class="wc-loader"></span>
										<div class="wc-pro-image">
											<a href="javascript:;"><span class="wd-icon"></span></a>
											<img src="">
											<input type="hidden" class="wc-hidden-pro-image" name="custom_invoice_data[1][wc-hidden-pro-image]" value=""><input type="hidden" class="product_file_id" name="custom_invoice_data[1][product_file_id]" value="">
										</div>
										<input type="file" name="custom_invoice_data[1][custom-product-image]" accept="image/*" class="wc-product-image"/>
										<br>
									</td>
									<td class="wc-repete-desc">
										<textarea name="custom_invoice_data[1][description]" class="wc-desc" aria-required="true" aria-invalid="false" placeholder="Additional Information"></textarea>
									</td>
									<td class="wc-repete-unit">
										<div class="wc-unit-price">
											<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
											<input type="text" name="custom_invoice_data[1][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
										</div>
									</td>
									<td class="wc-repete-qty">
										<input type="number" name="custom_invoice_data[1][qty]" id="qty" min="0" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0" step="1" >
									</td>
									<td class="wc-repete-total">
										<div class="wc-total-price">
											<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
											<input type="text" tabindex="-1" name="custom_invoice_data[1][total]" id="total" class="lineTotal" placeholder="0" value="" readonly="">
										</div>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<div class="button-row">
						<button type="button" class="btn btn-xs btnAddRow" id="add-custom-row" data-count="<?php echo $cnt; ?>">
							<i class="icon-new"></i><span>New Line</span>
						</button>
					</div>
				</div>
				<div class="wp-foot">
					<div class="wp-foot-left">
						<div class="wc-extra-comment">
							<label class="input-label">Setup Fee And Run Charge Info</label>
							<div id="extra_editor" class="comman_editor">
								<?php echo isset($quotes_extra_data['wc_extra_comments']) ? $quotes_extra_data['wc_extra_comments'] : ''; ?>
							</div>
							<textarea style="display:none" name="wc_extra_comments" id="wc_extra_comments" rows="4" class="growTextarea wc_extra_comments comman_editor_val"><?php echo isset($quotes_extra_data['wc_extra_comments']) ? $quotes_extra_data['wc_extra_comments'] : '';
						?></textarea> 
					</div>
					<label class="input-label notes">Notes</label>
					<div id="editor" class="comman_editor">
						<?php
						echo isset($quotes_extra_data['invoice_notes']) ? $quotes_extra_data['invoice_notes'] : 'NOTE:<br/>FOB Newark, NJ.<br/>Freight Estimate Available by Request.<br/>Visit peerlessumbrella.com/inventory for real-time inventory status<br/>Please contact your rep for virtuals, questions or additional assistance.';
						?>
					</div>
					<textarea name="invoice_notes" id="invoice_notes" rows="4" class="growTextarea comman_editor_val" style="display:none"><?php
					echo isset($quotes_extra_data['invoice_notes']) ? $quotes_extra_data['invoice_notes'] : 'NOTE:<br/>FOB Newark, NJ.<br/>Freight Estimate Available by Request.<br/>Visit peerlessumbrella.com/inventory for real-time inventory status<br/>Please contact your rep for virtuals, questions, additional assistance.';
				?></textarea> 
			</div>
			<div class="wp-foot-right" style="display:none;">
				<table>
					<tbody>
						<tr>
							<td class="sum-label">Subtotal</td>
							<td class="wc-sub"><span class="wc-sub-currency"><?php echo  get_woocommerce_currency_symbol();?></span><span class="wc_value"><?php echo isset($quotes_extra_data['foot_sub_total']) ? $quotes_extra_data['foot_sub_total'] : '0.00';  ?></span><input type="hidden" name="foot_sub_total" value="<?php echo isset($quotes_extra_data['foot_sub_total']) ? $quotes_extra_data['foot_sub_total'] : '0.00';  ?>"></td>
						</tr>
						<tr>
							<td class="sum-label">Total</td>
							<td class="wc-total"><span class="wc-total-currency"><?php echo get_woocommerce_currency_symbol();?></span><span class="wc_value"><?php echo isset($quotes_extra_data['foot_total']) ? $quotes_extra_data['foot_total'] : '0.00';  ?></span><input type="hidden" name="foot_total" value="<?php echo isset($quotes_extra_data['foot_total']) ? $quotes_extra_data['foot_total'] : '0.00';  ?>"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="wp-viewinvoice-form-wrapper">
		<div class="viewinvoice-address-logo">
			<div class="invoice-from-address">
				<h4 style="margin-bottom: 0;">From</h4>
				<span class="wc-from-comapny-name"></span><br/>
				<span class="wc-from-name"></span><br/>
				<span class="wc-from-email"></span><br/>
				<span class="wc-from-address"></span>
			</div>
			<div class="invoice-from-logo">
				<img src="" alt="">
			</div>
		</div>
		<div class="viewinvoice-toname-table">
			<div class="invoice-from-toname">
				<h4 style="margin-bottom: 0;">To</h4>
				<span class="wc-to-comapny-name"></span><br/>
				<span class="wc-to-name"></span><br/>
				<span class="wc-to-email"></span><br/>
				<span class="wc-to-phone"></span><br/>
				<span class="wc-to-address"></span>
			</div>
			<div class="invoice-table">
				<h4 style="margin-bottom:0px;">Quotes</h4>
				<table>
					<tbody>
						<tr>
							<td>Quotes #</td>
							<td class="preview-invoice-id"></td>
						</tr>
						<tr>
							<td>P.O. #</td>
							<td class="preview-po"></td>
						</tr>
						<tr>
							<td>Quote Date</td>
							<td class="preview-invoice-date"></td>
						</tr>
						<tr>
							<td>Expiration Date</td>
							<td class="preview-due-date"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="viewinvoice-dataTable">
			<table>
				<thead>
					<tr>
						<th>Image</th>
						<th>Description</th>
						<th class="wc-hide-price">Unit Price</th>
						<th>Quantity</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>									
					<tr class="quotes-append-html">
						<td colspan="5"></td>
					</tr>	
					<tr class="wc-notes-pricing">
						<td class="wc-text" colspan="5" style="text-align: right;padding: 10px 10px;"></td>
					</tr>
					<tr class="wc-extra-notes">
						<td class="wc-extra-text" colspan="5"></td>
					</tr>
					<tr>
						<td class="invoice-notes" colspan="5"><u>NOTES:</u></td>
					</tr>
					<tr class="invoice-row" style="display:none;">
						<td colspan="4"></td>
						<td>
							<div class="invoice-subtotal-table">
								<table>
									<tbody>
										<tr>
											<td>Subtotal</td>
											<td class="wc-preview-subtotal"></td>
										</tr>
										<tr class="invoice-sum-tax" style="display:none;">
											<td><span></span></td>
											<td><span></span></td>
										</tr>
										<tr>
											<td>Total</td>
											<td class="wc-preview-total"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>	
	</div>
	<!------------------- ||  Print Html Start || --------------------->
	<!-- ------------------||*****************||-------------------->

	<div class="wp-viewinvoice-form-wrapper print-html-hidden" style="max-width: 500px; width: 100%;">
		<table width="100%" style="caption-side: bottom;border-collapse: collapse;">
			<tr>
				<td>
					<div class="viewinvoice-address-logo" style="width:100%;">
						<table style="width:100%;">
							<tr>
								<td style="text-align:left;">
								</td>
								<td  style="text-align:right; vertical-align: top;">
									<div class="invoice-from-logo">
										<img src="" alt="" style="width: 100px;height: auto;">
									</div>
								</td>
							</tr>
							<tr>
								<td style="text-align:left;">
									<div class="invoice-from-address">
										<h4 style="margin: 0;">From</h4>
										<span class="wc-from-comapny-name"></span><br/>
										<span class="wc-from-name"></span><br/>
										<span class="wc-from-email"></span><br/>
										<span class="wc-from-address"></span>
									</div>
								</td>
								<td  style="text-align:right; vertical-align: top;"></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<span class="viewinvoice-toname-table" style="width:100%;clear: both;margin: 0;display: block; ">
						<table style="width:100%;" style="caption-side: bottom;border-collapse: collapse;">
							<tr class="invoice-from-toname"><br/>
								<th style="text-align:left;"><h4 style="margin-bottom: -10px;">To</h4></th>
								<th></th>
								<th style="text-align:right;"><h4 style="margin-bottom: -10px;">Quotes</h4></th>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-comapny-name"></span></td>
								<td style="text-align:right;">Quotes #</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-invoice-id" style="text-align:right;"></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-name"></span></td>
								<td style="text-align:right;">P.O. #</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-po" style="text-align:right;"></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-email"></span></td>
								<td style="text-align:right;">Quote Date</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-invoice-date" style="text-align:right;"></td></tr></table></span></td>
							</tr>
							<tr>
								<td class="invoice-from-toname"><span class="wc-to-phone"></span></td>
								<td style="text-align:right;">Expiration Date</td>
								<td style="padding: 0;"><span class="invoice-table"><table align="right"><tr><td class="preview-due-date" style="text-align:right;"></td></tr></table></span></td>
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
				<td style="padding-top: 5px;">
					<div class="viewinvoice-dataTable">
						<table style="border: 1px solid #00abc8; border-collapse: collapse; border-spacing: 0;width: 100%; caption-side: bottom;border-collapse: collapse;">
							<thead>
								<tr>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">image</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Description</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Unit Price</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff; border-right: 1px solid #fff; text-align: center;">Quantity</th>
									<th bgcolor="#00abc8" style="background-color: #00abc8!important; -webkit-print-color-adjust: exact; color: #fff;  text-align: center;">Amount</th>
								</tr>
							</thead>
							<tbody>									
								<tr class="quotes-append-html" style="text-align: center; border: 1px solid #000;">
									<td colspan="5" style="border-bottom: 0; text-align: center;"></td>
								</tr>
								<tr class="wc-notes-pricing">
									<td class="wc-text" colspan="5" style="padding: 10px 10px;border-bottom: 0; text-align: right!important;"></td>
								</tr>
								<tr class="wc-extra-notes">
									<td class="wc-extra-text" colspan="5" style="border-bottom: 0;  padding-top: 10px; padding-left: 12px; margin-bottom:5px;"></td>
								</tr>
								<tr>
									<td class="invoice-notes" colspan="5" style="border-bottom: 0;  padding-top: 20px; padding-left: 12px; margin-bottom:5px;"><u>NOTES:</u></td>
								</tr>
								<tr class="invoice-row">
									<td colspan="4" style="border-bottom: 0;"></td>
									<td style="border-bottom: 0;">
										<div class="invoice-subtotal-table">
											<table style="margin-bottom: 0; border: 0; border-left: 1px solid #00abc8; border-collapse: collapse; border-spacing: 0; caption-side: bottom;border-collapse: collapse;">
												<tbody>
													<tr style="display:none;">
														<td style="font-weight: bold; color: #000000; padding: 10px 12px; border-bottom: 0;">Subtotal</td>
														<td class="wc-preview-subtotal" style="padding: 10px 12px; border-bottom: 0;"></td>
													</tr>
													<tr class="invoice-sum-tax" style="display:none;">
														<td style="color: #777777 !important; padding: 0px 12px 10px !important; font-weight: normal !important; border-bottom: 1px solid #ccc !important;"><span></span></td>
														<td style="color: #777777 !important; padding: 0px 12px 10px !important; font-weight: normal !important; border-bottom: 1px solid #ccc !important;"><span></span></td>
													</tr>
													<tr style="display:none;">
														<td style="font-weight: bold; color: #000000; padding: 10px 12px; border-bottom: 0;">Total</td>
														<td class="wc-preview-total" style="padding: 10px 12px; border-bottom: 0;"></td>
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
	<!------------------- ||  Print Html End || --------------------->
	<!-- ------------------||***************||-------------------->
</div>

<div class="wp-tabs">
	<ul class="nav tabs">
		<li class="nav-item tab-link current" data-tab="tab-1">
			<a id="preview" class="wc-preview nav-link active" aria-current="page" href="javascript:;">Preview</a>
		</li>
		<li class="nav-item tab-link" data-tab="tab-2">
			<a id="age" title="" class="nav-link wc-print" href="javascript:;">Print</a>
		</li>
		<li class="nav-item tab-link" data-tab="tab-3">
			<a id="age" title="" class="nav-link wc-pdf" href="javascript:;"><span class="wc-pdf-name">PDF</span><span class="wc-loader"></span></a>
		</li>
		<li class="nav-item tab-link" data-tab="tab-4">
			<a id="age" title="" class="nav-link wc-send quotes-popup" data-id="<?php echo $uer_id; ?>" href="#quotes-form"><span class="wc-send-name">Send</span><span class="wc-loader"></span></a>
		</li>
	</ul>
	<?php  
	$edit_id = isset( $_GET['edit'] ) ? $_GET['edit'] : ''; 
	$title_html = ( $edit_id ) ? 'Update' : 'Save';
	?>
	<div class="save-btn">
		<?php $login_class = ( !is_user_logged_in()  ) ? 'disabled' : ''; ?>
		<a href="#logged-in-modal" class="wc-save <?php echo $login_class; ?>"><span class="wc-save-title"><?php echo $title_html; ?></span><span class="wc-loader"></span></a>
		<input type="hidden" name="update_data" value="<?php echo base64_encode($edit_id); ?>">
	</div>
</div>
<?php wp_nonce_field('custom_quotes_Save', 'formType'); ?>
</form>
<div class="wc_custom_ship_repeter">
	<div style="display: none;">
		<table>
			<tr class="empty-row custom-screen-reader-text">
				<td>
					<button tabindex="-1" type="button" class="button custom-remove-row btnDeleteRow">
						<i class="wd-tools-icon close-icon"></i>
					</button>
				</td>
				<td class="wc-repete-image">
					<div class="wc-pro-image">
						<a href="javascript:;"><span class="wd-icon"></span></a>
						<img src="">
						<input type="hidden" class="wc-hidden-pro-image" name="custom_invoice_data[rand_no][wc-hidden-pro-image]" value=""><input type="hidden" class="product_file_id" name="custom_invoice_data[rand_no][product_file_id]" value="">
					</div>
					<input type="file" name="custom_invoice_data[rand_no][custom-product-image]" accept="image/*" class="wc-product-image"/>
					<br>
				</td>
				<td class="wc-repete-desc">
					<textarea name="custom_invoice_data[rand_no][description]" class="wc-desc" aria-required="true" aria-invalid="false" placeholder="Additional Information"></textarea>
				</td>
				<td class="wc-repete-unit">
					<div class="wc-unit-price">
						<div class="wc-unit-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
						<input type="text" name="custom_invoice_data[rand_no][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
					</div>
				</td>
				<td class="wc-repete-qty">
					<input type="number" name="custom_invoice_data[rand_no][qty]" id="qty" class="text-right lineQty inputNumber" min="0" value="" autocomplete="nope" placeholder="0" step="1" >
				</td>
				<td class="wc-repete-total">
					<div class="wc-total-price">
						<div class="wc-total-currency"><span><?php echo get_woocommerce_currency_symbol(); ?></span></div>
						<input type="text" tabindex="-1" name="custom_invoice_data[rand_no][total]" id="total" class="lineTotal" placeholder="0" value="" readonly="">
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<!-- 
//-----------------------------------------------------
// New Custom Quotes End 
//----------------------------------------------------- 
-->
<form id="quotes-form" class="mfp-hide white-popup-block" method="post" enctype="multipart/form-data">
	<h1>Send this Quote</h1>
	<div class="wc-email-form">
		<div class="wc-quotes-client_email  wc-key-input">
			<label for="client_email"></label>
			<input id="client_email" name="client_email" type="text" placeholder="From Email" required="">
		</div>
		<div class="wc-quotes-fname wc-key-input">
			<label for="name">Customer Name</label>
			<input id="name" name="fname" type="text" placeholder="Customer Name" required="">
		</div>
		<div class="wc-quotes-email wc-key-input">
			<label for="email">Customer Email</label>
			<input id="email" name="email" type="text" placeholder="Customer Email" required="">
		</div>
		<div class="wc-quotes-submit wc-key-input">	
			<input type="submit" class="" id="wc-quotes-sub" value="Submit">
			<span class="wc-loader"></span>
		</div>
	</div>
	<button type="button" class="quotes-modal-dismiss">×</button>
</form>
<?php } ?>
</div>
</div>
<?php
return ob_get_clean();
}


/* //--------// Quote Data Ajax Function  //---------// */
/* //----------// //-------------//  */
add_action( 'wp_ajax_nopriv_wc_quote_user_data', 'wc_quote_user_data_fun' );
add_action( 'wp_ajax_wc_quote_user_data', 'wc_quote_user_data_fun' );
function wc_quote_user_data_fun(){

	if( is_user_logged_in() ){
		$serialized_arr = array();
		$user_id = get_current_user_id();
		parse_str($_POST['serialized'], $serialized_arr);
		if (isset($serialized_arr['formType']) && wp_verify_nonce($serialized_arr['formType'], 'quotesSave')) {
			wc_save_quotes_fileds( $serialized_arr, 'new', 0 );
		}
	}
	wp_send_json( array( 'type' => 'success' ) );
}


/* //--------// cutsom Quote Data Ajax Function  //---------// */
/* //----------// //-------------//  */
add_action( 'wp_ajax_nopriv_wc_custom_quote_user_data', 'wc_custom_quote_user_data_fun' );
add_action( 'wp_ajax_wc_custom_quote_user_data', 'wc_custom_quote_user_data_fun' );
function wc_custom_quote_user_data_fun(){

	if( is_user_logged_in() ){
		$serialized_arr = array();
		$user_id = get_current_user_id();
		parse_str($_POST['serialized'], $serialized_arr);
		if (isset($serialized_arr['formType']) && wp_verify_nonce($serialized_arr['formType'], 'custom_quotes_Save')) {
			wc_save_quotes_fileds( $serialized_arr, 'custom', 1 );
		}
	}
	wp_send_json( array( 'type' => 'success' ) );
}

/* //--------// Delete Quote List   //---------// */
/* //----------// //-------------//  */
add_action( 'wp_ajax_nopriv_wc_quote_list_delete_data', 'wc_quote_list_delete_data_fun' );
add_action( 'wp_ajax_wc_quote_list_delete_data', 'wc_quote_list_delete_data_fun' );
function wc_quote_list_delete_data_fun(){
	$data_id = isset($_POST['data_id']) ? $_POST['data_id'] : '';
	if( $data_id ){
		wp_delete_post( $data_id, true); 
	}
	wp_send_json( array( 'type' => 'success', 'id' => $data_id ) );
}

/* //--------// Preview Page Show data   //---------// */
/* //-----------------// //-------------------//  */
add_action( 'wp_ajax_nopriv_get_wc_priview_quotes', 'get_wc_priview_quotes_fun' );
add_action( 'wp_ajax_get_wc_priview_quotes', 'get_wc_priview_quotes_fun' );
function get_wc_priview_quotes_fun(){
	$user_id = get_current_user_id();
	$attach_id = 0;

	if( isset( $_FILES['file'] ) ){
		$response_data = array();
		$fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);	
		$fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);	
		$upload_dir = wp_upload_dir();

		$_filter = true; 
		$guest_id = time().rand(1,3);

		add_filter( 'upload_dir', function( $arr ) use( &$_filter, &$user_id ){
			if ( $_filter ) {				
				if( $user_id ){
					$folder = '/invoice/'.$user_id;		
				}else{
					$folder = '/invoice/';
				}			
				$arr['path'] = $arr['basedir'].$folder;
				$arr['url'] = $arr['baseurl'].$folder;	
			}
			return $arr;
		} );
		$upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

		$send_type = 'clients';
		if( empty($user_id) ){
			$user_id = $guest_id;	
			$send_type = 'guest';
		}
		if( $upload ){
			$file_url = $upload['url'];
			$file_path = $upload['file'];	
			$wp_filetype = wp_check_filetype( $fileName, '' );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( $fileName ),
				'post_content' => '',
				'post_status' => 'inherit',
				'post_author' => $user_id
			);
			$attach_id = wp_insert_attachment( $attachment, $file_path );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );	
			wp_update_attachment_metadata( $attach_id, $attach_data );		
		}
	}

	wp_send_json( array( 'type' => 'sucess', 'path' => $upload, 'attach_id' => $attach_id ) );

}


/* //--------// Custom Preview Page Show data   //---------// */
/* //-----------------// //-------------------//  */
add_action( 'wp_ajax_nopriv_get_wc_custom_priview_quotes', 'get_wc_custom_priview_quotes_fun' );
add_action( 'wp_ajax_get_wc_custom_priview_quotes', 'get_wc_custom_priview_quotes_fun' );
function get_wc_custom_priview_quotes_fun(){
	$user_id = get_current_user_id();
	$attach_id = 0;

	if( isset( $_FILES['file'] ) ){
		$response_data = array();
		$fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);	
		$fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);	
		$upload_dir = wp_upload_dir();

		$_filter = true; 
		$guest_id = time().rand(1,3);

		add_filter( 'upload_dir', function( $arr ) use( &$_filter, &$user_id ){
			if ( $_filter ) {				
				if( $user_id ){
					$folder = '/invoice/'.$user_id;		
				}else{
					$folder = '/invoice/';
				}			
				$arr['path'] = $arr['basedir'].$folder;
				$arr['url'] = $arr['baseurl'].$folder;	
			}
			return $arr;
		} );
		$upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

		$send_type = 'clients';
		if( empty($user_id) ){
			$user_id = $guest_id;	
			$send_type = 'guest';
		}
		if( $upload ){
			$file_url = $upload['url'];
			$file_path = $upload['file'];	
			$wp_filetype = wp_check_filetype( $fileName, '' );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( $fileName ),
				'post_content' => '',
				'post_status' => 'inherit',
				'post_author' => $user_id
			);
			$attach_id = wp_insert_attachment( $attachment, $file_path );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );	
			wp_update_attachment_metadata( $attach_id, $attach_data );		
		}
	}

	wp_send_json( array( 'type' => 'sucess', 'path' => $upload, 'attach_id' => $attach_id ) );

}

/* //--------// Custom product Upload Image   //---------// */
/* //-----------------// //-------------------//  */
add_action( 'wp_ajax_nopriv_get_wc_custom_product_image', 'get_wc_custom_product_image_fun' );
add_action( 'wp_ajax_get_wc_custom_product_image', 'get_wc_custom_product_image_fun' );
function get_wc_custom_product_image_fun(){
	$user_id = get_current_user_id();
	$attach_id = 0;
	if( isset( $_FILES['file'] ) ){
		$response_data = array();
		$fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);	
		$fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);	
		$upload_dir = wp_upload_dir();

		$_filter = true; 
		$guest_id = time().rand(1,3);

		add_filter( 'upload_dir', function( $arr ) use( &$_filter, &$user_id ){
			if ( $_filter ) {				
				if( $user_id ){
					$folder = '/product-image/'.$user_id;		
				}else{
					$folder = '/product-image/';
				}			
				$arr['path'] = $arr['basedir'].$folder;
				$arr['url'] = $arr['baseurl'].$folder;	
			}
			return $arr;
		} );

		$upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

		$send_type = 'clients';
		if( empty($user_id) ){
			$user_id = $guest_id;	
			$send_type = 'guest';
		}
		if( $upload ){
			$file_url = $upload['url'];
			/*$file_path = $upload['file'];	
			$wp_filetype = wp_check_filetype( $fileName, '' );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( $fileName ),
				'post_content' => '',
				'post_status' => 'inherit',
				'post_author' => $user_id
			);
			$attach_id = wp_insert_attachment( $attachment, $file_path );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );	
			wp_update_attachment_metadata( $attach_id, $attach_data );*/		
		}
	}

	wp_send_json( array( 'type' => 'sucess', 'path' => $file_url ) );

}


/* //--------// Preview Page Show data   //---------// */
/* //----------------// //----------------------//  */
add_action( 'wp_ajax_nopriv_wc_quote_variation_prod_id', 'wc_quote_variation_prod_id_fun' );
add_action( 'wp_ajax_wc_quote_variation_prod_id', 'wc_quote_variation_prod_id_fun' );
function wc_quote_variation_prod_id_fun(){
	$product_id = isset($_POST['data_id']) ? $_POST['data_id'] : '';
	$variations = array();
	$product = wc_get_product( $product_id );
	if( is_object($product) ){
		$variations = $product->get_available_variations();
	}
	$regular_price = $product->get_regular_price();

	$varition_html = '';
	ob_start();
	if( $variations ){
		$variations_id = wp_list_pluck( $variations, 'variation_id' );
		if( $variations_id ){			
			foreach ($variations_id as $value) {
				$variation = new WC_Product_Variation($value);
				$regular_price = $variation->get_price();
				$variationName = implode(" / ", $variation->get_variation_attributes());
				$pricing_values  = get_post_meta( $value, '_as_quantity_range_pricing_values', true );
				$thumbnail_id  = get_post_meta( $value, '_thumbnail_id', true );
				$image_attributes = wp_get_attachment_image_src($thumbnail_id, 'full');
				if( !empty($image_attributes) ){
					$image = $image_attributes[0];
				} else {
					$image = wc_placeholder_img_src( 'woocommerce_thumbnail' );
				}
				?>
				<option value="<?php echo $value; ?>" data-price="<?php echo $regular_price; ?>" data-currency="<?php echo get_woocommerce_currency_symbol()?>" data-image="<?php echo $image; ?>"  data-variation='<?php echo json_encode($pricing_values); ?>'><?php echo $variationName; ?></option>
				<?php
			}
		}
	}
	$varition_html = ob_get_clean();
	wp_send_json( array( 'type' => 'sucess', 'html' => $varition_html ) );	
}

/* //--------// Preview Page Show data   //---------// */
/* //--------------------// //---------------------//  */
add_action( 'wp_ajax_nopriv_wc_quote_send_data', 'wc_quote_send_data_fun' );
add_action( 'wp_ajax_wc_quote_send_data', 'wc_quote_send_data_fun' );
function wc_quote_send_data_fun(){

	$serialized_arr = array();
	$quote_html = (isset( $_POST['quote_html'] ) ) ? $_POST['quote_html'] : '';
	$pdf_url = wc_pdf_file_genrate_fun( $quote_html, 1 );
	$serialized = isset( $_POST['serialized'] ) ? $_POST['serialized'] : '';
	parse_str($serialized, $serialized_arr);
	
	$f_name = $serialized_arr['fname'];
	$email = $serialized_arr['email'];
	$email_explode = explode(",",$email);
	$client_email = $serialized_arr['client_email'];
	
	$quote_html_str = str_replace('500px','800px', $quote_html);
	$quote_html_str = str_replace('margin-top: 15px;','margin-top: 0px;', $quote_html_str);
	$html_str = '';	
	ob_start();
	?>
	<div class="email_reply_format">
		<p class="user_name"><strong>Dear <?php echo $f_name; ?></strong></p>
		<div class="wc_personal_data">
			<?php echo str_replace( '\\', '', $quote_html_str ); ?>
		</div>
	</div>
	<?php
	$html_str .= ob_get_clean();
	$headers = array();
	$subject = 'You got a new quote!';
	$headers[] = 'From: Peerless Umbrella <'.$client_email.'>';
	$headers[] = array('Content-Type: text/html; charset=UTF-8');
	$sent = wp_mail( $email_explode, $subject, $html_str, $headers, array($pdf_url) );
	if( $sent ){ unlink($pdf_url); }
}

/* //--------// Quote Mail Remainder   //---------// */
/* //-----------------// //----------------------//  */
add_action( 'wp_ajax_nopriv_wc_quote_mail_remainder', 'wc_quote_mail_remainder_fun' );
add_action( 'wp_ajax_wc_quote_mail_remainder', 'wc_quote_mail_remainder_fun' );
function wc_quote_mail_remainder_fun(){
	$date = date('Y-m-d', strtotime(' + 7 days')); 
	$data_id = isset( $_POST['data_id'] ) ? $_POST['data_id'] : '';
	update_post_meta( $data_id, 'quote_mail_remainder', 'yes' );
	update_post_meta( $data_id, 'quote_mail_remainder_date', $date );
	wp_send_json( array( 'type' => 'success', 'data_id' => $data_id) );
}

/* //--------// Unsend Quote Mail Remainder   //---------// */
/* //-------------------// //---------------------------//  */
add_action( 'wp_ajax_nopriv_wc_quote_mail_remainder_unsend', 'wc_quote_mail_remainder_unsend_fun' );
add_action( 'wp_ajax_wc_quote_mail_remainder_unsend', 'wc_quote_mail_remainder_unsend_fun' );
function wc_quote_mail_remainder_unsend_fun(){
	$data_id = isset( $_POST['data_id'] ) ? $_POST['data_id'] : '';
	if( $data_id ){
		update_post_meta( $data_id, 'quote_mail_remainder', 'no' );
		delete_post_meta($data_id, 'quote_mail_remainder_date');
	}
	wp_send_json( array( 'type' => 'success', 'data_id' => $data_id) );
}


//add_action( 'wp_ajax_nopriv_wc_init_fun', 'wc_init_fun_fun' );
add_action( 'init', 'wc_init_fun_fun' );
function wc_init_fun_fun(){
	$args = array(
		'post_type'  => 'quotes',
		'post_status' => 'publish',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => 'quote_mail_remainder_date',
				'value'   => date("Y-m-d"),
				'compare' => '=',
				'type' => 'DATETIME'
			),
			array(
				'key'     => 'quote_mail_remainder',
				'value'   => 'yes',
				'compare' => '=',
			),
		),
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) { 
		while ( $the_query->have_posts() ) { $the_query->the_post(); 
			$post_id = get_the_ID();
			$author_id = get_post_field('post_author', $post_id);
			$quote_mail_remainder_date = get_post_meta( $post_id, 'quote_mail_remainder_date', true );
			$quote_mail_remainder = get_post_meta( $post_id, 'quote_mail_remainder', true );
			$user_info = get_userdata($author_id);
			$user_email = $user_info->user_email;
			$remainder_html = wc_quotes_data_mail_remainder_fun($post_id);
			$pdf_url = wc_pdf_file_genrate_fun( $remainder_html, 1 );

			$subject = 'You got a new quote!';
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$sent = wp_mail( $user_email, $subject, $remainder_html, $headers, array($pdf_url) );
			update_post_meta( $post_id, 'quote_mail_remainder', 'no' );
			delete_post_meta( $post_id, 'quote_mail_remainder_date' );
			if( $sent ){ unlink($pdf_url); }
		} 
	} 
}

/* //--------// Add the custom columns to the Quotes post type   //---------// */
/* //--------------------------------// //-------------------------------//  */
add_filter( 'manage_quotes_posts_columns', 'set_custom_edit_quotes_columns' );
function set_custom_edit_quotes_columns($columns) {
	//unset( $columns['author'] );
	$columns['quotes_user'] = __( 'User Name', 'your_text_domain' );
	$columns['invoce_date'] = __( 'Quote date', 'your_text_domain' );
	$columns['due_date'] = __( 'Expiration Date', 'your_text_domain' );
	$columns['quotes_total'] = __( 'Total', 'your_text_domain' );
	$columns['quotes_id'] = __( 'Invoice Number', 'your_text_domain' );

	return $columns;
}

/* //--------// Add the data to the custom columns for the Quotes post type   //---------// */
/* //---------------------------------------// //---------------------------------------//  */
add_action( 'manage_quotes_posts_custom_column' , 'custom_quotes_column', 10, 2 );
function custom_quotes_column( $column, $post_id ) {

	switch ( $column ) {

		case 'quotes_user' :

		//$user_id  = get_current_user_id();
		$user_id = get_post_field( 'post_author', $post_id );
		$user = get_userdata($user_id);

		$username  = '';
		if( $user->first_name ){
			$username = $user->first_name;
		}
		if( $user->last_name ) {
			$username .= ' '. $user->last_name;
		}

		$username = trim($username);
		if( empty($username) ){
			$username = $user->display_name;
		}

		if ( $username ){
			echo $username;
		} else {
			_e( 'Unable to get author(s)', 'your_text_domain' );
		}
		break;

		case 'invoce_date' :
		$quotes_extra_data = get_post_meta( $post_id, 'quotes_extra_data', true );
		if( $quotes_extra_data ) {
			echo $quotes_extra_data['trip-start'];
		}
		break;

		case 'due_date' :
		$quotes_extra_data = get_post_meta( $post_id, 'quotes_extra_data', true );
		if( $quotes_extra_data ) {
			echo $quotes_extra_data['trip-end'];
		}
		break;

		case 'quotes_total' :
		$quotes_extra_data = get_post_meta( $post_id, 'quotes_extra_data', true );
		$invoice_data  = isset($quotes_extra_data['foot_total']) ? get_woocommerce_currency_symbol().''.$quotes_extra_data['foot_total'] : '';
		echo  $invoice_data;
		break;

		case 'quotes_id' :
		$quotes_extra_data = get_post_meta( $post_id, 'quotes_extra_data', true );
		if( $quotes_extra_data ) {
			echo $quotes_extra_data['invoice_number'];
		}
		break;

	}
}

/* //--------// Quotes Custom Post meta   //---------// */
/* //--------------------// //--------------------//  */
add_action( 'add_meta_boxes', 'wc_quotes_meta_box_add' );
function wc_quotes_meta_box_add() {
	add_meta_box( 'quotes-post-box', 'Quotes List Data', 'wc_quotes_post_func', 'quotes', 'normal', 'high' );
}

function wc_quotes_post_func($post){
	$quotes_extra_data = get_post_meta( $post->ID, 'quotes_extra_data', true );
	$quotes_type = get_post_meta( $post->ID, 'quotes_type', true );
	?>
	<style type="text/css">
		#customers {
			font-family: Arial, Helvetica, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		#customers td, #customers th {
			border: 1px solid #ddd;
			padding: 8px;
		}

		#customers th {
			padding-top: 12px;
			padding-bottom: 12px;
			color: #1d2327;
		}
		#customers tbody td img {
			width: 50px;
			height: auto;
		}

		#customers tbody tr td,
		#customers thead tr th {
			text-align: center;
		}
		#customers .wc-invo-prod-desc img{
			width: 58px;
			height: 50px;
			display: block;
			object-fit: contain;
			margin: 0 auto;
			border: 1px solid #e6e6e6;
			margin-bottom: 5px;
			padding: 5px;
			object-position: center;
		}
	</style>
	<table class="quotes-list" id="customers">
		<thead>	
			<tr>
				<th>Company</th>
				<th>Quotes Number</th>
				<th>Quotes Start Date</th>
				<th>Expiration Date</th>
				<th rowspan="4">Quotes List</th>
			</tr>
		</thead>
		<tbody>
			<tr data-id="<?php echo $post->ID; ?>">
				<td class="wc-image">
					<?php
					$image_id = get_post_meta( $post->ID, 'logo_id', true );
					$image_url = '';
					if( $image_id !== 0 ){
						$image_attributes = wp_get_attachment_image_src($image_id, 'full');
						$image_url = $image_attributes[0];
						echo '<img src="'.$image_url.'" />';
					}
					?>
				</td>
				<td class="wc-invgoice-num"><?php echo isset($quotes_extra_data['invoice_number']) ? $quotes_extra_data['invoice_number']: '-'; ?></td>
				<td class="wc-invo-start-date"><?php echo isset($quotes_extra_data['trip-start']) ? $quotes_extra_data['trip-start']: '-'; ?></td>
				<td class="wc-invo-end-date"><?php echo isset($quotes_extra_data['trip-end']) ? $quotes_extra_data['trip-end']: '-'; ?></td>

				<td colspan="4">
					<table style="width: 100%;">
						<thead >
							<?php
							if($quotes_type == 'custom'){ ?>
								<th>Image</th>
								<th>Description</th>
							<?php } else { ?>
								<th>Item</th>
								<th>Color</th>
								<th>Descriptions</th>
								<th>Unit Price</th>
								<th>Quantity</th>
								<th>Amount</th>
							<?php } ?>
						</thead>
						<tbody>
							<?php
							if($quotes_type == 'custom'){
								$custom_invoice_data  = isset($quotes_extra_data['custom_invoice_data']) ? $quotes_extra_data['custom_invoice_data'] : '';
								if( $custom_invoice_data ){
									foreach ($custom_invoice_data as $value) {
										$product_file_id = isset($value['product_file_id']) ? $value['product_file_id'] : '';
										$convert_ids = isset($value['convert_ids']) ? $value['convert_ids'] : '';
										//$attch_ids = get_post_meta( $product_file_id, 'p_attach_ids', true );
										$wc_hidden_pro_image = isset($value['wc-hidden-pro-image']) ? $value['wc-hidden-pro-image'] : '';
										$img_src_string = ( $convert_ids ) ? 'https://drive.google.com/uc?id='.$convert_ids : $wc_hidden_pro_image;
										$description = isset($value['description']) ? $value['description'] : '';
										?>
										<tr>
											<td class="wc-invo-prod-title"><img src="<?php echo $img_src_string; ?>" width="100" height="100"></td>
											<td class="wc-invo-prod-title"><?php echo $description; ?></td>
										</tr>
										<?php
									}
								}
							} else {
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
										$variations = array();
										if( is_object($product) ){
											$variations = $product->get_available_variations(); 
											$product_title = $product->get_title();
										}
										?>
										<tr>
											<td class="wc-invo-prod-title"><?php echo $product_title; ?></td>
											<td class="wc-invo-prod-title">
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
											<td class="wc-invo-prod-desc"><?php echo '<img src="'.$wc_hidden_var_image.'">'.''.$description?></td>
											<td class="wc-invo-prod-price"><?php echo get_woocommerce_currency_symbol().''.$unit_data;?></td>
											<td class="wc-invo-quantity"><?php echo $qty_data;?></td>
											<td class="wc-invo-total"><?php echo get_woocommerce_currency_symbol().''.$total_data;?></td>
										</tr>
										<?php
									}
								}
							}
							?>
							<?php
							if($quotes_type == 'custom'){
								?>
								<tr>
									<td>Total</td>
									<td class="wc-invo-total-amnt"><?php echo ( $quotes_extra_data['foot_total'] ) ? get_woocommerce_currency_symbol().''.$quotes_extra_data['foot_total'] : '';?></td>
								</tr>
								<?php
							} else {
								?>
								<tr>
									<td colspan="4"></td>
									<td>Total</td>
									<td class="wc-invo-total-amnt"><?php echo ( $quotes_extra_data['foot_total'] ) ? get_woocommerce_currency_symbol().''.$quotes_extra_data['foot_total'] : '';?></td>
								</tr>
								<?php
							} ?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<?php    
}
