<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if(!class_exists('WAR_Approve_Registration_Frontend')){
	class WAR_Approve_Registration_Frontend {
		public function __construct() {
			if ( 'yes'== get_option('war_enable_user_authentication') ){
				add_action('user_register', array($this, 'war_user_register'));
				add_filter('authenticate', array($this,'war_authenticate_user'), 100,2);
				add_filter('woocommerce_registration_auth_new_customer', array($this, 'war_check_status_login'),10,2 );
				add_action('init', array($this, 'war_before_error'));
			}
		}
		public function war_user_register($user_id){
			update_user_meta( $user_id, 'war_user_authentication_status', 'disabled');
			do_action('wpwc_user_auth_status_changed', $user_id, 'disabled');
		}
		public function war_authenticate_user($user,$username){
			if(is_object($user) && !is_wp_error($user)){
				$user_data = $user->data;
			    $user_id = $user_data->ID;
			    $user_status = get_user_meta($user_id, 'war_user_authentication_status', true);
			    if ( 'disabled' == $user_status ) {
					$value=get_option('war_message_account_in_pending');
					$value=!empty($value) ? $value : esc_html__('Your account is disablled.', 'wc-war');
			        return new WP_Error( 'disabled_account', $value);
			    }else{
			       return $user;
			    }
			}
        	return $user;
		}
		public function war_before_error(){
			if(isset($_COOKIE['war_authenticate_user'])){
				setcookie('war_authenticate_user', null, time());
				add_action('woocommerce_before_customer_login_form', array($this,' war_custom_error'));
			}
		}
		public function war_check_status_login($status, $user_id){
		    $user_sts = get_user_meta($user_id, 'war_user_authentication_status', true);
		    if ( 'disabled' == $user_sts ) {
		    	setcookie('war_authenticate_user', $user_id, time()+10);
				$status = false;
			}
			return $status;
		}
		public function war_custom_error(){
			$error=get_option('war_message_account_created');
			$error=!empty($error) ? $error : esc_html__('You are registered but your account is disabled.', 'wc-war');
			?>
			<div class="woocommerce-notices-wrapper">
				<div class="woocommerce-error" role="alert">
					<?php echo '<strong>' . esc_html__('Error:', 'wc-war') . '</strong> ' . esc_html($error);  ?>	
				</div>
			</div>
			<?php
		}
	}
	new WAR_Approve_Registration_Frontend();
}