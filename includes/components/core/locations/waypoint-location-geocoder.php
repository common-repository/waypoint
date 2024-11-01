<?php
namespace Waypoint\Core\Locations;
use Waypoint\Core\Google\Waypoint_Google_Geocoder;

/**
 * Class Waypoint_Location_Geocoder
 *
 * @package Waypoint\Core\Locations
 */
class Waypoint_Location_Geocoder
{
	const ENDPOINT = 'waypoint_geocode_address';

	public static function get_url()
	{
		return admin_url() . 'admin-ajax.php?action=' . static::ENDPOINT;
	}

	public function __construct()
	{
		$endpoint = static::ENDPOINT;
		add_action( 'wp_ajax_' . $endpoint, [$this, 'geocode_address'] );
	}

	public function geocode_address()
	{
		$post_id = $_POST['post_id'];
		$address_string = $_POST['address'];
		$geocoder = new Waypoint_Google_Geocoder;
		$result = $geocoder->geocode_address( $address_string );

		if ( empty( $result ) ) {
			wp_send_json_error( 'No results.', 400 );
		}

		if ( isset( $result['street_address'] ) ) {
			update_post_meta( $post_id, '_address_street', $result['street_address'] );
		}
		if ( isset( $result['city'] ) ) {
			update_post_meta( $post_id, '_address_city', $result['city'] );
		}
		if ( isset( $result['state'] ) ) {
			update_post_meta( $post_id, '_address_state', $result['state_abbreviation'] );
		}
		if ( isset( $result['zip_code'] ) ) {
			update_post_meta( $post_id, '_address_zip_code', $result['zip_code'] );
		}
		if ( isset( $result['country_long'] ) ) {
			update_post_meta( $post_id, '_address_country', $result['country_long'] );
		}
		if ( isset( $result['lat'] ) ) {
			update_post_meta( $post_id, '_address_lat', $result['lat'] );
		}
		if ( isset( $result['lng'] ) ) {
			update_post_meta( $post_id, '_address_lng', $result['lng'] );
		}

		// Update post meta.
		wp_send_json( $result );
	}
}