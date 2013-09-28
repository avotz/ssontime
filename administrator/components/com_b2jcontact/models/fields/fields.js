/* ------------------------------------------------------------------------
 * Bang2Joom Contact for Joomla 3.0+
 * ------------------------------------------------------------------------
 * Copyright (C) 2011-2013 Bang2Joom. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: Bang2Joom
 * Websites: http://www.bang2joom.com
  ------------------------------------------------------------------------
 */
function EmailChooserChange(select)
{
	var visibility = select.options[select.selectedIndex].className;
	var children = document.getElementById(select.id + "_children");

	children.style.display = visibility;
}

window.addEvent('domready',
function()
{
	selects = document.getElementsByTagName('select');

	for (var i = 0; i < selects.length; ++i)
	{
		var select = selects[i];
		if (select.getAttribute('class') == 'b2jemailchooser')
		{
			select.onchange(select);
		}
	}
});