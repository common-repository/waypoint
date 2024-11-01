<?php
namespace Waypoint\Core\Google;

class Waypoint_Logger_Settings
{
	/**
	 * Is the logger enabled?
	 * @return bool
	 */
	public static function enabled()
	{
		return 1 == get_option( '_waypoint_settings_logger_enabled' );
	}
}