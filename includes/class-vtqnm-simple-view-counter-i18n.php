<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://vtqnm.xyz
 * @since      1.0.0
 *
 * @package    Vtqnm_Simple_View_Counter
 * @subpackage Vtqnm_Simple_View_Counter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Vtqnm_Simple_View_Counter
 * @subpackage Vtqnm_Simple_View_Counter/includes
 * @author     Vitalii Terentev <vtqnm0@gmail.com>
 */
class Vtqnm_Simple_View_Counter_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'vtqnm-simple-view-counter',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
