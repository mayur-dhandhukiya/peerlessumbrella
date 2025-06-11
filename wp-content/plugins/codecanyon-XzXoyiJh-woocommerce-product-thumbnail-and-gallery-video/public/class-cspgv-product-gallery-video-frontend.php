<?php

      if ( ! defined( 'ABSPATH' ) ) {
            exit; // Exit if accessed directly
      }

      /**
	 * It's the main class that does all the things.
	 *
	 * @class Cspgv_Product_Gallery_Video_Frontend
	 * @version 1.0.0
	 * @since 1.0.0
	 */
      class Cspgv_Product_Gallery_Video_Frontend{
            
            function __construct(){
                  $video_on_shop=get_option('cspgv_video_on_shop')=='yes'?1:0;
                  $video_single_product=get_option('cspgv_video_on_single_product')=='yes'?1:0;

                  add_action('wp_enqueue_scripts',array($this,'cspgv_load_scripts'));
                  if(!empty($video_single_product)){
                        add_action('template_redirect', array($this,'cspgv_remove_gallery_thumbnail_images'));
                        add_action( 'woocommerce_product_thumbnails', array($this,'cspgv_display_video_gallery'), 20 );
                  }
                  if(!empty($video_on_shop)){
                        add_filter('woocommerce_product_get_image', array($this,'cspgv_video_on_shop'),10,6);
                  }
            }

            function cspgv_load_scripts(){
                  wp_enqueue_style('cspgv-frontend-css',CSPGV_URL.'public/assets/css/cspgv-frontend-css.css',array(),CSPGV_VERSION);
                  wp_enqueue_script('cspgv-frontend-js',CSPGV_URL.'public/assets/js/cspgv-frontend-js.js',array('jquery'),CSPGV_VERSION,true);
            }

            function cspgv_remove_gallery_thumbnail_images(){
                  if ( is_product() ) {
                        remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
                  }
            }

            function cspgv_display_video_gallery() {
              
                  global $wpdb;
                  $post_id_arr = $wpdb->get_results("SELECT post_id,meta_value FROM $wpdb->postmeta WHERE meta_key = 'cspgv_videolink' " );
                  foreach ($post_id_arr as $key => $value) {
                        $new_post_id_arr[$value->meta_value] = $value->post_id;
                  }
            
                  $product_thum_id = get_post_meta(get_the_ID(),'_thumbnail_id',true); 
                  $video_html=$this->get_video_html($product_thum_id);
                  if(!empty( $video_html)){
                        $script='jQuery(document).ready(function($) {
                              jQuery(".woocommerce-product-gallery__wrapper").find("div a").first().html("'.$video_html.'");
                        });';
                        wp_add_inline_script( 'cspgv-frontend-js', $script );
                  }
                  
                  global $woocommerce,$product;
                  
                  $attachment_ids = $product->get_gallery_image_ids();
                  if ( !empty($attachment_ids )) {
                        $newhtml = "";
                        foreach ( $attachment_ids as $attachment_id ) {
                              $gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
                              $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
                              $video_html=$this->get_video_html($attachment_id);

                              $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
                              $alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
                             
                              if(!empty($video_html)){
                                    $newhtml .= '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="woocommerce-product-gallery__image" >';
                                    $newhtml .= $video_html; 
                                    $newhtml .= '</div>'; 
                              }else{     
                                    $newhtml .=  apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id );
                              }
                              
                        }                        
                        echo $newhtml;     
                  }
            }

            function cspgv_video_on_shop($image, $product, $size, $attr, $placeholder, $image1){
                  global $wpdb;
                  $product_thum_id = get_post_meta($product->get_id(),'_thumbnail_id',true);
                  $video_html=$this->get_video_html($product_thum_id);
                  if(!empty($video_html)){
                        $image = $video_html;
                  }
                  return $image;
            }
            

            function get_video_html($thumbnail_id){
                  $video_link = get_post_meta($thumbnail_id,'cspgv_videolink',true);
                  $video_type = get_post_meta($thumbnail_id,'cspgv_video_type',true);
                  $video_html='';
                  if(!empty($video_type) && !empty($video_link)){
                        $auto_play=get_option('cspgv_auto_play')=='yes'?true:false;
                        $width=!empty(get_option('cspgv_video_width'))?get_option('cspgv_video_width'):'560';
                        $height=!empty(get_option('cspgv_video_height'))?get_option('cspgv_video_height'):'315';
                        switch($video_type){
                              case 'youtube':
                                    $video=explode('=',$video_link);
                                    $video_id=end($video);
                                    $url         = "https://www.youtube.com/embed/" . $video_id .'?autoplay='.$auto_play;
                                    $video_html="<iframe width='$width' height='$height' src='".esc_url($url)."' frameborder='0' allow='accelerometer;";
                                    if($auto_play){
                                          $video_html.=" autoplay; "; 
                                    }
                                    $video_html.=" encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
                              break;
                              case 'vimeo':
                                    $video=explode('/',$video_link);
                                    $video_id=end($video);
                                    $url = 'https://player.vimeo.com/video/'.$video_id.'?autoplay='.$auto_play;
                                    $video_html="<iframe width='$width' height='$height' src='".esc_url($url)."' frameborder='0'";
                                    if($auto_play){
                                          $video_html.=" allow='autoplay' ";
                                    }
                                    $video_html.=" 'encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
                              break;
                              case 'daily-motion':
                                    $video=explode('/',$video_link);
                                    $video_id=end($video);
                                    $url = 'https://www.dailymotion.com/embed/video/'.$video_id.'?autoplay='.$auto_play;
                                    $video_html="<iframe frameborder='0' width='$width' height='$height' src='".esc_url($url)."' allow='autoplay'></iframe>";
                              break;
                              case 'uploaded_video':
                                    $thumbnail_src     = wp_get_attachment_image_src( $thumbnail_id,'shop_single');
                                    $url = $video_link;
                                    $autoplay=!empty($auto_play)?1:0;
                                    $video_html="<iframe class='thumb-video' frameborder='0' width='$width' height='$height' src='".esc_url(CSPGV_URL.'public/views/play-video.php?video_link='.$video_link.'&autoplay='.$autoplay)."' allow='$autoplay' allow='loop'></iframe>";
                              break;
                        }
                  }
                  return $video_html;
            }
      }