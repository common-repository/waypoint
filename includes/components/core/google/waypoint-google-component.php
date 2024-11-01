<?php
namespace Waypoint\Core\Google;

use Waypoint\Core\Waypoint_Component_Abstract;

class Waypoint_Google_Component extends Waypoint_Component_Abstract
{
	public function load() {
		require_once __DIR__ . '/waypoint-google-admin-settings-page.php';
		require_once __DIR__ . '/waypoint-google-geocoder.php';
		require_once __DIR__ . '/waypoint-google-settings.php';
	}

	public function register() {
		$this->load();

		new Waypoint_Google_Admin_Settings;
	}
}