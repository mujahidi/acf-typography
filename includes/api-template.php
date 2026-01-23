<?php
/**
 * API Template functions for ACF Typography field.
 *
 * @package ACF_Typography
 * @since 3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get typography field value.
 *
 * @since 3.0.0
 * @param string $selector Field name or key.
 * @param string $property Property name to get.
 * @param mixed  $post_id Post ID.
 * @param bool   $format_value Whether to format the value.
 * @return string Property value.
 */
function get_typography_field( $selector, $property, $post_id = false, $format_value = true ) {
	// Filter post_id.
	$post_id = acf_get_valid_post_id( $post_id );

	// Get field.
	$field = acf_maybe_get_field( $selector, $post_id );

	// Create dummy field.
	if ( ! $field ) {
		$field = acf_get_valid_field(
			array(
				'name' => $selector,
				'key'  => '',
				'type' => '',
			)
		);

		// Prevent formatting.
		$format_value = false;
	}

	// Get value for field.
	$value = acf_get_value( $post_id, $field );

	// Format value.
	if ( $format_value ) {
		// Get value for field.
		$value = acf_format_value( $value, $post_id, $field );
	}

	// Get property.
	$property_value = '';
	if ( is_array( $value ) && array_key_exists( $property, $value ) ) {
		$property_value = esc_attr( $value[ $property ] );
	}

	return $property_value;
}

/**
 * Display typography field value.
 *
 * @since 3.0.0
 * @param string $selector Field name or key.
 * @param string $property Property name to display.
 * @param mixed  $post_id Post ID.
 * @param bool   $format_value Whether to format the value.
 */
function the_typography_field( $selector, $property, $post_id = false, $format_value = true ) {
	$value = get_typography_field( $selector, $property, $post_id, $format_value );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo esc_html( $value );
}

/**
 * Get typography sub field value.
 *
 * @since 3.0.0
 * @param string $selector Field name or key.
 * @param string $property Property name to get.
 * @param bool   $format_value Whether to format the value.
 * @param bool   $load_value Whether to load the value (not used).
 * @return string Property value.
 */
function get_typography_sub_field( $selector, $property, $format_value = true, $load_value = true ) {
	// Get sub field.
	$sub_field = get_sub_field_object( $selector, $format_value );

	// Bail early if no sub field.
	if ( ! $sub_field ) {
		return false;
	}

	$property_value = '';
	if ( is_array( $sub_field['value'] ) && array_key_exists( $property, $sub_field['value'] ) ) {
		$property_value = esc_attr( $sub_field['value'][ $property ] );
	}

	// Return.
	return $property_value;
}

/**
 * Display typography sub field value.
 *
 * @since 3.0.0
 * @param string $field_name Field name.
 * @param string $property Property name to display.
 * @param bool   $format_value Whether to format the value.
 */
function the_typography_sub_field( $field_name, $property, $format_value = true ) {
	$value = get_typography_sub_field( $field_name, $property, $format_value );

	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo esc_html( $value );
}

/**
 * ACF Typography shortcode.
 *
 * Usage: [acf_typography field="heading" property="font_size" post_id="123" format_value="1"]
 *
 * @since 3.0.0
 * @param array $atts Shortcode attributes.
 * @return string Field value.
 */
function acf_typography_shortcode( $atts ) {
	// Parse attributes.
	$atts = shortcode_atts(
		array(
			'field'        => '',
			'property'     => '',
			'post_id'      => false,
			'format_value' => true,
		),
		$atts,
		'acf_typography'
	);

	// Get value and return it.
	$value = get_typography_field( $atts['field'], $atts['property'], $atts['post_id'], $atts['format_value'] );

	// Array.
	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	// Return.
	return $value;
}
add_shortcode( 'acf_typography', 'acf_typography_shortcode' );