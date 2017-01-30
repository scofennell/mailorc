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

	function __construct( string $list_id, string $id, $args = array() ) {

		$this -> id = $id;

		$this -> list_id = $list_id;

		parent::__construct();

	}

	function set_endpoint() {

		$id = $this -> get_id();

		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/interest-categories/$id";

	}

	function get_collection() {

		$id = $this -> get_id();

		$list_id = $this -> get_list_id();

		$interests = new Interests( $list_id, $id );
		$interests = $interests -> get_collection();

		return $interests;

	}
	
}