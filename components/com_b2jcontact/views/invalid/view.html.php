<?php defined('_JEXEC') or die('Restricted access');

/* ------------------------------------------------------------------------
 * Bang2Joom Contact for Joomla 3.0+
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2013 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT . "/lib/functions.php";

class B2jContactViewInvalid extends JViewLegacy
{
	function display($tpl = null)
	{
		$language = JFactory::getLanguage();
		$application = JFactory::getApplication("site");
		$menu = $application->getMenu();
		echo("<h2>" . $language->_($GLOBALS["COM_NAME"] . "_ERR_PROVIDE_VALID_URL") . "</h2>");
		$valid_items = $menu->getItems("component", $GLOBALS["com_name"]);
		echo("<ul>");
		foreach ($valid_items as &$valid_item)
		{
			echo('<li><a href="' . B2JGetLink($valid_item->id) . '">' . $valid_item->title . '</a></li>');
		}
		echo("</ul>");

		$language->load("com_b2jcontact", JPATH_ADMINISTRATOR);
	}
}