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
require_once($inc_dir . '/b2jdatapump.php');
require_once($inc_dir . '/b2jsession.php');
include_once(realpath(dirname(__FILE__) . "/../" . substr(basename(realpath(dirname(__FILE__) . "/..")), 4) . ".inc"));

class B2JCaptcha extends B2JDataPump
{
	protected $b2jsession;


	public function __construct(&$params, B2JMessageBoard &$messageboard)
	{
		parent::__construct($params, $messageboard);

		$this->Name = "B2JCaptcha";

		
		$this->Fields['Value'] = $this->FaultTolerance(JRequest::getVar("b2jcaptcha", NULL, 'POST'));
		
		$jsession = JFactory::getSession();

		$this->b2jsession = new B2JSession($jsession->getId(), $this->Application->b2jcomid, $this->Application->b2jmoduleid, $this->Application->bid);
		$this->Fields['Secret'] = $this->FaultTolerance($this->b2jsession->Load('captcha_answer'));
		
		$this->isvalid = intval($this->Validate());
	}


	protected function LoadFields()
	{
	}


	protected function LoadField($type, $number) 
	{
	}


	function OverrideFields()
	{
	}


	function OverrideField($type, $number)
	{
	}


	public function Show()
	{
		if (!(bool)$this->Params->get("stdcaptchadisplay")) return "";
		$captcha_width = (int)$this->Params->get("stdcaptchawidth", "");
		$captcha_height = (int)$this->Params->get("stdcaptchaheight", "");

		$valid = (!empty($this->Fields['Secret']) && $this->Fields['Value'] == $this->Fields['Secret']);

		$this->Fields["Name"] = $this->Params->get("stdcaptcha", "");
		$this->Fields["Display"] = 2;
		$this->CreateStandardLabel($this->Fields);

		$result =
			'<div class="control-group' . $this->TextStyleByValidation() . '"';
		if ($valid) $result .= ' style="display:none !important;"';
		$result .= '>' . PHP_EOL .
			$this->LabelHtmlCode .

			'<div ' .
			'class="controls" ' .
			'>' . PHP_EOL;

		if (!$valid)
		{
			$result .=

				'<div class="b2jcaptchafieldcontainer">' .
					'<img src="' .
					JRoute::_('index.php?option=' . $GLOBALS["com_name"] .
						"&view=loader" .
						"&owner=" . $this->Application->owner .
						"&id=" . $this->Application->oid .
						"&bid=" . $this->Application->bid .
						"&root=none" .
						"&filename=none" .
						'&type=captcha') .
					'" ' .
					'class="b2j_captcha_img" ' .
					'alt="captcha" ' . 
					'id="b2jcaptcha_' . $this->GetId() . '" width="' . $captcha_width . '" height="' . $captcha_height . '"/>' .
					'</div>';
		}

		$result .=
			'<div class="b2jcaptchainputcontainer">' .

				

				'<input ' .
				'type="text" ' .
				'name="' . "b2jcaptcha" . '" ' .
				'style="width:' . ($captcha_width - 40) . 'px !important;" ' .
				'value="' . $this->FieldValue . '" ' .
				'title="' . $this->Params->get("stdcaptcha", "") . '" ' .
				$this->JSCode;

		if ($valid)
		{
			$result .=
				'readonly="readonly" ';
		}

		$result .=
			'/>' .
            $this->DescriptionByValidation() . 
				'</div>'; 

		if (!$valid)
		{
			$result .=

				'<div class="b2jcaptcha-reload-container">' .
					'<img src="' . JUri::base(true) . '/media/' . $GLOBALS["com_name"] . '/images/transparent.gif" ' .
					'id="reloadbtn_' . $this->GetId() . '" ' .
					'alt="' . JTEXT::_($GLOBALS["COM_NAME"] . '_RELOAD_ALT') . '" ' .
					'title="' . JTEXT::_($GLOBALS["COM_NAME"] . '_RELOAD_TITLE') . '" ' .
					'width="16" height="16" ' .
					"onclick=\"javascript:ReloadB2JCaptcha('b2jcaptcha_" . $this->GetId() . "')\" />" .
					'</div>' .
					"<script language=\"javascript\" type=\"text/javascript\">BuildReloadButton('reloadbtn_" . $this->GetId() . "');</script>";
		}

		$result .=
			'</div>' . 
				'</div>' . 
				PHP_EOL;

		if (!$this->isvalid)
		{
			$this->MessageBoard->Add(JText::sprintf($GLOBALS["COM_NAME"] . '_ERR_INVALID_VALUE', JText::_($GLOBALS["COM_NAME"] . '_SECURITY_CODE')), B2JMessageBoard::error);
		}
		return $result;
	}


	private function build_label(&$field)
	{
		return '<label ' .
			'class="control-label"' .
			'>' .
			$this->Params->get("stdcaptcha", "&nbsp;") .
			'</label>' . PHP_EOL;
	}


	protected function TextStyleByValidation()
	{
		if (!$this->Submitted) return "";
		return $this->isvalid ? " success" : " error";
	}


	function Validate()
	{
		$isrequired = (bool)$this->Params->get("stdcaptchadisplay");

		$this->isvalid = (!empty($this->Fields['Secret']) && $this->Fields['Value'] == $this->Fields['Secret']);
		return !($this->Submitted && $isrequired && !$this->isvalid);
	}


	private function DescriptionByValidation()
	{
		return $this->isvalid ? "" : (" <span class=\"asterisk\"></span>");
	}


	private function FaultTolerance($string)
	{
		if ($string == $this->Params->get("stdcaptcha", "")) return $string;

		$string = strtolower($string);
		$string = preg_replace("/[l1]/", "i", $string);
		$string = preg_replace("/[0]/", "o", $string);
		$string = preg_replace("/[q9]/", "g", $string);
		$string = preg_replace("/[5]/", "s", $string);
		$string = preg_replace("/[8]/", "b", $string);

		return $string;
	}

}


class b2jcaptchaCheckEnvironment
{
	protected $InstallLog;


	public function __construct()
	{
		$this->InstallLog = new B2JLogger("b2jcaptchaimage", "install");
		$this->InstallLog->Write("--- Determining if this system is able to draw captcha images ---");

		switch (true)
		{
			case $this->gd_usable():
				$value = "use_gd";
				break;
			
			default:
				$value = "disabled";
		}

		$db = JFactory::getDBO();

		$sql = "REPLACE INTO #__" . $GLOBALS["ext_name"] . "_settings (name, value) VALUES ('captchadrawer', '$value');";
		$db->setQuery($sql);
		$result = $db->query();

		$this->InstallLog->Write("--- Method choosen to draw captcha images is [$value] ---");
		return $result;
	}


	private function gd_usable()
	{
		if (!extension_loaded("gd") || !function_exists("gd_info"))
		{
			$this->InstallLog->Write("gd extension not found");
			return false;
		}

		$this->InstallLog->Write("gd extension found. Let's see if it works.");

		$gdinfo = gd_info();
		foreach ($gdinfo as $key => $line) $this->InstallLog->Write($key . "... [" . $line . "]");

		$result = true;
		$result &= $this->testfunction("imagecreate");
		$result &= $this->testfunction("imagecolorallocate");
		$result &= $this->testfunction("imagefill");
		$result &= $this->testfunction("imageline");
		$result &= $this->testfunction("imagettftext");
		$result &= $this->testfunction("imagejpeg");
		$result &= $this->testfunction("imagedestroy");

		return $result;
	}


	private function testfunction($function)
	{
		$result = function_exists($function);
		$this->InstallLog->Write("testing function [$function]... [" . intval($result) . "]");
		return $result;
	}

}

?>
