<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       akashsoni.com
 * @since      1.0.0
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Products_Quantity_Range_Pricing
 * @subpackage Woo_Products_Quantity_Range_Pricing/public
 * @author     Akash Soni <soniakashc@gmail.com>
 */
class Woo_Products_Quantity_Range_Pricing_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

    	$this->plugin_name = $plugin_name;
    	$this->version = $version;

    	$woo_qrp_enable = get_option('woo_qrp_enable', false);

    	if ($woo_qrp_enable) {

            // This action use for apply quantity range price in cart items.
    		add_action('woocommerce_before_calculate_totals', array(
    			$this,
    			'woo_product_quantity_range_prices_apply_in_cart',
    		));

            // This action use for show list of quality range price details on product detail page.
          add_action('woocommerce_before_add_to_cart_form', array(
            $this,
            'wc_show_quantity_range_price_list_table',
        ),15,0);  

            // Ajax action for victor user get product quantity range price table.
          add_action('wp_ajax_nopriv_wc_get_select_variation_quantity_pricing_table', array(
           $this,
           'wc_show_quantity_range_price_list_table',
       ));

            // Ajax action for login user get product quantity range price table.
          add_action('wp_ajax_wc_get_select_variation_quantity_pricing_table', array(
           $this,
           'wc_show_quantity_range_price_list_table',
       ));

          add_action('wp_footer', array(
           $this,
           'as_script',
       ));

          add_action( 'woocommerce_cart_item_price', array(
            $this,
            'wc_change_woocommerce_cart_item_price',
        ), 99, 3 );

          add_action('wp_head', array(
             $this,
             'wp_all_product_price'
         ));
      }
  }

  public function wp_all_product_price() {
   global $post;
   if ($post->post_type === 'product') {
      $terms = get_the_terms($post->id, 'product_cat');
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
   $_product = wc_get_product($post->ID);
   $variable_data = array();
   $as_woo_qrp_label = get_post_meta($post->ID, '_as_quantity_range_pricing_label', true);
   if ($_product->is_type('simple')) {
     $as_woo_qrp_enable = get_post_meta($post->ID, '_as_quantity_range_pricing_enable', true);
     if (!empty($as_woo_qrp_enable)) {
        $as_quantity_rage_values = get_post_meta($post->ID, '_as_quantity_range_pricing_values', true);
        if ($as_quantity_rage_values) {
           $as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order($as_quantity_rage_values);
       }
       $variable_data[$post->ID] = $as_quantity_rage_values;
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
    var complex = <?php echo json_encode($variable_data); ?>;
    var v_label = '<?php echo $as_woo_qrp_label; ?>';
    var product_bags = '<?php echo $data_load; ?>';
    var symbol_currency = '<?php echo get_woocommerce_currency_symbol(); ?>';
</script>
<?php
}
if (!$bag_id) {
 ?>
 <style type="text/css">

 </style>
 <?php
}
}
}

public function as_script() {
   global $post, $product;
   $product_cat = '';
   $product_cat_ids = array();
   if ($product) {
      $product_cat = wp_get_post_terms($product->id, 'product_cat');
      if (!empty($product_cat)) {
         foreach ($product_cat as $product_cat_item) {
            if ( $product_cat_item->parent == 192 ) {                        
                $product_cat_ids[] = $product_cat_item->term_id;
                $product_cat_ids[] = $product_cat_item->parent;
            }
        }
    }
}
$loader_img = plugin_dir_url(__FILE__) . 'images/loading_spinner.gif';
?>
<style type="text/css">
  .single-product .variation_quantity_table {
     display: none !important;
 }
</style>
<script>
  var after_price_box_tex = '<?php echo get_field('after_price_box_tex', $post->ID); ?>';
  var loader_img = '<?php echo $loader_img; ?>';
  function readCookie(name) {
     var nameEQ = encodeURIComponent(name) + "=";
     var ca = document.cookie.split(';');
     for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ')
           c = c.substring(1, c.length);
       if (c.indexOf(nameEQ) === 0)
           return decodeURIComponent(c.substring(nameEQ.length, c.length));
   }
   return null;
}

jQuery(document).ready(function () {
 jQuery('.single-product form.cart').before('<div class="loader-div"><img src=' + loader_img + '></div>');
 jQuery('.product-images-inner').after('<div class="loader-div"><img src=' + loader_img + '></div>');


 jQuery(document).on('click', '.as-tabs .nav .nav-item .nav-link', function () {
    jQuery('.as-tabs .nav .nav-item').removeClass('active');
    jQuery(this).parent().addClass('active');
    jQuery('.as-tabs .tab-content .tab-pane').removeClass('active');
    jQuery(jQuery(this).attr('href')).addClass('active');
    return false; 
});
 setTimeout(function () {
    if (readCookie('lang') == 'canada') {
       jQuery('.langvage a[title="Canada"]').click();
   }
}, 1000);
 setTimeout(function () {
    jQuery('.single-product .loader-div').remove();
    jQuery('.woodmart-product-brands').show();
    // jQuery('.single-product .variation_quantity_table').attr('style','display: block !important;');
    // jQuery('.single-product .variation_quantity_table').css({'order': '5 !important'});

}, 2000);

 if( jQuery('body').hasClass('single-product') ){
     jQuery(document).on('change', '.variation_id', function () {
        var data_product_id = jQuery('input[name="product_id"]').val();
        var data_variation_id = jQuery('input[name="variation_id"]').val();
        var data_qty_field = tab_price_tab_one_2 = tab_price_tab_three_2 = tab_price_tab_one = tab_price_tab_sub_one = tab_price_tab_two_2 = tab_price_tab_sub_two = tab_price_tab_one_sec = tab_price_tab_two_sec = tab_price_tab_two = tab_price_sec_tab_three = tab_price_tab_three = '';
        var price_data = 0;
        if( complex ){
            jQuery.each(complex, function (key, value) {
               if (value != '') {
                  var v_data_id = 0;
                  if( data_variation_id != 0 ) {
                    v_data_id = data_variation_id;
                } else {
                    v_data_id = data_product_id;
                }

                if( v_data_id == key ) {
                 var tab_header = '';
                 var tab_price = 0;
                 var sale_flag = 0;
                 var special_flag = 0;
                 jQuery.each(value, function (v_key, v_value) {
                    if (v_value.min_qty) {
                        tab_price++;
                        tab_header += '<th>' + v_value.min_qty + '</th>';
                        var price_d = (v_value.cnd_price * 1.32);
                        var fix_price = parseFloat(v_value.price).toFixed(2);
                        var fix_cnd_price = parseFloat(v_value.cnd_price).toFixed(2); 

                        if( parseFloat(v_value.sales_price) && parseFloat(v_value.sales_price) > 0 ) {
                            fix_price = '<del style="text-decoration-color: red;">' + parseFloat(v_value.price).toFixed(2) + '</del>';
                            var sales_price =  parseFloat(v_value.sales_price).toFixed(2);
                            sale_flag = 1;
                        } else {
                            var sales_price = '-';
                        }

                        if( parseFloat(v_value.special_price) && parseFloat(v_value.special_price) > 0 ) {
                            fix_cnd_price = '<del style="text-decoration-color: red;">' + parseFloat(v_value.cnd_price).toFixed(2) + '</del>';
                            var special_price =  parseFloat(v_value.special_price).toFixed(2);
                            special_flag = 1;
                        } else {
                            var special_price = '-';
                        }

                        tab_price_tab_one += '<td data-price="' + parseFloat(v_value.price).toFixed(2) + '" >'+ fix_price +'</td>';
                        tab_price_tab_sub_one += '<td data-price="' + parseFloat(v_value.sales_price).toFixed(2) + '" >'+ sales_price +'</td>';

                        tab_price_tab_three += '<td data-price="' + price_d.toFixed(2) + '" > ' + price_d.toFixed(2) + ' </td>';

                        if( v_value.display_blank_price == 1 ){
                            tab_price_tab_two += '<td></td>';
                        } else {
                            tab_price_tab_two += '<td>' + fix_cnd_price + '</td>';
                            tab_price_tab_sub_two += '<td>' + special_price + '</td>';
                        }    

                        tab_price_tab_two_2 += '<td> ' + parseFloat(v_value.sec_cnd_price).toFixed(2) + ' </td>';

                        if( v_value.display_blank_price == 1 ){
                            tab_price_tab_one_2 += '<td>  </td>';
                        } else {
                            tab_price_tab_one_2 += '<td> ' + parseFloat(v_value.sec_price).toFixed(2) + ' </td>';
                        }
                        var ds_2 = (v_value.sec_cnd_pricece * 1.32);
                        tab_price_tab_three_2 += '<td> ' + parseFloat(ds_2).toFixed(2) + ' </td>';

                    }
                });

                 var tab_price_tab_sub_one_html = '';
                 if( tab_price_tab_sub_one != '' && sale_flag == 1 ){
                    tab_price_tab_sub_one_html = '<tr><td>Special</td>' + tab_price_tab_sub_one + '</tr>';
                }  

                var tab_price_tab_sub_two_html = '';
                if( tab_price_tab_sub_two != '' && special_flag == 1 ){
                    tab_price_tab_sub_two_html = '<tr><td>Special</td>' + tab_price_tab_sub_two + '</tr>';
                }

                var data_sec = '';
                var cha = '(P)';
                var d_D = '';
                var t_sec = '';
                var tabs = '';
                var div_tabs = '';
                if( product_bags == '0' ) {
                    if( v_label ) {
                       cha = '(' + v_label + ')';
                   }
                   var c_d = '';
                   if( cha == '(C)' ) {
                       c_d = '(C)';
                   } else if( cha == '(P)' ) {
                       c_d = '(A)';
                   }
                   tabs += '<li data-load="<?php echo json_encode($product_cat_ids); ?>" class="nav-item english-nav active english-nav" >' +
                   '<a href="#tab-one" id="tab-one-title" class="nav-link" data-toggle="tab">US Pricing</a>' +
                   '</li>';
                   tabs += '<li class="nav-item ">' +
                   '<a href="#tab-two" id="tab-two-title" class="nav-link" data-toggle="tab">FOB: Canada (USD)</a>' +
                   '</li>';
                   tabs += '<li class="nav-item franch-nav" >' +
                   '<a href="#tab-three" id="tab-three-title" class="nav-link" data-toggle="tab">CND Currency</a>' +
                   '</li>';
                   <?php if ( $product_cat_ids ) { ?>
                       tabs += '<li class="nav-item english-nav franch-nav price-nav" >' +
                       '<a href="#tab-four" id="tab-three-title" class="nav-link" data-toggle="tab">Imprint Charges</a>' +
                       '</li>';
                   <?php } ?>

                   div_tabs += '<div id="tab-one" class="tab-pane active">' +
                   '<div class="wpb_text_column wpb_content_element ">' +
                   '<div class="wpb_wrapper">' +
                   '<h2></h2>' +
                   '<table class="table" width="100px">' +
                   '<tbody>' +
                   '<tr><th>Qty</th>' + tab_header + '</tr>' +
                   '<tr><td>' + tab_price + cha + '</td>' + tab_price_tab_one + '</tr>' +
                   tab_price_tab_sub_one_html +
                   '</tbody>' +
                   '</table>' +
                   '' + data_sec + '' +
                   '</div>' +
                   '</div>' +
                   '</div>';
                   div_tabs += '<div id="tab-two" class="tab-pane">' +
                   '<div class="wpb_text_column wpb_content_element ">' +
                   '<div class="wpb_wrapper">' +
                   '<h2></h2>' +
                   '<table class="table" width="100px" style="margin-bottom: 10px;" >' +
                   '<tbody>' +
                   '<tr><th>Qty</th>' + tab_header + '</tr>' +
                   '<tr><td>' + tab_price + c_d + '</td>' + tab_price_tab_two + '</tr>' +
                   tab_price_tab_sub_two_html +
                   '</tbody>' +
                   '</table> ' + d_D + '' +
                   '<p>Canadian Program is billed in US$. FOB: Canadian Border. Duty & Brokerage is included.</p>' +
                   '</div>' +
                   '</div>' +
                   '</div>';
                   div_tabs += '<div id="tab-three" class="tab-pane">' +
                   '<div class="wpb_text_column wpb_content_element ">' +
                   '<div class="wpb_wrapper">' +
                   '<h2></h2>' +
                   '<table class="table" width="100px" style="margin-bottom: 10px;" >' +
                   '<tbody>' +
                   '<tr><th>Qty</th> ' + tab_header + '</tr>' +
                   '<tr><td>' + tab_price + c_d + '</td>' + tab_price_tab_three + '</tr>' +
                   '</tbody>' +
                   '</table>' +
                   '' + t_sec + '' +
                   '<p>Pricing above is in Canadian dollars and is provided for information purposes only – pricing changes daily based on currency exchange rates. Peerless will BILL at the Canadian program price in USA dollars.</p>' +
                   '</div>' +
                   '</div>' +
                   '</div>';
                   <?php 
                   if ( $product_cat_ids ) { ?>
                       div_tabs += '<div id="tab-four" class="tab-pane">' +
                       '<div class="wpb_text_column wpb_content_element ">' +
                       '<div class="wpb_wrapper">' +
                       '<h2>Number of Colors in the logo?</h2>' +
                       '<ul class="color-numbers" ><li class="active" >1</li><li>2</li><li>3</li><li>4</li></ul>' +
                       '<h2>How many panels will be printed?</h2>' +
                       '<ul class="panel-numbers" ><li class="active" >1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li></ul>' +
                       '<h2>Your Pricing:</h2>' +
                       '<table class="table english-nav" width="100px" style="margin-bottom: 10px;" >' +
                       '<tbody>' +
                       '<tr><th>Qty</th> ' + tab_header + '</tr>' +
                       '<tr><th>' + tab_price + cha + '</th>' + tab_price_tab_one + '</tr>' +
                       tab_price_tab_sub_one_html +
                       '</tbody>' +
                       '</table>' +
                       '<table class="table franch-nav" width="100px" style="margin-bottom: 10px;" >' +
                       '<tbody>' +
                       '<tr><th>Qty</th> ' + tab_header + '</tr>' +
                       '<tr><th>' + tab_price + c_d + '</th>' + tab_price_tab_three + '</tr>' +
                       '</tbody>' +
                       '</table>' +
                       '' + data_sec + '' +
                       '<p>Pricing shown is based on the same decoration on each panel. For pricing on multi-panel imprints with different logos/design, please contact us. Setup charges are not included and are an additional fee.</p>' +
                       '<br>' +	
                       '</div>' +
                       '</div>' +
                       '</div>';
                   <?php } ?>
               } else if( product_bags == '1' ) {
                cha = '(C)';
                if( v_label ) {
                   cha = '(' + v_label + ')';
               }
               tabs += '<li class="nav-item english-nav active english-nav" >' +
               '<a href="#tab-one" id="tab-one-title" class="nav-link" data-toggle="tab">US Pricing Decorated</a>' +
               '</li>';
               tabs += '<li class="nav-item english-nav active english-nav" >' +
               '<a href="#tab-one-2" id="tab-one-title" class="nav-link" data-toggle="tab">US Pricing Blank</a>' +
               '</li>';
               tabs += '<li class="nav-item franch-nav">' +
               '<a href="#tab-two" id="tab-two-title" class="nav-link" data-toggle="tab">CND Program Decorated</a>' +
               '</li>';
               tabs += '<li class="nav-item franch-nav">' +
               '<a href="#tab-two-2" id="tab-two-title" class="nav-link" data-toggle="tab">CND Program Blank</a>' +
               '</li>';
               tabs += '<li class="nav-item franch-nav" >' +
               '<a href="#tab-three" id="tab-three-title" class="nav-link" data-toggle="tab">CND Currency Decorated</a>' +
               '</li>';
               tabs += '<li class="nav-item franch-nav" >' +
               '<a href="#tab-three-2" id="tab-three-title" class="nav-link" data-toggle="tab">CND Currency Blank</a>' +
               '</li>';
               div_tabs += '<div id="tab-one" class="tab-pane active">' +
               '<div class="wpb_text_column wpb_content_element ">' +
               '<div class="wpb_wrapper">' +
               '<h2></h2>' +
               '<table class="table" width="100px">' +
               '<tbody>' +
               '<tr><th>Qty</th>' + tab_header + '</tr>' +
               '<tr class="kk"><td>' + tab_price + cha + '</td>' + tab_price_tab_one + '</tr>' +
               tab_price_tab_sub_one_html +
               '</tbody>' +
               '</table>' +
               '' + data_sec + '' +
               '</div>' +
               '</div>' +
               '</div>';
               div_tabs += '<div id="tab-one-2" class="tab-pane active">' +
               '<div class="wpb_text_column wpb_content_element ">' +
               '<div class="wpb_wrapper">' +
               '<h2></h2>' +
               '<table class="table" width="100px">' +
               '<tbody>' +
               '<tr><th>Qty</th>' + tab_header + '</tr>' +
               '<tr><td>' + tab_price + cha + '</td>' + tab_price_tab_one_2 + '</tr>' +
               '</tbody>' +
               '</table>' +
               '' + data_sec + '' +
               '</div>' +
               '</div>' +
               '</div>';
               div_tabs += '<div id="tab-two" class="tab-pane">' +
               '<div class="wpb_text_column wpb_content_element ">' +
               '<div class="wpb_wrapper">' +
               '<h2></h2>' +
               '<table class="table" width="100px">' +
               '<tbody>' +
               '<tr><th>Qty</th>' + tab_header + '</tr>' +
               '<tr><td>' + tab_price + cha + '</td>' + tab_price_tab_two + '</tr>' +
               tab_price_tab_sub_two_html +
               '</tbody>' +
               '</table> ' + d_D + '' +
               '<p>Canadian Program is billed in US$. FOB: Canadian Border. Duty & Brokerage is included.</p></div>' +
               '</div>' +
               '</div>';
               div_tabs += '<div id="tab-two-2" class="tab-pane">' +
               '<div class="wpb_text_column wpb_content_element ">' +
               '<div class="wpb_wrapper">' +
               '<h2></h2>' +
               '<table class="table" width="100px">' +
               '<tbody>' +
               '<tr><th>Qty</th>' + tab_header + '</tr>' +
               '<tr><td>' + tab_price + cha + '</td>' + tab_price_tab_two_2 + '</tr>' +
               '</tbody>' +
               '</table> ' + d_D + '' +
               '<p>Canadian Program is billed in US$. FOB: Canadian Border. Duty & Brokerage is included.</p></div>' +
               '</div>' +
               '</div>';
               div_tabs += '<div id="tab-three" class="tab-pane">' +
               '<div class="wpb_text_column wpb_content_element ">' +
               '<div class="wpb_wrapper">' +
               '<h2></h2>' +
               '<table class="table" width="100px">' +
               '<tbody>' +
               '<tr><th>Qty</th> ' + tab_header + '</tr>' +
               '<tr><td>' + tab_price + cha + '</td>' + tab_price_tab_three + '</tr>' +
               '</tbody>' +
               '</table>' +
               '' + t_sec + '' +
               '<p>Pricing above is in Canadian dollars and is provided for information purposes only – pricing changes daily based on currency exchange rates. Peerless will BILL at the Canadian program price in USA dollars.</p></div>' +
               '</div>' +
               '</div>';
               div_tabs += '<div id="tab-three-2" class="tab-pane">' +
               '<div class="wpb_text_column wpb_content_element ">' +
               '<div class="wpb_wrapper">' +
               '<h2></h2>' +
               '<table class="table" width="100px">' +
               '<tbody>' +
               '<tr><th>Qty</th> ' + tab_header + '</tr>' +
               '<tr><th>' + tab_price + cha + '</th>' + tab_price_tab_three_2 + '</tr>' +
               '</tbody>' +
               '</table>' +
               '' + t_sec + '' +
               '<p>Pricing above is in Canadian dollars and is provided for information purposes only – pricing changes daily based on currency exchange rates. Peerless will BILL at the Canadian program price in USA dollars.</p></div>' +
               '</div>' +
               '</div>';
           }
           data_qty_field = '<div style="display: none !important;" class="as-tabs tabs variation_quantity_table">' +
           '<ul class="nav nav-tabs ">' + tabs + '</ul>' +
           '<div class="tab-content">' + div_tabs + '</div>' +
           '<div class="after_price_box_tex" style="margin-top: -30px;margin-bottom:25px;font-size: 12px;" > ' + after_price_box_tex + ' </div>' +
           '</div>';


       }
   }
});
}
jQuery(".variation_quantity_table").remove();

if( data_qty_field != 0 ) {
	jQuery(".wc-custom-price").before(data_qty_field);

	if( readCookie('lang') == 'canada' ) {
		jQuery('.langvage a[title="Canada"]').click();
	} else {
		jQuery('.langvage a[title="English"]').click();
	}

}
jQuery('.single-product .loader-div').remove();
jQuery('.woodmart-product-brands').show();
jQuery('.single-product .variation_quantity_table').attr('style','display: block !important;');

setTimeout(function () {
	
}, 1000);

});
}
});

function color_price_count() {
	var data = jQuery('.color-numbers .active').text();
	var data1 = jQuery('.panel-numbers .active').text();
	if (parseFloat(data) == 1) {
		//data = 0;
	}
	var data11 = data;
	if (data == 0) {
		data11 = 2;
	}
	console.log(data);
	//data1 = (parseFloat(data1) - 1) * parseFloat(data11);
	console.log(data1);
	jQuery('#tab-four .table td').each(function () {
		var price_d = jQuery(this).attr('data-price');
		console.log( price_d + ' + ((( ' + data + ' x ' + data1 + ' ) - 1 ) x 1.67 ) ' );
		price_d = parseFloat(price_d) +((( parseFloat(data) * parseFloat(data1) )-1)*1.67);
		price_d = price_d.toFixed(2);
		jQuery(this).html(price_d);
	});
}

jQuery(document).on('click', '.color-numbers li,.panel-numbers li', function () {
	jQuery(this).parent().find('.active').removeClass('active');
	jQuery(this).addClass('active');
	color_price_count();
});

</script>
<style type="text/css">
	.price-nav {
		display: block !important;
	}
	#tab-four h2{
		text-align: center;
		font-weight: normal;
		margin: 15px 0;
	}
	.color-numbers {
		text-align: center;
	}
	ul.color-numbers li,ul.panel-numbers li  {
		display: inline-block;
		width: 30px;
		height: 30px;
		background: #666;
		color: #fff;
		text-align: center;
		cursor: pointer;
		padding: 5px;
		border-radius: 50%;
		margin: 0 8%;
	}
	ul.color-numbers li.active,ul.panel-numbers li.active {
		background: #12acc8;
	}
	ul.panel-numbers li {
		margin: 0 3%;
	}

</style>
<?php
}

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Products_Quantity_Range_Pricing_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Products_Quantity_Range_Pricing_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-products-quantity-range-pricing-public.css', array(), rand(), 'all');

        $custom_inline_style = '.variation_quantity_table_1 {
        	border: 1px solid #eee;
        	display:' . get_option('as_table_display', 'none') . '
        }
        .as-tabs .tab-content .tab-pane { display: none; }
        .as-tabs .tab-content .tab-pane.active { display: block; }
        .as-tabs .nav {
        	display: -webkit-flex;
        	display: -ms-flexbox;
        	border-bottom: 1px solid #ddd;
        	margin: 0;
        	text-align: left;
        	display: flex;
        	-webkit-flex-wrap: wrap;
        	-ms-flex-wrap: wrap;
        	flex-wrap: wrap;
        	padding-left: 0;
        	margin-bottom: 0;
        	list-style: none;
        }
        .as-tabs .nav-tabs .nav-item {
        	margin-bottom: -1px;
        }
        .as-tabs ul.nav-tabs li.active a, .as-tabs ul.nav-tabs li.active a:hover, .as-tabs ul.nav-tabs li.active a:focus {
        	border-top-color: #0088cc;
        	color: #0088cc;
        }
        .nav-tabs li.active a, .nav-tabs li.active a:hover, .nav-tabs li.active a:focus {
        	background: #fff;
        	border-left-color: #eee;
        	border-right-color: #eee;
        	border-top: 3px solid #ccc;
        }
        .as-tabs .nav-tabs li .nav-link,.as-tabs .nav-tabs li .nav-link:hover {
        	border-left: 1px solid #eee;
        	border-right: 1px solid #eee;
        }
        .as-tabs .nav-tabs li .nav-link, .as-tabs  .tabs-navigation .nav-tabs > li:first-child .nav-link {
        	border-radius: 5px 5px 0 0;
        	font-size:13px;
        	padding:10px 5px;
        }
        .as-tabs .nav-link {
        	display: block;
        	padding: 0.5rem 1rem;
        }
        .variation_quantity_table_1 tr th:nth-child(1) {
        	width: 120px;
        }
        .nav-tabs li .nav-link, .nav-tabs li .nav-link:hover {
        	background: #f4f4f4;
        	border-left: 1px solid #eee;
        	border-right: 1px solid #eee;
        	border-top: 3px solid #eee;
        }
        .tabs ul.nav-tabs a:hover, .tabs ul.nav-tabs a:focus {
        	border-top-color: #0088cc;
        }

        .variation_quantity_table_1 thead tr th {
        	background: ' . get_option('as_title_bg_color', '#4D82B1') . ';
        	color: ' . get_option('as_title_text_color', '#ffffff') . ';
        }

        .variation_quantity_table_1 tr td {
        	background: ' . get_option('as_price_bg_color', '#eeeeee') . ';
        	color: ' . get_option('as_price_text_color', '#000000') . ';
        }

        .variation_quantity_table_1 tr td:last-child, .variation_quantity_table_1 tr th:last-child {
        	border-right-width: ' . get_option('as_border_size', '1') . 'px !important;
        }

        .variation_quantity_table_1 tr:last-child td {
        	border-bottom-width: ' . get_option('as_border_size', '1') . 'px !important;
        }

        .variation_quantity_table_1 tr td, .variation_quantity_table_1 tr th {
        	border: ' . get_option('as_border_size', '1') . 'px ' . get_option('as_border_style', 'solid') . ' ' . get_option('as_border_color', '#4D82B1') . ';
        	text-align: ' . get_option('as_text_align', 'left') . ';
        	padding: 5px 10px !important;
        	border-bottom-width: 0;
        	border-right-width: 0;
        }';

        wp_add_inline_style($this->plugin_name, $custom_inline_style);
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Products_Quantity_Range_Pricing_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Products_Quantity_Range_Pricing_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-products-quantity-range-pricing-public.js', array('jquery'), $this->version, false);

        // Create as_woo_pricing object ajax url variable in frontend side for ajax request.
        wp_localize_script($this->plugin_name, 'as_woo_pricing', array(
        	'ajax_url' => admin_url('admin-ajax.php'),
        )
    );
    }

    /**
     * Apply items quantity range price check and add in cart.
     *
     * Callback function for woocommerce_before_calculate_totals (action).
     *
     * @since   1.0.0
     * @access  public
     *
     * @param    object $cart_object This is oject of cart items.
     */
    public function woo_product_quantity_range_prices_apply_in_cart($cart_object) {

    	foreach ($cart_object->cart_contents as $key => $value) {

    		$taxonomy_use = false;
    		$term_item_id = '';
    		if (!empty($value['variation_id'])) {
    			$item_id = $value['variation_id'];
    			$woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
    			if (empty($woo_qrp_enable)) {
    				$item_id = $value['product_id'];
    			}
    		} else {
    			$item_id = $value['product_id'];
    		}
    		$regular_price = get_post_meta($item_id, '_regular_price', true);
    		$sale_price = get_post_meta($item_id, '_sale_price', true);
    		$woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
    		if (empty($woo_qrp_enable)) {
    			$taxonomy_use = true;
    		}
    		if ($taxonomy_use) {
                //Get all product terms
    			$product_terms = wp_get_object_terms($item_id, 'product_cat');
    			$product_tag_terms = wp_get_object_terms($item_id, 'product_tag');
                // Check product have at taxonomy terms and product shipping added or not.

    			if (!empty($product_terms)) {

                    // Check Product term have any error.
    				if (!is_wp_error($product_terms)) {

                        // Looping of product terms.
    					foreach ($product_terms as $term) {

    						$woo_qrp_enable = get_term_meta($term->term_id, '_as_quantity_range_pricing_enable', true);
    						$waps_category_products = get_term_meta($term->term_id, '_as_quantity_range_category_products', true);

    						if (!empty($waps_category_products) && !empty($woo_qrp_enable)) {
    							if (in_array($item_id, $waps_category_products)) {
    								$term_item_id = $term->term_id;
    								break;
    							}
    						}
    					}
    				}
    			}

    			if (!empty($product_tag_terms) && empty($term_item_id)) {
                    // Check Product term have any error.

    				if (!is_wp_error($product_tag_terms)) {

                        // Looping of product terms.
    					foreach ($product_tag_terms as $term) {

    						$woo_qrp_enable = get_term_meta($term->term_id, '_as_quantity_range_pricing_enable', true);
    						$waps_category_products = get_term_meta($term->term_id, '_as_quantity_range_category_products', true);
    						if (!empty($waps_category_products) && !empty($woo_qrp_enable)) {
    							if (in_array($item_id, $waps_category_products)) {
    								$term_item_id = $term->term_id;
    								break;
    							}
    						}
    					}
    				}
    			}
    		}

    		if (!empty($term_item_id)) {
    			$as_quantity_rage_values = get_term_meta($term_item_id, '_as_quantity_range_pricing_values', true);
    		} else {
    			$as_quantity_rage_values = get_post_meta($item_id, '_as_quantity_range_pricing_values', true);
    		}


    		if (!empty($sale_price)) {
    			$final_price = $sale_price;
    		} else {
    			$final_price = $regular_price;
    		}
    		$as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order($as_quantity_rage_values);

    		if (!empty($as_quantity_rage_values)) {

    			foreach ($as_quantity_rage_values as $as_quantity_rage_v) {
    				$as_quantity_rage_value = '';
    				if (is_array($as_quantity_rage_v)) {
    					$as_quantity_rage_value = $as_quantity_rage_v;
    				} else {
    					$as_quantity_rage_value = array(
    						'min_qty' => $as_quantity_rage_v->min_qty,
    						'max_qty' => $as_quantity_rage_v->max_qty,
    						'role' => $as_quantity_rage_v->role,
    						'price' => $as_quantity_rage_v->price,
    						'cnd_price' => $as_quantity_rage_v->cnd_price,
    						'regular_price' => $as_quantity_rage_v->regular_price,
    						'sales_price' => $as_quantity_rage_v->sales_price,
    						'type' => $as_quantity_rage_v->type,
    					);
    				}

    				$data_show = false;
    				if (empty($as_quantity_rage_value['role'])) {
    					$as_quantity_rage_value['role'] = '';
    				}
    				$roles = $as_quantity_rage_value['role'];
    				if (!empty($roles)) {
    					if (is_user_logged_in()) {
    						$user_info = get_userdata(get_current_user_id());
    						foreach ($user_info->roles as $role) {
    							if (in_array($role, $roles)) {
    								$data_show = true;
    							}
    						}
    					} else {
    						$user_roles = get_option('as_wpqrp_user_role', '');
    						foreach ($user_roles as $role) {
    							if (in_array($role, $roles)) {
    								$data_show = true;
    							}
    						}
    					}
    				} else {
    					$data_show = true;
    				}
    				if (!empty($as_quantity_rage_value['min_qty']) && $as_quantity_rage_value['max_qty'] && $as_quantity_rage_value['price'] && $data_show != false) {

    					if (( $as_quantity_rage_value['min_qty'] <= $value['quantity'] && $value['quantity'] <= $as_quantity_rage_value['max_qty'] ) || ( $as_quantity_rage_value['min_qty'] <= $value['quantity'] && $as_quantity_rage_value['max_qty'] == - 1 )) {

    						$type = $as_quantity_rage_value['type'];
    						$price = $as_quantity_rage_value['price'];
                            $sales_price = $as_quantity_rage_value['sales_price'];

                            if( $sales_price ) {
                                $price = $sales_price;
                            }

                            // get woocommerce decimal separator.
                            $decimal_separator = wc_get_price_decimal_separator();

                            // get woocommerce thousand separator.
                            $thousand_separator = wc_get_price_thousand_separator();

                            $price = str_replace($decimal_separator, ".", $price);
                            $price = str_replace($thousand_separator, "", $price);

                            if (!empty($woo_qrp_enable)) {
                               switch ($type) {

                                case 'percentage':
                                $value['data']->set_price($final_price - ( ( $final_price * $price ) / 100 ));
                                break;

                                case 'price':
                                $value['data']->set_price($final_price - $price);
                                break;

                                case 'fixed':
                                $value['data']->set_price($price);
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
}

    /**
     * Create quantity rage price list table on product detail page.
     *
     * Callback function for wp_ajax_nopriv_{action_name} , wp_ajax_{action_name} And
     * woocommerce_cart_item_price(action).
     *
     * @since   1.0.0
     * @access  public
     */
    public function wc_change_woocommerce_cart_item_price( $price, $value, $cart_item_key ){

        $taxonomy_use = false;
        $term_item_id = '';
        if (!empty($value['variation_id'])) {
            $item_id = $value['variation_id'];
            $woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
            if (empty($woo_qrp_enable)) {
                $item_id = $value['product_id'];
            }
        } else {
            $item_id = $value['product_id'];
        }
        $regular_price = get_post_meta($item_id, '_regular_price', true);
        $sale_price = get_post_meta($item_id, '_sale_price', true);
        $woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
        if (empty($woo_qrp_enable)) {
            $taxonomy_use = true;
        }
        if ($taxonomy_use) {

            $product_terms = wp_get_object_terms($item_id, 'product_cat');
            $product_tag_terms = wp_get_object_terms($item_id, 'product_tag');

            if (!empty($product_terms)) {

                if (!is_wp_error($product_terms)) {

                    foreach ($product_terms as $term) {

                        $woo_qrp_enable = get_term_meta($term->term_id, '_as_quantity_range_pricing_enable', true);
                        $waps_category_products = get_term_meta($term->term_id, '_as_quantity_range_category_products', true);

                        if (!empty($waps_category_products) && !empty($woo_qrp_enable)) {
                            if (in_array($item_id, $waps_category_products)) {
                                $term_item_id = $term->term_id;
                                break;
                            }
                        }
                    }
                }
            }

            if (!empty($product_tag_terms) && empty($term_item_id)) {

                if (!is_wp_error($product_tag_terms)) {

                    foreach ($product_tag_terms as $term) {

                        $woo_qrp_enable = get_term_meta($term->term_id, '_as_quantity_range_pricing_enable', true);
                        $waps_category_products = get_term_meta($term->term_id, '_as_quantity_range_category_products', true);
                        if (!empty($waps_category_products) && !empty($woo_qrp_enable)) {
                            if (in_array($item_id, $waps_category_products)) {
                                $term_item_id = $term->term_id;
                                break;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($term_item_id)) {
            $as_quantity_rage_values = get_term_meta($term_item_id, '_as_quantity_range_pricing_values', true);
        } else {
            $as_quantity_rage_values = get_post_meta($item_id, '_as_quantity_range_pricing_values', true);
        }


        if (!empty($sale_price)) {
            $final_price = $sale_price;
        } else {
            $final_price = $regular_price;
        }

        $as_quantity_rage_values = Woo_Products_Quantity_Range_Pricing_Admin::woo_quantity_value_sorting_by_order($as_quantity_rage_values);

        if (!empty($as_quantity_rage_values)) {

            foreach ($as_quantity_rage_values as $as_quantity_rage_v) {
                $as_quantity_rage_value = '';
                if (is_array($as_quantity_rage_v)) {
                    $as_quantity_rage_value = $as_quantity_rage_v;
                } else {
                    $as_quantity_rage_value = array(
                        'min_qty' => $as_quantity_rage_v->min_qty,
                        'max_qty' => $as_quantity_rage_v->max_qty,
                        'role' => $as_quantity_rage_v->role,
                        'price' => $as_quantity_rage_v->price,
                        'cnd_price' => $as_quantity_rage_v->cnd_price,
                        'regular_price' => $as_quantity_rage_v->regular_price,
                        'sales_price' => $as_quantity_rage_v->sales_price,
                        'type' => $as_quantity_rage_v->type,
                    );
                }

                $data_show = false;
                if (empty($as_quantity_rage_value['role'])) {
                    $as_quantity_rage_value['role'] = '';
                }
                $roles = $as_quantity_rage_value['role'];
                if (!empty($roles)) {
                    if (is_user_logged_in()) {
                        $user_info = get_userdata(get_current_user_id());
                        foreach ($user_info->roles as $role) {
                            if (in_array($role, $roles)) {
                                $data_show = true;
                            }
                        }
                    } else {
                        $user_roles = get_option('as_wpqrp_user_role', '');
                        foreach ($user_roles as $role) {
                            if (in_array($role, $roles)) {
                                $data_show = true;
                            }
                        }
                    }
                } else {
                    $data_show = true;
                }

                if (!empty($as_quantity_rage_value['min_qty']) && $as_quantity_rage_value['max_qty'] && $as_quantity_rage_value['price'] && $data_show != false) {

                    if (( $as_quantity_rage_value['min_qty'] <= $value['quantity'] && $value['quantity'] <= $as_quantity_rage_value['max_qty'] ) || ( $as_quantity_rage_value['min_qty'] <= $value['quantity'] && $as_quantity_rage_value['max_qty'] == - 1 )) {

                        $type = $as_quantity_rage_value['type'];
                        $price = $as_quantity_rage_value['price'];
                        $sales_price = $as_quantity_rage_value['sales_price'];

                        if( $sales_price ){
                            $price = $sales_price;                            
                        }

                        $decimal_separator = wc_get_price_decimal_separator();

                        $thousand_separator = wc_get_price_thousand_separator();

                        $price = str_replace($decimal_separator, ".", $price);
                        $price = str_replace($thousand_separator, "", $price);

                        if (!empty($woo_qrp_enable)) {
                            switch ($type) {

                                case 'percentage':
                                $price = $final_price - ( ( $final_price * $price ) / 100 );
                                $price = wc_price( $price );
                                break;

                                case 'price':
                                $price =  $final_price - $price;
                                $price = wc_price( $price );
                                break;

                                case 'fixed':
                                $price =  $price;
                                $price = wc_price( $price );
                                break;
                            }
                        }
                    }
                }
            }
        } 

        return $price;
    }

    /**
     * Create quantity rage price list table on product detail page.
     *
     * Callback function for wp_ajax_nopriv_{action_name} , wp_ajax_{action_name} And
     * woocommerce_before_add_to_cart_form(action).
     *
     * @since   1.0.0
     * @access  public
     */
    public function wc_show_quantity_range_price_list_table() {
    	global $wpdb, $product;


    	$data_html = $item_id = $term_item_id = '';
    	$currency = get_woocommerce_currency_symbol();
    	$taxonomy_use = false;
    	if (isset($_POST['variation_id'])) {
    		$item_id = $_POST['variation_id'];
    	} else {
    		$item_id = $product->id;
    		$as_woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
    		if (!$product->is_type('simple')) {
    			$handle = new WC_Product_Variable($product->id);
    			$variations1 = $handle->get_children();
    			foreach ($variations1 as $value) {
    				$as_woo_qrp_enable = get_post_meta($value, '_as_quantity_range_pricing_enable', true);
    				if (!empty($as_woo_qrp_enable)) {
    					$item_id = $value;
    				}
    			}
    		}
    		if (empty($as_woo_qrp_enable)) {
    			$taxonomy_use = true;
    		}
    	}
    	$regular_price = get_post_meta($item_id, '_regular_price', true);
    	$sale_price = get_post_meta($item_id, '_sale_price', true);
    	if ($taxonomy_use) {
            //Get all product terms
    		$product_terms = wp_get_object_terms($item_id, 'product_cat');
    		$product_tag_terms = wp_get_object_terms($item_id, 'product_tag');
            // Check product have at taxonomy terms and product shipping added or not.

    		if (!empty($product_terms)) {

                // Check Product term have any error.
    			if (!is_wp_error($product_terms)) {

                    // Looping of product terms.
    				foreach ($product_terms as $term) {

    					$waps_category_enable = get_term_meta($term->term_id, '_as_quantity_range_pricing_enable', true);
    					$waps_category_products = get_term_meta($term->term_id, '_as_quantity_range_category_products', true);

    					if (!empty($waps_category_products) && !empty($waps_category_enable)) {
    						if (in_array($item_id, $waps_category_products)) {
    							$term_item_id = $term->term_id;
    							break;
    						}
    					}
    				}
    			}
    		}

    		if (!empty($product_tag_terms) && empty($term_item_id)) {
                // Check Product term have any error.

    			if (!is_wp_error($product_tag_terms)) {

                    // Looping of product terms.
    				foreach ($product_tag_terms as $term) {

    					$waps_category_enable = get_term_meta($term->term_id, '_as_quantity_range_pricing_enable', true);
    					$waps_category_products = get_term_meta($term->term_id, '_as_quantity_range_category_products', true);
    					if (!empty($waps_category_products) && !empty($waps_category_enable)) {
    						if (in_array($item_id, $waps_category_products)) {
    							$term_item_id = $term->term_id;
    							break;
    						}
    					}
    				}
    			}
    		}
    	}

    	if (!empty($sale_price)) {
    		$final_price = $sale_price;
    	} else {
    		$final_price = $regular_price;
    	}
    	if ($taxonomy_use && !empty($term_item_id)) {
    		$as_quantity_rage_values = get_term_meta($term_item_id, '_as_quantity_range_pricing_values', true);
    		$as_woo_qrp_enable = get_term_meta($term_item_id, '_as_quantity_range_pricing_enable', true);
    		$as_woo_qrp_label = get_term_meta($term_item_id, '_as_quantity_range_pricing_label', true);

            //print_r($as_quantity_rage_values);
    	} else {
    		$as_quantity_rage_values = get_post_meta($item_id, '_as_quantity_range_pricing_values', true);
    		$as_woo_qrp_enable = get_post_meta($item_id, '_as_quantity_range_pricing_enable', true);
    		$as_woo_qrp_label = get_post_meta($item_id, '_as_quantity_range_pricing_label', true);
    	}
    	$heading_quantity = apply_filters('wpqrp_heading_quantity', 'Quantity');
    	$heading_price = apply_filters('wpqrp_heading_price', 'Price');
    	$label_or_more = apply_filters('wpqrp_label_or_more', 'or more');

    	if (!empty($as_quantity_rage_values) && !empty($final_price) && 'on' === $as_woo_qrp_enable) {
    		$data_load = false;

    		$terms = get_the_terms($product->id, 'product_cat');
    		if ($terms) {
    			foreach ($terms as $term) {
    				if ($term->slug == 'bags') {
    					$data_load = true;
    				}
    			}
    		}
    		if ($data_load) {
    			include 'partials/front-quantity-range-pricing-table-two.php';
    		} else {
              //  include 'partials/front-quantity-range-pricing-table.php';
    		}
    	}
    	if (isset($_POST['variation_id']) && isset($_POST['variation_call'])) {
    		die();
    	}
    }

} 