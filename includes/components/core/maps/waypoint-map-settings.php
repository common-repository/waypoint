<?php
namespace Waypoint\Core\Maps;

class Waypoint_Map_Settings {

	public static function get_map_pin_graphic_type() {
		return get_option( '_waypoint_map_pin_type', 'default' );
	}

	public static function get_map_pin_graphic_image_id() {
		return get_option( '_waypoint_map_pin_image_id', 0 );
	}

	public static function get_map_height() {
		return get_option( '_waypoint_map_height', '400' );
	}

	public static function get_map_style() {
		$option = get_option( '_waypoint_map_default_style', '[]' );
		return $option;
	}

	public static function get_desktop_center_lat() {
		return get_option( '_waypoint_map_desktop_center_lat', 0 );
	}

	public static function get_desktop_center_lng() {
		return get_option( '_waypoint_map_desktop_center_lng', 0 );
	}

	public static function get_desktop_center_zoom() {
		return get_option( '_waypoint_map_desktop_center_zoom', 2 );
	}

	public static function get_mobile_center_lat() {
		return get_option( '_waypoint_map_mobile_center_lat', 0 );
	}

	public static function get_mobile_center_lng() {
		return get_option( '_waypoint_map_mobile_center_lng', 0 );
	}

	public static function get_mobile_center_zoom() {
		return get_option( '_waypoint_map_mobile_center_zoom', 0 );
	}

	public static function get_info_window_template()
	{
		$default = "{post_title}\n{street_address}\n{city}, {state} {zip_code}\n{country}";
		return get_option( '_waypoint_map_info_window_template', $default );
	}
}