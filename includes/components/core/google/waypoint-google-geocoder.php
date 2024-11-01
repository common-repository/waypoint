<?php
namespace Waypoint\Core\Google;

use Waypoint\Core\Locations\Waypoint_Location_Post_Type;
use Waypoint\Core\Logger\Waypoint_Logger;

/**
 * Class Waypoint_Google_Geocoder
 *
 * @package Waypoint\Core\Google
 */
class Waypoint_Google_Geocoder
{
	/**
	 * @param $address_components array
	 *
	 * @return array
	 */
	public function parse_address_components( $address_components )
	{
		$output = [];

		foreach( $address_components as $component ) {

			// Combine the types array into a single string.
			$types = implode( ',', $component['types'] );

			// Map types to address elements.
			switch( $types ) {

				case 'street_number':
					$output['street_number'] = $component['long_name'];
					break;

				case 'route':
					$output['route'] = $component['long_name'];
					break;

				case 'locality,political':
					$output['city'] = $component['long_name'];
					break;

				case 'administrative_area_level_2,political':
					$output['county'] = $component['long_name'];
					break;

				case 'administrative_area_level_1,political':
					$output['state_abbreviation'] = $component['short_name'];
					$output['state'] = $component['long_name'];
					break;

				case 'country,political':
					$output['country_long'] = $component['long_name'];
					$output['country_short'] = $component['short_name'];
					break;

				case 'postal_code':
					$output['zip_code'] = $component['long_name'];
					break;
			}
		}

		// Combine street number and street name.
		if ( isset( $output['street_number'] ) && isset( $output['route'] ) ) {
			$output['street_address'] = $output['street_number'] . ' ' . $output['route'];
		}

		return $output;
	}

	/**
	 * @param $address string An address to geocode.
	 *
	 * @return array|bool
	 */
	public function geocode_address( $address )
	{
	    $logs = ['Attempting to geocode address: "' . $address . '"'];

	    $disable_transients = Waypoint_Google_Settings::get_disable_transients();
	    $transient_lifetime = Waypoint_Google_Settings::get_transient_lifetime();

		$address = trim( $address );
		$address = urlencode( $address );
		$hash = md5( $address );
		$transient_key = 'waypoint_geocoder_request_' . $hash;

		$api_key = Waypoint_Google_Settings::get_google_api_key();
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$api_key}";

		if ( ! $disable_transients ) {
            // Get request from transient.
            $result = get_transient( $transient_key );

            if ( ! $result ) {
                $result = wp_remote_get( $url );
                set_transient( $transient_key, $result, $transient_lifetime );
            }
        } else {
            $result = wp_remote_get( $url );
        }

		if ( 200 != $result['response']['code'] ) {
		    $logs[] = 'Invalid response code: ' . $result['response']['code'];
		    $logs[] = 'Result:';
		    $logs[] = $result;
            Waypoint_Logger::add( $logs );
			return false;
		}

		$body = $result['body'];
		$data = json_decode( $body, true );

		if ( empty( $data['results'] ) ) {
            $logs[] = 'Result:';
            $logs[] = $result;
            Waypoint_Logger::add( $logs );
			delete_transient( $transient_key );
			return false;
		}

		// Reference results.
		$data = $data['results'][0];

		// Parse address components.
		$address_components = $data['address_components'];
		$address_components = $this->parse_address_components( $address_components );

		$geometry = $data['geometry'];

		$output = [
			'lat' => $geometry['location']['lat'],
			'lng' => $geometry['location']['lng'],
			'formatted' => $data['formatted_address']
		];

		$output = array_merge( $address_components, $output );

		return $output;
	}

	/**
	 * Get location posts with missing or no lat/lng data.
	 * @return \WP_Post[]
	 */
	public function get_all_ungeocoded_locations()
	{
		$query = new \WP_Query([
			'post_type' => Waypoint_Location_Post_Type::KEY,
			'post_status' => 'publish',
			'posts_per_page' => -1
		]);

		$posts = array_map( function( \WP_Post $post ) {
			$post->lat = get_post_meta( $post->ID, '_address_lat', true );
			$post->lng = get_post_meta( $post->ID, '_address_lng', true );
			return $post;
		}, $query->posts );

		$posts = array_filter( $posts, function( \WP_Post $post ) {
			return empty( $post->lat ) || empty( $post->lng );
		});

		return $posts;
	}
}