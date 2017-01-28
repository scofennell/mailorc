<?php

/**
 * A class for creating a dashboard widget.
 *
 * @package WordPress
 * @subpackage Mailorc
 * @since Mailorc 0.1
 */

namespace Mailorc;

class Subsite_Control_Panel {

	function __construct() {

		// Grab our plugin-wide helpers.
		global $mailorc;
		$this -> meta     = $mailorc -> meta;
		$this -> settings = $mailorc -> settings;

		// Add our options page to wp-admin.
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Register our options sections.
		add_action( 'admin_init', array( $this, 'register' ) );

		// Register our admin notices.
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

	}

	/**
	 * Determine if we are on the settings page.
	 * 
	 * @return boolean Returns TRUE if we are on the settings page, else FALSE.
	 */
	function is_current_page() {

		global $pagenow;

		// If we're not in either of these two, bail.  options.php is required for form handling.
		if( ( $pagenow != 'options-general.php' ) && ( $pagenow != 'options.php' ) ) { return FALSE; }

		if( $pagenow == 'options-general.php' ) {

			if( ! isset( $_GET['page'] ) ) { return FALSE; }

			if( $_GET['page'] != MAILORC ) { return FALSE; }

		} 

		return TRUE;

	}

	/**
	 * Add our plugin settings page.
	 */
	function add_options_page() {

		$page_title = $this -> meta -> get_label();
		$menu_title = $this -> meta -> get_label();
		$capability = 'update_core';
		$menu_slug  = MAILORC;
		$function   = array( $this, 'the_page' );
		#$icon_url   = 'dashicons-email';
		#$position   = 100;


		$out = add_options_page(
			$page_title,
			$menu_title,
			$capability,
			$menu_slug,
			$function
		);

		return $out;

	}

	/**
	 * Callback function for add_options_page.
	 */
	function the_page() {

		echo $this -> get_page();

	}

	/**
	 * Get the content for the options page.
	 * 
	 * @return string The content for our options page.
	 */
	function get_page() {

		$title = '<h1>' . $this -> meta -> get_label() . '</h1>';
		$form = $this -> get_form();

		$out = "
			<div class='wrap'>
				$title
				$form
			</div>
		";

		return $out;

	}

	/**
	 * Get the settings form.
	 * 
	 * @return string The settings form.
	 */
	function get_form() {

		// Get the definition of our settings.
		$get_settings = $this -> settings -> get_settings();

		// Will hold form fields.
		$out = '';

		// Start an output buffer since some of these functions always echo.
		ob_start();

		// Dump the nonce and some other hidden form stuff into the OB.
		settings_fields( MAILORC );

		// Dump the form inputs into the OB.
		do_settings_sections( MAILORC );

		// Grab the stuff from the OB, clean the OB.
		$form_fields = ob_get_clean();

		// Grab a submit button.
		$submit = $this -> get_submit_button();

		// Nice!  Time to build the page!
		$out = "
			<form method='POST' action='options.php'>
				$form_fields
				$submit
			</form>
		";

		return $out;

	}

	/**
	 * Get an HTML input of the submit type.
	 * 
	 * @return string An HTML input of the submit type.
	 */
	public function get_submit_button() {

		// Args for get_submit_button().
		$text             = esc_html__( 'Submit', 'mailorc' );
		$type             = 'primary';
		$name             = 'submit';
		$wrap             = FALSE;
		$other_attributes = array();

		// Grab the submit button.
		$out = get_submit_button(
			$text,
			$type,
			$name,
			$wrap,
			$other_attributes
		);

		return $out;

	}	

	/**
	 * Loop through our settings and register them.
	 */
	public function register() {

		if( ! $this -> is_current_page() ) { return FALSE; }

		// Grab our plugin settings definition.
		$subsite_settings = $this -> settings -> get_subsite_settings();

		// For each section of settings...
		foreach( $subsite_settings as $section_id => $section ) {

			// Grab the label.
			$section_label = $section['label'];
			
			// Add the section.
			add_settings_section(
				
				// The ID for this settings section.
				$section_id,

				// The label for this settings section.
				$section_label,

				// Could provide a cb function here to output some help text, but don't need to.
				array( $this, 'get_section_description' ),

				// Needs to match the first arg in register_setting().
				MAILORC

			);

			// For each setting in this section...
			foreach( $section['settings'] as $setting_id => $setting ) {

				// The setting label.
				$label    = $setting['label'];

				// The cb to draw the input for this setting.
				$callback = array( $this, 'the_field' );

				/**
				 * $args to pass to $callback.
				 * We'll pass it the setting as an array member of the settings section.
				 */
				$args = array(
					'section_id' => $section_id,
					'setting_id' => $setting_id,
					'setting'    => $setting,
				);

				// Add the settings field.
				add_settings_field(
					
					$setting_id,
					$label,
					
					// Echo the form input.
					$callback,
					
					// Matches the value in do_settings_sections().
					MAILORC,
					
					// Matches the first arg in add_settings_section().
					$section_id,

					// Passed to $callback.
					$args
				
				);

			}

		}

		// Designate a sanitization function for our settings.
		$sanitize_callback = array( $this, 'sanitize' );

		// Register the settings!
		register_setting(

			// Matches the value in settings_fields().
			MAILORC,

			// The name for our option in the DB.
			MAILORC,
			
			// The callback function for sanitizing values.
			$sanitize_callback
		
		);

	}

	function the_section_description( $section ) {

		echo $this -> get_section_description( $section );

	}

	function get_section_description( $section ) {

		$section_id = $section['id'];

		$section = $this -> settings -> get_section( $section_id );

		return $section['description']; 

	}

	/**
	 * Output an HTML form field.
	 * 
	 * @param  array $args An array of args from add_settings_field(). Contains settings section and setting.
	 */
	public function the_field( $args = array() ) {
	
		$out = $this -> get_field( $args );

		echo $out;

	}

	/**
	 * Get an HTML form field.
	 * 
	 * @param  array  $args An array of args from add_settings_field(). Contains settings section and setting.
	 * @return string An HTML form field.
	 */
	public function get_field( $args = array() ) {

		$section_id = $args['section_id'];
		$setting_id = $args['setting_id'];
		$setting    = $args['setting'];
		
		// Get our plugin option.  We'll need it to prepopulate the form fields.
		$value = esc_attr( $this -> settings -> get_subsite_value( $section_id, $setting_id ) );

		// It's probably a text input!
		$type = $setting['type'];

		// The ID for the input, expected by the <label for=''> that get's printed via do_settings_sections().
		$id = MAILORC . "-$section_id-$setting_id";

		$description = $setting['description'];

		/**
		 * The name of this setting.
		 * It's a member of the section array, which in turn is a member of the plugin array.
		 */
		$name = MAILORC . '[' . $section_id . ']' . '[' . $setting_id . ']';

		$attrs = '';
		if( isset( $setting['attrs'] ) ) {
			$attrs = $this -> get_attrs_from_array( $setting['attrs'] );
		}

		$description = "<p class='description'>$description</p>";

		if( $type == 'select' ) {

			$options_class = __NAMESPACE__ . '\\' . $setting['options_cb'][0];
			$options_obj = new $options_class( $value );
			$options_method = $setting['options_cb'][1];
			$options = call_user_func( array( $options_obj, $options_method ) );

			$out = "
				<select $attrs class='regular-text' id='$id' name='$name' >
					$options
				</select>
				$description
			";

		} else {

			$out = "
				<input $attrs class='regular-text' type='$type' id='$id' name='$name' value='$value'>
				$description
			";

		}

		return $out;

	}	

	/**
	 * Convert an associative array into html attributes.
	 * 
	 * @param  array $array An associative array.
	 * @return string       HTML attributes.
	 */
	function get_attrs_from_array( array $array ) {

		$out = '';

		foreach( $array as $k => $v ) {

			$k = sanitize_key( $k );
			$v = esc_attr( $v );

			$out .= " $k='$v' ";

		}

		return $out;

	}

	/**
	 * Our sanitize_callback for register_setting().
	 * 
	 * @param  array  $dirty The form values, dirty.
	 * @return array  The form values, clean.
	 */
	public function sanitize( $dirty = array() ) {

		// Will hold cleaned values.
		$clean = array();

		// For each section of settings...
		foreach( $dirty as $section => $settings ) {

			// For each setting...
			foreach( $settings as $k => $v ) {

				// The only tags should be script tags.  Weird, right?
				$v = sanitize_text_field( $v );

				// Nice!  Pass the cleaned value into the array.
				$clean[ $section ][ $k ] = $v;

			}
	
		}

		return $clean;

	}

	/**
	 * Get the admin notices for our settings page.
	 * 
	 * @return string The admin notices for our settings page.
	 */
	function get_admin_notices() {

		// If the plugin is all set up, say so.
		if( $this -> is_setup() ) {

			$message = esc_html__( 'Nice!  Your API Key is valid.', 'mailorc' );
			$type = 'success';

		// Else, issue a warning.
		} else {

			$message = esc_html__( 'Please provide a valid API Key.', 'mailorc' );
			$type = 'warning';

		}

		$out = "
			<div class='notice-$type notice is-dismissible'>
				<p>$message</p>
			</div>
		";

		return $out;

	}

	/**
	 * Output our admin notices.
	 */
	function admin_notices() {

		if( ! $this -> is_current_page() ) { return FALSE; }

		echo $this -> get_admin_notices();

	}

	/**
	 * Determine if our plugin page is ready to go.
	 * 
	 * @return boolean Returns TRUE if our plugin is ready, else FALSE.
	 */
	function is_setup() {

		$has_api_key = $this -> meta -> has_api_key();

		if( is_wp_error( $has_api_key ) ) { return FALSE; }

		return TRUE;

	}

}