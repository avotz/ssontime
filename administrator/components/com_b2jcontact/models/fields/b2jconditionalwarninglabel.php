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

class JFormFieldB2JConditionalWarningLabel extends JFormField
	{
	protected $type = 'B2JConditionalWarningLabel';

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

		$db = JFactory::getDBO();
		$sql = "SELECT value FROM #__" . substr($cn, 4) . "_settings WHERE name = '" . $this->element['triggerkey'] . "';";
		$db->setQuery($sql);
		$method = $db->loadResult();

		if (!$method)
			{
			$style = 'clear:both; background:#f4f4f4; border:1px solid silver; padding:5px; margin:5px 0;';
			$image = '<img style="margin:0; float:' . $left . ';" src="' . JUri::base() . '../media/' . $cn . '/images/exclamation-16.png">';
			return
				'<div style="' . $style . '">' .
				$image .
				'<span style="padding-' . $left . ':5px; line-height:16px;">' .
				'Problems with database'.
				'</span>' .
				'</div>';
			}

		if ($method != $this->element['triggervalue'])
			{
			return "";
			}

		echo '<div class="clr"></div>';
		$image = '';
		$icon	= (string)$this->element['icon'];
		if (!empty($icon))
			{
			$image .= '<img style="margin:0; float:' . $left . ';" src="' . JUri::base() . '../media/' . $cn . '/images/' . $icon . '">';
			}
		$style = 'background:#f4f4f4; border:1px solid silver; padding:5px; margin:5px 0;';
		if ($this->element['default'])
			{
			return '<div style="' . $style . '">' .
				$image .
				'<span style="padding-' . $left . ':5px; line-height:16px;">' .
				'error' .
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
