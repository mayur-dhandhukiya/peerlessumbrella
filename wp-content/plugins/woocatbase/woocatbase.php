<?php
/*
Plugin Name: Woo Category Base Permalink Fixer
Plugin URI: https://masterns-studio.com/code-factory/wordpress-plugin/woo-category-base/
Description: A simple plugin that fixes 404 error when product category base is set the same as shop base in WooCommerce 
Version: 2.3
Author: MasterNs
Author URI: https://masterns-studio.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 4.9.8
WC requires at least: 3.0.0
WC tested up to: 3.4.5
*/
if ( ! defined( 'WPINC' ) ) { die; }
define('WPG_ROOT', dirname(__FILE__));
include(dirname(__FILE__)."/woocatbase_status.php");

 
/**
 * Flush rewrite rules on activation and deactivation.
 **/

register_deactivation_hook( __FILE__, 'woocatbase_flush_deactivation' );
register_activation_hook( __FILE__, 'woocatbase_flush_activation' );

/**
 * Flush rewrite rules on product_cat change detected
 **/

add_action('created_product_cat', 'woocatbase_flush_activation', 10, 2);
add_action( 'edited_product_cat', 'woocatbase_flush_activation', 10, 2 );
add_action( 'delete_product_cat', 'woocatbase_flush_activation', 10, 2 );

function woocatbase_flush_deactivation() {   
   	$arr = array();
   	$my_options = get_option('woocatbase_options');
   	$my_options['cat_structure'] = "empty";
   update_option('woocatbase_options', $my_options);
   flush_rewrite_rules();
}

function woocatbase_flush_activation() {
   woocatbase_make_structure(false);
   flush_rewrite_rules();
}
/*
*
* delete options if removed
*
*/
function woocatbase_delete_plugin() {
	delete_option('woocatbase_options');
	update_option( 'woocatbase_ord_ref','' );
	update_option( 'woocatbase_cust_ref','' );
	update_option( 'woocatbase_allowed','' );
	update_option( 'woocatbase_licence_active', 'false' );
}
register_uninstall_hook(__FILE__, 'woocatbase_delete_plugin');

/*
*
* set initial values
*
*/
function woocatbase_setdefaults() {
	$tmp = get_option('woocatbase_options');
	if($tmp === FALSE) {		
		$arr = array();
		update_option('woocatbase_options', $arr);
	}
}
//register_activation_hook(__FILE__, 'woocatbase_setdefaults');



/*
*
* init options page & validate inputs
*
*/
function woocatbase_validate($input) {

		$input['wcb_licence'] =  wp_filter_nohtml_kses($input['wcb_licence']);
		return $input;
	}
function woocatbase_init(){		
	register_setting( 'woocatbase_options', 'woocatbase_options','woocatbase_validate' );	
	
}
add_action( 'admin_init', 'woocatbase_init' );

/*
*
* add admin settings page
*
*/
function woocatbase_add_page() {
	add_options_page('Fixer Status', 'WCBP Fixer', 'manage_options', 'woocatbase_status.php', 'woocatbase_status');
	
}
add_action( 'admin_menu', 'woocatbase_add_page' );

/*
*
* add admin files
*
*/

add_action( 'admin_enqueue_scripts', 'woocatbase_enqueue' );
function woocatbase_enqueue($hook) {
	//wp_enqueue_style('woocatbase_style', plugins_url('woocatbase_style.css?_='.time(), __FILE__));	     
	wp_enqueue_script( 'ajax-script', plugins_url('/js/woocatbase_script.js', __FILE__), array('jquery'), '3' );	
	wp_localize_script( 'ajax-script', 'woocatbase_object',
	array( 
	'ajax_url' => admin_url( 'admin-ajax.php' )			
	) );
}

/*
*
* add custom plugin action links
*
*/
function woocatbase_action_links( $links ) {
	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/options-general.php?page=woocatbase_status.php' ) ) . '">' . __( 'Status', 'textdomain' ) . '</a>'
	), $links );
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocatbase_action_links' );

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {	

	add_filter( 'rewrite_rules_array', 'woocatbase_rules', 20);
	woocatbase_allowed();

	function woocatbase_rules($rules ){	
		$permalinks = get_option( 'woocommerce_permalinks' );
		$cat_base = $permalinks['category_base'];
		$cat_base = rtrim($cat_base, " \t/");
		$cat_base = ltrim($cat_base, " \t/");
		$shop_id = get_option( 'woocommerce_shop_page_id' ); 		
		$shop = get_post($shop_id);
		$shop_base = $shop->post_name;

		$children = get_pages( array( 'child_of' => $shop_id ) );
		$children_no = count( $children );

		
				
		$set = get_option('woocatbase_options');
		$opt_rules = $set['cat_structure'];
		
			if (($shop_base == $cat_base )&&($opt_rules != 'empty')){
			    $main_cat_rules = array();				
			   	if($opt_rules != ''){
					foreach($opt_rules as $opt_rule) {
						//main categories
						$main_cat = $opt_rule['main-cat'];
						$main_cat_rules_temp = array(
							$shop_base.'/'.$main_cat.'/page/([0-9]{1,})/?$' => 'index.php?product_cat='.$main_cat.'&paged=$matches[1]',
							$shop_base.'/'.$main_cat.'/?$' => 'index.php?product_cat='.$main_cat,
						 );
						 $main_cat_rules = $main_cat_rules_temp + $main_cat_rules;

						    if(isset( $opt_rule['sub-cats'] )){
								$seco = $opt_rule['sub-cats'] ;
						    	$sub_cat_rules = array();
						    	foreach($seco as $sub_cat) {
						    		$sub_cat_rules_temp = array(
							    	 $shop_base.'/'.$main_cat.'/'.$sub_cat['sub-cat'].'/page/([0-9]{1,})/?$' => 'index.php?product_cat='.$sub_cat['sub-cat'].'&paged=$matches[1]',
						        	 $shop_base.'/'.$main_cat.'/'.$sub_cat['sub-cat'].'/?$' => 'index.php?product_cat='.$sub_cat['sub-cat'].'',
						        	);
						        	$sub_cat_rules = $sub_cat_rules + $sub_cat_rules_temp ;

							        	
							        	if(isset( $sub_cat['xsub-cats'] )){
											$sub_seco = $sub_cat['xsub-cats'];
									    	$xsub_cat_rules = array();
									    	foreach($sub_seco as $xsub_cat) {
									    		$xsub_cat_rules_temp = array(
										    	 $shop_base.'/'.$main_cat.'/'.$sub_cat['sub-cat'].'/'.$xsub_cat['xsub-cat'].'/page/([0-9]{1,})/?$' => 'index.php?product_cat='.$xsub_cat['xsub-cat'].'&paged=$matches[1]',
									        	 $shop_base.'/'.$main_cat.'/'.$sub_cat['sub-cat'].'/'.$xsub_cat['xsub-cat'].'/?$' => 'index.php?product_cat='.$xsub_cat['xsub-cat'].'',
									        	);
									        	$xsub_cat_rules = $xsub_cat_rules + $xsub_cat_rules_temp ;
									    	}
									    	$sub_cat_rules = $sub_cat_rules + $xsub_cat_rules ;
									    }
						    	}
						    	$main_cat_rules = $main_cat_rules + $sub_cat_rules;
						    }			        			
					}
					if($children_no > 0 ){
						$child_rules = array();
						foreach ( $children as $page ) {
							$child_id = $page->ID;
							$child_slug = $page->post_name;
							$child_rules_temp = array(
								$shop_base.'/'.$child_slug.'/page/([0-9]{1,})/?$' => 'index.php?page_id='.$child_id.'&paged=$matches[1]',
								$shop_base.'/'.$child_slug.'/?$' => 'index.php?page_id='.$child_id.'$matches[1]',
							);
							   $child_rules = $child_rules + $child_rules_temp ;
						}
						$main_cat_rules = $main_cat_rules + $child_rules;
					}									
					return $main_cat_rules + $rules;				        		
				} else { 
					if($children_no > 0 ){
						$child_rules = array();
						foreach ( $children as $page ) {
							$child_id = $page->ID;
							$child_slug = $page->post_name;
							$child_rules_temp = array(
								$shop_base.'/'.$child_slug.'/page/([0-9]{1,})/?$' => 'index.php?page_id='.$child_id.'&paged=$matches[1]',
								$shop_base.'/'.$child_slug.'/?$' => 'index.php?page_id='.$child_id.'$matches[1]',
							);
							   $child_rules = $child_rules + $child_rules_temp ;
						}
						$main_cat_rules = $main_cat_rules + $child_rules;
					}
			    	return $main_cat_rules + $rules;
				}
			} else {
				return $rules;
			}		
	} 
}


function woocatbase_allowed() {
$allowed = get_option('woocatbase_allowed');
	if($allowed == 'allowed'){
		$myorder = get_option('woocatbase_ord_ref');
		$cust = get_option( 'woocatbase_cust_ref');		
		if(($cust != '')&&($myorder != '')){		
		require dirname(__FILE__).'/plugin-update-checker/plugin-update-checker.php';
			$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
						'https://masterns-studio.com/wp-json/plugin_upd/v1/plugina/2357/'.$myorder.'/'.urlencode($cust).'/',
						__FILE__,
						'woocatbase'
			);
		}		
	}	
}

