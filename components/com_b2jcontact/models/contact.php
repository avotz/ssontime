<?php
/* ------------------------------------------------------------------------
 * Bang2Joom Contact for Joomla 3.0+
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2013 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 * @since       1.5
 */

class B2jContactModelContact
{

	protected $_item = null;

	public function getItem($pk = null,$cparams)
	{
		$app = JFactory::getApplication('site');
		$pk = (!empty($pk)) ? $pk : (int) $app->bid;

		if ($this->_item === null)
		{
			$this->_item = array();
		}
	
		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);

				$case_when = ' CASE WHEN ';
				$case_when .= $query->charLength('a.alias', '!=', '0');
				$case_when .= ' THEN ';
				$a_id = $query->castAsChar('a.id');
				$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
				$case_when .= ' ELSE ';
				$case_when .= $a_id.' END as slug';

				$query->select('a.*' . ','.$case_when);
				$query->from('#__b2jcontact_details AS a');

				
				$query->where('a.id = ' . (int) $pk);

				$nullDate = $db->Quote($db->getNullDate());
				$nowDate = $db->Quote(JFactory::getDate()->toSql());

				$published = 1;

				if (is_numeric($published))
				{
					$query->where('(a.published = ' . (int) $published . ')');
					$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
					$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
				}
				
				$db->setQuery($query,$offset = 0, $limit = 0);
				$data = $db->loadObject($class = 'stdClass');

				if (empty($data))
				{
					JError::raiseError(404, JText::_('COM_B2JCONTACT_ERROR_CONTACT_NOT_FOUND'));
				}
				

				if ((is_numeric($published)) && ($data->published != $published))
				{
					JError::raiseError(404, JText::_('COM_B2JCONTACT_ERROR_CONTACT_NOT_FOUND'));
				}
				
				$registry = new JRegistry;
				$registry->loadString($data->params);
				$data->params = clone $cparams;
				$data->params->merge($registry);

				$user = JFactory::getUser();
				$groups = $user->getAuthorisedViewLevels();

				$data->params->set('access-view', in_array($data->access, $groups));
				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}


		return $this->_item[$pk];
	}
}
