<?php
/**
 * Plugin Name: Waypoint
 * Plugin URI:
 * Description: Add locations and Google Maps integration to WordPress.
 * Author: Sideways8 Interactive, LLC
 * Version: 1.0.0
 * Author URI: https://sideways8.com/
 * Copyright: (c) 2018 Sideways8 Interactive, LLC
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: waypoint
*/

/** Absolute path to Waypoint plugin directory (with trailing slash). */
define( 'WAYPOINT_PLUGIN_DIR', trailingslashit( __DIR__ ) );

/** Public relative URL to Waypoint plugin directory (with trailing slash). */
define( 'WAYPOINT_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

// Load plugin bootstrapper.
require_once __DIR__ . '/includes/bootstrapper.php';

