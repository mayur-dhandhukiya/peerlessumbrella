<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://plugins.db-dzine.com
 * @since      1.0.0
 *
 * @package    WooCommerce_wishlist
 * @subpackage WooCommerce_wishlist/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_wishlist
 * @subpackage WooCommerce_wishlist/public
 * @author     Daniel Barenkamp <support@db-dzine.com>
 */
class WooCommerce_Wishlist_Search extends WooCommerce_Wishlist {

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
	}

    /**
     * Init the Bought together
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function init()
    {
        global $woocommerce_wishlist_options;
        $this->options = $woocommerce_wishlist_options;

		if (!$this->get_option('enable')) {
			return false;
		}

    }

    public function get_search_page()
    {
		if(!$this->get_option('guestWishlists') && !is_user_logged_in()) {
			$wishlistLoginPage = $this->get_option('wishlistLoginPage');
			if(!empty($wishlistLoginPage)) {
				$login_url = get_permalink($wishlistLoginPage);
			} else {
				$login_url = wp_login_url( get_permalink() );
			}
			$txt = '<a href="' . $login_url . '">';
				$txt .= __('You need to create an account or login to use our wishlist.', 'woocommerce-wishlist');
			$txt .= '</a>';
			return $txt;
		}

	    $args = shortcode_atts( array(
	        'product' => '',
	    ), $atts );

		$product = intval( $args['product'] );

		ob_start();
		wc_get_template( 'woocommerce-wishlist-search.php', array(), '', plugin_dir_path(__FILE__) . 'templates/' );
		$html = ob_get_clean();
		
		return $html;
    }

    public function search_wishlists()
    {
    	$term = $_POST['term'];

        $args = array(
            'post_type' => 'wishlist',
            'post_status' => 'publish',
            's' => $term,
            'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key'     => 'visibility',
					'value'   => 'public',
					'compare' => '=',
				),
			),
        );
        $search = new WP_Query($args);
        $wishlists = array();
        $count = 0;
        if(!empty($search->posts)) {
        	$wishlistPage = get_permalink( $this->get_option('wishlistPage') );
			foreach ($search->posts as $wishlist) {
				$products = get_post_meta($wishlist->ID, 'products', true);

				if(empty($products)) {
					continue;
				}
				$wishlists[] = array(
					'name' => $wishlist->post_title,
					'author' => __('By ', 'woocommerce-wishlist') . get_the_author_meta('user_nicename', $wishlist->post_author),
					'products' => count($products) . __(' Products', 'woocommerce-wishlist'),
					'url' => $wishlistPage . '?wishlist=' . $wishlist->ID
				);
			}
	        $count = count($wishlists);
	        $message = sprintf( __('%d Wishlists found for %s', 'woocommerce-wishlist'), $count, $term);
        } else {
        	$message = sprintf( __('No Wishlists found for %s', 'woocommerce-wishlist'), $term);
        }

        $response = array(
            'count' => count($search->posts),
            'wishlists' => $wishlists,
            'message' => $message,
        );

        die(json_encode($response));
    }
}