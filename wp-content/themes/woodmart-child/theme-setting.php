<?php
add_action( 'admin_menu', 'zoom_catelog_setting_create_menu' );

function zoom_catelog_setting_create_menu() {

	//create new top-level menu
	add_menu_page( 'Zoom Catelog Settings', 'Zoom Catelog Settings', 'administrator', 'zoom-catalog-setting', 'zoom_catelog_setting_page' , 'dashicons-admin-generic');

	//call register settings function
	add_action( 'admin_init', 'register_my_theme_setting' );
}

function register_my_theme_setting() {
	//register our settings
	global $zoom_error;
	register_setting( 'my-theme-settings-group', 'zoom_client_id' );
	register_setting( 'my-theme-settings-group', 'zoom_client_secret' );
	register_setting( 'my-theme-settings-group', 'zoom_username' );

	if(isset($_GET['revoke'])) {
		delete_option( 'zoomcatalog_access_token' );
		delete_option( 'zoomcatalog_user_token' );
		wp_redirect( get_admin_url( 'zoom-catalog-setting' ) );
	}

	if ( isset( $_POST['formType'] ) && wp_verify_nonce( $_POST['formType'], 'zoomcatalogLogin' ) ) {

		$client_id = get_option('zoom_client_id');
		$client_secret = get_option('zoom_client_secret');
		$zoom_username = get_option('zoom_username');

		/*$zoom_array = array(
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'authorization_code'
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.zoomcatalog.com/auth/authorize');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $zoom_array ) );

		$headers = array();
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}

		$zoom_error = curl_error($ch);

		curl_close($ch);

		$result = json_decode( $result );*/

		$ch = curl_init();
		$body = array(
			"grant_type" => "authorization_code",
			"client_id" => $client_id,
			"client_secret" => $client_secret,
			"for_username" => $zoom_username
		);

		curl_setopt($ch, CURLOPT_URL, "https://api.zoomcatalog.com/auth/authorize");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $body ) );

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}

		$zoom_error = curl_error($ch);

		curl_close($ch);

		$result = json_decode( $result );

		if( isset( $result->access_token ) || isset( $result->user_token ) ) {
			update_option( 'zoomcatalog_access_token', $result->access_token );
			update_option( 'zoomcatalog_user_token', $result->user_token );
		} else {
			delete_option( 'zoomcatalog_access_token' );
			delete_option( 'zoomcatalog_user_token' );
		}
	}
}

function zoom_catelog_setting_page() {
	?>
	<div class="wrap">
		<h1>Zoom Catelog Setting</h1>
		<?php
		$access_token = get_option( 'zoomcatalog_access_token' );
		$disable = 0;
		if(!empty($access_token)) {
			$disable = 1;
			?>
			<div id="setting-error-tgmpa" class="notice notice-success settings-error is-dismissible"> 
				<p><strong><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">Zoom Catelog Successfully Integrated...</span></strong></p>
			</div>
		<?php } else { ?>
			<?php if(!empty($zoom_error)) { ?>
				<div class="error notice is-dismissible">
					<p><pre><?php print_r($zoom_error); ?></pre></p>
				</div>
			<?php } ?>
		<?php } ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'my-theme-settings-group' ); ?>
			<?php do_settings_sections( 'my-theme-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Client Id</th>
					<td><input style="width: 30%;" type="text" name="zoom_client_id" value="<?php echo esc_attr( get_option('zoom_client_id') ); ?>" <?php echo ($disable) ? 'disabled' : ''; ?> /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Client Secret</th>
					<td><input style="width: 30%;" type="text" name="zoom_client_secret" value="<?php echo esc_attr( get_option('zoom_client_secret') ); ?>" <?php echo ($disable) ? 'disabled' : ''; ?> /></td>
				</tr>

				<tr valign="top">
					<th scope="row">User Name</th>
					<td><input style="width: 30%;" type="text" name="zoom_username" value="<?php echo esc_attr( get_option('zoom_username') ); ?>" <?php echo ($disable) ? 'disabled' : ''; ?> /></td>
				</tr>
			</table>
			<?php wp_nonce_field( 'zoomcatalogLogin', 'formType' ); ?>
			<?php if(!empty($access_token)) { ?>
				<a href='<?php echo admin_url(sprintf(basename($_SERVER['REQUEST_URI']))); ?>&revoke=1' style="color:red;">Revoke</a>
			<?php } else { ?>
				<?php submit_button(); ?>
			<?php } ?>
		</form>
	</div>
<?php }