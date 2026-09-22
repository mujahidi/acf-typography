<?php
/**
 * Plugin Name: ACFT test template output (dev only)
 * Description: Adds a repeater with a Typography sub field to pages (ACF Pro), and prints field values through the public template functions when the URL has ?acft_print=1. Never ship.
 *
 * Output: <pre id="acft-print"> appended to the post content, one "label=value" per line.
 *
 * @package ACF_Typography_Field
 */

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return; // Repeater is ACF Pro only.
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_acft_test_repeater',
				'title'    => 'ACFT Test Repeater',
				'fields'   => array(
					array(
						'key'        => 'field_acft_rep',
						'label'      => 'Typography Rows',
						'name'       => 'typo_rows',
						'type'       => 'repeater',
						'sub_fields' => array(
							array(
								'key'                => 'field_acft_rep_typo',
								'label'              => 'Row Typography',
								'name'               => 'row_typo',
								'type'               => 'Typography',
								'display_properties' => array( 'font_family', 'font_weight', 'font_size', 'text_color' ),
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'page',
						),
					),
				),
			)
		);
	}
);

add_filter(
	'the_content',
	function ( $content ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['acft_print'] ) || ! in_the_loop() || ! function_exists( 'the_typography_field' ) ) {
			return $content;
		}

		$props = array( 'font_size', 'font_family', 'font_weight', 'font_style', 'font_variant', 'font_stretch', 'line_height', 'letter_spacing', 'text_align', 'text_color', 'text_decoration', 'text_transform' );

		ob_start();
		foreach ( array( 'heading_typo', '3b_typo' ) as $selector ) {
			foreach ( $props as $prop ) {
				echo "\n" . esc_html( $selector . '.' . $prop ) . '=';
				the_typography_field( $selector, $prop );
			}
		}

		echo "\noption.site_typo.font_family=" . esc_html( get_typography_field( 'site_typo', 'font_family', 'option' ) );

		if ( function_exists( 'have_rows' ) && have_rows( 'typo_rows' ) ) {
			$i = 0;
			while ( have_rows( 'typo_rows' ) ) {
				the_row();
				++$i;
				foreach ( array( 'font_family', 'font_weight', 'font_size', 'text_color' ) as $prop ) {
					echo "\nrow" . (int) $i . '.' . esc_html( $prop ) . '=';
					the_typography_sub_field( 'row_typo', $prop );
				}
				echo "\nrow" . (int) $i . '.get_sub.font_family=' . esc_html( get_typography_sub_field( 'row_typo', 'font_family' ) );
			}
		}

		return $content . '<pre id="acft-print">' . ob_get_clean() . "\n</pre>";
	},
	20
);
