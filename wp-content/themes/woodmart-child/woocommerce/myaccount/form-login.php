<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$tabs = woodmart_get_opt( 'login_tabs' );
$reg_text = woodmart_get_opt( 'reg_text' );
$login_text = woodmart_get_opt( 'login_text' );
$account_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

$class = 'woodmart-registration-page';

if ( $tabs && get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
	$class .= ' woodmart-register-tabs';
}

if ( get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) {
	$class .= ' woodmart-no-registration';
}

if ( $login_text && $reg_text ) {
	$class .= ' with-login-reg-info';
}

if ( isset( $_GET['action'] ) && 'register' === $_GET['action'] && $tabs ) {
	$class .= ' active-register';
}

//WC 3.5.0
if ( function_exists( 'WC' ) && version_compare( WC()->version, '3.5.0', '<' ) ) {
	wc_print_notices();
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="<?php echo esc_attr( $class ); ?>">

	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

		<div class="row" id="customer_login">

			<div class="col-12 col-md-6 col-login">

			<?php endif; ?>

			<h2 class="wd-login-title"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

			<?php woodmart_login_form( true, add_query_arg( 'action', 'login', $account_link ) ); ?>

			<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

			</div>

			<div class="col-12 col-md-6 col-register">

				<h2 class="wd-login-title"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

				<form method="post" action="<?php echo esc_url( add_query_arg( 'action', 'register', $account_link ) ); ?>" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

					<?php do_action( 'woocommerce_register_form_start' ); ?>




					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
							<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>" />
						</p>

					<?php endif; ?>
					<?php

					global $woocommerce;
					$countries_obj   = new WC_Countries();
					$countries = array(
						'CA'=>'Canada',
						'US'=>'United States (US)',
					);

					$default_country = 'CA';

					$default_county_states = $countries_obj->get_states( $default_country );


					?>
					<p class="form-row form-row-wide">
						<label for="reg_billing_name"><?php _e( 'Name', 'woocommerce' ); ?><span class="required">*</span></label>
						<input type="text" class="input-text" name="billing_name" id="reg_billing_name" value="<?php if ( ! empty( $_POST['billing_name'] ) ) esc_attr_e( $_POST['billing_name'] ); ?>" />
					</p>
					<p class="form-row form-row-wide">
						<label for="reg_billing_company"><?php _e( 'Company', 'woocommerce' ); ?><span class="required">*</span></label>
						<input type="text" class="input-text" name="billing_company" id="reg_billing_company" value="<?php if ( ! empty( $_POST['billing_company'] ) ) esc_attr_e( $_POST['billing_company'] ); ?>" />
					</p>
					<p class="form-row form-row-wide">
						<label for="reg_billing_address"><?php _e( 'Address', 'woocommerce' ); ?><span class="required">*</span></label>
						<input type="text" class="input-text" name="billing_address" id="reg_billing_address" value="<?php if ( ! empty( $_POST['billing_address'] ) ) esc_attr_e( $_POST['billing_address'] ); ?>" />
					</p>
					<p class="form-row form-row-wide">
						<label for="reg_billing_city"><?php _e( 'City', 'woocommerce' ); ?><span class="required">*</span></label>
						<input type="text" class="input-text" name="billing_city" id="reg_billing_city" value="<?php if ( ! empty( $_POST['billing_city'] ) ) esc_attr_e( $_POST['billing_city'] ); ?>" />
					</p>
					



					<?php

					wp_enqueue_script( 'wc-country-select' );
					woocommerce_form_field('billing_country', array(
						'type'       => 'country',
						'class'      => array( 'chzn-drop' ),
						'label'      => __('Country'),
						'required'    => true,
						'placeholder'    => __('Enter country'),
						'options'    => $countries
					)
				);

					woocommerce_form_field('billing_state', array(
						'type'       => 'state',
						'class'      => array( 'chzn-drop' ),
						'label'      => __('State'),
						'required'    => true,
						'placeholder'    => __('Enter state'),
						'options'    => $default_county_states
					)
				);
				?>
				<p class="form-row form-row-wide">
					<label for="reg_billing_postcode"><?php _e( 'Zip', 'woocommerce' ); ?><span class="required">*</span></label>
					<input type="text" class="input-text" name="billing_postcode" id="reg_billing_postcode" value="<?php if ( ! empty( $_POST['billing_postcode'] ) ) esc_attr_e( $_POST['billing_postcode'] ); ?>" />
				</p>


				<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
					<label for="reg_email"><?php esc_html_e( 'Email', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( $_POST['email'] ) : ''; ?>" />
				</p>



				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
						<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
					</p>

					<?php else : ?>

						<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

					<?php endif; ?>

					<p class="form-row form-row-wide">
						<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
						<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
					</p>
					<p class="form-row form-row-wide">
						<label for="reg_asi"><?php _e( 'ASI#', 'woocommerce' ); ?></label>
						<input type="text" class="input-text" name="reg_asi" id="reg_asi" value="<?php esc_attr_e( $_POST['reg_asi'] ); ?>" />
					</p>
					<p class="form-row form-row-wide">
						<label for="reg_ppai_upic"><?php _e( 'PPAI / UPIC', 'woocommerce' ); ?></label>
						<input type="text" class="input-text" name="reg_ppai_upic" id="reg_ppai_upic" value="<?php esc_attr_e( $_POST['reg_ppai_upic'] ); ?>" />
					</p>	
					<p class="form-row form-row-wide">
						<label for="reg_sage"><?php _e( 'SAGE', 'woocommerce' ); ?></label>
						<input type="text" class="input-text" name="reg_sage" id="reg_sage" value="<?php esc_attr_e( $_POST['reg_sage'] ); ?>" />
					</p>	
					<p class="form-row form-row-wide">
						<label for="reg_dc"><?php _e( 'DC', 'woocommerce' ); ?></label>
						<input type="text" class="input-text" name="reg_dc" id="reg_dc" value="<?php esc_attr_e( $_POST['reg_dc'] ); ?>" />
					</p>	
					<p class="form-row form-row-wide">
						<label for="reg_buying_group"><?php _e( 'Buying Group', 'woocommerce' ); ?></label>
						<input type="text" class="input-text" name="reg_buying_group" id="reg_buying_group" value="<?php esc_attr_e( $_POST['reg_buying_group'] ); ?>" />
					</p>
					<p class="form-row form-row-wide">
						<label for="reg_receive_mrk_email"><?php _e( 'Do you agree to receive marketing emails from Peerless Umbrella:', 'woocommerce' ); ?></label>
						<select name="reg_receive_mrk_email" id="reg_receive_mrk_email" >
							<option value="yes" <?php echo (isset($_POST['reg_receive_mrk_email']) && $_POST['reg_receive_mrk_email'] == 'yes') ? 'selected' : ''; ?>>Yes</option>
							<option value="no" <?php echo (isset($_POST['reg_receive_mrk_email']) && $_POST['reg_receive_mrk_email'] == 'no') ? 'selected' : ''; ?>>No</option>
						</select>
					</p>	
					<!-- Spam Trap -->
					<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php esc_html_e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="woocommerce-form-row form-row">
						<?php wp_nonce_field( 'woocommerce-register' ); ?>
						<button type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
					</p>



					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>

			</div>

			<?php if ( $tabs ): ?>
				<div class="col-12 col-md-6 col-register-text">

					<span class="register-or wood-login-divider"><?php esc_html_e( 'Or', 'woodmart' ); ?></span>

					<?php 
					$reg_title = woodmart_get_opt( 'reg_title' ) ? woodmart_get_opt( 'reg_title' ) : esc_html__( 'Register', 'woocommerce' );
					$login_title = woodmart_get_opt( 'login_title' ) ? woodmart_get_opt( 'login_title' ) : esc_html__( 'Login', 'woocommerce' );

					$title = $reg_title;

					if ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) {
						$title = $login_title;
					}
					?>

					<?php if ( $login_text || $reg_text ): ?>
						<h2 class="wd-login-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif ?>

					<?php if ( $login_text ): ?>
						<div class="login-info"><?php echo do_shortcode( $login_text ); ?></div>
					<?php endif ?>

					<?php if ( $reg_text ): ?>
						<div class="registration-info"><?php echo do_shortcode( $reg_text ); ?></div>
					<?php endif ?>

					<?php 
					$button_text = esc_html__( 'Register', 'woocommerce' );

					if ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) {
						$button_text = esc_html__( 'Login', 'woocommerce' );
					}
					?>

					<a href="#" class="btn woodmart-switch-to-register" data-login="<?php esc_html_e( 'Login', 'woocommerce') ?>" data-login-title="<?php echo esc_attr( $login_title ) ?>" data-reg-title="<?php echo esc_attr( $reg_title ) ?>" data-register="<?php esc_html_e( 'Register', 'woocommerce') ?>"><?php echo esc_html( $button_text ); ?></a>

				</div>
			<?php endif ?>

		</div>
	<?php endif; ?>

</div><!-- .woodmart-registration-page -->

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>



