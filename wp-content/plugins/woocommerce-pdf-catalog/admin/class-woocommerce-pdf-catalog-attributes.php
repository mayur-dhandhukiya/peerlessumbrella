<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://woocommerce.db-dzine.de
 * @since      1.0.0
 *
 * @package    WooCommerce_Attribute_Images
 * @subpackage WooCommerce_Attribute_Images/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WooCommerce_Attribute_Images
 * @subpackage WooCommerce_Attribute_Images/admin
 * @author     Daniel Barenkamp <support@welaunch.io>
 */
class WooCommerce_PDF_Catalog_Attributes extends WooCommerce_PDF_Catalog {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Initialize things in the backend
     *
     * @since 2.1.0
     */
    public function init() 
    {
        global $woocommerce_pdf_catalog_options;

        $this->options = $woocommerce_pdf_catalog_options;

        if(!$this->get_option('enableAttributes')) {
            return false;
        }
        // add_action( 'woocommerce_after_edit_attribute_fields', array($this, 'edit_form_fields'));
        // add_action( 'woocommerce_after_add_attribute_fields', array($this, 'add_form_fields'));

        // add_action( 'woocommerce_attribute_updated', array( $this, 'save_forms' ), 10, 2 );
        // add_action( 'woocommerce_attribute_added', array( $this, 'save_forms' ), 10, 2);


        // Get taxonomies
        $taxonomies = get_taxonomies();

        foreach ($taxonomies as $key => $taxonomy) {
            if (substr($key, 0, 3) !== 'pa_') {
                unset($taxonomies[$key]);
            }
        }

        // Loop through taxonomies
        foreach ( $taxonomies as $taxonomy ) {

            // Add forms
            add_action( $taxonomy . '_add_form_fields', array( $this, 'add_form_fields' ), 10 );
            add_action( $taxonomy . '_edit_form_fields', array( $this, 'edit_form_fields' ), 10, 2 );
            
            add_filter( 'manage_edit-'. $taxonomy .'_columns', array( $this, 'admin_columns' ) );
            add_filter( 'manage_'. $taxonomy .'_custom_column', array( $this, 'admin_column' ), 10, 3 );

            // Save forms
            add_action( 'created_'. $taxonomy, array( $this, 'save_forms' ), 10, 3 );
            add_action( 'edit_'. $taxonomy, array( $this, 'save_forms' ), 10, 3 );

        }
    }

    /**
     * Add Thumbnail field to add form fields
     *
     * @since 2.1.0
     */
    public static function add_form_fields( $taxonomy ) {

        // Enqueue media for media selector
        wp_enqueue_media(); ?>

        <?php
        // Options not needed for Woo
        if ( 'product_cat' != $taxonomy ) : ?>

            <div class="form-field">
                <label for="headerTopLeft"><?php esc_html_e( 'Custom Header Left', 'woocommerce-pdf-catalog' ); ?></label>
                <textarea name="headerTopLeft" id="" cols="30" rows="10"></textarea>
            </div>

            <div class="form-field">
                <label for="footerTopLeft"><?php esc_html_e( 'Custom Footer Left', 'woocommerce-pdf-catalog' ); ?></label>
                <textarea name="footerTopLeft" id="" cols="30" rows="10"></textarea>
            </div>

            <div class="form-field">

                <label for="cover"><?php esc_html_e( 'Custom Cover', 'woocommerce-pdf-catalog' ); ?></label>

                <div>

                    <div id="woocommerce_pdf_catalog-cover" style="float:left;margin-right:10px;">
                        <img class="woocommerce_pdf_catalog-cover-img" src="<?php echo wc_placeholder_img_src(); ?>" width="60px" height="60px" />
                    </div>

                    <input type="hidden" id="woocommerce_pdf_catalog_cover" name="woocommerce_pdf_catalog_cover" />

                    <button type="submit" class="woocommerce_pdf_catalog-remove-cover button"><?php esc_html_e( 'Remove image', 'woocommerce-pdf-catalog' ); ?></button>
                    <button type="submit" class="woocommerce_pdf_catalog-add-cover button"><?php esc_html_e( 'Upload/Add image', 'woocommerce-pdf-catalog' ); ?></button>

                    <script type="text/javascript">

                        // Only show the "remove image" button when needed
                        if ( ! jQuery( '#woocommerce_pdf_catalog_cover' ).val() ) {
                            jQuery( '.woocommerce_pdf_catalog-remove-cover' ).hide();
                        }

                        // Uploading files
                        var file_frame;
                        jQuery( document ).on( 'click', '.woocommerce_pdf_catalog-add-cover', function( event ) {

                            event.preventDefault();

                            // If the media frame already exists, reopen it.
                            if ( file_frame ) {
                                file_frame.open();
                                return;
                            }

                            // Create the media frame.
                            file_frame = wp.media.frames.downloadable_file = wp.media({
                                title    : '<?php esc_html_e( 'Choose an image', 'woocommerce-pdf-catalog' ); ?>',
                                button   : {
                                    text : '<?php esc_html_e( 'Use image', 'woocommerce-pdf-catalog' ); ?>',
                                },
                                multiple : false
                            });

                            // When an image is selected, run a callback.
                            file_frame.on( 'select', function() {
                                attachment = file_frame.state().get( 'selection' ).first().toJSON();
                                jQuery( '#woocommerce_pdf_catalog_cover' ).val( attachment.id );
                                jQuery( '.woocommerce_pdf_catalog-cover-img' ).attr( 'src', attachment.url );
                                jQuery( '.woocommerce_pdf_catalog-remove-cover' ).show();
                            });

                            // Finally, open the modal.
                            file_frame.open();

                        });

                        jQuery( document ).on( 'click', '.woocommerce_pdf_catalog-remove-cover', function( event ) {
                            jQuery( '.woocommerce_pdf_catalog-cover' ).attr( 'src', '<?php echo wc_placeholder_img_src(); ?>' );
                            jQuery( '#woocommerce_pdf_catalog_cover' ).val( '' );
                            jQuery( '.woocommerce_pdf_catalog-remove-cover' ).hide();
                            return false;
                        });

                    </script>

                </div>

                <div class="clear"></div>

            </div>

        <?php endif; ?>

    <?php
    }

    /**
     * Add Thumbnail field to edit form fields
     *
     * @since 2.1.0
     */
    public static function edit_form_fields( $term = "", $taxonomy = "" ) {

        // Enqueue media for media selector
        wp_enqueue_media();

        $term_id = "";

        // Get current taxonomy
        if(isset($term->term_id)) {
            $term_id = $term->term_id;
        } elseif($_GET['page'] == 'product_attributes') {
            $term_id = intval($_GET['edit']);
        }
       

        // Get term data
        $term_data = get_option( 'woocommerce_pdf_catalog_term_data' );

        // Options not needed for Woo
        if ( 'product_cat' != $taxonomy ) :


            // Get thumbnail
            $thumbnail_id  = isset( $term_data[$term_id]['thumbnail'] ) ? $term_data[$term_id]['thumbnail'] : '';
            if ( $thumbnail_id ) {
                $thumbnail_src = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail', false );
                $thumbnail_url = ! empty( $thumbnail_src[0] ) ? $thumbnail_src[0] : '';
            } 

            $headerTopLeft  = isset( $term_data[$term_id]['headerTopLeft'] ) ? $term_data[$term_id]['headerTopLeft'] : '';
            $footerTopLeft  = isset( $term_data[$term_id]['footerTopLeft'] ) ? $term_data[$term_id]['footerTopLeft'] : '';

            ?>

            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="headerTopLeft"><?php esc_html_e( 'Header Left', 'woocommerce-pdf-catalog' ); ?></label>
                </th>
                <td>
                    <textarea name="headerTopLeft" id="" cols="30" rows="10"><?php echo $headerTopLeft ?></textarea>
                </td>
            </tr>

            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="footerTopLeft"><?php esc_html_e( 'Footer Left', 'woocommerce-pdf-catalog' ); ?></label>
                </th>
                <td>
                    <textarea name="footerTopLeft" id="" cols="30" rows="10"><?php echo $footerTopLeft ?></textarea>
                </td>
            </tr>

            <tr class="form-field">

                <th scope="row" valign="top">
                    <label for="cover"><?php esc_html_e( 'Custom Cover', 'woocommerce-pdf-catalog' ); ?></label>
                </th>

                <td>

                    <div id="woocommerce_pdf_catalog-cover" style="float:left;margin-right:10px;">
                        <?php if ( ! empty( $thumbnail_url ) ) { ?>
                            <img class="woocommerce_pdf_catalog-cover-img" src="<?php echo esc_url( $thumbnail_url ); ?>" width="60px" height="60px" />
                        <?php } else { ?>
                            <img class="woocommerce_pdf_catalog-cover-img" src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />
                        <?php } ?>
                    </div>

                    <input type="hidden" id="woocommerce_pdf_catalog_cover" name="woocommerce_pdf_catalog_cover" value="<?php echo esc_attr( $thumbnail_id ); ?>" />

                    <button type="submit" class="woocommerce_pdf_catalog-remove-cover button"<?php if ( ! $thumbnail_id ) echo 'style="display:none;"'; ?>>
                        <?php esc_html_e( 'Remove image', 'woocommerce-pdf-catalog' ); ?>
                    </button>

                    <button type="submit" class="woocommerce_pdf_catalog-add-cover button">
                        <?php esc_html_e( 'Upload/Add image', 'woocommerce-pdf-catalog' ); ?>
                    </button>

                    <script type="text/javascript">

                        // Uploading files
                        var file_frame;

                        jQuery( document ).on( 'click', '.woocommerce_pdf_catalog-add-cover', function( event ) {

                            event.preventDefault();

                            // If the media frame already exists, reopen it.
                            if ( file_frame ) {
                                file_frame.open();
                                return;
                            }

                            // Create the media frame.
                            file_frame = wp.media.frames.downloadable_file = wp.media({
                                title    : '<?php esc_html_e( 'Choose an image', 'woocommerce-pdf-catalog' ); ?>',
                                button   : {
                                    text : '<?php esc_html_e( 'Use image', 'woocommerce-pdf-catalog' ); ?>',
                                },
                                multiple : false
                            } );

                            // When an image is selected, run a callback.
                            file_frame.on( 'select', function() {
                                attachment = file_frame.state().get( 'selection' ).first().toJSON();
                                jQuery( '#woocommerce_pdf_catalog_cover' ).val( attachment.id );
                                jQuery( '.woocommerce_pdf_catalog-cover-img' ).attr( 'src', attachment.url );
                                jQuery( '.woocommerce_pdf_catalog-remove-cover' ).show();
                            } );

                            // Finally, open the modal.
                            file_frame.open();

                        } );

                        jQuery( document ).on( 'click', '.woocommerce_pdf_catalog-remove-cover', function( event ) {
                            jQuery( '.woocommerce_pdf_catalog-cover-img' ).attr( 'src', '<?php echo wc_placeholder_img_src(); ?>' );
                            jQuery( '#woocommerce_pdf_catalog_cover' ).val( '' );
                            jQuery( '.woocommerce_pdf_catalog-remove-cover' ).hide();
                            return false;
                        } );
                    </script>

                    <div class="clear"></div>

                </td>

            </tr>

        <?php endif; ?>

        <?php

    }

    /**
     * Adds the thumbnail to the database
     *
     * @since 2.1.0
     */
    public static function add_term_data( $term_id, $key, $data ) {

        // Validate data
        if ( empty( $term_id ) || empty( $data ) || empty( $key ) ) {
            return;
        }

        // Get thumbnails
        $term_data = get_option( 'woocommerce_pdf_catalog_term_data' );

        // Add to options
        $term_data[$term_id][$key] = $data;
        
        // Update option
        update_option( 'woocommerce_pdf_catalog_term_data', $term_data );

    }

    /**
     * Deletes the thumbnail from the database
     *
     * @since 2.1.0
     */
    public static function remove_term_data( $term_id, $key ) {

        // Validate data
        if ( empty( $term_id ) || empty( $key ) ) {
            return;
        }

        // Get thumbnails
        $term_data = get_option( 'woocommerce_pdf_catalog_term_data' );

        // Add to options
        if ( isset( $term_data[$term_id][$key] ) ) {
            unset( $term_data[$term_id][$key] );
        }

        // Update option
        update_option( 'woocommerce_pdf_catalog_term_data', $term_data );
        
    }

    /**
     * Update thumbnail value
     *
     * @since 2.1.0
     */
    public static function update_thumbnail( $term_id, $thumbnail_id ) {

        // Add thumbnail
        if ( ! empty( $thumbnail_id ) ) {
            self::add_term_data( $term_id, 'thumbnail', $thumbnail_id );
        }

        // Delete thumbnail
        else {
            self::remove_term_data( $term_id, 'thumbnail' );
        }


    }

    /**
     * Update page header image option
     *
     * @since 2.1.0
     */
    public static function update_headerTopLeft( $term_id, $display ) {
        
        // Add option
        if ( ! empty( $display ) ) {
            self::add_term_data( $term_id, 'headerTopLeft', $display );
        }

        // Remove option
        else {
            self::remove_term_data( $term_id, 'headerTopLeft' );
        }

    }

    /**
     * Update page header image option
     *
     * @since 2.1.0
     */
    public static function update_footerTopLeft( $term_id, $display ) {
        
        // Add option
        if ( ! empty( $display ) ) {
            self::add_term_data( $term_id, 'footerTopLeft', $display );
        }

        // Remove option
        else {
            self::remove_term_data( $term_id, 'footerTopLeft' );
        }

    }
        
    /**
     * Save Forms
     *
     * @since 2.1.0
     */
    public static function save_forms( $term_id, $tt_id = '', $taxonomy = '' ) {
        if ( isset( $_POST['woocommerce_pdf_catalog_cover'] ) ) {
            self::update_thumbnail( $term_id, $_POST['woocommerce_pdf_catalog_cover'] );
        }
        if ( isset( $_POST['headerTopLeft'] ) ) {
            self::update_headerTopLeft( $term_id, $_POST['headerTopLeft'] );
        }
        if ( isset( $_POST['footerTopLeft'] ) ) {
            self::update_footerTopLeft( $term_id, $_POST['footerTopLeft'] );
        }
    }

    /**
     * Thumbnail column added to category admin.
     *
     * @since 2.1.0
     */
    public static function admin_columns( $columns ) {
        $columns['woocommerce_pdf_catalog-col'] = esc_attr__( 'PDF Catalog', 'woocommerce-pdf-catalog' );
        return $columns;
    }

    /**
     * Thumbnail column value added to category admin.
     *
     * @since 2.1.0
     */
    public static function admin_column( $columns, $column, $id ) {

        // Add thumbnail to columns
        if ( 'woocommerce_pdf_catalog-col' == $column ) {
            if(isset($_GET['taxonomy'])) {
                echo '<a href="' . get_site_url() . '?pdf-catalog=attr&taxonomy=' . $_GET['taxonomy'] . '&term-id=' . $id . '">Generate PDF</a>';
            }
        }

        // Return columns
        return $columns;

    }

    /**
     * Retrieve term thumbnail
     *
     * @since 2.1.0
     */
    public static function get_cover_id( $term_id = null ) {

        // Get term id if not defined and is tax
        $term_id = $term_id ? $term_id : get_queried_object()->term_id;

        // Return if no term id
        if ( ! $term_id ) {
            return;
        }

        // Get data
        $term_data = get_option( 'woocommerce_pdf_catalog_term_data' );
        $term_data = ! empty( $term_data[ $term_id ] ) ? $term_data[ $term_id ] : '';

        // Return thumbnail ID
        if ( $term_data && ! empty( $term_data['thumbnail'] ) ) {
            return $term_data['thumbnail'];
        }
        
    }

    /**
     * Check if on a tax archive
     *
     * @since 2.1.0
     */
    public static function is_tax_archive() {
        if ( ! is_search() && ( is_tax() || is_category() || is_tag() ) ) {
            return true;
        }
    }
}

/**
 * Get term thumbnail
 *
 * @since 2.1.0
 */
function woocommerce_pdf_catalog_get_cover_id( $term_id = '' ) {

    // Default return
    $thumbnail_id = '';

    // Get term id if not defined and is tax
    $term_id = $term_id ? $term_id : get_queried_object()->term_id;

    // Get thumbnail ID from term
    if ( $term_id ) {

        // Woo Check first
        if ( function_exists( 'get_woocommerce_term_meta' )
            && $woo_id = get_woocommerce_term_meta( $term_id, 'thumbnail_id', true )
        ) {

            $thumbnail_id = $woo_id;

        } else {
                
            // Get data
            $term_data = get_option( 'woocommerce_pdf_catalog_term_data' );
            $term_data = ! empty( $term_data[ $term_id ] ) ? $term_data[ $term_id ] : '';
            
            // Return thumbnail ID
            if ( $term_data && ! empty( $term_data['thumbnail'] ) ) {
                return $term_data['thumbnail'];
            }

        }

    }

    // Apply filters and return
    return apply_filters( 'woocommerce_pdf_catalog_get_cover_id', $thumbnail_id );

}