<?php

// trace($this);
$current_action = $current_id = '';

if (isset($_GET['action']) ) {
	$current_action = $_GET['action'];
}

if (isset($_GET['shelfId']) ) {
	$current_id = $_GET['shelfId'];
}

$shelf_ids = get_option('shelf_ids');
if(!$shelf_ids){
	$shelf_ids = array();
}
$shelves = array();
foreach ($shelf_ids as $id) {
	// trace($id);
	$shelf = get_option('shelf_'.$id);
	// trace($shelf);
	if($shelf){
		$shelves[$id] = $shelf;
		// array_push($flipbooks,$book);
	}else{
		//remove id from array
		$shelf_ids = array_diff($shelf_ids, array($id));
	}
}

switch( $current_action ) {

	case 'edit':

		include("edit-shelf.php");

		break;

	case "add_new":

	//generate ID 
		$new_id = 0;
		$highest_id = 0;

		foreach ($shelf_ids as $id) {
			if((int)$id > $highest_id) {
				$highest_id = (int)$id;
			}
		}

		$current_id = $highest_id + 1;
		//create new book 
		$shelf = array(	"id" => $current_id, 
						"name" => "shelf " . $current_id,
						// "items" => array(),
						"date" => current_time( 'mysql' )
					);
		//save new book to database
		delete_option('shelf_'.(string)$current_id);
		add_option('shelf_'.(string)$current_id,$shelf);
		//add new book to books
		// array_push($flipbooks,$book);
		$shelves[$current_id] = $shelf;
		
		//save new id to array of id-s
		array_push($shelf_ids,$current_id);
		update_option('shelf_ids',$shelf_ids);

		include("edit-shelf.php");
	
		break;

	case 'duplicate':

		$new_id = 0;
		$highest_id = 0;

		foreach ($shelf_ids as $id) {
			if((int)$id > $highest_id) {
				$highest_id = (int)$id;
			}
		}
		$new_id = $highest_id + 1;

		$shelves[$new_id] = $shelves[$current_id];
		$shelves[$new_id]["id"] = $new_id;
		$shelves[$new_id]["name"] = $shelves[$current_id]["name"]." (copy)";
		
		$shelves[$new_id]["date"] = current_time( 'mysql' );

		delete_option('shelf_'.(string)$new_id);
		add_option('shelf_'.(string)$new_id,$shelves[$new_id]);

		array_push($shelf_ids,$new_id);

		delete_option('shelf_ids');
		add_option('shelf_ids',$shelf_ids);

		include("shelves.php");

		break;

	case 'delete':
		//backup
		delete_option('shelf_ids_back');
		add_option('shelf_ids_back',$shelf_ids);
		foreach ($shelf_ids as $id) {
			update_option("shelf_ids",array());
		}
		
		$ids = explode(',', $current_id);
		
		foreach ($ids as $id) {
			unset($shelves[$id]);
		}
		$shelf_ids = array_diff($shelf_ids, $ids);
		update_option('shelf_ids', $shelf_ids);
		
		include("shelves.php");

		break;

	case "delete_all":

		delete_option('shelf_ids_back');
		add_option('shelf_ids_back',$shelf_ids);
		foreach ($shelf_ids as $id) {
			delete_option('shelf_'.(string)$id);
		}
		$shelves = array();
		include("shelves.php");

		break;

	default:

		include("shelves.php");

		break;

}