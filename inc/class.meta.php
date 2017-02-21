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
	 * Determine if the plugin has been configured with landing pages.
	 * 
	 * @return mixed Returns TRUE on success, a wp_error on failure.
	 */
	function has_landing_pages() {

		$get_landing_pages = $this -> settings -> get_subsite_value( 'wordpress_setup', 'landing_pages' );

		$error = new \WP_Error( 'no_landing_pages', 'Please choose at least one landing page.' );

		if( ! is_array( $get_landing_pages ) ) {
			return $error;
		}

		$count = count( $get_landing_pages );
		if( empty( $count ) ) {
			return $error;
		}

		foreach( $get_landing_pages as $id ) {
			
			$get_page = get_page( $id );

			if( is_a( $get_page, 'WP_Post' ) ) {
				
				return TRUE;

			}		

		}

		return $error;
	
	}

	/**
	 * Determine if we are on the landing page.
	 * 
	 * @return boolean Returns TRUE if we are on the landing page, else FALSE.
	 */
	function is_landing_page() {

		// No landing page?
		if( is_wp_error( $this -> has_landing_pages() ) ) { return FALSE; }

		// No page ID?
		$current_page_id = get_the_ID();
		if( empty( $current_page_id ) ) { return FALSE; }

		// Not the landing page?
		$landing_pages = $this -> settings -> get_subsite_value( 'wordpress_setup', 'landing_pages' );
		if( ! in_array( $current_page_id, $landing_pages ) ) { return FALSE; }

		return TRUE;

	}

	/**
	 * Determine if the plugin has been configured with a list ID.
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

	/**
	 * Determine if the plugin has been configured with valid list ID.
	 * 
	 * @return mixed Returns TRUE on success, a wp_error on failure.
	 */
	function has_subsite_list_obj() {

		$has_subsite_list = $this -> has_subsite_list();

		if( is_wp_error( $has_subsite_list ) ) {
			return $has_subsite_list;
		}

		// Make a test call to assess the validity of the list.
		$list_obj = $this -> get_subsite_list_obj();
		$response = $list_obj -> get_response();

		if( is_wp_error( $response ) ) {
			return new \WP_Error( 'no_subsite_list', 'Please choose a MailChimp list.' ); 
		}

		return TRUE;

	}

	/**
	 * Get the site-wide list ID.
	 * 
	 * @return string the site-wide list ID.
	 */
	function get_subsite_list() {

		return $this -> settings -> get_subsite_value( 'mailchimp_account_setup', 'list_id' );

	}

	/**
	 * The site-wide list object.
	 * 
	 * @return object The site-wide list object.
	 */
	function get_subsite_list_obj() {

		return new Single_List( $this -> get_subsite_list() );

	}

	/**
	 * Determine if the site-wide list has interests.
	 * 
	 * @return boolean Returns TRUE if the site-wide list has interests, else FALSE.
	 */
	function has_subsite_interests() {

		$subsite_interests = $this -> get_subsite_interests();

		if( is_wp_error( $subsite_interests ) ) {
			return new \wp_error( 'subsite_interests', 'Your list has no interests.' );
		}
	
		$count = count( $subsite_interests );
		if( empty( $count ) ) {
			return new \wp_error( 'subsite_interests', 'Your list has no interests.' );
		}

		return TRUE;

	}

	/**
	 * Get the interest categories for the sitewide list.
	 * 
	 * @return mixed Returns an array of interest category ID's or a wp_error.
	 */
	function get_subsite_interest_categories() {

		$ic = new Interest_Categories( $this -> get_subsite_list() );
	
		$out = $ic -> get_ids();

		$count = count( $out );

		if( empty( $out ) ) {
			return new \WP_Error( 'no_interest_categories', 'Your list has no interest categories.', $ic );
		}

		return $out;

	}

	/**
	 * Get the interests for the sitewide list.
	 * 
	 * @return mixed Returns an array of interest objects or a wp_error.
	 */
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