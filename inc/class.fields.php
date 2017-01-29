<?php

/**
 * A class for building form fields.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Fields {

	function __construct( $current_value = FALSE ) {

		global $mailorc;

		$this -> current_value = $current_value;
		
	}

	function get_pages_as_options() {

		$pages = get_pages();

		$pages = wp_list_pluck( $pages, 'post_title', 'ID' );

		$pages = array( 0 => esc_html__( 'Please choose a page.', 'mailorc' ) ) + $pages;

		$out = $this -> get_array_as_options( $pages );

		return $out;

	}

	function get_lists_as_options() {

		$lists = new Lists;
		$get_lists = $lists -> get_as_kv();

		$get_lists = array( 0 => esc_html__( 'Please choose a list.', 'mailorc' ) ) + $get_lists;

		$out = $this -> get_array_as_options( $get_lists );

		return $out;

	}	

	function get_array_as_options( array $array ) {

		$out = '';

		foreach( $array as $k => $v ) {

			$selected = selected( $this -> current_value, $k, FALSE );

			$out .= "<option value='$k' $selected>$v</option>";

		}

		return $out;

	}

}