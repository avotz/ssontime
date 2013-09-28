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

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldB2JConditionalList extends JFormField
{
	protected $type = 'B2JConditionalList';

	protected function getInput()
	{

		$html = array();
		$attr = '';

		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$options = (array) $this->getOptions();

		$db = JFactory::getDBO();
		$sql = "SELECT value FROM #__" . substr(basename(realpath(dirname(__FILE__) . '/../..')), 4) . "_settings WHERE name = '" . $this->element['triggerkey'] . "';";
		$db->setQuery($sql);
		$method = $db->loadResult();
		if (!$method || $method == $this->element['triggervalue']) {
			$attr .= ' disabled="disabled"';
			$this->value = (string)$this->element['triggerdata'];
		}

		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option) {

			if ($option->getName() != 'option') {
				continue;
			}

			$tmp = JHtml::_('select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', ((string) $option['disabled']=='true'));

			$tmp->class = (string) $option['class'];

			$tmp->onclick = (string) $option['onclick'];

			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
