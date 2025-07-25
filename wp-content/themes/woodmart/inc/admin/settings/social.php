<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Admin\Modules\Options;

Options::add_field(
	array(
		'id'          => 'sticky_social',
		'name'        => esc_html__( 'Sticky social links', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'sticky-social-links.mp4" autoplay loop muted></video>',
		'description' => esc_html__( 'Social buttons will be fixed on the screen when you scroll the page.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'social_profiles',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'sticky_social_type',
		'name'     => esc_html__( 'Sticky social links type', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'social_profiles',
		'default'  => 'follow',
		'options'  => array(
			'share'  => array(
				'name'  => esc_html__( 'Share', 'woodmart' ),
				'value' => 'share',
			),
			'follow' => array(
				'name'  => esc_html__( 'Follow', 'woodmart' ),
				'value' => 'follow',
			),
		),
		'priority' => 20,
		'requires' => array(
			array(
				'key'     => 'sticky_social',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'sticky_social_position',
		'name'     => esc_html__( 'Sticky social links position', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'social_profiles',
		'default'  => 'right',
		'options'  => array(
			'left'  => array(
				'name'  => esc_html__( 'Left', 'woodmart' ),
				'value' => 'left',
			),
			'right' => array(
				'name'  => esc_html__( 'Right', 'woodmart' ),
				'value' => 'right',
			),
		),
		'priority' => 20,
		'requires' => array(
			array(
				'key'     => 'sticky_social',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

/**
 * Links to social profiles.
 */
Options::add_field(
	array(
		'id'       => 'social_follow_notice',
		'type'     => 'notice',
		'style'    => 'info',
		'name'     => '',
		'content'  => esc_html__( 'Configure your [social_buttons] shortcode. You can leave the field empty to remove a particular link. Note that there are two types of social buttons. First one is SHARE buttons [social_buttons type="share"]. It displays icons that share your page on social media. And the second one is FOLLOW buttons [social_buttons type="follow"]. Simply displays links to your social profiles. You can configure both types here.', 'woodmart' ),
		'section'  => 'social_links',
		'priority' => 9,
	)
);

Options::add_field(
	array(
		'id'       => 'fb_link',
		'name'     => esc_html__( 'Facebook link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '#',
		'priority' => 10,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'twitter_link',
		'name'     => esc_html__( 'X link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '#',
		'priority' => 20,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'bluesky_link',
		'name'     => esc_html__( 'Bluesky link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 30,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'isntagram_link',
		'name'     => esc_html__( 'Instagram', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '#',
		'priority' => 40,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'threads_link',
		'name'     => esc_html__( 'Threads link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 50,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'pinterest_link',
		'name'     => esc_html__( 'Pinterest link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '#',
		'priority' => 60,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'youtube_link',
		'name'     => esc_html__( 'YouTube link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '#',
		'priority' => 70,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'tumblr_link',
		'name'     => esc_html__( 'Tumblr link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 80,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'linkedin_link',
		'name'     => esc_html__( 'LinkedIn link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 90,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'vimeo_link',
		'name'     => esc_html__( 'Vimeo link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 100,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'flickr_link',
		'name'     => esc_html__( 'Flickr link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 110,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'github_link',
		'name'     => esc_html__( 'Github link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 120,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'dribbble_link',
		'name'     => esc_html__( 'Dribbble link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 130,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'behance_link',
		'name'     => esc_html__( 'Behance link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 140,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'soundcloud_link',
		'name'     => esc_html__( 'SoundCloud link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 150,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'spotify_link',
		'name'     => esc_html__( 'Spotify link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 160,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'skype_link',
		'name'     => esc_html__( 'Skype link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 170,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'whatsapp_link',
		'name'     => esc_html__( 'WhatsApp link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 180,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'snapchat_link',
		'name'     => esc_html__( 'Snapchat link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 190,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'tg_link',
		'name'     => esc_html__( 'Telegram link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 200,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'viber_link',
		'name'     => esc_html__( 'Viber link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 210,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'tiktok_link',
		'name'     => esc_html__( 'TikTok link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 220,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'discord_link',
		'name'     => esc_html__( 'Discord link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 230,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'yelp_link',
		'name'     => esc_html__( 'Yelp link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 240,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'vk_link',
		'name'     => esc_html__( 'VK link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 250,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'ok_link',
		'name'     => esc_html__( 'OK link', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'social_links',
		'default'  => '',
		'priority' => 260,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'social_email_links',
		'name'     => esc_html__( 'Email for social links', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_links',
		'default'  => false,
		'priority' => 270,
		'status'   => 'deprecated',
	)
);

/**
 * Share buttons.
 */
Options::add_field(
	array(
		'id'       => 'social_share_notice',
		'type'     => 'notice',
		'style'    => 'info',
		'name'     => '',
		'content'  => esc_html__( 'Configure your [social_buttons] shortcode. You can leave the field empty to remove a particular link. Note that there are two types of social buttons. First one is SHARE buttons [social_buttons type="share"]. It displays icons that share your page on social media. And the second one is FOLLOW buttons [social_buttons type="follow"]. Simply displays links to your social profiles. You can configure both types here.', 'woodmart' ),
		'section'  => 'social_share',
		'priority' => 9,
	)
);

Options::add_field(
	array(
		'id'       => 'share_fb',
		'name'     => esc_html__( 'Share in Facebook', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => '1',
		'priority' => 10,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_twitter',
		'name'     => esc_html__( 'Share in X', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => '1',
		'priority' => 20,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_pinterest',
		'name'     => esc_html__( 'Share in Pinterest', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => '1',
		'priority' => 30,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_linkedin',
		'name'     => esc_html__( 'Share in LinkedIn', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => '1',
		'priority' => 40,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_whatsapp',
		'name'     => esc_html__( 'Share in Whatsapp', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => false,
		'priority' => 50,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_tg',
		'name'     => esc_html__( 'Share in Telegram', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => '1',
		'priority' => 60,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_viber',
		'name'     => esc_html__( 'Share in Viber', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => false,
		'priority' => 70,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_vk',
		'name'     => esc_html__( 'Share in VK', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => false,
		'priority' => 80,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_ok',
		'name'     => esc_html__( 'Share in OK', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => false,
		'priority' => 90,
		'class'    => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'       => 'share_email',
		'name'     => esc_html__( 'Email for share links', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'social_share',
		'default'  => false,
		'priority' => 110,
		'class'    => 'xts-col-6',
	)
);
