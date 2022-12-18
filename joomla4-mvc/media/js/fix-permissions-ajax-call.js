// The following is to enable setting the permission's Calculated Setting 
// when you change the permission's Setting. 
// The core javascript code for initiating the Ajax request looks for a field
// with id="jform_title" and sets its value as the 'title' parameter to send in the Ajax request
jQuery(document).ready(function() {
        greeting = jQuery("#jform_greeting").val();
		jQuery("#jform_title").val(greeting);
	});