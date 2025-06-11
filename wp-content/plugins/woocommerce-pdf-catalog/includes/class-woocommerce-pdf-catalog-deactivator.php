<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 * @since      1.0.0
 *
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/includes
 * @author     Daniel Barenkamp <support@welaunch.io>
 */
class WooCommerce_PDF_Catalog_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'woocommerce_pdf_catalog_generate_cache' );
	}

}
