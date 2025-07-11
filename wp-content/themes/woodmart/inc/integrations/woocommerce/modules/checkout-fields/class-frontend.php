<?php
/**
 * Checkout frontend class.
 *
 * @package woodmart
 */

namespace XTS\Modules\Checkout_Fields;

use XTS\Singleton;

/**
 * Checkout frontend class.
 */
class Frontend extends Singleton {
	/**
	 * Instance of the Helper class.
	 *
	 * @var Helper
	 */
	public $helper;

	/**
	 * Init.
	 */
	public function init() {
		$this->helper = Helper::get_instance();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'woocommerce_checkout_fields', array( $this, 'update_checkout_fields' ), 99999, 1 );

		add_filter( 'woocommerce_get_country_locale_default', array( $this, 'change_locale_fields' ) );
		add_filter( 'woocommerce_get_country_locale_base', array( $this, 'change_locale_fields' ) );
		add_filter( 'woocommerce_get_country_locale', array( $this, 'change_country_locale_country' ), 20, 1 );
		add_filter( 'woocommerce_format_postcode', array( $this, 'normalize_postcode' ) );
	}

	/**
	 * Enqueue checkout scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! woodmart_get_opt( 'checkout_fields_enabled' ) || ! is_checkout() ) {
			return;
		}

		$minified = woodmart_is_minified_needed() ? '.min' : '';

		wp_enqueue_script( 'checkout-fields', WOODMART_THEME_DIR . '/js/scripts/wc/checkoutFields' . $minified . '.js', array( 'jquery' ), WOODMART_VERSION, true );
		wp_localize_script( 'checkout-fields', 'woodmart_checkout_fields', $this->add_localized_settings() );
	}

	/**
	 * Normalize postcode.
	 *
	 * @param string $postcode Postcode.
	 */
	public function normalize_postcode( $postcode ) {
		if ( '-' === $postcode ) {
			return '';
		}

		return $postcode;
	}

	/**
	 * Update checkout fields.
	 *
	 * @param array $checkout_fields List of checkout fields.
	 *
	 * @return array
	 */
	public function update_checkout_fields( $checkout_fields ) {
		if ( ! woodmart_get_opt( 'checkout_fields_enabled' ) || ! is_checkout() ) {
			return $checkout_fields;
		}

		$change_options = get_option( 'xts_checkout_fields_manager_options', array() );

		if ( empty( $change_options ) ) {
			return $checkout_fields;
		}

		foreach ( $change_options as $group_key => $checkout_groups_fields ) {
			if ( ! isset( $checkout_fields[ $group_key ] ) ) {
				continue;
			}

			foreach ( $checkout_groups_fields as $field_key => $checkout_field ) {
				if ( ! isset( $checkout_fields[ $group_key ][ $field_key ] ) ) {
					continue;
				}

				if ( isset( $checkout_field['status'] ) && ! $checkout_field['status'] ) {
					unset( $checkout_fields[ $group_key ][ $field_key ] );

					continue;
				}

				// Change field priority.
				if ( isset( $checkout_field['priority'] ) ) {
					$checkout_fields[ $group_key ][ $field_key ]['priority'] = $checkout_field['priority'];
				}

				// Remove screen reader.
				if ( isset( $checkout_fields[ $group_key ][ $field_key ]['label_class'] ) && is_array( $checkout_fields[ $group_key ][ $field_key ]['label_class'] ) && in_array( 'screen-reader-text', $checkout_fields[ $group_key ][ $field_key ]['label_class'], true ) ) {
					unset( $checkout_fields[ $group_key ][ $field_key ]['label_class'][ array_search( 'screen-reader-text', $checkout_fields[ $group_key ][ $field_key ]['label_class'], true ) ] );
				}

				// Remove old position class before added new.
				if ( isset( $checkout_field['class'] ) ) {
					$remove_classes    = array();
					$positions_classes = array(
						'form-row-first',
						'form-row-wide',
						'form-row-last',
					);

					foreach ( $positions_classes as $positions_class ) {
						$remove_classes[] = array_search( $positions_class, $checkout_fields[ $group_key ][ $field_key ]['class'], true );
					}

					$remove_classes = array_filter(
						array_unique( $remove_classes ),
						function ( $el ) {
							return $el || 0 === $el;
						}
					);

					if ( ! empty( $remove_classes ) ) {
						foreach ( $remove_classes as $id ) {
							unset( $checkout_fields[ $group_key ][ $field_key ]['class'][ $id ] );
						}
					}
				}

				$checkout_fields[ $group_key ][ $field_key ] = $this->helper->recursive_parse_args( $checkout_field, $checkout_fields[ $group_key ][ $field_key ] );
			}

			uasort( $checkout_fields[ $group_key ], 'wc_checkout_fields_uasort_comparison' );
		}

		return $checkout_fields;
	}

	/**
	 * Change country locale settings.
	 *
	 * @param array $fields List of country locale settings.
	 *
	 * @return array
	 */
	public function change_locale_fields( $fields ) {
		if ( ! woodmart_get_opt( 'checkout_fields_enabled' ) ) {
			return $fields;
		}

		// Remove default locale settings in order to custom settings was not overwritten by js event 'country_to_state_changing'.
		foreach ( $fields as $key => $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			unset( $fields[ $key ]['priority'] );
			unset( $fields[ $key ]['class'] );
		}

		return $fields;
	}

	/**
	 * Change country locale settings.
	 *
	 * @param array $fields List of country locale settings.
	 *
	 * @return array
	 */
	public function change_country_locale_country( $fields ) {
		if ( ! woodmart_get_opt( 'checkout_fields_enabled' ) || is_wc_endpoint_url( 'edit-address' ) || ! is_array( $fields ) ) {
			return $fields;
		}

		foreach ( $fields as $key => $val ) {
			foreach ( $val as $vkey => $vval ) {
				if ( isset( $vval['priority'] ) ) {
					unset( $fields[ $key ][ $vkey ]['priority'] );
				}
				if ( isset( $vval['class'] ) ) {
					unset( $fields[ $key ][ $vkey ]['class'] );
				}
			}
		}

		return $fields;
	}

	/**
	 * Add localized settings.
	 *
	 * @return array
	 */
	public function add_localized_settings() {
		$localized_settings = array();

		$change_options = get_option( 'xts_checkout_fields_manager_options', array() );

		if ( empty( $change_options ) ) {
			return $localized_settings;
		}

		$default_locale_fields = WC()->countries->get_country_locale_field_selectors();
		$locale_fields         = array();
		$prefixes              = array(
			'billing_',
			'shipping_',
		);

		foreach ( $prefixes as $prefix ) {
			foreach ( array_keys( $default_locale_fields ) as $locale_field ) {
				$locale_fields[] = $prefix . $locale_field;
			}
		}

		foreach ( $change_options as $group_key => $checkout_groups_fields ) {
			foreach ( $checkout_groups_fields as $field_key => $checkout_field ) {
				if ( ! in_array( $field_key, $locale_fields, true ) || ( isset( $checkout_field['status'] ) && ! $checkout_field['status'] ) || ! isset( $checkout_field['required'] ) ) {
					continue;
				}

				$localized_settings[ $field_key ]['required'] = $checkout_field['required'];
			}
		}

		return $localized_settings;
	}
}

Frontend::get_instance();
