<?php

/**
 * A class for interacting with the members/ endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Members extends Resource {

	/**
	 * Set up our class members.
	 * 
	 * @param string $list_id The list ID for this member.
	 * @param array  $args    Other args.
	 */
	function __construct( $list_id, $args = array() ) {

		$this -> list_id = $list_id;

		parent::__construct();

	}

	/**
	 * Set the endpoint for this resource.
	 */
	function set_endpoint() {

		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/members";

	}

	/**
	 * A helper for getting a member by uniqid.
	 * 
	 * @param  string $unique_email_id A uniqid.
	 * @return mixed  Returns an HTTP request for a member, or wp_error.
	 */
	function get_member_by_unique_email_id( $unique_email_id ) {

		$args = array(
			'endpoint' => $this -> get_endpoint(),
			
			// Filter our query to only the member with this ID.
			'params'   => array(
				'unique_email_id' => $unique_email_id,
			),
		);

		$call     = new Call( $args );
		$response =  $call -> get_response();

		if( is_wp_error( $response ) ) { return $response; }

		// We should have gotten a list of members that's exactly 1 member long.
		$members = $response['members'];
		$member_count = count( $members );
		if( $member_count != 1 ) {
			return new \WP_Error( 'member_count', 'Could not find a member with this UNIQID.', $call );
		}

		return $members[0];

	}

}