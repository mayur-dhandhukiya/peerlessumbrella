<?php
$logo_url = WOODMART_IMAGES . '/wood-logo-dark.svg';

$protocol = woodmart_http() . '://';

$has_sticky_logo     = ( isset( $params['sticky_image']['url'] ) && ! empty( $params['sticky_image']['url'] ) );
$width_height_needed = isset( $params['width_height'] ) && $params['width_height'];

if ( isset( $params['image']['url'] ) && $params['image']['url'] ) {
	$logo_url = $params['image']['url'];
}

$logo_url     = $protocol . str_replace( array( 'http://', 'https://' ), '', $logo_url );
$width        = isset( $params['width'] ) ? (int) $params['width'] : 150;
$sticky_width = isset( $params['sticky_width'] ) ? (int) $params['sticky_width'] : 150;
$logo_attrs   = array(
	'src'   => $logo_url,
	'alt'   => get_bloginfo( 'name' ),
	'style' => 'max-width: ' . esc_attr( $width ) . 'px;',
);

if ( $width_height_needed ) {
	$logo_attrs['width']  = 370;
	$logo_attrs['height'] = 50;
}

if ( ! woodmart_get_opt( 'disable_wordpress_lazy_loading' ) ) {
	$logo_attrs['loading'] = 'lazy';
}

$logo = '<img ' . implode(' ', array_map( function( $key, $value ) {
		return $key . '="' . esc_attr( $value ) . '"';
}, array_keys( $logo_attrs ), $logo_attrs ) ) . ' />';

if ( isset( $params['image']['id'] ) && $params['image']['id'] && $width_height_needed ) {
	woodmart_lazy_loading_deinit( true );
	$logo = wp_get_attachment_image( $params['image']['id'], 'full', false, array( 'style' => 'max-width:' . $width . 'px;' ) );
	woodmart_lazy_loading_init();
}

$logo_classes = ' whb-' . $id;

if ( $has_sticky_logo ) {
	$logo_classes .= ' wd-switch-logo';
}

?>
<div class="site-logo<?php echo esc_attr( $logo_classes ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wd-logo wd-main-logo<?php echo woodmart_get_old_classes( ' woodmart-logo woodmart-main-logo' ); ?>" rel="home" aria-label="<?php esc_html_e( 'Site logo', 'woodmart' ); ?>">
		<?php echo $logo; // phpcs:ignore ?>
	</a>
	<?php if ( $has_sticky_logo ) : ?>
		<?php
		$logo_sticky_url = $protocol . str_replace( array( 'http://', 'https://' ), '', $params['sticky_image']['url'] );

		$logo_sticky = '<img src="' . $logo_sticky_url . '" alt="' . get_bloginfo( 'name' ) . '" style="max-width: ' . esc_attr( $sticky_width ) . 'px;" />';

		if ( isset( $params['sticky_image']['id'] ) && $params['sticky_image']['id'] && $width_height_needed ) {
			woodmart_lazy_loading_deinit( true );
			$logo_sticky = wp_get_attachment_image( $params['sticky_image']['id'], 'full', false, array( 'style' => 'max-width:' . $sticky_width . 'px;' ) );
			woodmart_lazy_loading_init();
		}
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wd-logo wd-sticky-logo" rel="home">
			<?php echo $logo_sticky; // phpcs:ignore ?>
		</a>
	<?php endif ?>
</div>
