<?php
/**
 * Admin settings page for Google Fonts API.
 *
 * @package ACF_Typography
 * @since 3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'acft_add_admin_menu' );
add_action( 'admin_init', 'acft_settings_init' );

/**
 * Add admin menu for ACF Typography settings.
 *
 * @since 3.0.0
 */
function acft_add_admin_menu() {
	add_submenu_page(
		'options-general.php',
		__( 'ACF Typography Settings', 'acf-typography' ),
		__( 'ACF Typography Settings', 'acf-typography' ),
		'manage_options',
		'acf-typography-field',
		'acft_options_page'
	);
}

/**
 * Initialize settings for ACF Typography.
 *
 * @since 3.0.0
 */
function acft_settings_init() {
	register_setting(
		'acf-typography-field',
		'acft_settings',
		array(
			'sanitize_callback' => 'acft_sanitize_settings',
		)
	);

	add_settings_section(
		'acft_acf-typography-field_section',
		__( '', 'acf-typography' ),
		'acft_settings_section_callback',
		'acf-typography-field'
	);

	add_settings_field(
		'acft_text_field_0',
		__( 'Google Fonts Key', 'acf-typography' ),
		'acft_google_key_field',
		'acf-typography-field',
		'acft_acf-typography-field_section'
	);
}


/**
 * Sanitize settings before saving.
 *
 * @since 3.2.3
 * @param array $input Input settings.
 * @return array Sanitized settings.
 */
function acft_sanitize_settings( $input ) {
	$sanitized = array();
	
	if ( isset( $input['google_key'] ) ) {
		$sanitized['google_key'] = sanitize_text_field( $input['google_key'] );
	}
	
	return $sanitized;
}

/**
 * Render Google Fonts API key field.
 *
 * @since 3.0.0
 */
function acft_google_key_field() {
	$acft_options = get_option( 'acft_settings' );
	$google_key   = '';
	if ( $acft_options && isset( $acft_options['google_key'] ) ) {
		$google_key = $acft_options['google_key'];
	}
	?>
	<input type='text' name='acft_settings[google_key]' value='<?php echo esc_attr( $google_key ); ?>'>
	<?php
}


/**
 * Settings section callback.
 *
 * @since 3.0.0
 */
function acft_settings_section_callback() {}

/**
 * Render the options page.
 *
 * @since 3.0.0
 */
function acft_options_page() {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action='options.php' method='post'>
			<?php
			settings_fields( 'acf-typography-field' );
			do_settings_sections( 'acf-typography-field' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}