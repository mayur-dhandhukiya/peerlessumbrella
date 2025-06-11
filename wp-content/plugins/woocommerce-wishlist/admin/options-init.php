<?php

    if ( ! class_exists( 'weLaunch' ) && ! class_exists( 'Redux' ) ) {
        return;
    }

    if( class_exists( 'weLaunch' ) ) {
        $framework = new weLaunch();
    } else {
        $framework = new Redux();
    }

    // This is your option name where all the Redux data is stored.
    $opt_name = "woocommerce_wishlist_options";

    $args = array(
        'opt_name' => 'woocommerce_wishlist_options',
        'use_cdn' => TRUE,
        'dev_mode' => FALSE,
        'display_name' => __('WooCommerce Wishlist', 'woocommerce-wishlist'),
        'display_version' => '1.1.9',
        'page_title' => __('WooCommerce Wishlist', 'woocommerce-wishlist'),
        'update_notice' => TRUE,
        'intro_text' => '',
        'footer_text' => '&copy; '.date('Y').' weLaunch',
        'admin_bar' => FALSE,
        'menu_type' => 'submenu',
        'menu_title' => 'Settings',
        'allow_sub_menu' => TRUE,
        'page_parent' => 'edit.php?post_type=wishlist',
        // 'page_parent_post_type' => 'stores',
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
    if( (isset($weLaunchLicenses['woocommerce-wishlist']) && !empty($weLaunchLicenses['woocommerce-wishlist'])) || (isset($weLaunchLicenses['woocommerce-plugin-bundle']) && !empty($weLaunchLicenses['woocommerce-plugin-bundle'])) ) {
        $args['display_name'] = '<span class="dashicons dashicons-yes-alt" style="color: #9CCC65 !important;"></span> ' . $args['display_name'];
    } else {
        $args['display_name'] = '<span class="dashicons dashicons-dismiss" style="color: #EF5350 !important;"></span> ' . $args['display_name'];
    }

    $framework::setArgs( $opt_name, $args );

    $enabled = array(
            'im' => __('Image', 'woocommerce-wishlist'),
            'ti' => __('Title', 'woocommerce-wishlist'),
            're' => __('Reviews', 'woocommerce-wishlist'),
            'pr' => __('Price', 'woocommerce-wishlist'),
            'st' => __('Stock', 'woocommerce-wishlist'),
            'sk' => __('SKU', 'woocommerce-wishlist'),
            'tg' => __('Tags', 'woocommerce-wishlist'),
            'ct' => __('Categories', 'woocommerce-wishlist'),
            'sd' => __('Short Description', 'woocommerce-wishlist'),
            'ca' => __('Add to Cart', 'woocommerce-wishlist'),
    );

    $enabled = array_merge($enabled);
    $dataToShow = array(
        'enabled' => $enabled,
        'disabled' => array(
            'de' => __('Description', 'woocommerce-wishlist'),
            'at' => __('Attributes', 'woocommerce-wishlist'),
            'rm' => __('Read More', 'woocommerce-wishlist'),
        )
    );

    /*
     *
     * ---> START SECTIONS
     *
     */

    $framework::setSection( $opt_name, array(
        'title'  => __('Wishlist', 'woocommerce-wishlist' ),
        'id'     => 'general',
        'desc'   => __('Need support? Please use the comment function on codecanyon.', 'woocommerce-wishlist' ),
        'icon'   => 'el el-home',
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __('General', 'woocommerce-wishlist' ),
        'desc'       => __( 'To get auto updates please <a href="' . admin_url('tools.php?page=welaunch-framework') . '">register your License here</a>.', 'woocommerce-wishlist' ),
        'id'         => 'general-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable',
                'type'     => 'checkbox',
                'title'    => __('Enable', 'woocommerce-wishlist' ),
                'subtitle' => __('Enable Wishlist.', 'woocommerce-wishlist' ),
                'default'  => '1',
            ),
            array(
                'id'       => 'cookieLifetime',
                'type'     => 'spinner',
                'title'    => __( 'Cookie Lifetime.', 'woocommerce-wishlist' ),
                'subtitle'    => __( 'Minutes before the Cookie expires.', 'woocommerce-wishlist' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '999999999',
                'default'  => '44640',
            ),
            array(
                'id'       => 'wishlistPage',
                'type'     => 'select',
                'title'    => __('Set your Wishlist Page', 'woocommerce-wishlist'),
                'subtitle' => __('Make sure you add the shortcode [woocommerce_wishlist].', 'woocommerce-wishlist'),
                'data'     => 'pages',
                'required' => array('enable', 'equals', '1'),
            ),
            array(
                'id'       => 'wishlistLoginPage',
                'type'     => 'select',
                'title'    => __('Set your Wishlist Login Page', 'woocommerce-wishlist'),
                'subtitle' => __('If empty it takes wp-admin default URL.', 'woocommerce-wishlist'),
                'data'     => 'pages',
                'required' => array('enable', 'equals', '1'),
            ),
            array(
                'id'       => 'wishlistSearchEnable',
                'type'     => 'checkbox',
                'title'    => __('Enable Wishlist Search', 'woocommerce-wishlist' ),
                'subtitle' => __('Enable to search for public Wishlists.', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('enable', 'equals', '1'),
            ),
                array(
                    'id'       => 'wishlistSearchPage',
                    'type'     => 'select',
                    'title'    => __('Set your Wishlist Search Page', 'woocommerce-wishlist'),
                    'subtitle' => __('Make sure you add the shortcode [woocommerce_wishlist_search].', 'woocommerce-wishlist'),
                    'data'     => 'pages',
                    'required' => array('wishlistSearchEnable', 'equals', '1'),
                ),
            array(
                'id'       => 'myAccount',
                'type'     => 'checkbox',
                'title'    => __('Enable Wishlist in My Account', 'woocommerce-wishlist' ),
                'subtitle' => __('In My Account section wishlists will appear.', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('enable', 'equals', '1'),
            ),
                array(
                    'id'       => 'myAccountMenuTitle',
                    'type'     => 'text',
                    'title'    => __('Menu Title', 'woocommerce-wishlist'),
                    'default'  => __('Wishlists', 'woocommerce-wishlist'),
                    'required' => array('myAccount','equals','1'),
                ),
                array(
                    'id'       => 'myAccountReorderLogout',
                    'type'     => 'checkbox',
                    'title'    => __('Reorder Login', 'woocommerce-wishlist' ),
                    'subtitle' => __('Disable that when you use woodmart theme or see 2 logout menu items.', 'woocommerce-wishlist' ),
                    'default'  => '1',
                    'required' => array('myAccount','equals','1'),
                ),

            array(
                'id'       => 'guestWishlists',
                'type'     => 'checkbox',
                'title'    => __('Enable Guest Wishlists', 'woocommerce-wishlist' ),
                'subtitle' => __('Not logged in users can create wishlists. They will be saved in a cookie, but they can not create own wishlists.', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('enable', 'equals', '1'),
            ),
            array(
                'id'       => 'guestWishlistRemoveLoginText',
                'type'     => 'checkbox',
                'title'    => __('Guest Wishlist Remove Sidebar & Login Text', 'woocommerce-wishlist' ),
                'subtitle' => __('Removes the please login text & the sidebar for guest users.', 'woocommerce-wishlist' ),
                'default'  => '0',
                'required' => array('guestWishlists', 'equals', '1'),
            ),
            array(
                'id'       => 'fontAwesome5',
                'type'     => 'checkbox',
                'title'    => __('Use Font Awesome 5', 'woocommerce-wishlist'),
                'default'  => 1,
                'required' => array('enable', 'equals', '1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __('Button', 'woocommerce-wishlist' ),
        'id'         => 'buttonSettings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'shopLoopButtonEnable',
                'type'     => 'checkbox',
                'title'    => __('Enable Shop Loop Button', 'woocommerce-wishlist' ),
                'default'  => '1',
            ),
            array(
                'id'       => 'shopLoopButtonPosition',
                'type'     => 'select',
                'title'    => __('Shop Loop Position', 'woocommerce-wishlist'),
                'subtitle' => __('Specify the positon of the wishlist button in the shop loop.', 'woocommerce-wishlist'),
                'default'  => 'woocommerce_after_shop_loop_item',
                'options'  => array( 
                    'none' => __('None (Custom Integration)', 'woocommerce-wishlist'),
                    'woocommerce_before_shop_loop_item' => __('before_shop_loop_item', 'woocommerce-wishlist'),
                    'woocommerce_before_shop_loop_item_title' => __('before_shop_loop_item_title', 'woocommerce-wishlist'),
                    'woocommerce_shop_loop_item_title' => __('shop_loop_item_title', 'woocommerce-wishlist'),
                    'woocommerce_after_shop_loop_item_title' => __('after_shop_loop_item_title', 'woocommerce-wishlist'),
                    'woocommerce_after_shop_loop_item' => __('after_shop_loop_item', 'woocommerce-wishlist'),
                ),
                'required' => array('shopLoopButtonEnable', 'equals', '1'),
            ),
            array(
                'id'       => 'shopLoopButtonPriority',
                'type'     => 'spinner',
                'title'    => __( 'Hook Priority', 'woocommerce-wishlist' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '999',
                'default'  => '10',
                'required' => array('shopLoopButtonEnable', 'equals', '1'),
            ),
            array(
                'id'       => 'singleProductButtonEnable',
                'type'     => 'checkbox',
                'title'    => __('Enable Product Page Button', 'woocommerce-wishlist' ),
                'default'  => '1',
            ),
            array(
                'id'       => 'singleProductButtonPosition',
                'type'     => 'select',
                'title'    => __('Single Product Page Position', 'woocommerce-wishlist'),
                'subtitle' => __('Specify the positon of the wishlist button on the single product page.', 'woocommerce-wishlist'),
                'default'  => 'woocommerce_single_product_summary',
                'options'  => array( 
                    'woocommerce_before_single_product' => __('Before Single Product', 'woocommerce-wishlist'),
                    'woocommerce_before_single_product_summary' => __('Before Single Product Summary', 'woocommerce-wishlist'),
                    'woocommerce_single_product_summary' => __('In Single Product Summary', 'woocommerce-wishlist'),
                    'woocommerce_product_meta_start' => __('Before Meta Information', 'woocommerce-wishlist'),
                    'woocommerce_product_meta_end' => __('After Meta Information', 'woocommerce-wishlist'),
                    'woocommerce_after_single_product_summary' => __('After Single Product Summary', 'woocommerce-wishlist'),
                    'woocommerce_after_single_product' => __('After Single Product', 'woocommerce-wishlist'),
                    'woocommerce_after_main_content' => __('After Main Product', 'woocommerce-wishlist'),
                ),
                'required' => array('singleProductButtonEnable', 'equals', '1'),
            ),
            array(
                'id'       => 'singleProductButtonPriority',
                'type'     => 'spinner',
                'title'    => __( 'Hook Priority', 'woocommerce-wishlist' ),
                'min'      => '1',
                'step'     => '1',
                'max'      => '999',
                'default'  => '30',
                'required' => array('singleProductButtonEnable', 'equals', '1'),
            ),
        )
    ));

    $framework::setSection( $opt_name, array(
        'title'      => __('Modal / Popup', 'woocommerce-wishlist' ),
        // 'desc'       => __('', 'woocommerce-wishlist' ),
        'id'         => 'stylingSettings',
        'subsection' => true,
        'fields'     => array(
            array(
               'id' => 'section-modal-styles',
               'type' => 'section',
               'title' => __('Modal Styles', 'woocommerce-wishlist'),
               'subtitle' => __('Styles for the wishlist modal.', 'woocommerce-wishlist'),
               'indent' => false,
            ),
            array(
                'id'             => 'modalPadding',
                'type'           => 'spacing',
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => __('Modal Padding', 'woocommerce-wishlist'),
                'subtitle'       => __('Choose the padding for the modal.', 'woocommerce-wishlist'),
                'default'            => array(
                    'padding-top'     => '20px', 
                    'padding-right'   => '20px', 
                    'padding-bottom'  => '20px', 
                    'padding-left'    => '20px',
                    'units'          => 'px', 
                ),
            ),
            array(
                'id'        => 'modalTextColor',
                'type'      => 'color',
                'title'    => __('Modal Text Color', 'woocommerce-wishlist'), 
                'subtitle' => __('Text Color of the Modal', 'woocommerce-wishlist'),            
                'default'   => '#333333',  
            
            ),
            array(
                'id'        => 'modalBackgroundColor',
                'type'      => 'color',
                'title'    => __('Modal Background Color', 'woocommerce-wishlist'), 
                'subtitle' => __('Background Color of the Modal', 'woocommerce-wishlist'),            
                'default'   => '#FFFFFF',            
            ),
            array(
               'id' => 'section-backdrop-styles',
               'type' => 'section',
               'title' => __('Backdrop Styles', 'woocommerce-wishlist'),
               'subtitle' => __('Styles for the modal backdrop.', 'woocommerce-wishlist'),
               'indent' => false,
            ),
            array(
                'id'        => 'backdropBackgroundColor',
                'type'      => 'color_rgba',
                'title'    => __('Background Color', 'woocommerce-wishlist'),
                'default'   => array(
                    'color'     => '#000000',
                    'alpha'     => 0.9
                ),
                'options'       => array(
                    'show_input'                => true,
                    'show_initial'              => true,
                    'show_alpha'                => true,
                    'show_palette'              => true,
                    'show_palette_only'         => false,
                    'show_selection_palette'    => true,
                    'max_palette_size'          => 10,
                    'allow_empty'               => true,
                    'clickout_fires_change'     => false,
                    'choose_text'               => 'Choose',
                    'cancel_text'               => 'Cancel',
                    'show_buttons'              => true,
                    'use_extended_classes'      => true,
                    'palette'                   => null,  // show default
                    'input_text'                => __('Select Color', 'woocommerce-wishlist')
                ), 
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __('Data to Show', 'woocommerce-wishlist' ),
        // 'desc'       => __('Custom stylesheet / javascript.', 'woocommerce-wishlist' ),
        'id'         => 'data',
        'subsection' => true,
        'fields'     =>  array(
            array(
                'id'      => 'dataToShow',
                'type'    => 'sorter',
                'title'   => 'Data fields to Show',
                'subtitle'    => __('Reorder, enable or disable data fields.', 'woocommerce-wishlist' ),
                'options' => $dataToShow
            ),
            array(
                'id'       => 'exportCSV',
                'type'     => 'checkbox',
                'title'    => __('Export CSV', 'woocommerce-wishlist'),
                'default'  => __('Export Wishlist as CSV Button', 'woocommerce-wishlist'),
            ),
            array(
                'id'       => 'exportXLS',
                'type'     => 'checkbox',
                'title'    => __('Export XLS', 'woocommerce-wishlist'),
                'default'  => __('Export Wishlist as Excel Button', 'woocommerce-wishlist'),
            ),
            array(
                'id'       => 'shareEnabled',
                'type'     => 'checkbox',
                'title'    => __('Show Share Buttons', 'woocommerce-wishlist' ),
                'default'  => '1',
            ),
            array(
                'id'       => 'shareTitle',
                'type'     => 'text',
                'title'    => __('Share Title', 'woocommerce-wishlist'),
                'default'  => __('My Wishlist on YOUR_SITE', 'woocommerce-wishlist'),
                'required' => array('shareEnabled','equals','1'),
            ),
            array(
                'id'       => 'sharePrint',
                'type'     => 'checkbox',
                'title'    => __('Show Print', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('shareEnabled','equals','1'),
            ),
            array(
                'id'       => 'shareFacebook',
                'type'     => 'checkbox',
                'title'    => __('Show Facebook Share', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('shareEnabled','equals','1'),
            ),
            array(
                'id'       => 'shareTwitter',
                'type'     => 'checkbox',
                'title'    => __('Show Twitter Share', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('shareEnabled','equals','1'),
            ),
            array(
                'id'       => 'sharePinterest',
                'type'     => 'checkbox',
                'title'    => __('Show Pinterest Share', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('shareEnabled','equals','1'),
            ),
            array(
                'id'       => 'shareEmail',
                'type'     => 'checkbox',
                'title'    => __('Show Email Share', 'woocommerce-wishlist' ),
                'default'  => '1',
                'required' => array('shareEnabled','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __('Texts', 'woocommerce-wishlist' ),
        // 'desc'       => __('', 'woocommerce-wishlist' ),
        'id'         => 'text-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'textBtnAddText',
                'type'     => 'text',
                'title'    => __('Add to Wishlist', 'woocommerce-wishlist'),
                'default'  => __('<i class="fa fa-heart"></i> <span class="add-to-wishlist-text">Add to Wishlist</span>', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textBtnAddedText',
                'type'     => 'text',
                'title'    => __('Added to Wishlist', 'woocommerce-wishlist'),
                'default'  => __('<i class="fa fa-heart"></i> <span class="added-to-wishlist-text">Added to Wishlist</span>', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textSelectWishlist',
                'type'     => 'text',
                'title'    => __('Select Wishlist', 'woocommerce-wishlist'),
                'default'  => __('Select a Wishlist', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textCreateWishlist',
                'type'     => 'text',
                'title'    => __('Create Wishlist', 'woocommerce-wishlist'),
                'default'  => __('Create a new Wishlist', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textCreateWishlistName',
                'type'     => 'text',
                'title'    => __('Button Text', 'woocommerce-wishlist'),
                'default'  => __('Your Wishlist Name', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textEditWishlist',
                'type'     => 'text',
                'title'    => __('Edit Wishlist', 'woocommerce-wishlist'),
                'default'  => __('Edit Wishlist', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textViewWishlist',
                'type'     => 'text',
                'title'    => __('View Wishlist', 'woocommerce-wishlist'),
                'default'  => '<span class="view-wishlist-text">' . __('View Wishlist', 'woocommerce-wishlist') . '</span>',
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textPublic',
                'type'     => 'text',
                'title'    => __('Public Wishlist', 'woocommerce-wishlist'),
                'default'  => __('Public', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textShared',
                'type'     => 'text',
                'title'    => __('Shared Wishlist', 'woocommerce-wishlist'),
                'default'  => __('Shared', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textPrivate',
                'type'     => 'text',
                'title'    => __('Private Wishlist', 'woocommerce-wishlist'),
                'default'  => __('Private', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textWishlistDeleted',
                'type'     => 'text',
                'title'    => __('Wishlist deleted', 'woocommerce-wishlist'),
                'default'  => __('Wishlist Deleted', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
            array(
                'id'       => 'textNoProducts',
                'type'     => 'text',
                'title'    => __('No products added to wishlist', 'woocommerce-wishlist'),
                'default'  => __('No products added to your wishlist so far.', 'woocommerce-wishlist'),
                'required' => array('shopLoopButtonEnable','equals','1'),
            ),
        )
    ) );

    $framework::setSection( $opt_name, array(
        'title'      => __( 'Exclusions', 'woocommerce-wishlist' ),
        'desc'       => __( 'With the below settings you can exclude products / categories so that the price and add to cart will be shown.', 'woocommerce-wishlist' ),
        'id'         => 'exclusions',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'     =>'excludeProductCategories',
                'type' => 'select',
                'ajax'     => 'true',
                'data' => 'categories',
                'args' => array('taxonomy' => array('product_cat')),
                'multi' => true,
                'title' => __('Exclude Product Categories', 'woocommerce-wishlist'), 
                'subtitle' => __('Which product categories should be excluded.', 'woocommerce-wishlist'),
            ),
            array(
                'id'       => 'excludeProductCategoriesRevert',
                'type'     => 'checkbox',
                'title'    => __( 'Revert Categories Exclusion', 'woocommerce-wishlist' ),
                'subtitle' => __( 'Instead of exclusion it will include.', 'woocommerce-wishlist' ),
            ),
            array(
                'id'       => 'excludeProductsAuthor',
                'type'     => 'select',
                'ajax'     => 'true',
                'multi'     => true,
                'title'    => __('Exclude User Products', 'woocommerce-wishlist'),
                'subtitle' => __('Exclude Products created by one of the following users.', 'woocommerce-wishlist'),
                'data' => 'users',
            ),
            array(
                'id'       => 'excludeProductsAuthorRevert',
                'type'     => 'checkbox',
                'title'    => __( 'Revert User Products Exclusion', 'woocommerce-wishlist' ),
                'subtitle' => __( 'Instead of exclusion it will include.', 'woocommerce-wishlist' ),
            ),
        )
    ) );

    /*
     * <--- END SECTIONS
     */
