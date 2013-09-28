<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ContactHelper', JPATH_ADMINISTRATOR . '/components/com_b2jcontact/helpers/contact.php');

/**
 * Item Model for a Contact.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class B2jContactModelContact extends JModelAdmin
{
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;
		
		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}
		
		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object	$record	A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since   1.6
	 */
	
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_b2jcontact');//, 'com_b2jcontact.category.'.(int) $record->catid);
		}
	}
	
	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object	$record	A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return parent::canEditState($record);
	}
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	$type	The table type to instantiate
	 * @param   string	$prefix	A prefix for the table class name. Optional.
	 * @param   array  $config	Configuration array for model. Optional.
	 *
	 * @return  JTable	A database object
	 * @since   1.6
	 */
	public function getTable($type = 'Contact', $prefix = 'ContactTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array  $data		Data for the form.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_b2jcontact/models/fields');

		// Get the form

		$form = $this->loadForm('com_b2jcontact.contact', 'contact', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}
		
		// Modify the form based on access controls.
		
		if (!$this->canEditState((object) $data))
		{
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer	$pk	The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();
		}

		// Load associated contact items
		$app = JFactory::getApplication();

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_b2jcontact.edit.contact.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 *
	 * @return  boolean  True on success.
	 * @since	3.0
	 */
	public function save($data)
	{
		$app = JFactory::getApplication();
		
		if (parent::save($data))
		{
			return true;
		}

		return false;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable	$table
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name		= trim(htmlspecialchars_decode($table->name, ENT_QUOTES));
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = JApplication::stringURLSafe($table->name);
		}

		if (empty($table->id))
		{
			// Set the values
			$table->created	= $date->toSql();

			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__b2jcontact_details');
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified	= $date->toSql();
			$table->modified_by	= $user->get('id');
		}
		// Increment the content version number.
		$table->version++;

	}
	
	/**
	 * Method to duplicate modules.
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @since   1.6
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user	= JFactory::getUser();
		$db		= $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.create', 'com_modules'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				// Alter the name.
				$m = null;
				if (preg_match('#\((\d+)\)$#', $table->name, $m))
				{
					$table->name = preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $table->name);
				}
				else
				{
					$table->name .= ' (2)';
				}
				// Alter the alias.
				$n = null;
				if (preg_match('#\((\d+)\)$#', $table->alias, $n))
				{
					$table->alias = preg_replace('#\(\d+\)$#', '(' . ($n[1] + 1) . ')', $table->alias);
				}
				else
				{
					$table->alias .= ' (2)';
				}

				// Unpublish duplicate contact
				$table->published = 0;

				if (!$table->check() || !$table->store())
				{
					throw new Exception($table->getError());
				}
			}
			else
			{
				throw new Exception($table->getError());
			}
		}
		return true;
	}
}
