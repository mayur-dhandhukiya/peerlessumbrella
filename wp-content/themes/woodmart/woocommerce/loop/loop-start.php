<?php
/**
 * Product Loop Start
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

use XTS\Modules\Layouts\Global_Data;
use XTS\Modules\Layouts\Main;

$class        = '';
$current_view = woodmart_loop_prop( 'products_view' );
$is_slider    = woodmart_loop_prop( 'is_slider' );
$is_shortcode = woodmart_loop_prop( 'is_shortcode' );
$shop_view    = woodmart_get_opt( 'shop_view' );
$is_builder   = Main::get_instance()->has_custom_layout( 'shop_archive' );
$attributes   = '';

if ( ( 'grid' === $shop_view || 'list' === $shop_view ) && ! $is_builder ) {
	$current_view = $shop_view;
}

if ( $is_slider ) {
	$current_view = 'grid';
}

if ( $is_shortcode ) {
	$current_view = woodmart_loop_prop( 'products_view' );
} elseif ( 'list' === $current_view && 'subcategories' === woocommerce_get_loop_display_mode() && ! is_search() ) {
	$current_view = 'grid';
}

if ( woodmart_loop_prop( 'products_masonry' ) ) {
	wp_enqueue_script( 'imagesloaded' );
	woodmart_enqueue_js_library( 'isotope-bundle' );
	woodmart_enqueue_js_script( 'shop-masonry' );
}

if ( 'grid' === $current_view && 1 < woodmart_loop_prop( 'products_columns' ) && ( woodmart_loop_prop( 'products_masonry' ) || woodmart_loop_prop( 'products_different_sizes' ) ) ) {
	if ( woodmart_loop_prop( 'products_masonry' ) ) {
		$class .= ' grid-masonry';
	}

	$class .= ' wd-grid-f-col';
} else {
	$class .= ' wd-grid-g';
}

if ( 'list' === $current_view ) {
	$class .= ' elements-list';
} else {
	$class .= ' grid-columns-' . woodmart_loop_prop( 'products_columns' );
	$class .= ' elements-grid';
}

if ( ( woodmart_loop_prop( 'products_bordered_grid' ) || 'enable' === woodmart_loop_prop( 'products_bordered_grid' ) ) && 'disable' !== woodmart_loop_prop( 'products_bordered_grid' ) ) {
	woodmart_enqueue_inline_style( 'bordered-product' );

	if ( 'outside' === woodmart_loop_prop( 'products_bordered_grid_style' ) ) {
		$class .= ' products-bordered-grid';
	} elseif ( 'inside' === woodmart_loop_prop( 'products_bordered_grid_style' ) ) {
		$class .= ' products-bordered-grid-ins';
	}
}

if ( woodmart_get_opt( 'quick_shop_variable' ) ) {
	if ( 'variation_form' === woodmart_get_opt( 'quick_shop_variable_type', 'select_options' ) ) {
		woodmart_enqueue_js_script( 'quick-shop-with-form' );
	} else {
		woodmart_enqueue_js_script( 'quick-shop' );
		woodmart_enqueue_js_script( 'swatches-variations' );
	}

	woodmart_enqueue_js_script( 'add-to-cart-all-types' );
	wp_enqueue_script( 'wc-add-to-cart-variation' );
}

if ( woodmart_get_opt( 'product_quantity' ) ) {
	$class .= ' wd-quantity-enabled';
}

if ( Global_Data::get_instance()->get_data( 'shop_pagination' ) ) {
	$pagination_type = Global_Data::get_instance()->get_data( 'shop_pagination' );
} else {
	$pagination_type = woodmart_get_opt( 'shop_pagination' );
}

if ( $pagination_type ) {
	$class .= ' pagination-' . $pagination_type;
}

if ( 'none' !== woodmart_get_opt( 'product_title_lines_limit' ) && 'list' !== $current_view ) {
	woodmart_enqueue_inline_style( 'woo-opt-title-limit' );
	$class .= ' title-line-' . woodmart_get_opt( 'product_title_lines_limit' );
}

// fix for price filter ajax
$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

$product_design    = woodmart_loop_prop( 'product_hover' );
$categories_design = woodmart_loop_prop( 'product_categories_design' );

if ( 'list' === $current_view ) {
	$product_design = 'list';
}

if ( woodmart_is_old_category_structure( $categories_design ) ) {
	woodmart_set_loop_prop( 'old_structure', true );
}

woodmart_enqueue_product_loop_styles( $product_design );
if ( 'alt' !== $categories_design && 'inherit' !== $categories_design ) {
	if ( 'light' === woodmart_loop_prop( 'products_color_scheme', 'default' ) && 'default' === $categories_design ) {
		woodmart_enqueue_inline_style( 'categories-loop-' . $categories_design . '-scheme-light' );
	} else {
		woodmart_enqueue_inline_style( 'categories-loop-' . $categories_design );
	}
}

woodmart_enqueue_inline_style( 'woo-categories-loop' );

if ( woodmart_loop_prop( 'old_structure' ) ) {
	woodmart_enqueue_inline_style( 'categories-loop' );
}

if ( ( woodmart_loop_prop( 'stretch_product_desktop' ) || woodmart_loop_prop( 'stretch_product_tablet' ) || woodmart_loop_prop( 'stretch_product_mobile' ) ) && in_array( $product_design, array( 'icons', 'alt', 'button', 'standard', 'tiled', 'quick', 'base', 'fw-button', 'buttons-on-hover' ), true ) ) {
	woodmart_enqueue_inline_style( 'woo-opt-stretch-cont' );
	if ( woodmart_loop_prop( 'stretch_product_desktop' ) ) {
		$class .= ' wd-stretch-cont-lg';
	}
	if ( woodmart_loop_prop( 'stretch_product_tablet' ) ) {
		$class .= ' wd-stretch-cont-md';
	}
	if ( woodmart_loop_prop( 'stretch_product_mobile' ) ) {
		$class .= ' wd-stretch-cont-sm';
	}
}

if ( 'default' !== woodmart_loop_prop( 'products_color_scheme', 'default' ) && ( woodmart_loop_prop( 'products_bordered_grid' ) || 'enable' === woodmart_loop_prop( 'products_bordered_grid' ) ) && 'disable' !== woodmart_loop_prop( 'products_bordered_grid' ) && 'outside' === woodmart_loop_prop( 'products_bordered_grid_style' ) ) {
	$class .= ' wd-bordered-' . woodmart_loop_prop( 'products_color_scheme' );
}

if ( woodmart_loop_prop( 'products_with_background' ) ) {
	woodmart_enqueue_inline_style( 'woo-opt-products-bg' );

	$class .= ' wd-products-with-bg';
}

if ( woodmart_loop_prop( 'products_shadow' ) ) {
	woodmart_enqueue_inline_style( 'woo-opt-products-shadow' );

	$class .= ' wd-products-with-shadow';
}

if ( ! empty( $GLOBALS['woodmart_loop'] ) && $is_builder && ( 'more-btn' === $pagination_type || 'infinit' === $pagination_type ) ) {
	$loop_settings = array();
	$attr_keys     = array( 'img_size', 'img_size_custom', 'products_view', 'products_columns', 'products_columns_tablet', 'products_columns_mobile', 'products_spacing', 'products_spacing_tablet', 'products_spacing_mobile', 'product_hover', 'products_bordered_grid', 'products_bordered_grid_style', 'products_color_scheme', 'products_with_background', 'products_shadow' );

	foreach ( $attr_keys as $key ) {
		$value = woodmart_loop_prop( $key );

		$loop_settings[ $key ] = is_bool( $value ) ? (int) $value : $value;
	}

	if ( $loop_settings ) {
		$attributes .= ' data-atts=\'' . wp_json_encode( $loop_settings ) . '\'';
	}
}

if ( 'grid' === $current_view ) {
	$attributes .= ' style="' . woodmart_get_grid_attrs(
		array(
			'columns'        => woodmart_loop_prop( 'products_columns' ),
			'columns_tablet' => woodmart_loop_prop( 'products_columns_tablet' ),
			'columns_mobile' => woodmart_loop_prop( 'products_columns_mobile' ),
			'spacing'        => woodmart_loop_prop( 'products_spacing' ),
			'spacing_tablet' => woodmart_loop_prop( 'products_spacing_tablet' ),
			'spacing_mobile' => woodmart_loop_prop( 'products_spacing_mobile' ),
		)
	) . '"';
} else {
	$attributes .= ' style="' . woodmart_get_grid_attrs(
		array(
			'columns'        => 1,
			'columns_tablet' => 1,
			'columns_mobile' => 1,
		)
	) . '"';
}
?>
<?php if ( ! $is_builder ) : ?>
	<?php woodmart_sticky_loader( ' wd-content-loader' ); ?>
<?php endif; ?>

<div class="products wd-products<?php echo esc_attr( $class ); ?>" data-source="main_loop" data-min_price="<?php echo esc_attr( $min_price ); ?>" data-max_price="<?php echo esc_attr( $max_price ); ?>" data-columns="<?php echo esc_attr( woodmart_loop_prop( 'products_columns' ) ); ?>"<?php echo wp_kses( $attributes, true ); ?>>
