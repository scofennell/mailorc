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

	function __construct( $id ) {

		$this -> id = sanitize_text_field( $id );

		parent::__construct();

	}

	/**
	 * The endpoint in the MailChimp API.
	 */
	function set_endpoint() {

		$id = $this -> get_id();

		$this -> endpoint = "lists/$id";

	}

	/**
	 * Call MailChimp and get a list of all of the interest categories for this list.
	 * 
	 * @return array An array of interest categories.
	 */
	function get_interest_categories() {

		$ic = new Interest_Categories( $this -> get_id() );

		return $ic -> get_collection();

	}

	/**
	 * Call MailChimp and get a list of all of the interests for this list.
	 * 
	 * @return string An html list of interests.
	 */
	function get_interests_as_list() {

		$out = '';

		$interest_categories = $this -> get_interest_categories();

		foreach( $interest_categories as $interest_category_id => $interest_category ) {

			$ic_asset = $interest_category['asset'];
			$ic_collection = $interest_category['collection'];
			$ic_title = $ic_asset -> get_title();

			$out .= "<ul><h4>$ic_title</h4>";

			foreach( $ic_collection as $interest_id => $interest ) {

				$name = $interest -> get_name();
				$id = $interest -> get_id();

				$out .= "
					<li>$name: <code>$id</code></li>
				";

			}

			$out .= '</ul>';

		}

		if( empty( $out ) ) { return FALSE; }

		return $out;

	}

	/**
	 * Call MailChimp and get a list of some of the interests for this list.
	 * 
	 * @return string An comma-sep list of interests.
	 */
	function get_interests_as_comma_sep() {
	
		$out = '';

		$interest_categories = $this -> get_interest_categories();

		$list_id = $this -> get_id();

		// The max number of results to include.
		$max = 2;

		$i = 0;
		foreach( $interest_categories as $interest_category_id => $interest_category ) {

			$ic_asset = $interest_category['asset'];
			$ic_collection = $interest_category['collection'];

			foreach( $ic_collection as $interest_id => $interest ) {

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