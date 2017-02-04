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

	function __construct( $list_id, $interest_category_id, $args = array() ) {

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

	function get_as_list() {

		$out = '';

		$response = $this -> get_response();
		$interests = $response['interests'];



		foreach( $interests as $interest ) {

			$name = $interest['name'];
			$id   = $interest['id'];			

			$out .= "<li><strong>$name</strong>: $id</li>";

		}

		if( empty( $out ) ) { return FALSE; }

		$out = "<ul>$out</ul>";

		return $out;


	}

	function get_collection() {

		$out = array();

		$response = $this -> get_response();
		$interests = $response['interests'];

		$list_id = $this -> get_list_id();

		$interest_category_id = $this -> get_interest_category_id();

		foreach( $interests as $interest ) {

			$out[ $interest['id'] ] = new Interest( $list_id, $interest_category_id, $interest['id'] );

		}

		return $out;

	}

}