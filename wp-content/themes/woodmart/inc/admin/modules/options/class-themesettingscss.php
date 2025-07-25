<?php
/**
 * Dynamic css
 *
 * @package xts
 */

namespace XTS;

use XTS\Admin\Modules\Options;
use XTS\Admin\Modules\Options\Presets;
use XTS\Modules\Styles_Storage;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Dynamic css class
 *
 * @since 1.0.0
 */
class Themesettingscss {
	public $storage;

	private $is_preset_active;

	/**
	 * Set up all properties
	 */
	public function __construct() {
		add_action( 'xts_after_theme_settings', array( $this, 'reset_data' ), 100 );
		add_action( 'xts_after_theme_settings', array( $this, 'write_file' ), 200 );

		if ( is_admin() ) {
			add_action( 'init', array( $this, 'set_storage' ), 100 );
			add_action( 'init', array( $this, 'print_styles' ), 10300 );
		} else {
			add_action( 'wp', array( $this, 'set_storage' ), 10200 );
			add_action( 'wp', array( $this, 'print_styles' ), 10300 );
		}

		add_action( 'woodmart_pjax_top_part', array( $this, 'print_styles_inline' ), 10 );
	}

	/**
	 * Set storage.
	 *
	 * @since 1.0.0
	 */
	public function set_storage() {
		$this->storage = new Styles_Storage( $this->get_file_name() );
	}

	/**
	 * Get all css.
	 *
	 * @param string $preset_id Preset id.
	 *
	 * @return mixed|void
	 */
	private function get_all_css( $preset_id = '' ) {
		if ( $preset_id ) {
			$this->is_preset_active = $preset_id;
		}

		$options = Options::get_instance();

		$options->load_defaults();
		$options->load_options();
		$options->load_presets( $preset_id );
		$options->override_options_from_meta();
		if ( ! apply_filters( 'woodmart_demo_presets_fix', false ) ) {
			$options->setup_globals();
		}

		$css  = $this->get_icons_font_css();
		$css .= $options->get_css_output( $this->is_preset_active() );
		$css .= $this->get_theme_settings_css();
		$css .= $this->get_custom_fonts_css();
		$css .= $this->get_custom_css();

		return apply_filters( 'woodmart_get_all_theme_settings_css', $css );
	}

	/**
	 * Get file name.
	 *
	 * @since 1.0.0
	 */
	private function get_file_name() {
		$active_presets = Presets::get_active_presets();
		$preset_id      = $active_presets ? end( $active_presets ) : 'default';
		return 'theme_settings_' . $preset_id;
	}

	/**
	 * Write file.
	 *
	 * @since 1.0.0
	 */
	public function reset_data() {
		if ( ! isset( $_GET['settings-updated'] ) ) {
			return;
		}

		$this->storage->reset_data();
	}

	/**
	 * Write file.
	 *
	 * @since 1.0.0
	 */
	public function write_file() {
		if ( ! isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'xts_theme_settings' !== $_GET['page'] ) ) { // phpcs:ignore
			return;
		}

		$this->storage->write( $this->get_all_css() );

		if ( ! Presets::get_active_presets() && isset( $_GET['settings-updated'] ) && Presets::get_all() ) { // phpcs:ignore
			$index = 0;
			foreach ( Presets::get_all() as $preset ) {
				$index++;
				$this->storage->set_data_name( 'theme_settings_' . $preset['id'] );
				$this->storage->set_data( 'xts-theme_settings_' . $preset['id'] . '-file-data' );
				$this->storage->set_css_data( 'xts-theme_settings_' . $preset['id'] . '-css-data' );
				$this->storage->reset_data();
				$this->storage->delete_file();

				if ( $index <= apply_filters( 'xts_theme_settings_presets_file_reset_count', 10 ) ) {
					$this->storage->write( $this->get_all_css( $preset['id'] ) );
				}
			}
		}
	}

	public function print_styles_inline() {
		$this->print_styles( 'inline' );
	}

	/**
	 * Print styles.
	 *
	 * @since 1.0.0
	 */
	public function print_styles( $inline = false ) {
		$presets = Presets::get_active_presets();
		array_unshift( $presets, 'default' );

		foreach ( $presets as $preset ) {
			$storage = new Styles_Storage( 'theme_settings_' . $preset );

			if ( ! $storage->is_css_exists() ) {
				$storage->write( $this->get_all_css( $preset ), true );
			}

			if ( 'inline' === $inline ) {
				$storage->print_styles_inline();
			} else {
				$storage->print_styles();
			}
		}
	}

	/**
	 * Get custom css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_custom_css() {
		$output          = '';
		$custom_css      = woodmart_get_opt( 'custom_css' );
		$css_desktop     = woodmart_get_opt( 'css_desktop' );
		$css_tablet      = woodmart_get_opt( 'css_tablet' );
		$css_wide_mobile = woodmart_get_opt( 'css_wide_mobile' );
		$css_mobile      = woodmart_get_opt( 'css_mobile' );

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'custom_css' ) ) ) {
			if ( $custom_css ) {
				$output .= $custom_css;
			}
		}

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'css_desktop' ) ) ) {
			if ( $css_desktop ) {
				$output .= '@media (min-width: 1025px) {' . "\n";
				$output .= "\t" . $css_desktop . "\n";
				$output .= '}' . "\n\n";
			}
		}

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'css_tablet' ) ) ) {
			if ( $css_tablet ) {
				$output .= '@media (min-width: 768px) and (max-width: 1024px) {' . "\n";
				$output .= "\t" . $css_tablet . "\n";
				$output .= '}' . "\n\n";
			}
		}

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'css_wide_mobile' ) ) ) {
			if ( $css_wide_mobile ) {
				$output .= '@media (min-width: 577px) and (max-width: 767px) {' . "\n";
				$output .= "\t" . $css_wide_mobile . "\n";
				$output .= '}' . "\n\n";
			}
		}

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'css_mobile' ) ) ) {
			if ( $css_mobile ) {
				$output .= '@media (max-width: 576px) {' . "\n";
				$output .= "\t" . $css_mobile . "\n";
				$output .= '}' . "\n\n";
			}
		}

		return $output;
	}

	/**
	 * Get custom fonts css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_custom_fonts_css() {
		$fonts = woodmart_get_opt( 'multi_custom_fonts' );

		$output       = '';
		$font_display = woodmart_get_opt( 'google_font_display' );

		if ( isset( $fonts['{{index}}'] ) ) {
			unset( $fonts['{{index}}'] );
		}

		if ( ! $fonts ) {
			return $output;
		}

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && ( woodmart_is_opt_changed( 'icons_font_display' ) || woodmart_is_opt_changed( 'multi_custom_fonts' ) ) ) ) {
			foreach ( $fonts as $key => $value ) {
				$eot   = isset( $value['font-eot'] ) ? $this->get_custom_font_url( $value['font-eot'] ) : '';
				$woff  = isset( $value['font-woff'] ) ? $this->get_custom_font_url( $value['font-woff'] ) : '';
				$woff2 = isset( $value['font-woff2'] ) ? $this->get_custom_font_url( $value['font-woff2'] ) : '';
				$ttf   = isset( $value['font-ttf'] ) ? $this->get_custom_font_url( $value['font-ttf'] ) : '';
				$svg   = isset( $value['font-svg'] ) ? $this->get_custom_font_url( $value['font-svg'] ) : '';

				if ( ! $value['font-name'] ) {
					continue;
				}

				$output .= '@font-face {' . "\n";
				$output .= "\t" . 'font-family: "' . sanitize_text_field( $value['font-name'] ) . '";' . "\n";

				if ( $eot ) {
					$output .= "\t" . 'src: url("' . esc_url( $eot ) . '");' . "\n";
				}

				if ( $eot || $woff || $woff2 || $ttf || $svg ) {
					$output .= "\t" . 'src: ';

					if ( $eot ) {
						$output .= 'url("' . esc_url( $eot ) . '#iefix") format("embedded-opentype")';
					}

					if ( $woff2 ) {
						if ( $eot ) {
							$output .= ', ' . "\n";
						}
						$output .= 'url("' . esc_url( $woff2 ) . '") format("woff2")';
					}

					if ( $woff ) {
						if ( $woff2 || $eot ) {
							$output .= ', ' . "\n";
						}
						$output .= 'url("' . esc_url( $woff ) . '") format("woff")';
					}

					if ( $ttf ) {
						if ( $woff2 || $woff || $eot ) {
							$output .= ', ' . "\n";
						}
						$output .= 'url("' . esc_url( $ttf ) . '") format("truetype")';
					}

					if ( $svg ) {
						if ( $ttf || $woff2 || $woff || $eot ) {
							$output .= ', ' . "\n";
						}
						$output .= 'url("' . esc_url( $svg ) . '#' . sanitize_text_field( $value['font-name'] ) . '") format("svg")';
					}

					$output .= ';' . "\n";
				}

				if ( isset( $value['font-weight'] ) && $value['font-weight'] ) {
					$output .= "\t" . 'font-weight: ' . sanitize_text_field( $value['font-weight'] ) . ';' . "\n";
				} else {
					$output .= "\t" . 'font-weight: normal;' . "\n";
				}

				if ( 'disable' !== $font_display ) {
					$output .= "\t" . 'font-display:' . $font_display . ';' . "\n";
				}

				$output .= "\t" . 'font-style: normal;' . "\n";
				$output .= '}' . "\n\n";
			}

		}

		return $output;
	}

	/**
	 * Icons font css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_icons_font_css() {
		$output    = '';
		$icon_font = woodmart_get_opt( 'icon_font', array( 'font' => '1', 'weight' => '400' ) );

		$font_display = woodmart_get_opt( 'icons_font_display', 'disable' );

		if ( ! $this->is_preset_active() || ( $this->is_preset_active() && ( woodmart_is_opt_changed( 'icons_font_display' ) || woodmart_is_opt_changed( 'icon_font' ) ) ) ) {
			$icon_font_name = 'woodmart-font-';

			if ( ! empty( $icon_font['font'] ) ) {
				$icon_font_name .= $icon_font['font'];
			}

			if ( ! empty( $icon_font['weight'] ) ) {
				$icon_font_name .= '-' . $icon_font['weight'];
			}
			$output .= '@font-face {' . "\n";
			$output .= "\t" . 'font-weight: normal;' . "\n";
			$output .= "\t" . 'font-style: normal;' . "\n";
			$output .= "\t" . 'font-family: "woodmart-font";' . "\n";
			$output .= "\t" . 'src: url("' . woodmart_remove_https( WOODMART_THEME_DIR . '/fonts/' . $icon_font_name . '.woff2' ) . '?v=' . woodmart_get_theme_info( 'Version' ) . '") format("woff2");' . "\n";

			if ( 'disable' !== $font_display ) {
				$output .= "\t" . 'font-display:' . $font_display . ';' . "\n";
			}

			$output .= '}' . "\n\n";
		}

		$styles_always = woodmart_get_opt( 'styles_always_use' );

		if ( ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'styles_always_use' ) ) ) && $styles_always && is_array( $styles_always ) && in_array( 'base-deprecated', $styles_always, true ) ) {
			$output .= '@font-face {' . "\n";
			$output .= "\t" . 'font-weight: normal;' . "\n";
			$output .= "\t" . 'font-style: normal;' . "\n";
			$output .= "\t" . 'font-family: "woodmart-font-deprecated";' . "\n";
			$output .= "\t" . 'src: url("' . woodmart_remove_https( WOODMART_THEME_DIR . '/fonts/woodmart-font-deprecated.woff2' ) . '?v=' . woodmart_get_theme_info( 'Version' ) . '") format("woff2");' . "\n";

			if ( 'disable' !== $font_display ) {
				$output .= "\t" . 'font-display:' . $font_display . ';' . "\n";
			}

			$output .= '}' . "\n\n";
		}

		if ( woodmart_woocommerce_installed() && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'disable_gutenberg_css' ) ) ) && ! woodmart_get_opt( 'disable_gutenberg_css' ) ) {
			$output .= '@font-face {' . "\n";
			$output .= "\t" . 'font-family: "star";' . "\n";
			$output .= "\t" . 'font-weight: 400;' . "\n";
			$output .= "\t" . 'font-style: normal;' . "\n";
			$output .= "\t" . 'src: ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/star.eot?#iefix', WC_PLUGIN_FILE ) ) . '") format("embedded-opentype"), ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/star.woff', WC_PLUGIN_FILE ) ) . '") format("woff"), ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/star.ttf', WC_PLUGIN_FILE ) ) . '") format("truetype"), ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/star.svg#star', WC_PLUGIN_FILE ) ) . '") format("svg");' .
				"\n";
			$output .= '}' . "\n\n";

			$output .= '@font-face {' . "\n";
			$output .= "\t" . 'font-family: "WooCommerce";' . "\n";
			$output .= "\t" . 'font-weight: 400;' . "\n";
			$output .= "\t" . 'font-style: normal;' . "\n";
			$output .= "\t" . 'src: ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/WooCommerce.eot?#iefix', WC_PLUGIN_FILE ) ) . '") format("embedded-opentype"), ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/WooCommerce.woff', WC_PLUGIN_FILE ) ) . '") format("woff"), ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/WooCommerce.ttf', WC_PLUGIN_FILE ) ) . '") format("truetype"), ' .
				'url("' . woodmart_remove_https( plugins_url( '/assets/fonts/WooCommerce.svg#WooCommerce', WC_PLUGIN_FILE ) ) . '") format("svg");' .
				"\n";
			$output .= '}' . "\n\n";
		}

		return $output;
	}

	/**
	 * Get custom font url.
	 *
	 * @since 1.0.0
	 *
	 * @param array $font Font data.
	 *
	 * @return string
	 */
	public function get_custom_font_url( $font ) {
		$url = '';

		if ( isset( $font['id'] ) && $font['id'] ) {
			$url = wp_get_attachment_url( $font['id'] );
		} elseif ( is_array( $font ) ) {
			$url = $font['url'];
		}

		return woodmart_remove_https( $url );
	}

	public function is_preset_active() {
		return ( Presets::get_active_presets() || $this->is_preset_active ) && 'default' !== $this->is_preset_active;
	}

	/**
	 * Get theme settings css.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_theme_settings_css() {
		$site_custom_width     = woodmart_get_opt( 'site_custom_width' );
		$predefined_site_width = woodmart_get_opt( 'site_width' );

		$text_font    = woodmart_get_opt( 'text-font' );
		$primary_font = woodmart_get_opt( 'primary-font' );

		$site_width = '';

		if ( 'full-width' === $predefined_site_width ) {
			$site_width = 1222;
		} elseif ( 'boxed' === $predefined_site_width ) {
			$site_width = 1160;
		} elseif ( 'boxed-2' === $predefined_site_width ) {
			$site_width = 1160;
		} elseif ( 'wide' === $predefined_site_width ) {
			$site_width = 1600;
		} elseif ( 'custom' === $predefined_site_width ) {
			$site_width = $site_custom_width;
		}

		// Default button.
		$default_btn_style       = woodmart_get_opt( 'btns_default_style' );
		$default_btn_color       = '';
		$default_btn_color_hover = '';

		if ( 'custom' !== woodmart_get_opt( 'btns_default_color_scheme' ) ) {
			$default_btn_color = 'light' === woodmart_get_opt( 'btns_default_color_scheme' ) ? '#fff' : '#333';
		}
		if ( 'custom' !== woodmart_get_opt( 'btns_default_color_scheme_hover' ) ) {
			$default_btn_color_hover = 'light' === woodmart_get_opt( 'btns_default_color_scheme_hover' ) ? '#fff' : '#333';
		}

		// Accent button.
		$accent_btn_style       = woodmart_get_opt( 'btns_shop_style' );
		$accent_btn_color       = '';
		$accent_btn_color_hover = '';

		if ( 'custom' !== woodmart_get_opt( 'btns_shop_color_scheme' ) ) {
			$accent_btn_color = 'light' === woodmart_get_opt( 'btns_shop_color_scheme' ) ? '#fff' : '#333';
		}
		if ( 'custom' !== woodmart_get_opt( 'btns_shop_color_scheme_hover' ) ) {
			$accent_btn_color_hover = 'light' === woodmart_get_opt( 'btns_shop_color_scheme_hover' ) ? '#fff' : '#333';
		}

		// Forms.
		$form_style = woodmart_get_opt( 'form_fields_style' );

		// Rounding.
		$rounding     = woodmart_get_opt( 'rounding_size', 'none' );
		$rounding_cat = woodmart_get_opt( 'categories_rounding' );

		if ( 'none' === $rounding ) {
			$rounding = 0;
		}

		ob_start();
		// phpcs:disable
		?>
<?php if ( ! $this->is_preset_active() || ( $this->is_preset_active() && ( woodmart_is_opt_changed( 'form_fields_style' ) || woodmart_is_opt_changed( 'btns_default_color_scheme' ) || woodmart_is_opt_changed( 'btns_default_color_scheme_hover' ) || woodmart_is_opt_changed( 'btns_shop_color_scheme' ) || woodmart_is_opt_changed( 'btns_shop_color_scheme_hover' ) || woodmart_is_opt_changed( 'btns_default_style' ) || woodmart_is_opt_changed( 'btns_shop_style' ) || woodmart_is_opt_changed( 'rounding_size' ) || woodmart_is_opt_changed( 'site_custom_width' ) || woodmart_is_opt_changed( 'site_width' ) ) ) ) : ?>
	:root{
		<?php if ( $site_width && ( ! $this->is_preset_active() || ( $this->is_preset_active() && ( woodmart_is_opt_changed( 'site_custom_width' ) || woodmart_is_opt_changed( 'site_width' ) ) ) ) ) : ?>
			--wd-container-w: <?php echo esc_html( $site_width ); ?>px;
		<?php endif; ?>
		<?php if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'form_fields_style' ) ) ) : ?>
			<?php if ( 'rounded' === $form_style ) : ?>
				--wd-form-brd-radius: 35px;
			<?php endif; ?>
			<?php if ( 'semi-rounded' === $form_style ) : ?>
				--wd-form-brd-radius: 5px;
			<?php endif; ?>
			<?php if ( 'square' === $form_style || 'underlined' === $form_style ) : ?>
				--wd-form-brd-radius: 0px;
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( $default_btn_color && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'btns_default_color_scheme' ) ) ) ) : ?>
			--btn-default-color: <?php echo esc_attr( $default_btn_color ) ?>;
		<?php endif; ?>
		<?php if ( $default_btn_color_hover && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'btns_default_color_scheme_hover' ) ) ) ) : ?>
			--btn-default-color-hover: <?php echo esc_attr( $default_btn_color_hover ) ?>;
		<?php endif; ?>
		<?php if ( $accent_btn_color && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'btns_shop_color_scheme' ) ) ) ) : ?>
			--btn-accented-color: <?php echo esc_attr( $accent_btn_color ) ?>;
		<?php endif; ?>
		<?php if ( $accent_btn_color_hover && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'btns_shop_color_scheme_hover' ) ) ) ) : ?>
			--btn-accented-color-hover: <?php echo esc_attr( $accent_btn_color_hover ) ?>;
		<?php endif; ?>
		<?php if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'btns_default_style' ) ) ) : ?>
			<?php if ( 'flat' === $default_btn_style ) : ?>
				--btn-default-brd-radius: 0px;
				--btn-default-box-shadow: none;
				--btn-default-box-shadow-hover: none;
				--btn-default-box-shadow-active: none;
				--btn-default-bottom: 0px;
			<?php endif; ?>
			<?php if ( '3d' === $default_btn_style ) : ?>
				--btn-default-bottom-active: -1px;
				--btn-default-brd-radius: 0px;
				--btn-default-box-shadow: inset 0 -2px 0 rgba(0, 0, 0, .15);
				--btn-default-box-shadow-hover: inset 0 -2px 0 rgba(0, 0, 0, .15);
			<?php endif; ?>
			<?php if ( 'rounded' === $default_btn_style ) : ?>
				--btn-default-brd-radius: 35px;
				--btn-default-box-shadow: none;
				--btn-default-box-shadow-hover: none;
			<?php endif; ?>
			<?php if ( 'semi-rounded' === $default_btn_style ) : ?>
				--btn-default-brd-radius: 5px;
				--btn-default-box-shadow: none;
				--btn-default-box-shadow-hover: none;
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'btns_shop_style' ) ) ) : ?>
			<?php if ( 'flat' === $accent_btn_style ) : ?>
				--btn-accented-brd-radius: 0px;
				--btn-accented-box-shadow: none;
				--btn-accented-box-shadow-hover: none;
				--btn-accented-box-shadow-active: none;
				--btn-accented-bottom: 0px;
			<?php endif; ?>
			<?php if ( '3d' === $accent_btn_style ) : ?>
				--btn-accented-bottom-active: -1px;
				--btn-accented-brd-radius: 0px;
				--btn-accented-box-shadow: inset 0 -2px 0 rgba(0, 0, 0, .15);
				--btn-accented-box-shadow-hover: inset 0 -2px 0 rgba(0, 0, 0, .15);
			<?php endif; ?>
			<?php if ( 'rounded' === $accent_btn_style ) : ?>
				--btn-accented-brd-radius: 35px;
				--btn-accented-box-shadow: none;
				--btn-accented-box-shadow-hover: none;
			<?php endif; ?>
			<?php if ( 'semi-rounded' === $accent_btn_style ) : ?>
				--btn-accented-brd-radius: 5px;
				--btn-accented-box-shadow: none;
				--btn-accented-box-shadow-hover: none;
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( 'custom' !== $rounding && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'rounding_size' ) ) ) ) : ?>
			--wd-brd-radius: <?php echo esc_attr( $rounding ) ?>px;
		<?php endif; ?>
		<?php if ( 'custom' !== $rounding_cat && ( ( $rounding_cat || '0' === $rounding_cat ) && ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'categories_rounding' ) ) ) ) ) : ?>
			--wd-cat-brd-radius: <?php echo esc_attr( $rounding_cat ) ?>px;
		<?php endif; ?>
	}
<?php endif; ?>

<?php if ( 'external' === woodmart_get_opt( 'current_builder', 'external' ) && ( ! $this->is_preset_active() || ( $this->is_preset_active() && ( woodmart_is_opt_changed( 'site_custom_width' ) || woodmart_is_opt_changed( 'site_width' ) ) ) ) ) : ?>
	<?php if ( $site_width && 'wpb' === woodmart_get_current_page_builder() ): ?>
		@media (min-width: <?php echo esc_html( $site_width ); ?>px) {
			[data-vc-full-width]:not([data-vc-stretch-content]),
			:is(.vc_section, .vc_row).wd-section-stretch {
				padding-left: calc((100vw - <?php echo esc_html( $site_width ); ?>px - var(--wd-sticky-nav-w) - var(--wd-scroll-w)) / 2);
				padding-right: calc((100vw - <?php echo esc_html( $site_width ); ?>px - var(--wd-sticky-nav-w) - var(--wd-scroll-w)) / 2);
			}
		}
	<?php elseif ( $site_width && 'enabled' === woodmart_get_opt( 'negative_gap' ) && 'elementor' === woodmart_get_current_page_builder() ) : ?>
		@media (min-width: <?php echo esc_html( $site_width ); ?>px) {
			section.elementor-section.wd-section-stretch > .elementor-container {
				margin-left: auto;
				margin-right: auto;
			}
		}
	<?php endif; ?>
<?php endif; ?>

<?php if ( ! $this->is_preset_active() || ( $this->is_preset_active() && woodmart_is_opt_changed( 'rev_slider_inherit_theme_font' ) ) ) : ?>
	<?php if ( woodmart_get_opt( 'rev_slider_inherit_theme_font' ) ): ?>
		<?php if ( isset( $text_font[0] ) && isset( $text_font[0]['font-family'] ) ) : ?>
			rs-slides :is([data-type=text],[data-type=button]) {
				font-family: <?php echo esc_html( $text_font[0]['font-family'] ); ?> !important;
			}
		<?php endif; ?>
		<?php if ( isset( $primary_font[0] ) && isset( $primary_font[0]['font-family'] ) ): ?>
			rs-slides :is(h1,h2,h3,h4,h5,h6)[data-type=text] {
				font-family: <?php echo esc_html( $primary_font[0]['font-family'] ); ?> !important;
			}
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php

return str_replace( "\t", '', ob_get_clean() );
	}
}
