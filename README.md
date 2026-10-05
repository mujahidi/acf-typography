# Advanced Custom Fields: Typography Field

A Typography Add-on for the Advanced Custom Fields Plugin.

  - Requires at least: WP 6.2
  - Tested up to: WP 7.1
  - Requires PHP: 7.4
  - Stable: 3.3.0
  - Latest: 3.3.0

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
* Supports Google Fonts. The selected Google Fonts are automatically enqueued on front-end of posts/pages. Google Fonts also work with ACF Options.
* Supports Gutenberg Blocks created with ACF.
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

The shortcode only reads Typography fields.

## Compatibility

This ACF field type is compatible with:
* Free and paid versions of the ACF plugin

## Installation

- Download the plugin from [WordPress Repository](https://wordpress.org/plugins/acf-typography-field/) or use the latest release from this repository.
- Google API Key is required for Google Fonts. Please add one by going to `WordPress Admin Dashboard > Settings > ACF Typography Settings`
- Or define the key in `wp-config.php` (this overrides the settings page):
  ```php
  define( 'ACFT_GOOGLE_API_KEY', 'your-key' );
  ```
  The old `YOUR_API_KEY` constant still works but is deprecated and will be removed in 4.0.

## Changelog
See changelog on [CHANGELOG.md](CHANGELOG.md) file.