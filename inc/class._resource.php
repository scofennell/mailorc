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

		global $mailorc;
		$this -> settings = $mailorc -> settings;

		// Store the endpoint to which we're making api calls.
		$this -> set_endpoint();

		// Store the args that were passed in.
		$this -> set_args( $args );

	}

	function get_id() {

		return $this -> id;

	}

	function get_list_id() {

		return $this -> list_id;

	}

	function get_subsite_list() {

		return $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'list_id' );

	}

	/**
	 * Get the endpoint to which we're making API calls.
	 * 
	 * @return string The endpoint to which we're making API calls.
	 */
	function get_endpoint() {

		return $this -> endpoint;

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
			'endpoint' => $this -> get_endpoint(),
		);

		$call = new Call( $args );

		return $call -> get_response();

	}

	function get_title() {

		$r = $this -> get_response();

		if( ! isset( $r['title'] ) ) { return FALSE; }

		return $r['title'];

	}	

	function get_name() {

		$r = $this -> get_response();

		if( ! isset( $r['name'] ) ) { return FALSE; }

		return $r['name'];

	}	

}