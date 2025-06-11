<div class="wrap">
   <h2>Book shelves
      <a href='<?php echo admin_url( "admin.php?page=book_shelves&action=add_new" ); ?>' class='add-new-h2'>Add New</a>
   </h2>
   <?php
      if (isset($_GET['action'])){
         
         if($_GET['action'] == "delete"){
         
            $names = '';
            $ids = explode(',', $_GET['shelfId']);
            if(count($ids) == 1)
            	$prefix = 'Book shelf';
            else
            	$prefix = 'Book shelves';
            foreach ($ids as $id) {
            	if($names != '')
            		$names = $names . ', ';
            	$book = get_option('real3dflipbook_shelves'.$id);
            	if($book)
            		$names = $names . $id;
            }
         
         	echo '<div id="message" class="updated notice is-dismissible below-h2">
         			<p><strong>'.$prefix .' </strong><i>' . $names.'</i> <strong>deleted</strong>. <a class="undo" href="#">Undo		</a></p>
         			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
         		</div>';
         
         }elseif($_GET['action'] == "delete_all"){
         
         	echo '<div id="message" class="updated notice is-dismissible below-h2">
         			<p>All shelves deleted. <a class="undo" href="#">Undo		</a></p>
         			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
         		</div>';
         
         }elseif($_GET['action'] == "import_from_json_confirm" ) {
         			
         	echo '<div id="message" class="updated notice is-dismissible below-h2">
         			<p>Shelves imported from JSON. <a class="undo" href="#">Undo		</a></p>
         			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
         		</div>';
         }	
      }		
      
      ?>			
   <div class="tablenav top">
      <div class="alignleft actions bulkactions">
         <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
         <select name="action" id="bulk-action-selector-top">
            <option value="-1" selected="selected">Bulk Actions</option>
            <option value="trash">Trash</option>
         </select>
         <input type="submit" id="doaction" class="button action bulkactions-apply" value="Apply">
      </div>
      <div class="tablenav-pages">
         <span class="displaying-num"></span>
         <span class="pagination-links"><a class="first-page" title="Go to the first page" href="#">«</a>
         <a class="prev-page" title="Go to the previous page" href="#">‹</a>
         <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input class="current-page" id="current-page-selector" title="Current page" type="text" name="paged" value="1" size="1"> of <span class="total-pages"></span></span>
         <a class="next-page" title="Go to the next page" href="#">›</a>
         <a class="last-page" title="Go to the last page" href="#">»</a></span>
      </div>
   </div>
   <table class='shelves-table wp-list-table widefat fixed striped pages'>
      <thead>
         <tr>
            <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
               <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
               <input id="cb-select-all-1" type="checkbox">
            </th>
            <th scope="col" id="name" class="manage-column column-title sorted desc"><a href="#"><span>Name</span><span class="sorting-indicator"></span></a></th>
            <th style="width:200px">Shortcode</th>
            <th style="width:100px" scope="col" id="name" class="manage-column column-title sorted desc"><a href="#"><span>Date</span><span class="sorting-indicator"></span></a></th>
         </tr>
      </thead>
      <tbody id="shelves-table">
      </tbody>
   </table>
   <div class="tablenav bottom">
      <div class="alignleft actions bulkactions">
         <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
         <select name="action" id="bulk-action-selector-bottom">
            <option value="-1" selected="selected">Bulk Actions</option>
            <option value="trash">Trash</option>
         </select>
         <input type="submit" id="doaction" class="button action bulkactions-apply" value="Apply">
      </div>
      <div class="tablenav-pages"><span class="displaying-num"></span>
         <span class="pagination-links"><a class="first-page" title="Go to the first page" href="#">«</a>
         <a class="prev-page" title="Go to the previous page" href="#">‹</a>
         <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Select Page</label><input class="current-page" id="current-page-selector" title="Current page" type="text" name="paged" value="1" size="1"> of <span class="total-pages"></span></span>
         <a class="next-page" title="Go to the next page" href="#">›</a>
         <a class="last-page" title="Go to the last page" href="#">»</a></span>
      </div>
   </div>
   <br/>
   <br/>
   <br/>
   <h3>Export shelves</h3>
   <p>
      <a class='button-secondary' href='<?php echo admin_url( "admin.php?page=book_shelves&action=download_json" ); ?>'>Download JSON</a>
   </p>
   <br/> 
   <br/> 
   <br/> 
   <h3>Import shelves</h3>
   <p>Import book shelves from JSON( overwrite existing shelves)</p>
   <textarea name="shelves" id="flipbook-admin-json" rows="20" cols="100" placeholder="Paste JSON here"></textarea>
   <p class="submit"><div class="button save-button button-secondary import">Import</div></p>
   <br/>
   <br/>
   <br/>
   <span class="submitbox"><a class="submitdelete" href='<?php echo admin_url( "admin.php?page=book_shelves&action=delete_all" );; ?>'>Delete all book shelves</a></span>
   <input type="text" id="copy-text-hidden" value="" style="opacity: 0; pointer-events: none; ">
</div>
<?php
wp_enqueue_script("real3d_flipbook_shelves");
$shelves_formatted = array();
foreach ($shelves as $b) {
$shelf = array(	"id" => $b['id'], 
"name" => $b['name'],
"date" => $b['date']
);
array_push($shelves_formatted,$shelf);
}

$r3d_nonce = wp_create_nonce( "r3d_nonce");

wp_localize_script( 'real3d_flipbook_shelves', 'shelves', json_encode($shelves_formatted) );
wp_localize_script( 'real3d_flipbook_shelves', 'r3d_nonce', $r3d_nonce);
if (isset($_GET['action']) && $_GET['action'] == "download_json") {
wp_localize_script( 'real3d_flipbook_shelves', 'shelves_json', json_encode($shelves) );
}