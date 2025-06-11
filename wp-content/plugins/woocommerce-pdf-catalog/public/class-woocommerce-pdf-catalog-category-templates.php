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
class WooCommerce_PDF_Catalog_Category_Templates extends WooCommerce_PDF_Catalog {

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
	 * category
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $category
	 */
	private $category;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options ) 
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
		$this->level = 0;

		$this->category = array();
		$this->category_id = 0;
		$this->level = 0;
		// $this->category->image = '';
		// $this->category->description = '';
	}

	/**
	 * Set Category Information
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @param   [type]                       $category [description]
	 */
    public function set_category($category_id)
    {
    	$this->category_id = $category_id;
    }

    public function get_level()
    {
    	return $this->level;
    }

    public function get_category_id()
    {
    	return $this->category_id;
    }

    /**
     * Get Category Template
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @param   [type]                       $template [description]
     * @return  [type]                                 [description]
     */
    public function get_template($template)
    {
    	$this->set_defaults();

    	// Set div of Contents Enty
    	if($this->category->parent !== 0) {
    		$parentCategory = get_term($this->category->parent, 'product_cat');
    		if($parentCategory->parent !== 0) {
    			$this->level = 2;
    		} else {
    			$this->level = 1;
    		}

    		if(isset($_GET['pdf-catalog']) && ( intval($_GET['pdf-catalog']) == $this->category->parent)) {
    			$this->level = 1;
			}
    	}

    	if(isset($_GET['pdf-catalog']) && $_GET['pdf-catalog'] == $this->category_id) {
    		$this->level = 0;
    	}



		if($this->get_option('ToCShowCategories')) {
    		$html = '<tocentry content="' . htmlentities( $this->category->name ) . '" level="' . $this->level . '" />';
    	}

    	if($this->get_option('enableCategory')) { 

    		$customLayout = get_term_meta( $this->category_id, 'woocommerce_pdf_catalog_category_layout', true );
    		if(!empty($customLayout)) {
    			$template = $customLayout;
    		}

	    	// Get Layout
			if($template == 1)
				$html .= $this->get_first_category_layout();

			if($template == 2)
				$html .= $this->get_second_category_layout();

			if($template == 3)
				$html .= $this->get_third_category_layout();

			if($template == 4)
				$html .= $this->get_fourth_category_layout();

			if($template == 5)
				$html .= $this->get_fifth_category_layout();

			if($template == 6)
				$html .= $this->get_sixth_category_layout();

			if($this->get_option('categoryTitlePagebreak')) {
				$html .= '<pagebreak />';
			}
		}

		return $html;
    }

    /**
     * Set the Defaults
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     */
    public function set_defaults()
    {
    	$categoryImageSizeType = $this->get_option('categoryImageSizeType');

		$taxonomy = "product_cat";
		if(isset($_GET['taxonomy']) && !empty($_GET['taxonomy'])) {
			$taxonomy = esc_attr($_GET['taxonomy']);
		}

		$this->category = get_term($this->category_id, $taxonomy);
		$this->category->description = apply_filters('woocommerce_pdf_catalog_category_description', $this->category->description, $this->category->term_id);

		$customCategoryDescription = get_term_meta( $this->category_id, 'woocommerce_pdf_catalog_description', true );
		if(!empty($customCategoryDescription)) {
			$this->category->description = $customCategoryDescription;
		}

		if($this->get_option('categoryHideDescription')) {
			$this->category->description = "";
		}

	    $customCategoryImage = get_term_meta( $this->category_id, 'woocommerce_pdf_catalog_cateory_image', true );
        if(!empty($customCategoryImage)) {
        	if(isset($customCategoryImage['url'])) {
	        	$this->category->image = $customCategoryImage['url'];
        	}
		}

		// Woo Standard Category Image fallback
		if(empty($this->category->image)) {
		    $thumbnail_id = get_term_meta( $this->category_id, 'thumbnail_id', true );
		    $image = wp_get_attachment_image_src( $thumbnail_id, $categoryImageSizeType);

		    if ( isset($image[0]) && !empty($image[0]) ) {
			    $this->category->image = $image[0];
			}
		}

		$globalImage = $this->get_option('categoryGlobalImage');

		if(isset($globalImage['url']) && !empty($globalImage['url'])) {
			$this->category->image = $globalImage['url'];
		}

		if($this->get_option('performanceUseImageLocally') && !empty($this->category->image)) {
		    $uploads = wp_upload_dir();
			$this->category->image = str_replace( $uploads['baseurl'], $uploads['basedir'], $this->category->image );
		}


        $this->categoryImageSize = $this->get_option('categoryImageSize');	
    }

    /**
     * Image left - Text Right
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
     * @return  [type]                       [description]
     */
    public function get_first_category_layout()
    {
    	$html = '<div class="category-container category-layout-one">';
	    	
		    	if(isset($this->category->image)){
			    	$html .= '<div class="category-image">';
			    		$html .= '<img width="' . $this->categoryImageSize . '" src="' . $this->category->image .'">';
			    	$html .= '</div>';
					$html .= '<div class="category-information-container">';
		    	} else {
		    		$html .= '<div class="category-information-container category-information-container-no-image">';
		    	}
				    	$html .= '<div class="category-information">';
					    	$html .= $this->get_category_title();
					    	$html .= $this->get_category_description();
						$html .= '</div>';		    	

		    	$html .= '</div>';
		    $html .= '<div class="clear"></div>';
	    	
    	$html .= '</div>';

		return $html;
	}

	/**
	 * Image Right - Text Left
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_second_category_layout()
    {
    	$html = '<div class="category-container category-layout-two">';

    			if(isset($this->category->image)){
					$html .= '<div class="category-information-container">';
				} else {
					$html .= '<div class="category-information-container category-information-container-no-image">';
				}
			    	$html .= '<div class="category-information">';
				    	$html .= $this->get_category_title();
				    	$html .= $this->get_category_description();
					$html .= '</div>';		
		    	$html .= '</div>';
		    	
		    	if(isset($this->category->image)){
			    	$html .= '<div class="category-image">';
			    		$html .= '<img width="' . $this->categoryImageSize . '" src="' . $this->category->image .'">';
			    	$html .= '</div>';
	    		}
	    	$html .= '<div class="clear"></div>';
    	$html .= '</div>';

		return $html;
	}

	/**
	 * Only Text
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_third_category_layout()
    {
    	$html = '<div class="category-container">';
	    	$html .= '<div class="category-information">';

	    	$html .= $this->get_category_title();
	    	$html .= $this->get_category_description();

	    	$html .= '</div>';
    	$html .= '</div>';

		return $html;
	}

	/**
	 * Big Category Image
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_fourth_category_layout()
    {
    	$html = '<div style="background-image: url(' . $this->category->image . '); height: 80%; width: 100%; background-image-resize: 6;"></div>';
    	$html .= '<div class="category-container">';
        	$html .= '<div class="category-information">';

            	$html .= $this->get_category_title();
            	$html .= $this->get_category_description();

        	$html .= '</div>';
    	$html .= '</div>';

		return $html;
	}

	/**
	 * Background Image and Text
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_fifth_category_layout()
    {
    	$html = '<div style="border: none; width: 100%;">';
	    	$html .= '<div class="category-container" style="background: transparent url(' . $this->category->image .'); background-image-opacity: 0.6; background-image-resize: 6;">';
	    		$html .= '<div class="category-information">';
			    	$html .= $this->get_category_title();
		    		$html .= $this->get_category_description();
    			$html .= '</div>';
	    	$html .= '</div>';
    	$html .= '</div>';

		return $html;
	}

	/**
	 * Big Category Image
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
    public function get_sixth_category_layout()
    {
    	
    	$html = '<div class="category-container category-layout-6">';

			$categoryLayout6Image = $this->get_option('categoryLayout6Image');

			if(isset($categoryLayout6Image['url']) && !empty($categoryLayout6Image['url'])) {
				$categoryLayout6Image = $categoryLayout6Image['url'];
			}

			if($this->get_option('performanceUseImageLocally') && !empty($categoryLayout6Image)) {
			    $uploads = wp_upload_dir();
				$categoryLayout6Image = str_replace( $uploads['baseurl'], $uploads['basedir'], $categoryLayout6Image );
			}

			if(!empty($categoryLayout6Image)) {
				$html .= 
				'<div class="category-header-container">
					<img class="category-header-logo" src="' . $categoryLayout6Image . '" alt="">
				</div>';
			}


        	$html .= '<div class="category-information">';

            	$html .= $this->get_category_title();
            	$html .= $this->get_category_description();

        	$html .= '</div>';
    	
    		$html .= '<div class="category-image-container"><img class="category-image" src="' . $this->category->image . '"></div>';
    	$html .= '</div>';

		return $html;
	}

	/**
	 * Get Category Description
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
	protected function get_category_description()
	{
		$html = '';
		if(!empty($this->category->description)) {
			$html .= '<p class="category-description">' . wpautop( do_shortcode( $this->category->description ) ) . '</p>';
		}

		return $html;
	}

	/**
	 * Get Category Title
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @return  [type]                       [description]
	 */
	protected function get_category_title()
	{
		$html = '';
		if(!empty($this->category->name)) {
			$html .= '<h1 class="category-title">' . $this->category->name . '</h1>';
		}

		return $html;
	}
}