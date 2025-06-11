<?php
add_action( 'woocommerce_single_product_summary', 'wc_zoomcatelog_button', 31 );
function wc_zoomcatelog_button(){ ?>
	<div class="wc-zoomcatelog-btn">
		<?php
		$redirect_uri = webby_zoom_get_auto_login_uri_call_back();
		$target = isset( $redirect_uri ) ? 'target=_blank' : '';
		$href = 'javascript:;';

		if( ! empty ( $redirect_uri ) ){
			$href = $redirect_uri;
		}
		?>
		<a href="<?php echo $href; ?>" <?php echo $target; ?> class="button woocommerce-zoomcatelog-add-product btn btn-default theme-button theme-btn">Create Presentation</a> 
		<!-- <a href="<?php //echo site_url('/presentation'); ?>" data-href="<?php //echo $href; ?>" <?php //echo $target; ?> class="button woocommerce-zoomcatelog-add-product btn btn-default theme-button theme-btn">Create Presentation</a> -->
	</div>
	<?php
}

add_action( 'wp_head', 'wc_zoomcatelog_css' );
function wc_zoomcatelog_css(){ ?>
	<style type="text/css">
		.wc-zoomcatelog-btn>a:before { content: "\f145"; font-family: woodmart-font; display: flex; align-items: center; justify-content: center; margin-right: 5px; width: 14px; height: 14px; font-weight: 400; }
		.single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-zoomcatelog-add-product:hover { color: #33333399; box-shadow: none; }
		.single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary .wc-zoomcatelog-btn{ order: 8; }
		.single-product .row.product-image-summary-wrap .col-lg-6.col-12.col-md-6.summary.entry-summary a.button.woocommerce-zoomcatelog-add-product { background: none; }
	</style>
	<?php
}

add_action( 'wp_ajax_webby_zoom_catelog', 'webby_zoom_catelog_call_back' );
add_action( 'wp_ajax_nopriv_webby_zoom_catelog', 'webby_zoom_catelog_call_back' );
function webby_zoom_catelog_call_back(){
	
	/*$zoom_array = array(
		'client_id' => 'zoom-api-www-pe81e.umbrella',
		'client_secret' => 'd44f998eb47687d269ed30cf7442623c',
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

	curl_close($ch);

	$result = json_decode( $result );

	$access_token = update_option( 'zoomcatalog_access_token', $result->access_token );

	wp_send_json( array( 'data' => $result ) );*/

	$client_id = get_option('zoom_client_id');
	$client_secret = get_option('zoom_client_secret');
	$zoom_username = get_option('zoom_username');

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

	curl_close($ch);

	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}

	$result = json_decode( $result );

	$access_token = update_option( 'zoomcatalog_access_token', $result->access_token );

	$user_token = update_option( 'zoomcatalog_user_token', $result->user_token );

	wp_send_json( array( 'data' => $result ) );

}

//add_action( 'wp', 'webby_new_user_access_token_call_back');
function webby_new_user_access_token_call_back(){
	
	$access_token = get_option( 'zoomcatalog_access_token' );	
	$user_token = get_option( 'zoomcatalog_user_token' );
	$client_id = get_option( 'zoom_client_id' );
	$client_secret = get_option( 'zoom_client_secret' );
	$zoom_username = get_option( 'zoom_username' );

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

	curl_close($ch);

	$result = json_decode( $result );

	if( isset( $access_token ) &&  ! empty( $access_token ) && !empty( $result->access_token ) && trim( $access_token ) != trim( $result->access_token ) ) {
		$access_token = update_option( 'zoomcatalog_access_token', $result->access_token );
		$user_token = update_option( 'zoomcatalog_user_token', $result->user_token );
	}

}


add_action('wp_ajax_webby_zoom_get_auto_login', 'webby_zoom_get_auto_login_call_back');
add_action('wp_ajax_nopriv_webby_zoom_get_auto_login', 'webby_zoom_get_auto_login_call_back');
function webby_zoom_get_auto_login_call_back(){
	$redirect_uri = webby_zoom_get_auto_login_uri_call_back();	
}

function webby_zoom_get_auto_login_uri_call_back(){

	$access_token = get_option( 'zoomcatalog_access_token' );	
	$user_token = get_option( 'zoomcatalog_user_token' );

	$ch = curl_init();
	$redirect_uri = "https://peerlessumbrella.zoomcustom.com/user/flyer/create";

	curl_setopt($ch, CURLOPT_URL, "https://api.zoomcatalog.com/auth/autologin?uri=$redirect_uri");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json", "Authorization: $user_token"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	$response = curl_exec($ch);
	curl_close($ch);

	$uri = '';
	if( $response ){
		$result = json_decode( $response );
		if( isset( $result->uri ) ){
			$uri = $result->uri;	
		}
	}

	if( empty( $uri ) ){
		webby_new_user_access_token_call_back();
		$uri = webby_zoom_get_auto_login_uri_call_back();
	}

	return $uri;

}


add_action('wp_ajax_webby_get_zoom_catelogs_all_catelogs', 'webby_get_zoom_catelog_all_catelogs_call_back');
add_action('wp_ajax_nopriv_webby_get_zoom_catelog_all_catelogs', 'webby_get_zoom_catelog_all_catelogs_call_back');
function webby_get_zoom_catelog_all_catelogs_call_back(){

	$access_token = get_option( 'zoomcatalog_access_token' );

	$user_token = get_option( 'zoomcatalog_user_token' );

	$ch = curl_init();

	//Set the user_token previously got from the authorize call
	//$user_token = "eyJ0eXAiOiJqd3QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXAiOiJ…";
	$api_url = "https://api.zoomcatalog.com/custom/catalogs"; //For Custom catalogs
	//or https://api.zoomcatalog.com/catalogs"; //For Non-Custom catalogs
	curl_setopt($ch, CURLOPT_URL, "https://api.zoomcatalog.com/custom/catalogs");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json", "Authorization: $user_token"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	$response = curl_exec($ch);

	curl_close($ch);

	$result = json_decode( $response );

	return $result;
}