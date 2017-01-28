<?php

/**
 * A class for getting info about our plugin itself.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Landing_Page {

	function __construct() {

		global $mailorc;
		$this -> settings = $mailorc -> settings;

		$this -> set_interests();
		$this -> set_member();		

		add_action( 'wp', array( $this, 'handle' ), 100 );

	}

	function get_member() {

		if( ! isset( $this -> member ) ) { return FALSE; }

		return $this -> member;

	}

	function set_member() {

		if( ! isset( $_GET['email'] ) ) { return FALSE; }

		$email = sanitize_email( $_GET['email'] );

		$member = new Member( $email );

		if( is_wp_error( $member ) ) { return FALSE; }

		$this -> member = $member;

	}

	function get_interests() {
		
		if( ! isset( $this -> interests ) ) { return FALSE; }

		return $this -> interests;

	}

	function set_interests() {

		$interests = array();

		if( ! isset( $_GET['interests'] ) ) { return FALSE; }

		$interests_arr = explode( ',', $_GET['interests'] );

		$interests_arr = array_map( 'sanitize_text_field', $interests_arr );

		foreach( $interests_arr as $interest_id ) {

			$new_interest = new Interest( $interest_id );

			if( is_wp_error( $new_interest ) ) { continue; }

			$interests[ $interest_id ] = $new_interest;

		}

		$interests_count = count( $interests );

		if( empty( $interests_count ) ) { return FALSE; }

		$this -> interests = $interests;

	}

	function handle() {

		if( ! $this -> is_okay_to_run() ) { return FALSE; }

	}
	
	function is_okay_to_run() {

		if( is_admin() ) { return FALSE; }
		if( is_feed() ) { return FALSE; }

		$current_page_id = get_the_ID();
		if( empty( $current_page_id ) ) { return FALSE; }

		$landing_page_id = $this -> settings -> get_subsite_value( 'wordpress_setup', 'landing_page' );
		if( empty( $landing_page_id ) ) { return FALSE; }

		if( $current_page_id != $landing_page_id ) { return FALSE; }

		if( empty( $this -> get_interests() ) ) { return FALSE; }
		if( empty( $this -> get_member() ) ) { return FALSE; }


		return TRUE;

	}

}