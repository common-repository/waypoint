<?php
namespace Waypoint\Core\Maps;

use Waypoint\Core\Google\Waypoint_Google_Settings;
use Waypoint\Core\Locations\Waypoint_Location_Post_Type;
use Waypoint\Core\Locations\Waypoint_Locations;
use Waypoint\Core\Taxonomies\Filters\Waypoint_Taxonomy_Map_Pin_Filter;
use Waypoint\Core\Taxonomies\Filters\Waypoint_Taxonomy_Order_Filter;

class Waypoint_Map_Preload
{
    /**
     * A model of the global Waypoint map configuration and styles. This object is exposed
     * to the global javascript scope as: window.waypoint.
     *
     * @return array
     */
	public function get_preload_model() {

		$model = [
			'google' => [
				'api_key' => Waypoint_Google_Settings::get_google_api_key()
			],

			'map_defaults' => [

                'height' => Waypoint_Map_Settings::get_map_height(),

                'style' => Waypoint_Map_Settings::get_map_style(),

                'map_pin_type' => Waypoint_Map_Settings::get_map_pin_graphic_type(),
                'map_pin_image_id' => Waypoint_Map_Settings::get_map_pin_graphic_image_id(),
                'map_pin_url' => wp_get_attachment_image_url( Waypoint_Map_Settings::get_map_pin_graphic_image_id() ),

                'desktop' => [
                    'lat' => Waypoint_Map_Settings::get_desktop_center_lat(),
                    'lng' => Waypoint_Map_Settings::get_desktop_center_lng(),
                    'zoom' => Waypoint_Map_Settings::get_desktop_center_zoom()
                ],

                'mobile' => [
                    'lat' => Waypoint_Map_Settings::get_mobile_center_lat(),
                    'lng' => Waypoint_Map_Settings::get_mobile_center_lng(),
                    'zoom' => Waypoint_Map_Settings::get_mobile_center_zoom()
                ]
			]
		];

		return apply_filters( 'waypoint_map_preload_model', $model );
	}

	public function script()
	{
		?>
		<script>
			window.waypoint = <?php echo json_encode( $this->get_preload_model() ); ?>;
		</script>
		<?php
	}

	public function get_map_by_id_json( $map_id, $grouping_taxonomy = null ) {

        $map = get_post( $map_id );

        // No post found.
        if ( empty( $map ) ) {
            return json_encode( [] );
        }

        // Post found but wrong post type.
        if ( Waypoint_Map_Post_Type::KEY !== $map->post_type ) {
            return json_encode( [] );
        }

        $location_query_type = get_post_meta( $map_id, '_location_query_type', true );

        $term_ids = get_post_meta( $map_id, '_selected_term_ids', true );
        $term_ids = explode( ',', $term_ids );

        $desktop_lat     = get_post_meta( $map_id, '_map_center_desktop_lat', true );
        $desktop_lng     = get_post_meta( $map_id, '_map_center_desktop_lng', true );
        $desktop_zoom    = get_post_meta( $map_id, '_map_center_desktop_zoom', true );
        $mobile_lat      = get_post_meta( $map_id, '_map_center_mobile_lat', true );
        $mobile_lng      = get_post_meta( $map_id, '_map_center_mobile_lng', true );
        $mobile_zoom     = get_post_meta( $map_id, '_map_center_mobile_zoom', true );
        $map_center_type = get_post_meta( $map_id, '_map_center_type', true );

        $locations = [];

        if ( 'All' === $location_query_type ) {
            $query = new \WP_Query( [
                'post_type'      => Waypoint_Location_Post_Type::KEY,
                'posts_per_page' => - 1,
            ] );

            if ( ! empty( $query->posts ) ) {
                $locations = $query->posts;
            }
        }

        if ( 'Taxonomy' === $location_query_type ) {

            $taxonomies = get_object_taxonomies( Waypoint_Location_Post_Type::KEY );

            $tax_queries = [];
            foreach ( $taxonomies as $taxonomy ) {
                $queries = [];
                foreach ( $term_ids as $id ) {
                    $term = get_term( $id );
                    if ( $term->taxonomy === $taxonomy ) {
                        $queries[] = [
                            'field'    => 'term_id',
                            'terms'    => $id,
                            'taxonomy' => $taxonomy
                        ];
                    }
                }
                if ( count( $queries ) > 1 ) {
                    $queries['relation'] = 'OR';
                }
                if ( ! empty( $queries ) ) {
                    $tax_queries[] = $queries;
                }
            }
            if ( count( $tax_queries ) > 1 ) {
                $tax_queries['relation'] = 'OR';
            }
            $query = new \WP_Query( [
                'post_type'      => Waypoint_Location_Post_Type::KEY,
                'posts_per_page' => - 1,
                'tax_query'      => $tax_queries
            ] );
        }

        if ( isset( $query ) && ! empty( $query->posts ) ) {
            $locations = array_map( function ( \WP_Post $post ) {
                return Waypoint_Locations::prepare_model( $post );
            }, $query->posts );
        }

        $location_taxonomies = get_object_taxonomies( Waypoint_Location_Post_Type::KEY );

        $locations = array_map( function ( \WP_Post $post ) use ( $map_id, $location_taxonomies ) {
            $post->meta                = Waypoint_Locations::get_meta( $post->ID );
            $post->info_window_content = apply_filters( 'waypoint_location_info_window_content', $post->post_title, $post, $map_id );
            $post->terms               = array_map( function ( \WP_Term $term ) {
                $term->sort_order = Waypoint_Taxonomy_Order_Filter::get_sort_order( $term->term_id );
                $image_id         = Waypoint_Taxonomy_Map_Pin_Filter::get_map_pin_id( $term->term_id );
                if ( ! empty( $image_id ) ) {
                    $image_url = wp_get_attachment_image_url( $image_id, 'map_pin' );
                } else {
                    $image_url = '';
                }
                $term->map_pin_url = $image_url;

                return $term;
            }, wp_get_object_terms( $post->ID, $location_taxonomies ) );

            return $post;
        }, $locations );

        $data = [
            'query_type'        => $location_query_type,
            'term_ids'          => $term_ids,
            'desktop'           => [
                'lat'  => $desktop_lat,
                'lng'  => $desktop_lng,
                'zoom' => $desktop_zoom
            ],
            'mobile'            => [
                'lat'  => $mobile_lat,
                'lng'  => $mobile_lng,
                'zoom' => $mobile_zoom
            ],
            'locations'         => $locations,
            'map_center_type'   => $map_center_type,
            'grouping_taxonomy' => $grouping_taxonomy
        ];

        $data = apply_filters( 'waypoint_map_data_json', $data, $map_id );

		return json_encode( $data );
	}

	public function __construct() {
		add_action( 'wp_head', [$this, 'script'] );
	}
}