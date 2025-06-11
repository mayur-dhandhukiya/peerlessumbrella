jQuery(document).ready(function($){
	"use strict";
	
	$(document).on('click','.cspgb-video-site', function(){
		$('.cspgv-upload-video').hide();
		if($(this).val()=='uploaded_video'){
			$('.cspgv-upload-video').show();
		}else{
			$('.cspgv-upload-video').hide();
		}
	});

	var meta_image_frame;
	$(document).on('click','.cspgv-upload-video', function(e){

		var attach_id=$(this).data('id');

		// If the frame already exists, re-open it.
		if ( meta_image_frame ) {
			meta_image_frame.open();
			return;
		}

		// Sets up the media library frame
		meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			title: upload_video.title,
			button: { text:  upload_video.button },
			library: { type: 'video' }
		});

		// Runs when an image is selected.
		meta_image_frame.on('select', function(){

			// Grabs the attachment selection and creates a JSON representation of the model.
			var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

			// Sends the attachment URL to our custom image input field.
			$('#attachments-'+attach_id+'-cspgv_videolink').val(media_attachment.url);
			$('#attachments-'+attach_id+'-cspgv_videolink').trigger("change");
		});

		// Opens the media library frame.
		meta_image_frame.open();
	});
});