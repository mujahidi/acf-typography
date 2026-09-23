# CHANGELOG

##### 3.3.0
* Now requires WordPress 6.2+ and PHP 7.4+. Tested up to WordPress 7.1.
* [NEW] `ACFT_GOOGLE_API_KEY` constant to set the Google API key in `wp-config.php`.
* [NEW] Admins see a notice on ACF screens when Google Fonts can't be loaded.
* [BUG] Chosen font weights are now loaded (in addition to regular and bold). Web-safe fonts are no longer sent to Google Fonts.
* [BUG] Google Fonts now use `display=swap`.
* [BUG] Fixed warnings and errors on PHP 8.x (404, archive and search pages, ACF blocks without fields, nested blocks, required subfields). #29
* [BUG] No fatal error when ACF is inactive. Template functions and shortcode work on ACF 4.
* [BUG] The Google Fonts list is now cached in the database instead of a file inside the plugin folder, and a bad key or network error no longer causes warnings. #27
* [BUG] A saved font missing from the list is kept instead of being replaced on save.
* Security: escaped output and sanitized settings and shortcode attributes.
* Security: the `[acf_typography]` shortcode now only reads Typography fields. Before, a Contributor could use it to read other stored data.
* Text domain is now `acf-typography-field`, so the plugin can be translated on WordPress.org. Changes made through the old `acf-typography` text domain keep working until 4.0. #24
* Deleting the plugin now removes its settings (including the saved Google API key) and the cached font list.
* Deprecated: the `YOUR_API_KEY` constant still works but will be removed in 4.0. Use `ACFT_GOOGLE_API_KEY`.

##### 3.2.3
* Added new font-weight values

##### 3.2.2
* #23

##### 3.2.1
* Fixed typo
* Fixed a few bugs

##### 3.2.0
* [NEW] Added 'initial' and 'inherit' values for 'font-family' property. #19
* [NEW] Added support for ACF Blocks. #18
* [BUG] Fixed #17

##### 3.1.0
* [NEW] Google Fonts support for ACF Options
* [BUG] Different font weights were not enqueued when used in a repeater field

##### 3.0.0
* [NEW] Introduces functions and shortcode
* [NEW] Hides nonselected properties in fieldgroup edit page
* [NEW] Supports Google Fonts
* [NEW] Enqueue google fonts CSS on page/post

##### 2.2.0
* [NEW] Font Stretch subfield

##### 2.1.0
* [NEW] Font Variant subfield

##### 2.0.0
* [NEW] Now supports ACF 5 (Pro version)
* Added description to subfields

##### 1.1.1
* Fixed a bug which used to appear when none of the sub-field was made required.

##### 1.1.0
* [NEW] Text Transform subfield
* Added changelog file
* Added screenshots to README.md

##### 1.0.0
* Initial Release.
