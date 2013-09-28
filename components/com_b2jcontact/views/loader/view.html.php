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
require_once JPATH_COMPONENT . "/models/contact.php";

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
		$owner = $this->Input->get("owner", "");

		$type = $this->Input->get("type", "");

		preg_match('/^[a-z_-]+$/', $owner) or $owner = "";

		$method = "_get_" . $owner . "_params_";

		if($type == 'js'){
			$params = $this->$method(false);
		}else{
			$params = $this->$method();
		}
		
		preg_match('/^[a-z_-]+$/', $type) or $type = "";

		$root = $this->Input->get("root", "");
		preg_match('/^components|media$/', $root) or $root = "components";

		$option = $this->Input->get("option", "");
		preg_match('/^[a-z_0-9-]+$/', $option) or $option = "";

		$view = $this->Input->get("v", "");

		preg_match('/^[a-z_0-9-]+$/', $view) or $view = "";
		$view = $view ? "/views/" . $view : "";


		jimport("b2jcontact.loader." . $type) or die("loader library not found");

		$classname = $type . "Loader";
		$loader = new $classname();
		$loader->IncludePath = JPATH_SITE . "/$root/$option" . $view;

		$loader->Params = & $params;
		
		$loader->Show();
	}


	private function _get__params_()
	{
		return new JRegistry;
	}


	private function _get_component_params_($loadComponent = true)
	{
		$application = @JFactory::getApplication('site');
		$menu = @$application->getMenu();
		$params = $menu->getParams(intval($this->Input->get("id", 0)));

		if($loadComponent){
			$params = $this->_getparams($params);
		}
		return $params;
	}


	private function _get_module_params_($loadComponent = true)
	{
		$db = JFactory::getDbo();
		jimport("joomla.database.databasequery");
		$query = $db->getQuery(true);
		$query->select($db->quoteName("params"));
		$query->from($db->quoteName("#__modules"));
		$query->where($db->quoteName("id") . "=" . intval($this->Input->get("id", 0)));
		$query->where($db->quoteName("module") . "=" . $db->quote("mod_b2jcontact"));
		$db->setQuery($query);

		$json = $db->loadResult();
			
		$params = new JRegistry($json);
		if($loadComponent){
			$params = $this->_getparams($params);
		}


		return $params;
	}
	private function _getparams($params){
		$b2jContactModel = new B2jContactModelContact;
		$b2jContactItem = $b2jContactModel->getItem(intval($this->Input->get("bid")),$params);
		
		$params = $b2jContactItem->params;	
		
		return $params;
	}
}