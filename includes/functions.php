<?php

/**
 *  Get the Google Fonts API key saved on the settings page
 *
 *  acft_get_saved_google_api_key()
 *
 *  @since      3.3.0
 *  @return     string  Empty string when no key is saved.
 */
function acft_get_saved_google_api_key() {

	$acft_options = get_option( 'acft_settings' );

	if ( is_array( $acft_options ) && ! empty( $acft_options['google_key'] ) && is_string( $acft_options['google_key'] ) ) {
		return $acft_options['google_key'];
	}

	return '';
}

/**
 *  Remember whether this plugin defined the legacy YOUR_API_KEY constant itself
 *
 *  acft_legacy_api_key_is_ours()
 *
 *  @since      3.3.0
 *  @param      bool|null $set  Pass true once the plugin has defined the constant.
 *  @return     bool
 */
function acft_legacy_api_key_is_ours( $set = null ) {

	static $ours = false;

	if ( null !== $set ) {
		$ours = (bool) $set;
	}

	return $ours;
}

/**
 *  Where the Google Fonts API key comes from
 *
 *  Order: ACFT_GOOGLE_API_KEY constant, the legacy YOUR_API_KEY constant (when
 *  something other than this plugin defined it), then the settings page.
 *
 *  acft_google_api_key_source()
 *
 *  @since      3.3.0
 *  @return     string  'constant', 'legacy_constant', 'option' or '' when no key is set.
 */
function acft_google_api_key_source() {

	if ( defined( 'ACFT_GOOGLE_API_KEY' ) && is_string( ACFT_GOOGLE_API_KEY ) && '' !== ACFT_GOOGLE_API_KEY ) {
		return 'constant';
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- legacy constant, removed in 4.0.
	if ( defined( 'YOUR_API_KEY' ) && ! acft_legacy_api_key_is_ours() && is_string( YOUR_API_KEY ) && '' !== YOUR_API_KEY ) {
		return 'legacy_constant';
	}

	if ( '' !== acft_get_saved_google_api_key() ) {
		return 'option';
	}

	return '';
}

/**
 *  Get the Google Fonts API key
 *
 *  acft_get_google_api_key()
 *
 *  @since      3.3.0
 *  @return     string  Empty string when no key is set.
 */
function acft_get_google_api_key() {

	switch ( acft_google_api_key_source() ) {

		case 'constant':
			return ACFT_GOOGLE_API_KEY;

		case 'legacy_constant':
			static $warned = false;
			if ( ! $warned ) {
				$warned = true;
				_doing_it_wrong(
					__FUNCTION__,
					esc_html__( 'The YOUR_API_KEY constant is deprecated and will be removed in 4.0. Define ACFT_GOOGLE_API_KEY instead.', 'acf-typography-field' ),
					'3.3.0'
				);
			}
			return YOUR_API_KEY;

		case 'option':
			return acft_get_saved_google_api_key();
	}

	return '';
}

/**
 *  Define the legacy YOUR_API_KEY constant for code that still reads it
 *
 *  acft_maybe_define_legacy_api_key()
 *
 *  @since      3.3.0
 */
function acft_maybe_define_legacy_api_key() {

	if ( defined( 'YOUR_API_KEY' ) ) {
		return;
	}

	$api_key = acft_get_google_api_key();

	if ( '' === $api_key ) {
		return;
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- kept for backward compatibility until 4.0.
	define( 'YOUR_API_KEY', $api_key );
	acft_legacy_api_key_is_ours( true );
}

/**
 *  Update Google Fonts JSON file
 *
 *  acft_update_gf_json_file()
 *
 *  @since      3.0.0
 */
function acft_update_gf_json_file( $API_KEY ) {

	$dir      = plugin_dir_path( __DIR__ );
	$filename = $dir . 'google_fonts.json';

	if ( file_exists( $filename ) ) {

		$file_date = date( 'Ymd', filemtime( $filename ) );
		$now       = date( 'Ymd', time() );
		$time      = $now - $file_date;

		if ( ! filesize( $filename ) || $time > 2 ) {

			$json = file_get_contents( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $API_KEY );

			$gf_file = fopen( $filename, 'wb' );
			fwrite( $gf_file, $json );
			fclose( $gf_file );

		}
	}
}

/**
 *  Get google fonts for Font-Family drop-down subfield
 *
 *  acft_get_google_font_family()
 *
 *  @since      3.0.0
 */
function acft_get_google_font_family() {

	$api_key = acft_get_google_api_key();

	if ( '' === $api_key ) {
		return;
	}

	acft_update_gf_json_file( $api_key );

	// Load json file for extra seting
	$dir         = plugin_dir_path( __DIR__ );
	$json        = file_get_contents( "{$dir}google_fonts.json" );
	$fontArray   = json_decode( $json );
	$font_family = array();

	if ( $fontArray ) {
		foreach ( $fontArray as $k => $v ) {
			if ( is_array( $v ) ) {
				foreach ( $v as $value ) {
					foreach ( $value as $key1 => $value1 ) {
						if ( $key1 == 'family' ) {
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
 *  Enqueue Google Fonts file
 *
 *  acft_enqueue_google_fonts_file()
 *
 *  @since      3.0.0
 */
add_action( 'wp_enqueue_scripts', 'acft_enqueue_google_fonts_file' );
function acft_enqueue_google_fonts_file() {

	global $post;

	$all_post_fields   = get_fields( $post->ID, false ) ?: array();
	$all_option_fields = get_fields( 'option', false ) ?: array();

	// for Gutenberg Blocks
	$blocks = parse_blocks( $post->post_content );
	foreach ( $blocks as $block ) {

		if ( strpos( $block['blockName'], 'acf/' ) === 0 ) { // a custom block made with ACF

			$all_post_fields[] = $block['attrs']['data'];

		}
	}

	$font_family = $font_weight = array();

	$all_fields = array_merge_recursive( $all_post_fields, $all_option_fields );

	if ( is_array( $all_fields ) ) {

		array_walk_recursive(
			$all_fields,
			function ( $item, $key ) use ( &$font_family, &$font_weight ) {
				if ( $key === 'font_family' ) {
					if ( ! in_array( $item, $font_family ) ) {
						$font_family[] = $item;
					} elseif ( $key === 'font_weight' ) {
						if ( ! in_array( $item, $font_weight ) ) {
							$font_weight[] = $item;
						}
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

		wp_enqueue_style( 'acft-gf', 'https://fonts.googleapis.com/css?family=' . $font_family );

	}
}
