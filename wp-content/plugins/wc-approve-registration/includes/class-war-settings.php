<?php
if (!defined('ABSPATH')) {
    exit; 
} 
if(!class_exists('WAR_Approve_Registration_Settings')){
	class WAR_Approve_Registration_Settings{
		public function __construct(){
			$current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'war-general-settings';
	        $tabs = array(
	        'war-general-settings' => esc_html__('General Settings', 'wc-war' ),
			'war-email-settings' => esc_html__('Notification Settings', 'wc-war' ),
	        );
	        self::init_tabs( apply_filters('wpwc_user_registration_setting_tabs',$tabs) );
	        self::current_tab( apply_filters('wpwc_user_registration_current_setting_tab',$current) );
		}
		public static function init_tabs($tabs=array()){
	        $current = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'war-general-settings';
	        echo '<h5 class="nav-tab-wrapper">';
	        foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
	        echo '<a class="nav-tab ' . esc_attr($class) . '" href="'.esc_url(admin_url().'admin.php?page=war-approve-customer&tab='.$tab).'">'.esc_html($name).'</a>';
	        }
	        echo '</h5>';
	    }
		public static function current_tab( $current='war-general-settings' ) {
	        switch ($current) {
	        case 'war-general-settings':
	            self::war_general_settings();
	            break;
	        case 'war-email-settings':
	            self::war_email_settings();
				break; 
	        default:
	            self::war_general_settings();
	            break;
	        }
	    }
		public static function war_general_settings(){
			?>
			<div class="wrap">
				<form method="post" action="options.php">
					<?php settings_errors(); ?>
					<?php settings_fields('war-approve-customer-group'); ?>
					<?php do_settings_sections('war-approve-customer-group'); ?>
					 <table class="form-table">
						<tr valign="top">
							<th><label for="war_enable_module_functionality">
								<?php esc_html_e('Enable Module', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_enable_module_functionality'); ?>
								<input type="checkbox" name="war_enable_module_functionality" value="yes" id="war_enable_module_functionality" <?php checked('yes', $value); ?>>
								<span><?php esc_html_e('Enable module to work on frontend.', 'wc-war'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="war_enable_user_authentication">
								<?php esc_html_e('Enable Manual Approve', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_enable_user_authentication'); ?>
								<input type="checkbox" name="war_enable_user_authentication" value="yes" id="war_enable_user_authentication" <?php checked('yes', $value); ?>>
								<span><?php esc_html_e('Enable this option to manually approve new customer registration.', 'wc-war'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_created">
								<?php esc_html_e('Message For Account Created', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_created'); ?>
								<input type="text" name="war_message_account_created" class="regular-text" id="war_message_account_created" value="<?php esc_attr_e($value); ?>">
								<p><i><?php esc_html_e('Message for customers when account is created but pending for approval.', 'wc-war'); ?></i></p>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_in_pending">
								<?php esc_html_e('Account Disabled Login Message', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_in_pending'); ?>
								<input type="text" name="war_message_account_in_pending" class="regular-text" id="war_message_account_in_pending" value="<?php esc_attr_e($value); ?>">
								<p><i><?php esc_html_e('Message for customers when account is disabled for login.', 'wc-war'); ?></i></p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
			<?php
		}
		public static function war_email_settings() {
			?>
			<div class="wrap">
				<form method="post" action="options.php">
					<?php settings_errors(); ?>
					<?php settings_fields('war-approve-notification-group'); ?>
					<?php do_settings_sections('war-approve-notification-group'); ?>
					 <table class="form-table">
						<tr valign="top">
							<th><label for="war_message_account_pending_email">
								<?php esc_html_e('Enable Account Pending Notification', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_pending_email'); ?>
								<input type="checkbox" name="war_message_account_pending_email" value="yes" id="war_message_account_pending_email" <?php checked('yes', $value); ?>>
								<span><?php esc_html_e('Enable this option to enable the account pending notification.', 'wc-war'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_pending_subject">
								<?php esc_html_e('Account Pending Notification Subject', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_pending_subject'); ?>
								<input type="text" name="war_message_account_pending_subject" class="regular-text" id="war_message_account_pending_subject" value="<?php esc_attr_e($value); ?>">
								<p><i><?php esc_html_e('Email notification subject for account in pending.', 'wc-war'); ?></i></p>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_pending_body">
								<?php esc_html_e('Account Pending Notification Body', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_pending_body'); ?>
								<?php wp_editor( $value, 'war_message_account_pending_body', array('textarea_rows' => 3 ) ) ?>
								<p><?php esc_html_e('User role grant email notification body.', 'wc-war'); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_approve_email">
								<?php esc_html_e('Enable Account Approve Notification', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_approve_email'); ?>
								<input type="checkbox" name="war_message_account_approve_email" value="yes" id="war_message_account_approve_email" <?php checked('yes', $value); ?>>
								<span><?php esc_html_e('Enable this option to enable the account approve notification.', 'wc-war'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_approve_subject">
								<?php esc_html_e('Account Approval Notification Subject', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_approve_subject'); ?>
								<input type="text" name="war_message_account_approve_subject" class="regular-text" id="war_message_account_approve_subject" value="<?php esc_attr_e($value); ?>">
								<p><i><?php esc_html_e('Email notification subject for account approval.', 'wc-war'); ?></i></p>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_approve_body">
								<?php esc_html_e('Account Approve Notification Body', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_approve_body'); ?>
								<?php wp_editor( $value, 'war_message_account_approve_body', array('textarea_rows' => 3 ) ) ?>
								<p><?php esc_html_e('Account approval email notification body.', 'wc-war'); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_reject_email">
								<?php esc_html_e('Enable Account Disapprove Notification', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_reject_email'); ?>
								<input type="checkbox" name="war_message_account_reject_email" value="yes" id="war_message_account_reject_email" <?php checked('yes', $value); ?>>
								<span><?php esc_html_e('Enable this option to enable the account disapprove notification.', 'wc-war'); ?></span>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_reject_subject">
								<?php esc_html_e('Account Disapprove Notification Subject', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_reject_subject'); ?>
								<input type="text" name="war_message_account_reject_subject" class="regular-text" id="war_message_account_reject_subject" value="<?php esc_attr_e($value); ?>">
								<p><i><?php esc_html_e('Email notification subject for account disapprove.', 'wc-war'); ?></i></p>
							</td>
						</tr>
						<tr>
							<th><label for="war_message_account_reject_body">
								<?php esc_html_e('Account Disapprove Notification Body', 'wc-war'); ?></label>
							</th>
							<td><?php $value=get_option('war_message_account_reject_body'); ?>
								<?php wp_editor( $value, 'war_message_account_reject_body', array('textarea_rows' => 3 ) ) ?>
								<p><?php esc_html_e('Account disapprove email notification body.', 'wc-war'); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
			<?php
		}
	}
	new WAR_Approve_Registration_Settings();
}