<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 * @since      1.0.0
 *
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/includes
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
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/includes
 * @author     Daniel Barenkamp <support@welaunch.io>
 */
class WooCommerce_PDF_Catalog {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WooCommerce_PDF_Catalog_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'woocommerce-pdf-catalog';
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
	 * - WooCommerce_PDF_Catalog_Loader. Orchestrates the hooks of the plugin.
	 * - WooCommerce_PDF_Catalog_i18n. Defines internationalization functionality.
	 * - WooCommerce_PDF_Catalog_Admin. Defines all hooks for the admin area.
	 * - WooCommerce_PDF_Catalog_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pdf-catalog-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-pdf-catalog-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-pdf-catalog-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-pdf-catalog-attributes.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		$options = get_option('woocommerce_pdf_catalog_options');

		if(isset($options['tableView']) && $options['tableView'] == "1") {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/table/class-woocommerce-pdf-catalog-public.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/table/class-woocommerce-pdf-catalog-category-templates.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/table/class-woocommerce-pdf-catalog-products-templates.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/table/class-woocommerce-pdf-catalog-product-templates.php';
		} else {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-pdf-catalog-public.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-pdf-catalog-category-templates.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-pdf-catalog-products-templates.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-pdf-catalog-product-templates.php';
		}
		
        if (file_exists(plugin_dir_path(dirname(__FILE__)).'admin/Tax-meta-class/Tax-meta-class.php')) {
            require_once plugin_dir_path(dirname(__FILE__)).'admin/Tax-meta-class/Tax-meta-class.php';
        }
		
	    // Load MPDF
     	if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php' ) && !class_exists('\Mpdf\Mpdf') ) {
	        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
	    }

		$this->loader = new WooCommerce_PDF_Catalog_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WooCommerce_PDF_Catalog_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$this->plugin_i18n = new WooCommerce_PDF_Catalog_i18n();

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

		$this->admin = new WooCommerce_PDF_Catalog_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'plugins_loaded', $this->admin, 'load_redux' );
		$this->loader->add_action( 'init', $this->admin, 'init', 1);		
        $this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles', 20);
        $this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts', 20);
        $this->loader->add_action( 'admin_footer', $this->admin, 'add_preview_frame' );
		
		add_shortcode( 'pdf_catalog', array($this->admin, 'generate_pdf_catalog_link'));

		$this->attributes = new WooCommerce_PDF_Catalog_Attributes( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $this->attributes, 'init');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->public = new WooCommerce_PDF_Catalog_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $this->public, 'init', 2);

		$this->loader->add_action('wp_ajax_nopriv_woocommerce_pdf_catalog_send_email', $this->public, 'send_email');
        $this->loader->add_action('wp_ajax_woocommerce_pdf_catalog_send_email', $this->public, 'send_email');
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
	 * @return    WooCommerce_PDF_Catalog_Loader    Orchestrates the hooks of the plugin.
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
	 * Get Option Key
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @param   [type]                       $option [description]
	 * @return  [type]                               [description]
	 */
    protected function get_option($option) {
    	if(!is_array($this->options)) {
    		return false;
    	}
    	if(!array_key_exists($option, $this->options))
    	{
    		return false;
    	}
    	return $this->options[$option];
    }	

}