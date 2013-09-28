<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="tab-pane" id="dynamicfields" style="position:relative;">
	<div class="add_field_con">
		<a href='javascript:void(0)' class="add_field_btn" onclick='showAddField(0);'><?php echo JText::_('COM_B2JCONTACT_DYNAMIC_ADD_FIELD'); ?></a>
	</div>

	<div style="clear:both;"></div>
	<?php
	$fieldSets = $this->form->getFieldsets('params');
	$i = 0;
	?>
	<div class="b2jContactFields">
		<?php foreach ($this->form->getFieldset("formfields") as $field) : ?>
				<?php echo $field->input; ?>
		<?php endforeach; ?>
		<div id="b2jNewFields"></div>	
	</div>
	<div id='b2jTypeCon'></div>
	<div id='b2jFieldsCon'></div>
</div>