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

	function get_interests_by_category() {

		$out = array();

		$list_id = $this -> get_id();

		$interest_categories = $this -> get_interest_categories();

		$interest_category_ids = $interest_categories -> get_ids();

		foreach( $interest_category_ids as $interest_category_id ) {

			$interests = new Interests( $list_id, $interest_category_id );
			$interests = $interests -> get_response();
			$interests = $interests['interests'];

			foreach( $interests as $interest ) {

				$out[ $interest_category_id ][ $interest['id'] ] = new Interest( $list_id, $interest_category_id, $interest['id'] );

			}

		}

		return $out;

	}

	function get_interests_as_list() {

		$out = '';

		$interest_categories = $this -> get_interests_by_category();

		foreach( $interest_categories as $interest_category_id => $interests ) {

			foreach( $interests as $interest ) {

				$name = $interest -> get_name();
				$id = $interest -> get_id();

				$out .= "
					<ul>
						<li>$name: <code>$id</code></li>
					</ul>
				";

			}

		}

		if( empty( $out ) ) { return FALSE; }

		return $out;

	}

	function get_interests_as_comma_sep() {
		
		$out = '';

		$interest_categories = $this -> get_interests_by_category();

		$list_id = $this -> get_id();

		$max = 2;
		$i = 0;

		foreach( $interest_categories as $interest_category_id => $interests ) {

			foreach( $interests as $interest ) {

				$i++;
				$out .= $interest -> get_id() . ',';

				if( $i == $max ) { break; }

			}

			if( $i == $max ) { break; }

		}

		$out = rtrim( $out, ',' );

		if( empty( $out ) ) { return FALSE; }

		return $out;

	}
	
}