<?php
/*------------------------------------------------------------------------
# plg_system_cachebuster - Cachebusting Plugin for Stylesheets
# ------------------------------------------------------------------------
# author    Gary Dominguez
# copyright Copyright (C) 2019 www.garydominguez.com. All Rights Reserved.
# Websites: https://www.garydominguez.com/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgSystemCacheBusterInstallerScript {

	/**
	* Called on installation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function install($adapter) {
	}

	/**
	* Called on uninstallation
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*/
	function uninstall($adapter) {
		//echo '<p>'. JText::_('1.6 Custom uninstall script') .'</p>';
	}

	/**
	* Called on update
	*
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function update($adapter) {
		//echo '<p>'. JText::_('1.6 Custom update script') .'</p>';
	}

	/**
	* Called before any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function preflight($route, $adapter) {
		//echo '<p>'. JText::sprintf('1.6 Preflight for %s', $route) .'</p>';
	}

	/**
	* Called after any type of action
	*
	* @param   string  $route  Which action is happening (install|uninstall|discover_install)
	* @param   JAdapterInstance  $adapter  The object responsible for running this script
	*
	* @return  boolean  True on success
	*/
	function postflight($route, $adapter) {
		
		if ($route=='update') {
			$oldfolders = array();
			foreach ($oldfolders as $oldfolder) {
				if (JFolder::exists($oldfolder))
					JFolder::delete($oldfolder);
			}
	
			$oldfiles = array();
			
			foreach ($oldfiles as $oldfile) {
				if (JFile::exists($oldfile))
					JFile::delete($oldfile);
			}
		}
		
		if ($route=='install' || $route=='update') {
			$lang = JFactory::getLanguage();
			$lang->load('plg_system_cachebuster',JPATH_SITE.'/plugins/system/cachebuster');
			$url = 'index.php?option=com_plugins&filter_search=cachebuster';
	
			$plugin_name = JText::_('PLG_SYSTEM_CACHEBUSTER');
			?>
			<div class="well clearfix">
				<h2><?php echo $plugin_name; ?></h2>
				<p class="lead">Plugin installed</p>
			</div>
			<br />
			<?php
		}
	}
}