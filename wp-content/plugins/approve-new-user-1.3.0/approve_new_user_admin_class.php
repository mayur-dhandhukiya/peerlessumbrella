<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // restict for direct access
}

if ( !class_exists( 'Admin_Class_Addify_Approve_New_User' ) ) {

	class Admin_Class_Addify_Approve_New_User extends Addify_Approve_New_User {

		function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'anu_admin_assets' ) );
			add_action( 'admin_menu', array( $this, 'anu_custom_menu_admin' ) );
			add_action('admin_init', array($this, 'anu_main_options'));

			add_action( 'wp_loaded', array( $this, 'anu_admin_loaded_functions' ) );
		}


		public function anu_custom_menu_admin() {	
			
			add_menu_page (
		        __('Approve New User', 'addify_anu'), // page title 
		        __('Approve New User', 'addify_anu'), // menu title
		        'manage_options', // capability
		        'addify-approve-new-user',  // menu-slug
		        array($this, 'anu_settings'),   // function that will render its output
		        plugins_url( 'assets/img/small_logo_white.png',  __FILE__  ),   // link to the icon that will be displayed in the sidebar
		        '10'    // position of the menu option
		    );

		    
	    }


	    public function anu_admin_assets() {

	    	wp_enqueue_style( 'addify_anu_admin_css', plugins_url( '/assets/css/addify_anu_admin_css.css', __FILE__ ), false );
	    	
	    }



	    public function approve_new_user_page() {

	    }

	    public function anu_settings() {

	    	if( isset( $_GET[ 'tab' ] ) ) {  
	            $active_tab = $_GET[ 'tab' ];  
	        } else {
	            $active_tab = 'tab_one';
	        }
	    ?>

	    <div class="wrap">
	    	<h2><?php _e('Approve New Users Settings', 'addify_anu'); ?></h2>
	    	<div class="description"><?php _e('Manage your approve new users settings from here!', 'addify_anu'); ?></div>

	    	<h2 class="nav-tab-wrapper">
	    		<a href="?page=addify-approve-new-user&tab=tab_one" class="nav-tab <?php echo $active_tab == 'tab_one' ? 'nav-tab-active' : ''; ?>"><?php _e('General Settings', 'addify_anu'); ?></a>
	    		<a href="?page=addify-approve-new-user&tab=tab_two" class="nav-tab <?php echo $active_tab == 'tab_two' ? 'nav-tab-active' : ''; ?>"><?php _e('Email Settings', 'addify_anu'); ?></a>
	    		<a href="?page=addify-approve-new-user&tab=tab_three" class="nav-tab <?php echo $active_tab == 'tab_three' ? 'nav-tab-active' : ''; ?>"><?php _e('Additional Field Settings', 'addify_anu'); ?></a> 
	    	</h2>

	    	<form method="post" action="options.php">
	    		<?php

	    			if( $active_tab == 'tab_one' ) {  

	                    settings_fields( 'anu-setting-1' );
	                    do_settings_sections( 'anu-setting-1' );

	                } else if( $active_tab == 'tab_two' ) {  

	                    settings_fields( 'anu-setting-2' );
	                    do_settings_sections( 'anu-setting-2' );

	                } else if($active_tab == 'tab_three') {

	                	settings_fields( 'anu-setting-3' );
	                    do_settings_sections( 'anu-setting-3' );

	                } else {
	                	settings_fields( 'anu-setting-1' );
	                    do_settings_sections( 'anu-setting-1' );

	                }

	    			submit_button();
	    		?>
	    	</form>

	    </div>


	    <?php 

	    }


	    function anu_main_options() {


	    	//Setting sections
			//General Settings
			add_settings_section(  
		        'anu_section_1',         // ID used to identify this section and with which to register options  
		        __('General Settings', 'addify_anu'),    // Title to be displayed on the administration page  
		        array($this, 'anu_section_1_callback'), // Callback used to render the description of the section  
		        'anu-setting-1'   // Page on which to add this section of options                 

		    );


			add_settings_section(  
		        'anu_section_1',         // ID used to identify this section and with which to register options  
		        __('Email Settings', 'addify_anu'),    // Title to be displayed on the administration page  
		        array($this, 'anu_section_2_callback'), // Callback used to render the description of the section  
		        'anu-setting-2'   // Page on which to add this section of options                 

		    );


		    add_settings_section(  
		        'anu_section_1',         // ID used to identify this section and with which to register options  
		        __('Additional Field Settings', 'addify_anu'),    // Title to be displayed on the administration page  
		        array($this, 'anu_section_3_callback'), // Callback used to render the description of the section  
		        'anu-setting-3'   // Page on which to add this section of options                 

		    );



		    add_settings_field (   
		        'anu_user_roles',                   
		        __('Select user roles which you want to approve manually', 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_user_roles_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-1',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Select All User Roles to Apply restrict for every new account. In order to apply manual approval on selective user roles, you would need to show user roles on registration page. To show user roles on registration page you can use this module  <a href='https://codecanyon.net/item/woocommerce-wordpress-choose-user-roles-at-registration/21967283' target='_self'>Wordpress Choose User Roles at Registration</a>.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-1',  
		        'anu_user_roles'  
		    );



		    add_settings_field (   
		        'anu_message_for_approval',                   
		        __('Message for Users when Account is Created', 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_message_for_approval_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-1',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("First message that will be displayed to user when he completes the registration process, this message will be displayed only when manual approval is required.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-1',  
		        'anu_message_for_approval'  
		    );



		    add_settings_field (   
		        'anu_message_for_pending',                   
		        __("Message for Users when Account is pending for approval", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_message_for_pending_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-1',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This will be displayed when user will attempt to login after registration and his account is still pending for admin approval.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-1',  
		        'anu_message_for_pending'  
		    );


		    add_settings_field (   
		        'anu_message_for_denied',                   
		        __("Message for Users when Account is disapproved", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_message_for_denied_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-1',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Message for Users when Account is Disapproved By Admin.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-1',  
		        'anu_message_for_denied'  
		    );



		    add_settings_field (   
		        'anu_approval_email_message_subject',                   
		        __("Approval Email Subject", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_approval_email_message_subject_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-2',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This is the email subject; this subject is used when email is sent to user on account creation to inform that account is created but needs to be approved by administrator.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-2',  
		        'anu_approval_email_message_subject'  
		    );


		    add_settings_field (   
		        'anu_approval_email_message_text',                   
		        __("Approval Email Message", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_approval_email_message_text_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-2',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This is the email text, this text will be used when email sent to user on account creation to inform that acccount is created but need to approved by administrator.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-2',  
		        'anu_approval_email_message_text'  
		    );



		    add_settings_field (   
		        'anu_denied_email_message_subject',                   
		        __("Disapproved Email Subject", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_denied_email_message_subject_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-2',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This is the disapproved email subject, this subject is used when account is disapproved by administrator.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-2',  
		        'anu_denied_email_message_subject'  
		    );


		    add_settings_field (   
		        'anu_denied_email_message_text',                   
		        __("Disapproved Email Message", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_denied_email_message_text_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-2',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This is the disapproved email message, this message is used when account is disapproved by administrator.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-2',  
		        'anu_denied_email_message_text'  
		    );




		    add_settings_field (   
		        'anu_approved_email_message_subject',                   
		        __("Approved Email Subject", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_approved_email_message_subject_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-2',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This is the approved email subject, this subject is when used when account is approved by administrator.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-2',  
		        'anu_approved_email_message_subject'  
		    );


		    add_settings_field (   
		        'anu_approved_email_message_text',                   
		        __("Approved Email Message", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_approved_email_message_text_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-2',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("This is the approved email message, this message is used when account is approved by administrator.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-2',  
		        'anu_approved_email_message_text'  
		    );



		    add_settings_field (   
		        'anu_enable_additional_field',                   
		        __("Enable Additional Textarea Field", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_enable_additional_field_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-3',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Enable additional textarea field on the registration page for getting some additional info from customer during registration.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-3',  
		        'anu_enable_additional_field'  
		    );



		    add_settings_field (   
		        'anu_additional_field_title',                   
		        __("Additional Textarea Field Title", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_additional_field_title_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-3',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Title of the additional textarea field.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-3',  
		        'anu_additional_field_title'  
		    );


		    add_settings_field (   
		        'anu_additional_field_required',                   
		        __("Is Required ?", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_additional_field_required_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-3',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Additional tetarea field is required or optional.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-3',  
		        'anu_additional_field_required'  
		    );


		    add_settings_field (   
		        'anu_additional_field_message',                   
		        __("Field Message", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_additional_field_message_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-3',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Message for the additional textarea field.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-3',  
		        'anu_additional_field_message'  
		    );


		    add_settings_field (   
		        'anu_additional_field_edit_user',                   
		        __("Allow User to Edit in My Account?", 'addify_anu'),                           // The label to the left of the option interface element  
		        array($this, 'anu_additional_field_edit_user_callback'),   // The name of the function responsible for rendering the option interface  
		        'anu-setting-3',                          // The page on which this option will be displayed  
		        'anu_section_1',         // The name of the section to which this field belongs  
		        array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		            __("Allow user to edit this field in my account on front end.", 'addify_anu'),
		        )  
		    );  
		    register_setting(  
		        'anu-setting-3',  
		        'anu_additional_field_edit_user'  
		    );







	    }


	    //General Settings Callbacks
		function anu_section_1_callback() {  
		    //echo '<p>'._e('Enter general settings here!', 'addify_anu').'</p>';  
		} 


		function anu_user_roles_callback($args) { ?>

			<?php 
			$pre_vals = get_option('anu_user_roles');
			global $anu_roles;
		   	if ( !isset( $anu_roles ) ) $anu_roles = new WP_Roles();
		  	

		  	?>

		  	<div class="tdborder">
		  	<?php foreach($anu_roles->roles as $role_key=>$role): ?>
              
                <div class="rolesinner">
                <label for="anu-user-roles-<?php echo $role_key; ?>" class="chooseable">
                <input type="checkbox" value="<?php echo $role_key; ?>" name="anu_user_roles[]" id="anu-user-roles-<?php echo $role_key; ?>" <?php if(!empty($pre_vals) &&in_array($role_key, $pre_vals)) { echo "checked"; } ?> /> <?php echo $role['name']; ?>
               
                </label>
            	</div>
              
            <?php endforeach; ?>
            </div>


			<p class="description anu_user_roles"> <?php echo $args[0] ?> </p>
		<?php }



		function anu_message_for_approval_callback($args) { ?>

			<textarea name="anu_message_for_approval" id="anu_message_for_approval" rows="10" cols="70"><?php echo get_option('anu_message_for_approval'); ?></textarea>
			<p class="description anu_message_for_approval"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_message_for_pending_callback($args) { ?>

			<textarea name="anu_message_for_pending" id="anu_message_for_pending" rows="10" cols="70"><?php echo get_option('anu_message_for_pending'); ?></textarea>
			<p class="description anu_message_for_pending"> <?php echo $args[0] ?> </p>
		<?php }

		function anu_message_for_denied_callback($args) { ?>

			<textarea name="anu_message_for_denied" id="anu_message_for_denied" rows="10" cols="70"><?php echo get_option('anu_message_for_denied'); ?></textarea>
			<p class="description anu_message_for_denied"> <?php echo $args[0] ?> </p>
		<?php }




		//Email settings call backs

		function anu_section_2_callback() {  
		    //echo '<p>'._e('Enter email settings here!', 'addify_anu').'</p>';  
		} 



		function anu_approval_email_message_subject_callback($args) { ?>

			<input type="text" id="anu_approval_email_message_subject" class="text_title" name="anu_approval_email_message_subject" value="<?php echo get_option('anu_approval_email_message_subject') ?>">
			<p class="description anu_approval_email_message_subject"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_approval_email_message_text_callback($args) { 

			$content = get_option('anu_approval_email_message_text');
			$editor_id = 'anu_approval_email_message_text';
			$settings = array(
			    'tinymce' => true,
			    'textarea_rows' => 10,
			    'quicktags' => array('buttons' => 'em,strong,link',),
			    'quicktags' => true,
			    'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

		?>

			
			<p class="description anu_approval_email_message_text"> <?php echo $args[0] ?> </p>
		<?php }



		function anu_denied_email_message_subject_callback($args) { ?>

			<input type="text" id="anu_denied_email_message_subject" class="text_title" name="anu_denied_email_message_subject" value="<?php echo get_option('anu_denied_email_message_subject') ?>">
			<p class="description anu_denied_email_message_subject"> <?php echo $args[0] ?> </p>
		<?php }



		function anu_denied_email_message_text_callback($args) { 

			$content = get_option('anu_denied_email_message_text');
			$editor_id = 'anu_denied_email_message_text';
			$settings = array(
			    'tinymce' => true,
			    'textarea_rows' => 10,
			    'quicktags' => array('buttons' => 'em,strong,link',),
			    'quicktags' => true,
			    'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

		?>
		<p class="description anu_approval_email_message_text"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_approved_email_message_subject_callback($args) { ?>

			<input type="text" id="anu_approved_email_message_subject" class="text_title" name="anu_approved_email_message_subject" value="<?php echo get_option('anu_approved_email_message_subject') ?>">
			<p class="description anu_approved_email_message_subject"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_approved_email_message_text_callback($args) { 

			$content = get_option('anu_approved_email_message_text');
			$editor_id = 'anu_approved_email_message_text';
			$settings = array(
			    'tinymce' => true,
			    'textarea_rows' => 10,
			    'quicktags' => array('buttons' => 'em,strong,link',),
			    'quicktags' => true,
			    'tinymce' => true,
			);

			wp_editor( $content, $editor_id, $settings );

		?>

			
			<p class="description anu_approved_email_message_text"> <?php echo $args[0] ?> </p>
		<?php }


		//Additional Field settings call backs

		function anu_section_3_callback() {  
		    echo '<p>'._e('You can add additional textarea field to get some additional information from customer during registration.', 'addify_anu').'</p>';  
		} 


		function anu_enable_additional_field_callback($args) { ?>

			<input type="checkbox" name="anu_enable_additional_field" id="anu_enable_additional_field" class="" value="yes" <?php echo checked('yes', get_option('anu_enable_additional_field')); ?> />
			<p class="description anu_enable_additional_field"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_additional_field_title_callback($args) { ?>

			<input type="text" id="anu_additional_field_title" class="text_title" name="anu_additional_field_title" value="<?php echo get_option('anu_additional_field_title') ?>">
			<p class="description anu_additional_field_title"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_additional_field_required_callback($args) { ?>

			<input type="checkbox" name="anu_additional_field_required" id="anu_additional_field_required" class="" value="yes" <?php echo checked('yes', get_option('anu_additional_field_required')); ?> />
			<p class="description anu_additional_field_required"> <?php echo $args[0] ?> </p>
		<?php }


		function anu_additional_field_message_callback($args) { ?>

			<input type="text" id="anu_additional_field_message" class="text_title" name="anu_additional_field_message" value="<?php echo get_option('anu_additional_field_message') ?>">
			<p class="description anu_additional_field_message"> <?php echo $args[0] ?> </p>
		<?php }



		function anu_additional_field_edit_user_callback($args) { ?>

			<input type="checkbox" name="anu_additional_field_edit_user" id="anu_additional_field_edit_user" class="" value="yes" <?php echo checked('yes', get_option('anu_additional_field_edit_user')); ?> />
			<p class="description anu_additional_field_edit_user"> <?php echo $args[0] ?> </p>
		<?php }






		function anu_admin_loaded_functions() {

			require_once( addify_anu_plugindir.'anu_users_lists.php' );
		}





















	}

	new Admin_Class_Addify_Approve_New_User();

}