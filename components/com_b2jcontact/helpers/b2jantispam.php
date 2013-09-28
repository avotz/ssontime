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
require_once($inc_dir . '/b2jlogger.php');

class B2JAntispam extends B2JDataPump
{
	protected $FieldsBuilder;


	public function __construct(&$params, B2JMessageBoard &$messageboard, $fieldsbuilder)
	{
		parent::__construct($params, $messageboard);

		$this->Name = "B2JAntispam";
		$this->FieldsBuilder = $fieldsbuilder;
		$this->isvalid = intval($this->ValidateForSpam($fieldsbuilder));
	}


	public function Show()
	{
		if (!$this->isvalid)
		{
			$this->MessageBoard->Add($this->Params->get("spam_detected_text"), B2JMessageBoard::warning);
		}
	}


	protected function LoadFields()
	{
	}


	protected function ValidateForSpam(&$fieldsbuilder)
	{
		$message = "";

		foreach ($fieldsbuilder->DynamicFields as $Fields)
        {
        	foreach ($Fields[1] as $field) {
        		if (strpos($field->type, "b2jDynamicTextarea") !== 0) continue;
        		$message .= $field->b2jFieldValue;
        	}
        } 
		// foreach ($fieldsbuilder->Fields as $key => $field)
		// {
		// 	if (strpos($field['Type'], "textarea") !== 0) continue;
		// 	$message .= $field['Value'];
		// }
		$spam_words = $this->Params->get("spam_words", "");

		if (!(bool)($this->Params->get("spam_check", 0)) && !(bool)($this->Params->get("copy_to_submitter", 0))) return true;

		if (empty($spam_words)) return true;

		$arr_spam_words = explode(",", $spam_words);

		foreach ($arr_spam_words as $word)
		{
			if (stripos($message, $word) !== false)
			{
				$logger = new B2JLogger();
				$logger->Write("Spam attempt blocked:" . PHP_EOL . print_r($fieldsbuilder->Fields, true) . "-----------------------------------------");

				return false;
			}
		}

		return true;
	}
}

?>
