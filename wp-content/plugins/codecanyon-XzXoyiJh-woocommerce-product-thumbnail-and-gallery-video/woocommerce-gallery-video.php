<?php
    /**
     * Plugin Name: WooCommerce Product Gallery Video
     * Plugin URI: https://plugins.crevolsoft.com
     * Description: WooCommerce Product Gallery Video is plugin for add video in product gallery.
     * Version: 1.1.2
     * Author: Crevol software team
     * Author URI: https://crevolsoft.com
     * Requires at least: 3.6
     * Tested up to: 5.0.3
     * WC requires at least: 2.9
     * WC tested up to: 3.5.4
     * Text Domain: wc-product-gallery-video
     * Domain Path: /languages/
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }


    /* Set plugin version constant. */
    define( 'CSPGV_VERSION', '1.0.0' );

    /* Set plugin text-domain constant. */
    define('CSPGV_TEXT_DOMAIN','wc-product-gallery-video');

    /* Set constant path to the plugin directory. */
    define( 'CSPGV_PATH', plugin_dir_path(__FILE__));

    /* Set the constant path to the plugin directory URI. */
    define('CSPGV_URL',plugin_dir_url(__FILE__));


    if( class_exists('woocommerce') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins',get_option('active_plugins')))){

        add_action('plugins_loaded', 'run_cspgv_plugin'); 
        add_filter( 'plugin_action_links', 'cspgv_product_gallery_action', 10, 5 );
    }

    /**
     * run split order payments plugin.
     * @since 1.0.0
     * @return void
     */
    function run_cspgv_plugin(){
        require CSPGV_PATH.'includes/class-cspgv-product-gallery-video.php';
        Cspgv_Product_Gallery_Video::loader();
    }

    /**
	* plugin action.
    * @since 1.0.0
	* @return array
	*/
    function cspgv_product_gallery_action($actions, $plugin_file) {
        static $plugin;

        if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
        if ($plugin == $plugin_file) {

            $settings = array('settings' => '<a href="'.site_url().'/wp-admin/admin.php?page=wc-settings&tab=cspgv_settings">'.esc_attr__('Settings',CSPGV_TEXT_DOMAIN).'</a>',
            'support' => '<a href="http://plugins.crevolsoft.com" >'.esc_attr__('Support',CSPGV_TEXT_DOMAIN).'</a>',
            'documentation' => '<a href="http://crevolsoft.com/blog" >'.esc_attr__('Docs',CSPGV_TEXT_DOMAIN).'</a>'
            );
            $actions = array_merge($settings, $actions);  
        }
        return $actions;
    }