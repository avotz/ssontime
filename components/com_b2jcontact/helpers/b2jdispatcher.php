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
require_once($inc_dir . "/b2jdatapump.php");
require_once($inc_dir . "/b2jlogger.php");
require_once($inc_dir . "/b2jsession.php");
require_once($inc_dir . "/emailhelper.php");
jimport('joomla.mail.helper');

abstract class B2JDispatcher extends B2JDataPump
{
	protected $FieldsBuilder;
	protected $B2JSession;
	protected $FileList;


	abstract public function Process();


	protected function LoadFields()
	{
	}


	public function __construct(&$params, B2JMessageBoard &$messageboard, &$fieldsbuilder)
	{
		parent::__construct($params, $messageboard);

		$this->FieldsBuilder = $fieldsbuilder;
		$this->Logger = new B2JLogger();

		$jsession = JFactory::getSession();
		$this->B2JSession = new B2JSession($jsession->getId(), $this->Application->b2jcomid, $this->Application->b2jmoduleid, $this->Application->bid);
		$data = $this->B2JSession->Load('filelist');
		if ($data) $this->FileList = explode("|", $data);
		else $this->FileList = array();

	}


	protected function submittername()
	{
		return
			isset($this->FieldsBuilder->Fields['sender0']) ?
				$this->FieldsBuilder->Fields['sender0']['Value'] :
				$this->Application->getCfg("fromname");
	}


	protected function submitteraddress()
	{
		$addr =
			isset($this->FieldsBuilder->Fields['sender1']['Value']) &&
				!empty($this->FieldsBuilder->Fields['sender1']['Value']) ?
				$this->FieldsBuilder->Fields['sender1']['Value'] :
				$this->Application->getCfg("mailfrom");

		return JMailHelper::cleanAddress($addr);
	}


	protected function body()
	{
		$result = "";
		foreach ($this->FieldsBuilder->Fields as $key => $field)
		{
			switch ($field['Type'])
			{
				case 'sender':
				case 'text':
				case 'textarea':
				case 'dropdown':
				case 'checkbox':
				case 'date':
					$result .= $this->AddToBody($field);
			}
		}
        
        foreach ($this->FieldsBuilder->DynamicFields as $key => $field)
		{
			foreach ($field[1] as $dynamicfield){
                $result .= $this->AddToBodyDynamic($dynamicfield);
            }
		}

		$result .= PHP_EOL;
		return $result;
	}


	protected function AddToBody(&$field)
	{
		if (!$field['Display']) return "";
		return "*" . JFilterInput::getInstance()->clean($field["Name"], "") . "*" . PHP_EOL . JFilterInput::getInstance()->clean($field["Value"], "") . PHP_EOL . PHP_EOL;
	}
    
    protected function AddToBodyDynamic(&$field)
	{
		return "*" . JFilterInput::getInstance()->clean($field->b2jFieldName, "") . "*" . PHP_EOL . JFilterInput::getInstance()->clean($field->b2jFieldValue, "") . PHP_EOL . PHP_EOL;
	}


	protected function CurrentURL()
	{
		$url = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $url .= "s";
		$url .= "://";
		$url .= $_SERVER["SERVER_NAME"];
		if ($_SERVER["SERVER_PORT"] != "80") $url .= ":" . $_SERVER["SERVER_PORT"];
		$url .= $_SERVER["REQUEST_URI"];
		return $url;
	}


	protected function ClientIPaddress()
	{
		if (isset($_SERVER["REMOTE_ADDR"])) return $_SERVER["REMOTE_ADDR"];
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
		if (isset($_SERVER["HTTP_CLIENT_IP"])) return $_SERVER["HTTP_CLIENT_IP"];
		return "?";
	}


	protected function send(&$mail)
	{
		if (($error = $mail->Send()) !== true)
		{
			$info = empty($mail->ErrorInfo) ? $error->getMessage() : $mail->ErrorInfo;
			$msg = JText::_($GLOBALS["COM_NAME"] . "_ERR_SENDING_MAIL") . ". " . $info;
			$this->MessageBoard->Add($msg, B2JMessageBoard::error);
			$this->Logger->Write($msg);

			return false;
		}


		$this->Logger->Write("Email sent.");

		if (get_class($this) == "B2JAdminMailer")
		{
			$this->MessageBoard->Add($this->Params->get("email_sent_text"), B2JMessageBoard::success);
		}
		return true;
	}
}

?>
