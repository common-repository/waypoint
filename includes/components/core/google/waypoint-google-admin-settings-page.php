<?php

namespace Waypoint\Core\Google;

use Waypoint\Core\Administration\Waypoint_Settings_Page_Abstract;
use Waypoint\Core\Locations\Waypoint_Location_Query;
use Waypoint\Core\Locations\Waypoint_Locations;
use Waypoint\Core\Logger\Waypoint_Logger;

class Waypoint_Google_Admin_Settings extends Waypoint_Settings_Page_Abstract {

	const KEY = '_google_settings';

	const LABEL = 'Google';

	public function render() {

		$api_key = Waypoint_Google_Settings::get_google_api_key();
		$transient_lifetime = Waypoint_Google_Settings::get_transient_lifetime();
		$disable_transients = Waypoint_Google_Settings::get_disable_transients();
		?>

        <table class="form-table">
            <tr>
                <th>API Key</th>
                <td>
                    <input type="text" class="widefat" name="_waypoint_settings_google_api_key"
                           value="<?php echo esc_attr( $api_key ); ?>">
                    <p>
                        <i>
                            Learn how to get your Google Maps API key
                            <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a>.
                        </i>
                    </p>
                </td>
            </tr>

            <tr>
                <th>Transient Lifetime</th>
                <td>
                    <input type="number" name="_waypoint_settings_google_transient_lifetime" value="<?= esc_attr( $transient_lifetime ); ?>">
                    <p>
                        <i>How long, in seconds, should successful google results be cached?</i>
                    </p>
                </td>
            </tr>

            <tr>
                <th>Disable Transients</th>
                <td>
                    <input type="checkbox" name="_waypoint_settings_google_disable_transients" value="1" <?= $disable_transients ? 'checked="checked"' : ''; ?>>
                    <p>
                        <i>If checked, results will not be cached. This option will consume more Google API requests, but may be helpful for debugging.</i>
                    </p>
                </td>
            </tr>

            <tr>
                <th>Geocode Addresses</th>
                <td>
                    <input type="checkbox" name="_geocode_addresses">
                    <p>
                        <i>
							<?php
							$ungeocoded = new Waypoint_Google_Geocoder();
							$ungeocoded = $ungeocoded->get_all_ungeocoded_locations();
							?>
                            There are <?php echo count( $ungeocoded ); ?> locations with no lat/lng.
                        </i>
                    </p>
                </td>
            </tr>
        </table>

        <button type="submit" class="button button-primary">Save</button>
		<?php
	}

	public function geocode_addresses() {

        $fail       = 0;
        $success    = 0;
        $geocoder   = new Waypoint_Google_Geocoder();
        $ungeocoded = $geocoder->get_all_ungeocoded_locations();

        $logs = [
            'task'        => 'Geocoding locations with no lat or lng data.',
            'items_found' => count( $ungeocoded )
        ];

        $logs['results'] = [];

        foreach ( $ungeocoded as $post ) {

            // Group this post under its own array.
            $log_result = [];

            // Reference post ID.
            $post_id = $post->ID;

            // Get address meta.
            $meta     = Waypoint_Locations::get_meta( $post->ID );
            $street   = $meta['street_address'];
            $city     = $meta['city'];
            $state    = $meta['state'];
            $zip_code = $meta['zip_code'];

            // Combine address into string.
            $address = "$street $city, $state $zip_code";

            $log_result[] = 'Attempting to geocode address: \'' . $address . '\'';
            $log_result[] = 'Post ID: ' . $post->ID;

            $result = $geocoder->geocode_address( $address );

            if ( empty( $result ) ) {
                $log_result[]      = 'FAIL';
                $logs['results'][] = $log_result;
                $fail ++;
                continue;
            } else {
                $log_result[]      = 'SUCCESS';
                $log_result[]      = $result;
                $logs['results'][] = $log_result;
                $success ++;
            }

            $keys = [
                '_address_street'   => 'street_address',
                '_address_city'     => 'city',
                '_address_state'    => 'state_abbreviation',
                '_address_zip_code' => 'zip_code',
                '_address_country'  => 'country_long',
                '_address_lat'      => 'lat',
                '_address_lng'      => 'lng'
            ];

            foreach ( $keys as $meta_key => $key ) {
                if ( isset( $result[$key] ) ) {
                    update_post_meta( $post_id, $meta_key, $result[$key] );
                }
            }
        }

        $logs['totals'] = [
            'failed'  => $fail,
            'success' => $success
        ];

        Waypoint_Logger::add( $logs );

        if ( $success > 0 ) {
            $this->add_success_message( "Geocoded $success locations." );
        }

        if ( $fail > 0 ) {
            $this->add_error_message( "Unable to geocode $fail items." );
        }
    }
	public function save() {

		$keys = [
			'_waypoint_settings_google_api_key',
            '_waypoint_settings_google_transient_lifetime'
		];

		foreach ( $keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = filter_var( $_POST[ $key ], FILTER_SANITIZE_STRING );
				update_option( $key, $value );
			}
		}

		$checkbox_keys = [
            '_waypoint_settings_google_disable_transients'
        ];

		foreach( $checkbox_keys as $key ) {
		    if ( isset( $_POST[$key] ) ) {
                $value = filter_var( $_POST[$key], FILTER_SANITIZE_STRING );
                update_option( $key, $value );
            } else {
		        delete_option( $key );
            }
        }

		// Show message.
        $this->add_success_message( 'Google settings updated.' );

		// Geocode addresses.
        if ( isset( $_POST['_geocode_addresses'] ) ) {
            $this->geocode_addresses();
        }
	}
}