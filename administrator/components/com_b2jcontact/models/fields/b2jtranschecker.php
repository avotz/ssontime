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

jimport('joomla.form.formfield');

class JFormFieldB2JTranschecker extends JFormField
	{
	protected $type = 'B2JTranschecker';

	protected function getInput()
		{
		return "";
		}

	protected function getLabel()
		{
		$lang = JFactory::getLanguage();

		$cn = basename(realpath(dirname(__FILE__) . '/../..'));
		$direction = intval($lang->get('rtl', 0));
		$left  = $direction ? "right" : "left";
		$right = $direction ? "left" : "right";
		$image = '<img style="margin:0; float:' . $left . ';" src="' . JUri::base() . '../media/' . $cn . '/images/translations.png' . '">';
		$style = 'background:#f4f4f4; border:1px solid silver; padding:5px; margin:5px 0;';
		$msg_skel =
			'<div style="' . $style . '">' .
			$image .
			'<span style="padding-' . $left . ':5px; line-height:16px;">' .
			'Admin side translation for %s language is still %s. Please consider to contribute by writing and sharing your own translation.' .
			'</span>' .
			'</div>';


		if (intval(JText::_(strtoupper($cn) . '_PARTIAL')))
			{
			return sprintf($msg_skel, $lang->get("name"), "incomplete");
			}

		if (!file_exists(JPATH_ADMINISTRATOR . "/language/" . $lang->get("tag") . "/" . $lang->get("tag") . "." . $cn . ".ini"))
			{
			return sprintf($msg_skel, $lang->get("name"), "missing");
			}

		return "";
		}
	}