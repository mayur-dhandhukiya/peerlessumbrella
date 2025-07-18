<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) exit( 'No direct script access allowed' );

/**
* ------------------------------------------------------------------------------------------------
* Image hotspot shortcode
* ------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( 'woodmart_image_hotspot_shortcode' ) ) {
	function woodmart_image_hotspot_shortcode( $atts, $content ) {
		$image   = '';
		$video_attr = '';
		$classes = apply_filters( 'vc_shortcodes_css_class', '', '', $atts );

		$atts = shortcode_atts(
			array(
				'source_type'           => 'image',
				'video'                 => '',
				'video_poster'          => '',
				'video_poster_size'     => 'full',
				'img'                   => '',
				'img_size'              => 'full',
				'action'                => 'hover',
				'icon'                  => 'default',
				'icon_position'         => 'static',
				'woodmart_color_scheme' => 'dark',
				'el_class'              => '',
				'css'                   => '',
				'woodmart_css_id'       => '',
			),
			$atts
		);

		$classes .= ' wd-event-' . $atts['action'];
		$classes .= ' hotspot-icon-' . $atts['icon'];
		$classes .= ' color-scheme-' . $atts['woodmart_color_scheme'];
		$classes .= ' wd-hotspot-' . $atts['icon_position'];
		$classes .= ( $atts['el_class'] ) ? ' ' . $atts['el_class'] : '';

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
		}

		if ( 'video' === $atts['source_type'] && $atts['video_poster'] ) {
			$video_attr .= ' poster="' . woodmart_otf_get_image_url( $atts['video_poster'], $atts['video_poster_size'] ) . '"';
		}

		ob_start();

		wp_enqueue_script( 'imagesloaded' );
		woodmart_enqueue_js_script( 'hotspot-element' );
		woodmart_enqueue_js_script( 'product-more-description' );
		woodmart_enqueue_inline_style( 'image-hotspot' );
		woodmart_enqueue_inline_style( 'mod-more-description' );
		?>
		<div class="wd-spots<?php echo esc_attr( $classes ); ?>">
			<div class="wd-image-hotspot-hotspots">
				<?php
				if ( 'image' === $atts['source_type'] && $atts['img'] ) {
					echo woodmart_otf_get_image_html( $atts['img'], $atts['img_size'], array(), array( 'class' => 'wd-image-hotspot-img' ) ); //phpcs:ignore
				} elseif ( 'video' === $atts['source_type'] && $atts['video'] ) {
					?>
					<video class="wd-image-hotspot-img" src="<?php echo esc_url( wp_get_attachment_url( $atts['video'] ) ); ?>" autoplay muted loop playsinline<?php echo wp_kses( $video_attr, true ); ?>></video>
					<?php
				}
					echo do_shortcode( $content );
				?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}
}

/**
* ------------------------------------------------------------------------------------------------
* Image hotspot shortcode
* ------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( 'woodmart_hotspot_shortcode' ) ) {
	function woodmart_hotspot_shortcode( $atts, $content ) {
		$output = $classes = $content_classes = $product = $image = '';
		extract(
			shortcode_atts(
				array(
					'hotspot'               => '',
					'hotspot_type'          => 'product',
					'hotspot_dropdown_side' => 'left',
					'product_id'            => '',
					'title'                 => '',
					'link_text'             => '',
					'link'                  => '',
					'img'                   => '',
					'img_size'              => 'full',
					'el_class'              => '',
				),
				$atts
			)
		);

		$classes         .= ' hotspot-type-' . $hotspot_type;
		$classes         .= ( $el_class ) ? ' ' . $el_class : '';
		$content_classes .= ' hotspot-dropdown-' . $hotspot_dropdown_side;

		$position = explode( '||', $hotspot );
		$left     = ( isset( $position[0] ) && $position[0] ) ? $position[0] : '50';
		$top      = ( isset( $position[1] ) && $position[1] ) ? $position[1] : '50';

		if ( $product_id && woodmart_woocommerce_installed() ) {
			$product = wc_get_product( apply_filters( 'wpml_object_id', $product_id, 'product', true ) );
		}

		if ( $hotspot_type == 'product' && $product ) {
			$rating_count = $product->get_rating_count();
			$average      = $product->get_average_rating();

			if ( 'nothing' !== woodmart_get_opt( 'add_to_cart_action' ) ) {
				woodmart_enqueue_js_script( 'action-after-add-to-cart' );
			}

			if ( 'popup' === woodmart_get_opt( 'add_to_cart_action' ) ) {
				woodmart_enqueue_js_library( 'magnific' );
				woodmart_enqueue_inline_style( 'add-to-cart-popup' );
				woodmart_enqueue_inline_style( 'mfp-popup' );
			}

			$args = array(
				'class'      => implode(
					' ',
					array_filter(
						array(
							'button',
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
						)
					)
				),
				'attributes' => wc_implode_html_attributes(
					array(
						'data-product_id' => $product->get_id(),
						'rel'             => 'nofollow',
					)
				),
				'url'        => $product->add_to_cart_url(),
				'text'       => $product->add_to_cart_text(),
			);

			$output      = '<div class="hotspot-product hotspot-content' . esc_attr( $content_classes ) . '">';
				$output .= '<div class="hotspot-content-image"><a href="' . esc_url( get_permalink( $product->get_ID() ) ) . '">' . $product->get_image() . '</a></div>';
				$output .= '<h4 class="wd-entities-title"><a href="' . esc_url( get_permalink( $product->get_ID() ) ) . '">' . esc_html( $product->get_title() ) . '</a></h4>';
			if ( wc_review_ratings_enabled() ) {
				$output .= wc_get_rating_html( $average, $rating_count );
			}
				$output .= '<div class="price">' . $product->get_price_html() . '</div>';
				$output .= '<div class="hotspot-content-text wd-more-desc reset-last-child' . woodmart_get_old_classes( ' woodmart-more-desc' ) . '"><div class="wd-more-desc-inner' . woodmart_get_old_classes( ' woodmart-more-desc-inner' ) . '">' . do_shortcode( $product->get_short_description() ) . '</div><a href="#" rel="nofollow" class="wd-more-desc-btn" aria-label="' . esc_html__( 'Read more description', 'woodmart' ) . '"></a></div>';
				$output .= '<a href="' . esc_url( $args['url'] ) . '" class="' . esc_attr( $args['class'] ) . '" ' . $args['attributes'] . '>' . esc_html( $args['text'] ) . '</a>';
			$output     .= '</div>';
		}

		if ( $hotspot_type == 'text' && ( $title || $content || $link_text ) ) {
			if ( $link ) {
				$attributes = woodmart_get_link_attributes( $link );
			}

			$image = woodmart_otf_get_image_html( $img, $img_size, array(), array( 'class' => 'wd-image-hotspot-img' ) );

			$image_allowed_tags = array(
				'img' => array(
					'width'       => true,
					'height'      => true,
					'src'         => true,
					'alt'         => true,
					'data-src'    => true,
					'data-srcset' => true,
					'class'       => true,
				),
			);

			$output = '<div class="hotspot-text hotspot-content' . esc_attr( $content_classes ) . '">';
			if ( $image ) {
				$output .= '<div class="hotspot-content-image">' . wp_kses( $image, $image_allowed_tags ) . '</div>';
			}
			if ( $title ) {
				$output .= '<h4 class="wd-entities-title">' . esc_html( $title ) . '</h4>';
			}
			if ( $content ) {
				$output .= '<div class="hotspot-content-text reset-last-child">' . $content . '</div>';
			}
			if ( $link_text && $link ) {
				$output .= '<a class="btn" ' . $attributes . '>' . esc_html( $link_text ) . '</a>';
			}
			$output .= '</div>';
		}

		if ( ! $output ) {
			return;
		}
		echo '<div class="wd-image-hotspot' . esc_attr( $classes ) . '" style="left: ' . esc_attr( $left ) . '%; top: ' . esc_attr( $top ) . '%;">';
			echo '<span class="hotspot-sonar"></span>';
			echo '<div class="hotspot-btn wd-fill"></div>';
			echo apply_filters( 'woodmart_hotspot_content', $output );
		echo '</div>';

	}
}

if ( ! function_exists( 'woodmart_get_hotspot_image' ) ) {
	function woodmart_get_hotspot_image() {
		check_ajax_referer( 'woodmart-get-hotspot-image-nonce', 'security' );

		$source_id = sanitize_text_field( $_GET[ 'image_id' ] ); //phpcs:ignore
		$html      = '';

		if ( false !== strpos( get_post_mime_type( $source_id ), 'video' ) ) {
			$html = '<video class="xts-hotspot-img" src="' . wp_get_attachment_url( $source_id ) . '" autoplay loop muted playsinline></video>';
		} else {
			$background_image = wp_get_attachment_image_src( $source_id, 'full' );

			if ( $background_image ) {
				$html = '<img class="xts-hotspot-img" src="' . esc_url( $background_image[0] ) . '" width="' . esc_attr( $background_image[1] ) . '" height="' . esc_attr( $background_image[2] ) . '">';
			}
		}

		if ( ! $html ) {
			$response = array(
				'status' => 'warning',
				'html'   => '<div class="woodmart-warning">' . esc_html__( 'You need to upload an image for the parent element first.', 'woodmart' ) . '</div>',
			);
			echo json_encode( $response );
			die();
		}

		$response = array(
			'status' => 'success',
			'html'   => $html,
		);

		echo json_encode( $response );
		die();
	}

	add_action( 'wp_ajax_woodmart_get_hotspot_image', 'woodmart_get_hotspot_image' );
}
