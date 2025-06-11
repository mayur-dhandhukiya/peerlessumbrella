

function pps_image() { 

	var image = wp.media({ 
		title: 'Upload Image',
		// mutiple: true if you want to upload multiple files at once
		multiple: false
	}).open()
	.on('select', function(){
		// This will return the selected image from the Media Uploader, the result is an object
		var uploaded_image = image.state().get('selection').first();
		// We convert uploaded_image to a JSON object to make accessing it easier
		// Output to the console uploaded_image
		//console.log(uploaded_image);
		var image_url = uploaded_image.toJSON().url;
		// Let's assign the url value to the input field
		jQuery('#pps_image_url').val(image_url);
		jQuery('#imgdisplay').html("<img width='200' src='"+image_url+"'/>");
	});

	

}

function showCoImg(value) {

	if(value == 'colour') {
		jQuery('.pps_back_colour').show();
		jQuery('.pps_back_image').hide();
	} else if(value == 'image') {
		jQuery('.pps_back_image').show();
		jQuery('.pps_back_colour').hide();
	} else {
		jQuery('.pps_back_colour').hide();
		jQuery('.pps_back_image').hide();
	}

}

jQuery(document).ready(function($) {
	var selected_val = $('#pps_background').val();
	if(selected_val == 'colour') {
		jQuery('.pps_back_colour').show();
		jQuery('.pps_back_image').hide();
	} else if(selected_val == 'image') {
		jQuery('.pps_back_image').show();
		jQuery('.pps_back_colour').hide();
	} else {
		jQuery('.pps_back_colour').hide();
		jQuery('.pps_back_image').hide();
	}
	
});

jQuery(document).ready(function() {
    jQuery('.js-example-basic-multiple-product').select2();
});

jQuery(function($) {
  $(".child").on("click",function() {
      $parent = $(this).prevAll(".parent");
      if ($(this).is(":checked")) $parent.prop("checked",true);
      else {
         var len = $(this).parent().find(".child:checked").length;
         $parent.prop("checked",len>0);
      }    
  });
  $(".parent").on("click",function() {
      $(this).parent().find(".child").prop("checked",this.checked);
  });
});

function getMode(value) {

	if(value == 'selected_items') {
		jQuery('.pps_privatize_products').show();
	} else {
		jQuery('.pps_privatize_products').hide();
	}

}

jQuery(document).ready(function($) {
	var checked_val = $('input[name=pps_privatize]:checked').val();
	if(checked_val == 'selected_items') {
		jQuery('.pps_privatize_products').show();
	} else {
		jQuery('.pps_privatize_products').hide();
	}
});

















