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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class B2jInstaller
{
	private $InstallLog;

	protected $component_name;
	protected $extension_name;
	protected $event;


	public function __construct($parent)
	{
	}


	public function install($parent)
	{
		$this->initialize($parent);

		// Environment data
		$this->InstallLog->Write("Running " . $this->event . " on: " . PHP_OS . " | " . $_SERVER["SERVER_SOFTWARE"] . " | php " . PHP_VERSION . " | safe_mode: " . intval(ini_get("safe_mode")) . " | interface: " . php_sapi_name());

		$this->chain_install($parent);
		$this->logo($parent);
	}


	public function uninstall($parent)
	{
	}


	public function update($parent)
	{
	}


	public function preflight($type, $parent)
	{
		$jversion = new JVersion();
		$jmin = (string)$parent->get("manifest")->attributes()->{"version"};
		$jmax = (string)$parent->get("manifest")->{"version"};

		if (version_compare($jversion->RELEASE, $jmin, "<"))
		{
			JFactory::getApplication()->enqueueMessage("B2J Contact " . $jmax . " only works on Joomla " . $jmin . "+", "error");
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_installer', false));
		}

		$this->component_name = $parent->get("element");
		$this->extension_name = substr($this->component_name, 4);
		$this->event = $type;
	}


	public function postflight($type, $parent)
	{
		$this->check_environment($parent);
		$this->InstallLog->Write("Component installation seems successfull.");
	}


	protected function initialize(&$parent)
	{
		(include_once JPATH_ROOT . "/components/" . $parent->get('element') . "/helpers/b2jlogger.php") or die(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", "b2jlogger.php"));
		$this->InstallLog = new B2JLogger("installscript", "install");
	}


	private function chain_install(&$parent)
	{
		$manifest = $parent->get("manifest");
		$extensions = isset($manifest->chain->extension) ? $manifest->chain->extension : new stdClass();
		$this->InstallLog->Write("Found " . count($extensions) . " chained extensions.");

		$result = array();
		foreach ($extensions as $extension)
		{
			$installer = new JInstaller();

			$attributes = $extension->attributes();
			$item = $parent->getParent()->getPath("source") . "/" . $attributes["directory"] . "/" . $attributes["name"];
			$result["type"] = strtoupper((string)$attributes["type"]);
			$result["result"] = $installer->install($item) ? "SUCCESS" : "ERROR";
			$this->results[(string)$attributes["name"]] = $result;
			$this->InstallLog->Write("Installing " . $result["type"] . "... [" . $result["result"] . "]");
		}

		$result["type"] = "COMPONENT";
		$result["result"] = "SUCCESS";
		$this->results[$this->component_name] = $result;
	}


	private function check_environment(&$parent)
	{
		$this->check_permissions($parent);

		$files = JFolder::files(JPATH_ROOT . "/components/" . $parent->get("element") . "/helpers", ".php") or $files = array();
		foreach ($files as $file)
		{
			
			(include_once JPATH_ROOT . "/components/" . $parent->get('element') . "/helpers/" . $file)
				or $this->InstallLog->Write(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", $file));

			$name = JFile::stripExt($file);
			$classname = $name . "CheckEnvironment";	
			if (class_exists($classname))
			{
				$installerclass = new $classname();
			}
		}
	}


	private function check_permissions(&$parent)
	{
		$permissions = fileperms(JPATH_ADMINISTRATOR . "/index.php");
		$buffer = sprintf("Determining correct file permissions...  [%o]", $permissions);
		$this->InstallLog->Write($buffer);
		if ($permissions)
		{
			$files = JFolder::files(JPATH_ROOT . "/components/" . $parent->get("element") . '/lib', ".php", false, true);
			foreach ($files as $file)
			{
				$this->set_permissions($file, $permissions);
			}
		}

		$permissions = fileperms(JPATH_ADMINISTRATOR);
		$buffer = sprintf("Determining correct directory permissions...  [%o]", $permissions);
		$this->InstallLog->Write($buffer);
		if ($permissions)
		{
			$this->set_permissions(JPATH_ROOT . "/components", $permissions);
			$this->set_permissions(JPATH_ROOT . "/components/" . $parent->get("element"), $permissions);
			$this->set_permissions(JPATH_ROOT . "/components/" . $parent->get("element") . "/lib", $permissions);
		}

	}


	private function set_permissions($filename, $permissions)
	{
		jimport("joomla.client.helper");
		$ftp_config = JClientHelper::getCredentials("ftp");

		if ($ftp_config["enabled"])
		{
			jimport("joomla.client.ftp");
			jimport("joomla.filesystem.path");
			$jpath_root = JPATH_ROOT;
			$filename = JPath::clean(str_replace(JPATH_ROOT, $ftp_config["root"], $filename), "/");
			$ftp = new JFTP($ftp_config);
			$result = intval($ftp->chmod($filename, $permissions));
		}
		else
		{
			$result = intval(@chmod($filename, $permissions));
		}

		$this->InstallLog->Write("setting permissions for [$filename]... [$result]");
		return $result;
	}


	private function logo(&$parent)
	{
		$manifest = $parent->get("manifest");
		echo(
			'<div class="b2j_contact_install"><style type="text/css">' .
				'@import url("' . JUri::base(true) . "/components/" . $this->component_name . "/css/install.css" . '");' .
				'</style>' .
				'<img ' .
				'class="install_logo" width="150" ' .
				'src="' . JUri::root() . "/media/" . $this->component_name . "/images/". $this->extension_name ."-logo.png" . '" ' .
				'alt="' . JText::_((string)$manifest->name) . ' Logo" ' .
				'/>' .
			'<div class="install_container">' .
					'<div style="height:104px;">' .
						'<h2 class="install_title">' . JText::_((string)$manifest->name) . '</h2>' .
						'<div class="clear"></div>' .
						'<div class="install_desc">'.JText::_((string)$manifest->desc).'</div>' .
					'</div>');
		echo(	'<div class="install_social_box">
			        <div class="b2j-fb">
				        <div id="fb-root"></div>
						<script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=371410889631698";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, "script", "facebook-jssdk"));</script>
						<div class="fb-like" data-href="http://facebook.com/bang2joom" data-send="false" data-layout="button_count" data-width="205" data-show-faces="false"></div>
			        </div>
			        <span class="b2j-google">
			        	 <link  rel="canonical" href="http://www.bang2joom.com" />
   						 <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
   						  <g:plusone  annotation="none" width="80"></g:plusone>
			        </span>
			        <span class="b2j-twitter"> <a href="https://twitter.com/bang2joom" class="twitter-follow-button" data-show-count="false">Follow @bang2joom</a>
				        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			        </span>
				    <div class="install_donate" style="display: inline-block;width: 92px;height: 26px;float: right;">
				            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LALM7FWPENP3N" target="_blank"></a>
					</div>
				</div>
			</div>');
		foreach ($this->results as $name => $extension)
		{
			echo(
				'<div class="clear"></div>
				<div class="install_row">' .
					'<div class="install_' . strtolower($extension["type"]) . ' install_icon">' . JText::_("COM_INSTALLER_TYPE_" . $extension["type"]) . '</div>' .
					'<div class="install_type_title">' . $name . '</div>' .
					'<div class="install_' . strtolower($extension["result"]) . ' install_status">' . JText::sprintf("COM_INSTALLER_INSTALL_" . $extension["result"], "") . '</div>' .
				'</div>'
			);
		}
		echo('</div>');
	}

}