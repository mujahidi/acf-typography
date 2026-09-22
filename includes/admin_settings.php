<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

	register_setting(
		'acf-typography-field',
		'acft_settings',
		array( 'sanitize_callback' => 'acft_sanitize_settings' )
	);

	add_settings_section(
		'acft_acf-typography-field_section',
		'',
		'acft_settings_section_callback',
		'acf-typography-field'
	);

	add_settings_field(
		'acft_text_field_0',
		__( 'Google Fonts Key', 'acf-typography-field' ),
		'acft_google_key_field',
		'acf-typography-field',
		'acft_acf-typography-field_section'
	);
}

/**
 * Sanitize the API key submitted through the settings form.
 *
 * @param mixed $settings Submitted option value.
 * @return array
 */
function acft_sanitize_settings( $settings ) {
	if ( in_array( acft_google_api_key_source(), array( 'constant', 'legacy_constant' ), true ) ) {
		return array( 'google_key' => acft_get_saved_google_api_key() );
	}

	if ( ! is_array( $settings ) || ! isset( $settings['google_key'] ) || ! is_string( $settings['google_key'] ) ) {
		return array( 'google_key' => '' );
	}

	return array( 'google_key' => sanitize_text_field( $settings['google_key'] ) );
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


/**
 *  Is the current admin screen one of ACF's own screens?
 *
 *  Covers the field group, post type, taxonomy and options page editors and their
 *  sub-pages (e.g. Tools), plus ACF 4's "acf" post type. Not the plugin's own settings
 *  page, which shows the error inline.
 *
 *  acft_is_acf_admin_screen()
 *
 *  @since      3.3.0
 *  @return     bool
 */
function acft_is_acf_admin_screen() {

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || empty( $screen->post_type ) ) {
		return false;
	}

	return 'acf' === $screen->post_type || 0 === strpos( $screen->post_type, 'acf-' );
}


/**
 *  Warn admins on ACF screens when the Google Fonts list could not be loaded
 *
 *  Without it a broken key goes unnoticed: the field just offers web-safe fonts (or
 *  an old list). Kept to one line; the full error is on the settings page. Not
 *  dismissible; it disappears once a fetch succeeds.
 *
 *  acft_google_fonts_error_notice()
 *
 *  @since      3.3.0
 */
add_action( 'admin_notices', 'acft_google_fonts_error_notice' );
function acft_google_fonts_error_notice() {

	if ( ! current_user_can( 'manage_options' ) || ! acft_is_acf_admin_screen() || '' === acft_google_api_key_source() ) {
		return;
	}

	$fonts_cache = acft_get_google_fonts_cache();

	if ( '' === $fonts_cache['error'] ) {
		return;
	}

	?>
	<div class="notice notice-warning">
		<p>
			<?php esc_html_e( 'ACF Typography: Google Fonts could not be loaded.', 'acf-typography-field' ); ?>
			<a class="button button-small" href="<?php echo esc_url( admin_url( 'options-general.php?page=acf-typography-field' ) ); ?>"><?php esc_html_e( 'Settings', 'acf-typography-field' ); ?></a>
		</p>
	</div>
	<?php
}


function acft_options_page() {

	?>
	<form action='options.php' method='post'>

		<h2><?php esc_html_e( 'ACF Typography Settings', 'acf-typography-field' ); ?></h2>

		<?php
		settings_fields( 'acf-typography-field' );
		do_settings_sections( 'acf-typography-field' );
		submit_button();
		?>

	</form>
	<?php
}
