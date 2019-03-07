<?php

/**
 *  get_typography_field()
 */
function get_typography_field( $selector, $property, $post_id = false, $format_value = true ) {
	
	// filter post_id
	$post_id = acf_get_valid_post_id( $post_id );
	
	// get field
    $field = acf_maybe_get_field( $selector, $post_id );
	
	// create dummy field
	if( !$field ) {	
		$field = acf_get_valid_field(array(
			'name'	=> $selector,
			'key'	=> '',
			'type'	=> '',
		));
		
		// prevent formatting
		$format_value = false;
	}
	
	// get value for field
    $value = acf_get_value( $post_id, $field );
	
	// format value
	if( $format_value ) {	
		// get value for field
		$value = acf_format_value( $value, $post_id, $field );
    }
    
    // get property
    if( is_array($value) && array_key_exists( $property, $value ) )
        $property_value = $value[ $property ];
    else
        $property_value = '';
    
	return $property_value;
 
}

/**
 *  the_typography_field()
 */
function the_typography_field( $selector, $property, $post_id = false, $format_value = true ) {
	
	$value = get_typography_field($selector, $property, $post_id, $format_value);
	
	if( is_array($value) ) {
		$value = @implode( ', ', $value );
	}
	
	echo $value;
	
}

/*
*  acf_typography_shortcode()
*
*  [acf_typography field="heading" property="font_size" post_id="123" format_value="1"]
*/

function acf_typography_shortcode( $atts ) {
	
	// extract attributs
	extract( shortcode_atts( array(
		'field'			=> '',
		'property'		=> '',
		'post_id'		=> false,
		'format_value'	=> true
	), $atts ) );
	
	
	// get value and return it
	$value = get_typography_field( $field, $property, $post_id, $format_value );
	
	
	// array
	if( is_array($value) ) {
		
		$value = @implode( ', ', $value );
		
	}
	
	
	// return
	return $value;
	
}

add_shortcode('acf_typography', 'acf_typography_shortcode');