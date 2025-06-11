<?php
if ( ! defined( 'ABSPATH' ) ) { 
	exit; // restict for direct access
}


/*
 * Plugin Name:       Addify - Approve New User
 * Plugin URI:        http://www.addifypro.com
 * Description:       Ability to manualy approve or reject users, Compatible with wordpress and woocommerce.
 * Version:           1.3.0
 * Author:            Addify
 * Developed By:  	  Addify
 * Author URI:        http://www.addifypro.com
 * Support:		  	  http://www.addifypro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       addify_anu
 */


if ( !class_exists( 'Addify_Approve_New_User' ) ) {

	class Addify_Approve_New_User {

		function __construct() {

			add_action( 'wp_loaded', array( $this, 'anu_main_init' ) );
			$this->anu_constant_vars();

			if ( is_admin() ) {
				require(addify_anu_plugindir.'approve_new_user_admin_class.php');
			} else {
				require(addify_anu_plugindir.'approve_new_user_front_class.php');
			}
			
		}


		function anu_main_init() {
	        if ( function_exists( 'load_plugin_textdomain' ) )
	            load_plugin_textdomain( 'addify_anu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	   	}


	   	function anu_constant_vars() {


            if ( !defined( 'addify_anu_url' ) )
                define( 'addify_anu_url', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'addify_anu_basename' ) )
                define( 'addify_anu_basename', plugin_basename( __FILE__ ) );

            if ( ! defined( 'addify_anu_plugindir' ) )
                define( 'addify_anu_plugindir', plugin_dir_path( __FILE__ ) );
        }
	}

	new Addify_Approve_New_User();

}