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
require_once($inc_dir . '/b2jlanghandler.php');
require_once($inc_dir . '/b2jlogger.php');

class B2JFieldsBuilder extends B2JDataPump
{

	public function __construct(&$params, B2JMessageBoard &$messageboard)
	{
		parent::__construct($params, $messageboard);

		$this->ValidateEmail();		
		if (!isset($GLOBALS[$GLOBALS["ext_name"] . '_js_loaded']))
		{


			$min = JFactory::getConfig()->get("debug") ? "" : ".min";

	
			JHtml::_("jquery.framework");

			$document = JFactory::getDocument();


			$document->addScript(JRoute::_("index.php?option=" . $GLOBALS["com_name"] . "&amp;view=loader&amp;owner=" . $this->Application->owner . "&amp;id=" . $this->Application->oid . "&amp;type=js&amp;filename=jtext"));
	
			$document->addScript(JUri::base(true) . "/components/" . $GLOBALS["com_name"] . "/js/fileuploader" . $min . ".js");

			
			$uncompressed = JFactory::getConfig()->get("debug") ? "-uncompressed" : "";
			$document->addScript(JUri::base(true) . "/media/system/js/core" . $uncompressed . ".js");
			$document->addScript(JUri::base(true) . "/media/jui/js/chosen.jquery" . $min . ".js");
			$document->addScript(JUri::base(true) . "/components/" . $GLOBALS["com_name"] . "/js/glDatePicker.js");

			$GLOBALS[$GLOBALS["ext_name"] . '_js_loaded'] = true;
		}

		$this->isvalid = intval($this->ValidateForm()); 

		$lang_handler = new B2JLangHandler();
		if ($lang_handler->HasMessages())
		{
			$messageboard->Append($lang_handler->GetMessages(), B2JMessageBoard::warning);
		}
	}
    
    public function PrepareDynamicFields(){
        $dynamicfields_string = $this->Params->get('dynamicfields');
        $itemgroups_string = $this->Params->get('itemgroups');
        $dynamicfields = json_decode($dynamicfields_string);
        $itemgroups = json_decode($itemgroups_string);
        if($dynamicfields){
            $dynamicfieldsGroup = array();
            foreach($itemgroups as $key => $group){
                    $dynamicfieldsGroup[$key] = $group[0];	
            }
            $fieldsByOrder = array();
            foreach ($dynamicfields as $key => $item) {
                $fieldsByOrder[$item[0]->b2jFieldOrdering] = $item[0];
            }
            uasort($fieldsByOrder, array($this, "sort_dynamicfields"));
            $fieldsByGroup = array();
            foreach ($fieldsByOrder as $key => $item) {
                $item->IsValid = true;
                $fieldsByGroup[$item->b2jFieldGroup][0] = $dynamicfieldsGroup[$item->b2jFieldGroup];
                $fieldsByGroup[$item->b2jFieldGroup][1][] = $item;
            }
            uasort($fieldsByGroup, array($this, "sort_dynamicgroups"));
            $this->DynamicFields = $fieldsByGroup;
        }
    }
    
    function sort_dynamicfields($a, $b)
    {
        if ($a->b2jFieldGroup > $b->b2jFieldGroup){
            return 1;
        } else if ($a->b2jFieldGroup < $b->b2jFieldGroup){
            return -1;
        } else if ($a->b2jFieldOrdering > $b->b2jFieldOrdering){
            return 1;
        }
        return -1;
    }
    
    function sort_dynamicgroups($a, $b)
    {
        return $a[0]->ordering > $b[0]->ordering;
    }

	public function count_fields(&$fields, $type)
	{

		$result = 0;
		$type_len = strlen($type);
		foreach ($fields as $fname => $fvalue)
		{
			if (
				substr($fname, 0, $type_len) == $type && 
				substr($fname, strlen($fname) - 7) == "display" 
			)
				++$result;
		}
		return $result;
	}

	public function Show()
	{
		$result = "";
		uasort($this->Fields, "B2JSort_fields");
        
        $numItems = count($this->Fields);
        $it = 0;
        
		foreach ($this->Fields as $key => $field)
		{
            $it++;
			switch ($field['Type'])
			{
				case 'customhtml':
                    $this->AcceptTermsText = $this->BuildCustomHtmlField($key, $field);
                    if($it != $numItems) {
                        $result .= $this->AcceptTermsText;
                    }
					break;
				case 'sender':
				case 'text':
					$result .= $this->BuildTextField($key, $field); 
					break;
				case 'dropdown':
					$result .= $this->BuildDropdownField($key, $field); 
					break;
				case 'textarea':
					$result .= $this->BuildTextareaField($key, $field);
					break;
				case 'checkbox':
					$result .= $this->BuildCheckboxField($key, $field);
					break;
				case 'date':
					$result .= $this->BuildDateField($key, $field);
					break;
			}

			if (!$field["IsValid"]) $this->MessageBoard->Add(JText::sprintf($GLOBALS["COM_NAME"] . '_ERR_INVALID_VALUE', $field["Name"]), B2JMessageBoard::error);
		}
        
        //dynamic fields and groups
        if (isset($this->DynamicFields) and count($this->DynamicFields) > 0){
            $result .= "<div class='b2j_clearfix'></div>";
            foreach ($this->DynamicFields as $key => $field)
            {
                if (!$field[0]->state){
                    continue;
                }
                $result .= "<div class='b2j-contact-group-class ".$field[0]->class."'>";
                foreach ($field[1] as $dynamicfield){
                    if(!$dynamicfield->b2jFieldState || $dynamicfield->b2jFieldGroup == 0){
                        continue;
                    }
                 	if(isset($dynamicfield->b2jDefaultValue)){
                    	$dynamicfield->b2jDefaultValue = htmlspecialchars ( $dynamicfield->b2jDefaultValue );
                	}
                    $dynamicfield->b2jFieldName = htmlspecialchars ( $dynamicfield->b2jFieldName );
                    $dynamicfield->b2jFieldValue = htmlspecialchars ( $dynamicfield->b2jFieldValue );
                    switch ($dynamicfield->type){
                        case 'b2jDynamicText':
                            $result .= $this->BuildDynamicTextField($dynamicfield); 
                            break;
						case 'b2jDynamicDropdown':
							$result .= $this->BuildDynamicDropdownField($dynamicfield); 
                            break;
						case 'b2jDynamicTextarea':
							$result .= $this->BuildDynamicTextareaField($dynamicfield); 
                            break;
						case 'b2jDynamicCheckbox':
							$result .= $this->BuildDynamicCheckboxField($dynamicfield); 
                            break;
						case 'b2jDynamicDate':
							$result .= $this->BuildDynamicDateField($dynamicfield); 
                            break;
                        case 'b2jDynamicLabel':
							$result .= $this->BuildDynamicLabelField($dynamicfield); 
                            break;    
						default:
							$html .= "field type error"; 	
							break;		
					}
                    if (!$dynamicfield->IsValid) {
                    	$this->MessageBoard->Add(JText::sprintf($GLOBALS["COM_NAME"] . '_ERR_INVALID_VALUE', $dynamicfield->b2jFieldName), B2JMessageBoard::error);
                  		$this->isvalid = false;
                    }
                }
                $result .= "<div class='b2j_clearfix'></div>";
                $result .= "</div>";
            }
            //$result .= $customResult;
        }
		return $result;
	}

	protected function LoadFields()
	{
		$fields = $this->Params->toArray();
		$text_count = $this->count_fields($fields, "text");
		$dropdown_count = $this->count_fields($fields, "dropdown");
		$textarea_count = $this->count_fields($fields, "textarea");
		$checkbox_count = $this->count_fields($fields, "checkbox");
		$date_count     = $this->count_fields($fields, "date");
        
        //dynamic fields
        foreach ($this->DynamicFields as $key => &$field)
            {
                if (!$field[0]->state){
                    continue;
                }
                foreach ($field[1] as &$dynamicfield){
                    if(!$dynamicfield->b2jFieldState || $dynamicfield->b2jFieldGroup == 0){
                        continue;
                    }
                    $this->LoadDynamicField($dynamicfield);
                }
            } 
		// Loads parameters and $_POST data
		$this->LoadField("labels", "");
		$this->LoadField("customhtml", 0);
		for ($n = 0; $n < 2; ++$n) $this->LoadField("sender", $n);
		for ($n = 0; $n < $text_count; ++$n) $this->LoadField("text", $n);
		for ($n = 0; $n < $dropdown_count; ++$n) $this->LoadField("dropdown", $n);
		for ($n = 0; $n < $textarea_count; ++$n) $this->LoadField("textarea", $n);
		for ($n = 0; $n < $checkbox_count; ++$n) $this->LoadField("checkbox", $n);
		for ($n = 0; $n < $date_count; ++$n) $this->LoadField("date", $n);
		$this->LoadField("customhtml", 1);
	}

	protected function LoadField($type, $number) 
	{
		
		$name = $type . (string)$number; 
		
		if (!parent::LoadField($type, $name)) return false;

		
		$this->Fields[$name]['Value'] = htmlspecialchars(JRequest::getVar($this->Fields[$name]['PostName'], NULL, 'POST'));


		if ($this->Fields[$name]['Value'] == $this->Fields[$name]['Name']) 
		{

			$this->Fields[$name]['Value'] = "";
		}

		
		
		$this->Fields[$name]['IsValid'] = intval($this->ValidateField($this->Fields[$name]['Value'], $this->Fields[$name]['Display']));

		
		if ($type == "checkbox" && $this->Fields[$name]['Value'] == "") $this->Fields[$name]['Value'] = JText::_('JNO');

		return true;
	}
    
    protected function LoadDynamicField(&$field) 
	{


		$post_value = htmlspecialchars(JRequest::getVar('dynamic_'.$field->b2jFieldKey, NULL, 'POST'));
        $field->b2jFieldValue = $post_value;
        
        if ($field->b2jFieldName == $field->b2jFieldValue || $field->b2jDefaultValue == $field->b2jFieldValue) 
		{
			$post_value = "";
		}
		
		$field->IsValid = intval($this->ValidateField($post_value, $field->b2jFieldState));

		return true;
	}

	private function BuildCustomHtmlField($key, &$field)
	{
	
		if (empty($field['Name'])) return "";

		$result = '<div class="control-group">';
		if($this->Params->get("labelsdisplay") == 1){
            $result .= '<label class="control-label"></label>';
        }  
		$result .= '<div class="controls">' .
			'<div>' .
			$field['Name'] .
			"</div>" .
			"</div>" .
			"</div>";

		return $result;
	}
	private function BuildDynamicLabelField(&$field)
	{
	
		if (empty($field->b2jFieldName)) return "";

		$result = '<div class="control-group' . $this->DynamicTextStyleByValidation($field) . '">' .
			'<div class="controls">' .
			'<div>' .
			$field->b2jFieldName .
			"</div>" .
			"</div>" .
			"</div>";

		return $result;
	}
	private function BuildTextField($key, &$field)
	{


		$this->CreateStandardLabel($field);

		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<input ' .
			'type="text" ' .
			'value="' . $this->FieldValue . '" ' .
			'title="' . $field['Name'] . '" ' .
			'name="' . $field['PostName'] . '" ' .
			$this->JSCode .
			'/>' .
			$this->DescriptionByValidation($field) . 
			'</div>' . 
			'</div>'; 

		return $result;
	}
    
    private function BuildDynamicTextField(&$field)
	{
		$this->CreateDynamicStandardLabel($field);

		$result = '<div class="control-group' . $this->DynamicTextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<input ' .
			'type="text" ' .
			'value="' . $field->b2jFieldValue . '" ' .
			'title="' . $field->b2jFieldName . '" ' .
			'name="dynamic_' . $field->b2jFieldKey . '" ' .
			$this->JSCode .
			'/>' .
			$this->DynamicDescriptionByValidation($field) . 
			'</div>' . 
			'</div>'; 

		return $result;
	}

	private function BuildDropdownField($key, &$field)
	{
		$this->CreateStandardLabel($field);

		$placeholder = $this->Params->get("labelsdisplay") ? " " : $field['Name'];
		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<select ' .
			'class="b2j_select" ' .
			'data-placeholder="' . $placeholder . '"' .
			'name="' . $field['PostName'] . '" ' .
			'>';

		$result .= '<option value=""></option>';

		$options = explode(",", $field['Values']);
		foreach ($options as $option)
		{
			$result .= "<option value=\"" . $option . "\"";
			if ($field['Value'] === $option && !empty($option))
			{
				$result .= " selected ";
			}
			$result .= ">" . $option . "</option>";
		}
		$result .= "</select>" .
			$this->DescriptionByValidation($field) .
			'</div>' . 
			"</div>"; 

		return $result;
	}
    
    private function BuildDynamicDropdownField(&$field)
	{
		$this->CreateDynamicStandardLabel($field);
        
        $def_value = " ";
        if (isset($field->b2jDefaultValue) and $field->b2jDefaultValue!=""){
            $def_value = $field->b2jDefaultValue;
        }

		$placeholder = $this->Params->get("labelsdisplay") ? $def_value : $field->b2jFieldName;
		$result = '<div class="control-group' . $this->DynamicTextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<select ' .
			'class="b2j_select" ' .
			'data-placeholder="' . $placeholder . '"' .
			'name="dynamic_' . $field->b2jFieldKey . '" ' .
			'>';


		$result .= '<option value=""></option>';
    
		$options = explode(",", $field->b2jFieldItems);
		foreach ($options as $option)
		{
            $option = trim($option);
			$result .= "<option value=\"" . $option . "\"";
			if ($field->b2jFieldValue == $option && !empty($option))
			{
				$result .= " selected ";
			}
			$result .= ">" . $option . "</option>";
		}
		$result .= "</select>" .
			$this->DynamicDescriptionByValidation($field) .
			'</div>' . 
			"</div>"; 

		return $result;
	}


	private function BuildCheckboxField($key, &$field)
	{
		if ($field['Value'] == JText::_('JYES')) $checked = 'checked=""';
		else $checked = "";

		$this->CreateSpacerLabel();

		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' .
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
			$this->AdditionalDescription($field['Display']) . 
			$field['Name'] .
			$this->DescriptionByValidation($field) . 
			'</label>' .
			'</div>' .
			'</div>';

		return $result;
	}
    private function BuildDynamicCheckboxField(&$field)
	{
        if (!$this->Submitted and isset($field->b2jDefaultValue) and $field->b2jDefaultValue == 1 ){
            $field->b2jFieldValue = JText::_('JYES');
        }
		if ($field->b2jFieldValue == JText::_('JYES')) $checked = 'checked=""';
		else $checked = "";

		$this->CreateSpacerLabel();
		$result = '<div class="control-group' . $this->DynamicTextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<label class="checkbox">' .
			'<input ' .
			'type="checkbox" ' .
			"value=\"" . JText::_('JYES') . "\" " .
			$checked .
			'name="dynamic_' . $field->b2jFieldKey . '" ' .
			'id="dynamic_c' . $field->b2jFieldKey . '" ' .
			'/>' .
			$this->AdditionalDescription($field->b2jFieldState) . 
			$field->b2jFieldName .
			$this->DynamicDescriptionByValidation($field) . 
			'</label>' .
			'</div>' .
			'</div>';

		return $result;
	}

	private function BuildDateField($key, &$field)
	{

		$this->CreateStandardLabel($field);

		$result = '<script type="text/javascript">
	var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
    jQuery(window).load(function()
    {
        jQuery("#' . $key.'_'.$this->Application->owner . '").glDatePicker({
        	cssName: "flatwhite",
        	monthNames: monthNames,
        	allowMonthSelect: false,
        	allowYearSelect: false,
        	showAlways: false,
        	hideOnClick: true,
        	onClick: function(target, cell, date, data) {
		        target.val(date.getDate()  + " - " +
		                    monthNames[date.getMonth()] + " - " +
		        			date.getFullYear()
		                    );
		    }
        });
    });
</script>'
.'<div class="control-group' . $this->TextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<input ' .
			'type="text" ' .
			'value="' . $this->FieldValue . '" ' .
			'title="' . $field['Name'] . '" ' .
			'name="' . $field['PostName'] . '" ' .
			'id="' . $key.'_'.$this->Application->owner . '" ' .
			$this->JSCode .
			'/>' .
			$this->DescriptionByValidation($field) .
			'</div>' . 
			'</div>';

		return $result;
	}
    
    private function BuildDynamicDateField(&$field)
	{

		$this->CreateDynamicStandardLabel($field);

		$result = '<script type="text/javascript">
	var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
    jQuery(window).load(function()
    {
        jQuery("#' .$field->b2jFieldKey. '_'.$this->Application->owner . '").glDatePicker({
        	cssName: "flatwhite",
        	monthNames: monthNames,
        	allowMonthSelect: false,
        	allowYearSelect: false,
        	showAlways: false,
        	hideOnClick: true,
        	onClick: function(target, cell, date, data) {
		        target.val(date.getDate()  + " - " +
		                    monthNames[date.getMonth()] + " - " +
		        			date.getFullYear()
		                    );
		    }
        });
    });
</script>'
.'<div class="control-group' . $this->DynamicTextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			'<input ' .
			'type="text" ' .
			'value="' . $field->b2jFieldValue . '" ' .
			'title="' . $field->b2jFieldName . '" ' .
			'name="dynamic_' . $field->b2jFieldKey . '" ' .
			'id="' . $field->b2jFieldKey . '_'.$this->Application->owner . '" ' .
			$this->JSCode .
			'/>' .
			$this->DynamicDescriptionByValidation($field) .
			'</div>' . 
			'</div>';

		return $result;
	}

	private function BuildTextareaField($key, &$field)
	{
		$this->CreateStandardLabel($field);

		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			"<textarea " .
			'rows="10" ' .
			'cols="30" ' .
			'name="' . $field['PostName'] . '" ' .
			'title="' . $field['Name'] . '" ' .
			$this->JSCode .
			">" .
			$this->FieldValue . 
			"</textarea>" .
			$this->DescriptionByValidation($field) .
			'</div>' . 
			'</div>'; 

		return $result;

	}
    
    private function BuildDynamicTextareaField(&$field)
	{
		$this->CreateDynamicStandardLabel($field);

		$result = '<div class="control-group' . $this->DynamicTextStyleByValidation($field) . '">' .
			$this->LabelHtmlCode .
			'<div class="controls">' .
			"<textarea " .
			'rows="10" ' .
			'cols="30" ' .
			'name="dynamic_' . $field->b2jFieldKey . '" ' .
			'title="' . $field->b2jFieldName . '" ' .
			$this->JSCode .
			">" .
			$field->b2jFieldValue . 
			"</textarea>" .
			$this->DynamicDescriptionByValidation($field) .
			'</div>' . 
			'</div>'; 

		return $result;

	}

	function DescriptionByValidation(&$field)
	{
		return $field['IsValid'] ? "" : (" <span class=\"asterisk\"></span>");
	}
    
    function DynamicDescriptionByValidation(&$field)
	{
		return $field->IsValid ? "" : (" <span class=\"asterisk\"></span>");
	}



	function CheckboxStyleByValidation(&$field)
	{
		if (!$this->Submitted) return "b2jcheckbox";

		return $field['IsValid'] ? "validcheckbox" : "invalidcheckbox";
	}


	protected function TextStyleByValidation(&$field)
	{
		
		if (!$this->Submitted) return "";
		
		return $field['IsValid'] ? " success" : " error";
	}
    
    protected function DynamicTextStyleByValidation(&$field)
	{
		
		if (!$this->Submitted) return "";
		
		return $field->IsValid ? " success" : " error";
	}


	function ValidateForm()
	{
		$result = true;

		
		$result &= $this->ValidateGroup("sender");
		$result &= $this->ValidateGroup("text");
		$result &= $this->ValidateGroup("dropdown");
		$result &= $this->ValidateGroup("checkbox");
		$result &= $this->ValidateGroup("textarea");
		$result &= $this->ValidateGroup("date");

		return $result;
	}


	function ValidateGroup($family)
	{
		$result = true;

		for ($l = 0; $l < 10; ++$l)
		{
			if (isset($this->Fields[$family . $l]) && $this->Fields[$family . $l]['Display'])
			{
				$result &= $this->Fields[$family . $l]['IsValid'];
			}
		}

		return $result;
	}


	function ValidateField($fieldvalue, $fieldtype)
	{
		return !($this->Submitted && ($fieldtype == 2) && empty($fieldvalue));
	}


	function ValidateEmail()
	{
		
		if (!isset($_POST[$this->GetId()])) return true;

		
		if (!isset($this->Fields['sender1'])) return true;

	
		if (empty($this->Fields['sender1']['Value']) && $this->Fields['sender1']['Display'] == 1) return true;

		if (!isset($this->Fields['sender1']['Value'])) return false;

		
		$this->Fields['sender1']['IsValid'] &= (preg_match('/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/', strtolower($this->Fields['sender1']['Value'])) == 1);

		
		$db = JFactory::getDBO();
		$sql = "SELECT value FROM #__" . $GLOBALS["ext_name"] . "_settings WHERE name = 'dns';";
		$db->setQuery($sql);
		$method = $db->loadResult();
		if ($method)
		{
			$this->$method();
		}
	}


	function dns_check()
	{
		
		if (empty($this->Fields['sender1']['Value'])) return;

		$parts = explode("@", $this->Fields['sender1']['Value']);
		$domain = array_pop($parts);
		if (!empty($domain))
			$this->Fields['sender1']['IsValid'] &= checkdnsrr($domain, "MX");
	}


	function disabled()
	{
		return true;
	}

}


function B2JSort_fields($a, $b)
{
	return $a["Order"] - $b["Order"];
}

class fieldsbuilderCheckEnvironment
{
	protected $InstallLog;


	public function __construct()
	{
		$this->InstallLog = new B2JLogger("fieldsbuilder", "install");
		$this->InstallLog->Write("--- Determining if this system is able to query DNS records ---");

		$value = $this->test_function("checkdnsrr");

		$db = JFactory::getDBO();
		$sql = "REPLACE INTO #__" . $GLOBALS["ext_name"] . "_settings (name, value) VALUES ('dns', '$value');";
		$db->setQuery($sql);
		$result = $db->query();

		$this->InstallLog->Write("--- Method choosen to query DNS records is [$value] ---");


		$params = JComponentHelper::getComponent("com_b2jcontact")->params->toObject();

	
		$this->test_addresses($params);

		
		$query = $db->getQuery(true);
		$query->update($db->quoteName("#__extensions"));
		$query->set($db->quoteName("params") . " = " . $db->quote(json_encode($params)));
		
		$query->where($db->quoteName("element") . " = " . $db->quote("com_b2jcontact"));
		$query->where($db->quoteName("client_id") . " = " . $db->quote("1"));
		$db->setQuery($query);
		$result = $db->query();


		return $result;
	}


	private function test_function($fname)
	{
		if (!function_exists($fname))
		{
			$this->InstallLog->Write("$fname function doesn't exist.");
			return "disabled";
		}
		$this->InstallLog->Write("$fname function found. Let's see if it works.");

		$result = $fname("b2j.ra.it", "MX");
		$this->InstallLog->Write("testing function [$fname]... [" . intval($result) . "]");
		return $result ? "dns_check" : "disabled";
	}


	private function test_addresses(&$params)
	{
		isset($params->adminemailfrom) or $params->adminemailfrom = new stdClass();
		isset($params->adminemailreplyto) or $params->adminemailreplyto = new stdClass();
		isset($params->submitteremailfrom) or $params->submitteremailfrom = new stdClass();
		isset($params->submitteremailreplyto) or $params->submitteremailreplyto = new stdClass();

		$params->adminemailfrom->select = "admin";
		$params->adminemailreplyto->select = "submitter";

		$params->submitteremailfrom->select = "admin";
		$params->submitteremailreplyto->select = "admin";

		$application = JFactory::getApplication();
		
		if ($application->getCfg("mailer") == "smtp" && (bool)$application->getCfg("smtpauth") && strpos($application->getCfg("smtpuser"), "@") !== false)
		{
			$params->adminemailfrom->select = "custom";
			$params->adminemailfrom->name = $application->getCfg("fromname");
			$params->adminemailfrom->email = $application->getCfg("smtpuser");

			$params->submitteremailfrom->select = "custom";
			$params->submitteremailfrom->name = $application->getCfg("fromname");
			$params->submitteremailfrom->email = $application->getCfg("smtpuser");
		}
	}

}

?>