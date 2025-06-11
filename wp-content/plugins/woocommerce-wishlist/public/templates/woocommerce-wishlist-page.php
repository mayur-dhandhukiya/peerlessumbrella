<?php
global $product, $woocommerce_wishlist_options;
$elements = $woocommerce_wishlist_options['dataToShow']['enabled'];

$current_user_id = get_current_user_id();
if($current_user_id !== 0) {
	$args = array(
	    'author' => $current_user_id,
	    'post_type' => 'wishlist',
	    'posts_per_page' => -1
	);
	$query = new WP_Query( $args );
	$wishlists = $query->posts;
}

$class = "";
if($woocommerce_wishlist_options['guestWishlistRemoveLoginText'] == "1" && !is_user_logged_in()) {
	$class = " woocommerce-wishlist-sidebar-hidden";
}

?>

<div class="woocommerce-wishlist-container woocommerce <?php echo $class ?>">

	<?php if($woocommerce_wishlist_options['guestWishlistRemoveLoginText'] == "0" || is_user_logged_in()) { ?>

	<div class="woocommerce-wishlist-sidebar">
		<?php
		if(!is_user_logged_in()) {
			$wishlistLoginPage = $woocommerce_wishlist_options['wishlistLoginPage'];
			if(!empty($wishlistLoginPage)) {
				$login_url = get_permalink($wishlistLoginPage);
			} else {
				$login_url = wp_login_url( get_permalink() );
			}
			echo '<a href="' . $login_url . '">';
				echo __('Please login to use all wishlist features.', 'woocommerce-wishlist');
			echo '</a>';
		} else {
		?>
			<div class="woocommerce-wishlist-your-wishlists">
				<h3><?php echo __('Your Favorites', 'woocommerce-wishlist'); ?></h3>
				<?php 	
	 			if(!empty($wishlists)) {
	 				echo '<ul>';
					foreach ($wishlists as $wishlist) {

						$visibility = get_post_meta($wishlist->ID, 'visibility', true);

						if($visibility == "public") {
							$visibility = '<i class="fa fa-globe"></i> ';
						} elseif ($visibility == "shared") {
							$visibility = '<i class="fa fa-link"></i> ';
						} else {
							$visibility = '<i class="fa fa-lock"></i> ';
						}

						echo '<li id="wishlist-li-' . $wishlist->ID . '">' .
								$visibility .
								'<a href="#" data-id="' .  $wishlist->ID . '" class="woocommerce-wishlist-view">' . $wishlist->post_title . '</a>' .
							'</li>';
					}
					echo '</ul>';
				} else {
					echo __('No Favorites created yet.', 'woocommerce-wishlist');
					echo '<ul>';
					echo '</ul>';
				}
				?>
			</div>
			<div class="woocommerce-wishlist-actions">
				<a href="#" id="woocommerce-wishlist-create" class="woocommerce-wishlist-action">
					<?php echo __('Create new Favorites', 'woocommerce-wishlist'); ?>
				</a>
				<?php if($woocommerce_wishlist_options['wishlistSearchEnable'] == "1") { ?>
				<br>
				<a href="<?php echo get_permalink($woocommerce_wishlist_options['wishlistSearchPage']) ?>" id="woocommerce-wishlist-search-btn" class="woocommerce-wishlist-action">
					<?php echo __('Search Public Favorites', 'woocommerce-wishlist'); ?>
				</a>
				<?php } ?>
			</div>
		<?php
		} 
		?>
	</div>

	<?php } ?>

	<div class="woocommerce-wishlist-items">

	</div>
</div>