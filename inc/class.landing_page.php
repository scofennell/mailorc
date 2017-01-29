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
		$this -> meta = $mailorc -> meta;

		$this -> set_interests();
		$this -> set_interest_names();
		$this -> set_email();
		$this -> set_member();		

		add_action( 'wp', array( $this, 'set_result' ), 100 );

		add_filter( 'the_content', array( $this, 'the_content' ) );

		add_action( 'wp_footer', array( $this, 'comment' ), 100 );

	}

	function the_content( $content ) {

		if( is_admin() ) { return $content; }
		if( is_feed() ) { return $content; }
		if( ! $this -> meta -> is_landing_page() ) { return $content; }


		if( empty( $this -> get_email() ) && empty( $this -> get_interests() ) ) { return $content; }
		

		$class = sanitize_html_class( __CLASS__ . '-' . __FUNCTION__ );

		$result = $this -> get_result();

		$interest_names = $this -> get_interest_names();
		$interest_names_count = count( $interest_names );
		$interest_names_str = '';
		$i = 0;
		if( is_array( $interest_names ) ) {
			foreach( $interest_names as $interest_name ) {

				$interest_names_str .= "<strong>$interest_name</strong>";

				$i++;

				if( $i == ( $interest_names_count - 1 ) ) {

					$interest_names_str .= ' ' . esc_html__( 'and', 'mailorc' ) . ' ';

				} elseif( $i < $interest_names_count ) {

					$interest_names_str .= ' ' . esc_html__( ',', 'mailorc' ) . ' ';

				}

			}

		}

		if( $result ) {

			$message = sprintf( esc_html__( 'Thank you for reading %s!', 'mailorc' ), $interest_names_str );

		} else {

			if( empty( $interest_names_str ) ) {
				$interest_names_str = '<strong>(empty)</strong>';
			}

			$message = sprintf( esc_html__( 'There has been an error attempting to thank you for reading %s.', 'mailorc' ), $interest_names_str );

		}

		return "<p class='$class'>$message</p>$content";

	}

	function get_email() {

		if( ! isset( $this -> email ) ) { return FALSE; }

		return $this -> email;

	}

	function set_email() {


		if( ! isset( $_GET['email'] ) ) { return FALSE; }

		$email = sanitize_email( $_GET['email'] );

		$this -> email = $email;

	}

	function get_member() {

		if( ! isset( $this -> member ) ) { return FALSE; }

		return $this -> member;

	}

	function set_member() {

		$email = $this -> get_email();

		$list_id = $this -> meta -> get_subsite_list();

		$member = new Member( $list_id, $email );

		if( is_wp_error( $member ) ) { return FALSE; }

		$this -> member = $member;

	}

	function get_interests() {
		
		if( ! isset( $this -> interests ) ) { return FALSE; }

		return $this -> interests;

	}

	function set_interests() {

		if( ! isset( $_GET['interests'] ) ) { return FALSE; }

		$url_interests_arr = explode( ',', $_GET['interests'] );

		$url_interests_count = count( $url_interests_arr );

		$url_interests_arr = array_map( 'sanitize_text_field', $url_interests_arr );

		$interests_count = 0;

		$subsite_interests = $this -> meta -> get_subsite_interests();

		foreach( $url_interests_arr as $interest_id ) {

			if( ! in_array( $interest_id, $subsite_interests ) ) { continue; }

			$interests_count ++;

		}

		if( $url_interests_count != $interests_count ) { return FALSE; }

		$this -> interests = $url_interests_arr;

	}

	function get_result() {

		if( ! isset( $this -> result ) ) { return FALSE; }

		return $this -> result;

	}

	function set_result() {

		if( ! $this -> is_okay_to_run() ) { return FALSE; }

		$interests = $this -> get_interests();
		$interest_count = count( $interests );
		$add_count = 0;

		$member = $this -> get_member();

		$out = array();

		foreach( $interests as $interest_id ) {

			$add = $member -> add_interest( $interest_id );

			if( ! is_wp_error( $add ) ) {
			
				$add_count++;
			
			} else {

				break;

			}

		}

		if( $add_count == $interest_count ) {

			$this -> result = TRUE;

			return;

		}

		$this -> result = FALSE;

	}
	
	function is_okay_to_run() {

		if( is_admin() ) { return FALSE; }
		if( is_feed() ) { return FALSE; }

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		if( empty( $this -> get_interests() ) ) { return FALSE; }

		if( empty( $this -> get_member() ) ) { return FALSE; }

		return TRUE;

	}

	function comment() {

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		echo $this -> get_comment();

	}

	function get_comment() {

		$messages = array(
			sprintf( esc_html__( 'This is your %s campaign landing page!', 'mailorc' ), $this -> meta -> get_label() ),
			sprintf( esc_html__( 'Email: %s', 'mailorc' ), $this -> get_email() ),
			sprintf( esc_html__( 'Interests: %s', 'mailorc' ), json_encode( $this -> get_interests() ) ),	
		);

		$message = implode( ' | ', $messages );

		$out = "<!-- $message -->";

		return $out;

	}

	function get_interest_names() {

		if( ! isset( $this -> interest_names ) ) { return FALSE; }

		return $this -> interest_names;

	}

	function set_interest_names() {

		$list_id = $this -> meta -> get_subsite_list();

		$interests = $this -> get_interests();

		if( ! $interests ) { return FALSE; }

		$interest_categories = new Interest_Categories( $list_id );

		foreach( $interests as $interest_id ) {

			$interest_category_id = $interest_categories -> get_interest_category_id_by_interest_id( $interest_id );

			$i_obj = new Interest( $list_id, $interest_category_id, $interest_id );

			$names[] = $i_obj -> get_name();

		}

		$this -> interest_names = $names;

	}

}