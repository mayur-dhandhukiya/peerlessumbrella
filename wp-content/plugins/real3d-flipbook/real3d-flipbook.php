<?php

	/*
	Plugin Name: Real 3D Flipbook
	Plugin URI: http://codecanyon.net/user/creativeinteractivemedia/portfolio?ref=creativeinteractivemedia
	Description: Premium Responsive Real 3D FlipBook  
	Version: 3.9
	Author: creativeinteractivemedia
	Author URI: http://codecanyon.net/user/creativeinteractivemedia?ref=creativeinteractivemedia
	*/

	include_once( plugin_dir_path(__FILE__).'/includes/Real3DFlipbook.php' );

	$real3dflipbook = Real3DFlipbook::get_instance();
	define('REAL3D_FLIPBOOK_VERSION', '3.9');
	$real3dflipbook->PLUGIN_VERSION = REAL3D_FLIPBOOK_VERSION;
	$real3dflipbook->PLUGIN_DIR_URL = plugin_dir_url( __FILE__ );
	$real3dflipbook->PLUGIN_DIR_PATH = plugin_dir_path( __FILE__ );


	if(!function_exists("trace")){

		function trace($var){
			echo("<pre style='position:relative;z-index:999999;background:rgba(0,0,0,.5);color:#0f0;font-size:14px;margin:0;padding:0;'>");
			print_r($var);
			echo("</pre>");
		}
	}