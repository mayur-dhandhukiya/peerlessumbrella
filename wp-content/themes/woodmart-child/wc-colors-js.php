<?php
/*
*color frame View Html
*side-bar-content 
*search remove js
*three-dots dropdown
*mouseup div close
*close dropdown 
*color popup 
*favourite fill and ajax 
*favourite fill and ajax 
*sinle like & pop up like 
*copy url dropdown link 
*popup modal dismiss
*color popup quick view 
*color code copy
*color popup view
*sinle color code copy
*copyToClipboard function 
*/

add_action( 'wp_footer', 'wc_colors_pettle_js_func' );

function wc_colors_pettle_js_func(){

		/*===============================================================
		*==================| color frame View Html  |====================
		================================================================*/

		?>
		<div id="color-frame" class="color-frame mfp-hide white-popup-block">
			<div class="popup-title">
				<h2>Quick view</h2>
			</div>
			<div class="popup-content">
				<ul class="color-types">
					<li class="code code-pms" style="background-color:#ced4da;">
						<span class="code-type">PANTONE</span>
						<span class="color-code">100</span>
					</li>	
					<li class="code code-hex" style="background-color:#ced4da;">
						<span class="code-type">HEX</span>
						<span class="color-code">6635UH</span>
					</li>
					<li class="code code-hsb" style="background-color:#ced4da;">
						<span class="code-type">HSB</span>
						<span class="color-code">9, 5, 97</span>
					</li>
					<li class="code code-hsl" style="background-color:#ced4da;">
						<span class="code-type">HSL</span>
						<span class="color-code">9, 48, 95</span>
					</li>
					<li class="code code-cmyk" style="background-color:#ced4da;">
						<span class="code-type">CMYK</span>
						<span class="color-code">0, 4, 5, 3</span>
					</li>
				<!-- 	 <li class="code code-lab" style="background-color:#ced4da;">
						<span class="code-type">LAB</span>
						<span class="color-code">0, 4, 5, 3</span>
					</li>  -->
					<li class="code code-rgb" style="background-color:#ced4da;">
						<span class="code-type">RGB</span>
						<span class="color-code">248, 237, 235</span>
					</li>

				<!-- 	<li class="code code-rgba" style="background-color:#ced4da;">
						<span class="code-type">RGBA</span>
						<span class="color-code">248, 237, 235, 1</span>
					</li>
					<li class="code code-xyz" style="background-color:#ced4da;">
						<span class="code-type">XYZ</span>
						<span class="color-code">~ Seasell</span>
					</li> -->
				</ul>
			</div>
			<div class="popup-footer wc-all-color-pattels">
				<ul class="wc-colors">
					<li class="wc-sinle-color" style="background-color:#ced4da ;"><span class="color-name">#ced4da</span></li>
					<li class="wc-sinle-color" style="background-color:#adb5bd ;"><span class="color-name">#adb5bd</span></li>
					<li class="wc-sinle-color  has-dark-color" style="background-color:#6c757d ;"><span class="color-name">#6c757d</span></li>
					<li class="wc-sinle-color  has-dark-color" style="background-color:#343a40 ;"><span class="color-name">#343a40</span></li>
				</ul>
			</div>
			<div class="color_url">
				<a href="javascript:;" target="_blank">View Products</a>
			</div>
			<p><a class="popup-modal-dismiss" href="#"><i class="fa-solid fa-xmark"></i></a></p>
		</div>
		<script>
			jQuery( document ).ready(function() {

			/*===============================================================
			*=====================|side-bar-content  |========================
			================================================================*/

				jQuery(document).on("click", ".mobile-menu-icon", function() {
					jQuery(this).toggleClass("active");
					jQuery(".side-bar-content").slideToggle("");
				});

				jQuery(document).on("click", ".mobile-menu-icon a.open-menu", function() {
					jQuery('.side-bar-content').addClass('active');
				});

				jQuery(document).on('click', '.side-bar-close', function() {
					jQuery('.side-bar-content').removeClass('active');
					//jQuery(".mobile-menu-icon").removeClass('active');
				});

				jQuery(document).on('click', '.Explore-tabs .side-bar-close', function() {
					jQuery(".mobile-menu-icon").removeClass('active');
				});

			/*===============================================================
			*=====================|search remove js |========================
			================================================================*/

				jQuery(document).on('click',".wc-search-platters .search-palettes-container #palettes-search-input",function($){
					jQuery('.palette-card-colors').removeClass('active');
					jQuery(this).parents('.wc-search-platters').find('.palette-card-colors').addClass('active');
				});

			/*===============================================================
			*===================|three-dots dropdown |=======================
			================================================================*/

				jQuery(document).on('click',".three-dots",function($){
					jQuery(".dropdown").removeClass("active");
					jQuery(this).parents('.dropdown-container').find('.dropdown').addClass('active');
				});

			/*===============================================================
			*=====================|mouseup div close |=======================
			================================================================*/

				jQuery(document).mouseup(function(e){

					var container = jQuery(".dropdown.active, .palette-card-colors.wc_colors_search.active ");

					if (!container.is(e.target) && container.has(e.target).length === 0) 
					{
						container.removeClass("active");
					}
				});

			/*===============================================================
			*=====================| close dropdown  |=======================
			================================================================*/

				jQuery(document).on('click',".close-dropdown",function(){
					jQuery(".dropdown").removeClass("active");
				});

			/*===============================================================
			*=====================| color popup  |=======================
			================================================================*/	

				jQuery('.color-popup').magnificPopup({
					type: 'inline',
					preloader: false,
					focus: '#username',
					modal: true,
				});

			/*===============================================================
			*================| favourite fill and ajax  |====================
			================================================================*/	

				jQuery(document).on("click",'.wc-color-palette-wrap .wc-color-palette .palette-card-btns .palette-like-btn',function(e){

					e.preventDefault();

					var favourite = jQuery(this);
					var colors_id = delete_id = html = '';

					favourite.find('.fa-heart').css('display','none');

					favourite.find('.fa-spinner').css('display','inline-block');

					jQuery('.palettes-sidebar_palettes').html('<div class="palettes-sidebar-loader"><i class="fa fa-spinner fa-spin"></i></div>');

					if( favourite.parents('.wc-color-palette').hasClass('active') ){
						favourite.parents('.wc-color-palette').removeClass('active');
						favourite.parents('.wc-color-palette').find('.dropdown .save_palette').removeClass('active');
						delete_id = favourite.parents('.wc-color-palette').attr('colors_id');
					}else{
						favourite.parents('.wc-color-palette').addClass('active');
						favourite.parents('.wc-color-palette').find('.dropdown .save_palette').addClass('active');
						colors_id = favourite.parents('.wc-color-palette').attr('colors_id');
					}

					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						data : { action: "favourite_colors_list", post_id : colors_id , delete_id : delete_id },
						success: function(response) {

							jQuery('.palettes-sidebar_palettes').html(response.favourite_colors_ids_html);

							jQuery('.wc-color-palette-wrap .wc-color-palette .palette-card-btns .palette-like-btn').find('.fa-spinner').css('display','none');
							jQuery('.wc-color-palette-wrap .wc-color-palette .palette-card-btns .palette-like-btn').find('.fa-heart').css('display','inline-block');

							jQuery('.wc-color-palette .dropdown .save_palette .link-name').find('.fa-spinner').css('display','none');
							jQuery('.wc-color-palette .dropdown .save_palette .link-name').find('.fa-heart').css('display','inline-block');
						}
					});		

					return false;
				});

			/*===============================================================
			*================| favourite fill and ajax  |====================
			================================================================*/	

				jQuery(document).on("click",'.wc-color-palette.palette-card-colors .palette-card-btns .palette-like-btn',function(e){

					e.preventDefault();

					var favourite = jQuery(this);
					var colors_id = delete_id = html = '';

					favourite.find('.fa-heart').css('display','none');

					favourite.find('.fa-spinner').css('display','inline-block');

					if( favourite.parents('.wc-color-palette.palette-card-colors').hasClass('active') ){
						favourite.parents('.wc-color-palette.palette-card-colors').removeClass('active');
						favourite.parents('.wc-color-palette.palette-card-colors').find('.dropdown .save_palette').removeClass('active');
						delete_id = favourite.parents('.wc-color-palette.palette-card-colors').attr('colors_id');

					}else{
						favourite.parents('.wc-color-palette.palette-card-colors').addClass('active');
						favourite.parents('.wc-color-palette.palette-card-colors').find('.dropdown .save_palette').addClass('active');
						colors_id = favourite.parents('.wc-color-palette.palette-card-colors').attr('colors_id');
					}

					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						data : { action: "favourite_colors_list", post_id : colors_id , delete_id : delete_id },
						success: function(response) {
							jQuery('.wc-color-palette.palette-card-colors .palette-card-btns .palette-like-btn').find('.fa-spinner').css('display','none');
							jQuery('.wc-color-palette.palette-card-colors .palette-card-btns .palette-like-btn').find('.fa-heart').css('display','inline-block');

							jQuery('.wc-color-palette .dropdown .save_palette .link-name').find('.fa-spinner').css('display','none');
							jQuery('.wc-color-palette .dropdown .save_palette .link-name').find('.fa-heart').css('display','inline-block');
						}
					});		

					return false;
				});

			/*===============================================================
			*================| sinle like & pop up like |====================
			================================================================*/	

				jQuery(document).on("click",'.wc-color-palette .dropdown .save_palette .link-name, .wc-single-palette .palette-page_button .palette-like-btn , .wc-single-palette  .dropdown .save_palette .link-name',function(e){
					e.preventDefault();

					var single_favourite = jQuery(this);
					var colors_id = delete_id = html = '';

					single_favourite.find('.fa-heart').css('display','none');

					single_favourite.find('.fa-spinner').css('display','inline-block');

					if( single_favourite.parents('.wc-single-palette').hasClass('single_post') &&
						!single_favourite.parents('.wc-color-palette.palette-card-colors').length  ){

						if( single_favourite.parents('.palette-page_button').hasClass('active') ){
							single_favourite.parents('.palette-page_button').removeClass('active');
							single_favourite.parents('.palette-page_button').find('.dropdown .save_palette').removeClass('active');
							delete_id = single_favourite.parents('.wc-single-palette.single_post').attr('colors_id');
						}else{
							single_favourite.parents('.palette-page_button').addClass('active');
							single_favourite.parents('.palette-page_button').find('.dropdown .save_palette').addClass('active');
							colors_id = single_favourite.parents('.wc-single-palette.single_post').attr('colors_id');
						}

						jQuery.ajax({
							type : "post",
							dataType : "json",
							url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
							data : { action: "favourite_colors_list", post_id : colors_id , delete_id : delete_id },
							success: function(response) {

								jQuery('.wc-single-palette .palette-page_button .palette-like-btn').find('.fa-spinner').css('display','none');
								jQuery('.wc-single-palette .palette-page_button .palette-like-btn').find('.fa-heart').css('display','inline-block');

								jQuery('.wc-single-palette  .dropdown .save_palette .link-name').find('.fa-spinner').css('display','none');
								jQuery('.wc-single-palette  .dropdown .save_palette .link-name').find('.fa-heart').css('display','inline-block');
							}
						});			
					}else{
						jQuery(this).parents('.wc-color-palette').find('.palette-card-btns .palette-like-btn').click();
					}
					return false;
				});

			/*===============================================================
			*=================| copy url dropdown link |=====================
			================================================================*/	

				jQuery(document).on('click', '.wc-color-palette .palette-card-btns .dropdown a.link-name.copy-url,.wc-single-palette .palette-page_button .dropdown a.link-name.copy-url', function(e){
					e.preventDefault();

					var copy_url = jQuery(this);

					color_url = copy_url.attr('copy_url');
					copyToClipboard( color_url );
					if( color_url ){
						copy_url.find('.copy_span').html('<i class="fa fa-check check" aria-hidden="true"></i>');
					}else{
						copy_url.find('.copy_span').html('<i class="fa fa-times close" aria-hidden="true"></i>');
					}

					setTimeout(function(){
						jQuery('.dropdown a.link-name.copy-url .copy_span').html('');
					},1000);
					return false;
				});

			/*===============================================================
			*===================| popup modal dismiss |======================
			================================================================*/	

				jQuery(document).on('click', '.popup-modal-dismiss', function (e) {
					e.preventDefault();
					jQuery.magnificPopup.close();
				});


			/*===============================================================
			*====================| color code copy  |========================
			================================================================*/	

				jQuery(document).on('click', '.color-frame .popup-content .color-types li', function(){

					var t_this = jQuery(this);
					jQuery('.color-frame .tooltiptext').remove();
					var color_code = t_this.find('.color-code').text();
					if( color_code ){
						copyToClipboard( color_code );
						t_this.find('.color-code').after('<span class="tooltiptext">Copied</span>');
						t_this.find('.tooltiptext').addClass('active');
						setTimeout( function() {
							jQuery('.color-frame .tooltiptext').remove();
						}, 300 ); 
					}
				});

			/*===============================================================
			*==================| sinle color code copy |=====================
			================================================================*/	

				jQuery(document).on('click', '.wc-color-palette-wrap .wc-color-palette .wc-colors .wc-sinle-color, .wc-single-palette .wc-colors .wc-sinle-color', function(){

					var t_this = jQuery(this);
					var copied_color = t_this.find('.color-name').text();
					jQuery('.wc-sinle-color .tooltiptext').text('');
					jQuery('.tooltiptext').removeClass('active');
					copyToClipboard( copied_color );
					t_this.find('.tooltiptext').text('Copied');
					t_this.find('.tooltiptext').addClass('active');

					setTimeout( function() {
						jQuery('.wc-sinle-color .tooltiptext').text('');
						t_this.find('.tooltiptext').removeClass('active');
					}, 300 ); 
				});

			/*===============================================================
			*===============| copyToClipboard function |=====================
			================================================================*/	

				function copyToClipboard(text) {
					var textField = document.createElement('textarea');
					textField.innerText = text;
					document.body.appendChild(textField);
					textField.select();
					textField.focus(); 
					document.execCommand('copy');
					textField.remove();
				}

				// jQuery(document).on('click','.open-popup-link',function(){
				// 	jQuery('.white-popup').addClass('ani_start');
				// 	jQuery('.white-popup').removeClass('ani_end');
				// 	palette_colors_fullscreen_pop_up();
				// });

				jQuery(document).on('click','.mfp-close',function(){
					jQuery('.white-popup').addClass('ani_end');
					jQuery('.white-popup').removeClass('ani_start');
				});

				// function palette_colors_fullscreen_pop_up(){
				// 	//var $i = 1;
				// 	var lis = $(".white-popup ul.palette_colors-fullscreen li"), str;
				// 	for( var x = 1; x <= lis.length; x++){
				// 		str = ( 0.1 + 0.1 * ( x-1 ) ) + "s";
				// 		lis.eq( x-1 ).css({ "animation-delay" : str } );
				// 	}
				// }

			/*===============================================================
			*=====================| color popup view |======================
			================================================================*/	

				jQuery(document).on('click', '.color-frame .popup-footer .wc-colors li', function(){
					alert
					var t_this = jQuery(this);

					jQuery('.color-frame .popup-footer .wc-colors li').removeClass('active');
					t_this.addClass('active');

					if( t_this.hasClass('has-dark-color') ){
						t_this.parents('.color-frame').find('.popup-content .color-types').addClass('has-dark-color');
					} else {
						jQuery('.popup-content .color-types').removeClass('has-dark-color');
					}

					var url =  t_this.attr('data-palettes-url');
					if( url){
						t_this.parents('.color-frame').find('.color_url a').show();
						t_this.parents('.color-frame').find('.color_url a').attr( "href", url );						
					}else{
						t_this.parents('.color-frame').find('.color_url a').hide();
						t_this.parents('.color-frame').find('.color_url a').attr( "href", '' );
					}
					
					var rgb = t_this.attr('data-rgb');
					if( rgb ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-rgb .color-code').text(rgb);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-rgb .color-code').text('');
					}


					var rgba = t_this.attr('data-rgba');
					if( rgba ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-rgba .color-code').text(rgba);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-rgba .color-code').text('');
					}


					var hex = t_this.attr('data-hex');
					t_this.parents('.color-frame').find('.popup-content .color-types .code').css( 'background-color', hex );

					if( hex ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-hex .color-code').text(hex);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-hex .color-code').text('');
					}


					var cmyk = t_this.attr('data-cmyk');
					if( cmyk ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-cmyk .color-code').text(cmyk);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-cmyk .color-code').text('');
					}


					var hsl = t_this.attr('data-hsl');
					if( hsl ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-hsl .color-code').text(hsl);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-hsl .color-code').text('');
					}


					var hsla = t_this.attr('data-hsla');
					if( hsla ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-hsla .color-code').text(hsla);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-hsla .color-code').text('');
					}

					var hsb = t_this.attr('data-hsb');
					if( hsb ){
						jQuery('.color-frame .popup-content .color-types .code.code-hsb .color-code').text(hsb);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-hsb .color-code').text('');
					}

					var lab = t_this.attr('data-lab');
					if( lab ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-lab .color-code').text(lab);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-lab .color-code').text('');
					}

					var xyz = t_this.attr('data-xyz');
					if( xyz ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-xyz .color-code').text(xyz);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-xyz .color-code').text('');
					}

					var pms = t_this.attr('data-pms');
					if( pms ){
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-pms .color-code').text(pms);
					} else {
						t_this.parents('.color-frame').find('.popup-content .color-types .code.code-pms .color-code').text('');
					}

				});

				/*===============================================================
				*=================| color popup quick view |=====================
				================================================================*/	

				jQuery(document).on( 'click', '.color-popup', function() {


					var t_this = jQuery(this);
					var li_html = '';

					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						li_html = t_this.parents('.wc-single-palette ').find('.wc-colors').html();
					}else{
						li_html = t_this.parents('.wc-color-palette').find('.wc-colors').html();
					}

					if( li_html ){
						jQuery('.color-frame .popup-footer .wc-colors').html(li_html);
						jQuery('.color-frame .popup-footer .wc-colors li:first-child').addClass('active');
						setTimeout( function() {
							if( jQuery('.color-frame .popup-footer .wc-colors li:first-child').hasClass('has-dark-color') ){
								jQuery('.popup-content .color-types').addClass('has-dark-color');
							}	
						},100 );
					}


					var url = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						url = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-palettes-url');						
					}else{
						url = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-palettes-url');
					}



					if( url ){
						jQuery('.color-frame .color_url a').show();
						jQuery('.color-frame .color_url a').attr( "href", url );
					} else {
						jQuery('.color-frame .color_url a').hide();
						jQuery('.color-frame .color_url a').attr( '' );
					}

					var rgb = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						rgb = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-rgb');
					}else{
						rgb = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-rgb');
					}

					if( rgb ){
						jQuery('.color-frame .popup-content .color-types .code.code-rgb .color-code').text(rgb);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-rgb .color-code').text('');
					}

					var pms = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						pms = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-pms');
					}else{
						pms = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-pms');
					}

					if( pms ){
						jQuery('.color-frame .popup-content .color-types .code.code-pms .color-code').text(pms);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-pms .color-code').text('');
					}

					var rgba = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						rgba = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-rgba');
					}else{
						rgba = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-rgba');
					}

					if( rgba ){
						jQuery('.color-frame .popup-content .color-types .code.code-rgba .color-code').text(rgba);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-rgba .color-code').text(rgba);
					}

					var hex = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						hex = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-hex');
					}else{
						hex = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-hex');
					}

					jQuery('.color-frame .popup-content .color-types .code').css( 'background-color', hex );

					if( hex ){
						jQuery('.color-frame .popup-content .color-types .code.code-hex .color-code').text(hex);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-hex .color-code').text(hex);
					}

					var cmyk = '';

					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						cmyk = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-cmyk');
					}else{
						cmyk = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-cmyk');
					}

					if( cmyk ){
						jQuery('.color-frame .popup-content .color-types .code.code-cmyk .color-code').text(cmyk);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-cmyk .color-code').text('');
					}

					var hsl = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						hsl = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-hsl');
					}else{
						hsl = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-hsl');
					}

					if( hsl ){
						jQuery('.color-frame .popup-content .color-types .code.code-hsl .color-code').text(hsl);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-hsl .color-code').text('');
					}

					var hsla = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						hsla = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-hsla');
					}else{
						hsla = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-hsla');
					}

					if( hsla ){
						jQuery('.color-frame .popup-content .color-types .code.code-hsla .color-code').text(hsla);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-hsla .color-code').text('');
					}

					var hsb = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						hsb = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-hsb');
					}else{
						hsb = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-hsb');
					}

					if( hsb ){
						jQuery('.color-frame .popup-content .color-types .code.code-hsb .color-code').text(hsb);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-hsb .color-code').text('');
					}

					var lab = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						lab = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-lab');
					}else{
						lab = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-lab');
					}

					if( lab ){
						jQuery('.color-frame .popup-content .color-types .code.code-lab .color-code').text(lab);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-lab .color-code').text(lab);
					}

					var xyz = '';
					if( t_this.parents('.wc-single-palette').hasClass('single_post') && !t_this.parents('.wc-color-palette.palette-card-colors').length  ){
						xyz = t_this.parents('.wc-single-palette').find('.wc-colors li:first-child').attr('data-xyz');
					}else{
						xyz = t_this.parents('.wc-color-palette').find('.wc-colors li:first-child').attr('data-xyz');
					}

					if( xyz ){
						jQuery('.color-frame .popup-content .color-types .code.code-xyz .color-code').text(xyz);
					} else {
						jQuery('.color-frame .popup-content .color-types .code.code-xyz .color-code').text('');
					}
				});
});
</script>
<?php
}