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

global $sh_LANG;
$sefConfig = & shRouter::shGetConfig();  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;

if(isset($Itemid)){
	shRemoveFromGETVarsList('Itemid');
}else{
	$Itemid = null;
}
shRemoveFromGETVarsList('lang');
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('bid');

if (strpos($string, "view=loader") === false)
{
	shRemoveFromGETVarsList("view");
}

if(!isset($task)){
	$task = '';
}

$title[] = getMenuTitle($option, $task, $Itemid, null, $shLangName);
  
if ($dosef){
   $string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
      (isset($shLangName) ? @$shLangName : null));
}
  
