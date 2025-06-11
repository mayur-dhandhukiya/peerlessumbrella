<?php
/**
 * Custom Post Type for Stores and Taxonomies.
 */
class WooCommerce_Wishlist_Post_Type
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

        add_filter('manage_wishlist_posts_columns', array($this, 'columns_head'));
        add_action('manage_wishlist_posts_custom_column', array($this, 'columns_content'), 10, 1);
    }

    /**
     * Init.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function init()
    {
        $this->register_wishlist_post_type();
    }

    /**
     * Register Store Post Type.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @return bool
     */
    public function register_wishlist_post_type()
    {
        $singular = __('Wishlist', 'woocommerce-wishlist');
        $plural = __('Wishlists', 'woocommerce-wishlist');

        $labels = array(
            'name' => $plural,
            'all_items' => sprintf(__('All %s', 'woocommerce-wishlist'), $plural),
            'singular_name' => $singular,
            'add_new' => sprintf(__('New %s', 'woocommerce-wishlist'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'woocommerce-wishlist'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'woocommerce-wishlist'), $singular),
            'new_item' => sprintf(__('New %s', 'woocommerce-wishlist'), $singular),
            'view_item' => sprintf(__('View %s', 'woocommerce-wishlist'), $plural),
            'search_items' => sprintf(__('Search %s', 'woocommerce-wishlist'), $plural),
            'not_found' => sprintf(__('No %s found', 'woocommerce-wishlist'), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'woocommerce-wishlist'), $plural),
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'menu_position' => 57,
            'rewrite' => array(
                'slug' => 'wishlist',
                'with_front' => FALSE
            ),
            'query_var' => 'stores',
            'supports' => array('title', 'author', 'thumbnail'),
            'menu_icon' => 'dashicons-heart',
        );

        register_post_type('wishlist', $args);

    }

    /**
     * Columns Head.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $columns Columnd
     *
     * @return string
     */
    public function columns_head($columns)
    {
        $output = array();
        foreach ($columns as $column => $name) {
            $output[$column] = $name;

            if ($column === 'title') {
                $output['visibility'] = __('Visibility', 'woocommerce-wishlist');
            }
        }

        return $output;
    }

    /**
     * Columns Content.
     *
     * @author Daniel Barenkamp
     *
     * @version 1.0.0
     *
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     *
     * @param string $column_name Column Name
     *
     * @return string
     */
    public function columns_content($column_name)
    {
        global $post;

        if ($column_name == 'visibility') {
            $visibility = get_post_meta($post->ID, 'visibility', true);
            echo $visibility;
        }
    }

    /**
     * Add custom ticket metaboxes
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $post_type [description]
     * @param   [type]                       $post      [description]
     */
    public function add_custom_metaboxes($post_type, $post)
    {
        add_meta_box('woocommerce-wishlist-products', 'Products', array($this, 'products'), 'wishlist', 'normal', 'high');
    }

    /**
     * Display Metabox Address
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function products()
    {
        global $post;

        wp_nonce_field(basename(__FILE__), 'woocommerce_wishlist_meta_nonce');

        $products = get_post_meta($post->ID, 'products');

        if(!empty($products)) {
            echo '<ol>';
            foreach ($products as $product) {
                foreach ($product as $product_data) {
                    $real_product_data = wc_get_product($product_data['product_id']);
                    if(!$real_product_data) {
                        continue;
                    }
                    
                    echo '<li>';
                        echo $real_product_data->get_title();
                        echo __(' added on ', 'woocommerce-wishlist') . date_i18n( get_option( 'date_format' ), $product_data['added']);
                    echo '</li>';
                }
            }
            echo '</ol>';
        }
    }
}