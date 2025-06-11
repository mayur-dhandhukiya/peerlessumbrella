jQuery(document).ready(function(){
    jQuery("#allperm").click(function(){
        jQuery(".rest").slideToggle();
        if(jQuery(this).text() == 'Show all'){
        	jQuery(this).text('Hide');
        } else {
        	jQuery(this).text('Show all');
        }
    });
});
		
		