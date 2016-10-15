# Advanced Custom Fields: Typography Field

A Typography Add-on for the Advanced Custom Fields Plugin.

  - Requires at least: WP 3.5.0
  - Tested up to: WP 4.6.1
  - Stable: 1.0.0
  - License: GPLv2 or later
  - License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description
Typography field type for "Advanced Custom Fields" plugin that lets you add different text properties e.g. Font Size, Font Family, Font Color etc.
### Supported Subfields
* Font Size
* Font Family
* Font Weight
* Font Style
* Line Height
* Letter Spacing
* Text Align
* Text Color
* Text Decoration

### Other Features
* Option to show/hide each subfield individually
* Option to make each subfield required individually
* Color Picker for Text Color subfield

### Sample Output for Theme Developers
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
)
`
### Compatibility

This ACF field type is compatible with:
* ACF 4

### Installation

1. Copy the `acf-typography` folder into your `wp-content/plugins` folder
2. Activate the Typography plugin via the plugins admin page
3. Create a new field via ACF and select the Typography type
4. Please refer to the description for more info regarding the field type settings