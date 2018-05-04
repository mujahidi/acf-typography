=== Advanced Custom Fields: Typography Field ===
Contributors: mujahid158
Tags: typography, acf, advanced custom fields, addon, admin, field, custom, custom field
Requires at least: 3.5.0
Tested up to: 4.9.5
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Typography Add-on for the Advanced Custom Fields Plugin.

== Description ==

Typography field type for "Advanced Custom Fields" plugin that lets you add different text properties e.g. Font Size, Font Family, Font Color etc.

= Supported Subfields =
* Font Size
* Font Family
* Font Weight
* Font Style
* Font Variant
* Line Height
* Letter Spacing
* Text Align
* Text Color
* Text Decoration
* Text Transform

= Other Features =
* Option to show/hide each subfield individually
* Option to make each subfield required individually
* Color Picker for Text Color subfield

= Sample Output for Theme Developers =
`
Array
(
    [font_size] => 18
    [font_family] => "MS Sans Serif", Geneva, sans-serif
    [font_weight] => 400
    [font_style] => italic
    [font_variant => small-caps
    [line_height] => 1.5
    [letter_spacing] => 1
    [text_align] => center
    [text_color] => #ff0000
    [text_decoration] => underline
	[text_transform] => uppercase
)
`
`
$getTypography = get_field('typography');
echo $getTypography['font_size'];
`

= Compatibility =

This ACF field type is compatible with:
* ACF 4 (free version of ACF)
* ACF 5 (Pro)

== Installation ==

1. Copy the `acf-typography` folder into your `wp-content/plugins` folder
2. Activate the Typography plugin via the plugins admin page
3. Create a new field via ACF and select the Typography type
4. Please refer to the description for more info regarding the field type settings

== Frequently Asked Questions ==
= Q. Is it compatible with ACF Pro (v5)? =
A. Yes

= Q. Does it support Google Fonts? =
A. No. Currently, it only supports Web Safe Fonts. But the support for Google Fonts is coming in next major release soon.

== Screenshots ==

1. Typography Field Settings

2. Typography Field Content Editing

== Changelog ==
= 2.1.0 =
* [NEW] Font Variant subfield

= 2.0.0 =
* [NEW] Now supports ACF 5 (Pro version)

= 1.1.1 =
* Fixed a bug

= 1.1.0 =
* [NEW] Text Transform subfield

= 1.0.0 =
* Initial Release.