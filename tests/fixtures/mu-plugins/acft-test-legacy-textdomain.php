<?php
/**
 * Plugin Name: ACFT test legacy text domain (dev only)
 * Description: Changes plugin strings the way a theme could before 3.3.0, through the old 'acf-typography' / 'acf' domains. Never ship.
 *
 * Usage: wp option update acft_test_legacy_td '["filter","domain_filter","mo","acf","acf_default","new_mo"]' --format=json
 *   filter        gettext filter, domain acf-typography: Typography -> Font Settings
 *   domain_filter gettext_acf-typography filter: Font Family -> Schriftart
 *   mo            .mo loaded under acf-typography: Text Color -> Legacy MO Color
 *   acf           gettext filter, domain acf: Google Fonts Key -> Fonts API Key
 *   acf_default   gettext filter, domain acf: Default -> ACF Default (must NOT reach our strings)
 *   new_mo        .mo loaded under acf-typography-field: Typography -> New Domain Typo, Letter Spacing -> New Spacing
 *
 * @package ACF_Typography_Field
 */

$acft_test_legacy_td = get_option( 'acft_test_legacy_td' );

if ( is_array( $acft_test_legacy_td ) ) {

	if ( in_array( 'filter', $acft_test_legacy_td, true ) || in_array( 'acf', $acft_test_legacy_td, true ) || in_array( 'acf_default', $acft_test_legacy_td, true ) ) {
		add_filter(
			'gettext',
			function ( $translation, $text, $domain ) use ( $acft_test_legacy_td ) {
				if ( in_array( 'filter', $acft_test_legacy_td, true ) && 'acf-typography' === $domain && 'Typography' === $text ) {
					return 'Font Settings';
				}
				if ( in_array( 'acf', $acft_test_legacy_td, true ) && 'acf' === $domain && 'Google Fonts Key' === $text ) {
					return 'Fonts API Key';
				}
				if ( in_array( 'acf_default', $acft_test_legacy_td, true ) && 'acf' === $domain && 'Default' === $text ) {
					return 'ACF Default';
				}
				return $translation;
			},
			10,
			3
		);
	}

	if ( in_array( 'domain_filter', $acft_test_legacy_td, true ) ) {
		add_filter(
			'gettext_acf-typography',
			function ( $translation, $text ) {
				return 'Font Family' === $text ? 'Schriftart' : $translation;
			},
			10,
			2
		);
	}

	// .mo files are built on the fly with WordPress's own MO writer
	$acft_test_mo = array(
		'mo'     => array( 'acf-typography', array( 'Text Color' => 'Legacy MO Color' ) ),
		'new_mo' => array( 'acf-typography-field', array( 'Typography' => 'New Domain Typo', 'Letter Spacing' => 'New Spacing' ) ),
	);
	foreach ( $acft_test_mo as $acft_test_mode => $acft_test_def ) {
		if ( ! in_array( $acft_test_mode, $acft_test_legacy_td, true ) ) {
			continue;
		}
		require_once ABSPATH . WPINC . '/pomo/mo.php';
		$acft_test_file = WP_CONTENT_DIR . '/uploads/acft-test-' . $acft_test_def[0] . '.mo';
		$acft_test_obj  = new MO();
		foreach ( $acft_test_def[1] as $acft_test_orig => $acft_test_tr ) {
			$acft_test_obj->add_entry( new Translation_Entry( array( 'singular' => $acft_test_orig, 'translations' => array( $acft_test_tr ) ) ) );
		}
		$acft_test_obj->export_to_file( $acft_test_file );
		load_textdomain( $acft_test_def[0], $acft_test_file );
	}
}
