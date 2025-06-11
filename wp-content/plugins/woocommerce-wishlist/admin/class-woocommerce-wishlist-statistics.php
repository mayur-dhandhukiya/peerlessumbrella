<?php
/**
 * Custom Post Type for Stores and Taxonomies.
 */
class WooCommerce_Wishlist_Statistics
{
    private $plugin_name;
    private $version;
    /**
     * Constructor.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->prefix = 'woocommerce_wishlist_';
    }

    /**
     * Init Reports Page in Admin
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function add_menu()
    {        
        add_submenu_page(
            'edit.php?post_type=wishlist',
            __('Statistics', 'woocommerce-wishlist'),
            __('Statistics', 'woocommerce-wishlist'),
            'manage_options',
            'wishlist-statistics',
            array($this, 'render')
        );
    }

    public function render()
    {
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'name',
			'order'            => 'ASC',
			'post_type'        => 'wishlist'
		);
    	$wishlists = get_posts($args);
    	if(empty($wishlists)) {
    		echo __('No Wishlists created yet', 'woocommerce-wishlist');
    		return false;
    	}
    	
    	$products = array();
    	foreach ($wishlists as $wishlist) {

			$wishlist_products = get_post_meta($wishlist->ID, 'products', true);

			if(empty($wishlist_products)) {
				continue;
			}
			foreach ($wishlist_products as $wishlist_product) {
				$product_id = $wishlist_product['product_id'];
				if(isset($products[$product_id])) {
					$products[$product_id] = $products[$product_id] + 1;
				} else {
					$products[$product_id] = 1;
				}
			}
    	}

    	?>

		<style>
		#woocommerce_wishlist {
		    font-family: Arial, Helvetica, sans-serif;
		    border-collapse: collapse;
		    width: 100%;
		    margin-top:20px;
		}

		#woocommerce_wishlist td, #woocommerce_wishlist th {
		    border: 1px solid #ddd;
		    padding: 8px;
		}

		#woocommerce_wishlist tr:nth-child(even){background-color: #f2f2f2;}

		#woocommerce_wishlist tr:hover {background-color: #ddd;}

		#woocommerce_wishlist th {
		    padding-top: 12px;
		    padding-bottom: 12px;
		    text-align: left;
		    background-color: #4CAF50;
		    color: white;
		}
		</style>
		<table id="woocommerce_wishlist">
			<thead>
				<tr>
					 <th><?php echo __('Product', 'woocommerce-wishlist') ?></th>
					 <th><?php echo __('SKU', 'woocommerce-wishlist') ?></th>
					 <th><?php echo __('Wishlist Count', 'woocommerce-wishlist') ?></th>
				</tr>
			</thead>

			<?php
	    	foreach ($products as $product => $count) {

	    		$product = wc_get_product($product);
	    		if(!$product) {
	    			continue;
	    		}
	    		
				echo '<tr>';
					echo '
						<td>' . $product->get_title() . '</td>
						<td>' . $product->get_sku() . '</td>
						<td>' . $count . '</td>';
				echo '</tr>';
	    	}
	    	?>
		</table>
    	<?php
    }
}