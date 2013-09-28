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

function B2JHeaderRedirect(&$params)
	{
	$redirect = $params->get("email_sent_action", 0);
	if (!$redirect) return;
	$link = B2JGetLink(intval($params->get("email_sent_page", 0)));
	if (!$link) return;

   switch($redirect)
      {
      case 1:
			header("Location: " . $link);
			break;
      case 2:
			header("refresh:5;url=" . $link); 
		}
	}


function B2JGetLink($menu_id = NULL, $anchor = NULL)
	{


	global $app;
	$wholemenu = $app->getMenu();
	if ($menu_id) $targetmenu = $wholemenu->getItem($menu_id);
	else $targetmenu = $wholemenu->getActive();

	if (!is_object($targetmenu)) return NULL;

	$link = $targetmenu->link;

	$router = JSite::getRouter();

	if ($router->getMode() == JROUTER_MODE_SEF) $link = 'index.php?Itemid=' . $targetmenu->id;
	else $link .= '&Itemid=' . $targetmenu->id;

	$link .= $anchor;

	return JRoute::_($link);

	}


function B2JGetHelpLink($msg)
	{
	$link = array();

	$lang = JFactory::getLanguage();
	$lang->load('com_b2jcontact.sys', JPATH_ADMINISTRATOR);


	return 'Error';
	}


?>
