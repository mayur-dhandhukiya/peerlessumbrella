<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin
 * @author     Akash Soni <soniakashc@gmail.com>
 */
class Woo_Products_Quantity_Range_Pricing_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param    string $plugin_name The name of this plugin.
	 * @param    string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$woo_qrp_enable = get_option( 'woo_qrp_enable', false );

		if ( $woo_qrp_enable ) {

			// Add quantity pricing fields for simple products.
			add_action( 'woocommerce_product_options_general_product_data', array(
				$this,
				'woo_simple_product_quantity_range_prices_fields',
			) );

			// Save quantity pricing fields values for simple products.
			add_action( 'woocommerce_process_product_meta', array(
				$this,
				'woo_quantity_range_prices_fields_values_save',
			) );

			// Add quantity pricing fields for Variable products.
			add_action( 'woocommerce_product_after_variable_attributes', array(
				$this,
				'woo_variable_product_quantity_range_prices_fields',
			), 10, 3 );

			// Save quantity pricing fields values for variation products..
			add_action( 'woocommerce_save_product_variation', array(
				$this,
				'woo_variable_quantity_range_prices_fields_values_save',
			), 10, 2 );

			// Ajax action for get new quantity range price fiels HTML.
			add_action( 'wp_ajax_woo_get_new_quantity_range_price_fields', array(
				$this,
				'woo_get_new_quantity_range_price_fields',
			) );

            // Add the fields to the "presenters" taxonomy, using our callback function
            add_action( 'product_cat_edit_form_fields', array(
                $this,
                'presenters_taxonomy_custom_fields'
            ), 99, 2 );
            add_action( 'product_tag_edit_form_fields', array(
                $this,
                'presenters_taxonomy_custom_fields'
            ), 99, 2 );

            //Save the changes made on the product taxonomy, using our callback function
            add_action( 'edited_product_cat', array(
                $this,
                'save_taxonomy_custom_fields'
            ), 99, 2 );
            add_action( 'edited_product_tag', array(
                $this,
                'save_taxonomy_custom_fields'
            ), 99, 2 );

             add_action( 'admin_menu', array( 
	        	$this,
	        	'woo_admin_menu' 
	        ) );

		 add_filter( 'woocommerce_csv_product_post_columns', array( $this, 'as_woocommerce_csv_product_post_columns' ), 10, 1 );

		}
	}

	public function as_woocommerce_csv_product_post_columns( $post_columns ) {

		$post_columns['_as_quantity_range_pricing_values'] = 'quantity_range_pricing_values';
		$post_columns['_as_quantity_range_pricing_enable'] = 'quantity_range_pricing_enable';
		
		return $post_columns;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
		public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Products_Quantity_Range_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Products_Quantity_Range_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) .  'css/select2.min.css', array(),null);
		wp_enqueue_style($this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-products-quantity-range-pricing-admin.css', array(), $this->version, 'all' );

        // Import select2 css on admin side.
        // wp_enqueue_style( 'wc-enhanced-select' );
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Products_Quantity_Range_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Products_Quantity_Range_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//Import colopicker js on admin side option page.
		wp_enqueue_style( 'wp-color-picker' );

        //Import select2 js on admin side taxonomy page.
        // wp_enqueue_script( 'wc-enhanced-select' );
        wp_enqueue_script( 'woo-products-select2-min-js', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array('jquery'), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-products-quantity-range-pricing-admin.js', array(
			'jquery',
			'wp-color-picker',
		), $this->version, false );

		// Create as_woo_pricing object ajax url variable in admin side for ajax request.
		wp_localize_script( $this->plugin_name, 'as_woo_pricing',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

	}

	public function woo_admin_menu() {
		add_submenu_page( 'woocommerce',
			'Price Import Facility',
			'Price Import Facility',
			'manage_woocommerce',
			'woo-import-option',
			array( $this, 'woo_option_import' )
		);
	}

	public function woo_option_import(){

		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>

			<?php settings_errors(); ?>
			<h1><?php esc_attr_e( 'WMVATC Import Facility' ); ?> </h1>

			<form method="post" action="#" enctype="multipart/form-data">
				<table class="form-table">
					<tbody>	
						<tr valign="top">
							<th scope="row" class="titledesc"><label for="woocommerce_myaccount_downloads_endpoint">Select CSV File</label></th>
							<td class="forminp forminp-text"><input type="file" name="qty_csv"  /><p>Here you import quantity range price field data. For sample CSV file <a href="<?php echo plugin_dir_url( __FILE__ )  ?>upload/variation_range_data.csv">Click here</a></p></td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input name="save" class="button-primary woocommerce-save-button" value="Save changes" type="submit">
				</p>	
			</form>
		</div>
		<?php
		
		if( ! empty( $_FILES['qty_csv'] ) && isset( $_FILES['qty_csv'] ) ) {
			if( ! empty( $_FILES['qty_csv']['tmp_name']) ){
				$fh = fopen($_FILES['qty_csv']['tmp_name'], 'r+');
				$lines = array();
				while( ($row = fgetcsv($fh, 8192)) !== FALSE ) {
					$lines[] = $row;
				}
				$data_pro = array();
				if( count( $lines ) > 1 ) {				
					unset($lines[0]);
					foreach ( $lines as $key => $values ) {	
						$n = 1;
						foreach ( $values as $value ) {
							if ( $n === 1 ) {
								echo $value;
							} else {
								echo 'd';
								print_r( json_decode( $value ) );
							}
							$n++;
							
						}
						/*$data_v = array();
						$data_v['min_qty']	= $value[1];
						$data_v['max_qty'] 	= $value[2];
						$data_v['type'] 	= $value[3];
						$data_v['price'] 	= $value[4];
						$data_v['role'] 	= $value[5];
						$data_pro[$value[0]][] = $data_v;*/
					}
					/*if( count( $data_pro ) > 0 ) {
						foreach( $data_pro as $product_id => $product_value ) {
							update_post_meta( $product_id, '_as_quantity_range_pricing_values', $product_value );
							update_post_meta( $product_id, '_as_quantity_range_pricing_enable', $value[6] );
							
						}
					}*/
					echo '<p class="wmvatc-done">Quantity Range Data Successfully Imported.</p>';
				}else{
					echo '<p class="wmvatc-error">No Data Read In File.</p>';
				}
			}	
		}
		
		
	}

    /**
     * This function provide add shipping data facility in taxonomy product edit page.
     *
     * @since  1.0.0
     * @return string
     */
    public function presenters_taxonomy_custom_fields( $tag ) {

        $value_id                   = $tag->term_id;
        $wpp_wpqrp_category_products = get_term_meta( $value_id, '_as_quantity_range_category_products', true );
        $screen                     = get_current_screen();
        $taxonomy_slug              = $screen->taxonomy;
        $args                       = array(
            'post_type'      => 'product',
            'posts_per_page' => - 1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'term_id',
                    'terms'    => array( $value_id )
                )
            )
        );
        if ( 'product_tag' === $taxonomy_slug ) {
            $args['tax_query'][0]['taxonomy'] = 'product_tag';
        } elseif ( 'product_cat' === $taxonomy_slug ) {
            $args['tax_query'][0]['taxonomy'] = 'product_cat';
        }
        // Get all product of this terms.
        $wpqrp_products     = get_posts( $args );
        $data_have         = false;
        $simple_show_class = '';

        if ( empty( $woo_qrp_enable ) ) {
            echo '<style>.wpqrp-box-' . esc_attr( $value_id ) . '{ display:none !important; }</style>';
        }

        echo '<table class="wpqrp-form-table"><tr><td><div class="options_group wpqrp-box">';
        ob_start();

        $this->woo_product_quantity_range_prices_fields( $value_id, 'term' );

        $wpqrp_shiiping_fields = ob_get_contents();
        ob_end_clean();
        echo $wpqrp_shiiping_fields;
        echo '<div class="wpqrp-select-category-product-box as-box-' . $value_id . '">';
        esc_attr_e( 'Select Product :', 'woo-advanced-product-shipping' );
        echo '<select class="wpqrp_chosen_select wpqrp-select-products" name="woo-quantity-range-shpping-product-' . $value_id . '[]" multiple >';
        foreach ( $wpqrp_products as $wpqrp_product ) {
            $selected = '';
            if ( in_array( $wpqrp_product->ID, $wpp_wpqrp_category_products ) ) {
                $selected = 'selected';
            }
            echo '<option value="' . $wpqrp_product->ID . '" ' . $selected . ' >' . $wpqrp_product->post_title . '</option>';
        }
        echo '</select>';
        echo '<div class="wpqrp-note">
                    <span class="wpqrp-red">';
        esc_attr_e( 'Note : Select which product apply this term quantity range price. Please do not add one product in multiple terms.', 'woo-advanced-product-shipping' );
        echo '</span>                
                  </div>';
        echo '</div>';
        echo '</div>';

        echo '</td></tr></table>';

    }
	/**
	 * Add quantity range price fields in product.
	 *
	 * Callback function for woocommerce_product_after_variable_attributes (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    int $value_id uniqe id of table.
	 * @param    string $product_type type of product simaple/variation.
	 */
	public function woo_product_quantity_range_prices_fields( $value_id, $product_type ) {

		if( 'term' === $product_type ) {
            // Get quantity range pricing values in product.
            $as_quantity_rage_values = get_term_meta( $value_id, '_as_quantity_range_pricing_values', true );
            // Get quantity range pricing enable / disable status.
            $woo_qrp_enable    = get_term_meta( $value_id, '_as_quantity_range_pricing_enable', true );
            $woo_qrp_label    = get_term_meta( $value_id, '_as_quantity_range_pricing_label', true );
            
        } else {
            // Get quantity range pricing values in product.
            $as_quantity_rage_values = get_post_meta( $value_id, '_as_quantity_range_pricing_values', true );

            if ( ! is_array( $as_quantity_rage_values ) && ! empty( $as_quantity_rage_values ) ) {
            	$as_quantity_rage_values = json_decode( $as_quantity_rage_values );
            }
            // Get quantity range pricing enable / disable status.
            $woo_qrp_enable    = get_post_meta( $value_id, '_as_quantity_range_pricing_enable', true );
            $woo_qrp_label    = get_post_meta( $value_id, '_as_quantity_range_pricing_label', true );
        }
		$wqrp_style        = '';
		$simple_show_class = '';
        $quantity_value    = true;
		if ( empty( $woo_qrp_enable ) ) {
			echo '<style>.as-box-' . esc_attr( $value_id ) . '{ display:none !important; }</style>';
		}

		if ( 'simple' === $product_type ) {
			$simple_show_class = 'show_if_simple';
		}

		ob_start();
		if ( ! empty( $as_quantity_rage_values ) ) {
			$number                  = 0;
			$as_quantity_rage_values = $this->woo_quantity_value_sorting_by_order( $as_quantity_rage_values );
			foreach ( $as_quantity_rage_values as $as_quantity_rage_value ) {
				if ( is_array( $as_quantity_rage_value ) ) {
					if ( ! empty( $as_quantity_rage_value['min_qty'] ) && ! empty( $as_quantity_rage_value['max_qty'] ) ) {
	                    if( empty( $as_quantity_rage_value['role'] ) ) { $as_quantity_rage_value['role'] = ''; }
						$this->woo_quantity_range_prices_fields_html( $as_quantity_rage_value['display_blank_price'], $as_quantity_rage_value['min_qty'], $as_quantity_rage_value['max_qty'], $as_quantity_rage_value['type'], $as_quantity_rage_value['regular_price'],$as_quantity_rage_value['sales_price'],$as_quantity_rage_value['special_price'], $as_quantity_rage_value['price'], $as_quantity_rage_value['cnd_price'], $as_quantity_rage_value['sec_price'], $as_quantity_rage_value['sec_cnd_price'], $as_quantity_rage_value['role'], $number, $value_id );
						$number ++;
	                    $quantity_value = false;
					}
				} else {
					if ( ! empty( $as_quantity_rage_value->min_qty ) && ! empty( $as_quantity_rage_value->max_qty ) ) {
	                    if( empty( $as_quantity_rage_value->role ) ) { $as_quantity_rage_value->role = ''; }
						$this->woo_quantity_range_prices_fields_html( $as_quantity_rage_value->display_blank_price, $as_quantity_rage_value->min_qty, $as_quantity_rage_value->max_qty, $as_quantity_rage_value->type,$as_quantity_rage_value->regular_price,$as_quantity_rage_value->sales_price,$as_quantity_rage_value->special_price, $as_quantity_rage_value->price, $as_quantity_rage_value->cnd_price, $as_quantity_rage_value->sec_price, $as_quantity_rage_value->sec_cnd_price, $as_quantity_rage_value->role, $number, $value_id );
						$number ++;
	                    $quantity_value = false;
					}
				}
				
			}

		}
		if( $quantity_value ){
			$this->woo_quantity_range_prices_fields_html( '','', '', 'percentage', 0,0,0,0, 0, '','', '', 0, $value_id );

		}
		$quantity_wish_pricing_fields = ob_get_contents();
		ob_end_clean();

	 include 'partials/admin-quantity-range-pricing-options.php';

	}

    public function save_taxonomy_custom_fields( $term_id ) {


        if ( isset( $_POST[ 'as_woo_pricing_' . $term_id ] ) ):

            // Save quantity range pricing values in product.
            update_term_meta( $term_id, '_as_quantity_range_pricing_values', $_POST[ 'as_woo_pricing_' . $term_id ] );

        endif;

        // Save quantity range pricing facility status in product.
        update_term_meta( $term_id, '_as_quantity_range_pricing_enable', $_POST[ 'woo-qrp-enable-' . $term_id ] );

        update_term_meta( $term_id, '_as_quantity_range_category_products', $_POST[ 'woo-quantity-range-shpping-product-' . $term_id ] );
        update_term_meta( $term_id, '_as_quantity_range_pricing_label', $_POST[ 'woo-qrp-label-' . $term_id ] );
    }
	/**
	 * Return quantity range price fields HTML in simple product.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param number $min_quantity Product minimum quantity.
	 * @param number $max_quantity Product maximum quantity.
	 * @param string $type Product price type.
	 * @param number $price Product quantity range price.
	 * @param number $number Product quantity range price filed number.
	 * @param int $value_id Product unique ID.
	 */
	public function woo_quantity_range_prices_fields_html( $display_blank_price = '',$min_quantity = 0, $max_quantity = 0, $type = 'percentage', $regular_price = 0, $sales_price = 0, $special_price = 0, $price = 0, $cnd_price = 0, $sec_price = 0, $sec_cnd_price = 0, $wpqrp_roles = '', $number = 0, $value_id ) {
        global $wp_roles;
		/**
		 * This function is provided HTML of quantity range price field.
		 */
		$type_values = array(
			'Percentage discount' => 'percentage',
			'Price discount'      => 'price',
			'Selling price'       => 'fixed',
		);
		// Include HTML of quantity range pricing fileds.
		 include 'partials/admin-quantity-range-pricing-fields.php';
	}

	/**
	 * Add quantity range price fields in simple product.
	 *
	 * Callback function for woocommerce_product_options_general_product_data (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function woo_simple_product_quantity_range_prices_fields() {

		/**
		 * This function is provided quantity range price fields on admin product page.
		 *
		 * This price fields for simple product pricing.
		 */

		global $post;

		$this->woo_product_quantity_range_prices_fields( $post->ID, 'simple' );

	}

	/**
	 * Save quantity range price fields values of simple product.
	 *
	 * Callback function for woocommerce_process_product_meta (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    number $post_id Product ID.
	 */
	public function woo_quantity_range_prices_fields_values_save( $post_id ) {
		// Check quantity pricing values havent
		if ( isset( $_POST[ 'as_woo_pricing_' . $post_id ] ) ):

			// Save quantity range pricing values in product.
			update_post_meta( $post_id, '_as_quantity_range_pricing_values', $_POST[ 'as_woo_pricing_' . $post_id ] );

		endif;

		// Save quantity range pricing facility status in product.
		update_post_meta( $post_id, '_as_quantity_range_pricing_enable', $_POST[ 'woo-qrp-enable-' . $post_id ] );
		update_post_meta( $post_id, '_as_quantity_range_pricing_label', $_POST[ 'woo-qrp-label-' . $post_id ] );
	}

	/**
	 * Save quantity range price fields values of variation product.
	 *
	 * Callback function for woocommerce_save_product_variation (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    number $variation_id Variation ID.
	 */
	public function woo_variable_quantity_range_prices_fields_values_save( $variation_id ) {

		// Check quantity pricing values havent.
		if ( isset( $_POST[ 'as_woo_pricing_' . $variation_id ] ) ):

			// Save quantity range pricing values in product.
			update_post_meta( $variation_id, '_as_quantity_range_pricing_values', $_POST[ 'as_woo_pricing_' . $variation_id ] );

		endif;

		// Save quantity range pricing facility status in product.
		update_post_meta( $variation_id, '_as_quantity_range_pricing_enable', $_POST[ 'woo-qrp-enable-' . $variation_id ] );
		update_post_meta( $variation_id, '_as_quantity_range_pricing_label', $_POST[ 'woo-qrp-label-' . $variation_id ] );

	}

	/**
	 * Get new quantity range price fields HTML.
	 *
	 * Callback function for wp_ajax_nopriv_woo_get_new_quantity_range_price_fields (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function woo_get_new_quantity_range_price_fields() {

		$row_id   = $_POST['row_id'];
		$value_id = $_POST['value_id'];

		$this->woo_quantity_range_prices_fields_html( '','', '', 'percentage', 0,0,0,0,0, '','', '', $row_id, $value_id );

		die();

	}

	/**
	 * Add quantity range price fields in variable product.
	 *
	 * Callback function for woocommerce_product_after_variable_attributes (action).
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param    boolean $loop This is callback value true/false.
	 * @param    array $variation_data This array is variation post meta data.
	 * @param    array $variation This array is variation post data.
	 */
	public function woo_variable_product_quantity_range_prices_fields( $loop, $variation_data, $variation ) {

		/**
		 * This function is provided quantity range price fields on admin product page.
		 *
		 * This price fields for variable product pricing.
		 */

		$this->woo_product_quantity_range_prices_fields( $variation->ID, 'variation' );

	}

	/**
	 * This function are shorting the array of quantity range pricing values.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return array
	 *
	 * @param    array $as_quantity_rage_values This array is quantity range pricing values.
	 */
	public static function woo_quantity_value_sorting_by_order( $as_quantity_rage_values ) {

		$sortArray = array();
		if ( ! is_array( $as_quantity_rage_values ) && ! empty( $as_quantity_rage_values ) ) {
            	$as_quantity_rage_values = json_decode( $as_quantity_rage_values );
            }
		if( $as_quantity_rage_values ){

			$as_quantity_rage_valuesd = array();
			foreach ( $as_quantity_rage_values as $as_quantity_rage_v ) {
				if ( is_array( $as_quantity_rage_v ) ) {
						$as_quantity_rage_value = $as_quantity_rage_v;
					} else {
						$as_quantity_rage_value = array(
							'min_qty' => $as_quantity_rage_v->display_blank_price,
							'min_qty' => $as_quantity_rage_v->min_qty,
							'max_qty' => $as_quantity_rage_v->max_qty,
							'role' => $as_quantity_rage_v->role,
							'price' => $as_quantity_rage_v->price,
							'cnd_price' => $as_quantity_rage_v->cnd_price,
							'sec_price' => $as_quantity_rage_v->sec_price,
							'sec_cnd_price' => $as_quantity_rage_v->sec_cnd_price,
							'type' => $as_quantity_rage_v->type,
							);	
					}
					$as_quantity_rage_valuesd[] = $as_quantity_rage_value;
			}
			foreach ( $as_quantity_rage_valuesd as $as_quantity_rage_v ) {
					
				foreach ( $as_quantity_rage_v as $key => $value ) {
					if ( ! isset( $sortArray[ $key ] ) ) {
						$sortArray[ $key ] = array();
					}
					$sortArray[ $key ][] = $value;
				}
			}

			$orderby = "min_qty";

			array_multisort( $sortArray[ $orderby ], SORT_ASC, $as_quantity_rage_valuesd );
		}

		return $as_quantity_rage_values;

	}
}
