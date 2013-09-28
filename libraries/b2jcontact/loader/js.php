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

require_once "loader.php";

class jsLoader extends Loader
{
	protected function type()
	{
		return "js";
	}

	protected function http_headers()
	{
		header('Content-Type: application/javascript; charset=utf-8');
	}

	protected function content_header()
	{
		
	}

	protected function content_footer()
	{
		
	}
}
