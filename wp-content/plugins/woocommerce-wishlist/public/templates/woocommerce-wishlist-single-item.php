<?php

global $post, $product, $woocommerce_wishlist_options;

$elements = $woocommerce_wishlist_options['dataToShow']['enabled'];
$wishlist = $post;
$current_user_id = get_current_user_id();

if(!$wishlistProduct) {
	return false;
}

$parentProduct = false;
if($wishlistProduct->is_type('variation')) {
	$parentProduct = wc_get_product( $wishlistProduct->get_parent_id() );
}

$product_id = $wishlistProduct->get_id();
$product_url = get_permalink($product_id);

do_action( 'woocommerce_wishlist_item_start' );

echo '<div class="woocommerce-wishlist-item">';
	if($wishlist->post_author == $current_user_id) {
		echo '<a href="#" data-product="' . $product_id . '" class="woocommerce-wishlist-remove-product"><i class="fa fa-times"></i></a>';
	}

	if(isset($elements['im'])) {

		if ( has_post_thumbnail($product_id)) { 
			$product_image_id = get_post_thumbnail_id($wishlistProduct->get_id());
			$thumbnail = wp_get_attachment_image_src( $product_image_id, 'full' ); 
			$product_image_src = $thumbnail[0];
		} else { 
			$product_image_src = wc_placeholder_img_src();
		}
		echo '<a href="' . $product_url . '" class="woocommerce-wishlist-item-image">';
			echo sprintf('<img src="%s" alt="%s" class="woocommerce-wishlist-image-src">', $product_image_src, $wishlistProduct->get_title());
		echo '</a>';
	}
	?>

	<div class="woocommerce-wishlist-item-content">
	
		<?php
		do_action( 'woocommerce_wishlist_item_content_start' );

		// Title
		if(isset($elements['ti'])) {
			$title =  apply_filters( 'the_title', $wishlistProduct->get_title(), $wishlistProduct->get_id() );
			if(!empty($title)) {
				echo 
				'<a href="' . $product_url . '">
					<h4 class="woocommerce-wishlist-title">
						' . apply_filters( 'woocommerce_wishlist_title', $title ) . '
					</h4>
				</a>';
			}
		}

		// Rating
		if(isset($elements['re'])) {

			if($parentProduct) {
				$rating =  wc_get_rating_html( $parentProduct->get_average_rating() );
			} else {
				$rating =  wc_get_rating_html( $wishlistProduct->get_average_rating() );	
			}

			if(!empty($rating)) {
				echo 
				'<div class="woocommerce-wishlist-rating">
					' . apply_filters( 'woocommerce_wishlist_rating', $rating ) . '
				</div>';
			}
		}

		// Price
		if(isset($elements['pr'])) {
			$price =  $wishlistProduct->get_price_html();
			if(!empty($price)) {
				echo 
				'<div class="woocommerce-wishlist-price">
					' . apply_filters( 'woocommerce_wishlist_price', $price ) . '
				</div>';
			}
		}

		// Short Description
		if(isset($elements['sd'])) {

			$short_description =  $wishlistProduct->get_short_description();
			if($parentProduct && empty($short_description)) {
				$short_description = $parentProduct->get_short_description();
			}

			
			if(!empty($short_description)) {
				echo 
				'<div class="woocommerce-wishlist-short-description">
					' . apply_filters( 'woocommerce_wishlist_short_description', $short_description ) . '
				</div>';
			}
		}

		// Description
		if(isset($elements['de'])) {

			$description =  $wishlistProduct->get_description();
			if($parentProduct && empty($description)) {
				$description = $parentProduct->get_description();
			}

			if(!empty($description)) {
				echo 
				'<div class="woocommerce-wishlist-short-description">
					' . apply_filters( 'woocommerce_wishlist_description', $description ) . '
				</div>';
			}
		}
		?>

		<div class="woocommerce-wishlist-meta">
			<?php
			// Stock Status
			if(isset($elements['st'])) {

				$stock_status = wc_get_stock_html( $wishlistProduct );
				if(!empty($stock_status)) {
					echo 
					'<div class="woocommerce-wishlist-stock">' .
						apply_filters( 'woocommerce_wishlist_sku', $stock_status) .
					'</div>';
				}
			}

			// SKU
			if(isset($elements['sk'])) {

				$sku = $wishlistProduct->get_sku();
				if(!empty($sku)) {
					echo 
					'<div class="woocommerce-wishlist-sku">' .
						__('SKU: ', 'woocommerce-wishlist') . 
						apply_filters( 'woocommerce_wishlist_sku', $sku) .
					'</div>';
				}
			}

			// Tags
			if(isset($elements['tg'])) { 

				if($parentProduct) {
					$tags = wc_get_product_tag_list( 
							$parentProduct->get_id(), 
							', ', 
							'<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $parentProduct->get_tag_ids() ), 'woocommerce' ) . ' '
							, '</span>' );
				} else {
					$tags = wc_get_product_tag_list( 
							$wishlistProduct->get_id(), 
							', ', 
							'<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $wishlistProduct->get_tag_ids() ), 'woocommerce' ) . ' '
							, '</span>' );
				}

				if(!empty($tags)) {
					echo 
					'<div class="woocommerce-wishlist-tags">' .
						apply_filters( 'woocommerce_wishlist_tags', $tags) .
					'</div>';
				}
			}

			// Categories
			if(isset($elements['ct'])) { 

				if($parentProduct) {
					$categories = wc_get_product_category_list( 
								$parentProduct->get_id(), 
								', ', 
								'<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $parentProduct->get_category_ids() ), 'woocommerce' ) . ' ', 
								'</span>' 
							);
				} else {
					$categories = wc_get_product_category_list( 
								$wishlistProduct->get_id(), 
								', ', 
								'<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $wishlistProduct->get_category_ids() ), 'woocommerce' ) . ' ', 
								'</span>' 
							);
				}

				if(!empty($categories)) {
					echo 
					'<div class="woocommerce-wishlist-categories">' .
						apply_filters( 'woocommerce_wishlist_categories', $categories) .
					'</div>';
				}
			}
			?>
		</div>

		<?php if(isset($elements['ca'])) { ?> 
			<div class="woocommerce-wishlist-add-to-cart">
				<?php 
				if($product->is_type('simple')) {
					do_action( 'woocommerce_' . $wishlistProduct->get_type() . '_add_to_cart' ); 
				} else {
					echo '<a class="single_add_to_cart_button button btn btn-primary alt" href="' . $product->add_to_cart_url() . '">' . $product->add_to_cart_text() . '</a>';
				}
				?>
			</div>
		<?php } ?>

		<?php if(isset($elements['rm'])) { ?> 
			<div class="woocommerce-wishlist-read-more">
				<?php 
					printf('<a href="%s" class="woocommerce-wishlist-read-more-btn btn button">%s</a>', $wishlistProduct->get_permalink(), __('Read More', 'woocommerce-wishlist') );
				?>
			</div>
		<?php } ?>

		<?php if(isset($elements['at'])) { ?> 
			<div class="woocommerce-wishlist-attributes">
				<?php do_action( 'woocommerce_product_additional_information', $wishlistProduct ); ?>
			</div>
		<?php } ?>

		<?php do_action( 'woocommerce_wishlist_item_content_end' ); ?>

	</div>
	<hr>
</div>

<?php 
do_action( 'woocommerce_wishlist_item_end' ); 