<?php
use Google\Client;
use Google\Service\Drive;

//----------------------------------------------------------------------------------
// Google Drive Upload File Functions
//----------------------------------------------------------------------------------
add_action( 'wp_ajax_nopriv_wc_google_file_upload', 'wc_google_file_upload' );
add_action( 'wp_ajax_wc_google_file_upload', 'wc_google_file_upload' );
function wc_google_file_upload( $up_image, $p_id ){

	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	$client = getClient();
	$upload_dir = wp_upload_dir();
	$image  = $up_image;

	$client->useApplicationDefaultCredentials();
	$client->addScope(Drive::DRIVE);

	$service = new Google\Service\Drive($client);
	$file = $image;
	$filename = basename($file);
	$filetype = wc_mime_content_type($filename);
	$post_id = $p_id;
	$wc_folder_ids  = get_post_meta( $post_id, 'wc_folder_ids', true  );

	if( empty($wc_folder_ids) ) {
		$postBody = new Google\Service\Drive\DriveFile([
			'name' => 'Quote-'.$post_id,
			'mimeType' => 'application/vnd.google-apps.folder',
			'parents' => ['13uDX5pU6pVTaCJyZstrQ3awGFFgNMfH_']
		]);

		$result = $service->files->create($postBody);
		$wc_folder_ids = $result->id;

		update_post_meta( $post_id, 'wc_folder_ids', $wc_folder_ids  );
	} 
	
	$resource = new Google\Service\Drive\DriveFile([
		'name' => $filename,
		'parents' => [$wc_folder_ids]
	]);

	$result = $service->files->create($resource, [
		'data' => file_get_contents($file),
		'mimeType' => $filetype,
		'uploadType' => 'multipart',
	]);

	$file_id = $result->id;
	//update_post_meta( $attach_ids, 'p_attach_ids', $file_id  );
	return $file_id;
}

//----------------------------------------------------------------------------------
// Google Drive Upload File Mime Type Functions
//----------------------------------------------------------------------------------
function wc_mime_content_type($filename) {

	$mime_types = array(
		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	if( $filename ){
		$ext = strtolower(array_pop(explode('.',$filename)));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}
}

//-----------------------------------------------------------------------
// Returns an authorized API client.
//-----------------------------------------------------------------------
function getClient(){
	$root_path = get_home_path();
	
	require __DIR__ . '/Google_Drive/vendor/autoload.php';

	/*if (php_sapi_name() != 'cli') {
		throw new Exception('This application must be run on the command line.');
	}*/

	putenv('GOOGLE_APPLICATION_CREDENTIALS='.$root_path.'/service-account.json');
	$client = new Client();
	$client->setApplicationName('Google Drive API PHP Quickstart');	
	$client->setScopes(array('https://www.googleapis.com/auth/drive.metadata.readonly','https://www.googleapis.com/auth/drive.file','https://www.googleapis.com/auth/drive','https://www.googleapis.com/auth/drive.appdata'));
	$client->setAuthConfig($root_path.'credentials.json');
	$client->setAccessType('offline');
	$client->setPrompt('select_account consent');
	$tokenPath = $root_path.'token.json';
	if (file_exists($tokenPath)) {
		$accessToken = json_decode(file_get_contents($tokenPath), true);
        print_r($accessToken);
		if( $accessToken ){
			$client->setAccessToken($accessToken);	
		}
		
	}
	try{
		if ($client->isAccessTokenExpired()) {
			if ($client->getRefreshToken()) {
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			} else {
				$authUrl = $client->createAuthUrl();
				printf("Open the following link in your browser:\n%s\n", $authUrl);
				print 'Enter verification code: ';
				$authCode = '';
				if( isset($_GET['code']) ){
					$authCode = trim($_GET['code']);
					$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);				
					$client->setAccessToken($accessToken);
					$_SESSION['google_access_token'] = $accessToken;

					if (array_key_exists('error', $accessToken)) {
						throw new Exception(join(', ', $accessToken));
					}
				}				

			}
			if (!file_exists(dirname($tokenPath))) {
				mkdir(dirname($tokenPath), 0700, true);
			}
			file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		}
	}
	catch(Exception $e) {
		echo 'Some error occured: '.$e->getMessage();
	}
	return $client;
}