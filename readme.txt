=== Advanced Custom Fields: Typography Field ===
Contributors: mujahid158, codejp3
Tags: typography, acf, advanced custom fields, addon, admin, field, custom, custom field, acf typography, acf google fonts, google fonts
Requires at least: 3.5.0
Tested up to: 6.0
Stable tag: 3.2.3
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
* Supports serving Google Fonts remotely, or locally.
* Supports Gutenberg Blocks created with ACF.
* Supports Option pages created with ACF.
* Automatically enqueues stylesheet on front-end of site when typography values are present.
* Option to show/hide each subfield individually.
* Option to make each subfield required individually.
* Color Picker for Text Color subfield.
* Shortcode for getting typography field values.
* Shortcode for displaying link/style HTML code in-line.

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

= Shortcodes =
Retrieve typography field values for display or use anywhere you want. 
`
[acf_typography field="field-name" property="font_size" post_id="123" format_value="1"]
`

Retrieve either link or style HTML tag codes for font stylesheets to use in-line anywhere you want.
`
[acf_typography_stylesheet link_type="link" post_id="123"]

// link_type = "link" or "style" (str) (optional) (default: "link") 

// Returns link code for link_type="link"
// <link rel="stylesheet" ... />

// Returns style code for link_type="style"
// <style> ... </style>  

// post_id = a specific post_id to get the stylsheets for (str) (optional) (default: current post_id)
`
Want to alter the output for the link/style code returned?
`
function myprefix_change_stylesheet_output( $output, $tag, $attr ) {
	
	//make sure it is the right shortcode
	if('acf_typography_stylesheet' != $tag){ 
		return $output;
	}
	
	// general code to do something with the output string 
	// some examples are shown below
	
	// check for specific link_type
	if(isset($attr['link_type'])){
		// only do something based on supplied link_type ("link" or "style")
		if ($attr['link_type'] === "style") {
			// example to partially "minify" in-line CSS stylesheet code 
			$output = str_replace(array("\r", "\n"), "", $output);
		}
	}
	
	// check for specific post_id
	if(isset($attr['post_id'])){
		// only do something based on supplied post_id
	}
	
	// get current object ID if you did not supply a post_id
	if (!isset($attr['post_id'])) {
		$object_id = get_queried_object_id();
		// only do something based on current object_id
		
		// get post_type for current object_id
		if (get_post_type($object_id) == "my_custom_post_type") {
                        // only do something based on a specific post_type
		}
	}

	return $output;
}
add_filter( 'do_shortcode_tag', 'myprefix_change_stylesheet_output', 10, 3 );
`

= Github repository =
[@mujahidi/acf-typography](https://github.com/mujahidi/acf-typography)

= Compatibility =

This ACF field type is compatible with:
* Free and paid versions of the ACF plugin

== Installation ==

1. Copy the `acf-typography-field` folder into your `wp-content/plugins` folder
2. Activate the Typography plugin via the plugins admin page
3. Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`
4. Create a new field via ACF and select the Typography type
5. Please refer to the description for more info regarding the field type settings

== Frequently Asked Questions ==
= Q. Is it compatible with ACF Pro? =
A. Yes. This plugin is compatiable with both free and paid ACF plugins.

= Q. Does it support Google Fonts? =
A. Version 3.0.0 and greater supports Google Fonts

= Q. Why I do not see Google Fonts in the Font Family drop down?
A. Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`

= Q. Does it enqueue selected Google Fonts? =
A. Yes. This plugin automatically enqueues user selected Google Fonts to front-end of posts/pages.

= Q. Does it support Gutenberg Blocks? =
A. Yes. This plugin does support Gutenberg Blocks created with ACF.

= Q. Is it GDPR compliant? =
A. Depends. If you choose the admin option to serve files locally, then YES (after the first page load where font styles are used). If you choose the admin option to serve files remotely, then NO.

= Q. How can I contribute? =
A. Join in on Github repository [@mujahidi/acf-typography](https://github.com/mujahidi/acf-typography)

== Screenshots ==

1. Typography Field Settings

2. Typography Field Content Editing

3. Admin Settings Page Options

== Upgrade Notice ==


== Changelog ==
= 3.3.0 =
* [UPDATE] Fixed consistent textdomain usage for i18n translations.
* [UPDATE] Made sure displayed strings are all i18n translation-ready.
* [NEW] Plugin now 100% translation-ready and initial .pot file generated! #24
* [UPDATE] Added a few minor additional checks for some variables to prevent possible errors in the future.
* [BUG] Fixed 'Notice: Trying to get property ID of non-object' when enqueueing font files on non-objects. ( WP Support )
* [BUG] Fixed 'Warning: failed to open stream: HTTP request failed' when bad API Key supplied, or other API connection issues. #27
* [NEW] Admin Settings checks and help messages for Google API Key/connection issues.
* [NEW] Google Fonts can now be saved and served locally.
* [NEW] Admin Settings Option to choose between serving font stylesheets and font files Remotely or Locally.
* [NEW] Description and link provided for getting Google Fonts API key in Admin Settings page.
* [NEW] Shortcode to print in-line stylesheet link or style HTML code (acf_typography_stylesheet).
* [NEW] Tested up to PHP 8.2
* [UPDATE] Minor code cleanup for readability. 
* [UPDATE] Added new screenshot for new Admin Settings Page Options. 

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
