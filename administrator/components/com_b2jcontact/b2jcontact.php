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

// Access check
if (!JFactory::getUser()->authorise("core.manage", "com_b2jcontact"))
{
	return JFactory::getApplication()->enqueueMessage(JText::_("JERROR_ALERTNOAUTHOR"), "error");
}

$language = JFactory::getLanguage();
if ($language->get("tag") != $language->getDefault())
{
    $GLOBALS["com_name"] = basename(dirname(__FILE__));
    $language->load($GLOBALS["com_name"], JPATH_ADMINISTRATOR, $language->getDefault(), true);
    $language->load($GLOBALS["com_name"], JPATH_ADMINISTRATOR, null, true);
}

jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance("B2jContact");
$controller->execute(JFactory::getApplication()->input->get("task", "display"));
$controller->redirect();
