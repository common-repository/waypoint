<?php
namespace Waypoint\Core\Locations;

use Waypoint\Core\Waypoint_Component_Abstract;

/**
 * Class Waypoint_Location_Component
 *
 * @package Waypoint\Core\Locations
 */
class Waypoint_Location_Component extends Waypoint_Component_Abstract
{
	public function load()
	{
		require_once __DIR__ . '/waypoint-location-details-meta-box.php';
		require_once __DIR__ . '/waypoint-location-map-options-meta-box.php';
		require_once __DIR__ . '/waypoint-location-geocoder.php';
		require_once __DIR__ . '/waypoint-location-post-type.php';
		require_once __DIR__ . '/waypoint-location-taxonomy-category.php';
        require_once __DIR__ . '/waypoint-locations.php';
	}

	public function register() {
		$this->load();
		new Waypoint_Location_Post_Type;
		new Waypoint_Location_Details_Meta_Box;
		new Waypoint_Location_Geocoder;
		new Waypoint_Location_Map_Options_Meta_Box;
		new Waypoint_Location_Category_Taxonomy;
	}
}