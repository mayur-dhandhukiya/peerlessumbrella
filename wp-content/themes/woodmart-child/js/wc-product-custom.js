// jQuery(document).ready(function ($) {

// 	jQuery('.wc-carousel-image').magnificPopup({
// 		type: 'image',
// 		closeOnContentClick: true,
// 		closeBtnInside: false,
// 		fixedContentPos: true,
//             mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
//             image: {
//             	verticalFit: true
//             },
//             zoom: {
//             	enabled: true,
//                 duration: 300 // don't foget to change the duration also in CSS
//             }
//         });
// 	jQuery('.wc-carousel-video ').magnificPopup({
// 		disableOn: 700,
// 		type: 'iframe',
// 		mainClass: 'mfp-fade',
// 		removalDelay: 160,
// 		preloader: false,

// 		fixedContentPos: false
// 	});

// 	setTimeout(function(){
// 		jQuery(document).on( "ajaxComplete", function( event, xhr, settings ) {

// 			if(settings.url.indexOf('wc-ajax=get_variation') != -1){
// 				var m_url = jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-thumb .wd-carousel-wrap .wd-carousel-item img').attr('src');

// 				console.log(2222);
// 				console.log(m_url);
// 				jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a').attr('herf', m_url );
// 				jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('src', m_url );
// 				jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('srcset', m_url ); 
// 				jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('data-src', m_url ); 
// 				jQuery('.mobile_slider .woocommerce-product-gallery .wd-gallery-images .wd-carousel-wrap .wd-carousel-item a .wp-post-image').attr('data-large_image', m_url ); 
// 			}
// 		});
// 	},1000);
// });