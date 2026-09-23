<?php
/**
 * What a Typography value becomes when it is saved (acft_sanitize_typography_value) and when it is
 * printed (the_typography_field, the [acf_typography] shortcode). Dev only; never ship.
 *
 * Registers its own field group, creates throwaway posts, and deletes them at the end, so it needs
 * nothing from the site except this plugin and ACF (free or Pro).
 *
 * Usage: wp eval-file - < tests/wp-eval/field-values-unit.php
 *
 * @package ACF_Typography_Field
 */

// phpcs:disable -- test script, run through wp eval-file

$GLOBALS['pass'] = 0;
$GLOBALS['fail'] = 0;
function t( $name, $exp, $got ) {
	$ok = $exp === $got;
	$ok ? $GLOBALS['pass']++ : $GLOBALS['fail']++;
	echo ( $ok ? 'PASS' : 'FAIL' ) . "  $name  (" . wp_json_encode( $got ) . ( $ok ? '' : ' expected ' . wp_json_encode( $exp ) ) . ")\n";
}
$warn = array();
set_error_handler( function ( $n, $s ) use ( &$warn ) { $warn[] = $s; return true; } );

acf_add_local_field_group(
	array(
		'key'      => 'group_acft_values_test',
		'title'    => 'ACFT values test',
		'fields'   => array(
			array( 'key' => 'field_acft_vt_typo', 'name' => 'heroTypo', 'type' => 'Typography', 'label' => 'Hero' ),
			array( 'key' => 'field_acft_vt_choices', 'name' => 'acftSecretChoices', 'type' => 'checkbox', 'label' => 'Secret', 'choices' => array( 'secret' => 'Secret' ) ),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
	)
);

$GLOBALS['posts'] = array(); // wp eval-file runs this file inside a function, so file-level variables are not global
function new_post( $status = 'publish' ) {
	$id = wp_insert_post( array( 'post_title' => 'ACFT values test', 'post_status' => $status, 'post_type' => 'post' ) );
	$GLOBALS['posts'][] = $id;
	return $id;
}
function stored( $id ) { wp_cache_delete( $id, 'post_meta' ); return get_post_meta( $id, 'heroTypo', true ); }
function sc( $atts ) { return do_shortcode( '[acf_typography ' . $atts . ']' ); }

echo "-- saving: acft_sanitize_typography_value() through update_field()\n";
$id = new_post();
update_field(
	'field_acft_vt_typo',
	array(
		'font_family'    => '<script>alert(1)</script>Roboto',
		'font_size'      => "18\nfont-size:99",
		'text_color'     => '" onmouseover=alert(1) x="',
		'font_weight'    => array( 'nested' => array( '700' ) ),
		'text_transform' => '<b>upper</b>case',
		'line_height'    => 1.5,
		'letter_spacing' => null,
	),
	$id
);
$v = stored( $id );
t( 's1 script tag and its content removed', 'Roboto', $v['font_family'] );
t( 's2 line break replaced by a space', '18 font-size:99', $v['font_size'] );
t( 's3 quotes kept (output escaping handles them)', '" onmouseover=alert(1) x="', $v['text_color'] );
t( 's4 array property dropped', false, array_key_exists( 'font_weight', $v ) );
t( 's5 other tags stripped, text kept', 'uppercase', $v['text_transform'] );
t( 's6 number kept as a number', 1.5, $v['line_height'] );
t( 's7 null kept', true, array_key_exists( 'letter_spacing', $v ) && null === $v['letter_spacing'] );

update_field( 'field_acft_vt_typo', array( 'font_family' => '"Arial Black", Gadget, sans-serif', 'font_size' => 18, 'text_align' => 'center' ), $id );
$v = stored( $id );
t( 's8 quoted web-safe family unchanged', '"Arial Black", Gadget, sans-serif', $v['font_family'] );
t( 's9 int unchanged', 18, $v['font_size'] );
t( 's10 plain value unchanged', 'center', $v['text_align'] );
t( 's11 a plain string value is cleaned too', 'x', acft_sanitize_typography_value( '<b>x</b>' ) );

echo "-- printing: the_typography_field() escapes values saved before 3.3.0 (not cleaned)\n";
$old = new_post();
update_field( 'field_acft_vt_typo', array( 'font_size' => '16' ), $old ); // saves the field reference
update_post_meta( $old, 'heroTypo', array( 'font_family' => '"><script>alert(1)</script>', 'text_color' => '" onmouseover=alert(1) x="' ) );
wp_cache_delete( $old, 'post_meta' );
ob_start(); the_typography_field( 'heroTypo', 'font_family', $old ); $out = ob_get_clean();
t( 'p1 no raw tag or quote in the output', false, false !== strpos( $out, '<' ) || false !== strpos( $out, '"' ) );
t( 'p2 value printed escaped', '&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $out );
t( 'p3 shortcode escapes the same way', '&quot; onmouseover=alert(1) x=&quot;', sc( 'field="heroTypo" property="text_color" post_id="' . $old . '"' ) );

echo "-- shortcode\n";
$p = new_post();
update_field( 'field_acft_vt_typo', array( 'font_family' => '"Arial Black", Gadget, sans-serif', 'font_size' => '18', 'font_weight' => '' ), $p );
// array values, so the getter would read a property from them if the shortcode let it
update_field( 'field_acft_vt_choices', array( 'secret' ), $p );
update_post_meta( $p, 'acft_plain_meta', array( 'k' => 'secret' ) );
update_option( 'options_acft_secret', array( 'k' => 'secret' ), false );
t( 'c1 camelCase field name', '18', sc( 'field="heroTypo" property="font_size" post_id="' . $p . '"' ) );
t( 'c2 field key', '18', sc( 'field="field_acft_vt_typo" property="font_size" post_id="' . $p . '"' ) );
t( 'c3 quoted family escaped', '&quot;Arial Black&quot;, Gadget, sans-serif', sc( 'field="heroTypo" property="font_family" post_id="' . $p . '"' ) );
t( 'c4 format_value="false"', '18', sc( 'field="heroTypo" property="font_size" post_id="' . $p . '" format_value="false"' ) );
t( 'c5 format_value="0"', '18', sc( 'field="heroTypo" property="font_size" post_id="' . $p . '" format_value="0"' ) );
t( 'c6 empty property value', '', sc( 'field="heroTypo" property="font_weight" post_id="' . $p . '"' ) );
t( 'c7 property not saved', '', sc( 'field="heroTypo" property="text_align" post_id="' . $p . '"' ) );
t( 'c8 no property attribute', '', sc( 'field="heroTypo" post_id="' . $p . '"' ) );
t( 'c9 no field attribute', '', sc( 'property="font_size" post_id="' . $p . '"' ) );
t( 'c10 wrong case does not match (names are case-sensitive)', '', sc( 'field="herotypo" property="font_size" post_id="' . $p . '"' ) );
$GLOBALS['post'] = get_post( $p ); setup_postdata( $GLOBALS['post'] );
t( 'c11 current post when post_id is left out', '18', sc( 'field="heroTypo" property="font_size"' ) );
wp_reset_postdata();

echo "-- shortcode reads Typography fields only\n";
$u   = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
$uid = 'user_' . (int) $u[0];
// control: the theme function has no such limit, so each value below is readable and the empty results mean the check works
t( 'r0 control: get_typography_field() reads them', 'secret|secret|secret|1', get_typography_field( 'acftSecretChoices', '0', $p ) . '|' . get_typography_field( 'acft_plain_meta', 'k', $p ) . '|' . get_typography_field( 'acft_secret', 'k', 'option' ) . '|' . get_typography_field( 'wp_capabilities', 'administrator', $uid ) );
t( 'r1 other ACF field type', '', sc( 'field="acftSecretChoices" property="0" post_id="' . $p . '"' ) );
t( 'r2 other ACF field by key', '', sc( 'field="field_acft_vt_choices" property="0" post_id="' . $p . '"' ) );
t( 'r3 plain post meta', '', sc( 'field="acft_plain_meta" property="k" post_id="' . $p . '"' ) );
t( 'r4 option that is not a field', '', sc( 'field="acft_secret" property="k" post_id="option"' ) );
t( 'r5 user meta (roles)', '', sc( 'field="wp_capabilities" property="administrator" post_id="' . $uid . '"' ) );

echo "-- other places a Typography value can live\n";
update_field( 'field_acft_vt_typo', array( 'font_size' => '22' ), 'option' );
t( 'o1 post_id="option"', '22', sc( 'field="heroTypo" property="font_size" post_id="option"' ) );
update_field( 'field_acft_vt_typo', array( 'font_size' => '24' ), 'user_' . (int) $u[0] );
t( 'o2 post_id="user_N"', '24', sc( 'field="heroTypo" property="font_size" post_id="user_' . (int) $u[0] . '"' ) );
$private = new_post( 'private' );
update_field( 'field_acft_vt_typo', array( 'font_size' => '26' ), $private );
t( 'o3 private post still prints (by design: styles are not secret)', '26', sc( 'field="heroTypo" property="font_size" post_id="' . $private . '"' ) );

// clean up
foreach ( $GLOBALS['posts'] as $id ) {
	wp_delete_post( $id, true );
}
delete_field( 'field_acft_vt_typo', 'option' );
delete_option( 'options_acft_secret' );
delete_field( 'field_acft_vt_typo', 'user_' . (int) $u[0] );
restore_error_handler();
echo 'warnings: ' . count( $warn ) . ( $warn ? ' ' . implode( ' | ', array_unique( $warn ) ) : '' ) . "\n";
echo "RESULT: {$GLOBALS['pass']} passed, {$GLOBALS['fail']} failed\n";
