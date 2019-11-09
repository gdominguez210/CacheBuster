<?php
/*------------------------------------------------------------------------
# plg_system_cachebuster - Cachebusting Plugin for Stylesheets
# ------------------------------------------------------------------------
# author    Directive Technology Inc.
# copyright Copyright (C) 2019 www.directive.com. All Rights Reserved.
# Website: https://www.directive.com
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.browser');
jimport('joomla.filesystem.file');

class plgSystemCacheBuster extends JPlugin
{

	public function __construct(& $subject, $params = array())
	{
		parent::__construct( $subject, $params );
	}

	function onBeforeCompileHead()
	{
		//error_reporting(E_ALL);
		$app = JFactory::getApplication('site');
		if ( $app->isAdmin()) return; //Exit if in administration
		$doc = JFactory::getDocument();
        $paths = array();
        $filepaths = array();
	    if ($this->params->get('cssfile', '0') == 1) {
	        $stylesheets_to_add = trim( (string) $this->params->get('cssfilepath', ''));
	        $filepaths = array_map('trim', (array) explode("\n", $stylesheets_to_add));
	        foreach ($filepaths as $path) {
	        $doc->addStyleSheet($path);
	        }
	    }
		$stylesheets_to_handle = trim( (string) $this->params->get('stylesheets_to_handle', ''));
		
		if ($stylesheets_to_handle) {
			$paths = array_map('trim', (array) explode("\n", $stylesheets_to_handle));
			foreach ($paths as $path) {

				if (strpos($path,'http')===0) {
					continue;
				}
				
				$path = trim($path);

				//Get the path only
				$uri = JUri::getInstance($path);
				$pathonly = $uri->toString(array('path'));
				if ($pathonly != $path) {
					$paths[] = $pathonly;
				}
				
			}
			
		}
		
		$qsStylesheets = array();
	
        foreach ($doc->_styleSheets as $url => $stylesheetparams) {

            //Get the path only
            $searchUrl = trim($url);
            $uri = JUri::getInstance($searchUrl);
            $searchUrl = $uri->toString(array('path'));
			
		/*	if  (strstr($url, $searchURL)) { //search for instance of filepath specified in plugin field
				   if ($this->params->get('querystring_lm')) { //if plugin is set to use last-modified-date query string
				   	$filename = $searchURL; //shorthand for the filename to use the filemtime function on
					clearstatcache(); //makes sure we're getting fresh php stats
                    $newurl = $url.'?v='.filemtime($filename); // joins the path set in the plugin with the ?v=last-modified-date-timestamp
				}
			}*/
	    	$filepath = $url;
			$firstcharinpath = substr ($filepath, 0, 1);
			if ($firstcharinpath == '/') {
			$filepath = substr_replace ($filepath, "", 0, 1);
			}
			/*if ($firstcharinpath == '/') {
			    $filepath = str_replace($firstcharinpath, "", $filepath);
			}*/
			clearstatcache();
			$filetime = filemtime($filepath);
			if (in_array($searchUrl,$paths)) {
			    $url = $searchUrl .'?v='. $filetime; 
			}
			/*echo "<pre>";
			echo "filepath: "; var_dump($filepath);
		    echo "last-modified-date: ";var_dump($filetime);
		    echo "first character in filepath: ";var_dump($firstcharinpath);
		    echo "new modified filepath: ";var_dump($url);
		    echo "<hr>";
		    echo "</pre>";*/
            $qsStylesheets[$url] = $stylesheetparams; //fill array
        }
       
		$doc->_styleSheets = $qsStylesheets;
		 return true;
	}
}
