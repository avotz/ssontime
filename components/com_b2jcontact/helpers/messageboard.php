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

class B2JMessageBoard
{
	const success = 0x01;
	const info = 0x02;
	const warning = 0x04;
	const error = 0x08;

	protected $Level = 0;
	protected $Messages = array();
	public static $Levels = array(
		B2JMessageBoard::success => "success",
		B2JMessageBoard::success => "info",
		B2JMessageBoard::warning => "warning",
		B2JMessageBoard::error => "error"
	);


	public function Add($message, $level = 0)
	{
		$this->Messages[] = $message;
		$this->RaiseLevel($level);
	}


	public function Append($messages, $level = 0)
	{
		$this->Messages += $messages;
		$this->RaiseLevel($level);
	}


	public function RaiseLevel($level)
	{
		if ($level > $this->Level) $this->Level = $level;
	}


	public function Display()
	{
		echo $this->__toString();
	}


	public function __toString()
	{
		$result = "";
		if (!count($this->Messages)) return $result;


		$result .= '<div class="alert alert-' . B2JMessageBoard::$Levels[$this->Level] . '">' .
			'<ul class="b2j_messages">';

		foreach ($this->Messages as $message)
		{
			$result .= '<li>' . $message . '</li>';
		}

		$result .= '</ul>' .
			'</div>';

		return $result;
	}
}