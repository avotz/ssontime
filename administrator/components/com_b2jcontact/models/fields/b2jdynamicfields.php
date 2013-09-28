<?php defined('JPATH_BASE') or die('Restricted access');

/* ------------------------------------------------------------------------
 * Bang2Joom Contact for Joomla 3.0+
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2013 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */

jimport('joomla.form.formfield');

class JFormFieldB2JDynamicfields extends JFormField
{
	protected $type = 'B2JDynamicfields';

	protected function getInput()
	{
		require_once JPATH_COMPONENT.'/helpers/contact.php';
		if(!isset($this->value))
		{
			$html = '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value=\''.$this->element['default'].'\' >';
			$items = json_decode($this->element['default']);
		}
		else
		{
			$html = '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'" value=\''.$this->value.'\' >';
			$items = json_decode($this->value);
		}
		$itemID = 0;
  		
  		$data = null;
		foreach ((Array)$this->form as $key => $val) {
			if($val instanceof JRegistry){
			  $data = &$val;
			  break;
			}
		}
		$groupsValue = false;
		$data = $data->toArray();
		if(isset($data['params']['itemgroups'])){
			$groupsValue = json_decode($data['params']['itemgroups']);
		}

		if(!$groupsValue){
			$formfields = $this->form->getFieldset('formfields');
			$xmlData = $formfields["jform_params_itemgroups"]->element;

			$groupsValue = (string)$xmlData['default'];
			$groupsValue = json_decode($groupsValue);
		}
  		$gradingGroups = array();
  		foreach($groupsValue as $key => $group){
  				$gradingGroups[$group[0]->ordering] = $group[0];	
  				$gradingGroups[$group[0]->ordering]->val = $key;
  		}
  		ksort($gradingGroups);
		
		if($items){
			$fieldsByOrder = array();
			foreach ($items as $key => $item) {
				$fieldsByOrder[$item[0]->b2jFieldOrdering][] = $item[0];
			}
			
			ksort($fieldsByOrder);

			$fieldsByGroup = array();
			foreach ($fieldsByOrder as $key => $fieldByOrder) {
				foreach ($fieldByOrder as  $item) {
					$fieldsByGroup[$item->b2jFieldGroup][] = $item;
				}
			}
		}
		$html .='<ol class="sortable">';
       
		$groupHtml = '';		
		$fieldHtml = '';
		unset($gradingGroups[0]);
		foreach ($gradingGroups as $group){	
			$html .= '<li class="b2j-group" id="group_'.(string)$group->val.'" groupId="'.(string)$group->val.'">	
						<div><i class="icon-menu"></i><span class="group-name">'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_GROUP_LBL').': '.$group->title.'</span><span class="group-action"><a href="#" class="group-edit-btn" isGroup="true" onClick="showEditField(this,'.(string)$group->val.')">Edit</a>&nbsp;<a href="#" isGroup="true" class="group-delete-btn" onClick="deleteField(this,'.(string)$group->val.');">Delete</a></span></div>';

			$groupHtml .= ContactHelper::rendGroup($group);
			
			if(isset($fieldsByGroup[$group->val])){
				$html .= '<ol>';

				foreach ($fieldsByGroup[$group->val] as $item) {
					switch ($item->type) {
						case 'b2jDynamicText':
							$fieldHtml .= ContactHelper::rendTextField($item,$item->b2jFieldKey,$gradingGroups);
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_TEXT');
							break;
						case 'b2jDynamicDropdown':
							$fieldHtml .= ContactHelper::rendDropdownField($item,$item->b2jFieldKey,$gradingGroups);
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_DROPDOWN');
							break;
						case 'b2jDynamicTextarea':
							$fieldHtml .= ContactHelper::rendTextareaField($item,$item->b2jFieldKey,$gradingGroups);
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_TEXT_AREA');
							break;
						case 'b2jDynamicCheckbox':
							$fieldHtml .= ContactHelper::rendCheckboxField($item,$item->b2jFieldKey,$gradingGroups);
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_CHECK_BOX');
							break;
						case 'b2jDynamicDate':
							$fieldHtml .= ContactHelper::rendDateField($item,$item->b2jFieldKey,$gradingGroups);
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_DATE');
							break;
						case 'b2jDynamicLabel':
							$fieldHtml .= ContactHelper::rendLabelField($item,$item->b2jFieldKey,$gradingGroups);
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_LABEL');
							break;				
						default:
							$fieldHtml .= JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_ERROR');
							$type = JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_TYPE_ERROR');	
							break;		
					}
					$html .= 	'<li id="item_'.$item->b2jFieldKey.'" class="row'.$item->b2jFieldKey.' fields" key="'.$item->b2jFieldKey.'" groupId="'.(string)$group->val.'">
									<div>
										<i class="icon-menu"></i>
										<span class="b2j-dynamic-field-name">'.$item->b2jFieldName.'</span>
										<span class="b2j-dynamic-field-type">'.$type.'</span>
										<span style="float:right;">
											<input type="button" class="b2j-dynamic-field-action-links edit" isGroup="false" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_EDIT_BTN').'" onClick="showEditField(this,'.$item->b2jFieldKey.')">
											<input type="button" class="b2j-dynamic-field-action-links delete" isGroup="false" value="'.JText::_('COM_B2JCONTACT_DYNAMIC_FIELD_DELETE_BTN').'" onClick="deleteField(this,'.$item->b2jFieldKey.');">
										</span>
									</div>
									</li>';
				}
				$html .= '</ol>';
			}
		}
		$html .= '</ol>';
		$html .= $fieldHtml;
		$html .= $groupHtml;
		$doc = JFactory::$document;
		$name = $this->element['name'][0];
		$script='
			var value = \''.$this->value.'\';
			var hiddenInputId = \''.$this->id.'\';
			
			if(value==""){
				value=new Array();
			}else{
				value = JSON.parse(value);
			}

			for(key in value){
				value[key] = value[key][0];
			}
			
			var name = "'.$name.'";
			var id = "'.$this->id.'";
		';
        $doc->addScriptDeclaration($script);

		return $html;
	}
	protected function getLabel(){
		return null;
	}
}
