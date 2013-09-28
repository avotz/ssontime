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

class B2jEmailHelper
{
	protected $Params;

	public function __construct(&$params)
	{
		$this->Params = $params;
	}

	public function convert($data)
	{
		$type = $data->select;
		return $this->{$data->select}($data);
	}

	public function submitter($data)
	{
		$application = JFactory::getApplication();
		$name = "_" . md5($this->Params->get("sender0") . $application->b2jcomid . $application->b2jmoduleid);
		$name = JRequest::getVar($name, NULL, "POST");
		$address = "_" . md5($this->Params->get("sender1") . $application->b2jcomid . $application->b2jmoduleid);
		$address = JRequest::getVar($address, NULL, "POST");
		return array($address, $name);
	}

	public function admin($data)
	{
		$application = JFactory::getApplication();
		$name = $application->getCfg("fromname");
		$address = $application->getCfg("mailfrom");
		return array($address, $name);
	}

	public function custom($data)
	{
		return array($data->email, $data->name);
	}
}


