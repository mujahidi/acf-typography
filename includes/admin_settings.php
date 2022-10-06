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
		__( '', 'acf-typography' ), 
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
	if( $acft_options && $acft_options['google_key'] )
		$google_key = $acft_options['google_key'];
	?>
	<input type='text' name='acft_settings[google_key]' value='<?php echo $google_key; ?>'>
        <p><?php 
	/* translators: 1: google fonts api url, 2: target="_blank" to open in new tab */
	echo sprintf( 
            __( 'You can get an API key <a href="%1$s" %2$s title="Google Fonts API">HERE</a>.', 'acf-typography' ), 
            esc_url( 'https://developers.google.com/fonts/docs/developer_api' ),
            'target="_blank"'
        ); ?></p>
	<?php

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
                <label for="remote"><?php _e("Remote", 'acf-typography'); ?></label>
            </div>

            <div>
                <input type="radio" id="acftSourceLocal" name="acft_settings[files_source]" value="local"
                <?php echo (($files_source == 'local') ? "checked" : "" )?>>
                <label for="local"><?php _e("Local", 'acf-typography'); ?></label>
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
		settings_fields( 'acf-typography-field' );
		do_settings_sections( 'acf-typography-field' );
		submit_button();
		?>

	</form>
	<?php

}
