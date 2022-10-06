# Advanced Custom Fields: Typography Field

A Typography Add-on for the Advanced Custom Fields Plugin.

  - Requires at least: WP 3.5.0
  - Tested up to: WP 6.0
  - Tested up to: PHP 8.2
  - Stable: 3.2.3
  - Latest: 3.3.0

## Description
Typography field type for "Advanced Custom Fields" plugin that lets you add different text properties e.g. Font Size, Font Family, Font Color etc.

If you want any kind of font/typography features within ACF, this is the plugin for you! There's nothing else like it!

### Supported Subfields
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

### Other Features
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
* Translation-ready .pot file.

## Screenshots
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-1.png "Typography Sample Field Settings")
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-2.png "Typography Sample Field Content Editing")
![Typography Field Screenshot](https://github.com/codejp3/acf-typography/blob/1fe3d490fa8ecb79b3e0bb6e51f4c0a90b8b2239/screenshot-3.png "Admin Settings Page Options")

## Documentation
```php
// Returns the value of a specific property
get_typography_field( $selector, $property, [$post_id], [$format_value] );

// Displays the value of a specific property
the_typography_field( $selector, $property, [$post_id], [$format_value] );

// Returns the value of a specific property from a sub field.
get_typography_sub_field( $selector, $property, [$format_value] );

// Displays the value of a specific property from a sub field.
the_typography_sub_field( $selector, $property, [$format_value] );
```
#### Shortcodes
Retrieve typography field values for display or use anywhere you want. 
```php
[acf_typography field="field-name" property="font_size" post_id="123" format_value="1"]
```

Retrieve either link or style HTML tag codes for font stylesheets to use in-line anywhere you want.
```php
[acf_typography_stylesheet link_type="link" post_id="123"]

// link_type = "link" or "style" (str) (optional) (default: "link") 

// Returns link code for link_type="link"
// <link rel="stylesheet" ... />

// Returns style code for link_type="style"
// <style> ... </style>  

// post_id = a specific post_id to get the stylsheets for (str) (optional) (default: current post_id)
```
Want to alter the output for the link/style code returned?
Use the Filter Hook (acf_alter_typography_stylesheet) like this:
```php
function myprefix_change_stylesheet_output( $output ) {

	// code to do something with the output string

	return $output;
}
add_filter( 'acf_alter_typography_stylesheet', 'myprefix_change_stylesheet_output' );
```

## Compatibility

This ACF field type is compatible with:
* Free and paid versions of the ACF plugin

## Installation

- Download the plugin from [WordPress Repository](https://wordpress.org/plugins/acf-typography-field/) or use the latest release from this repository.
- Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`

## Changelog
See changelog on [CHANGELOG.md](CHANGELOG.md) file.
