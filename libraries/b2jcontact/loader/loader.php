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

abstract class Loader
{
	abstract protected function type();
	abstract protected function http_headers();
	abstract protected function content_header();
	abstract protected function content_footer();

	public function Show()
	{
		$this->headers();
		$this->http_headers();
		$this->content_header();
		$this->load();
		$this->content_footer();

		JFactory::getApplication()->close();
	}


	private function headers()
	{
		header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}


	protected function load()
	{
		$input = JFactory::getApplication()->input;
		$owner = $input->get("owner", "component");
		$id = $input->get("id", "0");
		$uid = "_" . $owner[0] . $id;
		$language = JFactory::getLanguage();
		$direction = intval($language->get('rtl', 0));
		$left = $direction ? "right" : "left";
		$right = $direction ? "left" : "right";
		$juri_root = JURI::root(true);

		$filename = JFactory::getApplication()->input->get("filename", "");
		
		if($filename == "b2jcontact"){
			$templateDir = JPATH_ROOT. DIRECTORY_SEPARATOR .'templates'.DIRECTORY_SEPARATOR . JFactory::getApplication()->getTemplate() .DIRECTORY_SEPARATOR.'css'. DIRECTORY_SEPARATOR .'b2jcontact.css';
			
			if(file_exists($templateDir)){
				require_once $templateDir;
			}else{
				require_once $this->IncludePath . "/" . $this->type() . "/" . $filename . "." . $this->type();
			}
		}else{
			require_once $this->IncludePath . "/" . $this->type() . "/" . $filename . "." . $this->type();
		}
	}

}

