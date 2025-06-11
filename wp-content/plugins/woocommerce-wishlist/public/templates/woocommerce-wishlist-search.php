<?php
global $product, $woocommerce_wishlist_options;
$elements = $woocommerce_wishlist_options['dataToShow']['enabled'];

$current_user_id = get_current_user_id();
if($current_user_id !== 0) {
	$args = array(
	    'author' => $current_user_id,
	    'post_type' => 'wishlist',
	);
	$query = new WP_Query( $args );
	$wishlists = $query->posts;
}


?> 

<div class="woocommerce-wishlist-container woocommerce">
	<div class="woocommerce-wishlist-search-form">
		<input type="text" id="woocommerce-wishlist-search" name="woocommerce_wishlist_search" placeholder="<?php echo __('Search for Public Favorites.', 'woocommerce-wishlist'); ?>">
	</div>

	<h3 class="woocommerce-wishlist-search-message">

	</h3>

	<div class="woocommerce-wishlist-search-items">

	</div>
</div>