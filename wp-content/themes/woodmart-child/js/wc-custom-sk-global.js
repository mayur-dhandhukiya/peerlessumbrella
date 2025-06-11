
jQuery('.video-container').removeClass('active');
jQuery(document).on('click', '.custom-banner .image_field', function() {
    
    jQuery(this).hide();
    jQuery(this).parents('.custom-banner').find('.video-container').addClass('active');
});