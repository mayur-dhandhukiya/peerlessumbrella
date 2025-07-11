<?php
/**
 * Blog template function.
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_elementor_blog_template' ) ) {
	function woodmart_elementor_blog_template( $settings ) {
		$default_settings = [
			// General.
			'post_type'               => 'post',

			// Query.
			'items_per_page'          => 12,
			'include'                 => '',
			'taxonomies'              => '',
			'offset'                  => '',
			'orderby'                 => 'date',
			'order'                   => 'DESC',
			'meta_key'                => '',
			'exclude'                 => '',

			// Visibility.
			'parts_media'             => true,
			'parts_title'             => true,
			'parts_meta'              => true,
			'parts_text'              => true,
			'parts_btn'               => true,
			'parts_published_date'    => true,

			// Design.
			'img_size'                => 'medium',
			'blog_design'             => 'default',
			'blog_carousel_design'    => 'masonry',
			'blog_columns'            => array( 'size' => 3 ),
			'blog_columns_tablet'     => array( 'size' => '' ),
			'blog_columns_mobile'     => array( 'size' => '' ),
			'blog_spacing'            => woodmart_get_opt( 'blog_spacing' ),
			'blog_spacing_tablet'     => woodmart_get_opt( 'blog_spacing_tablet', '' ),
			'blog_spacing_mobile'     => woodmart_get_opt( 'blog_spacing_mobile', '' ),
			'pagination'              => '',

			// Carousel.
			'speed'                   => '5000',
			'slides_per_view'         => array( 'size' => 4 ),
			'slides_per_view_tablet'  => array( 'size' => '' ),
			'slides_per_view_mobile'  => array( 'size' => '' ),
			'wrap'                    => '',
			'autoplay'                => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons'  => '',
			'scroll_per_page'         => 'yes',

			// Extra.
			'lazy_loading'            => 'no',
			'scroll_carousel_init'    => 'no',
			'ajax_page'               => '',
			'custom_sizes'            => apply_filters( 'woodmart_blog_shortcode_custom_sizes', false ),
			'elementor'               => true,
		];

		$settings         = wp_parse_args( $settings, $default_settings );
		$encoded_settings = wp_json_encode( array_intersect_key( $settings, $default_settings ) );
		$is_ajax          = woodmart_is_woo_ajax();
		$paged            = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$id               = uniqid();

		if ( $settings['ajax_page'] > 1 ) {
			$paged = $settings['ajax_page'];
		}

		$query_args = [
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'paged'          => $paged,
			'posts_per_page' => $settings['items_per_page'],
		];

		if ( 'ids' === $settings['post_type'] && $settings['include'] ) {
			$query_args['post__in']            = $settings['include'];
			$query_args['ignore_sticky_posts'] = true;
		}

		if ( $settings['exclude'] ) {
			$query_args['post__not_in'] = $settings['exclude'];
		}

		if ( $settings['taxonomies'] ) {
			$taxonomy_names = get_object_taxonomies( 'post' );
			$terms          = get_terms(
				$taxonomy_names,
				[
					'orderby' => 'name',
					'include' => $settings['taxonomies'],
				]
			);

			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$query_args['tax_query'] = array( 'relation' => 'OR' );
				foreach ( $terms as $key => $term ) {
					$query_args['tax_query'][] = [
						'taxonomy'         => $term->taxonomy,
						'field'            => 'slug',
						'terms'            => [ $term->slug ],
						'include_children' => true,
						'operator'         => 'IN',
					];
				}
			}
		}

		if ( $settings['order'] ) {
			$query_args['order'] = $settings['order'];
		}

		if ( $settings['offset'] ) {
			$query_args['offset'] = $settings['offset'];
		}

		if ( $settings['meta_key'] ) {
			$query_args['meta_key'] = $settings['meta_key'];
		}

		if ( $settings['orderby'] ) {
			$query_args['orderby'] = $settings['orderby'];
		}

		$blog_query = new WP_Query( $query_args );

		$settings['blog_columns'] = isset( $settings['blog_columns']['size'] ) ? $settings['blog_columns']['size'] : 3;

		// Loop.
		woodmart_set_loop_prop( 'blog_type', 'shortcode' );
		woodmart_set_loop_prop( 'blog_design', $settings['blog_design'] );
		woodmart_set_loop_prop( 'img_size', $settings['img_size'] );
		woodmart_set_loop_prop( 'blog_columns', $settings['blog_columns'] );
		woodmart_set_loop_prop( 'blog_columns_tablet', ! empty( $settings['blog_columns_tablet']['size'] ) ? $settings['blog_columns_tablet']['size'] : 'auto' );
		woodmart_set_loop_prop( 'blog_columns_mobile', ! empty( $settings['blog_columns_mobile']['size'] ) ? $settings['blog_columns_mobile']['size'] : 'auto' );

		woodmart_set_loop_prop( 'woodmart_loop', 0 );
		woodmart_set_loop_prop( 'parts_title', $settings['parts_title'] );
		woodmart_set_loop_prop( 'parts_meta', $settings['parts_meta'] );
		woodmart_set_loop_prop( 'parts_published_date', $settings['parts_published_date'] );
		woodmart_set_loop_prop( 'parts_text', $settings['parts_text'] );
		woodmart_set_loop_prop( 'parts_btn', $settings['parts_btn'] );
		woodmart_set_loop_prop( 'parts_media', $settings['parts_media'] );

		if ( ! empty( $settings['img_size_custom'] ) ) {
			woodmart_set_loop_prop( 'img_size_custom', $settings['img_size_custom'] );
		}

		if ( 'carousel' === $settings['blog_design'] ) {
			woodmart_set_loop_prop( 'blog_design', $settings['blog_carousel_design'] );
		}
		if ( '' === $settings['blog_spacing'] ) {
			$settings['blog_spacing'] = woodmart_get_opt( 'blog_spacing' );

			if ( '' === $settings['blog_spacing_tablet'] ) {
				$settings['blog_spacing_tablet'] = woodmart_get_opt( 'blog_spacing_tablet' );
			}
			if ( '' === $settings['blog_spacing_mobile'] ) {
				$settings['blog_spacing_mobile'] = woodmart_get_opt( 'blog_spacing_mobile' );
			}
		}
//		if ( ! $settings['parts_btn'] ) {
//			woodmart_set_loop_prop( 'parts_btn', false );
//		}

		if ( $is_ajax ) {
			ob_start();
		}

		$blog_design = $settings['blog_design'];
		if ( 'carousel' === $blog_design ) {
			$blog_design = $settings['blog_carousel_design'];
		}

		woodmart_enqueue_inline_style( 'blog-base' );
		if ( woodmart_is_blog_design_new( $blog_design ) ) {
			woodmart_enqueue_inline_style( 'blog-loop-base' );
		} else {
			woodmart_enqueue_inline_style( 'blog-loop-base-old' );
		}
		if ( 'small-images' === $blog_design || 'chess' === $blog_design ) {
			woodmart_enqueue_inline_style( 'blog-loop-design-small-img-chess' );
		} else {
			woodmart_enqueue_inline_style( 'blog-loop-design-' . $blog_design );
		}

		if ( 'carousel' === $settings['blog_design'] ) {
			woodmart_set_loop_prop( 'blog_layout', 'carousel' );
			$settings['slides_per_view'] = $settings['slides_per_view']['size'];

			if ( ( isset( $settings['slides_per_view_tablet']['size'] ) && ! empty( $settings['slides_per_view_tablet']['size'] ) ) || ( isset( $settings['slides_per_view_mobile']['size'] ) && ! empty( $settings['slides_per_view_mobile']['size'] ) ) ) {
				$settings['custom_sizes'] = array(
					'desktop' => $settings['slides_per_view'],
					'tablet'  => $settings['slides_per_view_tablet']['size'],
					'mobile'  => $settings['slides_per_view_mobile']['size'],
				);
			}

			$settings['spacing']        = $settings['blog_spacing'];
			$settings['spacing_tablet'] = $settings['blog_spacing_tablet'];
			$settings['spacing_mobile'] = $settings['blog_spacing_mobile'];

			if ( $is_ajax ) {
				return ob_get_clean() . woodmart_generate_posts_slider( $settings, $blog_query );
			}

			return woodmart_generate_posts_slider( $settings, $blog_query );
		} else {
			$attributes      = '';
			$wrapper_classes = '';

			// Lazy loading.
			if ( 'yes' === $settings['lazy_loading'] ) {
				woodmart_lazy_loading_init( true );
				woodmart_enqueue_inline_style( 'lazy-loading' );
			}

			if ( in_array( $settings['blog_design'], array( 'masonry', 'mask', 'meta-image' ), true ) ) {
				if ( 'meta-image' !== $settings['blog_design'] ) {
					$wrapper_classes .= ' wd-masonry wd-grid-f-col';

					wp_enqueue_script( 'imagesloaded' );
					woodmart_enqueue_js_library( 'isotope-bundle' );
					woodmart_enqueue_js_script( 'masonry-layout' );
				}

				$attributes .= ' style="' . woodmart_get_grid_attrs(
					array(
						'columns'        => woodmart_loop_prop( 'blog_columns' ),
						'columns_tablet' => woodmart_loop_prop( 'blog_columns_tablet' ),
						'columns_mobile' => woodmart_loop_prop( 'blog_columns_mobile' ),
						'spacing'        => $settings['blog_spacing'],
						'spacing_tablet' => $settings['blog_spacing_tablet'],
						'spacing_mobile' => $settings['blog_spacing_mobile'],
					)
				) . '"';
			}

			if ( ! in_array( $settings['blog_design'], array( 'masonry', 'mask' ), true ) ) {
				$wrapper_classes .= ' wd-grid-g';
			}

			?>
			<?php if ( ! $is_ajax ) : ?>
				<div class="wd-blog-element">
					<div class="wd-posts wd-blog-holder<?php echo esc_attr( $wrapper_classes ); ?>" id="<?php echo esc_attr( $id ); ?>" data-paged="1" data-atts="<?php echo esc_attr( $encoded_settings ); ?>" data-source="shortcode"<?php echo wp_kses( $attributes, true ); ?>>
			<?php endif; ?>

				<?php while ( $blog_query->have_posts() ) : ?>
					<?php $blog_query->the_post(); ?>
					<?php get_template_part( 'templates/content', woodmart_get_blog_design_name( $settings['blog_design'] ) ); ?>
				<?php endwhile; ?>

			<?php if ( ! $is_ajax ) : ?>
					</div>
			<?php endif; ?>

			<?php if ( $blog_query->max_num_pages > 1 && ! $is_ajax && $settings['pagination'] ) : ?>
				<div class="wd-loop-footer blog-footer">
					<?php if ( 'infinit' === $settings['pagination'] || 'more-btn' === $settings['pagination'] ) : ?>
						<?php wp_enqueue_script( 'imagesloaded' ); ?>
						<?php woodmart_enqueue_js_script( 'blog-load-more' ); ?>
						<?php if ( 'infinit' === $settings['pagination'] ) : ?>
							<?php woodmart_enqueue_js_library( 'waypoints' ); ?>
						<?php endif; ?>
						<?php woodmart_enqueue_inline_style( 'load-more-button' ); ?>
						<a href="#" data-holder-id="<?php echo esc_attr( $id ); ?>" rel="nofollow noopener" class="btn wd-load-more wd-blog-load-more load-on-<?php echo 'more-btn' === $settings['pagination'] ? 'click' : 'scroll'; ?>"><span class="load-more-label"><?php esc_html_e( 'Load more posts', 'woodmart' ); ?></span></a>
						<div class="btn wd-load-more wd-load-more-loader"><span class="load-more-loading"><?php esc_html_e( 'Loading...', 'woodmart' ); ?></span></div>
					<?php else : ?>
						<?php query_pagination( $blog_query->max_num_pages ); ?>
					<?php endif ?>
				</div>
			<?php endif; ?>
			<?php if ( ! $is_ajax ) : ?>
				</div>
			<?php endif; ?>
			<?php
		}

		wp_reset_postdata();
		woodmart_reset_loop();

		// Lazy loading.
		if ( 'yes' === $settings['lazy_loading'] ) {
			woodmart_lazy_loading_deinit();
		}

		if ( $is_ajax ) {
			return array(
				'items'  => ob_get_clean(),
				'status' => $blog_query->max_num_pages > $paged ? 'have-posts' : 'no-more-posts',
			);
		}
	}
}

