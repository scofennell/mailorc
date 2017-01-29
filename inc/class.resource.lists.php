<?php

/**
 * A class for interacting with the list/$list_id endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Lists extends Resource {

	function __construct( $args = array() ) {

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_endpoint() {

		$this -> endpoint = 'lists';

	}

	function get_as_kv() {

		$lists = $this -> get_response();
		if( is_wp_error( $lists ) ) { return $lists; }
		$lists = $lists['lists'];

		foreach( $lists as $list ) {

			$out[ $list['id'] ] = $list['name'];

		}

		return $out;

	}
	
}