<?php

/**
 * A class for interacting with the members/$subscriber_hash endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Member extends Resource {

	/**
	 * Set up our class members.
	 * 
	 * @param string $list_id The list ID for this member.
	 * @param string $email   The email address for this member.
	 * @param array  $args    Other args.
	 */
	function __construct( string $list_id, string $email, $args = array() ) {

		$this -> list_id = $list_id;

		$this -> email = $email;
		$this -> set_hash();

		parent::__construct();

	}

	/**
	 * Get the email address for this member.
	 * 
	 * @return string The email address for this member.
	 */
	function get_email() {

		return $this -> email;

	}

	function set_endpoint() {

		$hash = $this -> get_hash();

		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/members/$hash";

	}

	/**
	 * Store the email as a hash per MC conventions.
	 */
	function set_hash() {

		$this -> hash = md5( strtolower( $this -> get_email() ) );

	}

	/**
	 * Get the hashed email.
	 * 
	 * @return string The hashed email.
	 */
	function get_hash() {

		return $this -> hash;

	}

	/**
	 * Add an interest to this member.
	 * 
	 * @return array The result of an API call.
	 */
	function add_interest( $interest_id ) {

		$args = array(
			'endpoint' => $this -> get_endpoint(),
			'params'   => array(
				'interests' => array(
					$interest_id => TRUE,
				),
			),
			'method'   => 'PATCH',
		);

		$call = new Call( $args );

		return $call -> get_response();

	}

}