<?php
get_header();

use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Rgb;
use Spatie\Color\Distance;
use Spatie\Color\Contrast;
use Spatie\Color\Hex;
require_once get_stylesheet_directory() .'/Color_Palatte/vendor/autoload.php';

global $post;

$post_id = $post->ID;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$color_pattel = get_post_meta( $post_id, 'custom_repeater_item', true ); 
$color_palettes_url = get_post_meta( $post_id, 'color_palettes_url', true ); 
$user_colors = get_user_meta( $user_id, 'favourite_colors_ids', true ); 
$user_colors = explode(',', $user_colors);

if( !empty( $user_colors ) && $user_colors ){
	if( in_array( $post_id, $user_colors ) ){
		$class = 'active';    
	}else{
		$class = '';
	}
}

?>
<div class="wc-single-palette single_post" colors_id="<?php echo $post_id; ?>">
	<div class="wc-plalette-top">
		<h2 class="wc-palette-name"><?php echo get_the_title(); ?></h2>
		<div class="palette-page_button <?php echo $class; ?>">
			<a class="palette-like-btn" href="#"><i class="fa fa-heart"></i><i class="fa fa-spinner fa-spin"></i></a>
			<div class="menu-nav">
				<div class="dropdown-container" tabindex="-1">
					<div class="three-dots"></div>
					<div class="dropdown">
						<ul>
							<li><a href="#" class="link-name copy-url" copy_url="<?php echo $color_palettes_url; ?>"><i class="fa fa-clone"></i>Copy URL<span class="copy_span"></span></a></li>
							<li><a href="#color-frame" class="color-popup link-name close-dropdown"><i class="fa fa-eye"></i>Quick view</a></li>
							<li><a href="#test-popup-<?php echo $post_id; ?>" class="link-name open-popup-link"><i class="fa fa-up-right-and-down-left-from-center"></i>View fullscreen</a></li>
							<li class="save_palette <?php echo $class; ?>"><a href="#" class="link-name"><i class="fa fa-heart"></i><i class="fa fa-spinner fa-spin"></i>Save palette</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="test-popup-<?php echo $post_id; ?>" class="white-popup mfp-hide">
		<ul class="palette_colors-fullscreen">
			<?php foreach( $color_pattel as $color ){ 
				$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : ''; ?>
				<li class="light" style="background:<?php echo $color_name ?>"></li>
			<?php } ?>
		</ul>
	</div>
	<!-- big-pletters -->
	<div class="palette-big_copy">
		<?php 

		if( $color_pattel ){
			echo '<ul class="wc-colors single_colors">';
			foreach( $color_pattel as $color ){
				$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : '';
				$color_pms = isset( $color['color_pms'] ) ? $color['color_pms'] : '';
				$palettes_url = isset( $color['palettes_url'] ) ? $color['palettes_url'] : '';
						//$str_replace = str_replace( '#', '', $color_name );
				$dark_color_class = ( $color['dark_color_palettes'] ) ? 'has-dark-color' : '';

				$rgb = Hex::fromString( $color_name );

				$hex2 = Hex::fromString('#2d78c8');

				$rgb = $rgb->toRgb();
				$new_rgb = str_replace( 'rgb', '', $rgb );

				$rgba = $rgb->toRgba(); 
				$rgba->alpha(); 
				$new_rgba = str_replace( 'rgba', '', $rgba );

				$hex = $rgb->toHex(); 
				$rgba->alpha(); 

				$cmyk = $rgb->toCmyk(); 
				$cmyk = str_replace( 'cmyk', '', $cmyk );

				$hsl = $rgb->toHsl(); 
				$hsl = str_replace( 'hsl', '', $hsl );

				$hsla = $rgb->toHsla();
				$hsla = str_replace( 'hsla', '', $hsla );

				$hsb = $rgb->toHsb(); 
				$hsb = str_replace( 'hsb', '', $hsb );

				$lab = $rgb->toCIELab();
				$lab = str_replace( 'CIELab', '', $lab );

				$xyz = $rgb->toXyz();
				$xyz = str_replace( 'xyz', '', $xyz );


				echo '<li class="wc-sinle-color '.$dark_color_class.'" data-palettes-url="'.$palettes_url.'" data-pms="'.$color_pms.'" data-url="'.$color_palettes_url.'" data-rgb="'.$new_rgb.'" data-rgba="'.$new_rgba.'" data-hex="'.$hex.'" data-cmyk="'.$cmyk.'" data-hsl="'.$hsl.'" data-hsla="'.$hsla.'" data-hsb="'.$hsb.'" data-lab="'.$lab.'" data-xyz="'.$xyz.'"  style="background-color:'.$color_name.';"><span class="color-name">' . $color_pms . '</span><span class="tooltiptext '.$dark_color_class.'"></span></li>';
			}
			echo '</ul>';
		}
		?>
	</div>
	<?php
	$the_query = new WP_Query( array(
		'post_type' => 'colors',
		'posts_per_page' => -1,
		'ignore_sticky_posts' => 1,
		'orderby' => 'rand',
		'post__not_in'=>array($post->ID)
	));

	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$user_colors = get_user_meta( $user_id, 'favourite_colors_ids', true ); 
	$user_colors = explode(',', $user_colors);
	$color_palettes_url = '';

	if ( $the_query->have_posts() ) { 
		?>
		<!-- similar-platters -->
		<div class="wc-single-palette-card">
			<h2>Similar palettes</h2>
			<div class="wc-single-card-grid">
				<?php 
				while ( $the_query->have_posts() ) { $the_query->the_post(); 
					$post_id = get_the_ID();
					$color_pattel = get_post_meta( $post_id, 'custom_repeater_item', true ); 
					$color_palettes_url = get_post_meta( $post_id, 'color_palettes_url', true ); 

					if( !empty( $user_colors ) &&  $user_colors ){
						if( in_array( $post_id, $user_colors ) ){
							$class = 'active';    
						}else{
							$class = '';
						}
					}
					?>
					<div class="wc-color-palette palette-card-colors <?php echo $class; ?>" colors_id="<?php echo $post_id; ?>">
						<?php
						if( $color_pattel ){
							echo '<ul class="wc-colors">';
							foreach( $color_pattel as $color ){
								$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : '';
								$color_pms = isset( $color['color_pms'] ) ? $color['color_pms'] : '';
								$dark_color_class = ( $color['dark_color_palettes'] ) ? 'has-dark-color' : '';
								

								$rgb = Hex::fromString( $color_name );

								$hex2 = Hex::fromString('#2d78c8');

								$rgb = $rgb->toRgb();
								$new_rgb = str_replace( 'rgb', '', $rgb );

								$rgba = $rgb->toRgba(); 
								$rgba->alpha(); 
								$new_rgba = str_replace( 'rgba', '', $rgba );

								$hex = $rgb->toHex(); 
								$rgba->alpha(); 

								$cmyk = $rgb->toCmyk(); 
								$cmyk = str_replace( 'cmyk', '', $cmyk );

								$hsl = $rgb->toHsl(); 
								$hsl = str_replace( 'hsl', '', $hsl );

								$hsla = $rgb->toHsla();
								$hsla = str_replace( 'hsla', '', $hsla );

								$hsb = $rgb->toHsb(); 
								$hsb = str_replace( 'hsb', '', $hsb );

								$lab = $rgb->toCIELab();
								$lab = str_replace( 'CIELab', '', $lab );

								$xyz = $rgb->toXyz();
								$xyz = str_replace( 'xyz', '', $xyz );


								echo '<li class="single-card-colors wc-sinle-color '.$dark_color_class.'" data-url="'.$color_palettes_url.'" data-rgb="'.$new_rgb.'" data-rgba="'.$new_rgba.'" data-hex="'.$hex.'" data-cmyk="'.$cmyk.'" data-hsl="'.$hsl.'" data-hsla="'.$hsla.'" data-hsb="'.$hsb.'" data-lab="'.$lab.'" data-xyz="'.$xyz.'"  style="background-color:'.$color_name.';"><span class="color-name">' . $color_pms . '</span><span class="tooltiptext '.$dark_color_class.'"></span></li>';
							}
							echo '</ul>';

							?>

							<div id="test-popup-<?php echo $post_id; ?>" class="white-popup mfp-hide">
								<ul class="palette_colors-fullscreen">
									<?php foreach( $color_pattel as $color ){ 
										$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : ''; ?>
										<li class="light" style="background:<?php echo $color_name ?>"></li>
									<?php } ?>
								</ul>
							</div>
							<?php
						}
						?>
						<div class="palette-card-btns ">
							<a class="palette-like-btn" href="#"><i class="fa fa-heart fill"></i><i class="fa fa-spinner fa-spin"></i></a>
							<div class="menu-nav">
								<div class="dropdown-container" tabindex="-1">
									<div class="three-dots"></div>
									<div class="dropdown">
										<ul>
											<li><a href="<?php echo get_the_permalink(); ?>" target="_blank" class="link-name"><i class="fa fa-up-right-from-square"></i>Open palette</a></li>
											<li><a href="#" class="link-name copy-url" copy_url="<?php echo $color_palettes_url; ?>"><i class="fa fa-clone"></i>Copy URL<span class="copy_span"></span></a></li>
											<li><a href="#color-frame" class="color-popup link-name close-dropdown"><i class="fa fa-eye"></i>Quick view</a></li>
											<li><a href="#test-popup-<?php echo $post_id; ?>" class="link-name open-popup-link"><i class="fa fa-up-right-and-down-left-from-center"></i>View fullscreen</a></li>
											<li class="save_palette <?php echo $class; ?>"><a href="#" class="link-name"><i class="fa fa-heart"></i><i class="fa fa-spinner fa-spin"></i>Save palette</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

					<?php
				} wp_reset_postdata();
				?>
			</div>
		</div>
	<?php } get_footer(); 