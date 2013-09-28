jQuery(document).ready(function()
{
	var configTabs = jQuery('ul#configTabs');
	
	configTabs.children('li:first').remove()
	
	jQuery('div#JACTION_ADMIN').remove();
	
	configTabs.children('li:first').addClass('active');
	
	jQuery('div#adminemail').addClass('active');
});