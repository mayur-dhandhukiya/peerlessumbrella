<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
class WooCommerce_Wishlist_Public extends WooCommerce_Wishlist {

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
	 * Enqueue Styles
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @return  boolean
	 */
	public function enqueue_styles()
	{
		global $woocommerce_wishlist_options;

		$this->options = $woocommerce_wishlist_options;

		if (!$this->get_option('enable')) {
			return false;
		}

		wp_enqueue_style('jquery-wishlist-modal', plugin_dir_url(__FILE__).'vendor/jquery-modal-master/jquery.modal.min.css', array(), '0.9.1', 'all');

		if($this->get_option('fontAwesome5')) {
			wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css', array(), '5.12.2', 'all');
		} else {
			wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'all');	
		}
		

		wp_enqueue_style($this->plugin_name.'-public', plugin_dir_url(__FILE__).'css/woocommerce-wishlist-public.css', array('jquery-wishlist-modal'), $this->version, 'all');

		$css = "";
		$modalHeight = $this->get_option('modalHeight');
		$modalPadding = $this->get_option('modalPadding');
		$modalTextColor = $this->get_option('modalTextColor');
		$modalBackgroundColor = $this->get_option('modalBackgroundColor');

		$backdropBackgroundColor = $this->get_option('backdropBackgroundColor');
		$backdropBackgroundColorRGBA = 'rgba(0,0,0,0.9)';
		if(isset($backdropBackgroundColor['rgba'])) {
			$backdropBackgroundColorRGBA = $backdropBackgroundColor['rgba'];
		}


		$css .= '.wishlistmodal { 
			padding-top: ' . $modalPadding['padding-top'] . '; 
			padding-right: ' . $modalPadding['padding-right'] . '; 
			padding-bottom: ' . $modalPadding['padding-bottom'] . '; 
			padding-left: ' . $modalPadding['padding-left'] . '; 
			background-color: ' . $modalBackgroundColor . ';
		}

		.wishlistmodal {
			color: ' . $modalTextColor . ';
			max-height: ' . $modalHeight . ';
		}

		.jquery-wishlistmodal.blocker  {
			background-color: ' . $backdropBackgroundColorRGBA . ';
		}';

		$customCSS = $this->get_option('customCSS');
		$css = $css . $customCSS;

		file_put_contents( dirname(__FILE__)  . '/css/woocommerce-wishlist-custom.css', $css);

		wp_enqueue_style( $this->plugin_name.'-custom', plugin_dir_url( __FILE__ ) . 'css/woocommerce-wishlist-custom.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @return  boolean
	 */
	public function enqueue_scripts()
	{
		global $woocommerce_wishlist_options;

		$this->options = $woocommerce_wishlist_options;

		if (!$this->get_option('enable')) {
			return false;
		}

		global $woocommerce;

		wp_enqueue_script('jquery-wishlist-modal', plugin_dir_url(__FILE__).'vendor/jquery-modal-master/jquery.modal.min.js', array('jquery'), '0.9.1', true);
		wp_enqueue_script(
			$this->plugin_name . '-public', 
			plugin_dir_url(__FILE__).'js/woocommerce-wishlist-public.js', 
			array('jquery', 'jquery-wishlist-modal'), 
			$this->version, 
			true
		);

		$forJS['ajax_url'] = admin_url('admin-ajax.php');
		$forJS['wishlistPage'] = get_permalink( $this->get_option('wishlistPage') );
		$forJS['wishlistSearchPage'] = get_permalink( $this->get_option('wishlistSearchPage') );
		$forJS['cookieLifetime'] = $this->get_option( 'cookieLifetime' );
		$forJS['trans'] = array(
			'btnAddText' => $this->get_option('textBtnAddText'),
			'btnAddedText' => $this->get_option('textBtnAddedText'),
			'selectWishlist' => $this->get_option('textSelectWishlist'),
			'createWishlist' => $this->get_option('textCreateWishlist'),
			'createWishlistName' => $this->get_option('textCreateWishlistName'),
			'editWishlist' => $this->get_option('textEditWishlist'),
			'viewWishlist' => $this->get_option('textViewWishlist'),
			'public' => $this->get_option('textPublic'),
			'shared' => $this->get_option('textShared'),
			'private' => $this->get_option('textPrivate'),
			'wishlistDeleted' => $this->get_option('textWishlistDeleted'),
			'noProducts' => $this->get_option('textNoProducts'),
		);
		$forJS = apply_filters('woocommerce_wishlist_js_settings', $forJS);
		wp_localize_script($this->plugin_name . '-public', 'woocommerce_wishlist_options', $forJS);
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

    	if(!$this->get_option('guestWishlists') && !is_user_logged_in()) {
    		return false;
    	}

		// Shop Loop Button
    	if($this->get_option('shopLoopButtonEnable')) {

    		$shopLoopButtonPosition = $this->get_option('shopLoopButtonPosition');
    		!empty($shopLoopButtonPosition) ? $shopLoopButtonPosition = $shopLoopButtonPosition : $shopLoopButtonPosition = 'woocommerce_after_shop_loop_item';

    		$shopLoopButtonPriority = $this->get_option('shopLoopButtonPriority');

    		add_action($shopLoopButtonPosition, array($this, 'wishlist_button'), $shopLoopButtonPriority);
    	}

		// Single Product Page Button
    	if($this->get_option('singleProductButtonEnable')) {

    		$singleProductButtonPosition = $this->get_option('singleProductButtonPosition');
    		!empty($singleProductButtonPosition) ? $singleProductButtonPosition = $singleProductButtonPosition : $singleProductButtonPosition = 'woocommerce_single_product_summary';

    		$singleProductButtonPriority = $this->get_option('singleProductButtonPriority');

    		add_action($singleProductButtonPosition, array($this, 'wishlist_button'), $singleProductButtonPriority);
    	}

    	add_action('wp_footer', array($this, 'wishlist_modal'), 20);
    }

    public function wishlist_button_shortcode($atts)
    {
    	global $product;

    	$args = shortcode_atts( array(
    		'product' => '',
    	), $atts );

    	$productId = intval( $args['product'] );

    	if(empty($productId)) {
    		$wishlistProduct = $product;
    	} else {
    		$wishlistProduct = wc_get_product($productId);
    	}	 

    	if(!$wishlistProduct) {
    		return false;
    	}

    	$productId = $wishlistProduct->get_id();

    	$excludeProductCategories = $this->get_option('excludeProductCategories');
    	$excludeProductCategoriesRevert = $this->get_option('excludeProductCategoriesRevert');
    	$terms = get_the_terms( $productId, 'product_cat' );
    	if(!empty($terms && !empty($excludeProductCategories)) ) {
    		foreach ($terms as $term) {
    			if($excludeProductCategoriesRevert) {
    				if(!in_array($term->term_id, $excludeProductCategories)) {
    					return TRUE;
    				}
    			} else {
    				if(in_array($term->term_id, $excludeProductCategories)) {
    					return TRUE;
    				}
    			}
    		}
    	}

    	$excludeProductsAuthor = $this->get_option('excludeProductsAuthor');
    	$excludeProductsAuthorRevert = $this->get_option('excludeProductsAuthorRevert');
    	if(!empty($excludeProductsAuthor)) {
    		$post = get_post($productId);
    		if($excludeProductsAuthorRevert) {
    			if(!in_array($post->post_author, $excludeProductsAuthor)) {
    				return TRUE;
    			}
    		} else {
    			if(in_array($post->post_author, $excludeProductsAuthor)) {
    				return TRUE;
    			}
    		}
    	}

    	$btn_text = $this->get_option('textBtnAddText');

    	$html = '<a href="#" class="button woocommerce-wishlist-add-product btn button btn-default theme-button theme-btn" data-product-id="' . $productId . '" rel="nofollow">' . $btn_text . '</a>';

    	return $html;
    }

    public function wishlist_button()
    {
    	global $product;

    	if(!is_object($product)) {
    		return;
    	}

    	$productId = $product->get_id();

    	$excludeProductCategories = $this->get_option('excludeProductCategories');
    	if(!$excludeProductCategories) {
    		$excludeProductCategories = array();
    	}

    	$excludeProductCategoriesRevert = $this->get_option('excludeProductCategoriesRevert');
    	$terms = get_the_terms( $productId, 'product_cat' );
    	if(!empty($excludeProductCategories) && !empty($terms)) {
    		foreach ($terms as $term) {
    			if($excludeProductCategoriesRevert) {
    				if(!in_array($term->term_id, $excludeProductCategories)) {
    					return TRUE;
    				}
    			} else {
    				if(in_array($term->term_id, $excludeProductCategories)) {
    					return TRUE;
    				}
    			}
    		}
    	}

    	$excludeProductsAuthor = $this->get_option('excludeProductsAuthor');
    	if(!$excludeProductsAuthor) {
    		$excludeProductsAuthor = array();
    	}

    	$excludeProductsAuthorRevert = $this->get_option('excludeProductsAuthorRevert');
    	if(!empty($excludeProductsAuthor)) {
    		$post = get_post($productId);
    		if($excludeProductsAuthorRevert) {
    			if(!in_array($post->post_author, $excludeProductsAuthor)) {
    				return TRUE;
    			}
    		} else {
    			if(in_array($post->post_author, $excludeProductsAuthor)) {
    				return TRUE;
    			}
    		}
    	}

    	$btn_text = $this->get_option('textBtnAddText');

    	$html = '<a href="#" class="button woocommerce-wishlist-add-product btn button btn-default theme-button theme-btn" data-product-id="' . $productId . '" rel="nofollow">' . $btn_text . '</a>';

    	echo $html;
    }

    public function wishlist_modal()
    {
    	echo '<div class="woocommerce-wishlist-modal">
    	<a href="#close-wishlistmodal" rel="wishlistmodal:close" class="close-wishlistmodal" aria-label="' . __('Close Wishlist modal', 'woocommerce-wishlist') . '"></a>
    	<div class="woocommerce-wishlist-modal-content">

    	</div>
    	</div>';
    }

    public function get_wishlists()
    {
    	$user_id = get_current_user_id();

    	if(!$user_id) {
    		return false;
    	}

    	$wishlists = array();

    	$args = array(
    		'post_type' => 'wishlist',
    		'posts_per_page' => -1,
    		'author' => $user_id
    	);

    	$users_wishlists = get_posts($args);
    	if(!empty($users_wishlists)) {
    		foreach ($users_wishlists as $users_wishlist) {
    			$wishlists[$users_wishlist->ID] = array(
    				'id' => $users_wishlist->ID,
    				'name' => $users_wishlist->post_title,
    				'visibility' => get_post_meta($users_wishlist->ID, 'visibility', true)
    			);
    		}
    	}

    	echo json_encode($wishlists);
    	wp_die();
    }

    public function get_wishlist()
    {
    	$wishlist = $this->validate_wishlist();

    	$wishlist = array(
    		'id' => $wishlist->ID,
    		'name' => $wishlist->post_title,
    		'author' => $wishlist->post_author,
    		'visibility' => get_post_meta($wishlist->ID, 'visibility', true),
    	);

    	echo json_encode($wishlist);
    	wp_die();
    }

    public function delete_wishlist()
    {
    	$wishlist = $this->validate_wishlist(true);

    	wp_delete_post($wishlist->ID);
    }

    public function view_wishlist()
    {
    	global $post;

    	$post = $this->validate_wishlist();

    	ob_start();

    	wc_get_template( 'woocommerce-wishlist.php', array(), '', plugin_dir_path(__FILE__) . 'templates/' );

    	$html = ob_get_clean();
    	echo $html;
    	die();
    }

    public function get_cookie_wishlist()
    {
    	global $product;

    	if(!isset($_POST['products']) || empty($_POST['products'])){
    		echo __('No Products found', 'woocommerce-wishlist');
    		die();
    	}

    	ob_start();

    	do_action( 'woocommerce_wishlist_before_wishlist' );

    	do_action( 'woocommerce_wishlist_before_products' );

    	$products = $_POST['products'];
    	foreach ($products as $product) {
    		$product = wc_get_product($product);
    		wc_get_template( 'woocommerce-wishlist-single-item.php', array('wishlistProduct' => $product), '', plugin_dir_path(__FILE__) . 'templates/' );
    	}

    	do_action( 'woocommerce_wishlist_after_products' );

    	do_action( 'woocommerce_wishlist_after_wishlist' );

		// Guest Wishlist share option
    	if($this->get_option('shareEnabled') == "1" && 
    		isset($_COOKIE['woocommerce_wishlist_products']) && 
    		!empty($_COOKIE['woocommerce_wishlist_products'])) {

    		$cookieProducts = implode(',', json_decode(stripslashes($_COOKIE['woocommerce_wishlist_products']), true) );

    	echo '<div class="woocommerce-wishlist-share">';

    	echo '<div class="woocommerce-wishlist-share-title">'. __('Share this Wishlist ...', 'woocommerce-wishlist') . '</div>';

    	$title = $this->get_option('shareTitle');
    	$url = get_permalink($this->get_option('wishlistPage')) . '?wishlist-products=' . $cookieProducts;


    	if($this->get_option('sharePrint') == "1") {
    		echo '<a href="javascript:window.print();" class="woocommerce-wishlist-share-print"><i class="fa fa-print"></i></a>';
    	}

    	if($this->get_option('shareFacebook') == "1") {
    		$data = array(
    			'title' => $title,
    			'u' => $url,
    		);
    		$share_url = '//www.facebook.com/sharer.php?' . http_build_query($data);
    		if($this->get_option('fontAwesome5')) {
    			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-facebook" target="_blank"><i class="fab fa-facebook"></i></a>';
    		} else {
    			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-facebook" target="_blank"><i class="fa fa-facebook"></i></a>';	
    		}

    	}

    	if($this->get_option('shareTwitter') == "1") {
    		$data = array('url' => $url);
    		$share_url = '//twitter.com/share?' . http_build_query($data);

    		if($this->get_option('fontAwesome5')) {
    			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-twitter" target="_blank"><i class="fab fa-twitter"></i></a>';
    		} else {
    			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-twitter" target="_blank"><i class="fa fa-twitter"></i></a>';
    		}
    	}

    	if($this->get_option('sharePinterest') == "1") {
    		$data = array('url' => $url);
    		$share_url = '//pinterest.com/pin/create/button/?' . http_build_query($data);

    		if($this->get_option('fontAwesome5')) {
    			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-pinterest" target="_blank"><i class="fab fa-pinterest"></i></a>';
    		} else {
    			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-pinterest" target="_blank"><i class="fa fa-pinterest"></i></a>';
    		}
    	}

    	if($this->get_option('shareEmail') == "1") {
    		$data = array(
    			'subject' => $title,
    			'body' => $url,
    		);
    		$share_url = 'mailto:?' . http_build_query($data);
    		echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-envelope" target="_blank"><i class="fa fa-envelope"></i></a>';
    	}


    	echo '</div>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();
    echo $html;
    die();
}

public function create_wishlist()
{
	if (!defined('DOING_AJAX') || !DOING_AJAX) {
		header('HTTP/1.1 400 No AJAX call', true, 400);
		die();
	}

	if (!isset($_POST['visibility']) || !isset($_POST['name'])) {
		header('HTTP/1.1 400 No visibility or name set.', true, 400);
		die();
	}

	$visibility = in_array($_POST['visibility'], array('public', 'shared', 'private')) ? $_POST['visibility'] : 'private';
	$name = sanitize_text_field($_POST['name']);

	if(empty($name)) {
		header('HTTP/1.1 400 Name', true, 400);
		die();
	}

	$data = array(
		'post_title' => $name, 
		'post_type' => 'wishlist', 
		'post_content' => '',
		'post_status' => 'publish'
	);
	$id = wp_insert_post($data);

	update_post_meta($id, 'visibility', $visibility);

	$wishlist_created = get_post($id);
	$wishlist = array(
		'ID' => $wishlist_created->ID,
		'name' => $wishlist_created->post_title,
		'visibility' => get_post_meta($id, 'visibility', true),
	);

        // Add product to a newly create wishlist
	if(isset($_POST['product']) && !empty($_POST['product'])) {

		$product_id = $_POST['product'];
		$current_products = get_post_meta($wishlist_created->ID, 'products', true);

		if(!$current_products) {
			$current_products = array(
				$product_id => array(
					'product_id' => $product_id,
					'added' => current_time('timestamp'),
				),
			);
		} else {
			$current_products[$product_id] = array(
				'product_id' => $product_id,
				'added' => current_time('timestamp'),
			);
		}

		update_post_meta($wishlist_created->ID, 'products', $current_products);
	}

	echo json_encode($wishlist);
	wp_die();
}

public function edit_wishlist()
{
	if (!defined('DOING_AJAX') || !DOING_AJAX) {
		header('HTTP/1.1 400 No AJAX call', true, 400);
		die();
	}

	if (!isset($_POST['visibility']) || !isset($_POST['name'])) {
		header('HTTP/1.1 400 No visibility or name set.', true, 400);
		die();
	}

	$wishlist = $this->validate_wishlist(true);
	$visibility = in_array($_POST['visibility'], array('public', 'shared', 'private')) ? $_POST['visibility'] : 'private';
	$name = sanitize_text_field($_POST['name']);

	if(empty($name)) {
		header('HTTP/1.1 400 Name Missing', true, 400);
		die();
	}

	$data = array(
		'ID' => $wishlist->ID,
		'post_title' => $name, 
		'post_type' => 'wishlist', 
		'post_content' => '',
		'post_status' => 'publish'
	);
	$id = wp_update_post($data);

	update_post_meta($id, 'visibility', $visibility);

	$wishlist_updated = get_post($id);
	$wishlist = array(
		'ID' => $wishlist_updated->ID,
		'name' => $wishlist_updated->post_title,
		'visibility' => get_post_meta($id, 'visibility', true)
	);
	echo json_encode($wishlist);
	wp_die();
}

public function add_product()
{
	$wishlist = $this->validate_wishlist();
	$wishlistPage = get_permalink( $this->get_option('wishlistPage') );
	$favorites = '/favorites/';
	$product = $this->validate_product();
	$product_id = $product->get_id();
	$site_url = site_url($favorites.'?wishlist='. $wishlist->ID);
	$current_products = get_post_meta($wishlist->ID, 'products', true);

	if($current_products && isset($current_products[$product_id])) {
		$html = sprintf( __('Product %s is already on your Wishlist %s!', 'woocommerce-wishlist'), $product->get_title(), $wishlist->post_title);
		$html .= sprintf( '<br><br><a class="button btn button btn-default theme-button theme-btn" href="'. $site_url.'">'. $this->get_option('textViewWishlist') .'</a>');
		echo $html;
		die();
	}

	if(!$current_products) {
		$current_products = array(
			$product_id => array(
				'product_id' => $product_id,
				'added' => current_time('timestamp'),
			),
		);
	} else {
		$current_products[$product_id] = array(
			'product_id' => $product_id,
			'added' => current_time('timestamp'),
		);
	}

	update_post_meta($wishlist->ID, 'products', $current_products);

	$html = sprintf( __('Product %s has been added to your Wishlist %s.', 'woocommerce-wishlist'), $product->get_title(), $wishlist->post_title);
	$html .= sprintf( '<br><br><a class="button btn button btn-default theme-button theme-btn" href="'. $site_url.'">'. $this->get_option('textViewWishlist') .'</a>');
	echo $html;
	wp_die();
}

public function remove_product()
{
	$wishlist = $this->validate_wishlist(true);
	$product = $this->validate_product();
	$product_id = $product->get_id();

	$current_products = get_post_meta($wishlist->ID, 'products', true);
	unset($current_products[$product_id]);

	update_post_meta($wishlist->ID, 'products', $current_products);
	$html = sprintf( __('Product %s has been removed from your Wishlist %s.', 'woocommerce-wishlist'), $product->get_title(), $wishlist->post_title);
	echo $html;
	die();
}

public function load_wishlist_template($template) {
	global $post;

	if ($post->post_type == "wishlist" && $template !== locate_template(array("single-wishlist.php"))){
		return plugin_dir_path( __FILE__ ) . "single-wishlist.php";
	}

	return $template;
}

private function validate_wishlist($own_user_only = false)
{
	if (!defined('DOING_AJAX') || !DOING_AJAX) {
		header('HTTP/1.1 400 No AJAX call', true, 400);
		die();
	}

	if (!isset($_POST['wishlist'])) {
		header('HTTP/1.1 400 No wishlist ID set.', true, 400);
		die();
	}

	$wishlist = get_post(intval($_POST['wishlist']));

	if(empty($wishlist)){
		header('HTTP/1.1 400 Wishlist not found.', true, 400);
		die();
	}

	if($wishlist->post_author != get_current_user_id() && $own_user_only) {
        	// $visibility = get_post_meta($wishlist->ID, 'visibility', true);
		header('HTTP/1.1 400 Not your wishlist.', true, 400);
		die();
	}

	return $wishlist;
}

private function validate_product()
{
	if (!isset($_POST['product'])) {
		header('HTTP/1.1 400 No product ID set.', true, 400);
		die();
	}

	$product = wc_get_product(intval($_POST['product']));
	if(empty($product)){
		header('HTTP/1.1 400 product not found.', true, 400);
		die();
	}

	return $product;
}

public function wishlist_export()
{
	if(!isset($_GET['export-wishlist']) || empty($_GET['export-wishlist'])) {
		return false;
	}

	if(!isset($_GET['type']) || empty($_GET['type'])) {
		header('HTTP/1.1 400 type not defined.', true, 400);
		die('?');
	}

	$products = array();
	$type = $_GET['type'];

	if(isset($_GET['export-wishlist']) && !empty($_GET['export-wishlist'])) {
		$wishlist = get_post(intval($_GET['export-wishlist']));
		$products = get_post_meta($wishlist->ID, 'products', true);
	} else {
		if(isset($_COOKIE['woocommerce_wishlist_products']) && !empty($_COOKIE['woocommerce_wishlist_products'])) {
			$products = implode(',', json_decode(stripslashes($_COOKIE['woocommerce_wishlist_products']), true) );
		}
	}

	if(empty($products)) {
		header('HTTP/1.1 400 no products found.', true, 400);
		die();
	}

	$export_data = array();
	foreach ($products as $product_id => $product_data) {
		$product = wc_get_product($product_id);

		$image = "";

		if($product->get_image_id()) {
			$image = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail')[0];
		}

		$export_data[$product_id] = array(
			'Image' => $image,
			'SKU' => $product->get_sku(),
			'Name' => $product->get_name(),
			'Price' => $product->get_price(),
			'Description' => $product->get_short_description(),
		);

	}
	if($type == "csv") {
		$this->export_as_csv($export_data);
	} elseif($type == "xls") {
		$this->export_as_xls($export_data);
	}

}

public function export_as_csv($products)
{
	$fp = fopen('php://output', 'w');
	$first = true;

	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=test.csv");

	foreach ($products as $product) {
		if($first) {
			fputcsv($fp, array_keys($product), ";");
			$first = false;
		}
		fputcsv($fp, $product, ";");
	}

	fclose($fp);
	    // tell the browser it's going to be a csv file
	header('Content-Type: application/csv');
	    // tell the browser we want to save it instead of displaying it
	header('Content-Disposition: attachment; filename="wishlist-export' . date('Y-m-d_H-i-s') . '.csv";');
	    // make php send the generated csv lines to the browser
	fpassthru($fp);

	exit();
}

public function export_as_xls($products)
{
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	$sheet->getDefaultColumnDimension()->setWidth(20);
	$sheet->setCellValueByColumnAndRow(1, 2, get_bloginfo('name') . ' – ' . __('Wishlist Export'));
	$sheet->getStyle("A1:X4")->getFont()->setBold( true );

        $row = 4; // 1-based index
        $first = true;
        foreach ($products as $product) {
        	$col = 1;
        	if ($first) {

                // Set Keys
        		$keys = array_keys($product);
        		foreach ($keys as $key) {
        			$sheet->setCellValueByColumnAndRow($col, $row, $key);
        			$col++;
        		}
        		$row++;
        		$col = 1;
        		$first = false;
        	}
        	foreach ($product as $prod_key => $prod_value) {

            	// Image
        		if($prod_key == "Image") {
        			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        			$uploads = wp_upload_dir();
        			$file_path = str_replace( $uploads['baseurl'], $uploads['basedir'], $prod_value );

					$drawing->setPath($file_path); // put your path and image here
					$drawing->setCoordinates('A' . $row);
					$drawing->setWorksheet($spreadsheet->getActiveSheet());
					
				} else {
					$sheet->getRowDimension($row)->setRowHeight(120);
					$sheet->setCellValueByColumnAndRow($col, $row, $prod_value);
				}
				$sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

				$col++;
			}
			$row++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="wishlist-export_' . date('Y-m-d_H-i-s') . '.xlsx"');
		header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = new Xlsx($spreadsheet);
        ob_end_clean();
        $objWriter->save('php://output');
        exit();
        return true;
    }
}