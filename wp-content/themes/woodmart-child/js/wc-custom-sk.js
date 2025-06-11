jQuery(document).ready(function($){
	console.log('js-verstion-0.5');

    /*after load*/

	setTimeout(function(){
		single_product_check_active_color();

		if( jQuery('.wc-carousel-container.wc-gallery-thumb-mobile').length == 0){
			jQuery('.wd-carousel-container.wd-gallery-images').show();
		}
	},300);

    /*after load*/

    /*click events*/
	add_single_thumb_gallary_swiper();
	jQuery(document).on('click', '.wd-swatches-product.wd-swatches-single .wd-swatch', function() {
		setTimeout(function(){
			single_product_check_active_color();
			add_single_thumb_gallary_swiper();
		},300);

	});

	jQuery(document).on('click', '.wd-product .wd-swatch', function() {

		var current_image_class = '';
		var current_swatch = jQuery(this);
		var selectedColor = current_swatch.data('image-src');

		if( current_swatch.parents('.wd-product').find('.wcpb-product-badges-loop-container').length > 0 ){
			current_image_class = current_swatch.parents('.wd-product').find('.hover-img .wcpb-product-badges-loop-container img, .product-element-top .product-image-link .wcpb-product-badges-loop-container img');
		}else{
			current_image_class =  current_swatch.parents('.wd-product').find('.product-element-top img,.hover-img img');
		}

		checkImageExists(selectedColor, function(exists) {
			if (exists) {
				current_image_class.attr('src', selectedColor);
				console.log(12365);
				current_image_class.attr('srcset', selectedColor + ' 600w, ' +
					selectedColor + ' 150w, ' +
					selectedColor + ' 1200w, ' +
					selectedColor + ' 630w, ' +
					selectedColor + ' 263w, ' +
					selectedColor + ' 768w, ' +
					selectedColor + ' 896w, ' +
					selectedColor + ' 1500w');

				current_swatch.parents('.wd-product').removeClass('wd-loading-image');
				setTimeout(function(){
					current_swatch.addClass('wd-active wd-tooltip-inited');
					current_swatch.parents('.wd-product').removeClass('wd-loading-image');
				},200);
			} 
		}); 
	});

    /*click events*/

 	/*All Functions*/

	function single_product_check_active_color(){

		var carousel_color = jQuery('.wc-carousel-container .wc-carousel.wc-grid .wc-carousel-wrap');
		var wd_swatch_main = jQuery('.single-product .variations .value.cell.with-swatches .wd-swatches-product .wd-swatch');
		var gallary_Swiper = jQuery('.wc-carousel-container.wc-gallery-thumb-mobile');

		carousel_color.parents('.wc-gallery-thumb').removeClass('wc-active-gallary');
		carousel_color.find('.wc-carousel-color.wc-carousel-item').addClass('wc-hide-color');
		gallary_Swiper.find('.wc-carousel-content.wc-gallery-slider-mobile').addClass('wc-swiper-hide-swiping');


		wd_swatch_main.each(function(){

			var wd_swatch = jQuery(this);
			if( wd_swatch.hasClass('wd-active') ){
				current_color =  wd_swatch.data('value');
				carousel_color.find('.wc-carousel-color.wc-carousel-item[data-color="'+current_color+'"]').removeClass('wc-hide-color');
				carousel_color.parents('.wc-gallery-thumb').addClass('wc-active-gallary');
				gallary_Swiper.find('.wc-carousel-content.wc-gallery-slider-mobile.wc-gallery-slider-'+current_color).removeClass('wc-swiper-hide-swiping');
			}
		});
	}

	function add_single_thumb_gallary_swiper(){

		if (jQuery('.wc-gallery-slider-mobile').length > 0) {
			jQuery('.wc-gallery-slider-mobile').each(function(index) {
				var gallery_slider = jQuery(this);
				var swiper, swiper2;

				var mainSwiperSelector = gallery_slider.find('.gallary_Swiper').attr('slider-attr');
				
				if (mainSwiperSelector) {
					swiper = new Swiper('.'+mainSwiperSelector, {
						loop: true,
						spaceBetween: 10,
						slidesPerView: 4,
						freeMode: true,
						watchSlidesProgress: true,
						
					});
				}

				var thumbSwiperSelector = gallery_slider.find('.gallary_Swiper_img').attr('slider-attr');


				if (thumbSwiperSelector && swiper) {
					swiper2 = new Swiper('.'+thumbSwiperSelector, {
						loop: true,
						spaceBetween: 10,
						navigation: {
							nextEl: gallery_slider.find('.swiper-button-next')[index],
							prevEl: gallery_slider.find('.swiper-button-prev')[index],
						},
						thumbs: {
							swiper: swiper,
						},
					});
				}
			});
		}

	}

});

