jQuery(document).ready(function ()
{
	if (!jQuery('div#status').length) return;


	var options = jQuery("#menuOptions");
	
	jQuery("#details").append(options);

	
	jQuery('a[href="\\#options"]').parent().remove();

	for (var f = 0; f < 2; ++f)
	{
		jQuery(options[0].children[0].children[1].children[0].children[0]).remove();
	}
});
