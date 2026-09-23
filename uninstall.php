<?php
/**
 *  Remove the plugin's data when it is deleted
 *
 *  The settings (Google Fonts API key), the cached Google Fonts list, a lock row
 *  left by a request that died mid-fetch, and a pending background refresh.
 *  On multisite, from every site.
 *
 *  @since      3.3.0
 */

// exit unless WordPress is uninstalling the plugin
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function acft_uninstall_site() {
	delete_option( 'acft_settings' );
	delete_option( 'acft_google_fonts' );
	delete_option( 'acft_google_fonts_lock' );
	wp_clear_scheduled_hook( 'acft_refresh_google_fonts_event' );
}

if ( is_multisite() ) {
	$acft_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $acft_site_ids as $acft_site_id ) {
		switch_to_blog( $acft_site_id );
		acft_uninstall_site();
		restore_current_blog();
	}
} else {
	acft_uninstall_site();
}
