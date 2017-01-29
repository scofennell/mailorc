<?php

/**
 * A class for getting info about our plugin itself.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Meta {

	function __construct() {

		global $mailorc;
		$this -> settings = $mailorc -> settings;

	}

	/**
	 * Get the public-facing name for the plugin.
	 * 
	 * @return string The public-facing name for the plugin.
	 */
	function get_label() {

		return esc_html__( 'MailOrc', 'mailorc' );

	}

	/**
	 * Make a test call to the API to see if we have a valid API key.
	 * 
	 * @return mixed Returns an http response on success, a wp_error on failure.
	 */
	function has_subsite_api_key() {

		$call = new call();

		$response = $call -> get_response();

		if( is_wp_error( $response ) ) {
			return new \WP_Error( 'no_api_key', 'Please provide a valid API Key.', $response );
		}

		return $response;

	}

	/**
	 * Determine if the plugin has been configured with a landing page.
	 * 
	 * @return mixed Returns TRUE on success, a wp_error on failure.
	 */
	function has_landing_page() {

		$get_landing_page = $this -> settings -> get_subsite_value( 'wordpress_setup', 'landing_page' );

		if( empty( $get_landing_page ) ) {
			return new \WP_Error( 'no_landing_page', 'Please choose a landing page.' );
		}

		$get_page = get_page( $get_landing_page );

		if( ! ( is_a( $get_page, 'WP_Post' ) ) ) {
			return new \WP_Error( 'no_landing_page', 'Please choose a landing page.' );
		}		

		return TRUE;

	}	

	function is_landing_page() {

		if( ! $this -> has_landing_page() ) { return FALSE; }

		$current_page_id = get_the_ID();
		if( empty( $current_page_id ) ) { return FALSE; }

		$landing_page_id = $this -> settings -> get_subsite_value( 'wordpress_setup', 'landing_page' );
		if( empty( $landing_page_id ) ) { return FALSE; }

		if( $current_page_id != $landing_page_id ) { return FALSE; }

		return TRUE;

	}

	/**
	 * Determine if the plugin has been configured with a landing page.
	 * 
	 * @return mixed Returns TRUE on success, a wp_error on failure.
	 */
	function has_subsite_list() {

		$get_subsite_list = $this -> get_subsite_list();

		if( empty( $get_subsite_list ) ) {

			return new \WP_Error( 'no_subsite_list', 'Please choose a MailChimp list.' );
	
		}

		return TRUE;

	}	

	function has_subsite_list_obj() {

		$has_subsite_list = $this -> has_subsite_list();

		if( is_wp_error( $has_subsite_list ) ) {
			return $has_subsite_list;
		}

		$list_obj = $this -> get_subsite_list_obj();
		$response = $list_obj -> get_response();
		if( is_wp_error( $response ) ) {
			return new \WP_Error( 'no_subsite_list', 'Please choose a MailChimp list.' ); 
		}

		return TRUE;

	}

	function get_subsite_list() {

		return $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'list_id' );

	}

	function get_subsite_list_obj() {

		return new Single_List( $this -> get_subsite_list() );

	}

	function has_subsite_interests() {

		$subsite_interests = $this -> get_subsite_interests();

		if( is_wp_error( $subsite_interests ) ) {

			return new \wp_error( 'subsite_interests', 'Your list has no interests.' );

		}
	
		return TRUE;

	}

	function get_subsite_interest_categories() {

		$ic = new Interest_Categories( $this -> get_subsite_list() );
	
		$out = $ic -> get_ids();

		$count = count( $out );

		if( empty( $out ) ) {
			return new \WP_Error( 'no_interest_categories', 'Your list has no interest categories.', $ic );
		}

		return $out;

	}

	function get_subsite_interests() {

		$out = array();

		$list_id = $this -> get_subsite_list();

		$interest_categories = $this -> get_subsite_interest_categories();

		if( is_wp_error( $interest_categories ) ) {
			return $interest_categories;
		}

		foreach( $interest_categories as $ic_id ) {

			$interests = new Interests( $list_id, $ic_id );
			$get_interests = $interests -> get_ids();
			foreach( $get_interests as $interest_id ) {

				$out []= $interest_id;

			}

		}

		return $out;

	}

}