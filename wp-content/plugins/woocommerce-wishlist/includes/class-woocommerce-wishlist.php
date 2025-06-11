<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://woocommerce.db-dzine.com
 * @since      1.0.0
 *
 * @package    WooCommerce_wishlist
 * @subpackage WooCommerce_wishlist/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WooCommerce_wishlist
 * @subpackage WooCommerce_wishlist/includes
 * @author     Daniel Barenkamp <support@db-dzine.com>
 */
class WooCommerce_Wishlist {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WooCommerce_wishlist_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */

	public function __construct($version) {

		$this->plugin_name = 'woocommerce-wishlist';
		$this->version = $version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WooCommerce_wishlist_Loader. Orchestrates the hooks of the plugin.
	 * - WooCommerce_wishlist_i18n. Defines internationalization functionality.
	 * - WooCommerce_wishlist_Admin. Defines all hooks for the admin area.
	 * - WooCommerce_wishlist_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-wishlist-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-wishlist-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-wishlist-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-wishlist-post-type.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-wishlist-statistics.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-wishlist-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-wishlist-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-wishlist-search.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-wishlist-my-account.php';

        // Load Vendors
        if (file_exists(plugin_dir_path(dirname(__FILE__)).'vendor/autoload.php')) {
            require_once plugin_dir_path(dirname(__FILE__)).'vendor/autoload.php';
        }


		$this->loader = new WooCommerce_wishlist_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WooCommerce_wishlist_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$this->plugin_i18n = new WooCommerce_wishlist_i18n();

		$this->loader->add_action( 'plugins_loaded', $this->plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->plugin_admin = new WooCommerce_wishlist_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action('admin_init', $this->plugin_admin, 'init' );
		$this->loader->add_action('plugins_loaded', $this->plugin_admin, 'load_extensions');

        $this->plugin_post_type = new WooCommerce_Wishlist_Post_Type($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('init', $this->plugin_post_type, 'init');
        $this->loader->add_action('add_meta_boxes', $this->plugin_post_type, 'add_custom_metaboxes', 10, 2);

        $this->statistics = new WooCommerce_Wishlist_Statistics($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_menu', $this->statistics, 'add_menu', 120);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		
		$this->plugin_public = new WooCommerce_Wishlist_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action('wp_enqueue_scripts', $this->plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts');

		$this->loader->add_action( 'init', $this->plugin_public, 'init', 10 );

		add_shortcode( 'woocommerce_wishlist_button', array($this->plugin_public, 'wishlist_button_shortcode'));

		// AJAX
        $this->loader->add_action('wp_ajax_view_wishlist', $this->plugin_public, 'view_wishlist');
        $this->loader->add_action('wp_ajax_nopriv_view_wishlist', $this->plugin_public, 'view_wishlist');
        $this->loader->add_action('wp_ajax_delete_wishlist', $this->plugin_public, 'delete_wishlist');
        $this->loader->add_action('wp_ajax_edit_wishlist', $this->plugin_public, 'edit_wishlist');
        $this->loader->add_action('wp_ajax_delete_product', $this->plugin_public, 'delete_product');
       	$this->loader->add_action('wp_ajax_create_wishlist', $this->plugin_public, 'create_wishlist');
        $this->loader->add_action('wp_ajax_get_wishlists', $this->plugin_public, 'get_wishlists');
        $this->loader->add_action('wp_ajax_get_wishlist', $this->plugin_public, 'get_wishlist');
        $this->loader->add_action('wp_ajax_add_product', $this->plugin_public, 'add_product');
        $this->loader->add_action('wp_ajax_remove_product', $this->plugin_public, 'remove_product');

		$this->loader->add_action('init', $this->plugin_public, 'wishlist_export');

		$this->loader->add_action('wp_ajax_get_cookie_wishlist', $this->plugin_public, 'get_cookie_wishlist');
		$this->loader->add_action('wp_ajax_nopriv_get_cookie_wishlist', $this->plugin_public, 'get_cookie_wishlist');

		// Wishlist Pge
        $this->plugin_page = new WooCommerce_Wishlist_Page( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'init', $this->plugin_page, 'init', 10 );
        add_shortcode( 'woocommerce_wishlist', array($this->plugin_page, 'wishlist_page'));

        // Wishlist Search
        $this->search = new WooCommerce_Wishlist_Search( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'init', $this->search, 'init', 10 );
        add_shortcode( 'woocommerce_wishlist_search', array($this->search, 'get_search_page'));
        $this->loader->add_action('wp_ajax_search_wishlists', $this->search, 'search_wishlists');
        $this->loader->add_action('wp_ajax_nopriv_search_wishlists', $this->search, 'search_wishlists');

		// My Account Wishlist
		$this->my_account = new WooCommerce_Wishlist_My_Account( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'init', $this->my_account, 'init', 10 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WooCommerce_wishlist_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    /**
     * Get Options
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   mixed                         $option The option key
     * @return  mixed                                 The option value
     */
    protected function get_option($option)
    {
        if (!is_array($this->options)) {
            return false;
        }

        if (!array_key_exists($option, $this->options)) {
            return false;
        }

        return $this->options[$option];
    }
}