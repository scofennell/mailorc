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
	function has_api_key() {

		$call = new call();

		$response = $call -> get_response();

		return $response;

	}

	/**
	 * Determine if the plugin has been configured with a landing page.
	 * 
	 * @return mixed Returns TRUE on success, a wp_error on failure.
	 */
	function has_landing_page() {

		$get_landing_page = $this -> settings -> get_value( 'wordpress_setup', 'landing_page' );

		if( empty( $get_landing_page ) ) {
			return new \WP_Error( 'no_landing_page', 'Please choose a landing page.' );
		}

		return TRUE;

	}	

}