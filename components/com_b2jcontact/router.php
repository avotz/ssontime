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

function parameters()
{
	static $result = array(
		0 => "view",
		1 => "owner",
		2 => "id",
		3 => "root",
		4 => "filename",
		5 => "type"
	);

	return $result;
}

function B2jContactBuildRoute(&$query)
{
	$segments = array();
	$parameters = parameters();

	foreach ($parameters as $name)
	{
		if (isset($query[$name]))
		{
			$segments[] = $query[$name];
			unset($query[$name]);
		}
		else
		{

			break;
		}
	}

	return $segments;
}

function B2jContactParseRoute($segments)
{
	$vars = array();
	$parameters = parameters();

	foreach ($parameters as $index => $name)
	{
		if (isset($segments[$index]))
		{

			$vars[$name] = preg_replace('/[^A-Z0-9_]/i', "", $segments[$index]);
		}
		else
		{

			break;
		}
	}

	return $vars;
}
