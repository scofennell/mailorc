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

		// Grab our plugin-wide helpers.
		global $mailorc;
		$this -> meta = $mailorc -> meta;

		// Determine which interests we're adding.
		add_action( 'wp', array( $this, 'set_interests' ), 50 );

		// Grab their names.
		add_action( 'wp', array( $this, 'set_interest_names' ), 51 );

		// Determine the email of the person we're updating.
		add_action( 'wp', array( $this, 'set_email' ), 52 );

		// Grab the object for the person we're updating.
		add_action( 'wp', array( $this, 'set_member' ), 53 );

		// Make a call to add the member to the interests.
		add_action( 'wp', array( $this, 'set_result' ), 100 );

		// Add some UI feedback.
		add_filter( 'wp_footer', array( $this, 'the_feedback' ) );

	}

	/**
	 * Add some console feedback on our attempt to add interests to this person.
	 * 
	 * @param  string $content The post content.
	 * @return string          The post content filtered.
	 */
	function the_feedback() {

		// Are we on the landing page?
		if( is_admin() ) { return FALSE; }
		if( is_feed() ) { return FALSE; }
		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		// Did we get the data we needed?
		if( empty( $this -> get_email() ) && empty( $this -> get_interests() ) ) { return FALSE; }
		
		echo $this -> get_feedback();

	}

	function get_feedback() {
	
		$result = $this -> get_result();
		if( is_wp_error( $result ) ) {
			$result = $result -> get_error_message();
		} else {
			$result = 'SUCCESS';
		}

		$out = array(
			'is_campaign_landing_page' => TRUE,
			'result'                   => $result,
			'email'                    => $this -> get_email(),
			'interests'                => $this -> get_interests(),
			'interest_names'           => $this -> get_interest_names(),
		);

		$out = json_encode( $out );

		$out = "
			<!-- MAILORC FEEDBACK -->
			<script>
				console.log( $out );
			</script>
		";

		return $out;


	}

	/**
	 * Get the email for the member we're updating.
	 * 
	 * @return string The email for the member we're updating.
	 */
	function get_email() {

		if( ! isset( $this -> email ) ) { return FALSE; }

		return $this -> email;

	}

	/**
	 * Store the email for the member we're updating.
	 */
	function set_email() {

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		if( ! isset( $_GET['email'] ) ) { return FALSE; }

		// PHP thinks an email address with a plus in it as actually a space.
		$email = str_replace( ' ', '+', $_GET['email'] );
		$email = sanitize_email( $email );

		$this -> email = $email;

	}

	/**
	 * Get the object for the member we're updating.
	 * 
	 * @return object The object for the member we're updating.
	 */
	function get_member() {

		if( ! isset( $this -> member ) ) { return FALSE; }

		return $this -> member;

	}

	/**
	 * Store the object for the member we're updating.
	 */
	function set_member() {

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		$email = $this -> get_email();

		if( empty( $email ) ) { return FALSE; }

		$list_id = $this -> meta -> get_subsite_list();

		if( empty( $list_id ) ) { return FALSE; }

		$member = new Member( $list_id, $email );

		if( is_wp_error( $member ) ) { return FALSE; }

		$this -> member = $member;

	}

	/**
	 * Get the interests we're adding to the member.
	 * 
	 * @return array The interests we're adding to the member.
	 */
	function get_interests() {
		
		if( ! isset( $this -> interests ) ) { return FALSE; }

		return $this -> interests;

	}

	/**
	 * Store the interests we're adding to the member.
	 */
	function set_interests() {

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		// Were there any in the url?
		if( ! isset( $_GET['interests'] ) ) { return FALSE; }

		// Gran them out of the url.
		$url_interests_arr   = explode( ',', $_GET['interests'] );
		$url_interests_arr   = array_map( 'sanitize_text_field', $url_interests_arr );
		$url_interests_count = count( $url_interests_arr );

		// Get a list of interests for this subsite.
		$subsite_interests = $this -> meta -> get_subsite_interests();

		// Will hold a count of valid interests.
		$interests_count = 0;

		// Loop through the provided interests.
		foreach( $url_interests_arr as $interest_id ) {

			// If it's not valid, skip it.
			if( ! in_array( $interest_id, $subsite_interests ) ) { continue; }

			$interests_count ++;

		}

		// If they weren't all valid, bail.
		if( $url_interests_count != $interests_count ) { return FALSE; }

		$this -> interests = $url_interests_arr;

	}

	/**
	 * Get the result of our API call for adding the interests.
	 * 
	 * @return array An http response.
	 */
	function get_result() {

		if( ! isset( $this -> result ) ) { return FALSE; }

		return $this -> result;

	}

	/**
	 * Store the result of our API call for adding the interests.
	 */
	function set_result() {

		// If it's not okay to make a call, don't!
		if( ! $this -> is_okay_to_run() ) { return FALSE; }

		// Grab the interests.
		$interests      = $this -> get_interests();
		$interest_count = count( $interests );
		
		// Keep track of the interests that were successfully added.
		$add_count = 0;

		// Grab the member to which we're adding interests.
		$member = $this -> get_member();

		// For each interest...
		foreach( $interests as $interest_id ) {

			// Try to add it to the member.
			$add = $member -> add_interest( $interest_id );

			if( ! is_wp_error( $add ) ) {
			
				$add_count++;
			
			} else {

				$out = $add;
				break;

			}

		}

		if( $add_count == $interest_count ) {

			$this -> result = TRUE;

			return;

		}

		$this -> result = $out;

	}
	
	/**
	 * Is it okay to try to add interests to the member?
	 * 
	 * @return boolean Return TRUE if we have everything we need for adding interests, else FALSE.
	 */
	function is_okay_to_run() {

		if( is_admin() ) { return FALSE; }
		if( is_feed() ) { return FALSE; }

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		if( empty( $this -> get_interests() ) ) { return FALSE; }

		if( empty( $this -> get_member() ) ) { return FALSE; }

		return TRUE;

	}

	/**
	 * Get the names of the interests we're adding.
	 * 
	 * @return array The names of the interests we're adding.
	 */
	function get_interest_names() {

		if( ! isset( $this -> interest_names ) ) { return FALSE; }

		return $this -> interest_names;

	}

	/**
	 * Store the names of the interests we're adding.
	 */
	function set_interest_names() {

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		// Get the interests for this page.
		$interests = $this -> get_interests();
		if( ! $interests ) { return FALSE; }

		// Get the interest categories for this subsite.
		$list_id             = $this -> meta -> get_subsite_list();
		$interest_categories = new Interest_Categories( $list_id );

		// For each interest on this page...
		foreach( $interests as $interest_id ) {

			// Get the name.
			$interest_category_id = $interest_categories -> get_interest_category_id_by_interest_id( $interest_id );
			$i_obj                = new Interest( $list_id, $interest_category_id, $interest_id );
			$names[]              = $i_obj -> get_name();

		}

		$this -> interest_names = $names;

	}

}