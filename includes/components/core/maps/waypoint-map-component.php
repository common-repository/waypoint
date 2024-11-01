<?php
namespace Waypoint\Core\Maps;

use Waypoint\Core\Waypoint_Component_Abstract;

class Waypoint_Map_Component extends Waypoint_Component_Abstract
{
	public function load() {
		require_once __DIR__ . '/waypoint-map-admin-settings.php';
		require_once __DIR__ . '/waypoint-map-options-meta-box.php';
		require_once __DIR__ . '/waypoint-map-post-type.php';
		require_once __DIR__ . '/waypoint-map-list-table-filters.php';
		require_once __DIR__ . '/waypoint-map-preload.php';
		require_once __DIR__ . '/waypoint-map-settings.php';
		require_once __DIR__ . '/waypoint-map-short-code.php';
		require_once __DIR__ . '/waypoint-map-info-window-template.php';
		require_once __DIR__ . '/waypoint-map-widget.php';
		require_once __DIR__ . '/waypoint-maps.php';
	}

	public function register() {
		$this->load();
		new Waypoint_Map_Admin_Settings;
		new Waypoint_Map_Post_Type;
		new Waypoint_Map_Options_Meta_Box;
		new Waypoint_Map_List_Table_Filters;
		new Waypoint_Map_Short_Code;
		new Waypoint_Map_Preload;
	}
}