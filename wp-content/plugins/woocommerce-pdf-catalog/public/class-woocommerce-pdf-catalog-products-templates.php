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
	public $mpdf;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options, $product_templates, $mpdf, $level = 0, $category_id = 0) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
		$this->product_templates = $product_templates;
		$this->mpdf = $mpdf;

		if($this->get_option('ToCShowCategories')) {
			$level += 1;
		}

		$this->excludeCategories = $this->get_option('excludeProductsWithCategories') ? $this->get_option('excludeProductsWithCategories') : array();

		$this->level = $level;
		$this->category_id = $category_id;

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
    public function get_template($template, $write = false)
    {
    	$html = '';

    	$this->pagebreak = $this->get_option('productPagebreak');

		if(!empty($this->category_id)) {
			$customLayout = get_term_meta( $this->category_id, 'woocommerce_pdf_catalog_products_layout', true );
			if(!empty($customLayout)) {
				$template = $customLayout;
			}
		}

		if($template == 1) {
			$html .= $this->get_first_products_layout($write);
		} elseif($template == 2) {
			$html .= $this->get_second_products_layout($write);
		} elseif($template == 3) {
			$html .= $this->get_third_products_layout($write);
		} elseif($template == 4) {
			$html .= $this->get_fourth_products_layout($write);
		} elseif($template == 5) {
			$html .= $this->get_fifth_products_layout($write);
		} elseif($template == 6) {
			$html .= $this->get_sixth_products_layout($write);
		} elseif($template == 7) {
			$html .= $this->get_seventh_products_layout($write);
		} elseif($template == 8) {
			$html .= $this->get_eighth_products_layout($write);
		} elseif($template == 9) {
			$html .= $this->get_ninth_products_layout($write);
		} elseif($template == 11) {
			$html .= $this->get_eleventh_products_layout($write);
		} elseif($template == 12) {
			$html .= $this->get_twelfth_products_layout($write);
		} elseif($template == 13) {
			$html .= $this->get_thirteen_products_layout($write);
		} elseif($template == 14) {
			$html .= $this->get_fourteen_products_layout($write);
		}


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
    public function get_first_products_layout($write = false)
    {

    	$html = '<div class="container first-layout">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

			$html .= '<div class="products-container">';

	    		if($this->get_option('ToCShowProducts')) {
	    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
	    		}
				
			
				if($loop % 2 == 0) {
					$html .= $this->product_templates->get_first_product_layout();
				} else {
					$html .= $this->product_templates->get_first_product_layout(true);
				}
    	
    		$html .= '</div>';

    		if($this->pagebreak) {
    			$html .= '<pagebreak/>';
    		}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	$html .= '</div>';


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
    public function get_second_products_layout($write)
    {
    	$html = '<div class="container second-layout">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}
			
			$html .= '<div class="products-container">';
    		$html .= $this->product_templates->get_first_product_layout();
    		$html .= '</div>';

    		if($this->pagebreak) {
    			$html .= '<pagebreak/>';
    		}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	$html .= '</div>';

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
    public function get_third_products_layout($write)
    {
    	$html = '<div class="container third-layout two-cols">';
		$loop 		= 0;
		$columns 	= 2;
		$loopFinished = false;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}

			$html .= '<div class="col">';
				$html .= '<div class="products-container">';
	    			$html .= $this->product_templates->get_first_product_layout();
	    		$html .= '</div>';
    		$html .= '</div>';
			if ( ( $loop + 1 ) % $columns === 0 ) {
				$html .= '<div class="clear"></div>';

	    		if($this->pagebreak) {
	    			$html .= '<pagebreak/>';
	    		}
			}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}

    	$html .= '</div>';

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
    public function get_fourth_products_layout($write)
    {
    	$html = '<div class="container fourth-layout two-cols">';
		$loop 		= 0;
		$columns 	= 2;
		$loopFinished = false;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}

    		$html .= '<div class="col">';
				$html .= '<div class="products-container">';
	    			$html .= $this->product_templates->get_second_product_layout();
    			$html .= '</div>';
    		$html .= '</div>';

			if ( ( $loop + 1 ) % $columns === 0 ) {
				$html .= '<div class="clear"></div>';

	    		if($this->pagebreak) {
	    			$html .= '<pagebreak/>';
	    		}
			}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}

    	$html .= '</div>';

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
    public function get_fifth_products_layout($write)
    {
    	$html = '<div class="container fifth-layout three-cols">';
		$loop 		= 0;
		$columns 	= 3;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}

    		$html .= '<div class="col">';
				$html .= '<div class="products-container">';
	    			$html .= $this->product_templates->get_second_product_layout();
	    		$html .= '</div>';
    		$html .= '</div>';

			if ( ( $loop + 1 ) % $columns === 0 ) {
				$html .= '<div class="clear"></div>';

	    		if($this->pagebreak) {
	    			$html .= '<pagebreak/>';
	    		}
			}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}

    	$html .= '</div>';

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
    public function get_sixth_products_layout($write)
    {

    	$html = '<div class="container sixth-layout four-cols">';
		$loop 		= 0;
		$columns 	= 4;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}

    		$html .= '<div class="col">';
				$html .= '<div class="products-container">';
		    		$html .= $this->product_templates->get_second_product_layout();
	    		$html .= '</div>';
    		$html .= '</div>';

			if ( ( $loop + 1 ) % $columns === 0 ) {
				$html .= '<div class="clear"></div>';

	    		if($this->pagebreak) {
	    			$html .= '<pagebreak/>';
	    		}
			}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	
    	$html .= '</div>';
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
    public function get_seventh_products_layout($write)
    {

    	$html = '<div class="container sixth-layout">';

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}

			$html .= '<div class="products-container">';
	    		$html .= $this->product_templates->get_second_product_layout();
    		$html .= '</div>';

    		if($this->pagebreak) {
    			$html .= '<pagebreak/>';
    		}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}
    	}
    	
    	$html .= '</div>';
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
 	public function get_eighth_products_layout($write)
    {
  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showPrice = $this->get_option('showPrice');
		$showStock = $this->get_option('showStock');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showCategories = $this->get_option('showCategories');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');
		$customMetaKeys = $this->get_option('customMetaKeys');
		if(isset($customMetaKeys['enabled'])) {
			unset($customMetaKeys['enabled']['placebo']);
		}
		if(isset($customMetaKeys['enabled']) && !empty($customMetaKeys['enabled'])) {
			$customMetaKeys = $customMetaKeys['enabled'];
		}

    	$html = '<div class="container third-layout">';
			$html .= '<div class="products-container">';
				$html .= '<div class="product-header">';
					if($showImage) { 
						$html .= '<div class="fl col-width col-image">' . __( 'Image', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showSKU) {
						$html .= '<div class="fl col-width col-sku">' . __( 'SKU', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showTitle) {
						$html .= '<div class="fl col-width col-title">' . __( 'Product', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showShortDescription) {
						$html .= '<div class="fl col-width col-short-description">' . __( 'Short Description', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showDescription) {
						$html .= '<div class="fl col-width col-description">' . __( 'Description', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showAttributes) {
						$html .= '<div class="fl col-width col-attributes">' . __( 'Attributes', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showPrice) {
						$html .= '<div class="fl col-width col-price">' . __( 'Price', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showStock) {
						$html .= '<div class="fl col-width col-stock">' . __( 'Stock', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showReadMore) {
						$html .= '<div class="fl col-width col-readmore">' . $this->get_option('showReadMoreText') . '</div>';
					}
					if($showCategories) {
						$html .= '<div class="fl col-width col-categories">' . __( 'Categories', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showTags) {
						$html .= '<div class="fl col-width col-tags">' . __( 'Tags', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showQR) {
						$html .= '<div class="fl col-width col-qr">' . __( 'QR', 'woocommerce-pdf-catalog') . '</div>';
					}
					if($showBarcode) {
						$html .= '<div class="fl col-width col-barcode">' . __( 'Barcode', 'woocommerce-pdf-catalog') . '</div>';
					}

					if(!empty($customMetaKeys) && is_array($customMetaKeys)) {
					    foreach ($customMetaKeys as $key => $meta_key) {
					    	$html .= '<div class="fl col-width col-' . $meta_key . '">' . $this->get_option('showCustomMetaKeyText_' . $meta_key) . '</div>';
				    	}
			    	}

				$html .= '</div>';	

				$i = 0;
		    	foreach ($this->products as $product) {

		    		if(!$this->product_templates->set_post($product)) {
		    			continue;
		    		}

		    		if(!empty($this->excludeCategories)) {
		    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
		    				continue;
		    			}
		    		}

		    		if($this->get_option('ToCShowProducts')) {
		    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
		    		}

	    			$i++;
		    		$html .= '<div class="product-container clear ' . ($i%2 ? 'odd':'even') . '">';
		    			$html .= $this->product_templates->get_third_product_layout();
	    			$html .= '</div>';

		    		if($write) {
		    			$this->mpdf->WriteHTML($html, 0, false, false);
		    			$html = "";
		    		}
			
				}
				
			$html .= '</div>';	
		$html .= '</div>';    	

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
 	public function get_ninth_products_layout($write)
    {
  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showPrice = $this->get_option('showPrice');
		$showStock = $this->get_option('showStock');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showCategories = $this->get_option('showCategories');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');
		$customMetaKeys = $this->get_option('customMetaKeys');
		if(isset($customMetaKeys['enabled'])) {
			unset($customMetaKeys['enabled']['placebo']);
		}

		if(isset($customMetaKeys['enabled']) && !empty($customMetaKeys['enabled'])) {
			$customMetaKeys = $customMetaKeys['enabled'];
		}

    	$html = '<div class="container third-layout">';
			$html .= '<div class="products-container">';
				
				$html .= '<div class="two-cols">';
					$html .= '<div class="col">';
						$html .= '<div class="product-header">';
							if($showImage) { 
								$html .= '<div class="fl col-width col-image">' . __( 'Image', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showSKU) {
								$html .= '<div class="fl col-width col-sku">' . __( 'SKU', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showTitle) {
								$html .= '<div class="fl col-width col-title">' . __( 'Product', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showShortDescription) {
								$html .= '<div class="fl col-width col-short-description">' . __( 'Short Description', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showDescription) {
								$html .= '<div class="fl col-width col-description">' . __( 'Description', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showAttributes) {
								$html .= '<div class="fl col-width col-attributes">' . __( 'Attributes', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showPrice) {
								$html .= '<div class="fl col-width col-price">' . __( 'Price', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showStock) {
								$html .= '<div class="fl col-width col-stock">' . __( 'Stock', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showReadMore) {
								$html .= '<div class="fl col-width col-readmore">' . $this->get_option('showReadMoreText') . '</div>';
							}
							if($showCategories) {
								$html .= '<div class="fl col-width col-categories">' . __( 'Categories', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showTags) {
								$html .= '<div class="fl col-width col-tags">' . __( 'Tags', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showQR) {
								$html .= '<div class="fl col-width col-qr">' . __( 'QR', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showBarcode) {
								$html .= '<div class="fl col-width col-barcode">' . __( 'Barcode', 'woocommerce-pdf-catalog') . '</div>';
							}
							if(!empty($customMetaKeys)) {
							    foreach ($customMetaKeys as $key => $meta_key) {
							    	$html .= '<div class="fl col-width col-' . $meta_key . '">' . $this->get_option('showCustomMetaKeyText_' . $meta_key) . '</div>';
						    	}
					    	}
				    	$html .= '</div>';	
					$html .= '</div>';	
					$html .= '<div class="col">';
						$html .= '<div class="product-header">';
							if($showImage) { 
								$html .= '<div class="fl col-width col-image">' . __( 'Image', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showSKU) {
								$html .= '<div class="fl col-width col-sku">' . __( 'SKU', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showTitle) {
								$html .= '<div class="fl col-width col-title">' . __( 'Product', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showShortDescription) {
								$html .= '<div class="fl col-width col-short-description">' . __( 'Short Description', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showDescription) {
								$html .= '<div class="fl col-width col-description">' . __( 'Description', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showAttributes) {
								$html .= '<div class="fl col-width col-attributes">' . __( 'Attributes', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showPrice) {
								$html .= '<div class="fl col-width col-price">' . __( 'Price', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showStock) {
								$html .= '<div class="fl col-width col-stock">' . __( 'Stock', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showReadMore) {
								$html .= '<div class="fl col-width col-readmore">' . $this->get_option('showReadMoreText') . '</div>';
							}
							if($showCategories) {
								$html .= '<div class="fl col-width col-categories">' . __( 'Categories', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showTags) {
								$html .= '<div class="fl col-width col-tags">' . __( 'Tags', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showQR) {
								$html .= '<div class="fl col-width col-qr">' . __( 'QR', 'woocommerce-pdf-catalog') . '</div>';
							}
							if($showBarcode) {
								$html .= '<div class="fl col-width col-barcode">' . __( 'Barcode', 'woocommerce-pdf-catalog') . '</div>';
							}
							if(!empty($customMetaKeys)) {
							    foreach ($customMetaKeys as $key => $meta_key) {
							    	$html .= '<div class="fl col-width col-' . $meta_key . '">' . $this->get_option('showCustomMetaKeyText_' . $meta_key) . '</div>';
						    	}
					    	}
						$html .= '</div>';	
					$html .= '</div>';
				$html .= '</div>';	

				$i = 1;
				$ii = 1;
				$html .= '<div class="two-cols">';
		    	foreach ($this->products as $product) {

		    		if(!$this->product_templates->set_post($product)) {
		    			continue;
		    		}

		    		if(!empty($this->excludeCategories)) {
		    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
		    				continue;
		    			}
		    		}

		    		if($this->get_option('ToCShowProducts')) {
		    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
		    		}
					
					$html .= '<div class="col">';

		    			$html .= '<div class="product-container clear ' . (($i % 2) == 0 ? 'odd':'even') . '">';

			    			$html .= $this->product_templates->get_third_product_layout();
							
						$html .= '</div>';	
					$html .= '</div>';

		    		if($write) {
		    			$this->mpdf->WriteHTML($html, 0, false, false);
		    			$html = "";
		    		}

					if(($ii % 2) == 0) {
						$i++;
					}
					$ii++;
				}
				$html .= '</div>';
			$html .= '</div>';	
		$html .= '</div>';    	

		return $html;
	}

	/**
	 * 5 Columns image text below
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_eleventh_products_layout($write)
    {

    	$html = '<div class="container sixth-layout five-cols">';
		$loop 		= 0;
		$columns 	= 5;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}

    		$html .= '<div class="col">';
				$html .= '<div class="products-container">';
		    		$html .= $this->product_templates->get_second_product_layout();
	    		$html .= '</div>';
    		$html .= '</div>';

			if ( ( $loop + 1 ) % $columns === 0 ) {
				$html .= '<div class="clear"></div>';

	    		if($this->pagebreak) {
	    			$html .= '<pagebreak/>';
	    		}
			}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	
    	$html .= '</div>';
		return $html;
	}

	/**
	 * 3 Columns layout
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_twelfth_products_layout($write)
    {
    	$html = '<div class="container twelfth-layout">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}
			
			$html .= '<div class="products-container">';
    		$html .= $this->product_templates->get_fourth_product_layout();
    		$html .= '<div class="clear"></div>';
    		$html .= '</div>';

    		if($this->pagebreak) {
    			$html .= '<pagebreak/>';
    		}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	$html .= '</div>';

		return $html;
	}

	/**
	 * 1 Columns layout
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_thirteen_products_layout($write)
    {
    	$html = '<div class="container thirteen-layout">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}
			
			$html .= '<div class="products-container">';
    		$html .= $this->product_templates->get_fifth_product_layout();
    		$html .= '<div class="clear"></div>';
    		$html .= '</div>';

    		if($this->pagebreak) {
    			$html .= '<pagebreak/>';
    		}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	$html .= '</div>';

		return $html;
	}

	/**
	 * 2 Columns layout
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_fourteen_products_layout($write)
    {
    	$html = '<div class="container fourteen-layout two-cols">';
		$loop 		= 0;
		$columns 	= 1;

    	foreach ($this->products as $product) {

    		if(!$this->product_templates->set_post($product)) {
    			continue;
    		}

    		if(!empty($this->excludeCategories)) {
    			if(has_term($this->excludeCategories, 'product_cat', $product)) {
    				continue;
    			}
    		}

    		if($this->get_option('ToCShowProducts')) {
    			$html .= '<tocentry content="' . htmlentities( $product->post_title ) . '" level="' . $this->level . '" />';
    		}
			
			$html .= '<div class="col">';
				$html .= '<div class="products-container">';
	    		$html .= $this->product_templates->get_sixth_product_layout();
	    		$html .= '<div class="clear"></div>';
	    		$html .= '</div>';
    		$html .= '</div>';

    		if($this->pagebreak) {
    			$html .= '<pagebreak/>';
    		}

    		if($write) {
    			$this->mpdf->WriteHTML($html, 0, false, false);
    			$html = "";
    		}

			$loop++;
    	}
    	$html .= '</div>';

		return $html;
	}

	
	
}