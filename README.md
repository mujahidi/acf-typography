# Advanced Custom Fields: Typography Field

A Typography Add-on for the Advanced Custom Fields Plugin.

  - Requires at least: WP 3.5.0
  - Tested up to: WP 5.0.3
  - Stable: 2.2.0
  - License: GPLv2 or later
  - License URI: http://www.gnu.org/licenses/gpl-2.0.html

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
* Option to show/hide each subfield individually
* Option to make each subfield required individually
* Color Picker for Text Color subfield

### Screenshots
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-1.png "Typography Sample Field Settings")
![Typography Field Screenshot](https://raw.githubusercontent.com/mujahidi/typography/master/screenshot-2.png "Typography Sample Field Content Editing")

### For Developers
`
Array
(
    [font_size] => 18
    [font_family] => "MS Sans Serif", Geneva, sans-serif
    [font_weight] => 400
    [font_style] => italic
    [font_variant => small-caps
    [font_stretch] => expanded
    [line_height] => 1.5
    [letter_spacing] => 1
    [text_align] => center
    [text_color] => #ff0000
    [text_decoration] => underline
	[text_transform] => uppercase
)
`
```php
$getTypography = get_field('typography');
echo $getTypography['font_size'];
```
### Compatibility

This ACF field type is compatible with:
* ACF 4 (free version of ACF)
* ACF 5 (Pro)

### Installation

1. Copy the `acf-typography` folder into your `wp-content/plugins` folder
2. Activate the Typography plugin via the plugins admin page
3. Create a new field via ACF and select the Typography type
4. Please refer to the description for more info regarding the field type settings

### Changelog
See changelog on [CHANGELOG.md](CHANGELOG.md) file.