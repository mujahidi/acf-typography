<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

/*
 * 
 * This file exists as a placeholder for the call for 
 * include_field_types from acf-typography.php to register
 * the fields for this plugin, if/when ACF will utilize it.
 * 
 * ACF v5 fields currently work fine for v6 (with only two bugs 
 * related to number fields and color_picker), so until there's 
 * actually a reason to do something different for v6, we just
 * use the existing v5 fields file and keep this file here to prevent
 * surprise plugin errors if/when ACF makes changes in the future.
 * 
 * Both Bugs are UI-related and have been reported to ACF for patching.
 * 
 */

require_once plugin_dir_path( __FILE__ ) . 'acf-Typography-v5.php';
