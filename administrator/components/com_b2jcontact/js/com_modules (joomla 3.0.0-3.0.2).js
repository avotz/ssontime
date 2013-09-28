var JText = [];
JText['COM_B2JCONTACT_FIELDS_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_FIELDS_LBL"); ?>';
JText['COM_B2JCONTACT_EVENTS_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_EVENTS_LBL"); ?>';
JText['COM_B2JCONTACT_SECURITY_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_SECURITY_LBL"); ?>';
JText['COM_B2JCONTACT_NEWSLETTER_INTEGRATION_LBL'] = '<?php echo JText::_("COM_B2JCONTACT_NEWSLETTER_INTEGRATION_LBL"); ?>';

jQuery(document).ready(function ()
{
	if (!jQuery('div#status').length) return;

	var menuOptions;
	jQuery("#details").append(
		menuOptions = jQuery("<div/>", {id:'menuOptions', class:'accordion'})
	);

	var tabs = jQuery('ul[class="nav nav-tabs"]')[0];

	var sections = jQuery('div.tab-pane');

	sections.each(
		function (index)
		{
			if (index == 0 || index == sections.length - 1) return;

			var title =
				JText[jQuery(tabs.children[index]).text()] ||
					jQuery(tabs.children[index]).text();

			menuOptions.append(
				jQuery("<div/>", {class:'accordion-group'}).append(

					jQuery("<div/>", {class:'accordion-heading'}).append(
						jQuery("<strong/>").append(
							jQuery("<a/>", {'href':'#collapse' + index, 'data-parent':'#menuOptions', 'data-toggle':'collapse', 'class':'accordion-toggle collapsed', 'text':title})
						)
					),

					jQuery("<div/>", {'class':'accordion-body collapse', 'id':'collapse' + index, 'style':'height: 0px;'}).append(
						jQuery("<div/>", {class:'accordion-inner'}).append(
							this
						)
					)

				)
			);

			jQuery(tabs.children[index]).addClass("remove-me");

			if (index == 1)
			{
				for (var f = 0; f < 2; ++f)
				{
					jQuery(this.children[0]).remove();
				}
			}
		});

	jQuery('li.remove-me').each(
		function (index)
		{
			jQuery(this).remove();
		});
});
