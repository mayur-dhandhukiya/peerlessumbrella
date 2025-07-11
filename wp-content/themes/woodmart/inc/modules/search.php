<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

// **********************************************************************//
// Search full screen
// **********************************************************************//
if( ! function_exists( 'woodmart_search_full_screen' ) ) {
	function woodmart_search_full_screen() {

		if ( ! woodmart_is_header_frontend_editor() && ( is_admin() || defined( 'IFRAME_REQUEST' ) ) ) {
			return;
		}

		$settings = whb_get_settings();

		$desktop_search_design = '';
		$mobile_search_design  = '';

		if ( isset( $settings['search']['display'] ) ) {
			$desktop_search_design = $settings['search']['display'];
		}

		if ( isset( $settings['mobilesearch']['display'] ) ) {
			$mobile_search_design = $settings['mobilesearch']['display'];
		}

		if ( 'full-screen' === $desktop_search_design || 'full-screen-2' === $desktop_search_design ) {
			$search_args['type']             = $settings['search']['display'];
			$search_args['post_type']        = $settings['search']['post_type'];
			$search_args['ajax']             = $settings['search']['ajax'];
			$search_args['count']            = ( isset( $settings['search']['ajax_result_count'] ) && $settings['search']['ajax_result_count'] ) ? $settings['search']['ajax_result_count'] : 40;
			$search_args['popular_requests'] = isset( $settings['search']['popular_requests'] ) ? $settings['search']['popular_requests'] : '';

			if ( 'full-screen-2' === $search_args['type'] ) {
				$search_args['show_categories']    = isset( $settings['search']['categories_dropdown'] ) ? $settings['search']['categories_dropdown'] : '';
				$search_args['cat_selector_style'] = isset( $settings['search']['cat_selector_style'] ) ? $settings['search']['cat_selector_style'] : '';
			}

			woodmart_search_form( $search_args );
		}

		if ( ( 'full-screen' === $mobile_search_design || 'full-screen-2' === $mobile_search_design ) && $desktop_search_design !== $mobile_search_design ) {
			$search_args['type'] = $settings['mobilesearch']['display'];

			if ( isset( $settings['search']['ajax'] ) ) {
				$search_args['ajax'] = $settings['search']['ajax'];
			}

			if ( isset( $settings['mobilesearch']['post_type'] ) ) {
				$search_args['post_type'] = $settings['mobilesearch']['post_type'];
			}

			woodmart_search_form( $search_args );
		}
	}

	add_action( 'woodmart_before_wp_footer', 'woodmart_search_full_screen', 100 );
}

// **********************************************************************//
// Search form
// **********************************************************************//
if( ! function_exists( 'woodmart_search_form' ) ) {
	function woodmart_search_form( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'ajax' => false,
			'post_type' => 'post',
			'show_categories' => false,
			'type' => 'form',
			'thumbnail' => true,
			'price' => true,
			'count' => 20,
			'icon_type' => '',
			'search_style' => 'default',
			'custom_icon' => '',
			'el_classes' => '',
			'wrapper_custom_classes' => '',
			'popular_requests' => false,
			'cat_selector_style' => 'bordered',
			'el_id' => '',
		) );

		$args = apply_filters( 'woodmart_search_form_args', $args );

		extract( $args );

		ob_start();

		$class             = '';
		$btn_classes       = '';
		$data              = '';
		$wrapper_classes   = '';
		$dropdowns_classes = '';
		$wrapper_atts      = '';

		if ( $el_id ) {
			$wrapper_atts .= ' id="' . $el_id . '"';
		}

		if ( $show_categories && $post_type == 'product' ) {
			$class .= ' wd-with-cat';
			$class .= woodmart_get_old_classes( ' has-categories-dropdown' );
		}

		if ( $icon_type == 'custom' ) {
			$btn_classes .= ' wd-with-img';
			$btn_classes .= woodmart_get_old_classes( ' woodmart-searchform-custom-icon' );
		}

		if ( 'full-screen-2' === $type ) {
			$search_style = 'with-bg';
		}

		if ( $search_style ) {
			$class .= ' wd-style-' . $search_style;
			$class .= woodmart_get_old_classes( ' search-style-' . $search_style );
		}

		if ( $show_categories && $cat_selector_style ) {
			$class .= ' wd-cat-style-' . $cat_selector_style;
		}

		$ajax_args = array(
			'thumbnail' => $thumbnail,
			'price' => $price,
			'post_type' => $post_type,
			'count' => $count,
			'sku' => woodmart_get_opt( 'show_sku_on_ajax' ) ? '1' : '0',
			'symbols_count' => apply_filters( 'woodmart_ajax_search_symbols_count', 3 ),
		);

		if( $ajax ) {
			woodmart_enqueue_inline_style( 'wd-search-results' );

			woodmart_enqueue_js_library( 'autocomplete' );
			woodmart_enqueue_js_script( 'ajax-search' );

			$class .= ' woodmart-ajax-search';

			foreach ( $ajax_args as $key => $value ) {
				$data .= ' data-' . $key . '="' . $value . '"';
			}
		}

		switch ( $post_type ) {
			case 'product':
				$placeholder = esc_attr_x( 'Search for products', 'submit button', 'woodmart' );
				$description = esc_html__( 'Start typing to see products you are looking for.', 'woodmart' );
			break;

			case 'portfolio':
				$placeholder = esc_attr_x( 'Search for projects', 'submit button', 'woodmart' );
				$description = esc_html__( 'Start typing to see projects you are looking for.', 'woodmart' );
			break;

			case 'page':
				$placeholder = esc_attr_x( 'Search for pages', 'submit button', 'woodmart' );
				$description = esc_html__( 'Start typing to see pages you are looking for.', 'woodmart' );
				break;

			default:
				$placeholder = esc_attr_x( 'Search for posts', 'submit button', 'woodmart' );
				$description = esc_html__( 'Start typing to see posts you are looking for.', 'woodmart' );
			break;
		}

		if ( $el_classes ) {
			$class .= ' ' . $el_classes;
		}

		if ( $wrapper_custom_classes ) {
			$wrapper_classes .= ' ' . $wrapper_custom_classes;
		}

		if ( 'dropdown' === $type ) {
			$wrapper_classes .= ' wd-dropdown';
		}

		if ( 'full-screen' === $type || 'full-screen-2' === $type ) {
			woodmart_enqueue_js_script( 'search-full-screen' );
			woodmart_enqueue_inline_style( 'header-search-fullscreen' );
			$wrapper_classes .= ' wd-fill';

			if ( 'full-screen' === $type ) {
				woodmart_enqueue_inline_style( 'header-search-fullscreen-1' );
			} else {
				woodmart_enqueue_inline_style( 'header-search-fullscreen-2' );
			}
		} else {
			$dropdowns_classes .= ' wd-dropdown';
		}

		if ( 'light' === whb_get_dropdowns_color() ) {
			if ( 'form' !== $type ) {
				$wrapper_classes .= ' color-scheme-light';
			}
			$dropdowns_classes .= ' color-scheme-light';
		}

		$popular_search_requests = '';

		if ( $popular_requests ) {
			$request = woodmart_get_opt( 'popular_requests' );

			if ( $request ) {
				woodmart_enqueue_inline_style( 'popular-requests' );

				$wrapper_classes        .= ' wd-requests-enabled';
				$popular_search_requests = explode( "\n", $request );
			}
		}

		$full_search_content = '';

		if ( 'full-screen' === $type || 'full-screen-2' === $type ) {
			if ( 'text' === woodmart_get_opt( 'full_search_content_type', 'content' ) && woodmart_get_opt( 'full_search_content_text' ) ) {
				$full_search_content = 'text';
			} elseif ( 'content' === woodmart_get_opt( 'full_search_content_type', 'content' ) && woodmart_get_opt( 'full_search_content_html_block' ) ) {
				$full_search_content = 'content';
			}
		}

		$wrapper_classes   .= woodmart_get_old_classes( ' woodmart-search-' . $type );
		$dropdowns_classes .= woodmart_get_old_classes( ' woodmart-search-results' );

		if ( $full_search_content && woodmart_get_opt( 'ajax_fullscreen_content', true ) ) {
			$wrapper_classes .= ' wd-ajax-search-content';
		}

		woodmart_enqueue_inline_style( 'wd-search-form' );

		if ( 'full-screen' !== $type ) {
			woodmart_enqueue_js_script( 'clear-search' );
		}
		?>
			<div class="wd-search-<?php echo esc_attr( $type ); ?><?php echo esc_html( $wrapper_classes ); ?>"<?php echo wp_kses( $wrapper_atts, true ); ?>>
				<?php if ( 'full-screen' === $type || 'full-screen-2' === $type ) : ?>
					<span class="wd-close-search wd-action-btn wd-style-icon wd-cross-icon<?php echo woodmart_get_old_classes( ' woodmart-close-search' ); ?>"><a href="#" rel="nofollow" aria-label="<?php esc_attr_e( 'Close search form', 'woodmart' ); ?>"></a></span>
				<?php endif ?>

				<?php if ( 'full-screen-2' === $type ) : ?>
					<div class="container">
				<?php endif; ?>

				<form role="search" method="get" class="searchform <?php echo esc_attr( $class ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo ! empty( $data ) ? $data : ''; ?>>
					<input type="text" class="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo get_search_query(); ?>" name="s" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>" title="<?php echo esc_attr( $placeholder ); ?>"<?php echo esc_attr( apply_filters( 'woodmart_show_required_in_search_form', true ) ? ' required' : '' ); ?>/>
					<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
					<?php if ( 'full-screen' !== $type ) : ?>
						<span class="wd-clear-search<?php echo get_search_query() ? '' : ' wd-hide'; ?>"></span>
					<?php endif; ?>
					<?php if( $show_categories && $post_type == 'product' ) woodmart_show_categories_dropdown(); ?>
					<button type="submit" class="searchsubmit<?php echo esc_attr( $btn_classes ); ?>">
						<span>
							<?php echo esc_attr_x( 'Search', 'submit button', 'woodmart' ); ?>
						</span>
						<?php
							if ( $icon_type == 'custom' ) {
								echo whb_get_custom_icon( $custom_icon );
							}
						?>
					</button>
				</form>

				<?php if ( $popular_search_requests ) : ?>
					<div class="wd-search-requests">
						<span class="wd-search-requests-text title"><?php echo esc_html__( 'Popular requests: ', 'woodmart' ); ?></span>
						<ul>
							<?php foreach ( $popular_search_requests as $request ) : ?>
								<li>
									<a href="<?php echo esc_url( get_site_url() . '/?s=' . rawurlencode( trim( $request ) ) . '&post_type=' . $post_type ); ?>">
										<?php echo esc_html( $request ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( 'full-screen' === $type ) : ?>
					<div class="wd-search-loader wd-fill<?php echo esc_attr( woodmart_get_old_classes( ' woodmart-search-loader' ) ); ?>"></div>
					<div class="search-info-text"><span><?php echo esc_html( $description ); ?></span></div>
				<?php endif ?>

				<?php if ( $ajax ) : ?>
					<div class="search-results-wrapper">
						<div class="wd-dropdown-results wd-scroll<?php echo esc_attr( $dropdowns_classes ); ?>">
							<div class="wd-scroll-content"></div>
						</div>
					</div>
				<?php endif ?>

				<?php if ( $full_search_content ) : ?>
					<div class="wd-search-area wd-scroll">
						<?php if ( ! woodmart_get_opt( 'ajax_fullscreen_content', true ) ) : ?>
							<?php woodmart_get_full_search_area_content( $full_search_content ); ?>
						<?php endif; ?>
					</div>
				<?php endif ?>

				<?php if ( 'full-screen-2' === $type ) : ?>
					</div>
				<?php endif; ?>
			</div>
		<?php

		echo apply_filters( 'get_search_form', ob_get_clean(), $args );
	}
}

if ( ! function_exists( 'woodmart_get_full_search_area_content' ) ) {
	/**
	 * Get full search area content.
	 *
	 * @param string  $full_search_content Content type.
	 * @param boolean $return Return.
	 * @return false|string|void
	 */
	function woodmart_get_full_search_area_content( $full_search_content, $return = false ) {
		if ( $return ) {
			ob_start();
		}

		?>
		<div class="wd-search-area-inner wd-scroll-content wd-entry-content">
			<?php if ( 'text' === $full_search_content ) : ?>
				<?php echo do_shortcode( woodmart_get_opt( 'full_search_content_text' ) ); ?>
			<?php elseif ( 'content' === $full_search_content ) : ?>
				<?php echo woodmart_get_html_block( woodmart_get_opt( 'full_search_content_html_block' ) ); //phpcs:ignore ?>
			<?php endif; ?>
		</div>
		<?php

		if ( $return ) {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'woodmart_load_full_search_html' ) ) {
	/**
	 * Ajax load full search area content.
	 *
	 * @return void
	 */
	function woodmart_load_full_search_html() {
		if ( class_exists( 'WPBMap' ) ) {
			WPBMap::addAllMappedShortcodes();
		}

		$content = woodmart_get_full_search_area_content( woodmart_get_opt( 'full_search_content_type', 'content' ), true );

		wp_send_json(
			array(
				'content' => $content,
			)
		);
	}

	add_action( 'wp_ajax_woodmart_load_full_search_html', 'woodmart_load_full_search_html' );
	add_action( 'wp_ajax_nopriv_woodmart_load_full_search_html', 'woodmart_load_full_search_html' );
}

if( ! function_exists( 'woodmart_show_categories_dropdown' ) ) {
	function woodmart_show_categories_dropdown() {
		woodmart_enqueue_inline_style( 'wd-search-cat' );

		$args = array(
			'hide_empty' => 1,
			'parent' => 0
		);
		$terms = get_terms('product_cat', apply_filters( 'woodmart_header_search_categories_dropdown_args', $args ) );
		if( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$dropdown_classes = '';

			if ( 'light' === whb_get_dropdowns_color() ) {
				$dropdown_classes .= ' color-scheme-light';
			}

			$dropdown_classes .= woodmart_get_old_classes( ' list-wrapper' );

			woodmart_enqueue_js_script( 'simple-dropdown' );
			?>
			<div class="wd-search-cat wd-scroll<?php echo woodmart_get_old_classes( ' search-by-category' ); ?>">
				<input type="hidden" name="product_cat" value="0">
				<a href="#" rel="nofollow" data-val="0">
					<span>
						<?php esc_html_e( 'Select category', 'woodmart' ); ?>
					</span>
				</a>
				<div class="wd-dropdown wd-dropdown-search-cat wd-dropdown-menu wd-scroll-content wd-design-default<?php echo esc_attr( $dropdown_classes ); ?>">
					<ul class="wd-sub-menu<?php echo woodmart_get_old_classes( ' sub-menu' ); ?>">
						<li style="display:none;"><a href="#" data-val="0"><?php esc_html_e('Select category', 'woodmart'); ?></a></li>
						<?php
							if( ! apply_filters( 'woodmart_show_only_parent_categories_dropdown', false ) ) {
						        $args = array(
						            'title_li' => false,
									'taxonomy' => 'product_cat',
									'use_desc_for_title' => false,
						            'walker' => new WOODMART_Custom_Walker_Category(),
						        );
						        wp_list_categories($args);
							} else {
							    foreach ( $terms as $term ) {
							        ?>
										<li><a href="#" data-val="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
							        <?php
							    }
							}
						?>
					</ul>
				</div>
			</div>
			<?php
		}
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Blog results on search page
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_show_blog_results_on_search_page' ) ) {
	function woodmart_show_blog_results_on_search_page() {
		if ( ! is_search() || ! woodmart_get_opt( 'enqueue_posts_results' ) ) {
			return;
		}

		$search_query = get_search_query();
		$column       = woodmart_get_opt( 'search_posts_results_column' );
		$blog_results = woodmart_shortcode_blog(
			array(
				'slides_per_view' => $column,
				'blog_design'     => 'carousel',
				'search'          => $search_query,
				'items_per_page'  => 10,
			)
		);

		if ( empty( $blog_results ) ) {
			return;
		}

		$show_all_url = add_query_arg(
			array(
				's'         => esc_attr( $search_query ),
				'post_type' => 'post',
			),
			home_url()
		);

		ob_start();
		?>
		<div class="wd-blog-search-results">
			<h4 class="wd-el-title slider-title">
				<span><?php esc_html_e( 'Results from blog', 'woodmart' ); ?></span>
			</h4>

			<?php echo $blog_results; // phpcs:ignore. ?>

			<div class="wd-search-show-all">
				<a href="<?php echo esc_url( $show_all_url ); ?>" class="button">
					<?php esc_html_e( 'Show all blog results', 'woodmart' ); ?>
				</a>
			</div>
		</div>
		<?php

		echo ob_get_clean(); // phpcs:ignore.
	}

	add_action( 'woocommerce_after_shop_loop', 'woodmart_show_blog_results_on_search_page', 100 );
	add_action( 'woodmart_after_portfolio_loop', 'woodmart_show_blog_results_on_search_page', 100 );
	add_action( 'woodmart_after_no_product_found', 'woodmart_show_blog_results_on_search_page', 100 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Ajax search
 * ------------------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'woodmart_init_search_by_sku' ) ) {
	function woodmart_init_search_by_sku() {
		if ( apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) && woodmart_woocommerce_installed() ) {
			add_filter( 'posts_search', 'woodmart_product_search_sku', 9 );
		}
	}

	add_action( 'init', 'woodmart_init_search_by_sku', 10 );
}

if ( ! function_exists( 'woodmart_ajax_suggestions' ) ) {
	function woodmart_ajax_suggestions() {

		$allowed_types = array( 'post', 'product', 'portfolio', 'any', 'page' );
		$post_type = 'product';

		if ( apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) && woodmart_woocommerce_installed() ) {
			add_filter( 'posts_search', 'woodmart_product_ajax_search_sku', 10 );
		}

		$query_args = array(
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'post_type'      => $post_type,
			'no_found_rows'  => 1,
		);

		if ( ! empty( $_REQUEST['post_type'] ) && in_array( $_REQUEST['post_type'], $allowed_types ) ) {
			$post_type = strip_tags( $_REQUEST['post_type'] );
			$query_args['post_type'] = $post_type;
		}

		if ( $post_type == 'product' && woodmart_woocommerce_installed() ) {

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$query_args['tax_query']['relation'] = 'AND';

			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $product_visibility_term_ids['exclude-from-search'],
				'operator' => 'NOT IN',
			);

			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => array( 'exclude-from-search' ),
				'operator' => 'NOT IN',
			);

			if ( apply_filters( 'woodmart_ajax_search_product_cat_args_old_style', false ) ) {
				if ( ! empty( $_REQUEST['product_cat'] ) ) {
					$query_args['product_cat'] = strip_tags( $_REQUEST['product_cat'] );
				}
			} else {
				if ( ! empty( $_REQUEST['product_cat'] ) ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => strip_tags( $_REQUEST['product_cat'] ),
					);
				}
			}
		}

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && $post_type == 'product' ) {
			$query_args['meta_query'][] = array( 'key' => '_stock_status', 'value' => 'outofstock', 'compare' => 'NOT IN' );
		}

		if ( ! empty( $_REQUEST['query'] ) ) {
			$query_args['s'] = sanitize_text_field( $_REQUEST['query'] );
		}

		if ( ! empty( $_REQUEST['number'] ) ) {
			$query_args['posts_per_page'] = (int) $_REQUEST['number'];
		}

		$query_args = apply_filters( 'woodmart_ajax_search_args', $query_args, $post_type );

		$results = new WP_Query( $query_args );

		if ( woodmart_get_opt( 'relevanssi_search' ) && function_exists( 'relevanssi_do_query' ) ) {
			add_filter( 'relevanssi_hits_filter', 'woodmart_update_hits_filter_by_product_sku', 10, 2 );
			relevanssi_do_query( $results );
		}

		$suggestions = array();

		if ( $results->have_posts() ) {

			if ( $post_type == 'product' && woodmart_woocommerce_installed() ) {
				$factory = new WC_Product_Factory();
			}

			while ( $results->have_posts() ) {
				$results->the_post();

				if ( $post_type == 'product' && woodmart_woocommerce_installed() ) {
					$product = $factory->get_product( get_the_ID() );

					$suggestions[] = array(
						'value' => html_entity_decode( get_the_title() ),
						'permalink' => get_the_permalink(),
						'price' => $product->get_price_html(),
						'thumbnail' => $product->get_image(),
						'sku' => $product->get_sku() ? esc_html__( 'SKU:', 'woodmart' ) . ' ' . $product->get_sku() : '',
					);
				} else {
					$suggestions[] = array(
						'value' => html_entity_decode( get_the_title() ),
						'permalink' => get_the_permalink(),
						'thumbnail' => get_the_post_thumbnail( null, 'medium', '' ),
					);
				}
			}

			wp_reset_postdata();
		} else {
			$suggestions[] = array(
				'value'              => ( $post_type == 'product' ) ? esc_html__( 'No products found', 'woodmart' ) : esc_html__( 'No posts found', 'woodmart' ),
				'products_not_found' => true,
				'permalink'          => ''
			);
		}

		if ( woodmart_get_opt( 'enqueue_posts_results' ) && 'post' !== $post_type ) {
			$post_suggestions = woodmart_get_post_suggestions();
			$suggestions = array_merge( $suggestions, $post_suggestions );
		}

		if ( 1 === count( $suggestions ) && isset( $suggestions[0]['products_not_found'] ) ) {
			$suggestions[0]['no_results'] = true;
		}

		wp_send_json(
			array(
				'suggestions' => $suggestions,
			)
		);
	}

	add_action( 'wp_ajax_woodmart_ajax_search', 'woodmart_ajax_suggestions', 10 );
	add_action( 'wp_ajax_nopriv_woodmart_ajax_search', 'woodmart_ajax_suggestions', 10 );
}

if ( ! function_exists( 'woodmart_get_post_suggestions' ) ) {
	function woodmart_get_post_suggestions() {
		$query_args = array(
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'post_type'      => 'post',
			'no_found_rows'  => 1,
		);

		if ( ! empty( $_REQUEST['query'] ) ) {
			$query_args['s'] = sanitize_text_field( $_REQUEST['query'] );
		}

		if ( ! empty( $_REQUEST['number'] ) ) {
			$query_args['posts_per_page'] = (int) $_REQUEST['number'];
		}

		$results = new WP_Query( $query_args );
		$suggestions = array();

		if ( $results->have_posts() ) {

			$suggestions[] = array(
				'value' => '',
				'divider' => esc_html__( 'Results from blog', 'woodmart' ),
			);

			while ( $results->have_posts() ) {
				$results->the_post();

				$suggestions[] = array(
					'value' => html_entity_decode( get_the_title() ),
					'permalink' => get_the_permalink(),
					'thumbnail' => get_the_post_thumbnail( null, 'medium', '' ),
				);
			}

			wp_reset_postdata();
		}

		return $suggestions;
	}
}

if ( ! function_exists( 'woodmart_get_product_categories_suggestions' ) ) {
	function woodmart_get_product_categories_suggestions() {
		if ( empty( $_REQUEST['query'] ) ) {
			return array();
		}

		$search     = sanitize_text_field( $_REQUEST['query'] );
		$categories = get_categories(
			array(
				'taxonomy'   => 'product_cat',
				'number'     => ! empty( $_REQUEST['number'] ) ? (int) $_REQUEST['number'] : 5,
				'orderby'    => 'name',
				'search'     => $search,
				'hide_empty' => false,
			)
		);

		if ( empty( $categories ) ) {
			return array();
		}

		$suggestions[] = array(
			'value'   => '',
			'divider' => esc_html__( 'Product categories', 'woodmart' ),
		);

		foreach ( $categories as $category ) {
			$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );

			$suggestions[] = array(
				'value'     => $category->name,
				'permalink' => get_term_link( $category ),
				'thumbnail' => wp_get_attachment_image( $thumbnail_id, 'medium' ),
			);
		}

		return $suggestions;
	}
}

if ( ! function_exists( 'woodmart_product_search_sku' ) ) {
	function woodmart_product_search_sku( $where, $class = false ) {
		global $pagenow, $wpdb, $wp;

		$type = array('product', 'jam');

		if ( ( is_admin() ) //if ((is_admin() && 'edit.php' != $pagenow)
				|| !is_search()
				|| !isset( $wp->query_vars['s'] )
				//post_types can also be arrays..
				|| (isset( $wp->query_vars['post_type'] ) && 'product' != $wp->query_vars['post_type'] )
				|| (isset( $wp->query_vars['post_type'] ) && is_array( $wp->query_vars['post_type'] ) && !in_array( 'product', $wp->query_vars['post_type'] ) )
				) {
			return $where;
		}

		$s = $wp->query_vars['s'];

		//WC 3.6.0
		if ( function_exists( 'WC' ) && version_compare( WC()->version, '3.6.0', '<' ) ) {
			return woodmart_sku_search_query( $where, $s );
		} else {
			return woodmart_sku_search_query_new( $where, $s );
		}
	}
}

if ( ! function_exists( 'woodmart_product_ajax_search_sku' ) ) {
	function woodmart_product_ajax_search_sku( $where ) {
		if ( ! empty( $_REQUEST['query'] ) ) {
			$s = sanitize_text_field( $_REQUEST['query'] );

			//WC 3.6.0
			if ( function_exists( 'WC' ) && version_compare( WC()->version, '3.6.0', '<' ) ) {
				return woodmart_sku_search_query( $where, $s );
			} else {
				return woodmart_sku_search_query_new( $where, $s );
			}
		}

		return $where;
	}
}

if ( ! function_exists( 'woodmart_sku_search_query' ) ) {
	function woodmart_sku_search_query( $where, $s ) {
		global $wpdb;

		$search_ids = array();
		$terms = explode( ',', $s );

		foreach ( $terms as $term ) {
			//Include the search by id if admin area.
			if ( apply_filters( 'woodmart_search_by_id', true ) && is_numeric( $term ) ) {
				$search_ids[] = $term;
			}
			// search for variations with a matching sku and return the parent.

			$sku_to_parent_id = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->postmeta} pm on p.ID = pm.post_id and pm.meta_key='_sku' and pm.meta_value LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", wc_clean( $term ) ) );

			//Search for a regular product that matches the sku.
			$sku_to_id = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value LIKE '%%%s%%';", wc_clean( $term ) ) );

			$search_ids = array_merge( $search_ids, $sku_to_id, $sku_to_parent_id );
		}

		$search_ids = array_filter( array_map( 'absint', $search_ids ) );

		if ( sizeof( $search_ids ) > 0 ) {
			$where = str_replace( ')))', ") OR ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . "))))", $where );
		}

		#remove_filters_for_anonymous_class('posts_search', 'WC_Admin_Post_Types', 'product_search', 10);
		return $where;
	}
}

if ( ! function_exists( 'woodmart_sku_search_query_new' ) ) {
	function woodmart_sku_search_query_new( $where, $s ) {
		global $wpdb;

		$search_ids = array();
		$terms = explode( ',', $s );

		foreach ( $terms as $term ) {
			//Include the search by id if admin area.
			if ( apply_filters( 'woodmart_search_by_id', true ) && is_numeric( $term ) ) {
				$search_ids[] = $term;
			}
			// search for variations with a matching sku and return the parent.

			$sku_to_parent_id = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->wc_product_meta_lookup} ml on p.ID = ml.product_id and ml.sku LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", wc_clean( $term ) ) );

			//Search for a regular product that matches the sku.
			$clean_term = wc_clean( $term );
			$sku_to_id = $wpdb->get_results( "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} WHERE sku LIKE '%{$clean_term}%';", ARRAY_N );

			$sku_to_id_results = array();
			if ( is_array( $sku_to_id ) ) {
				foreach ( $sku_to_id as $id ) {
					$sku_to_id_results[] = $id[0];
				}
			}

			$search_ids = array_merge( $search_ids, $sku_to_id_results, $sku_to_parent_id );
		}

		$search_ids = array_filter( array_map( 'absint', $search_ids ) );

		if ( sizeof( $search_ids ) > 0 ) {
			$where = str_replace( ')))', ")) OR ( {$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . ")))", $where );
		}

		return $where;
	}
}

if ( ! function_exists( 'woodmart_rlv_index_variation_skus' ) ) {
	function woodmart_rlv_index_variation_skus( $content, $post ) {
		if ( ! woodmart_get_opt( 'search_by_sku' ) || ! woodmart_get_opt( 'relevanssi_search' ) || ! function_exists( 'relevanssi_do_query' ) ) {
			return $content;
		}

		if ( $post->post_type == 'product' ) {

			$args = array( 'post_parent' => $post->ID, 'post_type' => 'product_variation', 'posts_per_page' => -1 );
			$variations = get_posts( $args );
			if ( !empty( $variations)) {
				foreach ( $variations as $variation ) {
					$sku = get_post_meta( $variation->ID, '_sku', true );
					$content .= " $sku";
				}
			}
		}

		return $content;
	}

	add_filter( 'relevanssi_content_to_index', 'woodmart_rlv_index_variation_skus', 10, 2 );
}

if ( ! function_exists( 'woodmart_update_hits_filter_by_product_sku' ) ) {
	function woodmart_update_hits_filter_by_product_sku( $filter_data, $query ) {
		if ( ! apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) || ! isset( $query->query['post_type'] ) || 'product' !== $query->query['post_type'] ) {
			return $filter_data;
		}

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_sku',
					'value'   => $query->query['s'],
					'compare' => 'LIKE',
				),
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'hidden' ),
					'compare' => 'NOT IN',
				),
			),
		);

		$posts = get_posts( $args );

		if ( $posts ) {
			$product_ids = array_column( (array) $filter_data[0], 'ID' );

			foreach ( $posts as $post ) {
				if ( ! in_array( apply_filters( 'wpml_object_id', $post->ID, 'product', true ), $product_ids ) ) {
					array_unshift( $filter_data[0], $post );
				}
			}
		}

		return $filter_data;
	}
}

if ( ! function_exists( 'woodmart_update_filtered_term_product_counts_by_sku' ) ) {
	function woodmart_update_filtered_term_product_counts_by_sku( $query ) {
		if ( empty( $_GET['s'] ) || ! apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) ) {
			return $query;
		}

		global $wpdb;

		$term = woodmart_clean( $_GET['s'] );

		$sku_to_parent_id = $wpdb->get_col( $wpdb->prepare( "SELECT p.post_parent as post_id FROM {$wpdb->posts} as p join {$wpdb->wc_product_meta_lookup} ml on p.ID = ml.product_id and ml.sku LIKE '%%%s%%' where p.post_parent <> 0 group by p.post_parent", $term ) );

		$sku_to_id = $wpdb->get_results( "SELECT product_id FROM {$wpdb->wc_product_meta_lookup} WHERE sku LIKE '%{$term}%';", ARRAY_N );

		$sku_to_id_results = array();
		if ( is_array( $sku_to_id ) ) {
			foreach ( $sku_to_id as $id ) {
				$sku_to_id_results[] = $id[0];
			}
		}

		$search_ids = array_merge( $sku_to_id_results, $sku_to_parent_id );

		if ( $search_ids ) {
			$placeholders   = implode(',', array_fill(0, count($search_ids), '%d'));
			$sql            = $wpdb->prepare( "{$wpdb->posts}.ID IN ($placeholders)", ...$search_ids );
			$query['where'] = str_replace( '))', ") OR ({$sql}))", $query['where'] );
		}

		return $query;
	}

	add_filter( 'woocommerce_get_filtered_term_product_counts_query', 'woodmart_update_filtered_term_product_counts_by_sku' );
}

if ( ! function_exists( 'woodmart_update_woocommerce_price_filter_query_by_sku' ) ) {
	function woodmart_update_woocommerce_price_filter_query_by_sku ( $sql ) {
		if ( isset( $_GET['s'] ) && apply_filters( 'woodmart_search_by_sku', woodmart_get_opt( 'search_by_sku' ) ) ) {
			$sql = preg_replace('/\s+(?=\)+$)/', '', $sql); // The latest SQL request characters should be ")))" to use the function woodmart_sku_search_query_new.
			$sql = woodmart_sku_search_query_new( $sql, $_GET['s'] );
		}
	
		return $sql;
	}

	add_filter( 'woocommerce_price_filter_sql', 'woodmart_update_woocommerce_price_filter_query_by_sku' );
}
