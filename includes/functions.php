<?php

/**
 *  Create directory if not exists
 * 
 *  Helper function for saving stylesheets and font files locally.
 *  If needed, it creates the directory and sets permissions.
 * 
 *  acft_create_dir_if_not_exists( $path, $index=false ) 
 * 
 *  @param	$path (str) The dir path to check/create
 *  @return	n/a
 * 
 *  @since	3.3.0
 */
function acft_create_dir_if_not_exists( $path ) {
    if ( !is_dir($path) )
        mkdir( $path, 0755, true );
    return;
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
        
        if ( !filesize($filename) || $time > 2 ) {

            // suppress errors for now
            $json = @file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=' . $API_KEY);
            
            // if we didn't get an error, save json data to file
            if ($json != false) {
                    $gf_file = fopen($filename, 'wb');
                    fwrite($gf_file, $json);
                    fclose( $gf_file );
            }

        }

    }

}


/**
 *  Get google fonts for Font-Family drop-down subfield
 * 
 *  acft_get_google_font_family()
 *  
 *  @since		3.0.0
 */
function acft_get_google_font_family(){

    if ( !defined('YOUR_API_KEY') ) return;

    acft_update_gf_json_file( YOUR_API_KEY );

    // Load json file for extra seting
    $dir = plugin_dir_path( dirname(__FILE__) );
    $json = file_get_contents( "{$dir}google_fonts.json" );
    $fontArray = json_decode( $json );
    $font_family = array();

    if( $fontArray ){
        foreach ( $fontArray as $k => $v ) {
            if (is_array($v)){
                foreach ($v as $value){
                    foreach ($value as $key1 => $value1) {
                        if($key1 == "family"){
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
 *  Get Data Array for a font family from Google fonts json file
 * 
 *  Data for font family is used to save remote files locally.
 * 
 *  acft_get_font_stylesheet_data($font_family)
 * 
 *  @param	$font_family (str) The font family name to retrieve
 *  @return	$fontArr (array) An array containing version, variants, 
 *              and files for the current font family
 * 
 *  @since	3.3.0
 */
function acft_get_font_stylesheet_data( $font_family ) {
    
    // get google fonts json file values and build out array
    $dir = plugin_dir_path( dirname(__FILE__) );

    $json = file_get_contents( "{$dir}google_fonts.json" );
    $jsonArr = json_decode( $json, true );

    $fontArr = array();
    
    // get latest values for this font family from the saved json file
    if( $jsonArr ){
        foreach ( $jsonArr['items'] as $gf_item ) {
            if ( $gf_item['family'] == $font_family ) {
                $fontArr[$font_family]['version'] = $gf_item['version'];
                $fontArr[$font_family]['variants'] = $gf_item['variants'];
                $fontArr[$font_family]['files'] = $gf_item['files'];
                break;
            }
        }
    }
    
    return $fontArr;
    
}


/**
 *  Prepare stylesheet URL string for Google fonts
 * 
 *  acft_save_local_font_stylesheet($font_family)
 * 
 *  @param	$font_family (str) The font family name to save
 *  @param      $google_dataArr (arr) Array of font_family data
 *              including version, variants, and font files
 *  @return	$url (str) The full url string to querey Google with
 * 
 *  @since	3.3.0
 */
function acft_prepare_stylesheet_url_string( $font_family, $google_dataArr ) {
    
    if ( !defined('FONT_FILE_SOURCE') ) return;
    if ( FONT_FILE_SOURCE != 'local' ) return;
    
    $ff_variants = $google_dataArr[$font_family]['variants'];
    $ff_files = $google_dataArr[$font_family]['files'];
    
    
    // breakdown variants to get query string for google file saving
    // eg- :ital,wght@0,400;0,700;1,400;1,700
    // loop through file variants and add relevant values to an array
    foreach( $ff_files as $k => $v ) {
        // regular default weight
        // only add leading "0" if italic is also availaible for this font_family variants
        if ( $k == 'regular' ) { $fwArr[] = in_array('italic', $ff_variants) ? '0,400;' : '400;'; } 
        // italic default weight
        if ( $k == 'italic' ) { $fwArr[] = '1,400;'; }
        // other weight
        // only add leading "0" if italic is also availaible for this font_family variants
        if ( is_int($k) ) { $fwArr[] = in_array('italic', $ff_variants) ? '0,'.$k.';' : $k.';'; }
        // other italic weight
        $w_i_test = explode('italic', $k);
        if ( count($w_i_test)>1 && !empty($w_i_test[0]) ) { $fwArr[] = '1,'.str_replace('italic', '', $k).';'; }
    }
    
    // sort array to follow google order standards, make sure we have more than one array item, convert to string, and append to URL query string
    sort($fwArr);
    $gf_q_string = ''; // empty string to add to URL, or query vars below
    if ( count($fwArr) > 1 ) {
        $gf_q_string .= ':'.(in_array('italic', $ff_variants) ? 'ital,' : '').'wght@';
        $gf_q_string .= implode('', $fwArr);
        // strip last ';' to prevent 404 errors
        $gf_q_string = substr($gf_q_string, 0, -1);
    }
    
    // create the URL string for the full font family and all variants
    
    $url = 'https://fonts.googleapis.com/css2?family='.str_replace(' ', '+', $font_family).$gf_q_string;
    
    return $url;
}


/**
 *  Save Google font file to local system
 * 
 *  acft_save_local_font_stylesheet($font_family)
 * 
 *  @param	$font_family (str) The font family name to save
 *  @param	$font_file (str) The url to the font_file to save
 *  @param      $google_dataArr (arr) Array of font_family data
 *              including version, variants, and font files
 *  @return	n/a
 * 
 *  @since	3.3.0
 */
function acft_save_local_font_file( $font_family, $font_file, $google_dataArr ) {
    
    if ( !defined('FONT_FILE_SOURCE') ) return;
    if ( FONT_FILE_SOURCE != 'local' ) return;
    
    $dir = plugin_dir_path( dirname(__FILE__) );
    $font_dir = trailingslashit($dir.trailingslashit('assets').'fonts');
    acft_create_dir_if_not_exists( $font_dir );
     
    $ff_ver = $google_dataArr[$font_family]['version'];
    
    // set path to mimic google fonts URL structure
    // eg- /fontfamily/version/filename
    $ff_path = $font_dir.trailingslashit(strtolower(str_replace(' ', '', $font_family)));
    acft_create_dir_if_not_exists( $ff_path );

    $ffv_path = $ff_path.trailingslashit(strtolower($ff_ver));
    acft_create_dir_if_not_exists( $ffv_path) ;

    $ff_loc_fn = basename( $font_file );

    // save font file locally, get file first
    // suppress errors for now
    $ff_content = @file_get_contents( $font_file );

    // if we didn't get an error, actually save font file locally
    if ( $ff_content != false ) {
            file_put_contents( $ffv_path.$ff_loc_fn, $ff_content );
    }
    
    return;
    
}


/**
 *  Prepare stylesheet replacement lines for local font files
 * 
 *  acft_save_local_font_stylesheet($font_family)
 * 
 *  @param	$font_family (str) The font family name to save
 *  @param	$font_stylesheet (str) The local path/filename to the 
 *              font stylesheet that needs to be revised
 *  @param      $google_dataArr (arr) Array of font_family data
 *              including version, variants, and font files
 *  @return	$matchArr (arr) Array of line numbers and the best matching
 *              new line to replace that line number with 
 * 
 *  @since	3.3.0
 */
function acft_prepare_stylesheet_replacement_lines( $font_family, $font_stylesheet, $google_dataArr ) {
    
    if ( !defined('FONT_FILE_SOURCE') ) return;
    if ( FONT_FILE_SOURCE != 'local' ) return;
    
    /* 
     * We have to upate the local stylesheet with the new local font files.
     * For consistency, we want the files saved and linked in stylesheets
     * to match what's in the google_fonts.json file.
     * 
     * Google sucks at consistent filenames for font files.
     * They can be as short as 8 chars, or over 36 chars.
     * what gets reterned in the stylesheet does not match what gets
     * returned in the json file.
     * 
     * It's about the worst-case scenario I've seen trying to do any
     * kind of find/replace.
     * 
     * After trying multiple different ways to get consistenet/accurate
     * filename/path replacements, I've settled on building out an array
     * that counts the # of chars in common between the json file data and
     * what Google returns in the stylesheet, saving the filename that best 
     * matches to the line it best matches.
     *
     * Then we loop through the file and update those specific lines with
     * the specific highest matching filename.
     * 
     */
        
    $ff_ver = $google_dataArr[$font_family]['version'];
    $ff_files = $google_dataArr[$font_family]['files'];

    $font_css_rel_url = trailingslashit(trailingslashit('..').'fonts');

    
    // opening stylesheet now to prevent repeat file opens unnecessarily
    $lines = file( $font_stylesheet );
    
    // loop through every file for this font family, save locally, and replace remote URL with local URL
    foreach ( $ff_files as $k => $v ) {  
        
        // setup some initial vals
        $v_loc_fn = basename( $v );
        
        // set url for new font file
        // mimic Google URL path
        // ../fonts/fontfamily/ver/filename
        $ff_base_url = $font_css_rel_url.trailingslashit(strtolower(str_replace(' ', '', $font_family))).trailingslashit($ff_ver);
        $v_url = substr(esc_url($ff_base_url.$v_loc_fn), 7); // strip off the leading http:// WP adds by default
        
        // get the extension and format string for the supplied font
        // Google changes the format returned based on the server making the request
        // We match what Google returns for this particular server
        switch ( pathinfo($v_loc_fn, PATHINFO_EXTENSION) ) {
            case 'woff':
                $format_ext = 'woff';
                break;
            case 'woff2':
                $format_ext = 'woff2';
                break;
            case 'svg':
                $format_ext = 'svg';
                break;
            default:
                $format_ext = 'truetype';
        }
        
        // prepare new replacement line
        $new_line = "  src: url('".$v_url."') format('".$format_ext."');\n";
        
        
        /* Part 1 of updating local filenames in the local stylesheet */
        
        $count = 0;
        foreach( $lines as $line ) {
            $count++;
            // does the line start with a remote URL?
            if ( strpos(str_replace(' ', '', $line), 'src:url(http') === 0 ) {
                $str_len = strlen($v_loc_fn); // str length of local filename taken from json file
                $match = false; // flag
                while ( !$match ) {
                    // see if filename is in current line
                    if ( stristr($line, substr($v_loc_fn, 0, $str_len)) ) {
                        
                        // we got a match
                        $match = true; //set flag
                        
                        // no match for this line yet, add it
                        if ( !isset($matchArr[$count]) ) {
                            $matchArr[$count] = array($str_len => $new_line);
                        }
                        
                        // got a match for this line already, let's see what's the better match
                        if ( isset($matchArr[$count]) ) {
                            foreach( $matchArr[$count] as $k => $v ) {
                                // compare # of chars. higher char # wins
                                if ( $k < $str_len ) {
                                    $matchArr[$count] = array($str_len => $new_line);
                                }
                            }
                        }
                        
                        break; // break out for good measure
                        
                    }
                    
                    // no match that loop
                    $str_len--; // decrement str_length by 1 and try again
     
                } // end while loop   
                
            } // end if line starts with remote URL link

        } // end foreach line
    
    } // end foreach font file
    
    return $matchArr;
}


/**
 *  Update the src links in stylesheet with local font files
 * 
 *  acft_update_src_links_in_stylesheet($font_stylesheet, $matchArr)
 * 
 *  @param	$font_stylesheet (str) File path/name of the local stylesheet
 *  @param	$matchArr (arr) The array of line matches and new lines to use
 *  @return	n/a
 * 
 *  @since	3.3.0
 */
function acft_update_src_links_in_stylesheet( $font_stylesheet, $matchArr ) {
    
    if ( !defined('FONT_FILE_SOURCE') ) return;
    if ( FONT_FILE_SOURCE != 'local' ) return;
    
    /* Part 2 of updating local filenames in the local stylesheet */
    
    $count = 0;
    // replace remote URL with best matching local file and save file
    file_put_contents( $font_stylesheet, implode('', 
        array_map( function($line) use ($matchArr, &$count) {
            // increase count (line number) for each array item (line)
            $count++;

            // we have a match for this line
            if ( isset($matchArr[$count]) ) {
                foreach( $matchArr[$count] as $k => $v ) {
                    $line = $v; // modify this particular line with best match
                }
            }
            return $line; // return line whether modified or unmodified

        }, file( $font_stylesheet ) )
    ));
        
    return;
}


/**
 *  Save Google font stylesheet and font files to local system
 * 
 *  acft_save_local_font_stylesheet($font_family)
 * 
 *  @param	$font_family (str) The font family name to save
 *  @param      $google_dataArr (arr) Array of font_family data
 *              including version, variants, and font files
 *  @return	n/a
 * 
 *  @since	3.3.0
 */
function acft_save_local_font_stylesheet( $font_family, $google_dataArr ) {
    
    if ( !defined('FONT_FILE_SOURCE') ) return;
    if ( FONT_FILE_SOURCE != 'local' ) return;
     
    $ff_ver = $google_dataArr[$font_family]['version'];
    $ff_files = $google_dataArr[$font_family]['files'];

    $dir = plugin_dir_path( dirname(__FILE__) );
    
    $stylesheet_dir = trailingslashit($dir.trailingslashit('assets').'font-styles');
    acft_create_dir_if_not_exists($stylesheet_dir);

    
    $ss_fn = esc_html(strtolower(str_replace(' ', '', $font_family).'-'.$ff_ver).'.css');
    $font_stylesheet = $stylesheet_dir.$ss_fn;    

    
    $url = acft_prepare_stylesheet_url_string( $font_family, $google_dataArr );

    
    // save google stylesheet file locally
    // suppress errors for now
    $url_content = @file_get_contents( $url );
    
    // if we got an error or stylesheet doesn't start with @font, then return. nothing to process for now
    if ( $url_content === false || !strncmp($url_content, "@font", 5) === 0 ) {
            return;
    }

    file_put_contents($font_stylesheet, $url_content);
    
    
    // since we just had to save the stylesheet, we already know we need the font files too
    // loop through every file for this font family, save locally
    foreach ( $ff_files as $k => $v ) {     
        
        // actually save the local font file
        acft_save_local_font_file( $font_family, $v, $google_dataArr );

    }
    
    // build array of replacement lines pointing to local files 
    $matchArr = acft_prepare_stylesheet_replacement_lines( $font_family, $font_stylesheet, $google_dataArr );
    
    // actually apply the new lines for local font files to the local stylesheet
    acft_update_src_links_in_stylesheet( $font_stylesheet, $matchArr );
    
    return;
    
}


/**
 *  Get Local font stylesheet path/filename
 * 
 *  If there is no local stylesheet found, it triggers multiple
 *  functions to get/save the stylesheet and all associated 
 *  font files for the stylesheet locally
 * 
 *  acft_get_local_font_stylesheet($font_family)
 * 
 *  @param	$font_family (str) The font family name to retrieve
 *  @return	$ss_url (str) URL string of the local stylesheet file
 *              for the supplied font_family
 * 
 *  @since      3.3.0
 */
function acft_get_local_font_stylesheet($font_family) {
    
    if ( !defined('FONT_FILE_SOURCE') ) return;
    if ( FONT_FILE_SOURCE != 'local' ) return;
    
    acft_update_gf_json_file( YOUR_API_KEY );
    
    $google_dataArr = acft_get_font_stylesheet_data($font_family);  
    $ff_ver = $google_dataArr[$font_family]['version'];
    
    $dir = plugin_dir_path( dirname(__FILE__) );
    $stylesheet_dir = trailingslashit($dir.trailingslashit('assets').'font-styles');
    
    $ss_fn = strtolower(str_replace(' ', '', $font_family).'-'.$ff_ver).'.css';
    $font_stylesheet = $stylesheet_dir.$ss_fn;
    
    // if the stylesheet does not exist locally, let's add it
    if ( !file_exists($font_stylesheet) ) {
        acft_save_local_font_stylesheet( $font_family, $google_dataArr );
    }
    
    // return full url to local font stylesheet
    $ss_base_url = plugin_dir_url($font_stylesheet);
    return $ss_base_url.$ss_fn;

}


/**
 *  Get a valid post_id for the current object
 * 
 *  acft_get_valid_post_id( $post_id )
 * 
 *  @param	$post_id (str) (optional) A post_id to retrieve fields
 *  @return	(str) post_id when valid post_id found, or (bool) false
 *              when no valid post_id is found for the object 
 * 
 *  @since      3.3.0
 */
function acft_get_valid_post_id( $post_id = false ) {
    
    // supplied a post_id, use it
    if ( $post_id ) {
        $post_obj = get_post(intval($post_id));
        if ( is_object($post_obj) && !is_wp_error($post_obj) ) {
            $post_id = $post_obj->ID;
    }}
    
    // if no post_id, try getting post_id if called inside the loop
    if ( $post_id === false ) {
        global $post;
        if ( is_object($post) ) {
            $post_id = $post->ID;
    }}

    // if still no post_id, try getting post_id if called outside the loop
    if ( $post_id === false ) {
        global $wp_query; 
        if ( is_object($wp_query->post) ) {
            $post_id = $wp_query->post->ID;
    }}
    
    return $post_id;
}


/**
 *  Get all fields for an object (post, block, option)
 * 
 *  acft_get_all_fields( $post_id )
 * 
 *  @param	$post_id (str) (optional) A post_id to retrieve fields
 *  @return	(array) An array of any fields it could find for current object
 * 
 *  @since      3.3.0
 */
function acft_get_all_fields( $post_id = false ) {
    
    // get actual valid post_id, starting with optional supplied post_id
    $post_id = acft_get_valid_post_id( $post_id );   
            
    // fields for options
    $all_option_fields = get_fields( 'option', false ) ?: array();
            
    // if still no post_id, there's nothing to grab values for, so just return option fields array
    if ( $post_id === false ) { return $all_option_fields; }
    
    
    // we have a post_id, so let's grab post and block fields for it
    
    // fields for posts
    $all_post_fields = get_fields( $post_id, false ) ?: array();
    
    // get post object for this valid post_id
    $post_obj = get_post($post_id); 

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
 *  Merge all supplied fields for an object (post, block, options page)
 * 
 *  Returns an array of the font_family array and font_weight array
 *  used for enqueuing font styles, and displaying with the shortcode
 * 
 *  acft_merge_all_fields( $all_fields )
 * 
 *  @param	$all_fields (array) The array of collected fields for an object
 *  @return	(array) An array of arrays - font family, font weight
 * 
 *  @since      3.3.0
 */
function acft_merge_all_fields( $all_fields = false ) {
    
    $mergedArray = array();
    $mergedArray['font_family'] = $mergedArray['font_weight'] = array();

    if ( is_array($all_fields) ) {

        array_walk_recursive($all_fields, function($item, $key) use (&$mergedArray) {
            if( $key === 'font_family' ) {
                    if ( !in_array($item, $mergedArray['font_family']) ) {
                        $mergedArray['font_family'][] = $item;
                    }
            } elseif( $key === 'font_weight' ) {
                    if ( !in_array($item, $mergedArray['font_weight']) ) {
                        $mergedArray['font_weight'][] = $item;
                    }
            }
        });

    }
    
    return $mergedArray;
}


/**
 *  Get the HTML code for stylesheets used by an object.
 * 
 *  Used by the stylesheet shortcode to return tag code
 *  as a string instead of actually enqueueing it.
 * 
 *  acft_the_typography_stylesheet( $link_type, $post_id )
 * 
 *  @param	$link_type (str) (optional) (default: 'link')
 *              Can be "link" or "style"
 *              The type of stylesheet linking method
 *  @param	$post_id (str) (optional) (default: current_post_id)
 *              A specific $post_id to retrieve the stylesheet(s) for 
 *
 *  @return	$stylesheet (str) 
 *              Returns standard link for link_type = 'link'
 *              // <link rel="stylesheet" ... />
 *              Returns style for link_type = 'style'
 *              // <style> ... </style>
 * 
 *  @since      3.3.0
 */
function acft_the_typography_stylesheet( $link_type = 'link', $post_id = false ) {
    
    // get all fields for current object
    $all_fields = acft_get_all_fields( $post_id );
    
    // merge all fields for current object
    $merged_fields = acft_merge_all_fields( $all_fields );
    
    $font_family = $merged_fields['font_family'];
    $font_weight = $merged_fields['font_weight'];
    
    
    // set results as an empty string. We'll build it out if there are values
    // or we'll return the empty string if there are no values
    $results = '';
    
    if( is_array($font_family) && count($font_family) > 0 ){
        
        
        // handle local stylesheets
        if ( defined('FONT_FILE_SOURCE') ) {
            if ( FONT_FILE_SOURCE == 'local' ) {
            
                foreach ( $font_family as $ff ) {

                    $font_stylesheet = acft_get_local_font_stylesheet( $ff );

                    $ff_slug = strtolower(str_replace(' ', '', $ff));

                    if ($link_type === 'style') {
                        $results .= '<style id="acft-gf-local-'.$ff_slug.'-css">';
                        $results .= file_get_contents( $font_stylesheet );
                        $results .= '</style>';

                    } else {
                        $results .= '<link rel="stylesheet" id="acft-gf-local-'.$ff_slug.'-css"  href="'.$font_stylesheet.'?ver='.get_bloginfo( 'version' ).'" media="all" />';
                    }

                }
                
                return $results;
            
            }// end if FONT_FILE_SOURCE is local check
            
        } // end if for local / remote check
  
            
        
        // handle remote stylesheets  
        if( is_array($font_weight) && count($font_weight) > 0 ){
            $font_weight = implode( ',', $font_weight );
            $font_family = implode( ':'.$font_weight.'|', $font_family );
        }else{
            $font_family = implode( ':400,700|', $font_family );
        }

        // url to query
        $url = 'https://fonts.googleapis.com/css?family='.str_replace(' ', '+', $font_family);

        // suppress errors for now
        $url_content = @file_get_contents( $url );

        // if we didn't get an error, use the url or url_content for in-line display
        if ( $url_content != false ) {

            // style code link_type
            if ( $link_type === 'style' ) {

                    $results .= '<style>';
                    $results .= $url_content;
                    $results .= '</style>';

            // link code link_type
            } else {

                    $results .= '<link rel="stylesheet" href="'.$url.'" media="all" />';

            }

        } // end if url error check
            
    } // end if for font_family array has values 
    
    return $results;
 
}


/**
 *  Enqueue Google Fonts file or Local Fonts file
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


    // let's do the enqueueing if there's actually something to enqueue
    if( is_array($font_family) && count($font_family) > 0 ){
  
        
        // enqueue locally saved/served fonts
        if ( defined('FONT_FILE_SOURCE') ) {
            if ( FONT_FILE_SOURCE == 'local' ) {
            
                foreach ( $font_family as $ff ) {

                    $font_stylesheet = acft_get_local_font_stylesheet( $ff );

                    $ff_slug = strtolower(str_replace(' ', '', $ff));

                    wp_enqueue_style( 'acft-gf-local-'.$ff_slug, $font_stylesheet );

                }
                
                return;
            
            }// end if FONT_FILE_SOURCE is local check
            
        } // end if for local / remote enqueueig check
  
        
        // enqueue remote google fonts    
        if( is_array($font_weight) && count($font_weight) > 0 ){
            $font_weight = implode( ',', $font_weight );
            $font_family = implode( ':'.$font_weight.'|', $font_family );
        }else{
            $font_family = implode( ':400,700|', $font_family );
        }

        // wp_enqueue_style automatically replaces ' ' with '+', but some themes like (twentytwentytwo) include these 
        // enqueued files through another method that does properly encode the URL string. We just go ahead and do it
        // to prevent 404 errors when a font family name has a space in the name
        wp_enqueue_style( 'acft-gf', 'https://fonts.googleapis.com/css?family='.str_replace(' ', '+', $font_family) );

        
    } // end if for font_family array has values 

}
