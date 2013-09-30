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

$GLOBALS["ext_name"] = basename(__FILE__);
$GLOBALS["com_name"] = dirname(__FILE__);
$GLOBALS["mod_name"] = realpath(dirname(__FILE__) . "/../../modules");
$GLOBALS["EXT_NAME"] = strtoupper($GLOBALS["ext_name"]);
$GLOBALS["COM_NAME"] = strtoupper($GLOBALS["com_name"]);
$GLOBALS["MOD_NAME"] = strtoupper($GLOBALS["mod_name"]);

$GLOBALS["left"] = false;
$GLOBALS["right"] = true;


$application = JFactory::getApplication('site');
$menu = $application->getMenu();

$activemenu = $menu->getActive() or $activemenu = new stdClass();
$application->owner = "component";
$application->oid = isset($activemenu->id) ? $activemenu->id : 0;
$application->b2jcomid = isset($activemenu->id) ? $activemenu->id : 0;
$application->b2jmoduleid = 0;
$application->bid = isset($activemenu->query['bid']) ? $activemenu->query['bid']: 0;


$application->submitted = (bool)count($_POST) && isset($_POST["b2jcomid_$application->b2jcomid"]);
$me = basename(__FILE__);
$name = substr($me, 0, strrpos($me, '.'));
include(realpath(dirname(__FILE__) . "/" . $name . ".inc"));

$language = JFactory::getLanguage();
if ($language->get("tag") != $language->getDefault())
{
    $language->load($GLOBALS["com_name"], JPATH_SITE, $language->getDefault(), true);
    $language->load($GLOBALS["com_name"], JPATH_SITE, null, true);
}

jimport('joomla.application.component.controller');
$controller = JControllerLegacy::getInstance('B2jContact');
$controller->execute(JFactory::getApplication()->input->get("task", "display"));
$controller->redirect();