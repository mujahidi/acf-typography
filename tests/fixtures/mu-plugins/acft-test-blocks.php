<?php
/**
 * Plugin Name: ACFT test blocks (dev only)
 * Description: Registers ACF blocks for tests: a block with a Typography field, a block with no fields (GH #29), and a usePostMeta block. Never ship.
 *
 * Copy this file AND the acft-test-blocks/ folder into wp-content/mu-plugins/.
 *
 * @package ACF_Typography_Field
 */

add_action(
	'init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		foreach ( array( 'typo', 'empty', 'meta' ) as $acft_test_block ) {
			register_block_type( __DIR__ . '/acft-test-blocks/' . $acft_test_block );
		}
	}
);

add_action(
	'acf/init',
	function () {
		foreach ( array( 'typo', 'meta' ) as $acft_test_block ) {
			acf_add_local_field_group(
				array(
					'key'      => 'group_acft_block_' . $acft_test_block,
					'title'    => 'ACFT Test Block ' . $acft_test_block,
					'fields'   => array(
						array(
							'key'                => 'field_acft_block_' . $acft_test_block,
							'label'              => 'Block Typography',
							'name'               => 'block_typo_' . $acft_test_block,
							'type'               => 'Typography',
							'display_properties' => array( 'font_family', 'font_weight', 'font_size', 'text_color' ),
						),
					),
					'location' => array(
						array(
							array(
								'param'    => 'block',
								'operator' => '==',
								'value'    => 'acf/acft-' . $acft_test_block,
							),
						),
					),
				)
			);
		}
	}
);
