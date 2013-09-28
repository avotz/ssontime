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

	protected $default_view = 'contacts';

	function display($cachable = false, $urlparams = array())
	{
		require_once JPATH_COMPONENT.'/helpers/contact.php';

		$view   = $this->input->get('view', 'contacts');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');


		if ($view == 'contact' && $layout == 'edit' && !$this->checkEditId('com_b2jcontact.edit.contact', $id)) {
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_b2jcontact&view=contacts', false));

			return false;
		}
		parent::display();

		return $this;

		parent::display($cachable, $urlparams);
	}
	function showAddField() {
		require_once JPATH_COMPONENT.'/helpers/contact.php';

		$type = JFactory::getApplication()->input->get("type",false);

        $response = ContactHelper::getFieldForm($type);

        echo json_encode($response);
  
        exit();
    }
    function saveNewField() {
		require_once JPATH_COMPONENT.'/helpers/contact.php';

		$type			= JFactory::getApplication()->input->get("type",false);
		$fieldName		= JFactory::getApplication()->input->get("fieldName",false,"string");
		$defaultValue	= JFactory::getApplication()->input->get("defaultValue",false,"string");
		$fieldState 	= JFactory::getApplication()->input->get("fieldState",false);
		$fieldGroup		= JFactory::getApplication()->input->get("fieldGroup",false);
		$fieldItems 	= JFactory::getApplication()->input->get("fieldItems",false,"string");
		//$b2jGroups 	= JFactory::getApplication()->input->get("b2jGroups",false,"string");

		$newGroupName 	= JFactory::getApplication()->input->get("newGroupName",false);
		$key 	= JFactory::getApplication()->input->get("key",false);

        $response = ContactHelper::saveNewField($type,$fieldName,$defaultValue,$fieldState,$fieldGroup,$fieldItems,$newGroupName,$key);//,$b2jGroups
        
        echo json_encode($response);
  
        exit();
    }
}
