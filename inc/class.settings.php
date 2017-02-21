<?php

/**
 * A class for defining our plugin settings.
 *
 * @package WordPress
 * @subpackage MailOrc
 * @since MailOrc 0.1
 */

namespace MailOrc;

class Settings {

	function __construct() {

		// Define our settings.
		$this -> set_settings();

	}

	/**
	 * Get the array that defines our plugin settings.
	 * 
	 * @return array Our plugin settings.
	 */
	function get_settings() {

		return $this -> settings;

	}

	/**
	 * Get the definition of a settings section.
	 * 
	 * @param  string $section_id The ID of a section.
	 * @return array              The definition of a settings section.
	 */
	function get_section( $section_id ) {

		$settings = $this -> get_settings();

		return $settings[ $section_id ];

	}

	/**
	 * Store our plugin settigns definitions.
	 */
	function set_settings() {

		$sample_api_key = '<code>2t3g46fy4hf75k98uytr5432wer3456u-us3</code>';

		$out = array(

			// A section.
			'mailchimp_account_setup' => array(

				// The label for this section.
				'label' => esc_html__( 'MailChimp Account Setup', 'mailorc' ),

				'description' => esc_html__( 'The section where one configures the settings pertaining to ones MailChimp account.', 'mailorc' ),

				// For subsites?
				'subsite' => TRUE,

				// For multisite?
				'network' => FALSE,

				// The settings for this section.
				'settings' => array(

					// A setting.
					'api_key' => array(
						'type'        => 'text',
						'label'       => esc_html__( 'API Key', 'mailorc' ),
						'description' => sprintf( esc_html__( 'Example: %s.', 'mailorc' ), $sample_api_key ),
						'attrs'       => array(
							'required'    => 'required',
							'placeholder' => esc_attr__( 'Your MailChimp API Key', 'mailorc' ),
							'pattern'     => '.{30,40}',
							'title'       => esc_attr__( 'Should be about 36 characters and include your datacenter', 'mailorc' ),
						),
					),

					// A setting.
					'list_id' => array(
						'type'        => 'select',
						'label'       => esc_html__( 'List', 'mailorc' ),
						'description' => esc_html__( 'Choose a list from your account.', 'mailorc' ),
						'options_cb'  => array( 'Fields', 'get_lists_as_options' ),
						
					),

				),

			),

			// A section.
			'wordpress_setup' => array(

				// The label for this section.
				'label' => esc_html( 'WordPress Setup', 'mailorc' ),

				'description' => esc_html__( 'The section where one configures the settings pertaining to ones WordPress account.', 'mailorc' ),


				// For subsites?
				'subsite' => TRUE,

				// For multisite?
				'network' => FALSE,

				// The settings for this section.
				'settings' => array(

					// A setting.
					'landing_pages' => array(
						'type'        => 'checkbox_group',
						'label'       => esc_html__( 'Campaign Landing Pages', 'mailorc' ),
						'description' => "<a href='" . get_admin_url( FALSE, 'edit.php?post_type=page' ) . "'>" . esc_html__( 'You may have to create your landing pages if you have not yet done so.', 'mailorc' ) . '</a>',
						'options_cb'  => array( 'Fields', 'get_pages_as_checkboxes' ),
					),

				),

			),			

		);

		$this -> settings = $out;

	}

	/**
	 * Get the values of our subsite settings.
	 * 
	 * @return array The values of our subsite settings.
	 */
	function get_subsite_values() {

		if( ! isset( $this -> subsite_values ) ) {
			$this -> set_subsite_values();
		}

		return $this -> subsite_values;

	}

	/**
	 * Store the values of our subsite settings.
	 */
	function set_subsite_values() {

		$this -> subsite_values = get_option( MAILORC );

	}

	/**
	 * Get the definition of our subsite settings.
	 * 
	 * @return array The definition of our subsite settings.
	 */
	function get_subsite_settings() {

		// Start with all settings.
		$settings = $this -> get_settings();

		// For each setting...
		foreach( $settings as $section_id => $section ) {

			// If it's not a subsite setting, remove it.
			if( ! $section['subsite'] ) {
				unset( $settings[ $section_id] );
			}

		}

		return $settings;

	}

	/**
	 * Get the value of a given subsite setting.
	 * 
	 * @param  string $section_id The section.
	 * @param  string $setting_id The setting.
	 * @return mixed              The setting value.
	 */
	function get_subsite_value( $section_id, $setting_id ) {

		$values = $this -> get_subsite_values();

		if( ! isset( $values[ $section_id ] ) ) { return FALSE; }
		if( ! isset( $values[ $section_id ][ $setting_id ] ) ) { return FALSE; }
				
		return $values[ $section_id ][ $setting_id ];

	}

}