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

class B2JLogger
   {
   protected $Handle = NULL;
   protected $Prefix = "";

   public function __construct($prefix = NULL, $suffix = NULL)
      {
		$this->open($suffix);
		if ($prefix) $this->Prefix = "[" . $prefix . "] ";
      }


   function __destruct()
      {
      if ($this->Handle) fclose($this->Handle);
      }


   public function Write($buffer)
      {
      if (!$this->Handle) return false;

		fseek($this->Handle, 0, SEEK_END);
      $now = JFactory::getDate();
      return fwrite($this->Handle, $now->format("Y-m-d H:i:s") . " " . $this->Prefix . $buffer . PHP_EOL);
      }

	protected function open($suffix = NULL)
		{

		$application = JFactory::getApplication();
		if (!$suffix) $suffix = md5($application->getCfg("secret"));
		$this->Handle = @fopen($application->getCfg("log_path") . "/" . substr(basename(realpath(dirname(__FILE__) . '/..')), 4) . "-" . $suffix . ".txt", 'a+');
		}
   }


class BDebugLogger extends B2JLogger
	{
	public function __construct($prefix = NULL)
		{
		$jsession = JFactory::getSession();
		$debug = $jsession->get("debug");
		if ($debug) $this->open("debug");
		$this->Prefix = "[" . $prefix . "] ";
		}
	}

?>
