<?php

/**
 * @file CustomizedSjarPlugin.inc.php
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * With contributions from:
 * 	- 2014 Instituto Nacional de Investigacion y Tecnologia Agraria y Alimentaria
 *
 * @class CustomizedSjarPlugin
 * @ingroup plugins_generic_CustomizedSjar
 *
 * @brief This plugin helps with translation maintenance.
 */

// $Id$


import('lib.pkp.classes.plugins.GenericPlugin');

class CustomizedSjarPlugin extends GenericPlugin {
	
	function register($category, $path) {
			if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
  			HookRegistry::register ('LoadHandler', array(&$this, 'handleRequest'));
				
//				exit;
			}
			return true;
		}
		return false;
	}
/**
	 * Get the management plugin
	 * @return object
	 */
	function &getManagerPlugin() {
		$plugin =& PluginRegistry::getPlugin('generic', $this->parentPluginName);
		return $plugin;
	}
	function handleRequest($hookName, $args) {
		$page =& $args[0];
		$op =& $args[1];
		$sourceFile =& $args[2];

		if ($page === 'index' or $page === '' ) {
//			  if ($op==""){ 
//			$op='index';
			$this->import('CustomizedSjarHandler');
     	Registry::set('plugin', $this);
     	define('HANDLER_CLASS', 'CustomizedSjarHandler');
//    	exit;
			return true;
	
		}


return false;  
	}

	function getDisplayName() {
		return Locale::translate('plugins.generic.CustomizedSjar.name');
	}

	function getDescription() {
		return Locale::translate('plugins.generic.CustomizedSjar.description');
	}

	function isSitePlugin() {
		return true;
	}
/**
	 * Override the builtin to get the correct plugin path.
	 * @return string
	 */
	
}

?>
