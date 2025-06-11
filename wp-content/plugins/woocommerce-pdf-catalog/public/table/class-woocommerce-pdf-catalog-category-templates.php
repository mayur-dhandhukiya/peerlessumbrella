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
	}

	/**
	 * Set Category Information
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://www.welaunch.io/en/product/woocommerce-pdf-catalog/
	 * @param   [type]                       $category [description]
	 */
    public function set_category($category)
    {
    	$this->category = $category;
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

    	// Set Table of Contents Enty
    	$level = 0;
    	if($this->category->parent !== 0) {
    		// $parentCategory = get_term($this->category->parent, 'product_cat');
    		$level = 1;
    	}
    	$html = '<tocentry content="' . htmlentities( $this->category->name ) . '" level="' . $level . '" />';

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
    	$html = '<table class="category-container">';
	    	$html .= '<tr>';
		    	if(isset($this->category->image)){
			    	$html .= '<td width="50%" >';
			    	$html .= '<img width="' . $this->categoryImageSize . '" src="' . $this->category->image .'">';
			    	$html .= '</td>';
					$html .= '<td width="50%" class="category-information-container">';
		    	} else {
		    		$html .= '<td width="100%" class="category-information-container">';
		    	}
		    	
		    	$html .= $this->get_category_title();
		    	$html .= $this->get_category_description();

		    	$html .= '</td>';
	    	$html .= '</tr>';
    	$html .= '</table>';

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
    	$html = '<table class="category-container">';
	    	$html .= '<tr>';
		    	$html .= '<td width="50%" class="category-information-container">';

		    	$html .= $this->get_category_title();
		    	$html .= $this->get_category_description();

		    	$html .= '</td>';
		    	if(isset($this->category->image)){
			    	$html .= '<td width="50%">';
			    	$html .= '<img width="' . $this->categoryImageSize . '" src="' . $this->category->image .'" style="text-align:right;">';
			    	$html .= '</td>';
		    	}
	    	$html .= '</tr>';
    	$html .= '</table>';

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
    	$html = '<table class="category-container">';
	    	$html .= '<tr>';
		    	$html .= '<td class="category-information-container">';

		    	$html .= $this->get_category_title();
		    	$html .= $this->get_category_description();

		    	$html .= '</td>';
	    	$html .= '</tr>';
    	$html .= '</table>';

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
    	$html .= '<table class="category-container">';
        	$html .= '<tr>';
            	$html .= '<td>';

            	$html .= $this->get_category_title();
            	$html .= $this->get_category_description();

            	$html .= '</td>';
        	$html .= '</tr>';
    	$html .= '</table>';
    	$html .= '<pagebreak />';

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
    	$html = '<table class="category-container" style="border: none; width: 100%;">';
	    	$html .= '<tr style="border: none;">';
		    	$html .= '<td class="category-information-container" style="background: transparent url(' . $this->category->image .'); background-image-opacity: 0.6; background-image-resize: 6;">';

		    	$html .= $this->get_category_title();
	    		$html .= $this->get_category_description();

		    	$html .= '</td>';
	    	$html .= '</tr>';
    	$html .= '</table>';

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
			$html .= '<br/><p class="category-description">' . $this->category->description . '</p>';
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