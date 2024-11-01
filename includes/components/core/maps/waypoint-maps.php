<?php
namespace Waypoint\Core\Maps;

class Waypoint_Maps
{
    /**
     * @return \WP_Post[]
     */
    public static function all() {
        $query = new \WP_Query([
            'post_type' => Waypoint_Map_Post_Type::KEY,
            'orderby' => 'title',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);

        return $query->posts;
    }
}