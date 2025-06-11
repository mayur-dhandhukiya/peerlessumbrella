<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://webbycrown.com/
 * @since      1.0.0
 *
 * @package    Wc_style
 * @subpackage Wc_style/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wc_style
 * @subpackage Wc_style/public
 * @author     webbycrown <test@gmail.com>
 */
class Wc_style_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'wp_footer', array( $this, 'custom_script' ) );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_style_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_style_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc_style-public.css', array(), time(), 'all' );
		wp_enqueue_style( $this->plugin_name.'-media', plugin_dir_url( __FILE__ ) . 'css/wc_style_media.css', array(), time(), 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_style_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_style_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc_style-public.js', array( 'jquery' ), $this->version, false );

	}

	public function custom_script() { ?>
		<script type="text/javascript">
			jQuery( document ).on( 'click', '#menu-item-has-children span.wd-nav-opener', function() {
				jQuery( this ).toggleClass( 'wd-active' );
				// var t = jQuery( this ).parents( 'li.menu-item-has-children' );
				// var $this = jQuery( this );
				// if( !jQuery( t ).hasClass( 'opener-page' ) ){
				// 	jQuery( $this ).removeClass( 'wd-active' );
				// }else{
					
				// }
			});
		</script>
	<?php }

}
