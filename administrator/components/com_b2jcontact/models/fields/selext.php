<?php defined('JPATH_BASE') or die('Restricted access');

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
class JFormFieldSelext extends JFormFieldList
{
	protected $type = "Selext";

	public function __construct($form = null)
	{
		parent::__construct($form);

		static $resources = true;

		if ($resources)
		{

			$resources = false;
			$this->com_name = basename(realpath(dirname(__FILE__) . "/../.."));
			$this->document = JFactory::getDocument();

			$type = strtolower($this->type);
			
			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $this->com_name . "/js/" . $type . ".js"))
			{
				$this->document->addScript(JUri::base(true) . "/components/" . $this->com_name . "/js/" . $type . ".js");
			}

			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $this->com_name . "/css/" . $type . ".css"))
			{
				$this->document->addStyleSheet(JUri::base(true) . "/components/" . $this->com_name . "/css/" . $type . ".css");
			}
		}
	}


	protected function getInput()
	{
		if (!is_array($this->value))
		{
			$this->value = explode("|", $this->value);
			$this->value["text"] = $this->value[0];
			$this->value["select"] = $this->value[1];
		}

		$size = $this->element["size"] ? 'size="' . (int) $this->element["size"] . '" ' : '';

		$html =
		'<input ' .
		'type="text" ' .
		'name="' . $this->name . '[text]" ' .
		'id="' . $this->id . '_text" ' .
		'value="' . htmlspecialchars($this->value["text"], ENT_COMPAT, 'UTF-8') . '" ' .
		$size .
		'class="selext" />';

		$html .=
		'<select ' .
		'onchange="SelextSelectChange(this, \'' . $this->id . '\');" onkeyup="SelextSelectChange(this, \'' . $this->id . '\');" ' .
		'name="' . $this->name . '[select]" ' .
		'id="' . $this->id . '_select" ' .
		'class="selext">';

		$options = (array)$this->getOptions();
		foreach ($options as $option)
		{
			$selected = ($option->value == $this->value["select"]) ? $selected = 'selected="selected"' : "";
			$html .= '<option value="' . $option->value . '" class="' . $option->class . '" ' . $selected . '>' . $option->text . '</option>';
		}

		$html .= '</select>';

		return $html;
	}

}
