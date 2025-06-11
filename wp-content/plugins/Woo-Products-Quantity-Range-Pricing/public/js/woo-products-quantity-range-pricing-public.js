(function ($) {
    'use strict';

    // This Change event for get variaton quantity range price table on product detail page.
    $(document).on('change', '.variation_id', function () {

        var data_product_id = jQuery('input[name="product_id"]').val();
        var data_variation_id = jQuery('input[name="variation_id"]').val();
        var data_qty_field = '';
        var price_data = 0;
        jQuery.each( complex, function( key, value ) {
            if ( value != '' ) {
                var v_data_id = 0;
                if ( data_variation_id != 0 ) {
                    v_data_id = data_variation_id;
                }else{
                    v_data_id = data_product_id;
                }

                if ( v_data_id == key ) {
                    if ( product_bags == 0 ) {
                        data_qty_field = '<table class="variation_quantity_table_drop_down" style="margin:0;" cellspacing="0"><tbody><tr><td class="label"><label for="pa_color">Quantity</label></td><td class="value"><select class="variation_quantity_dropdown">';
                        jQuery.each(value, function (v_key, v_value) {
                            if (v_value.max_qty != -1) {
                                data_qty_field = data_qty_field + '<option value="' + v_value.min_qty + '">' + v_value.min_qty + ' - ' + v_value.max_qty + '   ( ' + symbol_currency + v_value.price + ' Each Pcs )</option>';
                            } else {
                                data_qty_field = data_qty_field + '<option value="' + v_value.min_qty + '">' + v_value.min_qty + ' Or more  ( ' + symbol_currency + v_value.price + ' Each Pcs )</option>';
                            }
                        });
                        data_qty_field = data_qty_field + '</select><td> </tr></tbody></table>';
                    }
                }    
            }     
        });
        $(".variation_quantity_table_drop_down").remove();
        if( data_qty_field != 0 ){
            $(".wc-custom-price").before( data_qty_field );
            var min_qty = $(".variation_quantity_dropdown").val();
            if ( min_qty ) {           
                min_change_quantity( min_qty );
            }
        }

        /*var variation_id = $('.variation_id').val();      

        if ( variation_id != "" ) {

            var data = {
                'action': 'wc_get_select_variation_quantity_pricing_table',
                'variation_id': variation_id, // We pass variation ID.
                'variation_call': 1 // We pass ajax status variation.
            };

            jQuery.post(as_woo_pricing.ajax_url, data, function (response) {
                $(".variation_quantity_table").remove();
                if( response != 0 ){
                    $(".variations").before(response);
                }
            });

        } else {

            $(".variation_quantity_table").remove();

        }*/
        
    });

    jQuery('.variations_form').attr('data-product_variations');
})(jQuery);
