<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // restict for direct access
}


if ( !class_exists( 'Admin_Anu_Users_List' ) ) {

	class Admin_Anu_Users_List extends Addify_Approve_New_User {

		function __construct() {

			add_filter( 'manage_users_columns', array( $this, 'anu_add_status_column' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'anu_status_column' ), 10, 3 );
			add_filter( 'user_row_actions', array( $this, 'anu_user_row_actions' ), 10, 2 );
			add_action( 'load-users.php', array( $this, 'anu_update_action' ) );
			add_action( 'restrict_manage_users', array( $this, 'anu_status_filter' ), 10, 1 );
			add_action( 'pre_user_query', array( $this, 'anu_filter_user_by_status' ) );
			add_action( 'admin_footer-users.php', array( $this, 'anu_admin_footer' ) );
			add_action( 'load-users.php', array( $this, 'anu_bulk_action' ) );
			add_action( 'admin_notices', array( $this, 'anu_admin_notices' ) );
			add_action( 'edit_user_profile', array( $this, 'anu_show_status_field_profile' ) );
			add_action( 'edit_user_profile_update', array( $this, 'anu_save_status_field_profile' ) );
		}



		public function anu_add_status_column( $columns ) {
			$the_columns['anu_new_user_status'] = __( 'Status', 'addify_anu' );

			$newcol = array_slice( $columns, 0, -1 );
			$newcol = array_merge( $newcol, $the_columns );
			$columns = array_merge( $newcol, array_slice( $columns, 1 ) );

			return $columns;
		}



		public function anu_status_column( $data, $column_name, $user_id ) {
			
			$status = get_user_meta($user_id, 'anu_new_user_status', true);
			if($status == 'approved') {
				$ustatus = __( 'Approved', 'addify_anu' );
			} else if($status == 'pending') {

				$ustatus = __( 'Pending', 'addify_anu' );
			} else if($status == 'denied') {

				$ustatus = __( 'Disapproved', 'addify_anu' );

			} else {
				$ustatus = __( '', 'addify_anu' );
			}

			return $ustatus;
					
		}

		public function anu_user_row_actions($actions, $user) {

			if ( $user->ID == get_current_user_id() ) {
				return $actions;
			}

			if ( is_super_admin( $user->ID ) ) {
				return $actions;
			}

			$user_status = $status = get_user_meta($user->ID, 'anu_new_user_status', true);

			$approve_link = add_query_arg( array( 'action' => 'approved', 'user' => $user->ID ) );
			$approve_link = remove_query_arg( array( 'new_role' ), $approve_link );
			$approve_link = wp_nonce_url( $approve_link, 'addify-approve-new-user' );

			$deny_link = add_query_arg( array( 'action' => 'denied', 'user' => $user->ID ) );
			$deny_link = remove_query_arg( array( 'new_role' ), $deny_link );
			$deny_link = wp_nonce_url( $deny_link, 'addify-approve-new-user' );

			$approve_action = '<a href="' . esc_url( $approve_link ) . '">' . __( 'Approve', 'addify_anu' ) . '</a>';
			$deny_action = '<a href="' . esc_url( $deny_link ) . '">' . __( 'Disapprove', 'addify_anu' ) . '</a>';

			if ( $user_status == 'pending' ) {
				$actions[] = $approve_action;
				$actions[] = $deny_action;
			} else if ( $user_status == 'approved' ) {
				$actions[] = $deny_action;
			} else if ( $user_status == 'denied' ) {
				$actions[] = $approve_action;
			}

			return $actions;

		}



		function anu_update_action() {
			if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'approved', 'denied' ) ) && !isset( $_GET['new_role'] ) ) {
				check_admin_referer( 'addify-approve-new-user' );

				$sendback = remove_query_arg( array( 'approved', 'denied', 'deleted', 'ids', 'anu-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback )
					$sendback = admin_url( 'users.php' );

				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$pagenum = $wp_list_table->get_pagenum();
				$sendback = add_query_arg( 'paged', $pagenum, $sendback );

				$status = sanitize_key( $_GET['action'] );
				$user = absint( $_GET['user'] );

				update_user_meta( $user, 'anu_new_user_status', $status);

				if ( $_GET['action'] == 'approved' ) {

					//Send Message to user that their account is approved.  

					$users = new WP_User( $user );
		            $user_login = stripslashes( $users->data->user_login );
					$user_email = stripslashes( $users->data->user_email );
					$user_pass  = stripslashes(get_user_meta($user, 'anu_new_user_pass', true) );

					$to = $users->data->user_email;
					$subject = get_option('anu_approved_email_message_subject');

					$message = "
					<html>
						<head>
							
						</head>
						<body>
						<p>".get_user_meta($user, 'first_name', true)." ".get_user_meta($user, 'last_name', true)."</p>
							<p>".get_option('anu_approved_email_message_text')."</p>

							<p><b>User Login:<b/> ".$user_login."</p>
							<p><b>Email:<b/> ".$user_email."</p>
							<p><b>Password:<b/> ".$user_pass."</p>

							
							
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
					$headers .= 'From: '.$fromname.' <'.$admin_email.'>' . "\r\n";

					wp_mail( $to, $subject, $message, $headers );


					$sendback = add_query_arg( array( 'approved' => 1, 'ids' => $user ), $sendback );
				} else {



					//Send Message to user that their account is denied.  

					$users = new WP_User( $user );
		            $user_login = stripslashes( $users->data->user_login );
					$user_email = stripslashes( $users->data->user_email );
					$user_pass  = stripslashes(get_user_meta($user, 'anu_new_user_pass', true) );

					$to = $users->data->user_email;
					$subject = get_option('anu_denied_email_message_subject');

					$message = "
					<html>
						<head>
							
						</head>
						<body>
							<p>".get_option('anu_denied_email_message_text')."</p>

							
							
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

					$sendback = add_query_arg( array( 'denied' => 1, 'ids' => $user ), $sendback );
				}

				wp_redirect( $sendback );
				exit;
			}
		}


		function anu_status_filter($s_filter) {


			$id = 'addify_approve_new_user_filter-' . $s_filter;

			$f_button = submit_button( __( 'Filter', 'addify_anu' ), 'button', 'anu-status-query-submit', false, array( 'id' => 'anu-status-query-submit' ) );
			$f_status = $this->changed_status();

			?>
			<label class="screen-reader-text" for="<?php echo $id ?>"><?php _e( 'View all users', 'addify_anu' ); ?></label>
			<select id="<?php echo $id ?>" name="<?php echo $id ?>" class="anusec">
				<option value=""><?php _e( 'View all users', 'addify_anu' ); ?></option>
			<?php foreach ( $this->get_all_statuses() as $status ) { ?>
				<option value="<?php echo esc_attr( $status ); ?>"<?php selected( $status, $f_status ); ?>>
					
					<?php

					if($status == 'denied') {
						echo "Disapproved";
					}  else {
						echo esc_html( ucfirst($status) );
					}
					

					 ?>
						
					</option>
			<?php } ?>
			</select>
			<?php echo apply_filters( 'addify_approve_new_user_filter_button', $f_button ); ?>
			
		<?php


		}


		function changed_status() {
			if ( ! empty( $_REQUEST['addify_approve_new_user_filter-top'] ) || ! empty( $_REQUEST['addify_approve_new_user_filter-bottom'] ) ) {
				return esc_attr( ( ! empty( $_REQUEST['addify_approve_new_user_filter-top'] ) ) ? $_REQUEST['addify_approve_new_user_filter-top'] : $_REQUEST['addify_approve_new_user_filter-bottom'] );
			}

			return null;
		}

		public function get_all_statuses() {
			return array( 'pending', 'approved', 'denied' );
		}

		function anu_filter_user_by_status($qry) {

			global $wpdb;

			if ( !is_admin() ) {
				return;
			}

			$screen = get_current_screen();
			if ( isset( $screen ) && 'users' != $screen->id ) {
				return;
			}

			if ( $this->changed_status() != null ) {
				$filter = $this->changed_status();

				$qry->query_from .= " INNER JOIN {$wpdb->usermeta} ON ( {$wpdb->users}.ID = $wpdb->usermeta.user_id )";

				if ( 'approved' == $filter ) {
					$qry->query_fields = "DISTINCT SQL_CALC_FOUND_ROWS {$wpdb->users}.ID";
					$qry->query_from .= " LEFT JOIN {$wpdb->usermeta} AS mt1 ON ({$wpdb->users}.ID = mt1.user_id AND mt1.meta_key = 'anu_new_user_status')";
					$qry->query_where .= " AND ( ( $wpdb->usermeta.meta_key = 'anu_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = 'approved' ) OR mt1.user_id IS NULL )";
				} else {
					$qry->query_where .= " AND ( ($wpdb->usermeta.meta_key = 'anu_new_user_status' AND CAST($wpdb->usermeta.meta_value AS CHAR) = '{$filter}') )";
				}



			}
		}


		public function anu_admin_footer() {
			$screen = get_current_screen();

			if ( $screen->id == 'users' ) { ?>
				<script type="text/javascript">
					jQuery(document).ready(function ($) {
						$('<option>').val('approved').text('<?php _e( 'Approve', 'addify_anu' )?>').appendTo("select[name='action']");
						$('<option>').val('approved').text('<?php _e( 'Approve', 'addify_anu' )?>').appendTo("select[name='action2']");

						$('<option>').val('denied').text('<?php _e( 'Disapprove', 'addify_anu' )?>').appendTo("select[name='action']");
						$('<option>').val('denied').text('<?php _e( 'Disapprove', 'addify_anu' )?>').appendTo("select[name='action2']");
					});
				</script>
			<?php }
		}



		public function anu_bulk_action() {
			$screen = get_current_screen();

			if ( $screen->id == 'users' ) {

				// get the action
				$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
				$action = $wp_list_table->current_action();


				$allowed_actions = array( 'approved', 'denied' );
				if ( !in_array( $action, $allowed_actions ) ) {
					return;
				}




				// security check
				check_admin_referer( 'bulk-users' );

				// make sure ids are submitted
				if ( isset( $_REQUEST['users'] ) ) {
					$user_ids = array_map( 'intval', $_REQUEST['users'] );
				}

				if ( empty( $user_ids ) ) {
					return;
				}

				$sendback = remove_query_arg( array( 'approved', 'denied', 'deleted', 'ids', 'addify_approve_new_user_filter', 'addify_approve_new_user_filter2', 'anu-status-query-submit', 'new_role' ), wp_get_referer() );
				if ( !$sendback ) {
					$sendback = admin_url( 'users.php' );
				}

				$pagenum = $wp_list_table->get_pagenum();
				$sendback = add_query_arg( 'paged', $pagenum, $sendback );

				switch ( $action ) {
					case 'approved':
						$approved = 0;
						foreach ( $user_ids as $user_id ) {


							//Send Message to user that their account is approved.  

							$users = new WP_User( $user_id );
				            $user_login = stripslashes( $users->data->user_login );
							$user_email = stripslashes( $users->data->user_email );
							$user_pass  = stripslashes(get_user_meta($user, 'anu_new_user_pass', true) );

							$to = $users->data->user_email;
							$subject = get_option('anu_approved_email_message_subject');

							$message = "
							<html>
								<head>
									
								</head>
								<body>
									<p>".get_option('anu_approved_email_message_text')."</p>

									<p><b>User Login:<b/> ".$user_login."</p>
							<p><b>Email:<b/> ".$user_email."</p>
							<p><b>Password:<b/> ".$user_pass."</p>
									
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
							$headers .= 'From: '.$fromname.' <'.$admin_email.'>' . "\r\n";

							wp_mail( $to, $subject, $message, $headers );
							
							update_user_meta( $user_id, 'anu_new_user_status', 'approved');
							$approved++;
						}

						$sendback = add_query_arg( array( 'approved' => $approved, 'ids' => join( ',', $user_ids ) ), $sendback );
						break;

					case 'denied':
						$denied = 0;
						foreach ( $user_ids as $user_id ) {


							//Send Message to user that their account is denied.  

							$users = new WP_User( $user_id );
				            $user_login = stripslashes( $users->data->user_login );
							$user_email = stripslashes( $users->data->user_email );
							$user_pass  = stripslashes(get_user_meta($user, 'anu_new_user_pass', true) );

							$to = $users->data->user_email;
							$subject = get_option('anu_denied_email_message_subject');

							$message = "
							<html>
								<head>
									
								</head>
								<body>
									<p>".get_option('anu_denied_email_message_text')."</p>

									
									
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
							
							update_user_meta( $user_id, 'anu_new_user_status', 'denied');
							$denied++;
						}

						$sendback = add_query_arg( array( 'denied' => $denied, 'ids' => join( ',', $user_ids ) ), $sendback );
						break;

					default:
						return;
				}

				$sendback = remove_query_arg( array( 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view' ), $sendback );

				wp_redirect( $sendback );
				exit();
			}
		}


		public function anu_admin_notices() {
			$screen = get_current_screen();

			if ( $screen->id != 'users' ) {
				return;
			}

			$message = null;

			if ( isset( $_REQUEST['denied'] ) && (int) $_REQUEST['denied'] ) {
				$denied = esc_attr( $_REQUEST['denied'] );
				$message = sprintf( _n( 'User disapproved.', '%s users denied.', $denied, 'addify_anu' ), number_format_i18n( $denied ) );
			}

			if ( isset( $_REQUEST['approved'] ) && (int) $_REQUEST['approved'] ) {
				$approved = esc_attr( $_REQUEST['approved'] );
				$message = sprintf( _n( 'User approved.', '%s users approved.', $approved, 'addify_anu' ), number_format_i18n( $approved ) );
			}

			if ( !empty( $message ) ) {
				echo '<div class="updated"><p>' . $message . '</p></div>';
			}
		}

		function anu_show_status_field_profile($user) {

			if ( $user->ID == get_current_user_id() ) {
				return;
			}

			$user_status = get_user_meta($user->ID, 'anu_new_user_status', true);
			?>
			<table class="form-table">
				<tr>
					<th><label for="anu_new_user_status"><?php _e( 'User Account Status', 'addify_anu' ); ?></label>
					</th>
					<td>
						<select id="anu_new_user_status" name="anu_new_user_status">
							<?php if ( $user_status == 'pending' ) : ?>
								<option value=""><?php _e( '--- Select Status ---', 'addify_anu' ); ?></option>
							<?php endif; ?>
							<?php foreach ( $this->get_all_statuses() as $status ) : ?>
								<option
									value="<?php echo esc_attr( $status ); ?>"<?php selected( $status, $user_status ); ?>>
									<?php 
									
									if($status == 'denied') {
										echo "Disapproved";
									}  else {
										echo esc_html( ucfirst($status) );
									} 

									?>
										
									</option>
							<?php endforeach; ?>
						</select>
						
					</td>
				</tr>

				<?php if(get_option('anu_enable_additional_field') == 'yes') { ?>

					<tr>
						<th><label for="anu_additional_info"><?php _e( get_option('anu_additional_field_title'), 'addify_anu' ); ?></label>
						</th>
						<td>
							
							<textarea name="anu_additional_info" id="anu_additional_info" class="input-text" cols="5" rows="7" placeholder="<?php echo $field_message; ?>">
								
								<?php echo get_user_meta($user->ID, 'anu_additional_info', true);   ?>
									
							</textarea>

						</td>
					</tr>

				<?php } ?>
			</table>
		<?php
		}


		function anu_save_status_field_profile($user_id) {

			if ( !current_user_can( 'edit_user', $user_id ) ) {
				return false;
			}

			if ( !empty( $_POST['anu_new_user_status'] ) ) {
				$status = esc_attr( $_POST['anu_new_user_status'] );

				if ( $status == 'approved' ) {
					$status = 'approved'; 
				} elseif ( $status == 'denied' ) {
					$status = 'denied';
				}

				
				update_user_meta( $user_id, 'anu_new_user_status', $status);
			}

		}



	}

	new Admin_Anu_Users_List();

}