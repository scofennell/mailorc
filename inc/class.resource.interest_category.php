<?php

/**
 * A class for interacting with the lists/$list_id/interest_category/$id endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Interest_Category extends Resource {

	/**
	 * Set up our class members.
	 * 
	 * @param string $list_id The list id for this interest category.
	 * @param string $id      The id for this interest category.
	 * @param array  $args    Other args.
	 */
	function __construct( $list_id, $id, $args = array() ) {

		$this -> id = $id;

		$this -> list_id = $list_id;

		parent::__construct();

	}

	/**
	 * Store the API endpoint for this resource.
	 */
	function set_endpoint() {

		$id      = $this -> get_id();
		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/interest-categories/$id";

	}

	/**
	 * Get a collection of interest objects for this category.
	 * 
	 * @return array An array of Interest objects.
	 */
	function get_collection() {

		$id      = $this -> get_id();
		$list_id = $this -> get_list_id();

		// Get the interests for this category.
		$interests = new Interests( $list_id, $id );
		$interests = $interests -> get_collection();

		return $interests;

	}
	
}