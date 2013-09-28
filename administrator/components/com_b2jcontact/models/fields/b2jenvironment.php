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

class JFormFieldB2JEnvironment extends JFormField
{
	protected $type = 'B2JEnvironment';


	public function __construct(JForm $form = null)
	{
		parent::__construct($form);



		static $resources = true;
		if ($resources)
		{
			$resources = false;
			$name = basename(realpath(dirname(__FILE__) . "/../.."));
			$document = JFactory::getDocument();

			$type = strtolower($this->type);

			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/js/" . $type . ".js"))
			{
				$document->addScript(JUri::current() . "?option=" . $name . "&amp;view=loader&amp;filename=" . $type . "&amp;type=js");
			}

			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/css/" . $type . ".css"))
			{
				$document->addStyleSheet(JUri::base(true) . "/components/" . $name . "/css/" . $type . ".css");
			}

			$scope = JFactory::getApplication()->scope;

			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/js/" . $scope . ".js"))
			{
				$document->addScript(JUri::current() . "?option=" . $name . "&amp;view=loader&amp;filename=" . $scope . "&amp;type=js");
			}

			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/css/" . $scope . ".css"))
			{
				$document->addStyleSheet(JUri::base(true) . "/components/" . $name . "/css/" . $scope . ".css");
			}

			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/js/glDatePicker.js"))
			{
				$document->addScript(JUri::current() . "?option=" . $name . "&amp;view=loader&amp;filename=glDatePicker&amp;type=js");
			}
			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/css/glDatePicker.css"))
			{
				$document->addStyleSheet(JUri::base(true) . "/components/" . $name . "/css/glDatePicker.css");
			}
			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/css/style.css"))
			{
				$document->addStyleSheet(JUri::base(true) . "/components/" . $name . "/css/style.css");
			}
			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/js/script.js"))
			{
				$document->addScript(JUri::current() . "?option=" . $name . "&amp;view=loader&amp;filename=script&amp;type=js");
			}
			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/js/jquery.mjs.nestedSortable.js"))
			{
				$document->addScript(JUri::current() . "?option=" . $name . "&amp;view=loader&amp;filename=jquery.mjs.nestedSortable&amp;type=js");
			}
			if (file_exists(JPATH_ADMINISTRATOR . "/components/" . $name . "/css/nestedSortable.css"))
			{
				$document->addStyleSheet(JUri::base(true) . "/components/" . $name . "/css/nestedSortable.css");
			}
			

			$GLOBALS["com_name"] = basename(realpath(dirname(__FILE__) . "/../.."));

			$module = JFactory::getApplication()->input->get("option") == "com_modules";
			$language = JFactory::getLanguage();
			$enGB = $language->get("tag") == $language->getDefault();

			if (!$enGB || $module)
			{
				$language->load($GLOBALS["com_name"], JPATH_ADMINISTRATOR, $language->getDefault(), true);
				$language->load($GLOBALS["com_name"], JPATH_ADMINISTRATOR, null, true);
			}

		}

	}


	protected function getInput()
	{
		return "";
	}


	protected function getLabel()
	{
		return "";
	}
}
