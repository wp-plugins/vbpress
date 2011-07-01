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

	var $options = array();
	
	/**
	 * Singleton
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
	 * Constructor. Initializes WordPress hooks
	 */
	function Vbpress() {
	
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
	}

	/**
	 * Activation callback
	 */
	function plugin_activation() {
	
	}

	/**
	 * Deactivation callback
	 */
	function plugin_deactivation() {
		
	}

	/**
	 * Create the vBPress admin menu option
	 */
	function admin_menu() {
		$vbpress_settings = Vbpress_Settings::init();
		$hook = add_menu_page( 'vBPress', 'vBPress', 'manage_options', 'vbpress_settings', array( &$vbpress_settings, 'settings' ), '' );
	}
}

/**
 * This is our "API" for vBulletin. All interaction with the vBulletin core
 * should be handled through this class.
 */
class Vbpress_Vb {
	
	/**
	 * Singleton
	 */
	function &init() {
		static $instance = false;

		if ( !$instance ) {
			load_plugin_textdomain( 'vbpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			$instance = new Vbpress_Vb;
		}

		return $instance;
	}

	/**
	 * Constructor. Initializes WordPress hooks
	 */
	function Vbpress_Vb() {
	
		$this->options = get_option( 'vbpress_options' );
		if ( isset( $GLOBALS['vbulletin'] ) ) {
			$this->vbulletin = $GLOBALS['vbulletin'];
		}
		
	}
	
	/**
	 * If available, returns an array of user info for the currently
	 * logged-in user
	 */
	function get_current_user_info() {
		if ( !empty($this->vbulletin->userinfo['userid']) ) {
			return $this->vbulletin->userinfo;
		} else {
			return false;
		}
	}

	/**
	 * If available, returns an array of user info for the specified
	 * user ID
	 */
	function get_user_info( $user_id ) {
		return fetch_userinfo($user_id);
	}
	
}

/**
 * Manages the vBPress settings
 */
class Vbpress_Settings {

	/**
	 * Singleton
	 */
	function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new Vbpress_Settings;
		}

		return $instance;
	}
	
	/**
	 * Constructor. Registers setting groups and subsequent settings
	 */
	function Vbpress_Settings() {
		
		register_setting( 'vbpress_options', 'vbpress_options', array( $this, 'options_validate' ) );
		
		// General settings
		add_settings_section( 'vbpress_options_general', __( 'General Settings', 'vbpress' ), array( $this, 'output_section_general' ), 'vbpress' );
		add_settings_field( 'vbpress_enabled', __( 'Enable vBPress', 'vbpress' ), array( $this, 'output_option_vbpress_enabled' ), 'vbpress', 'vbpress_options_general' );
		add_settings_field( 'vbulletin_path', __( 'vBulletin Path', 'vbpress' ), array( $this, 'output_option_vbulletin_path' ), 'vbpress', 'vbpress_options_general' );
	}

	/**
	 * vBPress settings page
	 */
	function settings() {
	
		$is_logged_in = false;
		$vb_user_info = array();
	
		$options = get_option( 'vbpress_options' );
		if ( !empty( $options['vbpress_enabled'] ) ) {
			$vbpress_vb = Vbpress_Vb::init();
			$current_user_info = $vbpress_vb->get_current_user_info();
		}
		
		// TODO: Could we create 'View' class that is used for loading action views?
		require( dirname( __FILE__ ) . '/core/views/settings.php' );
	}

	/**
	 * Output section HTML
	 */
	function output_section_general() {
		require( dirname( __FILE__ ) . '/core/views/option_section_general.php' );
	}

	/**
	 * Output option field HTML
	 */
	function output_option_vbpress_enabled() {
		$options = get_option( 'vbpress_options' );
		require( dirname( __FILE__ ) . '/core/views/option_field_vbpress_enabled.php' );
	}

	/**
	 * Output option field HTML
	 */
	function output_option_vbulletin_path() {
		$options = get_option( 'vbpress_options' );
		require( dirname( __FILE__ ) . '/core/views/option_field_vbulletin_path.php' );
	}

	/**
	 * Validate submitted options data
	 */
	function options_validate( $data ) {
		return $data;
	}
}

register_activation_hook( __FILE__, array( 'Vbpress', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Vbpress', 'plugin_deactivation' ) );

add_action( 'init', array( 'Vbpress', 'init' ) );
add_action( 'admin_init', array( 'Vbpress_Settings', 'init' ) );


/*
 * Is vBPress enabled? If so, we need to load the vBulletin core
 */
$vBPressOptions = get_option( 'vbpress_options' );
if ( !empty( $vBPressOptions['vbpress_enabled'] ) && !empty( $vBPressOptions['vbulletin_path'] ) ) {

	if (file_exists($vBPressOptions['vbulletin_path'].'/global.php')) {

		// vBulletin modifies request-related superglobals. Make a back up of them so
		// that we can reset them after vBulletin has been loaded.
		$request_superglobals = array(
			'_GET' => $_GET,
			'_POST' => $_POST,
			'_REQUEST' => $_REQUEST
		);

		// Load the vBulletin core
		$dir = getcwd();
		chdir( $vBPressOptions['vbulletin_path'] );
		require_once( './global.php' );
		chdir( $dir );
		
		// Reset request-related superglobals
		$_GET = $request_superglobals['_GET'];
		$_POST = $request_superglobals['_POST'];
		$_REQUEST = $request_superglobals['_REQUEST'];
		
		// Load the Vbpress_Vb class
		add_action( 'init', array( 'Vbpress_Vb', 'init' ) );
		
	} else {
		// TODO: Error, could not find the vBulletin global.php at this path $vBPressOptions['vbulletin_path']
	}
}