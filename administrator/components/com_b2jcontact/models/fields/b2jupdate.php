<?php

/* ------------------------------------------------------------------------
 * Bang2Joom K2 Calendar for Joomla 2.5+
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2012 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */
 
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
jimport('joomla.application.module.helper');

// Import library dependencies
jimport('joomla.application.component.modellist');
jimport('joomla.updater.update');


class JFormFieldb2jupdate extends JFormField {
	protected $type = 'b2jupdate';
    protected function getLabel() {
        return null;
    }

    protected function getInput() {
		$doc = JFactory::getDocument();
        $doc->addScriptDeclaration("jQuery(document).ready( function() {
                                        jQuery('#b2j_update_container').appendTo('#b2j_update_box');
                                    })");
		$m =  explode(DIRECTORY_SEPARATOR,dirname(dirname(dirname(realpath(__FILE__)))));
		$m = $m[count($m)-1];
		
		$eid = $this->getExtensionID($m);
		$this->purge($eid);
		$db = JFactory::getDBO();
		$this->findUpdates(array($eid));
		
		$db->setQuery('SELECT * FROM #__updates WHERE extension_id='.$eid);

		

		if($db->Query()){

			if($row = $db->loadObjectList())
			{
				echo '<span class="b2j_have_update b2j_update_container">
						<a href="'.$row[0]->infourl.'">'.JText::_('COM_B2JCONTACT_HAVE_UPDATE').'</a>
					 </span>';
			}
			else
			{
				echo '<span class="b2j_update b2j_update_container">
				<a href="javascript:void(0);">'.JText::_('COM_B2JCONTACT_UP_TO_DATE').'</a>
				</span>';	
			}
		}
		return null;
    }
	public function getExtensionID($module){
		$db = JFactory::getDBO();
		$db->setQuery('SELECT extension_id FROM #__extensions WHERE element="'.$module.'"');
		if($db->Query()){
			if($row = $db->loadObjectList()){
				return $row[0]->extension_id;
			}else
				return null;
		}
	}
	public function purge($eid)
	{
		$db = JFactory::getDBO();
		$db->setQuery('DELETE FROM #__updates WHERE extension_id='.$eid);
		if ($db->Query()) {
            $db->setQuery('UPDATE #__update_sites SET last_check_timestamp = '.$db->q(0));
			$db->query();
			return true;
		} else {
			return false;
		}
	}
	
	public function findUpdates($eid=0, $cache_timeout = 0)
	{
		$updater = JUpdater::getInstance();
		$error_r = error_reporting();
        error_reporting(0);
		$results = $updater->findUpdates($eid, $cache_timeout);
		error_reporting($error_r);
		return $results;
	}
}