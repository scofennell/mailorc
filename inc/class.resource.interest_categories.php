<?php

/**
 * A class for interacting with the lists/$list_id/interest_categories/ endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Interest_Categories extends Resource {

	/**
	 * Set up our class members.
	 * 
	 * @param string $list_id The ID of the list for these categories.
	 * @param array  $args    Other args.
	 */
	function __construct( string $list_id, $args = array() ) {

		$this -> list_id = $list_id;		

		parent::__construct();

	}

	/**
	 * Store the endpoint for making API calls to this resource.
	 */
	function set_endpoint() {

		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/interest-categories";

	}

	/**
	 * Get the ID's for all the items in this collection.
	 * 
	 * @return array The ID's for all the items in this collection.
	 */
	function get_ids() {

		$response = $this -> get_response();

		if( is_wp_error( $response ) ) { return $response; }

		$cats = $response['categories'];

		$out = wp_list_pluck( $cats, 'id' );

		return $out;

	}

	/**
	 * Get the collection of interest category objects, and their interest objects, for these categories.
	 * 
	 * @return array The interest category objects, along with their interest objects.
	 */
	function get_collection() {

		$out = array();

		$response = $this -> get_response();

		$cats = $response['categories'];

		// For each interest category...
		foreach( $cats as $cat ) {

			// Grab the object for it.
			$ic = new Interest_Category( $this -> get_list_id(), $cat['id'] );

			// Store the object.
			$out[ $cat['id'] ]['asset'] = $ic;

			// Store the collection of interest objects.
			$out[ $cat['id'] ]['collection'] = $ic -> get_collection();

		}

		return $out;

	}	
	
	/**
	 * Get the category of an interest.
	 * 
	 * @param  string $interest_id The ID of an interest.
	 * @return string              The ID of the category for an interest.
	 */
	function get_interest_category_id_by_interest_id( $interest_id ) {

		$out = FALSE;

		$response            = $this -> get_response();
		$interest_categories = $response['categories'];
		
		// For each interest category...
		foreach( $interest_categories as $cat ) {

			// Get the interests for this category.
			$interest_category_id = $cat['id'];
			$interests            = new Interests( $this -> get_list_id(), $interest_category_id );
			$interest_ids         = $interests -> get_ids();

			// If this interest is not in this category, move on.
			if( ! in_array( $interest_id, $interest_ids ) ) { continue; }

			// We found the category!
			$out = $interest_category_id;
			break;

		}

		return $out;

	}

}