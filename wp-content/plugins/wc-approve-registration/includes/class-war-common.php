<?php
if (!defined('ABSPATH')) {
    exit; 
} 
if(!class_exists('WAR_Approve_Registration_Common')){
	class WAR_Approve_Registration_Common{
		public function __construct(){
			add_action('war_user_authentication_status_changed', array($this,'war_account_status_changed'), 10,2);
		}
		public function war_account_status_changed( $user_id, $status ) {
			if( get_option('war_message_account_approve_email') =='yes' && $status=='active' ) {
				$subject=get_option('war_message_account_approve_subject');
				$subject=!empty($subject) ? $subject : esc_html__('Account Approved', 'wc-war');
				$body=get_option('war_message_account_approve_body');
				$user=get_user_by('ID', $user_id);
				if(!is_wp_error($user)){
					$sendor=get_option('blogname').' <'.get_option('admin_email') . '>';
					$headers  = "From: " . $sendor . PHP_EOL;
					$headers .= "MIME-Version: 1.0" . PHP_EOL; 
					$headers .= "Content-Type: text/html; boundary=\"" . md5(time()) . "\"";
					wp_mail( $user->user_email, $subject, $body, $headers, '' );
				}
			}
			if( get_option('war_message_account_reject_email') =='yes' && $status=='disabled' ) {
				$subject=get_option('war_message_account_reject_subject');
				$subject=!empty($subject) ? $subject : esc_html__('Account Disapproved', 'wc-war');
				$body=get_option('war_message_account_reject_body');
				$user=get_user_by('ID', $user_id);
				if(!is_wp_error($user)){
					$sendor=get_option('blogname').' <'.get_option('admin_email') . '>';
					$headers  = "From: " . $sendor . PHP_EOL;
					$headers .= "MIME-Version: 1.0" . PHP_EOL; 
					$headers .= "Content-Type: text/html; boundary=\"" . md5(time()) . "\"";
					wp_mail( $user->user_email, $subject, $body, $headers, '' );
				}
			}
			if( get_option('war_message_account_pending_email') =='yes' && $status=='pending' ) {
				$subject=get_option('war_message_account_pending_subject');
				$subject=!empty($subject) ? $subject : esc_html__('Account Disapproved', 'wc-war');
				$body=get_option('war_message_account_pending_body');
				$user=get_user_by('ID', $user_id);
				if(!is_wp_error($user)){
					$sendor=get_option('blogname').' <'.get_option('admin_email') . '>';
					$headers  = "From: " . $sendor . PHP_EOL;
					$headers .= "MIME-Version: 1.0" . PHP_EOL; 
					$headers .= "Content-Type: text/html; boundary=\"" . md5(time()) . "\"";
					wp_mail( $user->user_email, $subject, $body, $headers, '' );
				}
			}
		}
	}
	new WAR_Approve_Registration_Common();
}