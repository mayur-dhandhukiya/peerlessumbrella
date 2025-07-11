<?php
/**
 * Manager class file.
 *
 * @package Woodmart
 */

namespace XTS\Modules\Layouts;

use WP_Post;
use XTS\Singleton;

/**
 * Manager class.
 */
class Manager extends Singleton {
	/**
	 * Conditions cache.
	 *
	 * @var Conditions_Cache
	 */
	private $cache;
	/**
	 * Post type.
	 *
	 * @var string
	 */
	private $post_type = 'woodmart_layout';
	/**
	 * Type meta key.
	 *
	 * @var string
	 */
	private $type_meta_key = 'wd_layout_type';
	/**
	 * Conditions meta key.
	 *
	 * @var string
	 */
	private $conditions_meta_key = 'wd_layout_conditions';

	/**
	 * Constructor.
	 */
	public function init() {
		$this->cache = new Conditions_Cache();

		$this->add_actions();
	}

	/**
	 * Add actions.
	 */
	public function add_actions() {
		add_action( 'wp_ajax_wd_layout_create', array( $this, 'create_layout' ) );
		add_action( 'wp_ajax_wd_layout_edit', array( $this, 'edit_layout' ) );
		add_action( 'wp_ajax_wd_layout_conditions_query', array( $this, 'conditions_query' ) );
		add_action( 'wp_ajax_wd_layout_change_status', array( $this, 'change_status_action' ) );
		add_action( 'wp_trash_post', array( $this, 'remove_post_from_cache' ) );
		add_action( 'untrashed_post', array( $this, 'on_untrash_post' ) );
		add_action( 'save_post_' . $this->post_type, array( $this, 'save_post' ) );
		add_action( 'woodmart_after_import', array( $this, 'save_post' ) );
		add_filter( 'post_row_actions', array( $this, 'duplicate_action' ), 10, 2 );
	}

	/**
	 * Save template post.
	 */
	public function save_post() {
		$this->cache->regenerate();
	}

	/**
	 * On post untrashed.
	 *
	 * @param int $post_id Post id.
	 */
	public function on_untrash_post( $post_id ) {
		$conditions = get_post_meta( $post_id, $this->conditions_meta_key, true );
		$type       = get_post_meta( $post_id, $this->type_meta_key, true );

		$this->cache->add( $type, $conditions, $post_id )->save();
	}

	/**
	 * Remove post from cache.
	 *
	 * @param int $post_id Post id.
	 */
	public function remove_post_from_cache( $post_id ) {
		$this->cache->remove( $post_id )->save();
	}

	/**
	 * Change status action.
	 */
	public function change_status_action() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		$post_id = woodmart_clean( $_POST['id'] ); // phpcs:ignore
		$status  = woodmart_clean( $_POST['status'] ); // phpcs:ignore

		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => $status,
			)
		);

		wp_send_json(
			array(
				'new_html' => Admin::get_instance()->get_status_button_template( $post_id ),
			)
		);
	}

	/**
	 * Edit layout.
	 */
	public function edit_layout() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		$post_id = woodmart_clean( $_POST['id'] ); // phpcs:ignore
		$data    = isset( $_POST['data'] ) ? woodmart_clean( $_POST['data'] ) : array(); // phpcs:ignore

		update_post_meta( $post_id, $this->conditions_meta_key, $data );

		$this->cache->regenerate();

		wp_send_json(
			array(
				'new_html' => Admin::get_instance()->get_edit_conditions_template( $post_id ),
			)
		);
	}

	/**
	 * Create new layout.
	 */
	public function create_layout() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		$type            = woodmart_clean( $_POST['type'] ); // phpcs:ignore
		$title           = woodmart_clean( $_POST['name'] ); // phpcs:ignore
		$data            = isset( $_POST['data'] ) ? woodmart_clean( $_POST['data'] ) : array(); // phpcs:ignore
		$predefined_name = isset( $_POST['predefined_name'] ) ? woodmart_clean( $_POST['predefined_name'] ) : ''; // phpcs:ignore

		$post_args = array(
			'post_title' => $title,
			'post_type'  => $this->post_type,
			'meta_input' => array(
				$this->type_meta_key       => $type,
				$this->conditions_meta_key => $data,
			),
		);

		if ( 'single_product' === $type ) {
			$published_products = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => 1,
				)
			);

			if ( empty( $published_products ) ) {
				$create_product_link = add_query_arg(
					array(
						'post_type' => 'product',
					),
					admin_url( 'post-new.php' )
				);

				wp_send_json_error(
					array(
						'message' => sprintf(
							'%s <a href="' . $create_product_link . '">%s</a>',
							esc_html__( 'In order to create a Single product layout, you must first publish at least one product!', 'woodmart' ),
							esc_html__( 'Add new product', 'woodmart' )
						),
					)
				);
			}
		}

		$post_id = wp_insert_post( $post_args );

		if ( $predefined_name ) {
			new Import( $post_id, $type, $predefined_name );
		}

		$url = add_query_arg(
			array(
				'post'           => $post_id,
				'action'         => 'external' === woodmart_get_opt( 'current_builder', 'external' ) && 'elementor' === woodmart_get_current_page_builder() ? 'elementor' : 'edit',
				'classic-editor' => '',
			),
			admin_url( 'post.php' )
		);

		$this->cache->regenerate();

		wp_send_json(
			array(
				'redirect_url' => $url,
			)
		);
	}

	/**
	 * Conditions query.
	 */
	public function conditions_query() {
		check_ajax_referer( 'wd-new-template-nonce', 'security' );

		$query_type = woodmart_clean( $_POST['query_type'] ); // phpcs:ignore
		$search     = isset( $_POST['search'] ) ? woodmart_clean( $_POST['search'] ) : false; // phpcs:ignore

		$items = array();

		switch ( $query_type ) {
			case 'product_cat':
			case 'product_cat_children':
			case 'product_tag':
			case 'product_term':
			case 'product_attr_term':
			case 'product_brand':
			case 'filtered_product_by_term':
				$taxonomy = array();

				if ( 'product_cat' === $query_type || 'product_cat_children' === $query_type || 'product_term' === $query_type ) {
					$taxonomy[] = 'product_cat';
				}
				if ( 'product_tag' === $query_type || 'product_term' === $query_type ) {
					$taxonomy[] = 'product_tag';
				}
				if ( ( 'product_brand' === $query_type || 'product_term' === $query_type ) && taxonomy_exists( 'product_brand' ) ) {
					$taxonomy[] = 'product_brand';
				}
				if ( 'product_attr_term' === $query_type || 'product_term' === $query_type || 'filtered_product_by_term' === $query_type ) {
					$attribute_taxonomies = wc_get_attribute_taxonomies();

					if ( ! empty( $attribute_taxonomies ) ) {
						foreach ( $attribute_taxonomies as $tax ) {
							if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
								$taxonomy[] = wc_attribute_taxonomy_name( $tax->attribute_name );
							}
						}
					}
				}

				$terms = get_terms(
					array(
						'hide_empty' => false,
						'fields'     => 'all',
						'taxonomy'   => $taxonomy,
						'search'     => $search,
					)
				);

				if ( count( $terms ) > 0 ) {
					foreach ( $terms as $term ) {
						$items[] = array(
							'id'   => $term->term_id,
							'text' => $term->name . ' (ID: ' . $term->term_id . ') (Tax: ' . $term->taxonomy . ')',
						);
					}
				}
				break;
			case 'product_attr':
			case 'filtered_product_term':
				foreach ( wc_get_attribute_taxonomies() as $attribute ) {
					$items[] = array(
						'id'   => 'pa_' . $attribute->attribute_name,
						'text' => $attribute->attribute_label . ' (Tax: pa_' . $attribute->attribute_name . ')',
					);
				}
				break;
			case 'filtered_product_stock_status':
				$items = array(
					array(
						'id'   => 'instock',
						'text' => esc_html__( 'In Stock', 'woodmart' ),
					),
					array(
						'id'   => 'onsale',
						'text' => esc_html__( 'On Sale', 'woodmart' ),
					),
					array(
						'id'   => 'onbackorder',
						'text' => esc_html__( 'On backorder', 'woodmart' ),
					),
				);
				break;
			case 'product_type':
				foreach ( wc_get_product_types() as $type => $title ) {
					$items[] = array(
						'id'   => $type,
						'text' => $title,
					);
				}
				break;
			case 'product':
				$posts = get_posts(
					array(
						's'              => $search,
						'post_type'      => 'product',
						'posts_per_page' => 100,
					)
				);

				if ( count( $posts ) > 0 ) {
					foreach ( $posts as $post ) {
						$items[] = array(
							'id'   => $post->ID,
							'text' => $post->post_title . ' (ID: ' . $post->ID . ')',
						);
					}
				}
				break;
		}

		wp_send_json(
			array(
				'results' => $items,
			)
		);
	}

	/**
	 * Add duplicate action.
	 *
	 * @param string[] $actions An array of row action links. Defaults are
	 *                           'Edit', 'Quick Edit', 'Restore', 'Trash',
	 *                           'Delete Permanently', 'Preview', and 'View'.
	 * @param WP_Post  $post The post object.
	 *
	 * @return string[]
	 */
	public function duplicate_action( $actions, $post ) {
		if ( 'woodmart_layout' !== $post->post_type ) {
			return $actions;
		}

		if ( current_user_can( 'edit_posts' ) ) {
			$url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'woodmart_duplicate_post_as_draft',
						'post'   => $post->ID,
					),
					'admin.php'
				),
				'woodmart_duplicate_post_as_draft',
				'duplicate_nonce'
			);

			ob_start();
			?>
			<a href="<?php echo esc_url( $url ); ?>">
				<?php esc_html_e( 'Duplicate', 'woodmart' ); ?>
			</a>
			<?php
			$actions['duplicate'] = ob_get_clean();
		}
		return $actions;
	}
}

Manager::get_instance();
