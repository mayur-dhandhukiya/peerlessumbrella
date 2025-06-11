<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://welaunch.io
 * @since             1.0.
 * @package           WooCommerce_PDF_Catalog
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce PDF Catalog
 * Plugin URI:        https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 * Description:       Create a PDF-Catalog of your WooCommerce Shop
 * Version:           1.16.6
 * Author:            weLaunch
 * Author URI:        https://welaunch.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-pdf-catalog
 * Domain Path:       /languages
 * WC tested up to:   4.2.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-pdf-catalog-activator.php
 */
function activate_WooCommerce_PDF_Catalog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pdf-catalog-activator.php';
	WooCommerce_PDF_Catalog_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-pdf-catalog-deactivator.php
 */
function deactivate_WooCommerce_PDF_Catalog() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pdf-catalog-deactivator.php';
	WooCommerce_PDF_Catalog_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WooCommerce_PDF_Catalog' );
register_deactivation_hook( __FILE__, 'deactivate_WooCommerce_PDF_Catalog' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pdf-catalog.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_WooCommerce_PDF_Catalog() {

	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new WooCommerce_PDF_Catalog($version);
	$plugin->run();

	return $plugin;
}

function generate_cache() {
	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new WooCommerce_PDF_Catalog($version);
	$plugin->admin->generate_cache();
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'woocommerce/woocommerce.php') && (is_plugin_active('welaunch-framework/welaunch-framework.php') || is_plugin_active('redux-framework/redux-framework.php') || is_plugin_active('redux-dev-master/redux-framework.php') ) ){
	$WooCommerce_PDF_Catalog = run_WooCommerce_PDF_Catalog();

	// Cronjob
	add_action ('woocommerce_pdf_catalog_generate_cache', 'generate_cache'); 
} else {
	add_action( 'admin_notices', 'WooCommerce_PDF_Catalog_installed_notice' );
}

function WooCommerce_PDF_Catalog_installed_notice()
{
	?>
    <div class="error">
      <p><?php _e( 'WooCommerce PDF Catalog requires the WooCommerce and WeLaunch Framework plugin. Please install or activate them before! https://www.welaunch.io/updates/welaunch-framework.zip', 'woocommerce-pdf-catalog'); ?></p>
    </div>
    <?php
}