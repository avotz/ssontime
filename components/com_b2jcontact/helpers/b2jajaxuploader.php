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

class B2JAjaxUploader extends B2JDataPump
{
	public function __construct(&$params, B2JMessageBoard &$messageboard)
	{
		parent::__construct($params, $messageboard);

		$this->Name = "FAjaxFilePump";
		$this->isvalid = true;
	}


	protected function LoadFields()
	{
		
	}


	public function Show()
	{
		if (!(bool)$this->Params->get("uploaddisplay")) return "";

		$id = $this->GetId();

		$action =
			JRoute::_("index.php?option=" . $GLOBALS["com_name"] .
			"&view=loader" .
			"&owner=" . $this->Application->owner .
			"&id=" . $this->Application->oid .
			"&bid=" . $this->Application->bid .
			"&root=none" .
			"&filename=none" .
			"&type=uploader");

		$label = "";
		$span = "";

		if ((bool)$this->Params->get("labelsdisplay"))
		{
			$label =
				'<label class="control-label">' .
					$this->Params->get('upload') .
					'</label>';
		}

		else
		{
			$span =
				'<span class="help-block">' .
					$this->Params->get('upload') .
					'</span>';
		}

		$result =

			'<div class="control-group">' .
				$label .

				'<div class="controls">' .
				$span .

				'<div id="b2jupload_' . $id . '"></div>' . 
				'<span class="help-block">' . JText::_($GLOBALS["COM_NAME"] . '_FILE_SIZE_LIMIT') . " " . $this->human_readable($this->Params->get("uploadmax_file_size") * 1024) . '</span>' .
				'</div>' . 
				"<script language=\"javascript\" type=\"text/javascript\">" .
				"jQuery(document).ready(function () {" .

"if (typeof Joomla == 'undefined')" .
"{" .
"	Joomla = {};" .
"	Joomla.JText =" .
"	{" .
"		strings:{}," .
"		'_':function (key, def)" .
"		{" .
"			return typeof this.strings[key.toUpperCase()] !== 'undefined' ? this.strings[key.toUpperCase()] : def;" .
"		}," .
"		load:function (object)" .
"		{" .
"			for (var key in object)" .
"			{" .
"				this.strings[key.toUpperCase()] = object[key];" .
"			}" .
"			return this;" .
"		}" .
"	};" .
"}" .

	"Joomla.JText.load(" .
		"{" .
			"\"COM_B2JCONTACT_BROWSE_FILES\":'" .  JText::_("COM_B2JCONTACT_BROWSE_FILES") . "'," .
			"\"JCANCEL\":'" . JText::_("JCANCEL") . "'," .
			"\"COM_B2JCONTACT_FAILED\":'" . JText::_("COM_B2JCONTACT_FAILED") . "'," .
			"\"COM_B2JCONTACT_SUCCESS\":'" . JText::_("COM_B2JCONTACT_SUCCESS") . "'," .
			"\"COM_B2JCONTACT_NO_RESULTS_MATCH\":'" . JText::_("COM_B2JCONTACT_NO_RESULTS_MATCH") . "'" .
		"}" .
	");" .
				"CreateUploadButton('b2jupload_$id', '$action', " . $this->Application->b2jcomid . ", " . $this->Application->b2jmoduleid . ", '" . $this->Application->owner . "', " . $this->Application->oid . ");" .
				"});" .
				"</script>" .

				'<noscript>' .
				'<input ' .
				'type="file" ' .
				'name="b2jstdupload"' .
				" />" .
				'</noscript>' .
				"</div>" . PHP_EOL; 

		$jsession = JFactory::getSession();
		$b2jsession = new B2JSession($jsession->getId(), $this->Application->b2jcomid, $this->Application->b2jmoduleid, $this->Application->bid);
		$data = $b2jsession->Load('filelist'); 
		if ($data) $filelist = explode("|", $data);
		else $filelist = array();

	
		$result .= '<div class="control-group">' .
            '<label class="control-label"></label>'.
			'<div class="controls">';


		$result .= '<ul id="uploadlist-' . $this->Application->owner . $this->Application->oid . '" class="qq-upload-list">';
			foreach ($filelist as &$file)
			{
				$result .=
					'<li class="qq-upload-success">' .
						'<span class="qq-upload-file">' . $this->format_filename(substr($file, 14)) . '</span>' .
						'<span class="qq-upload-success-text">' . JTEXT::_($GLOBALS["COM_NAME"] . '_SUCCESS') . '</span>' .
						'</li>';
			}
			$result .= '</ul>' . PHP_EOL;

		$result .= '</div>' .
			'</div>' . PHP_EOL; 

		return $result;
	}


	protected function human_readable($value)
	{
		for ($i = 0; $value >= 1000; ++$i) $value /= 1024;
		$powers = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		return round($value, 1) . " " . $powers[$i];
	}


	protected function format_filename($value)
	{
		if (strlen($value) > 33)
		{
			$value = substr($value, 0, 19) . '...' . substr($value, -13);
		}
		return $value;
	}

}

?>
