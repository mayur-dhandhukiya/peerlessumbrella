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
class WooCommerce_PDF_Catalog_Products_Templates extends WooCommerce_PDF_Catalog {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
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
	 * The products
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $products    The current products of this plugin.
	 */
	private $products;
	private $product_templates;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options, $product_templates ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
		$this->product_templates = $product_templates;

		$this->defaults = array();
	}	

	/**
	 * Set products array
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @param   [type]                       $products [description]
	 */
    public function set_products($products)
    {
    	$this->products = $products;
    }

    /**
     * Get Products Template
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @param   [type]                       $template [description]
     * @return  [type]                                 [description]
     */
    public function get_template($template)
    {
    	$html = '';

		if($template == 1)
			$html .= $this->get_first_products_layout();

		if($template == 2)
			$html .= $this->get_second_products_layout();

		if($template == 3)
			$html .= $this->get_third_products_layout();

		if($template == 4)
			$html .= $this->get_fourth_products_layout();

		if($template == 5)
			$html .= $this->get_fifth_products_layout();

		if($template == 6)
			$html .= $this->get_sixth_products_layout();

		if($template == 7)
			$html .= $this->get_seventh_products_layout();

		if($template == 8)
			$html .= $this->get_eighth_products_layout();

		return $html;
    }

    /**
     * Left / Right Image Swap
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @return  [type]                       [description]
     */
    public function get_first_products_layout()
    {

    	$html = '<table class="container first-layout" style="width: 100%;">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {
			if ( $loop === 0 || $loop % $columns === 0 )
			{
				$html .= '<tr>';
			}

			$html .= '<td class="products-container" width="100%">';
			$this->product_templates->set_post($product);
			if($loop % 2 == 0) {
				$html .= $this->product_templates->get_first_product_layout();
			} else {
				$html .= $this->product_templates->get_first_product_layout(true);
			}
    		
    		$html .= '</td>';

			if ( ( $loop + 1 ) % $columns === 0 )
			{
				$html .= '</tr>';
			}
			$loop++;
    	}
    	$html .= '</table>';

		return $html;
	}

	/**
	 * Left Image
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_second_products_layout()
    {
    	$html = '<table class="container second-layout" style="width: 100%;">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {
			if ( $loop === 0 || $loop % $columns === 0 )
			{
				$html .= '<tr>';
			}

			
			$html .= '<td class="products-container" width="100%">';
			$this->product_templates->set_post($product);
    		$html .= $this->product_templates->get_first_product_layout();
    		$html .= '</td>';

			if ( ( $loop + 1 ) % $columns === 0 )
			{
				$html .= '</tr>';
			}
			$loop++;
    	}
    	$html .= '</table>';

		return $html;
	}

	/**
	 * 2 Colum left image, right text
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_third_products_layout()
    {
    	$html = '<table class="container third-layout" style="width: 100%;">';
		$loop 		= 0;
		$columns 	= 2;
		$loopFinished = false;

    	foreach ($this->products as $product) {
			if ( $loop === 0 || $loop % $columns === 0 )
			{
				$html .= '<tr>';
			}

			$html .= '<td class="products-container" width="50%">';
			$this->product_templates->set_post($product);
    		$html .= $this->product_templates->get_first_product_layout();
    		$html .= '</td>';

			if ( ( $loop + 1 ) % $columns === 0 )
			{
				$loopFinished = true;
				$html .= '</tr>';
			}
			$loop++;
    	}

    	if(!$loopFinished) {
	    	for ($i=0; $i < $columns; $i++) { 
		    	if($loop % $columns !== 0) {
		    		$html .= '<td class="products-container" width="50%">' . $this->product_templates->get_placeholder() . '</td>';
		    	} else {
		    		$html .= '</tr>';
		    		break;
		    	}
		    	$loop++;
	    	}
    	}

    	$html .= '</table>';

		return $html;
	}

	/**
	 * 2 Columnd Image text below
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_fourth_products_layout()
    {
    	$html = '<table class="container fourth-layout" style="width: 100%;">';
		$loop 		= 0;
		$columns 	= 2;
		$loopFinished = false;

    	foreach ($this->products as $product) {
			if ( $loop === 0 || $loop % $columns === 0 )
			{
				$html .= '<tr>';
			}

			$html .= '<td class="products-container" width="50%">';
			$this->product_templates->set_post($product);
    		$html .= $this->product_templates->get_second_product_layout();
    		$html .= '</td>';

			if ( ( $loop + 1 ) % $columns === 0 )
			{
				$loopFinished = true;
				$html .= '</tr>';
			}
			$loop++;
    	}

    	if(!$loopFinished) {
	    	for ($i=0; $i < $columns; $i++) { 
		    	if($loop % $columns !== 0) {
		    		$html .= '<td class="products-container" width="50%">' . $this->product_templates->get_placeholder() . '</td>';
		    	} else {
		    		$html .= '</tr>';
		    		break;
		    	}
		    	$loop++;
	    	}
    	}

    	$html .= '</table>';

		return $html;
	}

	/**
	 * 3 Columns image text below
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_fifth_products_layout()
    {
    	$html = '<table class="container fifth-layout" style="width: 100%;">';
		$loop 		= 0;
		$columns 	= 3;
		$loopFinished = false;

    	foreach ($this->products as $product) {
			if ( $loop === 0 || $loop % $columns === 0 )
			{
				$html .= '<tr>';
			}

			$html .= '<td class="products-container" width="33%">';
			$this->product_templates->set_post($product);
    		$html .= $this->product_templates->get_second_product_layout();
    		$html .= '</td>';

			if ( ( $loop + 1 ) % $columns === 0 )
			{
				$loopFinished = true;
				$html .= '</tr>';
			}
			$loop++;
    	}

    	if(!$loopFinished) {
	    	for ($i=0; $i < $columns; $i++) { 
		    	if($loop % $columns !== 0) {
		    		$html .= '<td class="products-container" width="33%">' . $this->product_templates->get_placeholder() . '</td>';
		    	} else {
		    		$html .= '</tr>';
		    		break;
		    	}
		    	$loop++;
	    	}
    	}

    	$html .= '</table>';

		return $html;
	}

	/**
	 * 4 Columns image text below
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_sixth_products_layout()
    {

    	$html = '<table class="container sixth-layout" style="width: 100%;">';
		$loop 		= 0;
		$columns 	= 4;
		$loopFinished = false;

    	foreach ($this->products as $product) {

			if ( $loop === 0 || $loop % $columns === 0 )
			{
				$html .= '<tr>';
			}

			$html .= '<td class="products-container" width="25%">';
			$this->product_templates->set_post($product);
    		$html .= $this->product_templates->get_second_product_layout();
    		$html .= '</td>';

			if ( ( $loop + 1 ) % $columns === 0 )
			{
				$loopFinished = true;
				$html .= '</tr>';
			}
			$loop++;
    	}

    	if(!$loopFinished) {
	    	for ($i=0; $i < $columns; $i++) { 
		    	if($loop % $columns !== 0) {
		    		$html .= '<td class="products-container" width="25%">' . $this->product_templates->get_placeholder() . '</td>';
		    	} else {
		    		$html .= '</tr>';
		    		break;
		    	}
		    	$loop++;
	    	}
    	}
    	
    	$html .= '</table>';
		return $html;
	}

	/**
	 * 2 Columns image text below
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_seventh_products_layout()
    {

    	$html = '<table class="container sixth-layout" style="width: 100%;">';

    	foreach ($this->products as $product) {

			$html .= '<tr>';
				$html .= '<td class="products-container" width="100%">';
					$this->product_templates->set_post($product);
		    		$html .= $this->product_templates->get_second_product_layout();
	    		$html .= '</td>';
			$html .= '</tr>';
    	}
    	
    	$html .= '</table>';
		return $html;
	}

	/**
	 * List Layout
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
 	public function get_eighth_products_layout()
    {
  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showPrice = $this->get_option('showPrice');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showCategories = $this->get_option('showCategories');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

    	$html = '<div class="container third-layout" style="width: 100%;">';
			$html .= '<div class="products-container" width="100%">';
				$html .= '<div class="product-header" width="100%">';
					if($showImage) { 
						$html .= '<div class="fl col-width col-image">' . __( 'Image', 'woocommerce') . '</div>';
					}
					if($showSKU) {
						$html .= '<div class="fl col-width col-sku">' . __( 'SKU', 'woocommerce') . '</div>';
					}
					if($showTitle) {
						$html .= '<div class="fl col-width col-title">' . __( 'Product', 'woocommerce') . '</div>';
					}
					if($showShortDescription) {
						$html .= '<div class="fl col-width col-short-description">' . __( 'Short Description', 'woocommerce') . '</div>';
					}
					if($showDescription) {
						$html .= '<div class="fl col-width col-description">' . __( 'Description', 'woocommerce') . '</div>';
					}
					if($showAttributes) {
						$html .= '<div class="fl col-width col-attributes">' . __( 'Attributes', 'woocommerce') . '</div>';
					}
					if($showPrice) {
						$html .= '<div class="fl col-width col-price">' . __( 'Price', 'woocommerce') . '</div>';
					}
					if($showReadMore) {
						$html .= '<div class="fl col-width col-readmore">' . __( 'Read More', 'woocommerce') . '</div>';
					}
					if($showCategories) {
						$html .= '<div class="fl col-width col-categories">' . __( 'Categories', 'woocommerce') . '</div>';
					}
					if($showTags) {
						$html .= '<div class="fl col-width col-tags">' . __( 'Tags', 'woocommerce') . '</div>';
					}
					if($showQR) {
						$html .= '<div class="fl col-width col-qr">' . __( 'QR', 'woocommerce') . '</div>';
					}
					if($showBarcode) {
						$html .= '<div class="fl col-width col-barcode">' . __( 'Barcode', 'woocommerce') . '</div>';
					}
				$html .= '</div>';	

				$i = 0;
		    	foreach ($this->products as $product) {
	    			$i++;
		    		$html .= '<div class="product-container clear ' . ($i%2 ? 'odd':'even') . '" width="100%">';
						$this->product_templates->set_post($product);
		    			$html .= $this->product_templates->get_third_product_layout();
	    			$html .= '</div>';
			
				}
				
			$html .= '</div>';	
		$html .= '</div>';    	

		return $html;
	}

    public function _get_eighth_products_layout()
    {
  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showPrice = $this->get_option('showPrice');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showCategories = $this->get_option('showCategories');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');

    	$html = '<table class="container second-layout" style="width: 100%;">';
			$html .= '<tr>';
				$html .= '<td class="products-container" width="100%">';
					$html .= '<table width="100%">';
			
						$html .= '<tr>';

							if($showImage) { 
								$html .= '<td>' . __( 'Image', 'woocommerce') . '</td>';
							}
							if($showSKU) {
								$html .= '<td>' . __( 'SKU', 'woocommerce') . '</td>';
							}
							if($showTitle) {
								$html .= '<td>' . __( 'Product', 'woocommerce') . '</td>';
							}
							if($showShortDescription) {
								$html .= '<td>' . __( 'Short Description', 'woocommerce') . '</td>';
							}
							if($showDescription) {
								$html .= '<td>' . __( 'Description', 'woocommerce') . '</td>';
							}
							if($showAttributes) {
								$html .= '<td>' . __( 'Attributes', 'woocommerce') . '</td>';
							}
							if($showPrice) {
								$html .= '<td>' . __( 'Price', 'woocommerce') . '</td>';
							}
							if($showReadMore) {
								$html .= '<td>' . __( 'Read More', 'woocommerce') . '</td>';
							}
							if($showCategories) {
								$html .= '<td>' . __( 'Categories', 'woocommerce') . '</td>';
							}
							if($showTags) {
								$html .= '<td>' . __( 'Tags', 'woocommerce') . '</td>';
							}
							if($showQR) {
								$html .= '<td>' . __( 'QR', 'woocommerce') . '</td>';
							}
						$html .= '</tr>';	
					$html .= '</table>';	

				$html .= '</td>';			    		
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td class="products-container" width="100%">';

				    	foreach ($this->products as $product) {
						$html .= '<tr>';
							$html .= '<td class="products-container" width="100%">';
								$this->product_templates->set_post($product);
				    			$html .= $this->product_templates->get_third_product_layout();

							$html .= '</td>';			    		
						$html .= '</tr>';
						}
					
				$html .= '</td>';	
			$html .= '</tr>';	
		$html .= '</table>';    	

		return $html;
	}
}