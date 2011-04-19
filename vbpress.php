<?php

/*
 * Plugin Name: vBPress
 * Plugin URI: http://www.vbpress.com
 * Description: vBPress seamlessly integrates WordPress with vBulletin
 * Author: Aaron Forgue, PJ Hile
 * Version: 0.1
 * Author URI: http://www.vbpress.com
 * Text Domain: vbpress
 * Domain Path: /languages/
 */

class Vbpress {

	/**
	 * Singleton
	 * @static
	 */
	function &init() {
		static $instance = false;

		if ( !$instance ) {
			load_plugin_textdomain( 'vbpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			$instance = new Vbpress;
		}

		return $instance;
	}

	/**
	 * Constructor.  Initializes WordPress hooks
	 */
	function Vbpress() {
	
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
	}

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	function plugin_activation() {
	
	}

	/**
	 * Removes all options.
	 * @static
	 */
	function plugin_deactivation() {
		
	}

	/**
	 * Create the vBPress admin menu option
	 */
	function admin_menu() {
		$hook = add_menu_page( 'vBPress', 'vBPress', 'manage_options', 'vbpress', array( $this, 'admin_page' ), '' );
	}
	

	/**
	 * vBPress settings page
	 */
	function admin_page() {
	
	}
}

class Vbpress_Error extends WP_Error {}

register_activation_hook( __FILE__, array( 'Vbpress', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Vbpress', 'plugin_deactivation' ) );

add_action( 'init', array( 'Vbpress', 'init' ) );
