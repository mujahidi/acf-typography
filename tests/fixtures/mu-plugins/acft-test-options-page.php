<?php
/**
 * Plugin Name: ACFT test options page (dev only)
 * Description: Registers an ACF Pro options page with a Typography field, for option-field and font enqueue tests. Never ship.
 *
 * Set a value: wp eval 'update_field( "field_acft_opt_typo", array( "font_family" => "Roboto", "font_weight" => "300" ), "option" );'
 *
 * @package ACF_Typography_Field
 */

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return; // ACF free has no options pages.
		}

		acf_add_options_page(
			array(
				'page_title' => 'ACFT Test Options',
				'menu_slug'  => 'acft-test-options',
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_acft_test_options',
				'title'    => 'ACFT Test Options',
				'fields'   => array(
					array(
						'key'                => 'field_acft_opt_typo',
						'label'              => 'Site Typography',
						'name'               => 'site_typo',
						'type'               => 'Typography',
						'display_properties' => array( 'font_family', 'font_weight', 'font_size' ),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'acft-test-options',
						),
					),
				),
			)
		);
	}
);
