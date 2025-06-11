<?php
/*
Plugin Name: WooCommerce Invoice Me Gateway
Plugin URI: http://woocommerce.rmweblab.com/invoice-me-payment-gateway
Description: Extends WooCommerce Payment Gateway allow customers to purchase products with Invoice Me Checkout Option.
Version: 2.1
Author: RM Web Lab
Author URI: http://rmweblab.com/
Text Domain: woocommerce-gateway-invoiceme
Domain Path: /languages
WC tested up to: 3.2.3
WC requires at least: 3.0.0

Copyright: © 2014 RMWebLab.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * Main InvoiceMe class which sets the gateway up for us
 */
class WC_InvoiceMe {

	/**
	 * Constructor
	 */
	public function __construct() {
		define( 'WC_INVOICEME_VERSION', '2.1' );
		define( 'WC_INVOICEME_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WC_INVOICEME_MAIN_FILE', __FILE__ );

		// Actions
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'capture_order' ) );
		add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'capture_order' ) );
		add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'cancel_order' ) );
		add_action( 'woocommerce_order_status_on-hold_to_refunded', array( $this, 'cancel_order' ) );
	}

	/**
	 * Add relevant links to plugins page
	 * @param  array $links
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=invoiceme' ) . '">' . __( 'Settings', 'woocommerce-gateway-invoiceme' ) . '</a>',
			'<a href="http://rmweblab.com/support">' . __( 'Support', 'woocommerce-gateway-invoiceme' ) . '</a>',
			'<a href="http://woocommerce.rmweblab.com/invoice-me-payment-gateway">' . __( 'Docs', 'woocommerce-gateway-invoiceme' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Init localisations and files
	 */
	public function init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		// Gateway Invoice Me Admin
		require_once( 'inc/class-wc-gateway-invoiceme-admin.php' );

		// Includes
		require_once( 'inc/class-wc-gateway-invoiceme.php' );

		// Localisation
		load_plugin_textdomain( 'woocommerce-gateway-invoiceme', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Role based customer availability
	 * Return true if valid customer false otherwise
	 */
	public function invoiceme_allowed_customer_role(){
		global $wp_roles;
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role = array_shift($roles);

		if(get_option('invoiceme_allow_roles')){
			$allowed_role_arr = get_option('invoiceme_allow_roles');
			if (is_array($allowed_role_arr) && in_array($role, $allowed_role_arr, true)) {
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}




	/**
	 * Register the gateway for use
	 */
	public function register_gateway( $methods ) {
		//check if gateway class exist;
		if ( ! class_exists( 'WC_Gateway_InvoiceMe' ) ) {
			return;
		}
		$is_visitors_enabled = get_option('invoiceme_allow_visitors');
		//enable for visitor
		if($is_visitors_enabled == 'yes'){
			$methods[] = 'WC_Gateway_InvoiceMe';
		}elseif(is_user_logged_in()){
			$current_user_id = get_current_user_id();
			if(((esc_attr( get_the_author_meta( 'invoice_me', $current_user_id ) ) == 'yes') || current_user_can('manage_options') || $this->invoiceme_allowed_customer_role())){
				$methods[] = 'WC_Gateway_InvoiceMe';
			}
		}


		return $methods;
	}

	/**
	 * Capture payment when the order is changed from on-hold to complete or processing
	 *
	 * @param  int $order_id
	 */
	public function capture_order( $order_id ) {
		$order = new WC_Order( $order_id );

		//backward capability
		$payment_method    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->payment_method          : $order->get_payment_method();

		if ( $payment_method == 'invoiceme' ) {
			//Sent Invoice to customer
			date_default_timezone_set("UTC");
			update_post_meta( $order_id, '_invoice_me_status', 'sent' );
			$date_time = date("Y-m-d H:i:s");
			update_post_meta( $order_id, '_invoice_me_date', $date_time );

		}
	}

	/**
	 * Cancel pre-auth on cancellation
	 *
	 * @param  int $order_id
	 */
	public function cancel_order( $order_id ) {
		$order = new WC_Order( $order_id );

		$payment_method    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->payment_method          : $order->get_payment_method();

		if ( $payment_method == 'invoiceme' ) {
			if(get_post_meta( $order_id, '_invoice_me', true )){
				//clean up invoice me meta value for this order.
				delete_post_meta($order_id, '_invoice_me_status', 'pending');
				delete_post_meta($order_id, '_invoice_me', 'yes');
			}

		}
	}
}

new WC_InvoiceMe();
