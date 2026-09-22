<?php
/**
 * Plugin Name: ACFT test HTTP mock (dev only)
 * Description: Fakes Google Fonts API responses and logs each request, so the font cache can be tested without a real key. Never ship.
 *
 * Usage: wp option update acft_test_http_mock ok|error400|wp_error   (delete the option to hit the real API)
 * Every intercepted or real request to the webfonts API is logged as "ACFT_HTTP <mode> <context>".
 *
 * @package ACF_Typography_Field
 */

add_filter(
	'pre_http_request',
	function ( $preempt, $args, $url ) {
		if ( false === strpos( $url, 'googleapis.com/webfonts' ) ) {
			return $preempt;
		}

		$mode    = get_option( 'acft_test_http_mock' );
		$context = is_admin() ? 'admin' : ( defined( 'WP_CLI' ) && WP_CLI ? 'cli' : 'front' );
		error_log( 'ACFT_HTTP ' . ( $mode ? $mode : 'real' ) . ' ' . $context ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

		switch ( $mode ) {
			case 'ok':
				$items = array();
				foreach ( array( 'Roboto', 'Open Sans', 'Lato' ) as $family ) {
					$items[] = array( 'family' => $family );
				}
				return array(
					'headers'  => array(),
					'body'     => wp_json_encode( array( 'items' => $items ) ),
					'response' => array( 'code' => 200, 'message' => 'OK' ),
					'cookies'  => array(),
				);

			case 'error400':
				return array(
					'headers'  => array(),
					'body'     => wp_json_encode( array( 'error' => array( 'code' => 400, 'message' => 'API key not valid. Please pass a valid API key.' ) ) ),
					'response' => array( 'code' => 400, 'message' => 'Bad Request' ),
					'cookies'  => array(),
				);

			case 'wp_error':
				return new WP_Error( 'http_request_failed', 'cURL error 28: Operation timed out after 10001 milliseconds' );
		}

		return $preempt;
	},
	10,
	3
);
