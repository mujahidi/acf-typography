<?php
/**
 * Render template for the acf/acft-empty test block.
 *
 * @package ACF_Typography_Field
 */

$acft_value = get_field( 'block_typo_empty' );
printf(
	'<p class="acft-test-block acft-test-block-empty">acft-empty: %s</p>',
	esc_html( is_array( $acft_value ) ? wp_json_encode( $acft_value ) : 'no value' )
);
