<?php
get_header();

use Spatie\Color\Exceptions\InvalidColorValue;
use Spatie\Color\Rgb;
use Spatie\Color\Distance;
use Spatie\Color\Contrast;
use Spatie\Color\Hex;
require_once get_stylesheet_directory() .'/Color_Palatte/vendor/autoload.php';


$current_user_id = get_current_user_id();
$term = get_queried_object();
?>
<!-- start-side-bar-content -->
<div class="side-bar-content">
	<div class="side-bar-text-content">
		<div class="Explore-tabs">
			<a href="#" class="Explore">Favorites</a>
			<div class="side-bar-close">
				<a href="javascript:;" class="close-menu"><i class="fa fa-xmark"></i></a>
			</div>
		</div>
		<div class="palettes-sidebar_palettes wc-all-color-pattels">
			<?php
			$favourite_colors_ids = get_user_meta( $current_user_id, 'favourite_colors_ids', true);
			if( $favourite_colors_ids ){
				$favourite_colors_ids = explode(',',$favourite_colors_ids);
				if( $favourite_colors_ids ){
					foreach( $favourite_colors_ids as $favourite_colors_id ){ ?>
						<?php $color_pattel = get_post_meta( $favourite_colors_id, 'custom_repeater_item', true ); 
						if( $color_pattel ){ ?>
							<div class="palette">
								<a href="<?php echo get_the_permalink( $favourite_colors_id  ); ?>"><span class="fav-pallete-name"><?php echo get_the_title( $favourite_colors_id ); ?></span></a>
								<ul class="palette_colors">
									<?php foreach( $color_pattel as $color ){ 
										$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : ''; 
										$color_pms = isset( $color['color_pms'] ) ? $color['color_pms'] : ''; 
										$palettes_url = isset( $color['palettes_url'] ) ? $color['palettes_url'] : '';
										$dark_color_class = ( $color['dark_color_palettes'] ) ? 'has-dark-color' : '';
										$target_blank = ( $palettes_url ) ? 'target=_blank' : '';
										$href = 'javascript:;';
										if( !empty( $palettes_url  ) ){
											$href = $palettes_url; 
										}
										?>
										<li class="is-light <?php echo $dark_color_class; ?>" style="background: <?php echo $color_name; ?>"><a href="<?php echo $href; ?>" target="_blank"><span class="color-name"><?php echo $color_pms; ?></span></a></li>
									<?php } ?>
								</ul>
								<div class="palette_text">49K saves</div>
							</div>
							<?php 
						} 
					}
				}
			} else { ?>
				<div class="palette">
					<p>No color palettes found</p>
				</div>
			<?php }
			?>
		</div>
	</div>
</div>
<!-- close-side-bar-content -->
<!-- start-header -->
<div class="wc-search-platters">
	<header class="header">
		<div class="header-contant">
			<div class="search-palettes-container">
				<i class="fa fa-magnifying-glass"></i>
				<a class="tag" id="<?php echo $term->slug; ?>" href="<?php echo site_url('/color-reference-2'); ?>"><i class="fa fa-circle" style="color:<?php echo $term->slug; ?>;"></i><?php echo $term->name; ?><i class="fa fa-close"></i></a>
				<input type="text" id="palettes-search-input" placeholder="Search with colors, topics, styles or hex values...">
			</div>
			<div class="menu-wrapper">
				<ul class="manu-bar">
					<li class="mobile-menu-icon">
						<a href="javascript:;" class="open-menu"><i class="fa fa-bars"></i></a>
						<div class="side-bar-close">
							<a href="javascript:;" class="close-menu"><i class="fa fa-xmark"></i></a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</header>
	<div class="palette-card-colors wc_colors_search">
		<h2>Colors</h2>
		<?php
		$allterms = get_terms([
			'taxonomy' => 'color-category',
			'hide_empty' => false,
		]);
		if(!empty($allterms)){ ?>
			<div class="megasearch_menu_color">
				<?php foreach ($allterms as $allterm){ ?>
					<a class="tag" id="<?php echo $allterm->slug; ?>" href="<?php echo get_term_link( $allterm->slug, 'color-category' ); ?>"><i class="fa fa-circle" style="color:<?php echo $allterm->slug; ?>;"></i><?php echo $allterm->name; ?></a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>
<!-- close-header -->


<div class="wc-color-palette-wrap">

	<?php
	$args = array(
		'post_type'   => 'colors',
		'post_status' => 'publish',
		'posts_per_page'=> -1,
		'order'     => 'ASC',
		'tax_query' => array(
			array(
				'taxonomy' => 'color-category',
				'field'    => 'slug',
				'terms'    => $term->slug,
			),
		),
	);

	$the_query = new WP_Query( $args );

	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$user_colors = get_user_meta( $user_id, 'favourite_colors_ids', true ); 
	$user_colors = explode(',', $user_colors);
	$color_palettes_url = '';

	if ( $the_query->have_posts() ) { 
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
			<div class="wc-color-palette <?php echo $class; ?>" colors_id="<?php echo $post_id; ?>">
				<!-- <h2 class="pallete-name"><a href="<?php //echo get_the_permalink();?>"><?php //echo get_the_title(); ?></a></h2> -->
				<h2 class="pallete-name"><?php echo get_the_title(); ?></h2>
				<?php
				if( $color_pattel ){
					echo '<ul class="wc-colors">';
					foreach( $color_pattel as $color ){
						$color_name = isset( $color['color_palettes'] ) ? $color['color_palettes'] : '';
						$color_pms = isset( $color['color_pms'] ) ? $color['color_pms'] : '';
						$palettes_url = isset( $color['palettes_url'] ) ? $color['palettes_url'] : '';
						$target_blank = ( $palettes_url ) ? 'target=_blank' : '';
						$href = 'javascript:;';

						if( !empty( $palettes_url  ) ){
							$href = $palettes_url; 
						}

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


						//echo '<li class="wc-sinle-color '.$dark_color_class.'" data-rgb="'.$new_rgb.'" data-rgba="'.$new_rgba.'" data-hex="'.$hex.'" data-cmyk="'.$cmyk.'" data-hsl="'.$hsl.'" data-hsla="'.$hsla.'" data-hsb="'.$hsb.'" data-lab="'.$lab.'" data-xyz="'.$xyz.'"  style="background-color:'.$color_name.';"><span class="color-name">' . $color_pms . '</span><span class="tooltiptext '.$dark_color_class.'"></span></li>';

						echo '<li class="wc-sinle-color '.$dark_color_class.'" data-palettes-url="'.$palettes_url.'" data-pms="'.$color_pms.'" data-url="'.$color_palettes_url.'" data-rgb="'.$new_rgb.'" data-rgba="'.$new_rgba.'" data-hex="'.$hex.'" data-cmyk="'.$cmyk.'" data-hsl="'.$hsl.'" data-hsla="'.$hsla.'" data-hsb="'.$hsb.'" data-lab="'.$lab.'" data-xyz="'.$xyz.'"  style="background-color:'.$color_name.';"><a href="'.$palettes_url.'" target="_blank"><span class="color-name">' . $color_pms . '</span><span class="tooltiptext '.$dark_color_class.'"></span></a></li>';
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
				<div class="palette-card-btns">
					<a class="palette-like-btn" href="#"><i class="fa fa-heart fill"></i> <i class="fa fa-spinner fa-spin"></i></a>
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
	} else { ?>
		<div class="palette">
			<p>No color palettes found</p>
		</div>
	<?php } 
	?>
</div>

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
		<!-- 	<li class="code code-lab" style="background-color:#ced4da;">
				<span class="code-type">LAB</span>
				<span class="color-code">0, 4, 5, 3</span>
			</li> -->
			<li class="code code-rgb" style="background-color:#ced4da;">
				<span class="code-type">RGB</span>
				<span class="color-code">248, 237, 235</span>
			</li>
			<!-- <li class="code code-rgba" style="background-color:#ced4da;">
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
	
	<p><a class="popup-modal-dismiss" href="#"><i class="fa-solid fa-xmark"></i></a></p>
</div>
<?php get_footer(); ?>