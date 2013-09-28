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

	$wholemenu = $this->Application->getMenu();
	$activemenu = $wholemenu->getActive();
	$b2jcomid = $activemenu->id;

	echo
		'<a name="b2jcomid_' . $b2jcomid . '"></a>' .
		'<div ' .
		'id="b2jcontainer_c' . $b2jcomid . '" ' .
		'class="b2jcontainer' . ' '. $this->cparams->get('component_class_sfx') . '">';

	if ($this->cparams->get('show_page_heading'))
		echo("<h1 class='componentheading'>" . $this->escape($this->cparams->get('page_heading')) . "</h1>" . PHP_EOL);

	$page_subheading = $this->cparams->get("page_subheading", "");
	if (!empty($page_subheading))
		echo("<h2>" . $page_subheading . "</h2>" . PHP_EOL);

	$xml = JFactory::getXML(JPATH_ADMINISTRATOR . "/components/" . $GLOBALS["com_name"] . "/" . $GLOBALS["ext_name"] . ".xml");

	$this->MessageBoard->Display();

	if (!empty($this->FormText)) { ?>
	<form enctype="multipart/form-data"
			id="b2j_form_c<?php echo $b2jcomid; ?>"
			name="b2j_form_c<?php echo $b2jcomid; ?>"
			class="b2j_form b2jform-<?php echo $this->cparams->get("form_layout", "extended"); ?>"
			method="post"
			action="<?php echo("#b2jcomid_" . $b2jcomid);?>">
		<?php echo($this->FormText); ?>
	</form>
	
	<?php
	}
	echo('</div>');
?>
