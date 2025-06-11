<?php

if( ! session_id() ) {
    session_start();
}

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // restict for direct access
}

if ( !class_exists( 'Front_Class_Addify_Approve_New_User' ) ) {

	class Front_Class_Addify_Approve_New_User extends Addify_Approve_New_User {

		function __construct() {

			add_action( 'wp_loaded', array( $this, 'anu_front_scripts' ) );

			//For WooCommerce
			//add_action( 'woocommerce_created_customer', array($this, 'set_status_user_woocommerce' ));
			add_action('woocommerce_registration_redirect', array($this, 'anu_user_autologout'), 2);
			add_action('woocommerce_before_customer_login_form', array($this, 'anu_registration_message'), 2);
			add_action( 'woocommerce_register_form', array($this, 'anu_extra_registration_form_end' ));
			add_action( 'woocommerce_register_post', array($this, 'anu_validate_extra_register_fields'), 10, 3 );


			add_action( 'user_register', array( $this, 'set_status_user_woocommerce' ) );

			//For Wordpress
			//add_action( 'user_register', array( $this, 'set_wordpress_user_status' ) );
			add_action( 'register_post', array( $this, 'anu_add_new_user' ), 10, 3 );
			add_action( 'register_form', array($this, 'anu_registration_form' ));

			add_filter('wp_authenticate_user', array($this, 'anu_auth_login'));
			add_filter( 'registration_errors', array( $this, 'anu_show_user_message' ) );


			//Allow user to edit additional field.
			if(get_option('anu_additional_field_edit_user') == 'yes') {

				add_action( 'woocommerce_edit_account_form', array($this, 'anu_update_role_my_account' ));
				add_action( 'woocommerce_save_account_details_errors', array($this, 'anu_validate_update_role_my_account'), 10, 1 );
				add_action( 'woocommerce_save_account_details', array($this, 'anu_save_update_role_my_account'), 12, 1 );
			}

			if(get_option('woocommerce_registration_generate_password') == 'yes') {

                add_filter( 'woocommerce_new_customer_data', array($this, 'af_filter_password_auto' ));
            }
		}

        function af_filter_password_auto( $args ) {
            $new_password = wp_generate_password( 10, true, true );
            $args['user_pass'] = $new_password;

            $_SESSION['new_user_pass'] = $new_password;

            return $args;

        }


		public function anu_front_scripts() {	
        	
        	wp_enqueue_style( 'anu-front-css', plugins_url( '/assets/css/addify_anu_front_css.css', __FILE__ ), false );
        	
        }
		

		function set_status_user_woocommerce($customer_id) { 

			
			if(isset($_POST['wp_urr_user_role']) && $_POST['wp_urr_user_role']!='') {
            	$default_role = $_POST['wp_urr_user_role'];
            } else {

            	$default_role = get_option("default_role");
            }



			if(in_array($default_role, get_option('anu_user_roles'))) {

				update_user_meta( $customer_id, 'anu_new_user_status', 'pending');

                if(get_option('woocommerce_registration_generate_password') == 'yes') {

                    update_user_meta($customer_id, 'anu_new_user_pass', $_SESSION['new_user_pass']);

                } else {

                    if (isset($_POST['password'])) {
                        update_user_meta($customer_id, 'anu_new_user_pass', $_POST['password']);
                    } else if (isset($_POST['input_2'])) {
                        update_user_meta($customer_id, 'anu_new_user_pass', $_POST['input_2']);
                    }
                }

				//Send Message to user that their account is pending.  

				$user = new WP_User( $customer_id );
	            $user_login = stripslashes( $user->data->user_login );
				$user_email = stripslashes( $user->data->user_email );

				$to = $user->data->user_email;
				$subject = get_option('anu_approval_email_message_subject');

				$message = "
				<html>
					<head>
						
					</head>
					<body>
						<p>".get_option('anu_approval_email_message_text')."</p>
						
					</body>
				</html>
				";

				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				$admin_email = get_option( 'admin_email' );
				if ( empty( $admin_email ) ) {
					$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
				}

				$fromname = get_option( 'blogname' );

				// More headers
				$headers .= 'From: '.$fromname.'<'.$admin_email.'>' . "\r\n";

				wp_mail( $to, $subject, $message, $headers );




				//Send Message to admin that their account is pending.  


				$to1 = get_option('admin_email');
				$subject1 = 'A new user has registred and waiting for your approval';

				$default_admin_url = admin_url( 'users.php?s&anu-status-query-submit=Filter&addify_approve_new_user_filter=pending&paged=1' );
				$admin_url = apply_filters( 'addify_approve_new_user_admin_link', $default_admin_url );


				$message1 = "
				<html>
					<head>
						<title>HTML email</title>
					</head>
					<body>
						<p>Please login to backoffice and navigate to users section in order to approve this user.</p>
						<table>
							<tr>
								<td><b>Username is:</b></td>
								<td>".$user_login."</td>
							</tr>
							<tr>
								<td><b>Email is:</b></td>
								<td>".$user_email."</td>
							</tr>
							<tr>
								<td><b>".get_option('anu_additional_field_title')."</b></td>
								<td>".$_POST['anu_additional_info'] ."</td>
							</tr>
							
						</table>
						<p>".$admin_url."</p>
					</body>
				</html>
				";


				wp_mail( $to1, $subject1, $message1, $headers );




			} else {

				update_user_meta( $customer_id, 'anu_new_user_status', 'approved');
			}


			//Additional Info
       		if ( isset( $_POST['anu_additional_info'] ) && $_POST['anu_additional_info']!='' ) {
		             
		        update_user_meta( $customer_id, 'anu_additional_info', sanitize_text_field( $_POST['anu_additional_info'] ) );
		             
		    }
		}



		function anu_user_autologout(){

		       if ( is_user_logged_in() ) {
	                $current_user = wp_get_current_user();
	                $user_id = $current_user->ID;

	                if(isset($_POST['wp_urr_user_role']) && $_POST['wp_urr_user_role']!='') {
		            	$default_role = $_POST['wp_urr_user_role'];
		            } else {

		            	$default_role = get_option("default_role");
		            }

	                if(in_array($default_role, get_option('anu_user_roles'))) {
		                $approved_status = get_user_meta($user_id, 'anu_new_user_status', true);
		                //if the user hasn't been approved yet by WP Approve User plugin, destroy the cookie to kill the session and log them out
				        if ( $approved_status == 'approved' ){
				            return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));

				        } else if($approved_status == 'pending'){
		            		wp_logout();
		                    return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount'))) . "?approved=pending";
		                } else if($approved_status == 'denied') {

		                	wp_logout();
		                    return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount'))) . "?approved=denied";
		                } else {
		                	return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
		                }
	            	} else {
	            		return wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
	            	}


		        }
		}


		function anu_registration_message(){
		        

		        if( isset($_REQUEST['approved']) ){

		        	$approved = $_REQUEST['approved'];
		        	if($approved == 'pending') {

		        		echo "<p class='enu_warning'>".get_option("anu_message_for_approval")."</p>";
		        	} else if($approved == 'denied') {

		        		echo "<p class='enu_error'>".get_option("anu_message_for_denied")."</p>";
		        	}
		        } else {

		        	
		        }


		}


		function anu_auth_login ($user) {

			$status = get_user_meta($user->ID, 'anu_new_user_status', true);

		    if ( empty( $status ) ) {
				// the user does not have a status so let's assume the user is good to go
				return $user;
			}

			$message = false;
			switch ( $status ) {
				case 'pending':
					$pending_message = get_option("anu_message_for_pending");
					$message = new WP_Error( 'pending_approval', $pending_message );
					break;
				case 'denied':
					$denied_message = get_option("anu_message_for_denied");
					$message = new WP_Error( 'denied_access', $denied_message );
					break;
				case 'approved':
					$message = $user;
					break;
			}

			return $message;
		}


		function set_wordpress_user_status($user_id) {


			$status = 'pending';
			
			if(isset($_POST['wp_urr_user_role']) && $_POST['wp_urr_user_role']!='') {
            	$default_role = $_POST['wp_urr_user_role'];
            } else {

            	$default_role = get_option("default_role");
            }

			//If user is created for admin then no need of approval, it will be approved by default.
			if ( isset( $_REQUEST['action'] ) && 'createuser' == $_REQUEST['action'] ) {
				$status = 'approved';
			}

			if(in_array($default_role, get_option('anu_user_roles'))) {
				$status = apply_filters( 'approve_new_user_default_status', $status, $user_id );


				//Send Message to user that their account is pending.  

				$user = new WP_User( $user_id );
	            $user_login = stripslashes( $user->data->user_login );
				$user_email = stripslashes( $user->data->user_email );

				$to = $user->data->user_email;
				$subject = get_option('anu_approval_email_message_subject');

				$message = "
				<html>
					<head>
						
					</head>
					<body>
						<p>".get_option('anu_approval_email_message_text')."</p>
						
					</body>
				</html>
				";

				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				$admin_email = get_option( 'admin_email' );
				if ( empty( $admin_email ) ) {
					$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
				}

				$fromname = get_option( 'blogname' );

				// More headers
				$headers .= 'From: '.$fromname.'<'.$admin_email.'>' . "\r\n";

				wp_mail( $to, $subject, $message, $headers );




				//Send Message to admin that their account is pending.  


				$to1 = get_option('admin_email');
				$subject1 = 'A new user has registred and waiting for your approval';

				$default_admin_url = admin_url( 'users.php?s&anu-status-query-submit=Filter&addify_approve_new_user_filter=pending&paged=1' );
				$admin_url = apply_filters( 'addify_approve_new_user_admin_link', $default_admin_url );


				$message1 = "
				<html>
					<head>
						<title>HTML email</title>
					</head>
					<body>
						<p>Please login to backoffice and navigate to users section in order to approve this user.</p>
						<table>
							<tr>
								<td><b>Username is:</b></td>
								<td>".$user_login."</td>
							</tr>
							<tr>
								<td><b>Email is:</b></td>
								<td>".$user_email."</td>
							</tr>
							<tr>
								<td><b>".get_option('anu_additional_field_title')."</b></td>
								<td>".$_POST['anu_additional_info'] ."</td>
							</tr>
							
						</table>
						<p>".$admin_url."</p>
					</body>
				</html>
				";


				wp_mail( $to1, $subject1, $message1, $headers );

				update_user_meta( $user_id, 'anu_new_user_status', $status );
			} else {
				update_user_meta( $user_id, 'anu_new_user_status', 'approved');
			}

			if ( ! empty( $_POST['anu_additional_info'] ) ) {
				update_user_meta( $user_id, 'anu_additional_info', $_POST['anu_additional_info'] );
			}
		}



		function anu_show_user_message($errors) {

			if(isset($_POST['wp_urr_user_role']) && $_POST['wp_urr_user_role']!='') {
            	$default_role = $_POST['wp_urr_user_role'];
            } else {

            	$default_role = get_option("default_role");
            }

			if(in_array($default_role, get_option('anu_user_roles'))) {
				if ( !empty( $_POST['redirect_to'] ) ) {
					// if a redirect_to is set, honor it
					wp_safe_redirect( $_POST['redirect_to'] );
					exit();
				}

				// if there is an error already, let it do it's thing
				if ( $errors->get_error_code() ) {
					return $errors;
				}

				$message = get_option('anu_message_for_approval');
				
				$message = apply_filters( 'approve_new_user_pending_message', $message );

				$errors->add( 'registration_required', $message, 'message' );

				$success_message = __( 'Registration successful.', 'addify_anu' );
				$success_message = apply_filters( 'approve_new_user_registration_message', $success_message );

				login_header( __( 'Pending Approval', 'addify_anu' ), '<p class="message register">' . $success_message . '</p>', $errors );
				login_footer();
				
			} else {

				return $errors;
			}

			if(get_option('anu_enable_additional_field') == 'yes') {

				if(get_option('anu_additional_field_required') == 'yes') {


					if ( empty( $_POST['anu_additional_info'] ) ) {
						$errors->add( 'anu_additional_info', __( '<strong>ERROR</strong>: Please Enter '.get_option('anu_additional_field_title'), 'addify_anu' ) );
					}
				}
			}
			

			exit();
		}


		function anu_add_new_user( $user_login, $user_email, $errors ) {


			if(get_option('anu_enable_additional_field') == 'yes') {

				if(get_option('anu_additional_field_required') == 'yes') {


					if ( empty( $_POST['anu_additional_info'] ) ) {
						$errors->add( 'anu_additional_info', __( '<strong>ERROR</strong>: Please Enter '.get_option('anu_additional_field_title'), 'addify_anu' ) );
						return;
					}
				}
			}

			if(isset($_POST['wp_urr_user_role']) && $_POST['wp_urr_user_role']!='') {
            	$default_role = $_POST['wp_urr_user_role'];
            } else {

            	$default_role = get_option("default_role");
            }

			if(in_array($default_role, get_option('anu_user_roles'))) {
				if ( $errors->get_error_code() ) {
					return;
				}

				//create the user
				$user_pass = wp_generate_password( 12, false );
				$user_id = wp_create_user( $user_login, $user_pass, $user_email );
				if ( !$user_id ) {
					$errors->add( 'not_reg', sprintf( __( '<strong>ERROR</strong>: Unable to register, please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
				}
			}

			
		}


		function anu_extra_registration_form_end() { 

			if(get_option('anu_enable_additional_field') == 'yes') {

				$field_title = get_option('anu_additional_field_title');
				$is_required = get_option('anu_additional_field_required');
				$field_message = get_option('anu_additional_field_message');

		?>

			<p id="anu_additional_info" class="form-row full">
				
				<label for="anu_additional_info"><?php _e( $field_title, 'addify_anu' ); ?> 
					<?php if($is_required == 'yes') { ?> <span class="required">*</span> <?php } ?>
				</label>

				<textarea name="anu_additional_info" id="anu_additional_info" class="input-text" cols="5" rows="5" placeholder="<?php echo $field_message; ?>"><?php if ( ! empty( $_POST['anu_additional_info'] ) ) esc_attr_e( $_POST['anu_additional_info'] ); ?></textarea>

				<?php if(isset($field_message) && $field_message!='') { ?>
					<span style="width:100%;float: left"><?php echo $field_message; ?></span>
				<?php } ?>

			</p>

		<?php } }


		function anu_validate_extra_register_fields($username, $email, $validation_errors) {

			global $woocommerce;
			if(get_option('anu_enable_additional_field') == 'yes') {

				$is_required = get_option('anu_additional_field_required');
				$field_title = get_option('anu_additional_field_title');

				if($is_required == 'yes') {

					if( isset( $_POST['anu_additional_info'] ) && empty( $_POST['anu_additional_info'] )) {
						$validation_errors->add( $field_title.'_error', __( $field_title.' is required!', 'addify_anu' ) );
					}

				}


			}

			return $validation_errors;
		}



		function anu_registration_form() { ?>
			<p>
				<label for="anu_additional_info"><?php esc_html_e( get_option('anu_additional_field_title'), 'addify_anu' ); ?><br/>
					
					<textarea name="anu_additional_info" id="anu_additional_info" class="input-text" cols="29" rows="5" placeholder="<?php echo get_option('anu_additional_field_message'); ?>"><?php if ( ! empty( $_POST['anu_additional_info'] ) ) esc_attr_e( $_POST['anu_additional_info'] ); ?></textarea><br/>

					<?php if(get_option('anu_additional_field_message')!='') { ?>
						<span style="width:100%;float: left"><?php echo get_option('anu_additional_field_message'); ?></span>
					<?php } ?>

				</label>
			</p>
			<?php
		}


		function anu_update_role_my_account() {

		  	$user = wp_get_current_user();



		    
		    ?>

		    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		        <label for="favorite_color"><?php _e( get_option('anu_additional_field_title'), 'addify_anu' ); ?>
		        	<?php if(get_option('anu_additional_field_required') == 'yes') { ?><span class="required">*</span> <?php } ?>
		    	</label>

		    	<textarea name="anu_additional_info" id="anu_additional_info" class="input-text" cols="29" rows="5" placeholder="<?php echo get_option('anu_additional_field_message'); ?>"><?php echo get_user_meta($user->ID, 'anu_additional_info', true);  ?></textarea><br/>

				<?php if(get_option('anu_additional_field_message')!='') { ?>
					<span style="width:100%;float: left"><?php echo get_option('anu_additional_field_message'); ?></span>
				<?php } ?>
		        
		    </p>

		    <?php
		  }


		  function anu_validate_update_role_my_account($args) {

		  	if(get_option('anu_additional_field_required') == 'yes') {

			  	if ( isset( $_POST['anu_additional_info'] ) )  {
			        if($_POST['anu_additional_info']=='') 
			        $args->add( 'error', __( '<b>'.get_option('anu_additional_field_title').'</b> is a required field.', 'addify_anu' ),'');
			    }
			}

		  }



		  function anu_save_update_role_my_account($user_id) {

		  	if( isset( $_POST['anu_additional_info'] ) && $_POST['anu_additional_info'] !='' ) {

		  		update_user_meta($user_id, 'anu_additional_info', $_POST['anu_additional_info']);
		  		
		  	}
	        

		  }






	}

	new Front_Class_Addify_Approve_New_User();


}