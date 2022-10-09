=== Advanced Custom Fields: Typography Field ===
Contributors: mujahid158, codejp3
Tags: typography, acf, advanced custom fields, addon, admin, field, custom, custom field, acf typography, acf google fonts, google fonts
Requires at least: 3.5.0
Tested up to: 6.0
Stable tag: 3.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Typography Add-on for the Advanced Custom Fields Plugin.

== Description ==

Typography field type for "Advanced Custom Fields" plugin that lets you add different text properties e.g. Font Size, Font Family, Font Color etc.

If you want any kind of font/typography features within ACF, this is the plugin for you! There's nothing else like it!

= Supported Subfields =
* Font Size
* Font Family
* Font Weight
* Font Style
* Font Variant
* Font Stretch
* Line Height
* Letter Spacing
* Text Align
* Text Color
* Text Decoration
* Text Transform

= Other Features =
* Supports Google Fonts. The selected Google Fonts are automatically enqueued on front-end of posts/pages. Google Fonts also work with ACF Options.
* Supports Gutenberg Blocks created with ACF.
* Option to show/hide each subfield individually
* Option to make each subfield required individually
* Color Picker for Text Color subfield

= Documentation =
`
// Returns the value of a specific property
get_typography_field( $selector, $property, [$post_id], [$format_value] );

// Displays the value of a specific property
the_typography_field( $selector, $property, [$post_id], [$format_value] );

// Returns the value of a specific property from a sub field.
get_typography_sub_field( $selector, $property, [$format_value] );

// Displays the value of a specific property from a sub field.
the_typography_sub_field( $selector, $property, [$format_value] );
`

= Shortcode =
`[acf_typography field="field-name" property="font_size" post_id="123" format_value="1"]`

= Github repository =
[@mujahidi/acf-typography](https://github.com/mujahidi/acf-typography)

= Compatibility =

This ACF field type is compatible with:
* ACF plugin v4, v5 & v6
* Free and paid versions of the ACF plugin

== Installation ==

1. Copy the `acf-typography-field` folder into your `wp-content/plugins` folder
2. Activate the Typography plugin via the plugins admin page
3. Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`
4. Create a new field via ACF and select the Typography type
5. Please refer to the description for more info regarding the field type settings

== Frequently Asked Questions ==
= Q. Which versions of ACF is this plugin compatible with? =
A. It is compatible with V4, V5, and the latest v6. There are 2 known UI bugs with v6 (number fields & color_picker fields) but functionality works, and we are currently working with the ACF plugin authors to resolve the visual issues present in v6.

= Q. Is it compatible with ACF Pro? =
A. Yes. This plugin is compatible with both free and paid ACF plugins.

= Q. Does it support Google Fonts? =
A. Version 3.0.0 and greater supports Google Fonts

= Q. Why I do not see Google Fonts in the Font Family drop down?
A. Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`

= Q. Does it enqueue selected Google Fonts? =
A. Yes. This plugin automatically enqueues user selected Google Fonts to front-end of posts/pages.

= Q. Does it support Gutenberg Blocks? =
A. Yes. This plugin does support Gutenberg Blocks created with ACF.

= Q. How can I contribute? =
A. Join in on Github repository [@mujahidi/acf-typography](https://github.com/mujahidi/acf-typography)

== Screenshots ==

1. Typography Field Settings

2. Typography Field Content Editing

3. Google Key Field required for Google Fonts

== Upgrade Notice ==


== Changelog ==
= 3.2.4 =
* [NEW] Plugin now officially supports ACF v6!
* [NEW] Tested up to PHP 8.2.
* [BUG] Fixed 'Notice: Trying to get property ID of non-object' when enqueueing font files on non-objects. ( WP Support )
* [BUG] Fixed 'Warning: failed to open stream: HTTP request failed' when bad API Key supplied, or other API connection issues. #27
* [NEW] Admin Settings - checks and help messages for Google API Key/connection issues.
* [NEW] Admin Settings - description and link provided for getting Google Fonts API key.
* [NEW] Admin Settings - checks to make sure ACF is installed and activated.
* [UPDATE] Translation string and textdomain updates to get plugin translation-ready (coming next release).
* [UPDATE] Added a few minor additional checks for some variables to prevent possible errors in the future.
* [UPDATE] Minor code cleanup for readability. 

= 3.2.3 =
* Added new font-weight values

= 3.2.2 =
* Fixed google fonts url too long issue

= 3.2.1 =
* Fixed typo and bugs

= 3.2.0 =
* [NEW] Added 'initial' and 'inherit' values for 'font-family' property.
* [NEW] Added support for ACF Blocks.
* [BUG] Fixed a PHP notice when any post does not have ACF Typography fields setup.

= 3.1.0 =
* [NEW] Google Fonts support for ACF Options
* [BUG] Different font weights were not enqueued when used in a repeater field

= 3.0.0 =
* [NEW] Introduces functions and shortcode
* [NEW] Hides nonselected properties in fieldgroup edit page
* [NEW] Supports Google Fonts
* [NEW] Enqueue google fonts CSS on page/post

= 2.2.0 =
* [NEW] Font Stretch subfield

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
