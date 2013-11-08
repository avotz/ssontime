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

	class B2JSubmitterMailer extends B2JDispatcher
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
			$copy_to_submitter =
			(bool)JRequest::getVar($this->SafeName("copy_to_submitter" . $this->GetId()), NULL, 'POST') || 
			($this->Params->get("copy_to_submitter", NULL) == 1); 

			if (!$copy_to_submitter || !isset($this->FieldsBuilder->senderEmail->b2jFieldValue) || empty($this->FieldsBuilder->senderEmail->b2jFieldValue))
			{
				$this->B2JSession->Clear('filelist');

				return true;
			}

			$mail = JFactory::getMailer();

			$this->set_from($mail);
			$this->set_to($mail);
			$mail->setSubject(JMailHelper::cleanSubject($this->Params->get("email_copy_subject", "")));

	
			$body = $this->Params->get("email_copy_text", "") . PHP_EOL;
			
			$body .= PHP_EOL;

			if ($this->Params->get("email_copy_summary", NULL))
			{
				$body .= $this->body();
				$body .= $this->attachments();
				$body .= PHP_EOL;
			}

			
			$body .= "------" . PHP_EOL . $this->Application->getCfg("sitename") . PHP_EOL;

			$body = JMailHelper::cleanBody($body);
			$mail->setBody($body);

			
			$this->B2JSession->Clear('filelist');

			$this->send($mail);

			return true;
		}


		private function set_from(&$mail)
		{
			$emailhelper = new B2jEmailHelper($this->Params);
			$config = JComponentHelper::getParams("com_b2jcontact");

			$submitteremailfrom = $config->get("submitteremailfrom");
			$from = $emailhelper->convert($submitteremailfrom);
			$mail->setSender($from);

			//$submitteremailreplyto = $config->get("submitteremailreplyto");
			//$replyto = $emailhelper->convert($submitteremailreplyto);

			$application = JFactory::getApplication();
			$name = $application->getCfg("fromname");
			if(isset($this->FieldsBuilder->senderEmail->b2jFieldValue) && !empty($this->FieldsBuilder->senderEmail->b2jFieldValue)){
				$replyto['0'] = $this->FieldsBuilder->senderEmail->b2jFieldValue; 	
			}else{
				$replyto['0'] = ''; 	
			}
			$replyto['1'] = $name; 	

			$mail->ClearReplyTos();
			$mail->addReplyTo($replyto);
		}


		private function set_to(&$mail)
		{
			//$addr = $this->FieldsBuilder->Fields['sender1']['Value'];
			$addr =$this->FieldsBuilder->senderEmail->b2jFieldValue;
			$mail->addRecipient(JMailHelper::cleanAddress($addr));
		}


		protected function attachments()
		{
			$result = "";


			if (count($this->FileList)) $result .= JText::_($GLOBALS["COM_NAME"] . "_ATTACHMENTS") . PHP_EOL;
			foreach ($this->FileList as &$file)
			{
				$result .= substr($file, 14) . PHP_EOL;
			}

			return $result;
		}

	}
?>
