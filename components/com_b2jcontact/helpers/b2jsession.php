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

	jimport('joomla.utilities.date');

	class B2JSession
	{
		private $db;
		protected $Id;
		protected $B2JComid;
		protected $B2JModuleid;
		protected $Bid;

		public function __construct($id, $b2jcomid, $b2jmoduleid, $bid)
		{

			$this->Id  = $id;
			$this->B2JComid = intval($b2jcomid);
			$this->B2JModuleid = intval($b2jmoduleid); 
			$this->Bid = intval($bid);

			global $app;
			$this->db = JFactory::getDBO();
		}


		public function Save($string, $keyword)
		{
			$sql = "SELECT data FROM #__" . $GLOBALS["ext_name"] . "_sessions WHERE id = '$this->Id' AND b2jcomid = $this->B2JComid AND b2jmoduleid = $this->B2JModuleid AND bid = $this->Bid AND keyword = '$keyword';";
			$this->db->setQuery($sql);
			$result = $this->db->query();


				
			if ((bool)$this->db->getNumRows())
			{
				$sql = "UPDATE #__" . $GLOBALS["ext_name"] . "_sessions SET data = '$string', birth = '" . JFactory::getDate()->toSql() . "' WHERE id = '$this->Id' AND b2jcomid = $this->B2JComid AND b2jmoduleid = $this->B2JModuleid AND bid = $this->Bid AND keyword = '$keyword';";
				$this->db->setQuery($sql);
				$result = $this->db->query();
			}
			else
			{
				$sql = "INSERT INTO #__" . $GLOBALS["ext_name"] . "_sessions (id, b2jcomid, b2jmoduleid, bid, keyword, birth, data) VALUES ('$this->Id', $this->B2JComid, $this->B2JModuleid, $this->Bid, '$keyword', '" . JFactory::getDate()->toSql() . "', '$string');";
				
				$this->db->setQuery($sql);
				$result = $this->db->query();
			}

			return $result;
		}


		public function Load($keyword)
		{
			$this->PurgeExpiredSessions();

			$sql = "SELECT data FROM #__" . $GLOBALS["ext_name"] . "_sessions WHERE id = '$this->Id' AND b2jcomid = $this->B2JComid AND b2jmoduleid = $this->B2JModuleid AND bid = $this->Bid AND keyword = '$keyword';";

			$this->db->setQuery($sql);
			return $this->db->loadResult();
		}


		public function PurgeValue($keyword)
		{
			$sql = "UPDATE #__" . $GLOBALS["ext_name"] . "_sessions SET data = NULL WHERE id = '$this->Id' AND b2jcomid = $this->B2JComid AND b2jmoduleid = $this->B2JModuleid AND bid = $this->Bid AND keyword = '$keyword';";
			$this->db->setQuery($sql);
			$this->db->query();
		}


		public function Clear($keyword)
		{
			$sql = "DELETE FROM #__" . $GLOBALS["ext_name"] . "_sessions WHERE id = '$this->Id' AND b2jcomid = $this->B2JComid AND b2jmoduleid = $this->B2JModuleid AND bid = $this->Bid AND keyword = '$keyword';";
			$this->db->setQuery($sql);
			$this->db->query();
		}


		private function PurgeExpiredSessions()
		{
	
			$lifetime = JFactory::$config->get("lifetime");
			$date = new JDate("-" . $lifetime . " minute");
			$sql = "DELETE FROM #__" . $GLOBALS["ext_name"] . "_sessions WHERE birth < '" . $date->toSql() . "';";
			$this->db->setQuery($sql);
			$this->db->query();
		}


	}

?>
