<?php

/**
 * A class for interacting with the members endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Members extends Resource {

	function __construct( $args = array() ) {

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_slug() {

		$this -> slug = 'members';

	}
	
}