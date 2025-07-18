<?php

use Elementor\Group_Control_Image_Size;
use XTS\Modules\Layouts\Main as Builder;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_output_sku_in_cart' ) ) {
	/**
	 * Show SKU in cart.
	 *
	 * @param array   $cart_item Product item.
	 * @param integer $cart_item_key Cart item key.
	 * @return void
	 */
	function woodmart_output_sku_in_cart( $cart_item, $cart_item_key ) {
		if ( ! woodmart_get_opt( 'show_sku_in_cart' ) ) {
			return;
		}

		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( ! $_product || ! $_product->get_sku() ) {
			return;
		}

		?>
		<div class="wd-product-detail wd-product-sku">
			<span class="wd-label">
				<?php echo esc_html__( 'SKU:', 'woodmart' ); ?>
			</span>
			<span>
				<?php echo esc_html( $_product->get_sku() ); ?>
			</span>
		</div>
		<?php
	}

	add_action( 'woocommerce_after_cart_item_name', 'woodmart_output_sku_in_cart', 5, 2 );
}

if ( ! function_exists( 'woodmart_woo_set_gutenberg_editor_for_product' ) ) {
	/**
	 * Set Gutenberg editor for product.
	 *
	 * @param integer $post_id Post ID.
	 * @return void
	 */
	function woodmart_woo_set_gutenberg_editor_for_product( $post_id ) {
		if ( 'product' !== get_post_type( $post_id ) || ! woodmart_get_opt( 'enable_gutenberg_for_products' ) ) {
			return;
		}

		if ( 'no' === get_post_meta( $post_id, '_wd_gutenberg_product_mode', true ) ) {
			update_post_meta( $post_id, '_wd_gutenberg_product_mode', 'yes' );
		}

		if ( woodmart_is_elementor_installed() && Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {
			Elementor\Plugin::$instance->documents->get( $post_id )->set_is_built_with_elementor( false );
		}

		add_filter( 'filter_block_editor_meta_boxes', '__return_empty_array', 100 );

		require_once ABSPATH . 'wp-admin/edit-form-blocks.php';
		require_once ABSPATH . 'wp-admin/admin-footer.php';

		die();
	}

	add_action( 'post_action_wd_gutenberg', 'woodmart_woo_set_gutenberg_editor_for_product' );
}

if ( ! function_exists( 'woodmart_woo_add_gutenberg_button_action' ) ) {
	/**
	 * Add gutenberg buttons action.
	 *
	 * @param object $post Current post.
	 * @return void
	 */
	function woodmart_woo_add_gutenberg_button_action( $post ) {
		if ( 'product' !== $post->post_type || ! woodmart_get_opt( 'enable_gutenberg_for_products' ) ) {
			return;
		}

		$url = add_query_arg(
			array(
				'post'   => $post->ID,
				'action' => 'wd_gutenberg',
			),
			admin_url( 'post.php' )
		);

		$back_url = add_query_arg(
			array(
				'post'                      => $post->ID,
				'action'                    => 'edit',
				'wd_gutenberg_product_mode' => 'no',
			),
			admin_url( 'post.php' )
		);

		$mode = get_post_meta( $post->ID, '_wd_gutenberg_product_mode', true );

		if ( ! $mode && has_blocks( $post->post_content ) ) {
			$mode = 'yes';
		}

		if ( 'yes' === $mode ) {
			global $_wp_post_type_features;

			if ( isset( $_wp_post_type_features['product'] ) && isset( $_wp_post_type_features['product']['editor'] ) ) {
				unset( $_wp_post_type_features['product']['editor'] );
			}
		}

		?>
		<div class="xts-switch-editor-mode">
			<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-hero xts-gutenberg-btn xts-gutenberg-editor<?php echo 'yes' === $mode ? ' xts-hidden' : ''; ?>">
				<span class="dashicons dashicons-wordpress-alt"></span>
				<?php esc_html_e( 'Edit with Gutenberg', 'woodmart' ); ?>
			</a>

			<?php if ( 'yes' === $mode ) : ?>
				<a href="<?php echo esc_url( $back_url ); ?>" class="button button-hero xts-back-classic-editor xts-gutenberg-btn">
					<span class="dashicons dashicons-arrow-left-alt"></span>
					<?php esc_html_e( 'Back to Classic Editor', 'woodmart' ); ?>
				</a>
			<?php endif; ?>

			<input type="hidden" name="wd_gutenberg_product_mode" value="<?php echo esc_attr( $mode ); ?>">
		</div>

		<?php if ( 'yes' === $mode ) : ?>
			<div class="xts-guten-wrapp">
				<a href="<?php echo esc_url( $url ); ?>" class="xts-fill"></a>
				<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-hero xts-gutenberg-btn">
					<span class="dashicons dashicons-wordpress-alt"></span>
					<?php esc_html_e( 'Edit with Gutenberg', 'woodmart' ); ?>
				</a>
			</div>
			<?php
		endif;
	}

	add_action( 'edit_form_after_title', 'woodmart_woo_add_gutenberg_button_action' );
}

if ( ! function_exists( 'woodmart_save_product_builder_mode' ) ) {
	/**
	 * Save product builder mode.
	 *
	 * @param integer $post_id Post ID.
	 * @return void
	 */
	function woodmart_save_product_builder_mode( $post_id ) {
		if ( empty( $_REQUEST['wd_gutenberg_product_mode'] ) ) { //phpcs:ignore
			return;
		}

		if ( ! $post_id ) {
			$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; //phpcs:ignore
		}

		if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, '_wd_gutenberg_product_mode', esc_attr( $_REQUEST['wd_gutenberg_product_mode'] ) ); //phpcs:ignore
	}

	add_action( 'save_post_product', 'woodmart_save_product_builder_mode' );
	add_action( 'admin_init', 'woodmart_save_product_builder_mode' );
}

if ( ! function_exists( 'woodmart_woo_set_breadcrumbs_settings' ) ) {
	/**
	 * Set bread.
	 *
	 * @param array $settings Can edit.
	 * @return array
	 */
	function woodmart_woo_set_breadcrumbs_settings( $settings ) {
		if ( ! empty( $settings['wrap_before'] ) ) {
			$settings['wrap_before'] = str_replace( 'class="woocommerce-breadcrumb', 'class="wd-breadcrumbs woocommerce-breadcrumb', $settings['wrap_before'] );
		}

		return $settings;
	}

	add_filter( 'woocommerce_breadcrumb_defaults', 'woodmart_woo_set_breadcrumbs_settings' );
}

if ( ! function_exists( 'woodmart_woo_set_default_dummy_content' ) ) {
	/**
	 * Set default Woocommerce shortcode for cart and checkout pages content.
	 *
	 * @param array $pages Woocommerce pages data.
	 * @return array
	 */
	function woodmart_woo_set_default_dummy_content( $pages ) {
		if ( apply_filters( 'woodmart_woo_set_default_dummy_content', true ) ) {
			if ( ! empty( $pages['cart'] ) ) {
				$pages['cart']['content'] = '<!-- wp:shortcode -->[woocommerce_cart]<!-- /wp:shortcode -->';
			}
			if ( ! empty( $pages['checkout'] ) ) {
				$pages['checkout']['content'] = '<!-- wp:shortcode -->[woocommerce_checkout]<!-- /wp:shortcode -->';
			}
		}

		return $pages;
	}

	add_filter( 'woocommerce_create_pages', 'woodmart_woo_set_default_dummy_content' );
}

if ( ! function_exists( 'woodmart_output_sku_in_thank_you_page' ) ) {
	/**
	 * Show SKU on thank you page.
	 *
	 * @param int    $item_id Item ID.
	 * @param object $item Product item.
	 * @param object $order Order.
	 */
	function woodmart_output_sku_in_thank_you_page( $item_id, $item, $order ) {
		$is_order_detail_page  = is_wc_endpoint_url( 'view-order' ) || is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' );
		$show_on_order_details = woodmart_get_opt( 'show_sku_in_thank_you_page' ) && $is_order_detail_page;
		$show_on_email         = woodmart_get_opt( 'show_sku_in_email_order' ) && ! $is_order_detail_page;

		if ( ! $show_on_order_details && ! $show_on_email ) {
			return;
		}

		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
		$product    = wc_get_product( $product_id );
		$sku        = $product->get_sku();

		if ( ! $sku ) {
			return;
		}

		?>
		<div class="wd-product-detail wd-product-sku">
			<span class="wd-label">
				<?php echo esc_html__( 'SKU:', 'woodmart' ); ?>
			</span>
			<span>
				<?php echo esc_html( $sku ); ?>
			</span>
		</div>
		<?php
	}

	add_action( 'woocommerce_order_item_meta_start', 'woodmart_output_sku_in_thank_you_page', 10, 3 );
}

if ( ! function_exists( 'woodmart_clear_shop_per_page_cookie' ) ) {
	/**
	 * Clear shop per page cookie on settings save.
	 */
	function woodmart_clear_shop_per_page_cookie() {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		if ( isset( $_COOKIE['shop_per_page'] ) ) {
			unset( $_COOKIE['shop_per_page'] );
			setcookie( 'shop_per_page', '', -1, '/' );
		}

		if ( isset( $_COOKIE['shop_per_row'] ) ) {
			unset( $_COOKIE['shop_per_row'] );
			setcookie( 'shop_per_row', '', -1, '/' );
		}
	}

	add_action( 'xts_theme_settings_save', 'woodmart_clear_shop_per_page_cookie', 10 );
}

if ( ! function_exists( 'woodmart_update_cart_fragments_fix' ) ) {
	/**
	 * Get active filter element width wrapper.
	 */
	function woodmart_update_cart_fragments_fix() {
		if ( ! apply_filters( 'woodmart_update_fragments_fix', true ) || ! function_exists( 'WC' ) ) {
			return;
		}

		wp_enqueue_script( 'wd-update-cart-fragments-fix', WOODMART_SCRIPTS . '/scripts/wc/updateCartFragmentsFix.js', array(), woodmart_get_theme_info( 'Version' ), true );

		wp_localize_script( 'wd-update-cart-fragments-fix', 'wd_cart_fragments_params', array(
			'ajax_url'        => WC()->ajax_url(),
			'wc_ajax_url'     => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'cart_hash_key'   => apply_filters( 'woocommerce_cart_hash_key', 'wc_cart_hash_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
			'fragment_name'   => apply_filters( 'woocommerce_cart_fragment_name', 'wc_fragments_' . md5( get_current_blog_id() . '_' . get_site_url( get_current_blog_id(), '/' ) . get_template() ) ),
			'request_timeout' => 5000,
		) );
	}

	add_action( 'wp_enqueue_scripts', 'woodmart_update_cart_fragments_fix', 1 );
}

if ( ! function_exists( 'woodmart_get_active_filters' ) ) {
	/**
	 * Get active filter element width wrapper.
	 */
	function woodmart_get_active_filters() {
		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$min_price          = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0; // phpcs:ignore.
		$max_price          = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0; // phpcs:ignore.
		$rating_filter      = isset( $_GET['rating_filter'] ) ? array_filter( array_map( 'absint', explode( ',', wp_unslash( $_GET['rating_filter'] ) ) ) ) : array(); // phpcs:ignore.

		if ( 0 === count( $_chosen_attributes ) && empty( $min_price ) && empty( $max_price ) && empty( $rating_filter ) ) {
			return;
		}

		woodmart_enqueue_inline_style( 'woo-shop-el-active-filters' );

		?>
		<div class="wd-active-filters<?php echo esc_attr( woodmart_get_old_classes( ' woodmart-active-filters' ) ); ?>">
			<?php woodmart_clear_filters_btn(); ?>

			<?php
			the_widget(
				'WC_Widget_Layered_Nav_Filters',
				array(
					'title' => '',
				),
				array()
			);
			?>
		</div>
		<?php
	}

	add_action( 'woodmart_shop_filters_area', 'woodmart_get_active_filters', 20 );
}

if ( ! function_exists( 'woodmart_add_brands_to_structured_data' ) ) {
	/**
	 * Add structured data to product page.
	 *
	 * @param  array $markup Markup.
	 * @return array
	 */
	function woodmart_add_brands_to_structured_data( $markup ) {
		global $post;

		if ( isset( $markup['brand'] ) ) {
			return $markup;
		}

		$brands = get_the_terms( $post->ID, woodmart_get_opt( 'brands_attribute' ) );

		if ( ! empty( $brands ) && is_array( $brands ) ) {
			// Can only return one brand, so pick the first.
			$markup['brand'] = array(
				'@type' => 'Brand',
				'name'  => $brands[0]->name,
			);
		}

		return $markup;
	}

	add_filter( 'woocommerce_structured_data_product', 'woodmart_add_brands_to_structured_data', 20 );
}

if ( ! function_exists( 'woodmart_wc_ajax_variation_threshold' ) ) {
	/**
	 * AJAX variation threshold.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $limit Limit.
	 *
	 * @return mixed
	 */
	function woodmart_wc_ajax_variation_threshold( $limit ) {
		if ( woodmart_get_opt( 'ajax_variation_threshold' ) && 30 !== (int) woodmart_get_opt( 'ajax_variation_threshold' ) ) {
			$limit = woodmart_get_opt( 'ajax_variation_threshold' );
		}

		return $limit;
	}

	add_filter( 'woocommerce_ajax_variation_threshold', 'woodmart_wc_ajax_variation_threshold' );
}

if ( ! function_exists( 'woodmart_wc_products_shortcode_compatibility' ) ) {
	/**
	 * Woocommerce products shortcode compatibility.
	 *
	 * @since 1.0.0
	 */
	function woodmart_wc_products_shortcode_compatibility() {
		add_action(
			'woocommerce_shortcode_products_query',
			function( $query ) {
				if ( isset( $_GET['per_page'] ) ) { // phpcs:ignore
					$query['posts_per_page'] =  wc_clean( wp_unslash( $_GET['per_page'] ) ); // phpcs:ignore
				}

				return $query;
			}
		);

		add_action(
			'woocommerce_shortcode_before_products_loop',
			function( $attributes ) {
				woodmart_products_per_page_select( true );
				woodmart_products_view_select( true );
				woocommerce_catalog_ordering();

				woodmart_set_loop_prop( 'products_columns', $attributes['columns'] ); // phpcs:ignore

				if ( isset( $_GET['shop_view'] ) ) { // phpcs:ignore
					woodmart_set_loop_prop( 'products_view', wc_clean( wp_unslash( $_GET['shop_view'] ) ) ); // phpcs:ignore
				}

				if ( isset( $_GET['per_row'] ) ) { // phpcs:ignore
					woodmart_set_loop_prop( 'products_columns', wc_clean( wp_unslash( $_GET['per_row'] ) ) ); // phpcs:ignore
				}

				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
				remove_action( 'woocommerce_before_shop_loop', 'woodmart_products_per_page_select', 25 );
				remove_action( 'woocommerce_before_shop_loop', 'woodmart_products_view_select', 27 );
			}
		);
	}

	add_action( 'wp', 'woodmart_wc_products_shortcode_compatibility', 10 );
}


if ( ! function_exists( 'woodmart_product_price_slider_script' ) ) {
	/**
	 * Enqueue script.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_name Template_name.
	 */
	function woodmart_product_price_slider_script( $template_name ) {
		if ( 'content-widget-price-filter.php' === $template_name ) {
			woodmart_enqueue_js_script( 'woocommerce-price-slider' );
		}
	}

	add_action( 'woocommerce_before_template_part', 'woodmart_product_price_slider_script', 10 );
}

if ( ! function_exists( 'woodmart_product_categories_widget_script' ) ) {
	/**
	 * Enqueue script.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Data.
	 *
	 * @return string
	 */
	function woodmart_product_categories_widget_script( $data ) {
		if ( woodmart_get_opt( 'categories_toggle' ) ) {
			woodmart_enqueue_js_script( 'categories-accordion' );
		}

		woodmart_enqueue_js_script( 'categories-dropdown' );

		return $data;
	}

	add_action( 'woocommerce_product_categories_widget_args', 'woodmart_product_categories_widget_script', 10 );
	add_action( 'woocommerce_product_categories_widget_dropdown_args', 'woodmart_product_categories_widget_script', 10 );
}

if ( ! function_exists( 'woodmart_woo_widgets_select2' ) ) {
	/**
	 * Enqueue style for woo widgets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Data.
	 *
	 * @return string
	 */
	function woodmart_woo_widgets_select2( $data ) {
		woodmart_enqueue_inline_style( 'select2' );

		return $data;
	}

	add_action( 'woocommerce_product_categories_widget_dropdown_args', 'woodmart_woo_widgets_select2', 10 );
	add_action( 'woocommerce_layered_nav_any_label', 'woodmart_woo_widgets_select2', 10 );
}

if ( ! function_exists( 'woodmart_single_product_add_to_cart_scripts' ) ) {
	/**
	 * Enqueue single product scripts.
	 *
	 * @since 1.0.0
	 */
	function woodmart_single_product_add_to_cart_scripts() {
		if ( woodmart_get_opt( 'single_ajax_add_to_cart' ) ) {
			woodmart_enqueue_js_script( 'add-to-cart-all-types' );
		}

		if ( 'nothing' !== woodmart_get_opt( 'add_to_cart_action' ) ) {
			woodmart_enqueue_js_script( 'action-after-add-to-cart' );
		}

		if ( 'popup' === woodmart_get_opt( 'add_to_cart_action' ) ) {
			woodmart_enqueue_js_library( 'magnific' );
			woodmart_enqueue_inline_style( 'add-to-cart-popup' );
			woodmart_enqueue_inline_style( 'mfp-popup' );
		}
	}

	add_action( 'woocommerce_before_add_to_cart_form', 'woodmart_single_product_add_to_cart_scripts' );
}

if ( ! function_exists( 'woodmart_product_loop_add_to_cart_scripts' ) ) {
	/**
	 * Enqueue product loop.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data.
	 *
	 * @return mixed
	 */
	function woodmart_product_loop_add_to_cart_scripts( $data ) {
		if ( 'nothing' !== woodmart_get_opt( 'add_to_cart_action' ) ) {
			woodmart_enqueue_js_script( 'action-after-add-to-cart' );
		}

		if ( 'popup' === woodmart_get_opt( 'add_to_cart_action' ) ) {
			woodmart_enqueue_js_library( 'magnific' );
			woodmart_enqueue_inline_style( 'add-to-cart-popup' );
			woodmart_enqueue_inline_style( 'mfp-popup' );
		}

		return $data;
	}

	add_action( 'woocommerce_loop_add_to_cart_link', 'woodmart_product_loop_add_to_cart_scripts' );
}

if ( ! function_exists( 'woodmart_get_previous_product' ) ) {
	/**
	 * Retrieves the previous product.
	 *
	 * @since 2.4.3
	 *
	 * @param bool         $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
	 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
	 * @param string       $taxonomy Optional. Taxonomy, if $in_same_term is true. Default 'product_cat'.
	 *
	 * @return WC_Product|false Product object if successful. False if no valid product is found.
	 */
	function woodmart_get_previous_product( $in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat' ) {
		$product = new XTS\Modules\WC_Adjacent_Products( $in_same_term, $excluded_terms, $taxonomy, true );

		return $product->get_product();
	}
}

if ( ! function_exists( 'woodmart_get_next_product' ) ) {
	/**
	 * Retrieves the next product.
	 *
	 * @since 2.4.3
	 *
	 * @param bool         $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
	 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
	 * @param string       $taxonomy Optional. Taxonomy, if $in_same_term is true. Default 'product_cat'.
	 *
	 * @return WC_Product|false Product object if successful. False if no valid product is found.
	 */
	function woodmart_get_next_product( $in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat' ) {
		$product = new XTS\Modules\WC_Adjacent_Products( $in_same_term, $excluded_terms, $taxonomy );

		return $product->get_product();
	}
}

if ( ! function_exists( 'woodmart_get_products_tab_ajax' ) ) {
	function woodmart_get_products_tab_ajax() {
		if ( ! empty( $_POST['atts'] ) ) {
			$atts = woodmart_clean( $_POST['atts'] );
			if ( isset( $atts['elementor'] ) && $atts['elementor'] ) {
				$data = woodmart_elementor_products_tab_template( $atts );
			} else {
				if ( class_exists( 'WPBMap' ) ) {
					WPBMap::addAllMappedShortcodes();
				}

				$data = woodmart_shortcode_products_tab( $atts );
			}
			wp_send_json( $data );
		}
	}

	add_action( 'wp_ajax_woodmart_get_products_tab_shortcode', 'woodmart_get_products_tab_ajax' );
	add_action( 'wp_ajax_nopriv_woodmart_get_products_tab_shortcode', 'woodmart_get_products_tab_ajax' );
}


if ( ! function_exists( 'woodmart_get_shortcode_products_ajax' ) ) {
	function woodmart_get_shortcode_products_ajax() {
		if ( ! empty( $_POST['atts'] ) ) {
			$atts              = woodmart_clean( $_POST['atts'] );
			$paged             = ( empty( $_POST['paged'] ) ) ? 2 : sanitize_text_field( (int) $_POST['paged'] );
			$atts['ajax_page'] = $paged;

			if ( isset( $atts['elementor'] ) && $atts['elementor'] ) {
				$data = woodmart_elementor_products_template( $atts );
			} else {
				if ( class_exists( 'WPBMap' ) ) {
					WPBMap::addAllMappedShortcodes();
				}

				$data = woodmart_shortcode_products( $atts );
			}

			wp_send_json( $data );

			die();
		}
	}

	add_action( 'wp_ajax_woodmart_get_products_shortcode', 'woodmart_get_shortcode_products_ajax' );
	add_action( 'wp_ajax_nopriv_woodmart_get_products_shortcode', 'woodmart_get_shortcode_products_ajax' );
}

if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) {
	add_action(
		'init',
		function() {
			if ( function_exists( 'wc' ) ) {
				wc()->frontend_includes();
			}
		},
		5
	);
}

if ( ! function_exists( 'woodmart_widget_get_current_page_url' ) ) {
	function woodmart_widget_get_current_page_url( $link ) {
		if ( isset( $_GET['stock_status'] ) ) {
			$link = add_query_arg( 'stock_status', wc_clean( $_GET['stock_status'] ), $link );
		}

		return $link;
	}

	add_filter( 'woocommerce_widget_get_current_page_url', 'woodmart_widget_get_current_page_url' );
}

if ( ! function_exists( 'woodmart_get_filtered_price_new' ) ) {
	function woodmart_get_filtered_price_new() {
		global $wpdb;

		if ( ! is_shop() && ! is_product_taxonomy() ) {
			$sql = "
			SELECT min( FLOOR( min_price ) ) as min_price, MAX( CEILING( max_price ) ) as max_price
			FROM {$wpdb->wc_product_meta_lookup}";

			return $wpdb->get_row( $sql );
		}

		$args       = WC()->query->get_main_query()->query_vars;
		$tax_query  = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
		$meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();

		if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
			$tax_query[] = array(
				'taxonomy' => $args['taxonomy'],
				'terms'    => array( $args['term'] ),
				'field'    => 'slug',
			);
		}

		foreach ( $meta_query + $tax_query as $key => $query ) {
			if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
				unset( $meta_query[ $key ] );
			}
		}

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query  = new WP_Tax_Query( $tax_query );
		$search     = WC_Query::get_main_search_query_sql();

		$meta_query_sql   = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql    = $tax_query->get_sql( $wpdb->posts, 'ID' );
		$search_query_sql = $search ? ' AND ' . $search : '';

		$sql = "
			SELECT min( min_price ) as min_price, MAX( max_price ) as max_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT ID FROM {$wpdb->posts}
				" . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
				AND {$wpdb->posts}.post_status = 'publish'
				" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
			)';

		$sql = apply_filters( 'woocommerce_price_filter_sql', $sql, $meta_query_sql, $tax_query_sql );

		return $wpdb->get_row( $sql ); // WPCS: unprepared SQL ok.
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Main loop
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_woocommerce_main_loop' ) ) {

	add_action( 'woodmart_woocommerce_main_loop', 'woodmart_woocommerce_main_loop' );

	function woodmart_woocommerce_main_loop( $fragments = false ) {
		global $paged, $wp_query;

		$max_page = $wp_query->max_num_pages;

		if ( $fragments ) {
			ob_start();
		}

		if ( $fragments && isset( $_GET['loop'] ) ) {
			if ( ! empty( $_GET['atts'] ) ) {
				$atts = woodmart_clean( $_GET['atts'] );

				foreach ( $atts as $key => $value ) {
					woodmart_set_loop_prop( $key, $value );
				}
			}

			woodmart_set_loop_prop( 'woocommerce_loop', (int) sanitize_text_field( $_GET['loop'] ) );
		}

		$on_sale_is_empty = false;

		if ( isset( $_GET['stock_status'] ) && 'onsale' === $_GET['stock_status'] ) {
			$on_sale_is_empty = empty( array_unique( (array) apply_filters( 'loop_shop_post_in', array() ) ) );
		}

		if ( woocommerce_product_loop() && ! $on_sale_is_empty ) : ?>

			<?php
			if ( ! $fragments ) {
				woocommerce_product_loop_start();}
			?>

			<?php if ( wc_get_loop_prop( 'total' ) || $fragments ) : ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>

					<?php
					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );
					?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>
			<?php endif; ?>


			<?php
			if ( ! $fragments ) {
				woocommerce_product_loop_end();}
			?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
			if ( ! $fragments ) {
				do_action( 'woocommerce_after_shop_loop' );
			}
			?>

		<?php else : ?>

			<?php
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
			?>

			<?php
		endif;

		if ( $fragments ) {
			$output = ob_get_clean();
		}

		ob_start();
		woocommerce_result_count();
		$result_count = ob_get_clean();

		if ( $fragments ) {
			wp_send_json(
				array(
					'items'       => $output,
					'status'      => ( $max_page > $paged ) ? 'have-posts' : 'no-more-posts',
					'nextPage'    => str_replace( '&#038;', '&', next_posts( $max_page, false ) ),
					'currentPage' => strtok( woodmart_get_current_url(), '?' ),
					'breadcrumbs' => woodmart_current_breadcrumbs( 'shop', true ),
					'resultCount' => $result_count,
				)
			);
		}
	}
}
/**
 * ------------------------------------------------------------------------------------------------
 * Change number of products displayed per page
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_shop_products_per_page' ) ) {
	function woodmart_shop_products_per_page() {
		$per_page = 12;
		$number   = apply_filters( 'woodmart_shop_per_page', woodmart_get_products_per_page() );
		if ( is_numeric( $number ) ) {
			$per_page = $number;
		}
		return $per_page;
	}

	add_filter( 'loop_shop_per_page', 'woodmart_shop_products_per_page', 20 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Set full width layouts for woocommerce pages on set up
 * ------------------------------------------------------------------------------------------------
 */

/**
 * ------------------------------------------------------------------------------------------------
 * Get base shop page link
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_shop_page_link' ) ) {
	function woodmart_shop_page_link( $keep_query = false, $taxonomy = '' ) {
		// Base Link decided by current page
		$link = '';

		if ( class_exists( 'Automattic\Jetpack\Constants' ) && Automattic\Jetpack\Constants::is_defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) || is_shop() ) {
			$link = get_permalink( wc_get_page_id( 'shop' ) );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} elseif ( get_queried_object() ) {
			$queried_object = get_queried_object();

			if ( property_exists( $queried_object, 'taxonomy' ) ) {
				$link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
			}
		}

		if ( $keep_query ) {

			// Min/Max
			if ( isset( $_GET['min_price'] ) ) {
				$link = add_query_arg( 'min_price', wc_clean( $_GET['min_price'] ), $link );
			}

			if ( isset( $_GET['max_price'] ) ) {
				$link = add_query_arg( 'max_price', wc_clean( $_GET['max_price'] ), $link );
			}

			// Orderby
			if ( isset( $_GET['orderby'] ) ) {
				$link = add_query_arg( 'orderby', wc_clean( $_GET['orderby'] ), $link );
			}

			if ( isset( $_GET['stock_status'] ) ) {
				$link = add_query_arg( 'stock_status', wc_clean( $_GET['stock_status'] ), $link );
			}

			if ( isset( $_GET['per_row'] ) ) {
				$link = add_query_arg( 'per_row', wc_clean( $_GET['per_row'] ), $link );
			}

			if ( isset( $_GET['per_page'] ) ) {
				$link = add_query_arg( 'per_page', wc_clean( $_GET['per_page'] ), $link );
			}

			if ( isset( $_GET['shop_view'] ) ) {
				$link = add_query_arg( 'shop_view', wc_clean( $_GET['shop_view'] ), $link );
			}

			if ( isset( $_GET['shortcode'] ) ) {
				$link = add_query_arg( 'shortcode', wc_clean( $_GET['shortcode'] ), $link );
			}

			/**
			 * Search Arg.
			 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
			 */
			if ( get_search_query() ) {
				$link = add_query_arg( 's', rawurlencode( wp_specialchars_decode( get_search_query() ) ), $link );
			}

			// Post Type Arg
			if ( isset( $_GET['post_type'] ) ) {
				$link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link );

				// Prevent post type and page id when pretty permalinks are disabled.
				if ( is_shop() ) {
					$link = remove_query_arg( 'page_id', $link );
				}
			}

			// Min Rating Arg
			if ( isset( $_GET['min_rating'] ) ) {
				$link = add_query_arg( 'min_rating', wc_clean( $_GET['min_rating'] ), $link );
			}

			// All current filters
			if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
				foreach ( $_chosen_attributes as $name => $data ) {
					if ( $name === $taxonomy ) {
						continue;
					}
					$filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
					if ( ! empty( $data['terms'] ) ) {
						$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
					}
					if ( 'or' == $data['query_type'] ) {
						$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
					}
				}
			}
		}

		$link = apply_filters( 'woodmart_shop_page_link', $link, $keep_query, $taxonomy );

		if ( is_string( $link ) ) {
			return $link;
		} else {
			return '';
		}
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Get product design option
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_product_design' ) ) {
	function woodmart_product_design() {
		$design = woodmart_get_opt( 'product_design' );
		if ( is_singular( 'product' ) ) {
			$custom = get_post_meta( get_the_ID(), '_woodmart_product_design', true );
			if ( ! empty( $custom ) && $custom != 'inherit' ) {
				$design = $custom;
			}
		}

		return $design;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Is product sticky
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_product_sticky' ) ) {
	function woodmart_product_sticky() {
		$sticky = woodmart_get_opt( 'product_sticky' ) && in_array( woodmart_get_opt( 'single_product_style' ), array( 1, 2, 3 ) ) ? true : false;
		if ( is_singular( 'product' ) ) {
			$custom = get_post_meta( get_the_ID(), '_woodmart_product_sticky', true );
			if ( ! empty( $custom ) && $custom != 'inherit' ) {
				$sticky = $custom;
			}
		}

		return $sticky;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Custom thumbnail function for slider
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_get_all_product_thumbnails_urls' ) ) {
	/**
	 * Get all product thumbnails urls.
	 *
	 * @return array List of product thumbnails urls.
	 * This array include all images from gallery and primary thumbnail image.
	 */
	function woodmart_get_all_product_thumbnails_urls() {
		global $product;

		$gallery_image_ids = $product->get_gallery_image_ids();
		$img_size          = 'woocommerce_thumbnail';
		$images_url        = array();

		if ( woodmart_loop_prop( 'img_size' ) ) {
			$img_size = woodmart_loop_prop( 'img_size' );
		}

		$img_size          = apply_filters( 'woodmart_custom_img_size', $img_size );
		$img_size_custom   = woodmart_loop_prop( 'img_size_custom' );
		$placeholder_image = get_option( 'woocommerce_placeholder_image', 0 );

		if ( $product->get_image_id() ) {
			array_unshift( $gallery_image_ids, $product->get_image_id() );
		} elseif ( ! empty( $placeholder_image ) ) {
			if ( is_numeric( $placeholder_image ) ) {
				array_unshift( $gallery_image_ids, $placeholder_image );
			} else {
				$images_url[]    = array(
					'url'    => $placeholder_image,
					'srcset' => '',
				);
			}
		}

		$max_number_product_thumbnails = apply_filters( 'woodmart_max_number_product_thumbnails', null );
		$gallery_image_ids             = array_slice( $gallery_image_ids, 0, is_null( $max_number_product_thumbnails ) ? null : intval( $max_number_product_thumbnails ) );

		foreach ( $gallery_image_ids as $attachment_id ) {
			$image_src         = woodmart_otf_get_image_url( $attachment_id, $img_size, $img_size_custom );
			$prepared_img_size = in_array( $img_size, array_keys( woodmart_get_all_image_sizes() ), true ) ? $img_size : 'medium';
			$image_srcset      = wp_get_attachment_image_srcset( $attachment_id, $prepared_img_size );

			$images_url[] = array(
				'url'    => apply_filters( 'woodmart_product_thumbnails_urls_image_src', $image_src, $attachment_id ),
				'srcset' => apply_filters( 'woodmart_product_thumbnails_urls_image_srcset', $image_srcset, $attachment_id ),
			);
		}

		return $images_url;
	}
}

if ( ! function_exists( 'woodmart_get_thumbnails_gallery_pagin' ) ) {
	/**
	 * Pagination for thumbnails gallery on products loop.
	 */
	function woodmart_get_thumbnails_gallery_pagin() {
		$images_ids = array_keys( woodmart_get_all_product_thumbnails_urls() );

		if ( 'no' === woodmart_loop_prop( 'grid_gallery' ) || ( ! woodmart_get_opt( 'grid_gallery' ) && ! woodmart_loop_prop( 'grid_gallery' ) ) || 'hover' !== woodmart_loop_prop( 'grid_gallery_control', 'hover' ) || count( $images_ids ) <= 1) {
			return '';
		}

		ob_start();
		?>
		<div class="wd-product-grid-slider-pagin">
			<?php foreach ( $images_ids as $id ) : ?>
				<div data-image-id="<?php echo esc_attr( $id ); ?>" class="wd-product-grid-slider-dot"></div>
			<?php endforeach; ?>
		</div>
		<?php

		return ob_get_clean();
	}
}

if ( ! function_exists( 'woodmart_template_loop_product_thumbnails_gallery' ) ) {
	/**
	 * Render thumbnails gallery for products loop.
	 */
	function woodmart_template_loop_product_thumbnails_gallery() {
		if ( 'no' === woodmart_loop_prop( 'grid_gallery' ) || ( ! woodmart_get_opt( 'grid_gallery' ) && ! woodmart_loop_prop( 'grid_gallery' ) ) ) {
			return;
		}

		$images_url = woodmart_get_all_product_thumbnails_urls();

		if ( count( $images_url ) <= 1 ) {
			return;
		}

		$nav_classes   = 'none' === woodmart_loop_prop( 'grid_gallery_enable_arrows', 'none' ) ? ' wd-hide-md' : '';
		$nav_classes  .= 'hover' === woodmart_loop_prop( 'grid_gallery_control', 'hover' ) && 'arrows' === woodmart_loop_prop( 'grid_gallery_enable_arrows', 'none' ) ? ' wd-hover-enabled' : '';

		woodmart_enqueue_inline_style( 'woo-opt-grid-gallery' );
		woodmart_enqueue_js_script( 'image-gallery-in-loop' );
		?>
		<div class="wd-product-grid-slider wd-fill">
			<?php foreach ( $images_url as $key => $image_url ) : ?>
				<div class="wd-product-grid-slide" data-image-url="<?php echo esc_url( $image_url['url'] ); ?>" data-image-srcset="<?php echo esc_attr( $image_url['srcset'] ); ?>" data-image-id="<?php echo esc_attr( $key ); ?>"></div>
			<?php endforeach; ?>
		</div>

		<?php if ( ! ( 'hover' === woodmart_loop_prop( 'grid_gallery_control', 'hover' ) && 'none' === woodmart_loop_prop( 'grid_gallery_enable_arrows', 'none' ) ) ) : ?>
			<div class="wd-product-grid-slider-nav wd-fill<?php echo esc_attr( $nav_classes ); ?>">
				<div class="wd-prev"></div>
				<div class="wd-next"></div>
			</div>
		<?php endif; ?>

		<?php if ( ! in_array( woodmart_loop_prop( 'product_hover' ), array( 'base', 'quick' ), true ) || 'list' === woodmart_new_get_shop_view( woodmart_get_opt( 'shop_view' ) ) ) : ?>
			<?php echo woodmart_get_thumbnails_gallery_pagin(); ?>
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'woodmart_template_loop_product_thumbnail' ) ) {
	function woodmart_template_loop_product_thumbnail() {
		echo woodmart_get_product_thumbnail();
	}
}

if ( ! function_exists( 'woodmart_get_product_thumbnail' ) ) {
	function woodmart_get_product_thumbnail( $size = 'woocommerce_thumbnail', $attach_id = false ) {
		global $product;
		$custom_size = $size;

		if ( woodmart_loop_prop( 'double_size' ) ) {
			$shop_catalog = wc_get_image_size( 'woocommerce_thumbnail' );

			$width  = (int) ( $shop_catalog['width'] * 2 );
			$height = ( ! empty( $shop_catalog['height'] ) ) ? (int) ( $shop_catalog['height'] * 2 ) : '';

			$size = array( $width, $height );
		}

		if ( $product->get_image_id() ) {
			if ( ! $attach_id ) {
				$attach_id = $product->get_image_id();
			}

			if ( woodmart_loop_prop( 'img_size' ) ) {
				$custom_size = woodmart_loop_prop( 'img_size' );
			}

			$custom_size = apply_filters( 'woodmart_custom_img_size', $custom_size );

			$img = woodmart_otf_get_image_html( $attach_id, $custom_size, woodmart_loop_prop( 'img_size_custom' ) );

			return apply_filters( 'woodmart_get_product_thumbnail', $img, $product, $size, $attach_id );
		} elseif ( wc_placeholder_img_src() ) {
			return wc_placeholder_img( $size );
		}
	}
}

if ( ! function_exists( 'woodmart_grid_swatches_attribute' ) ) {
	function woodmart_grid_swatches_attribute() {
		$custom = get_post_meta( get_the_ID(), '_woodmart_swatches_attribute', true );
		return empty( $custom ) ? woodmart_get_opt( 'grid_swatches_attribute' ) : $custom;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Get product page classes (columns) for product images and product information blocks
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_product_images_size' ) ) {
	function woodmart_product_images_size() {
		$summary_size = ( woodmart_product_summary_size() == 12 ) ? 12 : 12 - woodmart_product_summary_size();
		return apply_filters( 'woodmart_product_summary_size', $summary_size );
	}
}

if ( ! function_exists( 'woodmart_product_summary_size' ) ) {
	function woodmart_product_summary_size() {
		$page_layout = woodmart_get_opt( 'single_product_style' );

		$size = 6;
		switch ( $page_layout ) {
			case 1:
				$size = 8;
				break;
			case 2:
				$size = 6;
				break;
			case 3:
				$size = 4;
				break;
			case 4:
				$size = 12;
				break;
			case 5:
				$size = 12;
				break;
		}
		return apply_filters( 'woodmart_product_summary_size', $size );
	}
}

if ( ! function_exists( 'woodmart_single_product_class' ) ) {
	function woodmart_single_product_class() {
		$classes   = array();
		$classes[] = 'single-product-page';
		$classes[] = 'single-product-content';

		$design      = woodmart_product_design();
		$product_bg  = woodmart_get_opt( 'product-background' );
		$page_layout = woodmart_get_opt( 'single_product_style' );

		$classes[] = 'product-design-' . $design;
		$classes[] = 'tabs-location-' . woodmart_get_opt( 'product_tabs_location' );
		$classes[] = 'tabs-type-' . woodmart_get_opt( 'product_tabs_layout' );
		$classes[] = 'meta-location-' . woodmart_get_opt( 'product_show_meta' );
		$classes[] = 'reviews-location-' . woodmart_get_opt( 'reviews_location' );

		if ( woodmart_product_sticky() ) {
			$classes[] = 'wd-sticky-on';

			wp_enqueue_script( 'imagesloaded' );
			woodmart_enqueue_js_library( 'sticky-kit' );
			woodmart_enqueue_js_script( 'sticky-details' );
		}

		if ( $design == 'alt' ) {
			$classes[] = 'product-align-center';
		}

		if ( $page_layout == 4 || $page_layout == 5 ) {
			$classes[] = 'image-full-width';
		}

		if ( woodmart_get_opt( 'single_full_width' ) ) {
			$classes[] = 'product-full-width';
		}

		if ( woodmart_get_opt( 'product_summary_shadow' ) ) {
			$classes[] = 'product-summary-shadow';
		}

		if ( woodmart_product_sticky() ) {
			$classes[] = 'product-sticky-on';
		}

		if ( ! empty( $product_bg ) && ! empty( $product_bg['background-color'] ) ) {
			$classes[] = 'product-has-bg';
		} else {
			$classes[] = 'product-no-bg';
		}

		return $classes;

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Configure product image gallery JS
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_get_product_gallery_settings' ) ) {
	function woodmart_get_product_gallery_settings() {
		return apply_filters(
			'woodmart_product_gallery_settings',
			array(
				'thumbs_slider' => array(
					'items' => array(
						'desktop'        => woodmart_get_opt( 'single_product_thumbnails_items_desktop', 4 ),
						'tablet'         => woodmart_get_opt( 'single_product_thumbnails_items_tablet', 4 ),
						'mobile'         => woodmart_get_opt( 'single_product_thumbnails_items_mobile', 3 ),
						'vertical_items' => woodmart_get_opt( 'single_product_thumbnails_vertical_items', 3 ),
					),
				),
			)
		);
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * WooCommerce enqueues 3 stylesheets by default. You can disable them all with the following snippet
 * ------------------------------------------------------------------------------------------------
 */

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * ------------------------------------------------------------------------------------------------
 * Disable photoswipe
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'wp_footer', 'woocommerce_photoswipe' );

/**
 * ------------------------------------------------------------------------------------------------
 * Remove ordering from toolbar
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

/**
 * ------------------------------------------------------------------------------------------------
 * Unhook the WooCommerce wrappers
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * ------------------------------------------------------------------------------------------------
 * Disable default product zoom init
 * ------------------------------------------------------------------------------------------------
 */
add_filter( 'woocommerce_single_product_zoom_enabled', '__return_false' );

/**
 * ------------------------------------------------------------------------------------------------
 * Get CSS class for widget in shop area. Based on the number of widgets
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_get_widget_grid_attrs' ) ) {
	function woodmart_get_widget_grid_attrs( $filters_type = 'widgets' ) {
		global $_wp_sidebars_widgets;
		if ( empty( $_wp_sidebars_widgets ) ) {
			$_wp_sidebars_widgets = get_option( 'sidebars_widgets', array() );
		}

		$sidebars_widgets_count = $_wp_sidebars_widgets;
		$style_attrs            = '';

		if ( 'widgets' === $filters_type && isset( $sidebars_widgets_count[ 'filters-area' ] ) ) {
			$count         = ( isset( $sidebars_widgets_count[ 'filters-area' ] ) ) ? count( $sidebars_widgets_count[ 'filters-area' ] ) : 0;
			$widget_count  = apply_filters( 'widgets_count_filters-area', $count );
			$widget_column = 4;
			$column        = woodmart_get_opt( 'shop_filters_columns' );
			$column_tablet = woodmart_get_opt( 'shop_filters_columns_tablet', 'auto' );
			$column_mobile = woodmart_get_opt( 'shop_filters_columns_mobile', 'auto' );

			if ( $widget_count && $widget_count < 4 ) {
				$widget_column = $widget_count;
			}

			if ( ! $column ) {
				$column        = $widget_column;
				$column_tablet = 2;
				$column_mobile = 1;
			}

			$grid_attr = array(
				'columns'        => $column,
				'columns_tablet' => $column_tablet,
				'columns_mobile' => $column_mobile,
			);

			$spacing        = woodmart_get_opt( 'shop_filters_spacing', 30 );
			$spacing_tablet = woodmart_get_opt( 'shop_filters_spacing_tablet', '' );
			$spacing_mobile = woodmart_get_opt( 'shop_filters_spacing_mobile', '' );

			$style_attrs .= '--wd-gap-lg:' . $spacing . 'px;';
			$style_attrs .= $spacing_tablet || '0' === $spacing_tablet ? '--wd-gap-md:' . $spacing_tablet . 'px;' : '';
			$style_attrs .= $spacing_mobile || '0' === $spacing_mobile ? '--wd-gap-sm:' . $spacing_mobile . 'px;' : '';
		} else {
			$grid_attr = array(
				'columns' => 1,
			);
		}

		return woodmart_get_grid_attrs( $grid_attr ) . $style_attrs;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Play with woocommerce hooks
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_wc_comments_template' ) ) {
	/**
	 * This function just wrapper comments_template.
	 */
	function woodmart_wc_comments_template() {
		global $product;

		if ( ! $product->get_reviews_allowed() ) {
			return;
		}

		$content_classes  = ' wd-layout-' . woodmart_get_opt( 'reviews_section_columns', 'two-column' );
		$content_classes .= ' wd-form-pos-' . woodmart_get_opt( 'reviews_form_location', 'after' );

		woodmart_enqueue_inline_style( 'mod-comments' );
		?>
		<div class="wd-single-reviews<?php echo esc_attr( $content_classes ); ?>">
			<?php comments_template(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_woocommerce_hooks' ) ) {
	function woodmart_woocommerce_hooks() {
		global $woodmart_prefix;

		$product_meta_position = woodmart_get_opt( 'product_show_meta' );
		$product_show_meta     = ( $product_meta_position != 'hide' );
		$product_show_share    = woodmart_get_opt( 'product_share' );
		$product_show_desc     = woodmart_get_opt( 'product_short_description' );
		$tabs_layout           = woodmart_get_opt( 'product_tabs_layout' );
		$tabs_location         = woodmart_get_opt( 'product_tabs_location' );
		$reviews_location      = woodmart_get_opt( 'reviews_location' );

		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

		// Reviews location.
		if ( 'separate' === $reviews_location && ! Builder::get_instance()->has_custom_layout( 'single_product' ) ) {
			add_filter( 'woocommerce_product_tabs', 'woodmart_single_product_remove_reviews_tab', 98 );
			add_action( 'woocommerce_after_single_product_summary', 'woodmart_wc_comments_template', 50 );
		}

		// Upsells position.
		if ( is_singular( 'product' ) && 'hide' !== woodmart_get_opt( 'upsells_position' ) ) {
			if ( 'sidebar' === woodmart_get_opt( 'upsells_position' ) ) {
				add_action( 'woodmart_before_sidebar_area', 'woocommerce_upsell_display', 20 );
			} else {
				add_action( 'woodmart_woocommerce_after_sidebar', 'woocommerce_upsell_display', 10 );
			}
		}

		// Disable related products option
		if ( woodmart_get_opt( 'related_products' ) && ! get_post_meta( get_the_ID(), '_woodmart_related_off', true ) ) {
			add_action( 'woodmart_woocommerce_after_sidebar', 'woocommerce_output_related_products', 20 );
		}

		if ( woodmart_get_opt( 'shop_filters' ) || Builder::get_instance()->has_custom_layout( 'shop_archive' ) ) {

			// Use our own order widget list?
			if ( apply_filters( 'woodmart_use_custom_order_widget', ! woodmart_get_opt( 'hide_sort_by' ) ) ) {
				if ( ! is_active_widget( false, false, 'woodmart-woocommerce-sort-by', true ) ) {
					add_action( 'woodmart_before_filters_widgets', 'woodmart_sorting_widget', 10 );
				}
				if ( woodmart_get_opt( 'shop_filters_type' ) == 'widgets' ) {
					remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
				}
			}

			// Use our custom price filter widget list?
			if ( apply_filters( 'woodmart_use_custom_price_widget', ! woodmart_get_opt( 'hide_price_filter' ) ) && ! is_active_widget( false, false, 'woodmart-price-filter', true ) && ! is_admin() ) {
				woodmart_force_enqueue_style( 'widget-price-filter' );
				add_action( 'woodmart_before_filters_widgets', 'woodmart_price_widget', 20 );
			}

			// Add 'filters button'.
			add_action( 'woocommerce_before_shop_loop', 'woodmart_filter_buttons', 40 );
		}

		if ( ! Builder::get_instance()->has_custom_layout( 'shop_archive' ) ) {
			add_action( 'woocommerce_after_main_content', 'woodmart_get_extra_description_category', 5 );
		}

		add_action( 'woocommerce_cart_is_empty', 'woodmart_empty_cart_text', 20 );

		/**
		 * Remove default empty cart text
		 */
		remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );

		// Brand tab for single product
		if ( woodmart_get_opt( 'brand_tab' ) ) {
			add_filter( 'woocommerce_product_tabs', 'woodmart_product_brand_tab' );
		}

		// Poduct brand
		if ( woodmart_get_opt( 'product_brand_location' ) == 'about_title' && is_singular( 'product' ) ) {
			add_action( 'woocommerce_single_product_summary', 'woodmart_product_brand', 3 );
		} elseif ( is_singular( 'product' ) ) {
			add_action( 'woodmart_before_sidebar_area', 'woodmart_product_brand', 10 );
		}

		// Product share

		if ( $product_meta_position != 'after_tabs' && $product_show_share ) {
			add_action( 'woocommerce_single_product_summary', 'woodmart_product_share_buttons', 62 );
		}

		// Disable meta and description if turned off
		if ( $product_meta_position != 'add_to_cart' ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		}

		if ( ! $product_show_desc ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		}

		// Product tabs location.

		if ( 'summary' === $tabs_location && 'accordion' === $tabs_layout ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 39 );
		}

		if ( $product_meta_position == 'after_tabs' ) {
			add_action(
				'woodmart_after_product_tabs',
				function() {
					echo '<div class="wd-before-product-tabs"><div class="container">';
				},
				10
			);

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			if ( $product_show_meta ) {
				add_action( 'woodmart_after_product_tabs', 'woocommerce_template_single_meta', 20 );
			}
			if ( $product_show_share ) {
				add_action( 'woodmart_after_product_tabs', 'woodmart_product_share_buttons', 30 );
			}

			add_action(
				'woodmart_after_product_tabs',
				function() {
					echo '</div></div>';
				},
				200
			);
		}

		// Product video, 360 view, zoom
		$video_url          = get_post_meta( get_the_ID(), '_woodmart_product_video', true );
		$images_360_gallery = woodmart_get_360_gallery_attachment_ids();

		if ( ! empty( $video_url ) || ! empty( $images_360_gallery ) || woodmart_get_opt( 'photoswipe_icon' ) ) {
			add_action( 'woodmart_on_product_image', 'woodmart_additional_galleries_open', 25 );
			add_action( 'woodmart_on_product_image', 'woodmart_additional_galleries_close', 100 );
		}

		if ( ! empty( $video_url ) ) {
			add_action( 'woodmart_on_product_image', 'woodmart_product_video_button', 30 );
		}

		if ( ! empty( $images_360_gallery ) ) {
			add_action( 'woodmart_on_product_image', 'woodmart_product_360_view', 40 );
		}

		if ( woodmart_get_opt( 'photoswipe_icon' ) ) {
			add_action( 'woodmart_on_product_image', 'woodmart_product_zoom_button', 50 );
		}

		// Custom extra content
		$extra_block = get_post_meta( get_the_ID(), '_woodmart_extra_content', true );

		if ( ! empty( $extra_block ) && $extra_block != 0 ) {
			$extra_position = get_post_meta( get_the_ID(), '_woodmart_extra_position', true );
			if ( $extra_position == 'before' ) {
				add_action( 'woocommerce_before_single_product', 'woodmart_product_extra_content', 20 );
			} elseif ( $extra_position == 'prefooter' ) {
				add_action( 'woodmart_woocommerce_after_sidebar', 'woodmart_product_extra_content', 30 );
			} else {
				add_action( 'woodmart_after_product_content', 'woodmart_product_extra_content', 20 );

			}
		}

		// Custom tabs
		add_filter( 'woocommerce_product_tabs', 'woodmart_custom_product_tabs' );

		// Timer on the single product page
		add_action( 'woocommerce_single_product_summary', 'woodmart_single_product_countdown', 15 );

		// Product attibutes after of short description
		if ( woodmart_get_opt( 'attr_after_short_desc' ) && ! Builder::get_instance()->has_custom_layout( 'single_product' ) ) {
			add_action( 'woocommerce_single_product_summary', 'woodmart_display_product_attributes', 21 );
			add_filter( 'woocommerce_product_tabs', 'woodmart_single_product_remove_additional_information_tab', 98 );
		}

		// Single product stock progress bar
		if ( woodmart_get_opt( 'single_stock_progress_bar' ) ) {
			add_action( 'woocommerce_single_product_summary', 'woodmart_stock_progress_bar', 16 );
		}

		// Change single product labels position.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
		add_action( 'woodmart_before_single_product_main_gallery', 'woocommerce_show_product_sale_flash' );

		if ( 'fw-button' === woodmart_loop_prop( 'product_hover' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}

		// Hide tabs headings option.
		if ( woodmart_get_opt( 'hide_tabs_titles' ) || get_post_meta( get_the_ID(), '_woodmart_hide_tabs_titles', true ) ) {
			add_filter( 'woocommerce_product_description_heading', '__return_false', 20 );
			add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 20 );
		}
	}

	add_action( 'wp', 'woodmart_woocommerce_hooks', 1000 );
}

if ( ! function_exists( 'woodmart_woocommerce_init_hooks' ) ) {
	function woodmart_woocommerce_init_hooks() {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

		remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );

		add_action( 'woocommerce_before_shop_loop_item_title', 'woodmart_template_loop_product_thumbnail', 10 );

		// Remove product content link.
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woodmart_add_loop_btn', 'woocommerce_template_loop_add_to_cart', 10 );

		remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

		remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
		add_action( 'woocommerce_before_subcategory_title', 'woodmart_category_thumb_double_size', 10 );

		// Move crossels after totals.
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 20 );

		// Wrapp cart totals.
		add_action(
			'woocommerce_before_cart_totals',
			function() {
				echo '<div class="cart-totals-inner wd-set-mb reset-last-child wd-' . woodmart_get_opt( 'cart_totals_layout', 'layout-1' ) . '">';
			},
			1
		);
		add_action(
			'woocommerce_after_cart_totals',
			function() {
				echo '</div>';
			},
			200
		);

		/**
		 * Remove rating from grid.
		 */
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}

	add_action( 'init', 'woodmart_woocommerce_init_hooks', 1000 );
}

if ( ! function_exists( 'woodmart_single_product_countdown' ) ) {
	function woodmart_single_product_countdown( $tabs ) {
		$timer = woodmart_get_opt( 'product_countdown' );
		if ( $timer ) {
			woodmart_product_sale_countdown();
		}
	}
}

if ( ! function_exists( 'woodmart_display_product_attributes' ) ) {
	function woodmart_display_product_attributes() {
		global $product;
		if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
			wc_display_product_attributes( $product );
		}
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Additional tab
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_custom_product_tabs' ) ) {
	function woodmart_custom_product_tabs( $tabs ) {
		$additional_tab_title   = woodmart_get_opt( 'additional_tab_title' );
		$additional_tab_2_title = woodmart_get_opt( 'additional_tab_2_title' );
		$additional_tab_3_title = woodmart_get_opt( 'additional_tab_3_title' );
		$custom_tab_title       = get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_title', true );
		$custom_tab_title_2     = get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_title_2', true );

		if ( $additional_tab_title ) {
			$tabs['wd_additional_tab'] = array(
				'title'    => $additional_tab_title,
				'priority' => 50,
				'callback' => 'woodmart_additional_product_tab_content',
			);
		}

		if ( $additional_tab_2_title ) {
			$tabs['wd_additional_tab_2'] = array(
				'title'    => $additional_tab_2_title,
				'priority' => 60,
				'callback' => 'woodmart_additional_product_tab_2_content',
			);
		}

		if ( $additional_tab_3_title ) {
			$tabs['wd_additional_tab_3'] = array(
				'title'    => $additional_tab_3_title,
				'priority' => 70,
				'callback' => 'woodmart_additional_product_tab_3_content',
			);
		}

		if ( $custom_tab_title ) {
			$tabs['wd_custom_tab'] = array(
				'title'    => $custom_tab_title,
				'priority' => 80,
				'callback' => 'woodmart_custom_product_tab_content',
			);
		}

		if ( $custom_tab_title_2 ) {
			$tabs['wd_custom_tab_2'] = array(
				'title'    => $custom_tab_title_2,
				'priority' => 90,
				'callback' => 'woodmart_custom_product_tab_content_2',
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'woodmart_additional_product_tab_content' ) ) {
	function woodmart_additional_product_tab_content() {
		if ( 'text' === woodmart_get_opt( 'additional_tab_content_type', 'text' ) ) {
			echo do_shortcode( woodmart_get_opt( 'additional_tab_text' ) );
		} else {
			echo woodmart_get_html_block( woodmart_get_opt( 'additional_tab_html_block' ) );
		}
	}
}

if ( ! function_exists( 'woodmart_additional_product_tab_2_content' ) ) {
	function woodmart_additional_product_tab_2_content() {
		if ( 'text' === woodmart_get_opt( 'additional_tab_2_content_type', 'text' ) ) {
			echo do_shortcode( woodmart_get_opt( 'additional_tab_2_text' ) );
		} else {
			echo woodmart_get_html_block( woodmart_get_opt( 'additional_tab_2_html_block' ) );
		}
	}
}

if ( ! function_exists( 'woodmart_additional_product_tab_3_content' ) ) {
	function woodmart_additional_product_tab_3_content() {
		if ( 'text' === woodmart_get_opt( 'additional_tab_3_content_type', 'text' ) ) {
			echo do_shortcode( woodmart_get_opt( 'additional_tab_3_text' ) );
		} else {
			echo woodmart_get_html_block( woodmart_get_opt( 'additional_tab_3_html_block' ) );
		}
	}
}

if ( ! function_exists( 'woodmart_custom_product_tab_content' ) ) {
	function woodmart_custom_product_tab_content() {
		$type_content = get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_content_type', true );

		if ( 'html_block' === $type_content ) {
			$tab_content = woodmart_get_html_block( get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_html_block', true ) );
		} else {
			$tab_content = wpautop( get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_content', true ) );
		}

		echo do_shortcode( $tab_content );
	}
}

if ( ! function_exists( 'woodmart_custom_product_tab_content_2' ) ) {
	function woodmart_custom_product_tab_content_2() {
		$type_content = get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_content_type_2', true );

		if ( 'html_block' === $type_content ) {
			$tab_content = woodmart_get_html_block( get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_html_block_2', true ) );
		} else {
			$tab_content = wpautop( get_post_meta( get_the_ID(), '_woodmart_product_custom_tab_content_2', true ) );
		}

		echo do_shortcode( $tab_content );
	}
}

if ( ! function_exists( 'woodmart_single_product_remove_additional_information_tab' ) ) {
	/**
	 * Remove reviews tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	function woodmart_single_product_remove_additional_information_tab( $tabs ) {
		unset( $tabs['additional_information'] );
		return $tabs;
	}
}

if ( ! function_exists( 'woodmart_single_product_remove_reviews_tab' ) ) {
	/**
	 * Remove reviews tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	function woodmart_single_product_remove_reviews_tab( $tabs ) {
		unset( $tabs['reviews'] );
		return $tabs;
	}
}

if ( ! function_exists( 'woodmart_single_product_remove_description_tab' ) ) {
	/**
	 * Remove description tab
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	function woodmart_single_product_remove_description_tab( $tabs ) {
		unset( $tabs['description'] );
		return $tabs;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Filters buttons
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_filter_widgts_classes' ) ) {
	function woodmart_filter_widgts_classes( $count ) {

		if ( apply_filters( 'woodmart_use_custom_order_widget', true ) && ! is_active_widget( false, false, 'woodmart-woocommerce-sort-by', true ) ) {
			$count++;
		}

		if ( apply_filters( 'woodmart_use_custom_price_widget', true ) && ! is_active_widget( false, false, 'woodmart-price-filter', true ) ) {
			$count++;
		}

		return $count;
	}

	add_filter( 'widgets_count_filters-area', 'woodmart_filter_widgts_classes' );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Force WOODMART Swatche layered nav and price widget to work
 * ------------------------------------------------------------------------------------------------
 */


add_filter( 'woocommerce_is_layered_nav_active', 'woodmart_is_layered_nav_active' );
if ( ! function_exists( 'woodmart_is_layered_nav_active' ) ) {
	function woodmart_is_layered_nav_active() {
		return is_active_widget( false, false, 'woodmart-woocommerce-layered-nav', true );
	}
}

add_filter( 'woocommerce_is_price_filter_active', 'woodmart_is_layered_price_active' );

if ( ! function_exists( 'woodmart_is_layered_price_active' ) ) {
	function woodmart_is_layered_price_active() {
		$result = is_active_widget( false, false, 'woodmart-price-filter', true );
		if ( ! $result ) {
			$result = apply_filters( 'woodmart_use_custom_price_widget', true );
		}
		return $result;
	}
}



/**
 * ------------------------------------------------------------------------------------------------
 * Change the position of woocommerce breadcrumbs
 * ------------------------------------------------------------------------------------------------
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// **********************************************************************//
// ! Items per page select on the shop page
// **********************************************************************//

if ( ! function_exists( 'woodmart_set_customer_session' ) ) {
	function woodmart_set_customer_session() {
		if ( ! function_exists( 'WC' ) || ! apply_filters( 'woodmart_woo_session', false ) ) {
			return;
		}

		if ( WC()->version > '2.1' && ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) :
			WC()->session->set_customer_session_cookie( true );
		endif;
	}
	add_action( 'woodmart_before_shop_page', 'woodmart_set_customer_session', 10 );
}

if ( apply_filters( 'woodmart_woo_session', false ) ) {
	add_action( 'woodmart_before_shop_page', 'woodmart_woo_products_per_page_action', 100 );
} else {
	add_action( 'init', 'woodmart_products_per_page_action', 100 );
}

if ( ! function_exists( 'woodmart_products_per_page_action' ) ) {
	function woodmart_products_per_page_action() {
		if ( isset( $_REQUEST['per_page'] ) && 1 != $_REQUEST['per_page'] && ! isset( $_REQUEST['_locale'] ) && ! isset( $_REQUEST['shortcode'] ) && apply_filters( 'woodmart_per_page_custom_expression', true ) ) {
			setcookie( 'shop_per_page', intval( $_REQUEST['per_page'] ), 0, COOKIEPATH, COOKIE_DOMAIN, woodmart_cookie_secure_param(), false );
		}
	}
}

if ( ! function_exists( 'woodmart_woo_products_per_page_action' ) ) {
	function woodmart_woo_products_per_page_action() {
		if ( isset( $_REQUEST['per_page'] ) ) :
			if ( ! class_exists( 'WC_Session_Handler' ) ) {
				return;
			}
			$s = WC()->session; // WC()->session
			if ( is_null( $s ) ) {
				return;
			}

			$s->set( 'shop_per_page', intval( $_REQUEST['per_page'] ) );
		endif;
	}
}

// **********************************************************************//
// ! Get Items per page number on the shop page
// **********************************************************************//

if ( ! function_exists( 'woodmart_get_products_per_page' ) ) {
	function woodmart_get_products_per_page() {
		if ( apply_filters( 'woodmart_woo_session', false ) ) {
			return woodmart_woo_get_products_per_page();
		} else {
			return woodmart_new_get_products_per_page();
		}
	}
}

if ( ! function_exists( 'woodmart_new_get_products_per_page' ) ) {
	function woodmart_new_get_products_per_page() {
		if ( isset( $_REQUEST['per_page'] ) && ! empty( $_REQUEST['per_page'] ) ) {
			return woodmart_get_current_products_per_page( sanitize_text_field( $_REQUEST['per_page'] ) ); //phpcs:ignore
		} elseif ( isset( $_COOKIE['shop_per_page'] ) ) {
			$val = $_COOKIE['shop_per_page'];

			if ( ! empty( $val ) ) {
				return intval( $val );
			}
		}

		return intval( woodmart_get_opt( 'shop_per_page' ) );
	}
}

if ( ! function_exists( 'woodmart_woo_get_products_per_page' ) ) {
	function woodmart_woo_get_products_per_page() {
		if ( ! class_exists( 'WC_Session_Handler' ) ) {
			return;
		}
		$s = WC()->session; // WC()->session
		if ( is_null( $s ) ) {
			return intval( woodmart_get_opt( 'shop_per_page' ) );
		}

		if ( isset( $_REQUEST['per_page'] ) && ! empty( $_REQUEST['per_page'] ) ) :
			return woodmart_get_current_products_per_page( sanitize_text_field( $_REQUEST['per_page'] ) ); //phpcs:ignore
		elseif ( $s->__isset( 'shop_per_page' ) ) :
			$val = $s->__get( 'shop_per_page' );
			if ( ! empty( $val ) ) {
				return intval( $s->__get( 'shop_per_page' ) );
			}
		endif;
		return intval( woodmart_get_opt( 'shop_per_page' ) );
	}
}

if ( ! function_exists( 'woodmart_get_current_products_per_page' ) ) {
	/**
	 * Get per page.
	 *
	 * @param integer $request Product count in page.
	 * @return integer
	 */
	function woodmart_get_current_products_per_page( $request ) {
		if ( apply_filters( 'woodmart_get_min_per_page', -1 ) <= $request && apply_filters( 'woodmart_get_max_per_page', 500 ) >= $request ) {
			return intval( $request );
		}

		return intval( woodmart_get_opt( 'shop_per_page' ) );
	}
}

// **********************************************************************//
// ! Items view select on the shop page
// **********************************************************************//
if ( apply_filters( 'woodmart_woo_session', false ) ) {
	add_action( 'woodmart_before_shop_page', 'woodmart_woo_shop_view_action', 100 );
} else {
	add_action( 'init', 'woodmart_shop_view_action', 100 );
}

if ( ! function_exists( 'woodmart_shop_view_action' ) ) {
	function woodmart_shop_view_action() {
		if ( isset( $_REQUEST['shop_view'] ) && ! isset( $_REQUEST['shortcode'] ) ) {
			setcookie( 'shop_view', $_REQUEST['shop_view'], 0, COOKIEPATH, COOKIE_DOMAIN, woodmart_cookie_secure_param(), false );
		}
		if ( isset( $_REQUEST['per_row'] ) && ! isset( $_REQUEST['shortcode'] ) ) {
			setcookie( 'shop_per_row', $_REQUEST['per_row'], 0, COOKIEPATH, COOKIE_DOMAIN, woodmart_cookie_secure_param(), false );
		}
	}
}

if ( ! function_exists( 'woodmart_woo_shop_view_action' ) ) {
	function woodmart_woo_shop_view_action() {
		if ( ! class_exists( 'WC_Session_Handler' ) ) {
			return;
		}
		$s = WC()->session; // WC()->session
		if ( is_null( $s ) ) {
			return;
		}

		if ( isset( $_REQUEST['shop_view'] ) ) {
			$s->set( 'shop_view', $_REQUEST['shop_view'] );
		}
		if ( isset( $_REQUEST['per_row'] ) ) {
			$s->set( 'shop_per_row', $_REQUEST['per_row'] );
		}
	}
}
// **********************************************************************//
// ! Get Items per ROW number on the shop page
// **********************************************************************//

if ( ! function_exists( 'woodmart_get_products_columns_per_row' ) ) {
	function woodmart_get_products_columns_per_row() {
		if ( ! woodmart_woocommerce_installed() ) {
			return '';
		}

		if ( apply_filters( 'woodmart_woo_session', false ) ) {
			return woodmart_woo_get_products_columns_per_row();
		} else {
			return woodmart_new_get_products_columns_per_row();
		}
	}
}

if ( ! function_exists( 'woodmart_get_shop_view' ) ) {
	function woodmart_get_shop_view() {
		if ( apply_filters( 'woodmart_woo_session', false ) ) {
			return woodmart_woo_get_shop_view();
		} else {
			return woodmart_new_get_shop_view( woodmart_get_opt( 'shop_view' ) );
		}
	}
}

if ( ! function_exists( 'woodmart_new_get_products_columns_per_row' ) ) {
	function woodmart_new_get_products_columns_per_row( $per_row = '', $is_builder_element = false ) {
		$display_type = woocommerce_get_loop_display_mode();
		if ( isset( $_REQUEST['per_row'] ) && 'subcategories' !== $display_type ) {
			return intval( $_REQUEST['per_row'] );
		} elseif ( isset( $_COOKIE['shop_per_row'] ) && 'subcategories' !== $display_type ) {
			return intval( $_COOKIE['shop_per_row'] );
		} elseif ( $is_builder_element ) {
			return intval( $per_row );
		} else {
			return intval( woodmart_get_opt( 'products_columns' ) );
		}
	}
}

if ( ! function_exists( 'woodmart_new_get_shop_view' ) ) {
	function woodmart_new_get_shop_view( $shop_view = '', $is_builder_element = false ) {
		if ( isset( $_REQUEST['shop_view'] ) ) {
			return $_REQUEST['shop_view'];
		} elseif ( isset( $_COOKIE['shop_view'] ) ) {
			return $_COOKIE['shop_view'];
		} elseif ( ! $shop_view || $is_builder_element ) {
			return $shop_view;
		} else {
			$options_shop_view = woodmart_get_opt( 'shop_view' );
			if ( 'grid_list' === $options_shop_view ) {
				return 'grid';
			} elseif ( 'list_grid' === $options_shop_view ) {
				return 'list';
			} else {
				return $options_shop_view;
			}
		}
	}
}

if ( ! function_exists( 'woodmart_woo_get_products_columns_per_row' ) ) {
	function woodmart_woo_get_products_columns_per_row() {
		if ( ! class_exists( 'WC_Session_Handler' ) ) {
			return;
		}
		$s = WC()->session; // WC()->session
		if ( is_null( $s ) ) {
			return intval( woodmart_get_opt( 'products_columns' ) );
		}

		if ( isset( $_REQUEST['per_row'] ) ) {
			return intval( $_REQUEST['per_row'] );
		} elseif ( $s->__isset( 'shop_per_row' ) ) {
			return intval( $s->__get( 'shop_per_row' ) );
		} else {
			return intval( woodmart_get_opt( 'products_columns' ) );
		}
	}
}


if ( ! function_exists( 'woodmart_woo_get_shop_view' ) ) {
	function woodmart_woo_get_shop_view() {
		if ( ! class_exists( 'WC_Session_Handler' ) ) {
			return;
		}
		$s = WC()->session; // WC()->session
		if ( is_null( $s ) ) {
			return woodmart_get_opt( 'shop_view' );
		}

		if ( isset( $_REQUEST['shop_view'] ) ) {
			return $_REQUEST['shop_view'];
		} elseif ( $s->__isset( 'shop_view' ) ) {
			return $s->__get( 'shop_view' );
		} else {
			$shop_view = woodmart_get_opt( 'shop_view' );
			if ( $shop_view == 'grid_list' ) {
				return 'grid';
			} elseif ( $shop_view == 'list_grid' ) {
				return 'list';
			} else {
				return $shop_view;
			}
		}
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Remove () from numbers in categories widget
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_filter_product_categories_widget_args' ) ) {
	function woodmart_filter_product_categories_widget_args( $list_args ) {

		$list_args['walker'] = new WOODMART_WC_Product_Cat_List_Walker();

		return $list_args;
	}

	add_filter( 'woocommerce_product_categories_widget_args', 'woodmart_filter_product_categories_widget_args', 10, 1 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * AJAX add to cart for all product types
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_ajax_add_to_cart' ) ) {
	function woodmart_ajax_add_to_cart() {
		// Get messages
		ob_start();

		wc_print_notices();

		$notices = ob_get_clean();

		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'notices'   => $notices,
			'fragments' => apply_filters(
				'woocommerce_add_to_cart_fragments',
				array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
		);

		wp_send_json( $data );

		die();
	}
}

add_action( 'wp_ajax_woodmart_ajax_add_to_cart', 'woodmart_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_woodmart_ajax_add_to_cart', 'woodmart_ajax_add_to_cart' );

/**
 * ------------------------------------------------------------------------------------------------
 * Woodmart Related product count
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_related_count' ) ) {
	add_filter( 'woocommerce_output_related_products_args', 'woodmart_related_count' );
	function woodmart_related_count() {
		$args['posts_per_page'] = ( woodmart_get_opt( 'related_product_count' ) ) ? woodmart_get_opt( 'related_product_count' ) : 8;
		return $args;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Reset loop
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_reset_loop' ) ) {
	function woodmart_reset_loop() {
		unset( $GLOBALS['woodmart_loop'] );
		woodmart_setup_loop();
	}
	add_action( 'woocommerce_after_shop_loop', 'woodmart_reset_loop', 1000 );
	add_action( 'loop_end', 'woodmart_reset_loop', 1000 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Get loop prop
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_loop_prop' ) ) {
	function woodmart_loop_prop( $prop, $default = '' ) {
		woodmart_setup_loop();

		return isset( $GLOBALS['woodmart_loop'], $GLOBALS['woodmart_loop'][ $prop ] ) ? $GLOBALS['woodmart_loop'][ $prop ] : $default;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Set loop prop
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_set_loop_prop' ) ) {
	function woodmart_set_loop_prop( $prop, $value = '' ) {
		if ( ! isset( $GLOBALS['woodmart_loop'] ) ) {
			woodmart_setup_loop();
		}

		$GLOBALS['woodmart_loop'][ $prop ] = $value;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Setup loop
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'woodmart_setup_loop' ) ) {
	function woodmart_setup_loop( $args = array() ) {
		if ( isset( $GLOBALS['woodmart_loop'] ) ) {
			return; // If the loop has already been setup, bail.
		}

		$default_args = array(
			'products_bordered_grid'               => woodmart_get_opt( 'products_bordered_grid' ),
			'products_bordered_grid_style'         => woodmart_get_opt( 'products_bordered_grid_style', 'outside' ),
			'product_categories_is_element'        => false,
			'product_categories_color_scheme'      => woodmart_get_opt( 'categories_color_scheme' ),
			'hide_categories_product_count'        => woodmart_get_opt( 'hide_categories_product_count' ),
			'hide_categories_subcategories'        => woodmart_get_opt( 'hide_categories_subcategories' ),
			'products_color_scheme'                => woodmart_get_opt( 'products_color_scheme', 'default' ),
			'products_with_background'             => woodmart_get_opt( 'products_with_background' ),
			'products_shadow'                      => woodmart_get_opt( 'products_shadow' ),

			'products_different_sizes'             => woodmart_get_opt( 'products_different_sizes' ),
			'product_categories_design'            => woodmart_get_opt( 'categories_design' ),
			'product_categories_shadow'            => woodmart_get_opt( 'categories_with_shadow' ),
			'products_columns'                     => ( woodmart_get_opt( 'per_row_columns_selector' ) || Builder::get_instance()->has_custom_layout( 'shop_archive' ) ) ? apply_filters( 'loop_shop_columns', woodmart_get_products_columns_per_row() ) : woodmart_get_opt( 'products_columns' ),
			'products_columns_tablet'              => woodmart_get_opt( 'products_columns_tablet' ),
			'products_columns_mobile'              => woodmart_get_opt( 'products_columns_mobile' ),
			'products_spacing'                     => woodmart_get_opt( 'products_spacing' ),
			'products_spacing_tablet'              => woodmart_get_opt( 'products_spacing_tablet' ),
			'products_spacing_mobile'              => woodmart_get_opt( 'products_spacing_mobile' ),
			'product_categories_style'             => false,
			'product_hover'                        => woodmart_get_opt( 'products_hover' ),
			'stretch_product_desktop'              => woodmart_get_opt( 'stretch_product_desktop' ),
			'stretch_product_tablet'               => woodmart_get_opt( 'stretch_product_tablet' ),
			'stretch_product_mobile'               => woodmart_get_opt( 'stretch_product_mobile' ),
			'products_view'                        => woodmart_get_shop_view(),
			'products_masonry'                     => woodmart_get_opt( 'products_masonry' ),
			'grid_gallery'                         => woodmart_get_opt( 'grid_gallery' ),
			'grid_gallery_control'                 => woodmart_get_opt( 'grid_gallery_control', 'hover' ),
			'grid_gallery_enable_arrows'           => woodmart_get_opt( 'grid_gallery_enable_arrows', 'none' ),
			'product_quantity'                     => woodmart_get_opt( 'product_quantity' ),
			'shop_pagination'                      => woodmart_get_opt( 'shop_pagination' ),
			'product_categories_image_size'        => apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' ),
			'product_categories_image_size_custom' => false,
			'img_size'                             => false,
			'img_size_custom'                      => false,

			'timer'                                => woodmart_get_opt( 'shop_countdown' ),
			'progress_bar'                         => woodmart_get_opt( 'grid_stock_progress_bar' ),
			'swatches'                             => false,

			'is_slider'                            => false,
			'is_shortcode'                         => false,
			'is_quick_view'                        => false,

			'woocommerce_loop'                     => 0,
			'woodmart_loop'                        => 0,

			'parts_media'                          => true,
			'parts_title'                          => true,
			'parts_meta'                           => true,
			'parts_text'                           => true,
			'parts_btn'                            => true,
			'parts_published_date'                 => woodmart_get_opt( 'parts_published_date', true ),

			'blog_design'                          => woodmart_get_opt( 'blog_design' ),
			'blog_type'                            => false,
			'blog_layout'                          => 'grid',
			'blog_columns'                         => woodmart_get_opt( 'blog_columns' ),
			'blog_columns_tablet'                  => woodmart_get_opt( 'blog_columns_tablet' ),
			'blog_columns_mobile'                  => woodmart_get_opt( 'blog_columns_mobile' ),
			'double_size'                          => false,

			'portfolio_style'                      => woodmart_get_opt( 'portoflio_style' ),
			'portfolio_column'                     => woodmart_get_opt( 'projects_columns' ),
			'portfolio_columns_tablet'             => woodmart_get_opt( 'projects_columns_tablet' ),
			'portfolio_columns_mobile'             => woodmart_get_opt( 'projects_columns_mobile' ),
			'portfolio_image_size'                 => woodmart_get_opt( 'portoflio_image_size' ),
			'portfolio_image_size_custom'          => false,
		);

		$GLOBALS['woodmart_loop'] = wp_parse_args( $args, $default_args );
	}
	add_action( 'woocommerce_before_shop_loop', 'woodmart_setup_loop', 10 );
	add_action( 'wp', 'woodmart_setup_loop', 50 );
	add_action( 'loop_start', 'woodmart_setup_loop', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Hide woocommerce notice
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_hide_outdated_templates_notice' ) ) {
	function woodmart_hide_outdated_templates_notice( $value, $notice ) {
		if ( 'template_files' === $notice ) {
			return false;
		}

		return $value;
	}

	add_filter( 'woocommerce_show_admin_notice', 'woodmart_hide_outdated_templates_notice', 2, 10 );
}

if ( ! function_exists( 'woodmart_single_product_thumbnails_gallery_image_width' ) ) {
	/**
	 * Change default `gallery_thumbnail` size values
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function woodmart_single_product_thumbnails_gallery_image_width() {
		if ( woodmart_get_opt( 'single_product_thumbnails_gallery_image_width' ) ) {
			$size = array(
				'width'  => (int) woodmart_get_opt( 'single_product_thumbnails_gallery_image_width' ),
				'height' => 0,
				'crop'   => 0,
			);
		} else {
			$size = wc_get_image_size( 'woocommerce_thumbnail' );
		}

		if ( isset( $size['height'] ) && ! $size['height'] ) {
			$size['height'] = 0;
		}

		return $size;
	}

	add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'woodmart_single_product_thumbnails_gallery_image_width', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Change single product notice position
 * ------------------------------------------------------------------------------------------------
 */
remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );

add_action( 'woodmart_before_single_product_summary_wrap', 'woocommerce_output_all_notices', 10 );
add_action( 'woodmart_before_shop_page', 'woocommerce_output_all_notices', 10 );

if ( ! function_exists( 'woodmart_demo_storage_filter' ) ) {
	/**
	 * This function added inline style for WooCommerce demo store option.
	 * You can see it in Theme Customize when active Customizing ▸ WooCommerce ▸ Store Notice ▸ Enable store notice.
	 *
	 * @param string $content demo store content.
	 * @return string
	 */
	function woodmart_demo_storage_filter( $content ) {
		woodmart_enqueue_inline_style( 'woo-opt-demo-store' );
		return $content;
	}

	add_filter( 'woocommerce_demo_store', 'woodmart_demo_storage_filter' );
}

if ( ! function_exists( 'woodmart_get_recently_viewed_products' ) ) {
	/**
	 * Update AJAX recently viewed products.
	 *
	 * @return void
	 */
	function woodmart_get_recently_viewed_products() {
		if ( ! isset( $_POST['attr'] ) ) { //phpcs:ignore
			wp_send_json_error( 'No attributes found' );
		}

		$attributes = array();

		foreach ( $_POST['attr'] as $key => $value ) { //phpcs:ignore
			if ( 'inner_content' === $key ) {
				$unescaped = stripslashes( $value );

				$attributes[ $key ] = wp_kses_post( html_entity_decode( $unescaped, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

				continue;
			}

			$attributes[ $key ] = woodmart_clean( $value );
		}

		if ( 'elementor' === woodmart_get_current_page_builder() ) {
			$result = woodmart_elementor_products_template( $attributes );
		} else {
			$result = woodmart_shortcode_products( $attributes );
		}

		wp_send_json( $result );
	}

	add_action( 'wp_ajax_woodmart_get_recently_viewed_products', 'woodmart_get_recently_viewed_products' );
	add_action( 'wp_ajax_nopriv_woodmart_get_recently_viewed_products', 'woodmart_get_recently_viewed_products' );
}

if ( ! function_exists( 'woodmart_get_availability_stock_status' ) ) {
	/**
	 * Get availability option in stock status.
	 *
	 * @param array $availability Availability of the product.
	 * @return array
	 */
	function woodmart_get_availability_stock_status( $availability ) {
		if ( ! isset( $availability['availability'] ) || ! $availability['availability'] ) {
			return $availability;
		}

		$stock_status_design = woodmart_get_opt( 'stock_status_design', 'default' );

		if ( isset( $availability['class'] ) ) {
			$availability['class'] .= ' wd-style-' . $stock_status_design;
		}

		if ( in_array( $stock_status_design, array( 'with-bg', 'bordered' ) ,true ) ) {
			$availability['availability'] = '<span>' . $availability['availability'] . '</span>';
		}

		return $availability;
	}

	add_filter( 'woocommerce_get_availability', 'woodmart_get_availability_stock_status' );
}

if ( ! function_exists( 'woodmart_get_extra_description_category' ) ) {
	/**
	 * Output extra content description.
	 */
	function woodmart_get_extra_description_category() {
		$item    = get_queried_object();
		$content = '';

		if ( ! isset( $item->term_id ) || is_search() || ! in_array( absint( get_query_var( 'paged' ) ), array( 0, 1 ), true ) || ! is_product_taxonomy() ) {
			return;
		}

		$content_type = get_term_meta( $item->term_id, 'category_extra_description_type', true );

		if ( 'text' === $content_type ) {
			$content = wpautop( get_term_meta( $item->term_id, 'category_extra_description_text', true ) );
		} elseif ( 'html_block' === $content_type ) {
			$content = get_term_meta( $item->term_id, 'category_extra_description_html_block', true );
		}

		if ( ! $content ) {
			return;
		}

		?>
		<div class="wd-term-desc wd-entry-content">
			<?php if ( 'html_block' === $content_type ) : ?>
				<?php echo woodmart_get_html_block( $content ); // phpcs:ignore ?>
			<?php else : ?>
				<?php echo do_shortcode( $content ); ?>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_enqueue_js_files_for_shop' ) ) {
	/**
	 * Enqueue JS files for shop page with shop page display type category.
	 *
	 * @return void
	 */
	function woodmart_enqueue_js_files_for_shop() {
		if ( ! woodmart_get_opt( 'ajax_shop' ) || ! woodmart_woocommerce_installed() || ( ! is_shop() && ! is_product_category() ) ) {
			return;
		}

		$display_type = '';

		if ( is_shop() ) {
			$display_type = get_option( 'woocommerce_shop_page_display', '' );
		} elseif ( is_product_category() ) {
			$parent_id    = get_queried_object_id();
			$display_type = get_term_meta( $parent_id, 'display_type', true );
			$display_type = '' === $display_type ? get_option( 'woocommerce_category_archive_display', '' ) : $display_type;
		}

		if ( 'subcategories' !== $display_type ) {
			return;
		}

		$hover = woodmart_get_opt( 'products_hover' );

		if ( 'base' === $hover || 'fw-button' === $hover ) {
			wp_enqueue_script( 'imagesloaded' );
			woodmart_enqueue_js_script( 'product-hover' );
			woodmart_enqueue_js_script( 'product-more-description' );
		}

		if ( woodmart_get_opt( 'product_quantity' ) && ! woodmart_get_opt( 'catalog_mode' ) && in_array( $hover, array( 'fw-button', 'list', 'quick', 'standard' ), true ) ) {
			woodmart_enqueue_js_script( 'grid-quantity' );
		}

		if ( woodmart_get_opt( 'wishlist' ) ) {
			woodmart_enqueue_js_library( 'tooltips' );
			woodmart_enqueue_js_script( 'btns-tooltips' );
			woodmart_enqueue_js_script( 'wishlist' );

			if ( woodmart_get_opt( 'wishlist_expanded' ) && 'disable' !== woodmart_get_opt( 'wishlist_show_popup', 'disable' ) && is_user_logged_in() ) {
				woodmart_enqueue_js_script( 'wishlist-group' );
			}
		}

		if ( woodmart_get_opt( 'compare' ) ) {
			woodmart_enqueue_js_library( 'tooltips' );
			woodmart_enqueue_js_script( 'btns-tooltips' );
			woodmart_enqueue_js_script( 'compare' );
		}

		if ( woodmart_get_opt( 'quick_view' ) ) {
			woodmart_enqueue_js_library( 'magnific' );
			woodmart_enqueue_js_script( 'product-images-gallery' );
			woodmart_enqueue_js_script( 'quick-view' );
			woodmart_enqueue_js_library( 'tooltips' );
			woodmart_enqueue_js_script( 'btns-tooltips' );
			woodmart_enqueue_js_script( 'swatches-variations' );
			woodmart_enqueue_js_script( 'add-to-cart-all-types' );
			woodmart_enqueue_js_script( 'woocommerce-quantity' );
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'imagesloaded' );

			if ( woodmart_get_opt( 'single_product_swatches_limit' ) ) {
				woodmart_enqueue_js_script( 'swatches-limit' );
			}
		}

		if ( 'nothing' !== woodmart_get_opt( 'add_to_cart_action' ) ) {
			woodmart_enqueue_js_script( 'action-after-add-to-cart' );
		}

		if ( 'popup' === woodmart_get_opt( 'add_to_cart_action' ) ) {
			woodmart_enqueue_js_library( 'magnific' );
		}
	}

	add_action( 'wp', 'woodmart_enqueue_js_files_for_shop', 20 );
}

if ( ! function_exists( 'woodmart_get_products_attributes' ) ) {
	/**
	 * Get all product attributes.
	 *
	 * @return array
	 */
	function woodmart_get_products_attributes() {
		if ( ! woodmart_woocommerce_installed() ) {
			return array();
		}

		$taxonomies           = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		$taxonomies['weight']     = esc_html__( 'Weight', 'woocommerce' );
		$taxonomies['dimensions'] = esc_html__( 'Dimensions', 'woocommerce' );

		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$taxonomies[ 'pa_' . $tax->attribute_name ] = $tax->attribute_label . ' (pa_' . $tax->attribute_name . ')';
			}
		}

		return $taxonomies;
	}
}

if ( ! function_exists( 'woodmart_filters_get_page_base_url' ) ) {
	/**
	 * Get current page URL for layered nav items.
	 *
	 * @return string
	 */
	function woodmart_filters_get_page_base_url() {
		$link = '';

		if ( Automattic\Jetpack\Constants::is_defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_shop() ) {
			$link = get_permalink( wc_get_page_id( 'shop' ) );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} elseif ( get_queried_object() ) {
			$queried_object = get_queried_object();
			$link           = get_term_link( $queried_object->slug, $queried_object->taxonomy );
		}

		if ( is_wp_error( $link ) ) {
			$link = get_permalink( wc_get_page_id( 'shop' ) );
		}

		// Min/Max.
		if ( isset( $_GET['min_price'] ) ) {
			$link = add_query_arg( 'min_price', wc_clean( wp_unslash( $_GET['min_price'] ) ), $link );
		}

		if ( isset( $_GET['max_price'] ) ) {
			$link = add_query_arg( 'max_price', wc_clean( wp_unslash( $_GET['max_price'] ) ), $link );
		}

		// Order by.
		if ( isset( $_GET['orderby'] ) ) {
			$link = add_query_arg( 'orderby', wc_clean( wp_unslash( $_GET['orderby'] ) ), $link );
		}

		/**
		 * Search Arg.
		 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
		 */
		if ( get_search_query() ) {
			$link = add_query_arg( 's', rawurlencode( htmlspecialchars_decode( get_search_query() ) ), $link );
		}

		// Post Type Arg
		if ( isset( $_GET['post_type'] ) ) {
			$link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link );

			// Prevent post type and page id when pretty permalinks are disabled.
			if ( is_shop() ) {
				$link = remove_query_arg( 'page_id', $link );
			}
		}

		// Min Rating Arg
		if ( isset( $_GET['rating_filter'] ) ) {
			$link = add_query_arg( 'rating_filter', wc_clean( wp_unslash( $_GET['rating_filter'] ) ), $link );
		}

		// All current filters.
		$_chosen_attributes = \WC_Query::get_layered_nav_chosen_attributes();

		if ( ! empty( $_GET['filter_product_brand'] ) ) {
			$filter_product_brand = woodmart_clean( wp_unslash( $_GET['filter_product_brand'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$_chosen_attributes['product_brand']['terms']      = array_map( 'sanitize_title', explode( ',', $filter_product_brand ) );
			$_chosen_attributes['product_brand']['query_type'] = ! empty( $_GET['query_type_product_brand'] ) && in_array( $_GET[ 'query_type_product_brand' ], array( 'and', 'or' ), true ) ? wc_clean( wp_unslash( $_GET['query_type_product_brand'] ) ) : apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
		}

		if ( $_chosen_attributes ) {
			foreach ( $_chosen_attributes as $name => $data ) {
				$filter_name = wc_attribute_taxonomy_slug( $name );
				if ( ! empty( $data['terms'] ) ) {
					$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
				}
				if ( 'or' === $data['query_type'] ) {
					$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
				}
			}
		}

		return $link;
	}
}

if ( ! function_exists( 'woodmart_enqueue_emails_styles' ) ) {
	/**
     * Add custom CSS for Woodmart emails.
     *
     * @param string   $css WooCommerce email CSS code.
     * @param WC_Email $email Email object.
     *
     * @return string
     */
	function woodmart_enqueue_emails_styles( $css, $email ) {
		$woodmart_emails_list = apply_filters( 'woodmart_emails_list', array() );

		if ( in_array( get_class( $email ), $woodmart_emails_list, true ) ) {
			ob_start();
			wc_get_template( 'emails/wd-email-styles.php' );
			$css .= ob_get_clean();
		}

		return $css;
	}

	add_filter( 'woocommerce_email_styles', 'woodmart_enqueue_emails_styles', 10, 2 );
}

if ( ! function_exists( 'woodmart_set_cookie' ) ) {
	/**
	 * Set cookies.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name.
	 * @param string $value Value.
	 */
	function woodmart_set_cookie( $name, $value ) {
		$expire = time() + intval( apply_filters( 'woodmart_session_expiration', 60 * 60 * 24 * 7 ) );
		setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, woodmart_cookie_secure_param(), false );
		$_COOKIE[ $name ] = $value;
	}
}

if ( ! function_exists( 'woodmart_get_cookie' ) ) {
	/**
	 * Get cookie.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name.
	 *
	 * @return string
	 */
	function woodmart_get_cookie( $name ) {
		return isset( $_COOKIE[ $name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ) : false; // phpcs:ignore
	}
}

if ( ! function_exists( 'woodmart_settings_css' ) ) {
	/**
	 * Add custom CSS for product page.
	 *
	 * @return void
	 */
	function woodmart_settings_css() {
		$custom_product_background = get_post_meta( get_the_ID(), '_woodmart_product-background', true );

		echo '<style>';

		?>

		<?php if ( ! empty( $custom_product_background ) ) : ?>
			.single-product .wd-page-content{
			background-color: <?php echo esc_html( $custom_product_background ); ?> !important;
			}
		<?php endif ?>

		<?php

		echo '</style>';
	}
	add_action( 'wp_head', 'woodmart_settings_css', 200 );
}

if ( ! function_exists( 'woodmart_exclude_post_meta_from_woocommerce_app' ) ) {
	/**
	 * Exclude post meta from WooCommerce app.
	 *
	 * @param object $response The response object.
	 * @param object $product The product object.
	 * @param object $request The request object.
	 * @return object
	 */
	function woodmart_exclude_post_meta_from_woocommerce_app( $response, $product, $request ) {
		$excluded_meta_keys = array(
			'wd_page_css_files',
			'wd_page_css_files_mobile',
		);

		if ( isset( $response->data['meta_data'] ) && is_array( $response->data['meta_data'] ) ) {
			foreach ( $response->data['meta_data'] as $index => $meta ) {
				if ( is_object( $meta ) && isset( $meta->key ) && in_array( $meta->key, $excluded_meta_keys, true ) ) {
					unset( $response->data['meta_data'][ $index ] );
				} elseif ( is_array( $meta ) && isset( $meta['key'] ) && in_array( $meta['key'], $excluded_meta_keys, true ) ) {
					unset( $response->data['meta_data'][ $index ] );
				}
			}

			$response->data['meta_data'] = array_values( $response->data['meta_data'] );
		}

		return $response;
	}

	add_filter( 'woocommerce_rest_prepare_product_object', 'woodmart_exclude_post_meta_from_woocommerce_app', 10, 3 );
}

if ( ! function_exists( 'woodmart_change_product_image_with_active_filter' ) ) {
	/**
	 * Change product image with active filter attribute.
	 *
	 * @param string $image_html Image HTML.
	 * @param object $product Product object.
	 * @param string $size Image size.
	 * @param int    $attach_id Attachment ID.
	 * @return string
	 */
	function woodmart_change_product_image_with_active_filter( $image_html, $product, $size, $attach_id ) {
		if ( woodmart_get_opt( 'show_filtered_variation_image' ) && $product && $product->is_type( 'variable' ) && $product->get_image_id() === $attach_id ) {
			$chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

			if ( ! $chosen_attributes ) {
				return $image_html;
			}

			$product_attributes = $product->get_attributes();
			$valid_attributes   = false;

			foreach ( $chosen_attributes as $attribute => $data ) {
				if ( ! empty( $product_attributes[ $attribute ] ) ) {
					$valid_attributes = true;
					break;
				}
			}

			if ( ! $valid_attributes ) {
				return $image_html;
			}

			$variation_ids = $product->get_children();

			foreach ( $variation_ids as $variation_id ) {
				$variation = wc_get_product( $variation_id );

				if ( $variation ) {
					$attributes = $variation->get_variation_attributes();
					foreach ( $chosen_attributes as $attribute => $data ) {
						if ( isset( $attributes[ 'attribute_' . $attribute ] ) && in_array( $attributes[ 'attribute_' . $attribute ], $data['terms'], true ) ) {
							if ( $variation->get_image_id() ) {
								return wp_get_attachment_image( $variation->get_image_id(), $size );
							}
						}
					}
				}
			}
		}

		return $image_html;
	}

	add_filter( 'woodmart_get_product_thumbnail', 'woodmart_change_product_image_with_active_filter', 10, 4 );
}

if ( ! function_exists( 'woodmart_enqueue_track_order_style' ) ) {
	/**
	 * Enqueue track order style.
	 *
	 * @return void
	 */
	function woodmart_enqueue_track_order_style() {
		woodmart_enqueue_inline_style( 'woo-el-track-order' );
	}

	add_action( 'woocommerce_order_tracking_form_start', 'woodmart_enqueue_track_order_style' );
}
