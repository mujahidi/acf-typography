<?php

/**
 *  Admin settings page for Google Fonts API
 * 
 *  @since		3.0.0
 */

add_action( 'admin_menu', 'acft_add_admin_menu' );
add_action( 'admin_init', 'acft_settings_init' );


function acft_add_admin_menu(){ 

        add_submenu_page( 
                'options-general.php', 
                __('ACF Typography', 'acf-typography' ), // "Settings" removed from menu name to keep it on 1-line
                __('ACF Typography', 'acf-typography' ),  
                'manage_options', 
                'acf-typography-field', 
                'acft_options_page' 
        );

}

function acft_settings_init(){ 

	register_setting( 'acf-typography-field', 'acft_settings' );

	add_settings_section(
		'acft_acf-typography-field_section', 
		'', // as per WP Docs, empty strings should not be translated 
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
 *  Check if ACF installed & active
 * 
 *  acft_acf_active_check()
 * 
 *  @return     (str)
 *                  If error: HTML code for error & help messages
 *                  If no error: n/a
 * 
 *  @since      3.2.4
 */
function acft_acf_active_check() {
    
        // error message if ACF not installed/active
        if (!class_exists('ACF')) { ?>
                <div id="setting-error-settings_acf_required" class="notice notice-error settings-error is-dismissible">
                        <h3><strong>
                                <?php _e("Advanced Custom Fields plugin is required!", 'acf-typography') ?>
                        </strong></h3>
                        <p><?php echo sprintf( 
                                /* translators: 1: ACF WP plugin directory url, 2: 'target="_blank"' HTML code tag, 3: 'title=' HTML code tag */    
                                __('This plugin does nothing by itself, and requires <a href="%1$s" %2$s %3$s"Advanced Custom Fields">Advanced Custom Fields</a> (ACF) to be installed and activated. This ACF Typography Field plugin will work with both free or paid PRO versions of ACF, versions 4, 5 & 6!', 'acf-typography'),
                                esc_url('https://wordpress.org/plugins/advanced-custom-fields/'),
                                'target="_blank"',
                                'title=',
                        ); ?></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">
                                <?php 
                                        /* translators: Used for screen-reader-text to dismiss admin panel error messages. */ 
                                        _e("Dismiss this notice.", 'acf-typography'); 
                                ?>
                        </span></button>
                </div>
        <?php }
        
}


/**
 *  Check Google API Key for validity & good response
 * 
 *  acft_google_api_key_check( $google_key )
 * 
 *  @param      (str) $google_key
 *                  The supplied API Key to test
 *  @param      (str) $g_api_url 
 *                  URL to the Google Developer API
 * 
 *  @return     (str)
 *                  If error: HTML code for error & help messages
 *                  If no error: n/a
 * 
 *  @since      3.2.4
 */
function acft_google_api_key_check( $google_key,  $g_api_url  ) {
            
        // API Key URL to test
        $api_key_url = esc_url( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $google_key );

        // it out and suppress errors for now
        $url_test = @file_get_contents( $api_key_url );

        // if we got back a bad response, display a message
        if ( $url_test === false ) { ?>
            <div id="setting-error-settings_bad_api_response" class="notice notice-error settings-error is-dismissible">
                <h3>
                    <?php _e("Bad Response from Google API.", 'acf-typography') ?>
                </h3>
                <?php
                // display HTTP Header Response Error Code if present
                if (isset($http_response_header[0])) {
                        echo 
                        /* translators: Used for displaying HTTP Header Response Error Messages */ 
                        __("HTTP Response Error: ", 'acf-typography').'<strong>'.$http_response_header[0].'</strong><br>'; 
                } 
                // display Google API Error Message if present
                $context = stream_context_create(['http' => ['ignore_errors' => true]]);
                $url_response = json_decode(file_get_contents($api_key_url, false, $context), true);
                if (isset($url_response['error']['message'])) { 
                        echo 
                        /* translators: Used for displaying Google API Error Messages */ 
                        __("Google Error: ", 'acf-typography').'<strong>'.$url_response['error']['message'].'</strong><br>'; 
                } ?>
                <p><ul><?php 
                        // error help messages
                        echo '<li>'.__("Check your API Key to make sure it is correct.", "acf-typography").'</li>';
                        echo '<li>'.__("Check your settings in the Developer API to ensure that you have allowed this API key to be used without restrictions for this domain.", "acf-typography").'</li>';
                        echo '<li>'.__("Check the API Key Response link below to ensure you're actually getting back results.", "acf-typography").'</li>';
                ?></ul></p>
                <p><?php 
                        // error help links
                        echo
                        /* translators: Used for Google Developer API admin panel error message links. */
                        __("Developer API: ", 'acf-typography').'<a href="'.$g_api_url.'" target="_blank">'.$g_api_url.'</a>'; ?>
                        <br>
                        <?php 
                        echo 
                        /* translators: Used for Google Developer API admin panel error message links. */
                        __("API Key Response: ", 'acf-typography').'<a href="'.$api_key_url.'" target="_blank">'.$api_key_url.'</a>'; 
                ?></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">
                    <?php 
                        /* translators: Used for screen-reader-text to dismiss admin panel error messages. */ 
                        _e("Dismiss this notice.", 'acf-typography'); 
                    ?>

                </span></button>
            </div>
        <?php } // end if bad response
            
}


function acft_google_key_field(){ 

	$acft_options = get_option( 'acft_settings' );
	$google_key = '';
	if( $acft_options && $acft_options['google_key'] )
		$google_key = $acft_options['google_key']
	?>
	<input type='text' name='acft_settings[google_key]' value='<?php echo $google_key; ?>'>    
        <?php $g_api_url = esc_url( 'https://developers.google.com/fonts/docs/developer_api' ); ?>
        <p><?php echo sprintf( 
            /* translators: 1: google fonts api url, 2: target="_blank" to open in new tab */    
            __( 'You can get an API key <a href="%1$s" %2$s title="Google Fonts API">HERE</a>.', 'acf-typography' ), 
            $g_api_url,
            'target="_blank"'
        ); ?></p>
	<?php
        // is there a Google API Key?
        if ( !empty($google_key) ) echo acft_google_api_key_check( $google_key, $g_api_url );
 
}


function acft_settings_section_callback(){}


function acft_options_page(){ 

	?>
	<form action='options.php' method='post'>
		<h2><?php _e("ACF Typography Settings", 'acf-typography'); ?></h2>
		<?php
                        // display ACF must be active message if ACF not installed/active
                        echo acft_acf_active_check();

                        settings_fields( 'acf-typography-field' );
                        do_settings_sections( 'acf-typography-field' );
                        submit_button();
		?>
	</form>
	<?php

}
