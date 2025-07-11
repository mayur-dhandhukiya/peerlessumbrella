<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}
/**
* ------------------------------------------------------------------------------------------------
* Information box element map
* ------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( 'woodmart_get_vc_map_info_box_carousel' ) ) {
	function woodmart_get_vc_map_info_box_carousel() {
		return array(
			'name'                    => esc_html__( 'Information box carousel', 'woodmart' ),
			'base'                    => 'woodmart_info_box_carousel',
			'as_parent'               => array( 'only' => 'woodmart_info_box' ),
			'content_element'         => true,
			'show_settings_on_create' => true,
			'category'                => function_exists( 'woodmart_get_tab_title_category_for_wpb' ) ? woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ) : esc_html__( 'Theme elements', 'woodmart' ),
			'description'             => esc_html__( 'Show your brief information as a carousel', 'woodmart' ),
			'icon'                    => WOODMART_ASSETS . '/images/vc-icon/infobox-slider.svg',
			'params'                  => array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),
				/**
				 * Slider
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Carousel', 'woodmart' ),
					'param_name' => 'slider_divider',
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'js_composer' ),
				),
				function_exists( 'woodmart_get_vc_responsive_spacing_map' ) ? woodmart_get_vc_responsive_spacing_map() : '',
				/**
				 * Extra
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Extra options', 'woodmart' ),
					'group'      => esc_html__( 'Advanced', 'woodmart' ),
					'param_name' => 'extra_divider',
				),
				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'group'      => esc_html__( 'Advanced', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
			),
			'js_view'                 => 'VcColumnView',
		);
	}
}

if ( ! function_exists( 'woodmart_get_woodmart_info_box_shortcode_args' ) ) {
	function woodmart_get_woodmart_info_box_shortcode_args() {
		return array(
			'name'            => esc_html__( 'Information box', 'woodmart' ),
			'base'            => 'woodmart_info_box',
			'content_element' => true,
			'category'        => function_exists( 'woodmart_get_tab_title_category_for_wpb' ) ? woodmart_get_tab_title_category_for_wpb( esc_html__( 'Theme elements', 'woodmart' ) ) : esc_html__( 'Theme elements', 'woodmart' ),
			'description'     => esc_html__( 'Show some brief information', 'woodmart' ),
			'icon'            => WOODMART_ASSETS . '/images/vc-icon/information-box.svg',
			'params'          => woodmart_get_info_box_shortcode_params(),
		);
	}
}

if ( ! function_exists( 'woodmart_get_info_box_shortcode_params' ) ) {
	function woodmart_get_info_box_shortcode_params() {
		$secondary_font = woodmart_get_opt( 'secondary-font' );
		$text_font      = woodmart_get_opt( 'text-font' );
		$primary_font   = woodmart_get_opt( 'primary-font' );

		$secondary_font_title = isset( $secondary_font[0]['font-family'] ) ? esc_html__( 'Secondary font', 'woodmart' ) . ' (' . $secondary_font[0]['font-family'] . ')' : esc_html__( 'Secondary font', 'woodmart' );
		$text_font_title      = isset( $text_font[0]['font-family']  ) ? esc_html__( 'Text font', 'woodmart' ) . ' (' . $text_font[0]['font-family'] . ')' : esc_html__( 'Text', 'woodmart' );
		$primary_font_title   = isset( $primary_font[0]['font-family']  ) ? esc_html__( 'Title font', 'woodmart' ) . ' (' . $primary_font[0]['font-family'] . ')' : esc_html__( 'Title font', 'woodmart' );

		return apply_filters(
			'woodmart_get_info_box_shortcode_params',
			array(
				array(
					'type'       => 'woodmart_css_id',
					'param_name' => 'woodmart_css_id',
				),
				/**
				 * Icon
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Icon', 'woodmart' ),
					'param_name' => 'icon_divider',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Icon style', 'woodmart' ),
					'param_name'       => 'icon_style',
					'value'            => array(
						esc_html__( 'Simple', 'woodmart' ) => 'simple',
						esc_html__( 'With background', 'woodmart' ) => 'with-bg',
						esc_html__( 'With border', 'woodmart' ) => 'with-border',
					),
					'wood_tooltip'     => true,
					'images_value'     => array(
						'simple'      => WOODMART_ASSETS_IMAGES . '/settings/infobox/style/simple.png',
						'with-bg'     => WOODMART_ASSETS_IMAGES . '/settings/infobox/style/with-bg.png',
						'with-border' => WOODMART_ASSETS_IMAGES . '/settings/infobox/style/with-border.png',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column info-icon',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Icon type', 'woodmart' ),
					'hint'             => esc_html__( 'You can display icon based on image or just write some text like 01., 02., M, X etc.', 'woodmart' ),
					'param_name'       => 'icon_type',
					'value'            => array(
						esc_html__( 'Icon', 'woodmart' ) => 'icon',
						esc_html__( 'Text', 'woodmart' ) => 'text',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Icon background color', 'woodmart' ),
					'param_name'       => 'icon_bg_color',
					'css_args'         => array(
						'background-color' => array(
							' .info-box-icon',
						),
					),
					'dependency'       => array(
						'element' => 'icon_style',
						'value'   => array( 'with-bg' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Icon background color on hover', 'woodmart' ),
					'param_name'       => 'icon_bg_hover_color',
					'css_args'         => array(
						'background-color' => array(
							':hover .info-box-icon',
						),
					),
					'dependency'       => array(
						'element' => 'icon_style',
						'value'   => array( 'with-bg' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Icon border color', 'woodmart' ),
					'param_name'       => 'icon_border_color',
					'css_args'         => array(
						'border-color' => array(
							' .info-box-icon',
						),
					),
					'dependency'       => array(
						'element' => 'icon_style',
						'value'   => array( 'with-border' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Icon border color on hover', 'woodmart' ),
					'param_name'       => 'icon_border_hover_color',
					'css_args'         => array(
						'border-color' => array(
							':hover .info-box-icon',
						),
					),
					'dependency'       => array(
						'element' => 'icon_style',
						'value'   => array( 'with-border' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Icon text', 'woodmart' ),
					'param_name'       => 'icon_text',
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Icon text size', 'woodmart' ),
					'param_name'       => 'icon_text_size',
					'value'            => array(
						esc_html__( 'Default (52px)', 'woodmart' ) => 'default',
						esc_html__( 'Small (38px)', 'woodmart' ) => 'small',
						esc_html__( 'Large (74px)', 'woodmart' ) => 'large',
					),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Icon text color', 'woodmart' ),
					'param_name'       => 'icon_text_color',
					'css_args'         => array(
						'color' => array(
							' .box-with-text',
						),
					),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'text' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'param_name'       => 'image',
					'value'            => '',
					'hint'             => esc_html__( 'Select image from media library.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'icon' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image size', 'woodmart' ),
					'param_name'       => 'img_size',
					'hint'             => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'icon_type',
						'value'   => array( 'icon' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'          => esc_html__( 'Spacing', 'woodmart' ),
					'type'             => 'wd_slider',
					'param_name'       => 'icon_spacing',
					'devices'          => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'            => array(
						'px' => array(
							'min'  => 5,
							'max'  => 50,
							'step' => 1,
						),
					),
					'selectors'        => array(
						'{{WRAPPER}}.wd-info-box' => array(
							'--ib-icon-sp: {{VALUE}}px;',
						),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				/**
				 * Box style
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Box style', 'woodmart' ),
					'param_name' => 'style_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Box style', 'woodmart' ),
					'param_name'       => 'style',
					'value'            => array(
						esc_html__( 'Base', 'woodmart' )   => 'base',
						esc_html__( 'Bordered', 'woodmart' ) => 'border',
						esc_html__( 'Shadow', 'woodmart' ) => 'shadow',
						esc_html__( 'Background on hover', 'woodmart' ) => 'bg-hover',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'heading'       => esc_html__( 'Rounding', 'woodmart' ),
					'type'          => 'wd_select',
					'param_name'    => 'rounding_size',
					'style'         => 'select',
					'selectors'     => array(
						'{{WRAPPER}}' => array(
							'--wd-brd-radius: {{VALUE}}px;',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'value' => '',
						),
					),
					'value'         => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( '0', 'woodmart' )      => '0',
						esc_html__( '5', 'woodmart' )      => '5',
						esc_html__( '8', 'woodmart' )      => '8',
						esc_html__( '12', 'woodmart' )     => '12',
						esc_html__( 'Custom', 'woodmart' ) => 'custom',
					),
					'dependency'    => array(
						'element' => 'icon_type',
						'value'   => array( 'icon' ),
					),
					'generate_zero' => true,
				),
				array(
					'heading'       => esc_html__( 'Custom rounding', 'woodmart' ),
					'type'          => 'wd_slider',
					'param_name'    => 'custom_rounding_size',
					'selectors'     => array(
						'{{WRAPPER}}' => array(
							'--wd-brd-radius: {{VALUE}}{{UNIT}};',
						),
					),
					'devices'       => array(
						'desktop' => array(
							'value' => '',
							'unit'  => 'px',
						),
					),
					'range'         => array(
						'px' => array(
							'min'  => 0,
							'max'  => 300,
							'step' => 1,
						),
						'%'  => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
					),
					'dependency'    => array(
						'element' => 'rounding_size',
						'value'   => function_exists( 'woodmart_compress' ) ? woodmart_compress(
							wp_json_encode(
								array(
									'devices' => array(
										'desktop' => array(
											'value' => 'custom',
										),
									),
								)
							)
						) : '',
					),
					'generate_zero' => true,
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Color scheme', 'woodmart' ),
					'param_name'       => 'woodmart_color_scheme',
					'value'            => array(
						esc_html__( 'Inherit', 'woodmart' ) => '',
						esc_html__( 'Light', 'woodmart' ) => 'light',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Background', 'woodmart' ),
					'param_name' => 'hover_divider',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background type', 'woodmart' ),
					'param_name'       => 'bg_hover_colorpicker',
					'value'            => array(
						esc_html__( 'Color or image', 'woodmart' ) => 'colorpicker',
						esc_html__( 'Gradient', 'woodmart' ) => 'gradient',
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'heading'    => esc_html__( 'Background color', 'woodmart' ),
					'type'       => 'wd_colorpicker',
					'param_name' => 'bg_color',
					'selectors'  => array(
						'{{WRAPPER}}.wd-info-box' => array(
							'background-color: {{VALUE}};',
						),
					),
					'dependency' => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Background image', 'woodmart' ),
					'param_name'       => 'bg_image_box',
					'value'            => '',
					'hint'             => esc_html__( 'Select image from media library.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Background image size', 'woodmart' ),
					'param_name'       => 'bg_image_box_size',
					'hint'             => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background position', 'woodmart' ),
					'param_name'       => 'bg_image_box_position',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Center Center', 'woodmart' ) => 'center center',
						esc_html__( 'Center Left', 'woodmart' ) => 'center left',
						esc_html__( 'Center Right', 'woodmart' ) => 'center right',
						esc_html__( 'Top Center', 'woodmart' ) => 'top center',
						esc_html__( 'Top Left', 'woodmart' ) => 'top left',
						esc_html__( 'Top Right', 'woodmart' ) => 'top right',
						esc_html__( 'Bottom Center', 'woodmart' ) => 'bottom center',
						esc_html__( 'Bottom Left', 'woodmart' ) => 'bottom left',
						esc_html__( 'Bottom Right', 'woodmart' ) => 'bottom right',
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background repeat', 'woodmart' ),
					'param_name'       => 'bg_image_box_repeat',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'No-repeat', 'woodmart' ) => 'no-repeat',
						esc_html__( 'Repeat', 'woodmart' ) => 'repeat',
						esc_html__( 'Repeat-x', 'woodmart' ) => 'repeat-x',
						esc_html__( 'Repeat-y', 'woodmart' ) => 'repeat-y',
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background size', 'woodmart' ),
					'param_name'       => 'bg_image_box_sizes',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Cover', 'woodmart' ) => 'cover',
						esc_html__( 'Contain', 'woodmart' ) => 'contain',
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Hover background color', 'woodmart' ),
					'param_name'       => 'bg_hover_color',
					'css_args'         => array(
						'background-color' => array(
							':after',
						),
					),
					'wd_dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Hover background image', 'woodmart' ),
					'param_name'       => 'bg_hover_image',
					'value'            => '',
					'hint'             => esc_html__( 'Select image from media library.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'wd_dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Hover background image size', 'woodmart' ),
					'param_name'       => 'bg_hover_image_size',
					'hint'             => esc_html__( 'Enter image size. Example: \'thumbnail\', \'medium\', \'large\', \'full\' or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use \'thumbnail\' size.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'wd_dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Hover background position', 'woodmart' ),
					'param_name'       => 'bg_hover_image_position',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Center Center', 'woodmart' ) => 'center center',
						esc_html__( 'Center Left', 'woodmart' ) => 'center left',
						esc_html__( 'Center Right', 'woodmart' ) => 'center right',
						esc_html__( 'Top Center', 'woodmart' ) => 'top center',
						esc_html__( 'Top Left', 'woodmart' ) => 'top left',
						esc_html__( 'Top Right', 'woodmart' ) => 'top right',
						esc_html__( 'Bottom Center', 'woodmart' ) => 'bottom center',
						esc_html__( 'Bottom Left', 'woodmart' ) => 'bottom left',
						esc_html__( 'Bottom Right', 'woodmart' ) => 'bottom right',
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'wd_dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Hover background repeat', 'woodmart' ),
					'param_name'       => 'bg_hover_image_repeat',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'No-repeat', 'woodmart' ) => 'no-repeat',
						esc_html__( 'Repeat', 'woodmart' ) => 'repeat',
						esc_html__( 'Repeat-x', 'woodmart' ) => 'repeat-x',
						esc_html__( 'Repeat-y', 'woodmart' ) => 'repeat-y',
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'wd_dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Hover background size', 'woodmart' ),
					'param_name'       => 'bg_hover_image_sizes',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						esc_html__( 'Auto', 'woodmart' )  => 'auto',
						esc_html__( 'Cover', 'woodmart' ) => 'cover',
						esc_html__( 'Contain', 'woodmart' ) => 'contain',
					),
					'dependency'       => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'colorpicker' ),
					),
					'wd_dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_gradient',
					'heading'    => esc_html__( 'Background gradient', 'woodmart' ),
					'param_name' => 'bg_color_gradient',
					'dependency' => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'gradient' ),
					),
				),
				array(
					'type'          => 'woodmart_gradient',
					'heading'       => esc_html__( 'Hover background gradient', 'woodmart' ),
					'param_name'    => 'bg_hover_color_gradient',
					'wd_dependency' => array(
						'element' => 'bg_hover_colorpicker',
						'value'   => array( 'gradient' ),
					),
					'dependency'    => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
				),
				array(
					'type'             => 'woodmart_button_set',
					'heading'          => esc_html__( 'Color scheme on hover', 'woodmart' ),
					'param_name'       => 'woodmart_hover_color_scheme',
					'value'            => array(
						esc_html__( 'Light', 'woodmart' ) => 'light',
						esc_html__( 'Dark', 'woodmart' )  => 'dark',
					),
					'dependency'       => array(
						'element' => 'style',
						'value'   => array( 'bg-hover' ),
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				/**
				 * Layout
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Layout', 'woodmart' ),
					'param_name' => 'layout_divider',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Text alignment', 'woodmart' ),
					'param_name'       => 'alignment',
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )   => 'left',
						esc_html__( 'Center', 'woodmart' ) => 'center',
						esc_html__( 'Right', 'woodmart' )  => 'right',
					),
					'images_value'     => array(
						'center' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
						'left'   => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
						'right'  => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
					'std'              => 'left',
					'wood_tooltip'     => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column title-align',
				),
				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Image alignment', 'woodmart' ),
					'param_name'       => 'image_alignment',
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )  => 'left',
						esc_html__( 'Top', 'woodmart' )   => 'top',
						esc_html__( 'Right', 'woodmart' ) => 'right',
					),
					'images_value'     => array(
						'top'   => WOODMART_ASSETS_IMAGES . '/settings/infobox/position/top.png',
						'left'  => WOODMART_ASSETS_IMAGES . '/settings/infobox/position/left.png',
						'right' => WOODMART_ASSETS_IMAGES . '/settings/infobox/position/right.png',
					),
					'std'              => 'top',
					'wood_tooltip'     => true,
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Vertical alignment', 'woodmart' ),
					'param_name'       => 'image_vertical_alignment',
					'value'            => array(
						esc_html__( 'Top', 'woodmart' )    => 'top',
						esc_html__( 'Middle', 'woodmart' ) => 'middle',
						esc_html__( 'Bottom', 'woodmart' ) => 'bottom',
					),
					'images_value'     => array(
						'top'    => WOODMART_ASSETS_IMAGES . '/settings/infobox/vertical-position/top.png',
						'middle' => WOODMART_ASSETS_IMAGES . '/settings/infobox/vertical-position/middle.png',
						'bottom' => WOODMART_ASSETS_IMAGES . '/settings/infobox/vertical-position/bottom.png',
					),
					'std'              => 'top',
					'wood_tooltip'     => true,
					'dependency'       => array(
						'element' => 'image_alignment',
						'value'   => array( 'left', 'right' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Title
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Title', 'woodmart' ),
					'group'      => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name' => 'title_divider',
				),
				array(
					'type'       => 'textarea',
					'heading'    => esc_html__( 'Title text', 'woodmart' ),
					'param_name' => 'title',
					'holder'     => 'div',
					'group'      => esc_html__( 'Title and Subtitle', 'woodmart' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_font',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						$text_font_title      => 'text',
						$primary_font_title   => 'primary',
						$secondary_font_title => 'alt',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Title tag', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_tag',
					'value'            => array(
						'h1'   => 'h1',
						'h2'   => 'h2',
						'h3'   => 'h3',
						'h4'   => 'h4',
						'h5'   => 'h5',
						'h6'   => 'h6',
						'p'    => 'p',
						'div'  => 'div',
						'span' => 'span',
					),
					'std'              => 'h4',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Predefined title size', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_size',
					'value'            => array(
						esc_html__( 'Default (18px)', 'woodmart' ) => 'default',
						esc_html__( 'Small (16px)', 'woodmart' ) => 'small',
						esc_html__( 'Large (26px)', 'woodmart' ) => 'large',
						esc_html__( 'Extra Large (36px)', 'woodmart' ) => 'extra-large',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font weight', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_font_weight',
					'value'            => array(
						'' => '',
						esc_html__( 'Ultra-Light 100', 'woodmart' ) => 100,
						esc_html__( 'Light 200', 'woodmart' ) => 200,
						esc_html__( 'Book 300', 'woodmart' ) => 300,
						esc_html__( 'Normal 400', 'woodmart' ) => 400,
						esc_html__( 'Medium 500', 'woodmart' ) => 500,
						esc_html__( 'Semi-Bold 600', 'woodmart' ) => 600,
						esc_html__( 'Bold 700', 'woodmart' ) => 700,
						esc_html__( 'Extra-Bold 800', 'woodmart' ) => 800,
						esc_html__( 'Ultra-Bold 900', 'woodmart' ) => 900,
					),
					'selectors'        => array(
						'{{WRAPPER}} .info-box-title' => array(
							'font-weight: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Custom font size', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_font_size',
					'css_args'         => array(
						'font-size' => array(
							' .info-box-title',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Custom line height', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_line_height',
					'css_args'         => array(
						'line-height' => array(
							' .info-box-title',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Title and Subtitle', 'woodmart' ),
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Title style', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_style',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Underline', 'woodmart' ) => 'underlined',
					),
					'images_value'     => array(
						'default'    => WOODMART_ASSETS_IMAGES . '/settings/infobox/title-style/default.png',
						'underlined' => WOODMART_ASSETS_IMAGES . '/settings/infobox/title-style/underlined.png',
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'title_color',
					'css_args'         => array(
						'color' => array(
							' .info-box-title',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Subtitle
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Subtitle', 'woodmart' ),
					'group'      => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name' => 'subtitle_divider',
				),
				array(
					'type'       => 'textarea',
					'heading'    => esc_html__( 'Sub title text', 'woodmart' ),
					'param_name' => 'subtitle',
					'group'      => esc_html__( 'Title and Subtitle', 'woodmart' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_font',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => '',
						$text_font_title      => 'text',
						$primary_font_title   => 'primary',
						$secondary_font_title => 'alt',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Font weight', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_font_weight',
					'value'            => array(
						'' => '',
						esc_html__( 'Ultra-Light 100', 'woodmart' ) => 100,
						esc_html__( 'Light 200', 'woodmart' ) => 200,
						esc_html__( 'Book 300', 'woodmart' ) => 300,
						esc_html__( 'Normal 400', 'woodmart' ) => 400,
						esc_html__( 'Medium 500', 'woodmart' ) => 500,
						esc_html__( 'Semi-Bold 600', 'woodmart' ) => 600,
						esc_html__( 'Bold 700', 'woodmart' ) => 700,
						esc_html__( 'Extra-Bold 800', 'woodmart' ) => 800,
						esc_html__( 'Ultra-Bold 900', 'woodmart' ) => 900,
					),
					'selectors'        => array(
						'{{WRAPPER}} .info-box-subtitle' => array(
							'--wd-font-weight: {{VALUE}};',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Size', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_font_size',
					'css_args'         => array(
						'font-size' => array(
							' .info-box-subtitle',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Line height', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_line_height',
					'css_args'         => array(
						'line-height' => array(
							' .info-box-subtitle',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Title and Subtitle', 'woodmart' ),
				),
				array(
					'type'             => 'woodmart_dropdown',
					'heading'          => esc_html__( 'Predefined subtitle color', 'woodmart' ),
					'param_name'       => 'subtitle_color',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Primary', 'woodmart' ) => 'primary',
						esc_html__( 'Alternative', 'woodmart' ) => 'alt',
					),
					'style'            => array(
						'default' => '#f3f3f3',
						'primary' => woodmart_get_color_value( 'primary-color', '#7eb934' ),
						'alt'     => woodmart_get_color_value( 'secondary-color', '#fbbc34' ),
					),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Custom color', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_custom_color',
					'css_args'         => array(
						'color' => array(
							' .info-box-subtitle',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Subtitle style', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_style',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Background', 'woodmart' ) => 'background',
					),
					'images_value'     => array(
						'default'    => WOODMART_ASSETS_IMAGES . '/settings/subtitle-style/default.png',
						'background' => WOODMART_ASSETS_IMAGES . '/settings/subtitle-style/background.png',
					),
					'edit_field_class' => 'vc_col-sm-12 vc_column',
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Background color', 'woodmart' ),
					'group'            => esc_html__( 'Title and Subtitle', 'woodmart' ),
					'param_name'       => 'subtitle_custom_bg_color',
					'css_args'         => array(
						'background-color' => array(
							' .info-box-subtitle',
						),
					),
					'dependency'       => array(
						'element' => 'subtitle_style',
						'value'   => array( 'background' ),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Content
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Content', 'woodmart' ),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'param_name' => 'style_divider',
				),
				array(
					'type'       => 'textarea_html',
					'holder'     => 'div',
					'heading'    => esc_html__( 'Brief content', 'woodmart' ),
					'group'      => esc_html__( 'Content', 'woodmart' ),
					'param_name' => 'content',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Size', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'param_name'       => 'custom_text_size',
					'css_args'         => array(
						'font-size' => array(
							' .info-box-inner',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_responsive_size',
					'heading'          => esc_html__( 'Line height', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'param_name'       => 'custom_text_line_height',
					'css_args'         => array(
						'line-height' => array(
							' .info-box-inner',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'woodmart_empty_space',
					'param_name' => 'woodmart_empty_space',
					'group'      => esc_html__( 'Content', 'woodmart' ),
				),
				array(
					'type'             => 'woodmart_colorpicker',
					'heading'          => esc_html__( 'Color', 'woodmart' ),
					'group'            => esc_html__( 'Content', 'woodmart' ),
					'param_name'       => 'custom_text_color',
					'css_args'         => array(
						'color' => array(
							' .info-box-inner',
						),
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				/**
				 * Button
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Button', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'button_divider',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Button text', 'woodmart' ),
					'param_name'       => 'btn_text',
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Button position', 'woodmart' ),
					'param_name'       => 'btn_position',
					'value'            => array(
						esc_html__( 'Show on hover', 'woodmart' ) => 'hover',
						esc_html__( 'Static', 'woodmart' ) => 'static',
					),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_dropdown',
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'heading'          => esc_html__( 'Predefined button color', 'woodmart' ),
					'param_name'       => 'btn_color',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Primary color', 'woodmart' ) => 'primary',
						esc_html__( 'Alternative color', 'woodmart' ) => 'alt',
						esc_html__( 'White', 'woodmart' ) => 'white',
						esc_html__( 'Black', 'woodmart' ) => 'black',
					),
					'style'            => array(
						'default' => '#f3f3f3',
						'primary' => woodmart_get_color_value( 'primary-color', '#7eb934' ),
						'alt'     => woodmart_get_color_value( 'secondary-color', '#fbbc34' ),
						'black'   => '#212121',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Button size', 'woodmart' ),
					'param_name'       => 'btn_size',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Extra Small', 'woodmart' ) => 'extra-small',
						esc_html__( 'Small', 'woodmart' ) => 'small',
						esc_html__( 'Large', 'woodmart' ) => 'large',
						esc_html__( 'Extra Large', 'woodmart' ) => 'extra-large',
					),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Button style', 'woodmart' ),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'param_name'       => 'btn_style',
					'value'            => array(
						esc_html__( 'Default', 'woodmart' ) => 'default',
						esc_html__( 'Bordered', 'woodmart' ) => 'bordered',
						esc_html__( 'Link button', 'woodmart' ) => 'link',
						esc_html__( '3D', 'woodmart' ) => '3d',
					),
					'images_value'     => array(
						'default'  => WOODMART_ASSETS_IMAGES . '/settings/buttons/style/default.png',
						'bordered' => WOODMART_ASSETS_IMAGES . '/settings/buttons/style/bordered.png',
						'link'     => WOODMART_ASSETS_IMAGES . '/settings/buttons/style/link.png',
						'3d'       => WOODMART_ASSETS_IMAGES . '/settings/buttons/style/3d.png',
					),
					'title'            => false,
					'std'              => 'default',
					'edit_field_class' => 'vc_col-xs-12 vc_column button-style',
				),
				array(
					'type'             => 'woodmart_image_select',
					'heading'          => esc_html__( 'Button shape', 'woodmart' ),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'param_name'       => 'btn_shape',
					'value'            => array(
						esc_html__( 'Rectangle', 'woodmart' ) => 'rectangle',
						esc_html__( 'Circle', 'woodmart' ) => 'round',
						esc_html__( 'Round', 'woodmart' )  => 'semi-round',
					),
					'images_value'     => array(
						'rectangle'  => WOODMART_ASSETS_IMAGES . '/settings/buttons/shape/rectangle.jpeg',
						'round'      => WOODMART_ASSETS_IMAGES . '/settings/buttons/shape/circle.jpeg',
						'semi-round' => WOODMART_ASSETS_IMAGES . '/settings/buttons/shape/round.jpeg',
					),
					'dependency'       => array(
						'element'            => 'btn_style',
						'value_not_equal_to' => array( 'round', 'link' ),
					),
					'title'            => false,
					'std'              => 'rectangle',
					'edit_field_class' => 'vc_col-xs-12 vc_column button-shape',
				),
				/**
				 * Button icon
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'btn_icon_divider',
				),
				array(
					'type'       => 'woodmart_button_set',
					'heading'    => esc_html__( 'Type', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'btn_icon_type',
					'value'      => array(
						esc_html__( 'Icon', 'woodmart' )  => 'icon',
						esc_html__( 'Image', 'woodmart' ) => 'image',
					),
					'default'    => 'icon',
				),
				array(
					'type'             => 'attach_image',
					'heading'          => esc_html__( 'Image', 'woodmart' ),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'param_name'       => 'btn_image',
					'value'            => '',
					'dependency'       => array(
						'element' => 'btn_icon_type',
						'value'   => 'image',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'textfield',
					'heading'          => esc_html__( 'Image size', 'woodmart' ),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'param_name'       => 'btn_img_size',
					'description'      => esc_html__( 'Example: \'thumbnail\', \'medium\', \'large\', \'full\' or enter image size in pixels: \'200x100\'.', 'woodmart' ),
					'dependency'       => array(
						'element' => 'btn_icon_type',
						'value'   => 'image',
					),
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Icon library', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'value'      => array(
						esc_html__( 'Font Awesome', 'woodmart' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'woodmart' ) => 'openiconic',
						esc_html__( 'Typicons', 'woodmart' ) => 'typicons',
						esc_html__( 'Entypo', 'woodmart' ) => 'entypo',
						esc_html__( 'Linecons', 'woodmart' ) => 'linecons',
						esc_html__( 'Mono Social', 'woodmart' ) => 'monosocial',
						esc_html__( 'Material', 'woodmart' ) => 'material',
					),
					'param_name' => 'icon_library',
					'hint'       => esc_html__( 'Select icon library.', 'woodmart' ),
					'dependency' => array(
						'element' => 'btn_icon_type',
						'value'   => 'icon',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_fontawesome',
					'value'      => '',
					'settings'   => array(
						'emptyIcon'    => true,
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'fontawesome' ),
					),
					'hint'       => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_openiconic',
					'settings'   => array(
						'emptyIcon'    => true,
						'type'         => 'openiconic',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'openiconic' ),
					),
					'hint'       => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_typicons',
					'settings'   => array(
						'emptyIcon'    => true,
						'type'         => 'typicons',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'typicons' ),
					),
					'hint'       => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_entypo',
					'settings'   => array(
						'emptyIcon'    => true,
						'type'         => 'entypo',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'entypo' ),
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_linecons',
					'settings'   => array(
						'emptyIcon'    => true,
						'type'         => 'linecons',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'linecons' ),
					),
					'hint'       => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_monosocial',
					'settings'   => array(
						'emptyIcon'    => true,
						'type'         => 'monosocial',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'monosocial' ),
					),
					'hint'       => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => esc_html__( 'Icon', 'woodmart' ),
					'group'      => esc_html__( 'Button', 'woodmart' ),
					'param_name' => 'icon_material',
					'settings'   => array(
						'emptyIcon'    => true,
						'type'         => 'material',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_library',
						'value'   => array( 'material' ),
					),
					'hint'       => esc_html__( 'Select icon from library.', 'woodmart' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Button icon position', 'woodmart' ),
					'group'            => esc_html__( 'Button', 'woodmart' ),
					'param_name'       => 'btn_icon_position',
					'value'            => array(
						esc_html__( 'Left', 'woodmart' )  => 'left',
						esc_html__( 'Right', 'woodmart' ) => 'right',
					),
					'std'              => 'right',
					'edit_field_class' => 'vc_col-xs-12 vc_column button-style',
				),
				/**
				 * Extra
				 */
				array(
					'type'       => 'woodmart_title_divider',
					'holder'     => 'div',
					'title'      => esc_html__( 'Extra options', 'woodmart' ),
					'param_name' => 'extra_divider',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'SVG animation', 'woodmart' ),
					'param_name'       => 'svg_animation',
					'hint'             => esc_html__( 'By default, your SVG files will not be animated.', 'woodmart' ),
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'woodmart_switch',
					'heading'          => esc_html__( 'Information box inline', 'woodmart' ),
					'param_name'       => 'info_box_inline',
					'true_state'       => 'yes',
					'false_state'      => 'no',
					'default'          => 'no',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
					'type'             => 'vc_link',
					'heading'          => esc_html__( 'Link', 'woodmart' ),
					'param_name'       => 'link',
					'hint'             => esc_html__( 'Enter URL if you want this box to have a link.', 'woodmart' ),
					'edit_field_class' => 'vc_col-xs-12 vc_column',
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'CSS box', 'woodmart' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'js_composer' ),
				),
				function_exists( 'woodmart_get_vc_responsive_spacing_map' ) ? woodmart_get_vc_responsive_spacing_map() : '',
				( function_exists( 'vc_map_add_css_animation' ) ) ? vc_map_add_css_animation( true ) : '',

				function_exists( 'woodmart_get_vc_animation_map' ) ? woodmart_get_vc_animation_map( 'wd_animation' ) : '',
				function_exists( 'woodmart_get_vc_animation_map' ) ? woodmart_get_vc_animation_map( 'wd_animation_delay' ) : '',
				function_exists( 'woodmart_get_vc_animation_map' ) ? woodmart_get_vc_animation_map( 'wd_animation_duration' ) : '',

				array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Extra class name', 'woodmart' ),
					'param_name' => 'el_class',
					'hint'       => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'woodmart' ),
				),
				array(
					'type'             => 'dropdown',
					'heading'          => esc_html__( 'Background position', 'woodmart' ),
					'param_name'       => 'woodmart_bg_position',
					'group'            => esc_html__( 'Design Options', 'js_composer' ),
					'value'            => array(
						esc_html__( 'None', 'woodmart' ) => '',
						esc_html__( 'Left top', 'woodmart' ) => 'left-top',
						esc_html__( 'Left center', 'woodmart' ) => 'left-center',
						esc_html__( 'Left bottom', 'woodmart' ) => 'left-bottom',
						esc_html__( 'Right top', 'woodmart' ) => 'right-top',
						esc_html__( 'Right center', 'woodmart' ) => 'right-center',
						esc_html__( 'Right bottom', 'woodmart' ) => 'right-bottom',
						esc_html__( 'Center top', 'woodmart' ) => 'center-top',
						esc_html__( 'Center center', 'woodmart' ) => 'center-center',
						esc_html__( 'Center bottom', 'woodmart' ) => 'center-bottom',
					),
					'edit_field_class' => 'vc_col-xs-5',
				),
				/**
				 * Advanced.
				 */
				woodmart_get_vc_z_index_map('wd_z_index'),
				woodmart_get_vc_z_index_map('wd_z_index_custom'),
				function_exists( 'woodmart_get_vc_responsive_visible_map' ) ? woodmart_get_vc_responsive_visible_map( 'responsive_tabs_hide' ) : '',
				function_exists( 'woodmart_get_vc_responsive_visible_map' ) ? woodmart_get_vc_responsive_visible_map( 'wd_hide_on_desktop' ) : '',
				function_exists( 'woodmart_get_vc_responsive_visible_map' ) ? woodmart_get_vc_responsive_visible_map( 'wd_hide_on_tablet' ) : '',
				function_exists( 'woodmart_get_vc_responsive_visible_map' ) ? woodmart_get_vc_responsive_visible_map( 'wd_hide_on_mobile' ) : '',
			)
		);
	}
}

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_woodmart_info_box_carousel extends WPBakeryShortCodesContainer {}
}

// Replace Wbc_Inner_Item with your base name from mapping for nested element
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_woodmart_info_box extends WPBakeryShortCode {}
}
