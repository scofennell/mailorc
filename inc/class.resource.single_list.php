<?php

/**
 * A class for interacting with the list/$list_id endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Single_List extends Resource {

	function __construct( string $id ) {

		$this -> id = $id;

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_endpoint() {

		$id = $this -> get_id();

		$this -> endpoint = "lists/$id";

	}

	function get_interest_categories() {

		return new Interest_Categories( $this -> get_id() );

	}

	function get_interests_as_list() {

		$out = '';

		$list_id = $this -> get_id();

		$interest_categories = $this -> get_interest_categories();

		$interest_category_ids = $interest_categories -> get_ids();

		foreach( $interest_category_ids as $interest_category_id ) {

			$interests = new Interests( $list_id, $interest_category_id );

			$out .= "<li>" . $interests -> get_as_list() . "</li>";

		}

		if( empty( $out ) ) { return FALSE; }

		return "<ul>$out</ul>";

	}
	
}