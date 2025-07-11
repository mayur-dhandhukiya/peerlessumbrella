<?php
/**
 * Import.
 *
 * @package Woodmart
 */

namespace XTS\Admin\Modules;

use Elementor\Plugin;
use WP_Query;
use XTS\Admin\Modules\Import\Helpers;
use XTS\Admin\Modules\Import\Process;
use XTS\Admin\Modules\Import\Remove;
use XTS\Singleton;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Import.
 */
class Import extends Singleton {
	/**
	 * Available versions.
	 *
	 * @var array
	 */
	private $version_list = array();

	/**
	 * Helpers.
	 *
	 * @var Helpers
	 */
	private $helpers;

	/**
	 * Constructor.
	 */
	public function init() {
		$this->include_files();

		$this->helpers = Helpers::get_instance();

		add_action( 'admin_init', array( $this, 'set_versions_list' ) );
		add_action( 'wp_ajax_woodmart_import_action', array( $this, 'import_action' ) );
	}

	/**
	 * Include files.
	 *
	 * @return void
	 */
	public function include_files() {
		$files = array(
			'class-helpers',
			'class-process',
			'class-widgets',
			'class-sliders',
			'class-xml',
			'class-options',
			'class-headers',
			'class-after',
			'class-remove',
			'class-before',
			'class-images',
			'class-menu',
		);

		foreach ( $files as $file ) {
			require_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/admin/modules/import/' . $file . '.php' );
		}
	}

	/**
	 * Import action.
	 */
	public function import_action() {
		check_ajax_referer( 'woodmart-import-nonce', 'security' );

		if ( empty( $_GET['version'] ) || empty( $_GET['type'] ) || empty( $_GET['process'] ) ) {
			return;
		}

		$version = sanitize_text_field( wp_unslash( $_GET['version'] ) );
		$type    = sanitize_text_field( wp_unslash( $_GET['type'] ) );
		$process = sanitize_text_field( wp_unslash( $_GET['process'] ) );

		if ( ! empty( $_GET['hostname'] ) && ! empty( $_GET['username'] ) && ! empty( $_GET['password'] ) ) {
			global $wp_filesystem;

			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
			}

			$ftp_constants = array(
				'hostname'    => 'FTP_HOST',
				'username'    => 'FTP_USER',
				'password'    => 'FTP_PASS',
				'public_key'  => 'FTP_PUBKEY',
				'private_key' => 'FTP_PRIKEY',
			);

			foreach ( $ftp_constants as $key => $constant ) {
				if ( ! empty( $_GET[ $key ] ) ) {
					define( $constant, sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) );
				}
			}

			ob_start();
			$credentials = request_filesystem_credentials( self_admin_url() );
			ob_end_clean();

			if ( false === $credentials || ! WP_Filesystem( $credentials ) ) {
				$status['errorCode']    = 'unable_to_connect_to_filesystem';
				$status['errorMessage'] = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

				// Pass through the error from WP_Filesystem if one was raised.
				if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->has_errors() ) {
					$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
				}

				wp_send_json_error( $status );
			}
		}

		new Process( $version, $process, $type );

		wp_send_json(
			array(
				'preview_url' => $this->get_preview_url( $version, $type ),
				'remove_html' => Remove::get_instance()->popup_content( true, 'import' ),
				'success'     => true,
			)
		);
	}

	/**
	 * Get categories.
	 */
	public function get_categories() {
		$categories = array();

		foreach ( $this->version_list as $version_data ) {
			if ( ! isset( $version_data['categories'] ) ) {
				continue;
			}

			$type = 'version' === $version_data['type'] ? 'version' : 'page';

			foreach ( $version_data['categories'] as $category ) {
				$count = ! empty( $categories[ $type ][ $category['slug'] ]['count'] ) ? $categories[ $type ][ $category['slug'] ]['count'] : 0;

				$categories[ $type ][ $category['slug'] ] = array(
					'data'  => $category,
					'count' => $count + 1,
				);
			}
		}

		return $categories;
	}

	/**
	 * Get all category count by type.
	 *
	 * @param string $count_type Count type.
	 *
	 * @return int|mixed
	 */
	public function get_all_category_count( $count_type ) {
		$output = array();

		foreach ( $this->version_list as $version_data ) {
			$type = 'version' === $version_data['type'] ? 'version' : 'page';

			$output[ $type ] = isset( $output[ $type ] ) ? $output[ $type ] + 1 : 1;
		}

		return $output[ $count_type ];
	}

	/**
	 * Interface.
	 */
	public function render() {
		wp_enqueue_script( 'xts-import', WOODMART_ASSETS . '/js/import.js', array(), WOODMART_VERSION, true );

		$wrapper_classes = '';
		$items_classes   = '';

		$base_versions = $this->helpers->get_base_version();

		if ( $base_versions ) {
			foreach ( $base_versions as $version ) {
				if ( $this->is_imported( $version ) ) {
					$wrapper_classes .= ' xts-base-imported';

					break;
				}
			}
		}

		if ( Remove::get_instance()->has_data_to_remove() ) {
			$wrapper_classes .= ' xts-has-data';
		}

		if ( $this->get_notices() ) {
			$items_classes .= ' xts-disabled';
		}

		$version      = woodmart_get_theme_info( 'Version' );
		$current_base = get_option( 'wd_import_current_base', 'base' );

		wp_enqueue_script( 'woodmart-theme', WOODMART_SCRIPTS . '/scripts/global/helpers.min.js', array(), $version, true );
		wp_enqueue_script( 'xts-lazy-load', WOODMART_SCRIPTS . '/scripts/global/lazyLoading.min.js', array(), $version, true );
		wp_enqueue_style( 'xts-lazy-load', WOODMART_STYLES . '/parts/opt-lazy-load.min.css', array(), $version );

		?>
		<script>
			var woodmart_settings = {
				product_gallery    : {
					thumbs_slider: {
						position: true
					}
				},
				lazy_loading_offset: 0
			};
		</script>
		<div class="xts-box xts-import xts-theme-style<?php echo esc_attr( $wrapper_classes ); ?>" data-current-base="<?php echo esc_attr( $current_base ); ?>">
			<div class="xts-box-header">
				<div class="xts-row">
					<div class="xts-col">
						<h3>
							<?php esc_html_e( 'Prebuilt websites', 'woodmart' ); ?>
						</h3>
						<div class="xts-import-search xts-search xts-i-search">
							<input type="text" placeholder="<?php echo esc_attr__( 'Search by name', 'woodmart' ); ?>" aria-label="<?php echo esc_attr__( 'Search by name', 'woodmart' ); ?>">
						</div>
					</div>
					<div class="xts-col-auto xts-col-remove-content">
						<?php Remove::get_instance()->render(); ?>
					</div>
				</div>
			</div>
			<div class="xts-box-content">
				<div class="xts-row xts-sp-20">
					<div class="xts-col-12 xts-col-lg-3 xts-col-xl-2 xts-col-dummy-nav">
						<?php if ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && 'wizard' !== $_GET['tab'] ) ) : // phpcs:ignore ?>

							<div class="xts-import-cats-list-wrap">
								<div class="xts-buttons-control">
									<div class="xts-import-cats-set xts-btns-set">
										<div class="xts-set-item xts-set-btn xts-active" data-type="version">
											<span>
												<?php esc_html_e( 'Websites', 'woodmart' ); ?>
											</span>
										</div>
										<div class="xts-set-item xts-set-btn" data-type="page">
											<span>
												<?php esc_html_e( 'Additional pages', 'woodmart' ); ?>
											</span>
										</div>
									</div>
								</div>

								<div class="xts-import-cats-list">
									<?php foreach ( $this->get_categories() as $type => $categories ) : ?>
										<?php
										$classes = '';

										if ( 'version' === $type ) {
											$classes = wd_add_cssclass( 'xts-active', $classes );
										}
										?>
										<ul class="xts-filter <?php echo esc_attr( $classes ); ?>" data-type="<?php echo esc_attr( $type ); ?>">
											<li data-cat="*" class="xts-active">
												<a>
													<span><?php esc_html_e( 'All', 'woodmart' ); ?></span>
													<span class="xts-filter-count"><?php echo esc_html( $this->get_all_category_count( $type ) ); ?></span>
												</a>
											</li>
											<?php foreach ( $categories as $category ) : ?>
												<li data-cat="<?php echo esc_attr( $category['data']['slug'] ); ?>">
													<a>
														<span><?php echo esc_html( $category['data']['name'] ); ?></span>
														<span class="xts-filter-count"><?php echo esc_html( $category['count'] ); ?></span>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<div class="xts-col">
						<div class="xts-notices-wrapper xts-notices-sticky xts-import-notices"><?php $this->print_notices(); // Must be in one line. ?></div>
						<div class="xts-import-items xts-row xts-sp-20<?php echo esc_attr( $items_classes ); ?>">
							<?php foreach ( $this->version_list as $slug => $version_data ) : ?>
								<?php
								$item_classes        = '';
								$item_wrap_classes   = '';
								$is_version_imported = $this->is_imported( $slug );

								$type       = $version_data['type'];
								$base       = isset( $version_data['base'] ) ? $version_data['base'] : '';
								$tags       = isset( $version_data['tags'] ) ? $version_data['tags'] : '';
								$categories = isset( $version_data['categories'] ) ? $version_data['categories'] : array();

								if ( 'version' === $type ) {
									$item_wrap_classes = wd_add_cssclass( 'xts-active', $item_classes );
								}
								if ( $is_version_imported ) {
									$item_classes = wd_add_cssclass( 'xts-imported', $item_classes );
								}
								if ( ! defined( 'RS_REVISION' ) && str_contains( $version_data['process'], 'sliders' ) ) {
									$item_classes = wd_add_cssclass( 'xts-need-rs', $item_classes );
								}

								$categories_array = array();
								foreach ( $categories as $category ) {
									$categories_array[] = $category['slug'];
								}

								?>
								<div class="xts-import-item-wrap xts-cat-show xts-col-6 xts-col-xl-4 <?php echo esc_attr( $item_wrap_classes ); ?>">
									<div class="xts-import-item <?php echo esc_attr( $item_classes ); ?>" data-version="<?php echo esc_attr( $slug ); ?>" data-base="<?php echo esc_attr( $base ); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-tags="<?php echo esc_attr( $tags ); ?>" data-cats="<?php echo esc_attr( implode( ',', $categories_array ) ); ?>">
										<div class="xts-import-item-image">
											<img data-src="<?php echo esc_url( WOODMART_DUMMY_URL . $slug . '/preview.jpg' ); ?>" src="<?php echo esc_url( woodmart_lazy_get_default_preview() ); ?>" class="wd-lazy-load wd-lazy-fade" alt="<?php echo esc_attr__( 'Import preview', 'woodmart' ); ?>">
											<div class="xts-box-labels">
												<?php if ( 'main' === $slug ) : ?>
													<div class="xts-box-label xts-label-default xts-i-flag">
														<?php echo esc_attr__( 'Default', 'woodmart' ); ?>
													</div>
												<?php endif; ?>
												<div class="xts-box-label xts-label-warning xts-i-check">
													<?php echo esc_attr__( 'Imported', 'woodmart' ); ?>
												</div>
											</div>
											<a href="<?php echo esc_url( $this->get_demo_preview_url( $slug, $version_data ) ); ?>" class="xts-btn xts-color-white xts-import-item-preview xts-i-view" target="_blank">
												<?php esc_html_e( 'Live preview', 'woodmart' ); ?>
											</a>
											<div class="xts-import-progress-bar" data-progress="0"></div>
											<div class="xts-import-progress-bar-percent">0%</div>
										</div>
										<footer class="xts-import-item-footer">
											<span class="xts-import-item-title">
												<?php echo esc_html( $version_data['title'] ); ?>
											</span>

											<a href="#" class="xts-import-item-btn xts-btn xts-color-alt xts-i-check">
												<?php esc_html_e( 'Activate', 'woodmart' ); ?>
											</a>
											<a href="#" class="xts-import-item-btn xts-bordered-btn xts-color-primary xts-i-import">
												<?php esc_html_e( 'Import', 'woodmart' ); ?>
											</a>
											<a href="<?php echo esc_url( $this->get_preview_url( $slug, $type ) ); ?>" target="_blank" class="xts-view-item-btn xts-btn xts-color-alt xts-i-expand">
												<?php esc_html_e( 'View page', 'woodmart' ); ?>
											</a>
										</footer>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="xts-box-footer">
				<p>
					<?php esc_html_e( 'Import any of the demo versions that will include a home page, a few products, posts, projects, images and menus. You will be able to switch to any demo at any time. You can also remove all the previously imported content.', 'woodmart' ); ?>
				</p>
			</div>
		</div>

		<?php $this->get_request_filesystem_credentials(); ?>
		<?php
	}

	/**
	 * Print notices.
	 */
	public function print_notices() {
		$notices = $this->get_notices();

		if ( $notices ) {
			foreach ( $notices as $notice ) {
				$this->print_notice( $notice['type'], $notice['message'] );
			}
		}
	}

	/**
	 * Print notices.
	 */
	public function get_notices() {
		$notices = array();

		if ( $this->get_required_plugins() ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => sprintf( __( 'You need to install the following plugins to use our import function: <strong><a href="%1$s">%2$s</a></strong>', 'woodmart' ), esc_url( add_query_arg( 'page', rawurlencode( 'xts_plugins' ), admin_url( 'admin.php' ) ) ), implode( ', ', $this->get_required_plugins() ) ),
			);
		}

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			if ( defined( 'WPB_PLUGIN_DIR' ) ) {
				$notices[] = array(
					'type'    => 'warning',
					'message' => __( 'Please, deactivate one of the builders and leave only ONE plugin either <strong>WPBakery page builder</strong> or <strong>Elementor</strong>.', 'woodmart' ),
				);
			}

			if ( class_exists( 'Elementor\Plugin' ) && ! Plugin::$instance->experiments->is_feature_active( 'container' ) ) {
				$notices[] = array(
					'type'    => 'warning',
					'message' => __( 'You need to enable Elementor Flexbox Container feature in Elementor -> Settings -> Features to import our dummy content properly.', 'woodmart' ),
				);
			}
		}

		if ( ! class_exists( 'DOMDocument' ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => __( 'Please, contact the host support and ask them to enable <strong>DOMDocument</strong>.', 'woodmart' ),
			);
		}

		if ( ! function_exists( 'simplexml_load_file' ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => __( 'Please, contact the host support and ask them to enable <strong>simplexml_load_file</strong>.', 'woodmart' ),
			);
		}

		$protocol = is_ssl() ? 'https' : 'http';

		if ( wp_parse_url( get_home_url(), PHP_URL_SCHEME ) !== $protocol || wp_parse_url( get_home_url(), PHP_URL_SCHEME ) !== wp_parse_url( get_site_url(), PHP_URL_SCHEME ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => __( 'In your settings, the HTTP protocol is specified, but you opened the page via HTTPS. This can lead to an error during import. You need to correct the settings and specify the protocol https:// in WordPress -> Settings -> General.', 'woodmart' ),
			);
		}

		return $notices;
	}

	/**
	 * Get required plugins.
	 */
	public function get_required_plugins() {
		$plugins = array();

		if ( ! class_exists( 'WOODMART_Post_Types' ) ) {
			$plugins[] = 'Woodmart Core';
		}

		if ( ! function_exists( 'is_shop' ) ) {
			$plugins[] = 'Woocommerce';
		}

		return $plugins;
	}

	/**
	 * Print notice.
	 *
	 * @param string $type    Type.
	 * @param string $message Message.
	 */
	private function print_notice( $type, $message ) {
		?>
		<div class="xts-notice xts-<?php echo esc_attr( $type ); ?>">
			<?php echo wp_kses( $message, woodmart_get_allowed_html() ); ?>
		</div>
		<?php
	}

	/**
	 * Is version imported.
	 *
	 * @param string $slug Slug.
	 *
	 * @return bool
	 */
	public function is_imported( $slug ) {
		return in_array( $slug, get_option( 'wd_import_imported_versions', array() ), true );
	}

	/**
	 * Get demo preview URL.
	 *
	 * @param string $slug         Slug.
	 * @param array  $version_data Data.
	 *
	 * @return string
	 */
	private function get_demo_preview_url( $slug, $version_data ) {
		$url = WOODMART_DEMO_URL . $slug . '/';

		if ( 'version' === $version_data['type'] ) {
			$url = WOODMART_DEMO_URL . 'demo-' . $slug . '/demo/' . $slug . '/';
		}

		if ( isset( $version_data['link'] ) ) {
			$url = $version_data['link'];
		}

		return $url;
	}

	/**
	 * Get preview URL.
	 *
	 * @param string $slug Slug.
	 * @param string $type Type.
	 *
	 * @return string
	 */
	private function get_preview_url( $slug, $type ) {
		$query_args = array(
			'post_type'              => 'page',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		if ( 'version' === $type ) {
			$query_args['title'] = 'Home ' . $slug;

			$query = new WP_Query( $query_args );
			$page  = ! empty( $query->post ) ? $query->post : null;
		} else {
			$page = get_page_by_path( $slug, OBJECT, array( 'page' ) );
		}

		if ( ! $page ) {
			$query_args['title'] = str_replace( '-', ' ', $slug );

			$query = new WP_Query( $query_args );
			$page  = ! empty( $query->post ) ? $query->post : null;
		}

		if ( ! $page ) {
			return '';
		}

		return get_permalink( $page->ID );
	}

	/**
	 * Set versions list.
	 */
	public function set_versions_list() {
		$this->version_list = woodmart_get_config( 'versions' );
		$current_builder    = $this->helpers->get_page_builder();

		$base_versions = $this->helpers->get_base_version();

		if ( $base_versions ) {
			foreach ( $base_versions as $version ) {
				unset( $this->version_list[ $version ] );
			}
		}

		if ( 'gutenberg' === $current_builder ) {
			foreach ( $this->version_list as $key => $value ) {
				if ( isset( $value[ $current_builder ] ) && ! $value[ $current_builder ] ) {
					unset( $this->version_list[ $key ] );
				}
			}
		}
	}

	private function get_request_filesystem_credentials() {
		ob_start();

		wp_print_request_filesystem_credentials_modal();

		$credentials = ob_get_clean();

		if ( $credentials ) {
			echo '<div class="xts-request-credentials">' . $credentials . '</div>'; // phpcs:ignore
		}
	}
}

Import::get_instance();
