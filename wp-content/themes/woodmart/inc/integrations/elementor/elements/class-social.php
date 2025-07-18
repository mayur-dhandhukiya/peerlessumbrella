<?php
/**
 * Social buttons map.
 */

namespace XTS\Elementor;

use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Elementor widget that inserts an embeddable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Social extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wd_social_buttons';
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
		return esc_html__( 'Social buttons', 'woodmart' );
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
		return 'wd-icon-social';
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
		return array( 'wd-elements' );
	}

	/**
	 * Register the widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		/**
		 * Content tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_content_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label'        => esc_html__( 'Label', 'woodmart' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => esc_html__( 'On', 'woodmart' ),
				'label_off'    => esc_html__( 'Off', 'woodmart' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'label_text',
			array(
				'label'     => esc_html__( 'Label text', 'woodmart' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Share: ', 'woodmart' ),
				'condition' => array(
					'show_label' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'type',
			array(
				'label'   => esc_html__( 'Type', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'share'  => esc_html__( 'Share', 'woodmart' ),
					'follow' => esc_html__( 'Follow', 'woodmart' ),
				),
				'default' => 'share',
			)
		);

		$this->add_control(
			'social_links_source',
			array(
				'label'     => esc_html__( 'Social links source', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'theme_settings' => esc_html__( 'Theme settings', 'woodmart' ),
					'custom'         => esc_html__( 'Custom', 'woodmart' ),
				),
				'default'   => 'theme_settings',
				'condition' => array(
					'type' => array( 'follow' ),
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Links to social profiles.
		 */
		$this->start_controls_section(
			'social_links_content_section',
			array(
				'label'     => esc_html__( 'Links to social profiles', 'woodmart' ),
				'condition' => array(
					'type'                => array( 'follow' ),
					'social_links_source' => array( 'custom' ),
				),
			)
		);

		$this->add_control(
			'fb_link',
			array(
				'label'   => esc_html__( 'Facebook link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'twitter_link',
			array(
				'label'   => esc_html__( 'X link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'bluesky_link',
			array(
				'label'   => esc_html__( 'Bluesky link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'isntagram_link',
			array(
				'label'   => esc_html__( 'Instagram link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'threads_link',
			array(
				'label'   => esc_html__( 'Threads link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'pinterest_link',
			array(
				'label'   => esc_html__( 'Pinterest link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'youtube_link',
			array(
				'label'   => esc_html__( 'YouTube link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '#',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'tumblr_link',
			array(
				'label'   => esc_html__( 'Tumblr link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'linkedin_link',
			array(
				'label'   => esc_html__( 'LinkedIn link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'vimeo_link',
			array(
				'label'   => esc_html__( 'Vimeo link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'flickr_link',
			array(
				'label'   => esc_html__( 'Flickr link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'github_link',
			array(
				'label'   => esc_html__( 'Github link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'dribbble_link',
			array(
				'label'   => esc_html__( 'Dribbble link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'behance_link',
			array(
				'label'   => esc_html__( 'Behance link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'soundcloud_link',
			array(
				'label'   => esc_html__( 'SoundCloud link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'spotify_link',
			array(
				'label'   => esc_html__( 'Spotify link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'skype_link',
			array(
				'label'   => esc_html__( 'Skype link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'whatsapp_link',
			array(
				'label'   => esc_html__( 'WhatsApp link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'snapchat_link',
			array(
				'label'   => esc_html__( 'Snapchat link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'tg_link',
			array(
				'label'   => esc_html__( 'Telegram link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'viber_link',
			array(
				'label'   => esc_html__( 'Viber link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'tiktok_link',
			array(
				'label'   => esc_html__( 'TikTok link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'discord_link',
			array(
				'label'   => esc_html__( 'Discord link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'yelp_link',
			array(
				'label'   => esc_html__( 'Yelp link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'vk_link',
			array(
				'label'   => esc_html__( 'VK link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->add_control(
			'ok_link',
			array(
				'label'   => esc_html__( 'OK link', 'woodmart' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'ai'      => array(
					'active' => false,
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Style tab.
		 */

		/**
		 * General settings.
		 */
		$this->start_controls_section(
			'general_style_section',
			array(
				'label' => esc_html__( 'General', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'     => esc_html__( 'Layout', 'woodmart' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''        => esc_html__( 'Default', 'woodmart' ),
					'justify' => esc_html__( 'Justify', 'woodmart' ),
					'inline'  => esc_html__( 'Inline', 'woodmart' ),
				),
				'default'   => '',
				'condition' => array(
					'show_label' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'align',
			array(
				'label'   => esc_html__( 'Align', 'woodmart' ),
				'type'    => 'wd_buttons',
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/left.jpg',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/center.jpg',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woodmart' ),
						'image' => WOODMART_ASSETS_IMAGES . '/settings/align/right.jpg',
					),
				),
				'default' => 'center',
			)
		);

		$this->end_controls_section();

		/**
		 * Icons settings.
		 */
		$this->start_controls_section(
			'icons_style_section',
			array(
				'label' => esc_html__( 'Icons', 'woodmart' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => esc_html__( 'Style', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default'     => esc_html__( 'Default', 'woodmart' ),
					'simple'      => esc_html__( 'Simple', 'woodmart' ),
					'colored'     => esc_html__( 'Colored', 'woodmart' ),
					'colored-alt' => esc_html__( 'Colored alternative', 'woodmart' ),
					'bordered'    => esc_html__( 'Bordered', 'woodmart' ),
					'primary'     => esc_html__( 'Primary color', 'woodmart' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'form',
			array(
				'label'   => esc_html__( 'Shape', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'circle'  => esc_html__( 'Circle', 'woodmart' ),
					'square'  => esc_html__( 'Square', 'woodmart' ),
					'rounded' => esc_html__( 'Rounded', 'woodmart' ),
				),
				'default' => 'circle',
			)
		);

		$this->add_control(
			'size',
			array(
				'label'   => esc_html__( 'Size', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__( 'Default', 'woodmart' ),
					'small'   => esc_html__( 'Small', 'woodmart' ),
					'large'   => esc_html__( 'Large', 'woodmart' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'color',
			array(
				'label'   => esc_html__( 'Color', 'woodmart' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'dark'  => esc_html__( 'Dark', 'woodmart' ),
					'light' => esc_html__( 'Light', 'woodmart' ),
				),
				'default' => 'dark',
			)
		);

		$this->end_controls_section();

		/**
		 * Label settings.
		 */
		$this->start_controls_section(
			'label_style_section',
			array(
				'label'     => esc_html__( 'Label', 'woodmart' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_label' => array( 'yes' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'     => esc_html__( 'Typography', 'woodmart' ),
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .wd-social-icons .wd-label',
				'condition' => array(
					'show_label' => array( 'yes' ),
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Label color', 'woodmart' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wd-social-icons .wd-label' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'show_label' => array( 'yes' ),
				),
			)
		);

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
		$settings = wp_parse_args(
			$this->get_settings_for_display(),
			array(
				'elementor'           => true,
				'layout'              => '',
				'social_links_source' => 'theme_settings',
				// Label settings.
				'show_label'          => 'no',
				'label_text'          => esc_html__( 'Share: ', 'woodmart' ),
				'is_element'          => true,
			)
		);

		woodmart_shortcode_social( $settings );
	}
}

Plugin::instance()->widgets_manager->register( new Social() );
