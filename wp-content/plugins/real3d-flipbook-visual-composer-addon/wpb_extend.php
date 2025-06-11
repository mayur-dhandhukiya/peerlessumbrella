<?php
/*
Plugin Name: Real 3D Flipbook for WPBakery Page Builder
Plugin URI: http://wpbakery.com/vc
Description: Real3D Flipbook for WPBakery Page Builder (formerly Visual Composer)
Version: 1.0.1
Author: creativeinteractivemedia
Author URI: http://codecanyon.net/user/creativeinteractivemedia/portfolio?ref=creativeinteractivemedia
*/


// don't load directly
if (!defined('ABSPATH'))
    die('-1');

class Real3DFlipbook_VCAddon
{
    function __construct()
    {
        // We safely integrate with VC with this hook
        add_action('init', array(
            $this,
            'integrateWithVC'
        ));
        
        // Use this when creating a shortcode addon
        add_shortcode('real3dflipbook', array(
            $this,
            'real3dflipbook_vc'
        ));
        
        // Register CSS and JS
        add_action('wp_enqueue_scripts', array(
            $this,
            'loadCssAndJs'
        ));
    }
    
    public function integrateWithVC()
    {
        // Check if WPBakery Page Builder is installed
        if (!defined('WPB_VC_VERSION')) {
            // Display notice that Extend WPBakery Page Builder is required
            add_action('admin_notices', array(
                $this,
                'showVcVersionNotice'
            ));
            return;
        }

        // Check if WPBakery Page Builder is installed
        if (!defined('REAL3D_FLIPBOOK_VERSION')) {
            // Display notice that Real3D Flipbook is required
            add_action('admin_notices', array(
                $this,
                'showReal3DVersionNotice'
            ));
            return;
        }


        $real3dflipbooks_ids = get_option('real3dflipbooks_ids');
        $names_array = array();
        foreach ($real3dflipbooks_ids as $id) {
          $book = get_option('real3dflipbook_'.$id);
          $name = $book['name'];
          // array_push($names_array, $name);
          $names_array[__($name, 'vc_extend')] = $name;

        }

       $select_flipbook_dropdown =  array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __('Select flipbook', 'vc_extend'),
                    'param_name' => 'name',
                    'value' => $names_array,
                    'save_always' => true,
                );

        /*
        Add your WPBakery Page Builder logic here.
        Lets call vc_map function to "register" our custom shortcode within WPBakery Page Builder interface.
        
        More info: http://kb.wpbakery.com/index.php?title=Vc_map
        */
        vc_map(array(
            "name" => __("Real3D Flipbook", 'vc_extend'),
            "description" => __("Responsive Flipbook plugin", 'vc_extend'),
            "base" => "real3dflipbook",
            "class" => "",
            "controls" => "full",
            "icon" => plugins_url('assets/r3d.jpg', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_extend_my_class"
            "category" => __('Content', 'js_composer'),
            //'admin_enqueue_js' => array(plugins_url('assets/vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('assets/vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
            "params" => array(
              $select_flipbook_dropdown,
 
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __('Embed mode', 'vc_extend'),
                    'param_name' => 'mode',
                    'value' => array(
                        __('Normal (boxed)', 'vc_extend') => 'normal',
                        __('Fullscreen', 'vc_extend') => 'fullscreen',
                        __('Lightbox', 'vc_extend') => 'lightbox'
                    ),
                    'save_always' => true,
                    'description' => __('Flipbook embed mode', 'vc_extend'),
                    // 'group' => __('View mode', 'vc_extend')
                ),
                array(
                    'type' => 'dropdown',
                    'holder' => 'div',
                    'class' => '',
                    'heading' => __('View mode', 'vc_extend'),
                    'param_name' => 'viewmode',
                    'value' => array(
                        __('WebGL', 'vc_extend') => 'webgl',
                        __('3d', 'vc_extend') => '3d',
                        __('2d', 'vc_extend') => '2d',
                        __('swipe', 'vc_extend') => 'swipe',
                    ),
                    'save_always' => true,
                    'description' => __('Flipbook view mode', 'vc_extend'),
                    // 'group' => __('View mode', 'vc_extend')
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("PDF url", 'vc_extend'),
                    "param_name" => "pdf",
                    'save_always' => true,
                    "value" => __("", 'vc_extend'),
                    "description" => __("If set flibbook will display specified pdf", 'vc_extend')
                ),

                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Aspect", 'vc_extend'),
                    "param_name" => "aspect",
                    'save_always' => true,
                    "value" => __("1.5", 'vc_extend'),
                    "description" => __("Width / height ratio of flipbook container", 'vc_extend')
                ),

                  array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Link text", 'vc_extend'),
                    "param_name" => "lightboxtext",
                    'save_always' => true,
                    // 'not_empty' => true,
                    "value" => __("", 'vc_extend'),
                    "description" => __("Text link that will open lightbox", 'vc_extend'),
                    'group' => __('Lightbox', 'vc_extend')
                ),

                   array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("CSS class", 'vc_extend'),
                    "param_name" => "lightboxcssclass",
                    'save_always' => true,
                    // 'not_empty' => true,
                    "value" => __("", 'vc_extend'),
                    "description" => __("CSS class that will open lightbox", 'vc_extend'),
                    'group' => __('Lightbox', 'vc_extend')
                ),

                    array(
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __("Thumbnail image url", 'vc_extend'),
                    "param_name" => "lightboxthumbnailurl",
                    'save_always' => true,
                    // 'not_empty' => true,
                    "value" => __("", 'vc_extend'),
                    "description" => __("Thumbnail image that will open lightbox", 'vc_extend'),
                    'group' => __('Lightbox', 'vc_extend')
                ),

             
            )
        ));
    }
    
    /*
    Shortcode logic how it should be rendered
    */
    public function real3dflipbook_vc($atts, $content = null)
    {

        $content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content

        $output = "<div>{$content}</div>";
        
        return $output;
    }
    
   
    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function loadCssAndJs()
    {
        wp_register_style('vc_extend_style', plugins_url('assets/vc_extend.css', __FILE__));
        wp_enqueue_style('vc_extend_style');
        
        // If you need any javascript files on front end, here is how you can load them.
        //wp_enqueue_script( 'vc_extend_js', plugins_url('assets/vc_extend.js', __FILE__), array('jquery') );
    }

    public function showVcVersionNotice()
    {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']) . '</p>
        </div>';
    }

    public function showReal3DVersionNotice()
    {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="https://codecanyon.net/item/real3d-flipbook-wordpress-plugin/6942587?ref=creativeinteractivemedia" target="_blank">Real3D Flipbook</a></strong> plugin to be installed and activated on your site.', 'vc_extend'), $plugin_data['Name']) . '</p>
        </div>';
    }


}

// Finally initialize code
new Real3DFlipbook_VCAddon();
