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

class JFormFieldB2JHeader extends JFormField
	{
	protected $type = 'B2JHeader';

	protected function getInput()
		{
		return '';
		}

	protected function getLabel()
		{

		$cn = basename(realpath(dirname(__FILE__) . '/../..'));
		$direction = intval(JFactory::getLanguage()->get('rtl', 0));
		$left  = $direction ? "right" : "left";
		$right = $direction ? "left" : "right";

		echo '<div class="clr"></div>';

		$colorGroup	= (string)$this->element['color'];
		switch ($colorGroup) {
			case 'basic':
				$color[0] = "#6c9aab"; 
				$color[1] = "#78abbe";
				break;
			case 'fields':
				$color[0] = "#c07163"; 
				$color[1] = "#d67e6e";
				break;
			case 'events':
				$color[0] = "#855c6e"; 
				$color[1] = "#94667a";
				break;
			case 'security':
				$color[0] = "#b0ab81"; 
				$color[1] = "#c4be90";
				break;				
			
			default:
				$color[0] = "#cccccc"; 
				$color[1] = "#cccccc";
				break;
		}

		$style = 'background:'."$color[0]".';';
		if ($this->element['default'])
			{
			return '<div class="b2j-contact-field-title" style="' . $style . '">' .

			'<span style="padding-' . $left . ':5px;">' .
			JText::_($this->element['default']) .
			'</span>' .
			'</div>';
			}
		else
			{
			return parent::getLabel();
			}

		echo '<div class="clr"></div>';
		}
	}
?>
