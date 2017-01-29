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

	function __construct( string $list_id, $args = array() ) {

		$this -> list_id = $list_id;		

		parent::__construct();

	}

	function set_endpoint() {

		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/interest-categories";

	}

	function get_ids() {

		$response = $this -> get_response();

		if( is_wp_error( $response ) ) { return $response; }

		$cats = $response['categories'];

		$out = wp_list_pluck( $cats, 'id' );

		return $out;

	}
	
	function get_interest_category_id_by_interest_id( $interest_id ) {

		$out = FALSE;

		$response = $this -> get_response();
		$interest_categories = $response['categories'];
		foreach( $interest_categories as $cat ) {

			$interest_category_id = $cat['id'];

			$interests = new Interests( $this -> get_list_id(), $interest_category_id );
			$interest_ids = $interests -> get_ids();

			if( ! in_array( $interest_id, $interest_ids ) ) { continue; }

			$out = $interest_category_id;

		}

		return $out;

	}

}