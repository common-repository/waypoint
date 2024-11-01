<?php
namespace Waypoint\Core\Locations;

class Waypoint_Locations {

    public static function get_meta( $post_id ) {
        $map_pin_type     = get_post_meta( $post_id, '_map_pin_type', true );
        $map_pin_image_id = get_post_meta( $post_id, '_map_pin_image_id', true );

        $map_pin_url = '';
        if ( ! empty( $map_pin_image_id ) && $map_pin_image_id > 0 ) {
            $map_pin_url = wp_get_attachment_image_url( $map_pin_image_id );
        }

        $meta = [
            'street_address' => get_post_meta( $post_id, '_address_street', true ),
            'city'           => get_post_meta( $post_id, '_address_city', true ),
            'state'          => get_post_meta( $post_id, '_address_state', true ),
            'zip_code'       => get_post_meta( $post_id, '_address_zip_code', true ),
            'country'        => get_post_meta( $post_id, '_address_country', true ),
            'lat'            => get_post_meta( $post_id, '_address_lat', true ),
            'lng'            => get_post_meta( $post_id, '_address_lng', true ),
            'map_pin_url'    => $map_pin_url,
            'map_pin_type'   => $map_pin_type,
            'map_specific_pins' => json_decode( get_post_meta( $post_id, '_map_specific_pins', true ) ),
        ];

        $street = $meta['street_address'];
        $city = $meta['city'];
        $state = $meta['state'];
        $zip = $meta['zip_code'];

        $meta['address_string'] = "$street $city, $state $zip";

        return apply_filters( 'waypoint_get_location_meta', $meta, $post_id );
    }

    public static function prepare_model( \WP_Post $post ) {
        $post->meta = get_post_meta( $post->ID );
        $post->post_content_formatted = apply_filters( 'the_content', $post->post_content );
        return $post;
    }

    public static function all( $args = [] ) {

        $args = wp_parse_args( $args, [
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => [],
        ]);

        $query = new \WP_Query([
            'post_type' => Waypoint_Location_Post_Type::KEY,
            'orderby' => $args['orderby'],
            'order' => $args['order'],
            'meta_query' => $args['meta_query'],
            'posts_per_page' => - 1
        ]);

        $posts = array_map( 'static::prepare_model', $query->posts );

        return $posts;
    }

    /**
     * Get all posts with a present latitude and longitude value specified.
     *
     * @param array $args
     *
     * @return \WP_Post[]
     */
    public static function get_all_with_geo_coordinates( $args = [] ) {

        $posts = static::all([
            'meta_query' => [
                'relation' => 'AND',
                // Lng exists
                [
                    'key'     => '_address_lat',
                    'compare' => 'EXISTS'
                ],
                // Lng not empty
                [
                    'key'     => '_address_lat',
                    'compare' => '!=',
                    'value'   => ''
                ],
                // Lng exists
                [
                    'key'     => '_address_lng',
                    'compare' => 'EXISTS'
                ],
                // Lng not empty
                [
                    'key'     => '_address_lng',
                    'compare' => '!=',
                    'value'   => ''
                ],
            ]
        ]);

        return $posts;
    }
}