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
	public function __construct( $plugin_name, $version, $options) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
		$this->data = new stdClass;
	}

	public function set_post($post)
	{
		global $woocommerce;

		$this->data->ID = $post->ID;

		if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
			$product =  wc_get_product( $this->data->ID );
		} else {
			$product =  get_product( $this->data->ID );
		}
		
		$this->product = $product;
		$this->post = $post;

		$price = $this->product->get_price_html();
		$price = htmlspecialchars_decode($price);
	    $price = str_replace(array('&#8381;'), 'RUB', $price);

	    $sku = $this->product->get_sku();

		// product variables
		$this->data->title = apply_filters('woocommerce_pdf_catalog_product_title', $this->post->post_title, $post->ID);
		$this->data->short_description = apply_filters('woocommerce_pdf_catalog_product_short_description', do_shortcode( $this->post->post_excerpt ), $post->ID);
		$this->data->description = apply_filters('woocommerce_pdf_catalog_product_description', do_shortcode($this->post->post_content), $post->ID);
		$this->data->price = $price;
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
			$this->data->categories = wc_get_product_category_list($this->data->ID, ', ', '<b>' . _n( 'Category:', 'Categories:', $this->data->cat_count, 'woocommerce' ) . '</b> ');
			$this->data->tags = wc_get_product_tag_list($this->data->ID, ', ', '<b>' . _n( 'Tag:', 'Tags:', $this->data->tag_count, 'woocommerce' ) . '</b> ');
		} else {
			$this->data->categories = $this->product->get_categories( ', ', '<b>' . _n( 'Category:', 'Categories:', $this->data->cat_count, 'woocommerce' ) . '</b> ');
			$this->data->tags = $this->product->get_tags( ', ', '<b>' . _n( 'Tag:', 'Tags:', $this->data->tag_count, 'woocommerce' ) . '</b> ');
		}

		if ( has_post_thumbnail($this->post->ID)) { 
			if($this->get_option('performanceUseImageLocally')) {
				// $this->data->src = get_attached_file( get_post_thumbnail_id($this->post->ID));
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($this->post->ID), 'shop_single' ); 
			    $uploads = wp_upload_dir();
				$this->data->src = str_replace( $uploads['baseurl'], $uploads['basedir'], $thumbnail[0] );
			} else {
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($this->post->ID), 'shop_single' ); 
				$this->data->src = $thumbnail[0];
			}
		} else { 
			$this->data->src = plugin_dir_url( __FILE__ ) . 'img/placeholder.png';
		}

		return TRUE;
	}

	public function get_first_product_layout($imageRight = false)
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

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = $this->get_option('productsImageSize');
		$productsImageSizeColumn = $this->get_option('productsImageSizeColumn');

		$productsHeadingsFontSize = $this->get_option('productsHeadingsFontSize');
		$productsHeadingsFontFamily = $this->get_option('productsHeadingsFontFamily') ? $this->get_option('productsHeadingsFontFamily') : 'dejavusans';
		$productsFontSize = $this->get_option('productsFontSize');

		$productsHeadingsLineHeight = $this->get_option('productsHeadingsLineHeight');
		$productsLineHeight = $this->get_option('productsLineHeight');

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( $this->data->$indexKey, ENT_QUOTES ) . '" />';

		$html .='<table class="product-container" width="100%">';

		$html .= '<tr class="product-container-row">';
		if($showImage && $imageRight == false) {
			$html .= '<td class="product-image-container" valign="top" width="' . $productsImageSizeColumn . '">';
			if($showLinkOnImage) {
				$html .= '<a class="product-image-link" href="' . get_permalink($this->data->ID) . '">';
			}
			$html .= $featured_image;
			if($showLinkOnImage) { 
				$html .= '</a>';
			}
		}	

		if($imageRight == false) {
			if($showGalleryImages) {
				$galleryImageSize = $this->get_option('galleryImageSize');
				$galleryImageColumns = $this->get_option('galleryImageColumns');
				if(!$showImage) {
					$html .= '<td class="product-image-container" valign="top" width="' . $productsImageSizeColumn . '">';
				}
				$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);

				$html .= '</td>';
			} else {
				$html .= '</td>';
			}
		}

		$html .= '<td class="product-information-container" valign="top">';
			if($showTitle) {
				$html .= '<h1 class="product-title">' . $this->data->title . '</h1><br/>';
			}
			$html .= '<div class="product-information">';
				if($showShortDescription) {
					$html .= '<span class="product-short-description">' . wpautop($this->data->short_description) . '</span><br/>';
				}
				if($showDescription) {
					$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span><br/>';
				}
				if($showAttributes) {
					$html .= $this->get_attributes_table();
				}
				if($showReadMore) {
					$html .= '<a class="product-read-more" href="' . get_permalink($this->data->ID) . '">';
					$html .= __( 'Read More', 'woocommerce-pdf-catalog'  );
					$html .= '</a><br/>';
				}
				if($this->product->is_type( 'variable' ) && $this->get_option('showVariations')) {
					$html .= '<br/><h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3><br/>';
					$html .= $this->get_variation_table();
				}
				if($showSKU) {
					$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce'  ). '</b> '. $this->data->sku . '</span><br>';
				}
				if($showPrice) {
					$html .= '<span class="product-price"><b>'.__( 'Price:', 'woocommerce'  ). '</b> ' . $this->data->price . '</span><br>';
				}
				if($showCategories) {
					$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
				}
				if($showTags) {
					$html .= '<span class="product-tags">' . $this->data->tags . '</span>';
				}
				if($showQR) {
					$html .= '<barcode code="' . get_permalink($this->data->ID) . '" type="QR" class="qr-barcode" size="0.8" error="M" />';
				}
				if($showBarcode) {
					$barcodeType = $this->get_option('barcodeType');
					$barcodeMetaKey = $this->get_option('barcodeMetaKey');
					$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
					if(!empty($barcodeMetaValue)) {
						$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
					}
				}
				$html .= '</div>';
		$html .= '</td>';
		if($showImage && $imageRight == true) {
			$html .= '<td class="product-image-container" valign="top" width="' . $productsImageSizeColumn . '">';
			if($showLinkOnImage) {
				$html .= '<a class="product-image-link" href="' . get_permalink($this->data->ID) . '">';
			}
			$html .= $featured_image;

			if($showLinkOnImage) {
				$html .= '</a>';
			}
		}

		if($imageRight == true) {
			if($showGalleryImages) {
				$galleryImageSize = $this->get_option('galleryImageSize');
				$galleryImageColumns = $this->get_option('galleryImageColumns');
				if(!$showImage) {
					$html .= '<td class="product-image-container" valign="top" width="' . $productsImageSizeColumn . '">';
				}
				$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);

				$html .= '</td>';
			} else {
				$html .= '</td>';
			}
		}
		$html .= '</tr>';
		$html .= '</table>';

		return $html;

	}

	public function get_second_product_layout()
	{
  		$showImage = $this->get_option('showImage');
  		$showLinkOnImage = $this->get_option('showLinkOnImage');
  		$showGalleryImages = $this->get_option('showGalleryImages');
		$showTitle = $this->get_option('showTitle');
		$showPrice = $this->get_option('showPrice');
		$showShortDescription = $this->get_option('showShortDescription');
		$showDescription = $this->get_option('showDescription');
		$showAttributes = $this->get_option('showAttributes');
		$showReadMore = $this->get_option('showReadMore');
		$showSKU = $this->get_option('showSKU');
		$showCategories = $this->get_option('showCategories');
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = $this->get_option('productsImageSize');
		$productsImageSizeColumn = $this->get_option('productsImageSizeColumn');

		$productsHeadingsFontSize = $this->get_option('productsHeadingsFontSize');
		$productsHeadingsFontFamily = $this->get_option('productsHeadingsFontFamily') ? $this->get_option('productsHeadingsFontFamily') : 'dejavusans';
		$productsFontSize = $this->get_option('productsFontSize');

		$productsHeadingsLineHeight = $this->get_option('productsHeadingsLineHeight');
		$productsLineHeight = $this->get_option('productsLineHeight');

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( $this->data->$indexKey, ENT_QUOTES ) . '" />';

		$html .= '<table class="product-container" width="100%">';
		$html .= '<tr class="product-container-row">';

			if($showImage) {
				$html .= '<td class="product-image-container" valign="top" width="' . $productsImageSizeColumn . '">';

					if($showLinkOnImage) {
						$html .= '<a class="product-image-link" href="' . get_permalink($this->data->ID) . '">';
					}
						$html .= $featured_image;
					if($showLinkOnImage) {
						$html .= '</a>';
					}
				$html .= '</td>';
				$html .= '</tr>
					<tr class="product-container-row">';
			}

			if($showGalleryImages) {
				$galleryImageSize = $this->get_option('galleryImageSize');
				$galleryImageColumns = $this->get_option('galleryImageColumns');

					$html .= '<td class="product-gallery-images-container">';
						$html .= $this->get_gallery_images($galleryImageColumns, $galleryImageSize);
					$html .= '</td>';
				$html .= '</tr>
					<tr class="product-container-row">';
			}


			$html .= '<td class="product-information-container" valign="top">';
				if($showTitle) {
					$html .= '<h1 class="product-title">' . $this->data->title . '</h1><br/>';
				}

				$html .= '<div class="product-information">';
					if($showShortDescription) {
						$html .= '<span class="product-short-description">' . wpautop($this->data->short_description) . '</span><br/>';
					}
					if($showDescription) {
						$html .= '<span class="product-description">' . wpautop($this->data->description) . '</span><br/>';
					}
					if($showAttributes) {
						$html .= $this->get_attributes_table();
					}
					if($showReadMore) {
						$html .= '<a class="product-read-more" href="' . get_permalink($this->data->ID) . '">';
						$html .= __( 'Read More', 'woocommerce-pdf-catalog'  );
						$html .= '</a><br/>';
					}
					if($this->product->is_type( 'variable' ) && $this->get_option('showVariations')) {
						$html .= '<br/><h3 class="variations-title">' . __( 'Variations', 'woocommerce-pdf-catalog' ) . '</h3><br/>';
						$html .= $this->get_variation_table();
					}
					if($showSKU) {
						$html .= '<span class="product-sku"><b>'.__( 'SKU:', 'woocommerce'  ). '</b> '. $this->data->sku . '</span><br>';
					}
					if($showPrice) {
						$html .= '<span class="product-price"><b>'.__( 'Price:', 'woocommerce'  ). '</b> ' . $this->data->price . '</span><br>';
					}
					if($showCategories) {
						$html .= '<span class="product-categories">' . $this->data->categories . '</span><br>';
					}
					if($showTags) {
						$html .= '<span class="product-tags">' . $this->data->tags . '</span>';
					}
					if($showQR) {
						$html .= '<barcode code="' . get_permalink($this->data->ID) . '" type="QR" class="qr-code" size="0.8" error="M" />';
					}
					if($showBarcode) {
						$barcodeType = $this->get_option('barcodeType');
						$barcodeMetaKey = $this->get_option('barcodeMetaKey');
						$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
						if(!empty($barcodeMetaValue)) {
							$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
						}
					}
					
				$html .= '</div>';
			
			$html .= '</td>';

		$html .= '</tr>';
		$html .= '</table>';

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
	                <th><?php _e( 'SKU', 'woocommerce' ); ?></th>
	                <?php } ?>

	                <?php if($this->get_option('variationsShowDescription')){ ?>
	                <th><?php _e('Description', 'woocommerce') ?></th>
	                <?php } ?>

	                <?php 
	                if($this->get_option('variationsShowAttributes')){ 
	                	$variation_attributes = $this->product->get_variation_attributes();
	                	foreach ($variation_attributes as $key => $value) {
	                		echo '<th class="variation-attribute-head-' . $key . '">' . wc_attribute_label($key) . '</th>';
	                	}
                	} ?>
                	
		            <?php if($this->get_option('variationsShowPrice')){ ?>
		            <th><?php _e('Price', 'woocommerce') ?></th>
		            <?php } ?>

		            <?php if($this->get_option('variationsShowComment')){ ?>
		            <th><?php _e('Comment', 'woocommerce') ?></th>
		            <?php } ?>
	            </tr>
	        </thead>
	        <?php } ?>
			<tbody>
	        <?php 
	        $count = 0;
	        $variationLimitReached = false;
	        $variationsTableLimitReached = true;

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
	        		echo '<td valign="top"><table class="variation-table-split">';
	        		?>
		            <tr class="variation-head-row">
		            	<?php if($this->get_option('variationsShowImage')){ ?>
		                <th width="<?php echo $variations_image_size ?>px"><?php _e( 'Image', 'woocommerce-pdf-catalog' ); ?></th>
		                <?php } ?>

		            	<?php if($this->get_option('variationsShowSKU')){ ?>
		                <th><?php _e( 'SKU', 'woocommerce' ); ?></th>
		                <?php } ?>

		                <?php if($this->get_option('variationsShowDescription')){ ?>
		                <th><?php _e('Description', 'woocommerce') ?></th>
		                <?php } ?>

		                <?php 
		                if($this->get_option('variationsShowAttributes')){ 
		                	$variation_attributes = $this->product->get_variation_attributes();
		                	foreach ($variation_attributes as $key => $value) {
		                		echo '<th class="variation-attribute-head-' . $key . '">' . wc_attribute_label($key) . '</th>';
		                	}
	                	} ?>
	                	
			            <?php if($this->get_option('variationsShowPrice')){ ?>
			            <th><?php _e('Price', 'woocommerce') ?></th>
			            <?php } ?>

			            <?php if($this->get_option('variationsShowComment')){ ?>
			            <th><?php _e('Comment', 'woocommerce') ?></th>
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
	            	$variation_image = wp_get_attachment_image_src($variation_image_ID, 'full');
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

	                <?php if($this->get_option('variationsShowAttributes')){ ?>
		           		<?php foreach ($variation->get_variation_attributes() as $attr_name => $attr_value) : ?>
		                <td class="variations-attributes variation-attribute-value-<?php echo $attr_name ?>">
		                <?php
		                    // Get the correct variation values
		                    if (strpos($attr_name, '_pa_')){ // variation is a pre-definted attribute
		                        $attr_name = substr($attr_name, 10);
		                        $attr = get_term_by('slug', $attr_value, $attr_name);
		                        $attr_value = $attr->name;

		                        $attr_name = wc_attribute_label($attr_name);
		                    } else {
		                        $attr = maybe_unserialize( get_post_meta( $this->product->id, '_product_attributes' ) );
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
	        		echo '</table></td>';
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
	    	echo '<div class="variations-limit-reached"><a target="_blank" href="' . get_permalink($post->ID) . '">' . __('To see all Variations click here') . '</a></div>';
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

		ob_start();

		?>
		<table class="attributes">

			<?php if ( $product->has_weight() ) : $has_row = true; ?>
				<tr class="<?php if ( ( $alt = $alt * -1 ) === 1 ) echo 'alt'; ?>">
					<th><?php _e( 'Weight', 'woocommerce' ) ?></th>
					<td>
					<?php
						if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
							echo esc_html( wc_format_weight( $product->get_weight() ) );
						} else {
							echo $product->get_weight() . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) );
						}
					?></td>
				</tr>
			<?php endif; ?>

			<?php if ( $product->has_dimensions() ) : $has_row = true; ?>
				<tr class="<?php if ( ( $alt = $alt * -1 ) === 1 ) echo 'alt'; ?>">
					<th><?php _e( 'Dimensions', 'woocommerce' ) ?></th>
					<td>
					<?php
						if( version_compare( $woocommerce->version, '3.0.0', ">=" ) ) {
							echo esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) );
						} else {
							echo $product->get_dimensions(); 
						}
					?></td>
				</tr>
			<?php endif; ?>

			<?php foreach ( $attributes as $attribute ) :
				if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
					continue;
				} else {
					$has_row = true;
				}
				?>
				<tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>  attribute-other">
					<th><?php echo wc_attribute_label( $attribute['name'] ); ?></th>
					<td><?php
						if ( $attribute['is_taxonomy'] ) {

							$values = wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'names' ) );
							echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

						} else {

							// Convert pipes to commas and display values
							$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

						}
					?></td>
				</tr>
			<?php endforeach; ?>

		</table>
		<?php
		if ( $has_row ) {
			return ob_get_clean();
		} else {
			ob_end_clean();
		}
	}

	public function get_placeholder()
	{
		$productsImageSize = $this->get_option('productsImageSize');
		$productsImageSizeColumn = $this->get_option('productsImageSizeColumn');

		$html = '<table class="product-container">';
			$html .= '<tr class="product-container-row">';
			$html .= '<td class="product-image-container" valign="top" width="' . $productsImageSizeColumn . '">';
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

					$thumbnail = wp_get_attachment_image_src( $attachment_id, 'shop_single' ); 

					$src = $thumbnail[0];

					$gallery_image = '<img width="' . $galleryImageSize . 'px" src="' . $src . '" >';

					$image_class = esc_attr( implode( ' ', $classes ) );

					echo sprintf( '<td valign="top" class="%s">%s</td>', $image_class, $gallery_image);

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
		$showTags = $this->get_option('showTags');
		$showQR = $this->get_option('showQR');
		$showBarcode = $this->get_option('showBarcode');

		$productsBackgroundColor = $this->get_option('productsBackgroundColor');
		$productsImageSize = $this->get_option('productsImageSize');
		$productsImageSizeColumn = $this->get_option('productsImageSizeColumn');

		$productsHeadingsFontSize = $this->get_option('productsHeadingsFontSize');
		$productsHeadingsFontFamily = $this->get_option('productsHeadingsFontFamily') ? $this->get_option('productsHeadingsFontFamily') : 'dejavusans';
		$productsFontSize = $this->get_option('productsFontSize');

		$productsHeadingsLineHeight = $this->get_option('productsHeadingsLineHeight');
		$productsLineHeight = $this->get_option('productsLineHeight');

		$productsTextAlign = $this->get_option('productsTextAlign');
		$productsTextColor = $this->get_option('productsTextColor');

		$featured_image = '<img class="product-image" width="' . $productsImageSize . 'px" src="' . $this->data->src . '" >';

		$indexKey = $this->get_option('indexKey') ? $this->get_option('indexKey') : 'title';
		$html = '<indexentry content="' . htmlspecialchars( $this->data->$indexKey, ENT_QUOTES ) . '" />';

		if($showImage) { 
			$html .= '<div class="product-image-container fl col-width col-image" valign="top" width="' . $productsImageSizeColumn . '">';
			if($showLinkOnImage) {
				$html .= '<a class="product-image-link" href="' . get_permalink($this->data->ID) . '">';
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
			$html .= '<div class="fl col-width col-title"><span class="product-title-name">' . $this->data->title . '</span></div>';
		}
		if($showShortDescription) {
			$html .= '<div class="fl col-width col-short-description"><span class="product-short-description">' . $this->data->short_description . '</span></div>';
		}
		if($showDescription) {
			$html .= '<div class="fl col-width col-description"><span class="product-description">' . $this->data->description . '</span></div>';
		}
		if($showAttributes) {
			$html .= '<div class="fl col-width col-attributes">' .  $this->get_attributes_table() . '&nbsp;</div>';
		}
		if($showPrice) {
			$html .= '<div class="fl col-width col-price"><span class="product-price">' . $this->data->price . '</span></div>';
		}
		if($showReadMore) {
			$html .= '<div class="fl col-width col-readmore"><a class="product-read-more" href="' . get_permalink($this->data->ID) . '">';
			$html .= __( 'Read More', 'woocommerce-pdf-catalog'  );
			$html .= '</a></div>';
		}
		if($showCategories) {
			$html .= '<div class="fl col-width col-categories"><span class="product-categories">' . $this->data->categories . '</span></div>';
		}
		if($showTags) {
			$html .= '<div class="fl col-width col-tags"><span class="product-tags">' . $this->data->tags . '</span></div>';
		}
		if($showQR) {
			$html .= '<div class="fl col-width col-qr"><barcode code="' . get_permalink($this->data->ID) . '" type="QR" class="qr-code" size="0.8" error="M" /></div>';
		}
		if($showBarcode) {
			$barcodeType = $this->get_option('barcodeType');
			$barcodeMetaKey = $this->get_option('barcodeMetaKey');
			$barcodeMetaValue = get_post_meta($this->data->ID, $barcodeMetaKey, true);
			if(!empty($barcodeMetaValue)) {
				$html .= '<div class="fl col-width col-barcode">';
				$html .= apply_filters('woocommerce_pdf_catalog_barcode', '<barcode code="' . $barcodeMetaValue . '" type="' . $barcodeType .'" class="barcode"  />', $barcodeMetaValue, $barcodeType);
				$html .= '</div>';
			}
		}
		
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
}