<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
 * @since      1.0.0
 *
 * @package    woocommerce_pdf_catalog
 * @subpackage woocommerce_pdf_catalog/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    woocommerce_pdf_catalog
 * @subpackage woocommerce_pdf_catalog/public
 * @author     Daniel Barenkamp <support@welaunch.io>
 */
class WooCommerce_PDF_Catalog_Public extends WooCommerce_PDF_Catalog {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * options of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $options
	 */
	protected $options;

	/**
	 * Product URL
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $url
	 */
	private $url;

	/**
	 * category
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $category
	 */
	private $category;

	/**
	 * category
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $category
	 */
	private $current_category_id;

	/**
	 * Product
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $product
	 */
	private $product;

	/**
	 * Post
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $post
	 */
	private $post;

	/**
	 * Data
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      mixed    $data
	 */
	private $data;

	/**
	 * mpdf
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      mixed    $mpdf
	 */
	protected $mpdf;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->data = new stdClass;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() 
	{
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-pdf-catalog-public.css', array(), $this->version, 'all' );

		if($this->get_option('loadFontAwesome')) {
			wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . 'css/fontawesome-free-5.15.3-web/css/all.css', array(), '5.15.3', 'all');
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() 
	{
		global $woocommerce_pdf_catalog_options;
		$this->options = $woocommerce_pdf_catalog_options;

		if($this->get_option('sendEMail')) {
			wp_enqueue_script( $this->plugin_name.'-public', plugin_dir_url( __FILE__ ) . 'js/woocommerce-pdf-catalog-public.js', array( 'jquery' ), $this->version, true );
		}

	    $forJS['ajax_url'] = admin_url('admin-ajax.php');
        $forJS['sendEMailSuccessText'] = $this->get_option('sendEMailSuccessText');
               
        wp_localize_script($this->plugin_name . '-public', 'woocommerce_pdf_catalog_options', $forJS);

		$customJS = $this->get_option('customJS');
		if(empty($customJS)) {
			return false;
		}

		file_put_contents( dirname(__FILE__)  . '/js/woocommerce-pdf-catalog-custom.js', $customJS);
		wp_enqueue_script( $this->plugin_name.'-custom', plugin_dir_url( __FILE__ ) . 'js/woocommerce-pdf-catalog-custom.js', array( 'jquery' ), $this->version, true );
	}
	
	/**
	 * Init PDF Catalog 
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function init()
    {
		global $woocommerce_pdf_catalog_options;
		$this->options = $woocommerce_pdf_catalog_options;
			
		if (!$this->get_option('enable')) {
			return false;
		}

		if($this->get_option('debugMode')) {
			$this->options['splitChunks'] = false;
			$this->options['enableCache'] = false;
		}

		// Enable User check
		if($this->get_option('enableLimitAccess')) {
			$roles = $this->get_option('role');
			if(empty($roles)) {
				$roles[] = 'administrator';
			}

			$currentUserRole = $this->get_user_role();

			if(!in_array($currentUserRole, $roles)) {
				return FALSE;
			}
		}

		$actual_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		if( strpos($actual_link, '?') === FALSE ){ 
			$this->url = $actual_link . '?'; 
		} else {
		 	$this->url = $actual_link . '&'; 
		}
		
		// Normal Buttons
		$linkPosition = $this->get_option('linkPosition');
		add_action( $linkPosition, array($this, 'show_category_export_buttons'), 90 );

		// Cart Button
		$cartLinkPosition = $this->get_option('cartLinkPosition');
		add_action( $cartLinkPosition, array($this, 'show_cart_export_buttons'), 90 );

		// Wishlist Button
		if($this->get_option('showWishlistCatalogLink')) {
			$wishlistLinkPosition = $this->get_option('wishlistLinkPosition');
			add_action( $wishlistLinkPosition, array($this, 'show_wishlist_export_buttons'), 90 );
		}

		if($this->get_option('showCustomMetaKeys')) {
			add_filter('woocommerce_pdf_catalog_before_product_information_end', array($this, 'show_custom_meta_keys'), 20, 2);
		}

		if(isset($_GET['pdf-catalog'])) {
			add_action("wp", array($this, 'watch'));
		}

		if($this->get_option('sendEMail')) { 
			add_action( 'wp_footer', array($this, 'send_cart_email_popup'), 20 );
		}

		$this->cacheFolder = $this->get_uploads_dir( 'woocommerce-pdf-catalogs' );
		if ( ! file_exists( $this->cacheFolder ) ) {
			mkdir( $this->cacheFolder, 0775, true );
		}
    }

    /**
     * Show Catalog Buttons
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @return  [type]                       [description]
     */
    public function show_category_export_buttons()
    {
		$current_category = get_queried_object();
		if(!isset($current_category->term_id)) {
			return false;
		}
		
		$current_category_id = $current_category->term_id;

		$showFullCatalogLinkButtontext = $this->get_option('showFullCatalogLinkButtontext');
		$showCategoryCatalogLinkButtontext = $this->get_option('showCategoryCatalogLinkButtontext');
		$showTagCatalogLinkButtontext = $this->get_option('showTagCatalogLinkButtontext');
		$showSaleCatalogLinkButtontext = $this->get_option('showSaleCatalogLinkButtontext');
		$showTaxonomyCatalogLinkButtontext = $this->get_option('showTaxonomyCatalogLinkButtontext');
		

    	echo '<div class="woocommerce-pdf-catalog link-wrapper">';

    	if($this->get_option('showFullCatalogLink')) {
			echo '<a href="' . $this->url . 'pdf-catalog=full" class="woocommerce_pdf_catalog_button woocommerce_pdf_catalog_button_full button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>' . $showFullCatalogLinkButtontext . '</a>';
		}

		if($this->get_option('sendEMail')) {
			$sendEMailButtonText = $this->get_option('sendEMailButtonText');
			echo '<a href="#" class="woocommerce-pdf-catalog-email-button button alt"><i class="fa fa-envelope fa-1x "></i>  ' . $sendEMailButtonText . '</a>';	
		}

		if(is_product_category()) {

			if($this->get_option('showCategoryCatalogLink') && !empty($current_category_id)) {
				echo '<a href="' . $this->url . 'pdf-catalog=' . $current_category_id . '" class="woocommerce_pdf_catalog_button woocommerce_pdf_catalog_button_category button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>' . $showCategoryCatalogLinkButtontext . '</a>';
			}
		} elseif(is_product_tag()) {

			if($this->get_option('showTagCatalogLink') && !empty($current_category_id)) {
				echo '<a href="' . $this->url . 'pdf-catalog=' . $current_category_id . '&taxonomy=product_tag" class="woocommerce_pdf_catalog_button woocommerce_pdf_catalog_button_category button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>' . $showTagCatalogLinkButtontext . '</a>';
			}
			
		} else {

			if($this->get_option('showTaxonomyCatalogLink') && !empty($current_category_id)) {
				echo '<a href="' . $this->url . 'pdf-catalog=' . $current_category_id . '&taxonomy=' . $current_category->taxonomy . '" class="woocommerce_pdf_catalog_button woocommerce_pdf_catalog_button_category button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>' . $showTaxonomyCatalogLinkButtontext . '</a>';
			}
		}

		if($this->get_option('showSaleCatalogLink')) {
			if(!empty($current_category_id)) {
				echo '<a href="' . $this->url . 'pdf-catalog=sale&sale-category=' . $current_category_id . '" class="woocommerce_pdf_catalog_button woocommerce_pdf_catalog_button_sale button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>' . $showSaleCatalogLinkButtontext . '</a>';
			} else {
				echo '<a href="' . $this->url . 'pdf-catalog=sale" class="woocommerce_pdf_catalog_button woocommerce_pdf_catalog_button_sale button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>' . $showSaleCatalogLinkButtontext . '</a>';
			}
		}

    	echo '</div>';
    }

    /**
     * Show Cart Export Button
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @return  [type]                       [description]
     */
    public function show_cart_export_buttons()
    {
		if(!is_cart()) {
			return false;
		}

		$showCartCatalogLinkButtontext = $this->get_option('showCartCatalogLinkButtontext');
    	echo '<div class="woocommerce-pdf-catalog link-wrapper">';

    		if($this->get_option('showCartCatalogLink')) {
				echo '<a href="' . $this->url . 'pdf-catalog=cart" class="woocommerce_pdf_catalog_button button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>  ' . $showCartCatalogLinkButtontext . '</a>';
			}

			if($this->get_option('sendEMail')) {
				$sendEMailButtonText = $this->get_option('sendEMailButtonText');
				echo '<a href="#" class="woocommerce-pdf-catalog-email-button button alt"><i class="fa fa-envelope fa-1x "></i>  ' . $sendEMailButtonText . '</a>';	
			}

    	echo '</div>';
    }

    /**
     * Show Wishlist Export Button
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @return  [type]                       [description]
     */
    public function show_wishlist_export_buttons()
    {
    	if( (!isset($_REQUEST['wishlist']) || empty($_REQUEST['wishlist'])) && (empty($_COOKIE['woocommerce_wishlist_products']))) {
    		return false;
    	}

    	$wishlist_id = 0;
    	if(isset($_REQUEST['wishlist'])) {
    		$wishlist_id = absint($_REQUEST['wishlist']);
    	}

    	$showWishlistCatalogLinkButtontext = $this->get_option('showWishlistCatalogLinkButtontext');
    	echo '<div class="woocommerce-pdf-catalog woocommerce-pdf-catalog-wishlist link-wrapper">';
			echo '<a href="' . get_site_url() . '/?pdf-catalog=wishlist&wishlist-id=' . $wishlist_id . '" class="woocommerce_pdf_catalog_button button alt" target="_blank"><i class="fa fa-file-pdf fa-1x "></i>  ' . $showWishlistCatalogLinkButtontext . '</a>';
    	echo '</div>';
    }

    /**
     * Watch PDF Catalog Generation to start?
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @return  [type]                       [description]
     */
    public function watch()
    {
    	// default Variables
		$this->data->blog_name = get_bloginfo('name');
		$this->data->blog_description  = get_bloginfo('description');

		if(!empty($_GET['pdf-catalog']) || $_GET['pdf-catalog'] == 'full')
		{
			$this->current_category_id = $_GET['pdf-catalog'];
			try {
				$this->build_pdf();
	    	} catch (Exception $e) {
	    		echo $e->getMessage();
	    	}
		}
	}

	/**
	 * Build the PDF
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function build_pdf($write = false)
    {
    	$this->mpdf = $this->get_mpdf();

		$html = "";

		if($this->current_category_id == "full") {
			$filename = 'complete';
		} elseif($this->current_category_id == "cart") {
			$filename = 'cart';
		} elseif($this->current_category_id == "wishlist") {
			$filename = 'wishlist';
		} elseif($this->current_category_id == "sale") {
			$filename = 'sale';
		} elseif($this->current_category_id == "attr") {
			$filename = 'attr';
			$this->term_metas = get_option('woocommerce_pdf_catalog_term_data');
		} else {
			$term = get_term($this->current_category_id);
			$filename = $this->escape_filename($term->slug);	
		}

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
	  		$filename = $filename . '-' . ICL_LANGUAGE_CODE;
		}

		$this->mpdf->SetTitle( htmlspecialchars( ucfirst($filename) . ' ' . __('Catalog PDF', 'woocommerce-pdf-catalog'), ENT_QUOTES));

		// Check Caching
		$checkFileExists = true;
		$enableCache = $this->get_option('enableCache');
		if($enableCache === "1" && !in_array($filename, array( "cart", "wishlist", "sale", "attr"))) {

			$cachedFile = $this->cacheFolder . $filename . '.pdf';

			$checkFileExists = file_exists( $cachedFile );
			if($checkFileExists) {	

				// return file path only
				if($write) {
					return $cachedFile;
				} else {
					header("Content-Disposition:inline;filename=" . $filename . ".pdf");
					header("Content-type:application/pdf");
					readfile($cachedFile); 	
					exit();
				}
			}
		}

		if(in_array($this->current_category_id, array( "cart", "wishlist", "sale", "attr",  "full") ) ){
			$html .= '<div class="woocommerce-pdf-catalog-type-' . $this->current_category_id . '">';
		} else {
			$html .= '<div class="woocommerce-pdf-catalog-type-category woocommerce-pdf-catalog-type-category-id-' . $this->current_category_id . '">';
		}

		if($this->get_option('watermarkEnable')) {

			$watermarkType = $this->get_option('watermarkType');
			$watermarkTransparency = $this->get_option('watermarkTransparency');
			
			if($watermarkType == "text") {
				$watermarkText = $this->get_option('watermarkText');
				if(!empty($watermarkText)) {
					$this->mpdf->SetWatermarkText($watermarkText, $watermarkTransparency);
					$this->mpdf->showWatermarkText = true;
				}
			} elseif($watermarkType == "image") {
				$watermarkImage = $this->get_option('watermarkImage');
				if(!empty($watermarkImage['url'])) {
					$this->mpdf->SetWatermarkImage($watermarkImage['url'], $watermarkTransparency);
					$this->mpdf->showWatermarkImage = true;
				}
			}
		}

		// Check Cover Page
		$this->showCoverPage = false;
		if($this->get_option('enableCover')) {
			if($this->get_option('coverOnlyFull') && !($this->current_category_id == 'full') ) {
				$this->showCoverPage = false;
			} else {
				$this->showCoverPage = true;
				$html .= $this->get_cover();
			}
		}	

		// Get Custom CSS
		$css = $this->build_CSS();

		// Get Table of Contents
		if($this->get_option('enableToC') && $this->get_option('ToCPosition') == "first" && !in_array($this->current_category_id, array( "cart", "wishlist", "sale", "attr")) ) {

			if($this->get_option('ToCOnlyFull') && !($this->current_category_id == 'full') ) {
				
			} else {
				$html .= $this->table_of_contents();
			}
		}

		// Set Header
		if($this->get_option('enableHeader'))
		{
			$header = $this->get_header();
			$this->mpdf->DefHTMLHeaderByName('defaultHeader', $header);

			if($this->showCoverPage) {
				// $this->mpdf->SetHTMLHeaderByName('defaultHeader', 0, false);
			} else {
				$this->mpdf->SetHTMLHeaderByName('defaultHeader');
			}
		}

		// Set Footer
		if($this->get_option('enableFooter'))
		{
			$footer = $this->get_footer();
			$this->mpdf->DefHTMLFooterByName('defaultFooter', $footer);

			if($this->showCoverPage) {
				$this->mpdf->SetHTMLFooterByName('defaultFooter', 0, false);
			} else {
				$this->mpdf->SetHTMLFooterByName('defaultFooter');
			}
		}

		// Get Categories if not Cart
		if(!in_array($this->current_category_id, array( "cart", "wishlist", "sale", "attr") ) ){
			$categories = $this->get_categories();
		} 

		$this->set_exlusions();
		$this->set_ordering();

		// Split Chunks
		if($this->get_option('splitChunks')) {
			$this->mpdf->WriteHTML($css . $html, 0, true, false);
			$html = "";
		}

		$this->firstPage = true;
		if(!in_array($this->current_category_id, array( "cart", "wishlist", "sale", "attr")) ) {

			$categories_count = count($categories);
			$loop = 1;
			$parentCategoriesChecked = array();

			foreach ($categories as $category_key => $category_id) {

				if($this->get_option('splitChunks')) {
					$html = "";
				}

				$html .= '<div class="woocommerce-pdf-catalog-category-' . $category_id . '">';

					if(!empty($this->exclude_product_categories)){
						if($this->exclude_product_categories_revert) {
							if(!in_array($category_id, $this->exclude_product_categories)){
								$loop++;
								unset($categories[$category_key]);
								continue;
							}
						} else {
							if(in_array($category_id, $this->exclude_product_categories)){
								$loop++;
								unset($categories[$category_key]);
								continue;
							}
						}
					}

					if($this->get_option('flattenProducts')) {
						$products = array();
						foreach ($categories as $category_id) {
							$products = array_merge($products, $this->get_products($category_id));
						}

						// Remove duplicate products
						if($this->get_option('flattenProductsRemoveDuplicates')) {
							$temp = array();
							foreach($products as $prod) {
							    $temp[$prod->ID] = $prod;
							}
							$products = $temp;
						}
					} else {

						$products = $this->get_products($category_id);
						if(empty($products)) {
							$loop++;
							unset($categories[$category_key]);
							continue;
						}
					}

					if($this->get_option('yoastPrimaryOnlyInFull')) {
						foreach ($products as $productKey => $product) {

							if(class_exists('RankMath')) {
								$yoastPrimaryCategoryID = get_post_meta($product->ID, 'rank_math_primary_product_cat', true);
							} else {
								$yoastPrimaryCategoryID = get_post_meta($product->ID, '_yoast_wpseo_primary_product_cat', true);	
							}
							
							if($yoastPrimaryCategoryID && $yoastPrimaryCategoryID != $category_id) {
								unset($products[$productKey]);
							}
						}
					}
					
					// Parent Category 
					if($this->get_option('categoryShowParentCategory')) {

						$category = get_term($category_id, 'product_cat');
						$parentCategoryId = $category->parent;

						if($parentCategoryId > 0 && !in_array($parentCategoryId, $categories) && !in_array($parentCategoryId, $parentCategoriesChecked)) {

							// Category Cover
							if( $this->get_option('coverShowForCategories')) {
								if($this->current_category_id == 'full') {
									$html .= $this->get_category_cover($parentCategoryId);
								} elseif($loop !== 1) {
									$html .= $this->get_category_cover($parentCategoryId);
								}
							}
						  	
						  	// Category
						  	$categoryLayout = $this->get_option('categoryLayout');
							$categoryLayout = apply_filters('woocommerce_pdf_catalog_category_layout', $categoryLayout, $parentCategoryId);
							
							$categoryTemplate = new WooCommerce_PDF_Catalog_Category_Templates($this->plugin_name, $this->version, $this->options);
							$categoryTemplate->set_category($parentCategoryId);
							$html .= apply_filters( 'woocommerce_pdf_catalog_category_html', $categoryTemplate->get_template($categoryLayout), $parentCategoryId);

							$parentCategoriesChecked[] = $parentCategoryId;
						}
					}

					// Category Cover
					if( $this->get_option('coverShowForCategories')) {
						if($this->current_category_id == 'full') {
							$html .= $this->get_category_cover($category_id);
						} elseif($loop !== 1) {
							$html .= $this->get_category_cover($category_id);
						}
					}

					// Category Itself
				  	
				  	// Category
				  	$categoryLayout = $this->get_option('categoryLayout');
					$categoryLayout = apply_filters('woocommerce_pdf_catalog_category_layout', $categoryLayout, $category_id);
					
					$categoryTemplate = new WooCommerce_PDF_Catalog_Category_Templates($this->plugin_name, $this->version, $this->options);
					$categoryTemplate->set_category($category_id);
					$html .= apply_filters( 'woocommerce_pdf_catalog_category_html', $categoryTemplate->get_template($categoryLayout), $category_id);

					$exclude_parent_category_products = array();
					if($this->get_option('excludeParentCategoryProducts')) {
						if($this->get_not_in($category_id)) {
							
							if($this->get_option('splitChunks')) {
								$this->mpdf->WriteHTML($html, 0, false, false);
								$html = "";
							}

							$loop++;
							continue;
						};
					}

					if(!empty($this->exclude_product_categories_products)){
						if(in_array($category_id, $this->exclude_product_categories_products)) {
							$this->mpdf->WriteHTML($html, 0, false, false);
							$loop++;
							continue;
						}
					}

					// Products
					if($this->get_option('enableTextBeforeProducts')) {
						$html .= $this->get_text_before_products();
					}

					if($this->get_option('splitChunks')) {
						$this->mpdf->WriteHTML($html, 0, false, false);
						$html = "";
					}

					$productsLayout = $this->get_option('productsLayout');
					$productsLayout = apply_filters('woocommerce_pdf_catalog_products_layout', $productsLayout, $category_id);

					$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options, $filename);
					$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates, $this->mpdf, $categoryTemplate->get_level(), $categoryTemplate->get_category_id() );
					$productsTemplate->set_products($products);

					if($this->get_option('splitChunks')) {
						$html .= $productsTemplate->get_template($productsLayout, true);
					} else {
						$html .= $productsTemplate->get_template($productsLayout, false);
					}

					if($this->get_option('enableTextAfterProducts')) {
						$html .= $this->get_text_after_products();
					}

					if($loop != $categories_count) {
						if($this->get_option('enableCategory') && $this->get_option('categoryPagebreak')) {
							$html .= $this->get_pagebreak();
						}
					}

				$html .= '</div>';

				if($this->get_option('splitChunks')) {
					$this->mpdf->WriteHTML($html, 0, false, false);
					$html = "";
				}

				if($this->get_option('flattenProducts')) {
					break;
				}

				$loop++;
			}

		// Attribute PDF Catalog
		} elseif($this->current_category_id === "attr") {

			if(!isset($_GET['taxonomy']) || empty($_GET['taxonomy']) || !isset($_GET['term-id']) || empty($_GET['term-id'])) {
				wp_die('Missing Tax or Term Data');
			}

			$termId = $_GET['term-id'];
			$attribute_products = $this->get_products('', $termId);
			if(empty($attribute_products)) {
				wp_die('No Products found');
			}

			if($this->get_option('enableTextBeforeProducts'))
			{
				$html .= $this->get_text_before_products();
			}

			$productsLayout = $this->get_option('productsLayout');
			$productsLayout = apply_filters('woocommerce_pdf_catalog_attribute_products_layout', $productsLayout);

			$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options, $filename);
			$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates, $this->mpdf);
			$productsTemplate->set_products($attribute_products);
			$html .= $productsTemplate->get_template($productsLayout);

			if($this->get_option('enableTextAfterProducts')) {
				$html .= $this->get_text_after_products();
			}

		// Generate Wishlist PDF Catalog
		} elseif($this->current_category_id === "wishlist") {
			
			$wishlist_id = $_GET['wishlist-id'];
			$product_ids = get_post_meta($wishlist_id, 'products', true);

			if(empty($product_ids)) {
				$product_ids = json_decode(str_replace("\\", '', $_COOKIE['woocommerce_wishlist_products']), true);
				if(!empty($product_ids)) {
					$tmp = array();
					foreach ($product_ids as $tmp_single) {
						$tmp[] = array(
							'product_id' => $tmp_single
						);
					}
					$product_ids = $tmp;
				}
			}
			
			if(empty($product_ids)) {
				wp_die('Empty Wishlist');
			}

			if($this->get_option('showWishlistCatalogCategories')) {

				$wishlistCategoriesMapped = array();
				foreach($product_ids as $product_id) {

					$product = get_post($product_id['product_id']);
					if(!$product) {
						continue;
					}

					if(class_exists('RankMath')) {
						$yoastPrimaryCategoryID = get_post_meta($product->ID, 'rank_math_primary_product_cat', true);
					} else {
						$yoastPrimaryCategoryID = get_post_meta($product->ID, '_yoast_wpseo_primary_product_cat', true);	
					}

					if($yoastPrimaryCategoryID) {
						$wishlistMappedCategory = $yoastPrimaryCategoryID;
					} else {
						$productCategories = get_the_terms($product->ID, 'product_cat');
						if(!empty($productCategories)) {
							foreach ($productCategories as $productCategory) {
								$wishlistMappedCategory = $productCategory->term_id;
								break;
							}
						}
					}

					if(!isset($wishlistCategoriesMapped[$wishlistMappedCategory])) {
						$wishlistCategoriesMapped[$wishlistMappedCategory] = array();
					} 

					$wishlistCategoriesMapped[$wishlistMappedCategory][] = $product;
				}

				$categoryLayout = $this->get_option('categoryLayout');
				$productsLayout = $this->get_option('productsLayout');
				$productsLayout = apply_filters('woocommerce_pdf_catalog_wishlist_products_layout', $productsLayout);

				foreach($wishlistCategoriesMapped as $wishlistCategoriesMappedKey => $wishlistCategoriesMappedProducts) {
				  	
					$categoryLayout = apply_filters('woocommerce_pdf_catalog_category_layout', $categoryLayout, $wishlistCategoriesMappedKey);
					
					$categoryTemplate = new WooCommerce_PDF_Catalog_Category_Templates($this->plugin_name, $this->version, $this->options);
					$categoryTemplate->set_category($wishlistCategoriesMappedKey);
					$html .= apply_filters( 'woocommerce_pdf_catalog_category_html', $categoryTemplate->get_template($categoryLayout), $category_id);

					if(empty($wishlistCategoriesMappedProducts)) {
						continue;
					}

					if($this->get_option('enableTextBeforeProducts')) {
						$html .= $this->get_text_before_products();
					}

					$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options, $filename);
					$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates, $this->mpdf);
					$productsTemplate->set_products($wishlistCategoriesMappedProducts);
					$html .= $productsTemplate->get_template($productsLayout);

					if($this->get_option('enableTextAfterProducts')) {
						$html .= $this->get_text_after_products();
					}				
				}

			} else {

				$products = array();
				foreach ($product_ids as $product_id) {
					$product = get_post($product_id['product_id']);
					if($product) {
						$products[] = $product;
					}
				}

				if(empty($products)) {
					wp_die('Empty Wishlist');
				}

				if($this->get_option('enableTextBeforeProducts')) {
					$html .= $this->get_text_before_products();
				}

				$productsLayout = $this->get_option('productsLayout');
				$productsLayout = apply_filters('woocommerce_pdf_catalog_wishlist_products_layout', $productsLayout);

				$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options, $filename);
				$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates, $this->mpdf);
				$productsTemplate->set_products($products);
				$html .= $productsTemplate->get_template($productsLayout);

				if($this->get_option('enableTextAfterProducts')) {
					$html .= $this->get_text_after_products();
				}
			}

		// Generate Sale PDF Catalog
		} elseif($this->current_category_id === "sale") {
			
			// $product_ids = wc_get_product_ids_on_sale();
			$args = array(
			    'post_type'      => array('product', 'product_variation'),
			    'posts_per_page' => -1,
			    'meta_query'     => array(
			        'relation' => 'OR',
			        array( // Simple products type
			            'key'           => '_sale_price',
			            'value'         => 0,
			            'compare'       => '>',
			            'type'          => 'numeric'
			        ),
			        array( // Variable products type
			            'key'           => '_min_variation_sale_price',
			            'value'         => 0,
			            'compare'       => '>',
			            'type'          => 'numeric'
			        )
			    )
			);

			$sale_query = new WP_Query( $args );

			if(!isset($sale_query->posts) || empty($sale_query->posts)) {
				wp_die('No Products on sale.');
			}

			$product_ids = wp_list_pluck( $sale_query->posts, 'ID' );

			$sale_category = isset($_GET['sale-category']) && !empty($_GET['sale-category']) ? (int) $_GET['sale-category'] : '';

			if(empty($product_ids)) {
				wp_die('No Products on sale.');
			}

			if(!empty($sale_category)) {

				$sale_products = $this->get_products($sale_category);

				if(empty($sale_products)) {
					wp_die('No Produts on sale.');
				}

				$tmp = array();
				foreach ($sale_products as $sale_product) {
					if(in_array($sale_product->ID, $product_ids)) {
						$tmp[] = $sale_product->ID;
					}
				}

				$product_ids = $tmp;
			}

			if(empty($product_ids)) {
				wp_die('Empty Sale Products');
			}

			$products = array();
			foreach ($product_ids as $product_id) {

				if($this->get_option('showSaleCatalogOnlyInStock')) {
					$saleProduct = wc_get_product($product_id);
					if(!$saleProduct->is_in_stock()) {
						continue;
					}

					if(!$saleProduct->is_on_sale()) {
						continue;
					}
				}

				$products[] = get_post($product_id);
			}
			if(empty($products)) {
				wp_die('Empty Sale Products');
			}
			
			if($this->get_option('enableTextBeforeProducts'))
			{
				$html .= $this->get_text_before_products();
			}

			$productsLayout = $this->get_option('productsLayout');
			$productsLayout = apply_filters('woocommerce_pdf_catalog_sale_products_layout', $productsLayout);

			$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options, $filename);
			$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates, $this->mpdf);
			$productsTemplate->set_products($products);
			$html .= $productsTemplate->get_template($productsLayout);

			if($this->get_option('enableTextAfterProducts')) {
				$html .= $this->get_text_after_products();
			}

		// Cart PDF Catalog
		} else {
			$cart = WC()->cart->get_cart();
			
			if(empty($cart)) {
				wp_die('Empty Cart');
			}

			$products = array();
			foreach ($cart as $cart_item) {	
				if(isset($cart_item['variation_id']) && !empty($cart_item['variation_id'])) {
					$products[] = get_post($cart_item['variation_id']);
				} else {
					$products[] = get_post($cart_item['product_id']);
				}
			}

			if($this->get_option('enableTextBeforeProducts'))
			{
				$html .= $this->get_text_before_products();
			}

			$productsLayout = $this->get_option('productsLayout');
			$productsLayout = apply_filters('woocommerce_pdf_catalog_cart_products_layout', $productsLayout);

			$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options, $filename);
			$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates, $this->mpdf);
			$productsTemplate->set_products($products);
			$html .= $productsTemplate->get_template($productsLayout);

			if($this->get_option('enableTextAfterProducts')) {
				$html .= $this->get_text_after_products();
			}
		}

		if($this->get_option('enableToC') && $this->get_option('ToCPosition') == "last") {

			if($this->get_option('ToCOnlyFull') && !($this->current_category_id == 'full') ) {
				
			} else {
				$html .= $this->table_of_contents();
			}
		} 

		if($this->get_option('enableIndex')) {

			if($this->get_option('indexOnlyFull') && !($this->current_category_id == 'full') ) {
				
			} else {
				$html .= $this->get_pagebreak();
				$html .= $this->index();
			}
		}

		if(in_array($this->current_category_id, array( "cart", "wishlist", "sale", "attr") ) ){
			$html .= '</div>';
		}
        

		$debugMode = $this->get_option('debugMode');
		if($debugMode === "1") {
			die($css.$html);
		}


		// Performance 
		if($this->get_option('performanceDisableSubstitutions')) {
			$this->mpdf->useSubstitutions = false;
		}

		if($this->get_option('performanceUseSimpleTables')) {
			$this->mpdf->simpleTables = true;
		}

		if($this->get_option('performanceUsePackTableData')) {
			$this->mpdf->packTableData = true;
		}

		$this->mpdf->useAdobeCJK = true;
		$this->mpdf->autoScriptToLang = true;
		$this->mpdf->autoLangToFont = true;
		$this->mpdf->shrink_tables_to_fit = 1;

		if($this->get_option('splitChunks')) {
			if(!empty($html)) {
				$this->mpdf->WriteHTML($html, 0, false, true);
			}
			$html = "";
		} else {
			$this->mpdf->WriteHTML($css.$html);
		}

		if($this->get_option('enableBackcover')) {
			$this->mpdf->WriteHTML( $this->get_backcover() );
		}   

		if( ($enableCache === "1") && !$checkFileExists) {
			$this->mpdf->Output($cachedFile, 'F');
			chmod($cachedFile, 0775);

			if($write) {
				return $cachedFile;
			} else {
				header("Content-Disposition:inline;filename=" . $filename . ".pdf");
				header("Content-type:application/pdf");
				readfile($cachedFile);
			}

		} else {

			if($write) {
				$folder = $this->get_uploads_dir( 'woocommerce-pdf-catalog' );
				if ( ! file_exists( $folder ) ) {
					mkdir( $folder, 0775, true );
				}

				$filePath = $folder . $filename.'.pdf';
				$this->mpdf->Output($filePath, 'F');	
				return $filePath;
			} else {
				$this->mpdf->Output($filename.'.pdf', 'I');	
			}
		}
		exit;
    }

    protected function get_mpdf()
    {
    	if(!class_exists('\Mpdf\Mpdf')) return FALSE;

    	require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'fonts/customFonts.php');

    	$headerTopMargin = $this->get_option('headerTopMargin');
    	$footerTopMargin = $this->get_option('footerTopMargin');

    	$format = $this->get_option('format') ? $this->get_option('format') : 'A4' ;
    	$orientation = $this->get_option('orientation') ? $this->get_option('orientation') : 'P';

    	$fontFamily = $this->get_option('fontFamily') ? $this->get_option('fontFamily') : 'dejavusans';

		$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];

		$mpdfConfig = array(
			'mode' => 'utf-8', 
			'format' => $format,    // format - A4, for example, default ''
			'default_font_size' => 0,     // font size - default 0
			'default_font' => $fontFamily,    // default font family
			'margin_left' => 0,    	// 15 margin_left
			'margin_right' => 0,    	// 15 margin right
			'margin_top' => $headerTopMargin,     // 16 margin top
			'margin_bottom' => $footerTopMargin,    	// margin bottom
			'margin_header' => 0,     // 9 margin header
			'margin_footer' => 0,     // 9 margin footer
			'orientation' => $orientation,  	// L - landscape, P - portrait
			'tempDir' => plugin_dir_path( dirname( __FILE__ ) ) . 'cache/',
			'fontDir' => array(
				plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/mpdf/mpdf/ttfonts/',
				plugin_dir_path( dirname( __FILE__ ) ) . 'fonts/',
			),
		    'fontdata' => array_merge($fontData, $customFonts),

		    'curlFollowLocation' => $this->get_option('curlFollowLocation'),
		    'curlAllowUnsafeSslRequests' => $this->get_option('curlAllowUnsafeSslRequests'),



			// curlFollowLocation
			// curlAllowUnsafeSslRequests
			// curlCaCertificate
			// curlProxy
		);

		$mpdfConfig = apply_filters('woocommerce_pdf_catalog_mpdf_config', $mpdfConfig);

		$mpdf = new \Mpdf\Mpdf($mpdfConfig);	

		if($this->get_option('debugMPDF')) {
			$mpdf->debug = true;
			$mpdf->debugfonts = true;
			$mpdf->showImageErrors = true;
		}

		return $mpdf;
    } 

    public function build_CSS()
    {
		$css = "";

    	// Font
    	$fontFamily = $this->get_option('fontFamily') ? $this->get_option('fontFamily') : 'dejavusans';

    	$fontSize = $this->get_option('fontSize') ? $this->get_option('fontSize') : '11';
    	$fontSize = intval($fontSize);

    	$fontLineHeight =  $this->get_option('fontLineHeight') ? $this->get_option('fontLineHeight') : $fontSize + 6; 
    	$fontLineHeight = intval($fontLineHeight);

    	$attributeImageWidth = $this->get_option('attributeImageWidth') + 5;

		$css = '
		<head>
			<style media="all">';

		if($this->showCoverPage) {
			$css .= '@page {
					header: defaultHeader;
					footer: defaultFooter;	
				}
				@page :first {
					header: none;
					footer: none;	
				}';
		}

		$css .= 
		'
        @page noheader {
            header: none;
            footer: none;   
            margin-top: 0;
            margin-bottom: 0;
        }
        div.noheader {
            page-break-before: right;
            page: noheader;
        }
        body, table { 
			font-family: ' . $fontFamily . ', sans-serif; 
			font-size: ' . $fontSize . 'pt; 
			line-height: ' . $fontLineHeight . 'pt; 
			text-align: left; 
			margin: 0;
			padding: 0;
			width: 100%;
		} 
		.mpdf_toc_level_3 {
		    margin-left: 6em;
		    text-indent: -2em;
		}
		.mpdf_toc_level_4 {
		    margin-left: 8em;
		    text-indent: -2em;
		}
		p { 
			margin-bottom: 10px; 
		}
		.attribute-value p {
			margin: 0;
		}
		span.mpdf_toc_t_level_1, span.mpdf_toc_t_level_2 {
		    font-weight: normal !important;
		    font-style: normal;
		}
		.two-cols, .three-cols, .four-cols {
			width: 100% !important;
		}
		.two-cols .col {
			width: 49.5%;
			float: left;
		}
		.three-cols .col {
			width: 33%;
			float: left;
		}
		.four-cols .col {
			width: 24%;
			float: left;
		}

		.woocommerce-attribute-image img {
			margin-right: 5px;
		}

		.category-header-logo {
			padding-bottom: 30px;
			width: 200px;
		}

		.category-layout-6 .category-information {
			padding-top: 60px;
		}

		.category-header-container  {
			padding-top: 30px;
			width: 250px;
			text-align: center;
			margin: 0 auto;
			border-bottom: 1px solid #eaeaea;
		}

		.five-cols .col {
			width: 19%;
			float: left;
		}
		.product-data-container {
			margin-top: 55px;
			padding-left: 30px;
		}
		.attributes { width: 100%; font-size: 9pt; line-height: 10pt; margin-bottom: 5px; }
		.attributes th { width:33%; text-align:left; padding-top:2px; padding-bottom: 2px;}
		.attributes td { width:66%; text-align:left; }
		.meta { font-size: 10pt; }
		.title { width: 100%; }
		.title td { padding-bottom: 10px; padding-top: 40px; }
		.clear { float: none; clear: both;}
		.fl { float: left; }';
		

    	// Header
    	if($this->get_option('enableHeader')) {
	    	$headerFontSize = $this->get_option('headerFontSize');
	    	$headerLineHeight = $this->get_option('headerLineHeight');
	    	$headerPadding = $this->get_option('headerPadding');
	    	$headerBackgroundColor = $this->get_option('headerBackgroundColor');
	    	$headerTextColor = $this->get_option('headerTextColor');
			$this->get_option('headerHeight') ? $headerHeight = $this->get_option('headerHeight') : $headerHeight = 'auto';

	    	$css .= 
	    	'.header { 
				padding-top: ' . $headerPadding['padding-top'] . '; 
				padding-right: ' . $headerPadding['padding-right'] . '; 
				padding-bottom: ' . $headerPadding['padding-bottom'] . '; 
				padding-left: ' . $headerPadding['padding-left'] . '; 
				background-color: ' . $headerBackgroundColor . '; 
				color: ' . $headerTextColor . '; 
				height: ' . $headerHeight . 'px; 
				font-size: ' . $headerFontSize . 'pt;
				line-height: ' . $headerLineHeight . 'pt;
			}
			.header a {
				color: ' . $headerTextColor . '; 
				text-decoration: none;
			}
			.header p {
				margin-top: 0;
				margin-bottom: 0;
			}';
		}

		// Footer
		if($this->get_option('enableFooter')) {
			$footerFontSize = $this->get_option('footerFontSize');
			$footerLineHeight = $this->get_option('footerLineHeight');
	    	$footerPadding = $this->get_option('footerPadding');
	    	$footerBackgroundColor = $this->get_option('footerBackgroundColor');
	    	$footerTextColor = $this->get_option('footerTextColor');
	    	$footerHeight = $this->get_option('footerHeight');

	    	$css .= 
	    	'.footer { 
				padding-top: ' . $footerPadding['padding-top'] . '; 
				padding-right: ' . $footerPadding['padding-right'] . '; 
				padding-bottom: ' . $footerPadding['padding-bottom'] . '; 
				padding-left: ' . $footerPadding['padding-left'] . '; 
				background-color: ' . $footerBackgroundColor . '; 
				color: ' . $footerTextColor . '; 
				font-size: ' . $footerFontSize . 'pt;
				line-height: ' . $footerLineHeight . 'pt;
				
			}
			.footer p {
				margin-top: 0;
				margin-bottom: 0;
			}';
		}

    	// ToC
    	if($this->get_option('enableToC')) {
	    	$ToCPadding = $this->get_option('ToCPadding');
	    	$ToCFontFamily = $this->get_option('ToCFontFamily') ? $this->get_option('ToCFontFamily') : 'dejavusans';
	    	$ToCFontSize = $this->get_option('ToCFontSize') ? $this->get_option('ToCFontSize') : '13pt';
	    	$ToCLineHeight = $this->get_option('ToCLineHeight') ? $this->get_option('ToCLineHeight') : '16pt';

			$css .= 
			'.toc_before { 
				padding-top: ' . $ToCPadding['padding-top'] . '; 
				padding-right: ' . $ToCPadding['padding-right'] . '; 
				padding-bottom: ' . $ToCPadding['padding-bottom'] . '; 
				padding-left: ' . $ToCPadding['padding-left'] . '; 
				font-family: ' . $ToCFontFamily . ', sans-serif;
				font-size: ' . $ToCFontSize . 'pt;
				line-height: ' . $ToCLineHeight . 'pt;
			}
			.toc_after { 
				padding-top: ' . $ToCPadding['padding-top'] . '; 
				padding-right: ' . $ToCPadding['padding-right'] . '; 
				padding-bottom: 0; 
				padding-left: ' . $ToCPadding['padding-left'] . '; 
				font-family: ' . $ToCFontFamily . ', sans-serif;
				font-size: ' . $ToCFontSize . 'pt;
				line-height: ' . $ToCLineHeight . 'pt;
			}
			.mpdf_toc {
				padding-right: ' . $ToCPadding['padding-right'] . '; 
				padding-left: ' . $ToCPadding['padding-left'] . '; 
				font-family: ' . $ToCFontFamily . ', sans-serif;
				font-size: ' . $ToCFontSize . 'pt;
				line-height: ' . $ToCLineHeight . 'pt;
			}
			.mpdf_toc_a  {
				font-family: ' . $ToCFontFamily . ', sans-serif;
				font-size: ' . $ToCFontSize . 'pt;
				line-height: ' . $ToCLineHeight . 'pt;
			}
			.toc_before h1, .toc_before h2, .toc_before h3 {
				font-family: ' . $ToCFontFamily . ', sans-serif;
				line-height: ' . $ToCLineHeight . 'pt;
			}';
		}

    	// Index
    	if($this->get_option('enableIndex')) {
			$indexPadding = $this->get_option('indexPadding');
			$indexFontFamily = $this->get_option('indexFontFamily');
			$indexFontSize = $this->get_option('indexFontSize');
			$indexLineHeight = $this->get_option('indexLineHeight');

			$css .= '
			.index, .index_before, .index_after { 
				padding-top: ' . $indexPadding['padding-top'] . '; 
				padding-right: ' . $indexPadding['padding-right'] . '; 
				padding-bottom: ' . $indexPadding['padding-bottom'] . '; 
				padding-left: ' . $indexPadding['padding-left'] . '; 
				font-family: ' . $indexFontFamily . ', sans-serif;
				font-size: ' . $indexFontSize . 'pt;
				line-height: ' . $indexLineHeight . 'pt;
			}
			.index h1, .index_before h1, .index_after h1,
			.index h2, .index_before h2, .index_after h3,
			.index h3, .index_before h2, .index_after h3,
			div.mpdf_index_letter, div.mpdf_index_entry, a.mpdf_index_link {
				font-family: ' . $indexFontFamily . ', sans-serif;
			}				
			div.mpdf_index_main { 
				padding-right: ' . $indexPadding['padding-right'] . '; 
				padding-left: ' . $indexPadding['padding-left'] . '; 
				font-family: ' . $indexFontFamily . ', sans-serif;
				font-size: ' . $indexFontSize . 'pt;
				line-height: ' . $indexLineHeight . 'pt;
			}
			div.mpdf_index_letter {
				font-size: ' . ( $indexFontSize + 4 )  . 'pt;
			}';
		}

		// Category
		if($this->get_option('enableCategory')) {
			$categoryHeadingFontFamily = $this->get_option('categoryHeadingFontFamily') ? $this->get_option('categoryHeadingFontFamily') : 'dejavusans';
			$categoryHeadingFontSize = $this->get_option('categoryHeadingFontSize');
			$categoryTextFontSize = $this->get_option('categoryTextFontSize');

			$categoryTextColor = $this->get_option('categoryTextColor');
			$categoryTextAlign = $this->get_option('categoryTextAlign');
			$categoryTextVAlign = $this->get_option('categoryTextVAlign');
			$categoryLineHeight = $this->get_option('categoryLineHeight');

	    	$categoryPadding = $this->get_option('categoryPadding');
	    	$categoryPadding = $categoryPadding['padding-top'] . ' ' . $categoryPadding['padding-right'] . ' ' . $categoryPadding['padding-bottom'] . ' ' . $categoryPadding['padding-right'];

			$categoryInformationPadding = $this->get_option('categoryInformationPadding');
	    	$categoryInformationPadding = $categoryInformationPadding['padding-top'] . ' ' . $categoryInformationPadding['padding-right'] . ' ' . $categoryInformationPadding['padding-bottom'] . ' ' . $categoryInformationPadding['padding-right'];

			$css .= '
			.category-container {
				text-align: ' . $categoryTextAlign . ';
				line-height: ' . $categoryLineHeight . 'px;
				padding: ' . $categoryPadding . ';
				color: ' . $categoryTextColor . ';
			}
			.category-title {
				font-family: ' . $categoryHeadingFontFamily . ', sans-serif;
				font-size: ' . $categoryHeadingFontSize . 'px;
				
			}
			.category-description {
				font-size: ' . $categoryTextFontSize . 'px;
			}
			.category-information {
				padding: ' . $categoryInformationPadding .  ';
			}
			.category-layoutne .category-information-container,
			.category-layoutne .category-image,
			.category-layout-two .category-information-container,
			.category-layout-two .category-image
			{
				float: left;
				width: 50%;
			}
			.category-layoutne .category-information-container-no-image,
			.category-layout-two .category-information-container-no-image {
				width: 100%;
			}';
		}

		// Text Before#
		if($this->get_option('enableTextBeforeProducts'))
		{

			$productContainerPadding = $this->get_option('productContainerPadding');
	    	$productsContainerPadding = '10px ' . $productContainerPadding['padding-right'] . ' 0px '  . $productContainerPadding['padding-right'];

	    	$textBeforeProductsFontSize = $this->get_option('textBeforeProductsFontSize');
	    	$textBeforeProductsLineHeight = $this->get_option('textBeforeProductsLineHeight');
	    	$textBeforeProductsTextAlign = $this->get_option('textBeforeProductsTextAlign');

	    	$css .= '
				.text-before-container {
				font-size: ' . $textBeforeProductsFontSize . 'px; 
				line-height: ' . $textBeforeProductsLineHeight . 'px;
				text-align: ' . $textBeforeProductsTextAlign . ';
			}

			.text-before {
				padding: ' . $productsContainerPadding . ';
			}';
		}

		// Text After
		if($this->get_option('enableTextAfterProducts'))
		{
			$productContainerPadding = $this->get_option('productContainerPadding');
	    	$productsContainerPadding = '10px ' . $productContainerPadding['padding-right'] . ' 0px '  . $productContainerPadding['padding-right'];

	    	$textAfterProductsFontSize = $this->get_option('textAfterProductsFontSize');
	    	$textAfterProductsLineHeight = $this->get_option('textAfterProductsLineHeight');
	    	$textAfterProductsTextAlign = $this->get_option('textAfterProductsTextAlign');

	    	$css .= '
				.text-after-container {
				font-size: ' . $textAfterProductsFontSize . 'px; 
				line-height: ' . $textAfterProductsLineHeight . 'px;
				text-align: ' . $textAfterProductsTextAlign . ';
			}

			.text-after {
				padding: ' . $productsContainerPadding . ';
			}';
		}


		// Products
		$productsHeadingsFontSize = $this->get_option('productsHeadingsFontSize');
		$productsHeadingsFontFamily = $this->get_option('productsHeadingsFontFamily') ? $this->get_option('productsHeadingsFontFamily') : 'dejavusans';
		$productsHeadingsLineHeight = $this->get_option('productsHeadingsLineHeight');

    	$productContainerPadding = $this->get_option('productContainerPadding');
    	$productContainerPadding = $productContainerPadding['padding-top'] . ' ' . $productContainerPadding['padding-right'] . ' ' . $productContainerPadding['padding-bottom'] . ' ' . $productContainerPadding['padding-left'];

		$productInformationContainerPadding = $this->get_option('productInformationContainerPadding');
    	$productInformationContainerPadding = $productInformationContainerPadding['padding-top'] . ' ' . $productInformationContainerPadding['padding-right'] . ' ' . $productInformationContainerPadding['padding-bottom'] . ' ' . $productInformationContainerPadding['padding-left'];

		$productsTopBorder = $this->get_option('productsTopBorder');
		$productsRightBorder = $this->get_option('productsRightBorder');
		$productsBottomBorder = $this->get_option('productsBottomBorder');
		$productsLeftBorder = $this->get_option('productsLeftBorder');

		empty($productsTopBorder['border-top']) ? $productsTopBorder['border-top'] = '0' : $productsTopBorder['border-top'] = $productsTopBorder['border-top'];
		empty($productsRightBorder['border-top']) ? $productsRightBorder['border-top'] = '0' : $productsRightBorder['border-top'] = $productsRightBorder['border-top'];
		empty($productsBottomBorder['border-top']) ? $productsBottomBorder['border-top'] = '0' : $productsBottomBorder['border-top'] = $productsBottomBorder['border-top'];
		empty($productsLeftBorder['border-top']) ? $productsLeftBorder['border-top'] = '0' : $productsLeftBorder['border-top'] = $productsLeftBorder['border-top'];	

    	$productsTopBorder = $productsTopBorder['border-top'] . ' ' . $productsTopBorder['border-style'] . ' ' . $productsTopBorder['border-color'];
    	$productsRightBorder = $productsRightBorder['border-top'] . ' ' . $productsRightBorder['border-style'] . ' ' . $productsRightBorder['border-color'];
    	$productsBottomBorder = $productsBottomBorder['border-top'] . ' ' . $productsBottomBorder['border-style'] . ' ' . $productsBottomBorder['border-color'];
    	$productsLeftBorder = $productsLeftBorder['border-top'] . ' ' . $productsLeftBorder['border-style'] . ' ' . $productsLeftBorder['border-color'];

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsFontSize = $this->get_option('productsFontSize');
		$productsLineHeight = $this->get_option('productsLineHeight');
		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$productsImageWidth = $this->get_option('productsImageWidth');
		$productsContentWidth = $this->get_option('productsContentWidth');
		$productsContainerHeight = $this->get_option('productsContainerHeight');
	
		$css .= '
				.two-cols .product-images-container {
					width: ' . $productsImageWidth . '%; 
				}
				.two-cols .product-content-container {
					width: ' . $productsContentWidth . '%; 
				}
				.product-title, .variations-title {
					font-size: ' . $productsHeadingsFontSize . 'px; 
					font-family: ' . $productsHeadingsFontFamily . '; 
					line-height: ' . $productsHeadingsLineHeight . 'px;
				}
				.products-container {
					padding: ' . $productContainerPadding .  ' ;
					border-top: ' . $productsTopBorder . ';
					border-right: ' . $productsRightBorder . ';
					border-bottom: ' . $productsBottomBorder . ';
					border-left: ' . $productsLeftBorder . ';
					height: ' . $productsContainerHeight . 'px;
				}
				.product-container, .product-container-row {
					background-color: ' . $productsBackgroundColor . '; 
					text-align: ' . $productsTextAlign . '; 
					color: ' . $productsTextColor . ';
					font-size: ' . $productsFontSize . 'px;
					line-height: ' . $productsLineHeight . 'px;
					width: 100%;
				}
				indexentry {
				    width: 100%;
				    display: block;
				    clear: both;
				}
				.woocommerce-pdf-catalog-variations .variation-head-row, .woocommerce-pdf-catalog-variations .variation-head-row th {
					background-color: #333333;
					color: #FFF;
				}
				.woocommerce-pdf-catalog-variations {
					width: 100%;
				}
				.variations-table td {
					padding: 5px;
					border: 1px solid #eaeaea;
				}
				.split-variation-tables td {
					padding: 0px;
					border: none;
				}
				.split-variation-tables .variation-table-split td {
					padding: 5px;
					border: 1px solid #eaeaea;
				}
				.variation-table-split {
					margin-right: 10px;
				}
				.product-information-container {
					padding: ' . $productInformationContainerPadding .  ' ;
				}
				.firstlayout, { width:100%; }
				.firstlayout td { vertical-align: top; }
				h1,h2,h3,h4,h5,h6 { font-family: ' . $productsHeadingsFontFamily . ', sans-serif;}
				h1 { font-size: ' . $productsHeadingsFontSize . 'pt; text-transform: uppercase; line-height: ' . $productsHeadingsLineHeight . 'pt;}
				h2 { font-size: ' . $productsHeadingsFontSize . 'pt; text-transform: uppercase; line-height: ' . $productsHeadingsLineHeight . 'pt;}
				';

			$productsLayout = $this->get_option('productsLayout');
			if($productsLayout == 8) {
				$dataToShow = array(
			  		'showImage' => $this->get_option('showImage'),
					'showGalleryImages' => $this->get_option('showGalleryImages'),
					'showTitle' => $this->get_option('showTitle'),
					'showPrice' => $this->get_option('showPrice'),
					'showAttributes' => $this->get_option('showAttributes'),
					'showShortDescription' => $this->get_option('showShortDescription'),
					'showDescription' => $this->get_option('showDescription'),
					'showReadMore' => $this->get_option('showReadMore'),
					'showSKU' => $this->get_option('showSKU'),
					'showCategories' => $this->get_option('showCategories'),
					'showTags' => $this->get_option('showTags'),
					'showQR' => $this->get_option('showQR'),
				);

				$customMetaKeys = $this->get_option('customMetaKeys');
				if(isset($customMetaKeys['enabled'])) {
					unset($customMetaKeys['enabled']['placebo']);
				}

				if(isset($customMetaKeys['enabled']) && !empty($customMetaKeys['enabled'])) {
					$dataToShow = array_merge($dataToShow, $customMetaKeys['enabled']);
				}

				$count = count( array_filter($dataToShow));


				$divWidth = (90 / $count) - 2;
				$css .= '.col-width { width: ' . $divWidth . '%; padding-right: 25px; }
						.product-header { font-weight: bold; padding-top: 2px; padding-bottom: 5px; }
						.product-container { padding-top: 2px; padding-bottom: 2px; }
						.odd { background-color: #f2f2f2; display: block; }';
			}

			if($productsLayout == 9) {
				$dataToShow = array(
			  		'showImage' => $this->get_option('showImage'),
					'showGalleryImages' => $this->get_option('showGalleryImages'),
					'showTitle' => $this->get_option('showTitle'),
					'showPrice' => $this->get_option('showPrice'),
					'showAttributes' => $this->get_option('showAttributes'),
					'showShortDescription' => $this->get_option('showShortDescription'),
					'showDescription' => $this->get_option('showDescription'),
					'showReadMore' => $this->get_option('showReadMore'),
					'showSKU' => $this->get_option('showSKU'),
					'showCategories' => $this->get_option('showCategories'),
					'showTags' => $this->get_option('showTags'),
					'showQR' => $this->get_option('showQR'),
				);


				$customMetaKeys = $this->get_option('customMetaKeys');
				if(isset($customMetaKeys['enabled'])) {
					unset($customMetaKeys['enabled']['placebo']);
				}

				if(isset($customMetaKeys['enabled']) && !empty($customMetaKeys['enabled'])) {
					$dataToShow = array_merge($dataToShow, $customMetaKeys['enabled']);
				}

				$count = count( array_filter($dataToShow));

				$divWidth = (90 / $count) - 2;
				$css .= '.col-width { width: ' . $divWidth . '%; padding-left: 5px; }
						.product-header { font-weight: bold; padding-top: 2px; padding-bottom: 5px; }
						.product-container { padding-top: 2px; padding-bottom: 2px; margin-right: 10px; }
						.odd { background-color: #f2f2f2; display: block; }';
			}


		$customCSS = $this->get_option('customCSS');
		if(!empty($customCSS))
		{
			$css .= $customCSS;
		}

		$css .= '
			</style>

		</head>';

		return $css;
    }

    public function get_header()
    {
    	$headerLayout = $this->get_option('headerLayout');

    	$topLeft = $this->get_option('headerTopLeft');
    	$topMiddle = $this->get_option('headerTopMiddle');
    	$topRight = $this->get_option('headerTopRight');

    	if($headerLayout == "oneCol")
    	{
			$header = '
			<div class="header one-col">
				<div class="header-blockne" style="text-align: center;">' . $this->get_header_footer_type($topLeft, 'headerTopLeft') . '</div>
			</div>';
    	} elseif($headerLayout == "threeCols") {
			$header = '
			<div class="header three-cols">
				<div  class="col header-blockne" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'headerTopLeft') . '</div>
				<div  class="col header-block-two" style="text-align: center;">' . $this->get_header_footer_type($topMiddle, 'headerTopMiddle') . '</div>
				<div  class="col header-block-three" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'headerTopRight') . '</div>
			</div>';
		} else {
			$header = '
			<div class="header two-cols">
				<div  class="col header-blockne" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'headerTopLeft') . '</div>
				<div  class="col header-block-two" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'headerTopRight') . '</div>
			</div>';
		}

		return $header;
    }

    public function get_footer()
    {
    	$footerLayout = $this->get_option('footerLayout');

    	$topLeft = $this->get_option('footerTopLeft');
    	$topMiddle = $this->get_option('footerTopMiddle');
    	$topRight = $this->get_option('footerTopRight');

    	if($footerLayout == "oneCol")
    	{
			$footer = '
			<div class="footer one-col">
				<div class="footer-blockne" style="text-align: center;">' . $this->get_header_footer_type($topLeft, 'footerTopLeft') . '</div>
			</div>';
    	} elseif($footerLayout == "threeCols") {
			$footer = '
			<div class="footer three-cols">
				<div  class="col footer-blockne" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'footerTopLeft') . '</div>
				<div  class="col footer-block-two" style="text-align: center;">' . $this->get_header_footer_type($topMiddle, 'footerTopMiddle') . '</div>
				<div  class="col footer-block-three" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'footerTopRight') . '</div>
			</div>';
		} else {
			$footer = '
			<div class="footer two-cols">
				<div  class="col footer-blockne" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'footerTopLeft') . '</div>
				<div  class="col footer-block-two" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'footerTopRight') . '</div>
			</div>';
		}

		return $footer;
    }

    private function get_header_footer_type($type, $position)
    {
    	// Custom Attribute Header & Footer
		if($this->current_category_id == "attr" && ($position == "headerTopLeft" || $position == "footerTopLeft")) {

			$termId = $_GET['term-id'];

			if(isset($this->term_metas[$termId])) {

				if($position == "headerTopLeft" && isset($this->term_metas[$termId]['headerTopLeft']) && !empty($this->term_metas[$termId]['headerTopLeft'])) {
					return wpautop( do_shortcode( $this->term_metas[$termId]['headerTopLeft'] ) );
				}

				if($position == "footerTopLeft" && isset($this->term_metas[$termId]['footerTopLeft']) && !empty($this->term_metas[$termId]['footerTopLeft'])) {
					return wpautop( do_shortcode( $this->term_metas[$termId]['footerTopLeft'] ) );
				}
			}
    	}

    	switch ($type) {
    		case 'text':
    			return wpautop( do_shortcode( $this->get_option($position.'Text') ) );
    			break;
    		case 'bloginfo':
    			return $this->data->blog_name.'<br/>'.$this->data->blog_description;
    			break;
    		case 'pagenumber':
				return __( 'Page:', 'woocommerce-pdf-catalog').' {PAGENO}';
    			break;
    		case 'image':
    			$image = $this->get_option($position.'Image');
    			$imageSrc = $image['url'];
    			$imageHTML = '<img src="' . $image['url'] . '">';
    			return $imageHTML;
    			break;
    		case 'exportinfo':
    			return date( get_option('date_format') );
    			break;
    		case 'toc':
    			$backToToCText = $this->get_option('backToToCText');
    			return '<a href="#table_of_contents">' . $backToToCText . '</a>';
    			break;
			case 'qr':
				if($this->current_category_id == "full") {
					$url = get_permalink( wc_get_page_id( 'shop' ) );
				} 
				elseif($this->current_category_id == "cart") {
					$url = get_permalink( wc_get_page_id( 'cart' ) );
				} else {
					$category = get_term($this->current_category_id, 'product_cat');
					$url = get_term_link($category);
				}
				if(empty($url)) {
					return '';
				}	
				return '<barcode code="' . $url . '" type="QR" class="barcode" size="0.8" error="M" />';
				break;
			case 'category':
				if($this->current_category_id == "full") {
					return __('Complete Catalog', 'woocommerce-pdf-catalog');
				}
				$category = get_term($this->current_category_id, 'product_cat');
				return $category->name;
    		default:
    			return '&nbsp;';
    			break;
    	}
    }

    protected function get_categories()
    {
		$categories = array();

		// WOOF Filter plugin support
		$queryCategories = array();
		if(isset($_GET['product_cat']) && !empty($_GET['product_cat'])) {
			$queryCategories = explode(',', $_GET['product_cat']);
		}

		if($this->get_option('filterSupport') && !empty($queryCategories)) {
			foreach ($queryCategories as $queryCategory) {
				if(is_numeric($queryCategory)) {
					$categories[] = $queryCategory;
				} else {
					$queryCategory = get_term_by('slug', $queryCategory, 'product_cat');
					if($queryCategory) {
						$categories[] = $queryCategory->term_id;
					}
				}
			}
		} elseif($this->current_category_id == 'full'){
			$taxonomy     = 'product_cat';
			$orderby      = 'menu_order';

			if($this->get_option('orderCategories')) {
				$orderKey = $this->get_option('orderCategoriesKey');
				if(!empty($orderKey)) {
					$orderby = $orderKey;
				}
			
				$args = array(
				     'taxonomy'     => $taxonomy,
				     'orderby'      => $orderby,
				     'parent' 		=> 0
				);
			}

			$parentCategories = get_terms( $args );

			if (version_compare(phpversion(), '7.0.0', '<')) {
			    $temp = array();
			    foreach ($parentCategories as $parentCategory) {
			    	$temp[] = $parentCategory->term_id;
		    	}
		    	$parentCategories = $temp;
			} else {
				$parentCategories = array_column($parentCategories, 'term_id');	
			}

			foreach ($parentCategories as $parentCategory) {
				
				$categories[] = $parentCategory;
				if (!$this->get_option('showSubcategories')) {
					continue;
				}

				$args = array(
					'taxonomy' => 'product_cat', 
					'child_of' => $parentCategory,
					'orderby'  => $orderby,
				);
				$subcategories = get_terms($args);

				if (version_compare(phpversion(), '7.0.0', '<')) {
				    $temp = array();
				    foreach ($subcategories as $subcategory) {
				    	$temp[] = $subcategory->term_id;
			    	}
			    	$subcategories = $temp;
				} else {
					$subcategories = array_column($subcategories, 'term_id');
				}

				$categories = array_merge($categories, $subcategories);
			}

		} else {
			$categories = explode(',', $this->current_category_id);

			if ($this->get_option('showSubcategories'))
			{
				$args = array(
					'taxonomy' => 'product_cat', 
					'child_of' => $this->current_category_id,
				);

				if($this->get_option('orderCategories')) {
					$orderKey = $this->get_option('orderCategoriesKey');
					if(!empty($orderKey)) {
						$args['orderby'] = $orderKey;
					}
				}

				$subcategories = get_terms($args);

				if (version_compare(phpversion(), '7.0.0', '<')) {
				    $temp = array();
				    foreach ($subcategories as $subcategory) {
				    	$temp[] = $subcategory->term_id;
			    	}
			    	$subcategories = $temp;
				} else {
					$subcategories = array_column($subcategories, 'term_id');
				}

				$categories = array_merge($categories, $subcategories);
			}
		}

		return $categories;
    }

	public function set_exlusions()
	{
		$this->exclude_product_categories = $this->get_option('excludeProductCategories');
		$this->exclude_product_categories_revert = $this->get_option('excludeProductCategoriesRevert');
		$this->exclude_product_categories_products = $this->get_option('excludeProductCategoriesProducts');		
	}

	public function set_ordering()
	{
		// Setup Ordering
		$this->orderByMetaKey = ""; 
		$this->orderBy = $this->get_option('orderby');
		if(empty($this->orderBy)) {
			$this->orderBy = "date";
		}

		$needMetaKey = array('_regular_price', '_stock');
		if(in_array($this->orderBy, $needMetaKey)) {
			$this->orderByMetaKey = $this->orderBy;
			$this->orderBy = 'meta_value_num';
		}

		$needMetaKey = array('_sku');
		if(in_array($this->orderBy, $needMetaKey)) {
			$this->orderByMetaKey = $this->orderBy;
			$this->orderBy = 'meta_value';
		}

		$this->order = $this->get_option('order');
		if(empty($this->order)) {
			$this->order = "DESC";
		}


		if(isset($_GET['orderby'])) {

			switch ( $_GET['orderby'] ) {
				case 'id':
					$this->orderBy = 'ID';
					break;
				case 'menu_order':
					$this->orderBy = 'menu_order title';
					break;
				case 'title':
					$this->orderBy = 'title';
					$this->order = "DESC";
					break;
				case 'relevance':
					$this->orderBy = 'relevance';
					$this->order   = 'DESC';
					break;
				case 'rand':
					$this->orderBy = 'rand'; // @codingStandardsIgnoreLine
					break;
				case 'date':
					$this->orderBy = 'date ID';
					$this->order = "DESC";
					break;
				case 'price':
					$this->orderByMetaKey = '_price';
					$this->orderBy = 'meta_value_num';
					$this->order = "ASC";
					break;
				case 'price-desc':
					$this->orderByMetaKey = '_price';
					$this->orderBy = 'meta_value_num';
					$this->order = "DESC";
					break;
				case 'popularity':
					$this->orderByMetaKey = '_total_sales';
					$this->orderBy = 'meta_value_num';
					break;
				case 'rating':
					$this->orderByMetaKey = '_average_rating';
					$this->orderBy = 'meta_value_num';
					break;
			}
		}
	}

    public function get_products($category_id, $termId = "")
    {
		// Tax Query
		$tax_query = array();
		global $wp_query;

		$taxonomy = "product_cat";
		if(isset($_GET['taxonomy']) && !empty($_GET['taxonomy'])) {
			$taxonomy = esc_attr($_GET['taxonomy']);
		}

		// Merge Original Taxonomies from Query / For Filters enabling
		if($this->get_option('useDefaultQuery')) {
			$wp_query->tax_query;
			if(isset($wp_query->tax_query) && isset($wp_query->tax_query->queries)) {
				$original_tax_queries = $wp_query->tax_query->queries;
				foreach ($original_tax_queries as $key => $original_tax_query) {
					if(isset($original_tax_query['taxonomy']) && ($original_tax_query['taxonomy'] == "product_cat")) {
						unset($original_tax_queries[$key]);
					}
				}
				$tax_query = $original_tax_queries;
			}
		}

		// Set custom product_cat query
		if(!empty($category_id)) {
			$tax_query_custom = array(
				'taxonomy' => $taxonomy,
				'field' => 'id',
				'terms' => $category_id,
		    );

			if(!$this->get_option('includeChildren')) {
				$tax_query_custom['include_children']  = false;
			}	

			$tax_query[] = $tax_query_custom;
	    }

	    // Set attribute tax query
	    if(!empty($taxonomy) && !empty($termId)) {

			$tax_query_custom = array(
				'taxonomy' => $taxonomy,
				'field' => 'id',
				'terms' => $termId,
		    );
			$tax_query[] = $tax_query_custom;
	    }	
		
		// Meta Query
		$meta_query = array();
		if($this->get_option('useDefaultQuery')) {
			if(isset($wp_query->meta_query) && isset($wp_query->meta_query->queries)) {
				$original_meta_queries = $wp_query->meta_query->queries;
				$meta_query = $original_meta_queries;
			}
		}

		$excludeOutOfStock = $this->get_option('excludeOutOfStockProducts');
		if($excludeOutOfStock === "1") {
			$meta_query[] = array(
		            'key' => '_stock_status',
		            'value' => 'instock'
	        );
	        $meta_query['relation'] = 'AND';
		}

		$args = array( 
			'posts_per_page' => -1, 
			'post_status' => 'publish', 
			'post_type' => 'product', 
			'order' => $this->order,
			'orderby' => $this->orderBy,
			'tax_query' => $tax_query,
			'meta_query' => $meta_query,
			'meta_key' => $this->orderByMetaKey,
			'suppress_filters' => false
		);




		if($this->get_option('singleVariationsSupport') && class_exists('WooCommerce_Single_Variations')) {
			$args['post_type'] = array('product', 'product_variation');
		}

		$exclude_products = $this->get_option('excludeProducts');
		if(!empty($exclude_products)) {
			$excludeProductsRevert = $this->get_option('excludeProductsRevert');
			if($excludeProductsRevert) {
				$args['include'] = $exclude_products;
			} else {
				$args['exclude'] = $exclude_products;
			}
		}
				
		$products = get_posts($args);
// 		var_dump($args);
// var_dump($products);
// 		die();
		return $products;
    }

    protected function cmp($a, $b) {
    	return strcmp($a->path,$b->path);
	}

    protected function get_not_in($category_id)
    {
		$subcategories = get_term_children( $category_id, 'product_cat' );

		if(!empty($subcategories)) {
			return true;
		}

		return false;
    }

	public function get_pagebreak()
	{
		$html = '<pagebreak />';
		return $html;
	}

	public function get_text_before_products()
	{
		$textBeforeProducts = $this->get_option('textBeforeProducts');

		$html = '
		<div class="container text-before-container" width="100%">
				<div class="text-before" width="100%">' . wpautop( do_shortcode( $textBeforeProducts) ). '</div>
		</div>';

		return $html;
	}

	public function get_text_after_products()
	{
		$textAfterProducts = $this->get_option('textAfterProducts');

		$html = '
		<div class="container text-after-container" width="100%">
			<div class="text-after" width="100%">' . wpautop( do_shortcode( $textAfterProducts) ). '</div>
		</div>';

		return $html;
	}

    private function escape_filename($file)
    {
		$file = strtolower(trim($file));
		$find = array(' ', '&', '\r\n', '\n', '+',',');
		$file = str_replace ($find, '-', $file);
		$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
		$repl = array('', '-', '');
		$file = preg_replace ($find, $repl, $file);

		return $file;
    }

    public function get_cover()
    {
    	$html = "";
    	$coverImage = $this->get_option('coverImage');

    	if($this->current_category_id == "sale" && $this->get_option('saleCoverImage')) {
			$coverImage = $this->get_option('saleCoverImage');
    	} elseif($this->current_category_id == "wishlist" && $this->get_option('wishlistCoverImage')) {
			$coverImage = $this->get_option('wishlistCoverImage');
    	} elseif($this->current_category_id == "attr") {

			$termId = $_GET['term-id'];

			if(isset($this->term_metas[$termId]) && isset($this->term_metas[$termId]['thumbnail'])) {
				$coverImage = array(
					'url' => wp_get_attachment_image_src($this->term_metas[$termId]['thumbnail'], 'full')[0]
				);
			}

    	} else {
	        $customCoverImage = get_term_meta($this->current_category_id, 'woocommerce_pdf_catalog_cover_image');
	        if(!empty($customCoverImage)) {
	        	if(isset($customCoverImage[0]['url'])) {
		        	$coverImage = $customCoverImage[0];
	        	}
	        }
        }
        
    	if(!$coverImage) {
    		return $html;
    	} 

    	$imageURL = $coverImage['url'];

		if($this->get_option('performanceUseImageLocally') && !empty($imageURL)) {
		    $uploads = wp_upload_dir();
			$imageURL = str_replace( $uploads['baseurl'], $uploads['basedir'], $imageURL );
		}
   		
    	$html .= '<style media="all">@page :first { background: url("' . $imageURL . '"); background-repeat: none; background-image-resize: 6; }</style>';

    	$coverText = apply_filters('woocommerce_pdf_catalog_cover_text', $this->get_option('coverText'), $this->current_category_id);
    	if(!empty($coverText)) {
    		$html .= '<div class="woocommerce-pdf-catalog-cover-text">' . $coverText . '</div>';
    	}

    	$html .= '<a name="cover"><pagebreak odd-footer-name="defaultFooter" odd-footer-value="on" odd-header-name="defaultHeader" odd-header-value="on"></pagebreak></a>';
    	
    	return $html;
    }

    public function get_backcover()
    {
    	$html = "";
    	$backcoverImage = $this->get_option('backcoverImage');

    	if($this->current_category_id == "sale" && $this->get_option('saleBackcoverImage')) {
			$backcoverImage = $this->get_option('saleBackcoverImage');
    	} elseif($this->current_category_id == "wishlist" && $this->get_option('wishlistBackcoverImage')) {
			$backcoverImage = $this->get_option('wishlistBackcoverImage');
    	} else {
	        $customBackcoverImage = get_term_meta($this->current_category_id, 'woocommerce_pdf_catalog_backcover_image');
	        if(!empty($customBackcoverImage)) {
	        	if(isset($customBackcoverImage[0]['url'])) {
		        	$backcoverImage = $customBackcoverImage[0];
	        	}
	        }    		
    	}

    	if(!$backcoverImage) {
    		return $html;
    	} 
    	$imageURL = $backcoverImage['url'];

		if($this->get_option('performanceUseImageLocally') && !empty($imageURL)) {
		    $uploads = wp_upload_dir();
			$imageURL = str_replace( $uploads['baseurl'], $uploads['basedir'], $imageURL );
		}

		// $html .= $this->get_pagebreak();
		$html .= '<style media="all">@page :first { background: url("' . $imageURL . '"); background-repeat: none; background-image-resize: 6; }</style>';
		
		$html .= '<div class="noheader" style="background: url(' . $imageURL . '); background-repeat: none; background-image-resize: 6; width:100%; height:100%; margin-top: -50px;">';

	    	$backcoverText = apply_filters('woocommerce_pdf_catalog_backcover_text', $this->get_option('backcoverText'), $this->current_category_id);
	    	if(!empty($backcoverText)) {
	    		$html .= '<div class="woocommerce-pdf-catalog-backcover-text">' . $backcoverText . '</div>';
	    	}

		$html .= '</div>';
    	
    	return $html;
    }

    public function get_category_cover($category_id)
    {
    	$html = "";
        $customCoverImage = get_term_meta($category_id, 'woocommerce_pdf_catalog_cover_image');
        if(!empty($customCoverImage)) {
        	if(isset($customCoverImage[0]['url'])) {
	        	$coverImage = $customCoverImage[0];
        	}
        }

    	if(!$coverImage) {
    		return $html;
    	} 
    	$imageURL = $coverImage['url'];

   		if($this->get_option('performanceUseImageLocally') && !empty($imageURL)) {
		    $uploads = wp_upload_dir();
			$imageURL = str_replace( $uploads['baseurl'], $uploads['basedir'], $imageURL );
		}

		if(!$this->firstPage) {
			$html .= $this->get_pagebreak();	
		} else {
			$this->firstPage = false;
		}

    	$html .= '<div style="background: url(' . $imageURL . '); background-repeat: none; background-image-resize: 6; width:100%; height:100%; margin-top: -50px;"></div>';
    	$html .= $this->get_pagebreak();
    	
    	return $html;
    }

    public function table_of_contents()
    {

    	$paging = $this->get_option('ToCPaging');
    	$linking = $this->get_option('ToCLinking');

    	$textBefore = $this->get_option('ToCTextBefore') ? $this->get_option('ToCTextBefore') : '';
    	$textBefore = '<div class="toc_before">' . $textBefore . '</div>';

    	$textAfter = $this->get_option('ToCTextAfter') ? $this->get_option('ToCTextAfter') : '';
    	$textAfter = '<div class="toc_after">' . $textAfter . '</div>';

    	$additionalSettings = "";

    	$removeHeader = $this->get_option('ToCRemoveHeader');
    	if($removeHeader) {
    		$additionalSettings .= ' tocdd-header-value="-1"';
    	}

    	$removeFooter = $this->get_option('ToCRemoveFooter');
    	if($removeFooter) {
    		$additionalSettings .= ' tocdd-footer-value="-1"';
    	}

    	$resetPageNumber = $this->get_option('ToCResetPageNumber');
    	if($resetPageNumber || $this->showCoverPage) {
    		$additionalSettings .= ' toc-resetpagenum="1"';
    	}
    	
	    $html = '<tocpagebreak paging="' . $paging . '" ' . $additionalSettings . ' links="' . $linking . '" toc-preHTML="' . htmlspecialchars( $textBefore, ENT_QUOTES) . '" toc-postHTML="' . htmlspecialchars( $textAfter, ENT_QUOTES) . '" /><a name="table_of_contents"></a>';

	    return $html;
    }

    public function index()
    {

		$letters = $this->get_option('indexLetters');
		$linking = $this->get_option('indexLinking');
		$textBefore = $this->get_option('indexTextBefore');
		$textAfter = $this->get_option('indexTextAfter');
		$columns = $this->get_option('indexColumns');
		

		$html = '<div class="index_before">';
		$html .= $textBefore;
		$html .= '</div>';

		$html .= '<columns column-count="' . $columns . '" column-gap="5" />';
			$html .= '<indexinsert usedivletters="' . $letters . '" links="' . $linking . '" />';
		$html .= '<columns column-count="1" />';

		$html .= '<div class="index_after">';
		$html .= $textAfter;
		$html .= '</div>';
	
		return $html;

    }

	public function send_cart_email_popup()
	{
		$sendEMailTo = $this->get_option('sendEMailTo');
		$sendEMailText = $this->get_option('sendEMailText');
		$sendEMailSendButtonText = $this->get_option('sendEMailSendButtonText');

		$sendEMailToLabel = $this->get_option('sendEMailToLabel');
		$sendEMailTextLabel = $this->get_option('sendEMailTextLabel');
		$sendEMailTypeLabel = $this->get_option('sendEMailTypeLabel');
		$sendEMailCategoryLabel = $this->get_option('sendEMailCategoryLabel');

		$sendEMailToPlaceholder = $this->get_option('sendEMailToPlaceholder');
		$sendEMailTextPlaceholder = $this->get_option('sendEMailTextPlaceholder');
		$sendEMailTypePlaceholder = $this->get_option('sendEMailTypePlaceholder');
		$sendEMailCategoryPlaceholder = $this->get_option('sendEMailCategoryPlaceholder');

		$showFullCatalogLinkButtontext = $this->get_option('showFullCatalogLinkButtontext');
		$showCategoryCatalogLinkButtontext = $this->get_option('showCategoryCatalogLinkButtontext');
		$showSaleCatalogLinkButtontext = $this->get_option('showSaleCatalogLinkButtontext');
		$showCartCatalogLinkButtontext = $this->get_option('showCartCatalogLinkButtontext');

		$currentCategoryId = 0;
		$currentCategory = get_queried_object();
		if(isset($currentCategory->term_id)) {
			$currentCategoryId = $currentCategory->term_id;
		}

		?>

		<div class="woocommerce-pdf-catalog-overlay" style="display: none;"></div>
		<div class="woocommerce-pdf-catalog-popup-container" style="display: none;">
			<div class="woocommerce-pdf-catalog-popup">
				<form action="POST" class="woocommerce-pdf-catalog-email-form">

					<label for="woocommerce_pdf_catalog_email_to"><?php echo $sendEMailToLabel ?></label>
					<input name="woocommerce_pdf_catalog_email_to" class="woocommerce-pdf-catalog-email-to" type="text" placeholder="<?php echo $sendEMailToPlaceholder ?>" value="<?php echo $sendEMailTo ?>">

					<?php if($this->get_option('sendEMailTextShow')) { ?>
					<label for="woocommerce_pdf_catalog_email_text"><?php echo $sendEMailTextLabel ?></label>
					<textarea name="woocommerce_pdf_catalog_email_text" class="woocommerce-pdf-catalog-email-text" id="" placeholder="<?php echo $sendEMailTextPlaceholder ?>" cols="30" rows="10"><?php echo $sendEMailText ?></textarea>
					<?php } ?>

					<?php

					$sendEMailTypes = $this->get_option('sendEMailTypes')['enabled'];
					unset($sendEMailTypes['placebo']);

					if(count($sendEMailTypes) == 1 && $this->get_option('sendEMailTypeHideWhenSingle')) {

						$sendEMailType = reset($sendEMailTypes);
						echo '<input name="woocommerce_pdf_catalog_email_type" type="hidden" class="woocommerce-pdf-catalog-email-type" value="' . $sendEMailType . '">';

						if($sendEMailType == "cart" && $currentCategoryId > 0) {
							echo '<input name="woocommerce_pdf_catalog_category" type="hidden" class="woocommerce-pdf-catalog-email-category" value="' . $currentCategoryId . '">';
						}

					} else {

						echo '<label for="woocommerce_pdf_catalog_email_type">' . $sendEMailTypeLabel . '</label>';
						echo '<select name="woocommerce_pdf_catalog_email_type" class="woocommerce-pdf-catalog-email-type">';

						if(count($sendEMailTypes) == 1) {
							$sendEMailType = reset($sendEMailTypes);
							switch ($sendEMailType) {
								case 'full':
									echo '<option selected="selected" value="full">' . $showFullCatalogLinkButtontext . '</option>';
									break;
								case 'category':
									echo '<option selected="selected" value="category">' . $showCategoryCatalogLinkButtontext . '</option>';
									break;
								case 'sale':
									echo '<option selected="selected" value="sale">' . $showSaleCatalogLinkButtontext . '</option>';
									break;
								case 'cart':
									echo '<option selected="selected" value="cart">' . $showCartCatalogLinkButtontext . '</option>';
									break;
							}
						} else {

							echo '<option value="">' . $sendEMailTypePlaceholder . '</option>';
							foreach ($sendEMailTypes as $sendEMailType) {
								switch ($sendEMailType) {
									case 'full':
										echo '<option value="full">' . $showFullCatalogLinkButtontext . '</option>';
										break;
									case 'category':
										echo '<option value="category">' . $showCategoryCatalogLinkButtontext . '</option>';
										break;
									case 'sale':
										echo '<option value="sale">' . $showSaleCatalogLinkButtontext . '</option>';
										break;
									case 'cart':
										echo '<option value="cart">' . $showCartCatalogLinkButtontext . '</option>';
										break;
								}
							}
						}

						echo '</select>';
					}
					?>

					</select>

					<div class="woocommerce-pdf-catalog-email-category-select" style="display: none;">
						<label for="woocommerce_pdf_catalog_category"><?php echo $sendEMailCategoryLabel ?></label>
						<?php

						$args = array(
							'name'				 => 'woocommerce_pdf_catalog_category',
							'class'				 => 'woocommerce-pdf-catalog-email-category',
							'show_option_none'   => $sendEMailCategoryPlaceholder,
							'option_none_value'  => '',
						    'selected'           => $currentCategoryId,
						    'hierarchical'       => 1, 
						    'taxonomy'           => 'product_cat',
						    'exclude'			 => $this->get_option('excludeProductCategories'),
						);
						wp_dropdown_categories( $args ); 

						?>
					</div>

					<button type="submit" class="woocommerce-pdf-catalog-email-send button btn btn-primary"><?php echo $sendEMailSendButtonText ?></button>
				</form>
			</div>	
		</div>

		<?php
	}

    public function send_email()
    {
		$response = array(
			'status' => 0,
			'message' => '',
		);

        if (!defined('DOING_AJAX') || !DOING_AJAX) {
        	$response['message'] = esc_html__('No AJAX call', 'woocommerce-pdf-catalog');
        	echo json_encode($response);
            die();
        }

        if (!isset($_POST['to']) || !isset($_POST['type'])) {
            $response['message'] = esc_html__('To missing.', 'woocommerce-pdf-catalog');
            echo json_encode($response);
            die();
        }

        $to = sanitize_text_field($_POST['to']);
        $type = sanitize_text_field($_POST['type']);
        if($this->get_option('sendEMailTextShow')) {
	        $text = sanitize_text_field($_POST['text']);
	    } else {
	    	$text = $this->get_option('sendEMailText');
	    }

        if (empty($to) || empty($text) || empty($type)) {
            $response['message'] = esc_html__('Empty to, type or text.', 'woocommerce-pdf-catalog');
            echo json_encode($response);
            die();
        }

        $text .= sprintf( __('<br><br>This mail was sent to %s', 'woocommerce-pdf-catalog'), $to);

        $text = wpautop($text);

        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $subject = $this->get_option('sendEMailSubject');
        
        $cc = $this->get_option('sendEMailCC');
        if(!empty($cc)) {
        	$headers[] = 'Cc: ' . $cc;
        }

        $bcc = $this->get_option('sendEMailBCC');
        if(!empty($bcc)) {
        	$headers[] = 'Bcc: ' . $bcc;
        }

        if($type == "category") {

	        if (!isset($_POST['category']) || empty($_POST['category'])) {
	            $response['message'] = esc_html__('Category missing.', 'woocommerce-pdf-catalog');
	            echo json_encode($response);
	            die();
	        }

	        $category = intval($_POST['category']);
	        $this->current_category_id = $category;
        } else {
        	$this->current_category_id = $type;
        }

        $pdf = $this->build_pdf(true);

        if(wp_mail($to, $subject, $text, $headers, $pdf)) {
        	$response['status'] = 1;
			$response['message'] = esc_html__('Email sent.', 'woocommerce-pdf-catalog');
        } else {
			$response['message'] = esc_html__('Email not sent.', 'woocommerce-pdf-catalog');
        }

        echo json_encode($response);
        die();
    }

	/**
	 * Return the current user role
	 *
	 * @since    1.0.0
	 */
	private function get_user_role()
	{
		global $current_user;

		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);

		return $user_role;
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
}