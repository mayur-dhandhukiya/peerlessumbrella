<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WAR_Approve_Registration_Backend')){
	class WAR_Approve_Registration_Backend {
		public function __construct() {
			add_action('admin_menu', array(__CLASS__, 'war_add_menu_page'));
			add_action('admin_init', array(__CLASS__, 'war_register_settings'));
			add_filter('manage_users_columns', array($this, 'war_user_table'));
	      	add_filter('manage_users_custom_column', array($this, 'war_modify_user_table_row'), 10, 3 );
	      	add_filter('bulk_actions-users', array($this, 'war_user_bulk_actions'));
			add_filter('handle_bulk_actions-users', array($this, 'war_handle_user_bulk_actions'), 10, 3 );
		}
		public static function war_add_menu_page() {
			add_menu_page(esc_html__('Approve Customer', 'wc-war'), esc_html__('Approve Customer','wc-war'), 'manage_options', 'war-approve-customer', array(__CLASS__, 'war_approve_customer'),'dashicons-groups',57);
		}
		public static function war_approve_customer() {
			echo '<div class="wrap">';
			require_once WC_WAR_DIR . 'includes/class-war-settings.php';
			echo '</div>';
		}
		public static function war_register_settings() {
			register_setting('war-approve-customer-group', 'war_enable_module_functionality');
			register_setting('war-approve-customer-group', 'war_enable_user_authentication');
			register_setting('war-approve-customer-group', 'war_message_account_created');
			register_setting('war-approve-customer-group', 'war_message_account_in_pending');
			
			register_setting('war-approve-notification-group', 'war_message_account_pending_email');
			register_setting('war-approve-notification-group', 'war_message_account_pending_subject');
			register_setting('war-approve-notification-group', 'war_message_account_pending_body');
			register_setting('war-approve-notification-group', 'war_message_account_approve_email');
			register_setting('war-approve-notification-group', 'war_message_account_approve_subject');
			register_setting('war-approve-notification-group', 'war_message_account_approve_body');
			register_setting('war-approve-notification-group', 'war_message_account_reject_email');
			register_setting('war-approve-notification-group', 'war_message_account_reject_subject');
			register_setting('war-approve-notification-group', 'war_message_account_reject_body');
		}
		public function war_user_table( $column ) {
        	$column['war-login-status'] = esc_html__('Status', 'wc-war');
		    return $column;
        }
        public function war_modify_user_table_row($val, $column_name, $user_id){
        	switch ($column_name) {
		        case 'war-login-status' :
		            if ( 'disabled' == get_user_meta($user_id, 'war_user_authentication_status', true) ){
		            	return esc_html__('Disable', 'wc-war');
		            }else{
		            	return esc_html__('Active', 'wc-war');
		            }
		        break;
		    }
		    return $val;
        }
        public function war_user_bulk_actions($actions){
        	$actions['war-enable-login']=esc_html__('Enable Account', 'wc-war');
        	$actions['war-disable-login']=esc_html__('Disable Account', 'wc-war');
	        return $actions;
        }
		public function war_handle_user_bulk_actions($redirect_to, $doaction, $user_ids){
        	if ( $doaction == 'war-enable-login' ) {
		       if(!empty($user_ids)){
			       	$user=get_current_user_id();
			       	$user_ids=array_diff($user_ids, array($user));
			       	foreach ($user_ids as $key => $user_id) {
						   update_user_meta($user_id, 'war_user_authentication_status', 'active');
						   do_action('war_user_authentication_status_changed', $user_id,'active');
			       	}
		       }
		    } elseif ( $doaction == 'war-disable-login' ){
		    	if(!empty($user_ids)){
			       	$user=get_current_user_id();
			       	$user_ids=array_diff($user_ids, array($user));
			       	foreach ($user_ids as $key => $user_id) {
						   update_user_meta($user_id, 'war_user_authentication_status', 'disabled');
						   do_action('war_user_authentication_status_changed', $user_id,'disabled');
			       	}
		       }
		    }
		    return $redirect_to;
		}
	}
	new WAR_Approve_Registration_Backend();
}