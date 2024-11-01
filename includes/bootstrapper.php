<?php
// Core
require_once __DIR__ . '/components/core/waypoint-component-abstract.php';
require_once __DIR__ . '/components/core/waypoint-plugin.php';

// Components
require_once __DIR__ . '/components/core/admin/waypoint-administration-component.php';
require_once __DIR__ . '/components/core/google/waypoint-google-component.php';
require_once __DIR__ . '/components/core/locations/waypoint-location-component.php';
require_once __DIR__ . '/components/core/maps/waypoint-map-component.php';
require_once __DIR__ . '/components/core/logger/waypoint-logger-component.php';
require_once __DIR__ . '/components/core/taxonomies/waypoint-taxonomies-component.php';

// Initialize plugin.
\Waypoint\Core\Waypoint_Plugin::get_instance()->initialize();

// An array of core Waypoint components.
$core_components = [
	\Waypoint\Core\Administration\Waypoint_Administration_Component::class,
	\Waypoint\Core\Locations\Waypoint_Location_Component::class,
	\Waypoint\Core\Google\Waypoint_Google_Component::class,
	\Waypoint\Core\Maps\Waypoint_Map_Component::class,
	\Waypoint\Core\Logger\Waypoint_Logger_Component::class,
	\Waypoint\Core\Taxonomies\Waypoint_Taxonomies_Component::class,
];

foreach( $core_components as $component ) {
	\Waypoint\Core\Waypoint_Plugin::get_instance()->register_component( $component );
}
