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
		global $woocommerce_pdf_catalog_options;

		$this->options = $woocommerce_pdf_catalog_options;

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-pdf-catalog-public.css', array(), $this->version, 'all' );
		wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), '4.5.0', 'all' );
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

		$customJS = $this->get_option('customJS');
		if(empty($customJS))
		{
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
		add_action( $linkPosition, array($this, 'show_print_links'), 90 );

		// Cart Button
		if($this->get_option('showCartCatalogLink')) {
			$cartLinkPosition = $this->get_option('cartLinkPosition');
			add_action( $cartLinkPosition, array($this, 'show_cart_print_links'), 90 );
		}

		if(isset($_GET['pdf-catalog'])) {
			add_action("wp", array($this, 'watch'));
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
    public function show_print_links()
    {
		$current_category = get_queried_object();

		if(!isset($current_category->term_id)) {
			return false;
		}
		
		$current_category_id = $current_category->term_id;

    	echo '<div class="woocommerce-pdf-catalog link-wrapper">';

    	if($this->get_option('showFullCatalogLink')) {
			echo '<a href="' . $this->url . 'pdf-catalog=full" class="woocommerce_pdf_catalog_button button alt" target="_blank"><i class="fa fa-file-pdf-o fa-1x "></i>  ' . __('Complete Catalog (PDF)', 'woocommerce-pdf-catalog') . '</a>';
		}

		if($this->get_option('showCategoryCatalogLink') && !empty($current_category_id)) {
			echo '<a href="' . $this->url . 'pdf-catalog=' . $current_category_id . '" class="woocommerce_pdf_catalog_button button alt" target="_blank"><i class="fa fa-file-pdf-o fa-1x "></i>  ' . __('Category Catalog (PDF)', 'woocommerce-pdf-catalog') . '</a>';
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
    public function show_cart_print_links()
    {
		if(!is_cart()) {
			return false;
		}

    	echo '<div class="woocommerce-pdf-catalog link-wrapper">';

		echo '<a href="' . $this->url . 'pdf-catalog=cart" class="woocommerce_pdf_catalog_button button alt" target="_blank"><i class="fa fa-file-pdf-o fa-1x "></i>  ' . __('Cart Catalog (PDF)', 'woocommerce-pdf-catalog') . '</a>';

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
    public function build_pdf()
    {
    	$this->mpdf = $this->get_mpdf();

		$html = "";

		if($this->current_category_id == "full") {
			$filename = 'complete';
		} 
		elseif($this->current_category_id == "cart") {
			$filename = 'cart';
		} else {
			$term = get_term($this->current_category_id);
			$filename = $this->escape_filename($term->slug);	
		}

		// Check Caching
		$enableCache = $this->get_option('enableCache');
		if($enableCache === "1") {

			$cachedFile = plugin_dir_path( dirname( __FILE__ ) ) . 'cache/' . $filename . '.pdf';

			$checkFileExists = file_exists( $cachedFile );
			if($checkFileExists) {

				header("Content-type:application/pdf");
				// header("Content-Disposition:attachment;filename='" . $filename . ".pdf'");
				readfile($cachedFile);
				exit;
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

		if($this->get_option('enableToC') && $this->get_option('ToCPosition') == "first") {

			if($this->get_option('ToCOnlyFull') && !($this->current_category_id == 'full') ) {
				
			} else {
				$html .= $this->table_of_contents();
			}
		}

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

		if($this->current_category_id !== "cart") {
			$categories = $this->get_categories();
		}

		$exclude_product_categories = $this->get_option('excludeProductCategories');
		$exclude_product_categories_revert = $this->get_option('excludeProductCategoriesRevert');
		$exclude_product_categories_products = $this->get_option('excludeProductCategoriesProducts');

		// Order By
		$orderByMetaKey = ""; 
		$orderBy = $this->get_option('orderby');
		if(empty($orderBy)) {
			$orderBy = "date";
		}

		$needMetaKey = array('_sku', '_regular_price', '_stock');
		if(in_array($orderBy, $needMetaKey)) {
			$orderByMetaKey = $orderBy;
			$orderBy = 'meta_value_num';
		}

		$order = $this->get_option('order');
		if(empty($order)) {
			$order = "DESC";
		}

		if($this->current_category_id !== "cart") {

			$categories_count = count($categories);
			$loop = 1;
			foreach ($categories as $category_id) {
				$this->category = get_term($category_id, 'product_cat');

				$this->category->description = apply_filters('woocommerce_pdf_catalog_category_description', $this->category->description, $this->category->term_id);

				if(!empty($exclude_product_categories)){
					if($exclude_product_categories_revert) {
						if(!in_array($category_id, $exclude_product_categories)){
							$loop++;
							continue;
						}
					} else {
						if(in_array($category_id, $exclude_product_categories)){
							$loop++;
							continue;
						}
					}
				}

				// Tax Query
				$tax_query = array();
				global $wp_query;
				// Merge Original Taxonomies from Query
				if($this->get_option('useDefaultQuery')) {
					$wp_query->tax_query;
					if(isset($wp_query->tax_query) && isset($wp_query->tax_query->queries)) {
						$original_tax_queries = $wp_query->tax_query->queries;
						foreach ($original_tax_queries as $key => $original_tax_query) {
							if(isset($original_tax_query['taxonomy']) && ($original_tax_query['taxonomy'] == "product_cat")) {
								unset($original_tax_queries[$key]);
							}
						}

					}
					$tax_query = $original_tax_queries;
				}

				// Set custom product_cat query
				$tax_query_custom = array(
			      'taxonomy' => 'product_cat',
			      'field' => 'id',
			      'terms' => $category_id
			    );
				$tax_query[] = $tax_query_custom;

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
					'order' => $order,
					'orderby' => $orderBy,
					'tax_query' => $tax_query,
					'meta_query' => $meta_query,
					'meta_key' => $orderByMetaKey,
				);

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

				if(empty($products)) {
					$loop++;
					continue;
				}

				if($this->get_option('coverShowForCategories')) {
					if($this->current_category_id == 'full') {
						$html .= $this->get_category_cover($category_id);
					} elseif($loop !== 1) {
						$html .= $this->get_category_cover($category_id);
					}
				}

			    $thumbnail_id = get_term_meta( $category_id, 'thumbnail_id', true );
			    $image = wp_get_attachment_url( $thumbnail_id );
			    if ( $image ) {
				    $this->category->image = $image;
				}

				$globalImage = $this->get_option('categoryGlobalImage');

				if(isset($globalImage['url']) && !empty($globalImage['url'])) {
					$this->category->image = $globalImage['url'];
				}

			  	if($this->get_option('enableCategory')) {
				  	// Category
				  	$categoryLayout = $this->get_option('categoryLayout');
					$categoryLayout = apply_filters('woocommerce_pdf_catalog_category_layout', $categoryLayout, $this->category->term_id);
					
					$categoryTemplate = new WooCommerce_PDF_Catalog_Category_Templates($this->plugin_name, $this->version, $this->options);
					$categoryTemplate->set_category($this->category);
					$html .= $categoryTemplate->get_template($categoryLayout);
				}

				$exclude_parent_category_products = array();
				if($this->get_option('excludeParentCategoryProducts')) {
					if($this->get_not_in($category_id)) {
						$loop++;
						continue;
					};
				}

				if(!empty($exclude_product_categories_products)){
					if(in_array($category_id, $exclude_product_categories_products))
					{
						$loop++;
						continue;
					}
				}

				// Products
				if($this->get_option('enableTextBeforeProducts'))
				{
					$html .= $this->get_text_before_products();
				}

				$productsLayout = $this->get_option('productsLayout');
				$productsLayout = apply_filters('woocommerce_pdf_catalog_products_layout', $productsLayout, $this->category->term_id);

				$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options);
				$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates);
				$productsTemplate->set_products($products);
				$html .= $productsTemplate->get_template($productsLayout);

				if($this->get_option('enableTextAfterProducts')) {
					$html .= $this->get_text_after_products();
				}

				if($loop != $categories_count) {
					if($this->get_option('enableCategory')) {
						$html .= $this->get_pagebreak();
					}
				}

				$loop++;
			}
		} else {
			$cart = WC()->cart->get_cart();
			
			if(!empty($cart)) {
				$products = array();
				foreach ($cart as $cart_item) {	
					$products[] = get_post($cart_item['product_id']);
				}

				if($this->get_option('enableTextBeforeProducts'))
				{
					$html .= $this->get_text_before_products();
				}

				$productsLayout = $this->get_option('productsLayout');

				$productTemplates = new WooCommerce_PDF_Catalog_Product_Templates($this->plugin_name, $this->version, $this->options);
				$productsTemplate = new WooCommerce_PDF_Catalog_Products_Templates($this->plugin_name, $this->version, $this->options, $productTemplates);
				$productsTemplate->set_products($products);
				$html .= $productsTemplate->get_template($productsLayout);

				if($this->get_option('enableTextAfterProducts')) {
					$html .= $this->get_text_after_products();
				}
			} else {
				wp_die('Empty Cart');
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
		$this->mpdf->WriteHTML($css.$html);

		if( ($enableCache === "1") && !$checkFileExists) {
			$this->mpdf->Output($cachedFile, 'F');

			header("Content-type:application/pdf");
			// header("Content-Disposition:attachment;filename='" . $filename . ".pdf'");
			readfile($cachedFile);
		} else {
			$this->mpdf->Output($filename.'.pdf', 'I');	
		}
		exit;
    }

    protected function get_mpdf()
    {
    	if(!class_exists('\Mpdf\Mpdf')) return FALSE;

    	$headerTopMargin = $this->get_option('headerTopMargin');
    	$footerTopMargin = $this->get_option('footerTopMargin');

    	$format = $this->get_option('format') ? $this->get_option('format') : 'A4' ;
    	$orientation = $this->get_option('orientation') ? $this->get_option('orientation') : 'P';

    	$fontFamily = $this->get_option('fontFamily') ? $this->get_option('fontFamily') : 'dejavusans';

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
			'tempDir' => dirname( __FILE__ ) . '/../../cache/',
			'fontDir' => dirname( __FILE__ ) . '/../../vendor/mpdf/mpdf/ttfonts/',
		);
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
    	$headerPadding = $this->get_option('headerPadding');
    	$footerPadding = $this->get_option('footerPadding');

    	$backgroundColor = $this->get_option('backgroundColor');

    	// Text
    	$textAlign = $this->get_option('textAlign') ? $this->get_option('textAlign') : 'center';
    	$textColor = $this->get_option('textColor');
    	$linkColor = $this->get_option('linkColor');

    	// Font
    	$fontFamily = $this->get_option('fontFamily') ? $this->get_option('fontFamily') : 'dejavusans';

    	$fontSize = $this->get_option('fontSize') ? $this->get_option('fontSize') : '11';
    	$fontSize = intval($fontSize);

    	$fontLineHeight =  $this->get_option('fontLineHeight') ? $this->get_option('fontLineHeight') : $fontSize + 6; 
    	$fontLineHeight = intval($fontLineHeight);

    	// ToC
    	$ToCPadding = $this->get_option('ToCPadding');
    	$ToCFontFamily = $this->get_option('ToCFontFamily') ? $this->get_option('ToCFontFamily') : 'dejavusans';
    	$ToCFontSize = $this->get_option('ToCFontSize') ? $this->get_option('ToCFontSize') : '13pt';
    	$ToCLineHeight = $this->get_option('ToCLineHeight') ? $this->get_option('ToCLineHeight') : '16pt';

    	// Index
		$indexPadding = $this->get_option('indexPadding');
		$indexFontFamily = $this->get_option('indexFontFamily');
		$indexFontSize = $this->get_option('indexFontSize');
		$indexLineHeight = $this->get_option('indexLineHeight');

		// Category
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

		$css = '
		<head>
			<style media="all">';

		if(!empty($backgroundColor)) {
			$css .= 'body { background-color: ' . $backgroundColor . ';}';
		}
		if(!empty($textColor)) {
			$css .= 'body { color: ' . $textColor . ';}';
		}
		if(!empty($linkColor)) {
			$css .= 'a, a:hover { color: ' . $linkColor . ';}';
		}

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

		$css .= '
				body, table { font-family: ' . $fontFamily . ', sans-serif; font-size: ' . $fontSize . 'pt; line-height: ' . $fontLineHeight . 'pt; text-align: left; } 
				p { margin-bottom: 10px; }
				.header { 
					padding-top: ' . $headerPadding['padding-top'] . '; 
					padding-right: ' . $headerPadding['padding-right'] . '; 
					padding-bottom: ' . $headerPadding['padding-bottom'] . '; 
					padding-left: ' . $headerPadding['padding-left'] . '; 
				}
				.footer { 
					padding-top: ' . $footerPadding['padding-top'] . '; 
					padding-right: ' . $footerPadding['padding-right'] . '; 
					padding-bottom: ' . $footerPadding['padding-bottom'] . '; 
					padding-left: ' . $footerPadding['padding-left'] . '; 
				}
				.toc_before { 
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
				}
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
				.category-container {
					text-align: ' . $categoryTextAlign . ';
					line-height: ' . $categoryLineHeight . 'px;
					padding: ' . $categoryPadding . ';
					color: ' . $categoryTextColor . ';
				}
				.category-information-container {
					padding: ' . $categoryInformationPadding .  ';
					vertical-align: ' . $categoryTextVAlign .  ';
				}
				.category-title {
					font-family: ' . $categoryHeadingFontFamily . ', sans-serif;
					font-size: ' . $categoryHeadingFontSize . 'px;
				}
				.category-description {
					font-size: ' . $categoryTextFontSize . 'px;
				}
				.product-title, .variations-title {
					font-size: ' . $productsHeadingsFontSize . 'px; 
					font-family: ' . $productsHeadingsFontFamily . '; 
					line-height: ' . $productsHeadingsLineHeight . 'px;"
				}
				.products-container {
					padding: ' . $productContainerPadding .  ' ;
					border-top: ' . $productsTopBorder . ';
					border-right: ' . $productsRightBorder . ';
					border-bottom: ' . $productsBottomBorder . ';
					border-left: ' . $productsLeftBorder . ';
				}
				.product-container {
					background-color: ' . $productsBackgroundColor . '; 
					text-align: ' . $productsTextAlign . '; 
					color: ' . $productsTextColor . ';
					font-size: ' . $productsFontSize . 'px; 
					line-height: ' . $productsLineHeight . 'px;"
					width: 100%;
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
				div.mpdf_index_letter {
					font-size: ' . ( $indexFontSize + 4 )  . 'pt;
				}
				.firstlayout, { width:100%; }
				.firstlayout td { vertical-align: top; }
				h1,h2,h3,h4,h5,h6 { font-family: ' . $productsHeadingsFontFamily . ', sans-serif;}
				h1 { font-size: ' . $productsHeadingsFontSize . 'pt; text-transform: uppercase; line-height: ' . $productsHeadingsLineHeight . 'pt;}
				h2 { font-size: ' . $productsHeadingsFontSize . 'pt; text-transform: uppercase; line-height: ' . $productsHeadingsLineHeight . 'pt;}
				.attributes { width: 100%; font-size: 9pt; line-height: 10pt; margin-bottom: 5px; }
				.attributes th { width:33%; text-align:left; padding-top:2px; padding-bottom: 2px;}
				.attributes td { width:66%; text-align:left; }
				.meta { font-size: 10pt; }
				.title { width: 100%; }
				.title td { padding-bottom: 10px; padding-top: 40px; }
				.clear { float: none; clear: both;}
				.fl { float: left; }
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

				$count = array_count_values($dataToShow);
				$divWidth = (90 / $count[1]) - 2;
				$css .= '.col-width { width: ' . $divWidth . '%; padding-right: 25px; }
						.product-header { font-weight: bold; padding-top: 2px; padding-bottom: 5px; }
						.product-container { padding-top: 2px; padding-bottom: 2px; }
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
    	$headerBackgroundColor = $this->get_option('headerBackgroundColor');
    	$headerTextColor = $this->get_option('headerTextColor');
    	$headerLayout = $this->get_option('headerLayout');
    	$this->get_option('headerHeight') ? $headerHeight = $this->get_option('headerHeight') : $headerHeight = 'auto';
		$headerVAlign = $this->get_option('headerVAlign');

    	$topLeft = $this->get_option('headerTopLeft');
    	$topMiddle = $this->get_option('headerTopMiddle');
    	$topRight = $this->get_option('headerTopRight');

    	if($headerLayout == "oneCol")
    	{
			$header = '
			<table class="header one-col" width="100%" style="vertical-align: bottom; font-size: 9pt; background-color: ' . $headerBackgroundColor . '; color: ' . $headerTextColor . ';">
				<tr>
					<td  class="header-block-one" height="' . $headerHeight . '" valign="' . $headerVAlign . '" width="100%" style="text-align: center;">' . $this->get_header_footer_type($topLeft, 'headerTopLeft') . '</td>
				</tr>
			</table>';
    	} elseif($headerLayout == "threeCols") {
			$header = '
			<table class="header three-cols" width="100%" style="vertical-align: bottom; font-size: 9pt; background-color: ' . $headerBackgroundColor . '; color: ' . $headerTextColor . ';">
				<tr>
					<td  class="header-block-one" height="' . $headerHeight . '" valign="' . $headerVAlign . '" width="33%" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'headerTopLeft') . '</td>
					<td  class="header-block-two" height="' . $headerHeight . '" valign="' . $headerVAlign . '" width="33%" style="text-align: center;">' . $this->get_header_footer_type($topMiddle, 'headerTopMiddle') . '</td>
					<td  class="header-block-three" height="' . $headerHeight . '" valign="' . $headerVAlign . '" width="33%" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'headerTopRight') . '</td>
				</tr>
			</table>';
		} else {
			$header = '
			<table class="header two-cols" width="100%" style="vertical-align: bottom; font-size: 9pt; background-color: ' . $headerBackgroundColor . '; color: ' . $headerTextColor . ';">
				<tr>
					<td  class="header-block-one" height="' . $headerHeight . '" valign="' . $headerVAlign . '" width="50%" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'headerTopLeft') . '</td>
					<td  class="header-block-two" height="' . $headerHeight . '" valign="' . $headerVAlign . '" width="50%" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'headerTopRight') . '</td>
				</tr>
			</table>';
		}

		return $header;
    }

    public function get_footer()
    {
    	$footerBackgroundColor = $this->get_option('footerBackgroundColor');
    	$footerTextColor = $this->get_option('footerTextColor');
    	$footerLayout = $this->get_option('footerLayout');
    	$this->get_option('footerHeight') ? $footerHeight = $this->get_option('footerHeight') : $footerHeight = 'auto';
		$footerVAlign = $this->get_option('footerVAlign');

    	$topLeft = $this->get_option('footerTopLeft');
    	$topRight = $this->get_option('footerTopRight');
    	$topMiddle = $this->get_option('footerTopMiddle');

    	if($footerLayout == "oneCol")
    	{
			$footer = '
			<table class="footer one-col" width="100%" style="vertical-align: bottom; font-size: 9pt; background-color: ' . $footerBackgroundColor . '; color: ' . $footerTextColor . ';">
				<tr>
					<td class="footer-block-one" height="' . $footerHeight . '" valign="' . $footerVAlign . '" width="100%" style="text-align: center;">' . $this->get_header_footer_type($topLeft, 'footerTopLeft') . '</span></td>
				</tr>
			</table>';
    	} elseif($footerLayout == "threeCols") {
			$footer = '
			<table class="footer three-cols" width="100%" style="vertical-align: bottom; font-size: 9pt; background-color: ' . $footerBackgroundColor . '; color: ' . $footerTextColor . ';">
				<tr>
					<td class="footer-block-one" height="' . $footerHeight . '" valign="' . $footerVAlign . '" width="33%" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'footerTopLeft') . '</span></td>
					<td class="footer-block-two" height="' . $footerHeight . '" valign="' . $footerVAlign . '" width="33%" style="text-align: center;">'. $this->get_header_footer_type($topMiddle, 'footerTopMiddle') . '</td>
					<td class="footer-block-trhee" height="' . $footerHeight . '" valign="' . $footerVAlign . '" width="33%" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'footerTopRight') . '</td>
				</tr>
			</table>';
		} else {
			$footer = '
			<table class="footer two-cols" width="100%" style="vertical-align: bottom; font-size: 9pt; background-color: ' . $footerBackgroundColor . '; color: ' . $footerTextColor . ';">
				<tr>
					<td class="footer-block-one" height="' . $footerHeight . '" valign="' . $footerVAlign . '" width="50%" style="text-align: left;">' . $this->get_header_footer_type($topLeft, 'footerTopLeft') . '</span></td>
					<td class="footer-block-two" height="' . $footerHeight . '" valign="' . $footerVAlign . '" width="50%" style="text-align: right;">' . $this->get_header_footer_type($topRight, 'footerTopRight') . '</td>
				</tr>
			</table>';
		}

		return $footer;
    }

    private function get_header_footer_type($type, $position)
    {

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
    			return date('d.m.y');
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
    			return '';
    			break;
    	}
    }

    protected function get_categories()
    {
		$categories = array();
		if($this->current_category_id == 'full'){
			$taxonomy     = 'product_cat';
			$orderby      = 'menu_order';

			if($this->get_option('orderCategories')) {
				$orderKey = $this->get_option('orderCategoriesKey');
				if(!empty($orderKey)) {
					$orderby = $orderKey;
				}
			}

			$args = array(
			     'taxonomy'     => $taxonomy,
			     'orderby'      => $orderby,
			     'parent' 		=> 0
			);

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
		$productContainerPadding = $this->get_option('productContainerPadding');
    	$productsContainerPadding = $productContainerPadding['padding-top'] . ' ' . $productContainerPadding['padding-right'] . ' 0px '  . $productContainerPadding['padding-right'];

		$textBeforeProducts = $this->get_option('textBeforeProducts');
    	$textBeforeProductsFontSize = $this->get_option('textBeforeProductsFontSize');
    	$textBeforeProductsLineHeight = $this->get_option('textBeforeProductsLineHeight');
    	$textBeforeProductsTextAlign = $this->get_option('textBeforeProductsTextAlign');

		$html = '
		<div class="container text-before" width="100%" style="vertical-align: middle; 
		font-size: ' . $textBeforeProductsFontSize . 'px; 
		line-height: ' . $textBeforeProductsLineHeight . 'px;
		text-align: ' . $textBeforeProductsTextAlign . ';">
				<div width="100%" style="padding: ' . $productsContainerPadding .  ';">' . wpautop($textBeforeProducts). '</div>
		</div>';

		return $html;
	}

	public function get_text_after_products()
	{
		$productContainerPadding = $this->get_option('productContainerPadding');
		// $productContainerPadding['padding-top']
    	$productsContainerPadding =  '0px ' . $productContainerPadding['padding-right'] . ' ' . $productContainerPadding['padding-bottom'] . ' ' . $productContainerPadding['padding-right'];

		$textAfterProducts = $this->get_option('textAfterProducts');
    	$textAfterProductsFontSize = $this->get_option('textAfterProductsFontSize');
    	$textAfterProductsLineHeight = $this->get_option('textAfterProductsLineHeight');
    	$textAfterProductsTextAlign = $this->get_option('textAfterProductsTextAlign');

		$html = '
		<div class="container text-after" width="100%" style="vertical-align: middle; 
		font-size: ' . $textAfterProductsFontSize . 'px; 
		line-height: ' . $textAfterProductsLineHeight . 'px;
		text-align: ' . $textAfterProductsTextAlign . ';">
			<div width="100%" style="padding: ' . $productsContainerPadding .  ';">' . wpautop($textAfterProducts). '</div>
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

        $customCoverImage = get_term_meta($this->current_category_id, 'woocommerce_pdf_catalog_cover_image');
        if(!empty($customCoverImage)) {
        	if(isset($customCoverImage[0]['url'])) {
	        	$coverImage = $customCoverImage[0];
        	}
        }

    	if(!$coverImage) {
    		return $html;
    	} 
    	$imageURL = $coverImage['url'];

   		
    	$html .= '<style media="all">@page :first { background: url("' . $imageURL . '"); background-repeat: none; background-image-resize: 6; }</style>';
    	$html .= '<pagebreak odd-footer-name="defaultFooter" odd-footer-value="on" odd-header-name="defaultHeader" odd-header-value="on"></pagebreak>';
    	
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
   
    	$html .= '<div style="background: url(' . $imageURL . '); background-repeat: none; background-image-resize: 6; width:100%; height:100%; margin-top: -50px;"></div>';
    	
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
    		$additionalSettings .= ' toc-odd-header-value="-1"';
    	}

    	$removeFooter = $this->get_option('ToCRemoveFooter');
    	if($removeFooter) {
    		$additionalSettings .= ' toc-odd-footer-value="-1"';
    	}

    	$resetPageNumber = $this->get_option('ToCResetPageNumber');
    	if($resetPageNumber) {
    		$additionalSettings .= ' toc-resetpagenum="1"';
    	}

	    $html = '<tocpagebreak paging="' . $paging . '" ' . $additionalSettings . ' links="' . $linking . '" toc-preHTML="' . htmlspecialchars( $textBefore, ENT_QUOTES) . '" toc-postHTML="' . htmlspecialchars( $textAfter, ENT_QUOTES) . '" />';

	    return $html;
    }

    public function index()
    {

		$letters = $this->get_option('indexLetters');
		$linking = $this->get_option('indexLinking');
		$textBefore = $this->get_option('indexTextBefore');
		$textAfter = $this->get_option('indexTextAfter');

		$html = '<div class="index_before">';
		$html .= $textBefore;
		$html .= '</div>';

		$html .= '<indexinsert usedivletters="' . $letters . '" links="' . $linking . '" />';

		$html .= '<div class="index_after">';
		$html .= $textAfter;
		$html .= '</div>';

		return $html;

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
}