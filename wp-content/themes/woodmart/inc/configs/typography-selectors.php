<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

/**
 * ------------------------------------------------------------------------------------------------
 * Elements selectors for advanced typography options
 * ------------------------------------------------------------------------------------------------
 */

return apply_filters(
	'woodmart_typography_selectors',
	array(
		'main_nav'                           => array(
			'title' => 'Main navigation',
		),
		'main_navigation'                    => array(
			'title'          => 'Main navigation links',
			'selector'       => 'html .wd-nav.wd-nav-main > li > a',
			'selector-hover' => 'html .wd-nav.wd-nav-main > li:hover > a, html .wd-nav.wd-nav-main > li.current-menu-item > a',
		),
		'mega_menu_drop_first_level'         => array(
			'title'          => 'Menu dropdowns first level',
			'selector'       => 'html .wd-dropdown-menu.wd-design-sized .wd-sub-menu > li > a, body .wd-dropdown-menu.wd-design-full-width .wd-sub-menu > li > a, body .wd-dropdown-menu.wd-design-aside .wd-wp-menu > .sub-sub-menu > li > a, body .wd-dropdown-menu.wd-design-aside .wd-sub-menu .wd-sub-menu > li > a',
			'selector-hover' => 'html .wd-dropdown-menu.wd-design-sized .wd-sub-menu > li > a:hover, body .wd-dropdown-menu.wd-design-full-width .wd-sub-menu > li > a:hover, body .wd-dropdown-menu.wd-design-aside .wd-wp-menu > .sub-sub-menu  > li > a:hover, body .wd-dropdown-menu.wd-design-aside .wd-sub-menu .wd-sub-menu > li > a:hover',
		),
		'mega_menu_drop_second_level'        => array(
			'title'          => 'Menu dropdowns second level',
			'selector'       => 'html .wd-dropdown-menu.wd-design-sized .sub-sub-menu li a, html .wd-dropdown-menu.wd-design-full-width .sub-sub-menu li a, body .wd-dropdown-menu.wd-design-aside .wd-wp-menu > .sub-sub-menu .sub-sub-menu li a, body .wd-dropdown-menu.wd-design-aside .wd-sub-menu .wd-sub-menu .sub-sub-menu li a',
			'selector-hover' => 'html .wd-dropdown-menu.wd-design-sized .sub-sub-menu li a:hover, html .wd-dropdown-menu.wd-design-full-width .sub-sub-menu li a:hover, body .wd-dropdown-menu.wd-design-aside .wd-wp-menu > .sub-sub-menu .sub-sub-menu  li a:hover, body .wd-dropdown-menu.wd-design-aside .wd-sub-menu .wd-sub-menu .sub-sub-menu li a:hover',
		),
		'simple_dropdown'                    => array(
			'title'          => 'Menu links on simple dropdowns',
			'selector'       => 'html .wd-dropdown-menu.wd-design-default .wd-sub-menu li a',
			'selector-hover' => 'html .wd-dropdown-menu.wd-design-default .wd-sub-menu li a:hover',
		),
		'secondary_nav'                      => array(
			'title' => 'Other navigations',
		),
		'secondary_navigation'               => array(
			'title'          => 'Secondary navigation links',
			'selector'       => 'html .wd-nav.wd-nav-secondary > li > a',
			'selector-hover' => 'html .wd-nav.wd-nav-secondary > li:hover > a, html .wd-nav.wd-nav-secondary > li.current-menu-item > a',
		),
		'secondary_navigation_topbar'        => array(
			'title'          => 'Secondary navigation links in top bar',
			'selector'       => '.whb-top-bar .wd-nav.wd-nav-secondary > li > a',
			'selector-hover' => '.whb-top-bar .wd-nav.wd-nav-secondary > li:hover > a, .whb-top-bar .wd-nav.wd-nav-secondary > li.current-menu-item > a',
		),
		'secondary_navigation_main_header'        => array(
			'title'          => 'Secondary navigation links in main header',
			'selector'       => '.whb-general-header .wd-nav.wd-nav-secondary > li > a',
			'selector-hover' => '.whb-general-header .wd-nav.wd-nav-secondary > li:hover > a, .whb-general-header .wd-nav.wd-nav-secondary > li.current-menu-item > a',
		),
		'secondary_navigation_bottom_header'        => array(
			'title'          => 'Secondary navigation links in bottom header',
			'selector'       => '.whb-header-bottom .wd-nav.wd-nav-secondary > li > a',
			'selector-hover' => '.whb-header-bottom .wd-nav.wd-nav-secondary > li:hover > a, .whb-header-bottom .wd-nav.wd-nav-secondary > li.current-menu-item > a',
		),
		'browse_categories'                  => array(
			'title'          => '"Browse categories" title',
			'selector'       => 'html .whb-header .wd-header-cats .menu-opener',
			'selector-hover' => 'html .whb-header .wd-header-cats .menu-opener:hover',
		),
		'category_navigation'                => array(
			'title'          => 'Categories navigation links',
			'selector'       => 'html .wd-dropdown-cats .wd-nav.wd-nav-vertical > li > a',
			'selector-hover' => 'html .wd-dropdown-cats .wd-nav.wd-nav-vertical > li:hover > a',
		),
		'design_aside_navigation'                => array(
			'title'          => 'Menu design "Aside" navigation links',
			'selector'       => 'html .wd-dropdown-menu.wd-design-aside .wd-sub-menu-wrapp > .wd-sub-menu > li > a',
			'selector-hover' => 'html .wd-dropdown-menu.wd-design-aside .wd-sub-menu-wrapp > .wd-sub-menu > li:hover > a',
		),
		'my_account'                         => array(
			'title'          => 'My account links in the header',
			'selector'       => 'html .wd-dropdown-my-account .wd-sub-menu li a',
			'selector-hover' => 'html .wd-dropdown-my-account .wd-sub-menu li a:hover',
		),
		'mobile_nav'                         => array(
			'title' => 'Mobile menu',
		),
		'mobile_menu_first_level'            => array(
			'title'          => 'Mobile menu dropdown first level',
			'selector'       => 'html .wd-nav-mobile > li > a',
			'selector-hover' => 'html .wd-nav-mobile > li > a:hover, html .wd-nav-mobile > li.current-menu-item > a',
		),
		'mobile_menu_second_level'           => array(
			'title'          => 'Mobile menu dropdown second level',
			'selector'       => 'html .wd-nav-mobile .wd-sub-menu li a',
			'selector-hover' => 'html .wd-nav-mobile .wd-sub-menu li a:hover, html .wd-nav-mobile .wd-sub-menu li.current-menu-item > a',
		),
		'mobile_menu_drilldown'           => array(
			'title'          => 'Mobile menu drilldown',
			'selector'       => 'html .wd-nav.wd-layout-drilldown > li > a, html .wd-nav.wd-layout-drilldown > li [class*="sub-menu"] > :is(.menu-item,.wd-drilldown-back) > a, html .wd-nav.wd-layout-drilldown .woocommerce-MyAccount-navigation-link > a',
			'selector-hover' => 'html .wd-nav.wd-layout-drilldown > li > a:hover, html .wd-nav.wd-layout-drilldown > li [class*="sub-menu"] > :is(.menu-item,.wd-drilldown-back) > a:hover, html .wd-nav.wd-layout-drilldown >li [class*="sub-menu"] > .woocommerce-MyAccount-navigation-link > a:hover, html .wd-nav.wd-layout-drilldown li.current-menu-item > a',
		),
		'page_header'                        => array(
			'title' => 'Page heading',
		),
		'page_title'                         => array(
			'title'    => 'Page title',
			'selector' => 'html .page-title > .container > .title, html .page-title .wd-title-wrapp > .title',
			'selector-hover' => 'html .page-title > .container > .title:hover, html .page-title .wd-title-wrapp > .title:hover',
		),
		'page_title_bredcrumps'              => array(
			'title'          => 'Breadcrumbs links',
			'selector'       => 'html .page-title .wd-breadcrumbs a, html .page-title .wd-breadcrumbs span, html .page-title .yoast-breadcrumb a, html .page-title .yoast-breadcrumb span',
			'selector-hover' => 'html .page-title .wd-breadcrumbs a:hover, html .page-title .yoast-breadcrumb a:hover',
		),
		'checkout_steps'              => array(
			'title'          => 'Checkout steps',
			'selector'       => 'html .wd-checkout-steps li',
			'selector-hover' => 'html .wd-checkout-steps li:hover a',
		),
		'products_categories'                => array(
			'title' => 'Products and categories',
		),
		'product_title'                      => array(
			'title'          => 'Product grid title',
			'selector'       => 'html .wd-page-content .wd-product .product-wrapper .wd-entities-title',
			'selector-hover' => 'html .wd-page-content .wd-product .product-wrapper .wd-entities-title a:hover',
		),
		'product_grid_category'                      => array(
			'title'          => 'Product grid category',
			'selector'       => 'html .wd-product .wd-product-cats a',
			'selector-hover' => 'html .wd-product .wd-product-cats a:hover',
		),
		'product_grid_brand'                      => array(
			'title'          => 'Product grid brand',
			'selector'       => 'html .wd-product .wd-product-brands-links a',
			'selector-hover' => 'html .wd-product .wd-product-brands-links a:hover',
		),
		'product_price'                      => array(
			'title'    => 'Product grid price',
			'selector' => 'html .wd-product .price, html .wd-product .price > .amount, html .wd-product .price ins > .amount',
			'selector-hover' => 'html .wd-product .price:hover, html .wd-product .price > .amount:hover, html .wd-product .price ins > .amount:hover',
		),
		'product_old_price'                  => array(
			'title'    => 'Product grid old price',
			'selector' => 'html .product.wd-product del, html .product.wd-product del .amount',
			'selector-hover' => 'html .product.wd-product del:hover, html .product.wd-product del .amount:hover',
		),
		'product_category_title'             => array(
			'title'    => 'Category title',
			'selector' => 'html .product.wd-cat .wd-entities-title, html .product.wd-cat.cat-design-replace-title .wd-entities-title, html .wd-masonry-first .wd-cat:first-child .wd-entities-title',
			'selector-hover' => 'html .product.wd-cat:hover .wd-entities-title, html .product.wd-cat:hover .wd-entities-title a, .cat-design-side .wd-cat-inner > a:hover ~ .wd-cat-content .wd-entities-title a, html .wd-masonry-first .wd-cat:first-child:hover .wd-entities-title a',
		),
		'product_category_count'             => array(
			'title'          => 'Category products count',
			'selector'       => 'html .product.wd-cat .wd-cat-count, html .product.wd-cat.cat-design-replace-title .wd-cat-count',
			'selector-hover' => 'html .product.wd-cat:hover .wd-cat-count',
		),
		'single_product'                     => array(
			'title' => 'Single product',
		),
		'product_title_single_page'          => array(
			'title'    => 'Single product title',
			'selector' => 'html .product-image-summary-wrap .product_title, html .wd-single-title .product_title',
			'selector-hover' => 'html .product-image-summary-wrap .product_title:hover, html .wd-single-title .product_title:hover',
		),
		'product_price_single_page'          => array(
			'title'    => 'Single product price',
			'selector' => 'html .product-image-summary-wrap .summary-inner > .price, html .product-image-summary-wrap .summary-inner > .price > .amount, html .product-image-summary-wrap .summary-inner > .price > .price > .amount, html .product-image-summary-wrap .summary-inner > .price ins .amount, html .wd-single-price .price, html .wd-single-price .price > .amount, html .wd-single-price .price > ins .amount',
			'selector-hover' => 'html .product-image-summary-wrap .summary-inner > .price:hover, html .product-image-summary-wrap .summary-inner > .price > .amount:hover, html .product-image-summary-wrap .summary-inner > .price > .price .amount:hover, html .product-image-summary-wrap .summary-inner > .price > ins .amount:hover, html .wd-single-price .price:hover, html .wd-single-price .price > .amount:hover, html .wd-single-price .price > ins .amount:hover',
		),
		'product_price_old_single_page'      => array(
			'title'    => 'Single product old price',
			'selector' => 'html .product-image-summary-wrap .summary-inner > .price del, html .product-image-summary-wrap .summary-inner > .price del .amount, html .wd-single-price .price del .amount',
			'selector-hover' => 'html .product-image-summary-wrap .summary-inner > .price del:hover, html .product-image-summary-wrap .summary-inner > .price del .amount:hover, html .wd-single-price .price del .amount:hover',
		),
		'product_variable_price_single_page' => array(
			'title'    => 'Single product variation price',
			'selector' => 'html .product-image-summary-wrap .variations_form .woocommerce-variation-price .price > .amount, html .product-image-summary-wrap .variations_form .woocommerce-variation-price .price > ins .amount, html .wd-single-add-cart .variations_form .woocommerce-variation-price .price > .amount, html .wd-single-add-cart .variations_form .woocommerce-variation-price .price > ins .amount',
			'selector-hover' => 'html .product-image-summary-wrap .variations_form .woocommerce-variation-price .price > .amount:hover, html .product-image-summary-wrap .variations_form .woocommerce-variation-price .price > ins .amount:hover, html .wd-single-add-cart .variations_form .woocommerce-variation-price .price > .amount:hover, html .wd-single-add-cart .variations_form .woocommerce-variation-price .price > ins .amount:hover',
		),
		'product_variable_price_old_single_page' => array(
			'title'    => 'Single product variation old price',
			'selector' => 'html .product-image-summary-wrap .variations_form .woocommerce-variation-price > .price del, html .product-image-summary-wrap .variations_form .woocommerce-variation-price > .price del .amount, html .wd-single-add-cart .variations_form .woocommerce-variation-price > .price del, html .wd-single-add-cart .variations_form .woocommerce-variation-price > .price del .amount',
			'selector-hover' => 'html .product-image-summary-wrap .variations_form .woocommerce-variation-price > .price del:hover, html .product-image-summary-wrap .variations_form .woocommerce-variation-price > .price del .amount:hover, html .wd-single-add-cart .variations_form .woocommerce-variation-price > .price del:hover, html .wd-single-add-cart .variations_form .woocommerce-variation-price > .price del .amount:hover',
		),
		'product_nav_price_single_page'          => array(
			'title'    => 'Single product navigation price',
			'selector' => 'html .wd-product-nav-desc .price > .amount, html .wd-product-nav-desc .price > ins .amount, html .wd-product-nav-desc .price > ins .amount',
			'selector-hover' => 'html .wd-product-nav-desc .price > .amount:hover, html .wd-product-nav-desc .price > ins .amount:hover, html .wd-product-nav-desc .price > ins .amount:hover',
		),
		'product_accordion_single_page'          => array(
			'title'    => 'Single product accordion',
			'selector' => 'html .wd-builder-off .tabs-layout-accordion > .wd-accordion-item > .wd-accordion-title .wd-accordion-title-text',
			'selector-hover' => 'html .wd-builder-off .tabs-layout-accordion > .wd-accordion-item > .wd-accordion-title:hover .wd-accordion-title-text, html .wd-builder-off .tabs-layout-accordion > .wd-accordion-item > .wd-accordion-title.wd-active .wd-accordion-title-text',
		),
		'product_tabs_single_page'          => array(
			'title'    => 'Single product tabs',
			'selector' => 'html .wd-builder-off .tabs-layout-tabs .wd-nav-tabs > li > a',
			'selector-hover' => 'html .wd-builder-off .tabs-layout-tabs .wd-nav-tabs > li > a:hover, html .wd-builder-off .tabs-layout-tabs .wd-nav-tabs > li.active > a'
		),
		'quick_view'                               => array(
			'title' => 'Quick view',
		),
		'product_title_quick_view'          => array(
			'title'    => 'Quick view title',
			'selector' => 'html .product-quick-view .product_title',
			'selector-hover' => 'html .product-quick-view .product_title:hover',
		),
		'product_price_quick_view'          => array(
			'title'    => 'Quick view price',
			'selector' => 'html .product-quick-view  .summary-inner > .price > .amount, html .product-quick-view .summary-inner > .price > ins .amount',
			'selector-hover' => 'html .product-quick-view  .summary-inner > .price > .amount:hover, html .product-quick-view .summary-inner > .price > ins .amount:hover',
		),
		'product_price_old_quick_view'      => array(
			'title'    => 'Quick view old price',
			'selector' => 'html .product-quick-view  .summary-inner > .price del, html .product-quick-view  .summary-inner > .price del .amount',
			'selector-hover' => 'html .product-quick-view  .summary-inner > .price del:hover, html .product-quick-view  .summary-inner > .price del .amount:hover',
		),
		'product_variable_price_quick_view' => array(
			'title'    => 'Quick view variation price',
			'selector' => 'html .product-quick-view  .variations_form .woocommerce-variation-price .price > .amount, html .product-quick-view .variations_form .woocommerce-variation-price .price > ins .amount',
			'selector-hover' => 'html .product-quick-view  .variations_form .woocommerce-variation-price .price > .amount:hover, html .product-quick-view .variations_form .woocommerce-variation-price .price > ins .amount:hover',
		),
		'product_variable_price_old_quick_view' => array(
			'title'    => 'Quick view variation old price',
			'selector' => 'html .product-quick-view  .variations_form .woocommerce-variation-price > .price del, html .product-quick-view .variations_form .woocommerce-variation-price > .price del .amount',
			'selector-hover' => 'html .product-quick-view  .variations_form .woocommerce-variation-price > .price del:hover, html .product-quick-view .variations_form .woocommerce-variation-price > .price del .amount:hover',
		),
		'blog'                               => array(
			'title' => 'Blog',
		),
		'blog_title'                         => array(
			'title'          => 'Blog post title',
			'selector'       => 'html .post.wd-post .wd-entities-title',
			'selector-hover' => 'html .post.wd-post .wd-entities-title a:hover',
		),
		'blog_meta'                         => array(
			'title'          => 'Blog post meta',
			'selector'       => 'html .wd-post .entry-meta-list li, html .hovered-social-icons:after, html .wd-post-meta > div, html .wd-post-meta > div > a, html .wd-post-entry-meta :is(.wd-meta-date,.wd-post-cat,.wd-post-cat a), html .blog-design-list :is(.wd-meta-author,.wd-post-share,.wd-meta-reply a), html .wd-meta-reply-text',
			'selector-hover' => 'html .wd-post .entry-meta-list li a:hover, html .hovered-social-icons:hover:after, html .wd-post-meta > div > a:hover, html .wd-post-entry-meta .wd-post-cat a:hover, html .blog-design-list :is(.wd-meta-author a,.wd-post-share,.wd-meta-reply a):hover',
		),
		'blog_title_shortcode'               => array(
			'title'          => 'Blog title on WPBakery element',
			'selector'       => 'html .wd-blog-holder .post.wd-post .wd-entities-title',
			'selector-hover' => 'html .wd-blog-holder .post.wd-post .wd-entities-title a:hover',
		),
		'blog_title_carousel'                => array(
			'title'          => 'Blog title on carousel',
			'selector'       => 'html .wd-carousel-item .post.wd-post .wd-entities-title',
			'selector-hover' => 'html .wd-carousel-item .post.wd-post .wd-entities-title a:hover',
		),
		'blog_title_sinle_post'              => array(
			'title'    => 'Blog title on single post',
			'selector' => 'html .wd-single-post-header .title',
			'selector-hover' => 'html .wd-single-post-header .title:hover',
		),
		'widgets'                               => array(
			'title' => 'Widgets',
		),
		'widgets_price'          => array(
			'title'    => 'Widgets prices',
			'selector' => 'html .widget-area .widget .price > .amount, html .widget-area .widget .price > ins .amount, html .widget-area .widget .price > ins .amount',
			'selector-hover' => 'html .widget-area .widget .price > .amount:hover, html .widget-area .widget .price > ins .amount:hover, html .widget-area .widget .price > ins .amount:hover',
		),
		'product_categories_first_level'            => array(
			'title'          => 'Product categories first level',
			'selector'       => 'html .widget_product_categories .product-categories > li > a',
			'selector-hover' => '.widget_product_categories .product-categories > li > a:hover, html .widget_product_categories .product-categories > li.current-cat > a',
		),
		'product_categories_second_level'           => array(
			'title'          => 'Product categories second level',
			'selector'       => 'html .widget_product_categories .product-categories > li > .children > li > a',
			'selector-hover' => 'html .widget_product_categories .product-categories > li > .children > li > a:hover, html html .widget_product_categories .product-categories > li > .children > li.current-menu-item > a',
		),
		'product_categories_all_level'           => array(
			'title'          => 'Product categories all level',
			'selector'       => 'html .widget_product_categories .product-categories li a',
			'selector-hover' => '.widget_product_categories .product-categories li a:hover, .widget_product_categories .product-categories li.current-menu-item a',
		),
		'tables'                               => array(
			'title' => 'Tables',
		),
		'tables_heading_title'                         => array(
			'title'          => 'Tables heading title',
			'selector'       => 'html table th',
			'selector-hover' => 'html table th:hover',
		),
		'custom_selector'                    => array(
			'title' => 'Write your own selector',
		),
		'custom'                             => array(
			'title'    => 'Custom selector',
			'selector' => 'custom',
		),
	)
);
