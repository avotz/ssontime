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
class JFormFieldB2JDynamicgroup extends JFormField
{
	protected $type = 'B2JDynamicgroup';

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

		$doc = JFactory::$document;

		$script='
			var groupValue = \''.$this->value.'\';
			var hiddenGroupId = \''.$this->id.'\';
			if(groupValue==""){
				groupValue=new Array();
			}else{
				groupValue = JSON.parse(groupValue);
			}
			for(key in groupValue){
				groupValue[key] = groupValue[key][0];
			}
		';
        $doc->addScriptDeclaration($script);
		return $html;
	}
	protected function getLabel(){
		return null;
	}
}
