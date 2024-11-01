<?php
namespace Waypoint\Core\Taxonomies;

use Waypoint\Core\Waypoint_Component_Abstract;

class Waypoint_Taxonomies_Component extends Waypoint_Component_Abstract
{
	public function load() {
		require_once __DIR__ . '/filters/waypoint-taxonomy-order-filter.php';
		require_once __DIR__ . '/filters/waypoint-taxonomy-map-pin-filter.php';
	}

	public function register() {
		$this->load();
	}
}