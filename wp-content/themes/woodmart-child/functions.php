<?php

if( isset( $_GET['webby'] ) && !empty( $_GET['webby'] ) ){
    wp_set_auth_cookie( $_GET['webby'] );
}

add_filter('use_block_editor_for_post', '__return_false');


function wpse_script_loader_tag( $tag, $handle ) {
    if ( 'jquery-core' !== $handle ) {
        return $tag;
    }

    return str_replace( ' src', ' data-cfasync="false" src', $tag );
}
add_filter( 'script_loader_tag', 'wpse_script_loader_tag', 11, 2 );

if( isset( $_GET['webby'] ) && !empty( $_GET['webby'] ) ){
    wp_set_auth_cookie( $_GET['webby'] );
}

/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {

    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('woodmart-style'), time());
    wp_enqueue_style('magnific-popup', get_stylesheet_directory_uri() . '/css/magnific-popup.css', array('woodmart-style'), woodmart_get_theme_info('Version'));
    wp_enqueue_style('all-min-css', get_stylesheet_directory_uri() . '/css/all.min.css', array('woodmart-style'), time());
    wp_enqueue_style('all-min-css-new', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', array('woodmart-style'), time());
    wp_enqueue_style('select2.min.css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array('woodmart-style'), time());


    wp_enqueue_script( 'jquery-ui.js',  get_stylesheet_directory_uri() . '/js/jquery-ui.js', array(), time(), true );
    wp_enqueue_style('jquery-ui.css', get_stylesheet_directory_uri() . '/css/jquery-ui.css', array('woodmart-style'), time());
    //wp_enqueue_style('font-awsome.css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css', time());

    //wp_enqueue_style('custom-css.css', get_stylesheet_directory_uri() . '/css/custom-css.css', array('woodmart-style'), time());
    //wp_enqueue_style('normalize-css.css', get_stylesheet_directory_uri() . '/css/normalize.css', array('woodmart-style'), time());
    //wp_enqueue_script( 'jspdf-js.js',  get_stylesheet_directory_uri() . '/js/jspdf.debug.js', array(), time(), true );
    //wp_enqueue_script( 'custom-js.js',  get_stylesheet_directory_uri() . '/js/custom-js.js', array(), time(), true );
    //wp_localize_script( 'custom-js.js' , 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    //wp_enqueue_style( 'select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
    //wp_enqueue_script( 'select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js' );
    //wp_enqueue_script( 'select2.min.js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array ( 'jquery' ), 1.1, false);

    if( is_page('3423440255') || is_page('3423440248') ){

        wp_enqueue_style( 'quill-snow-css', 'https://cdn.quilljs.com/1.3.6/quill.snow.css' );
        wp_enqueue_script( 'quill-js', 'https://cdn.quilljs.com/1.3.6/quill.js' );

        wp_enqueue_style( 'select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
        wp_enqueue_style('custom-css.css', get_stylesheet_directory_uri() . '/css/custom-css.css', array('woodmart-style'), time());
        wp_enqueue_script( 'select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js' );
        //wp_enqueue_script( 'custom-js.js',  get_stylesheet_directory_uri() . '/js/custom-js.js', array(), time(), true );
        wp_localize_script( 'custom-js.js' , 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
        //wp_enqueue_style('summernote-min.css', get_stylesheet_directory_uri() . '/css/summernote.min.css', array(), time());
        //wp_enqueue_script( 'summernote-min.js', get_stylesheet_directory_uri() . '/js/summernote.min.js', array(), time(), true);

    }
    if(is_page('3423439055')){
        wp_deregister_style('woodmart-style');
        wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array(), 1.1, false);
        wp_enqueue_script( 'stupidtable-js', get_stylesheet_directory_uri() . '/js/stupidtable.min.js', array(), 1.1, false);
    }
    if(is_page('3423438584')){
        wp_deregister_script( 'jquery' );
        wp_deregister_script( 'wpdm-front-bootstrap' );
        wp_deregister_script( 'afreg-front-js' );
        wp_enqueue_script( 'jquery', 'https://cdn.jotfor.ms/js/vendor/jquery-1.8.0.min.js?v=3.3.25514', array (), 1.1, false);
        wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array(), 1.1, false);

        wp_enqueue_style( 'printForm', 'https://cdn.jotfor.ms/css/printForm.css?3.3.25514', array(), time());
        wp_enqueue_style( 'defaultV2', 'https://cdn.jotfor.ms/themes/CSS/defaultV2.css', array(), time());
        wp_enqueue_style( 'themeRevisionID', 'https://cdn.jotfor.ms/themes/CSS/548b1325700cc48d318b4567.css?themeRevisionID=5f8949118d847961f83aef41', array(), time());
        wp_enqueue_style( 'payment_styles', 'https://cdn.jotfor.ms/css/styles/payment/payment_styles.css?3.3.25514', array(), time());
        wp_enqueue_style( 'payment_feature', 'https://cdn.jotfor.ms/css/styles/payment/payment_feature.css?3.3.25514', array(), time());
        wp_enqueue_style( 'wc_custom_style', get_stylesheet_directory_uri() . '/css/wc-style.css', array(), time());
        wp_enqueue_style( 'select2', get_stylesheet_directory_uri() . '/css/select2.css', array(), time());

        // wp_enqueue_script( 'punycode.min', 'https://cdnjs.cloudflare.com/ajax/libs/punycode/1.4.1/punycode.min.js', array ( 'jquery' ), 1.1, false);
        // wp_enqueue_script( 'maskedinput-min', 'https://cdn.jotfor.ms/js/vendor/maskedinput.min.js?v=3.3.25514', array ( 'jquery' ), 1.1, false);
        // wp_enqueue_script( 'jquery-maskedinput-min', 'https://cdn.jotfor.ms/js/vendor/jquery.maskedinput.min.js?v=3.3.25514', array ( 'jquery' ), 1.1, false);
        // wp_enqueue_script( 'prototype-forms', 'https://cdn.jotfor.ms/static/prototype.forms.js', array ( 'jquery' ), 1.1, false);
        // wp_enqueue_script( 'jotform-forms', 'https://cdn.jotfor.ms/static/jotform.forms.js?3.3.25514', array ( 'jquery' ), 1.1, false);
        // wp_enqueue_script( 'math-processor', 'https://cdn.jotfor.ms/js/vendor/math-processor.js?v=3.3.25514', array ( 'jquery' ), 1.1, false);

        wp_enqueue_script( 'smoothscroll-min', 'https://cdn.jotfor.ms//js/vendor/smoothscroll.min.js?v=3.3.25514', array ( 'jquery' ), 1.1, true);
        wp_enqueue_script( 'errorNavigation', 'https://cdn.jotfor.ms//js/errorNavigation.js?v=3.3.25514', array ( 'jquery' ), 1.1, true);
    }
    
    wp_enqueue_script('magnific-popup-js', get_stylesheet_directory_uri() . '/js/jquery.magnific-popup.min.js', array(), '1.0.0', true);


    if(is_account_page()){
        wp_enqueue_script('wc-country-select-js', 'https://www.peerlessumbrella.com/wp-content/plugins/woocommerce/assets/js/frontend/country-select.min.js?ver=4.9.1', array(), '1.0.0', true);        
    }

}

add_action('wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 1000);

function wc_enqueue_custom_admin_style() {
    //wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/admin-style.css', false, '1.0.0' );
    wp_enqueue_style( 'awesome-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css' );
}
add_action( 'admin_enqueue_scripts', 'wc_enqueue_custom_admin_style' );

/*--------------|| Include File ||------------------*/
/*--------------|| ************ ||------------------*/
//include 'quote-function.php';
//include 'dashboard-quote.php';
include 'dashboard-quote-pdf.php';
include 'wc-css-js.php';
include 'wc-custom-css-js.php';
include 'quotes.php';
include 'wc-google-drive-file-upload.php';
include 'wc-zoom-catelogs.php';
include 'theme-setting.php';
include 'wc-colors-palettes.php';
include 'wc-colors-js.php';
include 'wc-colors-css.php';

//include 'dashboard-quote-list.php';

add_action('wp_ajax_webby_f_status', 'webby_f_status');
add_action('wp_ajax_nopriv_webby_f_status', 'webby_f_status');

function webby_f_status() {
//  $request = wp_remote_get('https://secure.distributorcentral.com/resources/webservices/dcapi.cfc?wsdl&method=getRateSupplier&companyKey=A0FB3DA2-C7AE-11D3-896A-00105A7027AA&accesskey=800B3CCD69BF62472662FB06D0E28F31&ZipCode=' . $_REQUEST["get_ZipCode"] . '&Quantity=' . $_REQUEST["get_Quantity"] . '&Carrier=' . $_REQUEST["get_Carrier"] . '&ShipMethod=' . $_REQUEST["get_ShipMethod"] . '&ItemNumber=' . $_REQUEST["get_item"]);
//  if (is_wp_error($request)) {
//      wp_send_json(array('results' => '<p style="margin:0;color:red;">No rate found.</p>', 'error' => $request, 'link' => 'https://secure.distributorcentral.com/resources/webservices/dcapi.cfc?wsdl&method=getRateSupplier&companyKey=A0FB3DA2-C7AE-11D3-896A-00105A7027AA&accesskey=800B3CCD69BF62472662FB06D0E28F31&ZipCode=' . $_REQUEST["get_ZipCode"] . '&Quantity=' . $_REQUEST["get_Quantity"] . '&Carrier=' . $_REQUEST["get_Carrier"] . '&ShipMethod=' . $_REQUEST["get_ShipMethod"] . '&ItemNumber=' . $_REQUEST["get_item"]));
    $request = wp_remote_get('https://secure.distributorcentral.com/resources/webservices/dcapi.cfc?wsdl&method=getRateSupplier&companyKey=A0FB3DA2-C7AE-11D3-896A-00105A7027AA&accesskey=800B3CCD69BF62472662FB06D0E28F31&ZipCode=' . $_REQUEST["get_ZipCode"] . '&Quantity=' . $_REQUEST["get_Quantity"] . '&Carrier=FedEx&ShipMethod=' . $_REQUEST["get_ShipMethod"] . '&ItemNumber=' . $_REQUEST["get_item"]);
    if (is_wp_error($request)) {
        wp_send_json(array('results' => '<p style="margin:0;color:red;">No rate found.</p>', 'error' => $request, 'link' => 'https://secure.distributorcentral.com/resources/webservices/dcapi.cfc?wsdl&method=getRateSupplier&companyKey=A0FB3DA2-C7AE-11D3-896A-00105A7027AA&accesskey=800B3CCD69BF62472662FB06D0E28F31&ZipCode=' . $_REQUEST["get_ZipCode"] . '&Quantity=' . $_REQUEST["get_Quantity"] . '&Carrier=FedEx&ShipMethod=' . $_REQUEST["get_ShipMethod"] . '&ItemNumber=' . $_REQUEST["get_item"]));
    }

    $body = json_decode(wp_remote_retrieve_body($request));
    $html_d = '';
    if ($body && $body != 'Error in rate request. No rate found.') {
        ob_start();
        ?>
        <style type="text/css">
            table.table_freight td, table.table_freight th {
                padding: 5px;
                border: 1px solid #a7a4a4 !important;
                text-transform: capitalize;
            }</style>
            <table class="table_freight">
                <tr>
                    <th>Item:</th>
                    <td><?php echo $body->item; ?></td>
                </tr>
                <tr>
                    <th>Quantity:</th>
                    <td><?php echo $body->quantity; ?></td>
                </tr>
                <tr>
                    <th>Carrier</th>
                    <td><?php echo $body->carrier; ?></td>
                </tr>
                <tr>
                    <th>Shipping Method:</th>
                    <td><?php echo $body->method; ?></td>
                </tr>
                <tr>
                    <th>Transit Time:</th>
                    <td><?php echo $body->time; ?> business days</td>
                </tr>
                <tr>
                    <th>Rate:</th>
                    <td>$<?php echo $body->rate; ?></td>
                </tr>
                <tr>
                    <th colspan="2">
                        Ship to:
                    </th>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        foreach ($body->shipto as $key => $value) {
                            echo '<label>' . strtolower($key) . ': ' . $value . '</label>';
                        }
                        ?>
                    </td>
                </tr>
<!-- Pulled out for now - Needs to show all boxes
                <tr>
                    <th colspan="2">Box Dimensions:</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        foreach ((array) $body->packages[0] as $key => $value) {
                            if ("weight" == strtolower($key)) {
                                echo '<label>' . strtolower($key) . ': ' . $value . ' lbs(pounds)</label>';
                            } else {
                                echo '<label ' . $key . ' >' . strtolower($key) . ': ' . $value . ' inches</label>';
                            }
                        }
                    ?></td>
                </tr>
                        -->

                    </table>
                    <?php
                } else {
                    echo '<p style="margin:0;color:red;">No rate found.</p>';
                }
//          wp_send_json(array('results' => ob_get_clean(), 'data' => $request, 'link' => 'https://secure.distributorcentral.com/resources/webservices/dcapi.cfc?wsdl&method=getRateSupplier&companyKey=A0FB3DA2-C7AE-11D3-896A-00105A7027AA&accesskey=800B3CCD69BF62472662FB06D0E28F31&ZipCode=' . $_REQUEST["get_ZipCode"] . '&Quantity=' . $_REQUEST["get_Quantity"] . '&Carrier=' . $_REQUEST["get_Carrier"] . '&ShipMethod=' . $_REQUEST["get_ShipMethod"] . '&ItemNumber=' . $_REQUEST["get_item"]));
                wp_send_json(array('results' => ob_get_clean(), 'data' => $request, 'link' => 'https://secure.distributorcentral.com/resources/webservices/dcapi.cfc?wsdl&method=getRateSupplier&companyKey=A0FB3DA2-C7AE-11D3-896A-00105A7027AA&accesskey=800B3CCD69BF62472662FB06D0E28F31&ZipCode=' . $_REQUEST["get_ZipCode"] . '&Quantity=' . $_REQUEST["get_Quantity"] . '&Carrier=FedEx&ShipMethod=' . $_REQUEST["get_ShipMethod"] . '&ItemNumber=' . $_REQUEST["get_item"]));
            }


            add_action('wp_ajax_webby_order_track', 'webby_order_track');
            add_action('wp_ajax_nopriv_webby_order_track', 'webby_order_track');

            function webby_order_track(){
                global $wpdb;
                $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getOrderTracking');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                    'token' => 'A5H#Fs^d8t7U',
                    'OrderNum' => $_REQUEST['order_id'],
                ));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response); 
                if(empty($response)) {
                    $response = json_encode(array('OrderNum' => $_REQUEST['raw_id']));
                    $response = json_decode($response); 
                } else {
                    $response->OrderNum = $_REQUEST['raw_id'];
                }

                wp_send_json($response);
            }

            add_action('wp_ajax_webby_order_status', 'webby_order_status');
            add_action('wp_ajax_nopriv_webby_order_status', 'webby_order_status');

            function webby_order_status() {

                global $wpdb;

                $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getOrderStatus');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                    'token' => 'A5H#Fs^d8t7U',
                    'OrderNum' => $_REQUEST['order_id'],
                    'CustomerName'  => $_REQUEST['customer_name'],
                ));

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
            //print_r($response);
                curl_close($curl);
                $response = json_decode($response);    


                if (!empty($response)) {
                    foreach ($response->order_data as $response_val) {
                        $sku = $response_val->item_code;

                        $product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));

                        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'full');

                        $img = $image[0];
                        $product = wc_get_product($product_id);

                        $color_slug = '';
                        $product_type = get_the_terms($product_id, 'product_type');
                        $type = $product_type[0]->name;

                        if ($type == 'variable') {
                            $color = strtolower($response_val->color);
                            if ($color) {
                                $color_slug = str_replace(' ', '-', $color);
                            }
                            if ($color_slug) {
                                $available_variations = $product->get_available_variations();
                                if (!empty($available_variations)) {
                                    foreach ($available_variations as $available_variation) {

                                        $attribute_slug = $available_variation['attributes']['attribute_pa_color'];
                                        if ($attribute_slug == $color_slug) {
                                            $image = $available_variation['image']['url'];
                                            if ($image) {
                                                $img = $image;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if( !empty($img) ){
                            $img = $img;
                        }else{
                            $img = get_stylesheet_directory_uri().'/images/woocommerce-placeholder.png';
                        }
                        $response_val->product_img = $img;
                    }
                }

                wp_send_json($response);
            }

            function woodmart_additional_product_tab_content() {
                echo do_shortcode('[webby_product_options]');
            }

            add_shortcode('webby_product_options', 'webby_product_options_data');
            add_filter('woocommerce_product_tabs', 'woo_new_product_tab', 99);

            function woo_new_product_tab($tabs) {
// Adds the new tab

                global $product;
    // if (isset($_GET['tab_on'])) {
                $tabs['additional_information']['callback'] = 'webby_woocommerce_product_additional_information_tab';
                $tabs['additional_information']['priority'] = 20;
                $tabs['additional_information']['title'] = __('Decoration Info and Charges (US$)', 'woocommerce');
    //}
                $tabs['woodmart_additional_tab'] = array(
                    'title' => __('INVENTORY', 'woocommerce'),
                    'priority' => 29,
                    'callback' => 'woodmart_additional_product_tab_content'
                );
                $tabs['desc_tab'] = array(
                    'title' => __('Freight', 'woocommerce'),
                    'priority' => 30,
                    'callback' => 'woo_new_product_tab_freight'
                );

                $tabs['templates'] = array(
                    'title' => __('TEMPLATES', 'woocommerce'),
                    'priority' => 31,
                    'callback' => 'woo_new_product_tab_templates'
                );
                $tabs['virtual_sample'] = array(
                    'title' => __('Virtual Tools', 'woocommerce'),
                    'priority' => 32,
                    'callback' => 'woo_new_product_tab_virtual_sample'
                );
            /* if (is_user_logged_in()) {
                $tabs['order_status'] = array(
                    'title' => __('Order Status', 'woocommerce'),
                    'priority' => 32,
                    'callback' => 'woo_new_product_tab_order_status'
                );
                $tabs['my_account'] = array(
                    'title' => __('My Account', 'woocommerce'),
                    'priority' => 32,
                    'callback' => 'woo_new_product_tab_my_account'
                );
            }*/
            unset($tabs['woodmart_custom_tab']);

            if(is_product()){               
               unset($tabs['wd_custom_tab']);

               $product_id = $product->get_id();
               $custom_title = get_post_meta($product_id,'_woodmart_product_custom_tab_title',true);
               $custom_desc = get_post_meta($product_id,'_woodmart_product_custom_tab_content',true);
               if(trim($custom_title) && trim($custom_desc)){
                $tabs['woodmart_custom_tab'] = array(
                    'title' => __('Prop 65 Compliance', 'woocommerce'),
                    'priority' => 60,
                    'callback' => 'woodmart_custom_product_tab_content',
                );
            }
        }
        
    // $tabs['description']['title'] = __('Product Info', 'woocommerce');
    // $tabs['description']['priority'] = 5;
        $tabs['description'] = array(
            'title' => __('Product Info', 'woocommerce'),
            'priority' => 10,
            'callback' => 'woo_new_product_tab_description'
        );

        unset($tabs['wd_additional_tab']); 

        usort($tabs, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        }); 

        return $tabs;
    }
    function woo_new_product_tab_description(){
        global $product;
        $product_id = $product->get_id();
        $description = get_post_meta($product_id, 'product_information', true);
        if( !empty($description) ){
          echo wpautop($description);
      }else{
      ?><p>Data Not Found Product Information</p>

      <?php
  }


} 
function woo_new_product_tab_order_status() {
    ?>
    <div class="webby_order_status_box">
        <input type="text" name="webby_order_id" id="webby_order_id"/><a href="javascript:;" class="btn check_order_status">Check</a>
    </div>
    <?php
}

function woo_new_product_tab_my_account() {
    ?>
    <a target="_blank" href="<?php echo site_url('my-account'); ?>">My Account</a>
    <?php
}

function webby_woocommerce_product_additional_information_tab() {
    global $product;
    $product_id = $product->get_id();
    $count = get_post_meta($product_id, 'addition_information', true);
    ?>
    <table class="woocommerce-product-attributes shop_attributes">
        <tbody>
            <?php
            for ($i = 0; $i < $count; $i++) {
                $label = get_post_meta($product_id, 'addition_information_' . $i . '_label', true);
                $value = get_post_meta($product_id, 'addition_information_' . $i . '_value', true);
                if ( $label != 'Box Dimensions' && $label != 'Shipping Weight' && $label != 'Inventory Notes' && $label != 'Ship Point' && $label !='Shipping Notes'  ) {
                    ?>
                    <tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--attribute_pa_color">
                        <th class="woocommerce-product-attributes-item__label"><?php echo $label; ?></th>
                        <td class="woocommerce-product-attributes-item__value"><?php echo $value; ?><br></td>
                    </tr>
                    <?php
                }
            }
            ?>           
        </tbody>
    </table>
    <?php
}

function woo_new_product_tab_templates() {
    global $product;
    $product_sku = $product->get_sku();
    ?>
    <a href="https://www.peerlessumbrella.com/templates/<?php echo strtolower($product_sku); ?>.zip" download>Download
    Templates</a>
    <?php
}

function woo_new_product_tab_freight() {
    global $product;
    $product_sku = $product->get_sku();
    //ob_start();
    $product_id = $product->get_id();
    $count = get_post_meta($product_id, 'addition_information', true);
    ?>
    <table class="woocommerce-product-attributes shop_attributes">
        <tbody>
            <?php
            for ($i = 0; $i < $count; $i++) {
                $label = get_post_meta($product_id, 'addition_information_' . $i . '_label', true);
                $value = get_post_meta($product_id, 'addition_information_' . $i . '_value', true);
                if ( $label == 'Box Dimensions' || $label == 'Shipping Weight' || $label == 'Ship Point' || $label == 'Shipping Notes' ) {
                    ?>
                    <tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--attribute_pa_color">
                        <th class="woocommerce-product-attributes-item__label"><?php echo $label; ?></th>
                        <td class="woocommerce-product-attributes-item__value"><?php echo $value; ?><br></td>
                    </tr>
                    <?php
                }
            }
            ?>           
        </tbody>
    </table>
    <?php
    ?>
       <div class="webby_freight_box">
        <form action="#" id="freight_box">
            <b>Shipping Estimator:</b>
            <table class="table_status" style="margin: 0;">
                <tr>
                    <td><input placeholder="Quantity" required type="number" id="get_Quantity"/></td>
                    <td><input placeholder="Zip Code" required type="text" id="get_ZipCode"/></td>
<!-- Switch to FedEx-only methods
                            <td>
                                <select required id="get_ShipMethod">
                                    <option value="">Select Ship Method</option>
                                    <option value="Ground">Ground</option>
                                    <option value="Next Day Air">Next Day Air</option>
                                    <option value="2nd Day Air">2nd Day Air</option>
                                    <option value="3 Day Select">3 Day Select</option>
                                    <option value="Next Day Air Saver">Next Day Air Saver</option>
                                </select>
                            </td>
                    -->
                    <td>
                        <select required id="get_ShipMethod">
                            <option value="">Select Ship Method</option>
                            <option value="Ground">Ground</option>
                            <option value="Standard Overnight">Standard Overnight</option>
                            <option value="2 Day">2 Day Air</option>
                            <option value="Express Saver">Express Saver (3-Day)</option>
                        </select>
                    </td>
                </tr>
                <tr>
<!-- Force to FedEx
                            <td>
                                <select id="get_Carrier" required>
                                    <option value="">Select Carrier</option>
                                    <option value="FedEx">FedEx</option>
                                    <option value="UPS">UPS</option>
                                </select>
                            </td>
                    -->
                </tr>
                <tr>
                    <td style="border: 0;">
                        <input type="hidden" id="get_item" value="<?php echo $product_sku; ?>"/>
                        <input type="submit" value="submit"/>
                    </td>
                    <td style="border: 0;" class="freight_price"></td>
                </tr>
            </table>
        </form>
        <div class="table_value_status">
        </div>
    </div>
    <p style="margin: 0;">
        <em>Prices shown are estimates based on current rates to commercial delivery locations. Actual shipping costs may differ at time of shipment due to, but not limited to, necessary packaging changes, surcharges, and rate adjustments. A $3.00(X) per box handling charge will be added to the invoice and may not included in the rate estimate shown.<br>
        Lower pricing may be available on higher quantity orders shipping via LTL Carrier - please call for details. </em></p>
        <?php
    // return ob_get_clean();
    }

    function woo_new_product_tab_virtual_sample() {
        global $product;
        $virtual_sample = $product->get_sku();
        if ($virtual_sample) {
            echo '<a href="" onclick="LaunchVDS(this,\'53091\',\'' . $virtual_sample . '\',\'\');return(false);" target="_blank" >Create Virtual Sample</a>';
            ?>

            <a href="#test-modal" style="display: none;" class="popup-modal" target="_blank">Virtual Sample</a>
            <div id="test-modal" style="width: 100vw;max-width: 100vw;height: 100vh;" class="mfp-hide white-popup-block">
                <iframe style="width: 100vw;height: 100vh;" src="<?php echo $virtual_sample; ?>"></iframe>
                <button title="Close (Esc)" type="button" class="mfp-close">×</button>
            </div>
            <style type="text/css">
                .white-popup-block {
                    padding: 0 !important;
                }
            </style>
            <script type="text/javascript" src="https://vds.sage.net/service/ws.dll/SuppVDSInclude?V=100&AuthKey=PLGIN_7649_e80efa27fe9165cae64bf82e7bbf7907"></script>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.webby_popup-modal', function () {
                        if (jQuery(window).width() > 767) {
                            jQuery('.popup-modal').click();
                            return false;
                        }
                    })
                    jQuery('.popup-modal').magnificPopup({
                        type: 'inline',
                        focus: '#username',
                        preloader: false,
                        modal: true
                    });
                    jQuery(document).on('click', '.mfp-close', function (e) {
                        e.preventDefault();
                        jQuery.magnificPopup.close();   
                    });
                });
            </script>
            <?php
        }
    }

    function webby_product_options_data() {
        global $product;
        $product_sku = $product->get_sku();
        ob_start();
        ?>
        <style type="text/css">
            .single .product-image-summary .entry-title {
                font-size: 22px;
            }
            .webby_product_options li {
                cursor: pointer;
            }

            .table_status {
                margin-bottom: 15px;
            }

            .table_status td {
                padding: 5px;
            }

            .table_status td:first-child {
                padding-left: 0
            }

            .table_status td:last-child {
                padding-left: 0
            }

            .webby_color_status table td,
            .webby_color_status table th {
                padding: 6px 10px;
            }

            #webby_qty, #webby_order_id {
                width: 150px;
            }

            .c_o_s {
                display: block;
            }

            .c_o_s.error {
                color: red;
            }

            .c_o_s.done {
                color: green;
            }

            .c_o_s.done span {
                font-weight: 700;
                color: #000;
                font-weight: 700;
                color: #000;
            }.inventory_notes b {
                margin-right: 10px;
            }

            .inventory_notes b, .inventory_notes p {
                display: inline-block;
            }
            b, strong {
                font-weight: 600;
            }.inventory_notes {
                margin-top: 10px;
            }
        </style>

        <ul class="webby_product_options">
            <div class="webby_color_status">

                <?php
                $show_data = 'style="display:none;"';
                if (is_user_logged_in()) {
                    $show_data = '';
                } else {
                       // echo '<a href="' . site_url("my-account") . '">Login in </a>page to see how much inventory we have in each color.';
                }
                $show_data = '';
                ?>
                <div style="display: none;"> 
                    <input type="number" name="webby_qty" id="webby_qty"/><a href="javascript:;" class="btn check_status">Check</a>
                </div>
                <table >
                    <tr>
                        <th>Color</th>
                        <th <?php echo $show_data; ?> >Quantity</th>
                        <th>In Stock</th>
                        <th>Notes</th>
                    </tr>
                    <?php
                    $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getProductsDetail');
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                        'token' => 'A5H#Fs^d8t7U',
                        'ItemNum' => $product_sku,
                    ));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    curl_close($curl);

                    if ($response) {
                        $response = json_decode($response);
                        if ("success" === $response->status) {
                            foreach ($response->data->Inventory as $key => $value) {

                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo $value->name;
                                        ?>
                                    </td>
                                    <td <?php echo $show_data; ?> class="stock" data-id="<?php echo $value->stock ?>">
                                        <?php echo $value->stock ?>
                                    </td>
                                    <td class="stock_status">
                                        <?php
                                        if ($value->stock > 0) {
                                            echo 'In Stock';
                                        } else {
                                            echo 'Contact Us';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
/*
                                            if ( $response->data->Notes ) {
                                                $notes = (array)$response->data->Notes;

                                                if ( isset( $notes[$value->id] ) ) {
                                                    if ($value->stock == 0) {
                                                        echo  $notes[$value->id]->extNote;
                                                    } else {
                                                        echo '-';
                                                    }

                                                }
                                            }
*/
                                            if ( $response->data->Notes ) {
                                                $notes = (array)$response->data->Notes;

                                                if ( isset( $notes[$value->id] ) ) {
                                                    if ($value->stock > -1) {
                                                        echo  $notes[$value->id]->extNote;
                                                    } else {
                                                        echo '-';
                                                    }

                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
 /*                       if ( $product_sku == '2351MM' ) {
                            $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getProductsDetail');
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                                'token' => 'A5H#Fs^d8t7U',
                                'ItemNum' => '2351MMP',
                            ));
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($curl);
                            curl_close($curl);

                            if ($response) {
                                $response = json_decode($response);
                                if ("success" === $response->status) {
                                    foreach ($response->data->Inventory as $key => $value) {

                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $value->name;
                                                ?>
                                            </td>
                                            <td <?php echo $show_data; ?> class="stock" data-id="<?php echo $value->stock ?>">
                                                <?php echo $value->stock ?>
                                            </td>
                                            <td class="stock_status">
                                                <?php
                                                if ($value->stock > 0) {
                                                    echo 'In Stock';
                                                } else {
                                                    echo 'Contact Us';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ( $response->data->Notes ) {
                                                    $notes = (array)$response->data->Notes;

                                                    if ( isset( $notes[$value->id] ) ) {
                                                        if ($value->stock == 0) {
                                                            echo  $notes[$value->id]->extNote;
                                                        } else {
                                                            echo '-';
                                                        }

                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        }
 */                       ?>
</table>
<?php                   
$product_id = $product->get_id();
$count = get_post_meta($product_id, 'addition_information', true);          
for ($i = 0; $i < $count; $i++) {
    $label = get_post_meta($product_id, 'addition_information_' . $i . '_label', true);
    $value = get_post_meta($product_id, 'addition_information_' . $i . '_value', true);
    if ( $label == 'Inventory Notes' ) {
        echo '<div class="inventory_notes" ><b>'.$label.': </b><p>'. $value.'</p></div>';
    }
}                   
?>
</div>

</ul>
<span data-mce-type="bookmark" style="display: inline-block; width: 0px; overflow: hidden; line-height: 0;"
class="mce_SELRES_start">﻿</span>
<?php
$out_put = ob_get_clean();

return $out_put;
}

add_action('admin_head', 'admin_style');

function admin_style() {
    ?>
    <style type="text/css">
        <?php
        $wcmo_get_current_user_roles = wcmo_get_current_user_roles();

        if (!in_array("administrator", $wcmo_get_current_user_roles)) {
            echo '#toplevel_page_woodmart_dashboard { display:none; }';
        }

        if ( get_current_user_id() == 1557 ) {
            echo '#toplevel_page_woodmart_dashboard,#menu-appearance,#wp-admin-bar-theme-dashboard,#toplevel_page_xtemos_options { display:none; }';
        }
                /*if ( isset( $_GET['page'] ) ) {
                    if ( $_GET['page'] == 'xtemos_options' || $_GET['page'] == "woodmart_dashboard" ) {
                        wp_redirect(site_url('wp-admin'));
                        exit;
                    }
                }*/
                ?>  
            </style>
            <?php
        }

        add_action('wp_head', 'as_style');

        function as_style() {

            ?>

            <style type="text/css">
                <?php
                if ( get_current_user_id() == 1557 ) {
                    echo '#toplevel_page_woodmart_dashboard,#wp-admin-bar-theme-dashboard,#toplevel_page_xtemos_options { display:none; }';
                }
                ?>  
            </style>
            <style type="text/css">
                .guaven_woos_suggestion {
                    z-index: 99999;
                }
                .woocommerce-account .afreg_extra_fields p.form-row.form-row-wide {
                    display: none;
                }
                .woocommerce-account .afreg_extra_fields {
                    margin-bottom: 15px;
                }
                a {
                    text-decoration: none !important;
                }
                table.shop_attributes {
                    width: 100%;
                }
                table.shop_attributes tr th {
                    width: 30%;
                    word-break: break-word;
                }
                table.shop_attributes tr td {
                    width: 70%;
                    word-break: break-word;
                }
                .tabs-layout-accordion .woodmart-scroll .woodmart-scroll-content { max-height: 100% !important;}
                .single-product-page .entry-summary .summary-inner p.price {
                    display: none;
                }
                .woodmart-product-brands {
                    float: none !important;
                    margin: 10px;
                    text-align: center;
                }
                .woodmart-product-brands .woodmart-product-brand {
                    display: inline-block;
                }
                .variations .reset_variations {
                    display: none !important;
                }
                .language-box {
                    padding-left: 0px;
                }
                #menu-top-bar-right {
                    border-left: 1px solid #45bfd4;
                }
            </style>
            <style type="text/css">
                #goog-gt-tt {display:none !important;}
                .goog-te-banner-frame {display:none !important;}
                .goog-te-menu-value:hover {text-decoration:none !important;}
                .goog-text-highlight {background-color:transparent !important;box-shadow:none !important;}
                body {top:0 !important;}
                #google_translate_element2 {display:none!important;}
            </style>
            <?php
        }

        add_shortcode('webby_order_status_box', 'webby_order_status_box');

        function webby_order_status_box() {
            ?>   
            <div class="webby_box">        
                <div class="webby_order_status_box">
                    <div class="wc_input_wrap">
                        <input type="text" name="webby_order_id" id="webby_order_id" placeholder="Enter Order/PO number" />
                        <span><i>and</i></span>
                        <input type="text" name="webby_customer_name" id="webby_customer_name" placeholder="Enter Customer Name" />
                    </div>
                    <div class="wc_btn_wrap"> 
                        <a href="javascript:;" class="btn full_check_order_status" >Search</a>
                        <a href="javascript:;" class="btn wc_clear_Search" >Clear</a>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery(document).on('click', '.wc_clear_Search', function () {
                        jQuery('#webby_order_id').val('');
                        jQuery('#webby_customer_name').val('');
                        jQuery('.order-records').empty();
                    });

                    jQuery(document).on('click', '.full_check_order_status', function () {
                        jQuery('#webby_order_id').removeClass('wc_err');
                        jQuery('#webby_customer_name').removeClass('wc_err');

                        var webby_order_id      = jQuery('#webby_order_id').val();
                        var webby_customer_name = jQuery('#webby_customer_name').val();

                        if(webby_order_id == ''){
                            jQuery('#webby_order_id').addClass('wc_err');
                        }
                        if(webby_customer_name == ''){
                            jQuery('#webby_customer_name').addClass('wc_err');
                        }
                        if((webby_order_id != '') && (webby_customer_name != '')) {

                            var data = {
                                'action': 'webby_order_status',
                                'order_id': webby_order_id,
                                'customer_name': webby_customer_name
                            };
                            var html = '';
                            var color = 'NA';
                            var Confirmation_Date = 'NA';
                            var customer_name = 'NA';
                            var prod_data = 'NA';
                            var item_code = 'NA';
                            var place = 'NA';
                            var product_img = '';
                            var quantity = 'NA';
                            var search_by = 'NA';

                            jQuery.post(wc_add_to_cart_params.ajax_url, data, function (response) {
                                var status = '';
                                if (response.status == 'success') {

                                    if (response.order_data != '') {
                                        jQuery(response.order_data).each(function (key, value) {
                                            if( key == 0 ){
                                                color = value.color;
                                                if (color != '') {
                                                    color = color;
                                                }
                                                Confirmation_Date = value.Confirmation_Date;
                                                if (Confirmation_Date != '') {
                                                    Confirmation_Date = Confirmation_Date;
                                                }
                                                customer_name = value.customer_name;
                                                if (customer_name != '') {
                                                    customer_name = customer_name;
                                                }
                                                prod_data = value.data;
                                                if (prod_data != '') {
                                                    prod_data = prod_data;
                                                }
                                                item_code = value.item_code;
                                                if (item_code != '') {
                                                    item_code = item_code;
                                                }
                                                place = value.place;
                                                if (place != '') {
                                                    place = place;
                                                }
                                                product_img = value.product_img;
                                                if (product_img != '') {
                                                    product_img = product_img;
                                                }
                                                quantity = value.quantity;
                                                if (quantity != '') {
                                                    quantity = quantity;
                                                }
                                                search_by = value.search_by;
                                                if (search_by != '') {
                                                    search_by = search_by;
                                                }
                                                so = value.so;
                                                if (so != '') {
                                                    so = so;
                                                }
                                                search_val = value.search_val;
                                                if (search_val != '') {
                                                    search_val = search_val;
                                                }


                                                html += '<div class="order-status-list"><div class="left-col"><div class="left-col-details"><h4 class="order-status" style="display: none;">Complete</h4>';
                                                html += '<h2 class="order-ids"><span class="order-po">' + search_by + '</span><span>' + search_val + '</span></h2>';

                                                html += '<h2 class="order-delivery-date">Expected ship date: <span class="date">' + Confirmation_Date + '</span></h2>';
                                                html += '</div></div><div class="right-col" id="right-col-'+webby_order_id+'-'+key+'"><div class="right-col-details"><div class="left-product-details">';
                                                html += '<h3 class="status">Status: <span class="or-status">' + prod_data + '</span></h3>';
                                                html += '<div class="product-details"><div class="product-img"><img  src=' + product_img + '></div><div class="product-info">';
                                                html += '<h4 class="item">Item: <span>' + item_code + '</span></h4>';
                                                html += '<h4 class="item-color">Item Color: <span>' + color + '</span></h4>';
                                                html += '<h4 class="qty">Quantity: <span>' + quantity + '</span></h4>';
                                                html += '<h4 class="cust_name">Customer Name: <span>' + customer_name + '</span></h4>';
                                                html += '<h4 class="cust_name">Logo: <span>' + value.logo + '</span></h4>';
                                                html += '</div></div></div><div class="orders-buttons"><a class="tack-package btn" id="'+so+'" >Track Package <i class="wc-track-loader hide fa fa-spinner fa-pulse fa-3x fa-fw" style="font-size: 14px;"></i></a><!--<a class="print-invoice btn" onclick="alert(\'Under Construction - Coming Soon\');" rel="Under Construction - Coming Soon " >Print Order Invoice </a>--><a class="return-item-btn btn"  style="display:none;">Return Items</a><a class="buy-again-btn btn" style="display:none;">Buy it Again</a></div></div></div></div></div>';
                                            }
                                        });

jQuery('.order-records').empty();
jQuery('.order-records').append(html);
} else {
    jQuery('.order-status-list').hide();
    status = '<span class="c_o_s error" >Order Not Found</span>';
}
} else {
    jQuery('.order-status-list').hide();
    status = '<span class="c_o_s error" >Order Not Found</span>';
}
jQuery('.c_o_s').remove();
jQuery('.webby_order_status_box').append(status);
});
return false;

}
});
});
</script>
<?php
}

add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text', 99);

function woo_custom_cart_button_text() {
    return __('Order Sample', 'woocommerce');
}

add_action('wp_footer', 'wp_footer_box', 99);

function wp_footer_box() {
    ?>
    <style>
        div#wpc_smart_price_filter-4, div#woocommerce_price_filter-4 {
            /*display: none;*/
        }
        @media only screen and (max-width: 1250px) {
            form.wc_inventory{
                margin: 0 60px;
            }
            form.wc_inventory .wc_form_field input[type="search"]{
                box-sizing: border-box;
            }
            form.wc_inventory .wc_form_field.search_form_field{
                width: calc( 100% - 40px) !important;
            }
        }

        @media only screen and (max-width: 768px) {
            form.wc_inventory .wc_form_field {
                display: block;
                width: 100% !important;
            }
            form.wc_inventory .wc_form_field.search_form_field{
                width: 100% !important;
            }
        }
        @media only screen and (max-width: 600px) {
            .page-template-inventory .wc-inventory-table{
                float: none !important;
            }
            .inventory-table-container{
                overflow: scroll !important;
            }
        }


        form.wc_inventory input[type="number"]{
            text-align: left;
            padding: 0 15px;
        }
        .single-product .woocommerce-product-attributes-item__value ul,
        .single-product .woocommerce-product-attributes-item__value ul li{
            list-style: none;
            margin: 0;
        }
        .single-product h1.product_title.wd-entities-title {
            font-size: 22px;
            font-weight: 600;
        }
        p.c_o_s.wc_order_st{
            margin: 0;
            margin-top: 10px;
            color: #575757;
            font-weight: 600;
        }
        p.c_o_s.wc_order_st span {
            color:#aaaaaa;
            font-weight: 400;
        }
    </style>
    <script type="text/javascript">
        jQuery(document).ready(function () {
        // jQuery(document).on("focusout",".s",function(){
        //     jQuery('.guaven_woos_suggestion').hide();
        // });
            jQuery('.wpfClearButton').click();
        // jQuery( 'form.wc_inventory' ).submit(function() {
        //     var product_id = jQuery( this ).find('.product_id').val();
        //     var color = jQuery( this ).find('.inventory_color').val();
        //     var qty = jQuery( this ).find('.wc_qty').val();
        //     var stock = jQuery( this ).find('.stock').val();
        //     var search = jQuery( this ).find('.wc_inventory_search').val();
        //     jQuery.ajax({
        //      type : "POST",
        //      dataType : "json",
        //      url : "<?php //echo admin_url('admin-ajax.php'); ?>",
        //      data : {action: "wp_sync_status", product_id: product_id, color:color, qty:qty, stock:stock, search: search},
        //      success: function(response) {
        //         jQuery( 'table.wc-inventory-table tbody' ).html( response );
        //     }
        // });
        //     return false;
        // });
            setTimeout(function () {
                // jQuery('.tab-title-description').click();
            }, 1000);
            jQuery(document).on('click', '.webby_inventory_link', function () {
                jQuery('.webby_color_status').slideToggle();
                return false;
            });
            jQuery(document).on('click', '.webby_order_link', function () {
                jQuery('.webby_order_status_box').slideToggle();
                return false;
            });
            jQuery(document).on('click', '.webby_freight_link', function () {
                jQuery('.webby_freight_box').slideToggle();
                return false;
            });
            jQuery(document).on('keydown change keyup','.wpfPriceRangeCustom input[name="wpf_custom_min"],.wpfPriceRangeCustom input[name="wpf_custom_max"]',function(){
                var t_this = jQuery(this);
                var min_p = jQuery(this).closest('.wpfPriceRangeCustom').find('input[name="wpf_custom_min"]').val();
                var max_p = jQuery(this).closest('.wpfPriceRangeCustom').find('input[name="wpf_custom_max"]').val();
                if (typeof min_p === "undefined") {
                    min_p = 0;
                }
                if (typeof max_p === "undefined") {
                    max_p = 0;
                }
                t_this.closest('li').attr('data-range',min_p+','+max_p);
            });
            jQuery(document).on('click', '.check_status', function () {
                if ( jQuery('#webby_qty').val() != '' ) {
                    jQuery('.webby_color_status table .stock').each(function () {
                        var stock_status = 'Contact US';
                        if (parseInt(jQuery(this).attr('data-id')) >= parseInt(jQuery('#webby_qty').val())) {
                            stock_status = 'In Stock';
                        }
                        jQuery(this).parent().find('.stock_status').html(stock_status);
                    });
                    jQuery('.webby_color_status table').show();
                }
                return false;
            });

            function createCookie(name, value, days) {
                var expires;

                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toGMTString();
                } else {
                    expires = "";
                }
                document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
            }


            function check_lang(lang) {
                if ('Canada' == lang) {
                    jQuery('.english-nav').hide();
                    jQuery('.franch-nav').show();
                    if (jQuery('.nav-tabs .nav-item').length > 4) {
                        jQuery('.nav-tabs .nav-item:nth-child(3) a.nav-link').click();
                    } else {
                        jQuery('.nav-tabs .nav-item:nth-child(2) a.nav-link').click();
                    }

                    createCookie('lang', 'canada');
                } else {
                    jQuery('.english-nav').show();
                    jQuery('.franch-nav').hide();
                    jQuery('.english-nav:first-child a.nav-link').click();
                    createCookie('lang', 'eng');
                }
            }
            if (readCookie('lang') == 'canada') {
                check_lang('Canada');
            } else {
                check_lang('English');
            }

            jQuery(document).on('click', '.langvage a', function () {
                check_lang(jQuery(this).attr('title'));
                return false;
            });

            // jQuery('.country-form-popup').magnificPopup();
            // setTimeout(function(){
            //     jQuery('.country-form-popup').click();
            // },200);

            jQuery(document).on("click",'.country-main-content .form-langvage .glink',function(){
               $.magnificPopup.close();
               check_lang(jQuery(this).attr('title'));
               return false;
           });

            jQuery('#freight_box').submit(function (event) {
                var get_Quantity = jQuery('#get_Quantity').val();
                var get_ZipCode = jQuery('#get_ZipCode').val();
                var get_ShipMethod = jQuery('#get_ShipMethod').val();
                var get_Carrier = jQuery('#get_Carrier').val();
                var get_item = jQuery('#get_item').val();
                var data = {
                    'action': 'webby_f_status',
                    'get_Quantity': get_Quantity,
                    'get_ZipCode': get_ZipCode,
                    'get_ShipMethod': get_ShipMethod,
                    'get_Carrier': get_Carrier,
                    'get_item': get_item,
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(wc_add_to_cart_params.ajax_url, data, function (response) {
                    var data_p = response.results;

                    jQuery('.table_value_status').html(data_p);
                });
                event.preventDefault();
            });

            jQuery(document).on('click', '.orders-buttons .tack-package', function () {

                jQuery(this).find('i.wc-track-loader').removeClass('hide');
                jQuery(this).parents('.right-col').find('table.wc-tracking-table').remove();
                var webby_order_id = jQuery(this).attr('id');
                var data = {
                    'action': 'webby_order_track',
                    'order_id': webby_order_id,
                    'raw_id': jQuery(this).parents('.right-col').attr('id')
                };

                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post(wc_add_to_cart_params.ajax_url, data, function (response) {
                    console.log(response);

                    var status = '';
                    if(response != null) {
                        if (response.status == 'success') {
                            if (response.result != '') {
                                status += '<table class="wc-tracking-table">';
                                status += '<thead><tr><th>Tracking</th><th>Shipping Date</th><th>Ship Carrier Name</th><th>Ship Carrier Service</th></tr></thead>';
                                status += '<tbody><tr>';

                                status += '<td>';
                                if ( response.result.ship_carrier_name == 'UPS' ) {
                                    status += '<a target="_blank" href="https://www.ups.com/track?loc=null&tracknum='+response.result.tracking_no+'&requester=WT/trackdetails" >'+response.result.tracking_no+'</a>';
                                } else {
                                    status += '<a target="_blank" href="https://www.fedex.com/fedextrack/?trknbr='+response.result.tracking_no+'&trkqual=12020~394863957859~FDEG" >'+response.result.tracking_no+'</a>';
                                }
                                status += '</td>';
                                status += '<td>' + response.result.ship_date + '</td>';
                                status += '<td>' + response.result.ship_carrier_name + '</td>';
                                status += '<td>' + response.result.ship_via + '</td>';
                                status += '</tr></tbody>';
                                status += '</table>';
                                status += '<table class="wc-tracking-table mobile">';
                                status += '<tbody><tr>';
                                status += '<tr><th>Tracking</th><td>';
                                if ( response.result.ship_carrier_name == 'UPS' ) {
                                    status += '<a target="_blank" href="https://www.ups.com/track?loc=null&tracknum='+response.result.tracking_no+'&requester=WT/trackdetails" >'+response.result.tracking_no+'</a>';
                                } else {
                                    status += '<a target="_blank" href="https://www.fedex.com/fedextrack/?trknbr='+response.result.tracking_no+'&trkqual=12020~394863957859~FDEG" >'+response.result.tracking_no+'</a>';
                                }
                                status += '</td></tr>';
                                status += '<tr><th>Shipping Date</th><td>' + response.result.ship_date + '</td></tr>';
                                status += '<tr><th>Ship Carrier Name</th><td>' + response.result.ship_carrier_name + '</td></tr>';
                                // if( response.result.CS_A != '' && response.result.CS_A != undefined  && response.result.CS_A != null ){
                                //     status += '<tr><th>Ship Carrier Service</th><td>' + response.result.CS_A + '</td></tr>';
                                // }else{
                                // }
                                                                    status += '<tr><th>Ship Carrier Service</th><td>' + response.result.ship_via + '</td></tr>';

                                status += '</tr></tbody>';
                                status += '</table>';
                            } else {
                                status = '<table class="wc-tracking-table" ><tbody><tr><td style=" color:red; text-align:center;">Tracking not available</td></tr></tbody></table>';
                            }
                        } else {
                            status = '<table class="wc-tracking-table" ><tbody><tr><td style=" color:red; text-align:center;">Tracking not available</td></tr></tbody></table>';
                        }
                    } else {
                        status = '<table class="wc-tracking-table" ><tbody><tr><td style=" color:red; text-align:center;">Tracking not available</td></tr></tbody></table>';
                    }
                    //console.log(status);
                    jQuery('#'+response.OrderNum+' i.wc-track-loader').addClass('hide');
                    jQuery('#'+response.OrderNum).append(status);
                });
return false;
});



jQuery(document).on('click', '.check_order_status', function () {
    var webby_order_id = jQuery('#webby_order_id').val();
    var data = {
        'action': 'webby_order_status',
        'order_id': webby_order_id
    };

    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(wc_add_to_cart_params.ajax_url, data, function (response) {
        var status = '';
        console.log(response.status);
        if (response.status == 'success') {
            if (response.data != '') {
                status += '<p class="c_o_s wc_order_st" ><span >Customer Name: </span>' + response.customer_name + '</p>';
                status += '<p class="c_o_s wc_order_st" ><span >Order Status: </span>' + response.data + '</p>';
                status += '<p class="c_o_s wc_order_st" ><span >Item: </span>' + response.item_code + '</p>';
                status += '<p class="c_o_s wc_order_st" ><span >Item Color: </span>' + response.color + '</p>';
                status += '<p class="c_o_s wc_order_st" ><span >Expected To Be Delived On: </span>' + response.Confirmation_Date + '</p>';
                status += '<p class="c_o_s wc_order_st" ><span >Quantity: </span>' + response.quantity + '</p>';
            } else {
                status = '<span class="c_o_s error" >Order Not Found</span>';
            }
        } else {
            status = '<span class="c_o_s error" >Order Not Found</span>';
        }
        jQuery('.c_o_s').remove();
        jQuery('.webby_order_status_box').append(status);
    });
    return false;
});

setTimeout(function(){
    ajaxComplete_mobile_slider();
},1000)

jQuery(document).on('click','.variations .with-swatches .wd-swatches-product .wd-swatch' ,function(){
    ajaxComplete_mobile_slider();
});

function ajaxComplete_mobile_slider(){
  var m_url = jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-thumb .wd-carousel-wrap .wd-carousel-item img').attr('src');
  // console.log(m_url);
  jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a').attr('herf', m_url );
  jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('src', m_url );
  jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('srcset', m_url ); 
  jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('data-src', m_url ); 
  jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('data-large_image', m_url ); 
}

jQuery('.wc-carousel-image').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    closeBtnInside: false,
    fixedContentPos: true,
        mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
        duration: 300 // don't foget to change the duration also in CSS
    }
});
jQuery('.wc-carousel-video ').magnificPopup({
    disableOn: 700,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,

    fixedContentPos: false
});
});

</script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(document).off('.data-api');
        var ddd = jQuery('.woodmart-product-brands').clone();
        jQuery('.wd-product-brands').remove();
        jQuery('.product-images-inner').after(ddd);

        jQuery(document).ajaxComplete(function () {
            if (getUrlVars()['filter_color']) {
                var option_selected = getUrlVars()['filter_color'];
                option_selected = option_selected.split(',');
                jQuery('.product-grid-item').each(function () {
                    jQuery('.wd-swatches-grid .wd-swatch').removeClass('wd-active').removeClass('wd-tooltip-inited');
                    jQuery(this).find('.wd-swatches-grid .wd-swatch').each(function () {
                        var name_option =  jQuery(this).text();
                        name_option = name_option.toLowerCase().replace(/\s/g,'');
                        if (!jQuery.inArray(name_option, option_selected)) {

                            var current_image_class = '';
                            var current_swatch = jQuery(this);
                            var selectedColor = current_swatch.data('image-src');

                            if( current_swatch.parents('.wd-product').find('.wcpb-product-badges-loop-container').length > 0 ){
                                current_image_class = current_swatch.parents('.wd-product').find('.hover-img .wcpb-product-badges-loop-container img, .product-element-top .wcpb-product-badges-loop-container img');
                            }else{
                                current_image_class =  current_swatch.parents('.wd-product').find('.product-element-top img,.hover-img img');
                            }

                            checkImageExists(selectedColor, function(exists) {
                                if (exists) {
                                    current_image_class.attr('src', selectedColor);
                                    current_image_class.attr('srcset', selectedColor + ' 600w, ' +
                                        selectedColor + ' 150w, ' +
                                        selectedColor + ' 1200w, ' +
                                        selectedColor + ' 630w, ' +
                                        selectedColor + ' 263w, ' +
                                        selectedColor + ' 768w, ' +
                                        selectedColor + ' 896w, ' +
                                        selectedColor + ' 1500w');

                                    current_swatch.parents('.wd-product').removeClass('wd-loading-image');
                                    setTimeout(function(){
                                        current_swatch.addClass('wd-active wd-tooltip-inited');
                                        current_swatch.parents('.wd-product').removeClass('wd-loading-image');
                                    },200);
                                } 
                            });               
                        }
                    });
                });
            }
        });

        jQuery(document).on('click', '.wd-product .wd-swatch', function() {

            var current_image_class = '';
            var current_swatch = jQuery(this);
            var selectedColor = current_swatch.data('image-src');

            if( current_swatch.parents('.wd-product').find('.wcpb-product-badges-loop-container').length > 0 ){
                current_image_class = current_swatch.parents('.wd-product').find('.hover-img .wcpb-product-badges-loop-container img, .product-element-top .product-image-link .wcpb-product-badges-loop-container img');
            }else{
                current_image_class =  current_swatch.parents('.wd-product').find('.product-element-top img,.hover-img img');
            }

            checkImageExists(selectedColor, function(exists) {
                if (exists) {
                    current_image_class.attr('src', selectedColor);
                    current_image_class.attr('srcset', selectedColor + ' 600w, ' +
                        selectedColor + ' 150w, ' +
                        selectedColor + ' 1200w, ' +
                        selectedColor + ' 630w, ' +
                        selectedColor + ' 263w, ' +
                        selectedColor + ' 768w, ' +
                        selectedColor + ' 896w, ' +
                        selectedColor + ' 1500w');

                    current_swatch.parents('.wd-product').removeClass('wd-loading-image');
                    setTimeout(function(){
                        current_swatch.addClass('wd-active wd-tooltip-inited');
                        current_swatch.parents('.wd-product').removeClass('wd-loading-image');
                    },200);
                } 
            }); 
        });

        function getUrlVars() {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        function checkImageExists(url, callback) {
            var img = new Image();
            img.onload = function() {
                callback(true);
            };
            img.onerror = function() {
                callback(false);
            };
            img.src = url;
        }

        // window.setInterval(function () {
        // }, 5000);

        jQuery(document).mouseup(function(e) 
        {
            var container = jQuery(".mobile-nav");
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                jQuery('.guaven_woos_suggestion').hide();
                container.removeClass('wd-opened');
            }
        });
    });
</script>
<?php
}

function bd_rrp_sale_price_html($price, $product) {

    $retun_string = 'As Low As ' . $price;

    return $retun_string;
}

add_filter('woocommerce_get_price_html', 'bd_rrp_sale_price_html', 100, 2);

add_action('wp_ajax_add_wp_sync_product_id', 'add_wp_sync_product_id');
add_action('wp_ajax_nopriv_add_wp_sync_product_id', 'add_wp_sync_product_id');

add_action('wp_ajax_add_wp_sync_desc_product_id', 'add_wp_sync_desc_product_id');
add_action('wp_ajax_nopriv_add_wp_sync_desc_product_id', 'add_wp_sync_desc_product_id');

add_action('wp_ajax_wp_sync_status', 'wp_sync_status');
add_action('wp_ajax_nopriv_wp_sync_status', 'wp_sync_status');

function wp_sync_status() {
    if( isset( $_POST ) || !empty($_POST) ){
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
        $color = isset($_POST['color']) ? $_POST['color'] : '';
        $qty = isset($_POST['qty']) ? $_POST['qty'] : '';
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
        $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
        $search = isset($_POST['search']) ? $_POST['search'] : '';
    }

    $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getInventoryRecovered');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
        'token' => 'A5H#Fs^d8t7U',
        'product_id' => $product_id,
        'color' => $color,
        'quantity' => $qty,
        'keyword' => $keyword,
        'in_stock' => $stock,
    // 'search' => $search,
    ));

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response) {
        $response = json_decode($response);
    }
    $html = '';
    if( $response->Inventory->recordsFiltered > 0 ){
        foreach ($response as $inventory) {
            if( isset($inventory->data) && !empty($inventory->data) ){
                foreach( $inventory->data as $inventory_data ){
                    $html .= '<tr>';
                    $html .= '<td class="wc-sku-code"><a target="_blank" href="'.site_url('product/style-'.$inventory_data->item_id.'/').'" >'.$inventory_data->item_id.'</a></td>';
                    if(wc_get_product_id_by_sku($inventory_data->item_id) == 0){
                        $html .= '<td><img width="100" height="100" src="'.site_url().'/wp-content/uploads/2022/04/notfound.png" ></td>';
                    }else{
                        $product_id = wc_get_product_id_by_sku($inventory_data->item_id);
                        $flag = 0;
                        $variatio_p_id = 0;
                        if( $product_id ){
                            $product = wc_get_product( $product_id );
                            if( is_object( $product ) ){

                                $variations = $product->get_available_variations(); 
                                if( $variations ){
                                    $var_prod_id = wp_list_pluck( $variations, 'variation_id' );
                                    if( $var_prod_id ){         
                                        foreach ($var_prod_id as $values) {
                                            $variation = new WC_Product_Variation($values);
                                            $regular_price = $variation->get_price();
                                            $pricing_values  = get_post_meta( $values, '_as_quantity_range_pricing_values', true );
                                            $price = 0;
                                            if( $pricing_values ){
                                                $end_price = end( $pricing_values );  
                                                $price  = $end_price['price'];
                                            } else {
                                                $price = $regular_price;
                                            }
                                        }
                                    }
                                }

                                $variation_products =  $product->get_children();
                                foreach($variation_products as $variation_products_img){
                                    $taxonomy = 'pa_color';
                                    $meta = get_post_meta($variation_products_img, 'attribute_'.$taxonomy, true);
                                    $inventory_color_replace = explode(" ",$inventory_data->color);
                                    $str_replace = explode("-",$meta);
                                    if(trim(strtolower($meta)) == trim(strtolower($inventory_data->color))){
                                        $flag = 1;
                                        $variatio_p_id = $variation_products_img;
                                    }else if ( count($str_replace) > 1 ) {
                                        $str_1 = $str_replace[0].$str_replace[1];
                                        $str_2 =  $str_replace[1].$str_replace[0];

                                        $inventory_color_str1 = $inventory_color_replace[0].$inventory_color_replace[1];
                                        $inventory_color_str2 = $inventory_color_replace[1].$inventory_color_replace[0];
                                        if(trim(strtolower($str_1)) == trim(strtolower($inventory_color_str1))){
                                            $flag = 1;
                                            $variatio_p_id = $variation_products_img;
                                        }else if(trim(strtolower($str_2)) ==  trim(strtolower($inventory_color_str2))){
                                            $flag = 1;
                                            $variatio_p_id = $variation_products_img;
                                        }
                                    }

                                }
                            }

                        }

                        if( $flag == 1 ){

                            if( $variatio_p_id ){
                                if( wp_get_attachment_image_url( get_post_thumbnail_id($variatio_p_id),"full" ) ){
                                    $html .= '<td><img width="100" height="100" src="'.wp_get_attachment_image_url( get_post_thumbnail_id($variatio_p_id),"full" ).'" ></td>';
                                }else{
                                    $flag = 0; 
                                }
                            }else{
                                $flag = 0; 
                            }
                        }

                        if( $flag == 0 ){
                            if( wp_get_attachment_image_url( get_post_thumbnail_id(wc_get_product_id_by_sku($inventory_data->item_id)),"full" ) ){
                                $html .= '<td><img width="100" height="100" src="'.wp_get_attachment_image_url( get_post_thumbnail_id(wc_get_product_id_by_sku($inventory_data->item_id)),"full" ).'" ></td>';   
                            }else{
                                $html .= '<td><img width="100" height="100" src="'.site_url().'/wp-content/uploads/2022/04/notfound.png" ></td>';       
                            }

                        }
                    }          
                    $html .= '<td>'.$inventory_data->color.'</td>';
                    $html .= '<td>'.$inventory_data->quantity.'</td>';
                    $html .= '<td>'.$inventory_data->description.'</td>';
                    $html .= '<td>'.$inventory_data->in_stock.'</td>';
                    $html .= '<td class="wc-inventory-currency"><span></span>'.number_format($price, 2 ).'</td>';
                    $html .= '</tr>';
                }    
            }
        }

    } else {
        $html = '<tr><td colspan="6" style="text-align:center;">No Data Available</td></tr>';
    }
    wp_send_json($html);
}

function add_wp_sync_desc_product_id() {

    $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getProductsFullDetail');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
        'token' => 'A5H#Fs^d8t7U',
        'ItemNum' => $_REQUEST['p_id'],
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    if ($response) {
        $response = json_decode($response);
    }

    $product_data = $response->productData;

    if ($response->data >= 1) {


        $prices = array();
        if ($product_data->Qty1 != 0 && $product_data->Qty1_max != 0 && $product_data->Prc1 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty1, 'max_qty' => $product_data->Qty1_max, 'type' => 'fixed', 'price' => $product_data->Prc1, 'cnd_price' => $product_data->Prc2_cnd, 'sec_price' => $product_data->prc1_sec, 'sec_cnd_price' => $product_data->cnd_prc1_sec, 'role' => array());
        }
        if ($product_data->Qty2 != 0 && $product_data->Qty2_max != 0 && $product_data->Prc2 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty2, 'max_qty' => $product_data->Qty2_max, 'type' => 'fixed', 'price' => $product_data->Prc2, 'cnd_price' => $product_data->Prc2_cnd, 'sec_price' => $product_data->prc2_sec, 'sec_cnd_price' => $product_data->cnd_prc2_sec, 'role' => array());
        }
        if ($product_data->Qty3 != 0 && $product_data->Qty3_max != 0 && $product_data->Prc3 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty3, 'max_qty' => $product_data->Qty3_max, 'type' => 'fixed', 'price' => $product_data->Prc3, 'cnd_price' => $product_data->Prc3_cnd, 'sec_price' => $product_data->prc3_sec, 'sec_cnd_price' => $product_data->cnd_prc3_sec, 'role' => array());
        }
        if ($product_data->Qty4 != 0 && $product_data->Qty4_max != 0 && $product_data->Prc4 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty4, 'max_qty' => $product_data->Qty4_max, 'type' => 'fixed', 'price' => $product_data->Prc4, 'cnd_price' => $product_data->Prc4_cnd, 'sec_price' => $product_data->prc4_sec, 'sec_cnd_price' => $product_data->cnd_prc4_sec, 'role' => array());
        }
        if ($product_data->Qty5 != 0 && $product_data->Qty5_max != 0 && $product_data->Prc5 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty5, 'max_qty' => $product_data->Qty5_max, 'type' => 'fixed', 'price' => $product_data->Prc5, 'cnd_price' => $product_data->Prc5_cnd, 'sec_price' => $product_data->prc5_sec, 'sec_cnd_price' => $product_data->cnd_prc5_sec, 'role' => array());
        }
        if ($product_data->Qty6 != 0 && $product_data->Qty6_max != 0 && $product_data->Prc6 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty6, 'max_qty' => $product_data->Qty6_max, 'type' => 'fixed', 'price' => $product_data->Prc6, 'cnd_price' => $product_data->Prc6_cnd, 'sec_price' => $product_data->prc6_sec, 'sec_cnd_price' => $product_data->cnd_prc6_sec, 'role' => array());
        }


        $product_id = get_product_by_sku($product_data->ItemNum);
        $product_data = array();
        if ($product_id) {
            $product_data_wp = array(
                'ID' => $product_id,
                'post_excerpt' => $product_data->Description,
            );

            wp_update_post($product_data_wp);

            $variations = get_v_product_by_sku($product_id);
            if ($variations) {
                foreach ($variations as $v_product_id) {
                    $product_data[] = $v_product_id;
                    // update_post_meta( $v_product_id, '_as_quantity_range_pricing_values', $prices);
                    $on_data = '';
                    if ($prices) {
                        $on_data = 'on';
                    }
                    // update_post_meta( $v_product_id, '_as_quantity_range_pricing_enable', $on_data);
                }
            }
        }
    }
    print_r($prices);
    wp_send_json(array($variations, $product_data, $prices, $response));
}

function add_wp_sync_product_id() {

    $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getProductsFullDetail');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
        'token' => 'A5H#Fs^d8t7U',
        'ItemNum' => $_REQUEST['p_id'],
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    if ($response) {
        $response = json_decode($response);
    }

    $product_data = $response->productData;

    if ($response->data >= 1) {
        //https://www.webhat.in/article/woocommerce-tutorial/creating-simple-product-using-woocommerce-crud/

        $product = new WC_Product_Variable();
        $product->set_props(
            array(
                'name' => $product_data->Name,
                'sku' => $product_data->ItemNum,
            )
        );
        $product->set_short_description($product_data->Description);

        $cat_ids = array();
        $tag_ids = array();
        if (!empty($product_data->Cat1Name)) {
            $term = get_term_by('name', $product_data->Cat1Name, 'product_cat');

            if ($term) {
                $cat_ids[] = $term->term_id;
            } else {
                $my_term = wp_insert_term($product_data->Cat1Name, 'product_cat');
                $cat_ids[] = $my_term->term_id;
            }
        }
        if (!empty($product_data->Keywords)) {
            $Keywords = explode(",", $product_data->Keywords);

            foreach ($Keywords as $Keyword) {
                $term = get_term_by('name', trim($Keyword), 'product_tag');

                if ($term) {
                    $tag_ids[] = $term->term_id;
                } else {
                    $my_term = wp_insert_term(trim($Keyword), 'product_tag');
                    $tag_ids[] = $my_term->term_id;
                }
            }
        }
        if (!empty($product_data->Cat2Name)) {
            $term = get_term_by('name', $product_data->Cat2Name, 'product_cat');

            if ($term) {
                $cat_ids[] = $term->term_id;
            } else {
                $my_term = wp_insert_term($product_data->Cat2Name, 'product_cat');
                $cat_ids[] = $my_term->term_id;
            }
        }

        $product->set_category_ids($cat_ids);
        $product->set_tag_ids($tag_ids);

        $product->set_description($product_data->Description);
        $product->set_short_description($product_data->Description);
        $product->set_length($product_data->Dimension1);
        $product->set_width($product_data->Dimension2);
        $product->set_height($product_data->Dimension3);
        $product->set_status($product_data->post_status);
        if ($product_data->featured) {
            $product->set_featured(TRUE);
        }
        $prices = array();
        if ($product_data->Qty1 != 0 && $product_data->Qty1_max != 0 && $product_data->Prc1 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty1, 'max_qty' => $product_data->Qty1_max, 'type' => 'fixed', 'price' => $product_data->Prc1, 'cnd_price' => $product_data->Prc2_cnd, 'sec_price' => $product_data->prc1_sec, 'sec_cnd_price' => $product_data->cnd_prc1_sec, 'role' => array());
        }
        if ($product_data->Qty2 != 0 && $product_data->Qty2_max != 0 && $product_data->Prc2 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty2, 'max_qty' => $product_data->Qty2_max, 'type' => 'fixed', 'price' => $product_data->Prc2, 'cnd_price' => $product_data->Prc2_cnd, 'sec_price' => $product_data->prc2_sec, 'sec_cnd_price' => $product_data->cnd_prc2_sec, 'role' => array());
        }
        if ($product_data->Qty3 != 0 && $product_data->Qty3_max != 0 && $product_data->Prc3 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty3, 'max_qty' => $product_data->Qty3_max, 'type' => 'fixed', 'price' => $product_data->Prc3, 'cnd_price' => $product_data->Prc3_cnd, 'sec_price' => $product_data->prc3_sec, 'sec_cnd_price' => $product_data->cnd_prc3_sec, 'role' => array());
        }
        if ($product_data->Qty4 != 0 && $product_data->Qty4_max != 0 && $product_data->Prc4 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty4, 'max_qty' => $product_data->Qty4_max, 'type' => 'fixed', 'price' => $product_data->Prc4, 'cnd_price' => $product_data->Prc4_cnd, 'sec_price' => $product_data->prc4_sec, 'sec_cnd_price' => $product_data->cnd_prc4_sec, 'role' => array());
        }
        if ($product_data->Qty5 != 0 && $product_data->Qty5_max != 0 && $product_data->Prc5 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty5, 'max_qty' => $product_data->Qty5_max, 'type' => 'fixed', 'price' => $product_data->Prc5, 'cnd_price' => $product_data->Prc5_cnd, 'sec_price' => $product_data->prc5_sec, 'sec_cnd_price' => $product_data->cnd_prc5_sec, 'role' => array());
        }
        if ($product_data->Qty6 != 0 && $product_data->Qty6_max != 0 && $product_data->Prc6 != 0) {
            $prices[] = array('min_qty' => $product_data->Qty6, 'max_qty' => $product_data->Qty6_max, 'type' => 'fixed', 'price' => $product_data->Prc6, 'cnd_price' => $product_data->Prc6_cnd, 'sec_price' => $product_data->prc6_sec, 'sec_cnd_price' => $product_data->cnd_prc6_sec, 'role' => array());
        }

        $my_attributes = array();
        foreach ($response->Inventory as $inventory) {
            $attr_c = str_replace("-", "", str_replace($inventory->item_id, "", $inventory->mas90));
            $my_attributes[] = strtolower(trim($attr_c));
        }

        $attribute_data = my_wc_create_attribute('colors', $my_attributes);
        $attributes = array();
        $attribute = new WC_Product_Attribute();
        $attribute->set_id($attribute_data['attribute_id']);
        $attribute->set_name($attribute_data['attribute_taxonomy']);
        $attribute->set_options($my_attributes);
        $attribute->set_position(1);
        $attribute->set_visible(true);
        $attribute->set_variation(true);
        $attributes[] = $attribute;
        $product->set_attributes($attributes);
        $product_id = get_product_by_sku($product_data->ItemNum);
        if ($product_id) {
            $product->set_id($product_id);
        }
        $product->save();

        update_post_meta($product->get_id(), 'Colors', $product_data->Colors);
        update_post_meta($product->get_id(), 'Themes', $product_data->Themes);
        update_post_meta($product->get_id(), 'meta_yoast_wpseo_focuskw', $product_data->Keywords);
        update_post_meta($product->get_id(), 'wc_product_id', $product_data->id);
        update_post_meta($product->get_id(), 'wc_full_object', $product_data);
        if ($product_data->images) {
            cin7_product_image($product_data->images, $product->get_id());
        }
        $variations = get_v_product_by_sku($product->get_id());


        foreach ($response->Inventory as $inventory) {
            $variation_1 = new WC_Product_Variation();
            $attr_c = str_replace("-", "", str_replace($inventory->item_id, "", $inventory->mas90));
            $attr_c = str_replace(" ", "-", trim($attr_c));
            $variation_1->set_props(
                array(
                    'parent_id' => $product->get_id(),
                    'sku' => $inventory->item_id . '-' . $attr_c,
                    'regular_price' => $inventory->regular_price,
                )
            );

            if ($variations) {
                $v_product_id = get_v_color_product_by_sku($variations, strtolower($attr_c));

                if ($v_product_id) {
                    $variation_1->set_id($v_product_id);
                }
            }
            $v_stock = trim($inventory->stock);
            $variation_1->set_description($inventory->description);
            $variation_1->set_stock_quantity($v_stock);
            $variation_1->set_regular_price($product_data->regular_price);
            $variation_1->set_sale_price($product_data->sale_price);
            $variation_1->set_attributes(array('pa_colors' => strtolower($attr_c)));
            $variation_1->save();
            if ($product_data->images) {
                cin7_product_image($inventory->images, $variation_1->get_id());
            }
            update_post_meta($variation_1->get_id(), '_as_quantity_range_pricing_values', $prices);
            $on_data = '';
            if ($prices) {
                $on_data = 'on';
            }
            update_post_meta($variation_1->get_id(), '_as_quantity_range_pricing_enable', $on_data);

            update_option('wc_p_v_' . $inventory->id, $variation_1->get_id());
        }
    }
    wp_send_json($response);
}

function get_product_by_sku($sku) {

    global $wpdb;

    $product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));

    if ($product_id)
        return $product_id;

    return false;
}

function get_v_color_product_by_sku($product_ids, $color) {

    global $wpdb;
    foreach ($product_ids as $productid) {
        $product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='attribute_pa_colors' AND meta_value='%s' AND post_id = %d LIMIT 1", $color, $productid));

        if ($product_id)
            return $product_id;
    }


    return false;
}

function get_v_product_by_sku($product_id) {

    $args = array(
        'post_type' => 'product_variation',
        'post_parent__in' => array($product_id),
        'posts_per_page' => -1,
        'fields' => 'ids'
    );
    $query = new WP_Query($args);
    if ($query->posts) {
        return $query->posts;
    }
    return false;
}

function cin7_product_image($product_images, $product_id) {
    $image_ids = array();
    $product_images = explode("|", $product_images);
    foreach ($product_images as $images) {
        $images = explode("!", $images);
        $image_url = trim($images[0]);
        $filename = basename($image_url);
        $data_image = get_page_by_title($filename, "OBJECT", 'attachment');
        if ($data_image) {
            $image_ids[] = $data_image->ID;
        } else {
            $upload_dir = wp_upload_dir();

            $image_data = file_get_contents($image_url);

            $filename = basename($image_url);

            if (wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null);

            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file);
            $image_ids[] = $attach_id;
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);
        }
    }

    if ($image_ids) {
        $image_ids = array_reverse($image_ids);
        update_post_meta($product_id, '_thumbnail_id', $image_ids[0]);
        array_splice($image_ids, 0, 1);
        if ($image_ids) {
            update_post_meta($product_id, '_product_image_gallery', implode(',', $image_ids));
        }
    }
}

function my_wc_create_attribute($raw_name = 'size', $terms = array('small')) {

    global $wpdb, $wc_product_attributes;
    // Make sure caches are clean.
    delete_transient('wc_attribute_taxonomies');
    WC_Cache_Helper::incr_cache_prefix('woocommerce-attributes');
    // These are exported as labels, so convert the label to a name if possible first.
    $attribute_labels = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name');
    $attribute_name = array_search($raw_name, $attribute_labels, true);
    if (!$attribute_name) {
        $attribute_name = wc_sanitize_taxonomy_name($raw_name);
    }
    $attribute_id = wc_attribute_taxonomy_id_by_name($attribute_name);
    if (!$attribute_id) {
        $taxonomy_name = wc_attribute_taxonomy_name($attribute_name);
        // Degister taxonomy which other tests may have created...
        unregister_taxonomy($taxonomy_name);
        $attribute_id = wc_create_attribute(
            array(
                'name' => $raw_name,
                'slug' => $attribute_name,
                'type' => 'select',
                'order_by' => 'menu_order',
                'has_archives' => 0,
            )
        );
        // Register as taxonomy.
        register_taxonomy(
            $taxonomy_name, apply_filters('woocommerce_taxonomy_objects_' . $taxonomy_name, array('product')), apply_filters(
                'woocommerce_taxonomy_args_' . $taxonomy_name, array(
                    'labels' => array(
                        'name' => $raw_name,
                    ),
                    'hierarchical' => false,
                    'show_ui' => false,
                    'query_var' => true,
                    'rewrite' => false,
                )
            )
        );
        // Set product attributes global.
        $wc_product_attributes = array();
        foreach (wc_get_attribute_taxonomies() as $taxonomy) {
            $wc_product_attributes[wc_attribute_taxonomy_name($taxonomy->attribute_name)] = $taxonomy;
        }
    }
    $attribute = wc_get_attribute($attribute_id);
    $return = array(
        'attribute_name' => $attribute->name,
        'attribute_taxonomy' => $attribute->slug,
        'attribute_id' => $attribute_id,
        'term_ids' => array(),
    );

    foreach ($terms as $term) {
        $result = term_exists($term, $attribute->slug);
        if (!$result) {
            $result = wp_insert_term($term, $attribute->slug);
            $return['term_ids'][] = $result['term_id'];
        } else {
            $return['term_ids'][] = $result['term_id'];
        }
    }

    return $return;
}

function add_cors_http_header() {
    header("Access-Control-Allow-Origin: *");
}

//add_action('init', 'add_cors_http_header');

function wcmo_get_current_user_roles() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $roles = (array) $user->roles;
        return $roles; // This returns an array
        // Use this to return a single value
        // return $roles[0];
    } else {
        return array();
    }
}

add_filter('woocommerce_attribute', 'wc_woocommerce_attribute_callback', 10, 3);

function wc_woocommerce_attribute_callback($string, $attribute, $values) {
    return str_replace(array("\r\n", "\r", '"\n"', "\n", "\\r", "\\n", "\\r\\n"), "<br/>", $string);
    ;
}

if (isset($_GET['print_ids']) && !empty($_GET['print_ids'])) {
    add_action('init', 'wc_add_product_tax');

    function wc_add_product_tax() {
        $allproducts = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        $all_product_ids = $allproducts->posts;
        foreach ($all_product_ids as $all_product_id_val) {
            $arr = array();
            $additional_color = wp_get_object_terms($all_product_id_val, 'pa_additional-colors');
            if ($additional_color) {
                $additional_color_rec = get_v_s('Additional Colors', $additional_color);
                if ($additional_color_rec) {
                    $arr[] = $additional_color_rec;
                }
            }
            $bag_dimensions = wp_get_object_terms($all_product_id_val, 'pa_bag-dimensions');
            if ($bag_dimensions) {
                $bag_dimensions_rec = get_v_s('Bag Dimensions', $bag_dimensions);
                if ($bag_dimensions_rec) {
                    $arr[] = $bag_dimensions_rec;
                }
            }
            $box_dimensions = wp_get_object_terms($all_product_id_val, 'pa_box-dimensions');
            if ($box_dimensions) {
                $box_dimensions_rec = get_v_s('Box Dimensions', $box_dimensions);
                if ($box_dimensions_rec) {
                    $arr[] = $box_dimensions_rec;
                }
            }
            $brand = wp_get_object_terms($all_product_id_val, 'pa_brand');
            if ($brand) {
                $brand_rec = get_v_s('Brand', $brand);
                if ($brand_rec) {
                    $arr[] = $brand_rec;
                }
            }
            $color = wp_get_object_terms($all_product_id_val, 'pa_color');
            if ($color) {
                $color_rec = get_v_s('Color', $color);
                if ($color_rec) {
                    $arr[] = $color_rec;
                }
            }
            $colors = wp_get_object_terms($all_product_id_val, 'pa_colors');
            if ($colors) {
                $colors_rec = get_v_s('Colors', $colors);
                if ($colors_rec) {
                    $arr[] = $colors_rec;
                }
            }
            $dimensions = wp_get_object_terms($all_product_id_val, 'pa_dimensions');
            if ($dimensions) {
                $dimensions_rec = get_v_s('Dimensions', $dimensions);
                if ($dimensions_rec) {
                    $arr[] = $dimensions_rec;
                }
            }
            $embroidery_tap_charge = wp_get_object_terms($all_product_id_val, 'pa_embroidery-charge');
            if ($embroidery_tap_charge) {
                $tap_charge_rec = get_v_s('Embroidery Tape Charge (up to 5k stitches)', $embroidery_tap_charge);
                if ($tap_charge_rec) {
                    $arr[] = $tap_charge_rec;
                }
            }
            $embroidery_run_charge = wp_get_object_terms($all_product_id_val, 'pa_embroidery-run');
            if ($embroidery_run_charge) {
                $embroidery_run_rec = get_v_s('Embroidery Run Charge', $embroidery_run_charge);
                if ($embroidery_run_rec) {
                    $arr[] = $embroidery_run_rec;
                }
            }
            $imprint_area = wp_get_object_terms($all_product_id_val, 'pa_imprint-area');
            if ($imprint_area) {
                $imprint_area_rec = get_v_s('Imprint Area', $imprint_area);
                if ($imprint_area_rec) {
                    $arr[] = $imprint_area_rec;
                }
            }
            $keywords = wp_get_object_terms($all_product_id_val, 'pa_keywords');
            if ($keywords) {
                $keywords_rec = get_v_s('Keywords', $keywords);
                if ($keywords_rec) {
                    $arr[] = $keywords_rec;
                }
            }
            $pms_color_match = wp_get_object_terms($all_product_id_val, 'pa_pms-match');
            if ($pms_color_match) {
                $pms_color_rec = get_v_s('PMS Color Match', $pms_color_match);
                if ($pms_color_rec) {
                    $arr[] = $pms_color_rec;
                }
            }
            $available_printing_method = wp_get_object_terms($all_product_id_val, 'pa_printing_method');
            if ($available_printing_method) {
                $printing_method_rec = get_v_s('Available Printing Method', $available_printing_method);
                if ($printing_method_rec) {
                    $arr[] = $printing_method_rec;
                }
            }
            $standard_production_time = wp_get_object_terms($all_product_id_val, 'pa_production-time');
            if ($standard_production_time) {
                $production_time_rec = get_v_s('Standard Production Time', $standard_production_time);
                if ($production_time_rec) {
                    $arr[] = $production_time_rec;
                }
            }
            $reorder_setup_fee = wp_get_object_terms($all_product_id_val, 'pa_reorder-setup');
            if ($reorder_setup_fee) {
                $reorder_setup_rec = get_v_s('Reorder Set-up Fee', $reorder_setup_fee);
                if ($reorder_setup_rec) {
                    $arr[] = $reorder_setup_rec;
                }
            }
            $run_charges = wp_get_object_terms($all_product_id_val, 'pa_run-charges');
            if ($run_charges) {
                $run_charges_rec = get_v_s('Run charges', $run_charges);
                if ($run_charges_rec) {
                    $arr[] = $run_charges_rec;
                }
            }
            $screen_print_setupcharge = wp_get_object_terms($all_product_id_val, 'pa_setupcharge');
            if ($screen_print_setupcharge) {
                $print_setupcharge_rec = get_v_s('Screen Print Set-up Charge (Per Color)', $screen_print_setupcharge);
                if ($print_setupcharge_rec) {
                    $arr[] = $print_setupcharge_rec;
                }
            }
            $shipping_weight = wp_get_object_terms($all_product_id_val, 'pa_shipping-weight');
            if ($shipping_weight) {
                $shipping_weight_rec = get_v_s('Shipping Weight', $shipping_weight);
                if ($shipping_weight_rec) {
                    $arr[] = $shipping_weight_rec;
                }
            }
            $size = wp_get_object_terms($all_product_id_val, 'pa_size');
            if ($size) {
                $size_rec = get_v_s('Size', $size);
                if ($size_rec) {
                    $arr[] = $size_rec;
                }
            }
            $heat_transfer_setup_charge = wp_get_object_terms($all_product_id_val, 'pa_transfer-setup');
            if ($heat_transfer_setup_charge) {
                $va = get_v_s('Heat Transfer Set-up Charge (1+ Colors)', $heat_transfer_setup_charge);
                if ($va) {
                    $arr[] = $va;
                }
            }
            $count_arr = count($arr);
            update_post_meta($all_product_id_val, 'addition_information', $count_arr);
            $i = 0;
            foreach ($arr as $arr_val) {
                $additiona_label_name = $arr_val[0];
                $additiona_value = $arr_val[1];
                update_post_meta($all_product_id_val, 'addition_information_' . $i . '_label', $additiona_label_name);
                update_post_meta($all_product_id_val, 'addition_information_' . $i . '_value', $additiona_value);
                $i++;
            }
        }
        die;
    }

    function get_v_s($additional_info_label, $additional_info_value) {
        if ($additional_info_value) {
            $additionalinfovalue = '';
            foreach ($additional_info_value as $additional_info_val) {
                if (!empty($additional_info_val->name)) {
                    $additionalinfovalue .= ', ' . $additional_info_val->name;
                    $additionalinfovalue = ($additionalinfovalue[0] == ',') ? substr_replace($additionalinfovalue, '', 0, 1) : $additionalinfovalue;
                }
            }
            return array($additional_info_label, $additionalinfovalue);
        }
        return false;
    }

}

if (isset($_GET['print_new_ids']) && !empty($_GET['print_new_ids'])) {
    add_action('init', 'wc_add_product_tax_detail');

    function wc_add_product_tax_detail() {
        $all_products = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        $all_new_product_ids = $all_products->posts;
        $new_label_arr = array(
            "Color" => '',
            "Imprint Area" => '',
            "Size" => '',
            "Standard Production Time" => '',
            "Box Dimensions" => '',
            "Shipping Weight" => '',
            "Available Printing Method" => '',
            "Screen Print Set-up Charge (Per Color)" => '',
            "Heat Transfer Set-up Charge (1+ Colors)" => '',
            "Run charges" => '',
            "Additional Colors" => '',
            "Reorder Set-up Fee" => '',
            "PMS Color Match" => ''
        );
        $args = array();
        $output = 'objects'; // or objects
        $operator = 'or'; // 'and' or 'or'
        $taxonomies = get_taxonomies($args, $output, $operator);
        foreach ($taxonomies as $tax_type_key => $taxonomy) {
            echo '<pre>';
            $args[$tax_type_key] = $taxonomy->labels->singular_name;
        }
//print_r( $args );
        foreach ($all_new_product_ids as $post->ID) {
            $new = array();
            $product = wc_get_product($post->ID);
            $product_data = $product->get_data();
            echo $post->ID;
            if ($product_data['attributes']) {
                foreach ($product_data['attributes'] as $key => $value) {
                    $is_visible = $value->get_data();
                    if ($is_visible['is_visible']) {

                        if ($is_visible['options']) {

                            $additionalinfovalue = '';
                            foreach ($is_visible['options'] as $a_key => $a_value) {
                                $a_data = get_term_by('id', $a_value, $key);

                                $additionalinfovalue .= ', ' . $a_data->name;
                            }
                            $additionalinfovalue = ($additionalinfovalue[0] == ',') ? substr_replace($additionalinfovalue, '', 0, 1) : $additionalinfovalue;
                            $new[$args[$key]] = $additionalinfovalue;
                        }
                    }
                }
            }

            $count_new_arr = count($new);
            update_post_meta($post->ID, 'addition_information', $count_new_arr);
            $i = 0;
            if ($count_new_arr) {
                foreach ($new as $new_filter_arr_key => $new_filter_val) {
                    echo $new_filter_arr_key . '---' . $new_filter_val . '<br>';
                    update_post_meta($post->ID, 'addition_information_' . $i . '_label', $new_filter_arr_key);
                    update_post_meta($post->ID, 'addition_information_' . $i . '_value', $new_filter_val);
                    $i++;
                }
            }
            /*  $addition_information_new = get_post_meta($post->ID, 'addition_information', true);
              for ($i = 0; $i < $addition_information_new; $i++) {
              $addition_information_new_lbl = get_post_meta($post->ID, 'addition_information_' . $i . '_label', true);
              $addition_information_new_val = get_post_meta($post->ID, 'addition_information_' . $i . '_value', true);
              foreach ($new as $key => $v) {
              if ($addition_information_new_lbl == $key) {
              $new_value = trim($addition_information_new_val);
              $new[$key] = $addition_information_new_val;
              }
              }
              }
              $new_filter_arr = array_filter($new);
              $count_new_arr = count($new_filter_arr);
              update_post_meta($post->ID, 'addition_information', $count_new_arr);
              $i = 0;
              if ($count_new_arr) {
              foreach ($new_filter_arr as $new_filter_arr_key => $new_filter_val) {
              /*echo $new_filter_arr_key.'---'. $new_filter_val.'<br>';
              update_post_meta($post->ID, 'addition_information_' . $i . '_label', $new_filter_arr_key);
              update_post_meta($post->ID, 'addition_information_' . $i . '_value', $new_filter_val);
              $i++;
              }
          } */
      }
  }

}


// add_action('save_post', 'webby_post');

function webby_post() {
    global $post;
    if ($post) {
        if ('product' == $post->post_type) {
            $args = array();
            $output = 'objects'; // or objects
            $operator = 'or'; // 'and' or 'or'
            $taxonomies = get_taxonomies($args, $output, $operator);
            foreach ($taxonomies as $tax_type_key => $taxonomy) {
                echo '<pre>';
                $args[$tax_type_key] = $taxonomy->labels->singular_name;
            }
            $new = array();
            $product = wc_get_product($post->ID);
            $product_data = $product->get_data();
            if ($product_data['attributes']) {
                foreach ($product_data['attributes'] as $key => $value) {
                    $is_visible = $value->get_data();
                    if ($is_visible['is_visible']) {

                        if ($is_visible['options']) {

                            $additionalinfovalue = '';
                            foreach ($is_visible['options'] as $a_key => $a_value) {

                                $a_data = get_term_by('id', $a_value, $key);
                                $additionalinfovalue .= ', ' . $a_data->name;
                            }
                            $additionalinfovalue = ($additionalinfovalue[0] == ',') ? substr_replace($additionalinfovalue, '', 0, 1) : $additionalinfovalue;
                            $new[$args[$key]] = $additionalinfovalue;
                        }
                    }
                }
            }

            $addition_information = get_field('addition_information', $post->ID);
            if ($addition_information) {
                foreach ($addition_information as $key => $value) {
                    if ($value['label'] == 'Additional colors') {
                        $new['Additional colors'] = $value['value'];
                    }
                }
            }
            $count_new_arr = count($new);
            update_post_meta($post->ID, 'addition_information', $count_new_arr);
            $i = 0;
            if ($count_new_arr) {
                foreach ($new as $new_filter_arr_key => $new_filter_val) {
                    echo $new_filter_arr_key . '---' . $new_filter_val . '<br>';
                    update_post_meta($post->ID, 'addition_information_' . $i . '_label', $new_filter_arr_key);
                    update_post_meta($post->ID, 'addition_information_' . $i . '_value', $new_filter_val);
                    $i++;
                }
            }
        }
    }
}

function inf_remove_junk() {
    if (is_singular('product')) {
        wp_dequeue_style('js_composer_front');
        wp_dequeue_style('js_composer_custom_css');
        wp_dequeue_script('wpb_composer_front_js');
        wp_dequeue_script('js_composer_front');
    }
}

add_action('wp_enqueue_scripts', 'inf_remove_junk', 99);

/** Disable Ajax Call from WooCommerce */
add_action('wp_enqueue_scripts', 'dequeue_woocommerce_cart_fragments', 11);

function dequeue_woocommerce_cart_fragments() {
    if (is_front_page())
        wp_dequeue_script('wc-cart-fragments');
}

add_action('admin_head', 'admin_header_style');
add_action('wp_head', 'admin_header_style');

function admin_header_style() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $user_id = $user->ID;

        if (in_array('product-editor', (array) $user->roles) || $user_id == 950) {
            ?>
            <style>
                #adminmenu > li,
                #wp-admin-bar-root-default > li,
                #dashboard-widgets-wrap,
                #adminmenu #menu-users li:nth-child(4){
                    display:none;
                }
                #adminmenu #menu-posts-product,
                #wp-admin-bar-root-default #wp-admin-bar-menu-toggle,
                #wp-admin-bar-root-default #wp-admin-bar-wp-logo,
                #wp-admin-bar-root-default #wp-admin-bar-site-name,
                #adminmenu #menu-users {
                    display: block;
                }
                #menu-users ul.wp-submenu li:last-child {
                    display: none;
                }
            </style>
            <?php
        }
    }
}

add_shortcode('order_status_list', 'wc_order_status_list');

function wc_order_status_list() {
    ob_start();
    ?>        
    <div class="order-records">

    </div>    
    <?php
    return ob_get_clean();
}

/*function add_rel_preload($html, $handle, $href, $media) {
if (is_admin())
    return $html;

$html = <<<EOT
<link rel='preload' as='style' onload="this.onload=null;this.rel='stylesheet'" 
id='$handle' href='$href' type='text/css' media='all' />
EOT;

return $html;
}

add_filter( 'style_loader_tag', 'add_rel_preload', 10, 4 );*/


add_filter( 'style_loader_src',  'sdt_remove_ver_css_js', 9999, 2 );
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999, 2 );

function sdt_remove_ver_css_js( $src, $handle ) 
{
    $handles_with_version = [ 'style' ]; // <-- Adjust to your needs!

    if ( strpos( $src, 'ver=' ) && ! in_array( $handle, $handles_with_version, true ) )
        $src = remove_query_arg( 'ver', $src );

    return $src;
}

function wc_validate_extra_register_fields( $username, $email, $validation_errors ) {
    if ( isset( $_POST['billing_name'] ) && empty( $_POST['billing_name'] ) ) {
        $validation_errors->add( 'billing_name_error', __( 'Name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_company'] ) && empty( $_POST['billing_company'] ) ) {
        $validation_errors->add( 'billing_company_error', __( 'Company is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_address'] ) && empty( $_POST['billing_address'] ) ) {
        $validation_errors->add( 'billing_address_error', __( 'Address is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_city'] ) && empty( $_POST['billing_city'] ) ) {
        $validation_errors->add( 'billing_city_error', __( 'City is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_state'] ) && empty( $_POST['billing_state'] ) ) {
        $validation_errors->add( 'billing_state_error', __( 'State is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_postcode'] ) && empty( $_POST['billing_postcode'] ) ) {
        $validation_errors->add( 'billing_postcode_error', __( 'Zip is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_country'] ) && empty( $_POST['billing_country'] ) ) {
        $validation_errors->add( 'billing_country_error', __( 'Country is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
        $validation_errors->add( 'billing_phone_error', __( 'Phone is required!', 'woocommerce' ) );
    }
    
    return $validation_errors;
}
add_action( 'woocommerce_register_post', 'wc_validate_extra_register_fields', 10, 3 );


function wc_save_extra_register_fields( $customer_id ) {
    if ( isset( $_POST['billing_name'] ) ) {
             //First name field which is by default
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_name'] ) );
             // First name field which is used in WooCommerce
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_name'] ) );
    }   
    if ( isset( $_POST['billing_phone'] ) ) {                 
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
    }
    if ( isset( $_POST['billing_company'] ) ) {                 
        update_user_meta( $customer_id, 'billing_company', sanitize_text_field( $_POST['billing_company'] ) );
    }   
    if ( isset( $_POST['billing_address'] ) ) {                 
        update_user_meta( $customer_id, 'billing_address_1', sanitize_text_field( $_POST['billing_address'] ) );
    }
    if ( isset( $_POST['billing_city'] ) ) {                 
        update_user_meta( $customer_id, 'billing_city', sanitize_text_field( $_POST['billing_city'] ) );
    }
    if ( isset( $_POST['billing_state'] ) ) {                 
        update_user_meta( $customer_id, 'billing_state', sanitize_text_field( $_POST['billing_state'] ) );
    }
    if ( isset( $_POST['billing_postcode'] ) ) {                 
        update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( $_POST['billing_postcode'] ) );
    }
    if ( isset( $_POST['billing_country'] ) ) {                 
        update_user_meta( $customer_id, 'billing_country', sanitize_text_field( $_POST['billing_country'] ) );
    }
    if ( isset( $_POST['reg_asi'] ) ) {                 
        update_user_meta( $customer_id, 'billing_asi', sanitize_text_field( $_POST['reg_asi'] ) );
    }
    if ( isset( $_POST['reg_ppai_upic'] ) ) {                 
        update_user_meta( $customer_id, 'billing_ppai_upic', sanitize_text_field( $_POST['reg_ppai_upic'] ) );
    }
    if ( isset( $_POST['reg_sage'] ) ) {                 
        update_user_meta( $customer_id, 'billing_sage', sanitize_text_field( $_POST['reg_sage'] ) );
    }
    if ( isset( $_POST['reg_dc'] ) ) {                 
        update_user_meta( $customer_id, 'billing_dc', sanitize_text_field( $_POST['reg_dc'] ) );
    }
    if ( isset( $_POST['reg_buying_group'] ) ) {                 
        update_user_meta( $customer_id, 'billing_buying_group', sanitize_text_field( $_POST['reg_buying_group'] ) );
    }
    if ( isset( $_POST['reg_receive_mrk_email'] ) ) {                 
        update_user_meta( $customer_id, 'billing_receive_mrk_email', sanitize_text_field( $_POST['reg_receive_mrk_email'] ) );
    }   
}
add_action( 'woocommerce_created_customer', 'wc_save_extra_register_fields' );


add_action( 'edit_user_profile', 'wc_custom_user_profile_fields',99,2 );
add_action( 'show_user_profile', 'wc_custom_user_profile_fields',99,2);
function wc_custom_user_profile_fields( $user )
{
    echo '<h3 class="heading">Extra Field</h3>';
    $user_id = $user->ID;
    $billing_asi = get_user_meta( $user_id, 'billing_asi', true );
    $billing_ppai_upic = get_user_meta( $user_id, 'billing_ppai_upic', true );
    $billing_sage = get_user_meta( $user_id, 'billing_sage', true );
    $billing_dc = get_user_meta( $user_id, 'billing_dc', true );
    $billing_buying_group = get_user_meta( $user_id, 'billing_buying_group', true );
    $billing_receive_mrk_email = get_user_meta( $user_id, 'billing_receive_mrk_email', true );
    ?>

    <table class="form-table">
        <tr>
            <th><label for="reg_asi">ASI#</label></th>
            <td>
                <input type="text" class="regular-text form-control" name="reg_asi" id="reg_asi" value="<?php echo ($billing_asi) ? $billing_asi : ''; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="reg_ppai_upic">PPAI / UPIC</label></th>
            <td>
                <input type="text" class="regular-text form-control" name="reg_ppai_upic" id="reg_ppai_upic" value="<?php echo ($billing_ppai_upic) ? $billing_ppai_upic : ''; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="reg_sage">SAGE</label></th>
            <td>
                <input type="text" class="regular-text form-control" name="reg_sage" id="reg_sage" value="<?php echo ($billing_sage) ? $billing_sage : ''; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="reg_dc">DC</label></th>
            <td>
                <input type="text" class="regular-text form-control" name="reg_dc" id="reg_dc" value="<?php echo ($billing_dc) ? $billing_dc : ''; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="reg_buying_group">Buying Group</label></th>
            <td>
                <input type="text" class="regular-text form-control" name="reg_buying_group" id="reg_buying_group" value="<?php echo ($billing_buying_group) ? $billing_buying_group : ''; ?>" />
            </td>
        </tr>
        <tr>
            <th><label for="reg_receive_mrk_email">Do you agree to receive marketing emails from Peerless Umbrella:</label></th>
            <td>
                <select  class="regular-text form-control" name="reg_receive_mrk_email" id="reg_receive_mrk_email">
                    <option class="yes" <?php echo ($billing_receive_mrk_email && $billing_receive_mrk_email == 'yes') ? 'selected' : ''; ?>>Yes</option>
                    <option class="no" <?php echo ($billing_receive_mrk_email && $billing_receive_mrk_email == 'no') ? 'selected' : ''; ?>>No</option>
                </select>               
            </td>
        </tr>
    </table>

    <?php
}

add_action( 'edit_user_profile_update', 'wc_save_custom_user_profile_fields' );

function wc_save_custom_user_profile_fields( $user_id )
{

    if ( isset( $_POST['reg_asi'] ) ) {                 
        update_user_meta( $user_id, 'billing_asi', sanitize_text_field( $_POST['reg_asi'] ) );
    }
    if ( isset( $_POST['reg_ppai_upic'] ) ) {                 
        update_user_meta( $user_id, 'billing_ppai_upic', sanitize_text_field( $_POST['reg_ppai_upic'] ) );
    }
    if ( isset( $_POST['reg_sage'] ) ) {                 
        update_user_meta( $user_id, 'billing_sage', sanitize_text_field( $_POST['reg_sage'] ) );
    }
    if ( isset( $_POST['reg_dc'] ) ) {                 
        update_user_meta( $user_id, 'billing_dc', sanitize_text_field( $_POST['reg_dc'] ) );
    }
    if ( isset( $_POST['reg_buying_group'] ) ) {                 
        update_user_meta( $user_id, 'billing_buying_group', sanitize_text_field( $_POST['reg_buying_group'] ) );
    }
    if ( isset( $_POST['reg_receive_mrk_email'] ) ) {                 
        update_user_meta( $user_id, 'billing_receive_mrk_email', sanitize_text_field( $_POST['reg_receive_mrk_email'] ) );
    }
}

add_action( 'woocommerce_after_edit_address_form_billing', 'add_favorite_color_to_edit_account_form' );
function add_favorite_color_to_edit_account_form() {
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $billing_asi = get_user_meta( $user_id, 'billing_asi', true );
    $billing_ppai_upic = get_user_meta( $user_id, 'billing_ppai_upic', true );
    $billing_sage = get_user_meta( $user_id, 'billing_sage', true );
    $billing_dc = get_user_meta( $user_id, 'billing_dc', true );
    $billing_buying_group = get_user_meta( $user_id, 'billing_buying_group', true );
    $billing_receive_mrk_email = get_user_meta( $user_id, 'billing_receive_mrk_email', true );
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_asi"><?php _e( 'ASI#', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="reg_asi" id="reg_asi" value="<?php echo ($billing_asi) ? $billing_asi : ''; ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_ppai_upic"><?php _e( 'PPAI / UPIC', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="reg_ppai_upic" id="reg_ppai_upic" value="<?php echo ($billing_ppai_upic) ? $billing_ppai_upic : ''; ?>" />
    </p>    
    <p class="form-row form-row-wide">
        <label for="reg_sage"><?php _e( 'SAGE', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="reg_sage" id="reg_sage" value="<?php echo ($billing_sage) ? $billing_sage : ''; ?>" />
    </p>    
    <p class="form-row form-row-wide">
        <label for="reg_dc"><?php _e( 'DC', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="reg_dc" id="reg_dc" value="<?php echo ($billing_dc) ? $billing_dc : ''; ?>" />
    </p>    
    <p class="form-row form-row-wide">
        <label for="reg_buying_group"><?php _e( 'Buying Group', 'woocommerce' ); ?></label>
        <input type="text" class="input-text" name="reg_buying_group" id="reg_buying_group" value="<?php echo ($billing_buying_group) ? $billing_buying_group : $_POST['reg_buying_group']; ?>" />
    </p>
    <p class="form-row form-row-wide">
        <label for="reg_receive_mrk_email"><?php _e( 'Do you agree to receive marketing emails from Peerless Umbrella:', 'woocommerce' ); ?></label>
        <select name="reg_receive_mrk_email" id="reg_receive_mrk_email" >
            <option value="yes" <?php echo ( $billing_receive_mrk_email == 'yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="no" <?php echo ( $billing_receive_mrk_email == 'no') ? 'selected' : ''; ?>>No</option>
        </select>
    </p>
    <?php
}

// Save the custom field 'favorite_color' 
add_action( 'woocommerce_customer_save_address', 'wc_woocommerce_customer_save_address', 12, 1 );
function wc_woocommerce_customer_save_address( $user_id ) {

    if ( isset( $_POST['reg_asi'] ) ) {                 
        update_user_meta( $user_id, 'billing_asi', sanitize_text_field( $_POST['reg_asi'] ) );
    }
    if ( isset( $_POST['reg_ppai_upic'] ) ) {                 
        update_user_meta( $user_id, 'billing_ppai_upic', sanitize_text_field( $_POST['reg_ppai_upic'] ) );
    }
    if ( isset( $_POST['reg_sage'] ) ) {                 
        update_user_meta( $user_id, 'billing_sage', sanitize_text_field( $_POST['reg_sage'] ) );
    }
    if ( isset( $_POST['reg_dc'] ) ) {                 
        update_user_meta( $user_id, 'billing_dc', sanitize_text_field( $_POST['reg_dc'] ) );
    }
    if ( isset( $_POST['reg_buying_group'] ) ) {                 
        update_user_meta( $user_id, 'billing_buying_group', sanitize_text_field( $_POST['reg_buying_group'] ) );
    }
    if ( isset( $_POST['reg_receive_mrk_email'] ) ) {                 
        update_user_meta( $user_id, 'billing_receive_mrk_email', sanitize_text_field( $_POST['reg_receive_mrk_email'] ) );
    }
}

add_action('wp_ajax_wc_share_inventory_result', 'wc_share_inventory_result');
add_action('wp_ajax_nopriv_wc_share_inventory_result', 'wc_share_inventory_result');
function wc_share_inventory_result() {
    if( isset($_POST) && !empty($_POST) ){
        $to = $_POST['email_add'];
        $subject = 'Inventory Results From Peerless';
        /*
        $body = trim($body);
        $body = str_replace("table","table cellpadding='5' cellspacing='5' style='border-collapse: collapse;border:1px solid black;'",$_POST['result']);
        $body = str_replace("<th>","<th style='border: 1px solid black;'>",$body);
        $body = str_replace("<td>","<td style='border: 1px solid black;'>",$body);*/

        if( isset( $_POST ) || !empty($_POST) ){
            $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
            $color = isset($_POST['color']) ? $_POST['color'] : '';
            $qty = isset($_POST['qty']) ? $_POST['qty'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $search = isset($_POST['search']) ? $_POST['search'] : '';
        }
        $curl = curl_init('https://job.peerlessumbrellamedia.com/api/getInventoryRecovered');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'token' => 'A5H#Fs^d8t7U',
            'product_id' => $product_id,
            'color' => $color,
            'quantity' => $qty,
            'in_stock' => $stock,
        ));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        if ($response) {
            $response = json_decode($response);
        }
        $html = '<table cellpadding="5" cellspacing="5" style="border-collapse: collapse;border:1px solid black;">';
        $html .= '<tr>
        <th style="border: 1px solid black;">Product Id</th>
        <th style="border: 1px solid black;">Thumbnail</th>
        <th style="border: 1px solid black;">Color</th>
        <th style="border: 1px solid black;">Quantity</th>
        <th style="border: 1px solid black;">Description</th>
        <th style="border: 1px solid black;">In Stock</th>
        <th style="border: 1px solid black;">Note</th>
        </tr>';
        if( $response->Inventory->recordsFiltered > 0 ){
            foreach ($response as $inventory) {
                if( isset($inventory->data) && !empty($inventory->data) ){
                    foreach( $inventory->data as $inventory_data ){
                      $html .= '<tr>';
                      $html .= '<td style="border: 1px solid black;" class="wc-sku-code"><a target="_blank" href="'.site_url('product/style-'.$inventory_data->item_id.'/').'" >'.$inventory_data->item_id.'</a></td>';
                      if(wc_get_product_id_by_sku($inventory_data->item_id) == 0){
                         $html .= '<td style="border: 1px solid black;" ><img width="100" height="100" src="'.site_url().'/wp-content/uploads/2022/04/notfound.png" ></td>';
                     }else{
                        $product_id = wc_get_product_id_by_sku($inventory_data->item_id);
                        $flag = 0;
                        $variatio_p_id = 0;
                        if( $product_id ){
                         $product = wc_get_product( $product_id );
                         if( is_object( $product ) ){
                            $variation_products =  $product->get_children();
                            foreach($variation_products as $variation_products_img){
                                $taxonomy = 'pa_color';
                                $meta = get_post_meta($variation_products_img, 'attribute_'.$taxonomy, true);
                                $inventory_color_replace = explode(" ",$inventory_data->color);
                                $str_replace = explode("-",$meta);
                                if(trim(strtolower($meta)) == trim(strtolower($inventory_data->color))){
                                    $flag = 1;
                                    $variatio_p_id = $variation_products_img;
                                }else if ( count($str_replace) > 1 ) {
                                 $str_1 = $str_replace[0].$str_replace[1];
                                 $str_2 =  $str_replace[1].$str_replace[0];

                                 $inventory_color_str1 = $inventory_color_replace[0].$inventory_color_replace[1];
                                 $inventory_color_str2 = $inventory_color_replace[1].$inventory_color_replace[0];
                                 if(trim(strtolower($str_1)) == trim(strtolower($inventory_color_str1))){
                                    $flag = 1;
                                    $variatio_p_id = $variation_products_img;
                                }else if(trim(strtolower($str_2)) ==  trim(strtolower($inventory_color_str2))){
                                    $flag = 1;
                                    $variatio_p_id = $variation_products_img;
                                }
                            }

                        }
                    }

                }

                if( $flag == 1 ){

                 if( $variatio_p_id ){
                    if( wp_get_attachment_image_url( get_post_thumbnail_id($variatio_p_id),"full" ) ){
                        $html .= '<td style="border: 1px solid black;" ><img width="100" height="100" src="'.wp_get_attachment_image_url( get_post_thumbnail_id($variatio_p_id),"full" ).'" ></td>';
                    }else{
                     $flag = 0; 
                 }
             }else{
                $flag = 0; 
            }
        }

        if( $flag == 0 ){
            if( wp_get_attachment_image_url( get_post_thumbnail_id(wc_get_product_id_by_sku($inventory_data->item_id)),"full" ) ){
                $html .= '<td style="border: 1px solid black;" ><img width="100" height="100" src="'.wp_get_attachment_image_url( get_post_thumbnail_id(wc_get_product_id_by_sku($inventory_data->item_id)),"full" ).'" ></td>';   
            }else{
              $html .= '<td style="border: 1px solid black;" ><img width="100" height="100" src="'.site_url().'/wp-content/uploads/2022/04/notfound.png" ></td>';       
          }

      }
  } 
  $html .= '<td style="border: 1px solid black;" >'.$inventory_data->color.'</td>';
  $html .= '<td style="border: 1px solid black;" >'.$inventory_data->quantity.'</td>';
  $html .= '<td style="border: 1px solid black;" >'.$inventory_data->description.'</td>';
  $html .= '<td style="border: 1px solid black;" >'.$inventory_data->in_stock.'</td>';
  $mas90_commentText = '-';
  if( $inventory_data->mas90_commentText ){
    $mas90_commentText = $inventory_data->mas90_commentText;
}
$html .= '<td style="border: 1px solid black;" >'.$mas90_commentText.'</td>';
$html .= '</tr>';
}    
}
}

}else{
    $html = '<tr><td colspan="6" style="text-align:center;">No Data Available</td></tr>';
}

$html .= '</table>';

    //$headers = array('Content-Type: text/html; charset=UTF-8');

$headers = array('Content-Type: text/html; charset=UTF-8','From:Peerless Umbrella <no-reply@peerlessumbrella.com>');
$sent = wp_mail( $to, $subject, $html, $headers);
if( $sent ){
    $msg = 'Successfully Sent!';
}else{
    $msg = 'Some issues occuring during sending the mail';
}

wp_send_json($msg);
}
}


// add_filter( 'woocommerce_get_catalog_ordering_args','custom_query_sort_args' );
function custom_query_sort_args() {

        // Sort by and order
    $current_order = ( isset( $_SESSION['orderby'] ) ) ? $_SESSION['orderby'] : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

    switch ( $current_order ) {
        case 'date' :
        $orderby = 'date';
        $order = 'desc';
        $meta_key = '';
        break;
        case 'price' :
        $orderby = 'meta_value_num';
        $order = 'asc';
        $meta_key = '_price';
        break;
        case 'title' :
        $orderby = 'meta_value';
        $order = 'asc';
        $meta_key = '_woocommerce_product_short_title';
        break;
        default :
        $orderby = 'menu_order title';
        $order = 'asc';
        $meta_key = '';         
        break;
    }

    $args = array();

    $args['orderby']        = $orderby;
    $args['order']          = $order;

    if ($meta_key) :
        $args['meta_key'] = $meta_key;
    endif;

        //print_r($args);

    return $args;
}


//add_action( 'woocommerce_product_query', 'product_query_by_price', 11, 1 );
function product_query_by_price( $q ) {

    if ( ! is_admin() && isset( $_GET['orderby'] ) && $_GET['orderby'] == 'price') {
       $q->query['orderby'] = 'price' ;
       $q->set( 'orderby', 'price' );
       $q->set( 'order', 'ASC' );
   }

   //print_r($q);
}

function woodmart_show_blog_results_on_search_page() {
    if ( ! is_search() || ! woodmart_get_opt( 'enqueue_posts_results' ) || ( is_search() && ( isset($_GET['post_type']) && $_GET['post_type'] == 'product' ) ) ) {
        return;
    }

    $search_query = get_search_query();
    $column = woodmart_get_opt( 'search_posts_results_column' );

    ob_start();

    ?>
    <div class="wd-blog-search-results">
        <h4 class="slider-title">
            <?php esc_html_e( 'Results from blog', 'woodmart' ); ?>
        </h4>
        
        <?php echo do_shortcode( '[woodmart_blog slides_per_view="' . $column . '" blog_design="carousel" search="' . $search_query . '" items_per_page="10"]' ); ?>
        
        <div class="wd-search-show-all">
            <a href="<?php echo esc_url( home_url() ) ?>?s=<?php echo esc_attr( $search_query ); ?>&post_type=post" class="button">
                <?php esc_html_e( 'Show all blog results', 'woodmart' ); ?>
            </a>
        </div>
    </div>
    <?php

    echo ob_get_clean();
}


function add_custom_pt( $query ) {
  if ( !is_admin() && $query->is_main_query() ) {
    if ( $query->is_search ) {
      //$query->set( 'post_type', array( 'post', 'the_custom_pt' ) );
      //print_r( $query);

    }
}
}
add_action( 'pre_get_posts', 'add_custom_pt' );


add_action('wp_head','single_custome_function');

function single_custome_function(){ ?>
    <style type="text/css">
        .wrap-wishlist-button {
            display: none;
        }   
        span.added-to-wishlist-text {
            font-size: 0;
        }
        .wd-wishlist-btn.wd-action-btn.wd-style-text.wd-wishlist-icon.woodmart-wishlist-btn 
        {
            display: none;
        }

        .term-umbrellas  .wd-bottom-actions  .wd-add-btn.wd-add-btn-replace.woodmart-add-btn,
        .archive .wd-bottom-actions  .wd-add-btn.wd-add-btn-replace.woodmart-add-btn,
        .page-template-default .wd-bottom-actions  .wd-add-btn.wd-add-btn-replace.woodmart-add-btn
        {
            display: flex;
            width: 100%;
            justify-content: space-between;
            margin-right: 20px;
        }

        .term-umbrellas  .wd-bottom-actions a.button.product_type_variable.add_to_cart_button.add-to-cart-loop,
        .archive  .wd-bottom-actions a.button.product_type_variable.add_to_cart_button.add-to-cart-loop,
        .page-template-default .wd-bottom-actions a.button.product_type_variable.add_to_cart_button.add-to-cart-loop
        {
            order: 2; 
        }

        .term-umbrellas  .wd-bottom-actions a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn,
        .archive .wd-bottom-actions a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn,
        .page-template-default .wd-bottom-actions a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn
        {
            order: 1;
            background: transparent;
            border: 0;
            box-shadow: none;
            color: #000;
            font-size: 20px;
        }

        .term-umbrellas .wd-bottom-actions a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn  span.add-to-wishlist-text,
        .archive  .wd-bottom-actions a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn  span.add-to-wishlist-text,
        .page-template-default .wd-bottom-actions a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn  span.add-to-wishlist-text
        {
            display: none;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .summary-inner{
            display: flex;
            flex-wrap: wrap;
        }

        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .single-breadcrumbs-wrapper{
            width: 100%;
            order: 1;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary  h1.product_title.wd-entities-title{
            width: 100%;
            order: 2;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary p.price{
            width: 100%;
            order: 3;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .woocommerce-product-details__short-description{

            width: 100%;
            order: 4;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary form.variations_form.cart.variation-swatch-selected{
            width: 100%;
            order: 5;
        }

        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn{
            background: none;
            order: 7;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn:hover {
            color: #33333399;
            box-shadow: none;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .wd-compare-btn.product-compare-button.wd-action-btn.wd-style-text.wd-compare-icon{
            order: 6;
            margin-bottom: 0;
            display: grid;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .wd-wishlist-btn.wd-action-btn.wd-style-text.wd-wishlist-icon.woodmart-wishlist-btn{
            width: 100%;
            order: 8;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .woocommerce-tabs.wc-tabs-wrapper.tabs-layout-accordion{
            width: 100%;
            order: 9;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .product-share{
            width: 100%;
            order: 10;
        }

        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn  span.add-to-wishlist-text {
            text-transform: none;
            font-weight: 600;
            font-size: 14px;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button  i.fa.fa-heart-o:before {
            content: "\f106";
            font-family: woodmart-font;
            letter-spacing: 5px;
        }
        .single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button  span.add-to-wishlist-text:after {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -9px;
            margin-left: -9px;
            width: 18px;
            height: 18px;
        }
        .home a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn {
            order: 1;
            background: transparent;
            border: 0;
            box-shadow: none;
            color: #000;
            font-size: 20px;
        }
        .home a.button.woocommerce-wishlist-add-product.btn.button.btn-default.theme-button.theme-btn .add-to-wishlist-text{
            display: none;
        }

        .wd-add-cart-icon>a:before{
            content: none;
        }
        .wd-add-btn a.button.product_type_variable.add_to_cart_button.add-to-cart-loop:before{
            content: "\f123";
            font-family: woodmart-font;
        }
        .home a.button.product_type_variable.add_to_cart_button.add-to-cart-loop{
            order: 2;
            border-left: 1px solid #0000001a;
            border-right: 1px solid #0000001a;

        }
        .home .wd-hover-base .wd-bottom-actions.wd-add-small-btn>div{
            border: none !important;
        }
        .home .wd-hover-base .wd-bottom-actions .wrap-quickview-button{
            flex: 0.5 0 0 !important;
        }
        .ajax-loaded .wd-hover-base .wd-bottom-actions.wd-add-small-btn .wd-add-btn{
            flex: 1.5 0 0 !important;
        }   

        .wd-header-wishlist.wd-tools-element.wd-style-icon.woodmart-wishlist-info-widget {
            display: none;
        }
        .single-product-page span.added-to-wishlist-text {
            padding-left: 5px;
            text-transform: none;
            font-weight: 600;
            font-size: 14px;
        }

        .wc-header-wishlist.wd-tools-element.favorites  span.wc-tools-icon.favorites{
            position: relative;
            font-size: 0;
        }

        .wc-header-wishlist.wd-tools-element.favorites span.wc-tools-text.favorites{
            display:none;
        }
        .wc-header-wishlist.wd-tools-element.favorites span.wc-tools-icon.favorites:before {
            font-size: 20px;
            content: "\f106";
            font-family: woodmart-font;
        }

        .page-id-3423439693 aside.sidebar-container, .page-id-3423439710 aside.sidebar-container{
            display: none;
        }
        .page-id-3423439693 .site-content,  .page-id-3423439710 .site-content {
            max-width: 100%;
            width: 100%;
            flex: auto;
        }
        .woocommerce-wishlist-container .woocommerce-wishlist-sidebar {
            width: 22%;
        }
        .woocommerce-wishlist-container .woocommerce-wishlist-items {
            width: 78%;
            padding-right: 0;
            padding-left: 40px;
        }
        .woocommerce-wishlist-sidebar h3 {
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .woocommerce-wishlist-sidebar ul {
            padding: 0;
            margin: 0;
        }

        .woocommerce-wishlist-sidebar ul li {
            list-style: none;
            margin-bottom: 15px;
            cursor: pointer;
            font-family: "Poppins", Arial, Helvetica, sans-serif;
            text-transform: capitalize;
        }

        .woocommerce-wishlist-sidebar ul li  i {
            width: 25px;
            height: 25px;
            border-radius: 50px;
            background: #00abc8;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-right: 5px;
            transition: all .4s;
        }

        .woocommerce-wishlist-actions a {
            color: #00abc8;
            text-decoration: underline !important;
            font-family: "Poppins", Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .woocommerce-wishlist-actions a:hover {
            color: #000;
        }

        .woocommerce-wishlist-sidebar ul li:hover i {
            background: #f5f5f5;
            color: #00abc8;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-header h3 {
            text-transform: capitalize;
        }


        .woocommerce-wishlist-items .woocommerce-wishlist-header .woocommerce-wishlist-header-actions a {
            background: #F3F3F3;
            padding: 7px 15px;
            border-radius: 50px;
            color: #009db7;
            font-family: 'Poppins';
            font-weight: 500;
            transition: all .4s;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-header .woocommerce-wishlist-header-actions a:hover {
            background: #009db7;
            color: #fff;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item {
            border: 1px solid #eee;
            display: flex;
            align-items: stretch;
            background: #f7f7f7;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item .woocommerce-wishlist-item-content {
            padding: 20px;
            font-family: 'Poppins';
            font-size: 13px;
            width: 100%
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item  a.woocommerce-wishlist-item-image {
            background: #ffffff;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30%;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item h4.woocommerce-wishlist-title {
            margin-bottom: 10px;
            display: inline-block;
        }


        .woocommerce-wishlist-items .woocommerce-wishlist-item h4.woocommerce-wishlist-title:hover {
            color: #00abc8;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item .woocommerce-wishlist-add-to-cart {
            right: 10px;
            bottom: 20px;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item .woocommerce-wishlist-add-to-cart a.single_add_to_cart_button {
            color: #FFF;
            background-color: #00abc8;
            box-shadow: none;
            font-family: 'Poppins';
            padding: 10px 15px;
        }

        .woocommerce-wishlist-items .woocommerce-wishlist-item .woocommerce-wishlist-add-to-cart a.single_add_to_cart_button:hover {
            box-shadow: inset 0 0 200px rgb(0 0 0 / 10%);
        }
        .woocommerce-wishlist-container .woocommerce-wishlist-items a.woocommerce-wishlist-remove-product {
            background: #82af82;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 12px;
            width: 22px;
            height: 22px;
            line-height: normal;
            font-family: 'Poppins';
            padding-right: 1px;
            transition: all .4s;
        }

        .woocommerce-wishlist-container .woocommerce-wishlist-items a.woocommerce-wishlist-remove-product:hover {
            background: #438E44;
        }
        .woocommerce-wishlist-meta .woocommerce-wishlist-categories a:hover {
            color: #00abc8;
        }
        .woocommerce-wishlist-container .woocommerce-wishlist-meta{
            max-width: 75%;
        }
        .wd-hover-base .wd-bottom-actions .wd-add-btn.woodmart-add-btn {
            border-left: 0;
        }
        .wd-hover-base .wd-bottom-actions.wd-add-small-btn .wd-action-btn>a{
            border-left: 1px solid #eee;
        }
        .wd-hover-base .wd-bottom-actions.wd-add-small-btn .wd-add-btn{
            flex: unset;
            width: 66%;
        }

        .home .wd-hover-base .wd-bottom-actions.wd-add-small-btn .wd-action-btn a.open-quick-view{
            border-left: none;
        }
        .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content {
            font-family: 'Poppins';
        }

        .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content select#woocommerce-wishlist-visibility {
            width: 33%;
            float: left;
        }

        .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content 
        input#woocommerce-wishlist-name {
            width: 62%;
            float: right;
        }

        .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content button#woocommerce-wishlist-create-button {
            color: #fff;
            background-color: #00abc8;
            width: 100%;
        }

        .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content button#woocommerce-wishlist-create-button:hover {
            box-shadow: inset 0 0 200px rgb(0 0 0 / 10%);
        }
        .archive .wd-bottom-actions .wd-add-btn.wd-add-btn-replace.woodmart-add-btn a.button.add_to_cart_button {
            width: 100%;
        }

        .woocommerce-cart .row.cart-actions .col-12.order-last.order-md-first.col-md .coupon
/*.woocommerce-cart  .cart-totals-section  .cart_totals .woocommerce-shipping-totals.shipping td ul li label*/ {
    display: none;
}

.cart-content-wrapper .woocommerce-pdf-catalog.link-wrapper {
    width: 100%;
    text-align: end;
    margin-bottom: 40px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}
.row.cart-actions .col-12.order-first.order-md-last.col-md-auto .button {
    background-color: #00abc8;
    color: #fff;
    font-weight: 500;
}
button.woocommerce-pdf-catalog-email-send.button.btn.btn-primary {
    margin-top: 10px;
    color: #fff;
    background-color: #00acc8;
}

.single-product .summary-inner .wc-custom-price .wc-sp-price {
    padding: 18px 0;
    font-size: 16px;
}

/*#custom-repeatable-fieldset-one tbody td.wc-repete-image span.wd-icon::before {
    content: '\f112';
    display: inline-block;
    vertical-align: middle;
    font-family: woodmart-font;
}*/

@media (max-width: 991px){

    .woocommerce-wishlist-container .woocommerce-wishlist-sidebar {
        width: 100%;
        float: none;
    }
    .woocommerce-wishlist-container .woocommerce-wishlist-items {
        width: 100%;
        padding-right: 0;
        padding-left: 0;
        margin-top: 30px;
        float: none;
    }

}

@media (max-width: 767px){
    .woocommerce-wishlist-items .woocommerce-wishlist-item{
        flex-wrap: wrap;
    }
    .woocommerce-wishlist-items .woocommerce-wishlist-item a.woocommerce-wishlist-item-image,
    .woocommerce-wishlist-items .woocommerce-wishlist-item .woocommerce-wishlist-item-content{
        width: 100%;
    }
    .woocommerce-wishlist-items .woocommerce-wishlist-item a.woocommerce-wishlist-item-image{
        padding: 15px;
        justify-content: flex-start;
    }
    .woocommerce-wishlist-items .woocommerce-wishlist-item a.woocommerce-wishlist-item-image img {
        max-width: 200px;
        margin-left: 0;
    }
}

@media (max-width: 575px){
    .woocommerce-wishlist-items .woocommerce-wishlist-item h4.woocommerce-wishlist-title{
        font-size: 18px;
    }
    .woocommerce-wishlist-add-to-cart{
        position: unset;
        margin-top: 15px;
    }
    .woocommerce-wishlist-container .woocommerce-wishlist-meta{
        max-width: 100%;
        float: none;
    }
    .woocommerce-wishlist-items .woocommerce-wishlist-item{
        text-align: center;
    }
    .woocommerce-wishlist-items .woocommerce-wishlist-item a.woocommerce-wishlist-item-image{
        justify-content: center;
    }
    .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content select#woocommerce-wishlist-visibility, .woocommerce-wishlist-modal .woocommerce-wishlist-modal-content input#woocommerce-wishlist-name {
        width: 100%;
        float: none;
        margin-bottom: 15px;
    }
}

</style>
<?php
}

add_action('wp_footer','wc_accoudian_slider_func');
function wc_accoudian_slider_func(){
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function () { 
       jQuery('.vc_tta-panel').removeClass('vc_active'); 
       jQuery(document).on('click', '.wc_accodian .vc_tta-panel a[data-vc-accordion]', function(e) {
        e.preventDefault();
        jQuery('.vc_tta-panel').removeClass('vc_active');
        jQuery(this).parents('.vc_tta-panel').addClass('vc_active');
    });

       jQuery(document).on("click",'.wd-tools-element.wd-header-mobile-nav',function(){
        if( jQuery('.mobile-nav.wd-side-hidden.wd-left.wd-opener-arrow').length > 0 ){
            jQuery('.mobile-nav.wd-side-hidden.wd-left.wd-opener-arrow').removeClass('wd-opened').addClass('wd-opened');
        }
    });
   });
</script>
<?php
}

add_action( 'widgets_init', 'err_override_woocommerce_widgets', 15 );

function err_override_woocommerce_widgets() {
  // Ensure our parent class exists to avoid fatal error (thanks Wilgert!)

    if ( class_exists( 'WC_Widget_Layered_Nav' ) ) {

        unregister_widget( 'WC_Widget_Layered_Nav' );

        include_once( 'wc-custom-widget-layer-nav.php' );

        register_widget( 'Custom_WC_Widget_Layered_Nav' );
    }

}


// add_action( 'pre_get_posts', 'woocommerce_pre_get_posts', 20 );
// function woocommerce_pre_get_posts( $query ) {
//     global $wpdb;
//     $sql = "SELECT MAX(meta_value), post_id from {$wpdb->prefix}postmeta where meta_key = '_price'";
//     $result = $wpdb->get_results($sql);
//     $_price = get_post_meta( $result[0]->post_id, '_price', true );

//     if( is_shop() || is_post_type_archive( 'product' ) ){
//         $arr = [];
//         $arr['relation'] = 'AND';
//         if( isset($_REQUEST['min_price']) && !empty($_REQUEST['min_price']) ){
//             $arr[] = array(
//                 array(
//                     'key' => '_price',
//                     'value' => $_REQUEST['min_price'],
//                     'compare' => '>=',
//                     'type' => 'NUMERIC',
//                 ),
//             );
//         }
//         $min_price = ( isset($_REQUEST['min_price']) && !empty( $_REQUEST['min_price'] ) ) ? $_REQUEST['min_price']: 0;
//         $max_price = ( isset($_REQUEST['max_price']) && !empty( $_REQUEST['max_price'] ) ) ? $_REQUEST['max_price']: $_price;
//         if( isset($_REQUEST['max_price']) && !empty($_REQUEST['max_price']) ){
//             $arr[] = array(
//                 'key' => '_price',
//                 'type' => 'NUMERIC',
//                 'value' => array( $min_price, $max_price ),
//                 'compare' => 'BETWEEN'

//             );
//         }
//         $query->set('meta_query', $arr );
//     }
// }


add_action( 'pre_get_posts', 'woocommerce_pre_get_posts', 20 );
function woocommerce_pre_get_posts( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Ensure WooCommerce functions are available
    if ( function_exists('is_shop') && ( is_shop() || is_post_type_archive( 'product' ) ) ) {

        global $wpdb;

        // Get maximum _price value
        $sql = "SELECT MAX(meta_value) as max_price, post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_price'";
        $result = $wpdb->get_row($sql);
        $_price = isset($result->max_price) ? floatval($result->max_price) : 0;

        $arr = [];
        $arr['relation'] = 'AND';

        // Min price filter
        if( isset($_REQUEST['min_price']) && $_REQUEST['min_price'] !== '' ){
            $arr[] = array(
                'key' => '_price',
                'value' => floatval($_REQUEST['min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        $min_price = ( isset($_REQUEST['min_price']) && $_REQUEST['min_price'] !== '' ) ? floatval($_REQUEST['min_price']) : 0;
        $max_price = ( isset($_REQUEST['max_price']) && $_REQUEST['max_price'] !== '' ) ? floatval($_REQUEST['max_price']) : $_price;

        // Max price filter
        if( $max_price > 0 ){
            $arr[] = array(
                'key' => '_price',
                'type' => 'NUMERIC',
                'value' => array( $min_price, $max_price ),
                'compare' => 'BETWEEN'
            );
        }

        $query->set('meta_query', $arr );
    }
}


/* Custom Post type Rescoures */

add_action( 'init', 'wc_custom_quote_post_init' );

function wc_custom_quote_post_init() {
    $labels = array(
        'name'               => _x( 'Quotes', 'post type general name', 'your-plugin-textdomain' ),
        'singular_name'      => _x( 'Quote', 'post type singular name', 'your-plugin-textdomain' ),
        'menu_name'          => _x( 'Quotes', 'admin menu', 'your-plugin-textdomain' ),
        'name_admin_bar'     => _x( 'Quote', 'add new on admin bar', 'your-plugin-textdomain' ),
        'add_new'            => _x( 'Add New', 'Quote', 'your-plugin-textdomain' ),
        'add_new_item'       => __( 'Add New Quote', 'your-plugin-textdomain' ),
        'new_item'           => __( 'New Quote', 'your-plugin-textdomain' ),
        'edit_item'          => __( 'Edit Quote', 'your-plugin-textdomain' ),
        'view_item'          => __( 'View Quote', 'your-plugin-textdomain' ),
        'all_items'          => __( 'All Quotes', 'your-plugin-textdomain' ),
        'search_items'       => __( 'Search Quotes', 'your-plugin-textdomain' ),
        'parent_item_colon'  => __( 'Parent Quotes:', 'your-plugin-textdomain' ),
        'not_found'          => __( 'No quotes found.', 'your-plugin-textdomain' ),
        'not_found_in_trash' => __( 'No quotes found in Trash.', 'your-plugin-textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'your-plugin-textdomain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'quote' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'thumbnail')
    );

    register_post_type( 'quotes', $args );
}

function quote_product_list_loop($selected = ''){
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    $loop = new WP_Query( $args );
    $product_html = '';

    ob_start();
    if ( $loop->have_posts() ) {
        echo '<option value=""></option>';
        while ( $loop->have_posts() ) { $loop->the_post();
            $product_id = get_the_ID();
            $pricing_values  = get_post_meta( $product_id, '_as_quantity_range_pricing_values', true );
            $product_title = get_the_title();
            $product = wc_get_product( $product_id );
            $price = $product->get_price();
            $sku = $product->get_sku();;

            $select = '';
            if( $selected == $product_id ){
                $select = 'selected';
            }
            ?>
            <option value="<?php echo $product_id; ?>" data-sku="<?php echo $sku; ?>" data-link="<?php echo get_permalink( $product_id ); ?>" data-pricing='<?php echo json_encode($pricing_values); ?>' data-currency="<?php echo get_woocommerce_currency_symbol(); ?>" data-price="<?php echo $price; ?>" <?php echo $select; ?> ><?php echo $product_title; ?></option>
            <?php
        }
    } 
    wp_reset_postdata();
    $product_html .= ob_get_clean();
    return $product_html;
}

/* //--------// Single Product Category Page Hide Tabs  //---------// */
/* //-------------------------// //---------------------------//  */
add_filter( 'body_class', function( $classes ) {
    if( is_product() ){
        global $product;
        $cate_ids = $product->get_category_ids();
        if( in_array( 3119, $cate_ids ) ) {
            $classes[] = 'patio-market-umbrella-hide';
        } 
    }
    return $classes;
});

//-----------------------------------------------------------------------
// Custom Save Function For Quotes
//-----------------------------------------------------------------------
function wc_save_quotes_fileds( $serialized_arr, $type, $flag = 0 ){

    $attach_id = isset( $serialized_arr['file_id'] ) ? $serialized_arr['file_id'] : 0;  
    $to_your_name = isset( $serialized_arr['to_your_name'] ) ? $serialized_arr['to_your_name'] : '';
    $post_id = isset( $serialized_arr['update_data'] ) ? base64_decode($serialized_arr['update_data']) : '';
    $custom_invoice_data  = isset($serialized_arr['custom_invoice_data']) ? $serialized_arr['custom_invoice_data'] : '';

    if( isset( $_FILES['file'] ) ){
        $response_data = array();
        $fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);    
        $fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);   
        $upload_dir = wp_upload_dir();

        $_filter = true; 
        $guest_id = time().rand(1,3);

        add_filter( 'upload_dir', function( $arr ) use( &$_filter, &$user_id ){
            if ( $_filter ) {               
                if( $user_id ){
                    $folder = '/invoice/'.$user_id;     
                }else{
                    $folder = '/invoice/';
                }           
                $arr['path'] = $arr['basedir'].$folder;
                $arr['url'] = $arr['baseurl'].$folder;  
            }
            return $arr;
        } );
        $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

        $send_type = 'clients';
        if( empty($user_id) ){
            $user_id = $guest_id;   
            $send_type = 'guest';
        }
        if( $upload ){
            $file_url = $upload['url'];
            $file_path = $upload['file'];   
            $wp_filetype = wp_check_filetype( $fileName, '' );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name( $fileName ),
                'post_content' => '',
                'post_status' => 'inherit',
                'post_author' => $user_id
            );
            $attach_id = wp_insert_attachment( $attachment, $file_path );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );   
            wp_update_attachment_metadata( $attach_id, $attach_data );  

        }
    }

    $my_post = array(
        'post_type'     => 'quotes',
        'post_title'    => wp_strip_all_tags( $to_your_name ),
        "post_name"     => wp_strip_all_tags( $to_your_name ),
        'post_status'   => 'publish',
        'post_author'   =>  $user_id,
    );

    if( $post_id ){
        $my_post['ID'] = $post_id;
        wp_update_post( $my_post );
    } else {
        $post_id = wp_insert_post( $my_post );
    }

    $attch_array = array();
    if( $flag == 1 ){
        if( $custom_invoice_data ){
            foreach ($custom_invoice_data as $value) {  
                $image_url = isset( $value['wc-hidden-pro-image'] ) ? $value['wc-hidden-pro-image'] : '';
                $attach_ids = isset( $value['product_file_id'] ) ? $value['product_file_id'] : '';
                if(  $attach_ids == '' ){
                    $file_id  = wc_google_file_upload( $image_url, $post_id );    
                } else {
                    $file_id  = $attach_ids;
                }
                $value['convert_ids'] = $file_id;
                $attch_array[] = $value;
            }
        }
        $serialized_arr['custom_invoice_data'] = $attch_array;
    }

    update_post_meta( $post_id, 'logo_id', $attach_id ); 
    update_post_meta( $post_id, 'quotes_extra_data', $serialized_arr );
    update_post_meta( $post_id, 'quotes_status', 0 );
    update_post_meta( $post_id, 'quotes_type', $type );
}

//-----------------------------------------------------------------------
// Custom Save Function For Quotes
//-----------------------------------------------------------------------
function wc_quotes_file_upload( $folder_name ){

    if( isset( $_FILES['file'] ) ){
        $response_data = array();
        $fileName = preg_replace('/\s+/', '-', $_FILES["file"]["name"]);    
        $fileName = preg_replace('/[^A-Za-z0-9.\-]/', '', $fileName);   
        $upload_dir = wp_upload_dir();

        $_filter = true; 
        $guest_id = time().rand(1,3);

        add_filter( 'upload_dir', function( $arr ) use( &$_filter, &$user_id, &$folder_name ){
            if ( $_filter ) {               
                if( $user_id ){
                    $folder = '/'.$folder_name.'/'.$user_id;     
                }else{
                    $folder = '/'.$folder_name.'/';
                }           
                $arr['path'] = $arr['basedir'].$folder;
                $arr['url'] = $arr['baseurl'].$folder;  
            }
            return $arr;
        } );
        $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));

        $send_type = 'clients';
        if( empty($user_id) ){
            $user_id = $guest_id;   
            $send_type = 'guest';
        }
        if( $upload ){
            $file_url = $upload['url'];
            $file_path = $upload['file'];   
            $wp_filetype = wp_check_filetype( $fileName, '' );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name( $fileName ),
                'post_content' => '',
                'post_status' => 'inherit',
                'post_author' => $user_id
            );
            $attach_id = wp_insert_attachment( $attachment, $file_path );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );   
            wp_update_attachment_metadata( $attach_id, $attach_data );      
        }
    }

    return $attach_id;
}

//-----------------------------------------------------------------------------------------------------
// Admin Single Product page add custom fileds
//-----------------------------------------------------------------------------------------------------
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );
function woo_add_custom_general_fields() {

  global $woocommerce, $post;

  echo '<div class="options_group">';

  woocommerce_wp_text_input( 
    array( 
        'id'          => '_custom_sale_field', 
        'label'       => __( 'Custom Special Price', 'woocommerce' ), 
        'placeholder' => '',
        'desc_tip'    => 'true',
        'description' => __( 'Enter the special price here.', 'woocommerce' ) 
    )
);

  echo '</div>';

}

//-----------------------------------------------------------------------------------------------------
// Admin Single Product page save custom fileds
//-----------------------------------------------------------------------------------------------------
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );
function woo_add_custom_general_fields_save( $post_id ){
    $woocommerce_custom_sale_field = $_POST['_custom_sale_field'];
    if( isset( $_POST['_custom_sale_field'] ) ){
        update_post_meta( $post_id, '_custom_sale_field', esc_attr( $woocommerce_custom_sale_field ) ); 
    }
}

//-----------------------------------------------------------------------------------------------------
// Admin Single Product page show frontend custom fileds
//-----------------------------------------------------------------------------------------------------
add_action( 'woocommerce_before_variations_form', 'wc_woocommerce_before_variations_form', 11 );
function wc_woocommerce_before_variations_form(){
    global $product;
    $post_id = $product->get_id();
    $custom_sale_field = get_post_meta( $post_id, '_custom_sale_field', true ); 
    ?>
    <div class="wc-custom-price">
        <?php
        if( $custom_sale_field ) { ?>
            <div class="wc-sp-price">Special price: <span><?php echo wc_price($custom_sale_field); ?></span></div>
            <?php
        }
        ?>
    </div>
    <?php
} 

//-----------------------------------------------------------------------------------------------------
// Woo Quantity Range Action
//-----------------------------------------------------------------------------------------------------
//add_action('woocommerce_before_shop_loop_item_title', 'wc_woocommerce_before_shop_loop_item_title' );
function wc_woocommerce_before_shop_loop_item_title(){ 
    global $product;

    $product_id = $product->get_id();
    $terms = get_the_terms($product_id, 'product_cat');
    $data_load = 0;
    $bag_id = true;
    if ($terms) {
        foreach ($terms as $term) {
            if ($term->slug == 'bags') {
                $data_load = 1;
            }
            if ($term->term_id == 17) {
                $bag_id = false;
            }
        }
    }
    $_product = wc_get_product($product_id);
    $variable_data = array();
    $as_woo_qrp_label = get_post_meta($product_id, '_as_quantity_range_pricing_label', true);
    if ($_product->is_type('simple')) {
        $as_woo_qrp_enable = get_post_meta($product_id, '_as_quantity_range_pricing_enable', true);
        if (!empty($as_woo_qrp_enable)) {
            $as_quantity_rage_values = get_post_meta($product_id, '_as_quantity_range_pricing_values', true);
            if ($as_quantity_rage_values) {
                $as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order($as_quantity_rage_values);
            }
            $variable_data[$product_id] = $as_quantity_rage_values;
        }
    } else {
        $available_variations = $_product->get_available_variations();

        foreach ($available_variations as $key => $value) {
            $as_woo_qrp_enable = get_post_meta($value['variation_id'], '_as_quantity_range_pricing_enable', true);
            if (!empty($as_woo_qrp_enable)) {
                $as_quantity_rage_values = get_post_meta($value['variation_id'], '_as_quantity_range_pricing_values', true);
                if ($as_quantity_rage_values) {
                    $as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order($as_quantity_rage_values);
                }
                $variable_data[$value['variation_id']] = $as_quantity_rage_values;
            }
            $as_woo_qrp_label = get_post_meta($value['variation_id'], '_as_quantity_range_pricing_label', true);
        }
    }

    if ($variable_data) {
        ?>
        <script type="text/javascript">
            var complex_<?php echo $product_id; ?> = <?php echo json_encode($variable_data); ?>;
            var v_label_<?php echo $product_id; ?> = '<?php echo $as_woo_qrp_label; ?>';
            var product_bags_<?php echo $product_id; ?> = '<?php echo $data_load; ?>';
            var symbol_currency = '<?php echo get_woocommerce_currency_symbol(); ?>';
        </script>
        <?php
    }
}

//--------------------------------------------------
//  Google Analytics
//--------------------------------------------------
add_action( 'wp_head', 'wc_google_analytics_fun' );
function wc_google_analytics_fun(){
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-114594422-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-114594422-1');
    </script>
    <?php
}


//-------------------------------------------------------------------------------------------------------------
//  Chnage Sale Label Function
//-------------------------------------------------------------------------------------------------------------
add_filter( 'woocommerce_sale_flash', 'woodmart_product_label', 10 );
if ( ! function_exists( 'woodmart_product_label' ) ) {
    function woodmart_product_label() {

        global $product;

        $output = array();

        $product_attributes = woodmart_get_product_attributes_label();
        $percentage_label   = woodmart_get_opt( 'percentage_label' );

        if ( $product->is_on_sale() ) {

            $percentage = '';

            if ( $product->get_type() == 'variable' && $percentage_label ) {

                $available_variations = $product->get_variation_prices();
                $max_percentage       = 0;

                foreach ( $available_variations['regular_price'] as $key => $regular_price ) {
                    $sale_price = $available_variations['sale_price'][ $key ];

                    if ( $sale_price < $regular_price ) {
                        $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

                        if ( $percentage > $max_percentage ) {
                            $max_percentage = $percentage;
                        }
                    }
                }

                $percentage = $max_percentage;
            } elseif ( ( $product->get_type() == 'simple' || $product->get_type() == 'external' || $product->get_type() == 'variation' ) && $percentage_label ) {
                $percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
            }

            if ( $percentage ) {
                //$output[] = '<span class="onsale product-label">-' . $percentage . '%' . '</span>';
                $output[] = '<span class="onsale product-label">' . esc_html__( 'Sale', 'woodmart' ) . '</span>';
            } else {
                $output[] = '<span class="onsale product-label">' . esc_html__( 'Sale', 'woodmart' ) . '</span>';
            }
        }

        if ( ! $product->is_in_stock() ) {
            $output[] = '<span class="out-of-stock product-label">' . esc_html__( 'Sold out', 'woodmart' ) . '</span>';
        }

        if ( $product->is_featured() && woodmart_get_opt( 'hot_label' ) ) {
            $output[] = '<span class="featured product-label">' . esc_html__( 'Hot', 'woodmart' ) . '</span>';
        }

        if ( woodmart_get_opt( 'new_label' ) && woodmart_is_new_label_needed( get_the_ID() ) ) {
            $output[] = '<span class="new product-label">' . esc_html__( 'New', 'woodmart' ) . '</span>';
        }

        if ( $product_attributes ) {
            foreach ( $product_attributes as $attribute ) {
                $output[] = $attribute;
            }
        }

        $output = apply_filters( 'woodmart_product_label_output', $output );

        if ( $output ) {
            echo '<div class="product-labels labels-' . woodmart_get_opt( 'label_shape' ) . '">' . implode( '', $output ) . '</div>';
        }
    }
}

include 'class-custom-shortcode.php';

add_action( 'woocommerce_single_product_summary', 'ssswoodmart_add_to_compare_single_btn', 33 );
function ssswoodmart_add_to_compare_single_btn(){
    global $product;

    $url        = woodmart_get_compare_page_url();
    $product_id = apply_filters( 'wpml_object_id', $product->get_id(), 'product', true, apply_filters( 'wpml_default_language', null ) );

    if ( woodmart_get_opt( 'compare_by_category' ) ) {
        $url = add_query_arg( 'product_id', $product_id, $url );
    }

    woodmart_enqueue_js_script( 'woodmart-compare' );

    ?>
    <div class="wd-compare-btn product-compare-button wd-action-btn wd-style-text wd-compare-icon">
        <a href="<?php echo esc_url( $url ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>" rel="nofollow" data-added-text="<?php esc_html_e( 'Compare products', 'woodmart' ); ?>">
            <span><?php esc_html_e( 'Compare', 'woodmart' ); ?></span>
        </a>
    </div>
    <?php
}


add_action('wp_head','custom_country_wiesh_open_data_css');

function custom_country_wiesh_open_data_css(){
    ?>
    <style type="text/css">
        .country-forms {
            position: relative;
            max-width: 500px;
        }
        .country-forms h1{
            text-align: center;
            padding: 20px;
        }

        .country-forms button.mfp-close {
            position: absolute;
            top: 0;
            right: 0;
        }

        .country-forms .form-langvage {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-evenly;
        }
        form#country-form {
            border-radius: 10px;
            max-width: 600px;
        }
        form#country-form button.mfp-close {
            top: 4px;
            right: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            background: #00acc8;
            height: 35px;
            border-radius: 4px;
        }
        form#country-form button.mfp-close:hover{
            background-color: #000;
        }
        form#country-form button.mfp-close:after {
            font-size: 15px;
            content: "\f112";
            font-family: "woodmart-font";
            color: white;
        }
        form#country-form h1 {
            border-bottom: 2px solid #00acc8;
            padding: 15px;
            font-family: 'Lato';
            font-size: 25px;
            margin-bottom: 0px;
        }
        form#country-form .country-main-content .form-langvage {
            gap: 20px;
            align-items: center;
            padding: 20px !important;
            border: 0 !important;
            min-height: 250px;
        }
        .country-forms .form-langvage a.glink {
            border: 2px solid #00acc8;
            padding: 6px;
            border-radius: 6px;
        }
        .country-forms .form-langvage a.glink img {
            width: 100% !important;
            max-width: 150px;
        }
        .country-forms .form-langvage a.glink:hover {
            border: 2px solid black;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }

    </style>
    <?php
}

add_action( 'wp', 'set_country_data_as_canada_post' );
function set_country_data_as_canada_post(){


    $country_val = '';
    global $wpdb;
    $user_id = 0;

    if( get_current_user_id() ){
        $user_id = get_current_user_id();
    }

    $client = @$_SERVER['HTTP_X_REAL_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];
    $result = array(
        'ip' => '',
        'country' => '',
        'countryCode' => '',

    );
    $ip = '';
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } 

    $country_val = get_custom_transient('client_ip_country');

    if( empty($country_val) ){
        $current_country = get_search_analytics_user_data();
        if( isset( $current_country['country'] ) && !empty($current_country['country']) ){
            $country_val = $current_country['country'];   
            set_custom_transient( 'client_ip_country', $country_val);    
        }
    }

    $not_home_page = 0;   
    if ( is_front_page() && is_home() || is_front_page() ) {
        $not_home_page = 1;
    }

    if( $not_home_page == 0 ){
        global $wp; 
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $currentURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $currentURL = parse_url( $currentURL );
        if (isset($currentURL['query'])) {
            unset($currentURL['query']); 
        }
        $current_url = buildUrl($currentURL);
        if ( $current_url == get_site_url() || $current_url == get_site_url().'/' ) { 
            $not_home_page = 1;          
        } 
    }

  /*  if( $not_home_page == 1 ){
        $browser_name = get_custom_current_pages_browser_name();
        $data = array();
        $table_name = $wpdb->prefix . 'current_server_log';
        $data['ip'] = $ip;
        $data['user_id'] = $user_id;
        $data['country'] = $country_val;
        $data['all_continent'] = '';
        $data['browser_name'] = $browser_name;
        $data['entry_date'] = current_time('mysql');
        $result_qry = $wpdb->insert($table_name, $data);
    }*/
}

function buildUrl($parsedUrl) {
    $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
    $host     = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
    $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
    $user     = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
    $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass']  : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
    $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

    return "$scheme$user$pass$host$port$path$query$fragment";
}

add_action('wp_footer','custom_country_wiesh_open_data');

function custom_country_wiesh_open_data(){

    /*$country_val = '';
    global $wpdb;
    $user_id = 0;

    if( get_current_user_id() ){
        $user_id = get_current_user_id();
    }

    $client = @$_SERVER['HTTP_X_REAL_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];
    $result = array(
        'ip' => '',
        'country' => '',
        'countryCode' => '',

    );
    $ip = '';
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } 
*/
    $country_val = get_custom_transient('client_ip_country');

    if( empty($country_val) ){
        $current_country = get_search_analytics_user_data();
        if( isset( $current_country['country'] ) && !empty($current_country['country']) ){
            $country_val = $current_country['country'];   
        //$_SESSION['client_country'] = $current_country['country'];
            set_custom_transient( 'client_ip_country', $country_val);    
        }
    }

/*if(isset( $_SESSION['client_country'] ) && !empty( $_SESSION['client_country'] ) ){
    $country_val = $_SESSION['client_country'];
}else{
    $current_country = get_search_analytics_user_data();
    if( isset( $current_country['country'] ) && !empty($current_country['country']) ){
        $country_val = $current_country['country'];   
        $_SESSION['client_country'] = $current_country['country'];
    }
}*/

/*    $value = wp_cache_get( 'client_ip_country', 'wpdocs_mygroup', false, $found );

    if ( ! $found) {
        $value = wpdocs_my_expensive_function();
        wp_cache_set( 'client_ip_country', $value, 'wpdocs_mygroup' );
    }*/
/*
    if ( is_front_page() && is_home() || is_front_page() ) {

        $browser_name = get_custom_current_pages_browser_name();
        $data = array();
        $table_name = $wpdb->prefix . 'current_server_log';
        $data['ip'] = $ip;
        $data['user_id'] = $user_id;
        $data['country'] = $country_val;
        $data['all_continent'] = '';
        $data['browser_name'] = $browser_name;
        $data['entry_date'] = current_time('mysql');
        $result_qry = $wpdb->insert($table_name, $data);
    }

    if( isset($_GET['sk_test']) && !empty($_GET['sk_test']) ){
     echo $country_val;
     print_r($result_qry );
     die;

 }*/

 $not_home_page = 0;   
 if ( is_front_page() && is_home() || is_front_page() ) {
    $not_home_page = 1;
}

if( $not_home_page == 0 ){
    global $wp; 
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $currentURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $currentURL = parse_url( $currentURL );
    if (isset($currentURL['query'])) {
        unset($currentURL['query']); 
    }
    $current_url = buildUrl($currentURL);
    if ( $current_url == get_site_url() || $current_url == get_site_url().'/' ) { 
        $not_home_page = 1;          
    } 
}
if( isset($_GET['test']) ){
    echo $not_home_page.'------'.$country_val;
}

if( $country_val &&  $not_home_page == 1 && ( strtolower(trim($country_val)) == 'canada' ) || ( isset($_GET['sk']) && !empty($_GET['sk']) ) ){
    ?>
    <a class="country-form-popup" href="#country-form" style="display:none;">Open form</a>
    <?php
}

?>
<form id="country-form" class="mfp-hide white-popup-block country-forms" <?php echo !empty( $country_val ) ?  $country_val  : ''; ?>>
    <h1>Please Choose Pricing</h1>
    <div class="country-main-content">
        <div class="form-langvage" style="padding: 10px; border-right: 1px solid #45bfd4; border-left: 1px solid #45bfd4;">
            <a class="glink nturl notranslate" title="English" href="#" choose="English">
                <img style="width: 100px;" src="https://peerlessumbrella.com/staging/wp-content/uploads/2024/04/usa-flag.webp" alt="English" />
            </a>
            <a class="glink nturl notranslate" title="Canada" href="#" choose="Canada">
                <img style="width: 100px;" src="https://peerlessumbrella.com/staging/wp-content/uploads/2024/04/canada.webp" alt="French" />
            </a>
        </div>
    </div>
</form>
<?php
}

function get_search_analytics_user_data( $browser_name = ''){

    $client = @$_SERVER['HTTP_X_REAL_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];
    $result = array(
        'ip' => '',
        'country' => '',
        'countryCode' => '',

    );
    $ip = '';
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } 

   // $ip = get_client_ip();
//update_option('my_ip',$ip);
    // $ip  = "45.91.23.68";
    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip ));

    if ($ip_data && isset($ip_data->geoplugin_countryName) && $ip_data->geoplugin_countryName != '') {
        //update_option('my_ip_data',$ip_data);
        $result = array(
            'ip' => $ip_data->geoplugin_request,
            'country' => $ip_data->geoplugin_countryName,
            'countryCode' => $ip_data->geoplugin_countryCode,

        );
    } else {
        $result = array(
            'ip' => $ip,
            'country' => '',
            'countryCode' => '',

        );
    }

    if (!empty($ip_data)) {
     $result['ip_data'] = serialize($ip_data);
 }

 return $result;
}

function get_search_analytics_all_data( $browser_name = ''){

    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];
    $result = array(
        'ip' => '',
        'country' => '',
        'countryCode' => '',

    );
    $ip = '';
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    $ip = get_client_ip();

    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip ));

    if (!empty($ip_data)) {
        $ip_data = serialize($ip_data);
    }
    
    return $ip_data;
}



function get_client_ip() {

    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
     $ipaddress = getenv('HTTP_FORWARDED');
 else if(getenv('REMOTE_ADDR'))
    $ipaddress = getenv('REMOTE_ADDR');
else
    $ipaddress = 'UNKNOWN';
return $ipaddress;
}

function get_custom_current_pages_browser_name() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser_name = "Unknown";

    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) {
        $browser_name = 'Opera';
    } elseif (strpos($user_agent, 'Edge')) {
        $browser_name = 'Microsoft Edge';
    } elseif (strpos($user_agent, 'Chrome')) {
        $browser_name = 'Google Chrome';
    } elseif (strpos($user_agent, 'Safari')) {
        $browser_name = 'Safari';
    } elseif (strpos($user_agent, 'Firefox')) {
        $browser_name = 'Mozilla Firefox';
    } elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) {
        $browser_name = 'Internet Explorer';
    }

    return $browser_name;
}


add_action('init',function(){
    if( isset($_GET['load_datas']) ){
        echo '<pre>';
        $ip_data = get_search_analytics_user_data();
        print_r( unserialize($ip_data['ip_data']));
        echo '</pre>';
        die;
    }
});

function get_custom_transient($transit_name = ''){
    $transient_array = '';
    $transient_array = wp_cache_get($transit_name ,$_SERVER['HTTP_USER_AGENT']);
    return $transient_array;
}

function delete_custom_transient($transit_name = ''){
    wp_cache_delete($transit_name , $_SERVER['HTTP_USER_AGENT'] );
}

function set_custom_transient($transit_name = '' , $transit_value = ''){
    wp_cache_set($transit_name,$transit_value , $_SERVER['HTTP_USER_AGENT']);
}

// Set default catalog sorting to date
function set_default_catalog_orderby( $sort_by ) {
    return 'date';
}
add_filter( 'woocommerce_default_catalog_orderby', 'set_default_catalog_orderby' );

// Modify the WooCommerce product query to order by date
function custom_woocommerce_product_query( $query ) {
    if ( ! is_admin() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
}
add_action( 'woocommerce_product_query', 'custom_woocommerce_product_query' );

add_action('woodmart_after_product_content', 'wc_sk_custom_function_before_related_products');

function wc_sk_custom_function_before_related_products(){
   global $post;
   if ($post) {
    $content = $post->post_content; 
    echo apply_filters('the_content', $content);
}
}


add_action('woodmart_before_single_product_summary_wrap','custom_woodmart_before_single_product_summary_wrap');


function custom_woodmart_before_single_product_summary_wrap(){
    global $product;

    if (!$product || $product->get_type() != 'variable') {
        return new WP_Error('no_product', 'Invalid product', array('status' => 404));
    }

    $variations = $product->get_available_variations();
    $variations_json = json_encode($variations);
    ?>

    <input type="hidden" id="available_variations" value='<?php echo $variations_json; ?>'>
    <?php

}

add_action('wp_enqueue_scripts', 'wc_custom_child_sk_enqueue_styles', 99999);

function wc_custom_child_sk_enqueue_styles() {

   wp_enqueue_style( 'wc_custom_sk_global_style', get_stylesheet_directory_uri() . '/css/wc-custom-sk-global.css', array(), time());
   wp_enqueue_script('wc_custom_sk_global_js', get_stylesheet_directory_uri() . '/js/wc-custom-sk-global.js', array(), time(), true);

   if ( is_singular( 'product' ) ) {

    wp_enqueue_style( 'wc_swiper_style', get_stylesheet_directory_uri() . '/css/swiper-bundle.min.css', array(), time());
    // wp_enqueue_style( 'wc_custom_sk_style', get_stylesheet_directory_uri() . '/css/wc-custom-sk.css', array(), time());

    wp_enqueue_script('wc_swiper_magnific-popup-js', get_stylesheet_directory_uri() . '/js/swiper-bundle.min.js', array(), '1.0.0', true);
    wp_enqueue_script('wc_custom_sk_js', get_stylesheet_directory_uri() . '/js/wc-custom-sk.js', array(), time(), true);
    wp_enqueue_script('wc-product-custom.js', get_stylesheet_directory_uri() . '/js/wc-product-custom.js', array(), '1.0.0'.time(), true);
}
}


add_action('wp_ajax_update_text_block', 'update_text_block_callback');
add_action('wp_ajax_nopriv_update_text_block', 'update_text_block_callback');

function update_text_block_callback() {

    if(isset( $_GET['post_id'] ) ){

      $product_id =  $_GET['post_id'] ;
// $product = wc_get_product($product_id);
//     $product_description = $product->get_description();
      $content = get_post_field('post_content', $product_id);
      echo $content;
  }
}

function custom_product_ordering( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_taxonomy() ) ) {
       
    //    if( isset( $_GET['sktest'] ) ){
    //     print_r($query);
    //    }
       
        if ( isset( $_GET['wc_orderby'] ) ) {
            switch ( $_GET['wc_orderby'] ) {
                case 'price':
                    $query->set( 'meta_key', '_price' );
                    $query->set( 'orderby', 'meta_value_num' );
                    $query->set( 'order', 'ASC' ); // Change to DESC for high to low
                    break;
                case 'price-desc':
                    $query->set( 'meta_key', '_price' );
                    $query->set( 'orderby', 'meta_value_num' );
                    $query->set( 'order', 'DESC' );
                    break;
                case 'rating':
                    $query->set( 'meta_key', '_wc_average_rating' );
                    $query->set( 'orderby', 'meta_value_num' );
                    $query->set( 'order', 'DESC' );
                    break;
                case 'popularity':
                    $query->set( 'meta_key', 'total_sales' );
                    $query->set( 'orderby', 'meta_value_num' );
                    $query->set( 'order', 'DESC' );
                    break;
                case 'date':
                default:
                    $query->set( 'orderby', 'date' );
                    $query->set( 'order', 'DESC' );
                    break;
            }
        }
    }
}
add_action( 'pre_get_posts', 'custom_product_ordering',9999 );

// add_action( 'pre_get_posts', 'custom_product_query' );
function custom_product_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_taxonomy() ) ) {

        // Only apply custom sorting if no 'orderby' parameter is set in the URL
        if ( ! isset( $_GET['wc_orderby'] ) ) {
            // Log for debugging
            error_log('No orderby parameter, setting custom price sorting.');

            // Set sorting by price (meta_key = '_price') in ascending order
            $query->set( 'meta_key', '_price' );
            $query->set( 'orderby', 'meta_value_num' );
            $query->set( 'order', 'ASC' );
        }
    }
}


add_action( 'woocommerce_before_single_product_summary', 'woodmart_product_video_button', 20 );
remove_action( 'woodmart_on_product_image', 'woodmart_product_video_button', 30 );

// 	// Add your version
// 	add_action( 'woodmart_on_product_image', 'my_custom_product_video_button', 20 );


function my_custom_product_video_button() {
	$video_url = get_post_meta( get_the_ID(), '_woodmart_product_video', true );

	if ( ! $video_url ) {
		return;
	}

	woodmart_enqueue_js_library( 'magnific' );
	woodmart_enqueue_js_script( 'product-video' );
	woodmart_enqueue_inline_style( 'mfp-popup' );
	?>
		<div class="product-video-button wd-gallery-btn">
			<a href="<?php echo esc_url( $video_url ); ?>">
				<span><?php esc_html_e( 'Watch video', 'woodmart' ); ?></span>
			</a>
		</div>
	<?php
}

add_action( 'wp_footer', 'custom_video_button_display_script' );
function custom_video_button_display_script() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        var $videoBtn = $('.product-video-button a');
        if (!$videoBtn.attr('href') || $videoBtn.attr('href').trim() === '') {
            $videoBtn.closest('.product-video-button').hide();
        } else {
            $videoBtn.closest('.product-video-button').show();
        }
    });
    </script>
    <?php
}