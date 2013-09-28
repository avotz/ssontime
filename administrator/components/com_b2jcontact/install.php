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

require_once(realpath(dirname(__FILE__) . '/b2jinstall.php'));

class com_b2jcontactInstallerScript extends B2jInstaller
{
	function update($parent)
	{
		parent::install($parent);
	}
}