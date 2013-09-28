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

jimport('joomla.application.component.controller');

class B2jContactController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		$application = JFactory::getApplication("site");
		$menu = $application->getMenu();
		$activemenu = $menu->getActive();
		$view = $application->input->get("view", $this->default_view);

		if ($view == "b2jcontact" && !$activemenu)
		{
			JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_b2jcontact&view=invalid"));

		}
		
		return parent::display($cachable, $urlparams);
	}
}
