# CHANGELOG

##### 3.3.0
* Now requires WordPress 6.2+ and PHP 7.4+. Tested up to WordPress 7.1.
* [NEW] `ACFT_GOOGLE_API_KEY` constant to set the Google API key in `wp-config.php`.
* [NEW] Admins see a notice on ACF screens when Google Fonts can't be loaded.
* [BUG] Chosen font weights are now loaded (in addition to regular and bold). Web-safe fonts are no longer sent to Google Fonts.
* [BUG] Google Fonts now use `display=swap`.
* [BUG] Fixed warnings and errors on PHP 8.x (404, archive and search pages, ACF blocks without fields, nested blocks, required subfields). #29
* [BUG] No fatal error when ACF is inactive. Template functions and shortcode work on ACF 4.
* [BUG] No fatal error on PHP 8 when Display Properties is given as a single string (fields registered in PHP).
* [BUG] The Google Fonts list is now cached in the database instead of a file inside the plugin folder, and a bad key or network error no longer causes warnings. It is refreshed daily in the background instead of during page loads. After updating, the first admin page fetches it once. #27
* [BUG] A saved font missing from the list is kept instead of being replaced on save. This now also applies to a field group's default font.
* [BUG] A saved 0 (e.g. letter spacing) now counts as a value: it passes the required check, and the editor shows 0 instead of the default.
* [BUG] The Tahoma and Times New Roman choices had wrong values; field groups that use them as the default are corrected when loaded.
* [BUG] Field labels are now closed and linked to their inputs, and each field has its own input ids. Admin CSS or JS that targeted the old ids (e.g. `#acf-font_size`) should use `[data-name="font_size"]` instead.
* [BUG] In field group settings, Display Properties and Required Properties are listed one per row, so no checkbox is cut off on laptop, tablet or phone screens.
* [BUG] Settings page: the Google Fonts Key field is labelled for screen readers and the title is the page heading. The page now says when no key is saved (only web-safe fonts are offered) and when a failed fetch means the last good font list is still in use.
* Security: escaped output and sanitized settings and shortcode attributes.
* Security: values are cleaned when saved, including in ACF blocks: HTML tags and the characters `; : { } \` are removed, so a value cannot add extra CSS. Values saved before 3.3.0 are not changed.
* Security: the `[acf_typography]` shortcode now only reads Typography fields, and a field key only where that field is saved. Before, a Contributor could use it to read other stored data.
* Text domain is now `acf-typography-field`, so the plugin can be translated on WordPress.org. Changes made through the old `acf-typography` text domain keep working until 4.0. #24
* Deleting the plugin now removes its settings (including the saved Google API key) and the cached font list.
* Deprecated: the `YOUR_API_KEY` constant still works but will be removed in 4.0. Use `ACFT_GOOGLE_API_KEY`.
* Deprecated: `acft_update_gf_json_file()`. Use `acft_refresh_google_fonts()`. The font list is stored in the `acft_google_fonts` option; `google_fonts.json` has been removed.
* Developers: `acf_field_Typography::$font_family` gets the Google fonts only when a field is shown. Call `load_font_family()` first if your code reads it.

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
