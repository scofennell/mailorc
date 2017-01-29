<?php

/**
 * A class for interacting with the members/$subscriber_hash endpoint of the MailChimp api.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Member extends Resource {

	function __construct( string $list_id, string $email, $args = array() ) {

		$this -> list_id = $list_id;

		$this -> email = $email;
		$this -> set_hash();


		parent::__construct();

	}

	function get_email() {

		return $this -> email;

	}

	function get_list_id() {

		return $this -> list_id;

	}	

	function set_endpoint() {

		$hash = $this -> get_hash();

		$list_id = $this -> get_list_id();

		$this -> endpoint = "lists/$list_id/members/$hash";

	}

	function set_hash() {

		$this -> hash = md5( strtolower( $this -> get_email() ) );

	}

	function get_hash() {

		return $this -> hash;

	}

	function add_interest( $interest_id ) {

		$args = array(
			'endpoint' => $this -> get_endpoint(),
			'params'   => array(
				'interests' => array(
					$interest_id => TRUE,
				),
			),
			'method'   => 'PATCH',
		);

		$call = new Call( $args );

		return $call -> get_response();

	}

}