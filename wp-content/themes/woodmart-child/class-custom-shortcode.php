<?php

/**
 * Add Custome Shortcode
 */
class Custom_Compare_Page 
{

		public $cookie_name = 'woodmart_compare_list';

	public function __construct(){
		add_shortcode('custom_woodmart_compare',array($this,'compare_page_shortcode'));
	}

	public function compare_page_shortcode( $active_category = '' ) {


		ob_start();

		$products = $this->get_compared_products_data();

		if ( $products ) {
			$products_categories = $this->get_product_categories();

			if ( woodmart_get_opt( 'compare_by_category' ) && 1 < count( $products_categories ) ) {
				woodmart_enqueue_inline_style( 'page-compare-by-category' );

				if ( isset( $_GET['product_cat'] ) ) { //phpcs:ignore
					$active_category = sanitize_text_field( $_GET['product_cat'] ); //phpcs:ignore
					$active_category = isset( $products_categories[ $active_category ] ) ? $active_category : '';
				} elseif ( ! empty( $_GET['product_id'] ) ) { //phpcs:ignore
					$active_product_id = intval( sanitize_text_field( $_GET['product_id'] ) ); //phpcs:ignore

					foreach ( $products as $product ) {
						if ( $product['id'] !== $active_product_id ) {
							continue;
						}

						foreach ( $products_categories as $category_id => $category ) {
							if ( in_array( $category_id, $product['category'], true ) ) {
								$active_category = $category_id;
							}
						}
					}
				}

				if ( ! $active_category ) {
					$active_category = array_key_first( $products_categories );
				}

				?>
				<div class="wd-compare-page">
					<?php $this->get_head_tabs_compare( $products_categories, $active_category ); ?>
					<div class="wd-compare-content">
						<?php foreach ( $products_categories as $category_id => $category ) : ?>
							<?php
							$classes = '';

							if ( intval( $active_category ) === $category_id ) {
								$classes = ' wd-active';

								if ( empty( $_GET['action'] ) || 'woodmart_remove_category_from_compare' !== $_GET['action'] ) { //phpcs:ignore
									$classes .= ' wd-in';
								}
							}
							?>
							<table class="wd-compare-table<?php echo esc_attr( $classes ); ?>" data-category-id="<?php echo esc_attr( $category_id ); ?>" data-category-url="<?php echo esc_url( get_term_link( $category_id, 'product_cat' ) ); ?>">
								<?php $this->compare_table( $products, $category_id ); ?>
							</table>
						<?php endforeach; ?>
						<div class="wd-loader-overlay wd-fill"></div>
					</div>
				</div>
				<?php
			} else {
				?>
				<table class="wd-compare-table">
					<?php $this->compare_table( $products ); ?>
				</table>
				<?php
			}
		} else {
			$this->get_empty_compare_content();
		}

		return ob_get_clean();
	}

		/**
	 * Get compared products data
	 *
	 * @since 3.3
	 */
	public function get_compared_products_data() {

		$ids = $this->get_compared_products();

		if ( empty( $ids ) ) {
			return array();
		}

		$args = array(
			'include'   => $ids,
			'limit'     => 100,
			'post_type' => array( 'product', 'product_variation' ),
		);

		$products = get_posts( $args );

		$products_data = array();

		$fields = $this->get_compare_fields();

		$fields = array_filter(
			$fields,
			function( $field ) {
				return 'pa_' === substr( $field, 0, 3 );
			},
			ARRAY_FILTER_USE_KEY
		);

		$divider = apply_filters( 'woodmart_compare_empty_field_symbol', '-' );

		foreach ( $products as $product ) {
			setup_postdata( $product );

			$product      = wc_get_product( $product );
			$rating_count = $product->get_rating_count();
			$average      = $product->get_average_rating();

			$products_data[ $product->get_id() ] = array(
				'basic'        => array(
					'title'       => $product->get_title() ? $product->get_title() : $divider,
					'image'       => $product->get_image() ? $product->get_image() : $divider,
					'rating'      => wc_get_rating_html( $average, $rating_count ),
					'price'       => $product->get_price_html() ? $product->get_price_html() : $divider,
					'add_to_cart' => $this->compare_add_to_cart_html( $product ) ? $this->compare_add_to_cart_html( $product ) : $divider,
				),
				'id'           => $product->get_id(),
				'category'     => wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) ),
				'image_id'     => $product->get_image_id(),
				'permalink'    => $product->get_permalink(),
				'dimensions'   => wc_format_dimensions( $product->get_dimensions( false ) ),
				'description'  => get_the_excerpt( $product->get_id() ) ? get_the_excerpt( $product->get_id() ) : $divider,
				'weight'       => $product->get_weight() ? $product->get_weight() : $divider,
				'sku'          => $product->get_sku() ? $product->get_sku() : $divider,
				'availability' => $this->compare_availability_html( $product ),
			);

			foreach ( $fields as $field_id => $field_name ) {
				if ( taxonomy_exists( $field_id ) ) {
					$products_data[ $product->get_id() ][ $field_id ] = array();
					$orderby = wc_attribute_orderby( $field_id ) ? wc_attribute_orderby( $field_id ) : 'name';
					$terms   = wp_get_post_terms(
						$product->get_id(),
						$field_id,
						array(
							'orderby' => $orderby,
						)
					);
					if ( ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							$term = sanitize_term( $term, $field_id );
							$products_data[ $product->get_id() ][ $field_id ][] = $term->name;
						}
					} else {
						$products_data[ $product->get_id() ][ $field_id ][] = apply_filters( 'woodmart_compare_empty_field_symbol', '-' );
					}
					$products_data[ $product->get_id() ][ $field_id ] = implode( ', ', $products_data[ $product->get_id() ][ $field_id ] );
				}
			}
		}
		wp_reset_postdata();

		return $products_data;
	}
	public function get_compared_products() {
		return isset( $_COOKIE[ $this->cookie_name ] ) ? json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ $this->cookie_name ] ) ), true ) : array();
	}

		/**
	 * Get content when empty compare.
	 *
	 * @codeCoverageIgnore
	 */
	public function get_empty_compare_content() {
		$empty_compare_text = woodmart_get_opt( 'empty_compare_text' );

		woodmart_enqueue_inline_style( 'woo-page-empty-page' );

		?>
		<p class="wd-empty-compare wd-empty-page">
			<?php esc_html_e( 'Compare list is empty.', 'woodmart' ); ?>
		</p>
		<?php if ( $empty_compare_text ) : ?>
			<div class="wd-empty-page-text">
				<?php echo wp_kses( $empty_compare_text, true ); ?>
			</div>
		<?php endif; ?>
		<p class="return-to-shop">
			<a class="button" href="<?php echo esc_url( apply_filters( 'woodmart_compare_return_to_shop_url', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Return to shop', 'woodmart' ); ?>
			</a>
		</p>
		<?php
	}

		/**
	 * Get compare fields data.
	 */
	public function get_compare_fields() {
		$fields = array(
			'basic' => '',
		);

		$fields_settings = woodmart_get_opt( 'fields_compare' );

		if ( class_exists( 'XTS\Admin\Modules\Options' ) && $fields_settings && count( $fields_settings ) > 1 ) {
			$fields_labels = woodmart_compare_available_fields();

			foreach ( $fields_settings as $field ) {
				if ( isset( $fields_labels [ $field ] ) ) {
					$fields[ $field ] = $fields_labels [ $field ]['name'];
				}
			}

			return $fields;
		}

		if ( isset( $fields_settings['enabled'] ) && count( $fields_settings['enabled'] ) > 1 ) {
			$fields = $fields + $fields_settings['enabled'];
			unset( $fields['placebo'] );
		}

		return $fields;
	}

	/**
	 * Get product add to cart html.
	 *
	 * @param object $product Product object.
	 *
	 * @return mixed|void
	 */
	public function compare_add_to_cart_html( $product ) {
		if ( ! $product ) {
			return;
		}

		$defaults = array(
			'quantity'   => 1,
			'class'      => implode(
				' ',
				array_filter(
					array(
						'button',
						'product_type_' . $product->get_type(),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
					)
				)
			),
			'attributes' => array(
				'data-product_id'  => $product->get_id(),
				'data-product_sku' => $product->get_sku(),
				'aria-label'       => $product->add_to_cart_description(),
				'rel'              => 'nofollow',
			),
		);

		$args = apply_filters( 'woocommerce_loop_add_to_cart_args', $defaults, $product );

		if ( isset( $args['attributes']['aria-label'] ) ) {
			$args['attributes']['aria-label'] = strip_tags( $args['attributes']['aria-label'] );
		}

		ob_start();
		?>
		<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="<?php echo esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ); ?>" class="<?php echo esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ); ?> add-to-cart-loop" <?php echo isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : ''; //phpcs:ignore ?>>
			<span>
				<?php echo esc_html( $product->add_to_cart_text() ); ?>
			</span>
		</a>
		<?php
		$content = ob_get_clean();

		return apply_filters( 'woocommerce_loop_add_to_cart_link', $content, $product, $args );
	}
		/**
	 * Get product availability html.
	 *
	 * @param object $product Object product.
	 *
	 * @return string
	 */
	public function compare_availability_html( $product ) {
		$html         = '';
		$availability = $product->get_availability();

		if ( empty( $availability['availability'] ) ) {
			$availability['availability'] = __( 'In stock', 'woocommerce' );

			if ( isset( $availability['class'] ) ) {
				$availability['class'] .= ' wd-style-' . woodmart_get_opt( 'stock_status_design', 'default' );
			}

			if ( 'with-bg' === woodmart_get_opt( 'stock_status_design', 'default' ) || 'bordered' === woodmart_get_opt( 'stock_status_design', 'default' ) ) {
				$availability['availability'] = '<span>' . $availability['availability'] . '</span>';
			}
		}

		if ( ! empty( $availability['availability'] ) ) {
			ob_start();

			wc_get_template(
				'single-product/stock.php',
				array(
					'product'      => $product,
					'class'        => $availability['class'],
					'availability' => $availability['availability'],
				)
			);

			$html = ob_get_clean();
		}

		return apply_filters( 'woocommerce_get_stock_html', $html, $product );
	}

	public function get_product_categories() {
		if ( ! woodmart_get_opt( 'compare_by_category' ) ) {
			return array();
		}

		$product_ids = $this->get_compared_products();
		$categories  = array();

		if ( ! $product_ids ) {
			return array();
		}

		foreach ( $product_ids as $id ) {
			$single_cats = get_the_terms( $id, 'product_cat' );

			if ( ! $single_cats ) {
				continue;
			}

			foreach ( $single_cats as $single_cat ) {
				if ( 1 < count( $single_cats ) && $single_cat->parent && isset( $categories[ $single_cat->parent ] ) ) {
					continue;
				}

				$hide_current_category = false;

				foreach ( $single_cats as $cat ) {
					if ( $single_cat->parent === $cat->term_id ) {
						$hide_current_category = true;
					}
				}

				if ( $hide_current_category ) {
					continue;
				}

				if ( ! isset( $categories[ $single_cat->term_id ] ) ) {
					$categories[ $single_cat->term_id ] = array(
						'name'  => $single_cat->name,
						'slug'  => $single_cat->slug,
						'count' => 1,
					);
				} else {
					++$categories[ $single_cat->term_id ]['count'];
				}
			}
		}

		return $categories;
	}

	/**
	 * Header category tabs.
	 *
	 * @codeCoverageIgnore
	 * @param array   $categories Product category.
	 * @param integer $active_category Current category.
	 *
	 * @return string|void
	 */
	public function get_head_tabs_compare( $categories, $active_category ) {
		if ( ! $categories ) {
			return '';
		}

		?>
		<div class="wd-compare-header">
			<div class="wd-compare-select-wrap">
				<label for="wd-compare-select" class="title">
					<?php esc_html_e( 'Compare by categories:', 'woodmart' ); ?>
				</label>
				<select id="wd-compare-select" class="wd-compare-select">
					<?php foreach ( $categories as $id => $category ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $active_category ); ?>>
							<?php echo esc_html( $category['name'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="wd-compare-actions">
				<?php if ( woodmart_get_opt( 'show_more_products_btn' ) ) : ?>
					<a href="<?php echo esc_url( get_term_link( (int) $active_category, 'product_cat' ) ); ?>" class="btn wd-compare-cat-link">
						<?php esc_html_e( 'Compare more products', 'woodmart' ); ?>
					</a>
				<?php endif; ?>
				<a href="#" class="btn wd-compare-remove-cat">
					<?php esc_html_e( 'Remove category', 'woodmart' ); ?>
				</a>
			</div>
		</div>
		<?php
	}
	/**
	 * Compare product table.
	 *
	 * @codeCoverageIgnore
	 * @param array   $products Products.
	 * @param integer $category Category ID.
	 *
	 * @return void
	 */
	public function compare_table( $products, $category = '' ) {
		if ( ! $products ) {
			return;
		}

		array_unshift( $products, array() );

		foreach ( $this->get_compare_fields() as $field_id => $field ) {
			if ( ! $this->is_products_have_field( $field_id, $products, $category ) ) {
				continue;
			}
			?>
			<tr class="compare-<?php echo esc_attr( $field_id ); ?>">
				<?php foreach ( $products as $product ) : ?>
					<?php if ( ! empty( $product ) ) : ?>
						<?php
						if ( isset( $product['category'] ) && $category && ! in_array( $category, $product['category'], true ) ) {
							continue;
						}
						?>
						<td class="compare-value" data-title="<?php echo esc_attr( $field ); ?>">
							<?php $this->compare_display_field( $field_id, $product ); ?>
						</td>
					<?php else : ?>
						<th class="compare-field">
							<?php echo esc_html( $field ); ?>
						</th>
					<?php endif; ?>
				<?php endforeach ?>
			</tr>
			<?php
		}
	}
		public function is_products_have_field( $field_id, $products, $category ) {
		foreach ( $products as $product ) {
			if ( isset( $product[ $field_id ] ) && ( ! empty( $product[ $field_id ] ) && apply_filters( 'woodmart_compare_empty_field_symbol', '-' ) !== $product[ $field_id ] && 'N/A' !== $product[ $field_id ] ) && ( ! $category || in_array( $category, $product['category'], true ) ) ) {
				return true;
			}
		}

		return false;
	}
	/**
	 * Get compare fields data.
	 *
	 * @codeCoverageIgnore
	 * @param integer $field_id Fields ID.
	 * @param array   $product Product data.
	 */
	public function compare_display_field( $field_id, $product ) {
		woodmart_enqueue_js_script( 'woodmart-compare' );

		$type = $field_id;

		if ( 'pa_' === substr( $field_id, 0, 3 ) ) {
			$type = 'attribute';
		}

		switch ( $type ) {
			case 'basic':
				?>
				<div class="wd-compare-remove-action wd-action-btn wd-style-text wd-cross-icon">
					<a href="#" rel="nofollow" class="wd-compare-remove" data-id="<?php echo esc_attr( $product['id'] ); ?>">
						<?php echo esc_html__( 'Remove', 'woodmart' ); ?>
					</a>
				</div>
				<a class="product-image" href="<?php echo esc_url( get_permalink( $product['id'] ) ); ?>">
					<?php echo $product['basic']['image']; //phpcs:ignore ?>
				</a>
				<a class="wd-entities-title" href="<?php echo esc_url( get_permalink( $product['id'] ) ); ?>">
					<?php echo wp_kses( $product['basic']['title'], true ); ?>
				</a>
				<?php echo wp_kses_post( $product['basic']['rating'] ); ?>
				<div class="price">
					<?php echo wp_kses_post( $product['basic']['price'] ); ?>
				</div>
				<?php
				if ( ! woodmart_get_opt( 'catalog_mode' ) ) {
					echo apply_filters( 'woodmart_compare_add_to_cart_btn', $product['basic']['add_to_cart'] ); // phpcs:ignore
				}
				break;

			case 'attribute':
				if ( woodmart_get_opt( 'brands_attribute' ) === $field_id ) {
					$brands = wc_get_product_terms( $product['id'], $field_id, array( 'fields' => 'all' ) );

					if ( empty( $brands ) ) {
						echo apply_filters( 'woodmart_compare_empty_field_symbol', '-' ); //phpcs:ignore
						return;
					}

					foreach ( $brands as $brand ) {
						$image = get_term_meta( $brand->term_id, 'image', true );

						if ( is_array( $image ) && isset( $image['id'] ) ) {
							$image = wp_get_attachment_image_url( $image['id'], 'full' );
						}

						if ( ! empty( $image ) ) {
							?>
							<div class="wd-compare-brand<?php echo esc_attr( woodmart_get_old_classes( ' woodmart-compare-brand' ) ); ?>">
								<?php echo apply_filters( 'woodmart_image', '<img src="' . esc_url( $image ) . '" title="' . esc_attr( $brand->name ) . '" alt="' . esc_attr( $brand->name ) . '" />' ); //phpcs:ignore ?>
							</div>
							<?php
						} else {
							echo wp_kses_post( $product[ $field_id ] );
						}
					}
				} else {
					echo wp_kses_post( $product[ $field_id ] );
				}
				break;

			case 'weight':
				if ( $product[ $field_id ] ) {
					$unit = apply_filters( 'woodmart_compare_empty_field_symbol', '-' ) !== $product[ $field_id ] ? get_option( 'woocommerce_weight_unit' ) : '';
					echo wc_format_localized_decimal( $product[ $field_id ] ) . ' ' . esc_attr( $unit ); //phpcs:ignore
				}
				break;

			case 'description':
				echo apply_filters( 'woocommerce_short_description', $product[ $field_id ] ); //phpcs:ignore
				break;

			default:
				echo wp_kses_post( $product[ $field_id ] );
				break;
		}
	}
	
}

$Custom_Compare_Page = new Custom_Compare_Page();