<?php
/**
 * Core functions for ACF Typography field.
 *
 * @package ACF_Typography
 * @since 3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update Google Fonts JSON file.
 *
 * @since 3.0.0
 * @param string $api_key Google Fonts API key.
 * @return bool True on success, false on failure.
 */
function acft_update_gf_json_file( $api_key ) {
	$dir      = plugin_dir_path( dirname( __FILE__ ) );
	$filename = $dir . 'google_fonts.json';

	if ( ! file_exists( $filename ) ) {
		return false;
	}

	$file_date = date( 'Ymd', filemtime( $filename ) );
	$now       = date( 'Ymd', time() );
	$time      = $now - $file_date;

	if ( ! filesize( $filename ) || $time > 2 ) {
		$api_url = add_query_arg(
			array( 'key' => sanitize_text_field( $api_key ) ),
			'https://www.googleapis.com/webfonts/v1/webfonts'
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$json = wp_remote_retrieve_body( $response );

		if ( empty( $json ) ) {
			return false;
		}

		// Verify JSON is valid before writing.
		$decoded = json_decode( $json );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		$gf_file = fopen( $filename, 'wb' );
		if ( ! $gf_file ) {
			return false;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		fwrite( $gf_file, $json );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		fclose( $gf_file );
	}

	return true;
}

/**
 * Get Google fonts for Font-Family drop-down subfield.
 *
 * @since 3.0.0
 * @return array|null Array of Google fonts or null if not available.
 */
function acft_get_google_font_family() {
	if ( ! defined( 'YOUR_API_KEY' ) ) {
		return null;
	}

	acft_update_gf_json_file( YOUR_API_KEY );

	// Load json file for extra setting.
	$dir  = plugin_dir_path( dirname( __FILE__ ) );
	$file = $dir . 'google_fonts.json';

	if ( ! file_exists( $file ) ) {
		return null;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$json       = file_get_contents( $file );
	$font_array = json_decode( $json );
	$font_family = array();

	if ( $font_array ) {
		foreach ( $font_array as $k => $v ) {
			if ( is_array( $v ) ) {
				foreach ( $v as $value ) {
					foreach ( $value as $key1 => $value1 ) {
						if ( 'family' === $key1 ) {
							$font_family[ $value1 ] = $value1;
						}
					}
				}
			}
		}
	}

	return $font_family;
}

/**
 * Enqueue Google Fonts file on front-end.
 *
 * @since 3.0.0
 */
function acft_enqueue_google_fonts_file() {
	global $post;

	// Check if we have a valid post object.
	if ( ! $post || ! is_object( $post ) || ! isset( $post->ID ) ) {
		return;
	}

	$all_post_fields   = get_fields( $post->ID, false ) ?: array();
	$all_option_fields = get_fields( 'option', false ) ?: array();

	// For Gutenberg Blocks.
	if ( isset( $post->post_content ) ) {
		$blocks = parse_blocks( $post->post_content );
		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) && strpos( $block['blockName'], 'acf/' ) === 0 ) {
				if ( isset( $block['attrs']['data'] ) ) {
					$all_post_fields[] = $block['attrs']['data'];
				}
			}
		}
	}

	$font_family = array();
	$font_weight = array();

	$all_fields = array_merge_recursive( $all_post_fields, $all_option_fields );

	if ( is_array( $all_fields ) ) {
		array_walk_recursive(
			$all_fields,
			function( $item, $key ) use ( &$font_family, &$font_weight ) {
				if ( 'font_family' === $key ) {
					if ( ! in_array( $item, $font_family, true ) ) {
						$font_family[] = $item;
					}
				} elseif ( 'font_weight' === $key ) {
					if ( ! in_array( $item, $font_weight, true ) ) {
						$font_weight[] = $item;
					}
				}
			}
		);
	}

	if ( is_array( $font_family ) && count( $font_family ) > 0 ) {
		if ( is_array( $font_weight ) && count( $font_weight ) > 0 ) {
			$font_weight = implode( ',', $font_weight );
			$font_family = implode( ':' . $font_weight . '|', $font_family );
		} else {
			$font_family = implode( ':400,700|', $font_family );
		}

		$google_fonts_url = add_query_arg(
			array( 'family' => rawurlencode( $font_family ) ),
			'https://fonts.googleapis.com/css'
		);

		wp_enqueue_style( 'acft-gf', esc_url_raw( $google_fonts_url ), array(), null );
	}
}
add_action( 'wp_enqueue_scripts', 'acft_enqueue_google_fonts_file' );
