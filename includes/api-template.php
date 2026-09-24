<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  get_typography_field()
 *
 *  @since      3.0.0
 */
function get_typography_field( $selector, $property, $post_id = false, $format_value = true ) {

	if ( function_exists( 'acf_get_valid_post_id' ) ) {

		// filter post_id
		$post_id = acf_get_valid_post_id( $post_id );

		// get field
		$field = acf_maybe_get_field( $selector, $post_id );

		// create dummy field
		if ( ! $field ) {
			$field = acf_get_valid_field(
				array(
					'name' => $selector,
					'key'  => '',
					'type' => '',
				)
			);

			// prevent formatting
			$format_value = false;
		}

		// get value for field
		$value = acf_get_value( $post_id, $field );

		// format value
		if ( $format_value ) {
			// get value for field
			$value = acf_format_value( $value, $post_id, $field );
		}
	} elseif ( function_exists( 'get_field' ) ) {
		// ACF 4 has no acf_get_valid_post_id()
		$value = get_field( $selector, $post_id, $format_value );
	} else {
		// ACF inactive
		return '';
	}

	// get property
	if ( is_array( $value ) && array_key_exists( $property, $value ) ) {
		$property_value = esc_attr( $value[ $property ] );
	} else {
		$property_value = '';
	}

	return $property_value;
}

/**
 *  the_typography_field()
 *
 *  @since      3.0.0
 */
function the_typography_field( $selector, $property, $post_id = false, $format_value = true ) {

	$value = get_typography_field( $selector, $property, $post_id, $format_value );

	if ( is_array( $value ) ) {
		$value = @implode( ', ', $value );
	}

	// already escaped: the getter returns esc_attr() output (unchanged since 3.0)
	echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 *  get_typography_sub_field()
 *
 *  @since      3.0.0
 */
function get_typography_sub_field( $selector, $property, $format_value = true, $load_value = true ) {

	// ACF inactive
	if ( ! function_exists( 'get_sub_field_object' ) ) {
		return false;
	}

	// get sub field
	$sub_field = get_sub_field_object( $selector, $format_value );

	// bail early if no sub field
	if ( ! $sub_field ) {
		return false;
	}

	if ( is_array( $sub_field['value'] ) && array_key_exists( $property, $sub_field['value'] ) ) {
		$property_value = esc_attr( $sub_field['value'][ $property ] );
	} else {
		$property_value = '';
	}

	// return
	return $property_value;
}

/**
 *  the_typography_sub_field()
 *
 *  @since      3.0.0
 */
function the_typography_sub_field( $field_name, $property, $format_value = true ) {

	$value = get_typography_sub_field( $field_name, $property, $format_value );

	if ( is_array( $value ) ) {

		$value = implode( ', ', $value );

	}

	// already escaped: the getter returns esc_attr() output (unchanged since 3.0)
	echo $value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
*  acf_typography_shortcode()
*
*  [acf_typography field="heading" property="font_size" post_id="123" format_value="1"]
*
*  @since       3.0.0
*/

function acf_typography_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'field'        => '',
			'property'     => '',
			'post_id'      => false,
			'format_value' => true,
		),
		$atts
	);

	if ( ! is_string( $atts['field'] ) || ! is_string( $atts['property'] ) || '' === $atts['field'] || '' === $atts['property'] ) {
		return '';
	}

	// not sanitize_key(): it lowercases and strips dots, and ACF field names are case-sensitive
	$field    = sanitize_text_field( $atts['field'] );
	$property = sanitize_key( $atts['property'] );
	$post_id  = false;
	if ( is_scalar( $atts['post_id'] ) && false !== $atts['post_id'] ) {
		$post_id = sanitize_text_field( (string) $atts['post_id'] );
	}
	$format_value = filter_var( $atts['format_value'], FILTER_VALIDATE_BOOLEAN );

	// anyone who can write a post can use the shortcode, so it must not read other data
	if ( ! acft_shortcode_field_is_typography( $field, $post_id ) ) {
		return '';
	}

	// get value and return it
	$value = get_typography_field( $field, $property, $post_id, $format_value );

	// array
	if ( is_array( $value ) ) {

		$value = @implode( ', ', $value );

	}

	// return
	return $value;
}

add_shortcode( 'acf_typography', 'acf_typography_shortcode' );

/**
 *  Whether the shortcode's field is a Typography field
 *
 *  get_typography_field() reads any meta value by name, like ACF's get_field(). That is
 *  fine in theme code, but the shortcode's attributes come from whoever writes the post,
 *  e.g. a Contributor, who could otherwise read other stored data (user roles, other
 *  plugins' settings). Typography values themselves are not secret: they end up on the
 *  page as CSS, so they are printed from any post, user, term or options page.
 *
 *  acft_shortcode_field_is_typography()
 *
 *  @since      3.3.0
 *  @param      string       $selector  Field name or key.
 *  @param      string|false $post_id   Post ID as given to the shortcode; false is the current post.
 *  @return     bool
 */
function acft_shortcode_field_is_typography( $selector, $post_id ) {

	if ( function_exists( 'acf_get_valid_post_id' ) ) {
		$post_id = acf_get_valid_post_id( $post_id );
		$field   = acf_maybe_get_field( $selector, $post_id );
	} elseif ( function_exists( 'get_field_object' ) ) {
		// ACF 4: a name that is not a field comes back as a stand-in text field
		$post_id = apply_filters( 'acf/get_post_id', $post_id ); // phpcs:ignore WordPress.NamingConventions -- ACF 4's own filter, as its get_field() uses it
		$field   = get_field_object( $selector, $post_id, array( 'load_value' => false ) );
	} else {
		return false;
	}

	if ( ! is_array( $field ) || ! isset( $field['type'], $field['name'], $field['key'] ) || 'Typography' !== $field['type'] ) {
		return false;
	}

	// a name is found through the reference ACF saves next to the value, but a key finds
	// its field anywhere, and the value is then read by the field's name: also require
	// the reference, so a key cannot read same-named data the object stores for other code
	if ( 0 === strpos( $selector, 'field_' ) ) {
		return acft_get_field_reference( $field['name'], $post_id ) === $field['key'];
	}

	return true;
}

/**
 *  Get the field key ACF saved next to a value
 *
 *  acft_get_field_reference()
 *
 *  @since      3.3.0
 *  @param      string $field_name  Field name.
 *  @param      mixed  $post_id     Post ID as ACF resolves it, e.g. 12, 'user_1' or 'options'.
 *  @return     string  Field key, or '' when there is none.
 */
function acft_get_field_reference( $field_name, $post_id ) {

	if ( function_exists( 'acf_get_reference' ) ) {
		$reference = acf_get_reference( $field_name, $post_id ); // ACF 5.9+
	} elseif ( function_exists( 'acf_get_field_reference' ) ) {
		$reference = acf_get_field_reference( $field_name, $post_id ); // ACF 5 before 5.9
	} elseif ( function_exists( 'get_field_reference' ) ) {
		$reference = get_field_reference( $field_name, $post_id ); // ACF 4
	} else {
		$reference = '';
	}

	return is_string( $reference ) ? $reference : '';
}
