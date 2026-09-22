<?php

/**
 *  Admin settings page for Google Fonts API
 *
 *  @since      3.0.0
 */

add_action( 'admin_menu', 'acft_add_admin_menu' );
add_action( 'admin_init', 'acft_settings_init' );


function acft_add_admin_menu() {

	add_submenu_page( 'options-general.php', 'ACF Typography Settings', 'ACF Typography Settings', 'manage_options', 'acf-typography-field', 'acft_options_page' );
}

function acft_settings_init() {

	register_setting( 'acf-typography-field', 'acft_settings' );

	add_settings_section(
		'acft_acf-typography-field_section',
		__( '', 'acf' ),
		'acft_settings_section_callback',
		'acf-typography-field'
	);

	add_settings_field(
		'acft_text_field_0',
		__( 'Google Fonts Key', 'acf' ),
		'acft_google_key_field',
		'acf-typography-field',
		'acft_acf-typography-field_section'
	);
}


function acft_google_key_field() {

	$google_key = acft_get_saved_google_api_key();
	$key_source = acft_google_api_key_source();
	// readonly, not disabled: a disabled field is not submitted and saving would wipe the stored key
	$readonly = in_array( $key_source, array( 'constant', 'legacy_constant' ), true );
	?>
	<input type='text' name='acft_settings[google_key]' value='<?php echo esc_attr( $google_key ); ?>'<?php echo $readonly ? ' readonly' : ''; ?>>
	<?php
	if ( 'constant' === $key_source ) {
		echo '<p class="description">' . wp_kses( __( 'The key is set by the <code>ACFT_GOOGLE_API_KEY</code> constant, which overrides this field.', 'acf-typography-field' ), array( 'code' => array() ) ) . '</p>';
	} elseif ( 'legacy_constant' === $key_source ) {
		echo '<p class="description">' . wp_kses( __( 'The key is set by the <code>YOUR_API_KEY</code> constant, which overrides this field. That constant is deprecated and will stop working in 4.0. Please rename it to <code>ACFT_GOOGLE_API_KEY</code>.', 'acf-typography-field' ), array( 'code' => array() ) ) . '</p>';
	}

	if ( '' === $key_source ) {
		return;
	}

	$fonts_cache = acft_get_google_fonts_cache();

	if ( '' !== $fonts_cache['error'] ) {
		/* translators: %s: error message returned by the Google Fonts API */
		echo '<div class="notice notice-error inline"><p>' . esc_html( sprintf( __( 'Google Fonts could not be loaded: %s', 'acf-typography-field' ), $fonts_cache['error'] ) ) . '</p></div>';
	}

	if ( $fonts_cache['fetched'] ) {
		/* translators: 1: number of Google Fonts, 2: date and time of the last update */
		echo '<p class="description">' . esc_html( sprintf( __( '%1$d Google Fonts available, last updated %2$s.', 'acf-typography-field' ), count( $fonts_cache['families'] ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $fonts_cache['fetched'] ) ) ) . '</p>';
	}
}


function acft_settings_section_callback() {}


function acft_options_page() {

	?>
	<form action='options.php' method='post'>

		<h2>ACF Typography Settings</h2>

		<?php
		settings_fields( 'acf-typography-field' );
		do_settings_sections( 'acf-typography-field' );
		submit_button();
		?>

	</form>
	<?php
}