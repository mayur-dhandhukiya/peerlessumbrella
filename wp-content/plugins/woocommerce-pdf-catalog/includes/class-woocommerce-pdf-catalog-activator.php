<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 * @since      1.0.0
 *
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/includes
 * @author     Daniel Barenkamp <support@welaunch.io>
 */
class WooCommerce_PDF_Catalog_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$timestamp = wp_next_scheduled( 'woocommerce_pdf_catalog_generate_cache_schedule' );

		if ( false === $timestamp ) {
			wp_schedule_event( time(), 'daily', 'woocommerce_pdf_catalog_generate_cache' );
		}
	}

}
