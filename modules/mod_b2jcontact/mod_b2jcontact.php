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

if (isset($GLOBALS["b2jcontact_mid_" . $module->id])) return;
else $GLOBALS["b2jcontact_mid_" . $module->id] = true;

$cache = JFactory::getCache("com_modules", "");
$cache->setCaching(false);

$cache = @JFactory::getCache("com_content", "view");
$cache->setCaching(false);

$GLOBALS["ext_name"] = basename(__FILE__);
$GLOBALS["com_name"] = realpath(dirname(__FILE__) . "/../../components");
$GLOBALS["mod_name"] = dirname(__FILE__);
$GLOBALS["EXT_NAME"] = strtoupper($GLOBALS["ext_name"]);
$GLOBALS["COM_NAME"] = strtoupper($GLOBALS["com_name"]);
$GLOBALS["MOD_NAME"] = strtoupper($GLOBALS["mod_name"]);
$GLOBALS["left"] = false;
$GLOBALS["right"] = true;
$app->owner = "module";
$app->oid = $module->id;
$app->b2jcomid = 0;
$app->b2jmoduleid = $module->id;
$app->bid = $params->get('bid');

$app->submitted = (bool)count($_POST) && isset($_POST["b2jmoduleid_$app->b2jmoduleid"]);
$me = basename(__FILE__);
$name = substr($me, 0, strrpos($me, '.'));

$GLOBALS["ext_name"] = substr(basename(realpath(dirname(__FILE__))), 4);

$GLOBALS["com_name"] = "com_" . $GLOBALS["ext_name"];
$GLOBALS["mod_name"] = "mod_" . $GLOBALS["ext_name"];
$GLOBALS["EXT_NAME"] = strtoupper($GLOBALS["ext_name"]);
$GLOBALS["COM_NAME"] = strtoupper($GLOBALS["com_name"]);
$GLOBALS["MOD_NAME"] = strtoupper($GLOBALS["mod_name"]);

$language = JFactory::getLanguage();
if (isset($language))
{
    $direction = intval($language->get('rtl', 0));
    $GLOBALS["left"]  = $direction ? "right" : "left";
    $GLOBALS["right"] = $direction ? "left" : "right";
}

$helpdir = JPATH_BASE . '/components/' . $GLOBALS["com_name"] . '/helpers/';
$libsdir = JPATH_BASE . '/components/' . $GLOBALS["com_name"] . '/lib/';
$modelsdir = JPATH_BASE . '/components/' . $GLOBALS["com_name"] . '/models/';


@require_once($modelsdir . 'contact.php');
@require_once($helpdir . 'fieldsbuilder.php');
@include_once($helpdir . 'b2jsubmitter.php');
@include_once($helpdir . 'b2jajaxuploader.php');
@include_once($helpdir . 'b2juploader.php');
@include_once($helpdir . 'b2jcaptcha.php');
@include_once($helpdir . 'b2jsession.php');
@include_once($helpdir . 'b2jantispam.php');
@require_once($helpdir . "b2jadminmailer.php");
@require_once($helpdir . "b2jsubmittermailer.php");
@require_once($helpdir . "b2jjmessenger.php");
@include_once($libsdir . 'functions.php');
@require_once($helpdir . "messageboard.php");


if ($scope == "com_content") echo("<!--{emailcloak=off}-->");

$document = JFactory::getDocument();
$user		= JFactory::getUser();

$prefix = "index.php?option=" . $GLOBALS["com_name"] .
	"&view=loader" .
	"&owner=" . JFactory::getApplication()->owner .
	"&bid=" . JFactory::getApplication()->bid .
	"&id=" . JFactory::getApplication()->oid;

		
$b2jContactModel = new B2jContactModelContact;
$b2jContactItem = $b2jContactModel->getItem($params->get('bid'),$params);
$params = $b2jContactItem->params;


$groups	= $user->getAuthorisedViewLevels();
if (!in_array($b2jContactItem->access, $groups))
{
	JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
	return;
}

$stylesheet = "b2jcontact.css";
$css_name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $stylesheet);
$document->addStyleSheet(JRoute::_($prefix . "&amp;root=components&amp;type=css&amp;filename=" . $css_name));

$action = "#b2jmoduleid_" . $module->id;

$language = JFactory::getLanguage();
$language->load($GLOBALS["com_name"], JPATH_SITE, $language->getDefault(), true);
$language->load($GLOBALS["com_name"], JPATH_SITE, null, true);

$page_subheading = $params->get("page_subheading", "");

$xml = JFactory::getXML(JPATH_SITE . '/modules/' . $app->scope . "/" . $app->scope . '.xml');

$messageboard = new B2JMessageBoard();

$fieldsBuilder = new B2JFieldsBuilder($params, $messageboard);
$submitter = new B2JSubmitter($params, $messageboard, $fieldsBuilder);
$ajax_uploader = new B2JAjaxUploader($params, $messageboard);
$uploader = new B2JUploader($params, $messageboard);
$b2jcaptcha = new B2JCaptcha($params, $messageboard);
$antispam = new B2JAntispam($params, $messageboard, $fieldsBuilder);
$jMessenger = new B2JJMessenger($params, $messageboard, $fieldsBuilder);

$adminMailer = new B2JAdminMailer($params, $messageboard, $fieldsBuilder);
$submitterMailer = new B2JSubmitterMailer($params, $messageboard, $fieldsBuilder);

$form_text = "";
$form_text .= $fieldsBuilder->Show();
$form_text .= $ajax_uploader->Show();
$form_text .= $b2jcaptcha->Show();
$form_text .= $antispam->Show();
$form_text .= $submitter->Show();

switch (0)
{
	case $submitter->IsValid():
		break;
	case $fieldsBuilder->IsValid():
		break;
	case $ajax_uploader->IsValid():
		break;
	case $uploader->IsValid():
		break;
	case $b2jcaptcha->IsValid():
		break;
	case $antispam->IsValid():
		break;
	case $jMessenger->Process():
		break;
	case $adminMailer->Process():
		break;
	case $submitterMailer->Process():
		break;
	default:
		$form_text = "";

		$jsession = JFactory::getSession();
		$b2jsession = new B2JSession($jsession->getId(), 0, $module->id, $app->bid);
		$b2jsession->PurgeValue("captcha_answer");

		B2JHeaderRedirect($params);
}


require(JModuleHelper::getLayoutPath($app->scope, $params->get('layout', 'default')));
