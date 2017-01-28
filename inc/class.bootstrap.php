<?php

/**
 * A class for loading our plugin.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

new Bootstrap;

class Bootstrap {

	function __construct() {

		$this -> load();

		$this -> create();

	}
	
	/**
	 * Load our plugin files.
	 * 
	 * @return boolean Returns FALSE if it loads all of its files, else TRUE.
	 */
	function load() {

		// For each php file in the inc/ folder, require it.
		foreach( glob( MAILORC_PATH . 'inc/*.php' ) as $filename ) {

			require_once( $filename );

		}

		return TRUE;

	}

	/**
	 * Instantiate and store a bunch of our plugin classes.
	 */
	function create() {

		global $mailorc;

		$mailorc -> settings              = new Settings;
		$mailorc -> meta                  = new Meta;
		$mailorc -> enqueue               = new Enqueue;
		$mailorc -> landing_page          = new Landing_Page;				
		$mailorc -> subsite_control_panel = new Subsite_Control_Panel;
		
		return $mailorc;

	}

}