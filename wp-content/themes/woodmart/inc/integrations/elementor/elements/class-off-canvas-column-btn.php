<?php
/**
 * Off canvas sidebar button element.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;
use XTS\Modules\Layouts\Global_Data as Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 */
class Off_Canvas_Column_Btn extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_builder_off_canvas_column_btn';
	}

	/**
	 * Get widget content.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Off canvas column button', 'woodmart' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'wd-icon-off-canvas-column-button';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'off', 'canvas', 'column', 'button', 'btn' );
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wd-elements' );
	}

	/**
	 * Register the widget controls.
	 */
	protected function register_controls() {
		$sticky_key = woodmart_is_elementor_pro_installed() ? 'wd_sticky' : 'sticky';
		/**
		 * Content tab
		 */

		/**
		 * General settings
		 */
		$this->start_controls_section(
			'general_content_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button text', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Show column',
			)
		);

		$this->add_control(
			$sticky_key,
			array(
				'label'        => esc_html__( 'Sticky', 'woodmart' ),
				'description'  => esc_html__( 'Add an additional sticky button.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'only_sticky_button',
			array(
				'label'        => esc_html__( 'Only sticky button', 'woodmart' ),
				'description'  => esc_html__( 'Hide the static button and show only the sticky one.', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'return_value' => 'yes',
				'condition'    => array(
					$sticky_key => 'yes',
				),
			)
		);

		$this->add_control(
			'css_classes',
			array(
				'type'         => 'wd_css_class',
				'default'      => 'wd-action-hide-btn',
				'prefix_class' => '',
				'condition'    => array(
					$sticky_key          => 'yes',
					'only_sticky_button' => array( 'yes' ),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Icon settings.
		 */
		$this->start_controls_section(
			'icon_style_section',
			array(
				'label' => esc_html__( 'Icon', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Type', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'without' => esc_html__( 'Without icon', 'woodmart' ),
					'default' => esc_html__( 'Default', 'woodmart' ),
					'custom'  => esc_html__( 'Custom image', 'woodmart' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'Choose image', 'woodmart' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'icon_type' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'icon',
				'default'   => 'thumbnail',
				'separator' => 'none',
				'condition' => array(
					'icon_type' => array( 'custom' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render() {
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'button_text' => 'Show column',
				'icon_type'   => 'default',
				'icon'        => array(),
				'sticky'      => '',
				'wd_sticky'   => '',
			)
		);
		woodmart_enqueue_js_script( 'off-canvas-colum-btn' );
		woodmart_enqueue_inline_style( 'el-off-canvas-column-btn' );

		Builder::get_instance()->set_data( 'wd_show_sticky_sidebar_button', true );

		// Icon settings.
		$icon_output               = '';
		$off_canvas_classes        = '';
		$sticky_off_canvas_classes = '';

		if ( 'custom' !== $settings['icon_size'] ) {
			$icon_size = $settings['icon_size'];
		} elseif ( ! empty( $settings['icon_custom_dimension']['width'] ) ) {
			$icon_size = $settings['icon_custom_dimension'];
		} else {
			$icon_size = array( 20, 20 );
		}

		if ( 'default' === $settings['icon_type'] ) {
			$off_canvas_classes        .= ' wd-burger-icon';
			$sticky_off_canvas_classes .= ' wd-burger-icon';
		} elseif ( 'custom' === $settings['icon_type'] ) {
			$off_canvas_classes        .= ' wd-action-custom-icon';
			$sticky_off_canvas_classes .= ' wd-action-custom-icon';
		}

		$off_canvas_classes .= woodmart_get_old_classes( ' woodmart-show-sidebar-btn' );

		if ( 'custom' === $settings['icon_type'] && ! empty( $settings['icon']['id'] ) ) {
			if ( woodmart_is_svg( $settings['icon']['url'] ) ) {
				$icon_output = woodmart_get_svg_html(
					$settings['icon']['id'],
					$icon_size
				);
			} else {
				$icon_output = woodmart_otf_get_image_html( $settings['icon']['id'], $settings['icon_size'], $settings['icon_custom_dimension'] );
			}
		}

		if ( 'yes' === $settings['sticky'] || 'yes' === $settings['wd_sticky'] ) {
			woodmart_enqueue_inline_style( 'mod-sticky-sidebar-opener' );
			woodmart_enqueue_js_script( 'sticky-sidebar-btn' );
		}

		woodmart_enqueue_inline_style( 'off-canvas-sidebar' );
		?>

		<div class="wd-off-canvas-btn wd-action-btn wd-style-text<?php echo esc_html( $off_canvas_classes ); ?>">
			<a href="#" rel="nofollow">
				<?php if ( ! empty( $icon_output ) ) : ?>
					<span class="wd-action-icon">
						<?php echo $icon_output; //phpcs:ignore; ?>
					</span>
				<?php endif; ?>
				<?php echo esc_html( $settings['button_text'] ); ?>
			</a>
		</div>

		<?php if ( 'yes' === $settings['sticky'] || 'yes' === $settings['wd_sticky'] ) : ?>
			<div class="wd-sidebar-opener wd-show-on-scroll wd-action-btn wd-style-icon<?php echo esc_html( $sticky_off_canvas_classes ); ?>">
				<a href="#" rel="nofollow">
					<?php if ( ! empty( $icon_output ) ) : ?>
						<span class="wd-action-icon">
							<?php echo $icon_output; //phpcs:ignore; ?>
						</span>
					<?php endif; ?>
				</a>
			</div>
		<?php endif; ?>
		<?php
	}
}

Plugin::instance()->widgets_manager->register( new Off_Canvas_Column_Btn() );
