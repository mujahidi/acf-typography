<?php


/**
 *  Get a valid post_id for the current object
 * 
 *  acft_get_valid_post_id( $post_id )
 * 
 *  @param	(str) $post_id (optional) 
 *                  A post_id to retrieve fields
 * 
 *  @return	(str) post_id when valid post_id found, or 
 *              (bool) false when no valid post_id is found 
 * 
 *  @since      3.2.4
 */
function acft_get_valid_post_id( $post_id = false ) {

    // supplied a post_id, use it
    if ( $post_id ) {
        $post_obj = get_post( intval( $post_id ) );
        if ( is_object( $post_obj ) && !is_wp_error( $post_obj ) ) {
            $post_id = $post_obj->ID;
    }}

    // if no post_id, try getting post_id if called inside the loop
    if ( $post_id === false ) {
        global $post;
        if ( is_object( $post ) ) {
            $post_id = $post->ID;
    }}

    // if still no post_id, try getting post_id if called outside the loop
    if ( $post_id === false ) {
        global $wp_query; 
        if ( is_object( $wp_query->post ) ) {
            $post_id = $wp_query->post->ID;
    }}

    return $post_id;
}


/**
 *  Update Google Fonts JSON file
 * 
 *  acft_update_gf_json_file()
 * 
 *  @since		3.0.0
 */
function acft_update_gf_json_file( $API_KEY ) {

    $dir = plugin_dir_path( dirname(__FILE__) );
    $filename = $dir . 'google_fonts.json';

    if ( file_exists( $filename ) ) {
        
        $file_date = date ( "Ymd", filemtime( $filename ) );
        $now = date ( "Ymd", time() );
        $time = $now - $file_date;
        
        if ( !filesize( $filename ) || $time > 2 ) {

            // suppress errors for now
            $json = @file_get_contents( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $API_KEY );
            
            // if we didn't get an error, save json data to file
            if ( $json ) {

                $gf_file = fopen( $filename, 'wb' );
                fwrite( $gf_file, $json );
                fclose( $gf_file );
                
            } // end if valid response check

        } // end if json file empty / last check more than 2 days ago

    } // end if json file exists check

}

/**
 *  Get google fonts for Font-Family drop-down subfield
 * 
 *  acft_get_google_font_family()
 * 
 *  @since		3.0.0
 */
function acft_get_google_font_family(){

    if ( !defined( 'YOUR_API_KEY' ) ) return;

    acft_update_gf_json_file( YOUR_API_KEY );

    // Load json file for extra seting
    $dir = plugin_dir_path( dirname(__FILE__) );
    $json = file_get_contents( "{$dir}google_fonts.json" );
    $fontArray = json_decode( $json );
    $font_family = array();

    if( $fontArray ){
        foreach ( $fontArray as $k => $v ) {
            if ( is_array( $v ) ){
                foreach ( $v as $value ){
                    foreach ( $value as $key1 => $value1 ) {
                        if( $key1== "family" ){
                            $font_family[ $value1 ] = $value1;
                        }		
                    }
                }
            }
        }
    }

    return $font_family;

}


/**
 *  Get all fields for an object (post, block, option)
 * 
 *  acft_get_all_fields( $post_id )
 * 
 *  @param	$post_id (str) (optional) A post_id to retrieve fields
 *  @return	(array) An array of any fields it could find for current object
 * 
 *  @since      3.2.4
 */
function acft_get_all_fields( $post_id = false ) {
    
    // try to get a valid post_id, starting with optional supplied post_id
    $post_id = acft_get_valid_post_id( $post_id );   
            
    // fields for options
    $all_option_fields = get_fields( 'option', false ) ?: array();
            
    // if no post_id, there's nothing to grab values for, so just return option fields array
    if ( $post_id === false ) { return $all_option_fields; }
    
    
    /* since we have a post_id, so let's grab post and block fields for it */
    
    // fields for posts
    $all_post_fields = get_fields( $post_id, false ) ?: array();
    
    // get post object for this valid post_id
    $post_obj = get_post( $post_id ); 

    // fields for Gutenberg Blocks
    $blocks = parse_blocks( $post_obj->post_content );
    foreach ( $blocks as $block ) {
        
        if ( strpos( $block['blockName'], 'acf/' ) === 0 ) { // a custom block made with ACF
            
            $all_post_fields[] = $block['attrs']['data'];
            
        }
        
    }

    // merge post/block fields array with options array and return
    return array_merge_recursive( $all_post_fields, $all_option_fields );

}


/**
 *  Merge all supplied fields for an object (post, block, options)
 * 
 *  Returns an array of the font_family array and font_weight array
 *  used for enqueuing font styles, and displaying with the (future) shortcode
 * 
 *  acft_merge_all_fields( $all_fields )
 * 
 *  @param	$all_fields (array) The array of collected fields for an object
 *  @return	(array) An array of arrays - font family, font weight
 * 
 *  @since      3.2.4
 */
function acft_merge_all_fields( $all_fields = false ) {
    
    $mergedArray = array();
    $mergedArray['font_family'] = $mergedArray['font_weight'] = array();

    if ( is_array( $all_fields ) ) {

        array_walk_recursive( $all_fields, function( $item, $key ) use ( &$mergedArray ) {
            if( $key === 'font_family' ) {
                    if ( !in_array( $item, $mergedArray['font_family'] ) ) {
                        $mergedArray['font_family'][] = $item;
                    }
            } elseif( $key === 'font_weight' ) {
                    if ( !in_array( $item, $mergedArray['font_weight'] ) ) {
                        $mergedArray['font_weight'][] = $item;
                    }
            }
        });

    }
    
    return $mergedArray;
}



/**
 *  Enqueue Google Fonts file
 * 
 *  acft_enqueue_google_fonts_file()
 * 
 *  @since		3.0.0
 */
add_action( 'wp_enqueue_scripts', 'acft_enqueue_google_fonts_file' );
function acft_enqueue_google_fonts_file() {
   
    // get all fields for current object
    $all_fields = acft_get_all_fields();
    
    // merge all fields for current object
    $merged_fields = acft_merge_all_fields( $all_fields );
    
    $font_family = $merged_fields['font_family'];
    $font_weight = $merged_fields['font_weight'];
    
    // if there's actually a font_family present, then we enqueue the stylesheet
    if( is_array( $font_family ) && count( $font_family ) > 0 ){

        if( is_array( $font_weight ) && count( $font_weight ) > 0 ){
            $font_weight = implode( ',', $font_weight );
            $font_family = implode( ':'.$font_weight.'|', $font_family );
        }else{
            $font_family = implode( ':400,700|', $font_family );
        }
        
        /**
         * wp_enqueue_style automatically replaces ' ' with '+', but some themes like (twentytwentytwo) include these 
         * enqueued files through another method that does properly encode the URL string. We just go ahead and do it
         * now to prevent 404 errors when a font_family name has a space in the name
         */ 
        wp_enqueue_style( 'acft-gf', 'https://fonts.googleapis.com/css?family='.str_replace(' ', '+', $font_family) );


    }

}
