<?php 
if ( ! defined( 'WPINC' ) ) {
    die; 
}

if ( !class_exists( 'Addify_Registration_Fields_Addon_Admin' ) ) { 

	class Addify_Registration_Fields_Addon_Admin extends Addify_Registration_Fields_Addon {

		public function __construct() {
			
			add_action( 'admin_enqueue_scripts', array( $this, 'afreg_admin_scripts' ) );
			//Custom meta boxes
			add_action( 'admin_init', array( $this, 'afreg_register_metaboxes' ), 10 );
			add_action( 'save_post', array($this, 'afreg_meta_box_save' ));
			add_filter('post_row_actions', array($this, 'afreg_remove_bulk_actions'),10,1);
			add_filter( 'manage_afreg_fields_posts_columns', array( $this, 'afreg_custom_columns' ) );
			add_action( 'manage_afreg_fields_posts_custom_column' , array($this, 'afreg_custom_column'), 10, 2 );
			add_filter('bulk_actions-edit-afreg_fields', array($this, 'afreg_bulk_action'));
			add_filter( 'handle_bulk_actions-edit-afreg_fields', array($this, 'afreg_bulk_action_handler'), 10, 3 );
			add_action( 'admin_notices', array( $this, 'afreg_bulk_action_admin_notice' ) );
			add_action( 'admin_menu', array( $this, 'afreg_custom_menu_admin' ) );
			add_action('admin_init', array($this, 'afreg_options'));
			add_action( 'edit_user_profile', array($this, 'afreg_profile_fields' ));
			add_action( 'edit_user_profile_update', array($this, 'afreg_update_profile_fields' ));
		}

		public function afreg_admin_scripts() { 
			
			wp_enqueue_script( 'color-spectrum-js', plugins_url( '/js/afreg_color_spectrum.js', __FILE__ ), false );
			wp_enqueue_style( 'color-spectrum-css', plugins_url( '/css/afreg_color_spectrum.css', __FILE__ ), false );
			wp_enqueue_style( 'afreg-admin-css', plugins_url( '/css/afreg_admin.css', __FILE__ ), false );
			wp_enqueue_script( 'afreg-admin-js', plugins_url( '/js/afreg_admin.js', __FILE__ ), false );
			$afreg_data = array(
			    'admin_url'  => admin_url('admin-ajax.php'),
			    
			);
			wp_localize_script( 'afreg-admin-js', 'afreg_php_vars', $afreg_data );
			
		}

		public function afreg_register_metaboxes() {

	        add_meta_box( 'afreg_field_details', esc_html__( "Field Details", "addify_reg" ), array( $this, 'afreg_field_details_callback' ), 'afreg_fields', 'normal', 'high' );
	        add_meta_box( 'afreg_field_formating', esc_html__( "Field Formating", "addify_reg" ), array( $this, 'afreg_field_formating_callback' ), 'afreg_fields', 'normal', 'high' );
	        add_meta_box( 'afreg_field_status', esc_html__( "Field Status", "addify_reg" ), array( $this, 'afreg_field_status_callback' ), 'afreg_fields', 'side', 'high' );
	        
	    }

	    public function afreg_field_details_callback() {
	    	global $post;
	    	wp_nonce_field( 'afreg_fields_nonce', 'afreg_field_nonce' );
	    	$afreg_field_type = get_post_meta( $post->ID, 'afreg_field_type', true );
	    	$afreg_field_options = unserialize(get_post_meta( $post->ID, 'afreg_field_option', true )); 
	    	$afreg_field_file_size = get_post_meta( $post->ID, 'afreg_field_file_size', true );
	    	$afreg_field_file_type = get_post_meta( $post->ID, 'afreg_field_file_type', true );
	    	
	    	?>
	    	<div class="addify_reg">
		    	<div class="meta_field_full">
		    		<label for="afreg_field_label"><?php echo esc_html__("Field Label", "addify_reg"); ?></label>
	    			<p class="afreg_field_label_msg"><?php echo esc_html__( "Enter the text in above title field, that will become field label.", "addify_reg" ); ?></p>
	    		</div>

	    		<div class="meta_field_full">
		    		<label for="afreg_field_type"><?php echo esc_html__("Field Type", "addify_reg"); ?></label>
	    			<select name="afreg_field_type" id="afreg_field_type" class="afreg_field_select" onchange="afreg_show_options(this.value)">
	    				<option value="text" <?php echo selected(esc_attr($afreg_field_type), 'text'); ?>><?php echo esc_html__("Text", "addify_reg"); ?></option>
	                    <option value="textarea" <?php echo selected(esc_attr($afreg_field_type), 'textarea'); ?>><?php echo esc_html__("Textarea", "addify_reg"); ?></option>
	                    <option value="email" <?php echo selected(esc_attr($afreg_field_type), 'email'); ?>><?php echo esc_html__("Email", "addify_reg"); ?></option>
	                    <option value="select" <?php echo selected(esc_attr($afreg_field_type), 'select'); ?>><?php echo esc_html__("Selectbox", "addify_reg"); ?></option>
	                    <option value="multiselect" <?php echo selected(esc_attr($afreg_field_type), 'multiselect'); ?>><?php echo esc_html__("Multi Selectbox", "addify_reg"); ?></option>
	                    <option value="checkbox" <?php echo selected(esc_attr($afreg_field_type), 'checkbox'); ?>><?php echo esc_html__("Checkbox", "addify_reg"); ?></option>
	                    <option value="radio" <?php echo selected(esc_attr($afreg_field_type), 'radio'); ?>><?php echo esc_html__("Radio Button", "addify_reg"); ?></option>
	                    <option value="number" <?php echo selected(esc_attr($afreg_field_type), 'number'); ?>><?php echo esc_html__("Number", "addify_reg"); ?></option>
	                    <option value="password" <?php echo selected(esc_attr($afreg_field_type), 'password'); ?>><?php echo esc_html__("Password", "addify_reg"); ?></option>
	                    <option value="fileupload" <?php echo selected(esc_attr($afreg_field_type), 'fileupload'); ?>><?php echo esc_html__("File Upload", "addify_reg"); ?></option>
	                    <option value="color" <?php echo selected(esc_attr($afreg_field_type), 'color'); ?>><?php echo esc_html__("Color Picker", "addify_reg"); ?></option>
	                    <option value="datepicker" <?php echo selected(esc_attr($afreg_field_type), 'datepicker'); ?>><?php echo esc_html__("Date Picker", "addify_reg"); ?></option>
	                    <option value="timepicker" <?php echo selected(esc_attr($afreg_field_type), 'timepicker'); ?>><?php echo esc_html__("Time Picker", "addify_reg"); ?></option>
	                    <option value="googlecaptcha" <?php echo selected(esc_attr($afreg_field_type), 'googlecaptcha'); ?>><?php echo esc_html__("Google reCAPTCHA", "addify_reg"); ?></option>
	    			</select>
	    		</div>

	    		<div id="afreg_recaptcha" class="meta_field_full">
	    			<p class="afreg_field_label_msg"><?php echo esc_html__( "For google reCaptcha field you must enter correct site key and secret key in our module settings. Without these keys google reCaptcha will not work.", "addify_reg" ); ?></p>
	    		</div>

	    		<div class="meta_field_full afreg_fileupload">
		    		<label for="afreg_field_file_size"><?php echo esc_html__("File Upload Size(MB)", "addify_reg"); ?></label>
	    			<input type="number" name="afreg_field_file_size" id="afreg_field_file_size" class="" value="<?php echo esc_attr($afreg_field_file_size); ?>" />
	    		</div>

	    		<div class="meta_field_full afreg_fileupload">
		    		<label for="afreg_field_file_type"><?php echo esc_html__("Allowed File Types(Add Comma(,) separated types. e.g png,jpg,gif)", "addify_reg"); ?></label>
	    			<input type="text" name="afreg_field_file_type" id="afreg_field_file_type" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_file_type); ?>" />
	    		</div>

	    		<div class="meta_field_full" id="afreg_field_options">
	    			<label for="afreg_field_options"><?php echo esc_html__("Field Options", "addify_reg"); ?></label>
	    			<div class="afreg_field_options">
	    				<table cellspacing="0" cellpadding="0" border="1" width="100%">
	    					<thead>
		    					<tr>
		    						<th><?php echo esc_html__("Option Value", "addify_reg"); ?></th>
		    						<th><?php echo esc_html__("Field Label/Text", "addify_reg"); ?></th>
		    						<th><?php echo esc_html__("Action", "addify_reg"); ?></th>
		    					</tr>
	    					</thead>
	    					<tbody>
	    						<?php 
	    						$afreg_a = 0;
	    						if(!empty($afreg_field_options)) {
	    						foreach($afreg_field_options as $afreg_field_option) { ?>
	    							<tr>
	    								<td>
	    									<input type="text" name="afreg_field_option[<?php echo intval($afreg_a); ?>][field_value]" id="afreg_field_option_value<?php echo intval($afreg_a); ?>" class="option_field" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" />
	    								</td>
	    								<td>
	    									<input type="text" name="afreg_field_option[<?php echo intval($afreg_a); ?>][field_text]" id="afreg_field_option_value<?php echo intval($afreg_a); ?>" class="option_field" value="<?php echo esc_attr($afreg_field_option['field_text']); ?>" />
	    								</td>
	    								<td><button type="button" class="button button-danger"><?php echo esc_html__("Remove Option", "addify_reg"); ?></button></td>
	    							</tr>
	    						<?php $afreg_a++; } } ?>
	    					</tbody>
	    					<tfoot>
	    						<tr id="NewField"></tr>
	    					</tfoot>
	    					
	    				</table>

	    				<div class="afreg_addbt"><button type="button" class="button-primary" onclick="afreg_add_option()"><?php echo esc_html__("Add New Option", "addify_reg"); ?></button></div>
	    			</div>
	    		</div>

    		</div>

	    	<?php 
	    }

	    public function afreg_field_formating_callback() {
	    	global $post;
	    	wp_nonce_field( 'afreg_fields_nonce', 'afreg_field_nonce' );
	    	$afreg_field_required = get_post_meta( $post->ID, 'afreg_field_required', true );
	    	$afreg_field_read_only = get_post_meta( $post->ID, 'afreg_field_read_only', true );
	    	$afreg_field_width = get_post_meta( $post->ID, 'afreg_field_width', true );
	    	$afreg_field_placeholder = get_post_meta( $post->ID, 'afreg_field_placeholder', true );
	    	$afreg_field_description = get_post_meta( $post->ID, 'afreg_field_description', true );
	    	$afreg_field_css = get_post_meta( $post->ID, 'afreg_field_css', true );
	    	?>
	    	<div class="addify_reg">
		    	<div class="meta_field_formating afreg_recaptchahide">
		    		<label for="afreg_field_required"><?php echo esc_html__("Required Field", "addify_reg"); ?></label>
	    			<input type="checkbox" name="afreg_field_required" id="afreg_field_required" <?php echo checked(esc_attr($afreg_field_required), "on"); ?> />
	    		</div>

	    		<div class="meta_field_formating afreg_recaptchahide">
		    		<label for="afreg_field_read_only"><?php echo esc_html__("Read Only Field(Customer can not update this from My Account page)", "addify_reg"); ?></label>
	    			<input type="checkbox" name="afreg_field_read_only" id="afreg_field_read_only" <?php echo checked(esc_attr($afreg_field_read_only), "on"); ?> />
	    		</div>

	    		<div class="meta_field_formating afreg_recaptchahide">
		    		<label for="afreg_field_width"><?php echo esc_html__("Field Width", "addify_reg"); ?></label>
		    		<select name="afreg_field_width" id="afreg_field_width">
		    			<option value="full" <?php echo selected(esc_attr($afreg_field_width), 'full'); ?>><?php echo esc_html__("Full Width", "addify_reg"); ?></option>
		    			<option value="half" <?php echo selected(esc_attr($afreg_field_width), 'half'); ?>><?php echo esc_html__("Half Width", "addify_reg"); ?></option>
		    		</select>
	    			
	    		</div>

	    		<div class="meta_field_full afreg_recaptchahide">
		    		<label for="afreg_field_placeholder"><?php echo esc_html__("Field Placeholder Text (Only works in WooCommerce Form)", "addify_reg"); ?></label>
	    			<input type="text" name="afreg_field_placeholder" id="afreg_field_placeholder" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_placeholder); ?>" />
	    		</div>

	    		<div class="meta_field_full">
		    		<label for="afreg_field_description"><?php echo esc_html__("Field Description", "addify_reg"); ?></label>
	    			<input type="text" name="afreg_field_description" id="afreg_field_description" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_description); ?>" />
	    		</div>

	    		<div class="meta_field_full afreg_recaptchahide">
		    		<label for="afreg_field_css"><?php echo esc_html__("Field Custom Css Class (Only works in WooCommerce Form)", "addify_reg"); ?></label>
	    			<input type="text" name="afreg_field_css" id="afreg_field_css" class="afreg_field_text" value="<?php echo esc_attr($afreg_field_css); ?>" />
	    		</div>

    		</div>

	    	<?php 
	    }

	    public function afreg_field_status_callback() {

	    	global $post;
	    	wp_nonce_field( 'afreg_fields_nonce', 'afreg_field_nonce' );
	    	?>
	    		<div class="addify_reg">

	    			<div class="meta_field_full">
		    			<label for="afreg_field_sort_order"><?php echo esc_html__("Field Sort Order", "addify_reg"); ?></label>
	    				<input type="number" min="0" name="afreg_field_sort_order" id="afreg_field_sort_order" value="<?php echo esc_attr($post->menu_order); ?>" />
	    			</div>

		    		<div class="meta_field_formating">
			    		<label for="afreg_field_status"><?php echo esc_html__("Field Status", "addify_reg"); ?></label>
			    		<select name="afreg_field_status" id="afreg_field_status">
			    			<option value="publish" <?php echo selected(esc_attr($post->post_status), 'publish'); ?>><?php echo esc_html__("Active", "addify_reg"); ?></option>
			    			<option value="draft" <?php echo selected(esc_attr($post->post_status), 'draft'); ?>><?php echo esc_html__("Inactive", "addify_reg"); ?></option>
			    		</select>
		    		</div>
	    		</div>
	    	<?php
	    }

	    public function afreg_meta_box_save( $post_id ) {

	    	// return if we're doing an auto save
	    	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	    	if ( get_post_status( $post_id ) === 'auto-draft' ) {
			    return;
			}

	    	// if our nonce isn't there, or we can't verify it, return
		    if( !isset( $_POST['afreg_field_nonce'] ) || !wp_verify_nonce( $_POST['afreg_field_nonce'], 'afreg_fields_nonce' ) ) return;

		    // if our current user can't edit this post, return
		    if( !current_user_can( 'edit_post' ) ) return;
		   
        	if( isset( $_POST['afreg_field_type'] ) ) { 
        		update_post_meta( intval($post_id), 'afreg_field_type', esc_attr( $_POST['afreg_field_type'] ) );
        	}

        	remove_action( 'save_post', array($this, 'afreg_meta_box_save'));

        	wp_update_post( array( 'ID' => intval($post_id), 'post_status' => esc_attr($_POST["afreg_field_status"]), 'menu_order' => esc_attr($_POST["afreg_field_sort_order"]) ) );

        	add_action( 'save_post', array($this, 'afreg_meta_box_save' ));

        	if( isset( $_POST['afreg_field_option'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_option', serialize($_POST['afreg_field_option']));
        	}

        	if( isset( $_POST['afreg_field_option_text'] ) ) {
        	}

        	if( isset( $_POST['afreg_field_required'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_required', esc_attr( $_POST['afreg_field_required'] ) );
        	} else {
        		update_post_meta( intval($post_id), 'afreg_field_required', 'off' );	
        	}

        	if( isset( $_POST['afreg_field_read_only'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_read_only', esc_attr( $_POST['afreg_field_read_only'] ) );
        	} else {
        		update_post_meta( intval($post_id), 'afreg_field_read_only', 'off' );
        	}

        	if( isset( $_POST['afreg_field_width'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_width', esc_attr( $_POST['afreg_field_width'] ) );
        	}

        	if( isset( $_POST['afreg_field_placeholder'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_placeholder', sanitize_text_field( $_POST['afreg_field_placeholder'] ) );
        	}

        	if( isset( $_POST['afreg_field_description'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_description', sanitize_text_field( $_POST['afreg_field_description'] ) );
        	}

        	if( isset( $_POST['afreg_field_css'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_css', sanitize_text_field( $_POST['afreg_field_css'] ) );
        	}

        	if( isset( $_POST['afreg_field_file_size'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_file_size', sanitize_text_field( $_POST['afreg_field_file_size'] ) );
        	}

        	if( isset( $_POST['afreg_field_file_type'] ) ) {
        		update_post_meta( intval($post_id), 'afreg_field_file_type', sanitize_text_field( $_POST['afreg_field_file_type'] ) );
        	}

	    }

	    public function afreg_remove_bulk_actions( $actions ){
		    unset($actions['view']);
		    return $actions;
	    }

	    public function afreg_custom_columns($columns) {
		    
		    unset($columns['date']);
		    $columns['afreg_field_type'] = esc_html__( 'Field Type', 'addify_reg' );
		    $columns['afreg_field_status'] = esc_html__( 'Status', 'addify_reg' );
		    $columns['afreg_field_sort_order'] = esc_html__( 'Sort Order', 'addify_reg' );
		    

		    return $columns;
		}

		public function afreg_custom_column( $column, $post_id ) {
			$afreg_post = get_post($post_id);
		    switch ( $column ) {
		        case 'afreg_field_type' :
		            echo esc_attr(ucfirst(get_post_meta($post_id, 'afreg_field_type', true)));
		            break;

		        case 'afreg_field_status' :
		            if($afreg_post->post_status == 'publish') {
		            	echo "Active";
		            } else {
		            	echo "Inactive";
		            }
		            break;

		        case 'afreg_field_sort_order' :
		            echo esc_attr($afreg_post->menu_order);
		            break;

		    }
		}

		public function afreg_bulk_action($bulk_actions) {
		  $bulk_actions['afreg_active'] = esc_html__( 'Active', 'addify_reg' );
		  $bulk_actions['afreg_inactive'] = esc_html__( 'Inactive', 'addify_reg' );
		  return $bulk_actions;
		}

		public function afreg_bulk_action_handler( $redirect_to, $action_name, $post_ids ) {

			if ( 'afreg_active' === $action_name ) {

				foreach ( $post_ids as $post_id ) { 
			      wp_update_post( array( 'ID' => intval($post_id), 'post_status' => 'publish' ) );
			    } 

			    $redirect_to = add_query_arg( 'afreg_active', count( $post_ids ), $redirect_to ); 
    			return $redirect_to; 

			} elseif ( 'afreg_inactive' === $action_name ) {

				foreach ( $post_ids as $post_id ) { 
			      wp_update_post( array( 'ID' => intval($post_id), 'post_status' => 'draft' ) );
			    } 

			    $redirect_to = add_query_arg( 'afreg_inactive', count( $post_ids ), $redirect_to ); 
    			return $redirect_to;
			} else {
				return $redirect_to;
			}

		} 

		public function afreg_bulk_action_admin_notice() { 

			$afreg_allowed_tags = array(
	        'a' => array(
	        'class' => array(),
	        'href'  => array(),
	        'rel'   => array(),
	        'title' => array(),
	        ),
	        'b' => array(),
	        
	        'div' => array(
	        'class' => array(),
	        'title' => array(),
	        'style' => array(),
	        ),
	        'p' => array(
	        'class' => array(),
	        ),
	        'strong' => array(),
	        
	        );

		    if ( ! empty( $_REQUEST['afreg_active'] ) ) { 
		    	$posts_count = intval( $_REQUEST['afreg_active'] ); 
		    	$afreg_woo_check = '<div id="message" class="updated notice notice-success is-dismissible"><p>'.$posts_count.' field(s) are set to active.</p><button type="button" class="notice-dismiss"></button></div>';
            	echo wp_kses( __( $afreg_woo_check, 'addify_reg' ), $afreg_allowed_tags);

		    } else if(! empty( $_REQUEST['afreg_inactive'] ) ) {
		  		$posts_count = intval( $_REQUEST['afreg_inactive'] ); 
		  		$afreg_woo_check = '<div id="message" class="updated notice notice-success is-dismissible"><p>'.$posts_count.' field(s) are set to inactive.</p><button type="button" class="notice-dismiss"></button></div>';
            	echo wp_kses( __( $afreg_woo_check, 'addify_reg' ), $afreg_allowed_tags);
		    } else {

		    }
		} 

		public function afreg_custom_menu_admin() {	
			
			add_submenu_page(
			    'edit.php?post_type=afreg_fields',
			    esc_html__( 'Settings', 'addify_reg' ),
			    esc_html__( 'Settings', 'addify_reg' ),
			    'manage_options',
			    'afreg-fields-settings',
			    array($this, 'afreg_settings_page')
			);
	    }

	    public function afreg_settings_page() {

	    	if( isset( $_GET[ 'tab' ] ) ) {  
	            $active_tab = $_GET[ 'tab' ];  
	        } else {
	            $active_tab = 'tab_one';
	        }
	        ?>
	        	<div class="wrap">
	        		<h2><?php echo esc_html__('Registration Fields Settings', 'addify_reg'); ?></h2>
	        		<?php settings_errors(); ?> 

	        		<h2 class="nav-tab-wrapper">  
	                
		                <a href="?post_type=afreg_fields&page=afreg-fields-settings&tab=tab_one" class="nav-tab <?php echo esc_attr($active_tab) == 'tab_one' ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Settings', 'addify_reg'); ?></a> 
	            	</h2>

	            	<form method="post" action="options.php"> 
			            <?php
			                if( $active_tab == 'tab_one' ) {  
			                    settings_fields( 'setting-group-1' );
			                    do_settings_sections( 'addify-registration-1' );
			                } 
			            ?>
		                <?php submit_button(); ?> 
		            </form> 

	        	</div>
	        <?php 

	    }

	    public function afreg_options() {

	    	add_settings_section(  
		        'page_1_section',         // ID used to identify this section and with which to register options  
		        '',   // Title to be displayed on the administration page  
		        array($this, 'afreg_page_1_section_callback'), // Callback used to render the description of the section  
		        'addify-registration-1'                           // Page on which to add this section of options  
		    );

		    add_settings_field (   
		        'afreg_additional_fields_section_title',                      // ID used to identify the field throughout the theme  
		        esc_html__('Additional Fields Section Title', 'addify_reg'),    // The label to the left of the option interface element  
		        array($this, 'afreg_additional_fields_section_title_callback'),   // The name of the function responsible for rendering the option interface  
		        'addify-registration-1',                          // The page on which this option will be displayed  
		        'page_1_section',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            esc_html__('This is the title for the section where additional fields are displayed on front end registration form.', 'addify_reg'),
		        )  
		    );  
		    register_setting(  
		        'setting-group-1',  
		        'afreg_additional_fields_section_title'  
		    );

		    add_settings_section(  
		        'page_2_section',         // ID used to identify this section and with which to register options  
		        '',   // Title to be displayed on the administration page  
		        array($this, 'afreg_page_2_section_callback'), // Callback used to render the description of the section  
		        'addify-registration-1'                           // Page on which to add this section of options  
		    );

		    add_settings_field (   
		        'afreg_site_key',                      // ID used to identify the field throughout the theme  
		        esc_html__('Site Key', 'addify_reg'),    // The label to the left of the option interface element  
		        array($this, 'afreg_site_key_callback'),   // The name of the function responsible for rendering the option interface  
		        'addify-registration-1',                          // The page on which this option will be displayed  
		        'page_2_section',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            esc_html__('This is gogle reCaptcha site key, you can get this from google. With this key google reCaptcha will not work.', 'addify_reg'),
		        )  
		    );  
		    register_setting(  
		        'setting-group-1',  
		        'afreg_site_key'  
		    );

		    add_settings_field (   
		        'afreg_secret_key',                      // ID used to identify the field throughout the theme  
		        esc_html__('Secret Key', 'addify_reg'),    // The label to the left of the option interface element  
		        array($this, 'afreg_secret_key_callback'),   // The name of the function responsible for rendering the option interface  
		        'addify-registration-1',                          // The page on which this option will be displayed  
		        'page_2_section',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            esc_html__('This is gogle reCaptcha secret key, you can get this from google. With this key google reCaptcha will not work.', 'addify_reg'),
		        )  
		    );  
		    register_setting(  
		        'setting-group-1',  
		        'afreg_secret_key'  
		    );
	    }

	    function afreg_page_1_section_callback() { ?>

		   <p><?php echo esc_html__("Manage registration module settings from here.", "addify_reg"); ?></p>

		<?php } // function afreg_page_1_section_callback

		function afreg_additional_fields_section_title_callback($args) {  
		    ?>
		    <input type="text" id="afreg_additional_fields_section_title" class="setting_fields" name="afreg_additional_fields_section_title" value="<?php echo get_option('afreg_additional_fields_section_title') ?>">
		    <p class="description afreg_additional_fields_section_title"> <?php echo esc_attr($args[0]); ?> </p>
		    <?php      
		} // end afreg_additional_fields_section_title_callback 

		function afreg_page_2_section_callback() { ?>

		   <h3><?php echo esc_html__("Google reCaptcha Settings", "addify_reg"); ?></h3>

		<?php } // function afreg_page_2_section_callback

		function afreg_site_key_callback($args) {  
		    ?>
		    <input type="text" id="afreg_site_key" class="setting_fields" name="afreg_site_key" value="<?php echo get_option('afreg_site_key') ?>">
		    <p class="description afreg_site_key"> <?php echo esc_attr($args[0]); ?> </p>
		    <?php      
		} // end afreg_site_key_callback 

		function afreg_secret_key_callback($args) {  
		    ?>
		    <input type="text" id="afreg_secret_key" class="setting_fields" name="afreg_secret_key" value="<?php echo get_option('afreg_secret_key') ?>">
		    <p class="description afreg_secret_key"> <?php echo esc_attr($args[0]); ?> </p>
		    <?php      
		} // end afreg_secret_key_callback 

		public function afreg_profile_fields() {

			?>
				<h3><?php echo esc_html__(get_option('afreg_additional_fields_section_title'), 'addify_reg'); ?></h3>
				<div class="afreg_extra_fields">
				<table class="form-table">
					<?php 

						$afreg_args = array( 
							'posts_per_page' => -1,
							'post_type' => 'afreg_fields',
							'post_status' => 'publish',
							'orderby' => 'menu_order',
							'order' => 'ASC'
						);
						$afreg_extra_fields = get_posts($afreg_args);
						if(!empty($afreg_extra_fields)) {

							foreach($afreg_extra_fields as $afreg_field) {

								$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
		    					$afreg_field_options = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
						    	$afreg_field_placeholder = get_post_meta( intval($afreg_field->ID), 'afreg_field_placeholder', true );
						    	$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );

						    	$value = get_user_meta( intval($_GET['user_id']), 'afreg_additional_'.intval($afreg_field->ID), true );

						    	if($afreg_field_type == 'text') { ?>
							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="text" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>
						    	<?php } else if($afreg_field_type == 'textarea') { ?>

						    		<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<textarea class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php echo esc_attr($value); ?></textarea>
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

						    	<?php } else if($afreg_field_type == 'email') { ?>

						    		<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="email" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'select') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<select class="input-select " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
												<?php foreach($afreg_field_options as $afreg_field_option) { ?>
													<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php echo selected(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>>
														<?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
													</option>
												<?php } ?>
											</select>
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'multiselect') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<select class="input-select " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" multiple>
												<?php foreach($afreg_field_options as $afreg_field_option) {

													$db_values = explode(', ', $value);

													if(!empty($db_values)) { ?>
														<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(in_array(esc_attr($afreg_field_option['field_value']),  $db_values)) echo "selected"; ?>>
															<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
													<?php } else { ?>
													<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>">
														<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
													</option>
												<?php } } ?>
											</select>
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'checkbox') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="checkbox" class="input-checkbox " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" <?php echo checked(esc_attr($value), 'on'); ?>  />
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'radio') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<?php foreach($afreg_field_options as $afreg_field_option) { ?>
												<input type="radio" class="input-radio " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php echo checked(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>  />
												<span class="afreg_radio"><?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?></span>
											<?php } ?>
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'number') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="number" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							   	<?php } else if($afreg_field_type == 'password') { ?>

							   		<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="password" class="regular-text" value="<?php echo esc_attr($value); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							   	<?php } else if($afreg_field_type == 'fileupload') { ?>

							   		<tr>
							   			<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php echo esc_html__('Current', 'addify_reg') ?> <?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>

							   			<td>
							   				<?php 
					
											$curr_image = AFREG_URL.'uploaded_files/'.$value;
											$ext = pathinfo($curr_image, PATHINFO_EXTENSION);
											if($ext == 'pdf' || $ext == 'PDF') { ?>
												<a href="<?php echo AFREG_URL; ?>uploaded_files/<?php echo esc_attr($value); ?>" target="_blank">
													<img src="<?php echo AFREG_URL; ?>images/pdf.png" width="150" height="150" title="Click to View" />
												</a>
											<?php } else { ?>
												<img src="<?php echo AFREG_URL; ?>uploaded_files/<?php echo esc_attr($value); ?>" width="150" height="150" />
											<?php } ?>
							   			</td>


							   		</tr>

							   		<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="file" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'color') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="color" class="input-text color_sepctrumm" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>

											<script>
						
											jQuery(".color_sepctrumm").spectrum({
											    color: "<?php echo $value; ?>",
											    preferredFormat: "hex",
											});

											</script>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'datepicker') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="date" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

							    <?php } else if($afreg_field_type == 'timepicker') { ?>

							    	<tr>
							    		<th><label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label></th>
										<td>
											<input type="time" class="input-text " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
											<br>
											<span class="description"></span>
											<?php if(!empty($afreg_field_description)) { ?>
												<span class="description"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
											<?php } ?>
										</td>
							    	</tr>

						    	<?php } 
							}
						}

					?>
					
				</table>
			</div>
			<?php 
		}

		public function afreg_update_profile_fields($customer_id) {

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);

			if(!empty($afreg_extra_fields)) {

				foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );

					if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) || isset( $_FILES['afreg_additional_'.intval($afreg_field->ID)] ) ) {

						if($afreg_field_type == 'fileupload') {

							if($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']!='') { 

								$file = time('m').$_FILES['afreg_additional_'.intval($afreg_field->ID)]['name'];
								$target_path = AFREG_PLUGIN_DIR.'uploaded_files/';
								$target_path = $target_path . $file;
								$temp = move_uploaded_file($_FILES['afreg_additional_'.intval($afreg_field->ID)]['tmp_name'], $target_path);
								update_user_meta($customer_id,'afreg_additional_'.intval($afreg_field->ID),$file);

							}

						} else if($afreg_field_type == 'multiselect') { 
		        			$prefix = '';
		        			$multival = '';
		        			foreach ($_POST['afreg_additional_'.intval($afreg_field->ID)] as $value) {
		        				$multival .= $prefix.$value;
	    						$prefix = ', ';
		        			}
		        			update_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID), esc_attr($multival) );

		        		} else {

							update_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID),  esc_attr($_POST['afreg_additional_'.intval($afreg_field->ID)]));
						}

					}
				}

			}
		}

	}

	new Addify_Registration_Fields_Addon_Admin();
}