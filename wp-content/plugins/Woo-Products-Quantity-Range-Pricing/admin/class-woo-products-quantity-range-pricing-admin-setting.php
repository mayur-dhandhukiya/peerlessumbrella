<?php

/**
 * The admin-specific Setting facility of the plugin.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin
 */

/**0
 * The admin-specific Setting facility of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/admin
 * @author     Akash Soni <soniakashc@gmail.com>
 */
class Woo_Products_Quantity_Range_Pricing_Admin_Setting {

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
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'admin_menu', array( $this, 'woo_products_quantity_range_pricing_setting' ) );

		add_action( 'admin_init', array( $this, 'display_options' ) );
	}

	/**
	 * That function are display all setting options.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function display_options() {

		if (isset($_FILES['woo_qrp_price'])) {
			$name = $_FILES['woo_qrp_price']['name'];
			$ext = strtolower(end(explode('.', $_FILES['woo_qrp_price']['name'])));
			$type = $_FILES['woo_qrp_price']['type'];
			$tmpName = $_FILES['woo_qrp_price']['tmp_name'];

            // check the file is a csv
			if ($ext === 'csv') {
				if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                    // necessary if a large csv file
					set_time_limit(0);

					$row = 0;

					while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {

						$col_count = count($data);
						if ($row > 0) {
							if (!empty($data)) {

								$i = 0;
								$sub_arr = array();
								$main_sub_arr = array();
								$product_sku = '';
								$enable = '';
								$label = '';
								$price = '';
								foreach ($data as $key => $value) {

									if ($key > 2) {										
										if ($i == 3) {
											$sub_arr['min_qty'] = $value;
										}
										if ($i == 4) {
											$sub_arr['max_qty'] = $value;
										}
										if ($i == 5) {
											$sub_arr['type'] = $value;
										}
										if ($i == 6) {
											if(!empty($value)){
												$sub_arr['regular_price'] = $value;	
											}											
										}
										if ($i ==7) {
											if(!empty($value)){
												$sub_arr['sales_price'] = $value;
											}
										}
										if ($i ==8) {
											if(!empty($value)){
												$sub_arr['price'] = $value;
												$price = $value;
											}
										}
										if ($i ==9) {
											if(!empty($value)){
												$sub_arr['cnd_price'] = $value;
											}
										}
										if ($i ==10) {
											if(!empty($value)){
												$sub_arr['sec_price'] = $value;
											}
										}	
										if ($i ==11) {
											if(!empty($value)){
												$sub_arr['sec_cnd_price'] = $value;
											}
										}										
										if ($i == 12) {
											if ($value == 'null') {
												$sub_arr['role'] = array();
											} else {
												$value = explode(',', $value);
												$sub_arr['role'] = $value;
											}											
											$main_sub_arr[] = $sub_arr;
											$sub_arr = array();
											$i = 2;
										}																	
									}
									$i++;
									if ($key == 0) {
										$product_sku = $value;
									}
									if ($key == 1) {
										$enable = $value;
									}
									if ($key == 2) {
										$label = $value;
									}
								
								}	
															
								global $wpdb;
								$product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $product_sku));
								if ($product_id ) {
									$product = wc_get_product( $product_id );
									if( $product->is_type( 'simple' ) ) {
										update_post_meta($product_id, '_as_quantity_range_pricing_values', $main_sub_arr);
										update_post_meta($product_id, '_as_quantity_range_pricing_enable', $enable);
										update_post_meta($product_id, '_as_quantity_range_pricing_label', $label);
										update_post_meta($product_id, '_regular_price', $price);
										update_post_meta($product_id, '_price', $price);
										
									}
									if( $product->is_type( 'variable' ) ) {
										$available_variations = $product->get_available_variations();
										if ( $available_variations ) {
											foreach ( $available_variations as $available_variation ) {
												$product_id = $available_variation['variation_id'];
												update_post_meta($product_id, '_as_quantity_range_pricing_values', $main_sub_arr);
												update_post_meta($product_id, '_as_quantity_range_pricing_enable', $enable);
												update_post_meta($product_id, '_as_quantity_range_pricing_label', $label);
												update_post_meta($product_id, '_regular_price', $price);
												update_post_meta($product_id, '_price', $price);
											}
										}
									}
								}
								
							}
						}
						$row++;

					}	
					fclose($handle);
				}
			}            
		}

		add_settings_section( 'plugin_setting', 'Plugin Setting', array(
			$this,
			'display_plugin_options_content',
		), 'quantity-range-pricing-setting' );

		add_settings_field( 'woo_qrp_enable', 'Enable Quantity Range Pricing facility?', array(
			$this,
			'plugin_enable_option',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_wpqrp_user_role', 'Select Which User Roles Quantity Range Pricing Show Visitor.', array(
			$this,
			'display_form_user_roles',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_border_size', 'Border Size', array(
			$this,
			'display_form_border_size',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_border_style', 'Border Style', array(
			$this,
			'display_form_border_style',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_border_color', 'Border Color', array(
			$this,
			'display_form_border_color',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_table_display', 'Table Display', array(
			$this,
			'display_form_table_display',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_text_align', 'Table Display', array(
			$this,
			'display_form_text_align',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_text_align', 'Text Align', array(
			$this,
			'display_form_text_align',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_title_bg_color', 'Title Background Color', array(
			$this,
			'display_form_title_bg_color',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_title_text_color', 'Title Text Color', array(
			$this,
			'display_form_title_text_color',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_price_bg_color', 'Price Background Color', array(
			$this,
			'display_form_price_bg_color',
		), 'quantity-range-pricing-setting', 'plugin_setting' );
		add_settings_field( 'as_price_text_color', 'Price Text Color', array(
			$this,
			'display_form_price_text_color',
		), 'quantity-range-pricing-setting', 'plugin_setting' );

		add_settings_field('as_price_csv_file', 'Import Price CSV File', array(
			$this,
			'get_setting_file_option_html',
		), 'quantity-range-pricing-setting', 'plugin_setting');

		register_setting( 'plugin_setting', 'woo_qrp_enable' );
		register_setting( 'plugin_setting', 'as_wpqrp_user_role' );
		register_setting( 'plugin_setting', 'as_border_size' );
		register_setting( 'plugin_setting', 'as_border_style' );
		register_setting( 'plugin_setting', 'as_border_color' );
		register_setting( 'plugin_setting', 'as_table_display' );
		register_setting( 'plugin_setting', 'as_text_align' );
		register_setting( 'plugin_setting', 'as_title_bg_color' );
		register_setting( 'plugin_setting', 'as_title_text_color' );
		register_setting( 'plugin_setting', 'as_price_bg_color' );
		register_setting( 'plugin_setting', 'as_price_text_color' );

	}

	/**
	 * That function are create submenu of quantity range pricing setting.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function woo_products_quantity_range_pricing_setting() {

		add_submenu_page( 'woocommerce',
			'Quantity Range Pricing Setting',
			'Quantity Range Pricing Setting',
			'manage_woocommerce',
			'quantity-range-pricing-setting',
			array( $this, 'woo_products_quantity_range_pricing_setting_options' )
		);
	}


	/**
	 * That function are show the setting option on page.
	 *
	 * Callback function for add_submenu_page (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function woo_products_quantity_range_pricing_setting_options() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>

			<?php settings_errors(); ?>
			<h1><?php esc_attr_e( 'Woo Quantity Range Pricing Setting' ); ?> </h1>

			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				settings_fields( 'plugin_setting' );

				do_settings_sections( 'quantity-range-pricing-setting' );

				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * That function are show sub text description of header section.
	 *
	 * Callback function for add_settings_section (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_plugin_options_content() {
		esc_attr_e( 'Plugin enable/disable and quantity range pricing table style settings.' );
	}

	/**
	 * That function are show checkbox field for plugin enable/disable option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function plugin_enable_option() {
		$woo_qrp_enable = get_option( 'woo_qrp_enable', false );
		?>
		<label class="switch">
			<input type="checkbox"
			<?php if ( 'on' === $woo_qrp_enable ) {
				echo 'checked'; } ?>
				name="woo_qrp_enable">
				<div class="slider round"></div>
			</label>
			<?php
		}

	/**
	 * That function are show number field for border size option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_border_size() {
		$as_border_size = get_option( 'as_border_size', '1' );
		?>
		<input type="number" min="0" value="<?php echo esc_attr( $as_border_size ); ?>" name="as_border_size"
		class="as_qrp_border_style  as_border_size"/>
		<?php
	}

	/**
	 * That function are show select field for border style option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_border_style() {
		$as_border_style = get_option( 'as_border_style', 'solid' );
		$border_styles   = array(
			'none',
			'hidden',
			'dotted',
			'dashed',
			'solid',
			'double',
			'groove',
			'ridge',
			'inset',
			'outset',
			'initial',
			'inherit',
		);
		$this->get_setting_option_html( $border_styles, $as_border_style, 'as_border_style' );
	}

	/**
	 * That function are show select user roles for visitor show quantity range price.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_user_roles() {
		global $wp_roles;
		$as_wpqrp_user_role = get_option( 'as_wpqrp_user_role', '' );
		if( empty( $as_wpqrp_user_role ) ) { $as_wpqrp_user_role = array(); }
		$wpqrp_user_roles   = array();
		foreach ( $wp_roles->roles as $key=>$value ) {
			$wpqrp_user_roles[$key] = $value['name'];
		}
		$this->get_multy_setting_option_html( $wpqrp_user_roles, $as_wpqrp_user_role, 'as_wpqrp_user_role' );
	}

	/**
	 * That function are show color field for border color option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_border_color() {
		$as_border_bg_color = get_option( 'as_border_color', '#4D82B1' );
		$this->get_setting_color_option_html( $as_border_bg_color, 'as_border_color' );
	}

	/**
	 * That function are show color field for title background color option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_title_bg_color() {
		$as_title_bg_color = get_option( 'as_title_bg_color', '#4D82B1' );
		$this->get_setting_color_option_html( $as_title_bg_color, 'as_title_bg_color' );
	}

	/**
	 * That function are show select field for table display option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_table_display() {
		$as_display = get_option( 'as_table_display', 'none' );
		$as_tables  = array( 'none', 'inline-table' );
		$this->get_setting_option_html( $as_tables, $as_display, 'as_table_display' );
	}

	/**
	 * That function are show select field for table text align option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_text_align() {
		$as_text_align = get_option( 'as_text_align', 'left' );
		$as_aligns     = array( 'left', 'right', 'center' );
		$this->get_setting_option_html( $as_aligns, $as_text_align, 'as_text_align' );
	}

	/**
	 * That function are show color field for title color option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_title_text_color() {
		$as_title_text_color = get_option( 'as_title_text_color', '#ffffff' );
		$this->get_setting_color_option_html( $as_title_text_color, 'as_title_text_color' );
	}

	/**
	 * That function are show color field for table price text background color option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_price_bg_color() {
		$as_price_bg_color = get_option( 'as_price_bg_color', '#eeeeee' );
		$this->get_setting_color_option_html( $as_price_bg_color, 'as_price_bg_color' );
	}

	/**
	 * That function are show color field for table price text color option.
	 *
	 * Callback function for add_settings_field (function).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_form_price_text_color() {
		$as_price_text_color = get_option( 'as_price_text_color', '#000000' );
		$this->get_setting_color_option_html( $as_price_text_color, 'as_price_text_color' );
	}

	/**
	 * That function are return option of setting values tag.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param   array $setting_values The array of setting values.
	 * @param   string $selected_value The string of selected setting value.
	 * @param   string $name_of_option The string of name and class of option.
	 */
	public function get_setting_option_html( $setting_values, $selected_value, $name_of_option ) {
		?>
		<select name="<?php echo esc_attr( $name_of_option ); ?>"
			class="<?php echo esc_attr( $name_of_option ); ?> woo_qrp_setting_option">
			<?php
			foreach ( $setting_values as $setting_value ) {
				echo '<option value="' . esc_attr( $setting_value ) . '" ';
				if ( $selected_value == $setting_value ) {
					echo 'selected';
				}
				echo ' >' . esc_attr( $setting_value ) . '</option>';
			}
			?>
			</select> <?php

		}

	/**
	 * That function are return multiple select option of setting values tag.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param   array $setting_values The array of setting values.
	 * @param   string $selected_value The string of selected setting value.
	 * @param   string $name_of_option The string of name and class of option.
	 */
	public function get_multy_setting_option_html( $setting_values, $selected_value, $name_of_option ) {
		?>
		<select name="<?php echo esc_attr( $name_of_option ); ?>[]"
			class="<?php echo esc_attr( $name_of_option ); ?> wpqrp-select-role woo_qrp_setting_option" multiple>
			<?php
			print_r($selected_value);
			foreach ( $setting_values as $key=>$setting_value ) {
				echo '<option value="' . esc_attr( $key ) . '" ';
				if ( in_array( $key, $selected_value ) ) {
					echo 'selected';
				}
				echo ' >' . esc_attr( $setting_value ) . '</option>';
			}
			?>
			</select> <?php

		}

	/**
	 * That function are return input tag of setting color value tag.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param   string $selected_value The string of selected setting value.
	 * @param   string $name_of_option The string of name and class of option.
	 */
	public function get_setting_color_option_html( $selected_value, $name_of_option ) {
		?>
		<input type="text" value="<?php echo esc_attr( $selected_value ); ?>"
		name="<?php echo esc_attr( $name_of_option ); ?>"
		class="as_color <?php echo esc_attr( $name_of_option ); ?> woo-color-field"/>
		<?php
	}

	/**
     * That function are return input tag of setting file value tag.
     *
     * @since    1.0.0
     * @access   public
     *
     * @param   string $selected_value The string of selected setting value.
     * @param   string $name_of_option The string of name and class of option.
     */
	public function get_setting_file_option_html() {
		?>

		<input type="file" name="woo_qrp_price">
		<?php
	}

}
