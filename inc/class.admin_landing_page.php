<?php

/**
 * A class for getting info about our plugin itself.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Admin_Landing_Page {

	function __construct() {

		global $mailorc;
		$this -> meta = $mailorc -> meta;

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 100 );

	}
	
	/**
	 * Echo a reminder to the admin that the current page is his landing page.
	 */
	function admin_notices() {

		if( ! $this -> meta -> is_landing_page() ) { return FALSE; }

		echo $this -> get_admin_notices();
	}

	/**
	 * Return a reminder to the admin that the current page is his landing page.
	 * 
	 * @return string A reminder to the admin that the current page is his landing page.
	 */
	function get_admin_notices() {

		$message = sprintf( esc_html__( 'This is your %s campaign landing page!', 'mailorc' ), $this -> meta -> get_label() );

		$out = "
			<div class='notice notice-warning is-dismissible'>
				<p>$message</p>
			</div>
		";

		return $out;

	}

}