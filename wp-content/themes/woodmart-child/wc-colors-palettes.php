<?php
/*==============================================
******| Create Sellers Custom post Type |******
==============================================*/

add_action( 'init', 'create_color_palettes_post_type', 0 );

function create_color_palettes_post_type() {

	/*==============================================
	****| Set UI labels for Sellers Post Type |****
	==============================================*/

	$labels = array(
		'name'                => _x( 'Colors', 'Post Type Normal Name', 'cxc-codexcoach' ),
		'singular_name'       => _x( 'Color', 'Post Type Singular Name', 'cxc-codexcoach' ),
		'menu_name'           => __( 'Colors', 'cxc-codexcoach' ),
		'parent_item_colon'   => __( 'Parent Color', 'cxc-codexcoach' ),
		'all_items'           => __( 'All Colors', 'cxc-codexcoach' ),
		'view_item'           => __( 'View Color', 'cxc-codexcoach' ),
		'add_new_item'        => __( 'Add New Color', 'cxc-codexcoach' ),
		'add_new'             => __( 'Add New', 'cxc-codexcoach' ),
		'edit_item'           => __( 'Edit Color', 'cxc-codexcoach' ),
		'update_item'         => __( 'Update Color', 'cxc-codexcoach' ),
		'search_items'        => __( 'Search Color', 'cxc-codexcoach' ),
		'not_found'           => __( 'Not Found', 'cxc-codexcoach' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'cxc-codexcoach' ),
	);

	/*==============================================
	****| Other arguments for Colors Post Typ- |****
	==============================================*/

	$args = array(
		'label'               => __( 'colors', 'cxc-codexcoach' ),
		'description'         => __( 'Color news and reviews', 'cxc-codexcoach' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ), //'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',
		'taxonomies'          => array( 'genres' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest' => true,
	);

	/*==============================================
	****| Registering your Colors Post Type  |****
	==============================================*/

	register_post_type( 'colors', $args );

}

/*===================================================
******| Add Custom Taxonomy in colors |******
===================================================*/

function wc_create_colors_taxonomy() {

	register_taxonomy(
		'color-category',
		'colors',
		array(
			'label' => __( 'Colors Categories' ),
			'rewrite' => array( 'slug' => 'color' ),
			//'rewrite' => array( 'slug' => 'colors' ),
			'hierarchical' => true,
		)
	);
}
add_action( 'init', 'wc_create_colors_taxonomy' );

/*===================================================
******| Add Custom Meta Box Colors post Type |******
===================================================*/

add_action( 'admin_init', 'cxc_color_palettes_rapater_meta_boxes', 2 );

function cxc_color_palettes_rapater_meta_boxes() {
	add_meta_box( 'cxc-color-palettes-repeater-data', 'Color Palettes', 'cxc_color_palettes_repeatable_meta_box_callback', 'colors', 'normal', 'default');
	add_meta_box( 'cxc-color-palettes-popup-data', 'Color Palettes Url', 'cxc_color_palettes_popup_url_meta_box_callback', 'colors', 'normal', 'default');
}

function cxc_color_palettes_repeatable_meta_box_callback( $post ) {
	$custom_repeater_item = get_post_meta( $post->ID, 'custom_repeater_item', true );
	wp_nonce_field( 'repeterBox', 'formType' );
	wp_enqueue_style( 'wp-color-picker');
	wp_enqueue_script( 'wp-color-picker');
	?>
	<script type="text/javascript">		
		jQuery(document).ready(function($){
			jQuery(".color-field").wpColorPicker();
			jQuery(document).on('click', '.wc-remove-item', function() {
				jQuery(this).parents('tr.wc-sub-row').remove();
			}); 				
			jQuery(document).on('click', '.wc-add-item', function() {
				var row_no = jQuery('.wc-item-table tr.wc-sub-row').length;    
				var p_this = jQuery(this);
				row_no = parseFloat(row_no);
				var row_html = jQuery('.wc-item-table .wc-hide-tr').html().replace(/rand_no/g, row_no).replace(/hide_custom_repeater_item/g, 'custom_repeater_item');
				jQuery('.wc-item-table tbody').append('<tr class="wc-sub-row">' + row_html + '</div>');   
				jQuery(".wc-sub-row:last-child .wc-color-field").wpColorPicker();
			});
		});
	</script>
	<table class="wc-item-table" width="100%">
		<tbody>
			<?php 
			if( $custom_repeater_item &&  is_array( $custom_repeater_item ) ){
				foreach( $custom_repeater_item as $item_key => $item_value ){
					?>
					<tr class="wc-sub-row">				
						<td>
							<input class="color-field" type="text" name="custom_repeater_item[<?php echo $item_key; ?>][color_palettes]" value="<?php echo (isset($item_value['color_palettes'])) ? $item_value['color_palettes'] : ''; ?>"/>
						</td>
						<td>
							<input type="text"  name="custom_repeater_item[<?php echo $item_key; ?>][color_pms]" value="<?php echo (isset($item_value['color_pms'])) ? $item_value['color_pms'] : ''; ?>" placeholder="Pantone Code">
						</td>
						<td>
							<input type="text"  name="custom_repeater_item[<?php echo $item_key; ?>][palettes_url]" value="<?php echo (isset($item_value['palettes_url'])) ? $item_value['palettes_url'] : ''; ?>">
						</td>
						<td>
							<input type="checkbox" id="dark_color_palettes" class="dark_color_palettes" <?php echo (isset($item_value['dark_color_palettes'])) ? 'checked' : ''; ?> name="custom_repeater_item[<?php echo $item_key; ?>][dark_color_palettes]">
							<label for="dark_color_palettes">Dark Color</label>
						</td>
						<td>
							<button class="wc-remove-item button" type="button">Remove</button>
						</td>
					</tr>
					<?php
				}
			}
			?>			
			<tr class="wc-hide-tr" style="display: none;">				
				<td>
					<input type="text" class="wc-color-field" name="hide_custom_repeater_item[rand_no][color_palettes]"/>
				</td>
				<td>
					<input type="text"  name="hide_custom_repeater_item[rand_no][color_pms]" placeholder="Pantone Code">
				</td>
				<td>
					<input type="text"  name="hide_custom_repeater_item[rand_no][palettes_url]" >
				</td>
				<td>
					<input type="checkbox" id="dark_color_palettes" name="hide_custom_repeater_item[rand_no][dark_color_palettes]">
					<label for="dark_color_palettes">Dark Color</label><br>
				</td>
				<td>
					<button class="wc-remove-item button" type="button">Remove</button>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4"><button class="wc-add-item button" type="button">Add another</button></td>
			</tr>
		</tfoot>
	</table>	
	<?php
}

function cxc_color_palettes_popup_url_meta_box_callback( $post ) {
	wp_nonce_field( 'colorBox', 'formType' );
	$color_palettes_url = get_post_meta( $post->ID, 'color_palettes_url', true );
	?>
	<style type="text/css">
		table.wc-color_url tr td:first-child {
			width: 8%;
		}
		table.wc-color_url input.color_palettes_url {
			width: 100%;
		}
	</style>	
	<table cellspacing="10" cellpadding="10" width=100% class="wc-color_url">
		<tr>
			<td><strong>Color Url</strong></td>
			<td>
				<?php 
				if( !empty( $color_palettes_url ) ) {
					?>
					<input type="text" name="color_palettes_url" class="color_palettes_url" value="<?php echo $color_palettes_url;?>">
					<?php
				} else {
					?>
					<input type="text" name="color_palettes_url" class="color_palettes_url" value="">
					<?php
				}
				?>
			</td>
		</tr>	
	</table>
	<?php
}

add_action( 'save_post', 'cxc_color_palettes_repeatable_meta_box_save' );
function cxc_color_palettes_repeatable_meta_box_save( $post_id ) {

	if ( !isset( $_POST['formType'] ) && !wp_verify_nonce( $_POST['formType'], 'repeterBox' ) ){
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}

	if ( !current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	if ( isset( $_POST['custom_repeater_item'] ) ) {
		update_post_meta( $post_id, 'custom_repeater_item', $_POST['custom_repeater_item'] );
	} else {
		update_post_meta( $post_id, 'custom_repeater_item', '' );
	}	
}

add_action( 'save_post', 'cxc_color_palettes_url_meta_box_save' );
function cxc_color_palettes_url_meta_box_save( $post_id ) {

	if ( !isset( $_POST['formType'] ) && !wp_verify_nonce( $_POST['formType'], 'colorBox' ) ){
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}

	if ( !current_user_can( 'edit_post', $post_id ) ){
		return;
	}

	if ( isset( $_POST['color_palettes_url'] ) ) {
		update_post_meta( $post_id, 'color_palettes_url', $_POST['color_palettes_url'] );
	} else {
		update_post_meta( $post_id, 'color_palettes_url', '' );
	}	
}


//add_shortcode( 'wc_color_pattel', 'wc_color_pattel_call_back' );
function wc_color_pattel_call_back(){
	$html = '';
	ob_start();

	$args = array(
		'post_type'   => 'colors',
		'post_status' => 'publish',
		'posts_per_page'=> -1,
		'order'     => 'ASC',
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) { 
		while ( $the_query->have_posts() ) { $the_query->the_post(); 
			$post_id = get_the_ID();
			$color_pattel = get_post_meta( $post_id, 'custom_repeater_item', true );
			?>
			<h2><?php the_title(); ?></h2>
			<?php
			if( $color_pattel ){
				foreach( $color_pattel as $color ){
					$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : '';
					echo '<p style="background-color: '.$color_name.';">'.$color_name.'</p>';
				}
			}
		} 
		wp_reset_postdata();
	} 

	$html .= ob_get_clean();
	return $html;
}


add_action( 'wp_ajax_favourite_colors_list', 'favourite_colors_list' );
add_action( 'wp_ajax_nopriv_favourite_colors_list', 'favourite_colors_list' );

function favourite_colors_list(){
	
	$post_id = '';
	$update = false;
	$data = array();
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;

	$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '' ;
	$delete_id = isset( $_POST['delete_id'] ) ? $_POST['delete_id'] : '' ;

	$user_colors = get_user_meta( $user_id, 'favourite_colors_ids', true ); 
	$user_colors = explode(',', $user_colors);

	if( $post_id ){

		if( is_array( $user_colors ) && !in_array( $post_id, $user_colors ) ){
			$user_colors[] = $post_id;
		}

	}else{

		if( $user_colors ){
			foreach( $user_colors as $color_key => $color_value ) {
				if( $delete_id == $color_value ){
					unset($user_colors[$color_key]);
				}
			}
		}
	}

	$user_colors = implode(",",$user_colors);
	$update = update_user_meta( $user_id, 'favourite_colors_ids', $user_colors );

	if( $update ){
		$update = true;
		$favourite_colors_ids = get_user_meta( $user_id, 'favourite_colors_ids', true);
		$favourite_colors_ids_html = '';
		ob_start();
		if( $favourite_colors_ids ){
			$favourite_colors_ids = explode(',',$favourite_colors_ids);
			if( $favourite_colors_ids ){
				foreach( $favourite_colors_ids as $favourite_colors_id ){ 
					$color_pattel = get_post_meta( $favourite_colors_id, 'custom_repeater_item', true ); 
					if( $color_pattel ){ ?>
						<div class="palette">
							<a href="<?php echo get_the_permalink( $favourite_colors_id  ); ?>"><span class="fav-pallete-name"><?php echo get_the_title( $favourite_colors_id ); ?></span></a>
							<ul class="palette_colors">
								<?php foreach( $color_pattel as $color ){ 
									$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : ''; 
									$color_pms = isset( $color['color_pms'] ) ? $color['color_pms'] : ''; 
									$palettes_url = isset( $color['palettes_url'] ) ? $color['palettes_url'] : '';
									$dark_color_class = ( $color['dark_color_palettes'] ) ? 'has-dark-color' : '';
									$target_blank = ( $palettes_url ) ? 'target=_blank' : '';
									$href = 'javascript:;';
									if( !empty( $palettes_url  ) ){
										$href = $palettes_url; 
									}
									?>
									<li class="is-light <?php echo $dark_color_class; ?>" style="background: <?php echo $color_name; ?>"><a href="<?php echo $palettes_url; ?>" target="_blank"><span class="color-name"><?php echo $color_pms; ?></span></a></li>
								<?php } ?>
							</ul>
							<div class="palette_text">49K saves</div>
						</div>
						<?php 
					} 
				}
			}
		} else { ?>
			<div class="palette">
				<p>No color palettes found</p>
			</div>
		<?php }
		$favourite_colors_ids_html .= ob_get_clean();
	}

	$data = array( 'success' => $update, 'favourite_colors_ids_html' => $favourite_colors_ids_html );
	wp_send_json( $data );
} 