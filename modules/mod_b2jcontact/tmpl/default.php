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

?>

<a name="<?php echo("b2jmoduleid_" . $module->id); ?>"></a>

<div
	id="b2jcontainer_m<?php echo $module->id; ?>"
	class="b2jcontainer <?php echo $params->get("module_class_sfx"); ?>">

	<?php
	if (!empty($page_subheading))
		echo("<h2>" . $page_subheading . "</h2>" . PHP_EOL);

	$messageboard->Display();
	?>

	<?php if (!empty($form_text)) { ?>
	<form enctype="multipart/form-data"
			id="b2j_form_m<?php echo $module->id; ?>"
			name="b2j_form_m<?php echo $module->id; ?>"
			class="b2j_form b2jform-<?php echo $params->get("form_layout", "extended"); ?>"
			method="post"
			action="<?php echo($action); ?>">
		<?php echo($form_text); ?>
	</form>
	<?php } ?>

</div>

