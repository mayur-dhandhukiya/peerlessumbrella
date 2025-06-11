'use strict';

(
    function($, wp) {
      $(document).ready(function() {
        $('body').on('click', '.wpc_smart_price_filter .add', function() {
          var thisTable = $(this).closest('.table');
          var thisNumber = $(this).data('number');
          var size = thisTable.find('.tr').length;
          var wpc_price_template = wp.template('wpc-price-range-' + thisNumber);
          $('.tbody', thisTable).append(wpc_price_template({
            index: size,
          }));
        });

        $('body').on('click', '.wpc_smart_price_filter .delete a', function() {
          var thisTr = $(this).closest('.tr');
          var thisTbody = $(this).closest('.tbody');
          thisTr.fadeOut(400, function() {
            $(this).remove();
            var loop = 0;
            $('.tr', thisTbody).each(function(index, row) {
              $('input', row).each(function(i, el) {
                var t = $(el);
                t.attr('name', t.attr('name').
                    replace(/\[dk_([^[]*)\]/, '[dk_' + loop + ']'));
              });
              loop++;
            });
          });
          return false;
        });
      });
    }
)(jQuery, wp);