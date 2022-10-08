<?php

/**
 *  Admin settings page for Google Fonts API
 * 
 *  @since		3.0.0
 */

add_action( 'admin_menu', 'acft_add_admin_menu' );
add_action( 'admin_init', 'acft_settings_init' );


function acft_add_admin_menu(){ 

	add_submenu_page( 'options-general.php', 'ACF Typography Settings', 'ACF Typography Settings', 'manage_options', 'acf-typography-field', 'acft_options_page' );

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
        
        add_settings_field( 
		'acft_radio_field_1', 
		__( 'Font Files Source', 'acf-typography' ), 
		'acft_files_source_field', 
		'acf-typography-field', 
		'acft_acf-typography-field_section' 
	);


}

/**
 *  Google Fonts API Key field
 * 
 *  acft_google_key_field()
 * 
 *  @since		3.0.0
 */
function acft_google_key_field(){ 

	$acft_options = get_option( 'acft_settings' );
	$google_key = '';
	if( $acft_options && $acft_options['google_key'] ) {
		$google_key = $acft_options['google_key'];
                
        }
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
        
        if (!empty($google_key)) {
            
            // if google key present, test it out and suppress errors for now
            $url = esc_url('https://www.googleapis.com/webfonts/v1/webfonts?key=' . $google_key);
            $url_test = @file_get_contents($url);
            // if we got back a bad response, display a message
            if ($url_test == false) {?>
                <div id="setting-error-settings_updated" class="notice notice-error settings-error is-dismissible">
                    
                    <h3><strong>
                        <?php _e("Bad Response from Google API.", 'acf-typography') ?>
                    </strong></h3>
                    
                    <?php if (isset($http_response_header[0])) { ?>
                            <?php echo 
                                /* translators: Used for displaying HTTP Header Response Error Messages */ 
                                __("HTTP Response Error: ", 'acf-typography').'<strong>'.$http_response_header[0].'</strong><br />'; 
                            ?>
                    <?php } ?>
                        
                    <?php 
                    $context = stream_context_create(['http' => ['ignore_errors' => true]]);
                    $url_response = json_decode(file_get_contents($url, false, $context), true);
                    if (isset($url_response['error']['message'])) { ?>
                            <?php echo 
                                /* translators: Used for displaying Google API Error Messages */ 
                                __("Google Error: ", 'acf-typography').'<strong>'.$url_response['error']['message'].'</strong><br />'; 
                            ?>
                    <?php } ?>
                      
                    <br />
                    <p><?php echo __("Check your API Key to make sure it is correct, check your settings in the Developer API to ensure that you have allowed this API key to be used without restrictions for this domain, and check the Google API Fonts link below to ensure you're actually getting back results.", 'acf-typography'); ?></p>
                    <br />
                    
                    <?php echo __("Google Developer API: ", 'acf-typography').'<a href="'.$g_api_url.'" target="_blank">'.$g_api_url.'</a>'; ?>
                    <br />
                    <?php echo __("Google API Fonts: ", 'acf-typography').'<a href="'.$url.'" target="_blank">'.$url.'</a>'; ?>
                    
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">
                        <?php 
                            /* translators: Used for screen-reader-text to dismiss admin panel error messages. */ 
                            _e("Dismiss this notice.", 'acf-typography'); 
                        ?>

                    </span></button>
                </div>

            <?php }
            
        }

}


/**
 *  Font Files Source field
 * 
 *  acft_files_source_field()
 * 
 *  @since		3.3.0
 */
function acft_files_source_field(){ 

	$acft_options = get_option( 'acft_settings' );
	$files_source = 'remote';
	if( $acft_options && isset($acft_options['files_source']) )
		$files_source = $acft_options['files_source'];
	?>
        <fieldset>
            <legend><strong><?php _e('Serve Font Files Remotely, or Locally?', 'acf-typography'); ?></strong></legend>
            <p><ul>
                <li><?php _e('Remote fonts are ideal for keeping the size of your site small, and minimizing server resources.', 'acf-typography') ?></li>
                <li><?php _e('Local fonts are ideal for GDPR compliance, and limiting 3rd-party resources on page loads.', 'acf-typography') ?></li>
            </ul></p>
            <div>
                <input type="radio" id="acftSourceRemote" name="acft_settings[files_source]" value="remote" 
                <?php echo (($files_source == 'remote') ? "checked" : "" )?>>
                <label for="remote"><?php 
                    /* translators: Adjective, Context: Remote Fonts - serving font files remotely  */  
                    _e("Remote", 'acf-typography'); 
                ?></label>
            </div>

            <div>
                <input type="radio" id="acftSourceLocal" name="acft_settings[files_source]" value="local"
                <?php echo (($files_source == 'local') ? "checked" : "" )?>>
                <label for="local"><?php
                    /* translators: Adjective, Context: Local Fonts - serving font files locally  */ 
                    _e("Local", 'acf-typography'); 
                ?></label>
            </div>

        </fieldset> 
	<?php

}


function acft_settings_section_callback(){}


function acft_options_page(){ 

	?>
	<form action='options.php' method='post'>

		<h2><?php _e("ACF Typography Settings", 'acf-typography'); ?></h2>

		<?php
                if (!class_exists('ACF')) { ?>
                 
                    <div id="setting-error-settings_updated" class="notice notice-error settings-error is-dismissible">
                        <h3><strong>
                            <?php _e("Advanced Custom Fields plugin is required!", 'acf-typography') ?>
                        </strong></h3>
                        <p><?php echo sprintf( 
                            /* translators: 1: ACF WP plugin directory url, 2: target="_blank" to open in new tab */    
                            __('This plugin does nothing by itself, and requires <a href="%1$s" %2$s title="Advanced Custom Fields">Advanced Custom Fields</a> (ACF) to be installed and activated. ACF Typography Field will work with both free or paid PRO versions of ACF, versions 4, 5 & 6!', 'acf-typography'),
                            esc_url('https://wordpress.org/plugins/advanced-custom-fields/'),
                            'target="_blank"'
                        ); ?></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">
                            <?php 
                                /* translators: Used for screen-reader-text to dismiss admin panel error messages. */ 
                                _e("Dismiss this notice.", 'acf-typography'); 
                            ?>
                        </span></button>
                    </div>
                    
                <?php }
		settings_fields( 'acf-typography-field' );
		do_settings_sections( 'acf-typography-field' );
		submit_button();
		?>

	</form>
	<?php

}
