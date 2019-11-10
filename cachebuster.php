<?php
/*------------------------------------------------------------------------
# plg_system_cachebuster - Cachebusting Plugin for Stylesheets
# ------------------------------------------------------------------------
# author    Gary Dominguez
# copyright Copyright (C) 2019 www.garydominguez.com. All Rights Reserved.
# Website: https://www.garydominguez.com
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.browser');
jimport('joomla.filesystem.file');

class plgSystemCacheBuster extends JPlugin{

	public function __construct(& $subject, $params = array()){
		parent::__construct( $subject, $params );
	}

	function onBeforeCompileHead(){

		//error_reporting(E_ALL);
		$app = JFactory::getApplication('site');
		if ( $app->isAdmin()) return;
		$doc = JFactory::getDocument();
        $paths = array();
		$filepaths = array();
		
	    if ($this->params->get('cssfile', '0') == 1){

	        $stylesheets_to_add = trim((string) $this->params->get('cssfilepath', ''));
			$filepaths = array_map('trim', (array) explode("\n", $stylesheets_to_add));
			
	        foreach ($filepaths as $path) {
	        	$doc->addStyleSheet($path);
			}
			
		}
		
		$stylesheets_to_handle = trim((string) $this->params->get('stylesheets_to_handle', ''));
		
		if ($stylesheets_to_handle){

			$paths = array_map('trim', (array) explode("\n", $stylesheets_to_handle));

			foreach ($paths as $path) {

				if (strpos($path,'http')===0){
					continue;
				}
				
				$path = trim($path);
				$uri = JUri::getInstance($path);
				$pathonly = $uri->toString(array('path'));

				if ($pathonly != $path) {
					$paths[] = $pathonly;
				}
				
			}
			
		}
		
		$qsStylesheets = array();
	
        foreach ($doc->_styleSheets as $url => $stylesheetparams){

            //Get the path only
            $searchUrl = trim($url);
            $uri = JUri::getInstance($searchUrl);
            $searchUrl = $uri->toString(array('path'));
			
	    	$filepath = $url;
			$firstcharinpath = substr($filepath, 0, 1);

			if ($firstcharinpath == '/'){
				$filepath = substr_replace($filepath, "", 0, 1);
			}

			clearstatcache();
			$filetime = filemtime($filepath);

			if (in_array($searchUrl,$paths)){
			    $url = $searchUrl .'?v='. $filetime; 
			}

            $qsStylesheets[$url] = $stylesheetparams; //fill array
        }
       
		$doc->_styleSheets = $qsStylesheets;
		return true;
	}
}
