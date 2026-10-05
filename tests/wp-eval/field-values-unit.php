<?php
/**
 * What a Typography value becomes when it is saved (acft_sanitize_typography_value) and when it is
 * printed (the_typography_field, the [acf_typography] shortcode), and which Font Family option the
 * editor selects (list keys, old field group defaults). Dev only; never ship.
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
t( 's2 line break replaced by a space, colon stripped', '18 font-size99', $v['font_size'] );
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

update_field(
	'field_acft_vt_typo',
	array(
		'font_size'   => '18px;background:url(https://evil.example/t)',
		'text_color'  => 'red}body{display:none',
		'line_height' => 'normal\\3b color\\3a red',
		'font_style'  => 'italic',
	),
	$id
);
$v = stored( $id );
t( 's12 ; and : stripped, so no extra declaration', '18pxbackgroundurl(https//evil.example/t)', $v['font_size'] );
t( 's13 { and } stripped, so no new rule', 'redbodydisplaynone', $v['text_color'] );
t( 's14 backslash stripped, so no CSS escapes', 'normal3b color3a red', $v['line_height'] );
t( 's15 plain keyword unchanged', 'italic', $v['font_style'] );
update_field( 'field_acft_vt_typo', array( 'font_size' => '16px', 'line_height' => 'normal', 'text_color' => 'rgba(0, 0, 0, .5)', 'letter_spacing' => '-0.5' ), $id );
$v = stored( $id );
t( 's16 unit, keyword, rgba() and negative number unchanged', array( '16px', 'normal', 'rgba(0, 0, 0, .5)', '-0.5' ), array( $v['font_size'], $v['line_height'], $v['text_color'], $v['letter_spacing'] ) );

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
t( 'c12 field key, current post', '18', sc( 'field="field_acft_vt_typo" property="font_size"' ) );
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
t( 'o4 field key on the options page and a user', '22|24', sc( 'field="field_acft_vt_typo" property="font_size" post_id="option"' ) . '|' . sc( 'field="field_acft_vt_typo" property="font_size" post_id="user_' . (int) $u[0] . '"' ) );

echo "-- shortcode: a Typography field key only reads where that field is saved\n";
// other code stored an array under the Typography field's name, with no ACF reference
$q = new_post();
update_post_meta( $q, 'heroTypo', array( 'k' => 'secret' ) );
t( 'r6 control: get_typography_field() reads it by key', 'secret', get_typography_field( 'field_acft_vt_typo', 'k', $q ) );
t( 'r6 shortcode with the key does not', '', sc( 'field="field_acft_vt_typo" property="k" post_id="' . $q . '"' ) );
// same name, reference to another field
update_post_meta( $q, '_heroTypo', 'field_acft_vt_choices' );
t( 'r7 reference to another field', '', sc( 'field="field_acft_vt_typo" property="k" post_id="' . $q . '"' ) );

echo "-- font family keys: list keys match their labels, old field group defaults are corrected\n";
foreach ( $GLOBALS['wp_filter']['acf/validate_value/type=Typography']->callbacks as $cbs ) {
	foreach ( $cbs as $cb ) {
		if ( is_array( $cb['function'] ) ) {
			$type = $cb['function'][0];
		}
	}
}
$type->load_font_family();
$mismatch = array();
foreach ( $type->font_family as $k => $v ) {
	if ( (string) $k !== $v ) {
		$mismatch[] = $k;
	}
}
t( 'k1 every font family key equals its label', array(), $mismatch );

acf_add_local_field_group(
	array(
		'key'      => 'group_acft_keys_test',
		'title'    => 'ACFT keys test',
		'fields'   => array(
			array( 'key' => 'field_acft_kt_tahoma', 'name' => 'ktTahoma', 'type' => 'Typography', 'label' => 'T', 'display_properties' => array( 'font_family' ), 'font_family' => 'Tahoma,Geneva, sans-serif' ),
			array( 'key' => 'field_acft_kt_times', 'name' => 'ktTimes', 'type' => 'Typography', 'label' => 'T', 'display_properties' => array( 'font_family' ), 'font_family' => '"Times New Roman", Times,serif' ),
			array( 'key' => 'field_acft_kt_georgia', 'name' => 'ktGeorgia', 'type' => 'Typography', 'label' => 'G', 'display_properties' => array( 'font_family' ), 'font_family' => 'Georgia, serif' ),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
	)
);
// the font family <select> of a rendered field: selected values and number of options
function family_select( $type, $key, $value ) {
	$field          = acf_get_field( $key );
	$field['name']  = 'acf[' . $key . ']';
	$field['id']    = acf_idify( $field['name'] );
	$field['value'] = $value;
	ob_start();
	$type->render_field( $field );
	$html = ob_get_clean();
	preg_match( '#<select[^>]*\[font_family\]"[^>]*>(.*?)</select>#s', $html, $m );
	preg_match_all( '#<option value="([^"]*)" ?selected#', $m[1], $sel );
	return array( 'selected' => array_map( 'html_entity_decode', $sel[1] ), 'options' => substr_count( $m[1], '<option' ) );
}
$count = count( $type->font_family );
t( 'k2 old Tahoma default corrected on load', 'Tahoma, Geneva, sans-serif', acf_get_field( 'field_acft_kt_tahoma' )['font_family'] );
t( 'k3 old Times default corrected on load', '"Times New Roman", Times, serif', acf_get_field( 'field_acft_kt_times' )['font_family'] );
t( 'k4 other default untouched', 'Georgia, serif', acf_get_field( 'field_acft_kt_georgia' )['font_family'] );
t( 'k5 non-string and unknown values untouched', array( array( 'x' ), 'Arial, Helvetica, sans-serif', 'Tahoma,Geneva,sans-serif' ), array( acft_typography_fix_font_family_key( array( 'x' ) ), acft_typography_fix_font_family_key( 'Arial, Helvetica, sans-serif' ), acft_typography_fix_font_family_key( 'Tahoma,Geneva,sans-serif' ) ) );
t( 'k6 old Tahoma default: selected once, no extra option', array( 'selected' => array( 'Tahoma, Geneva, sans-serif' ), 'options' => $count ), family_select( $type, 'field_acft_kt_tahoma', '' ) );
t( 'k7 old Times default: selected once, no extra option', array( 'selected' => array( '"Times New Roman", Times, serif' ), 'options' => $count ), family_select( $type, 'field_acft_kt_times', '' ) );
t( 'k8 saved value wins over the default', array( 'selected' => array( 'Georgia, serif' ), 'options' => $count ), family_select( $type, 'field_acft_kt_tahoma', array( 'font_family' => 'Georgia, serif' ) ) );
t( 'k9 saved Tahoma text (what posts store) is selected', array( 'selected' => array( 'Tahoma, Geneva, sans-serif' ), 'options' => $count ), family_select( $type, 'field_acft_kt_georgia', array( 'font_family' => 'Tahoma, Geneva, sans-serif' ) ) );
t( 'k10 saved value missing from the list is kept', array( 'selected' => array( 'Foo Sans' ), 'options' => $count + 1 ), family_select( $type, 'field_acft_kt_georgia', array( 'font_family' => 'Foo Sans' ) ) );

echo "-- editor markup: labels close, point at their input, ids unique per field\n";
$props = array( 'font_size', 'font_family', 'font_weight', 'font_style', 'font_variant', 'font_stretch', 'line_height', 'letter_spacing', 'text_align', 'text_color', 'text_decoration', 'text_transform' );
t( 'l1 property labels: all 12, in display order', $props, function_exists( 'acft_typography_property_labels' ) ? array_keys( acft_typography_property_labels() ) : 'no acft_typography_property_labels()' );
t( 'l2 single label comes from the same list', 'Letter Spacing|Text Color|Foo Bar', acft_typography_property_label( 'letter_spacing' ) . '|' . acft_typography_property_label( 'text_color' ) . '|' . acft_typography_property_label( 'foo_bar' ) );
acf_add_local_field_group(
	array(
		'key'      => 'group_acft_markup_test',
		'title'    => 'ACFT markup test',
		'fields'   => array(
			array( 'key' => 'field_acft_mt_a', 'name' => 'mtA', 'type' => 'Typography', 'label' => 'A', 'display_properties' => $props, 'required_properties' => array( 'font_size', 'font_family', 'text_color' ) ),
			array( 'key' => 'field_acft_mt_b', 'name' => 'mtB', 'type' => 'Typography', 'label' => 'B', 'display_properties' => $props ),
		),
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
	)
);
function render_html( $type, $key, $id = null ) {
	$field          = acf_get_field( $key );
	$field['name']  = 'acf[' . $key . ']';
	$field['id']    = null === $id ? acf_idify( $field['name'] ) : $id;
	$field['value'] = '';
	ob_start();
	$type->render_field( $field );
	return ob_get_clean();
}
$a = render_html( $type, 'field_acft_mt_a' );
$b = render_html( $type, 'field_acft_mt_b' );
preg_match_all( '#<label for="([^"]*)"#', $a, $for );
preg_match_all( '# id="([^"]*)"#', $a . $b, $ids );
$missing = array_values( array_diff( $for[1], $ids[1] ) );
$dupes   = array_values( array_unique( array_diff_assoc( $ids[1], array_unique( $ids[1] ) ) ) );
t( 'l3 every <label> is closed', array( 12, 12 ), array( substr_count( $a, '<label' ), substr_count( $a, '</label>' ) ) );
t( 'l4 every label points at an input of the field', array(), $missing );
t( 'l5 ids built from the field id', 'acf-field_acft_mt_a-font_size|acf-field_acft_mt_a-font_family|acf-field_acft_mt_a-text_color', $for[1][0] . '|' . $for[1][1] . '|' . $for[1][9] );
t( 'l6 two fields on one page share no ids', array(), $dupes );
t( 'l7 required stars: 3, each inside its label', 3, preg_match_all( '#<label for="[^"]*">\s*[^<]*<span class="acf-required">\*</span>\s*</label>#', $a ) );
preg_match_all( '#<label for="([^"]*)"#', render_html( $type, 'field_acft_mt_b', '' ), $for );
t( 'l8 no field id: old ids kept', 'acf-field-font_size|acf-field-text_transform', $for[1][0] . '|' . $for[1][11] );
$s = acf_get_field( 'field_acft_mt_a' );
$s['prefix'] = 'acf_fields[1]';
ob_start();
$type->render_field_settings( $s );
$h = ob_get_clean();
preg_match_all( '#name="acf_fields\[1\]\[display_properties\]\[\]" value="([^"]*)"#', $h, $d );
preg_match_all( '#name="acf_fields\[1\]\[required_properties\]\[\]" value="([^"]*)"#', $h, $r );
t( 'l9 settings checkboxes: same 12 properties in order', array( $props, $props ), array( $d[1], $r[1] ) );
ob_start();
acft_render_typography_select_options( $type->font_weight, 700 );
$h = ob_get_clean();
t( 'l10 a weight saved as a number selects its choice, no extra option', array( 9, 1, true ), array( substr_count( $h, '<option' ), substr_count( $h, 'selected' ), false !== strpos( $h, '<option value="700" selected>' ) ) );

echo "-- field settings: a default font missing from the list is kept\n";
t( 'd1 missing default added first', array( 'Acft Missing Font', 'initial' ), array_slice( array_keys( acft_typography_font_family_choices( $type->font_family, 'Acft Missing Font' ) ), 0, 2 ) );
t( 'd2 listed default: choices unchanged', true, $type->font_family === acft_typography_font_family_choices( $type->font_family, 'Georgia, serif' ) );
t( 'd3 empty or non-string default: unchanged', array( true, true ), array( $type->font_family === acft_typography_font_family_choices( $type->font_family, '' ), $type->font_family === acft_typography_font_family_choices( $type->font_family, array( 'x' ) ) ) );
$fs = acf_get_valid_field( array( 'key' => 'field_acft_fs', 'name' => 'fs', 'type' => 'Typography', 'prefix' => 'acf_fields[1]', 'font_family' => 'Acft Missing Font' ) );
ob_start();
$type->render_field_settings( $fs );
$h = ob_get_clean();
t( 'd4 settings screen selects the missing default', 1, preg_match( '#<option value="Acft Missing Font" selected#', $h ) );

echo "-- display properties given as a string (fields registered in PHP)\n";
t( 'p1 string -> one item', array( 'font_size' ), acft_typography_property_list( 'font_size' ) );
t( 'p2 empty string / null / unknown names', array( array(), array(), array( 'text_color' ) ), array( acft_typography_property_list( '' ), acft_typography_property_list( null ), acft_typography_property_list( array( 'foo', 'text_color', 3 ) ) ) );
$sf                       = acf_get_field( 'field_acft_mt_b' );
$sf['display_properties'] = 'font_size';
$sf['name']               = 'acf[field_acft_mt_b]';
$sf['id']                 = acf_idify( $sf['name'] );
$sf['value']              = '';
$n                        = count( $warn );
try {
	ob_start();
	$type->render_field( $sf );
	$h = ob_get_clean();
	t( 'p3 string display_properties renders that one input, no warning', array( 1, 0 ), array( substr_count( $h, 'name="acf[field_acft_mt_b][' ), count( $warn ) - $n ) );
} catch ( Throwable $e ) {
	ob_end_clean();
	t( 'p3 string display_properties renders that one input, no warning', 'no error', get_class( $e ) . ': ' . $e->getMessage() );
}
$sf['required_properties'] = 'font_size';
acf_reset_validation_errors();
$type->validate_value( true, array( 'font_size' => '' ), $sf, 'acf[field_acft_mt_b]' );
t( 'p4 string required_properties still validated', array( 'acf[field_acft_mt_b][font_size]' ), wp_list_pluck( acf_get_validation_errors() ?: array(), 'input' ) );
acf_reset_validation_errors();

echo "-- required properties: validate_value()\n";
$rq = acf_get_valid_field(
	array(
		'key'                 => 'field_acft_rq',
		'name'                => 'rq',
		'type'                => 'Typography',
		'display_properties'  => array( 'font_size', 'letter_spacing', 'text_color' ),
		'required_properties' => array( 'font_size', 'letter_spacing', 'font_family' ), // font_family is required but not shown
	)
);
function req_errors( $type, $field, $value, $input ) {
	acf_reset_validation_errors();
	$type->validate_value( true, $value, $field, $input );
	$e = wp_list_pluck( acf_get_validation_errors() ?: array(), 'input' );
	sort( $e );
	acf_reset_validation_errors();
	return $e;
}
t( 'v1 0 and "0" count as filled in', array(), req_errors( $type, $rq, array( 'font_size' => '0', 'letter_spacing' => 0 ), 'acf[field_acft_rq]' ) );
t( 'v2 empty and missing: one error each, on $input[property]; hidden font_family ignored', array( 'acf[field_acft_rq][font_size]', 'acf[field_acft_rq][letter_spacing]' ), req_errors( $type, $rq, array( 'font_size' => '', 'text_color' => '#000' ), 'acf[field_acft_rq]' ) );
t( 'v3 input name inside a repeater used as is', array( 'acf[field_rep][row-0][field_acft_rq][font_size]' ), req_errors( $type, $rq, array( 'letter_spacing' => '1' ), 'acf[field_rep][row-0][field_acft_rq]' ) );
$rq2                        = $rq;
$rq2['required_properties'] = '';
$rq2['display_properties']  = '';
$n                          = count( $warn );
t( 'v4 settings saved as "": no error, no warning', array( array(), 0 ), array( req_errors( $type, $rq2, array(), 'acf[field_acft_rq]' ), count( $warn ) - $n ) );

echo "-- editor: a saved 0 is shown, not the field default\n";
$z          = acf_get_field( 'field_acft_mt_b' );
$z['name']  = 'acf[field_acft_mt_b]';
$z['id']    = acf_idify( $z['name'] );
$z['value'] = array( 'font_size' => '0', 'line_height' => 0, 'letter_spacing' => '0' );
ob_start();
$type->render_field( $z );
$h = ob_get_clean();
preg_match_all( '#name="acf\[field_acft_mt_b\]\[(font_size|line_height|letter_spacing)\]" value="([^"]*)"#', $h, $m );
t( 'z1 font size, line height, letter spacing print value="0"', array( 'font_size' => '0', 'line_height' => '0', 'letter_spacing' => '0' ), array_combine( $m[1], $m[2] ) );

echo "-- front end: fonts collected from ACF blocks, and views with no post\n";
$blocks = parse_blocks(
	'<!-- wp:acf/typo {"name":"acf/typo","data":{"t":{"font_family":"Lato","font_weight":"300"}}} /-->'
	. '<p>freeform between blocks</p>'
	. '<!-- wp:group --><div class="wp-block-group"><!-- wp:acf/typo {"name":"acf/typo","data":{"t":{"font_family":"Roboto","font_weight":"900"}}} /--></div><!-- /wp:group -->'
	. '<!-- wp:acf/empty {"name":"acf/empty"} /-->'
);
$bd = acft_get_acf_blocks_data( $blocks );
t( 'b1 top-level and nested ACF blocks found; freeform and data-less blocks skipped (GH #29)', 2, count( $bd ) );
$w = array();
acft_collect_font_weights( $bd, $w );
t( 'b2 each family keeps its own weights', 'https://fonts.googleapis.com/css?family=Lato:300,400,700|Roboto:400,700,900&display=swap', acft_google_fonts_url( $w ) );
$saved_post      = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
$GLOBALS['post'] = null;
$n               = count( $warn );
acft_enqueue_google_fonts_file();
$GLOBALS['post'] = $saved_post;
t( 'b3 no post (404, search): no warning', 0, count( $warn ) - $n );

echo "-- block data: values saved in ACF's stored format are cleaned too\n";
if ( function_exists( 'acf_parse_save_blocks' ) && function_exists( 'acf_register_block_type' ) ) {
	if ( ! acf_has_block_type( 'acf/acft-vt' ) ) {
		acf_register_block_type( array( 'name' => 'acft-vt', 'title' => 'ACFT vt', 'render_callback' => '__return_empty_string', 'mode' => 'preview' ) );
	}
	$save_block = function ( $data ) {
		$in = '<!-- wp:acf/acft-vt ' . wp_json_encode( array( 'name' => 'acf/acft-vt', 'data' => $data, 'mode' => 'preview' ) ) . ' /-->';
		$b  = parse_blocks( wp_unslash( acf_parse_save_blocks( wp_slash( $in ) ) ) );
		return $b[0]['attrs']['data'];
	};
	// stored format, as typed in the Code editor: ACF saves it without update_value()
	$d = $save_block(
		array(
			'heroTypo'  => array( 'font_size' => '18px;position:fixed', 'font_family' => 'Lato' ),
			'_heroTypo' => 'field_acft_vt_typo',
			'noRef'     => array( 'text_color' => 'red}x' ),
			'grp'       => array( 'inner' => array( 'line_height' => '1;a:b' ) ),
			'other'     => 'a;b',
			'list'      => array( 'x;y' ),
		)
	);
	t( 'bk1 stored format: value cleaned, family kept', array( '18pxpositionfixed', 'Lato' ), array( $d['heroTypo']['font_size'], $d['heroTypo']['font_family'] ) );
	t( 'bk2 value without a field reference cleaned', 'redx', $d['noRef']['text_color'] );
	t( 'bk3 nested value cleaned', '1ab', $d['grp']['inner']['line_height'] );
	t( 'bk4 other data untouched', array( 'a;b', array( 'x;y' ), 'field_acft_vt_typo' ), array( $d['other'], $d['list'], $d['_heroTypo'] ) );
	// the block editor form sends field keys; ACF runs update_value() for those
	$d = $save_block( array( 'field_acft_vt_typo' => array( 'font_size' => '20px;x:y' ) ) );
	t( 'bk5 editor form format still cleaned', '20pxxy', $d['heroTypo']['font_size'] );

	// a block with a plain field named like a Typography property: ACF flattens block data,
	// so that field sits beside every other field (Codex review, 3.3.0)
	acf_add_local_field_group(
		array(
			'key'      => 'group_acft_vt_block',
			'title'    => 'ACFT values test block',
			'fields'   => array(
				array( 'key' => 'field_acft_vt_b_size', 'name' => 'font_size', 'type' => 'number', 'label' => 'Font size' ),
				array( 'key' => 'field_acft_vt_b_intro', 'name' => 'intro', 'type' => 'text', 'label' => 'Intro' ),
				array( 'key' => 'field_acft_vt_b_body', 'name' => 'body', 'type' => 'textarea', 'label' => 'Body' ),
				array( 'key' => 'field_acft_vt_b_tags', 'name' => 'tags', 'type' => 'checkbox', 'label' => 'Tags', 'choices' => array( 'a' => 'A', 'b' => 'B' ) ),
				array( 'key' => 'field_acft_vt_b_cta', 'name' => 'cta', 'type' => 'link', 'label' => 'Button' ),
				array( 'key' => 'field_acft_vt_b_typo', 'name' => 'blockTypo', 'type' => 'Typography', 'label' => 'Block' ),
			),
			'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'acf/acft-vt' ) ) ),
		)
	);
	$intro = 'Visit https://example.com; open 9:00';
	$body  = "<p>Line one</p>\n<p>Line two</p>";
	$cta   = array( 'title' => 'Book', 'url' => 'https://example.com/book', 'target' => '' );
	$d     = $save_block(
		array(
			'field_acft_vt_b_size'  => '16',
			'field_acft_vt_b_intro' => $intro,
			'field_acft_vt_b_body'  => $body,
			'field_acft_vt_b_tags'  => array( 'a', 'b' ),
			'field_acft_vt_b_cta'   => $cta,
			'field_acft_vt_b_typo'  => array( 'font_size' => '18px;position:fixed', 'font_family' => 'Lato' ),
		)
	);
	t( 'bk6 plain font_size field beside others: its value kept', '16', $d['font_size'] ?? null );
	t( 'bk7 ... text and textarea beside it unchanged', array( $intro, $body ), array( $d['intro'] ?? null, $d['body'] ?? null ) );
	t( 'bk8 ... checkbox and link beside it kept', array( array( 'a', 'b' ), $cta ), array( $d['tags'] ?? null, $d['cta'] ?? null ) );
	t( 'bk9 ... Typography value beside it kept and cleaned', array( '18pxpositionfixed', 'Lato' ), array( $d['blockTypo']['font_size'] ?? null, $d['blockTypo']['font_family'] ?? null ) );

	// stored format, hand-edited
	$d = $save_block(
		array(
			'font_size'  => '16',
			'_font_size' => 'field_acft_vt_b_size',
			'blockTypo'  => array( 'not_a_property' => 'red;x' ),
			'_blockTypo' => 'field_acft_vt_b_typo',
			'looseTypo'  => array( 'font_size' => '1;a:b', 'made_up' => 'c;d', 'deep' => array( 'x' ) ),
			'byName'     => array( 'font_family' => 'Lato;x' ),
			'_byName'    => 'blockTypo',
			'linkish'    => array( 'url' => 'https://example.com', 'inner' => array( 'line_height' => '2;y' ) ),
			'_linkish'   => 'field_acft_vt_b_cta',
		)
	);
	t( 'bk10 value with a Typography field reference cleaned, whatever its keys', 'redx', $d['blockTypo']['not_a_property'] ?? null );
	t( 'bk11 value without a reference: Typography property cleaned, other keys kept', array( '1ab', 'c;d', array( 'x' ) ), array( $d['looseTypo']['font_size'] ?? null, $d['looseTypo']['made_up'] ?? null, $d['looseTypo']['deep'] ?? null ) );
	t( 'bk12 a reference by name, not key, counts as no reference', 'Latox', $d['byName']['font_family'] ?? null );
	t( 'bk13 other field: its own value kept, Typography value inside it cleaned', array( 'https://example.com', '2y' ), array( $d['linkish']['url'] ?? null, $d['linkish']['inner']['line_height'] ?? null ) );

	// a value whose reference names a field this site does not have (e.g. content copied from
	// another site) may be another plugin's data that shares a property name (Codex review, 3.3.0)
	$css = 'font-size: 12px;color:red; background:blue';
	$d   = $save_block(
		array(
			'style'     => array(
				'font_size'  => '16px;position:fixed',
				'custom_css' => $css,
				'hover'      => array( 'color' => 'blue' ),
				'sizes'      => array( 'mobile' => array( 'line_height' => '1;a:b' ) ),
				'font_style' => array( 'a;b' ),
				'order'      => 2,
			),
			'_style'    => 'field_acft_vt_not_on_this_site',
			'customCss' => $css,
		)
	);
	t( 'bk14 unknown reference: Typography property cleaned', '16pxpositionfixed', $d['style']['font_size'] ?? null );
	t( 'bk15 ... its other text, nested data and numbers kept', array( $css, array( 'color' => 'blue' ), 2 ), array( $d['style']['custom_css'] ?? null, $d['style']['hover'] ?? null, $d['style']['order'] ?? null ) );
	t( 'bk16 ... a Typography value inside it cleaned; a list under a property name kept', array( '1ab', array( 'a;b' ) ), array( $d['style']['sizes']['mobile']['line_height'] ?? null, $d['style']['font_style'] ?? null ) );
	t( 'bk17 ... keys keep their order', array( 'font_size', 'custom_css', 'hover', 'sizes', 'font_style', 'order' ), array_keys( $d['style'] ?? array() ) );
	t( 'bk18 a text value with CSS in it is never touched', $css, $d['customCss'] ?? null );
} else {
	echo "SKIP  block data checks (need ACF Pro)\n";
}

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
