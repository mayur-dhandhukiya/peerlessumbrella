<?php /* Template Name: Zoom Api Template */ 
get_header();
?>

<style type="text/css">
	.cxc-prsentation-wrap .frontpage-title { font-size: 22px; text-align: center; line-height: 1.333em; font-weight: bolder; padding: 15px; text-transform: uppercase; color: #8B8D8D; }
	.cxc-prsentation-wrap .frontpage-subtitle { font-size: 17px; text-align: center; line-height: 1.333em; font-weight: normal; padding: 0 20px 15px; color: #8B8D8D; }
	.cxc-prsentation-wrap .cxc-zoom-data { width: 48%; border: 1px solid #8B8D8D; box-shadow: 0 0px 0px 0px #ccc; -moz-box-shadow: 0 0px 0px 0px #ccc; -webkit-box-shadow: 0 0px 0px 0px #ccc; float: left; background: white; padding: 20px; margin: 10px; position: relative; }
	.cxc-prsentation-wrap .cxc-zoom-data > a { color: #337ab7; text-decoration: none; text-align: center; line-height: 0px; height: 215px; width: 165px; margin: 0px 22px 0px 0px; border: 0px; border: 1px solid #8B8D8D; border-top: 3px solid #8B8D8D; border-bottom: 3px solid #8B8D8D; float: left; position: relative; }
	.cxc-prsentation-wrap .cxc-zoom-data > a .preview-icon{ background: url(../wp-content/themes/woodmart-child/images/fx.png) no-repeat; bottom: 0; height: 46px; position: absolute; right: 0; width: 76px; z-index: 5; display: none; text-align: center; line-height: 296px; }
	.cxc-prsentation-wrap .cxc-zoom-data > a:hover .preview-icon{ display: block; }
	.cxc-prsentation-wrap .cxc-zoom-data img{ width: 100%; }
	.cxc-prsentation-wrap .cxc-zoom-data .name { font-family: Helvetica, Tahoma, Arial, sans-serif; font-weight: bolder; font-size: 15px; color: #8B8D8D; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
	.cxc-prsentation-wrap .cxc-zoom-data .exp_date { color: #8B8D8D; margin-bottom: 12px; }
	.cxc-prsentation-wrap .description { font-family: MyriadPro-Regular, Helvetica, Tahoma, Arial, sans-serif !important; font-size: 14px; color: #666; }
	.cxc-prsentation-wrap .button{ float: right; color: #fff; padding: 8px 14px; height: initial; line-height: initial; font-size: 13px; background: #00A2E1; border: none; text-shadow: none; border-radius: 3px; margin-left: 8px; text-decoration: none; font-weight: 600; }
	.cxc-prsentation-wrap .button .icon{ background-image : url(../wp-content/themes/woodmart-child/images/btnsv2_objects.png); background-repeat: no-repeat; background-position: -9px -39px; display: inline-block; position: absolute; width: 20px; height: 20px; }
	.cxc-prsentation-wrap .button.pdf .icon{ background-position: -9px -7px; }
	.flags .US{ background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; background-position: 0 0; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .CA { background-position: 0 -34px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .US-CA { background-position: 0 -144px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .EU { background-position: 0 -72px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .GBR { background-position: 0 -108px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .AUS { background-position: 0 -182px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .UK { background-position: 0 -109px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .NZL { background-position: 0 -256px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .ZAF { background-position: 0 -220px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .FR { background-position: 0 -294px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .ER { background-position: 0 -331px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .DE { background-position: 0 -368px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.flags .IT { background-position: 0 -405px; background-image : url(../wp-content/themes/woodmart-child/images/flags-cube-small2.png); background-repeat: no-repeat; display: inline-block; width: 39px; height: 39px; float: right; }
	.customization { position: absolute; right: 12px; bottom: 12px; width: 100%; }
	@media(max-width: 991px){ .cxc-prsentation-wrap .cxc-zoom-data{ 	width: 47%; 	min-height: 328px; } .customization { 	position: static; 	right: 0; 	bottom: 12px; 	width: 100%; 	margin-top: 20px; 	float: left; } }
	@media(max-width: 767px){ ul.subcompanies-items{ 	padding: 0; } .cxc-prsentation-wrap .cxc-zoom-data{ 	width: 95%; 	padding: 15px; } .cxc-prsentation-wrap .cxc-zoom-data > a{ 	height: auto; } }
</style>

<div class="wc-zoom-wrap">
	<div class="zoom-inner">
		<h2>Create Your Presentation</h2>
		<div class="wc-presentation" style="display: none;">
			<?php
			$redirect_uri = webby_zoom_get_auto_login_uri_call_back();
			$href = 'javascript:;';

			if( ! empty ( $redirect_uri ) ){
				$href = $redirect_uri;
			}
			?>
			<iframe src="<?php echo $href; ?>" height="800" width="1800" title="Iframe Example"></iframe>
		</div>
		<div class="cxc-prsentation-wrap">
			<?php
			$cxc_catelog = webby_get_zoom_catelog_all_catelogs_call_back();
			
			if( !empty( $cxc_catelog ) && isset( $cxc_catelog ) ){

				foreach( $cxc_catelog->catalogs as $cat_val ) {

					$id = isset( $cat_val->id ) ? $cat_val->id : '';
					$expiration = isset( $cat_val->expiration ) ? $cat_val->expiration : '';
					$title = isset( $cat_val->title ) ? $cat_val->title : '';
					$description = isset( $cat_val->description ) ? $cat_val->description : '';
					$version = isset( $cat_val->version ) ? $cat_val->version : '';
					$url = isset( $cat_val->url ) ? $cat_val->url : '';
					$personalize = isset( $cat_val->personalize ) ? $cat_val->personalize : '';
					$created = isset( $cat_val->created ) ? $cat_val->created : '';
					$weight = isset( $cat_val->weight ) ? $cat_val->weight : '';
					$images = isset( $cat_val->images->medium ) ? $cat_val->images->medium : '';

					?>					
					<div class="cxc-zoom-data">
						<a href="<?php echo $url; ?>" nid="<?php echo $id; ?>">
							<img src="<?php echo $images; ?>" class="">
							<span class="preview-icon"></span>
						</a>
						<div class="info">
							<div class="name" title="<?php echo $title; ?>"><?php echo $title; ?></div>
							<p class="exp_date">Expires: <?php echo date( 'F j, Y', $expiration ); ?></p>
							<div class="slimScrollDiv"><div class="description"><?php echo $description; ?></div></div>
						</div>
						<div class="customization">

							<a href="<?php echo $url; ?>" target="_blank" class="button blue personalize">Customize</a>
							<a href="<?php echo $url; ?>" class="has-icon button blue no-label catalog" target="_blank" original-title="Sample"><span class="icon"></span><span class="label">&nbsp;</span></a>
							<a style="display: none;" href="https://peerlessumbrella.zoomcustom.com/flyers/<?php echo $id; ?>/download/pdf?parent" class="has-icon button blue no-label pdf" target="_blank" original-title="Download PDF"><span class="icon"></span><span class="label">&nbsp;</span>
							</a>

							<div class="flags">
								<a version="<?php echo $version; ?>" href="javascript:;" class="flag <?php echo $version; ?> last"></a>	
							</div>
						</div>
					</div>
					<?php		
				}
			}
			?>
		</div>
	</div>
</div>

<?php get_footer(); ?>