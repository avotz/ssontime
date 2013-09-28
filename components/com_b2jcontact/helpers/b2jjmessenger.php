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

$inc_dir = realpath(dirname(__FILE__));
require_once($inc_dir . '/b2jdispatcher.php');

class B2JJMessenger extends B2JDispatcher
{
	public function __construct(&$params, B2JMessageBoard &$messageboard, &$fieldsbuilder)
	{
		parent::__construct($params, $messageboard, $fieldsbuilder);

		$this->isvalid = true;
	}


	public function Process()
	{
		$uid = $this->Params->get("jmessenger_user", NULL);
		
		if (!$uid)
		{
			
			return true;
		}

		$body = $this->body();
		$body .= $this->attachments();
		$body .= PHP_EOL;
		
		$body .= $this->Application->getCfg("sitename") . " - " . $this->CurrentURL() . PHP_EOL;
		
		$body .= "Client: " . $this->ClientIPaddress() . " - " . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;

		$body = nl2br($body);
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->insert($db->quoteName("#__messages"));
		$query->set($db->quoteName("user_id_from") . "=" . $db->quote($uid));
		$query->set($db->quoteName("user_id_to") . "=" . $db->quote($uid));
		$query->set($db->quoteName("date_time") . "=" . $db->quote(JFactory::getDate()->toSql()));
		$query->set($db->quoteName("subject") . "=" . $db->quote($this->submittername() . " (" . $this->submitteraddress() . ")"));
		$query->set($db->quoteName("message") . "=" . $db->quote(JMailHelper::cleanBody($body)));

		$db->setQuery((string)$query);

		if (!$db->query())
		{

			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . "_ERR_SENDING_MESSAGE"), B2JMessageBoard::error);

			return false;
		}


		return true;

	}


	protected function attachments()
	{
		$result = "";

		if (count($this->FileList)) $result .= JText::_($GLOBALS["COM_NAME"] . "_ATTACHMENTS") . PHP_EOL;
		foreach ($this->FileList as &$file)
		{
			$result .= JUri::base() . 'components/' . $GLOBALS["com_name"] . '/uploads/' . $file . PHP_EOL;
		}

		return $result;
	}

}

?>
