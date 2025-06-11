<?php
/**
* Plugin Name: WooCommerce Approve Customer
* Plugin URI: https://codiffy.com/
* Description: WooCommerce approve new customer & custom registration fields.
* Version: 1.0.0
* Author: Codiffy
* Author URI: https://codiffy.com/
* Support: https://www.codiffy.com/
* License: GPL-2.0+
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain: wc-war
* Domain Path: /languages/
**/
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if ( !defined('WC_WAR_DIR') )
    define('WC_WAR_DIR', plugin_dir_path(__FILE__));
if ( !defined('WC_WAR_URL') )
    define('WC_WAR_URL', plugin_dir_url(__FILE__));
if ( !class_exists('WAR_Approve_Registration') ) {
    class WAR_Approve_Registration {
    	public function __construct(){
    		/**
			 * Check if WooCommerce is installed and active.
			 **/
    		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins'))) ) {
				$this->war_init();
	    	}else{
	    		add_action('admin_notices',  array($this, 'war_admin_notices'));
	    	}
		}
		public function war_init(){
			/**        
		    * Load language.     
		    */
		    if ( function_exists( 'load_plugin_textdomain' ) ){
				load_plugin_textdomain('wc-war', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			}
			require_once WC_WAR_DIR . 'includes/class-war-common.php';
			if(is_admin()){
				require_once WC_WAR_DIR . 'includes/class-war-admin-settings.php';
			}else{
				if ( 'yes' == get_option('war_enable_module_functionality') )
				require_once WC_WAR_DIR . 'includes/class-war-frontend.php';
			}
		}
		public function war_admin_notices(){
			global $pagenow;
	    	if($pagenow==='plugins.php'){
		    	$class = 'notice notice-error is-dismissible';
	            $message = esc_html__('WooCommerce Approve Customer needs WooCommerce to be installed and active.', 'wc-war');
	            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	        }
		}
	}
	new WAR_Approve_Registration();
}