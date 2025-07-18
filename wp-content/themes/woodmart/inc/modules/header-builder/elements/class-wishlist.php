<?php

namespace XTS\Modules\Header_Builder\Elements;

use XTS\Modules\Header_Builder\Element;

/**
 * ------------------------------------------------------------------------------------------------
 *  Wishlist icon in the header elements
 * ------------------------------------------------------------------------------------------------
 */
class Wishlist extends Element {

	public function __construct() {
		parent::__construct();
		$this->template_name = 'wishlist';
	}

	public function map() {
		$this->args = array(
			'type'            => 'wishlist',
			'title'           => esc_html__( 'Wishlist', 'woodmart' ),
			'text'            => esc_html__( 'Wishlist icon', 'woodmart' ),
			'icon'            => 'xts-i-heart',
			'editable'        => true,
			'container'       => false,
			'edit_on_create'  => true,
			'drag_target_for' => array(),
			'drag_source'     => 'content_element',
			'removable'       => true,
			'addable'         => true,
			'params'          => array(
				'design'              => array(
					'id'          => 'design',
					'title'       => esc_html__( 'Display', 'woodmart' ),
					'type'        => 'selector',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'value'       => 'icon',
					'options'     => array(
						'icon' => array(
							'value' => 'icon',
							'label' => esc_html__( 'Icon', 'woodmart' ),
						),
						'text' => array(
							'value' => 'text',
							'label' => esc_html__( 'Icon with text', 'woodmart' ),
						),
					),
					'description' => esc_html__( 'You can show the icon only or display "Wishlist" text too.', 'woodmart' ),
				),
				'icon_design'         => array(
					'id'      => 'icon_design',
					'title'   => esc_html__( 'Icon design', 'woodmart' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'Style', 'woodmart' ),
					'group'   => esc_html__( 'Icon', 'woodmart' ),
					'value'   => '2',
					'options' => array(
						'1' => array(
							'value' => '1',
							'label' => esc_html__( 'First', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/wishlist-icons/first.jpg',
						),
						'2' => array(
							'value' => '2',
							'label' => esc_html__( 'Second', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/wishlist-icons/second.jpg',
						),
						'4' => array(
							'value' => '4',
							'label' => esc_html__( 'Third', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/wishlist-icons/third.jpg',
						),
						'6' => array(
							'value' => '6',
							'label' => esc_html__( 'Fourth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/wishlist-icons/fourth.jpg',
						),
						'7' => array(
							'value' => '7',
							'label' => esc_html__( 'Fifth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/wishlist-icons/fifth.jpg',
						),
						'8' => array(
							'value' => '8',
							'label' => esc_html__( 'Sixth', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/wishlist-icons/sixth.jpg',
						),
					),
				),
				'wrap_type'           => array(
					'id'       => 'wrap_type',
					'title'    => esc_html__( 'Background wrap type', 'woodmart' ),
					'type'     => 'selector',
					'tab'      => esc_html__( 'Style', 'woodmart' ),
					'group'    => esc_html__( 'Icon', 'woodmart' ),
					'value'    => 'icon_only',
					'options'  => array(
						'icon_only'     => array(
							'value' => 'icon_only',
							'label' => esc_html__( 'Icon only', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/wishlist-wrap-icon.jpg',
						),
						'icon_and_text' => array(
							'value' => 'icon_and_text',
							'label' => esc_html__( 'Icon and text', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/bg-wrap-type/wishlist-wrap-icon-and-text.jpg',
						),
					),
					'requires' => array(
						'design'      => array(
							'comparison' => 'equal',
							'value'      => 'text',
						),
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '6', '7' ),
						),
					),
				),
				'color'               => array(
					'id'          => 'color',
					'title'       => esc_html__( 'Color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'hover_color'         => array(
					'id'          => 'hover_color',
					'title'       => esc_html__( 'Hover color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_color'            => array(
					'id'          => 'bg_color',
					'title'       => esc_html__( 'Background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element > a > .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'bg_hover_color'      => array(
					'id'          => 'bg_hover_color',
					'title'       => esc_html__( 'Hover background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'whb-row .{{WRAPPER}}.wd-tools-element:hover .wd-tools-inner, .whb-row .{{WRAPPER}}.wd-tools-element:hover > a > .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => array( '7', '8' ),
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_color'          => array(
					'id'          => 'icon_color',
					'title'       => esc_html__( 'Icon color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8 .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_hover_color'    => array(
					'id'          => 'icon_hover_color',
					'title'       => esc_html__( 'Hover icon color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8:hover .wd-tools-icon' => array(
							'color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_color'       => array(
					'id'          => 'icon_bg_color',
					'title'       => esc_html__( 'Icon background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8 .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_bg_hover_color' => array(
					'id'          => 'icon_bg_hover_color',
					'title'       => esc_html__( 'Hover icon background color', 'woodmart' ),
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'type'        => 'color',
					'value'       => '',
					'selectors'   => array(
						'{{WRAPPER}}.wd-tools-element.wd-design-8:hover .wd-tools-icon' => array(
							'background-color: {{VALUE}};',
						),
					),
					'requires'    => array(
						'icon_design' => array(
							'comparison' => 'equal',
							'value'      => '8',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'icon_type'           => array(
					'id'      => 'icon_type',
					'title'   => esc_html__( 'Icon type', 'woodmart' ),
					'type'    => 'selector',
					'tab'     => esc_html__( 'Style', 'woodmart' ),
					'group'   => esc_html__( 'Icon', 'woodmart' ),
					'value'   => 'default',
					'options' => array(
						'default' => array(
							'value' => 'default',
							'label' => esc_html__( 'Default', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/default-icons/wishlist-default.jpg',
						),
						'custom'  => array(
							'value' => 'custom',
							'label' => esc_html__( 'Custom', 'woodmart' ),
							'image' => WOODMART_ASSETS_IMAGES . '/header-builder/upload.jpg',
						),
					),
				),
				'custom_icon'         => array(
					'id'          => 'custom_icon',
					'title'       => esc_html__( 'Upload an image', 'woodmart' ),
					'type'        => 'image',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'value'       => '',
					'description' => '',
					'requires'    => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'custom_icon_width'   => array(
					'id'          => 'custom_icon_width',
					'title'       => esc_html__( 'Icon width', 'woodmart' ),
					'type'        => 'slider',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Icon', 'woodmart' ),
					'from'        => 0,
					'to'          => 60,
					'value'       => 0,
					'units'       => 'px',
					'selectors'   => array(
						'{{WRAPPER}}' => array(
							'--wd-tools-icon-width: {{VALUE}}px;',
						),
					),
					'requires'    => array(
						'icon_type' => array(
							'comparison' => 'equal',
							'value'      => 'custom',
						),
					),
					'extra_class' => 'xts-col-6',
				),
				'hide_product_count'  => array(
					'id'          => 'hide_product_count',
					'title'       => esc_html__( 'Hide product count label', 'woodmart' ),
					'hint'        => '<video src="' . WOODMART_TOOLTIP_URL . 'hb_wishlist_hide_product_count.mp4" autoplay loop muted></video>',
					'type'        => 'switcher',
					'tab'         => esc_html__( 'Style', 'woodmart' ),
					'group'       => esc_html__( 'Extra', 'woodmart' ),
					'value'       => false,
					'description' => esc_html__( 'Mark this option if you want to hide product count label.', 'woodmart' ),
				),
			),
		);
	}
}
