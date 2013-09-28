<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('jquery.ui', array('core', 'sortable'));

$app = JFactory::getApplication();
$input = $app->input;
$cn = basename(realpath(dirname(__FILE__) . '/../../..'));
$assoc = isset($app->item_associations) ? $app->item_associations : 0;

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'contact.cancel' || document.formvalidator.isValid(document.id('contact-form')))
		{
			if(task != 'contact.cancel'){
				saveSorting();
			}
			Joomla.submitform(task, document.getElementById('contact-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_b2jcontact&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="contact-form" class="form-validate form-horizontal"> 
	<div class="row-fluid">
		<div class="span10 form-horizontal">
		<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab" style="background-image: url('<?php echo JUri::base(); ?>../media/<?php echo $cn; ?>/images/basic-options-button.png');"><span><?php echo JText::_('COM_B2JCONTACT_BASIC_TAB_LABEL');?></span></a></li>
			<li><a href="#formfields" data-toggle="tab" style="background-image: url('<?php echo JUri::base(); ?>../media/<?php echo $cn; ?>/images/form-fields-button.png');"><span><?php echo JText::_('COM_B2JCONTACT_DEFAULTS_TAB_LABEL');?></span></a></li>
			<li><a href="#dynamicfields" data-toggle="tab" style="background-image: url('<?php echo JUri::base(); ?>../media/<?php echo $cn; ?>/images/dynamic-fields-button.png');"><span><?php echo JText::_('COM_B2JCONTACT_FIELDS_TAB_LABEL');?></span></a></li>
			<li><a href="#events" data-toggle="tab" style="background-image: url('<?php echo JUri::base(); ?>../media/<?php echo $cn; ?>/images/events-button.png');"><span><?php echo JText::_('COM_B2JCONTACT_EVENTS_TAB_LABEL');?></span></a></li>
			<li><a href="#security" data-toggle="tab" style="background-image: url('<?php echo JUri::base(); ?>../media/<?php echo $cn; ?>/images/security-button.png');"><span><?php echo JText::_('COM_B2JCONTACT_SECURITY_TAB_LABEL');?></span></a></li>
		</ul> 
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label">
						<div class="b2j-contact-field-title" style="background:#6c9aab">
							<span style="padding-left:5px; line-height:16px;"><?php echo JText::_('COM_B2JCONTACT_GENERAL_OPTION_LBL');?></span>
						</div>
					</div>
					<div class="controls"></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
				</div>
				<div class="control-group" style="display:none;">
					<div class="control-label"><?php //echo $this->form->getLabel('catid'); ?></div>
					<div class="controls"><?php //echo $this->form->getInput('catid'); ?></div>
				</div>
				<div class="control-group" style="display:none;">
					<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
				</div>
				<div class="control-group" style="display:none;">
					<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
					<?php
					$fieldSets = $this->form->getFieldsets('params');
					$i = 0;
					?>
					<div class="b2jContactFields">
					<?php foreach ($this->form->getFieldset("basic") as $field) : ?>
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
			

			<?php echo $this->loadTemplate('formfields'); ?>
			<?php echo $this->loadTemplate('dynamicfields'); ?>
			<?php echo $this->loadTemplate('events'); ?>
			<?php echo $this->loadTemplate('security'); ?>

			</div>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<!-- End Newsfeed -->
	<!-- Begin Sidebar -->
	<div class="span2">
		<div class="b2j_update_box">
			<?php
			$fieldSets = $this->form->getFieldsets('params');
			?>
			<?php foreach ($this->form->getFieldset("b2jupdate") as $field) : ?>
				<?php echo $field->input; ?>
			<?php endforeach; ?>
		</div>
		<h4><?php echo JText::_('JDETAILS');?></h4>
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('access'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('access'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('language'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('language'); ?>
				</div>
			</div>
		</fieldset>
	</div>
	<!-- End Sidebar -->
</form>
