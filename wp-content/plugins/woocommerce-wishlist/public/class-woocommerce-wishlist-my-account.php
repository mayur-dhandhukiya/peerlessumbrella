<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://welaunch.io/plugins/woocommerce-reward-points/
 * @since      1.0.0
 *
 * @package    WooCommerce_delivery
 * @subpackage WooCommerce_delivery/public
 */
class WooCommerce_Wishlist_My_Account extends WooCommerce_Wishlist {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $options
	 */
	protected $options;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->data = array();
	}

	/**
	 * Inits the My Account
	 *
	 * @since    1.0.0
	 */
    public function init()
    {
		global $woocommerce_wishlist_options;
		$this->options = $woocommerce_wishlist_options;

		if (!$this->get_option('enable') || !$this->get_option('myAccount')) {
			return false;
		}

		$endpoint =  'my-wishlists';

		add_action( 'woocommerce_account_' . $endpoint . '_endpoint', array($this, 'endpoint_content') );
		add_action( 'query_vars', array($this, 'query_vars'), 0);
		add_rewrite_endpoint( $endpoint, EP_ROOT | EP_PAGES );

		add_filter( 'woocommerce_account_menu_items', array($this, 'menu_items'));
	}

	/**
	 *  Add new query var.
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://welaunch.io/plugins/
	 * @param   [type]                       $vars [description]
	 * @return  [type]                             [description]
	 */
	public function query_vars( $vars )
	{
		$endpoint =  'my-wishlists';
		$vars[] = $endpoint;
		return $vars;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://welaunch.io/plugins/
	 * @param   [type]                       $items [description]
	 * @return  [type]                              [description]
	 */
	public function menu_items( $items ) 
	{
		$customerLogoutItem = false;
		if(isset($items['customer-logout'])) {
			$customerLogoutItem = $items['customer-logout'];
			unset($items['customer-logout']);
		}

		$items['my-wishlists'] = $this->get_option('myAccountMenuTitle');

		if($customerLogoutItem && $this->get_option('myAccountReorderLogout')) {
			$items[] = $customerLogoutItem;
		}

		return $items;
	}

	/**
	 * Endpoint contents
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://welaunch.io/plugins/
	 * @return  [type]                       [description]
	 */
	public function endpoint_content() 
	{
		$userId = get_current_user_id();
		if(!$userId) {
			return;
		}

		echo do_shortcode('[woocommerce_wishlist]');
	}
}