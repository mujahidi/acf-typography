<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Clean a Typography value before it is saved
 *
 *  Strips tags and line breaks from each property, so a crafted request cannot store
 *  markup that the_typography_field() prints as is. Also strips ; : { } and \, so a
 *  value printed inside a style attribute or a CSS rule cannot add more CSS (e.g.
 *  18px;background:url(...)). Quotes are kept: web-safe font values such as
 *  "Arial Black", Gadget, sans-serif need them. A property that is not a plain
 *  value (e.g. a posted array) is dropped.
 *
 *  acft_sanitize_typography_value()
 *
 *  @since      3.3.0
 *  @param      mixed $value  Field value about to be saved.
 *  @return     mixed
 */
function acft_sanitize_typography_value( $value ) {

	$clean = function ( $text ) {
		return str_replace( array( ';', ':', '{', '}', '\\' ), '', sanitize_text_field( $text ) );
	};

	if ( ! is_array( $value ) ) {
		return is_string( $value ) ? $clean( $value ) : $value;
	}

	foreach ( $value as $property => $property_value ) {
		if ( is_string( $property_value ) ) {
			$value[ $property ] = $clean( $property_value );
		} elseif ( null !== $property_value && ! is_scalar( $property_value ) ) {
			unset( $value[ $property ] );
		}
	}

	return $value;
}

/**
 *  Check whether a Typography value has a property filled in
 *
 *  0 is a real value (e.g. letter spacing), so only a missing, empty or
 *  non-scalar property counts as not filled in.
 *
 *  acft_typography_has_property()
 *
 *  @since      3.3.0
 *  @param      mixed  $value     Field value.
 *  @param      string $property  Property name, e.g. font_size.
 *  @return     bool
 */
function acft_typography_has_property( $value, $property ) {
	return is_array( $value ) && isset( $value[ $property ] ) && is_scalar( $value[ $property ] ) && '' !== (string) $value[ $property ];
}

/**
 * Get the translated label for a typography property.
 *
 * @param string $property Property name.
 * @return string
 */
function acft_typography_property_label( $property ) {
	$labels = array(
		'font_size'       => __( 'Font Size', 'acf-typography-field' ),
		'font_family'     => __( 'Font Family', 'acf-typography-field' ),
		'font_weight'     => __( 'Font Weight', 'acf-typography-field' ),
		'font_style'      => __( 'Font Style', 'acf-typography-field' ),
		'font_variant'    => __( 'Font Variant', 'acf-typography-field' ),
		'font_stretch'    => __( 'Font Stretch', 'acf-typography-field' ),
		'line_height'     => __( 'Line Height', 'acf-typography-field' ),
		'letter_spacing'  => __( 'Letter Spacing', 'acf-typography-field' ),
		'text_align'      => __( 'Text Align', 'acf-typography-field' ),
		'text_color'      => __( 'Text Color', 'acf-typography-field' ),
		'text_decoration' => __( 'Text Decoration', 'acf-typography-field' ),
		'text_transform'  => __( 'Text Transform', 'acf-typography-field' ),
	);

	return isset( $labels[ $property ] ) ? $labels[ $property ] : ucwords( str_replace( '_', ' ', $property ) );
}

/**
 *  Keep text changes made through the old text domains
 *
 *  Up to 3.2.3 the strings used 'acf-typography' (and 'acf' for the settings field
 *  label). A theme may have changed them with a gettext filter or its own .mo file
 *  under those domains. Their version wins over a translation in the new domain.
 *  Removed in 4.0.
 *
 *  acft_legacy_text_domain()
 *
 *  @since      3.3.0
 *  @param      string $translation  Text translated in the acf-typography-field domain.
 *  @param      string $text         Original text.
 *  @return     string
 */
add_filter( 'gettext_acf-typography-field', 'acft_legacy_text_domain', 10, 2 );
function acft_legacy_text_domain( $translation, $text ) {

	// phpcs:ignore WordPress.WP.I18n -- deliberate lookup of a variable string in the old domain.
	$legacy = translate( $text, 'acf-typography' );

	// only this string used ACF's domain
	if ( $legacy === $text && 'Google Fonts Key' === $text ) {
		// phpcs:ignore WordPress.WP.I18n -- deliberate lookup in the old domain.
		$legacy = translate( $text, 'acf' );
	}

	return $legacy !== $text ? $legacy : $translation;
}

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
			// normal wp-admin pages only: on REST and admin-ajax, a notice shown by WP_DEBUG_DISPLAY would break the JSON response
			if ( ! $warned && is_admin() && ! wp_doing_ajax() ) {
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
 *  Get the cached Google Fonts list and its fetch status
 *
 *  Stored in a non-autoloaded option, so it survives plugin updates and
 *  works on hosts where the plugin folder is read-only.
 *
 *  acft_get_google_fonts_cache()
 *
 *  @since      3.3.0
 *  @return     array  families (string[]), fetched (int), attempted (int), error (string), key_hash (string).
 */
function acft_get_google_fonts_cache() {

	$cache = get_option( 'acft_google_fonts' );

	if ( ! is_array( $cache ) ) {
		$cache = array();
	}

	$cache = wp_parse_args(
		$cache,
		array(
			'families'  => array(),
			'fetched'   => 0,
			'attempted' => 0,
			'error'     => '',
			'key_hash'  => '',
		)
	);

	if ( ! is_array( $cache['families'] ) ) {
		$cache['families'] = array();
	}

	return $cache;
}

/**
 *  Fetch the Google Fonts family list from the Google Fonts Developer API
 *
 *  acft_fetch_google_fonts()
 *
 *  @since      3.3.0
 *  @param      string $api_key  Google API key.
 *  @return     string[]|WP_Error  Family names, or the reason the request failed.
 */
function acft_fetch_google_fonts( $api_key ) {

	$response = wp_remote_get(
		'https://www.googleapis.com/webfonts/v1/webfonts?key=' . rawurlencode( $api_key ),
		array( 'timeout' => 10 )
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code ) {
		$message = isset( $body['error']['message'] ) && is_string( $body['error']['message'] ) ? $body['error']['message'] : sprintf( 'HTTP %d', $code );
		return new WP_Error( 'acft_google_fonts_http', $message );
	}

	if ( ! isset( $body['items'] ) || ! is_array( $body['items'] ) ) {
		return new WP_Error( 'acft_google_fonts_response', __( 'Unexpected response from the Google Fonts API.', 'acf-typography-field' ) );
	}

	$families = array();
	foreach ( $body['items'] as $item ) {
		if ( isset( $item['family'] ) && is_string( $item['family'] ) ) {
			$families[] = $item['family'];
		}
	}

	// an empty list would replace the last good one and hold for a week
	if ( ! $families ) {
		return new WP_Error( 'acft_google_fonts_response', __( 'Unexpected response from the Google Fonts API.', 'acf-typography-field' ) );
	}

	return $families;
}

/**
 *  Whether the cached Google Fonts list should be fetched again
 *
 *  True when the list is older than a week, the API key changed, or the last
 *  request failed. After a failed request it waits an hour before trying again,
 *  keeping the old list.
 *
 *  acft_google_fonts_needs_refresh()
 *
 *  @since      3.3.0
 *  @param      array  $cache     From acft_get_google_fonts_cache().
 *  @param      string $key_hash  md5 of the current API key.
 *  @param      int    $now       Current time.
 *  @return     bool
 */
function acft_google_fonts_needs_refresh( $cache, $key_hash, $now ) {

	// an error counts as stale, so a failed request is retried even when an older list is still fresh
	$stale = $cache['key_hash'] !== $key_hash || $cache['fetched'] < $now - WEEK_IN_SECONDS || '' !== $cache['error'];

	if ( ! $stale ) {
		return false;
	}

	// a request for this key failed within the hour
	return ! ( '' !== $cache['error'] && $cache['key_hash'] === $key_hash && $cache['attempted'] > $now - HOUR_IN_SECONDS );
}

/**
 *  Take or release the lock that stops parallel requests from all fetching
 *
 *  A plain options row written with INSERT IGNORE, so only one request can take
 *  it, with or without a persistent object cache (transients are not atomic).
 *  A lock older than 30 seconds is left over from a request that died, and is
 *  taken over.
 *
 *  acft_google_fonts_lock()
 *
 *  @since      3.3.0
 *  @param      bool $take  True to take the lock, false to release it.
 *  @return     bool  Whether this request now holds the lock (always false on release).
 */
function acft_google_fonts_lock( $take = true ) {

	global $wpdb;

	// phpcs:disable WordPress.DB.DirectDatabaseQuery -- the options API cannot insert atomically; the row is never read through the cache
	if ( ! $take ) {
		$wpdb->delete( $wpdb->options, array( 'option_name' => 'acft_google_fonts_lock' ) );
		return false;
	}

	$now = time();

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name = 'acft_google_fonts_lock' AND CAST( option_value AS UNSIGNED ) < %d", $now ) );

	$taken = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} ( option_name, option_value, autoload ) VALUES ( 'acft_google_fonts_lock', %s, 'no' )", (string) ( $now + 30 ) ) );
	// phpcs:enable WordPress.DB.DirectDatabaseQuery

	return 1 === $taken;
}

/**
 *  Refresh the cached Google Fonts list when it is stale
 *
 *  See acft_google_fonts_needs_refresh() for when a fetch happens.
 *
 *  acft_refresh_google_fonts()
 *
 *  @since      3.3.0
 *  @param      bool $force  Fetch now, ignoring the cache age, the retry delay and the lock.
 */
function acft_refresh_google_fonts( $force = false ) {

	$api_key = acft_get_google_api_key();

	if ( '' === $api_key ) {
		return;
	}

	$key_hash = md5( $api_key );
	$now      = time();
	$before   = acft_get_google_fonts_cache();

	if ( ! $force && ! acft_google_fonts_needs_refresh( $before, $key_hash, $now ) ) {
		return;
	}

	$locked = acft_google_fonts_lock();

	if ( ! $force && ! $locked ) {
		return; // another request is fetching right now
	}

	$families = acft_fetch_google_fonts( $api_key );

	// re-read: another request may have saved the list while this one waited on Google
	wp_cache_delete( 'acft_google_fonts', 'options' );
	wp_cache_delete( 'notoptions', 'options' );
	$cache = acft_get_google_fonts_cache();

	// a result saved while this request waited is newer (e.g. the key was changed and fetched meanwhile);
	// only replace it when it was a failure for the same key and this fetch worked
	$newer   = $cache !== $before;
	$replace = ! $newer || ( $cache['key_hash'] === $key_hash && '' !== $cache['error'] && ! is_wp_error( $families ) );

	if ( $replace ) {

		$cache['attempted'] = $now;
		$cache['key_hash']  = $key_hash;

		if ( is_wp_error( $families ) ) {
			$cache['error'] = $families->get_error_message();
		} else {
			$cache['families'] = $families;
			$cache['fetched']  = $now;
			$cache['error']    = '';
		}

		update_option( 'acft_google_fonts', $cache, false );
	}

	if ( $locked ) {
		acft_google_fonts_lock( false );
	}
}

/**
 *  Fetch the Google Fonts list right away when the settings are saved
 *
 *  @since      3.3.0
 */
add_action( 'add_option_acft_settings', 'acft_refresh_google_fonts_on_save' );
add_action( 'update_option_acft_settings', 'acft_refresh_google_fonts_on_save' );
function acft_refresh_google_fonts_on_save() {

	acft_refresh_google_fonts( true );
}

/**
 *  Also fetch when the settings are saved unchanged
 *
 *  update_option() skips the hooks above when the value did not change, so saving
 *  the same key again (e.g. after fixing the key in Google Cloud) would not retry.
 *
 *  @since      3.3.0
 *  @param      mixed $value      New value.
 *  @param      mixed $old_value  Current value.
 *  @return     mixed  The new value, unchanged.
 */
add_filter( 'pre_update_option_acft_settings', 'acft_refresh_google_fonts_on_unchanged_save', 10, 2 );
function acft_refresh_google_fonts_on_unchanged_save( $value, $old_value ) {

	if ( $value === $old_value ) {
		acft_refresh_google_fonts( true );
	}

	return $value;
}

/**
 *  Background refresh, scheduled by front-end requests when the list is due a refresh
 *
 *  @since      3.3.0
 */
add_action( 'acft_refresh_google_fonts_event', 'acft_refresh_google_fonts' );

/**
 *  Stop the background refresh when the plugin is deactivated
 *
 *  Registered in acf-typography.php. Saved data is removed in uninstall.php.
 *
 *  acft_deactivate()
 *
 *  @since      3.3.0
 */
function acft_deactivate() {

	wp_clear_scheduled_hook( 'acft_refresh_google_fonts_event' );
}

/**
 *  Update Google Fonts JSON file
 *
 *  The list is no longer stored in google_fonts.json. Kept so older theme code
 *  calling it does not fatal; removed in 4.0.
 *
 *  acft_update_gf_json_file()
 *
 *  @since      3.0.0
 *  @deprecated 3.3.0 Use acft_refresh_google_fonts() instead.
 *  @param      string $api_key  Ignored; the key comes from acft_get_google_api_key().
 */
function acft_update_gf_json_file( $api_key = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- kept for backward compatibility.

	_deprecated_function( __FUNCTION__, '3.3.0', 'acft_refresh_google_fonts()' );

	acft_refresh_google_fonts();
}

/**
 *  Get google fonts for Font-Family drop-down subfield
 *
 *  Only fetches from Google on wp-admin page loads, so front-end visitors never
 *  wait on the API. admin-ajax.php is skipped: it also serves front-end requests.
 *  When the list is due a refresh (e.g. never fetched right after updating from
 *  3.2.x, or the last fetch failed), other requests schedule a one-off background
 *  fetch, so sites that only use front-end forms still get the list.
 *
 *  acft_get_google_font_family()
 *
 *  @since      3.0.0
 *  @return     array  Family names as both keys and values; empty when no API key is set.
 */
function acft_get_google_font_family() {

	$api_key = acft_get_google_api_key();

	if ( '' === $api_key ) {
		return array();
	}

	if ( is_admin() && ! wp_doing_ajax() ) {
		acft_refresh_google_fonts();
	} elseif ( acft_google_fonts_needs_refresh( acft_get_google_fonts_cache(), md5( $api_key ), time() ) && ! wp_next_scheduled( 'acft_refresh_google_fonts_event' ) ) {
		wp_schedule_single_event( time(), 'acft_refresh_google_fonts_event' );
	}

	$families = acft_get_google_fonts_cache()['families'];

	return array_combine( $families, $families );
}

/**
 *  Collect the field data of ACF blocks, including blocks nested in other blocks
 *
 *  acft_get_acf_blocks_data()
 *
 *  @since      3.3.0
 *  @param      array $blocks  Blocks from parse_blocks().
 *  @return     array  One entry per ACF block that has field data.
 */
function acft_get_acf_blocks_data( $blocks ) {

	$blocks_data = array();

	foreach ( $blocks as $block ) {

		// freeform content between blocks has no block name; ACF blocks without fields have no data (GH #29)
		if ( is_string( $block['blockName'] ) && 0 === strpos( $block['blockName'], 'acf/' ) && ! empty( $block['attrs']['data'] ) && is_array( $block['attrs']['data'] ) ) {
			$blocks_data[] = $block['attrs']['data'];
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$blocks_data = array_merge( $blocks_data, acft_get_acf_blocks_data( $block['innerBlocks'] ) );
		}
	}

	return $blocks_data;
}

/**
 *  Collect the font weights used per font family from saved Typography values
 *
 *  Walks nested arrays (repeaters, groups, block data), so each family keeps
 *  the weights chosen next to it rather than every weight on the page.
 *
 *  acft_collect_font_weights()
 *
 *  @since      3.3.0
 *  @param      mixed $value    Field values, as returned by get_fields( ..., false ).
 *  @param      array $weights  Family => weight => true, filled in place.
 */
function acft_collect_font_weights( $value, &$weights ) {

	if ( ! is_array( $value ) ) {
		return;
	}

	if ( isset( $value['font_family'] ) && is_string( $value['font_family'] ) && '' !== $value['font_family'] ) {

		$family = $value['font_family'];
		$weight = isset( $value['font_weight'] ) && is_scalar( $value['font_weight'] ) ? (string) $value['font_weight'] : '';

		if ( ! isset( $weights[ $family ] ) ) {
			$weights[ $family ] = array();
		}

		// regular and bold always, as in 3.2.x: bold text needs 700, and Google rejects a
		// family (e.g. Lobster:700) when none of the requested weights exist
		$weights[ $family ]['400'] = true;
		$weights[ $family ]['700'] = true;

		if ( preg_match( '/^[1-9]00$/', $weight ) ) {
			$weights[ $family ][ $weight ] = true;
		}
	}

	foreach ( $value as $child ) {
		acft_collect_font_weights( $child, $weights );
	}
}

/**
 *  Whether a font family value should be loaded from Google Fonts
 *
 *  Web-safe values such as "Georgia, serif" and "initial" are not Google fonts.
 *  A family missing from the cached list (not fetched yet, or since dropped by
 *  Google) is judged by its name: Google family names are letters, digits, spaces
 *  and hyphens, web-safe values all contain a comma. Sending an unknown family is
 *  harmless: the v1 API skips it and still serves the others.
 *
 *  acft_is_google_font_family()
 *
 *  @since      3.3.0
 *  @param      string $family  Saved font family value.
 *  @return     bool
 */
function acft_is_google_font_family( $family ) {

	static $google = null;

	if ( null === $google ) {
		$google = array_flip( acft_get_google_fonts_cache()['families'] );
	}

	if ( isset( $google[ $family ] ) ) {
		return true;
	}

	return ! in_array( $family, array( 'initial', 'inherit' ), true ) && 1 === preg_match( '/^[A-Za-z0-9 -]+$/', $family );
}

/**
 *  Build the Google Fonts stylesheet URL
 *
 *  acft_google_fonts_url()
 *
 *  @since      3.3.0
 *  @param      array $weights  Family => weight => true, from acft_collect_font_weights().
 *  @return     string  Empty string when no Google font is used.
 */
function acft_google_fonts_url( $weights ) {

	$families = array();

	foreach ( $weights as $family => $family_weights ) {

		$family = (string) $family; // numeric-looking array keys come back as ints

		if ( ! acft_is_google_font_family( $family ) ) {
			continue;
		}

		$family_weights = array_map( 'strval', array_keys( $family_weights ) );
		sort( $family_weights, SORT_NUMERIC );

		$families[] = str_replace( ' ', '+', $family ) . ':' . implode( ',', $family_weights );
	}

	if ( ! $families ) {
		return '';
	}

	// display=swap: show fallback text while the font loads; the v1 API accepts it too
	return 'https://fonts.googleapis.com/css?family=' . implode( '|', $families ) . '&display=swap';
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

	// ACF inactive: nothing to read, and get_fields() would be undefined
	if ( ! function_exists( 'get_fields' ) ) {
		return;
	}

	global $post;

	$all_post_fields   = array();
	$all_option_fields = get_fields( 'option', false ) ?: array();

	// 404 and other views can have no post; option fields still apply there
	if ( $post instanceof WP_Post ) {
		$all_post_fields = get_fields( $post->ID, false ) ?: array();

		// for Gutenberg Blocks
		$all_post_fields = array_merge( $all_post_fields, acft_get_acf_blocks_data( parse_blocks( $post->post_content ) ) );
	}

	$weights = array();
	acft_collect_font_weights( array( $all_post_fields, $all_option_fields ), $weights );

	$url = acft_google_fonts_url( $weights );

	if ( '' !== $url ) {
		// null: no ?ver= on a third-party URL
		wp_enqueue_style( 'acft-gf', $url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
}
