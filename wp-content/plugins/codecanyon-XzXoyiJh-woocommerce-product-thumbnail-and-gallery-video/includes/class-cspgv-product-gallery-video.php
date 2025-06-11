<?php

      if ( ! defined( 'ABSPATH' ) ) {
            exit; // Exit if accessed directly
      }

      /**
	 * It's the main class that does all the things.
	 *
	 * @class Cspgv_Product_Gallery_Video
	 * @version 1.0.0
	 * @since 1.0.0
	 */
      final class Cspgv_Product_Gallery_Video{

        private static $instance = null;
        
        public static function loader(){
            self::$instance = new self();
            self::$instance->init_actions();
        }


        /**
		 * Setup the hooks, actions and filters.
         * 
		 * @since 1.0.0
		 * @access private
		 */
        private function init_actions(){
                
            add_action('init', array($this, 'run'),0);

            add_action( 'init', array( $this, 'textdomain' ) );
        }
            
        /**
		 * Admin and frontend class object.
		 *
		 * @since 1.0.0
		 */
        public function run(){
            if(is_admin()){
                require_once CSPGV_PATH.'admin/class-cspgv-product-gallery-video-admin.php';
                $class='Cspgv_Product_Gallery_Video_Admin';
            }else{
                require_once CSPGV_PATH.'public/class-cspgv-product-gallery-video-frontend.php';
                $class='Cspgv_Product_Gallery_Video_Frontend';
            }
            new $class();
        }

        /**
		 * Loads the plugin's translated strings. 
		 *
		 * @since 1.0.0
		 */
		public function textdomain() {
            load_textdomain(CSPGV_TEXT_DOMAIN, CSPGV_PATH.'languages/'.CSPGV_TEXT_DOMAIN.'-'.get_locale().'.mo');
			load_plugin_textdomain(CSPGV_TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages/' );
		}
    }