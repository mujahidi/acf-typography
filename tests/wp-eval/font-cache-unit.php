<?php
/**
 * Google Fonts cache checks that call the plugin functions directly: the fetch lock, keeping the
 * newer result, front-end scheduling, and font weights. Dev only; never ship.
 *
 * Needs the plugin active and tests/fixtures/mu-plugins/acft-test-http-mock.php on the site.
 * Saves and restores the options it changes.
 *
 * Usage: wp eval-file - < tests/wp-eval/font-cache-unit.php   (tests/run-font-cache-tests.sh runs it too)
 *
 * @package ACF_Typography_Field
 */

// phpcs:disable -- test script, run through wp eval-file

$GLOBALS['pass'] = 0;
$GLOBALS['fail'] = 0;
function t( $name, $exp, $got ) {
	$ok = $exp === $got;
	$ok ? $GLOBALS['pass']++ : $GLOBALS['fail']++;
	echo ( $ok ? 'PASS' : 'FAIL' ) . "  $name  (" . wp_json_encode( $got ) . ( $ok ? '' : ' expected ' . wp_json_encode( $exp ) ) . ")\n";
}
$warn = array();
set_error_handler( function ( $n, $s ) use ( &$warn ) { $warn[] = $s; return true; } );

echo "-- #6 the list is only read where a Typography field is shown\n";
// WP-CLI has no persistent object cache here, so a cached option means it was read while loading WordPress
t( '6a list not read while loading WordPress', false, wp_cache_get( 'acft_google_fonts', 'options' ) );
$ft = null;
foreach ( $GLOBALS['wp_filter']['acf/validate_value/type=Typography']->callbacks as $cbs ) { foreach ( $cbs as $cb ) { if ( is_array( $cb['function'] ) ) { $ft = $cb['function'][0]; } } }
t( '6b before: web-safe choices only', false, isset( $ft->font_family['inherit'] ) );
$ft->load_font_family();
$gf = (array) acft_get_google_fonts_cache()['families'];
t( '6c after: initial, inherit, then web-safe + Google sorted', array( 'initial', 'inherit' ), array_slice( array_keys( $ft->font_family ), 0, 2 ) );
t( '6d after: every cached Google family offered', array(), array_values( array_diff( $gf, array_keys( $ft->font_family ) ) ) );
$n = count( $ft->font_family ); $ft->load_font_family();
t( '6e loaded once', $n, count( $ft->font_family ) );

$bak = array(
	'settings' => get_option( 'acft_settings' ),
	'fonts'    => get_option( 'acft_google_fonts' ),
	'mock'     => get_option( 'acft_test_http_mock' ),
);

// a key of its own, saved without the save hooks so setup never fetches (a wp-config key still wins)
function setkey( $key ) {
	remove_all_actions( 'update_option_acft_settings' );
	remove_all_actions( 'add_option_acft_settings' );
	remove_all_filters( 'pre_update_option_acft_settings' );
	update_option( 'acft_settings', array( 'google_key' => $key ) );
}
setkey( 'unit-test-key' );
$GLOBALS['key'] = md5( acft_get_google_api_key() );

function setc( $c ) { update_option( 'acft_google_fonts', array_merge( array( 'families' => array( 'Roboto', 'Open Sans', 'Lato' ), 'fetched' => time(), 'attempted' => time(), 'error' => '', 'key_hash' => $GLOBALS['key'] ), $c ), false ); }
function unsched() { wp_clear_scheduled_hook( 'acft_refresh_google_fonts_event' ); }
function wk( $w, $family ) { $k = array_map( 'strval', array_keys( $w[ $family ] ) ); sort( $k, SORT_NUMERIC ); return implode( ',', $k ); }

echo "-- #4 font weights (regular and bold always added)\n";
$w = array(); acft_collect_font_weights( array( 'font_family' => 'Roboto', 'font_weight' => array( '100' ) ), $w );
t( '4a array weight: no warning', 0, count( $warn ) ); t( '4b array weight ignored', '400,700', wk( $w, 'Roboto' ) );
$w = array(); acft_collect_font_weights( array( 'font_family' => 'Roboto', 'font_weight' => 300 ), $w ); t( '4c int weight kept', '300,400,700', wk( $w, 'Roboto' ) );
$w = array(); acft_collect_font_weights( array( 'font_family' => 'Roboto', 'font_weight' => 'bold' ), $w ); t( '4d non-numeric weight ignored', '400,700', wk( $w, 'Roboto' ) );
$w = array(); acft_collect_font_weights( array( 'rows' => array( array( 'font_family' => 'Lato', 'font_weight' => '900' ), array( 'font_family' => 'Roboto', 'font_weight' => '100' ) ) ), $w );
t( '4e nested values keep their own weights', 'Lato=400,700,900 Roboto=100,400,700', 'Lato=' . wk( $w, 'Lato' ) . ' Roboto=' . wk( $w, 'Roboto' ) );

echo "-- #3 families missing from the list\n";
setc( array() );
t( '3a listed', true, acft_is_google_font_family( 'Open Sans' ) );
t( '3b unlisted Google-style name', true, acft_is_google_font_family( 'Merriweather' ) );
t( '3c digits + hyphen', true, acft_is_google_font_family( 'M PLUS 1p' ) );
t( '3d web-safe', false, acft_is_google_font_family( 'Georgia, serif' ) );
t( '3e initial', false, acft_is_google_font_family( 'initial' ) );
t( '3f URL-breaking name', false, acft_is_google_font_family( 'Foo&family=Bar' ) );
t( '3g quoted web-safe', false, acft_is_google_font_family( '"Arial Black", Gadget, sans-serif' ) );
t( '3h URL from given weights', 'https://fonts.googleapis.com/css?family=Merriweather:300|Roboto:300,700&display=swap', acft_google_fonts_url( array( 'Merriweather' => array( '300' => true ), 'Roboto' => array( '700' => true, '300' => true ), 'Georgia, serif' => array( '400' => true ) ) ) );
$w = array(); acft_collect_font_weights( array( array( 'font_family' => 'Merriweather', 'font_weight' => '300' ), array( 'font_family' => 'Open Sans', 'font_weight' => '' ), array( 'font_family' => 'Georgia, serif', 'font_weight' => '400' ) ), $w );
t( '3i URL from saved values', 'https://fonts.googleapis.com/css?family=Merriweather:300,400,700|Open+Sans:400,700&display=swap', acft_google_fonts_url( $w ) );

echo "-- #1 daily background refresh\n";
update_option( 'acft_test_http_mock', 'ok' );
function ev() { $e = wp_get_scheduled_event( 'acft_refresh_google_fonts_event' ); return $e ? $e->schedule : 'none'; }
unsched(); acft_schedule_google_fonts_refresh(); t( '1a missing event: scheduled daily', 'daily', ev() );
$ts = wp_next_scheduled( 'acft_refresh_google_fonts_event' ); acft_schedule_google_fonts_refresh(); t( '1b already scheduled: left alone', $ts, wp_next_scheduled( 'acft_refresh_google_fonts_event' ) );
unsched(); wp_schedule_single_event( time() + 60, 'acft_refresh_google_fonts_event' ); acft_schedule_google_fonts_refresh();
t( '1c one-off event replaced by the daily one', array( 'daily', 1 ), array( ev(), count( array_filter( _get_cron_array(), function ( $h ) { return isset( $h['acft_refresh_google_fonts_event'] ); } ) ) ) );
setc( array( 'fetched' => time() - 8 * DAY_IN_SECONDS, 'families' => array( 'Old' ) ) ); do_action( 'acft_refresh_google_fonts_event' );
t( '1d run with a stale list: fetched', '|3', acft_get_google_fonts_cache()['error'] . '|' . count( acft_get_google_fonts_cache()['families'] ) );
setc( array( 'fetched' => time() - DAY_IN_SECONDS, 'families' => array( 'Kept' ) ) ); do_action( 'acft_refresh_google_fonts_event' );
t( '1e run with a fresh list: no fetch', array( 'Kept' ), acft_get_google_fonts_cache()['families'] );
setc( array( 'error' => 'HTTP 400', 'attempted' => time() - 60, 'families' => array( 'Kept' ) ) ); do_action( 'acft_refresh_google_fonts_event' );
t( '1f run within an hour of a failure: no fetch', 'HTTP 400|Kept', acft_get_google_fonts_cache()['error'] . '|' . implode( ',', acft_get_google_fonts_cache()['families'] ) );
setc( array( 'error' => 'HTTP 400', 'attempted' => time() - 2 * HOUR_IN_SECONDS, 'fetched' => 0 ) ); do_action( 'acft_refresh_google_fonts_event' );
t( '1g run after a failure over an hour ago: recovers', '|3', acft_get_google_fonts_cache()['error'] . '|' . count( acft_get_google_fonts_cache()['families'] ) );
unsched(); setc( array( 'fetched' => time() - 8 * DAY_IN_SECONDS, 'families' => array( 'Old' ) ) ); $got = acft_get_google_font_family();
t( '1h showing a field neither fetches nor schedules', array( array( 'Old' => 'Old' ), false ), array( $got, (bool) wp_next_scheduled( 'acft_refresh_google_fonts_event' ) ) );
unsched();

echo "-- #2 lock\n";
acft_google_fonts_lock( false );
t( '2a take', true, acft_google_fonts_lock() ); t( '2b second take refused', false, acft_google_fonts_lock() );
acft_google_fonts_lock( false ); t( '2c released, take again', true, acft_google_fonts_lock() );
global $wpdb; $wpdb->update( $wpdb->options, array( 'option_value' => (string) ( time() - 5 ) ), array( 'option_name' => 'acft_google_fonts_lock' ) );
t( '2d stale lock taken over', true, acft_google_fonts_lock() );
$row = $wpdb->get_row( "SELECT option_value, autoload FROM {$wpdb->options} WHERE option_name='acft_google_fonts_lock'" );
t( '2e lock row not autoloaded', 'no', $row->autoload );
setc( array( 'fetched' => time() - 8 * DAY_IN_SECONDS ) ); $before = get_option( 'acft_google_fonts' );
acft_refresh_google_fonts(); t( '2f held lock blocks a normal refresh', $before, get_option( 'acft_google_fonts' ) );
acft_google_fonts_lock( false );
acft_refresh_google_fonts(); t( '2g lock released after refresh', null, $wpdb->get_var( "SELECT option_value FROM {$wpdb->options} WHERE option_name='acft_google_fonts_lock'" ) );
t( '2h stale list refreshed', true, acft_get_google_fonts_cache()['fetched'] >= time() - 5 );

echo "-- #2 newer result is kept (competing write happens during the HTTP call)\n";
// runs before the mock (priority 1): saves a competing result while this request "waits on Google"
function during( $c ) { $GLOBALS['during'] = $c; }
add_filter( 'pre_http_request', function ( $r ) { if ( null !== $GLOBALS['during'] ) { setc( $GLOBALS['during'] ); $GLOBALS['during'] = null; } return $r; }, 1 );
$GLOBALS['during'] = null;
update_option( 'acft_test_http_mock', 'ok' );
setc( array( 'fetched' => time() - 8 * DAY_IN_SECONDS ) ); during( array( 'key_hash' => 'otherkey', 'families' => array( 'X' ) ) ); acft_refresh_google_fonts();
t( '2i newer result for another key kept', array( 'otherkey', array( 'X' ) ), array( acft_get_google_fonts_cache()['key_hash'], acft_get_google_fonts_cache()['families'] ) );
setc( array() ); during( array( 'key_hash' => 'oldkey', 'families' => array( 'X' ) ) ); acft_refresh_google_fonts( true );
t( '2n settings save replaces a newer result for the old key', array( $GLOBALS['key'], 3 ), array( acft_get_google_fonts_cache()['key_hash'], count( acft_get_google_fonts_cache()['families'] ) ) );
setc( array() ); during( array( 'error' => 'HTTP 500', 'families' => array() ) ); acft_refresh_google_fonts( true );
t( '2j newer failure (same key) replaced by success', '|3', acft_get_google_fonts_cache()['error'] . '|' . count( acft_get_google_fonts_cache()['families'] ) );
update_option( 'acft_test_http_mock', 'error400' );
setc( array( 'fetched' => time() - 8 * DAY_IN_SECONDS ) ); during( array( 'families' => array( 'Y' ) ) ); acft_refresh_google_fonts( true );
t( '2k newer success not replaced by failure', '|Y', acft_get_google_fonts_cache()['error'] . '|' . implode( ',', acft_get_google_fonts_cache()['families'] ) );
setc( array() ); acft_refresh_google_fonts( true );
t( '2l no competing write: failure recorded as before', true, '' !== acft_get_google_fonts_cache()['error'] );
update_option( 'acft_test_http_mock', 'ok' );
setc( array( 'attempted' => time(), 'fetched' => time() - 8 * DAY_IN_SECONDS ) ); acft_refresh_google_fonts();
t( '2m same-second cache still refreshed', true, acft_get_google_fonts_cache()['fetched'] >= time() - 5 );

// restore (the save hooks are still removed, so this does not fetch)
false ===$bak['settings'] ? delete_option( 'acft_settings' ) : update_option( 'acft_settings', $bak['settings'] );
false === $bak['fonts'] ? delete_option( 'acft_google_fonts' ) : update_option( 'acft_google_fonts', $bak['fonts'], false );
false === $bak['mock'] ? delete_option( 'acft_test_http_mock' ) : update_option( 'acft_test_http_mock', $bak['mock'] );
acft_google_fonts_lock( false );
unsched();
restore_error_handler();
echo 'warnings: ' . count( $warn ) . ( $warn ? ' ' . implode( ' | ', array_unique( $warn ) ) : '' ) . "\n";
echo "RESULT: {$GLOBALS['pass']} passed, {$GLOBALS['fail']} failed\n";
