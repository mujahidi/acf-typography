<?php
/**
 * Render template for the acf/acft-meta test block.
 *
 * @package ACF_Typography_Field
 */

$acft_value = get_field( 'block_typo_meta' );
printf(
	'<p class="acft-test-block acft-test-block-meta">acft-meta: %s</p>',
	esc_html( is_array( $acft_value ) ? wp_json_encode( $acft_value ) : 'no value' )
);
