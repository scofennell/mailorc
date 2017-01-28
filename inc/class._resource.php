<?php

/**
 * A class for interacting with a given MailChimp API endpoint.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

abstract class Resource {

	function __construct( $args = array() ) {

		// Store the endpoint to which we're making api calls.
		$this -> set_slug();

		// Store the args that were passed in.
		$this -> set_args( $args );

	}

	/**
	 * Get the endpoint to which we're making API calls.
	 * 
	 * @return string The endpoint to which we're making API calls.
	 */
	function get_slug() {

		return $this -> slug;

	}

	/**
	 * Store the args that were passed in to our class.
	 */
	function set_args( $args ) {

		$this -> args = $args;

	}

	/**
	 * Get the API response for our resource.
	 * 
	 * @return mixed Returns an http response on success, wp_error on failure.
	 */
	function get_response() {

		$args = array(
			'resource' => $this -> get_slug(),
		);

		$call = new Call( $args );

		return $call -> get_response();

	}

}