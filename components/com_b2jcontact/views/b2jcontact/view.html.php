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

	$helpdir = JPATH_BASE . '/components/' . $GLOBALS["com_name"] . '/helpers/';

	@require_once($helpdir . "b2jsubmitter.php");
	@require_once($helpdir . "fieldsbuilder.php");
	@require_once($helpdir . "b2jajaxuploader.php");
	@require_once($helpdir . "b2juploader.php");
	@require_once($helpdir . "b2jadminmailer.php");
	@require_once($helpdir . "b2jsubmittermailer.php");
	@require_once($helpdir . "b2jantispam.php");
	@require_once($helpdir . "b2jcaptcha.php");
	@require_once($helpdir . "b2jjmessenger.php");
	@require_once($helpdir . "b2jsession.php");
	@require_once($helpdir . "messageboard.php");

	require_once JPATH_COMPONENT . "/lib/functions.php";
	require_once JPATH_COMPONENT."/models/contact.php";

	class B2jContactViewB2jContact extends JViewLegacy
	{
		protected $Application;
		protected $cparams;
		protected $Submitter;
		protected $FieldsBuilder;
		protected $AjaxUploader;
		protected $Uploader;
		protected $Antispam;
		protected $JMessenger;
		protected $AdminMailer;
		protected $SubmitterMailer;
		protected $B2jCaptcha;
		protected $MessageBoard;

		public $FormText = "";

		function display($tpl = null)
		{
			$this->Application = JFactory::getApplication();
			$user		= JFactory::getUser();
			
			$this->cparams = $this->Application->getMenu()->getActive()->params;

			
			if ($description = $this->cparams->get('menu-meta_description'))
				$this->document->setDescription($description);
			
			if ($keywords = $this->cparams->get('menu-meta_keywords'))
				$this->document->setMetadata('keywords', $keywords);
			
			if ($robots = $this->cparams->get('robots'))
				$this->document->setMetadata('robots', $robots);

			$prefix =
				"index.php?option=" . $this->Application->scope .
				"&view=loader" .
				"&owner=" . $this->Application->owner .
				"&bid=" . $this->Application->bid .
				"&id=" . $this->Application->oid;

			$b2jContactModel = new B2jContactModelContact;

			$b2jContactItem = $b2jContactModel->getItem($this->Application->bid,$this->cparams);
			$this->cparams = $b2jContactItem->params;
			

			$groups	= $user->getAuthorisedViewLevels();
			if (!in_array($b2jContactItem->access, $groups))
			{
				JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
				return;
			}

			$stylesheet = "b2jcontact.css";
			$css_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $stylesheet);
			$this->document->addStyleSheet($prefix . "&amp;root=components&amp;type=css&amp;filename=" . $css_name);

			$this->MessageBoard = new B2JMessageBoard();
            $this->FieldsBuilder = new B2JFieldsBuilder($this->cparams, $this->MessageBoard);
			$this->Submitter = new B2JSubmitter($this->cparams, $this->MessageBoard, $this->FieldsBuilder);
			$this->AjaxUploader = new B2JAjaxUploader($this->cparams, $this->MessageBoard);
			$this->Uploader = new B2JUploader($this->cparams, $this->MessageBoard);
			$this->B2jCaptcha = new B2JCaptcha($this->cparams, $this->MessageBoard);
			$this->JMessenger = new B2JJMessenger($this->cparams, $this->MessageBoard, $this->FieldsBuilder);
			$this->Antispam = new B2JAntispam($this->cparams, $this->MessageBoard, $this->FieldsBuilder);

			$this->AdminMailer = new B2JAdminMailer($this->cparams, $this->MessageBoard, $this->FieldsBuilder);
			$this->SubmitterMailer = new B2JSubmitterMailer($this->cparams, $this->MessageBoard, $this->FieldsBuilder);

			$this->FormText .= $this->FieldsBuilder->Show();
			$this->FormText .= $this->AjaxUploader->Show();

			$this->FormText .= $this->B2jCaptcha->Show();
			$this->FormText .= $this->Antispam->Show();
			$this->FormText .= $this->Submitter->Show();

			switch(0)
			{
				case $this->Submitter->IsValid(): break;
				case $this->FieldsBuilder->IsValid(): break;
				case $this->AjaxUploader->IsValid(): break;
				case $this->Uploader->IsValid(): break;
				case $this->B2jCaptcha->IsValid(): break;
				case $this->Antispam->IsValid(): break;
				case $this->JMessenger->Process(): break;

				case $this->AdminMailer->Process(): break;
				case $this->SubmitterMailer->Process(): break;
				default:  
					
					$this->FormText = "";

					$jsession = JFactory::getSession();
					$b2jsession = new B2JSession($jsession->getId(), $this->Application->b2jcomid, $this->Application->b2jmoduleid, $this->Application->bid);
					$b2jsession->PurgeValue("captcha_answer");

					B2JHeaderRedirect($this->cparams);
			}


			if (count($errors = $this->get('Errors')))
			{
				$this->Application->enqueueMessage(implode('<br />', $errors), 'error');

			}
			
			parent::display($tpl);
		}
	}
?>
