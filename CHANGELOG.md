# CHANGELOG

##### 3.2.4 (Unreleased)
* [SECURITY] Fixed XSS vulnerabilities in field rendering (proper output escaping)
* [SECURITY] Added input sanitization for Google Fonts API key
* [SECURITY] Added nonce verification and capability checks in admin settings
* [IMPROVEMENT] Updated to use wp_remote_get() instead of file_get_contents() for external requests
* [IMPROVEMENT] Added proper PHPDoc blocks for all functions and classes
* [IMPROVEMENT] Improved error handling for file operations
* [IMPROVEMENT] Updated code to follow WordPress Coding Standards
* [IMPROVEMENT] Optimized Google Fonts enqueuing to prevent errors
* [IMPROVEMENT] Replaced deprecated extract() function in shortcode
* [FIX] Fixed missing semicolon syntax error in admin settings
* [COMPATIBILITY] Updated WordPress compatibility to 6.7
* [COMPATIBILITY] Updated minimum WordPress version to 5.0
* [COMPATIBILITY] Added PHP 7.0 minimum requirement

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
