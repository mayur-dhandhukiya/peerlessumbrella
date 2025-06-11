<?php

/*
Plugin Name: Book shelf
Plugin URI: http://codecanyon.net/user/creativeinteractivemedia/portfolio?ref=creativeinteractivemedia
Description: Responsive shelf for dixplaying book covers
Version: 1.0.7
Author: creativeinteractivemedia
Author URI: http://codecanyon.net/user/creativeinteractivemedia?ref=creativeinteractivemedia
*/

include_once( plugin_dir_path(__FILE__).'/includes/main.php' );

$bookshelf_addon_plugin = Bookshelf_Addon::get_instance();
$bookshelf_addon_plugin->PLUGIN_VERSION = '1.0.7';
$bookshelf_addon_plugin->PLUGIN_DIR_URL = plugin_dir_url( __FILE__ );
$bookshelf_addon_plugin->PLUGIN_DIR_PATH = plugin_dir_path( __FILE__ );