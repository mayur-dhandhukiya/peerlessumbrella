<?php 
/**
 * Plugin Name:       Addify - Registration Fields Addon
 * Plugin URI:        http://www.addifypro.com
 * Description:       WordPress Registration fields plugin allows you to add extra fields to your registration form. Registration Fields Addon is compatible with both WordPress & WooCommerce. Support 14 types of fields and compatible with Addify plugins.
 * Version:           1.2.0
 * Author:            Addify
 * Developed By:      Addify
 * Author URI:        http://www.addifypro.com
 * Support:                http://www.addifypro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       addify_reg
 */

if (! defined('WPINC') ) {
    die;
}




if (!class_exists('Addify_Registration_Fields_Addon') ) { 

    class Addify_Registration_Fields_Addon {

        public function __construct() {

            $this->afreg_global_constents_vars();

            add_action('wp_loaded', array( $this, 'afreg_init' ));
            add_action( 'init', array($this, 'afreg_custom_post_type' ));

            if (is_admin() ) {
                include_once AFREG_PLUGIN_DIR . 'admin/class-afreg-fields-admin.php';
            } else {
                include_once AFREG_PLUGIN_DIR . 'front/class-afreg-fields-front.php';
            }            
        }

        public function afreg_global_constents_vars() {
            
            if (!defined('AFREG_URL') ) {
                define('AFREG_URL', plugin_dir_url(__FILE__));
            }

            if (!defined('AFREG_BASENAME') ) {
                define('AFREG_BASENAME', plugin_basename(__FILE__));
            }

            if (! defined('AFREG_PLUGIN_DIR') ) {
                define('AFREG_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }
        }

        
        public function afreg_init() {
            if (function_exists('load_plugin_textdomain') ) {
                load_plugin_textdomain('addify_reg', false, dirname(plugin_basename(__FILE__)) . '/languages/');
            }
        }

        public function afreg_custom_post_type() {

            $labels = array(
            'name'                => esc_html__('Registration Fields', 'addify_reg'),
            'singular_name'       => esc_html__('Registration Field', 'addify_reg'),
            'add_new'             => esc_html__('Add New Field', 'addify_reg'),
            'add_new_item'        => esc_html__('Add New Field', 'addify_reg'),
            'edit_item'           => esc_html__('Edit Registration Field', 'addify_reg'),
            'new_item'            => esc_html__('New Registration Field', 'addify_reg'),
            'view_item'           => esc_html__('View Registration Field', 'addify_reg'),
            'search_items'        => esc_html__('Search Registration Field', 'addify_reg'),
            'exclude_from_search' => true,
            'not_found'           => esc_html__('No registration field found', 'addify_reg'),
            'not_found_in_trash'  => esc_html__('No registration field found in trash', 'addify_reg'),
            'parent_item_colon'   => '',
            'all_items'           => esc_html__('All Fields', 'addify_reg'),
            'menu_name'           => esc_html__('Registration Fields', 'addify_reg'),
          );
        
          $args = array(
            'labels' => $labels,
            'menu_icon'  => plugin_dir_url( __FILE__ ).'images/small_logo_white.png',
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 30,
            'rewrite' => array('slug' => 'addify_reg', 'with_front'=>false ),
            'supports' => array('title')
          );
        
          register_post_type( 'afreg_fields', $args );

        }

    }

    new Addify_Registration_Fields_Addon();

}

