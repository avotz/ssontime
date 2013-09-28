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

class B2JLangHandler
	{
	protected $lang;
	protected $messages = array();

	function __construct()
		{
		$this->lang = JFactory::getLanguage();

		$this->check_partial();
		$this->check_missing();
		}


	public function HasMessages()
		{
		return (bool)count($this->messages);
		}


	public function GetMessages()
		{
		return $this->messages;
		}


	protected function check_partial()
		{
		if (intval(JText::_($GLOBALS["COM_NAME"] . '_PARTIAL')))
			{
	
			$this->messages[] = $this->lang->get("name") . " translation is still incomplete. Please consider to contribute by completing and sharing your own translation.";
			}
		}


	protected function check_missing()
		{
		$filename = JPATH_SITE . "/language/" . $this->lang->get("tag") . "/" . $this->lang->get("tag") . "." . $GLOBALS["com_name"] . ".ini";
		if (!file_exists($filename))
			{
			$this->messages[] = $this->lang->get("name") . " translation is still missing. Please consider to contribute by writing and sharing your own translation.";

			$this->check_availability();
			}
		}


	private function check_availability()
		{
		$filename = JPATH_ADMINISTRATOR . '/components/' . $GLOBALS["com_name"] . "/" . $GLOBALS["ext_name"] . '.xml';
		$xml = JFactory::getXML($filename);

		if (!$xml)
			{
	
			}
		else
			{
			foreach ($xml->languages->language as $l)
				{
				if (strpos((string)$l, $this->lang->get("tag")) === 0)
					{
					$this->messages = array();
					$this->messages[] = $this->lang->get("name") . " translation has not been installed, but <strong>is available</strong>. To fix this problem simply install this extension once again, without uninstalling it.";
					break;
					}
				}
			}


		}

	}

?>