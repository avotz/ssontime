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

class B2jContactViewLoader extends JViewLegacy
{
	protected $Input;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->Input = JFactory::getApplication()->input;
	}


	function display($tpl = null)
	{
		$type = $this->Input->get("type", "");
		preg_match('/^[a-z_-]+$/', $type) or $type = "";

		jimport("b2jcontact.loader." . $type) or die("loader library not found");

		$view = $this->Input->get("v", "");
		preg_match('/^[a-z_-]+$/', $view) or $view = "";

		$view = $view ? "/views/" . $view : "";
		$option = $this->Input->get("option", "");

		$classname = $type . "Loader";
		$loader = new $classname();
		$loader->IncludePath = JPATH_ADMINISTRATOR . "/components/$option" . $view;
		$loader->Show();
	}
}