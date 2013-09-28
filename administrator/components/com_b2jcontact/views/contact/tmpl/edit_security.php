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
<div class="tab-pane" id="security">
	<?php
	$fieldSets = $this->form->getFieldsets('params');
	$i = 0;
	?>
	<div class="b2jContactFields">
	<?php foreach ($this->form->getFieldset("security") as $field) : ?>
			<?php if( !$field->label ){?>
				<div class="control-group" style="display:none">
			<?php }
			else{?>
				<div class="control-group">
			<?php }?>
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
	<?php endforeach; ?>
	</div>
</div>