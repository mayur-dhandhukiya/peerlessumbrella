<?php
global $post, $product, $woocommerce_wishlist_options;

$elements = $woocommerce_wishlist_options['dataToShow']['enabled'];
$wishlist = $post;
$current_user_id = get_current_user_id();

$visibility = get_post_meta($wishlist->ID, 'visibility', true);

do_action( 'woocommerce_wishlist_before_wishlist' );
?>

<div class="woocommerce-wishlist-header">
	<h3 class="woocommerce-wishlist-header-title"><?php echo $wishlist->post_title ?></h3>
	<?php 
	if($wishlist->post_author != $current_user_id && $visibility == 'private') {
		echo __('This Favorites is set to private.', 'woocommerce-wishlist');
		echo '</div>';
		return false;
	} elseif($wishlist->post_author == $current_user_id) {

	?>
	<div class="woocommerce-wishlist-header-actions">
		<a href="#" data-id="<?php echo $wishlist->ID ?>" class="woocommerce-wishlist-edit"><?php echo __('Edit Favorites', 'woocommerce-wishlist') ?></a>
		<a href="#" data-id="<?php echo $wishlist->ID ?>" class="woocommerce-wishlist-delete"><?php echo __('Delete Favorites', 'woocommerce-wishlist') ?></a>
	</div>
	<?php } ?>
</div>

<?php

do_action( 'woocommerce_wishlist_before_products' );

$products = get_post_meta($wishlist->ID, 'products', true);

if(!$products || empty($products)) {
	echo $woocommerce_wishlist_options['textNoProducts'];
	return false;
}

foreach ($products as $product) {
	
	$product = wc_get_product($product['product_id']);

	wc_get_template( 'woocommerce-wishlist-single-item.php', array('wishlistProduct' => $product), '', plugin_dir_path(__FILE__));

	wp_reset_postdata();
}

do_action( 'woocommerce_wishlist_after_products' );

if($woocommerce_wishlist_options['exportCSV'] == "1" || $woocommerce_wishlist_options['exportXLS'] == "1") {

	$url = get_permalink($woocommerce_wishlist_options['wishlistPage']) . '?export-wishlist=' . $wishlist->ID;

	echo '<div class="woocommerce-wishlist-export">';

	if($woocommerce_wishlist_options['exportCSV'] == "1") {
		echo '<a href="' . $url . '&type=csv" class="woocommerce-wishlist-export-button woocommerce-wishlist-export-button-csv"><i class="fa fa-download"></i> ' . __('Export as CSV', 'woocommerce-wishlist') . '</a>';
	}

	if($woocommerce_wishlist_options['exportXLS'] == "1") {
		echo '<a href="' . $url . '&type=xls" class="woocommerce-wishlist-export-button woocommerce-wishlist-export-button-xls"><i class="fa fa-download"></i> ' . __('Export as XLS', 'woocommerce-wishlist') . '</a>';
	}

	echo '</div>';
}

if($woocommerce_wishlist_options['shareEnabled'] == "1" && $visibility !== "private") {

	echo '<div class="woocommerce-wishlist-share">';

		echo '<div class="woocommerce-wishlist-share-title">'. __('Share this Favorites ...', 'woocommerce-wishlist') . '</div>';

		$title = $woocommerce_wishlist_options['shareTitle'];
		$url = get_permalink($woocommerce_wishlist_options['wishlistPage']) . '?wishlist=' . $wishlist->ID;

		if($woocommerce_wishlist_options['sharePrint'] == "1") {
			echo '<a href="javascript:window.print();" class="woocommerce-wishlist-share-print"><i class="fa fa-print"></i></a>';
		}

		if($woocommerce_wishlist_options['shareFacebook'] == "1") {
			$data = array(
				'title' => $title,
				'u' => $url,
			);
			$share_url = '//www.facebook.com/sharer.php?' . http_build_query($data);

			if($woocommerce_wishlist_options['fontAwesome5'] == "1") {
				echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-facebook" target="_blank"><i class="fab fa-facebook"></i></a>';
			} else {
				echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-facebook" target="_blank"><i class="fa fa-facebook"></i></a>';
			}
			
		}

		if($woocommerce_wishlist_options['shareTwitter'] == "1") {
			$data = array('url' => $url);
			$share_url = '//twitter.com/share?' . http_build_query($data);
			if($woocommerce_wishlist_options['fontAwesome5'] == "1") {
				echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-twitter" target="_blank"><i class="fab fa-twitter"></i></a>';
			} else {
				echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-twitter" target="_blank"><i class="fa fa-twitter"></i></a>';	
			}

		}

		if($woocommerce_wishlist_options['sharePinterest'] == "1") {
			$data = array('url' => $url);
			$share_url = '//pinterest.com/pin/create/?' . http_build_query($data);
			if($woocommerce_wishlist_options['fontAwesome5'] == "1") {
				echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-pinterest" target="_blank"><i class="fab fa-pinterest"></i></a>';
			} else {
				echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-pinterest" target="_blank"><i class="fa fa-pinterest"></i></a>';	
			}

		}

		if($woocommerce_wishlist_options['shareEmail'] == "1") {
			$data = array(
				'subject' => $title,
				'body' => $url,
			);
			$share_url = 'mailto:?' . http_build_query($data);
			echo '<a href="' . $share_url . '" class="woocommerce-wishlist-share-envelope" target="_blank"><i class="fa fa-envelope"></i></a>';
		}


	echo '</div>';
}

do_action( 'woocommerce_wishlist_after_wishlist' );
?>