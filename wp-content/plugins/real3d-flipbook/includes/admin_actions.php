<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$current_action = $current_id = $page_id = '';
// handle action from url
if (isset($_GET['action']) ) {
	$current_action = $_GET['action'];
}

if (isset($_GET['bookId']) ) {
	$current_id = $_GET['bookId'];
}

if (isset($_GET['pageId']) ) {
	$page_id = $_GET['pageId'];
}

$url=admin_url( "admin.php?page=real3d_flipbook_admin" );

$reak3dflipbooks_converted = get_option("reak3dflipbooks_converted");

if(!$reak3dflipbooks_converted){

	$flipbooks = get_option("flipbooks");
	if(!$flipbooks){
		$flipbooks = array();
	}

	add_option('reak3dflipbooks_converted', true);
	$real3dflipbooks_ids = array();
	//trace('converting flipbooks...');
	foreach ($flipbooks as $b) {
		$id = $b['id'];
		//trace($id);
		delete_option('real3dflipbook_'.(string)$id);
		add_option('real3dflipbook_'.(string)$id, $b);
		array_push($real3dflipbooks_ids,(string)$id);
	}
}else{
	$real3dflipbooks_ids = get_option('real3dflipbooks_ids');
	if(!$real3dflipbooks_ids){
		$real3dflipbooks_ids = array();
	}
	$flipbooks = array();
	foreach ($real3dflipbooks_ids as $id) {
		$book = get_option('real3dflipbook_'.$id);
		if($book){
			$flipbooks[$id] = $book;
		}else{
			//remove id from array
			$real3dflipbooks_ids = array_diff($real3dflipbooks_ids, array($id));
		}
	}
}

update_option('real3dflipbooks_ids', $real3dflipbooks_ids);

switch( $current_action ) {

	case 'reset_settings':
		r3dfb_setDefaults();
		include("general.php");
		break;

	case 'save_settings':
		update_option("real3dflipbook_global", $_POST);
		include("general.php");
		break;

	case 'edit':
		include("edit-flipbook.php");
		break;
		
	case 'delete':
		//backup
		delete_option('real3dflipbooks_ids_back');
		add_option('real3dflipbooks_ids_back',$real3dflipbooks_ids);
		foreach ($real3dflipbooks_ids as $id) {
			update_option("real3dflipbooks_ids",array());
		}
	
		$ids = explode(',', $current_id);
		
		foreach ($ids as $id) {
			unset($flipbooks[$id]);
		}
		$real3dflipbooks_ids = array_diff($real3dflipbooks_ids, $ids);
		update_option('real3dflipbooks_ids', $real3dflipbooks_ids);
						
		include("flipbooks.php");

		break;
		
	case 'delete_all':
		//backup
		delete_option('real3dflipbooks_ids_back');
		add_option('real3dflipbooks_ids_back',$real3dflipbooks_ids);
		foreach ($real3dflipbooks_ids as $id) {
			delete_option('real3dflipbook_'.(string)$id);
		}

		delete_option('real3dflipbook_1');
		delete_option('real3dflipbook_2');
		delete_option('real3dflipbook_3');
		delete_option('real3dflipbook_4');
		delete_option('real3dflipbook_5');

		delete_option('real3dflipbook_ids');
		$flipbooks = array();
		include("flipbooks.php");

		break;
		
	case 'duplicate':
		$new_id = 0;
		$highest_id = 0;

		foreach ($real3dflipbooks_ids as $id) {
			if((int)$id > $highest_id) {
				$highest_id = (int)$id;
			}
		}
		$new_id = $highest_id + 1;
		$flipbooks[$new_id] = $flipbooks[$current_id];
		$flipbooks[$new_id]["id"] = $new_id;
		$flipbooks[$new_id]["name"] = $flipbooks[$current_id]["name"]." (copy)";
		
		$flipbooks[$new_id]["date"] = current_time( 'mysql' );

		delete_option('real3dflipbook_'.(string)$new_id);
		add_option('real3dflipbook_'.(string)$new_id,$flipbooks[$new_id]);

		array_push($real3dflipbooks_ids,$new_id);
		update_option('real3dflipbooks_ids',$real3dflipbooks_ids);


		include("flipbooks.php");
		break;
		
	case 'add_new':
		$new_id = 0;
		$highest_id = 0;

		foreach ($real3dflipbooks_ids as $id) {
			if((int)$id > $highest_id) {
				$highest_id = (int)$id;
			}
		}

		$current_id = $highest_id + 1;
		//create new book 
		$book = array(	"id" => $current_id, 
						"name" => "flipbook " . $current_id,
						"pages" => array(),
						"date" => current_time( 'mysql' ),
						"status" => "draft"
					);
		$flipbooks[$current_id] = $book;

		include("edit-flipbook.php");
		break;
		
	case 'add_new_cat':
		
		break;
		
	case 'generate_json':
		include("flipbooks.php");
		break;
	
	case 'import_from_json':
		include("flipbooks.php");
		break;
	
	case 'import_from_json_confirm':
		//backup
		delete_option('real3dflipbooks_ids_back');
		add_option('real3dflipbooks_ids_back',$real3dflipbooks_ids);

		//delete all flipbooks
		foreach ($real3dflipbooks_ids as $id) {
			delete_option('real3dflipbook_'.(string)$id);
		}

		$json = stripslashes($_POST['flipbooks']);

		$newFlipbooks = r3dfb_objectToArray(json_decode($json));
		if((string)$json != "" && is_array($newFlipbooks)){
			$real3dflipbooks_ids = array();
			foreach ($newFlipbooks as $b) {
				$id = $b['id'];
				add_option('real3dflipbook_'.(string)$id, $b);
				array_push($real3dflipbooks_ids,(string)$id);
			}
			update_option('real3dflipbooks_ids', $real3dflipbooks_ids);
			$flipbooks = $newFlipbooks;
		}
		include("flipbooks.php");
		break;
		
	case 'undo':

		$real3dflipbooks_ids = get_option('real3dflipbooks_ids_back');

		$flipbooks = array();
		foreach ($real3dflipbooks_ids as $id) {
			$book = get_option('real3dflipbook_'.$id);
			if($book){
				$flipbooks[$id] = $book;
			}else{
				//remove id from array
				$real3dflipbooks_ids = array_diff($real3dflipbooks_ids, array($id));
			}
		}
		update_option('real3dflipbooks_ids', $real3dflipbooks_ids);


		include("flipbooks.php");
		break;
	
	default:

		include("flipbooks.php");
		break;
		
}


if(!function_exists("r3dfb_objectToArray")){

	function r3dfb_objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}

}


