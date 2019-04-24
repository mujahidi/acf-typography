<?php

/**
 *  Update Google Fonts JSON file
 * 
 *  update_gf_json_file()
 * 
 *  @since		3.0.0
 */
function update_gf_json_file( $API_KEY ) {

    $dir = plugin_dir_path( dirname(__FILE__) );
    $filename = $dir . 'google_fonts.json';

    if ( file_exists( $filename ) ) {
        
        $file_date = date ( "Ymd", filemtime( $filename ) );
        $now = date ( "Ymd", time() );
        $time = $now - $file_date;
        
        if ($time > 2) {

            $json = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=' . $API_KEY);

            $gf_file = fopen($filename, 'wb');
            fwrite($gf_file, $json);
            fclose( $gf_file );

        }

    }

}

/**
 *  Get google fonts for Font-Family drop-down subfield
 * 
 *  get_google_font_family()
 * 
 *  @since		3.0.0
 */
function get_google_font_family(){

    if ( !defined('YOUR_API_KEY') ) return;

    update_gf_json_file( YOUR_API_KEY );

    // Load json file for extra seting
    $dir = plugin_dir_path( dirname(__FILE__) );
    $json = file_get_contents("{$dir}google_fonts.json");
    $fontArray = json_decode( $json);
    $font_family = array();

    if( $fontArray ){
        foreach ( $fontArray as $k => $v ) {
            if (is_array($v)){
                foreach ($v as $value){
                    foreach ($value as $key1 => $value1) {
                        if($key1== "family"){
                            $font_family[ $value1 ] = $value1;
                        }		
                    }
                }
            }
        }
    }

    return $font_family;

}