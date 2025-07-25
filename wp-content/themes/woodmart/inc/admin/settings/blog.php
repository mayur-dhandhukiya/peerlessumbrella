<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Admin\Modules\Options;

Options::add_field(
	array(
		'id'          => 'blog_layout',
		'name'        => esc_html__( 'Sidebar position', 'woodmart' ),
		'description' => esc_html__( 'Select main content and sidebar alignment for blog pages.', 'woodmart' ),
		'group'       => esc_html__( 'Sidebar', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_section',
		'options'     => array(
			'full-width'    => array(
				'name'  => esc_html__( '1 Column', 'woodmart' ),
				'value' => 'full-width',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/none.png',
			),
			'sidebar-left'  => array(
				'name'  => esc_html__( '2 Columns Left', 'woodmart' ),
				'value' => 'sidebar-left',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/left.png',
			),
			'sidebar-right' => array(
				'name'  => esc_html__( '2 Columns Right', 'woodmart' ),
				'value' => 'sidebar-right',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/right.png',
			),
		),
		'default'     => 'sidebar-right',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_sidebar_width',
		'name'        => esc_html__( 'Sidebar size', 'woodmart' ),
		'description' => esc_html__( 'You can set different sizes for your blog pages sidebar', 'woodmart' ),
		'group'       => esc_html__( 'Sidebar', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_section',
		'options'     => array(
			2 => array(
				'name'  => esc_html__( 'Small', 'woodmart' ),
				'value' => 2,
			),
			3 => array(
				'name'  => esc_html__( 'Medium', 'woodmart' ),
				'value' => 2,
			),
			4 => array(
				'name'  => esc_html__( 'Large', 'woodmart' ),
				'value' => 2,
			),
		),
		'default'     => 3,
		'priority'    => 20,
		'class'       => 'xts-tooltip-bordered',
	)
);

Options::add_field(
	array(
		'id'          => 'blog_hide_sidebar',
		'section'     => 'blog_section',
		'name'        => esc_html__( 'Off canvas sidebar for desktop', 'woodmart' ),
		'description' => esc_html__( 'You can hide the sidebar on desktop and show it nicely with a button click.', 'woodmart' ),
		'group'       => esc_html__( 'Sidebar', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'off-canvas-sidebar-for-mobile.mp4" autoplay loop muted></video>',
		'type'        => 'switcher',
		'default'     => '0',
		't_tab'       => array(
			'id'    => 'blog_hide_sidebar_tabs',
			'tab'   => esc_html__( 'Desktop', 'woodmart' ),
			'icon'  => 'xts-i-desktop',
			'style' => 'devices',
		),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_hide_sidebar_tablet',
		'name'        => esc_html__( 'Off canvas sidebar for tablet', 'woodmart' ),
		'description' => esc_html__( 'You can hide the sidebar on tablet and show it nicely with a button click.', 'woodmart' ),
		'group'       => esc_html__( 'Sidebar', 'woodmart' ),
		'section'     => 'blog_section',
		'type'        => 'switcher',
		'default'     => '1',
		't_tab'       => array(
			'id'   => 'blog_hide_sidebar_tabs',
			'tab'  => esc_html__( 'Tablet', 'woodmart' ),
			'icon' => 'xts-i-tablet',
		),
		'priority'    => 31,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_hide_sidebar_mobile',
		'name'        => esc_html__( 'Off canvas sidebar for mobile', 'woodmart' ),
		'description' => esc_html__( 'You can hide the sidebar on mobile devices and show it nicely with a button click.', 'woodmart' ),
		'group'       => esc_html__( 'Sidebar', 'woodmart' ),
		'section'     => 'blog_section',
		'type'        => 'switcher',
		'default'     => '1',
		't_tab'       => array(
			'id'   => 'blog_hide_sidebar_tabs',
			'tab'  => esc_html__( 'Mobile', 'woodmart' ),
			'icon' => 'xts-i-phone',
		),
		'priority'    => 32,
	)
);

Options::add_field(
	array(
		'id'          => 'single_post_justified_gallery',
		'name'        => esc_html__( 'Justify gallery', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-justify-gallery.jpg" alt="">', true ),
		'description' => esc_html__( 'This option will replace standard WordPress gallery with “Justified gallery” JS library.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_section',
		'default'     => '0',
		'priority'    => 40,
	)
);

/**
 * Blog archive.
 */
Options::add_field(
	array(
		'id'          => 'blog_design',
		'name'        => esc_html__( 'Blog design', 'woodmart' ),
		'description' => esc_html__( 'Choose one of the blog designs available in the theme.', 'woodmart' ),
		'group'       => esc_html__( 'Style', 'woodmart' ),
		'type'        => 'select',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'default'      => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'value' => 'default',
			),
			'default-alt'  => array(
				'name'  => esc_html__( 'Default alternative', 'woodmart' ),
				'value' => 'default-alt',
			),
			'small-images' => array(
				'name'  => esc_html__( 'Small images', 'woodmart' ),
				'value' => 'small-images',
			),
			'chess'        => array(
				'name'  => esc_html__( 'Chess', 'woodmart' ),
				'value' => 'chess',
			),
			'masonry'      => array(
				'name'  => esc_html__( 'Masonry grid', 'woodmart' ),
				'value' => 'masonry',
			),
			'mask'         => array(
				'name'  => esc_html__( 'Mask on image', 'woodmart' ),
				'value' => 'mask',
			),
			'meta-image'   => array(
				'name'  => esc_html__( 'Meta on image', 'woodmart' ),
				'value' => 'meta-image',
			),
			'list'         => array(
				'name'  => esc_html__( 'List', 'woodmart' ),
				'value' => 'list',
			),
		),
		'default'     => 'masonry',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_style',
		'name'        => esc_html__( 'Blog style', 'woodmart' ),
		'description' => esc_html__( 'You can use flat style or add a background to your blog posts.', 'woodmart' ),
		'group'       => esc_html__( 'Style', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'flat'   => array(
				'name'  => esc_html__( 'Flat', 'woodmart' ),
				'value' => 'flat',
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-style-flat.jpg" alt="">', true ),
			),
			'shadow' => array(
				'name'  => esc_html__( 'With background', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-style-with-shadow.jpg" alt="">', true ),
				'value' => 'shadow',
			),
		),
		'default'     => 'shadow',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'       => 'blog_with_shadow',
		'name'     => esc_html__( 'Add shadow', 'woodmart' ),
		'group'    => esc_html__( 'Style', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'blog_archive_section',
		'default'  => true,
		'on-text'  => esc_html__( 'Yes', 'woodmart' ),
		'off-text' => esc_html__( 'No', 'woodmart' ),
		'requires' => array(
			array(
				'key'     => 'blog_style',
				'compare' => 'equals',
				'value'   => array( 'shadow' ),
			),
		),
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_columns',
		'name'        => esc_html__( 'Blog columns on desktop', 'woodmart' ),
		'description' => esc_html__( 'Number of columns for the blog grid.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			1 => array(
				'name'  => 1,
				'value' => 1,
			),
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
			3 => array(
				'name'  => 3,
				'value' => 3,
			),
			4 => array(
				'name'  => 4,
				'value' => 4,
			),
		),
		'default'     => 3,
		'priority'    => 60,
		'requires'    => array(
			array(
				'key'     => 'blog_design',
				'compare' => 'equals',
				'value'   => array( 'masonry', 'mask', 'meta-image' ),
			),
		),
		't_tab'       => array(
			'id'       => 'blog_columns_tabs',
			'tab'      => esc_html__( 'Desktop', 'woodmart' ),
			'title'    => esc_html__( 'Blog columns', 'woodmart' ),
			'icon'     => 'xts-i-desktop',
			'style'    => 'devices',
			'requires' => array(
				array(
					'key'     => 'blog_design',
					'compare' => 'equals',
					'value'   => array( 'masonry', 'mask', 'meta-image' ),
				),
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_columns_tablet',
		'name'        => esc_html__( 'Blog columns on tablet', 'woodmart' ),
		'description' => esc_html__( 'Number of columns for the blog grid.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'auto' => array(
				'name'  => esc_html__( 'Auto', 'woodmart' ),
				'value' => 'auto',
			),
			1      => array(
				'name'  => 1,
				'value' => 1,
			),
			2      => array(
				'name'  => 2,
				'value' => 2,
			),
			3      => array(
				'name'  => 3,
				'value' => 3,
			),
			4      => array(
				'name'  => 4,
				'value' => 4,
			),
		),
		'default'     => 'auto',
		'priority'    => 70,
		'requires'    => array(
			array(
				'key'     => 'blog_design',
				'compare' => 'equals',
				'value'   => array( 'masonry', 'mask', 'meta-image' ),
			),
		),
		't_tab'       => array(
			'id'   => 'blog_columns_tabs',
			'icon' => 'xts-i-tablet',
			'tab'  => esc_html__( 'Tablet', 'woodmart' ),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_columns_mobile',
		'name'        => esc_html__( 'Blog columns on mobile', 'woodmart' ),
		'description' => esc_html__( 'Number of columns for the blog grid.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'auto' => array(
				'name'  => esc_html__( 'Auto', 'woodmart' ),
				'value' => 'auto',
			),
			1      => array(
				'name'  => 1,
				'value' => 1,
			),
			2      => array(
				'name'  => 2,
				'value' => 2,
			),
			3      => array(
				'name'  => 3,
				'value' => 3,
			),
			4      => array(
				'name'  => 4,
				'value' => 4,
			),
		),
		'default'     => 'auto',
		'priority'    => 80,
		'requires'    => array(
			array(
				'key'     => 'blog_design',
				'compare' => 'equals',
				'value'   => array( 'masonry', 'mask', 'meta-image' ),
			),
		),
		't_tab'       => array(
			'id'   => 'blog_columns_tabs',
			'icon' => 'xts-i-phone',
			'tab'  => esc_html__( 'Mobile', 'woodmart' ),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_spacing',
		'name'        => esc_html__( 'Space between posts on desktop', 'woodmart' ),
		'description' => esc_html__( 'You can set the different spacing between posts on the blog page.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			0  => array(
				'name'  => 0,
				'value' => 0,
			),
			2  => array(
				'name'  => 2,
				'value' => 2,
			),
			6  => array(
				'name'  => 5,
				'value' => 6,
			),
			10 => array(
				'name'  => 10,
				'value' => 10,
			),
			20 => array(
				'name'  => 20,
				'value' => 20,
			),
			30 => array(
				'name'  => 30,
				'value' => 30,
			),
		),
		'default'     => 20,
		'priority'    => 90,
		't_tab'       => array(
			'id'    => 'blog_spacing_tabs',
			'tab'   => esc_html__( 'Desktop', 'woodmart' ),
			'icon'  => 'xts-i-desktop',
			'style' => 'devices',
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_spacing_tablet',
		'name'        => esc_html__( 'Space between posts on tablet', 'woodmart' ),
		'description' => esc_html__( 'You can set the different spacing between posts on the blog page.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			0  => array(
				'name'  => 0,
				'value' => 0,
			),
			2  => array(
				'name'  => 2,
				'value' => 2,
			),
			6  => array(
				'name'  => 5,
				'value' => 6,
			),
			10 => array(
				'name'  => 10,
				'value' => 10,
			),
			20 => array(
				'name'  => 20,
				'value' => 20,
			),
			30 => array(
				'name'  => 30,
				'value' => 30,
			),
		),
		'default'     => '',
		'priority'    => 100,
		't_tab'       => array(
			'id'   => 'blog_spacing_tabs',
			'tab'  => esc_html__( 'Tablet', 'woodmart' ),
			'icon' => 'xts-i-tablet',
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_spacing_mobile',
		'name'        => esc_html__( 'Space between posts on mobile', 'woodmart' ),
		'description' => esc_html__( 'You can set the different spacing between posts on the blog page.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			0  => array(
				'name'  => 0,
				'value' => 0,
			),
			2  => array(
				'name'  => 2,
				'value' => 2,
			),
			6  => array(
				'name'  => 5,
				'value' => 6,
			),
			10 => array(
				'name'  => 10,
				'value' => 10,
			),
			20 => array(
				'name'  => 20,
				'value' => 20,
			),
			30 => array(
				'name'  => 30,
				'value' => 30,
			),
		),
		'default'     => '',
		'priority'    => 110,
		't_tab'       => array(
			'id'   => 'blog_spacing_tabs',
			'tab'  => esc_html__( 'Mobile', 'woodmart' ),
			'icon' => 'xts-i-phone',
		),
	)
);

Options::add_field(
	array(
		'id'          => 'blog_pagination',
		'name'        => esc_html__( 'Blog pagination', 'woodmart' ),
		'description' => esc_html__( 'Choose a type for the pagination on your blog page.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'pagination' => array(
				'name'  => esc_html__( 'Pagination links', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-pagination-pagination-links.jpg" alt="">', true ),
				'value' => 'pagination',
			),
			'load_more'  => array(
				'name'  => esc_html__( '"Load more" button', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-pagination-load-more-button.jpg" alt="">', true ),
				'value' => 'load_more',
			),
			'infinit'    => array(
				'name'  => esc_html__( 'Infinit scrolling', 'woodmart' ),
				'hint'  => '<video data-src="' . WOODMART_TOOLTIP_URL . 'blog-pagination-pagination-infinit.mp4" autoplay loop muted></video>',
				'value' => 'infinit',
			),
		),
		'default'     => 'pagination',
		'priority'    => 120,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_excerpt',
		'name'        => esc_html__( 'Posts excerpt', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'posts-excerpt.jpg" alt="">', true ),
		'description' => esc_html__( 'If you set this option to "Excerpt" then you would be able to set a custom excerpt for each post or it will be cut from the post content. If you choose "Full content" then all content will be shown, or you can add the "Read more button" while editing the post and by doing this cut your excerpt length as you need.', 'woodmart' ),
		'group'       => esc_html__( 'Post options', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'excerpt' => array(
				'name'  => esc_html__( 'Excerpt', 'woodmart' ),
				'value' => 'excerpt',
			),
			'full'    => array(
				'name'  => esc_html__( 'Full content', 'woodmart' ),
				'value' => 'full',
			),
		),
		'default'     => 'excerpt',
		'priority'    => 130,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_words_or_letters',
		'name'        => esc_html__( 'Excerpt length by words or letters', 'woodmart' ),
		'description' => esc_html__( 'Limit your excerpt text for posts by words or letters.', 'woodmart' ),
		'group'       => esc_html__( 'Post options', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_archive_section',
		'options'     => array(
			'word'   => array(
				'name'  => esc_html__( 'Word', 'woodmart' ),
				'value' => 'word',
			),
			'letter' => array(
				'name'  => esc_html__( 'Letters', 'woodmart' ),
				'value' => 'letter',
			),
		),
		'requires'    => array(
			array(
				'key'     => 'blog_excerpt',
				'compare' => 'equals',
				'value'   => array( 'excerpt' ),
			),
		),
		'default'     => 'letter',
		'priority'    => 140,
		'class'       => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'          => 'blog_excerpt_length',
		'name'        => esc_html__( 'Excerpt length', 'woodmart' ),
		'description' => esc_html__( 'Number of words or letters that will be displayed for each post if you use "Excerpt" mode and don\'t set custom excerpt for each post.', 'woodmart' ),
		'group'       => esc_html__( 'Post options', 'woodmart' ),
		'type'        => 'text_input',
		'attributes'  => array(
			'type' => 'number',
		),
		'section'     => 'blog_archive_section',
		'requires'    => array(
			array(
				'key'     => 'blog_excerpt',
				'compare' => 'equals',
				'value'   => array( 'excerpt' ),
			),
		),
		'default'     => 135,
		'priority'    => 150,
		'class'       => 'xts-col-6',
	)
);

Options::add_field(
	array(
		'id'          => 'parts_title',
		'name'        => esc_html__( 'Title for posts', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'parts_title.mp4" autoplay loop muted></video>',
		'description' => esc_html__( 'Display post title', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_archive_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 160,
	)
);

Options::add_field(
	array(
		'id'          => 'parts_meta',
		'name'        => esc_html__( 'Meta information', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'parts_meta.mp4" autoplay loop muted></video>',
		'description' => esc_html__( 'Display categories, share icons, author and replies', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_archive_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 170,
	)
);

Options::add_field(
	array(
		'id'          => 'parts_text',
		'name'        => esc_html__( 'Post text', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'parts_text.mp4" autoplay loop muted></video>',
		'description' => esc_html__( 'Display post excerpt', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_archive_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 180,
	)
);

Options::add_field(
	array(
		'id'          => 'parts_btn',
		'name'        => esc_html__( 'Read more button', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'parts_btn.mp4" autoplay loop muted></video>',
		'description' => esc_html__( 'Display "Continue reading" button ', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_archive_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 190,
	)
);

Options::add_field(
	array(
		'id'          => 'parts_published_date',
		'name'        => esc_html__( 'Published date', 'woodmart' ),
		'hint'        => '<video data-src="' . WOODMART_TOOLTIP_URL . 'parts-published-date.mp4" autoplay loop muted></video>',
		'description' => esc_html__( 'Display published date', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_archive_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 200,
	)
);

/**
 * Single post.
 */

Options::add_field(
	array(
		'id'          => 'single_post_design',
		'name'        => esc_html__( 'Single post design', 'woodmart' ),
		'description' => esc_html__( 'You can use different design for your single post page.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'blog_singe_post_section',
		'options'     => array(
			'default'     => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-single-post-design-default.jpg" alt="">', true ),
				'value' => 'default',
			),
			'large_image' => array(
				'name'  => esc_html__( 'Large image', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-single-post-design-large-image.jpg" alt="">', true ),
				'value' => 'large_image',
			),
		),
		'default'     => 'default',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'single_post_header',
		'name'        => esc_html__( 'Custom single post header', 'woodmart' ),
		'description' => esc_html__( 'You can use different header for your single post page.', 'woodmart' ),
		'group'       => esc_html__( 'Layout', 'woodmart' ),
		'type'        => 'select',
		'section'     => 'blog_singe_post_section',
		'options'     => '',
		'callback'    => 'woodmart_get_theme_settings_headers_array',
		'default'     => 'none',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_share',
		'name'        => esc_html__( 'Share buttons', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'single-post-share-buttons.jpg" alt="">', true ),
		'description' => esc_html__( 'Display share icons on single post page', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_singe_post_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_navigation',
		'name'        => esc_html__( 'Posts navigation', 'woodmart' ),
		'description' => esc_html__( 'Next and previous posts links on single post page', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'single-post-posts-navigation.jpg" alt="">', true ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_singe_post_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_author_bio',
		'name'        => esc_html__( 'Author bio', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'single-post-autor-bio.jpg" alt="">', true ),
		'description' => esc_html__( 'Display information about the post author', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_singe_post_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_related_posts',
		'name'        => esc_html__( 'Related posts', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'single-post-related-posts.jpg" alt="">', true ),
		'description' => esc_html__( 'Show related posts on single post page (by tags)', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_singe_post_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'blog_published_date',
		'name'        => esc_html__( 'Published date', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'blog-published-date.jpg" alt="">', true ),
		'description' => esc_html__( 'Display published date', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'blog_singe_post_section',
		'default'     => '1',
		'class'       => 'xts-col-6',
		'priority'    => 70,
	)
);
