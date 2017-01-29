<?php

/**
 * A class for interacting with the lists/$list_id/interest_categories/$cat_id/interests endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Interests extends Resource {

	function __construct( string $list_id, string $interest_category_id, $args = array() ) {

		$this -> interest_category_id = $interest_category_id;

		$this -> list_id = $list_id;

		parent::__construct();

	}

	function get_interest_category_id() {

		return $this -> interest_category_id;

	}

	function get_ids() {

		$response = $this -> get_response();
		$interests = $response['interests'];

		return wp_list_pluck( $interests, 'id' );

	}

	function set_endpoint() {

		$list_id = $this -> get_list_id();

		$interest_category_id = $this -> get_interest_category_id();

		$this -> endpoint = "lists/$list_id/interest-categories/$interest_category_id/interests";

	}

}