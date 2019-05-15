# Advanced Custom Fields: Typography Field

A Typography Add-on for the Advanced Custom Fields Plugin.

  - Requires at least: WP 3.5.0
  - Tested up to: WP 5.2.0
  - Stable: 2.2.0
  - Latest: 3.0.0 (pre-release)

## Version 3.0.0 introduces

* Support for Google Fonts
* The selected Google Fonts CSS is automatically enqueued on front-end of posts/pages
* Plugin functions and shortcode to fetch properties

## Description
Typography field type for "Advanced Custom Fields" plugin that lets you add different text properties e.g. Font Size, Font Family, Font Color etc.
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
* Support for Google Fonts
* The selected Google Fonts CSS is automatically enqueued on front-end of posts/pages
* Option to show/hide each subfield individually
* Option to make each subfield required individually
* Color Picker for Text Color subfield

## Screenshots
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-1.png "Typography Sample Field Settings")
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-2.png "Typography Sample Field Content Editing")
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-3.png "Google Key Field required for Google Fonts")

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
#### Shortcode
`[acf_typography field="field-name" property="font_size" post_id="123" format_value="1"]`

## Compatibility

This ACF field type is compatible with:
* Free and paid versions of the ACF plugin

## Installation

- Download the plugin from [WordPress Repository](https://wordpress.org/plugins/acf-typography-field/) or use the [Latest Release](releases)
- Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`

## Limitations
- Google Fonts CSS gets enqueued only on the front-end of posts and pages.

## Changelog
See changelog on [CHANGELOG.md](CHANGELOG.md) file.