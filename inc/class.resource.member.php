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
	 * @param string $list_id    The list ID for this member.
	 * @param string $identifier The email address or unique ID for this member.
	 * @param array  $args       Other args.
	 */
	function __construct( $list_id, $identifier, $args = array() ) {

		$this -> list_id    = $list_id;
		$this -> identifier = $identifier;

		$this -> set_unique_email_id();
		$this -> set_email();
		$this -> set_hash();

		parent::__construct();

	}

	/**
	 * Get the identifier for this member.
	 * 
	 * @return string The email address or uniqid for this member.
	 */
	function get_identifier() {

		return $this -> identifier;

	}

	/**
	 * Get the email for this member.
	 * 
	 * @return string The email address for this member.
	 */
	function get_email() {

		return $this -> email;

	}

	/**
	 * Set the email for this member.
	 */
	function set_email() {

		// Let's see how the class was instantiated.
		$identifier = $this -> get_identifier();

		// If it was with an email, great, we're ready.
		if( sanitize_email( $identifier ) == $identifier ) {

			$this -> email = $identifier;

		// If not, it was with a uniqid.
		} else {

			$this -> email = $this -> get_email_by_unique_email_id();

		}

	}

	/**
	 * Get the uniqid for this member.
	 * 
	 * @return string The uniqid for this member.
	 */
	function get_unique_email_id() {

		return $this -> unique_email_id;

	}

	/**
	 * Set the uniqid for this member.
	 */
	function set_unique_email_id() {

		// Let's see how the class was instantiated.
		$identifier = $this -> get_identifier();
		
		// If it was not with an email, great, we're ready.
		if( sanitize_email( $identifier ) != $identifier ) {

			$this -> unique_email_id = $identifier;

		// Otherwise we'll never need it or get it.
		} else {

			$this -> unique_email_id = FALSE;

		}

	}	

	/**
	 * Store the endpoint to which this class makes API requests.
	 */
	function set_endpoint() {

		$hash    = $this -> get_hash();
		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/members/$hash";

	}

	/**
	 * Store the email as a hash per MC conventions.
	 */
	function set_hash() {

		if( is_wp_error( $this -> get_email() ) ) { return FALSE; }

		$this -> hash = md5( strtolower( $this -> get_email() ) );

	}

	/**
	 * Get the hashed email.
	 * 
	 * @return string The hashed email.
	 */
	function get_hash() {

		if( ! isset( $this -> hash ) ) { return FALSE; }

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

	/**
	 * Get the email via the uniqid.
	 * 
	 * @return string The member email.
	 */
	function get_email_by_unique_email_id() {

		// Fire up the members endpoint.
		$members = new Members( $this -> get_list_id() );

		// Use the members helper function for getting a member by uniqid.
		$member = $members -> get_member_by_unique_email_id( $this -> get_unique_email_id() );

		if( is_wp_error( $member ) ) { return $member; }

		$email = $member['email_address'];

		return sanitize_email( $email );

	}

}