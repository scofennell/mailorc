<?php

/**
 * A class for interacting with the interest endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Interest extends Resource {

	/**
	 * Set up our class members.
	 * 
	 * @param string $list_id              The list ID for the interest.
	 * @param string $interest_category_id The category ID for the interest.
	 * @param string $id                   The ID for the interest.
	 * @param array  $args                 Other args.
	 */
	function __construct( $list_id, $interest_category_id, $id, $args = array() ) {

		$this -> id = $id;

		$this -> interest_category_id = $interest_category_id;

		$this -> list_id = $list_id;		

		parent::__construct();

	}

	/**
	 * Get the ID for the category for this interest.
	 * 
	 * @return string The ID for the category for this interest.
	 */
	function get_interest_category_id() {

		return $this -> interest_category_id;

	}

	/**
	 * Store the endpoint for making calls to this interest.
	 */
	function set_endpoint() {

		$list_id = $this -> get_list_id();

		$interest_category_id = $this -> get_interest_category_id();

		$interest_id = $this -> get_id();

		$this -> endpoint = "lists/$list_id/interest-categories/$interest_category_id/interests/$interest_id";

	}
	
}