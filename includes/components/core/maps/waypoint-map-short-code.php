<?php
namespace Waypoint\Core\Maps;

use Waypoint\Core\Google\Waypoint_Google_Geocoder;
use Waypoint\Core\Locations\Waypoint_Locations;

class Waypoint_Map_Short_Code
{
	const TAG = 'waypoint_map';

	/** @var int Cache time in seconds. */
	const CACHE_LIFETIME = 60;

    /**
     * @var array Short code attributes.
     */
	protected $atts = [
        'id'      => 0,
        'lat'     => '',
        'lng'     => '',
        'address' => '',
        'content' => ''
    ];

	public function reset() {
	    $this->atts = [
            'id'      => 0,
            'lat'     => '',
            'lng'     => '',
            'address' => '',
            'content' => ''
        ];
    }

	public function __construct() {
		add_shortcode( static::TAG, [$this, 'render'] );
		add_filter( 'waypoint_location_info_window_content', [$this, 'filter_info_window_content'], 10, 3 );
	}

	public function filter_info_window_content( $content, \WP_Post $post, $map_id ) {

	    // For non-numeric (ie, location hash) map IDs, do not filter.
	    if ( ! is_numeric( $map_id ) ) {
	        return $content;
        }

	    // Set meta if not present.
	    if ( ! isset( $post->meta ) ) {
	        $post->meta = Waypoint_Locations::get_meta( $post->ID );
        }

		// Get the template.
		$template = Waypoint_Map_Settings::get_info_window_template();

        if ( ! empty( $map_id ) ) {
	        $map_info_window_type = get_post_meta( $map_id, '_info_window_template_type', true );
	        $map_info_window_template = get_post_meta( $map_id, '_info_window_template', true );
	        if ( 'Custom' === $map_info_window_type ) {
	            $template = $map_info_window_template;
            }
        }

	    // Prepare replacements.
        $templater = Waypoint_Map_Info_Window_Template::get_instance();
	    $variables = $templater->prepare_variables( $post );
	    $rendered = $templater->replace_variables( $template, $variables );

	    return $rendered;
    }

    /** Enqueue assets for Waypoint frontend code. */
    public function enqueue_scripts() {
        wp_enqueue_script( 'underscore' );
        wp_enqueue_script( 'waypoint-frontend', WAYPOINT_PLUGIN_URL . 'frontend/dist/build.js', ['jquery'], false, true );
    }

    /**
     * Short code render callback.
     * @param array $atts The short code attributes.
     * @return string The rendered short code HTML.
     */
	public function render( $atts ) {

		if ( isset( $_GET['fl_builder'] ) ) {
			return 'Map view disabled while editing layout.';
		}

		// Enqueue assets for short code.
		$this->enqueue_scripts();

	    // Parse short code attributes.
	    $this->atts = wp_parse_args( $atts, $this->atts );

	    // Buffer output.
	    ob_start();

	    // If a map ID was specified in the short code attributes, render that map's configuration.
        // Otherwise, attempt to render by location attributes.
	    if ( ! empty( $this->atts['id'] ) ) {
	        $this->render_map_by_id();
        } else {
	        $this->render_map_by_location_data();
        }

        // Reset short code state.
        $this->reset();

        // Return HTML string.
        return ob_get_clean();
	}

    /**
     * @param int $map_id A valid map post ID.
     * @param bool $clear_cache If true, the existing transient will be deleted.
     *
     * @return string
     */
	public function get_map_json_by_id( $map_id, $clear_cache = false ) {

        // Get JSON model.
        $preload = new Waypoint_Map_Preload;

        // Transient key for this map ID.
        $transient_key = 'waypoint_map_' . $map_id;

        if ( $clear_cache ) {
            delete_transient( $transient_key );
        }

        // Cache map json model.
        if ( false === $json = get_transient( $transient_key ) ) {
            $json = $preload->get_map_by_id_json( $map_id );
            set_transient( $map_id, $json, static::CACHE_LIFETIME );
        }

        return $json;
    }

    /**
     * Renders a specific map configuration.
     */
	public function render_map_by_id() {

        // Reference map ID.
        $map_id = (int) $this->atts['id'];

        $json = $this->get_map_json_by_id( $map_id );

        ?>
        <script>
            window.waypoint_map_<?php echo (int) $map_id; ?> = <?php echo $json; ?>;
        </script>
        <?php

        $this->print_map( $map_id );
    }

    /**
     * Renders a map based on specified location data.
     */
    public function render_map_by_location_data() {

        // Validate location input data.
        $lat = (float) $this->atts['lat'];
        $lng = (float) $this->atts['lng'];
        $address = (string) $this->atts['address'];
        $address = trim( $address );

        // Hash attributes and prepare transient key.
        $hash = md5( json_encode( $this->atts ) );
        $hash = substr( $hash, 0, 6 );
        $transient_key = 'geocode_' . $hash;

        // No data specified, so do nothing.
        if ( empty( $lat ) && empty( $lng ) && empty( $address ) ) {
            echo 'Cannot render Waypoint Map. Please input either <strong>lat</strong> and <strong>lng</strong> or an <strong>address</strong>.';
            return;
        }

        // If both LAT and LNG were provided, no geocode lookup is necessary.
        if ( ! empty( $lat ) && ! empty( $lng ) ) {
            $locations = [
                [
                    'lat' => $lat,
                    'lng' => $lng,
                    'content' => $this->atts['content']
                ]
            ];
        } else {
            // Geocode address.
            $geo = new Waypoint_Google_Geocoder();
            if ( false === $data = get_transient( $transient_key ) ) {
                $response = $geo->geocode_address( $address );
                if ( false !== $response ) {
                    $data = $response;
                    set_transient( $transient_key, $data, 3600 );
                }
            }

            if ( ! empty( $data ) ) {
                $locations = [];
                $data = [$data];
                foreach( $data as $datum ) {
                    $content = $this->atts['content'];
                    if ( empty( $content ) ) {
                        $content = $datum['formatted'];
                    }
                    $locations[] = [
                        'lat' => $datum['lat'],
                        'lng' => $datum['lng'],
                        'content' => $datum['formatted']
                    ];
                }
            }
        }

        // Generate a map ID hash based on attributes.
        $map_id = md5( json_encode( $this->atts ) );

        // Prepare locations array.
        $map_data = [
            'locations' => $this->simulate_location_posts( $locations )
        ];

        ?>
        <script>window.waypoint_map_<?php echo $map_id; ?> = <?php echo json_encode( $map_data ); ?>;</script>

        <?php
        $this->print_map( $map_id );
	}

    /**
     * The front end expects locations to be in a WP Post format with embedded meta properties.
     * This function converts simple location arrays into fake posts suitable for the maps model.
     * @param array $locations [ ['lat' => '', 'lng' => '', 'content' => ''], ... ]
     * @return array
     */
	public function simulate_location_posts( $locations = [] ) {
        return array_map( function( $location ) {
            $class = new \stdClass;
            $fake_post = new \WP_Post( $class );
            $fake_meta = [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'map_pin_type' => 'default',
                'map_specific_pins' => [],
            ];
            $fake_post->meta = $fake_meta;
            $fake_post->info_window_content = $location['content'];
            return $fake_post;
        }, $locations );
    }

    /**
     * Print the vue.js Waypoint wrapper HTML.
     *
     * @param string $map_id
     */
	public function print_map( $map_id = '' ) {

	    // If the supplied map ID is not an integer (for example, a single location hash ID),
        // encode the value so that it can be passed to vue.js.
        $original_map_id = $map_id;
	    if ( ! is_numeric( $map_id ) ) {
	        $map_id = json_encode( $map_id );
        }
        ?>
        <style>
            div.gm-style p {
                padding-bottom: 0;
                margin-bottom: 0;
            }
        </style>
        <div class="waypoint-map-container" id="map-<?= esc_attr( $original_map_id ); ?>">
            <waypoint-map :map-id="<?php echo esc_attr( $map_id ); ?>"></waypoint-map>
        </div>
        <?php
    }
}