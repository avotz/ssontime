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
	require_once($inc_dir . '/b2jlogger.php');
	include_once(realpath(dirname(__FILE__) . "/../" . substr(basename(realpath(dirname(__FILE__) . "/..")), 4) . ".inc"));


	class B2JMimeType
	{
		public $Allowed = array();
		public $Mimetype;

		public function __construct()
		{
		}


		public function Check($filename, &$cparams)
		{

			if (!(bool)$cparams->get("upload_filter", 1)) return true;


			if ((bool)$cparams->get("upload_audio", 0)) $this->Allowed[] = "/^audio\//";
			if ((bool)$cparams->get("upload_video", 0)) $this->Allowed[] = "/^video\//";
			if ((bool)$cparams->get("upload_images", 0)) $this->Allowed[] = "/^image\//";
			if ((bool)$cparams->get("upload_archives", 0))
			{
				$this->Allowed[] = "/^application\/.*zip/"; 
				$this->Allowed[] = "/^application\/x-compress/";
				$this->Allowed[] = "/^application\/x-compressed/"; 
				$this->Allowed[] = "/^application\/x-gzip/"; 
				$this->Allowed[] = "/^application\/x-rar/"; 
			}

			if ((bool)$cparams->get("upload_documents", 0))
			{
				$this->Allowed[] = "/^(application|text)\/rtf/"; 
				$this->Allowed[] = "/^application\/pdf/"; 
				$this->Allowed[] = "/^application\/msword/"; 
				$this->Allowed[] = "/^application\/vnd.ms-/"; 
				$this->Allowed[] = "/^application\/vnd\.openxmlformats-officedocument\./";
				$this->Allowed[] = "/^application\/x-mspublisher/"; 
				$this->Allowed[] = "/^application\/x-mswrite/"; 
				$this->Allowed[] = "/^application\/vnd\.oasis\.opendocument\.text/"; 
			}

			$this->Mimetype = $this->read_mimetype($filename);


			if ($this->Mimetype == "disabled") return true;

			$this->Mimetype = preg_replace("/;.*/", "", $this->Mimetype);

			foreach ($this->Allowed as $allowed_type)
			{
				if ((bool)preg_match($allowed_type, $this->Mimetype)) return true;
			}

			return false;
		}


		private function read_mimetype($filename)
		{
			$debug_log = new BDebugLogger("b2jmimetype");
			$debug_log->Write("Determining mimetype");

			$db = JFactory::getDBO();
			$sql = "SELECT value FROM #__" . $GLOBALS["ext_name"] . "_settings WHERE name = 'mimefilter';";
			$db->setQuery($sql);
			$method = $db->loadResult();
			if (!$method)
			{

				$debug_log->Write("Error #" . $db->getErrorNum() . " while loading mimefilter from database");
				return "";
			}

			$result = $this->$method($filename);
			$debug_log->Write("mime method: [" . $method . "], mime detected: [" . $result . "]");
			return $result;
		}


		private function use_fileinfo($filename)
		{
			$minfo = new finfo(FILEINFO_MIME);
			return $minfo->file($filename);
		}


		private function use_mimecontent($filename)
		{
			return mime_content_type($filename);
		}


		private function use_exec($filename)
		{
			$output = array();
			$returncode = 0;
			return exec('file -b --mime-type ' . escapeshellarg($filename), $output, $returncode);
		}


		private function disabled($filename)
		{
			return "disabled";
		}

	}


	class b2jmimetypeCheckEnvironment
	{
		protected $InstallLog;

		public function __construct()
		{
			$this->InstallLog = new B2JLogger("b2jmimetype", "install");
			$this->InstallLog->Write("--- Determining if this system is able to detect file mime types ---");

			switch (true)
			{
				case $this->fileinfo_usable(): $value = "use_fileinfo"; break;
				case $this->mimecontent_usable(): $value = "use_mimecontent"; break;
				case $this->exec_usable(): $value = "use_exec"; break;

				default: $value = "disabled";
			}

			$db = JFactory::getDBO();
			$sql = "REPLACE INTO #__" . $GLOBALS["ext_name"] . "_settings (name, value) VALUES ('mimefilter', '$value');";
			$db->setQuery($sql);
			$result = $db->query();

			$this->InstallLog->Write("--- Method choosen to detect file mime types is [$value] ---");
			return $result;
		}

		private function fileinfo_usable()
		{
			if (!extension_loaded('fileinfo'))
			{
				$this->InstallLog->Write("fileinfo extension not found");
				return false;
			}
			$this->InstallLog->Write("fileinfo extension found. Let's see if it works.");

			$minfo = @new finfo(FILEINFO_MIME);

			$result = true;
			$result &= $this->test(@$minfo->file($this->filename("test.mp3")), "/^audio\//");
			$result &= $this->test(@$minfo->file($this->filename("test.mp4")), "/^video\//");
			$result &= $this->test(@$minfo->file($this->filename("test.jpg")), "/^image\//");
			$result &= $this->test(@$minfo->file($this->filename("test.zip")), "/^application\/.*zip/");
			$result &= $this->test(@$minfo->file($this->filename("test.pdf")), "/^application\/pdf/");
			return $result;
		}


		private function mimecontent_usable()
		{
			if (!function_exists('mime_content_type'))
			{
				$this->InstallLog->Write("mime_content_type() function not found");
				return false;
			}
			$this->InstallLog->Write("mime_content_type() function found. Let's see if it works.");

			$result = true;
			$result &= $this->test(mime_content_type($this->filename("test.mp3")), "/^audio\//");
			$result &= $this->test(mime_content_type($this->filename("test.mp4")), "/^video\//");
			$result &= $this->test(mime_content_type($this->filename("test.jpg")), "/^image\//");
			$result &= $this->test(mime_content_type($this->filename("test.zip")), "/^application\/.*zip/");
			$result &= $this->test(mime_content_type($this->filename("test.pdf")), "/^application\/pdf/");
			return $result;
		}



		private function exec_usable()
		{
	
			if (substr($_SERVER['PATH'], 0, 1) != '/')
			{
				$this->InstallLog->Write("Not a unix environment. No way to get mime info by calling system shell functions.");
				return false;
			}


			if (!function_exists('exec'))
			{
				$this->InstallLog->Write("exec() function disabled by server administrator. No way to get mime info by calling system shell functions.");
				return false;
			}

			if (!function_exists('escapeshellarg'))
			{
				$this->InstallLog->Write("escapeshellarg() function disabled by server administrator. It isn't safe to call exec().");
				return false;
			}


			$this->InstallLog->Write("exec() enabled. It should be safe to call it. Let's see if it works.");

			$result = true;
			$result &= $this->test($this->system_opinion($this->filename("test.mp3")), "/^audio\//");
			$result &= $this->test($this->system_opinion($this->filename("test.mp4")), "/^video\//");
			$result &= $this->test($this->system_opinion($this->filename("test.jpg")), "/^image\//");
			$result &= $this->test($this->system_opinion($this->filename("test.zip")), "/^application\/.*zip/");
			$result &= $this->test($this->system_opinion($this->filename("test.pdf")), "/^application\/pdf/");
			return $result;
		}


		private function system_opinion($filename)
		{
			$output = array();
			$returncode = 0;
			return  exec('file -b --mime-type ' . escapeshellarg($filename), $output, $returncode);

		}


		private function test($detected, $expected)
		{

			$result = preg_match($expected, $detected);
			$this->InstallLog->Write("testing detected mimetype [$detected] seeking expected string [$expected]... [" . intval($result) . "]");
			return $result;
		}


		private function filename($filename)
		{
			return JPATH_ROOT . "/media/" . $GLOBALS["com_name"] . "/mimetypes/" . $filename;
		}



	}

?>
