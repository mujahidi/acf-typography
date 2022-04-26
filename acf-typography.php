<?php

/*
Plugin Name: Advanced Custom Fields: Typography Field
Plugin URI: https://wordpress.org/plugins/acf-typography-field
Description: A Typography Add-on for the Advanced Custom Fields Plugin.
Version: 3.2.3
Author: Mujahid Ishtiaq
Author URI: https://github.com/mujahidi
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

$acft_options = get_option( 'acft_settings' );

if( $acft_options && $acft_options['google_key'] )
	define('YOUR_API_KEY', $acft_options['google_key']);

// check if class already exists
if( !class_exists('acf_plugin_Typography') ) :

	class acf_plugin_Typography {
		
		/*
		*  __construct
		*
		*  This function will setup the class functionality
		*
		*  @type	function
		*  @date	17/02/2016
		*  @since	1.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/
		
		function __construct() {
			
			// vars
			$this->settings = array(
				'version'	=> '3.2.2',
				'url'		=> plugin_dir_url( __FILE__ ),
				'path'		=> plugin_dir_path( __FILE__ )
			);
			
			// include files
			require plugin_dir_path( __FILE__ ) . 'includes/api-template.php';
			require plugin_dir_path( __FILE__ ) . 'includes/admin_settings.php';
			require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

			// include field
			add_action('acf/include_field_types', 	array($this, 'include_field_types')); // v5
			add_action('acf/register_fields', 		array($this, 'include_field_types')); // v4
			add_action('acf/field_group/admin_enqueue_scripts', array($this, 'field_group_admin_enqueue_scripts'));
			
		}
		
		
		/*
		*  include_field_types
		*
		*  This function will include the field type class
		*
		*  @type	function
		*  @date	17/02/2016
		*  @since	1.0.0
		*
		*  @param	$version (int) major ACF version. Defaults to false
		*  @return	n/a
		*/
		
		function include_field_types( $version = false ) {
			
			// support empty $version
			if( !$version ) $version = 4;
			
			
			// include
			include_once('fields/acf-Typography-v' . $version . '.php');
			
		}

		function field_group_admin_enqueue_scripts(){

			wp_enqueue_script( 'acf-typography-fieldgroup-script', plugin_dir_url( __FILE__ ) . 'assets/js/admin-field-group.js', array(), $this->settings['version'] );

		}
		
	}


	// initialize
	new acf_plugin_Typography();


// class_exists check
endif;
	
?>