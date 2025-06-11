<?php

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    /**
	 * It's the main class that does all the things.
	 *
	 * @class Cspgv_Product_Gallery_Video_Admin
	 * @version 1.0.0
	 * @since 1.0.0
	 */

    class Cspgv_Product_Gallery_Video_Admin{

        function __construct(){
            add_action('admin_enqueue_scripts', array($this,'cspgv_load_scripts'));

            
            add_filter( 'woocommerce_settings_tabs_array', array($this, 'cspgv_settings_tab'), 50 );

            add_action( 'woocommerce_settings_tabs_cspgv_settings', array($this, 'cspgv_settings'),10);

            add_action( 'woocommerce_update_options_cspgv_settings', array($this,'cspgv_update_settings') );

            add_filter( 'attachment_fields_to_edit', array($this,'cspgv_attachment_fields'), 20, 2);

            add_action( 'edit_attachment', array($this,'cspgv_save_attachament_video'),10,1);
        }

        function cspgv_settings_tab($settings_tabs){
            $settings_tabs['cspgv_settings'] = esc_attr__('Product Gallery Video',CSPGV_TEXT_DOMAIN);
            return $settings_tabs;
        }

        function cspgv_settings(){
            global $current_section;
            woocommerce_admin_fields($this->get_settings());
        }

        function cspgv_update_settings() {
            woocommerce_update_options( $this->get_settings() );
        }

        function get_settings(){
            $settings= array(
                'gallery_video_section' => array(
                    'name'     => esc_attr__('Product Gallery Video', CSPGV_TEXT_DOMAIN),
                    'type'     => 'title',
                    'desc_tip'     => '',
                    'id'       => 'cspgv_gallery_video'
                ),
                    
                'video_on_shop' =>array(
                    'title'    => esc_attr__('Video on Shop',CSPGV_TEXT_DOMAIN),
                    'desc'     => esc_attr__('Enable video on product image on shop page.', CSPGV_TEXT_DOMAIN),
                    'type'     => 'checkbox',
                    'id'       => 'cspgv_video_on_shop',
                    'desc_tip' => true
                ),

                'video_on_single_product' =>array(
                    'title'    => esc_attr__('Video on Single Product Page',CSPGV_TEXT_DOMAIN),
                    'desc'     => esc_attr__('Enable video on Single product page.', CSPGV_TEXT_DOMAIN),
                    'type'     => 'checkbox',
                    'id'       => 'cspgv_video_on_single_product',
                    'desc_tip' => true
                ),

                'auto_play' =>array(
                    'title'    => esc_attr__('Auto Play',CSPGV_TEXT_DOMAIN),
                    'desc'     => esc_attr__('Enable auto play for video.', CSPGV_TEXT_DOMAIN),
                    'type'     => 'checkbox',
                    'id'       => 'cspgv_auto_play',
                    'desc_tip' => true
                ),

                'video_width' => array(
                    'title'     => esc_attr__('Video Width',CSPGV_TEXT_DOMAIN),
                    'desc'=>esc_attr__('Set video Width. eg 500', CSPGV_TEXT_DOMAIN),
                    'id'       => 'cspgv_video_width',
                    'type'     => 'text',
                    'desc_tip'     => true,
                ),

                'video_height' => array(
                    'title'     => esc_attr__('Video Height',CSPGV_TEXT_DOMAIN),
                    'desc'=>esc_attr__('Set Video Height. eg 300', CSPGV_TEXT_DOMAIN),
                    'id'       => 'cspgv_video_height',
                    'type'     => 'text',
                    'desc_tip'     => true,
                ),
                                    
                'gallery_video_section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'cspgv_gallery_video_section_end'
                ),
            );
            return  $settings;
        }

        function cspgv_load_scripts(){
            wp_enqueue_media();
            wp_register_script('cspgv-admin-js', CSPGV_URL.'admin/assets/js/cspgv-admin-js.js',array('jquery'),CSPGV_VERSION);
            wp_localize_script( 'cspgv-admin-js', 'upload_video',
                array(
                    'title' => esc_attr__( 'Upload a Video', CSPGV_TEXT_DOMAIN),
                    'button' => esc_attr__( 'Use this video', CSPGV_TEXT_DOMAIN),
                )
            );
            wp_enqueue_script( 'cspgv-admin-js' );
        }

        function cspgv_attachment_fields( $form_fields, $attachment ) { 
   
            $nonce = wp_create_nonce( 'bdn-attach_' . $attachment->ID );
            $media_type=$attachment->post_mime_type;
            if($media_type=='video/mp4'){
                return $form_fields;
            }

            $field_value = get_post_meta( $attachment->ID, 'cspgv_videolink', true );
            
            $video_type = get_post_meta( $attachment->ID, 'cspgv_video_type', true );
            $uploaded = ($video_type == 'uploaded_video') ? 'selected' : '';
            $youtube = ($video_type == 'youtube') ? 'selected' : '';
            $vimeo = ($video_type == 'vimeo') ? 'selected' : '';
            $daily = ($video_type == 'daily-motion') ? 'selected' : '';
            $display='none';
            if(!empty($uploaded)){
                $display='block;';
            }
           
            $form_fields['cspgv_video_type'] = array(
                'input' => 'html',
                'value' => $video_type,
                'html' => "<select name='attachments[{$attachment->ID}][cspgv_video_type]' class='cspgb-video-site' >
                    <option value='uploaded_video' {$uploaded}>Uploaded</option>
                    <option value='youtube' {$youtube}>Youtube</option>
                    <option value='vimeo' {$vimeo}>Vimeo</option>
                    <option value='daily-motion' {$daily}>Daily Motion</option>
                </select>",
                'label'=>esc_attr__('Video Site',CSPGV_TEXT_DOMAIN)
            );
            $form_fields['cspgv_videolink'] = array(
                'value' => $field_value ? $field_value : '',
                'input' => "text",
                'label' => esc_attr__( 'Video Url' ,CSPGV_TEXT_DOMAIN) ,
                'class'=>'cspgv-videolink'
            );

            $form_fields['cspgv_upload_video']=array(
                'input' => 'html',
                'html' => "<input type='button' name='attachments[{$attachment->ID}][cspgv_upload_video]' value='Upload Video' class='button cspgv-upload-video' style=display:{$display}; data-id='{$attachment->ID}'>",
                'label'=>''
            );
            return $form_fields;
        }

        function cspgv_save_attachament_video($attachment_id){
            if ( isset( $_REQUEST['attachments'][$attachment_id]['cspgv_videolink'] ) ) {
                $video_link = $_REQUEST['attachments'][$attachment_id]['cspgv_videolink'];
                update_post_meta( $attachment_id, 'cspgv_videolink', $video_link );
            }
            if ( isset( $_REQUEST['attachments'][$attachment_id]['cspgv_video_type'] ) ) {
                $video_site = $_REQUEST['attachments'][$attachment_id]['cspgv_video_type'];
                update_post_meta( $attachment_id, 'cspgv_video_type', $video_site );
            }
        }
    }