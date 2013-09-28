<?php defined('JPATH_PLATFORM') or die;
/* ------------------------------------------------------------------------
 * Bang2Joom Contact for Joomla 3.0+
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2013 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */
jimport("joomla.form.formfield");

JFormHelper::loadFieldClass("list");
class JFormFieldB2jEmailChooser extends JFormFieldList
{
	protected $type = "B2jEmailChooser";

	public function __construct($form = null)
	{
		parent::__construct($form);

		$this->com_name = basename(realpath(dirname(__FILE__) . "/../.."));
		$this->ext_name = substr($this->com_name, 4);

		$this->document = JFactory::getDocument();

		if (!isset($GLOBALS[$this->ext_name . "_fields_js_loaded"]))
		{
			$this->document->addScript(JUri::base(true) . '/components/' . $this->com_name . "/models/fields/fields.js");
			$GLOBALS[$this->ext_name . "_fields_js_loaded"] = true;
		}
	}

	protected function getInput()
	{	
		$html = array();

		$options = (array)$this->getOptions();

		$html[] = '<select onchange="EmailChooserChange(this);" onkeyup="EmailChooserChange(this);" name="' . $this->name . '[select]" id="jform_' . $this->fieldname . '" class="b2jemailchooser">';
		foreach ($options as $option)
		{
			$selected = ($option->value == $this->value["select"]) ? ' selected="selected"' : "";
			$html[] = '<option value="' . $option->value . '" class="' . $option->class . '"' . $selected . '>' . $option->text . '</option>';
		}
		$html[] = '</select>';


		$html[] = '<fieldset class="panelform" id="' . $this->id . '_children">';
		
		$html[] = '<label for="jform_b2jemailchooser_name" aria-invalid="false">' . JText::_("COM_B2JCONTACT_NAME") . '</label>';
		$html[] = '<input type="text" name="' . $this->name . "[name]" . '" id="' . $this->id . '_name' . '"' . ' value="'
		. htmlspecialchars(empty($this->value["name"]) ? "" : $this->value["name"], ENT_COMPAT, 'UTF-8') . '"' . '/>';

		
		$html[] = '<label for="jform_b2jemailchooser_email" aria-invalid="false">' . JText::_("COM_B2JCONTACT_EMAIL_ADDRESS") . '</label>';
		$html[] = '<input type="text" name="' . $this->name . "[email]" . '" class="validate-email" id="' . $this->id . '_email' . '"' . ' value="'
		. htmlspecialchars(empty($this->value["email"]) ? "" : $this->value["email"], ENT_COMPAT, 'UTF-8') . '"' . '/>';
		$html[] = "</fieldset>";

		return implode($html);
	}

}
