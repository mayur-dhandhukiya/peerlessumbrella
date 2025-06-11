<?php
/*
Plugin Name: WPC Smart Price Filter for WooCommerce
Plugin URI: https://wpclever.net/
Description: WPC Smart Price Filter is an advanced widget help you to filter products by price with unlimited price ranges.
Version: 1.3.5
Author: WPClever
Author URI: https://wpclever.net
Text Domain: wpc-smart-price-filter
Domain Path: /languages/
Requires Plugins: woocommerce
Requires at least: 4.0
Tested up to: 6.7
WC requires at least: 3.0
WC tested up to: 9.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WOOSP_VERSION' ) && define( 'WOOSP_VERSION', '1.3.5' );
! defined( 'WOOSP_LITE' ) && define( 'WOOSP_LITE', __FILE__ );
! defined( 'WOOSP_FILE' ) && define( 'WOOSP_FILE', __FILE__ );
! defined( 'WOOSP_URI' ) && define( 'WOOSP_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOSP_DIR' ) && define( 'WOOSP_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WOOSP_REVIEWS' ) && define( 'WOOSP_REVIEWS', 'https://wordpress.org/support/plugin/wpc-smart-price-filter/reviews/?filter=5' );
! defined( 'WOOSP_CHANGELOG' ) && define( 'WOOSP_CHANGELOG', 'https://wordpress.org/plugins/wpc-smart-price-filter/#developers' );
! defined( 'WOOSP_DISCUSSION' ) && define( 'WOOSP_DISCUSSION', 'https://wordpress.org/support/plugin/wpc-smart-price-filter' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOSP_URI );

include 'includes/dashboard/wpc-dashboard.php';
include 'includes/kit/wpc-kit.php';
include 'includes/hpos.php';

if ( ! function_exists( 'woosp_init' ) ) {
	add_action( 'plugins_loaded', 'woosp_init', 11 );

	function woosp_init() {
		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'woosp_notice_wc' );

			return null;
		}

		class WPClever_Widget_Smart_Price_Filter extends WP_Widget {
			public function __construct() {
				$widget_ops = [
					'classname'   => 'wpc_smart_price_filter woocommerce widget_layered_nav',
					'description' => esc_html__( 'Smart product filter by price widget for WooCommerce', 'wpc-smart-price-filter' ),
				];

				$control_ops = [
					'width'  => 500,
					'height' => 350,
				];

				parent::__construct( 'wpc_smart_price_filter', esc_html__( 'WPC Smart Price Filter', 'wpc-smart-price-filter' ), $widget_ops, $control_ops );
			}

			public function widget( $args, $instance ) {
				if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
					return;
				}

				// Find min and max price in current result set
				$prices = $this->get_filtered_price();
				$from   = floor( $prices->min_price );
				$to     = ceil( $prices->max_price );

				if ( $from === $to ) {
					return;
				}

				echo $args['before_widget'];

				if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}

				if ( '' === get_option( 'permalink_structure' ) ) {
					$form_action = remove_query_arg( [ 'page', 'paged' ], $this->get_page_base_url() );
				} else {
					$form_action = preg_replace( '%\/page/[0-9]+%', '', $this->get_page_base_url() );
				}

				/**
				 * Adjust max if the store taxes are not displayed how they are stored.
				 * Min is left alone because the product may not be taxable.
				 * Kicks in when prices excluding tax are displayed including tax.
				 */
				if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
					$tax_classes = array_merge( [ '' ], WC_Tax::get_tax_classes() );
					$class_max   = $to;

					foreach ( $tax_classes as $tax_class ) {
						if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
							$class_max = $to + WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $to, $tax_rates ) );
						}
					}

					$to = $class_max;
				}

				$min_price  = isset( $_GET['min_price'] ) ? sanitize_text_field( $_GET['min_price'] ) : apply_filters( 'woocommerce_price_filter_widget_min_amount', $from );
				$max_price  = isset( $_GET['max_price'] ) ? sanitize_text_field( $_GET['max_price'] ) : apply_filters( 'woocommerce_price_filter_widget_max_amount', $to );
				$price_list = ! empty( $instance['price_list'] ) ? $instance['price_list'] : [];
				$show_count = ! empty( $instance['show_count'] ) ? intval( $instance['show_count'] ) : 0;

				if ( $price_list && is_array( $price_list ) ):
					?>
                    <ul class="woocommerce-widget-layered-nav-list">
						<?php foreach ( $price_list as $range ):
							$active = '';
							$from = $range['min'] ?? 0;
							$to = $range['max'] ?? 0;
							$label = isset( $range['label'] ) ? esc_attr( $range['label'] ) : '';
							$arg_url_query = [];

							if ( $from ) {
								$arg_url_query['min_price'] = $from;
							}

							if ( $to ) {
								$arg_url_query['max_price'] = $to;
							}

							if ( ! $to && $from ) {
								$form_action = remove_query_arg( [ 'max_price' ], $this->get_page_base_url() );
							}

							if ( $to && ! $from ) {
								$form_action = remove_query_arg( [ 'min_price' ], $this->get_page_base_url() );
							}

							$form_action = add_query_arg(
								$arg_url_query,
								$form_action
							);

							if ( $show_count ) {
								$count_post = $this->get_count_post( $from, $to );
							}

							if (
								( isset( $_GET['max_price'] ) && isset( $_GET['min_price'] ) && $to == $max_price && $min_price == $from )
								||
								( isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) && $to == $max_price )
								||
								( ! isset( $_GET['max_price'] ) && isset( $_GET['min_price'] ) && $min_price == $from )
							) {
								$active      = 'active woocommerce-widget-layered-nav-list__item--chosen chosen';
								$form_action = remove_query_arg( [
									'max_price',
									'min_price'
								], $this->get_page_base_url() );
							}
							?>
                            <li class="woocommerce-widget-layered-nav-list__item wc-layered-nav-term <?php echo $active; ?>">
                                <a href="<?php echo esc_url( $form_action ); ?>"><?php echo $label; ?></a><?php if ( $show_count ): ?>
                                    <span class="count">(<?php echo $count_post; ?>)</span><?php endif; ?>
                            </li>
						<?php endforeach; ?>
                    </ul>
				<?php
				endif;
				echo $args['after_widget'];
			}

			public function form( $instance ) {
				$title      = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Filter by price', 'wpc-smart-price-filter' );
				$show_count = ! empty( $instance['show_count'] ) ? intval( $instance['show_count'] ) : 0;
				$price_list = ! empty( $instance['price_list'] ) ? $instance['price_list'] : [];
				?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( esc_attr( 'Title:' ) ); ?></label>
                    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
                </p>
                <div class="wpc_smart_price_filter">
                    <div class="table">
                        <div class="tr">
                            <div class="td"><?php esc_html_e( 'From Price', 'wpc-smart-price-filter' ); ?></div>
                            <div class="td"><?php esc_html_e( 'To Price', 'wpc-smart-price-filter' ); ?></div>
                            <div class="td"><?php esc_html_e( 'Label', 'wpc-smart-price-filter' ); ?></div>
                            <div class="td"></div>
                        </div>
                        <div class="tbody">
							<?php foreach ( $price_list as $k => $range ):
								$from = $range['min'] ?? 0;
								$to = $range['max'] ?? 0;
								$label = isset( $range['label'] ) ? esc_attr( $range['label'] ) : '';
								?>
                                <div class="tr">
                                    <div class="td">
                                        <input class="widefat" type="number" min="0" name="<?php echo esc_attr( $this->get_field_name( 'price_list' ) ); ?>[<?php echo $k; ?>][min]" value="<?php echo esc_attr( $from ); ?>"/>
                                    </div>
                                    <div class="td">
                                        <input class="widefat" type="number" min="0" name="<?php echo esc_attr( $this->get_field_name( 'price_list' ) ); ?>[<?php echo $k; ?>][max]" value="<?php echo esc_attr( $to ); ?>"/>
                                    </div>
                                    <div class="td">
                                        <input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'price_list' ) ); ?>[<?php echo $k; ?>][label]" value="<?php echo esc_html( $label ); ?>"/>
                                    </div>
                                    <div class="td delete">
                                        <a href="#" title="<?php esc_html_e( 'Delete', 'wpc-smart-price-filter' ); ?>"> &times; </a>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                        <div class="tr">
                            <div class="td w100">
                                <a href="javascript:void(0);" data-number="<?php echo $this->number; ?>" class="button button-primary add">
									<?php esc_html_e( 'Add new price range', 'wpc-smart-price-filter' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <script type="text/html" id="tmpl-wpc-price-range-<?php echo $this->number; ?>">
                        <div class="tr">
                            <div class="td">
                                <input class="widefat" type="number" min="0" name="<?php echo esc_attr( $this->get_field_name( 'price_list' ) ); ?>[dk_{{data.index}}][min]"/>
                            </div>
                            <div class="td">
                                <input class="widefat" type="number" min="0" name="<?php echo esc_attr( $this->get_field_name( 'price_list' ) ); ?>[dk_{{data.index}}][max]"/>
                            </div>
                            <div class="td">
                                <input class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'price_list' ) ); ?>[dk_{{data.index}}][label]"/>
                            </div>
                            <div class="td delete">
                                <a href="#" title="<?php esc_html_e( 'Delete', 'wpc-smart-price-filter' ); ?>"> &times; </a>
                            </div>
                        </div>
                    </script>
                </div><p>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>">
                        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>" type="checkbox" value="1" <?php checked( 1, $show_count ); ?>> <?php esc_html_e( esc_attr( 'Show product counts', 'wpc-smart-price-filter' ) ); ?>
                    </label>
                </p>
				<?php
			}

			public function update( $new_instance, $old_instance ) {
				$instance               = [];
				$instance['title']      = ! empty( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
				$instance['show_count'] = ! empty( $new_instance['show_count'] ) ? intval( $new_instance['show_count'] ) : 0;
				$instance['price_list'] = ! empty( $new_instance['price_list'] ) ? $new_instance['price_list'] : [];

				return $instance;
			}

			protected function get_filtered_price() {
				global $wpdb, $wp_the_query;

				$args       = $wp_the_query->query_vars;
				$tax_query  = $args['tax_query'] ?? [];
				$meta_query = $args['meta_query'] ?? [];

				if ( ! is_post_type_archive( 'product' ) && ! empty( $args['taxonomy'] ) && ! empty( $args['term'] ) ) {
					$tax_query[] = [
						'taxonomy' => $args['taxonomy'],
						'terms'    => [ $args['term'] ],
						'field'    => 'slug',
					];
				}

				foreach ( $meta_query + $tax_query as $key => $query ) {
					if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
						unset( $meta_query[ $key ] );
					}
				}

				$meta_query     = new WP_Meta_Query( $meta_query );
				$tax_query      = new WP_Tax_Query( $tax_query );
				$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
				$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

				$sql = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
				$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
				$sql .= " 	WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', [ 'product' ] ) ) ) . "')
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', [ '_price' ] ) ) ) . "')
					AND price_meta.meta_value > '' ";
				$sql .= $tax_query_sql['where'] . $meta_query_sql['where'];

				if ( $search = WC_Query::get_main_search_query_sql() ) {
					$sql .= ' AND ' . $search;
				}

				return $wpdb->get_row( $sql );
			}

			protected function get_page_base_url() {
				if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
					$link = home_url();
				} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {
					$link = get_post_type_archive_link( 'product' );
				} elseif ( is_product_category() ) {
					$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
				} elseif ( is_product_tag() ) {
					$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
				} else {
					$queried_object = get_queried_object();
					$link           = get_term_link( $queried_object->slug, $queried_object->taxonomy );
				}

				// Min/Max
				if ( isset( $_GET['min_price'] ) ) {
					$link = add_query_arg( 'min_price', wc_clean( $_GET['min_price'] ), $link );
				}

				if ( isset( $_GET['max_price'] ) ) {
					$link = add_query_arg( 'max_price', wc_clean( $_GET['max_price'] ), $link );
				}

				// Order by
				if ( isset( $_GET['orderby'] ) ) {
					$link = add_query_arg( 'orderby', wc_clean( $_GET['orderby'] ), $link );
				}

				/**
				 * Search Arg.
				 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
				 */
				if ( get_search_query() ) {
					$link = add_query_arg( 's', rawurlencode( htmlspecialchars_decode( get_search_query() ) ), $link );
				}

				// Post Type Arg
				if ( isset( $_GET['post_type'] ) ) {
					$link = add_query_arg( 'post_type', wc_clean( $_GET['post_type'] ), $link );
				}

				// Min Rating Arg
				if ( isset( $_GET['rating_filter'] ) ) {
					$link = add_query_arg( 'rating_filter', wc_clean( $_GET['rating_filter'] ), $link );
				}

				// product_cat in search
				if ( isset( $_GET['product_cat'] ) ) {
					$link = add_query_arg( 'product_cat', wc_clean( $_GET['product_cat'] ), $link );
				}

				// All current filters
				if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
					foreach ( $_chosen_attributes as $name => $data ) {
						$filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );

						if ( ! empty( $data['terms'] ) ) {
							$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
						}

						if ( 'or' == $data['query_type'] ) {
							$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
						}
					}
				}

				return $link;
			}

			function get_count_post( $min_price = '', $max_price = '' ) {
				if ( ! $min_price && ! $max_price ) {
					return;
				}

				global $wp_the_query;
				$old_query  = $wp_the_query->query_vars;
				$meta_price = [];

				if ( ! $min_price && $max_price ) {
					$meta_price = [
						[
							'key'     => '_price',
							'value'   => $max_price,
							'compare' => '<=',
							'type'    => 'NUMERIC'
						]
					];
				}

				if ( ! $max_price && $min_price ) {
					$meta_price = [
						[
							'key'     => '_price',
							'value'   => $min_price,
							'compare' => '>=',
							'type'    => 'NUMERIC'
						]
					];
				}

				if ( $min_price && $max_price ) {
					$meta_price = [
						[
							'key'     => '_price',
							'value'   => [ $min_price, $max_price ],
							'compare' => 'BETWEEN',
							'type'    => 'NUMERIC'
						]
					];
				}

				$args = [
					'post_type'      => [ 'product' ],
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'meta_query'     => $meta_price
				];

				$tax_query = $old_query['tax_query'] ?? [];

				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0', '>=' ) ) {
					$tax_query[] = [
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'exclude-from-catalog',
						'operator' => 'NOT IN',
					];
				} else {
					$args['meta_query'][] = [
						'key'     => '_visibility',
						'value'   => [ 'catalog', 'visible' ],
						'compare' => 'IN'
					];
				}

				if ( is_tax() ) {
					if ( ! empty( $old_query['taxonomy'] ) && ! empty( $old_query['term'] ) ) {
						$tax_query[] = [
							'taxonomy' => $old_query['taxonomy'],
							'terms'    => [ $old_query['term'] ],
							'field'    => 'slug',
						];
					}
				}

				$args['tax_query'] = $tax_query;
				$posts             = get_posts( $args );

				return count( $posts );
			}
		}

		if ( ! class_exists( 'WPCleverWoosp' ) && class_exists( 'WC_Product' ) ) {
			class WPCleverWoosp {
				protected static $instance = null;

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					// init
					add_action( 'init', [ $this, 'init' ] );
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

					// widget
					add_action( 'widgets_init', [ $this, 'register_widget' ] );

					// settings page
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );

					// settings link
					add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );
				}

				function init() {
					// load text-domain
					load_plugin_textdomain( 'wpc-smart-price-filter', false, basename( WOOSP_DIR ) . '/languages/' );
				}

				function admin_enqueue_scripts() {
					// backend css & js
					wp_enqueue_style( 'woosp-backend', WOOSP_URI . 'assets/css/backend.css', false, WOOSP_VERSION );
					wp_enqueue_script( 'woosp-backend', WOOSP_URI . 'assets/js/backend.js', [
						'jquery',
						'wp-util'
					], WOOSP_VERSION, true );
				}

				function action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin == $file ) {
						$how = '<a href="' . esc_url( admin_url( 'admin.php?page=wpclever-woosp&tab=how' ) ) . '">' . esc_html__( 'How to use?', 'wpc-smart-price-filter' ) . '</a>';
						array_unshift( $links, $how );
					}

					return (array) $links;
				}

				function row_meta( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin == $file ) {
						$row_meta = [
							'support' => '<a href="' . esc_url( WOOSP_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'wpc-smart-price-filter' ) . '</a>',
						];

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Smart Price Filter', 'wpc-smart-price-filter' ), esc_html__( 'Price Filter', 'wpc-smart-price-filter' ), 'manage_options', 'wpclever-woosp', [
						$this,
						'admin_menu_content'
					] );
				}

				function admin_menu_content() {
					$active_tab = sanitize_key( $_GET['tab'] ?? 'how' );
					?>
                    <div class="wpclever_settings_page wrap">
                        <h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Smart Price Filter', 'wpc-smart-price-filter' ) . ' ' . esc_html( WOOSP_VERSION ); ?></h1>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
								<?php printf( /* translators: stars */ esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'wpc-smart-price-filter' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                                <br/>
                                <a href="<?php echo esc_url( WOOSP_REVIEWS ); ?>" target="_blank"><?php esc_html_e( 'Reviews', 'wpc-smart-price-filter' ); ?></a> |
                                <a href="<?php echo esc_url( WOOSP_CHANGELOG ); ?>" target="_blank"><?php esc_html_e( 'Changelog', 'wpc-smart-price-filter' ); ?></a> |
                                <a href="<?php echo esc_url( WOOSP_DISCUSSION ); ?>" target="_blank"><?php esc_html_e( 'Discussion', 'wpc-smart-price-filter' ); ?></a>
                            </p>
                        </div>
                        <div class="wpclever_settings_page_nav">
                            <h2 class="nav-tab-wrapper">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-woosp&tab=how' ) ); ?>" class="<?php echo esc_attr( $active_tab == 'how' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'How to use?', 'wpc-smart-price-filter' ); ?>
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpclever-kit' ) ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'wpc-smart-price-filter' ); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="wpclever_settings_page_content">
							<?php if ( $active_tab == 'how' ) { ?>
                                <div class="wpclever_settings_page_content_text">
                                    <p>
										<?php esc_html_e( 'After install & active plugin, you can go to Appearance >> Widgets to add widget "WPC Smart Price Filter" to the sidebar.', 'wpc-smart-price-filter' ); ?>
                                    </p>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}

				function register_widget() {
					register_widget( 'WPClever_Widget_Smart_Price_Filter' );
				}
			}

			return WPCleverWoosp::instance();
		}

		return null;
	}

	function woosp_notice_wc() {
		?>
        <div class="error">
            <p><?php esc_html_e( 'WPC Smart Price Filter require WooCommerce version 3.0 or greater.', 'wpc-smart-price-filter' ); ?></p>
        </div>
		<?php
	}
}
