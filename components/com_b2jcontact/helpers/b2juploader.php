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

jimport("joomla.filesystem.file");
jimport("joomla.filesystem.folder");
$inc_dir = realpath(dirname(__FILE__));
require_once($inc_dir . '/b2jdatapump.php');
require_once($inc_dir . '/b2jsession.php');
require_once($inc_dir . '/b2jmimetype.php');

define('KB', 1024);

class B2JUploader extends B2JDataPump
{

	public function __construct(&$params, B2JMessageBoard &$messageboard)
	{
		parent::__construct($params, $messageboard);

		$this->Name = "FFilePump";
		$this->isvalid = intval($this->DoUpload());
	}


	protected function LoadFields()
	{

		$this->LoadField("upload", NULL);
	}


	protected function DoUpload()
	{

		$file = JRequest::getVar('b2jstdupload', NULL, 'files', 'array');


		if (!$this->Submitted || !$file || $file['error'] == UPLOAD_ERR_NO_FILE) return true;

		$upload_directory = JPATH_SITE . "/components/" . $GLOBALS["com_name"] . "/uploads/";

		if (!is_writable($upload_directory))
		{
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_DIR_NOT_WRITABLE'), B2JMessageBoard::error);
			return false;
		}


		if ($file['error'])
		{

			$this->MessageBoard->Add(JText::sprintf($GLOBALS["COM_NAME"] . '_ERR_UPLOAD', $file['error']), B2JMessageBoard::error);

			return false;
		}

	
		$size = $file['size'];
		if ($size == 0) 
		{
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_FILE_EMPTY'), B2JMessageBoard::error);
			return false;
		}
		$max_filesize = intval($this->Params->get("uploadmax_file_size", "0")) * KB;
		if ($size > $max_filesize) 
		{
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_FILE_TOO_LARGE'), B2JMessageBoard::error);
			return false;
		}

		$mimetype = new B2JMimeType();
		if (!$mimetype->Check($file['tmp_name'], $this->Params))
		{
	
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_MIME') . " [" . $mimetype->Mimetype . "]", B2JMessageBoard::error);
			return false;
		}

	
		jimport('joomla.filesystem.file');


		$filename = JFile::makeSafe($file['name']);
		$filename = uniqid() . "-" . $filename;
		$dest = $upload_directory . $filename;

		if (!JFile::upload($file['tmp_name'], $dest)) return false;
		
		$jsession =& JFactory::getSession();
		$b2jsession = new B2JSession($jsession->getId(), $this->Application->b2jcomid, $this->Application->b2jmoduleid, $this->Application->bid); // session_id, cid, mid
		
		$data = $b2jsession->Load('filelist'); 
		if ($data) $filelist = explode("|", $data);
		else $filelist = array();
		$filelist[] = $filename; 
		$data = implode("|", $filelist);
		$b2jsession->Save($data, "filelist");

		return true;
	}

}

?>
