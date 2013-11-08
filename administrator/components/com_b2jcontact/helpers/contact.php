<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class ContactHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	$vName	The name of the active view.
	 *
	 * @return  void
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 * @param   integer  The contact ID.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($contactId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($contactId))
		{
			$assetName = 'com_b2jcontact';
			$level = 'component';
		}

		$actions = JAccess::getActions('com_b2jcontact', $level);

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
		
		return $result;
	}
	public static function getFieldForm($type = false, $defaultEmail = false){
		$response = array();	
		
		if(!$type or $type == 'none'){
			$form ='<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" for="b2jNewFieldType">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select id="b2jNewFieldType" onChange="getFormByType(this);" name="b2jNewFieldType">'.
									'<option value="none">--- '.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_SELECT_TYPE').' ---</option>'.
									'<option value="b2jDynamicText">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_TEXT').'</option>'.
									'<option value="b2jDynamicDropdown">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_DROPDOWN').'</option>'.
									'<option value="b2jDynamicTextarea">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_TEXT_AREA').'</option>'.
									'<option value="b2jDynamicEmail">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_EMAIL').'</option>'.
									'<option value="b2jDynamicCheckbox">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_CHECK_BOX').'</option>'.
									'<option value="b2jDynamicDate">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_DATE').'</option>'.
									'<option value="b2jDynamicLabel">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_LABEL').'</option>'.
								'</select>'.
							'</div>'.
						'</div>';
			$response['type'] = 'type';						
		}else{
			$form ='';
			switch ($type) {
			    case 'b2jDynamicText':
			        $response['type'] = 'b2jDynamicText';
			        $form .= self::getGeneralFieldName();
			        $form .= self::getGeneralFieldDefault();
					$form .= self::getGeneralFieldState();
			        break;
			    case 'b2jDynamicDropdown':
			        $form .= self::getDropdownItem();
			        $form .= self::getGeneralFieldName();
			        $form .= self::getGeneralFieldDefault();
					$form .= self::getGeneralFieldState();
			        $response['type'] = 'b2jDynamicDropdown';
			        break; 
			    case 'b2jDynamicTextarea':
			        $response['type'] = 'b2jDynamicTextarea';
			        $form .= self::getGeneralFieldName();
			        $form .= self::getGeneralFieldTextareaDefault();
					$form .= self::getGeneralFieldState();
			        break;
			    case 'b2jDynamicEmail':
			        $response['type'] = 'b2jDynamicEmail';
			        $form .= self::getGeneralFieldName();
			        $form .= self::getGeneralFieldDefault();
			        $form .= self::getGeneralFieldEmailDefault($defaultEmail);
					$form .= self::getGeneralFieldState();
			        break;    
			    case 'b2jDynamicCheckbox':
			        $response['type'] = 'b2jDynamicCheckbox';
			        $form .= self::getGeneralFieldName();
			        $form .= self::getGeneralFieldCheckboxDefault();
					$form .= self::getGeneralFieldState();
			        break;
			    case 'b2jDynamicDate':
			        $response['type'] = 'b2jDynamicDate';
			        $form .= self::getGeneralFieldName();
			        $form .= self::getGeneralFieldDefault();
					$form .= self::getGeneralFieldState();
			        break;
			    case 'b2jDynamicLabel':
			        $response['type'] = 'b2jDynamicLabel';
			        $form .= self::getGeneralFieldName();
					$form .= self::getGeneralFieldState('label');
			        break;               
			}	
					
			$response['formHtmlButtons'] = self::getFormButttons();
		}
 		$response['formHtml'] = $form;
 		return $response;
	}
	public static function getDropdownItem(){
		$dropdownField = '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" for="b2jNewFieldItems">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_ITEMS_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<textarea rows="3" cols="30" id="b2jNewFieldItems" placeholder="'.JText::_('COM_B2JCONTACT_DYNAMIC_ITEMS_PLACEHOLDER').'" name="b2jNewFieldItems" class=""></textarea>'.
							'</div>'.
						'</div>';
		return $dropdownField;
	}
	public static function getGeneralFieldTextareaDefault(){
		$generalFieldName = '<div class="control-group">'.
							  	'<div class="control-label">'.
									'<label title="" for="b2jNewFieldDefault">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
								'</div>'.
								'<div class="controls">'.
									'<input id="b2jNewFieldDefault" type="text" value="" size="26" name="b2jNewFieldDefault" class="">'.
								'</div>'.
							'</div>';
		return $generalFieldName;	
	}
	public static function getGeneralFieldCheckboxDefault(){
		$generalFieldName = '<div class="control-group">'.
							  	'<div class="control-label">'.
									'<label title="" for="b2jNewFieldDefault">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_DEFAULT_LBL').' *</label>'.
								'</div>'.
								'<div class="controls">'.
									'<select id="b2jNewFieldDefault" >'.
										 '<option value="0">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_NOT_SELECT_LBL').'</option>'.
										 '<option value="1" selected="selected">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_SELECT_LBL').'</option>'.
									'</select>'.
								'</div>'.
							'</div>';
		return $generalFieldName;	
	}
	public static function getGeneralFieldEmailDefault($default = false){

		if($default && $default != "false"){
			$disabled = "";	
		}else{
			$disabled = 'disabled="disabled"';
		}

		$generalFieldName = '<div class="control-group">'.
							  	'<div class="control-label">'.
									'<label class="hasTooltip" title="'.JText::_('COM_B2JCONTACT_DEFAULT_EMAIL').'" for="b2jNewFieldRadio">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_DEFAULT_LBL').' *</label>'.
								'</div>'.
								'<div class="controls b2jDefaultEmailCon">'.
									'<input type="radio" name="b2jNewFieldRadio" '.$disabled.' value="1"><label>'.JText::_('COM_B2JCONTACT_YES').'</label>'.
									'<input type="radio" name="b2jNewFieldRadio" '.$disabled.' value="0" checked><label>'.JText::_('COM_B2JCONTACT_NO').'</label>'.
								'</div>'.
							'</div>';
		return $generalFieldName;	
	}
	public static function getGeneralFieldDefault(){
		$generalFieldName = '<div class="control-group">'.
							  	'<div class="control-label">'.
									'<label title="" for="b2jNewFieldDefault">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
								'</div>'.
								'<div class="controls">'.
									'<input id="b2jNewFieldDefault" type="text" value="" size="26" name="b2jNewFieldDefault" class="">'.
								'</div>'.
							'</div>';
		return $generalFieldName;	
	}
	public static function getGeneralFieldName(){
		$generalFieldName = '<div class="control-group">'.
							  	'<div class="control-label">'.
									'<label title="" for="b2jNewFieldName">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
								'</div>'.
								'<div class="controls">'.
									'<input id="b2jNewFieldName" type="text" value="" size="26" name="b2jNewFieldName" class="">'.
								'</div>'.
							'</div>';
		return $generalFieldName;	
	}
	public static function getGeneralFieldState($type = false){
		$generalFieldState = '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" for="b2jNewFieldState">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">';
		if($type == 'label'){
			$generalFieldState .= '<select id="b2jNewFieldState" name="b2jNewFieldState" >'.
									'<option value="0">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL').'</option>'.
									'<option selected="selected" value="1">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_ENABLE_LBL').'</option>'.
								'</select>';
	
		}else{
			$generalFieldState .= '<select id="b2jNewFieldState" name="b2jNewFieldState" >'.
									'<option value="0">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL').'</option>'.
									'<option selected="selected" value="1">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL').'</option>'.
									'<option value="2">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL').'</option>'.
								'</select>';
		}					
		
		$generalFieldState .= '</div>'.
						'</div>';
		return $generalFieldState;	
	}
	public static function getFormButttons(){
		$buttons = '<div class="control-group">'.
						'<div class="controls">'.
							'<input class="b2j-btn b2j-fileds-cancel" type="button" onClick="resetAddField();" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_CANCEL_BTN').'" >'.
							'<input class="b2j-btn b2j-fileds-save" type="button" onClick="submitB2jNewField();" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_SAVE_BTN').'">'.
						'</div>'.
					'</div>';
		return $buttons;	
	}
	public static function saveNewField($type,$fieldName,$defaultValue,$fieldState,$fieldGroup,$fieldItems,$fieldRadio,$newGroupName,$key){//,$b2jGroups
		$item = new stdClass;
		$item->b2jFieldName = $fieldName;
		$item->b2jDefaultValue = $defaultValue;
		$item->b2jFieldState = $fieldState;
		$item->b2jFieldGroup = $fieldGroup;
		$item->b2jFieldItems = $fieldItems;
		$item->b2jFieldRadio = $fieldRadio;
		$item->b2jNewGroupName = $newGroupName;
		$item->b2jFieldOrdering = $key;

		switch ($type) {
			case 'b2jDynamicText':
				$response['html'] = self::rendTextField($item,$key);
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_TEXT');
				break;
			case 'b2jDynamicDropdown':
				$response['html'] = self::rendDropdownField($item,$key);
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_DROPDOWN');
				break;
			case 'b2jDynamicTextarea':
				$response['html'] = self::rendTextareaField($item,$key);
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_TEXT_AREA');
				break;
			case 'b2jDynamicEmail':
				$response['html'] = self::rendEmailField($item,$key);
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_EMAIL');
				break;	
			case 'b2jDynamicCheckbox':
				$response['html'] = self::rendCheckboxField($item,$key);//,$gradingGroups
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_CHECK_BOX');
				break;
			case 'b2jDynamicDate':
				$response['html'] = self::rendDateField($item,$key);//,$gradingGroups
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_DATE');
				break;
			case 'b2jDynamicLabel':
				$response['html'] = self::rendLabelField($item,$key);//,$gradingGroups
				$typeFormat = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_LABEL');
				break;									
		}
		$icon = '';
		if($type == 'b2jDynamicEmail' && $item->b2jFieldRadio == '1'){
			$icon = "<span class='email_default'> (Default)</span>";
		}
		$response['inTable'] ='<li id="item_'.$key.'" class="row'.$key.' fields" key="'.$key.'" groupId="'.(string)$item->b2jFieldGroup.'">
									<div>
										<i class="icon-menu"></i>
										<span class="b2j-dynamic-field-name">'.$item->b2jFieldName.'</span>
										<span class="b2j-dynamic-field-type">'.$typeFormat.$icon.'</span>
										<span style="float:right;">
											<input type="button" class="b2j-dynamic-field-action-links edit" isGroup="false" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_EDIT_BTN').'" onClick="showEditField(this,'.$key.')">
											<input type="button" class="b2j-dynamic-field-action-links delete" isGroup="false" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_DELETE_BTN').'" onClick="deleteField(this,'.$key.');">
										</span>
									</div>
								</li>';
		$response['groupKey'] = $item->b2jFieldGroup;					  
		$response['groupName'] = $item->b2jNewGroupName;					  
		if($item->b2jNewGroupName != "false"){
			$group = new stdClass;
			$group->val = $item->b2jFieldGroup;
			$group->title = $item->b2jNewGroupName;
			$group->class = "";
			$group->state = "1";
			$response['html'] .= self::rendGroup($group);				  
		}
		return $response;
	}
	public static function rendTextField($item,$key){
		
		$textField = '<div id="b2j_'.$key.'" class="b2jFields">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jDefaultValue" type="text" value="'.$item->b2jDefaultValue.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select  fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL');
									$options[2]['value'] = '2';
									$options[2]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';
						
		$textField .= ContactHelper::rendButtons("b2jDynamicText",$key);
		$textField .='</div>';


		return $textField;	
	}
	public static function rendDropdownField($item,$key){//,$groups	
			
		$textField = '<div id="b2j_'.$key.'" class="b2jFields">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_ITEMS_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<textarea rows="3" cols="30" fieldKey="'.$key.'" fieldType="b2jFieldItems" class="">'.$item->b2jFieldItems.'</textarea>'.
							'</div>'.
						'</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jDefaultValue" type="text" value="'.$item->b2jDefaultValue.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL');
									$options[2]['value'] = '2';
									$options[2]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';
						 //'<input fieldKey="'.$key.'" fieldType="b2jFieldCurrentGroup" type="hidden" value="'.$item->b2jFieldGroup.'" size="26">';
		$textField .= ContactHelper::rendButtons("b2jDynamicDropdown",$key);
		$textField .='</div>';

		return $textField;	
	}
	public static function rendTextareaField($item,$key){//,$groups
		$textField = '<div id="b2j_'.$key.'" class="b2jFields">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jDefaultValue" type="text" value="'.$item->b2jDefaultValue.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL');
									$options[2]['value'] = '2';
									$options[2]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';
						 //'<input fieldKey="'.$key.'" fieldType="b2jFieldCurrentGroup" type="hidden" value="'.$item->b2jFieldGroup.'" size="26">';
		$textField .= ContactHelper::rendButtons("b2jDynamicTextarea",$key);
		$textField .='</div>';

		return $textField;	
	}
	public static function rendEmailField($item,$key){
		
		$textField = '<div id="b2j_'.$key.'" class="b2jFields b2jDefaultEmailCon">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jDefaultValue" type="text" value="'.$item->b2jDefaultValue.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label class="hasTooltip" title="'.JText::_('COM_B2JCONTACT_DEFAULT_EMAIL').'" title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_DEFAULT_LBL').'</label>'.
							'</div>';
							if($item->b2jFieldRadio == '1'){
								$checkedYes = "checked";
								$checkedNo = "";
							}else{
								$checkedYes = "";
								$checkedNo = "checked";
							}
		$textField .= 		'<div class="controls b2jDefaultEmailControlsCon">'.
									'<input fieldKey="'.$key.'" fieldType="b2jFieldRadio" type="radio" name="b2jFieldRadio'.$key.'" value="1" '.$checkedYes.'><label>'.JText::_('COM_B2JCONTACT_YES').'</label>'.
									'<input fieldKey="'.$key.'" fieldType="b2jFieldRadio" type="radio" name="b2jFieldRadio'.$key.'" value="0" '.$checkedNo.'><label>'.JText::_('COM_B2JCONTACT_NO').'</label>'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select  fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL');
									$options[2]['value'] = '2';
									$options[2]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';
						
		$textField .= ContactHelper::rendButtons("b2jDynamicEmail",$key);
		$textField .='</div>';


		return $textField;	
	}
	public static function rendCheckboxField($item,$key){//,$groups
		
		$textField = '<div id="b2j_'.$key.'" class="b2jFields">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_DEFAULT_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$key.'" fieldType="b2jDefaultValue" >';
		

									$options[0]['value'] = '1';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_SELECT_LBL');
									$options[1]['value'] = '0';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_NOT_SELECT_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jDefaultValue) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="" >'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL');
									$options[2]['value'] = '2';
									$options[2]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';
						//'<input fieldKey="'.$key.'" fieldType="b2jFieldCurrentGroup" type="hidden" value="'.$item->b2jFieldGroup.'" size="26">';
		$textField .= ContactHelper::rendButtons("b2jDynamicCheckbox",$key);
		$textField .='</div>';

		return $textField;	
	}
	public static function rendDateField($item,$key){//,$groups
		// add to input onClick="datepicker('.$key.');" fieldKey="'.$key.'" for datepicker
		$textField = 
			'<div id="b2j_'.$key.'" class="b2jFields">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input  fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						  '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_PLACEHOLDER_LBL').'</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input fieldKey="'.$key.'" fieldType="b2jDefaultValue" type="text" value="'.$item->b2jDefaultValue.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_OPTIONAL_LBL');
									$options[2]['value'] = '2';
									$options[2]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_REQUIRED_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';
						//'<input fieldKey="'.$key.'" fieldType="b2jFieldCurrentGroup" type="hidden" value="'.$item->b2jFieldGroup.'" size="26">';
		$textField .= ContactHelper::rendButtons("b2jDynamicDate",$key);
		$textField .='</div>';

		return $textField;	
	}
	public static function rendLabelField($item,$key){
		$textField = 
			'<div id="b2j_'.$key.'" class="b2jFields">'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_NAME_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<input  fieldType="b2jFieldName" type="text" value="'.$item->b2jFieldName.'" size="26">'.
							'</div>'.
						 '</div>'.
						 '<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$key.'" fieldType="b2jFieldState" >';
		
									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_ENABLE_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $item->b2jFieldState) ? $selected = 'selected="selected"' : "";
										$textField .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$textField .=			'</select>'.
							'</div>'.
						 '</div>';				 
						//'<input fieldKey="'.$key.'" fieldType="b2jFieldCurrentGroup" type="hidden" value="'.$item->b2jFieldGroup.'" size="26">';
		$textField .= ContactHelper::rendButtons("b2jDynamicDate",$key);
		$textField .='</div>';

		return $textField;	
	}
	public static function rendGroup($group){
		$groupHtml = '<div id="b2j_group_'.$group->val.'" class="b2jGroup">'.
					'<div class="control-group">'.
						'<div class="control-label">'.
							'<label title="" for="b2jFieldGroup">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_GROUP_TITLE_LBL').' *</label>'.
						'</div>'.
						'<div class="controls">'.
							'<input id="" type="text" fieldType="title" fieldKey="'.$group->val.'" value="'. $group->title .'" size="26" name="b2jGroupName" class="">'.
						'</div>'.
					'</div>'.
					'<div class="control-group">'.
						'<div class="control-label">'.
							'<label title="" for="b2jFieldGroup">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_GROUP_CLASS_LBL').'</label>'.
						'</div>'.
						'<div class="controls">'.
							'<input id="" type="text" fieldType="class" fieldKey="'.$group->val.'" value="'. $group->class .'" size="26" name="b2jGroupClass" class="">'.
						'</div>'.
					'</div>'.
					'<div class="control-group">'.
						  	'<div class="control-label">'.
								'<label title="">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_STATE_LBL').' *</label>'.
							'</div>'.
							'<div class="controls">'.
								'<select fieldKey="'.$group->val.'" fieldType="state" >';


									$options[0]['value'] = '0';
									$options[0]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_DISABLED_LBL');
									$options[1]['value'] = '1';
									$options[1]['text'] = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_OPTION_ENABLE_LBL');
									foreach ($options as $option)
									{
										$selected = ($option['value'] == $group->state) ? $selected = 'selected="selected"' : "";
										$groupHtml .= '<option value="' . $option['value'] . '"  ' . $selected . '>' . $option['text'] . '</option>';
									}

		$groupHtml .=			'</select>'.
							'</div>'.
						 '</div>';
		$groupHtml .= ContactHelper::rendButtons("b2jGroup",$group->val);
		$groupHtml .='</div>';
		return $groupHtml;	
	}
	public static function rendButtons($type,$key){
		$buttons = '<div class="control-group">'.
						'<div class="controls">'.
							'<input class="b2j-btn b2j-fileds-cancel" type="button" onClick="closeEdit();" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_CANCEL_BTN').'" >'.
							'<input class="b2j-btn b2j-fileds-save" type="button" onClick=\'saveValue("'.$type.'",'.$key.');\' value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_SAVE_BTN').'">'.
						'</div>'.
					'</div>';
		return $buttons;
	}
}