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

class B2JSubmitter extends B2JDataPump
{

	public function __construct(&$params, B2JMessageBoard &$messageboard, &$fieldsbuilder)
	{
		parent::__construct($params, $messageboard);

		$this->Name = "B2JSubmitter";

		$this->isvalid = (count($_POST) > 1 && isset($_POST[$this->GetId()]));
        
        $this->fieldsbuilder = $fieldsbuilder;
	}


	public function Show()
	{
		$result = "";
        
        $result .= $this->fieldsbuilder->AcceptTermsText;

		$field = array();
		if ($this->Params->get("copy_to_submitter", 0) == 2 &&
			(bool)$this->fieldsbuilder->senderEmail
		) 
		{

			$field["Display"] = 1;
			$field["Type"] = "checkbox";
			$field["Name"] = JText::_($GLOBALS["COM_NAME"] . "_SEND_ME_A_COPY");
			$field["PostName"] = $this->SafeName("copy_to_submitter" . $this->GetId());
			$field["Value"] = JRequest::getVar($field["PostName"], NULL, 'POST');
			$field["IsValid"] = true;
			$result .= $this->BuildCheckboxField("", $field);
		}

		$this->CreateSpacerLabel();
		$result .= '<div class="control-group b2j-contact-actions">' .
			$this->LabelHtmlCode .
			'<div class="controls">' . PHP_EOL;

		switch ($this->Params->get("submittype"))
		{
			case 1:
	
				$result .= '<input class="btn btn-success" type="submit" style="margin-' . $GLOBALS["right"] . ':32px;" name="' . $this->GetId() . '" value="' . $this->Params->get("submittext") . '"/>' . PHP_EOL;
				break;

			default:
				
				$icon = $this->Params->get("submiticon");
				
				$result .= '<button class="btn btn-success" type="submit" style="margin-' . $GLOBALS["right"] . ':32px;" name="' . $this->GetId() . '">' . PHP_EOL .
					'<span ';
				//if ($icon != "-1") $result .= 'style="background: url(' . JUri::base(true) . '/media/' . $GLOBALS["com_name"] . '/images/submit/' . $icon . ') no-repeat scroll ' . $GLOBALS["left"] . ' top transparent; padding-' . $GLOBALS["left"] . ':20px;" ';
                if ($icon != "-1") $result .= 'style="background: url(' . JUri::base(true) . '/media/' . $GLOBALS["com_name"] . '/images/submit.png) no-repeat ' . $GLOBALS["left"] . ' center transparent; padding-' . $GLOBALS["left"] . ':20px;" ';
				$result .= '>' . PHP_EOL .
					$this->Params->get("submittext") .
					'</span>' . PHP_EOL .
					'</button>' . PHP_EOL;
		}

		if ($this->Params->get("resetbutton"))
		{
			switch ($this->Params->get("resettype"))
			{
				case 1:
					$result .= '<input class="btn btn-danger" type="reset" onClick="ResetB2jControls(this.form);" value="' . $this->Params->get("resettext") . '">' . PHP_EOL;
					break;

				default:
				

					$reseticon = $this->Params->get("reseticon");
					$result .= '<button class="btn btn-danger" type="reset" onClick="ResetB2jControls(this.form);">' . PHP_EOL .
						'<span ';
					//if ($reseticon != "-1") $result .= 'style="background: url(' . JUri::base(true) . '/media/' . $GLOBALS["com_name"] . '/images/reset/' . $reseticon . ') no-repeat scroll ' . $GLOBALS["left"] . ' top transparent; padding-' . $GLOBALS["left"] . ':20px;" ';
                    if ($reseticon != "-1") $result .= 'style="background: url(' . JUri::base(true) . '/media/' . $GLOBALS["com_name"] . '/images/reset.png) no-repeat ' . $GLOBALS["left"] . ' center transparent; padding-' . $GLOBALS["left"] . ':20px;" ';
					$result .= '>' . PHP_EOL .
						$this->Params->get("resettext") .
						'</span>' . PHP_EOL .
						'</button>' . PHP_EOL;
			}
		}
		$result .= '</div>' . 
			'</div>'; 
		$result .= 	'<div class="b2j_clearfix"></div>';
		return $result;
	}


	protected function LoadFields()
	{
	}


	private function BuildCheckboxField($key, &$field)
	{
		
		if ($field['Value'] == JText::_('JYES')) $checked = 'checked=""';
		else $checked = "";

		$this->CreateSpacerLabel();

		$result = '<div class="control-group b2j_copy_to_sender">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<label class="checkbox">' .
			'<input ' .
			'type="checkbox" ' .
			"value=\"" . JText::_('JYES') . "\" " .
			$checked .
			'name="' . $field['PostName'] . '" ' .
			'id="c' . $field['PostName'] . '" ' .
			'/>' .
			$field['Name'] .
			'</label>' .
			'</div>' .
			'</div>';

		return $result;
	}

}

?>
