<?php
/**
 * Extra menu list map
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Extra_Menu_List extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_extra_menu_list';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Extra menu list', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-extra-menu-list';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'wd-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Content tab
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_section',
			[
				'label' => esc_html__( 'General', 'woodmart' ),
			]
		);

		$this->start_controls_tabs( 'extra_menu_tabs' );

		$this->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'woodmart' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Menu parent item',
			]
		);

		$this->add_control(
			'link',
			[
				'label'   => esc_html__( 'Link', 'woodmart' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_tab',
			[
				'label' => esc_html__( 'Label', 'woodmart' ),
			]
		);

		$this->add_control(
			'label',
			[
				'label' => esc_html__( 'Label text (optional)', 'woodmart' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'   => esc_html__( 'Label color', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'primary'   => esc_html__( 'Primary Color', 'woodmart' ),
					'secondary' => esc_html__( 'Secondary', 'woodmart' ),
					'red'       => esc_html__( 'Red', 'woodmart' ),
					'green'     => esc_html__( 'Green', 'woodmart' ),
					'blue'      => esc_html__( 'Blue', 'woodmart' ),
					'orange'    => esc_html__( 'Orange', 'woodmart' ),
					'grey'      => esc_html__( 'Grey', 'woodmart' ),
					'white'     => esc_html__( 'White', 'woodmart' ),
					'black'     => esc_html__( 'Black', 'woodmart' ),
				],
				'default' => 'primary',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Image', 'woodmart' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose image', 'woodmart' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'extra_menu_tabs' );

		$repeater->start_controls_tab(
			'link_tab',
			[
				'label' => esc_html__( 'Link', 'woodmart' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'   => esc_html__( 'Title', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Menu child item',
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'   => esc_html__( 'Link', 'woodmart' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'label_tab',
			[
				'label' => esc_html__( 'Label', 'woodmart' ),
			]
		);

		$repeater->add_control(
			'label',
			[
				'label' => esc_html__( 'Label text (optional)', 'woodmart' ),
				'type'  => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'label_color',
			[
				'label'   => esc_html__( 'Label color', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'primary'   => esc_html__( 'Primary Color', 'woodmart' ),
					'secondary' => esc_html__( 'Secondary', 'woodmart' ),
					'red'       => esc_html__( 'Red', 'woodmart' ),
					'green'     => esc_html__( 'Green', 'woodmart' ),
					'blue'      => esc_html__( 'Blue', 'woodmart' ),
					'orange'    => esc_html__( 'Orange', 'woodmart' ),
					'grey'      => esc_html__( 'Grey', 'woodmart' ),
					'white'     => esc_html__( 'White', 'woodmart' ),
					'black'     => esc_html__( 'Black', 'woodmart' ),
				],
				'default' => 'primary',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'image_tab',
			[
				'label' => esc_html__( 'Image', 'woodmart' ),
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose image', 'woodmart' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'thumbnail',
				'separator' => 'none',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'menu_items_repeater',
			[
				'type'        => Controls_Manager::REPEATER,
				'label'       => esc_html__( 'List items', 'woodmart' ),
				'separator'   => 'before',
				'title_field' => '{{{ title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'       => 'Menu child item 1',
						'label'       => '',
						'label_color' => 'primary',
						'link'        => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item  2',
						'label'       => 'New',
						'label_color' => 'green',
						'link'        => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 3',
						'label'       => '',
						'label_color' => 'primary',
						'link'        => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 4',
						'label'       => '',
						'label_color' => 'primary',
						'link'        => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 5',
						'label'       => 'Hot',
						'label_color' => 'red',
						'link'        => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
					[
						'title'       => 'Menu child item 6',
						'label'       => '',
						'label_color' => 'primary',
						'link'        => [
							'url'         => '#',
							'is_external' => false,
							'nofollow'    => false,
						],
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__( 'Title', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .wd-sub-accented > li > a',
			)
		);

		$this->start_controls_tabs(
			'title_color_tabs',
		);

		$this->start_controls_tab(
			'title_color_tab',
			array(
				'label' => esc_html__( 'Idle', 'woodmart' ),
			)
		);

		$this->add_control(
			'title_idle_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-sub-accented > li > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_hover_color_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-sub-accented > li > a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'items_style_section',
			array(
				'label' => esc_html__( 'Items', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'label'    => esc_html__( 'Typography', 'woodmart' ),
				'selector' => '{{WRAPPER}} .sub-sub-menu > li > a',
			)
		);

		$this->start_controls_tabs(
			'items_color_tabs',
		);

		$this->start_controls_tab(
			'items_color_tab',
			array(
				'label' => esc_html__( 'Idle', 'woodmart' ),
			)
		);

		$this->add_control(
			'items_idle_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .sub-sub-menu > li > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'items_hover_color_tab',
			array(
				'label' => esc_html__( 'Hover', 'woodmart' ),
			)
		);

		$this->add_control(
			'items_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .sub-sub-menu > li:hover > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$default_settings = [
			'title'               => '',
			'link'                => '',
			'image'               => '',
			'label'               => '',
			'label_color'         => 'primary',
			'menu_items_repeater' => [],
		];

		$settings = wp_parse_args( $this->get_settings_for_display(), $default_settings );

		$this->add_render_attribute(
			[
				'parent_ul'    => [
					'class' => [
						'wd-sub-menu',
						'wd-sub-accented',
						woodmart_get_old_classes( 'sub-menu' ),
						'mega-menu-list',
					],
				],
				'parent_li'    => [
					'class' => [
						'item-with-label',
						'item-label-' . $settings['label_color'],
					],
				],
				'parent_title' => [
					'class' => [
						'nav-link-text',
					],
				],
				'parent_label' => [
					'class' => [
						'menu-label',
						'menu-label-' . $settings['label_color'],
					],
				],
			]
		);

		$this->add_inline_editing_attributes( 'parent_title' );
		$this->add_inline_editing_attributes( 'parent_label' );

		$link_attrs = woodmart_get_link_attrs( $settings['link'] );

		// Image settings.
		$image_output      = '';
		$custom_image_size = isset( $settings['image_custom_dimension']['width'] ) && $settings['image_custom_dimension']['width'] ? $settings['image_custom_dimension'] : array(
			'width'  => 128,
			'height' => 128,
		);

		if ( isset( $settings['image']['id'] ) && $settings['image']['id'] ) {
			$image_output = woodmart_otf_get_image_html( $settings['image']['id'], $settings['image_size'], $settings['image_custom_dimension'] );

			if ( woodmart_is_svg( $settings['image']['url'] ) ) {
				$custom_image_size = 'custom' !== $settings['image_size'] ? $settings['image_size'] : $custom_image_size;
				$image_output      = woodmart_get_svg_html( $settings['image']['id'], $custom_image_size );
			}
		}

		woodmart_enqueue_inline_style( 'mod-nav-menu-label' );
		?>
			<ul <?php echo $this->get_render_attribute_string( 'parent_ul' ); ?>>
				<li <?php echo $this->get_render_attribute_string( 'parent_li' ); ?>>
					<?php if ( $settings['title'] ) : ?>
						<a <?php echo $link_attrs; ?>>
							<?php if ( $settings['image'] ) : ?>
								<?php echo $image_output; // phpcs:ignore. ?>
							<?php endif; ?>

							<?php if ( woodmart_elementor_is_edit_mode() ) : ?>
								<span <?php echo $this->get_render_attribute_string( 'parent_title' ); ?>>
							<?php endif; ?>
								<?php echo wp_kses( $settings['title'], woodmart_get_allowed_html() ); ?>
							<?php if ( woodmart_elementor_is_edit_mode() ) : ?>
								</span>
							<?php endif; ?>

							<?php if ( $settings['label'] ) : ?>
								<span <?php echo $this->get_render_attribute_string( 'parent_label' ); ?>>
									<?php echo wp_kses( $settings['label'], woodmart_get_allowed_html() ); ?>
								</span>
							<?php endif; ?>
						</a>
					<?php endif; ?>

					<ul class="sub-sub-menu">
						<?php foreach ( $settings['menu_items_repeater'] as $index => $item ) : ?>
							<?php
							$repeater_li_key    = $this->get_repeater_setting_key( 'li', 'menu_items_repeater', $index );
							$repeater_title_key = $this->get_repeater_setting_key( 'title', 'menu_items_repeater', $index );
							$repeater_label_key = $this->get_repeater_setting_key( 'label', 'menu_items_repeater', $index );

							$this->add_render_attribute(
								[
									$repeater_li_key    => [
										'class' => [
											'item-with-label',
											'item-label-' . $item['label_color'],
										],
									],
									$repeater_label_key => [
										'class' => [
											'menu-label',
											'menu-label-' . $item['label_color'],
										],
									],
								]
							);

							$this->add_inline_editing_attributes( $repeater_title_key );
							$this->add_inline_editing_attributes( $repeater_label_key );

							$link_attrs = woodmart_get_link_attrs( $item['link'] );
							?>

							<li <?php echo $this->get_render_attribute_string( $repeater_li_key ); ?>>
								<a <?php echo $link_attrs; ?>>
									<?php if ( $item['image'] ) : ?>
										<?php echo woodmart_otf_get_image_html( $item['image']['id'], $item['image_size'], $item['image_custom_dimension'] );
										?>
									<?php endif; ?>

									<?php if ( woodmart_elementor_is_edit_mode() ) : ?>
										<span <?php echo $this->get_render_attribute_string( $repeater_title_key ); ?>>
									<?php endif; ?>
										<?php echo wp_kses( $item['title'], woodmart_get_allowed_html() ); ?>
									<?php if ( woodmart_elementor_is_edit_mode() ) : ?>
										</span>
									<?php endif; ?>

									<?php if ( $item['label'] ) : ?>
										<span <?php echo $this->get_render_attribute_string( $repeater_label_key ); ?>>
											<?php echo wp_kses( $item['label'], woodmart_get_allowed_html() ); ?>
										</span>
									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			</ul>
		<?php
	}
}

Plugin::instance()->widgets_manager->register( new Extra_Menu_List() );
