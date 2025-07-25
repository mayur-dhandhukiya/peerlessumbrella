<?php
/**
 * Static singleton class for options functions.
 *
 * @package Woodmart
 */

namespace XTS\Admin\Modules;

use XTS\Admin\Modules\Options\Presets;
use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Create options, register sections and fields.
 */
class Options extends Singleton {
	/**
	 * Options values array from the database.
	 *
	 * @var array
	 */
	private static $_options = array(); // phpcs:ignore

	/**
	 * Array of the options default values.
	 *
	 * @var array
	 */
	private static $_default_options = array(); // phpcs:ignore

	/**
	 * Fields objects array.
	 *
	 * @var array
	 */
	private static $_fields = array(); // phpcs:ignore

	/**
	 * Sections array.
	 *
	 * @var array
	 */
	private static $_sections = array(); // phpcs:ignore

	/**
	 * Key for options to be override from meta values.
	 *
	 * @var array
	 */
	private static $_override_options = array(); // phpcs:ignore

	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public static $opt_name = 'woodmart';

	/**
	 * Presets IDs.
	 *
	 * @var array
	 */
	private $_presets = false; // phpcs:ignore

	/**
	 * Array of field type and controls mapping.
	 *
	 * @var array
	 */
	public static $_controls_classes = array(
		'select'            => 'XTS\Admin\Modules\Options\Controls\Select',
		'text_input'        => 'XTS\Admin\Modules\Options\Controls\Text_Input',
		'switcher'          => 'XTS\Admin\Modules\Options\Controls\Switcher',
		'color'             => 'XTS\Admin\Modules\Options\Controls\Color',
		'checkbox'          => 'XTS\Admin\Modules\Options\Controls\Checkbox',
		'buttons'           => 'XTS\Admin\Modules\Options\Controls\Buttons',
		'upload'            => 'XTS\Admin\Modules\Options\Controls\Upload',
		'upload_list'       => 'XTS\Admin\Modules\Options\Controls\Upload_List',
		'background'        => 'XTS\Admin\Modules\Options\Controls\Background',
		'textarea'          => 'XTS\Admin\Modules\Options\Controls\Textarea',
		'image_dimensions'  => 'XTS\Admin\Modules\Options\Controls\Image_Dimensions',
		'typography'        => 'XTS\Admin\Modules\Options\Controls\Typography',
		'custom_fonts'      => 'XTS\Admin\Modules\Options\Controls\Custom_Fonts',
		'range'             => 'XTS\Admin\Modules\Options\Controls\Range',
		'editor'            => 'XTS\Admin\Modules\Options\Controls\Editor',
		'import'            => 'XTS\Admin\Modules\Options\Controls\Import',
		'notice'            => 'XTS\Admin\Modules\Options\Controls\Notice',
		'instagram_api'     => 'XTS\Admin\Modules\Options\Controls\Instagram_Api',
		'reset'             => 'XTS\Admin\Modules\Options\Controls\Reset',
		'sorter'            => 'XTS\Admin\Modules\Options\Controls\Sorter',
		'select_with_table' => 'XTS\Admin\Modules\Options\Controls\Select_With_Table',
		'responsive_range'  => 'XTS\Admin\Modules\Options\Controls\Responsive_Range',
		'icons_font'        => 'XTS\Admin\Modules\Options\Controls\Icons_Font',
		'dimensions'        => 'XTS\Admin\Modules\Options\Controls\Dimensions',
		'group'             => 'XTS\Admin\Modules\Options\Controls\Group',
		'conditions'        => 'XTS\Admin\Modules\Options\Controls\Conditions',
		'discount_rules'    => 'XTS\Admin\Modules\Options\Controls\Discount_Rules',
		'timetable'         => 'XTS\Admin\Modules\Options\Controls\Timetable',
	);

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->include_files();

		if ( ! is_admin() ) {
			add_action( 'wp', array( $this, 'set_custom_meta_for_post' ), 500 );
			add_action( 'wp', array( $this, 'set_options_for_post' ), 505 );
			add_action( 'wp', array( $this, 'specific_options' ), 510 );
			add_action( 'wp', array( $this, 'specific_taxonomy_options' ), 515 );
			add_action( 'wp', array( $this, 'specific_taxonomy_options' ), 40 );
		}

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'load_presets' ), 50 );

		add_action( 'wp', array( $this, 'load_defaults' ), 100 );
		add_action( 'wp', array( $this, 'load_options' ), 110 );
		add_action( 'wp', array( $this, 'load_presets' ), 200 );
		add_action( 'wp', array( $this, 'override_options_from_meta' ), 300 );
		add_action( 'wp', array( $this, 'setup_globals' ), 400 );

		add_action( 'init', array( $this, 'load_defaults' ), 100 );
		add_action( 'init', array( $this, 'load_options' ), 110 );
		add_action( 'init', array( $this, 'load_presets' ), 200 );
		add_action( 'init', array( $this, 'override_options_from_meta' ), 300 );
		add_action( 'init', array( $this, 'setup_globals' ), 400 );

		$this->load_defaults();
		$this->load_options();
		$this->load_presets();
		$this->override_options_from_meta();
		$this->setup_globals();
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		$classes_files = array(
			'class-google-fonts',
			'class-metabox',
			'class-metaboxes',
			'class-field',
			'class-presets',
			'class-sanitize',
			'class-page',
		);

		foreach ( $classes_files as $file ) {
			require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/admin/modules/options/' . $file . '.php' );
		}

		foreach ( self::$_controls_classes as $key => $class_name ) {
			$control_name = str_replace( '_', '-', $key );

			require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/admin/modules/options/controls/' . $control_name . '/class-' . $control_name . '.php' );
		}
	}

	/**
	 * Specific options
	 */
	public function set_options_for_post() {
		global $woodmart_options;

		$custom_options = json_decode( get_post_meta( woodmart_page_ID(), '_woodmart_options', true ), true );

		if ( ! empty( $custom_options ) ) {
			$woodmart_options = wp_parse_args( $custom_options, $woodmart_options );
		}

		$woodmart_options = apply_filters( 'woodmart_global_options', $woodmart_options );
	}


	/**
	 * [set_custom_meta_for_post description]
	 */
	public function set_custom_meta_for_post() {
		global $xts_woodmart_options, $woodmart_transfer_options, $woodmart_prefix;
		if ( ! empty( $woodmart_transfer_options ) ) {
			foreach ( $woodmart_transfer_options as $field ) {
				$meta = get_post_meta( woodmart_page_ID(), $woodmart_prefix . $field, true );
				if ( isset( $xts_woodmart_options[ $field ] ) ) {
					$xts_woodmart_options[ $field ] = ( isset( $meta ) && $meta != '' && $meta != 'inherit' && $meta != 'default' ) ? $meta : $xts_woodmart_options[ $field ];
				}
			}
		}
	}

	/**
	 * Specific options dependencies
	 */
	public function specific_options() {
		global $xts_woodmart_options;

		$rules = woodmart_get_config( 'specific-options' );

		foreach ( $rules as $option => $rule ) {
			if ( ! empty( $rule['will-be'] ) && ! isset( $rule['if'] ) ) {
				$xts_woodmart_options[ $option ] = $rule['will-be'];
			} elseif ( isset( $xts_woodmart_options[ $rule['if'] ] ) && in_array( $xts_woodmart_options[ $rule['if'] ], $rule['in_array'] ) ) {
				$xts_woodmart_options[ $option ] = $rule['will-be'];
			}
		}
	}


	/**
	 * Specific options for taxonomies
	 */
	public function specific_taxonomy_options() {
		global $xts_woodmart_options;

		if ( is_category() ) {
			$option_key       = 'blog_design';
			$category         = get_query_var( 'cat' );
			$current_category = get_category( $category );
			// $current_category->term_id;
			$category_blog_design = get_term_meta( $current_category->term_id, '_woodmart_' . $option_key, true );

			if ( ! empty( $category_blog_design ) && $category_blog_design != 'inherit' ) {
				$xts_woodmart_options[ $option_key ] = $category_blog_design;
			}
		}
	}

	/**
	 * Load options array from the database options table.
	 *
	 * @since 1.0.0
	 */
	public function load_options() {
		if ( get_option( 'xts-' . self::$opt_name . '-options' ) ) {
			self::$_options = get_option( 'xts-' . self::$opt_name . '-options' );
		}
	}

	/**
	 * Get active presets and load their options.
	 *
	 * @since 1.0.0
	 *
	 * @param string $preset_id Preset id.
	 */
	public function load_presets( $preset_id = '' ) {
		$presets        = Presets::get_active_presets();
		$this->_presets = ! empty( $presets ) ? $presets : false;

		if ( $preset_id && ! is_object( $preset_id ) ) {
			$this->_presets = array( $preset_id );
		}

		if ( ! $this->_presets ) {
			return;
		}

		$_options = apply_filters( 'xts_presets_options', self::$_options );

		foreach ( $this->_presets as $preset_id ) {
			if ( isset( $_options[ $preset_id ] ) && is_array( $_options[ $preset_id ] ) ) {
				foreach ( $_options[ $preset_id ] as $key => $value ) {
					self::$_options[ $key ] = $value;
				}
			}
		}
	}

	/**
	 * Load default options from Field objects arguments.
	 *
	 * @since 1.0.0
	 */
	public function load_defaults() {
		foreach ( self::$_fields as $field ) {
			if ( ! isset( $field->args['default'] ) ) {
				continue;
			}

			if ( ! isset( self::$_options[ $field->get_id() ] ) ) {
				self::$_options[ $field->get_id() ] = $field->args['default'];
			}
		}
	}

	/**
	 * Get options array.
	 *
	 * @return array Options array stored in the database.
	 *
	 * @since 1.0.0
	 */
	public static function get_options() {
		return self::$_options;
	}

	/**
	 * Setup global variable for this options set.
	 *
	 * @since 1.0.0
	 */
	public function setup_globals() {
		$GLOBALS[ 'xts_' . self::$opt_name . '_options' ] = self::$_options;
	}

	/**
	 * Register settings hook callback.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting( 'xts-options-group', 'xts-' . self::$opt_name . '-options', array( 'sanitize_callback' => array( $this, 'sanitize_before_save' ) ) );
	}

	/**
	 * Static method to add a section to the array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $section Arguments array for new section.
	 */
	public static function add_section( $section ) {
		self::$_sections[ $section['id'] ] = $section;
	}

	/**
	 * Static method t add a field object based on arguments array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args New field object arguments.
	 */
	public static function add_field( $args ) {
		$control_classname = self::$_controls_classes[ $args['type'] ];

		if ( ! isset( self::$_options[ $args['id'] ] ) ) {
			self::$_options[ $args['id'] ] = self::get_default( $args );
		}

		$field = new $control_classname( $args, self::$_options );

		self::$_fields[ $args['id'] ] = $field;
	}

	/**
	 * Get field default value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Args array for the field.
	 *
	 * @return mixed|string
	 */
	public static function get_default( $args ) {
		$value = '';

		if ( isset( $args['default'] ) ) {
			$value = $args['default'];
		}

		return $value;
	}

	/**
	 * Add option key to be overriden from meta value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $key Key for the option to override.
	 */
	public static function register_meta_override( $key ) {
		self::$_override_options[] = $key;
	}

	/**
	 * Override options from meta values.
	 *
	 * @since 1.0.0
	 */
	public static function override_options_from_meta() {
		foreach ( self::$_override_options as $key ) {
			$meta = get_post_meta( woodmart_get_the_ID(), '_woodmart_' . $key, true );

			if ( isset( self::$_options[ $key ] ) && ! empty( $meta ) && 'inherit' !== $meta ) {
				self::$_options[ $key ] = $meta;
			}
		}
	}

	/**
	 * Static method to get all fields objects.
	 *
	 * @since 1.0.0
	 *
	 * @return array Field objects array.
	 */
	public static function get_fields() {
		$fields = self::$_fields;

		usort(
			$fields,
			function ( $item1, $item2 ) {

				if ( ! isset( $item1->args['priority'] ) ) {
					return 1;
				}

				if ( ! isset( $item2->args['priority'] ) ) {
					return -1;
				}

				return $item1->args['priority'] - $item2->args['priority'];
			}
		);

		return $fields;

	}

	/**
	 * Static method to get all sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Section array.
	 */
	public static function get_sections() {
		$sections = self::$_sections;

		usort(
			$sections,
			function ( $item1, $item2 ) {

				if ( ! isset( $item1['priority'] ) ) {
					return 1;
				}

				if ( ! isset( $item2['priority'] ) ) {
					return -1;
				}

				return $item1['priority'] - $item2['priority'];
			}
		);

		$sections_assoc = array();

		foreach ( $sections as $key => $section ) {
			$sections_assoc[ $section['id'] ] = $section;
		}

		return $sections_assoc;
	}

	/**
	 * Get fields CSS code based on its controls and values.
	 *
	 * @since 1.0.0
	 */
	public function get_css_output( $is_preset_active ) {
		$output_css = '';
		$fields_css = array(
			'desktop' => array(
				':root' => array(),
			),
			'tablet'  => array(
				':root' => array(),
			),
			'mobile'  => array(
				':root' => array(),
			),
		);

		foreach ( self::$_fields as $key => $field ) {
			$field->set_presets( $this->_presets );

			if ( ! $is_preset_active || woodmart_is_opt_changed( $field->args['id'] ) || 'group' === $field->args['type'] ) {
				$field_css = $field->css_output();

				if ( $field_css && is_array( $field_css ) ) {
					$fields_css = array_merge_recursive( $fields_css, $field_css );
				}
			}
		}

		foreach ( $fields_css as $device => $device_css ) {
			if ( $device_css ) {
				$css = '';

				foreach ( $device_css as $selector => $raw_css ) {
					$prefix = in_array( $device, array( 'tablet', 'mobile' ), true ) ? "\t" : '';

					if ( $raw_css ) {
						$raw_css = implode( "\t", array_filter( (array) $raw_css ) );

						if ( trim( $raw_css ) ) {
							$css .= $prefix . $selector . " {\n\t" . $prefix . $raw_css . $prefix . "}\n";
						}
					}
				}

				if ( ! $css ) {
					continue;
				}

				$output_css .= $this->get_heading_css_attribute( $css, $device );
			}
		}

		return $output_css;
	}

	/**
	 * Print css heading.
	 *
	 * @param string $css CSS.
	 * @param string $device Device.
	 *
	 * @return string
	 */
	private function get_heading_css_attribute( $css, $device = '' ) {
		if ( 'tablet' === $device ) {
			return "\n@media (max-width: 1024px) {\n$css\n}\n";
		}

		if ( 'mobile' === $device ) {
			return "\n@media (max-width: 768.98px) {\n$css\n}\n";
		}

		return $css;
	}


	/**
	 * Update all fields options array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options New options.
	 */
	public function override_current_options( $options ) {
		foreach ( self::$_fields as $key => $field ) {
			$field->override_options( $options );
		}
	}

	/**
	 * Update options in the database with a new array. It should be sanitized
	 * with ->sanitize_before_save() method of the class.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $new_options New options.
	 */
	public function update_options( $new_options ) {
		update_option( 'xts-' . self::$opt_name . '-options', $new_options );
	}

	/**
	 * Sanitize all options before saving callback. Used also for import and reset default actions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Options from POST.
	 *
	 * @return array
	 */
	public function sanitize_before_save( $options ) {
		$sanitized_options = array();
		$imported_options  = array();
		$reset             = false;

		$sanitized_options['last_message'] = 'save';

		$default_options = get_option( 'xts-' . self::$opt_name . '-options' );

		$presets = Presets::get_all();

		if ( ! $presets ) {
			$presets = get_option( 'xts-' . self::$opt_name . '-options-presets' );
		}

		// If we are working with preset.
		if ( isset( $options['preset'] ) && $options['preset'] ) {
			$preset_id      = $options['preset'];
			$fields_to_save = explode( ',', $options['fields_to_save'] );

			// Take default options previously stored in the database.
			$options_to_save               = $default_options;
			$options_to_save[ $preset_id ] = array();

			// Create a subarray with preset options. Everything else leave unchanged.
			if ( strlen( $options['fields_to_save'] ) > 0 && is_array( $fields_to_save ) && count( $fields_to_save ) > 0 ) {
				foreach ( $fields_to_save as $option ) {
					$options_to_save[ $preset_id ][ $option ] = $options[ $option ];
				}
				$options_to_save[ $preset_id ]['fields_to_save'] = $options['fields_to_save'];
			}
			$options_to_save['last_tab'] = $options['last_tab'];
			$options                     = $options_to_save;
		}

		// If we submit the form with "import" button then import options from the field.
		if ( isset( $options['import-btn'] ) && $options['import-btn'] ) {
			$import_json = $options['import_export'];

			$imported_options = json_decode( $import_json, true );

			if ( isset( $imported_options['presets'] ) ) {
				$presets = $imported_options['presets'];
				update_option( 'xts-options-presets', $presets );
			}

			if ( isset( $imported_options['options'] ) ) {
				$imported_options = $imported_options['options'];
			}

			$sanitized_options['last_message'] = 'import';
		}

		// If reset defaults button is pushed.
		if ( isset( $options['reset-defaults'] ) && $options['reset-defaults'] ) {
			$sanitized_options['last_message'] = 'reset';
			$reset                             = true;
		}

		foreach ( self::$_fields as $key => $field ) {
			if ( 'group' === $field->args['type'] && ! empty( $field->inner_fields ) ) {
				foreach ( $field->inner_fields as $inner_field ) {
					$sanitized_options = $this->sanitized_options( $inner_field, $sanitized_options, $imported_options, $options, $reset );
				}

				continue;
			}

			$sanitized_options = $this->sanitized_options( $field, $sanitized_options, $imported_options, $options, $reset );
		}

		$sanitized_options['last_tab'] = isset( $options['last_tab'] ) ? $options['last_tab'] : '';

		unset( $sanitized_options['import_export'] );

		if ( $presets ) {
			foreach ( $presets as $id => $preset ) {
				$default_options_value    = isset( $default_options[ $id ] ) ? $default_options[ $id ] : '';
				$sanitized_options[ $id ] = isset( $options[ $id ] ) ? $options[ $id ] : $default_options_value;

				if ( isset( $options['import-btn'] ) && $options['import-btn'] && isset( $imported_options[ $id ] ) ) {
					$sanitized_options[ $id ] = $imported_options[ $id ];
				}
			}
		}

		return $sanitized_options;
	}

	/**
	 * Sanitized theme settings options.
	 *
	 * @param object  $field Field object.
	 * @param array   $sanitized_options Sanitized options.
	 * @param array   $imported_options Imported options.
	 * @param array   $options Raw options.
	 * @param boolean $reset Is reset theme settings.
	 *
	 * @return array
	 */
	private function sanitized_options( $field, $sanitized_options, $imported_options, $options, $reset ) {
		if ( isset( $imported_options[ $field->get_id() ] ) ) {
			$sanitized_options[ $field->get_id() ] = $field->sanitize( $imported_options[ $field->get_id() ] );
		} elseif ( $reset ) {
			$sanitized_options[ $field->get_id() ] = self::get_default( $field->args );
		} else {
			if ( isset( $options[ $field->get_id() ] ) ) {
				$sanitized_options[ $field->get_id() ] = $field->sanitize( $options[ $field->get_id() ] );
			} elseif ( 'select' !== $field->args['type'] ) {
				$sanitized_options[ $field->get_id() ] = self::get_default( $field->args );
			} else {
				$sanitized_options[ $field->get_id() ] = '';
			}
		}

		return $sanitized_options;
	}
}

Options::get_instance();
