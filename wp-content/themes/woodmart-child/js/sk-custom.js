jQuery(document).ready(function () {

	jQuery('.country-form-popup').magnificPopup();
	setTimeout(function(){
		jQuery('.country-form-popup').click();
	},200);

	jQuery('.color-popup').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#username',
		modal: true,
	});

	jQuery('.open-popup-link').magnificPopup({
		type: 'inline',
		midClick: true,
		mainClass: 'mfp-fade'
	});
	
	jQuery(document).on('click','.open-popup-link',function(){
		jQuery('.white-popup').addClass('ani_start');
		jQuery('.white-popup').removeClass('ani_end');
		palette_colors_fullscreen_pop_up();
	});

	function palette_colors_fullscreen_pop_up(){
					//var $i = 1;
		var lis = $(".white-popup ul.palette_colors-fullscreen li"), str;
		for( var x = 1; x <= lis.length; x++){
			str = ( 0.1 + 0.1 * ( x-1 ) ) + "s";
			lis.eq( x-1 ).css({ "animation-delay" : str } );
		}
	}	
});