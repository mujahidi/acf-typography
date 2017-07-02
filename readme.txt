=== Advanced Custom Fields: Typography Field ===
Contributors: mujahid158
Tags: typography, acf, advanced custom fields, addon, admin, field, custom, custom field
Requires at least: 3.5.0
Tested up to: 4.8
Stable tag: 1.1.1
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
* ACF 4

== Installation ==

1. Copy the `acf-typography` folder into your `wp-content/plugins` folder
2. Activate the Typography plugin via the plugins admin page
3. Create a new field via ACF and select the Typography type
4. Please refer to the description for more info regarding the field type settings

== Frequently Asked Questions ==
= Q. Is it compatible with ACF Pro (v5)? =
A. No. Currently, the plugin is ONLY compatible with v4 (free version of ACF) but support for v5 will arrive soon.

= Q. Does it support Google Fonts? =
A. No. Currently, it only supports Web Safe Fonts. The support for Google Fonts will arrive soon.

== Screenshots ==

1. Typography Field Settings

2. Typography Field Content Editing

== Changelog ==
= 1.1.1 =
* Fixed a bug

= 1.1.0 =
* [NEW] Text Transform subfield

= 1.0.0 =
* Initial Release.