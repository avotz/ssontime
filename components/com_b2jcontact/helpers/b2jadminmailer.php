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

	class B2JAdminMailer extends B2JDispatcher
	{

		public function __construct(&$params, B2JMessageBoard &$messageboard, &$fieldsbuilder)
		{
			parent::__construct($params, $messageboard, $fieldsbuilder);
		}


		protected function LoadFields()
		{
		}


		public function Process()
		{
			$mail = JFactory::getMailer();

			$this->set_from($mail);
			$this->set_to($mail, "to_address", "addRecipient");
			$this->set_to($mail, "cc_address", "addCC");
			$this->set_to($mail, "bcc_address", "addBCC");

			$mail->setSubject(JMailHelper::cleanSubject($this->Params->get("email_subject", "")));

			$body = $this->body();
			$body .= $this->attachments($mail);
			$body .= PHP_EOL;

			$body .= $this->Application->getCfg("sitename") . " - " . $this->CurrentURL() . PHP_EOL;

			$body .= "Client: " . $this->ClientIPaddress() . " - " . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;

			$body = JMailHelper::cleanBody($body);
			$mail->setBody($body);

			$this->Logger->Write("---------------------------------------------------" . PHP_EOL . $body);

			return $this->send($mail);
		}


		private function set_from(&$mail)
		{
			$emailhelper = new B2jEmailHelper($this->Params);
			$config = JComponentHelper::getParams("com_b2jcontact");

			$adminemailfrom = $config->get("adminemailfrom");
			$from = $emailhelper->convert($adminemailfrom);
			$mail->setSender($from);

			$adminemailreplyto = $config->get("adminemailreplyto");
			$replyto = $emailhelper->convert($adminemailreplyto);
			$mail->ClearReplyTos();
			$mail->addReplyTo($replyto);
		}


		private function set_to(&$mail, $param_name, $method)
		{
			if ($this->Params->get($param_name, NULL))
				$recipients = explode(",", $this->Params->get($param_name, ""));
			else
				$recipients = array();

			foreach ($recipients as $recipient)
			{
				if (empty($recipient)) continue;
				$mail->$method($recipient);
			}
		}


		protected function attachments(&$mail)
		{
			$result = "";
			$uploadmethod = intval($this->Params->get("uploadmethod", "1")); 

			if (count($this->FileList) && ($uploadmethod & 1)) $result .= JText::_($GLOBALS["COM_NAME"] . "_ATTACHMENTS") . PHP_EOL;
			foreach ($this->FileList as &$file)
			{
				$filename = 'components/' . $GLOBALS["com_name"] . '/uploads/' . $file;
				if ($uploadmethod & 1) $result .= JUri::base() . $filename . PHP_EOL;
				if ($uploadmethod & 2) $mail->addAttachment(JPATH_SITE . "/" . $filename);
			}

			return $result;
		}

	}
?>
