# CHANGELOG

##### 3.2.4
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
