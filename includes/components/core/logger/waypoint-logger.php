<?php
namespace Waypoint\Core\Logger;

use Waypoint\Core\Google\Waypoint_Logger_Settings;

class Waypoint_Logger
{
	/**
	 * @param $content
	 * @param string $log_key
	 *
	 * @return bool
	 */
	public static function add( $content, $log_key = 'debug' ) {

		if ( ! Waypoint_Logger_Settings::enabled() ) {
//			return;
		}

		$log = Waypoint_Logger_Container::get_by_key( $log_key );

		if ( $log ) {
			$log->add( $content );
			return true;
		}

		return false;
	}
}