<?php

namespace Waypoint\Core\Logger;

use Waypoint\Core\Logger\Logs\Waypoint_Logger_Debug_Log;
use Waypoint\Core\Waypoint_Component_Abstract;

class Waypoint_Logger_Component extends Waypoint_Component_Abstract {

	public function load() {
		require_once __DIR__ . '/waypoint-logger-admin-settings-page.php';
		require_once __DIR__ . '/waypoint-logger-settings.php';

		// Container
		require_once __DIR__ . '/waypoint-logger-container.php';
		require_once __DIR__ . '/waypoint-logger.php';

		// Logs
		require_once __DIR__ . '/logs/waypoint-logger-abstract.php';
		require_once __DIR__ . '/logs/waypoint-logger-debug-log.php';
	}

	public function register() {
		$this->load();
		new Waypoint_Logger_Admin_Settings;

		// Register debug log.
		Waypoint_Logger_Debug_Log::register();
	}

	public function enqueue_admin_assets() {
	}
}