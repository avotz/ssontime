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

require_once JPATH_COMPONENT . "/helpers/b2jsession.php";
require_once JPATH_COMPONENT . "/helpers/b2jlogger.php";
require_once JPATH_COMPONENT . "/helpers/b2jmimetype.php";

require_once "loader.php";

define('KB', 1024);

class uploaderLoader extends Loader
{
	protected function type()
	{
		return "uploader";
	}

	protected function http_headers()
	{
	}

	protected function content_header()
	{
	}

	protected function content_footer()
	{
	}

	protected function load()
	{

		switch (true)
		{
			case isset($_GET['qqfile']): $um = new XhrUploadManager(); break;
			case isset($_FILES['qqfile']): $um = new FileFormUploadManager(); break;
			default:
				$result = array('error' => JFactory::getLanguage()->_($GLOBALS["COM_NAME"] . '_ERR_NO_FILE'));
				exit(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
		}
		$um->Params = &$this->Params;
		$result = $um->HandleUpload(JPATH_COMPONENT . '/uploads/');
		echo(htmlspecialchars(json_encode($result), ENT_NOQUOTES));

	}
}





abstract class B2JUploadManager
{
	protected $Session;
	protected $Log;
	protected $DebugLog;

	abstract protected function save_file($path);
	abstract protected function get_file_name();
	abstract protected function get_file_size();


	function __construct()
	{
		$this->Log = new B2JLogger();
		$this->DebugLog = new BDebugLogger("file uploader");

		$this->Session = JFactory::getSession();
	}


	public function HandleUpload($uploadDirectory)
	{
		$this->DebugLog->Write("HandleUpload() started");

		if (!is_writable($uploadDirectory))
		{
			$this->DebugLog->Write("Directory " . $uploadDirectory . " is not writable");
			return array('error' => JFactory::getLanguage()->_($GLOBALS["COM_NAME"] . '_ERR_DIR_NOT_WRITABLE'));
		}
		$this->DebugLog->Write("Directory " . $uploadDirectory . " is ok");

		$size = $this->get_file_size();
		if ($size == 0) 
		{
			$this->DebugLog->Write("File size is 0");
			return array('error' => JFactory::getLanguage()->_($GLOBALS["COM_NAME"] . '_ERR_FILE_EMPTY'));
		}
		$this->DebugLog->Write("File size is > 0");

		$max = $this->Params->get("uploadmax_file_size", 0) * KB;
		
		if ($size > $max)
		{
			$this->DebugLog->Write("File size too large ($size > $max)");
			return array('error' => JFactory::getLanguage()->_($GLOBALS["COM_NAME"] . '_ERR_FILE_TOO_LARGE'));
		}
		$this->DebugLog->Write("File size ($size / $max) is ok");

		$filename = preg_replace("/[^\w\.-_]/", "_", $this->get_file_name());
		$filename = uniqid() . "-" . $filename;
		$full_filename = $uploadDirectory . $filename;

		if (!$this->save_file($full_filename))
		{
			$this->DebugLog->Write("Error saving file");
			return array('error'=> JFactory::getLanguage()->_($GLOBALS["COM_NAME"] . '_ERR_SAVE_FILE'));
		}
		$this->DebugLog->Write("File saved");

		$mimetype = new B2JMimeType();
		if (!$mimetype->Check($full_filename, $this->Params))
		{
			unlink($full_filename);
			$this->DebugLog->Write("File type [" . $mimetype->Mimetype . "] is not allowed. Allowed types are:" . PHP_EOL . print_r($mimetype->Allowed, true));
			return array('error' => JFactory::getLanguage()->_($GLOBALS["COM_NAME"] . '_ERR_MIME') . " [" . $mimetype->Mimetype . "]");
		}
		$this->DebugLog->Write("File type [" . $mimetype->Mimetype . "] is allowed");

		$b2jcomid = JFactory::getApplication()->input->get("b2jcomid", NULL);
		$b2jmoduleid = JFactory::getApplication()->input->get("b2jmoduleid", NULL);
		$owner = JFactory::getApplication()->input->get("owner", NULL);
		$id = JFactory::getApplication()->input->get("id", NULL);
		$bid = JFactory::getApplication()->input->get("bid", NULL);
		$jsession = JFactory::getSession();
		$b2jsession = new B2JSession($jsession->getId(), $b2jcomid, $b2jmoduleid, $bid);

		$data = $b2jsession->Load('filelist');
		if ($data) $filelist = explode("|", $data);
		else $filelist = array();
		$filelist[] = $filename;
		$data = implode("|", $filelist);
		$b2jsession->Save($data, "filelist");

		$this->Log->Write("File " . $filename . " uploaded succesful.");
		$this->DebugLog->Write("File uploaded succesful.");
		return array("success" => true);
	}


}


class XhrUploadManager extends B2JUploadManager
{

	public function __construct()
	{
		parent::__construct();
	}


	protected function save_file($path)
	{
		$input = fopen("php://input", "r");
		$target = fopen($path, "w");

		$realSize = stream_copy_to_stream($input, $target);

		fclose($input);
		fclose($target);

		return ($realSize == $this->get_file_size());
	}


	protected function get_file_name()
	{
		return $_GET['qqfile'];
	}


	protected function get_file_size()
	{
		if (isset($_SERVER["CONTENT_LENGTH"])) return (int)$_SERVER["CONTENT_LENGTH"];
		return 0;
	}

}


class FileFormUploadManager extends B2JUploadManager
{
	public function __construct()
	{
		parent::__construct();
	}


	protected function save_file($path)
	{
		return move_uploaded_file($_FILES['qqfile']['tmp_name'], $path);
	}


	protected function get_file_name()
	{
		return $_FILES['qqfile']['name'];
	}

	protected function get_file_size()
	{
		return $_FILES['qqfile']['size'];
	}

}

