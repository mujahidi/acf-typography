# CHANGELOG

##### 3.3.0
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
