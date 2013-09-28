jQuery(document).ready(function ()
{

	jQuery('.b2j_select').chosen(
		{
			disable_search_threshold:10,
			allow_single_deselect:true,
			no_results_text:'<?php echo JText::_("COM_B2JCONTACT_NO_RESULTS_MATCH"); ?>'
		});
        
    jQuery('.b2j-contact-group-class input, .b2j-contact-group-class textarea').each(function(){
        jQuery(this).trigger('blur');
    })
});
