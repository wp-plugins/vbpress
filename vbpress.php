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
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 */
	function plugin_activation() {
	
	}

	/**
	 * Removes all options.
	 */
	function plugin_deactivation() {
		
	}

	/**
	 * Create the vBPress admin menu option
	 */
	function admin_menu() {
		$hook = add_menu_page( 'vBPress', 'vBPress', 'manage_options', 'vbpress_settings', array( $this, 'settings' ), '' );
	}

	/**
	 * vBPress settings page
	 */
	function settings() {
	
		$options = new Vbpress_Options();
	
		if ( !empty($_POST) && !wp_verify_nonce($_POST['vbpress_update_settings'], 'vbpress_update_settings') ) {
			echo 'Sorry, your nonce did not verify.';
			exit;
		} else if ( !empty($_POST) ) {
		
			foreach ( $_POST as $name => $value ) {
				$options->set($name, $value);
			}
			
			$options->save();
		}
		
		echo '<pre>'.print_r($options->get(), 1).'</pre>';
		
		// TODO: Could we create 'View' class that is used for loading action views?
		require('core/views/settings.php');
	}
}

class Vbpress_Options {

	var $optionName = 'vbpress';

	var $validOptionNames = array(
		'enabled',
		'vbulletin_path'
	);

	var $options = array();
	
	/**
	 * Constructor
	 */
	function Vbpress_Options() {
		$this->load();
	}
	
	/**
	 * Populates $options from WordPress database
	 */
	function load() {
		$options = get_option($this->optionName, array());
		if (!empty($options)) {
			$options = $this->decode($options);
		}
	}
	
	/**
	 * Get a specified vBPress option
	 */
	function get($name = '') {
		if ($name == '') {
			return $this->options;
		} if (isset($this->options[$name])) {
			return $this->options[$name];
		}
		
		return null;
	}
	
	/**
	 * Set a specified vBPress option
	 */
	function set($name, $value, $unset = false) {
	
		// Strip out the prefix that we use in the forms
		$name = str_replace('vbpress_', '', $name);
		
		if ($unset && isset($this->options[$name])) {
			unset($this->options[$name]);
		} else if ( in_array($name, $this->validOptionNames) ) {
			$this->options[$name] = $value;
		}
	}
	
	/**
	 * Add or update the options in the WordPress database
	 */
	function save() {
		if (!get_option($this->optionName)) {
			add_option($this->optionName, $this->encode($this->options));
		} else {
			update_option($this->optionName, $this->encode($this->options));
		}
	}
	
	/*
	 * Removes all vBPress option data from the WordPress database
	 */
	function purge() {
		return (delete_option($this->optionName) ? true : false);
	}
	
	/**
	 * Encode vBPress option data
	 */
	function encode($data) {
		// TODO: Can we make the assumption that json_encode is an available function?
		return json_encode($data);
	}
	
	/**
	 * Decode vBPress option data
	 */
	function decode($data) {
		return !empty($data) ? json_decode($data) : null;
	}
}

class Vbpress_Error extends WP_Error {}

register_activation_hook( __FILE__, array( 'Vbpress', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Vbpress', 'plugin_deactivation' ) );

add_action( 'init', array( 'Vbpress', 'init' ) );
