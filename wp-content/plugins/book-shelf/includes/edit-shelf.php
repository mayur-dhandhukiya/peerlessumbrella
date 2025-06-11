<div class='wrap'>
<div id='edit-shelf'>
<!--    <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-2">
         <div id="post-body-content"> -->
            <a href="admin.php?page=book_shelves" class="back-to-list-link">&larr; 
            <?php _e('Back to shelves', 'shelf'); ?>
            </a>
            <form method="post" id="shelf-options-form" enctype="multipart/form-data" action="admin-ajax.php?page=book_shelves&action=save_settings&shelfId=<?php echo($current_id);?>">
               <h1><span id="edit-shelf-text"></span>
               </h1>
               <div>
                  <div class="">
                     <h2>Shelf preview</h2>
                     <p id="r3d-save" class="submit">
                        <span class="spinner"></span>
                        <input type="submit" name="btbsubmit" id="btbsubmit" class="alignright button save-button button-primary" value="Save settings">
                     </p>
                     <br class="clear" />

                     <table class="form-table" id="shelf-settings">
                        <tbody>
                           <tr>
                              <td>
                                 <p>
                                    <a class="add-flipbook-button button-primary thickbox" href="#TB_inline?width=600&height=550&inlineId=add-flipbook-popup">Add flipbooks to shelf</a>
                                 </p>
                              </td>
                           </tr>
                           <tr>
                              <div class="book-shelf-preview">
                                 <div class="book-shelf-preview-covers ui-sortable">
                                 </div>
                                 <img src="" id="shelf-image">
                              </div>
                           </tr>
                           <tr>
                           <td>
                              
                              <h2>Shelf settings</h2>
                           </td>
                           </tr>

                        </tbody>
                     </table>
                     <!-- <div class="shelf-settings">
                     </div> -->
                  </div>
               </div>
               <div id="add-flipbook-popup" style="display:none;">
                  <h2>Select flipbooks</h2>
                  <div class="book-shelf-popup">
                     <table class='flipbooks-table wp-list-table widefat fixed striped pages'>
                        <thead>
                           <tr>
                              <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                                 <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                 <input id="cb-select-all-1" type="checkbox">
                              </th>
                              <th scope="col" id="thumbnail" class="manage-column column-title sorted desc"><span></span></th>
                              <th scope="col" id="name" class="manage-column column-title sorted desc"><span>Name</span></th>
                              <!-- <th style="width:200px">Shortcode</th> -->
                              <th style="width:100px" scope="col" id="date" class="manage-column column-title sorted desc"><span>Date created</span></th>
                           </tr>
                        </thead>
                        <tbody id="flipbooks-table">
                           <?php

                              if(defined("REAL3D_FLIPBOOK_VERSION")){
                              
                              $real3dflipbooks_ids = get_option('real3dflipbooks_ids');
                              if(!$real3dflipbooks_ids){
                                 $real3dflipbooks_ids = array();
                              }
                              $books = array();
                              
                              foreach ($real3dflipbooks_ids as $id) {
                                 // trace($id);
                                 $book = get_option('real3dflipbook_'.$id);



                                 trace($book);
                              
                                 if($book && isset($book['lightboxThumbnailUrl'])){
                              
                                    echo '<tr><th scope="row" class="manage-column column-cb check-column"><input type="checkbox" class="row-checkbox" name="'.$book['id'].'"></th><td><img id="img-'.$book['id'].'" src="'.$book['lightboxThumbnailUrl'].'"></td><td><strong><a href="#" class="edit" title="Edit" name="'.$book['id'].'">'.$book['name'].'</a></strong></td><td>'.$book['date'].'</td> </tr>';
                              
                              
                                 }
                              }
                              
                              }
                              
                           ?>
                        </tbody>
                     </table>

                     <div class="content">
                        <ul class="selectable">
                     </div>
                     <div class="bottom-toolbar">
                        <div class="">
                           <div class="">
                              <button type="button" class="book-shelf-button-select button button-primary button-large">Select</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <p id="r3d-save" class="submit">
                  <span class="spinner"></span>
                  <input type="submit" name="btbsubmit" id="btbsubmit" class="alignright button save-button button-primary" value="Save settings">
               </p>
               <div id="r3d-save-holder" style="display: none;" />
            </form>
     <!--        </div>
         </div> -->
        <!--  <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
               <div id="submitdiv" class="postbox ">
                  <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Publish</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                  <h2 class="hndle ui-sortable-handle"><span>Publish</span></h2>
                  <div class="inside">
                     <div class="submitbox" id="submitpost">
                        <div id="minor-publishing">
                           <div style="display:none;">
                              <p class="submit"><input type="submit" name="save" id="save" class="button" value="Save"></p>
                           </div>
                           <div id="minor-publishing-actions">
                              <div id="save-action">
                                 <input type="submit" name="save" id="save-post" value="Save Draft" class="button">
                                 <span class="spinner" deluminate_imagetype="gif"></span>
                              </div>
                              <div id="preview-action">
                                 <a class="preview button" href="http://localhost/wordpress/?page_id=281&amp;preview=true" target="wp-preview-281" id="post-preview">Preview<span class="screen-reader-text"> (opens in a new window)</span></a>
                                 <input type="hidden" name="wp-preview" id="wp-preview" value="">
                              </div>
                              <div class="clear"></div>
                           </div>
                           <div id="misc-publishing-actions">
                              <div class="misc-pub-section misc-pub-post-status">
                                 Status: <span id="post-status-display">Draft</span>
                              </div>
                           </div>
                           <div class="clear"></div>
                        </div>
                        <div id="major-publishing-actions">
                           <div id="delete-action">
                              <a class="submitdelete deletion" href="http://localhost/wordpress/wp-admin/post.php?post=281&amp;action=trash&amp;_wpnonce=e58e9a6ff0">Move to Trash</a>
                           </div>
                           <div id="publishing-action">
                              <span class="spinner" deluminate_imagetype="gif"></span>
                              <input name="original_publish" type="hidden" id="original_publish" value="Publish">
                              <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish">
                           </div>
                           <div class="clear"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="pageparentdiv" class="postbox ">
                  <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Shelf settings</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                  <h2 class="hndle ui-sortable-handle"><span>Shelf settings</span></h2>
                  <div class="inside">
                     <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="parent_id">Imgae</label></p>
                     <select name="parent_id" id="parent_id">
                        <option value="">(no image)</option>
                        <option class="level-0" value="307">wood</option>
                        <option class="level-0" value="96">glass</option>
                        <option class="level-0" value="122">metal</option>
                     </select>
                     <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="menu_order">Image margin top</label></p>
                     <input name="menu_order" type="text" size="4" id="menu_order" value="0"><span>px</span>
                     <p>Need help? Use the Help tab above the screen title.</p>
                  </div>
               </div>
               <div id="postimagediv" class="postbox ">
                  <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Featured Image</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                  <h2 class="hndle ui-sortable-handle"><span>Featured Image</span></h2>
                  <div class="inside">
                     <p class="hide-if-no-js"><a href="http://localhost/wordpress/wp-admin/media-upload.php?post_id=281&amp;type=image&amp;TB_iframe=1" id="set-post-thumbnail" class="thickbox">Set featured image</a></p>
                     <input type="hidden" id="_thumbnail_id" name="_thumbnail_id" value="-1">
                  </div>
               </div>
            </div>
         </div> -->
      <!-- </div> -->
   </div>
</div>
<?php 
$shelves[$current_id]['PLUGIN_DIR_URL'] = $this->PLUGIN_DIR_URL;
wp_enqueue_media();
// wp_enqueue_script('common');
// wp_enqueue_script('wp-lists');
// wp_enqueue_script('postbox');
add_thickbox(); 


wp_enqueue_script(
   'alpha-color-picker',
   plugins_url(). '/book-shelf/js/alpha-color-picker.js', 
   array( 'jquery', 'wp-color-picker' ),
   $this->PLUGIN_VERSION,
   true
);

wp_enqueue_style(
   'alpha-color-picker',
   plugins_url(). '/book-shelf/css/alpha-color-picker.css', 
   array( 'wp-color-picker' ),
   $this->PLUGIN_VERSION 
);

wp_enqueue_script( 
   "edit_shelf", 
   plugins_url(). "/book-shelf/js/edit-shelf.js", 
   array( 'alpha-color-picker', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-selectable', 'wp-color-picker' ),
   $this->PLUGIN_VERSION
); 

wp_enqueue_style( 
   'edit_shelf_css', 
   plugins_url(). "/book-shelf/css/edit-shelf.css",
   array(), 
   $this->PLUGIN_VERSION 
); 
wp_localize_script( 
   'edit_shelf', 
   'shelf', 
   json_encode($shelves[$current_id]) 
);