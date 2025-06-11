<?php

if(!function_exists("r3d_trace")){

	function r3d_trace($var){
		echo("<pre style='position:relative;z-index:999999;background:rgba(0,0,0,.5);color:#0f0;font-size:14px;margin:0;padding:0;pointer-events:none;'>");
		print_r($var);
		echo("</pre>");
	}

}

/*plugin class*/
class Bookshelf_Addon {

	public $PLUGIN_VERSION;
	public $PLUGIN_DIR_URL;
	public $PLUGIN_DIR_PATH;

	// Singleton
	private static $instance = null;
	
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	protected function __construct() {
		$this->add_actions();
		register_activation_hook($this->my_plugin_basename(), array( $this, 'activation_hook' ) );
	}

	public function activation_hook($network_wide) {
	}
	
	public function enqueue_scripts() {
	}
	
	protected function get_translation_array() {
		return Array('worker_src' => $this->my_plugin_url().'js/pdfjs/pdf.worker.min.js',
		             'cmap_url' => $this->my_plugin_url().'js/pdfjs/cmaps/',
            'objectL10n' => array(
                'loading' => esc_html__('Loading...', 'bookshelf')
               
            ));
	}

	public function admin_link($links) {
		array_unshift($links, '<a href="' . get_admin_url() . 'options-general.php?page=book_shelves">Admin</a>');
		return $links;
	}
	
	protected function get_options_menuname() {
		return 'bookshelf_options';
	}
	
	protected function get_options_pagename() {
		return 'bookshelf_options';
	}
	
	public function admin_menu() {

	}
	
	public function admin_init() {

		// wp_enqueue_style( 'pdfemb_admin_other_css', $this->my_plugin_url().'css/pdfemb-admin-other.css', array(), $this->PLUGIN_VERSION  );

		// add_action( 'enqueue_block_editor_assets', array($this, 'gutenberg_enqueue_block_editor_assets') );

		wp_register_script("real3d_flipbook_shelves", $this->PLUGIN_DIR_URL."js/shelves.js", array('jquery' ),$this->PLUGIN_VERSION);

	}

	// public function attachment_fields_to_edit($form_fields, $post) {
	// 	return $form_fields;
	// }

	public function init() {
		add_shortcode( 'bookshelf', array($this, 'on_shortcode') );

		// Gutenberg block
		// if (function_exists('register_block_type')) {
		// 	register_block_type( 'pdfemb/pdf-embedder-viewer', array(
		// 		'render_callback' => array($this, 'on_shortcode')
		// 	) );
		// }

		// add_action( 'enqueue_block_assets', array($this, 'gutenberg_enqueue_block_assets') );
	}

	public function plugins_loaded() {
		load_plugin_textdomain( 'bookshelf', false, dirname($this->my_plugin_basename()).'/lang/' );
	}

	protected function add_actions() {

		add_action('plugins_loaded', array($this, 'plugins_loaded') );

		add_action('init', array($this, 'init') );
		
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 5, 0 );

		if (is_admin()) {

			add_action('admin_init', array($this, 'admin_init'), 5, 0 );
			
			add_action('admin_menu', array($this, 'admin_menu'));
			
	        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'admin_link'));
			add_action('wp_ajax_r3d_shelf_save', array( $this, 'save_options') );
			add_action('wp_ajax_nopriv_r3d_shelf_save', array($this, 'save_options') );

			add_action('wp_ajax_r3d_import', array( $this,  'ajax_import_shelves' ));
			add_action('wp_ajax_nopriv_r3d_import', array( $this,  'ajax_import_shelves' ));

			add_action('real3d_flipbook_menu', array($this,'real3d_flipbook_installed'));

			// add_filter('contextual_help', array($this,'plugin_help'), 10, 3);

		}
	}

	function plugin_help($contextual_help, $screen_id, $screen) {

	   // global $my_plugin_hook;
	   // if ($screen_id == $my_plugin_hook) {

	      $contextual_help = 'This is where I would provide help to the user on how everything in my admin panel works. Formatted HTML works fine in here too.';
	   // }
	   return $contextual_help;
	}

	public function include_bookshelf_admin(){

		include_once( plugin_dir_path(__FILE__).'admin.php' );
		
	}

	public function on_shortcode($atts, $content=null) {



		$args = shortcode_atts( 
			array(
				'id' => '-1',
				'pdf' => '-1',
				'width' =>'600',
				'height' =>'400',
				'viewmode' =>'-1'
			), 
			$atts
		);

		$id = $atts['id'];
		$viewMode = '';
		if(isset($atts['viewmode']))
			$viewMode = $atts['viewmode'];
		
		$shelf = get_option('shelf_'.$id);
		$image = $shelf['image'];
		$covers = $shelf['covers'];
		$coverSize = $shelf['coverSize'];
		$coverMaxWidth = $shelf['coverMaxWidth'];
		$coverMarginV = $shelf['coverMarginV'];
		$coverMarginH = $shelf['coverMarginH'];
		$coverShadow = $shelf['coverShadow'];
		$coversAlign = $shelf['coversAlign'];

		$shelfImageMarginTop = $shelf['shelfImageMarginTop'];
		$shelfAlign = $shelf['shelfAlign'];
		$shelfBg = $shelf['shelfBg'];
		$shelfPadding = $shelf['shelfPadding'];

		$output = '<div style="text-align: '.$shelfAlign.';background-color:'.$shelfBg.';padding:'.$shelfPadding.'px;"><div style="width:100%; height:auto; z-index: 99; position: relative;text-align:'.$coversAlign.'">';

		foreach ($covers as $cover) {

			$shortcodeText = '[real3dflipbook id="'.$cover['id'].'" mode="lightbox"'; 

			// trace($viewMode);
			if($viewMode == "3d" || $viewMode == "2d" || $viewMode == "webgl" || $viewMode == "swipe"){
				$shortcodeText .= ' viewmode="'.$viewMode.'"';
			}

			$shortcodeText .= ' thumbcss=";width:100%; display:block; vertical-align:top; box-shadow:'.$coverShadow.';" containercss="display: inline-block;cursor: pointer; margin: '.$coverMarginV.'px '.$coverMarginH.'px; width: '.$coverSize.'% !important; max-width:'.$coverMaxWidth.'px;"]';

			// trace($shortcodeText);

			$output .= do_shortcode($shortcodeText);

		}

		$output .= '</div>';

		if($image){
			$output .= '<img style="z-index:0; height: auto; max-width: 100%; vertical-align: top; margin: 0 auto; margin-top:'.$shelfImageMarginTop.'px; " class="shelf_img" alt="Shelf Wood" src="'.$image.'">';
		}

		$output .= '</div>';

		$args['workerSrc'] = plugins_url().'/pdf-reader/js/pdf.worker.min.js';

		$pdf = $args['pdf'];
		$width = $args['width'];
		$height = $args['height'];

		$plugin_dir = plugins_url().'/pdf-reader';

		// $output = do_shortcode('[real3dflipbook id="1" mode="lightbox"]');

		return $output;

	}

	public function real3d_flipbook_installed(){

		//add submenu item "Shelves" to menu item "Real3d Flipbook"
		add_submenu_page( 'real3d_flipbook_admin', 'Shelves', 'Shelves', 'publish_posts', 'book_shelves', array($this, 'include_bookshelf_admin'));
		
	}

	public function save_options() {
			
		$current_id = '';

		if (isset($_GET['shelfId']) ) {

			$current_id = $_GET['shelfId'];
			$shelf = get_option("shelf_".(string)$current_id);

			$new = array_merge($shelf, $_POST);
			delete_option('shelf_'.(string)$current_id);
			add_option('shelf_'.(string)$current_id,$new);

		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function ajax_import_shelves() {

		check_ajax_referer( 'r3d_nonce', 'security' );

		$json = stripslashes($_POST['shelves']);

		$newShelves = json_decode($json, true);

		if((string)$json != "" && is_array($newShelves)){

			$shelf_ids = get_option('shelf_ids'); 

			foreach ($shelf_ids as $id) {
				delete_option('shelf_'.(string)$id);
			}

			$shelf_ids = array();

			foreach ($newShelves as $s) {
				$id = $s['id'];

				add_option('shelf_'.(string)$id, $s);
				array_push($shelf_ids,(string)$id);
			}

			update_option('shelf_ids', $shelf_ids);
		}

		wp_die(); // this is required to terminate immediately and return a proper response

	}

	// Gutenberg enqueues

	// protected function gutenberg_enqueue_block_editor_assets() {
	// 	wp_enqueue_script(
	// 		'pdfemb-gutenberg-block-js', // Unique handle.
	// 		$this->my_plugin_url(). 'js/pdfemb-blocks.js',
	// 		array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
	// 		$this->PLUGIN_VERSION
	// 	);

	// 	wp_enqueue_style(
	// 		'pdfemb-gutenberg-block-css', // Handle.
	// 		$this->my_plugin_url(). 'css/pdfemb-blocks.css', // editor.css: This file styles the block within the Gutenberg editor.
	// 		array( 'wp-edit-blocks' ), // Dependencies, defined above.
	// 		$this->PLUGIN_VERSION
	// 	);
	// }

	// protected function gutenberg_enqueue_block_assets() {
	// 	wp_enqueue_style(
	// 		'pdfemb-gutenberg-block-backend-js', // Handle.
	// 		$this->my_plugin_url(). 'css/pdfemb-blocks.css', // style.css: This file styles the block on the frontend.
	// 		array( 'wp-blocks' ), // Dependencies, defined above.
	// 		$this->PLUGIN_VERSION
	// 	);
	// }

	protected function my_plugin_basename() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			$basename = basename(dirname(__FILE__)).'/'.basename(__FILE__);
		}
		return $basename;
	}
	
	protected function my_plugin_url() {
		$basename = plugin_basename(__FILE__);
		if ('/'.$basename == __FILE__) { // Maybe due to symlink
			return plugins_url().'/'.basename(dirname(__FILE__)).'/';
		}
		// Normal case (non symlink)
		return plugin_dir_url( __FILE__ );
	}
}