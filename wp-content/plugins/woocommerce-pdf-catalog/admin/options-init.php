<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

    if ( ! class_exists( 'weLaunch' ) && ! class_exists( 'Redux' ) ) {
        return;
    }

    if( class_exists( 'weLaunch' ) ) {
        $framework = new weLaunch();
    } else {
        $framework = new Redux();
    }

    // This is your option name where all the weLaunch data is stored.
    $opt_name = "woocommerce_pdf_catalog_options";

    $args = array(
        'opt_name' => 'woocommerce_pdf_catalog_options',
        'use_cdn' => TRUE,
        'dev_mode' => FALSE,
        'display_name' => __( 'WooCommerce PDF Catalog', 'woocommerce-pdf-catalog' ),
        'display_version' => '1.16.6',
        'page_title' => __( 'WooCommerce PDF Catalog', 'woocommerce-pdf-catalog' ),
        'update_notice' => TRUE,
        'intro_text' => '',
        'footer_text' => '&copy; '.date('Y').' weLaunch',
        'admin_bar' => FALSE,
        'menu_type' => 'submenu',
        'menu_title' => __( 'PDF Catalog', 'woocommerce-pdf-catalog' ),
        'allow_sub_menu' => TRUE,
        'page_parent' => 'woocommerce',
        'page_parent_post_type' => 'your_post_type',
        'customizer' => FALSE,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => TRUE,
        'output_tag' => TRUE,
        'settings_api' => TRUE,
        'cdn_check_time' => '1440',
        'compiler' => TRUE,
        'page_permissions' => 'manage_options',
        'save_defaults' => TRUE,
        'show_import_export' => TRUE,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => TRUE,
    );

    global $weLaunchLicenses;
    if( (isset($weLaunchLicenses['woocommerce-pdf-catalog']) && !empty($weLaunchLicenses['woocommerce-pdf-catalog'])) || (isset($weLaunchLicenses['woocommerce-plugin-bundle']) && !empty($weLaunchLicenses['woocommerce-plugin-bundle'])) ) {
        $args['display_name'] = '<span class="dashicons dashicons-yes-alt" style="color: #9CCC65 !important;"></span> ' . $args['display_name'];
    } else {
        $args['display_name'] = '<span class="dashicons dashicons-dismiss" style="color: #EF5350 !important;"></span> ' . $args['display_name'];
    }

    $framework::setArgs( $opt_name, $args );

    $atts = wc_get_attribute_taxonomies();
    $attributesSelect = array();
    if(!empty($atts)) {
        foreach ($atts as $value) {
            $attributesSelect['pa_' . $value->attribute_name] = $value->attribute_label;
        }
    }


    $woocommerce_pdf_catalog_options = get_option('woocommerce_pdf_catalog_options');

    // Get Custom Meta Keys for product
    $transient_name = 'woocommerce_pdf_catalog_options_meta_keys';
    $woocommerce_pdf_catalog_options_meta_keys = get_transient( $transient_name );

    if ( false === $woocommerce_pdf_catalog_options_meta_keys ) { 

        // Get Custom Meta Keys for post
        global $wpdb;
        $sql = "SELECT DISTINCT meta_key
                        FROM " . $wpdb->postmeta . "
                        INNER JOIN  " . $wpdb->posts . " 
                        ON post_id = ID
                        WHERE post_type = 'product'
                        ORDER BY meta_key ASC";

        $meta_keys = $wpdb->get_results( $sql, 'ARRAY_A' );

        $woocommerce_pdf_catalog_options_meta_keys = array(
            'order' => array(
                'id'      => 'customMetaKeys',
                'type'    => 'sorter',
                'title'   => 'Reorder / Disable Custom Data.',
                'options' => array(
                    'enabled'  => array(

                    ),
                    'disabled'  => array(

                    ),
                ),
            ),
        );

        $meta_keys_rearranged = array();
        foreach ($meta_keys as $key => $meta_key) {
            $meta_key = preg_replace('/[^\w-]/', '', $meta_key['meta_key']);

            if(isset($woocommerce_pdf_catalog_options['showCustomMetaKey_' . $meta_key]) && $woocommerce_pdf_catalog_options['showCustomMetaKey_' . $meta_key] == "1") {
                $meta_keys_rearranged[] = $meta_key;    
            }
            

            $woocommerce_pdf_catalog_options_meta_keys[$meta_key] = 
                array(
                    'id'       => 'showCustomMetaKey_' . $meta_key,
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Custom Meta Key ' . $meta_key, 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                );

            $woocommerce_pdf_catalog_options_meta_keys[$meta_key . '___text'] = 
                array(
                    'id'       => 'showCustomMetaKeyText_' . $meta_key,
                    'type'     => 'text',
                    'title'    => __( 'Text before Custom Meta Key ' . $meta_key, 'woocommerce-pdf-catalog' ),
                    'default'   => $meta_key,
                    'required' => array('showCustomMetaKey_' . $meta_key, 'equals' , '1'),
                );

            $woocommerce_pdf_catalog_options_meta_keys[$meta_key . '___acf'] = 
                array(
                    'id'       => 'showCustomMetaKeyACF_' . $meta_key,
                    'type'     => 'checkbox',
                    'title'    => __( 'Is ACF Field?','woocommerce-pdf-catalog' ),
                    'default'   => false,
                    'required' => array('showCustomMetaKey_' . $meta_key, 'equals' , '1'),
                );
        }

        $woocommerce_pdf_catalog_options_meta_keys['order']['options']['enabled'] = $meta_keys_rearranged;

        set_transient( $transient_name, $woocommerce_pdf_catalog_options_meta_keys, WEEK_IN_SECONDS);
    } else {

        $meta_keys_rearranged = array();
        
        foreach ($woocommerce_pdf_catalog_options_meta_keys as $meta_key => $meta_val) {

            if( (stristr($meta_key, '___text') !== FALSE) || (stristr($meta_key, '___acf') !== FALSE) || $meta_key == "order") {
                continue;
            }

            if($woocommerce_pdf_catalog_options['showCustomMetaKey_' . $meta_key] == "1") {
                $meta_keys_rearranged[] = $meta_key;    
            }
        }
        $woocommerce_pdf_catalog_options_meta_keys['order']['options']['enabled'] = $meta_keys_rearranged;
    }

    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    $framework::setSection( $opt_name, array(
        'title'  => __( 'PDF Catalog', 'woocommerce-pdf-catalog' ),
        'id'     => 'general',
        'desc'   => __( 'Need support? Please use the comment function on codecanyon.', 'woocommerce-pdf-catalog' ),
        'icon'   => 'el el-home',
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'General', 'woocommerce-pdf-catalog' ),
        'desc'       => __( 'To get auto updates please <a href="' . admin_url('tools.php?page=welaunch-framework') . '">register your License here</a>.', 'woocommerce-pdf-catalog' ),
        'id'         => 'general-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable PDF Catalog to use the options below', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'enableAttributes',
                'type'     => 'switch',
                'title'    => __( 'Enable Attribute PDF Catalogs', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'From wp-admin > attributes > values you can generate custom Attribute Value PDF catalogs.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'       => 'singleVariationsSupport',
                'type'     => 'switch',
                'title'    => __( 'Enable Single Variations Support', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'When enabled and you use our <a href="https://www.welaunch.io/en/product/woocommerce-single-variations/" target="_blank">Single Variations Plugin</a> all variations will be exported as own products and variable ones will be hidden.', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'loadFontAwesome',
                'type'     => 'checkbox',
                'title'    => __('Load Font Awesome 5 Free', 'wordpress-gdpr'),
                'subtitle'    => __('If your theme does not load it, our plugin will load it when checked.', 'wordpress-gdpr'),
                'default'  => '1',
            ),
        )
    ));


    $framework::setSection( $opt_name, array(
        'title'      => __( 'Query', 'woocommerce-pdf-catalog' ),
        'desc' => __( 'Configure how products are sorted.', 'woocommerce-pdf-catalog' ), 
        'id'         => 'query',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'showFullCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Full PDF Catalog Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show Full PDF Catalog Link', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'useDefaultQuery',
                'type'     => 'switch',
                'title'    => __( 'Enable Filtering', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'When you use WooCommerce Nav Filters check this option to retrieve filtered product data.', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
            array(
                'id'       => 'orderCategories',
                'type'     => 'switch',
                'title'    => __( 'Order Categories', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Custom override the sorting of categories.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'       => 'orderCategoriesKey',
                'type'     => 'select',
                'title'    => __( 'Order Categories By', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Sort categories by a parameter.', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    'term_id' => __('Term_id', 'woocommerce-pdf-catalog'),
                    'name' => __('Menu Order', 'woocommerce-pdf-catalog'),
                    'slug' => __('Slug', 'woocommerce-pdf-catalog'),
                    'count' => __('Count', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'name',
                'required' => array('orderCategories','equals','1'),
            ),
            array(
                'id'       => 'flattenProducts',
                'type'     => 'switch',
                'title'    => __( 'Flatten products', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'All products will show in one category only and displayed next to each other.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'       => 'flattenProductsRemoveDuplicates',
                'type'     => 'checkbox',
                'title'    => __( 'Flatten Products - Remove Duplicates', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Products that are in 2 categories for example will only show once.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
                'required' => array('flattenProducts','equals','1'),
            ),
            array(
                'id'       => 'yoastPrimaryOnlyInFull',
                'type'     => 'switch',
                'title'    => __( 'Yoast / Rank Math Primary Category only in Full Catalog', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Only show products that are in current primary category loop of the pdf.', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'filterSupport',
                'type'     => 'switch',
                'title'    => __( 'WOOF Filter Support', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'When categories with WOOF Filter plugin are selected, it will export these categories instead of all.', 'woocommerce-pdf-catalog' ),
                'default'  => '1',
                'required' => array('showFullCatalogLink','equals','1'),
            ),
            array(
                'id'       => 'showSubcategories',
                'type'     => 'switch',
                'title'    => __( 'Subcategories', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Show Subcategories and products', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'       => 'excludeParentCategoryProducts',
                'type'     => 'switch',
                'title'    => __( 'Exclude Parent Category Products', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Hide products from the parent category.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
                'required' => array('showSubcategories','equals','1'),
            ),
            array(
                'id'       => 'includeChildren',
                'type'     => 'switch',
                'title'    => __( 'Include Children Products', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'E.g. if Hoodie is assigned to Hoodies category, but not in Clothing, it can show in both. Disable this to show the Hoodie only in Hoodies.', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'order',
                'type'     => 'select',
                'title'    => __( 'Order Products', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Sort retrieved Products.', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    'DESC' => __('Descending', 'woocommerce-pdf-catalog'),
                    'ASC' => __('Ascending', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'DESC',
            ),
            array(
                'id'       => 'orderby',
                'type'     => 'select',
                'title'    => __( 'Order Products By', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Sort retrieved Products by parameter.', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    'none' => __('none', 'woocommerce-pdf-catalog'),
                    'ID' => __('ID', 'woocommerce-pdf-catalog'),
                    'author' => __('Author', 'woocommerce-pdf-catalog'),
                    'title' => __('Title', 'woocommerce-pdf-catalog'),
                    'name' => __('Post Name (slug)', 'woocommerce-pdf-catalog'),
                    'date' => __('Date', 'woocommerce-pdf-catalog'),
                    'modified' => __('Last modified date', 'woocommerce-pdf-catalog'),
                    'parent' => __('Post/page parent id', 'woocommerce-pdf-catalog'),
                    'rand' => __('Random order', 'woocommerce-pdf-catalog'),
                    'comment_count' => __('comment_count', 'woocommerce-pdf-catalog'),
                    'menu_order' => __('Menu order', 'woocommerce-pdf-catalog'),
                    '_sku' => __('SKU', 'woocommerce-pdf-catalog'),
                    '_price' => __('Price', 'woocommerce-pdf-catalog'),
                    '_regular_price' => __('Regular Price', 'woocommerce-pdf-catalog'),
                    '_sale_price' => __('Sale Price', 'woocommerce-pdf-catalog'),
                    '_stock' => __('Stock Amount', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'date',
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Buttons', 'woocommerce-pdf-catalog' ),
        'desc' => __( 'Configure what and where the export buttons should appear. If no hook position work use our shortcodes or contact your theme developer: [pdf_catalog category="full OR category ID" text="Export PDF"]', 'woocommerce-pdf-catalog' ), 
        'id'         => 'button',
        'subsection' => true,
        'fields'     => array(
            // Full
            array(
                'id'       => 'showFullCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Full PDF Catalog Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show Full PDF Catalog Link', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'showFullCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Full PDF Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the export full catalog button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Complete Catalog (PDF)', 'woocommerce-pdf-catalog'),
                'required' => array('showFullCatalogLink','equals','1'),
            ),

            // Category
            array(
                'id'       => 'showCategoryCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Category PDF Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show PDF Category Catalog Link', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'showCategoryCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Category PDF Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the export category button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Category Catalog (PDF)', 'woocommerce-pdf-catalog'),
                'required' => array('showCategoryCatalogLink','equals','1'),
            ),

            // Tag
            array(
                'id'       => 'showTagCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Tag PDF Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show PDF Tag Catalog Link', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'showTagCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Tag PDF Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the export Tag button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Tag Catalog (PDF)', 'woocommerce-pdf-catalog'),
                'required' => array('showTagCatalogLink','equals','1'),
            ),

            // Custom Taxonomy
            array(
                'id'       => 'showTaxonomyCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Custom Taxonomy PDF Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show PDF Catalog Link for custom Taxonomies (like Brand)', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'showTaxonomyCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Taxonomy PDF Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the export Taxonomy button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Export PDF Catalog', 'woocommerce-pdf-catalog'),
                'required' => array('showTaxonomyCatalogLink','equals','1'),
            ),

            // Sale Catalog
            array(
                'id'       => 'showSaleCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Sale PDF Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show PDF Sale Catalog Link', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'showSaleCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Sale PDF Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the export Sale button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Sale Catalog (PDF)', 'woocommerce-pdf-catalog'),
                'required' => array('showSaleCatalogLink','equals','1'),
            ),
            array(
                'id'       => 'showSaleCatalogOnlyInStock',
                'type'     => 'checkbox',
                'title'    => __( 'Sale PDF - Only in Stock', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Hide out of stock products in sale catalog.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('showSaleCatalogLink','equals','1'),
            ),

            array(
                'id'       => 'linkPosition',
                'type'     => 'select',
                'title'    => __( 'Link position', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Choose the position of the button on the category page.', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    'woocommerce_before_main_content' => __('Before Main Content', 'woocommerce-pdf-catalog'),
                    'woocommerce_archive_description' => __('After category description', 'woocommerce-pdf-catalog'),
                    'woocommerce_before_shop_loop' => __('Before Shop Loop', 'woocommerce-pdf-catalog'),
                    'woocommerce_after_shop_loop' => __('After Shop Loop', 'woocommerce-pdf-catalog'),
                    'woocommerce_after_main_content' => __('After Main Content', 'woocommerce-pdf-catalog'),
                    'woocommerce_sidebar' => __('In WooCommerce Sidebar', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'woocommerce_archive_description',
            ),
            array(
                'id'       => 'showCartCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Cart PDF Export Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show Cart PDF Catalog Link', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'showCartCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Cart PDF Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the export cart button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Cart Catalog (PDF)', 'woocommerce-pdf-catalog'),
                'required' => array('showCartCatalogLink','equals','1'),
            ),
            array(
                'id'       => 'cartLinkPosition',
                'type'     => 'select',
                'title'    => __( 'Cart Link position', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Choose the position of the button on the cart page.', 'woocommerce-pdf-catalog' ),
                'default'  => 'woocommerce_before_cart',
                'options'  => array( 
                    'woocommerce_before_cart' => __('before_cart', 'woocommerce-pdf-catalog'),
                    'woocommerce_before_cart_table' => __('before_cart_table', 'woocommerce-pdf-catalog'),
                    'woocommerce_before_cart_contents' => __('before_cart_contents', 'woocommerce-pdf-catalog'),
                    'woocommerce_cart_contents' => __('cart_contents', 'woocommerce-pdf-catalog'),
                    'woocommerce_cart_actions' => __('cart_actions', 'woocommerce-pdf-catalog'),
                    'woocommerce_after_cart_contents' => __('after_cart_contents', 'woocommerce-pdf-catalog'),
                    'woocommerce_after_cart_table' => __('after_cart_table', 'woocommerce-pdf-catalog'),
                    'woocommerce_cart_collaterals' => __('cart_collaterals', 'woocommerce-pdf-catalog'),
                    'woocommerce_after_cart' => __('after_cart', 'woocommerce-pdf-catalog'),
                ),
                'required' => array('showCartCatalogLink','equals','1'),
            ),
            array(
                'id'       => 'showWishlistCatalogLink',
                'type'     => 'switch',
                'title'    => __( 'Wishlist PDF Export Link', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show Wishlist PDF Catalog Link. This requires <a href="https://codecanyon.net/item/woocommerce-wishlist/22003411" target="_blank">our wishlist plugin!</a>', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'       => 'showWishlistCatalogCategories',
                'type'     => 'checkbox',
                'title'    => __( 'Wishlist PDF Export Product Categories', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'This will export the wishlist products grouped by their product category.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'       => 'showWishlistCatalogLinkButtontext',
                'type'     => 'text',
                'title'    => __( 'Wishlist Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the wishlist export button.', 'woocommerce-pdf-catalog' ),
                'default' => __('Export Wishlist as PDF', 'woocommerce-pdf-catalog'),
                'required' => array('showWishlistCatalogLink','equals','1'),
            ),
            array(
                'id'       => 'wishlistLinkPosition',
                'type'     => 'select',
                'title'    => __( 'Wishlist Link position', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Choose the position of the button on the Wishlist page.', 'woocommerce-pdf-catalog' ),
                'default'  => 'woocommerce_wishlist_after_wishlist',
                'options'  => array( 
                    'woocommerce_wishlist_before_wishlist' => __('Before Wishlist', 'woocommerce-pdf-catalog'),
                    'woocommerce_wishlist_before_products' => __('Before Products', 'woocommerce-pdf-catalog'),
                    'woocommerce_wishlist_after_products' => __('After Products', 'woocommerce-pdf-catalog'),
                    'woocommerce_wishlist_after_wishlist' => __('After Wishlist', 'woocommerce-pdf-catalog'),
                ),
                'required' => array('showWishlistCatalogLink','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Send Email Catalog', 'woocommerce-pdf-catalog' ),
        'id'         => 'send-email',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'sendEMail',
                'type'     => 'switch',
                'title'    => __( 'Enable Email Sending', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show a button send the cart via Email.', 'woocommerce-pdf-catalog' ),
                'default' => 1,
            ),
            array(
                'id'       => 'sendEMailButtonText',
                'type'     => 'text',
                'title'    => __( 'Email Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text inside the send button.', 'woocommerce-pdf-catalog' ),
                'default' => 'Send Catalog (PDF)',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailTo',
                'type'     => 'text',
                'title'    => __( 'Email Default To', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Default to address (split by comma if multiple).', 'woocommerce-pdf-catalog' ),
                'default' => '',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailToLabel',
                'type'     => 'text',
                'title'    => __( 'Email To Label', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Label for to field.', 'woocommerce-pdf-catalog' ),
                'default' => 'To Email (split by comma for multiple emails)',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailToPlaceholder',
                'type'     => 'text',
                'title'    => __( 'Email To Placeholder', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Placeholder for to field.', 'woocommerce-pdf-catalog' ),
                'default' => 'To Email (split by comma for multiple emails)',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailSubject',
                'type'     => 'text',
                'title'    => __( 'Email Subject', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Subject.', 'woocommerce-pdf-catalog' ),
                'default' => 'Your Cart as PDF | ' . get_bloginfo('name'),
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailCC',
                'type'     => 'text',
                'title'    => __( 'Email Default CC', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Default CC Address (split by comma if multiple).', 'woocommerce-pdf-catalog' ),
                'default' => '',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailBCC',
                'type'     => 'text',
                'title'    => __( 'Email BCC', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Default BCC Address  (split by comma if multiple).', 'woocommerce-pdf-catalog' ),
                'default' => '',
                'required' => array('sendEMail','equals','1'),
            ),
            // Type
            array(
                'id'       => 'sendEMailTypeLabel',
                'type'     => 'text',
                'title'    => __( 'Email Type Label', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Label for type select field.', 'woocommerce-pdf-catalog' ),
                'default' => 'Select Catalog',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailTypePlaceholder',
                'type'     => 'text',
                'title'    => __( 'Email Type Placeholder', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Placeholder for type select field.', 'woocommerce-pdf-catalog' ),
                'default' => 'Select Catalog',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'      => 'sendEMailTypes',
                'type'    => 'sorter',
                'title'   => 'Email Type Catalog Types',
                'subtitle'    => 'Available catalog types',
                'options' => array(
                    'enabled'  => array(
                        'category',
                        'full',
                        'sale',
                        'cart',
                    ),
                    'disabled' => array(
                    )
                ),
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailTypeHideWhenSingle',
                'type'     => 'checkbox',
                'title'    => __( 'Email Type Hide when one option', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Hide the select field when there is only one enabled option.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailCategoryLabel',
                'type'     => 'text',
                'title'    => __( 'Email Category Label', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Label for category select field.', 'woocommerce-pdf-catalog' ),
                'default' => 'Select Category',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailCategoryPlaceholder',
                'type'     => 'text',
                'title'    => __( 'Email Category Placeholder', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Placeholder for category select field.', 'woocommerce-pdf-catalog' ),
                'default' => 'Select Category',
                'required' => array('sendEMail','equals','1'),
            ),
            // Text
            array(
                'id'       => 'sendEMailText',
                'type'  => 'editor',
                'args'   => array(
                    'teeny'            => false,
                ),
                'title'    => __( 'Email Default Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Default Text.', 'woocommerce-pdf-catalog' ),
                'default' => '',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailTextShow',
                'type'     => 'checkbox',
                'title'    => __( 'Email Text Show in Frontend', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Show the text field in fronted so users can enter a custom message in the mail with the attachment. Otherwise the default message above will be used.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('sendEMail','equals','1'),
            ),
                array(
                    'id'       => 'sendEMailTextLabel',
                    'type'     => 'text',
                    'title'    => __( 'Email Text Label', 'woocommerce-pdf-catalog' ),
                    'subtitle'    => __( 'Label for text field.', 'woocommerce-pdf-catalog' ),
                    'default' => 'Your message',
                    'required' => array('sendEMailTextShow','equals','1'),
                ),
                array(
                    'id'       => 'sendEMailTextPlaceholder',
                    'type'     => 'text',
                    'title'    => __( 'Email Text Placeholder', 'woocommerce-pdf-catalog' ),
                    'subtitle'    => __( 'Placeholder for text field.', 'woocommerce-pdf-catalog' ),
                    'default' => 'Your message',
                    'required' => array('sendEMailTextShow','equals','1'),
                ),
            // Button
            array(
                'id'       => 'sendEMailSendButtonText',
                'type'     => 'text',
                'title'    => __( 'Email Send Button Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text for the send button.', 'woocommerce-pdf-catalog' ),
                'default' => 'Send PDF',
                'required' => array('sendEMail','equals','1'),
            ),
            array(
                'id'       => 'sendEMailSuccessText',
                'type'     => 'text',
                'title'    => __( 'Email Success Text', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Text after button has been sent.', 'woocommerce-pdf-catalog' ),
                'default' => 'Your PDF has been sent.',
                'required' => array('sendEMail','equals','1'),
            ),
            
        )
    ) );


    $framework::setSection( $opt_name, array(
        'title'      => __( 'Exclusion', 'woocommerce-pdf-catalog' ),
        'desc' => __( 'With the below settings you can exclude products / categories from the PDF catalog.', 'woocommerce-pdf-catalog' ), 
        'id'         => 'exclusion',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'   => 'excludeProductCategories',
                'type' => 'select',
                'data' => 'categories',
                'args' => array('taxonomy' => array('product_cat')),
                'multi' => true,
                'ajax'  => true,
                'title' => __('Exclude Product Categories', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Which product categories should be excluded in the PDF catalog.', 'woocommerce-pdf-catalog'),
            ),
            array(
                'id'       => 'excludeProductCategoriesRevert',
                'type'     => 'checkbox',
                'title'    => __( 'Revert Categories Exclusion', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Instead of exclusion it will include.', 'woocommerce-pdf-catalog' ),
            ),
            array(
                'id'     =>'excludeProductCategoriesProducts',
                'type' => 'select',
                'data' => 'categories',
                'args' => array('taxonomy' => array('product_cat')),
                'multi' => true,
                'ajax'  => true,
                'title' => __('Exclude All Products of the following categories', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('This will exclude only products inside a category, not the category title itself. Good for overview pages.', 'woocommerce-pdf-catalog'),
            ),
            array(
                'id'     =>'excludeProductsWithCategories',
                'type' => 'select',
                'data' => 'categories',
                'args' => array('taxonomy' => array('product_cat')),
                'multi' => true,
                'ajax'  => true,
                'title' => __('Exclude Products with categories', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('This will exclude products, which contain one of the excluded category.', 'woocommerce-pdf-catalog'),
            ),
            // FAST (custom options)
            array(
                'id'     =>'excludeProducts',
                'type' => 'select',
                // 'options' => $woocommerce_pdf_catalog_options_products,
                'data' => 'posts',
                'args' => array('post_type' => array('product'), 'posts_per_page' => -1),
                'multi' => true,
                // not working
                'ajax' => true,
                'title' => __('Exclude Products', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Which products should be excluded in the PDF catalog.', 'woocommerce-pdf-catalog'),
            ),
            array(
                'id'       => 'excludeProductsRevert',
                'type'     => 'checkbox',
                'title'    => __( 'Revert Products Exclusion', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Instead of exclusion it will include.', 'woocommerce-pdf-catalog' ),
            ),
            array(
                'id'     =>'excludeOutOfStockProducts',
                'type'     => 'checkbox',
                'title'    => __( 'Exclude out of stock products', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'When set to true this will hide out of stock products.', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Performance', 'woocommerce-pdf-catalog' ),
        'id'         => 'performance',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'performanceDisableSubstitutions',
                'type'     => 'switch',
                'title'    => __( 'Disable Substitutions', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Specify whether to substitute missing characters in UTF-8(multibyte) documents. <a target="_blank" href="https://mpdf.github.io/reference/mpdf-variables/usesubstitutions.html">Read more here</a>', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
            ),
            array(
                'id'       => 'performanceUseSimpleTables',
                'type'     => 'switch',
                'title'    => __( 'Use Simple Tables', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Disables complex table borders etc. to improve performance. <a target="_blank" href="https://mpdf.github.io/reference/mpdf-variables/simpletables.html">Read more here</a>', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
            ),
            array(
                'id'       => 'performanceUsePackTableData',
                'type'     => 'switch',
                'title'    => __( 'Use Pack Table Data', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Use binary packing of table data to reduce memory usage. <a target="_blank" href="https://mpdf.github.io/reference/mpdf-variables/packtabledata.html">Read more here</a>.', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
            ),
            array(
                'id'       => 'performanceUseImageLocally',
                'type'     => 'switch',
                'title'    => __( 'Use Images Locally', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'This will get images directly from your server paths rather than requesting it from a http or https. Only enable it if your images are on the same server!', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
            ),
            array(
                'id'       => 'enableCache',
                'type'     => 'switch',
                'title'    => __( 'Enable Cache', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Use button below to create PDF files for cache.', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
            ),
            array(
                'id'       => 'enableCacheRegenerate',
                'type'     => 'checkbox',
                'title'    => __( 'Regenerate Cache Daily', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'This will delete old PDFs and create new ones every day.', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
                'required' => array('enableCache','equals','1'),
            ),
            array(
                'id'          => 'enableCacheRegenerateAuth',
                'type'        => 'password',
                'username'    => true,
                'title'       => __('Username & Password (if Basic Authentication htpasswd)', 'wordpress-helpdesk'),
                'placeholder' => array(
                    'username'   => __('Enter your Username', 'woocommerce-pdf-catalog'),
                    'password'   => __('Enter your Password', 'woocommerce-pdf-catalog'),
                ),
                'required' => array('enableCacheRegenerate','equals','1'),
            ),
            array(
                'id'       => 'enableCacheRegenerateReport',
                'type'     => 'checkbox',
                'title'    => __( 'Send Regenerate Report', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'This will send a cache report to admin email each time.', 'woocommerce-pdf-catalog' ),
                'default'  => '0',
                'required' => array('enableCacheRegenerate','equals','1'),
            ),
            array(
                'id'   => 'cacheControls',
                'type' => 'info',
                'desc' => __('<div style="text-align:center;">
                    <a href="' . get_admin_url() . 'admin.php?page=woocommerce_pdf_catalog_options_options&delete_cached_pdfs=true" class="button button-primary">Delete Cached PDFs</a>  
                    <a href="' . get_admin_url() . 'admin.php?page=woocommerce_pdf_catalog_options_options&regenerate_cached_pdfs=true" class="button button-primary">Regenerate Cache</a>
                    </div>', 'woocommerce-pdf-catalog'),
                'required' => array('enableCache','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Defaults', 'woocommerce-pdf-catalog' ),
        'id'         => 'defaults',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'format',
                'type'     => 'select',
                'title'    => __( 'Format', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Choose a pre-defined page size. A4 is recommended!', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    'A4' => __('A4', 'woocommerce-pdf-catalog'),
                    'A4-L' => __('A4 Landscape', 'woocommerce-pdf-catalog'),
                    'A0' => __('A0', 'woocommerce-pdf-catalog'),
                    'A1' => __('A1', 'woocommerce-pdf-catalog'),
                    'A3' => __('A3', 'woocommerce-pdf-catalog'),
                    'A5' => __('A5', 'woocommerce-pdf-catalog'),
                    'A6' => __('A6', 'woocommerce-pdf-catalog'),
                    'A7' => __('A7', 'woocommerce-pdf-catalog'),
                    'A8' => __('A8', 'woocommerce-pdf-catalog'),
                    'A9' => __('A9', 'woocommerce-pdf-catalog'),
                    'A10' => __('A10', 'woocommerce-pdf-catalog'),
                    'Letter' => __('Letter', 'woocommerce-pdf-catalog'),
                    'Legal' => __('Legal', 'woocommerce-pdf-catalog'),
                    'Executive' => __('Executive', 'woocommerce-pdf-catalog'),
                    'Folio' => __('Folio', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'A4',
            ),
            array(
                'id'       => 'orientation',
                'type'     => 'select',
                'title'    => __( 'Orientation', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Choose landscape or portrait. Portrait recommended!', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    'P' => __('P', 'woocommerce-pdf-catalog'),
                    'L' => __('L', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'P',
            ),
            array(
                'id'     =>'fontFamily',
                'type'  => 'select',
                'title' => __('Default Font', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'dejavusans' => __('Sans', 'woocommerce-pdf-catalog' ),
                    'dejavuserif' => __('Serif', 'woocommerce-pdf-catalog' ),
                    'dejavusansmono' => __('Mono', 'woocommerce-pdf-catalog' ),
                    'droidsans' => __('Droid Sans', 'woocommerce-pdf-catalog'),
                    'droidserif' => __('Droid Serif', 'woocommerce-pdf-catalog'),
                    'lato' => __('Lato', 'woocommerce-pdf-catalog'),
                    'lora' => __('Lora', 'woocommerce-pdf-catalog'),
                    'merriweather' => __('Merriweather', 'woocommerce-pdf-catalog'),
                    'montserrat' => __('Montserrat', 'woocommerce-pdf-catalog'),
                    'poppins' => __('Poppins', 'woocommerce-pdf-catalog'),
                    'mulish'    => __('Mulish', 'woocommerce-pdf-catalog'),
                    'opensans' => __('Open sans', 'woocommerce-pdf-catalog'),
                    'opensanscondensed' => __('Open Sans Condensed', 'woocommerce-pdf-catalog'),
                    'oswald' => __('Oswald', 'woocommerce-pdf-catalog'),
                    'ptsans' => __('PT Sans', 'woocommerce-pdf-catalog'),
                    'raleway' => __('Raleway', 'woocommerce-pdf-catalog'),
                    'sourcesanspro' => __('Source Sans Pro', 'woocommerce-pdf-catalog'),
                    'slabo' => __('Slabo', 'woocommerce-pdf-catalog'),
                    'roboto' => __('Roboto', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'dejavusans',
            ),
            array(
                'id'     =>'fontSize',
                'type'     => 'spinner', 
                'title'    => __('Default Font size', 'woocommerce-pdf-catalog'),
                'default'  => '11',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
            ),
            array(
                'id'     =>'lineHeight',
                'type'     => 'spinner', 
                'title'    => __('Default line height', 'woocommerce-pdf-catalog'),
                'default'  => '16',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
            ),
            array(
                'id'       => 'backToToCText',
                'type'     => 'text',
                'title'    => __('Back to ToC Text', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Back to Table of Contents Text', 'woocommerce-pdf-catalog'),
                'default'  => 'Back to Table of Contents',
            ), 
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Watermark', 'woocommerce-pdf-catalog' ),
        'desc' => __( 'Add a watermark to your catalog.', 'woocommerce-pdf-catalog' ), 
        'id'         => 'watermark',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'watermarkEnable',
                'type'     => 'switch',
                'title'    => __( 'Enable Watermark', 'woocommerce-pdf-catalog' ),
                'subtitle'    => __( 'Show a Watermark in your PDF.', 'woocommerce-pdf-catalog' ),
                'default' => 0,
            ),
            array(
                'id'     =>'watermarkType',
                'type'  => 'select',
                'title' => __('Watermark Type', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'text' => __('Text', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'text',
                'required' => array('watermarkEnable','equals','1'),
            ),
            array(
                'id'     =>'watermarkImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Watermark Image', 'woocommerce-pdf-catalog'), 
                'required' => array('watermarkType','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'       => 'watermarkText',
                'type'     => 'text',
                'title'    => __('Watermark Text', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Enter your watermark text here.', 'woocommerce-pdf-catalog'),
                'default'  => 'CONFIDENTIAL',
                'required' => array('watermarkType','equals','text'),
            ), 
            array(
                'id'       => 'watermarkTransparency',
                'type'     => 'text',
                'title'    => __('Watermark Transparency', 'woocommerce-pdf-catalog'),
                'subtitle' => __('A value from 0 to 1. Example: 0.2', 'woocommerce-pdf-catalog'),
                'default'  => '0.2',
                'required' => array('watermarkEnable','equals','1'),
            ), 
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Cover Page', 'woocommerce-pdf-catalog' ),
        'id'         => 'cover',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableCover',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable the Cover Page', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
            ),
            array(
                'id'     =>'coverImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Cover image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Recommended Image Size: 1240 x 1754 px.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableCover','equals','1'),
            ),
            array(
                'id'     =>'saleCoverImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Sale Cover image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Recommended Image Size: 1240 x 1754 px.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableCover','equals','1'),
            ),
            array(
                'id'     =>'wishlistCoverImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Wishlist Cover image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Recommended Image Size: 1240 x 1754 px.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableCover','equals','1'),
            ),
            array(
                'id'       => 'coverOnlyFull',
                'type'     => 'checkbox',
                'title'    => __( 'Full catalog only', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Only show for complete Catalog', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableCover','equals','1'),
            ),
            array(
                'id'       => 'coverShowForCategories',
                'type'     => 'checkbox',
                'title'    => __( 'Show Cover Images between categories', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'This will place the cover image inside the PDF catalog for each category before.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableCover','equals','1'),
            ),
            array(
                'id'     =>'coverText',
                'type'  => 'editor',
                'title' => __('Text on Cover', 'woocommerce-pdf-catalog'), 
                'required' => array('enableCover','equals','1'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => '',
            ),
        ),
    ) );


    $framework::setSection( $opt_name, array(
        'title'      => __( 'Table of Contents', 'woocommerce-pdf-catalog' ),
        'id'         => 'toc',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableToC',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable Table of Contents page', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
            ),
            array(
                'id'       => 'ToCShowCategories',
                'type'     => 'checkbox',
                'title'    => __( 'Show Categories', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Show links to categories in your table of contents.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'       => 'ToCShowProducts',
                'type'     => 'checkbox',
                'title'    => __( 'Show Products', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Show links to products in your table of contents. Make sure split chunks in advanced settings is enabled!', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'       => 'ToCPaging',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Paging', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Place pages in the ToCe.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'       => 'ToCLinking',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Linking', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Place links in the ToC to the category page.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'       => 'ToCOnlyFull',
                'type'     => 'checkbox',
                'title'    => __( 'Full catalog only', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Only show for complete Catalog', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'       => 'ToCRemoveHeader',
                'type'     => 'checkbox',
                'title'    => __( 'Remover Header', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array(
                    array('enableToC','equals','1'),
                    array('enableCover','equals','0'),
                ),
            ),
            array(
                'id'       => 'ToCRemoveFooter',
                'type'     => 'checkbox',
                'title'    => __( 'Remove Footer', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array(
                    array('enableToC','equals','1'),
                    array('enableCover','equals','0'),
                ),
            ),
            array(
                'id'       => 'ToCResetPageNumber',
                'type'     => 'checkbox',
                'title'    => __( 'Reset the Page Number', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Next page starts with 1.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array(
                    array('enableToC','equals','1'),
                    array('enableCover','equals','0'),
                ),
            ),
            array(
                'id'     =>'ToCPosition',
                'type'  => 'select',
                'title' => __('Position', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'first' => __('First Page', 'woocommerce-pdf-catalog' ),
                    'last' => __('Last Page', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'first',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'     =>'ToCTextBefore',
                'type'  => 'editor',
                'title' => __('Text Before ToC', 'woocommerce-pdf-catalog'), 
                'required' => array('enableToC','equals','1'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => '<h1>Table of Contents</h1>',
            ),
            array(
                'id'     =>'ToCTextAfter',
                'type'  => 'editor',
                'title' => __('Text After ToC', 'woocommerce-pdf-catalog'), 
                'required' => array('enableToC','equals','1'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'             => 'ToCPadding',
                'type'           => 'spacing',
                // 'output'         => array('.site-header'),
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Choose the spacing or padding you want.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '50px', 
                    'padding-right'   => '60px', 
                    'padding-bottom'  => '10px', 
                    'padding-left'    => '60px',
                    'units'          => 'px', 
                ),
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'     =>'ToCFontFamily',
                'type'  => 'select',
                'title' => __('Default Font', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'dejavusans' => __('Sans', 'woocommerce-pdf-catalog' ),
                    'dejavuserif' => __('Serif', 'woocommerce-pdf-catalog' ),
                    'dejavusansmono' => __('Mono', 'woocommerce-pdf-catalog' ),
                    'droidsans' => __('Droid Sans', 'woocommerce-pdf-catalog'),
                    'droidserif' => __('Droid Serif', 'woocommerce-pdf-catalog'),
                    'lato' => __('Lato', 'woocommerce-pdf-catalog'),
                    'lora' => __('Lora', 'woocommerce-pdf-catalog'),
                    'merriweather' => __('Merriweather', 'woocommerce-pdf-catalog'),
                    'montserrat' => __('Montserrat', 'woocommerce-pdf-catalog'),
                    'poppins' => __('Poppins', 'woocommerce-pdf-catalog'),
                    'mulish'    => __('Mulish', 'woocommerce-pdf-catalog'),
                    'opensans' => __('Open sans', 'woocommerce-pdf-catalog'),
                    'opensanscondensed' => __('Open Sans Condensed', 'woocommerce-pdf-catalog'),
                    'oswald' => __('Oswald', 'woocommerce-pdf-catalog'),
                    'ptsans' => __('PT Sans', 'woocommerce-pdf-catalog'),
                    'sourcesanspro' => __('Source Sans Pro', 'woocommerce-pdf-catalog'),
                    'slabo' => __('Slabo', 'woocommerce-pdf-catalog'),
                    'roboto' => __('Roboto', 'woocommerce-pdf-catalog'),
                    'raleway' => __('Raleway', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'dejavusans',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'     =>'ToCFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '13',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
                'required' => array('enableToC','equals','1'),
            ),
            array(
                'id'     =>'ToCLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '16',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableToC','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Header', 'woocommerce-pdf-catalog' ),
        'id'         => 'header',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableHeader',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable header', 'woocommerce-pdf-catalog' ),
                'default'  => '1',
            ),
            array(
                'id'     =>'headerBackgroundColor',
                'type' => 'color',
                'title' => __('Header background color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'required' => array('enableHeader','equals','1'),
                'default'  => '#222222',
            ),
            array(
                'id'     =>'headerTextColor',
                'type'  => 'color',
                'title' => __('Header text color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'required' => array('enableHeader','equals','1'),
                'default'  => '#FFFFFF',
            ),
            array(
                'id'     =>'headerFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '9',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
                'required' => array('enableHeader','equals','1'),
            ),
            array(
                'id'     =>'headerLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '12',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableHeader','equals','1'),
            ),
            array(
                'id'     =>'headerLayout',
                'type'  => 'select',
                'title' => __('Header Layout', 'woocommerce-pdf-catalog'), 
                'required' => array('enableHeader','equals','1'),
                'options'  => array(
                    'oneCol' => __('1/1', 'woocommerce-pdf-catalog' ),
                    'twoCols' => __('1/2 + 1/2', 'woocommerce-pdf-catalog' ),
                    'threeCols' => __('1/3 + 1/3 + 1/3', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'twoCols',
            ),
            array(
                'id'     =>'headerTopMargin',
                'type'     => 'spinner', 
                'title'    => __('Header Margin', 'woocommerce-pdf-catalog'),
                'default'  => '20',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
            ),
            array(
                'id'     =>'headerHeight',
                'type'     => 'spinner', 
                'title'    => __('Header Height', 'woocommerce-pdf-catalog'),
                'default'  => '40',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
            ),
            array(
                'id'             => 'headerPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Header Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Choose the spacing or padding you want.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '10px', 
                    'padding-right'   => '20px', 
                    'padding-bottom'  => '10px', 
                    'padding-left'    => '20px',
                    'units'          => 'px', 
                )
            ),
            array(
                'id'     =>'headerTopLeft',
                'type'  => 'select',
                'title' => __('Top Left Header', 'woocommerce-pdf-catalog'), 
                'required' => array('enableHeader','equals','1'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-pdf-catalog' ),
                    'bloginfo' => __('Blog information', 'woocommerce-pdf-catalog' ),
                    'text' => __('Custom text', 'woocommerce-pdf-catalog' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-pdf-catalog' ),
                    'category' => __('Category', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                    'exportinfo' => __('Export Information', 'woocommerce-pdf-catalog' ),
                    'qr' => __('QR-Code', 'woocommerce-pdf-catalog' ),
                    'toc' => __('Back to Table of Contents', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'bloginfo',
            ),
            array(
                'id'     =>'headerTopLeftText',
                'type'  => 'editor',
                'title' => __('Top Left Header Text', 'woocommerce-pdf-catalog'), 
                'required' => array('headerTopLeft','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopLeftImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Left Header Image', 'woocommerce-pdf-catalog'), 
                'required' => array('headerTopLeft','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopMiddle',
                'type'  => 'select',
                'title' => __('Top Middle Header', 'woocommerce-pdf-catalog'), 
                'required' => array('headerLayout','equals','threeCols'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-pdf-catalog' ),
                    'bloginfo' => __('Blog information', 'woocommerce-pdf-catalog' ),
                    'text' => __('Custom text', 'woocommerce-pdf-catalog' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-pdf-catalog' ),
                    'category' => __('Category', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                    'exportinfo' => __('Export Information', 'woocommerce-pdf-catalog' ),
                    'qr' => __('QR-Code', 'woocommerce-pdf-catalog' ),
                    'toc' => __('Back to Table of Contents', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'category',
            ),
            array(
                'id'     =>'headerTopMiddleText',
                'type'  => 'editor',
                'title' => __('Top Middle Header Text', 'woocommerce-pdf-catalog'), 
                'required' => array('headerTopMiddle','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopMiddleImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Middle Header Image', 'woocommerce-pdf-catalog'), 
                'required' => array('headerTopMiddle','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopRight',
                'type'  => 'select',
                'title' => __('Top Right Header', 'woocommerce-pdf-catalog'), 
                'required' => array('headerLayout','equals',array('threeCols','twoCols')),
                'options'  => array(
                    'none' => __('None', 'woocommerce-pdf-catalog' ),
                    'bloginfo' => __('Blog information', 'woocommerce-pdf-catalog' ),
                    'text' => __('Custom text', 'woocommerce-pdf-catalog' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-pdf-catalog' ),
                    'category' => __('Category', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                    'exportinfo' => __('Export Information', 'woocommerce-pdf-catalog' ),
                    'qr' => __('QR-Code', 'woocommerce-pdf-catalog' ),
                    'toc' => __('Back to Table of Contents', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'pagenumber',
            ),
            array(
                'id'     =>'headerTopRightText',
                'type'  => 'editor',
                'title' => __('Top Right Header Text', 'woocommerce-pdf-catalog'), 
                'required' => array('headerTopRight','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'headerTopRightImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Right Header Image', 'woocommerce-pdf-catalog'), 
                'required' => array('headerTopRight','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Category Layout', 'woocommerce-pdf-catalog' ),
        'id'         => 'category-layout',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableCategory',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Show Category', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
            ),
            array(
                'id'       => 'categoryLayout',
                'type'     => 'image_select',
                'title'    => __( 'Select Layout', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    '1'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/category-layouts/1.jpg'),
                    '2'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/category-layouts/2.jpg'),
                    '3'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/category-layouts/3.jpg'),
                    '4'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/category-layouts/4.jpg'),
                    '5'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/category-layouts/5.jpg'),
                    '6'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/category-layouts/6.jpg'),
                ),
                'default' => '1',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryLayout6Image',
                'type' => 'media',
                'url'      => true,
                'title' => __('Layout 6 image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Set the image for category layout 6.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('categoryLayout','equals','6'),
            ),
            array(
                'id'       => 'categoryShowParentCategory',
                'type'     => 'checkbox',
                'title'    => __( 'Show Parent Category', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'If "Include Children Products" is not enabled and you have assigned products only on lower level, it will not show parent categories. Enable this to show also the parent categories.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'       => 'categoryTitlePagebreak',
                'type'     => 'checkbox',
                'title'    => __( 'Add Pagebreak after Category Name', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Add pagebreak after each category name / title elemen.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'       => 'categoryPagebreak',
                'type'     => 'checkbox',
                'title'    => __( 'Add Pagebreak after category products.', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Add pagebreak after each category and products.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'       => 'categoryHideDescription',
                'type'     => 'checkbox',
                'title'    => __( 'Hide Description', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Hide the category Description', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'             => 'categoryPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Category Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Choose the padding you want.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '20px', 
                    'padding-right'   => '50px', 
                    'padding-bottom'  => '2px', 
                    'padding-left'    => '50px',
                    'units'          => 'px', 
                ),
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'             => 'categoryInformationPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Category Information Container Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Padding for the element, where the text is.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '20px', 
                    'padding-right'   => '10px', 
                    'padding-bottom'  => '20px', 
                    'padding-left'    => '10px',
                    'units'          => 'px', 
                ),
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryGlobalImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Category image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('This overrides the category image.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryImageSize',
                'type'     => 'spinner', 
                'title'    => __('Image size', 'woocommerce-pdf-catalog'),
                'default'  => '400',
                'min'      => '1',
                'step'     => '10',
                'max'      => '1000',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'       => 'categoryImageSizeType',
                'type'     => 'text',
                'title'    => __('Image Size Type', 'woocommerce-pdf-catalog'),
                'subtitle' => __('You can use e.g. full, large, thumbnail, woocommerce_single, shop_single, shop_catalog...', 'woocommerce-pdf-catalog'),
                'default'  => 'large',
                'required' => array('enableCategory','equals','1'),
            ), 
            array(
                'id'     =>'categoryHeadingFontSize',
                'type'     => 'spinner', 
                'title'    => __('Heading font size', 'woocommerce-pdf-catalog'),
                'default'  => '30',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryHeadingFontFamily',
                'type'  => 'select',
                'title' => __('Headings Font', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'dejavusans' => __('Sans', 'woocommerce-pdf-catalog' ),
                    'dejavuserif' => __('Serif', 'woocommerce-pdf-catalog' ),
                    'dejavusansmono' => __('Mono', 'woocommerce-pdf-catalog' ),
                    'droidsans' => __('Droid Sans', 'woocommerce-pdf-catalog'),
                    'droidserif' => __('Droid Serif', 'woocommerce-pdf-catalog'),
                    'lato' => __('Lato', 'woocommerce-pdf-catalog'),
                    'lora' => __('Lora', 'woocommerce-pdf-catalog'),
                    'merriweather' => __('Merriweather', 'woocommerce-pdf-catalog'),
                    'montserrat' => __('Montserrat', 'woocommerce-pdf-catalog'),
                    'poppins' => __('Poppins', 'woocommerce-pdf-catalog'),
                    'mulish'    => __('Mulish', 'woocommerce-pdf-catalog'),
                    'opensans' => __('Open sans', 'woocommerce-pdf-catalog'),
                    'opensanscondensed' => __('Open Sans Condensed', 'woocommerce-pdf-catalog'),
                    'oswald' => __('Oswald', 'woocommerce-pdf-catalog'),
                    'ptsans' => __('PT Sans', 'woocommerce-pdf-catalog'),
                    'sourcesanspro' => __('Source Sans Pro', 'woocommerce-pdf-catalog'),
                    'slabo' => __('Slabo', 'woocommerce-pdf-catalog'),
                    'roboto' => __('Roboto', 'woocommerce-pdf-catalog'),
                    'raleway' => __('Raleway', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'dejavusans',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryTextFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '16',
                'min'      => '1',
                'step'     => '1',
                'max'      => '60',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '20',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryTextAlign',
                'type'  => 'select',
                'title' => __('Text Align', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'left' => __('Left', 'woocommerce-pdf-catalog' ),
                    'center' => __('Center', 'woocommerce-pdf-catalog' ),
                    'right' => __('Right', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'center',
                'required' => array('enableCategory','equals','1'),
            ),
            array(
                'id'     =>'categoryTextColor',
                'type'  => 'color',
                'url'      => true,
                'title' => __('Category text color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'default' => '#000000',
                'required' => array('enableCategory','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Products Layout', 'woocommerce-pdf-catalog' ),
        'id'         => 'products-layout',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'productsLayout',
                'type'     => 'image_select',
                'title'    => __( 'Select Layout', 'woocommerce-pdf-catalog' ),
                'options'  => array(
                    '1'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/1.jpg'),
                    '2'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/2.jpg'),
                    '3'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/3.jpg'),
                    '4'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/4.jpg'),
                    '5'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/5.jpg'),
                    '6'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/6.jpg'),
                    '7'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/7.jpg'),
                    '8'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/8.jpg'),
                    '9'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/9.jpg'),
                    // NEVER PAID !!!
                    // '10'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/10.jpg'),
                    '11'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/11.jpg'),
                    '12'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/12.jpg'),
                    '13'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/13.jpg'),
                    '14'      => array('img'   => plugin_dir_url( __FILE__ ) . 'img/product-layouts/14.jpg'),
                ),
                'default' => '2'
            ),
            array(
                'id'     =>'productsContainerHeight',
                'type'     => 'spinner', 
                'title'    => __('Container Height', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Important to avoid page overflows.', 'woocommerce-pdf-catalog'),
                'default'  => '350',
                'min'      => '1',
                'step'     => '1',
                'max'      => '99999',
            ),
            array(
                'id'     =>'productsImageWidth',
                'type'     => 'spinner', 
                'title'    => __('Image Width (%)', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Respect the Products Content With.', 'woocommerce-pdf-catalog'),
                'default'  => '30',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array(
                    array('productsLayout','!=','4'),
                    array('productsLayout','!=','5'),
                    array('productsLayout','!=','6'),
                    array('productsLayout','!=','7'),
                    array('productsLayout','!=','8'),
                ),
            ),
            array(
                'id'     =>'productsContentWidth',
                'type'     => 'spinner', 
                'title'    => __('Content Width (%)', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Respect the Products Image With.', 'woocommerce-pdf-catalog'),
                'default'  => '70',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array(
                    array('productsLayout','!=','4'),
                    array('productsLayout','!=','5'),
                    array('productsLayout','!=','6'),
                    array('productsLayout','!=','7'),
                    array('productsLayout','!=','8'),
                ),
            ),
            array(
                'id'       => 'productPagebreak',
                'type'     => 'checkbox',
                'title'    => __( 'Add Pagebreak', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Add pagebreak after each product row.', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
            ),
            array(
                'id'             => 'productContainerPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Product Container Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Choose the padding you want.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '30px', 
                    'padding-right'   => '30px', 
                    'padding-bottom'  => '30px', 
                    'padding-left'    => '30px',
                    'units'          => 'px', 
                ),
            ),
            array(
                'id'             => 'productInformationContainerPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Product Information Container Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Padding for the element, where the text is.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '5px', 
                    'padding-right'   => '20px', 
                    'padding-bottom'  => '50px', 
                    'padding-left'    => '20px',
                    'units'          => 'px', 
                ),
            ),
            array(
                'id'     =>'productsBackgroundColor',
                'type'  => 'color',
                'title' => __('Background color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'default' => 'transparent',
            ),
            array( 
                'id'       => 'productsTopBorder',
                'type'     => 'border',
                'title'    => __('Top Border', 'woocommerce-pdf-catalog'),
                'default'  => array(
                    'border-color'  => '#FFFFFF', 
                    'border-style'  => 'solid', 
                    'border-top'    => '0px', 
                    'border-right'  => '0px', 
                    'border-bottom' => '0px', 
                    'border-left'   => '0px'
                )
            ),
            array( 
                'id'       => 'productsRightBorder',
                'type'     => 'border',
                'title'    => __('Right Border', 'woocommerce-pdf-catalog'),
                'default'  => array(
                    'border-color'  => '#FFFFFF', 
                    'border-style'  => 'solid', 
                    'border-top'    => '0px', 
                    'border-right'  => '0px', 
                    'border-bottom' => '0px', 
                    'border-left'   => '0px'
                )
            ),
            array( 
                'id'       => 'productsBottomBorder',
                'type'     => 'border',
                'title'    => __('Bottom Border', 'woocommerce-pdf-catalog'),
                'default'  => array(
                    'border-color'  => '#FFFFFF', 
                    'border-style'  => 'solid', 
                    'border-top'    => '0px', 
                    'border-right'  => '0px', 
                    'border-bottom' => '0px', 
                    'border-left'   => '0px'
                )
            ),
            array( 
                'id'       => 'productsLeftBorder',
                'type'     => 'border',
                'title'    => __('Left Border', 'woocommerce-pdf-catalog'),
                'default'  => array(
                    'border-color'  => '#FFFFFF', 
                    'border-style'  => 'solid', 
                    'border-top'    => '0px', 
                    'border-right'  => '0px', 
                    'border-bottom' => '0px', 
                    'border-left'   => '0px'
                )
            ),
            array(
                'id'     =>'productsHeadingsFontSize',
                'type'     => 'spinner', 
                'title'    => __('Headings font size', 'woocommerce-pdf-catalog'),
                'default'  => '20',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
            ),
            array(
                'id'     =>'productsHeadingsLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Heading line height', 'woocommerce-pdf-catalog'),
                'default'  => '22',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
            ),
            array(
                'id'     =>'productsHeadingsFontFamily',
                'type'  => 'select',
                'title' => __('Headings Font', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'dejavusans' => __('Sans', 'woocommerce-pdf-catalog' ),
                    'dejavuserif' => __('Serif', 'woocommerce-pdf-catalog' ),
                    'dejavusansmono' => __('Mono', 'woocommerce-pdf-catalog' ),
                    'droidsans' => __('Droid Sans', 'woocommerce-pdf-catalog'),
                    'droidserif' => __('Droid Serif', 'woocommerce-pdf-catalog'),
                    'lato' => __('Lato', 'woocommerce-pdf-catalog'),
                    'lora' => __('Lora', 'woocommerce-pdf-catalog'),
                    'merriweather' => __('Merriweather', 'woocommerce-pdf-catalog'),
                    'montserrat' => __('Montserrat', 'woocommerce-pdf-catalog'),
                    'poppins' => __('Poppins', 'woocommerce-pdf-catalog'),
                    'mulish'    => __('Mulish', 'woocommerce-pdf-catalog'),
                    'opensans' => __('Open sans', 'woocommerce-pdf-catalog'),
                    'opensanscondensed' => __('Open Sans Condensed', 'woocommerce-pdf-catalog'),
                    'oswald' => __('Oswald', 'woocommerce-pdf-catalog'),
                    'ptsans' => __('PT Sans', 'woocommerce-pdf-catalog'),
                    'sourcesanspro' => __('Source Sans Pro', 'woocommerce-pdf-catalog'),
                    'slabo' => __('Slabo', 'woocommerce-pdf-catalog'),
                    'roboto' => __('Roboto', 'woocommerce-pdf-catalog'),
                    'raleway' => __('Raleway', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'dejavusans',
            ),
            array(
                'id'     =>'productsFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '13',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
            ),
            array(
                'id'     =>'productsLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '16',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
            ),
            array(
                'id'     =>'productsTextAlign',
                'type'  => 'select',
                'title' => __('Text Align', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'left' => __('Left', 'woocommerce-pdf-catalog' ),
                    'center' => __('Center', 'woocommerce-pdf-catalog' ),
                    'right' => __('Right', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'left'
            ),
            array(
                'id'     =>'productsTextColor',
                'type'  => 'color',
                'title' => __('Text Color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'default' => '#000000',
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Data to show', 'woocommerce-pdf-catalog' ),
        'id'         => 'data',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'showImage',
                'type'     => 'switch',
                'title'    => __( 'Show Product Image', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
                array(
                    'id'       => 'productsImageSize',
                    'type'     => 'spinner', 
                    'title'    => __('Image size', 'woocommerce-pdf-catalog'),
                    'default'  => '250',
                    'min'      => '1',
                    'step'     => '10',
                    'max'      => '1000',
                    'required' => array('showImage','equals','1'),
                ),
                array(
                    'id'       => 'showImageSize',
                    'type'     => 'text',
                    'title'    => __('Image Size Type', 'woocommerce-pdf-catalog'),
                    'subtitle' => __('You can use e.g. full, large, thumbnail, woocommerce_single, shop_single, shop_catalog...', 'woocommerce-pdf-catalog'),
                    'default'  => 'shop_catalog',
                    'required' => array('showImage','equals','1'),
                ), 
                array(
                    'id'       => 'showLinkOnImage',
                    'type'     => 'checkbox',
                    'title'    => __( 'Link Image to product page', 'woocommerce-pdf-catalog' ),
                    'default'   => 1,
                    'required' => array('showImage','equals','1'),
                ),
            array(
                'id'       => 'showGalleryImages',
                'type'     => 'switch',
                'title'    => __( 'Show Gallery Images', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
                array(
                    'id'       => 'galleryImageSize',
                    'type'     => 'spinner', 
                    'title'    => __('Gallery Image size', 'woocommerce-pdf-catalog'),
                    'default'  => '250',
                    'min'      => '1',
                    'step'     => '10',
                    'max'      => '1000',
                    'required' => array('showGalleryImages','equals','1'),
                ),
                array(
                    'id'       => 'galleryImageSizeType',
                    'type'     => 'text',
                    'title'    => __('Gallery Image Size Type', 'woocommerce-pdf-catalog'),
                    'subtitle' => __('You can use e.g. full, large, thumbnail, woocommerce_single, shop_single, shop_catalog...', 'woocommerce-pdf-catalog'),
                    'default'  => 'shop_catalog',
                    'required' => array('showGalleryImages','equals','1'),
                ), 
                array(
                    'id'       => 'galleryImageColumns',
                    'type'     => 'spinner', 
                    'title'    => __('Gallery Image Column', 'woocommerce-pdf-catalog'),
                    'default'  => '3',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '10',
                    'required' => array('showGalleryImages','equals','1'),
                ),
                array(
                    'id'       => 'galleryIncludeFeatureImage',
                    'type'     => 'checkbox',
                    'title'    => __( 'Include Feature image in Gallery', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showGalleryImages','equals','1'),
                ),
            array(
                'id'       => 'showCategoryDescription',
                'type'     => 'switch',
                'title'    => __( 'Show 1st Product Category Description', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'showTitle',
                'type'     => 'switch',
                'title'    => __( 'Show Product Title', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
                array(
                    'id'       => 'showTitleCartQuantity',
                    'type'     => 'switch',
                    'title'    => __( 'Show Cart Quantity', 'woocommerce-pdf-catalog' ),
                    'subtitle'    => __( 'Add quantity before title when using cart PDF Export.', 'woocommerce-pdf-catalog' ),
                    'default'   => 1,
                    'required' => array('showTitle','equals','1'),
                ),
                array(
                    'id'       => 'showTitlePrefix',
                    'type'     => 'text',
                    'title'    => __('Product Title Prefix', 'woocommerce-pdf-catalog'),
                    'subtitle' => __('You can add a cutom text like Product: here.', 'woocommerce-pdf-catalog'),
                    'default'  => '',
                    'required' => array('showTitle','equals','1'),
                ), 
            array(
                'id'       => 'showPrice',
                'type'     => 'switch',
                'title'    => __( 'Show Product Price', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
            array(
                'id'       => 'showShortDescription',
                'type'     => 'switch',
                'title'    => __( 'Show Short Description', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
            array(
                'id'       => 'showShortDescriptionStripShortcodes',
                'type'     => 'checkbox',
                'title'    => __( 'Strip Shortcodes of Short Description', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
                'required' => array('showShortDescription','equals','1'),
            ),
            array(
                'id'       => 'showShortDescriptionExcerpt',
                'type'     => 'checkbox',
                'title'    => __( 'Create Excerpt of Short Description', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
                'required' => array('showShortDescription','equals','1'),
            ),
            array(
                'id'       => 'showShortDescriptionExcerptLength',
                'type'     => 'spinner', 
                'title'    => __('Excerpt Length', 'woocommerce-pdf-catalog'),
                'default'  => '50',
                'min'      => '1',
                'step'     => '1',
                'max'      => '1000',
                'required' => array('showShortDescriptionExcerpt','equals','1'),
            ),
            array(
                'id'       => 'showDescription',
                'type'     => 'switch',
                'title'    => __( 'Show Long Description', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                array(
                    'id'       => 'showCustomTabs',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Custom Tabs', 'woocommerce-pdf-catalog' ),
                    'subtitle'    => __( 'Show Custom Tabs after Description. This requires our <a href="https://www.welaunch.io/en/product/woocommerce-ultimate-tabs/" target=_"blank">Ultimate Tabs plugin</a>.', 'woocommerce-pdf-catalog' ),
                    'default'   => 1,
                    'required' => array('showDescription','equals','1'),
                ),
                array(
                    'id'       => 'showDescriptionStripShortcodes',
                    'type'     => 'checkbox',
                    'title'    => __( 'Strip Shortcodes of Description', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showDescription','equals','1'),
                ),
                array(
                    'id'       => 'showDescriptionExcerpt',
                    'type'     => 'checkbox',
                    'title'    => __( 'Create Excerpt of Description', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showDescription','equals','1'),
                ),
                array(
                    'id'       => 'showDescriptionExcerptLength',
                    'type'     => 'spinner', 
                    'title'    => __('Excerpt Length', 'woocommerce-pdf-catalog'),
                    'default'  => '50',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '1000',
                    'required' => array('showDescriptionExcerpt','equals','1'),
                ),
            array(
                'id'       => 'showAttributes',
                'type'     => 'switch',
                'title'    => __( 'Show Product Attributes', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                 array(
                    'id'     =>'showAttributesAttributes',
                    'type'  => 'select',
                    'multi' => true,
                    'title' => __('Select attributes', 'woocommerce-pdf-catalog'), 
                    'subtitle' => __('If empy ALL attributes will show.', 'woocommerce-pdf-catalog'), 
                    'options'  => $attributesSelect,
                    'required' => array('showAttributes','equals','1'),
                ),
                array(
                    'id'       => 'showAttributesWeight',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show weight?', 'woocommerce-pdf-catalog' ),
                    'default'   => 1,
                    'required' => array('showAttributes','equals','1'),
                ),
                array(
                    'id'       => 'showAttributesDimensions',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Dimensions?', 'woocommerce-pdf-catalog' ),
                    'default'   => 1,
                    'required' => array('showAttributes','equals','1'),
                ),
                array(
                    'id'       => 'showAttributesMoveBelowSKU',
                    'type'     => 'checkbox',
                    'title'    => __( 'Move Attributes below SKU', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showAttributes','equals','1'),
                ),
                array(
                    'id'       => 'showAttributesImages',
                    'type'     => 'checkbox',
                    'title'    => __( 'Attribute images', 'woocommerce-pdf-catalog' ),
                    'subtitle'    => __( 'Enable support for our <a href="https://www.welaunch.io/en/product/woocommerce-attribute-images/" target="_blank">attribute images plugin</a> to display attributes as images.', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showAttributes','equals','1'),
                ),
                array(
                    'id'     =>'attributeImageWidth',
                    'type'     => 'spinner', 
                    'title'    => __('Attribute Image Width (px)', 'woocommerce-pdf-catalog'),
                    'default'  => '20',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '9999',
                    'required' => array('showAttributesImages','equals','1'),
                ),

            array(
                'id'       => 'showReadMore',
                'type'     => 'switch',
                'title'    => __( 'Show Read More Link', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
                array(
                    'id'       => 'showReadMoreText',
                    'type'     => 'text',
                    'title'    => __( 'Read More Text', 'woocommerce-pdf-catalog' ),
                    'default'  => __('Read More', 'woocommerce-pdf-catalog'),
                    'required' => array('showReadMore','equals','1'),
                ),
            array(
                'id'       => 'showSKU',
                'type'     => 'switch',
                'title'    => __( 'Show Product SKU', 'woocommerce-pdf-catalog' ),
                'default'   => 1,
            ),
                array(
                    'id'       => 'showSKUMoveUnderTitle',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show SKU under Title', 'woocommerce-print-products' ),
                    'default'   => 0,
                    'required' => array('showSKU','equals','1'),
                ),
            array(
                'id'       => 'showCategories',
                'type'     => 'switch',
                'title'    => __( 'Show Product Categories', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                array(
                    'id'       => 'showCategoriesSingleText',
                    'type'     => 'text',
                    'title'    => __( 'Category Single Text', 'woocommerce-pdf-catalog' ),
                    'default'  => __('Category:', 'woocommerce'),
                    'required' => array('showCategories','equals','1'),
                ),
                array(
                    'id'       => 'showCategoriesPluralText',
                    'type'     => 'text',
                    'title'    => __( 'Category Plural Text', 'woocommerce-pdf-catalog' ),
                    'default'  => __('Categories:', 'woocommerce'),
                    'required' => array('showCategories','equals','1'),
                ),
            array(
                'id'       => 'showStock',
                'type'     => 'switch',
                'title'    => __( 'Show Product Stock Status', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'showTags',
                'type'     => 'switch',
                'title'    => __( 'Show Product Tags', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                array(
                    'id'       => 'showTagsSingleText',
                    'type'     => 'text',
                    'title'    => __( 'Tag Single Text', 'woocommerce-pdf-catalog' ),
                    'default'  => __('Tag:', 'woocommerce'),
                    'required' => array('showTags','equals','1'),
                ),
                array(
                    'id'       => 'showTagsPluralText',
                    'type'     => 'text',
                    'title'    => __( 'Tag Plural Text', 'woocommerce-pdf-catalog' ),
                    'default'  => __('Tags:', 'woocommerce'),
                    'required' => array('showTags','equals','1'),
                ),
            array(
                'id'       => 'showLinkParameters',
                'type'     => 'text',
                'title'    => __('Link Parameters', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Add campaign or custom URL parameters to product linsk from your PDF to track. Variables you can use: {{productName}}, {{productSKU}}, {{catalogName}}. Example: ?utm_source=file&utm_medium=PDF&utm_campaign={{catalogName}}&utm_term={{productName}}', 'woocommerce-pdf-catalog'),
                'default'  => 'utm_source=file&utm_medium=PDF&utm_campaign={{catalogName}}&utm_term={{productName}}',
                'required' => array('showImage','equals','1'),
            ), 
            array(
                'id'       => 'showQR',
                'type'     => 'switch',
                'title'    => __( 'Show QR-Code', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                array(
                    'id'       => 'qrSize',
                    'type'     => 'text',
                    'title'    => __( 'QR Code Size', 'woocommerce-pdf-catalog' ),
                    'subtitle' => __('Float value (e.g. 0.6 or 1.5). Default is 0.8.', 'woocommerce-pdf-catalog'),
                    'default'  => '0.8',
                    'required' => array('showQR','equals','1'),
                ),
            array(
                'id'       => 'showBarcode',
                'type'     => 'switch',
                'title'    => __( 'Show Barcode', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                array(
                    'id'     =>'barcodeType',
                    'type'  => 'select',
                    'title' => __('Barcode Type', 'woocommerce-pdf-catalog'), 
                    'options'  => array(
                        'EAN13' => 'EAN13',
                        'ISBN' => 'ISBN',
                        'ISSN' => 'ISSN',
                        'UPCA' => 'UPCA',
                        'UPCE' => 'UPCE',
                        'EAN8' => 'EAN8',
                        'EAN13P2' => 'EAN13P2',
                        'ISBNP2' => 'ISBNP2',
                        'ISSNP2' => 'ISSNP2',
                        'UPCAP2' => 'UPCAP2',
                        'UPCEP2' => 'UPCEP2',
                        'EAN8P2' => 'EAN8P2',
                        'EAN13P5' => 'EAN13P5',
                        'ISBNP5' => 'ISBNP5',
                        'ISSNP5' => 'ISSNP5',
                        'UPCAP5' => 'UPCAP5',
                        'UPCEP5' => 'UPCEP5',
                        'EAN8P5' => 'EAN8P5',
                        'IMB' => 'IMB',
                        'RM4SCC' => 'RM4SCC',
                        'KIX' => 'KIX',
                        'POSTNET' => 'POSTNET',
                        'PLANET' => 'PLANET',
                        'C128A' => 'C128A',
                        'C128B' => 'C128B',
                        'C128C' => 'C128C',
                        'EAN128A' => 'EAN128A',
                        'EAN128B' => 'EAN128B',
                        'EAN128C' => 'EAN128C',
                        'C39' => 'C39',
                        'C39+' => 'C39+',
                        'C39E' => 'C39E',
                        'C39E+' => 'C39E+',
                        'S25' => 'S25',
                        'S25+' => 'S25+',
                        'I25' => 'I25',
                        'I25+' => 'I25+',
                        'I25B' => 'I25B',
                        'I25B+' => 'I25B+',
                        'C93' => 'C93',
                        'MSI' => 'MSI',
                        'MSI+' => 'MSI+',
                        'CODABAR' => 'CODABAR',
                        'CODE11' => 'CODE11',
                    ),
                    'default' => 'EAN13',
                    'required' => array('showBarcode','equals','1'),
                ),
                array(
                    'id'       => 'barcodeMetaKey',
                    'type'     => 'text',
                    'title'    => __( 'Your Barcode Meta Key', 'woocommerce-pdf-catalog' ),
                    'subtitle' => __('On Product level you need a meta key, where the bar code itself is stored.', 'woocommerce-pdf-catalog'),
                    'default'  => 'barcode',
                    'required' => array('showBarcode','equals','1'),
                ),
                array(
                    'id'       => 'barcodeSize',
                    'type'     => 'text',
                    'title'    => __( 'Barcode Size', 'woocommerce-pdf-catalog' ),
                    'subtitle' => __('Float value (e.g. 0.6 or 1.5). Default is 1.', 'woocommerce-pdf-catalog'),
                    'default'  => '1',
                    'required' => array('showBarcode','equals','1'),
                ),
            array(
                'id'       => 'showVariations',
                'type'     => 'switch',
                'title'    => __( 'Show Variations', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
                array(
                    'id'       => 'showVariationsTitle',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variations Title', 'woocommerce-pdf-catalog' ),
                    'default'   => 1,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsLimit',
                    'type'     => 'spinner', 
                    'title'    => __('Limit Variations', 'woocommerce-pdf-catalog'),
                    'subtitle' => __('Sometimes the layout breaks because of many variations. Set a limit', 'woocommerce-pdf-catalog'),
                    'default'  => '40',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '1000',
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsTableWidth',
                    'type'     => 'spinner', 
                    'title'    => __('Width of a variation table', 'woocommerce-pdf-catalog'),
                    'default'  => '250',
                    'min'      => '1',
                    'step'     => '10',
                    'max'      => '1000',
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsTables',
                    'type'     => 'checkbox', 
                    'title'    => __('Split Variations into multiple tables', 'woocommerce-pdf-catalog'),
                    'subtitle' => __('Set a limit after the table breaks below.', 'woocommerce-pdf-catalog'),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsTableLimit',
                    'type'     => 'spinner',
                    'title'    => __( 'Create a new table after X variations', 'woocommerce-pdf-catalog' ),
                    'default'  => '20',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '1000',
                    'required' => array( array('showVariations','equals','1'), array('variationsTables','equals','1')),
                ),
                array(
                    'id'       => 'variationsTableNewRow',
                    'type'     => 'spinner',
                    'title'    => __( 'Create a new row after X variation tables', 'woocommerce-pdf-catalog' ),
                    'default'  => '3',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '1000',
                    'required' => array( array('showVariations','equals','1'), array('variationsTables','equals','1')),
                ),
                array(
                    'id'       => 'variationsShowImage',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation Image', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsImageSize',
                    'type'     => 'spinner', 
                    'title'    => __('Variations Image Size', 'woocommerce-pdf-catalog'),
                    'default'  => '50',
                    'min'      => '1',
                    'step'     => '1',
                    'max'      => '1000',
                    'required' => array( 
                                    array('showVariations','equals','1'), 
                                    array('variationsShowImage','equals','1')
                                ),
                ),
                array(
                    'id'       => 'variationsShowSKU',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation SKU', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsShowPrice',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation Price', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsShowDescription',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation Description', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsShowStock',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation Stock', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsShowAttributes',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation Attributes', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
                array(
                    'id'       => 'variationsShowComment',
                    'type'     => 'checkbox',
                    'title'    => __( 'Show Variation Comment Field', 'woocommerce-pdf-catalog' ),
                    'default'   => 0,
                    'required' => array('showVariations','equals','1'),
                ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Custom Post Fields', 'woocommerce-pdf-catalog' ),
        'desc' => __( 'With the below settings you can show custom post meta keys for the posts. First enable a custom key, then reload the admin panel and drag the item into the enabled section.', 'woocommerce-pdf-catalog' ),
        'id'         => 'customData',
        'subsection' => true,
        'fields'     => $woocommerce_pdf_catalog_options_meta_keys
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Extra Texts', 'woocommerce-pdf-catalog' ),
        'id'         => 'extra-texts',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableTextBeforeProducts',
                'type'     => 'switch',
                'title'    => __( 'Enable Text Before Products', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'     =>'textBeforeProducts',
                'type'  => 'editor',
                'title' => __('Text Before Products', 'woocommerce-pdf-catalog'), 
                'required' => array('enableTextBeforeProducts','equals','1'),
            ),
            array(
                'id'     =>'textBeforeProductsFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '11',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
                'required' => array('enableTextBeforeProducts','equals','1'),
            ),
            array(
                'id'     =>'textBeforeProductsLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '15',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableTextBeforeProducts','equals','1'),
            ),
            array(
                'id'     =>'textBeforeProductsTextAlign',
                'type'  => 'select',
                'title' => __('Text Align', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'left' => __('Left', 'woocommerce-pdf-catalog' ),
                    'center' => __('Center', 'woocommerce-pdf-catalog' ),
                    'right' => __('Right', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'center',
                'required' => array('enableTextBeforeProducts','equals','1'),
            ),
            array(
                'id'       => 'enableTextAfterProducts',
                'type'     => 'switch',
                'title'    => __( 'Enable Text After Products', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'     =>'textAfterProducts',
                'type'  => 'editor',
                'title' => __('Text After Products', 'woocommerce-pdf-catalog'), 
                'required' => array('enableTextAfterProducts','equals','1'),
            ),
            array(
                'id'     =>'textAfterProductsFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '11',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
                'required' => array('enableTextAfterProducts','equals','1'),
            ),
            array(
                'id'     =>'textAfterProductsLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '15',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableTextAfterProducts','equals','1'),
            ),
            array(
                'id'     =>'textAfterProductsTextAlign',
                'type'  => 'select',
                'title' => __('Text Align', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'left' => __('Left', 'woocommerce-pdf-catalog' ),
                    'center' => __('Center', 'woocommerce-pdf-catalog' ),
                    'right' => __('Right', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'center',
                'required' => array('enableTextAfterProducts','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Index', 'woocommerce-pdf-catalog' ),
        'id'         => 'index',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableIndex',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable Index at the end of page.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
            ),
            array(
                'id'     =>'indexKey',
                'type'  => 'select',
                'title' => __('Index Key', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'title' => __('Title', 'woocommerce-pdf-catalog' ),
                    'sku' => __('SKU', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'title',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'       => 'indexLetters',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Letter Dividing', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Defines whether to divide index entries starting with the same letter, using a (large) letter as a heading.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'       => 'indexLinking',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Linking', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Place links in the index.', 'woocommerce-pdf-catalog' ),
                'default' =>  '1',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'       => 'indexOnlyFull',
                'type'     => 'checkbox',
                'title'    => __( 'Full catalog only', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Only show for complete Catalog', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'     =>'indexColumns',
                'type'     => 'spinner', 
                'title'    => __('Index Columns', 'woocommerce-pdf-catalog'),
                'default'  => '1',
                'min'      => '1',
                'step'     => '1',
                'max'      => '10',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'     =>'indexTextBefore',
                'type'  => 'editor',
                'title' => __('Text Before index', 'woocommerce-pdf-catalog'), 
                'required' => array('enableIndex','equals','1'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => '<h1>Index</h1>',
            ),
            array(
                'id'     =>'indexTextAfter',
                'type'  => 'editor',
                'title' => __('Text After index', 'woocommerce-pdf-catalog'), 
                'required' => array('enableIndex','equals','1'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'             => 'indexPadding',
                'type'           => 'spacing',
                // 'output'         => array('.site-header'),
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Choose the spacing or padding you want.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '20px', 
                    'padding-right'   => '60px', 
                    'padding-bottom'  => '10px', 
                    'padding-left'    => '60px',
                    'units'          => 'px', 
                ),
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'     =>'indexFontFamily',
                'type'  => 'select',
                'title' => __('Default Font', 'woocommerce-pdf-catalog'), 
                'options'  => array(
                    'dejavusans' => __('Sans', 'woocommerce-pdf-catalog' ),
                    'dejavuserif' => __('Serif', 'woocommerce-pdf-catalog' ),
                    'dejavusansmono' => __('Mono', 'woocommerce-pdf-catalog' ),
                    'droidsans' => __('Droid Sans', 'woocommerce-pdf-catalog'),
                    'droidserif' => __('Droid Serif', 'woocommerce-pdf-catalog'),
                    'lato' => __('Lato', 'woocommerce-pdf-catalog'),
                    'lora' => __('Lora', 'woocommerce-pdf-catalog'),
                    'merriweather' => __('Merriweather', 'woocommerce-pdf-catalog'),
                    'montserrat' => __('Montserrat', 'woocommerce-pdf-catalog'),
                    'poppins' => __('Poppins', 'woocommerce-pdf-catalog'),
                    'mulish'    => __('Mulish', 'woocommerce-pdf-catalog'),
                    'opensans' => __('Open sans', 'woocommerce-pdf-catalog'),
                    'opensanscondensed' => __('Open Sans Condensed', 'woocommerce-pdf-catalog'),
                    'oswald' => __('Oswald', 'woocommerce-pdf-catalog'),
                    'ptsans' => __('PT Sans', 'woocommerce-pdf-catalog'),
                    'sourcesanspro' => __('Source Sans Pro', 'woocommerce-pdf-catalog'),
                    'slabo' => __('Slabo', 'woocommerce-pdf-catalog'),
                    'roboto' => __('Roboto', 'woocommerce-pdf-catalog'),
                    'raleway' => __('Raleway', 'woocommerce-pdf-catalog'),
                ),
                'default' => 'dejavusans',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'     =>'indexFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '10',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
                'required' => array('enableIndex','equals','1'),
            ),
            array(
                'id'     =>'indexLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '14',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableIndex','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Footer', 'woocommerce-pdf-catalog' ),
        // 'desc'       => __( '', 'woocommerce-pdf-catalog' ),
        'id'         => 'footer',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableFooter',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable footer', 'woocommerce-pdf-catalog' ),
            ),
            array(
                'id'     =>'footerBackgroundColor',
                'type' => 'color',
                'url'      => true,
                'title' => __('Footer background color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'required' => array('enableFooter','equals','1'),
                'default' => '#222222',
            ),
            array(
                'id'     =>'footerTextColor',
                'type'  => 'color',
                'url'      => true,
                'title' => __('Footer text color', 'woocommerce-pdf-catalog'), 
                'validate' => 'color',
                'required' => array('enableFooter','equals','1'),
                'default' => '#FFFFFF',
            ),
            array(
                'id'     =>'footerFontSize',
                'type'     => 'spinner', 
                'title'    => __('Text font size', 'woocommerce-pdf-catalog'),
                'default'  => '9',
                'min'      => '1',
                'step'     => '1',
                'max'      => '40',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerLineHeight',
                'type'     => 'spinner', 
                'title'    => __('Text line height', 'woocommerce-pdf-catalog'),
                'default'  => '12',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
                'required' => array('enableFooter','equals','1'),
            ),
            array(
                'id'     =>'footerLayout',
                'type'  => 'select',
                'title' => __('Footer Layout', 'woocommerce-pdf-catalog'), 
                'required' => array('enableFooter','equals','1'),
                'options'  => array(
                    'oneCol' => __('1/1', 'woocommerce-pdf-catalog' ),
                    'twoCols' => __('1/2 + 1/2', 'woocommerce-pdf-catalog' ),
                    'threeCols' => __('1/3 + 1/3 + 1/3', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'oneCol',
            ),
            array(
                'id'     =>'footerTopMargin',
                'type'     => 'spinner', 
                'title'    => __('Footer Margin', 'woocommerce-pdf-catalog'),
                'default'  => '10',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
            ),
            array(
                'id'     =>'footerHeight',
                'type'     => 'spinner', 
                'title'    => __('Footer Height', 'woocommerce-pdf-catalog'),
                'default'  => '20',
                'min'      => '1',
                'step'     => '1',
                'max'      => '200',
            ),
            array(
                'id'             => 'footerPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Footer Padding', 'woocommerce-pdf-catalog'),
                'subtitle'       => __('Choose the spacing or padding you want.', 'woocommerce-pdf-catalog'),
                'default'            => array(
                    'padding-top'     => '10px', 
                    'padding-right'   => '20px', 
                    'padding-bottom'  => '10px', 
                    'padding-left'    => '20px',
                    'units'          => 'px', 
                )
            ),
            array(
                'id'     =>'footerTopLeft',
                'type'  => 'select',
                'title' => __('Top Left Footer', 'woocommerce-pdf-catalog'), 
                'required' => array('enableFooter','equals','1'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-pdf-catalog' ),
                    'bloginfo' => __('Blog information', 'woocommerce-pdf-catalog' ),
                    'text' => __('Custom text', 'woocommerce-pdf-catalog' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-pdf-catalog' ),
                    'category' => __('Category', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                    'exportinfo' => __('Export Information', 'woocommerce-pdf-catalog' ),
                    'qr' => __('QR-Code', 'woocommerce-pdf-catalog' ),
                    'toc' => __('Back to Table of Contents', 'woocommerce-pdf-catalog' ),
                ),
                'default' => 'pagenumber',
            ),
            array(
                'id'     =>'footerTopLeftText',
                'type'  => 'editor',
                'title' => __('Top Left Footer Text', 'woocommerce-pdf-catalog'), 
                'required' => array('footerTopLeft','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopLeftImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Left Footer Image', 'woocommerce-pdf-catalog'), 
                'required' => array('footerTopLeft','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopMiddle',
                'type'  => 'select',
                'title' => __('Top Middle Footer', 'woocommerce-pdf-catalog'), 
                'required' => array('footerLayout','equals','threeCols'),
                'options'  => array(
                    'none' => __('None', 'woocommerce-pdf-catalog' ),
                    'bloginfo' => __('Blog information', 'woocommerce-pdf-catalog' ),
                    'text' => __('Custom text', 'woocommerce-pdf-catalog' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-pdf-catalog' ),
                    'category' => __('Category', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                    'exportinfo' => __('Export Information', 'woocommerce-pdf-catalog' ),
                    'qr' => __('QR-Code', 'woocommerce-pdf-catalog' ),
                    'toc' => __('Back to Table of Contents', 'woocommerce-pdf-catalog' ),
                ),
            ),
            array(
                'id'     =>'footerTopMiddleText',
                'type'  => 'editor',
                'title' => __('Top Middle Footer Text', 'woocommerce-pdf-catalog'), 
                'required' => array('footerTopMiddle','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopMiddleImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Middle Footer Image', 'woocommerce-pdf-catalog'), 
                'required' => array('footerTopMiddle','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopRight',
                'type'  => 'select',
                'title' => __('Top Right Footer', 'woocommerce-pdf-catalog'), 
                'required' => array('footerLayout','equals',array('threeCols','twoCols')),
                'options'  => array(
                    'none' => __('None', 'woocommerce-pdf-catalog' ),
                    'bloginfo' => __('Blog information', 'woocommerce-pdf-catalog' ),
                    'text' => __('Custom text', 'woocommerce-pdf-catalog' ),
                    'pagenumber' => __('Pagenumber', 'woocommerce-pdf-catalog' ),
                    'category' => __('Category', 'woocommerce-pdf-catalog' ),
                    'image' => __('Image', 'woocommerce-pdf-catalog' ),
                    'exportinfo' => __('Export Information', 'woocommerce-pdf-catalog' ),
                    'qr' => __('QR-Code', 'woocommerce-pdf-catalog' ),
                    'toc' => __('Back to Table of Contents', 'woocommerce-pdf-catalog' ),
                ),
            ),
            array(
                'id'     =>'footerTopRightText',
                'type'  => 'editor',
                'title' => __('Top Right Footer Text', 'woocommerce-pdf-catalog'), 
                'required' => array('footerTopRight','equals','text'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'     =>'footerTopRightImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Top Right Footer Image', 'woocommerce-pdf-catalog'), 
                'required' => array('footerTopRight','equals','image'),
                'args'   => array(
                    'teeny'            => false,
                )
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Backcover', 'woocommerce-pdf-catalog' ),
        'id'         => 'backcover',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableBackcover',
                'type'     => 'switch',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable the Backcover Page', 'woocommerce-pdf-catalog' ),
                'default' =>  '0',
            ),
            array(
                'id'     =>'backcoverImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Backcover image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Recommended Image Size: 1240 x 1754 px.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableBackcover','equals','1'),
            ),
            array(
                'id'     =>'saleBackcoverImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Sale Backcover image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Recommended Image Size: 1240 x 1754 px.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableBackcover','equals','1'),
            ),
            array(
                'id'     =>'wishlistBackcoverImage',
                'type' => 'media',
                'url'      => true,
                'title' => __('Wishlist Backcover image', 'woocommerce-pdf-catalog'), 
                'subtitle' => __('Recommended Image Size: 1240 x 1754 px.', 'woocommerce-pdf-catalog'), 
                'args'   => array(
                    'teeny'            => false,
                ),
                'required' => array('enableBackcover','equals','1'),
            ),
            array(
                'id'     =>'backcoverText',
                'type'  => 'editor',
                'title' => __('Text on Backcover', 'woocommerce-pdf-catalog'), 
                'required' => array('enableBackcover','equals','1'),
                'args'   => array(
                    'teeny'            => false,
                ),
                'default' => '',
            ),
        ),
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Limit Access', 'woocommerce-pdf-catalog' ),
        // 'desc'       => __( '', 'woocommerce-pdf-catalog' ),
        'id'         => 'limit-access-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableLimitAccess',
                'type'     => 'checkbox',
                'title'    => __( 'Enable', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable the limit access. This will activate the below settings.', 'woocommerce-pdf-catalog' ),
            ),
            array(
                'id'     =>'role',
                'type' => 'select',
                'data' => 'roles',
                'title' => __('User Role', 'woocommerce-pdf-catalog'),
                'subtitle' => __('Select a custom user Role (Default is: administrator) who can use this plugin.', 'woocommerce-pdf-catalog'),
                'multi' => true,
                'default' => 'administrator',
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Advanced settings', 'woocommerce-pdf-catalog' ),
        'desc'       => __( 'Custom stylesheet / javascript.', 'woocommerce-pdf-catalog' ),
        'id'         => 'advanced',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'debugMode',
                'type'     => 'switch',
                'title'    => __( 'Enable Debug Mode', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'This stops creating the PDF and shows the plain HTML.', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'debugMPDF',
                'type'     => 'switch',
                'title'    => __( 'Enable MPDF Debug Mode', 'woocommerce-print-products' ),
                'subtitle' => __( 'Show image , font or other errors in the PDF Rendering engine.', 'woocommerce-print-products' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'splitChunks',
                'type'     => 'switch',
                'title'    => __( 'Split HTML into Chunks', 'woocommerce-print-products' ),
                'subtitle' => __( 'This prevents the pcre.backtrack_limit issue, but also costs performance. Only enable if needed!', 'woocommerce-print-products' ),
                'default'   => 1,
            ),
            array(
                'id'       => 'curlFollowLocation',
                'type'     => 'switch',
                'title'    => __( 'CURL Follow Location', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable when having when images are not rendered / displayed as X.', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'curlAllowUnsafeSslRequests',
                'type'     => 'switch',
                'title'    => __( 'CURL AllowU nsafe SSL Requests', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Enable when having when images are not rendered / displayed as X.', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            array(
                'id'       => 'tableView',
                'type'     => 'switch',
                'title'    => __( 'DEPRECATED: Use Tables instead of DIVs', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'DIVs are better for custom styling, but DIVs float over pages sometimes. Below Verson 1.5.8 we used tables only.', 'woocommerce-pdf-catalog' ),
                'default'   => 0,
            ),
            

            array(
                'id'       => 'customCSS',
                'type'     => 'ace_editor',
                'mode'     => 'css',
                'title'    => __( 'Custom CSS', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Add some custom CSS styles for the PDF document here.', 'woocommerce-pdf-catalog' ),
            ),
            array(
                'id'       => 'customJS',
                'type'     => 'ace_editor',
                'mode'     => 'javascript',
                'title'    => __( 'Custom JS', 'woocommerce-pdf-catalog' ),
                'subtitle' => __( 'Add some javascript for the frontend. JS does not work in PDF files.', 'woocommerce-pdf-catalog' ),
            ),
        )
    ));

    $framework::setSection( $opt_name, array(
        'title'  => __( 'Preview', 'woocommerce-pdf-catalog' ),
        'id'     => 'preview',
        'desc'   => __( 'Need support? Please use the comment function on codecanyon.', 'woocommerce-pdf-catalog' ),
        'icon'   => 'el el-eye-open',
    ) );

    /*
     * <--- END SECTIONS
     */
