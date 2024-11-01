<?php
namespace Waypoint\Core\Google;

class Waypoint_Google_Settings
{
    /**
     * @return mixed
     */
	public static function get_google_api_key() {
		return get_option( '_waypoint_settings_google_api_key' );
	}

	public static function get_transient_lifetime() {
	    return get_option( '_waypoint_settings_google_transient_lifetime', 3600 );
    }

    public static function get_disable_transients() {
	    return 1 == get_option( '_waypoint_settings_google_disable_transients', false );
	}
}