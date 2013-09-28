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

abstract class B2jDataPump
{
	public $Params; 
	public $Application;
	public $Name;
	public $Fields = array();
    public $DynamicFields = array();
    public $AcceptTermsText;
	public $Style = array();
	protected $Submitted;
	protected $Logger;
	protected $isvalid;
	protected $MessageBoard;

	protected $FieldValue;
	protected $LabelHtmlCode;
	protected $JSCode;


	abstract protected function LoadFields();


	public function __construct(&$params, B2JMessageBoard &$messageboard)
	{
		$this->Params = & $params;
		$this->MessageBoard = & $messageboard;
		$this->Application = JFactory::getApplication();
		$this->Submitted = (bool)count($_POST) && isset($_POST[$this->GetId()]);
        $this->PrepareDynamicFields();
		$this->LoadFields();
	}
    
    public function PrepareDynamicFields(){
    }

	public function IsValid()
	{
		return $this->isvalid;
	}


	public function js_load($js_name, $where, $how, &$placeholders = array(), &$values = array())
	{
		
		$action = $where * 1 + $how * 10;

		$js_local_name = JPATH_ROOT . "/components/" . $GLOBALS["com_name"] . "/js/" . $js_name;
		$js_http_name = JUri::base(true) . "/components/" . $GLOBALS["com_name"] . "/js/" . $js_name;

		$document = JFactory::getDocument();

		if (!$how)
		{
			$handle = @fopen($js_local_name, 'r');
			
			$js_source = "\n//<![CDATA[\n" . fread($handle, filesize($js_local_name)) . "\n//]]>\n";
			
			fclose($handle);
			
			$js_source = str_replace($placeholders, $values, $js_source);
		}


		switch ($action)
		{
			case 0: 
				return "\n" . '<script type="text/javascript">' . $js_source . "</script>\n";
			case 1: 
				$document->addScriptDeclaration($js_source);
				break;
			case 10: 
				return "\n" . '<script type="text/javascript" src="' . $js_http_name . '"></script>' . "\n";
			case 11: 
				$document->addScript($js_http_name);
		}

		return "";
	}


	protected function LoadField($type, $name) 
	{
		$enabled = intval($this->Params->get($name . "display", "0"));
		

		if (!$enabled) return false;

		$this->Fields[$name]["Display"] = intval($this->Params->get($name . "display", "0"));
		$this->Fields[$name]["Type"] = $type;
		$this->Fields[$name]["Name"] = $this->Params->get($name, "");
		$this->Fields[$name]["PostName"] = $this->SafeName($this->Fields[$name]["Name"] . $this->Application->b2jcomid . $this->Application->b2jmoduleid);
		$this->Fields[$name]["Values"] = $this->Params->get($name . "values", "");
		$this->Fields[$name]["Width"] = intval($this->Params->get($type . "width", ""));
		$this->Fields[$name]["Height"] = intval($this->Params->get($type . "height", ""));
		$this->Fields[$name]["Unit"] = $this->Params->get($type . "unit", "");
		$this->Fields[$name]["Order"] = intval($this->Params->get($name . "order", 0));
		return true;
	}


	protected function MakeText($key)
	{
		$text = $this->Params->get($key, "");
		if (empty($text)) return "";
		return
			'<div class="b2jmessage" style="clear:both;">' .
			$text .
			'</div>';
	}


	protected function AdditionalDescription($display)
	{
		return ($display == 2) ? ("<span class=\"required\"></span>") : "";
	}


	protected function SafeName($name)
	{

		return "_" . md5($name);
	}


	protected function GetComponentId()
	{
		global $app;
		if (strpos($app->scope, "com_") !== 0) return 0;

		$wholemenu = $this->Application->getMenu();
		$targetmenu = $wholemenu->getActive();
		return $targetmenu->id;
	}


	protected function GetId($separator = "_")
	{
		$id = substr($this->Application->scope, 0, 1);
		switch ($id)
		{
			case "c":
				$wholemenu = $this->Application->getMenu();
				$activemenu = $wholemenu->getActive();
				$id .= "id" . $separator . $activemenu->id;
				break;

			case "m":
				$id .= "id" . $separator . $this->Application->b2jmoduleid;
				break;

			default:
				$id = "";
		}

		return $id;
	}


	protected function CreateStandardLabel($field)
	{

		if ((bool)$this->Params->get("labelsdisplay"))
		{
			$this->FieldValue = $field["Value"];
			$this->LabelHtmlCode = '<label class="control-label">' . $field["Name"] . $this->AdditionalDescription($field["Display"]) . '</label>';
			$this->JSCode = "";
		}
		else
		{

			$this->FieldValue = $field["Value"] ? $field["Value"] : $field["Name"];
			$this->LabelHtmlCode = "";
			$this->JSCode = "onfocus=\"if(this.value==this.title) this.value='';\" onblur=\"if(this.value=='') this.value=this.title;\" ";
		}
	}
    
    protected function CreateDynamicStandardLabel($field)
	{

		if ((bool)$this->Params->get("labelsdisplay"))
		{
            if (isset($field->b2jDefaultValue) and $field->b2jDefaultValue!="" and $field->type != 'b2jDynamicDropdown' and $field->type != 'b2jDynamicCheckbox'){
                $this->FieldValue = $field->b2jFieldValue;
                $this->LabelHtmlCode = '<label class="control-label">' . $field->b2jFieldName . $this->AdditionalDescription($field->b2jFieldState) . '</label>';
                $this->JSCode = "onfocus=\"if(this.value=='".$field->b2jDefaultValue."') this.value='';\" onblur=\"if(this.value=='') this.value='".$field->b2jDefaultValue."';\" ";
            } else {
                $this->FieldValue = $field->b2jFieldValue;
                $this->LabelHtmlCode = '<label class="control-label">' . $field->b2jFieldName . $this->AdditionalDescription($field->b2jFieldState) . '</label>';
                $this->JSCode = "";
            }
		}
		else
		{

			$this->FieldValue = $field->b2jFieldValue ? $field->b2jFieldValue : $field->b2jFieldName;
			$this->LabelHtmlCode = "";
			$this->JSCode = "onfocus=\"if(this.value==this.title) this.value='';\" onblur=\"if(this.value=='') this.value=this.title;\" ";
		}
	}


	protected function CreateSpacerLabel()
	{

		$layout = $this->Params->get("form_layout", "extended");

		if ((bool)$this->Params->get("labelsdisplay") && ($layout == "compact" || $layout == "extended"))
		{
			$this->LabelHtmlCode = '<label class="control-label">&nbsp;</label>';
		}
		else
		{
			$this->LabelHtmlCode = "";
		}

	}

}

?>
