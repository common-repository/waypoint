<?php

namespace Waypoint\Core\Logger;

class Waypoint_Logger_Container {
	private function __construct() { }

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * @return Waypoint_Logger_Container
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * @var Waypoint_Logger_Abstract[]
	 */
	protected $loggers = [];

	/**
	 * Register/add a logger.
	 *
	 * @param Waypoint_Logger_Abstract $logger
	 */
	public function register_logger( Waypoint_Logger_Abstract $logger ) {

		// Don't add duplicate loggers.
		foreach ( $this->loggers as $existing_logger ) {
			if ( $logger::KEY == $existing_logger::KEY ) {
				return;
			}
		}

		// Add the logger.
		$this->loggers[] = $logger;
	}

	/**
	 * @return Waypoint_Logger_Abstract[]
	 */
	public function get_loggers() {
		return $this->loggers;
	}

	/**
	 * @param $log_key
	 *
	 * @return bool|Waypoint_Logger_Abstract
	 */
	public static function get_by_key( $log_key ) {
		foreach( static::get_instance()->get_loggers() as $logger ) {
			if ( strtolower( $log_key ) == $logger->get_key() ) {
				return $logger;
			}
		}
		return false;
	}
}