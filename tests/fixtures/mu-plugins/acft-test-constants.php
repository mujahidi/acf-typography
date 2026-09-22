<?php
/**
 * Plugin Name: ACFT test constants (dev only)
 * Description: Defines constants listed in the `acft_test_constants` option, to simulate wp-config.php. Never ship.
 *
 * Usage: wp option update acft_test_constants '{"ACFT_GOOGLE_API_KEY":"new-key","YOUR_API_KEY":"old-key"}' --format=json
 *
 * @package ACF_Typography_Field
 */

$acft_test_constants = get_option( 'acft_test_constants' );

if ( is_array( $acft_test_constants ) ) {
	foreach ( $acft_test_constants as $acft_test_name => $acft_test_value ) {
		if ( in_array( $acft_test_name, array( 'ACFT_GOOGLE_API_KEY', 'YOUR_API_KEY' ), true ) && ! defined( $acft_test_name ) ) {
			define( $acft_test_name, $acft_test_value );
		}
	}
}
