<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 * @since      1.0.0
 *
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_PDF_Catalog
 * @subpackage WooCommerce_PDF_Catalog/admin
 * @author     Daniel Barenkamp <support@welaunch.io>
 */
class WooCommerce_PDF_Catalog_Admin {

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

	private $notice;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->notice = "";
	}

	public function load_redux()
	{
        if(!is_admin() || !current_user_can('administrator') || (defined('DOING_AJAX') && DOING_AJAX && (isset($_POST['action']) && !$_POST['action'] == "woocommerce_pdf_catlaog_options_ajax_save") )) {
            return false;
        }

	    // Load the theme/plugin options
	    if ( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'admin/options-init.php' ) ) {
	        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/options-init.php';
	    }
	}

   /**
     * Enqueue Admin Styles
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name.'-admin', plugin_dir_url(__FILE__).'css/woocommerce-pdf-catalog-admin.css', array(), $this->version, 'all');
    }

    /**
     * Enqueue Admin Scripts
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_scripts()
    {
    	$forJS = array(
    		'frontend_url' => get_site_url(),
    	);
        wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__).'js/woocommerce-pdf-catalog-admin.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name . '-admin', 'woocommerce_pdf_catalog_options', $forJS);
    }

	public function init()
	{
        global $woocommerce_pdf_catalog_options;

        if(!is_admin() || !current_user_can('administrator') || (defined('DOING_AJAX') && DOING_AJAX)){
            $woocommerce_pdf_catalog_options = get_option('woocommerce_pdf_catalog_options');
        }

		$cacheFolder = $this->get_uploads_dir( 'woocommerce-pdf-catalogs' );
		if ( ! file_exists( $cacheFolder ) ) {
			mkdir( $cacheFolder, 0775, true );
		}

   		if(isset($_GET['delete_cached_pdfs'])) {

			foreach (new DirectoryIterator($cacheFolder) as $fileInfo) {
			    if(!$fileInfo->isDot()) {

			    	$file = $fileInfo->getPathname();
			        $delete = unlink($file);
			        if(!$delete) {
			        	$this->notice .= "File " . basename($file) . ' could not be deleted</br>';
			        } else {
			        	$this->notice .= "File " . basename($file) . ' deleted</br>';
			        }
			    }
			}

			add_action( 'admin_notices', array($this, 'notice' ));
	   	}

	   	if(isset($_GET['regenerate_cached_pdfs'])) {
	   		$this->generate_cache(true);
	   	}

	   	$this->add_custom_meta_fields();
	}

    public function notice()
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo $this->notice ?></p>
        </div>
        <?php
    }

	public function generate_pdf_catalog_link($atts)
	{
	    $attributes = shortcode_atts( array(
	        'category' => 'full',
	        'text' => 'Product Catalog (PDF)',
	    ), $atts );

	    if($attributes['category'] == "email") {
	    	return '<a href="#" class="woocommerce-pdf-catalog-email-button button alt"><i class="fa fa-envelope fa-1x "></i>  ' . $attributes['text'] . '</a>';	;
	    } else {
			$actual_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

			if( strpos($actual_link, '?') === FALSE ){ 
				$actual_link = $actual_link . '?'; 
			} else {
			 	$actual_link = $actual_link . '&'; 
			}

			$css = "";
			if($attributes['category'] == "full") {
				$css = ' woocommerce_pdf_catalog_button_full';
			}

			return '<a href="' . $actual_link . 'pdf-catalog=' . $attributes['category'] . '" class="woocommerce_pdf_catalog_button button alt ' . $css . '" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>  ' . $attributes['text'] . '</a>';
		}

	    
	}

    /**
     * Add Custom Meta Field Color to System, Type, Status
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     */
    public function add_custom_meta_fields()
    {
        $prefix = 'woocommerce_pdf_catalog_';
        $custom_taxonomy_meta_config = array(
            'id' => 'woocommerce_pdf_catalog_meta_box',
            'title' => 'Ticket Meta Box',
            'pages' => array('product_cat', 'product_tag'),
            'context' => 'side',
            'fields' => array(),
            'local_images' => false,
            'use_with_theme' => false,
        );
        wp_enqueue_script( 'wp-color-picker' );
        $custom_taxonomy_meta_fields = new Tax_Meta_Class($custom_taxonomy_meta_config);

        $custom_taxonomy_meta_fields->addImage($prefix . 'cateory_image', array(
        	'name' => __('PDF Catalog – Category Image ', 'woocommerce-pdf-catalog'),
        	'width' => '200px',
        ));

        $custom_taxonomy_meta_fields->addWysiwyg($prefix . 'description', array(
        	'name' => __('PDF Catalog – Description ', 'woocommerce-pdf-catalog'),
        ));

        $custom_taxonomy_meta_fields->addImage($prefix . 'cover_image', array(
        	'name' => __('PDF Catalog – Cover Image ', 'woocommerce-pdf-catalog'),
        	'width' => '200px',
        ));

        $custom_taxonomy_meta_fields->addImage($prefix . 'backcover_image',array(
        	'name' => __('PDF Catalog – Backcover Image ', 'woocommerce-pdf-catalog'),
        	'width' => '200px',
        ));

        $productsLayouts = array(
            '' => 'Default', 
            '1' => '1 Layout', 
            '2' => '2 Layout', 
            '3' => '3 Layout', 
            '4' => '4 Layout', 
            '5' => '5 Layout',
            '6' => '6 Layout',
            '7' => '7 Layout', 
            '8' => '8 Layout', 
            '9' => '9 Layout', 
            // '10' => '10 Layout', 
            '11' => '11 Layout',
            '12' => '12 Layout',
            '13' => '13 Layout',
            '14' => '14 Layout',
        );		
        $custom_taxonomy_meta_fields->addSelect($prefix . 'products_layout', $productsLayouts, array('name'=> __('PDF Catalog – Products Layout ','woocommerce-pdf-catalog'), 'std'=> array('')));

        $categoryLayouts = array(
            '' => 'Default', 
            '1' => '1 Layout', 
            '2' => '2 Layout', 
            '3' => '3 Layout', 
            '4' => '4 Layout', 
            '5' => '5 Layout',
            '6' => '6 Layout'
        );		
        $custom_taxonomy_meta_fields->addSelect($prefix . 'category_layout', $categoryLayouts, array('name'=> __('PDF Catalog – Category Layout ','woocommerce-pdf-catalog'), 'std'=> array('')));

        $custom_taxonomy_meta_fields->Finish();
    }


	public function generate_cache($manual = false)
	{
        global $woocommerce_pdf_catalog_options, $wpdb;

        $woocommerce_pdf_catalog_options = get_option('woocommerce_pdf_catalog_options');

        if(!$manual && (!isset($woocommerce_pdf_catalog_options['enableCacheRegenerate']) || $woocommerce_pdf_catalog_options['enableCacheRegenerate'] != "1")) {
        	return false;
        }

        if(isset($_GET['pdf-catalog'])) {
        	return false;
        }

	    $msg = "<h2>PDF Catalog Regeneration Report</h2>";

        $msg .= "File Deletion:<br>";
        $msg .= "<ul>";


		$cacheFolder = $this->get_uploads_dir( 'woocommerce-pdf-catalogs' );
		if ( ! file_exists( $cacheFolder ) ) {
			mkdir( $cacheFolder, 0775, true );
		}

        // Delete old Cache
		foreach (new DirectoryIterator($cacheFolder) as $fileInfo) {
		    if(!$fileInfo->isDot()) {

		    	$file = $fileInfo->getPathname();
		        $delete = unlink($file);
		        if(!$delete) {
		        	$msg .= "<li>File " . basename($file) . ' could not be deleted</li>';
		        } else {
		        	$msg .= "<li>File " . basename($file) . ' deleted</li>';
		        }
		    }
		}
		$msg .= "</ul>";
		// Create new Cache
		$product_categories = $wpdb->get_results( "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat'" , ARRAY_A);
		if(empty($product_categories)) {
			return false;
		}

		$tmp = array();
		foreach ($product_categories as $term) {
			$tmp[] = $term['term_id'];
		}
		$product_categories = $tmp;

		$context = false;
		if(	isset($woocommerce_pdf_catalog_options['enableCacheRegenerateAuth']['username']) 
			&& !empty($woocommerce_pdf_catalog_options['enableCacheRegenerateAuth']['username'])
			&& isset($woocommerce_pdf_catalog_options['enableCacheRegenerateAuth']['password']) 
			&& !empty($woocommerce_pdf_catalog_options['enableCacheRegenerateAuth']['password'])
		){
			$context = stream_context_create(array (
			    'http' => array (
			        'header' => 
			        'Authorization: Basic ' . base64_encode($woocommerce_pdf_catalog_options['enableCacheRegenerateAuth']['username'] . ':' . $woocommerce_pdf_catalog_options['enableCacheRegenerateAuth']['password'])
			    )
			));
		}

		$msg .= "File Creation:<br>";
		$msg .= "<ul>";

		$product_categories[] = 'full';
		$site_url = get_site_url();
		foreach ($product_categories as $product_category) {

			$time_start = microtime(true);

			if(!$context) {
				file_get_contents($site_url . '?pdf-catalog=' . $product_category);
  			} else {
  				file_get_contents($site_url . '?pdf-catalog=' . $product_category, false, $context);
  			}
			$time_end = microtime(true);

			$execution_time = round( ($time_end - $time_start) / 60, 2);
			$msg .= $site_url . '?pdf-catalog=' . $product_category;
  			$msg .= '<li>' . sprintf('Create Category PDF %s in %s seconds', $product_category, $execution_time) . '</li>';
		}
		$msg .= "</ul>";

		$admin_email = get_option('admin_email');
	    $subject = utf8_decode("WooCommerce PDF Catalog - Regeneration");
	    $headers = array('Content-Type: text/html; charset=UTF-8');

        if(!$manual && isset($woocommerce_pdf_catalog_options['enableCacheRegenerateReport']) && $woocommerce_pdf_catalog_options['enableCacheRegenerateReport'] == "1") {
        	wp_mail($admin_email, $subject, utf8_decode($msg), $headers);
        }

        if($manual) {
        	add_action( 'admin_notices', array($this, 'notice' ));

        	$this->notice = $msg;
        }
	 
		return true;
	}

	protected function get_uploads_dir( $subdir = '' ) 
	{
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		if ( '' != $subdir ) {
			$upload_dir = $upload_dir . '/' . $subdir . '/';
		}
		return $upload_dir;
	}

	public function add_preview_frame()
	{
		$categories = get_terms(array(
			'taxonomy' => 'product_cat'
		));
		?>
		<div id="pdf-catalog-preview-frame-container" class="pdf-catalog-preview-frame-container">
			<div class="pdf-catalog-preview-frame-header">
				<label for="category_id"><?php _e('Select category ID', 'woocommerce-pdf-catalog') ?></label>
				<select name="category_id" id="pdf-catalog-preview-category-id">
					<?php foreach ($categories as $category) {
						echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
					} 
					echo '<option value="full">Full Catalog</option>';
					?>
				</select>
			</div>
			<div id="pdf-catalog-preview-spinner" class="pdf-catalog-preview-spinner">
				<i class="el el-refresh el-spin"></i>
			</div>
			<iframe id="pdf-catalog-preview-frame" src="" width="100%" height="100%" class="pdf-catalog-preview-frame">

			</iframe>
		</div>
		<div id="pdf-catalog-preview-frame-overlay" class="pdf-catalog-preview-frame-overlay"></div>
		<?php
	}
}