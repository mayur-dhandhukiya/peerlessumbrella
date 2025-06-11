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
class WooCommerce_PDF_Catalog_Product_Templates extends WooCommerce_PDF_Catalog {

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
	 * Product
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $this->product
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options, $filename) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
		$this->filename = $filename;
		$this->data = new stdClass;
	}

	public function set_post($post)
	{
		global $woocommerce, $product;

		$this->data->ID = $post->ID;

		if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
			$product =  wc_get_product( $this->data->ID );
		} else {
			$product =  get_product( $this->data->ID );
		}
		
		if(!$product) {
			return false;
		}

		if($this->get_option('singleVariationsSupport') && class_exists('WooCommerce_Single_Variations') && $product->is_type('variable')) {
			return false;
		}

		$this->product = $product;
		$this->post = $post;

		$price = $this->product->get_price_html();
		$price = htmlspecialchars_decode($price);
	    $price = str_replace(array('&#8381;'), 'RUB', $price);

	    $sku = $this->product->get_sku();

		// product variables
		$this->data->title = apply_filters('woocommerce_pdf_catalog_product_title', $this->get_option('showTitlePrefix') . $this->post->post_title, $post->ID);

		if($this->get_option('showTitleCartQuantity') && $_GET['pdf-catalog'] == "cart") {
			$cart = WC()->cart->get_cart();
			if(!empty($cart)) {
				foreach ( $cart as $cart_item_key => $cart_item ) {

					$cart_product_id = $cart_item['product_id'];
					if(isset($cart_item['variation_id']) && !empty($cart_item['variation_id'])) {
						$cart_product_id = $cart_item['variation_id'];
					}
					// if(isset())
					if($cart_product_id == $product->get_id() && isset($cart_item['quantity'])) {
						$this->data->title = '<span class="quantity">' . $cart_item['quantity'] . ' x </span>' . $this->data->title;
					}
				}
			}
		}

		$this->data->short_description = apply_filters('woocommerce_pdf_catalog_product_short_description', do_shortcode( $this->post->post_excerpt ), $post->ID);
		$this->data->description = apply_filters('woocommerce_pdf_catalog_product_description', do_shortcode($this->post->post_content), $post->ID);

		if(class_exists('WooCommerce_Ultimate_Tabs') && $this->get_option('showDescription') && $this->get_option('showCustomTabs')) {


			$tabs = apply_filters( 'woocommerce_product_tabs', array() );
			if(empty($tabs)) {
				return;
			}

			unset($tabs['description']);
			unset($tabs['additional_information']);
			unset($tabs['reviews']);
			
			ob_start();

			echo '<div class="custom-tabs frame">';

			foreach ($tabs as $key => $tab) {
				$heading = $tab['title'];
				echo '<div id="custom-tab-' . $key . '" class="custom-tab">';
					call_user_func( $tab['callback'], $key, $tab );
				echo '</div>';
			}
			echo '</div>';

			$this->data->description .= ob_get_clean();

		}

		$this->data->price = apply_filters('woocommerce_pdf_catalog_product_price', $price, $this->product );
		$this->data->sku = !empty($sku) ? $sku : __( 'N/A', 'woocommerce-pdf-catalog' );
		$this->data->cat_count = 0;
		$cats = get_the_terms( $this->data->ID, 'product_cat' );
		if($cats) {
			$this->data->cat_count = sizeof( get_the_terms( $this->data->ID, 'product_cat' ) );
		}

		$this->data->tag_count = 0;
		$tags = get_the_terms( $this->data->ID, 'product_tag' );
		if($tags) {
			$this->data->tag_count = sizeof( $tags );
		}
		
		if($this->get_option('showShortDescriptionStripShortcodes')) {
			$this->data->short_description = preg_replace("/\[[^\]]+\]/", '', $this->data->short_description);
		}

		if($this->get_option('showDescriptionStripShortcodes')) {
			$this->data->description = preg_replace("/\[[^\]]+\]/", '', $this->data->description);
		}

		// Create Excerpt
		if($this->get_option('showShortDescriptionExcerpt')) {
			$excerptLength = $this->get_option('showShortDescriptionExcerptLength');
			$this->data->short_description = $this->get_excerpt($this->data->short_description, 0, $excerptLength);
		}

		if($this->get_option('showDescriptionExcerpt')) {
			$excerptLength = $this->get_option('showDescriptionExcerptLength');
			$this->data->description = $this->get_excerpt($this->data->description, 0, $excerptLength);
		}

		if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
			$this->data->categories = wc_get_product_category_list($this->data->ID, ', ', '<b>' . _n( $this->get_option('showCategoriesSingleText'), $this->get_option('showCategoriesPluralText'), $this->data->cat_count, 'woocommerce-pdf-catalog' ) . '</b> ');
			$this->data->tags = wc_get_product_tag_list($this->data->ID, ', ', '<b>' . _n( $this->get_option('showTagsSingleText'), $this->get_option('showTagsPluralText'), $this->data->tag_count, 'woocommerce-pdf-catalog' ) . '</b> ');
		} else {
			$this->data->categories = $this->product->get_categories( ', ', '<b>' . _n( $this->get_option('showCategoriesSingleText'), $this->get_option('showCategoriesPluralText'), $this->data->cat_count, 'woocommerce-pdf-catalog' ) . '</b> ');
			$this->data->tags = $this->product->get_tags( ', ', '<b>' . _n( $this->get_option('showTagsSingleText'), $this->get_option('showTagsPluralText'), $this->data->tag_count, 'woocommerce-pdf-catalog' ) . '</b> ');
		}

		if($this->get_option('showStock')) {
			$this->data->stock_status = strip_tags( wc_get_stock_html( $this->product ) );
			if(empty($this->data->stock_status)) {
				$this->data->stock_status = __( 'N/A', 'woocommerce-pdf-catalog' );
			}
		}
		if ( has_post_thumbnail($this->post->ID)) { 
			$showImageSize = $this->get_option('showImageSize');
			if($this->get_option('performanceUseImageLocally')) {
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($this->post->ID), $showImageSize ); 
			    $uploads = wp_upload_dir();
				$this->data->src = str_replace( $uploads['baseurl'], $uploads['basedir'], $thumbnail[0] );
			} else {
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($this->post->ID), $showImageSize ); 
				$this->data->src = $thumbnail[0];
			}
		} else { 

			if ( $this->post->post_parent > 0 && has_post_thumbnail($this->post->post_parent)) { 
				$showImageSize = $this->get_option('showImageSize');
				if($this->get_option('performanceUseImageLocally')) {
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($this->post->post_parent), $showImageSize ); 
				    $uploads = wp_upload_dir();
					$this->data->src = str_replace( $uploads['baseurl'], $uploads['basedir'], $thumbnail[0] );
				} else {
					$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($this->post->post_parent), $showImageSize ); 
					$this->data->src = $thumbnail[0];
				}
			} else {
				$this->data->src = plugin_dir_url( __FILE__ ) . 'img/placeholder.png';
			}
		}

		$customMetaKeys = $this->get_option('customMetaKeys');
		if(isset($customMetaKeys['enabled'])) {
			unset($customMetaKeys['enabled']['placebo']);
		}

		$temp = array();
		if(isset($customMetaKeys['enabled']) && !empty($customMetaKeys['enabled'])) {
			$customMetaKeys = $customMetaKeys['enabled'];

			$temp = array();
		    foreach ($customMetaKeys as $key => $meta_key) {
		        

		        if($this->get_option('showCustomMetaKeyACF_' . $meta_key) && function_exists('get_field')) {

		        	$field_name = get_post_meta( $this->data->ID, $meta_key, true);

					$temp[] = array (
						'key' => $meta_key,
						'before' => $this->get_option('showCustomMetaKeyText_' . $meta_key),
						'value' => get_field($field_name, $this->data->ID),
					);

	        	} else {
					$temp[] = array (
						'key' => $meta_key,
						'before' => $this->get_option('showCustomMetaKeyText_' . $meta_key),
						'value' => get_post_meta( $this->data->ID, $meta_key, true),
					);
				}

		    }
	    }

	    $this->data->meta_keys = apply_filters('woocommerce_pdf_catalog_meta_keys', $temp);
		$this->data = apply_filters('woocommerce_pdf_catalog_product_data', $this->data);

		return TRUE;
	}

	public function get_custom_meta_data()
	{
		$html = "";
		if(!empty($this->data->meta_keys)) {
	 		foreach ( $this->data->meta_keys as $meta_key) {
	 			if(empty($meta_key['value'])) {
	 				continue;
	 			}

	 			$html .= '<div class="meta_key_container meta_key_container_' . $meta_key['key'] . '">';
	 				$html .= apply_filters('woocommerce_pdf_catalog_meta_output', '<b class="meta_key ' . $meta_key['key'] . '">' . $meta_key['before'] . '</b>' . $meta_key['value'], $meta_key);
	 			$html .= '</div>';
	 		}
		}

		return $html;
	}

	public function get_first_product_layout($imageRight = false)
	{
		$productContainerClass = "";
		if($this->product->is_on_sale()) {
			$productContainerClass .= 'on-sale';
		}

  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showCategoryDescription = $this->get_option('showCategoryDescription');

		$showPrice = $this->get_option('showPrice');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showSKUMoveUnderTitle = $this->get_option('showSKUMoveUnderTitle');
		$showCategories = $this->get_option('showCategories');
		$showStock = $this->get_option('showStock');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( str_replace(':', '&#58;', $this->data->$indexKey ), ENT_QUOTES) . '" />';

		$html .='<div class="product-container two-cols ' . $productContainerClass . '" width="100%">';

		$html .= apply_filters('woocommerce_pdf_catalog_before_product_container', '', $this->data->ID);

		if($imageRight == false) {
			$html .= '<div class="col product-images-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_image', '', $this->data->ID);

				if($showImage) {
					$html .= '<div class="product-image-container">';
					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
					}
					$html .= $featured_image;
					if($showLinkOnImage) { 
						$html .= '</a>';
					}
					$html .= '</div>';
				}

				if($showGalleryImages) {
					$galleryImageSize = $this->get_option('galleryImageSize');
					$galleryImageColumns = $this->get_option('galleryImageColumns');
					$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
				}
			$html .= '</div>';
		}

		$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_container', '', $this->data->ID);

		$html .= '<div class="col product-content-container">';
			$html .= '<div class="product-information-container">';
				if($showCategoryDescription) {

					if(class_exists('RankMath')) {
						$primary_cat_id = get_post_meta($this->data->ID, 'rank_math_primary_product_cat', true);
					} else {
						$primary_cat_id = get_post_meta($this->data->ID, '_yoast_wpseo_primary_product_cat', true);	
					}

					if($primary_cat_id){
						$terms = array( get_term($primary_cat_id, 'product_cat') );
					} else {
						$terms = get_the_terms( $this->data->ID, 'product_cat' );
					}

					$txt = "";
					if(!empty($terms)) {
						foreach ($terms as $term) {
							if(isset($term->description) && !empty($term->description)) {
								$html .= '<p class="product-category-description">' . $term->description . '</p>';
								break;
							}
						}
					}
				}
				if($showTitle) {
					$html .= '<h1 class="product-title">' . $this->data->title . '</h1>';
				}

				if($showSKU && $showSKUMoveUnderTitle) {
					$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce-pdf-catalog'  ). '</b> '. $this->data->sku . '</span><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information', '', $this->data->ID);

				$html .= '<div class="product-information">';

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_start', '', $this->data->ID);

					if($showShortDescription) {
						$html .= '<span class="product-short-description">' . wpautop($this->data->short_description) . '</span>';
					}

					if($showDescription) {
						$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span>';
					}

					if($showAttributes && !$this->get_option('showAttributesMoveBelowSKU')) {
						$html .= $this->get_attributes_table();
					}

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_read_more', '', $this->data->ID);

					if($showReadMore) {
						$html .= '<a class="product-read-more" href="' . $this->get_permalink($this->data->ID) . '">';
						$html .= $this->get_option('showReadMoreText');
						$html .= '</a><br>';
					}

					if($this->product->is_type( 'variable' ) && $this->get_option('showVariations') ) {
						if($this->get_option('showVariationsTitle')) {
							$html .= '<h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3>';
						}
						$html .= $this->get_variation_table();
					}

					if($showSKU && !$showSKUMoveUnderTitle) {
						$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce-pdf-catalog'  ). '</b> '. $this->data->sku . '</span><br>';
					}

					if($showPrice) {
						$html .= '<span class="product-price"><b>'.__( 'Price:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->price . '</span><br>';
					}

					if($showStock) {
						$html .= '<span class="product-stock"><b>'.__( 'Stock:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->stock_status . '</span><br>';
					}

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_categories', '', $this->data->ID);
				

					if($showCategories) {
						$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
					}
					if($showTags) {
						$html .= '<span class="product-tags">' . $this->data->tags . '</span>';
					}

					if($showAttributes && $this->get_option('showAttributesMoveBelowSKU')) {
						$html .= $this->get_attributes_table();
					}

					if($showQR) {
						$html .= '<barcode code="' . $this->get_permalink($this->data->ID) . '" type="QR" class="qr-barcode" size="' . $this->get_option('qrSize') . '" error="M" />';
					}

					if($showBarcode) {
						$barcodeType = $this->get_option('barcodeType');
						$barcodeMetaKey = $this->get_option('barcodeMetaKey');
						$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
						if(!empty($barcodeMetaValue)) {
							$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode size="' . $this->get_option('barcodeSize') . '" code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
						}
					}

					$html .= $this->get_custom_meta_data();
					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_end', '', $this->data->ID);
					
				$html .= '</div>';

				$html .= apply_filters('woocommerce_pdf_catalog_after_product_information', '', $this->data->ID);

			$html .= '</div>';
		$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_after_product_information_container', '', $this->data->ID);

		if($imageRight == true) {
			$html .= '<div class="col product-images-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_image', '', $this->data->ID);

				if($showImage) {
					$html .= '<div class="product-image-container">';
					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
					}
					$html .= $featured_image;
					if($showLinkOnImage) { 
						$html .= '</a>';
					}
					$html .= '</div>';
				}

				if($showGalleryImages) {
					$galleryImageSize = $this->get_option('galleryImageSize');
					$galleryImageColumns = $this->get_option('galleryImageColumns');
					$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
				}
			$html .= '</div>';
		}	

		$html .= apply_filters('woocommerce_pdf_catalog_after_product_container', '', $this->data->ID);

		$html .= '<div class="clear"></div>';
		$html .= '</div>';

		$html = apply_filters('woocommerce_pdf_catalog_single_product_html', $html, $this->data->ID, $this->data);
		return $html;
	}

	public function get_second_product_layout()
	{
		$productContainerClass = "";
		if($this->product->is_on_sale()) {
			$productContainerClass .= 'on-sale';
		}

  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
  		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showCategoryDescription = $this->get_option('showCategoryDescription');
		$showPrice = $this->get_option('showPrice');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showAttributes = $this->get_option('showAttributes');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showSKUMoveUnderTitle = $this->get_option('showSKUMoveUnderTitle');
		$showCategories = $this->get_option('showCategories');
		$showStock = $this->get_option('showStock');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( $this->data->$indexKey, ENT_QUOTES ) . '" />';

		$html .= '<div class="product-container ' . $productContainerClass . '" width="100%">';

			$html .= apply_filters('woocommerce_pdf_catalog_before_product_container', '', $this->data->ID);

			if($showImage) {
				$html .= '<div class="product-image-container">';

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_image', '', $this->data->ID);

					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
					}
					
					$html .= $featured_image;

					if($showLinkOnImage) {
						$html .= '</a>';
					}

				$html .= '</div>';
				$html .= '</div>
					<div class="product-container-row">';
			}

			if($showGalleryImages) {
				$galleryImageSize = $this->get_option('galleryImageSize');
				$galleryImageColumns = $this->get_option('galleryImageColumns');

					$html .= '<div class="product-gallery-images-container">';
						$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
					$html .= '</div>';
				$html .= '</div>
					<div class="product-container-row">';
			}

			$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_container', '', $this->data->ID);

			$html .= '<div class="product-information-container">';

				if($showCategoryDescription) {

					$primary_cat_id = get_post_meta($this->data->ID, '_yoast_wpseo_primary_product_cat', true);
					if($primary_cat_id){
						$terms = array( get_term($primary_cat_id, 'product_cat') );
					} else {
						$terms = get_the_terms( $this->data->ID, 'product_cat' );
					}

					$txt = "";
					if(!empty($terms)) {
						foreach ($terms as $term) {
							if(isset($term->description) && !empty($term->description)) {
								$html .= '<p class="product-category-description">' . $term->description . '</p>';
								break;
							}
						}
					}
				}
				if($showTitle) {
					$html .= '<h1 class="product-title">' . $this->data->title . '</h1>';
				}

				if($showSKU && $showSKUMoveUnderTitle) {
					$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce-pdf-catalog'  ). '</b> '. $this->data->sku . '</span><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information', '', $this->data->ID);

				$html .= '<div class="product-information">';

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_start', '', $this->data->ID);

					if($showShortDescription) {
						$html .= '<span class="product-short-description">' . wpautop($this->data->short_description) . '</span>';
					}
					if($showDescription) {
						$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span>';
					}

					if($showAttributes && !$this->get_option('showAttributesMoveBelowSKU')) {
						$html .= $this->get_attributes_table();
					}

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_read_more', '', $this->data->ID);

					if($showReadMore) {
						$html .= '<a class="product-read-more" href="' . $this->get_permalink($this->data->ID) . '">';
						$html .= $this->get_option('showReadMoreText');
						$html .= '</a><br>';
					}
					if($this->product->is_type( 'variable' ) && $this->get_option('showVariations')) {
						if($this->get_option('showVariationsTitle')) {
							$html .= '<h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3>';
						}
						$html .= $this->get_variation_table();
					}
					if($showSKU && !$showSKUMoveUnderTitle) {
						$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce-pdf-catalog'  ). '</b> '. $this->data->sku . '</span><br>';
					}
					if($showPrice) {
						$html .= '<span class="product-price"><b>'.__( 'Price:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->price . '</span><br>';
					}
					if($showStock) {
						$html .= '<span class="product-stock"><b>'.__( 'Stock:', 'woocommerce-pdf-catalog'  ). '</b>' . $this->data->stock_status . '</span><br>';
					}

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_categories', '', $this->data->ID);

					if($showCategories) {
						$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
					}
					if($showTags) {
						$html .= '<span class="product-tags">' . $this->data->tags . '</span>';
					}

					if($showAttributes && $this->get_option('showAttributesMoveBelowSKU')) {
						$html .= $this->get_attributes_table();
					}

					if($showQR) {
						$html .= '<barcode code="' . $this->get_permalink($this->data->ID) . '" type="QR" class="qr-code" size="' . $this->get_option('qrSize') . '" error="M" />';
					}
					if($showBarcode) {
						$barcodeType = $this->get_option('barcodeType');
						$barcodeMetaKey = $this->get_option('barcodeMetaKey');
						$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
						if(!empty($barcodeMetaValue)) {
							$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode size="' . $this->get_option('barcodeSize') . '" code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
						}
					}

					$html .= $this->get_custom_meta_data();
					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_end', '', $this->data->ID);
					
				$html .= '</div>';

				$html .= apply_filters('woocommerce_pdf_catalog_after_product_information_container', '', $this->data->ID);
			
			$html .= '</div>';

			$html .= apply_filters('woocommerce_pdf_catalog_after_product_container', '', $this->data->ID);

		$html .= '</div>';

		$html = apply_filters('woocommerce_pdf_catalog_single_product_html', $html, $this->data->ID, $this->data);
		return $html;
	}

	private function get_variation_table()
	{
		global $woocommerce, $post;
		
		$variations_image_size = $this->get_option('variationsImageSize') ? $this->get_option('variationsImageSize') : '50';
		$variationsLimit = $this->get_option('variationsLimit') ? $this->get_option('variationsLimit') : '10';
		$available_variations = $this->product->get_available_variations();

		$variationsTables = $this->get_option('variationsTables') ? $this->get_option('variationsTables') : 0;
		$variationsTableLimit = $this->get_option('variationsTableLimit') ? $this->get_option('variationsTableLimit') : 20;
		$variationsTableNewRow = $this->get_option('variationsTableNewRow') ? $this->get_option('variationsTableNewRow') : 3;
		$variationsTableWidth = $this->get_option('variationsTableWidth') ? $this->get_option('variationsTableWidth') : 250;
		ob_start();

		$variationsTableClasses = 'woocommerce-pdf-catalog-variations variations-table';
		if($variationsTables) {
			$variationsTableClasses = 'woocommerce-pdf-catalog-variations split-variation-tables';
		}
		?>
		<table class="<?php echo $variationsTableClasses ?>">
			<?php if(!$variationsTables) { ?>
	 		<thead>
	            <tr class="variation-head-row">
	            	<?php if($this->get_option('variationsShowImage')){ ?>
	                <th width="<?php echo $variations_image_size ?>px"><?php _e( 'Image', 'woocommerce-pdf-catalog' ); ?></th>
	                <?php } ?>

	            	<?php if($this->get_option('variationsShowSKU')){ ?>
	                <th><?php _e( 'SKU', 'woocommerce-pdf-catalog' ); ?></th>
	                <?php } ?>

	                <?php if($this->get_option('variationsShowDescription')){ ?>
	                <th><?php _e('Description', 'woocommerce-pdf-catalog') ?></th>
	                <?php } ?>

	                <?php if($this->get_option('variationsShowStock')){ ?>
	                <th><?php _e('Stock Status', 'woocommerce-pdf-catalog') ?></th>
	                <?php } ?>

	                <?php 
	                if($this->get_option('variationsShowAttributes')){ 
	                	$variation_attributes = $this->product->get_variation_attributes();
	                	foreach ($variation_attributes as $key => $value) {
	                		echo '<th class="variation-attribute-head-' . $key . '">' . wc_attribute_label($key) . '</th>';
	                	}
                	} ?>
                	
		            <?php if($this->get_option('variationsShowPrice')){ ?>
		            <th><?php _e('Price', 'woocommerce-pdf-catalog') ?></th>
		            <?php } ?>

		            <?php if($this->get_option('variationsShowComment')){ ?>
		            <th><?php _e('Comment', 'woocommerce-pdf-catalog') ?></th>
		            <?php } ?>
	            </tr>
	        </thead>
	        <?php } ?>
			<tbody>
	        <?php 
	        $count = 0;
	        $variationsCount = count($available_variations);
	        $tableCount = 0;
	        $variationLimitReached = false;
	        $variationsTableLimitReached = true;
	        $tableRowCount = 1;

        	if($variationsTables) {
        		echo '<tr>';
        	}
	        foreach ($available_variations as $variation) {

	        	if($count == $variationsLimit) {
	        		$variationLimitReached = true;

	        		if($variationsTables && (count($available_variations) !== $count)) {
	        			echo '</table></td>';
	        		}

	        		break;
	        	}

	        	if($variationsTables && $count % $variationsTableLimit == 0) {

	        		echo '<td width="' . $variationsTableWidth . 'px"><table class="variation-table-split" style="width: ' . $variationsTableWidth . 'px">';
	        		?>
		            <tr class="variation-head-row">
		            	<?php if($this->get_option('variationsShowImage')){ ?>
		                <th width="<?php echo $variations_image_size ?>px"><?php _e( 'Image', 'woocommerce-pdf-catalog' ); ?></th>
		                <?php } ?>

		            	<?php if($this->get_option('variationsShowSKU')){ ?>
		                <th><?php _e( 'SKU', 'woocommerce-pdf-catalog' ); ?></th>
		                <?php } ?>

		                <?php if($this->get_option('variationsShowDescription')){ ?>
		                <th><?php _e('Description', 'woocommerce-pdf-catalog') ?></th>
		                <?php } ?>

		                <?php if($this->get_option('variationsShowStock')){ ?>
		                <th><?php _e('Stock Status', 'woocommerce-pdf-catalog') ?></th>
		                <?php } ?>

		                <?php 
		                if($this->get_option('variationsShowAttributes')){ 
		                	$variation_attributes = $this->product->get_variation_attributes();
		                	foreach ($variation_attributes as $key => $value) {
		                		echo '<th class="variation-attribute-head-' . $key . '">' . wc_attribute_label($key) . '</th>';
		                	}
	                	} ?>
	                	
			            <?php if($this->get_option('variationsShowPrice')){ ?>
			            <th><?php _e('Price', 'woocommerce-pdf-catalog') ?></th>
			            <?php } ?>

			            <?php if($this->get_option('variationsShowComment')){ ?>
			            <th><?php _e('Comment', 'woocommerce-pdf-catalog') ?></th>
			            <?php } ?>
		            </tr>
	            <?php
        		}

	        	?>
	            <tr class="variation-row">
	            <?php
	            $variation = new WC_Product_Variation($variation['variation_id']);

	            $variation_image_ID = $variation->get_image_id();
	            if(!empty($variation_image_ID)) {
	            	$variation_image = wp_get_attachment_image_src($variation_image_ID, 'medium');
	            	if(isset($variation_image[0]) && !empty($variation_image[0])) {
            			$variation_image = $variation_image[0];
	            	}
	            } else {
	            	$variation_image = false;
	            }

				if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
					if (!$variation_image) $variation_image = wc_placeholder_img_src();
				} else {
					if (!$variation_image) $variation_image = woocommerce_placeholder_img_src();
				}

				?>
					<?php if($this->get_option('variationsShowImage')){ ?>
					<td class="variations-image" style="text-align: center;"><?php echo '<img src="' . $variation_image . '" style="width: ' . $variations_image_size . 'px;">' ?></td>
					<?php } ?>
	            	<?php if($this->get_option('variationsShowSKU')){ ?>
	                <td class="variations-sku"><?php echo $variation->get_sku() ?></td>
	                <?php } ?>

	                <?php if($this->get_option('variationsShowDescription')){ ?>
	                <td class="variations-description"><?php echo $variation->get_description() ?></td>
	            	<?php } ?>

	                <?php if($this->get_option('variationsShowStock')){ ?>
	                <td class="variations-stock"><?php echo wc_get_stock_html($variation) ?></td>
	                <?php } ?>

	                <?php if($this->get_option('variationsShowAttributes')){ ?>
		           		<?php foreach ($variation->get_variation_attributes() as $attr_name => $attr_value) : ?>
		                <td class="variations-attributes variation-attribute-value-<?php echo $attr_name ?>">
		                <?php
		                    // Get the correct variation values
		                    if (strpos($attr_name, '_pa_')){ // variation is a pre-definted attribute
		                        $attr_name = substr($attr_name, 10);
		                        $attr = get_term_by('slug', $attr_value, $attr_name);
		                        if(!is_object($attr)) {
									$attr_value = $attr_value;
		                        } else {
		                        	$attr_value = $attr->name;
		                        }

		                        $attr_name = wc_attribute_label($attr_name);
		                    } else {
		                        $attr = maybe_unserialize( get_post_meta( $this->product->get_id(), '_product_attributes' ) );
		                        $attr_name = substr($attr_name, 10);
		                        $attr_name = $attr[0][$attr_name]['name'];
		                    }
		                    if(empty($attr_value)) {
		                    	echo sprintf( __('Any %s', 'woocommerce-pdf-catalog'), $attr_name);
	                    	} else {
	                    		echo $attr_value;
                    		}
		                ?>
		                </td>
			            <?php 
			            endforeach; ?>
		            <?php } ?>

	                <?php if($this->get_option('variationsShowPrice')){ ?>
	                <td class="variations-price" width="100px"><?php echo $variation->get_price_html() ?></td>
	                <?php } ?>

		            <?php if($this->get_option('variationsShowComment')){ ?>
		            <td></td>
		            <?php } ?>
	            </tr>
	        <?php 
	        	$count++;
	        	if($variationsTables && ($count % $variationsTableLimit == 0 || (count($available_variations) == $count))) {
	        		$tableCount++;
	        		echo '</table></td>';

	        		if($variationsCount == $count) {
	        		

	        			$tmp = $tableRowCount * $variationsTableNewRow;

				    	$missingTableRows = $tmp - $tableCount;

				    	for ($i=0; $i < $missingTableRows; $i++) { 
				    		echo '<td width="' . $variationsTableWidth . 'px"></td>';
				    	}
	        		}

        			if($tableCount % $variationsTableNewRow == 0) {
        				echo '</tr></tbody></table><table class="' . $variationsTableClasses . '"><tbody><tr>';
        				$tableRowCount++;
        			}
        		}
	    	} 

        	if($variationsTables) {
        		echo '</tr>';
        	}
	    	?>
	        </tbody>
	    </table>

	    <?php
	    
	    if($variationLimitReached) {
	    	echo '<div class="variations-limit-reached"><a target="_blank" href="' . $this->get_permalink($post->ID) . '">' . __('To see all Variations click here') . '</a></div>';
	    }
	    $html = ob_get_clean();
	    return $html;
	}

	private function get_attributes_table()
	{
		global $woocommerce;

    	$product = $this->product;

		$has_row    = false;
		$alt        = 1;
		$attributes = $product->get_attributes();
		$attributesToShow = $this->get_option('showAttributesAttributes');

		// remap variation attributes
		if($product->is_type('variation')) {
			$tmp = array();
			foreach ($attributes as $attributeKey => $attributeValue) {

				$taxonomy = get_taxonomy($attributeKey);
				if(!$taxonomy) {
					continue;
				}

				$term_obj  = get_term_by('slug', $attributeValue, $attributeKey);
				if(!$term_obj) {
					continue;
				}	

				$attribute = new WC_Product_Attribute();
				$attribute->set_id( $term_obj->term_id );
				$attribute->set_name( $attributeKey ); //  $taxonomy->label
				$attribute->set_options( array( esc_html( $term_obj->name ) ) );
				$attribute->set_visible( true );   

				$tmp[] = $attribute;
			}
			$attributes = $tmp;
			
		}

		ob_start();

		if(class_exists('WooCommerce_Group_Attributes')) {

			global $woocommerce_group_attributes_options;

			$layout = $woocommerce_group_attributes_options['layout'];
			$layout = apply_filters('woocommerce_group_attributes_layout', $layout, $product->get_id());

			include ABSPATH . 'wp-content/plugins/woocommerce-group-attributes/public/partials/woocommerce-group-attributes-output-layout-' . $layout . '.php';
			return ob_get_clean();
		}
		?>
		<div class="attributes two-cols">

			<?php if ( $this->get_option('showAttributesWeight') && $product->has_weight() ) : $has_row = true; ?>
				<div class="<?php if ( ( $alt = $alt * -1 ) === 1 ) echo 'alt'; ?>">
					<div class="col attribute-name"><?php _e( 'Weight', 'woocommerce-pdf-catalog' ) ?></div>
					<div class="col attribute-value">
					<?php
						if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
							echo esc_html( wc_format_weight( $product->get_weight() ) );
						} else {
							echo $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) );
						}
					?></div>
				</div>
			<?php endif; ?>

			<?php if ( $this->get_option('showAttributesDimensions') && $product->has_dimensions() ) : $has_row = true; ?>
				<div class="<?php if ( ( $alt = $alt * -1 ) === 1 ) echo 'alt'; ?>">
					<div class="col attribute-name"><?php _e( 'Dimensions', 'woocommerce-pdf-catalog' ) ?></div>
					<div class="col attribute-value">
					<?php
						if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
							echo esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) );
						} else {
							echo $product->get_dimensions(); 
						}
					?></div>
				</div>
			<?php endif; ?>

			<?php foreach ( $attributes as $attribute ) :

				if(!empty($attributesToShow) && !in_array($attribute->get_name(), $attributesToShow)) {
					continue;
				}

				if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
					continue;
				} else {
					$has_row = true;
				}
				?>
				<div class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>  attribute-other">
					<div class="col attribute-name">
						<?php 

						if($this->get_option('showAttributesImages')) {

							$hasImage = apply_filters('woocommerce_attribute_name_image', wc_attribute_label( $attribute->get_name() ), $attribute->get_id()); 
							if($hasImage) {
								echo $hasImage;
							} else {
								echo  wc_attribute_label( $attribute->get_name() );
							}
						} else {
							echo  wc_attribute_label( $attribute->get_name() );
						}
						?>
							
					</div>
					<div class="col attribute-value"><?php

						if ( $attribute['is_taxonomy'] ) {

							if($this->get_option('showAttributesImages')) {

								$values = array();
								$attribute_values = wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'all' ) );
								$attributeImageWidth = $this->get_option('attributeImageWidth');
								$attributeImageTermData = get_option( 'woocommerce_attribute_image_term_data' );

								foreach ( $attribute_values as $attribute_value ) {

									if(isset($attributeImageTermData[$attribute_value->term_id])) {
										$attributeImageData = $attributeImageTermData[$attribute_value->term_id];
										if(isset($attributeImageData['thumbnail']) && !empty($attributeImageData['thumbnail'])) {

											$attribute_value_image_src = wp_get_attachment_image_src($attributeImageData['thumbnail'], 'full');
											$value_name = '<img width="' . $attributeImageWidth . 'px" src="' . $attribute_value_image_src[0] . '">';
										} else {
											$value_name = esc_html( $attribute_value->name );	
										}
									} else {
										$value_name = esc_html( $attribute_value->name );
									}
									$value_name .= apply_filters('woocommerce_attribute_value_description', $attribute_value);

									if ( $attribute_taxonomy->attribute_public ) {
										$values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
									} else {
										$values[] = $value_name;
									}
								}

								if($hasImage) {
									echo apply_filters( 'woocommerce_attribute',  implode( ' ', $values ) , $attribute, $values );
								} else {
									echo apply_filters( 'woocommerce_attribute', implode( ', ', $values ), $attribute, $values );
								}
							} else {

								$values = wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'names' ) );
								echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
							}

						} else {

							// Convert pipes to commas and display values
							$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

						}
					?></div>
				</div>
			<?php endforeach; ?>

		</div>
		<?php
		if ( $has_row ) {
			return ob_get_clean();
		} else {
			ob_end_clean();
		}
	}

	public function get_placeholder()
	{
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$html = '<table class="product-container">';
			$html .= '<tr class="product-container-row">';
			$html .= '<td class="product-image-container">';
			$html .= '</td>';
			$html .= '</tr>';
		$html .= '</table>';

		return $html;
	}

	public function get_gallery_images($galleryImageColumns = 4, $galleryImageSize = '200')
    {
    	global $woocommerce;

		$product = $this->product;

		$galleryIncludeFeatureImage = $this->get_option('galleryIncludeFeatureImage');
		$galleryImageSizeType = $this->get_option('galleryImageSizeType');

		ob_start();

		if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
			$attachment_ids = $product->get_gallery_image_ids();
		} else {
			$attachment_ids = $product->get_gallery_attachment_ids();
		}

		if($galleryIncludeFeatureImage) {
			if ( has_post_thumbnail($this->post->ID)) { 
				$attachment_ids[] = get_post_thumbnail_id( $this->post->ID );
			}
		}

		$count_attachment_ids = count($attachment_ids);

		if ( $count_attachment_ids >= 1 ) {

			$loop 				= 0;

			?>
			<table class="woocommerce-gallery-images" cellspacing="0" cellpadding="0">
			<?php
				$customBreak = true;
				foreach ( $attachment_ids as $attachment_id ) {

					if ( $loop === 0 || $loop % $galleryImageColumns === 0 )
					{
						echo "<tr>";
						$classes[] = 'first';
					}


					$thumbnail = wp_get_attachment_image_src( $attachment_id, $galleryImageSizeType ); 
					if($this->get_option('performanceUseImageLocally')) {
					    $uploads = wp_upload_dir();
						$thumbnail = str_replace( $uploads['baseurl'], $uploads['basedir'], $thumbnail[0] );
						$src = $thumbnail;
					} else {
						$src = $thumbnail[0];
					}					

					$gallery_image = '<img width="' . $galleryImageSize . 'px" src="' . $src . '" >';

					$image_class = esc_attr( implode( ' ', $classes ) );

					echo sprintf( '<td class="%s">%s</td>', $image_class, $gallery_image);

					if ( ( $loop + 1 ) % $galleryImageColumns === 0 )
					{
						echo "</tr>";
						$classes[] = 'last';
					}
					$loop++;
				}

			?>
			</table>
			<?php
		} else {
			return FALSE;
		}

		return ob_get_clean();
    }

	public function get_third_product_layout()
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
		$showStock = $this->get_option('showStock');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( $this->data->$indexKey, ENT_QUOTES ) . '" />';

		$html .= apply_filters('woocommerce_pdf_catalog_before_product_container', '', $this->data->ID);

		if($showImage) { 
			$html .= '<div class="product-image-container fl col-width col-image">';
			if($showLinkOnImage) {
				$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
			}
			$html .= $featured_image;
			if($showLinkOnImage) { 
				$html .= '</a>';
			}
			$html .= '</div>';
		}
		if($showSKU) {
			$html .= '<div class="fl col-width col-sku"><span class="product-sku">' . $this->data->sku . '</span> </div>';
		}
		if($showTitle) {
			$html .= '<div class="fl col-width col-title"><span class="product-title-name">' . $this->data->title . '&nbsp;</span></div>';
		}
		if($showShortDescription) {
			$html .= '<div class="fl col-width col-short-description"><span class="product-short-description">' . $this->data->short_description . '&nbsp;</span></div>';
		}
		if($showDescription) {
			$html .= '<div class="fl col-width col-description"><span class="product-description">' . $this->data->description . '&nbsp;</span></div>';
		}
		if($showAttributes) {
			$html .= '<div class="fl col-width col-attributes">' .  $this->get_attributes_table() . '&nbsp;</div>';
		}
		if($showPrice) {
			$html .= '<div class="fl col-width col-price"><span class="product-price">' . $this->data->price . '&nbsp;</span></div>';
		}
		if($showStock) {
			$html .= '<div class="fl col-width col-stock"><span class="product-stock">' . $this->data->stock_status . '&nbsp;</span></div>';
		}
		if($showReadMore) {
			$html .= '<div class="fl col-width col-readmore"><a class="product-read-more" href="' . $this->get_permalink($this->data->ID) . '">';
			$html .= $this->get_option('showReadMoreText');
			$html .= '</a></div>';
		}
		if($showCategories) {
			$html .= '<div class="fl col-width col-categories"><span class="product-categories">' . $this->data->categories . '&nbsp;</span></div>';
		}
		if($showTags) {
			$html .= '<div class="fl col-width col-tags"><span class="product-tags">' . $this->data->tags . '&nbsp;</span></div>';
		}
		if($showQR) {
			$html .= '<div class="fl col-width col-qr"><barcode code="' . $this->get_permalink($this->data->ID) . '" type="QR" class="qr-code" size="' . $this->get_option('qrSize') . '" error="M" /></div>';
		}
		if($showBarcode) {
			$barcodeType = $this->get_option('barcodeType');
			$barcodeMetaKey = $this->get_option('barcodeMetaKey');
			$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
			if(!empty($barcodeMetaValue)) {
				$html .= '<div class="fl col-width col-barcode">';
				$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode size="' . $this->get_option('barcodeSize') . '" code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
				$html .= '</div>';
			}
		}

		if(!empty($this->data->meta_keys)) {
	 		foreach ( $this->data->meta_keys as $meta_key) {

	 			if(empty($meta_key['value'])) {
	 				continue;
	 			}

	 			$html .= '<div class="fl col-width col-' . $meta_key['key'] . '">' . $meta_key['value'] . '&nbsp;</div>';
	 		}
		}

		$html .= apply_filters('woocommerce_pdf_catalog_after_product_container', '', $this->data->ID);
		
		return $html;

	}

	public function get_fourth_product_layout()
	{
		$productContainerClass = "";
		if($this->product->is_on_sale()) {
			$productContainerClass .= 'on-sale';
		}

  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showCategoryDescription = $this->get_option('showCategoryDescription');

		$showPrice = $this->get_option('showPrice');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showSKUMoveUnderTitle = $this->get_option('showSKUMoveUnderTitle');
		$showCategories = $this->get_option('showCategories');
		$showStock = $this->get_option('showStock');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( str_replace(':', '&#58;', $this->data->$indexKey ), ENT_QUOTES) . '" />';

		$html .='<div class="product-container three-cols ' . $productContainerClass . '" width="100%">';

			$html .= apply_filters('woocommerce_pdf_catalog_before_product_container', '', $this->data->ID);

			$html .= '<div class="col product-images-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_image', '', $this->data->ID);

				if($showImage) {
					$html .= '<div class="product-image-container">';
					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
					}
					$html .= $featured_image;
					if($showLinkOnImage) { 
						$html .= '</a>';
					}
					$html .= '</div>';
				}

				if($showGalleryImages) {
					$galleryImageSize = $this->get_option('galleryImageSize');
					$galleryImageColumns = $this->get_option('galleryImageColumns');
					$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
				}
			$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_container', '', $this->data->ID);

		$html .= '<div class="col product-content-container">';
			$html .= '<div class="product-information-container">';
				if($showCategoryDescription) {

					if(class_exists('RankMath')) {
						$primary_cat_id = get_post_meta($this->data->ID, 'rank_math_primary_product_cat', true);
					} else {
						$primary_cat_id = get_post_meta($this->data->ID, '_yoast_wpseo_primary_product_cat', true);	
					}

					if($primary_cat_id){
						$terms = array( get_term($primary_cat_id, 'product_cat') );
					} else {
						$terms = get_the_terms( $this->data->ID, 'product_cat' );
					}

					$txt = "";
					if(!empty($terms)) {
						foreach ($terms as $term) {
							if(isset($term->description) && !empty($term->description)) {
								$html .= '<p class="product-category-description">' . $term->description . '</p>';
								break;
							}
						}
					}
				}
				if($showTitle) {
					$html .= '<h1 class="product-title">' . $this->data->title . '</h1>';
				}

				if($showSKU && $showSKUMoveUnderTitle) {
					$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce-pdf-catalog'  ). '</b> '. $this->data->sku . '</span><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information', '', $this->data->ID);

				$html .= '<div class="product-information">';

					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_start', '', $this->data->ID);

					if($showShortDescription) {
						$html .= '<span class="product-short-description">' . wpautop($this->data->short_description) . '</span>';
					}

					if($showDescription) {
						$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span>';
					}

					$html .= $this->get_custom_meta_data();
					$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_end', '', $this->data->ID);
					
				$html .= '</div>';

				if($showReadMore) {
					$html .= '<a class="product-read-more" href="' . $this->get_permalink($this->data->ID) . '">';
					$html .= $this->get_option('showReadMoreText');
					$html .= '</a><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_after_product_information', '', $this->data->ID);

			$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="col product-data-container-col">';
			$html .= '<div class="product-data-container">';

				if($showAttributes && !$this->get_option('showAttributesMoveBelowSKU')) {
					$html .= $this->get_attributes_table();
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_read_more', '', $this->data->ID);

				if($this->product->is_type( 'variable' ) && $this->get_option('showVariations') ) {
					if($this->get_option('showVariationsTitle')) {
						$html .= '<h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3>';
					}
					$html .= $this->get_variation_table();
				}

				if($showSKU && !$showSKUMoveUnderTitle) {
					$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce-pdf-catalog'  ). '</b> '. $this->data->sku . '</span><br>';
				}

				if($showPrice) {
					$html .= '<span class="product-price"><b>'.__( 'Price:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->price . '</span><br>';
				}

				if($showStock) {
					$html .= '<span class="product-stock"><b>'.__( 'Stock:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->stock_status . '</span><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_categories', '', $this->data->ID);
			

				if($showCategories) {
					$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
				}
				if($showTags) {
					$html .= '<span class="product-tags">' . $this->data->tags . '</span>';
				}				

				if($showAttributes && $this->get_option('showAttributesMoveBelowSKU')) {
					$html .= $this->get_attributes_table();
				}

				if($showQR) {
					$html .= '<barcode code="' . $this->get_permalink($this->data->ID) . '" type="QR" class="qr-barcode" size="' . $this->get_option('qrSize') . '" error="M" />';
				}
				if($showBarcode) {
					$barcodeType = $this->get_option('barcodeType');
					$barcodeMetaKey = $this->get_option('barcodeMetaKey');
					$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
					if(!empty($barcodeMetaValue)) {
						$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode size="' . $this->get_option('barcodeSize') . '" code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
					}
				}
			$html .= '</div>';

			$html .= apply_filters('woocommerce_pdf_catalog_after_product_information_container', '', $this->data->ID);

		$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_after_product_container', '', $this->data->ID);
		
		$html .= '<div class="clear"></div>';
		$html .= '</div>';

		$html = apply_filters('woocommerce_pdf_catalog_single_product_html', $html, $this->data->ID, $this->data);
		return $html;
	}

	public function get_fifth_product_layout()
	{
		$productContainerClass = "";
		if($this->product->is_on_sale()) {
			$productContainerClass .= 'on-sale';
		}

  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showCategoryDescription = $this->get_option('showCategoryDescription');

		$showPrice = $this->get_option('showPrice');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showSKUMoveUnderTitle = $this->get_option('showSKUMoveUnderTitle');
		$showCategories = $this->get_option('showCategories');
		$showStock = $this->get_option('showStock');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( str_replace(':', '&#58;', $this->data->$indexKey ), ENT_QUOTES) . '" />';

		$html .='<div class="product-container two-cols ' . $productContainerClass . '" width="100%">';

			$html .= apply_filters('woocommerce_pdf_catalog_before_product_container', '', $this->data->ID);

			$html .= '<div class="col product-images-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_image', '', $this->data->ID);

				if($showImage) {
					$html .= '<div class="product-image-container">';
					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
					}
					$html .= $featured_image;
					if($showLinkOnImage) { 
						$html .= '</a>';
					}
					$html .= '</div>';
				}

				if($showGalleryImages) {
					$galleryImageSize = $this->get_option('galleryImageSize');
					$galleryImageColumns = $this->get_option('galleryImageColumns');
					$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
				}
			$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_container', '', $this->data->ID);

		$html .= '<div class="col product-content-container">';
			$html .= '<div class="product-information-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information', '', $this->data->ID);

				if($showTitle) {
					$html .= '<h1 class="product-title">' . $this->data->title;

					if($showPrice) {
						$html .= ' - <span class="product-price">' . $this->data->price . '</span><br>';
					}

					$html .= '</h1>';
				} elseif($showPrice) {
					$html .= ' <span class="product-price">' . $this->data->price . '</span><br>';
				}

				if($showShortDescription) {
					$short_description = $this->data->short_description;

					if($showSKU) {
						$short_description .= ' ('. __( 'SKU:', 'woocommerce-pdf-catalog'  ) . $this->data->sku;

						if($showTags) {
							$short_description .= ' – <span class="product-tags">' . $this->data->tags . '</span>';
						}		

						$short_description .= ')';
					}

					$html .= '<span class="product-short-description">' . wpautop( $short_description ) . '</span>';
				} elseif($showSKU) {
					$html .= '<span class="product-sku">('.__( 'SKU:', 'woocommerce-pdf-catalog'  ). ' ' . $this->data->sku . ')</span><br>';
				}

				if($showDescription) {
					$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span>';
				}

				$html .= '<hr class="product-divider">';

				if($showCategoryDescription) {
					if(class_exists('RankMath')) {
						$primary_cat_id = get_post_meta($this->data->ID, 'rank_math_primary_product_cat', true);
					} else {
						$primary_cat_id = get_post_meta($this->data->ID, '_yoast_wpseo_primary_product_cat', true);	
					}

					if($primary_cat_id){
						$terms = array( get_term($primary_cat_id, 'product_cat') );
					} else {
						$terms = get_the_terms( $this->data->ID, 'product_cat' );
					}

					$txt = "";
					if(!empty($terms)) {
						foreach ($terms as $term) {
							if(isset($term->description) && !empty($term->description)) {
								$html .= '<p class="product-category-description">' . $term->description . '</p>';
								break;
							}
						}
					}
				}

				$html .= $this->get_custom_meta_data();

				if($showAttributes && !$this->get_option('showAttributesMoveBelowSKU')) {
					$html .= $this->get_attributes_table();
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_read_more', '', $this->data->ID);

				if($this->product->is_type( 'variable' ) && $this->get_option('showVariations') ) {
					if($this->get_option('showVariationsTitle')) {
						$html .= '<h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3>';
					}
					$html .= $this->get_variation_table();
				}

				if($showStock) {
					$html .= '<span class="product-stock"><b>'.__( 'Stock:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->stock_status . '</span><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_categories', '', $this->data->ID);
			

				if($showCategories) {
					$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
				}		


				if(!$showShortDescription && $showTags) {
					$short_description .= ' – <span class="product-tags">' . $this->data->tags . '</span>';
				}	

				if($showAttributes && $this->get_option('showAttributesMoveBelowSKU')) {
					$html .= $this->get_attributes_table();
				}

				if($showQR) {
					$html .= '<barcode code="' . $this->get_permalink($this->data->ID) . '" type="QR" class="qr-barcode" size="' . $this->get_option('qrSize') . '" error="M" />';
				}

				if($showBarcode) {
					$barcodeType = $this->get_option('barcodeType');
					$barcodeMetaKey = $this->get_option('barcodeMetaKey');
					$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
					if(!empty($barcodeMetaValue)) {
						$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode size="' . $this->get_option('barcodeSize') . '" code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
					}
				}
				
				if($showReadMore) {
					$html .= '<a class="product-read-more" href="' . $this->get_permalink($this->data->ID) . '">';
					$html .= $this->get_option('showReadMoreText');
					$html .= '</a><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_after_product_information', '', $this->data->ID);

			$html .= '</div>';
		$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_after_product_container', '', $this->data->ID);
		
		$html .= '<div class="clear"></div>';
		$html .= '</div>';

		$html = apply_filters('woocommerce_pdf_catalog_single_product_html', $html, $this->data->ID, $this->data);
		return $html;
	}

	public function get_sixth_product_layout()
	{
		$productContainerClass = "";
		if($this->product->is_on_sale()) {
			$productContainerClass .= 'on-sale';
		}

  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showCategoryDescription = $this->get_option('showCategoryDescription');

		$showPrice = $this->get_option('showPrice');
		$showAttributes = $this->get_option('showAttributes');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showSKUMoveUnderTitle = $this->get_option('showSKUMoveUnderTitle');
		$showCategories = $this->get_option('showCategories');
		$showStock = $this->get_option('showStock');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = apply_filters('woocommerce_pdf_catalog_product_image_size', $this->get_option('productsImageSize'), $this->data->ID);

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( str_replace(':', '&#58;', $this->data->$indexKey ), ENT_QUOTES) . '" />';

		$html .='<div class="product-container two-cols ' . $productContainerClass . '" width="100%">';

			$html .= apply_filters('woocommerce_pdf_catalog_before_product_container', '', $this->data->ID);

			$html .= '<div class="col product-images-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_image', '', $this->data->ID);

				if($showImage) {
					$html .= '<div class="product-image-container">';
					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . $this->get_permalink($this->data->ID) . '">';
					}
					$html .= $featured_image;
					if($showLinkOnImage) { 
						$html .= '</a>';
					}
					$html .= '</div>';
				}

				if($showGalleryImages) {
					$galleryImageSize = $this->get_option('galleryImageSize');
					$galleryImageColumns = $this->get_option('galleryImageColumns');
					$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
				}
			$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_container', '', $this->data->ID);

		$html .= '<div class="col product-content-container">';
			$html .= '<div class="product-information-container">';

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information', '', $this->data->ID);

				if($showTitle) {
					$html .= '<h1 class="product-title">' . $this->data->title;

					if($showSKU) {
						$html .= ' <span class="product-sku">(' . $this->data->sku . ')<span>';
					}

					if($showTags && !empty($this->data->tags)) {
						$html .= ' <span class="product-tags">(' . $this->data->tags . ')</span>';
					}		

					if($showPrice) {
						$html .= ' - <span class="product-price">' . $this->data->price . '</span><br>';
					}

					$html .= '</h1>';
				} else {
					if($showPrice) {
						$html .= '<span class="product-price">' . $this->data->price . '</span><br>';
					}
				}

				if($showSKU) {
					$html .= '<span class="product-sku">' . __( 'SKU:', 'woocommerce-pdf-catalog'  ) . $this->data->sku . '</span><br>';	
				}

				if($showTags) {
					$html .= '<span class="product-tags">' . $this->data->tags . '</span><br>';
				}	

				if($showShortDescription) {
					$html .= '<span class="product-short-description">' . wpautop( $this->data->short_description ) . '</span>';
				}

				if($showDescription) {
					$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span>';
				}

				$html .= '<hr class="product-divider">';

				if($showCategoryDescription) {
					if(class_exists('RankMath')) {
						$primary_cat_id = get_post_meta($this->data->ID, 'rank_math_primary_product_cat', true);
					} else {
						$primary_cat_id = get_post_meta($this->data->ID, '_yoast_wpseo_primary_product_cat', true);	
					}

					if($primary_cat_id){
						$terms = array( get_term($primary_cat_id, 'product_cat') );
					} else {
						$terms = get_the_terms( $this->data->ID, 'product_cat' );
					}

					$txt = "";
					if(!empty($terms)) {
						foreach ($terms as $term) {
							if(isset($term->description) && !empty($term->description)) {
								$html .= '<p class="product-category-description">' . $term->description . '</p>';
								break;
							}
						}
					}
				}

				$html .= $this->get_custom_meta_data();

				if($showAttributes && !$this->get_option('showAttributesMoveBelowSKU')) {
					$html .= $this->get_attributes_table();
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_read_more', '', $this->data->ID);

				if($this->product->is_type( 'variable' ) && $this->get_option('showVariations') ) {
					if($this->get_option('showVariationsTitle')) {
						$html .= '<h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3>';
					}
					$html .= $this->get_variation_table();
				}

				if($showStock) {
					$html .= '<span class="product-stock"><b>'.__( 'Stock:', 'woocommerce-pdf-catalog'  ). '</b> ' . $this->data->stock_status . '</span><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_before_product_information_categories', '', $this->data->ID);
			

				if($showCategories) {
					$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
				}		


				if(!$showShortDescription && $showTags) {
					$short_description .= ' – <span class="product-tags">' . $this->data->tags . '</span>';
				}	

				if($showAttributes && $this->get_option('showAttributesMoveBelowSKU')) {
					$html .= $this->get_attributes_table();
				}

				if($showQR) {
					$html .= '<barcode code="' . $this->get_permalink($this->data->ID) . '" type="QR" class="qr-barcode" size="' . $this->get_option('qrSize') . '" error="M" />';
				}

				if($showBarcode) {
					$barcodeType = $this->get_option('barcodeType');
					$barcodeMetaKey = $this->get_option('barcodeMetaKey');
					$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
					if(!empty($barcodeMetaValue)) {
						$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode size="' . $this->get_option('barcodeSize') . '" code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
					}
				}
				
				if($showReadMore) {
					$html .= '<a class="product-read-more" href="' . $this->get_permalink($this->data->ID) . '">';
					$html .= $this->get_option('showReadMoreText');
					$html .= '</a><br>';
				}

				$html .= apply_filters('woocommerce_pdf_catalog_after_product_information', '', $this->data->ID);

			$html .= '</div>';
		$html .= '</div>';

		$html .= apply_filters('woocommerce_pdf_catalog_after_product_container', '', $this->data->ID);
		
		$html .= '<div class="clear"></div>';
		$html .= '</div>';

		$html = apply_filters('woocommerce_pdf_catalog_single_product_html', $html, $this->data->ID, $this->data);
		return $html;
	}


    /**
     * Get excerpt from string
     * 
     * @param String $str String to get an excerpt from
     * @param Integer $startPos Position int string to start excerpt from
     * @param Integer $maxLength Maximum length the excerpt may be
     * @return String excerpt
     */
    private function get_excerpt($str, $startPos=0, $maxLength=250) {

        $excerpt = strip_tags( $str );
        
        if(strlen($excerpt) > $maxLength) {
            $excerpt   = substr($excerpt, $startPos, $maxLength-3);
            $lastSpace = strrpos($excerpt, ' ');
            $excerpt   = substr($excerpt, 0, $lastSpace);
            $excerpt  .= '...';
        } else {
            $excerpt = $str;
        }

        // $excerpt = strip_shortcodes( preg_replace("/\[[^\]]+\]/", '', $excerpt) );

        return $excerpt;
    }

    protected function get_permalink($productId) 
    {
    	if(!$productId) {
    		return false;
    	}

    	$link = get_permalink($productId);

    	$URLParameters = $this->get_option('showLinkParameters');
    	if(!empty($URLParameters)) {

    		$product = wc_get_product($productId);
    		if($product) {
    			// {{productName}}, {{productSKU}}, {{catalogName}}
    			$URLParameters = str_replace( array('{{productName}}', '{{productSKU}}', '{{catalogName}}'), array(urlencode($product->get_name() ), urlencode($product->get_sku() ), urlencode($this->filename)), $URLParameters);
    		}

			$hasParameter = parse_url($link);
			if(isset($hasParameter['query'])){
		    	$link .= '&' . $URLParameters;
			} else {
				$link .= '?' . $URLParameters;
			}
    		
    	}

    	return $link;
    }
}