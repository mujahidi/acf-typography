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
		__( '', 'acf' ), 
		'acft_settings_section_callback', 
		'acf-typography-field'
	);

	add_settings_field( 
		'acft_text_field_0', 
		__( 'Google Fonts Key', 'acf' ), 
		'acft_google_key_field', 
		'acf-typography-field', 
		'acft_acf-typography-field_section' 
	);


}


function acft_google_key_field(){ 

	$acft_options = get_option( 'acft_settings' );
	$google_key = '';
	if( $acft_options && $acft_options['google_key'] )
		$google_key = $acft_options['google_key']
	?>
	<input type='text' name='acft_settings[google_key]' value='<?php echo $google_key; ?>'>
	<?php

}


function acft_settings_section_callback(){}


function acft_options_page(){ 

	?>
	<form action='options.php' method='post'>

		<h2>ACF Typography Settings</h2>

		<?php
		settings_fields( 'acf-typography-field' );
		do_settings_sections( 'acf-typography-field' );
		submit_button();
		?>

	</form>
	<?php

}