<?php
/**
 * Plugin Name: Advanced Custom Fields: Typography Field
 * Plugin URI: https://wordpress.org/plugins/acf-typography-field
 * Description: A Typography Add-on for the Advanced Custom Fields Plugin.
 * Version: 3.2.3
 * Author: Mujahid Ishtiaq
 * Author URI: https://github.com/mujahidi
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: acf-typography
 * Domain Path: /languages
 *
 * @package ACF_Typography
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$acft_options = get_option( 'acft_settings' );

if ( $acft_options && isset( $acft_options['google_key'] ) && ! empty( $acft_options['google_key'] ) ) {
	define( 'YOUR_API_KEY', $acft_options['google_key'] );
}

// Check if class already exists.
if ( ! class_exists( 'acf_plugin_Typography' ) ) :

	/**
	 * Main ACF Typography plugin class.
	 *
	 * @since 1.0.0
	 */
	class acf_plugin_Typography {

		/**
		 * Settings array.
		 *
		 * @var array
		 */
		public $settings;

		/**
		 * Constructor.
		 *
		 * This function will setup the class functionality.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			// Vars.
			$this->settings = array(
				'version' => '3.2.3',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ ),
			);

			// Include files.
			require plugin_dir_path( __FILE__ ) . 'includes/api-template.php';
			require plugin_dir_path( __FILE__ ) . 'includes/admin_settings.php';
			require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

			// Include field.
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) ); // v5.
			add_action( 'acf/register_fields', array( $this, 'include_field_types' ) ); // v4.
			add_action( 'acf/field_group/admin_enqueue_scripts', array( $this, 'field_group_admin_enqueue_scripts' ) );
		}

		/**
		 * Include field types.
		 *
		 * This function will include the field type class.
		 *
		 * @since 1.0.0
		 * @param int|bool $version Major ACF version. Defaults to false.
		 */
		public function include_field_types( $version = false ) {
			// Support empty $version.
			if ( ! $version ) {
				$version = 4;
			}

			// Include.
			include_once 'fields/acf-Typography-v' . $version . '.php';
		}

		/**
		 * Enqueue scripts for field group admin.
		 *
		 * @since 3.0.0
		 */
		public function field_group_admin_enqueue_scripts() {
			wp_enqueue_script(
				'acf-typography-fieldgroup-script',
				plugin_dir_url( __FILE__ ) . 'assets/js/admin-field-group.js',
				array(),
				$this->settings['version'],
				false
			);
		}
	}

	// Initialize.
	new acf_plugin_Typography();

endif;