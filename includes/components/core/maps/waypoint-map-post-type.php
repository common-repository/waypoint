<?php
namespace Waypoint\Core\Maps;

/**
 * Class Waypoint_Map_Post_Type
 *
 * @package Waypoint\Core\Maps
 */
class Waypoint_Map_Post_Type
{
	const KEY = 'waypoint_map';

	public function get_labels()
	{
		$labels = [
			'name'                  => _x( 'Maps', 'Post Type General Name', 'waypoint' ),
			'singular_name'         => _x( 'Map', 'Post Type Singular Name', 'waypoint' ),
			'menu_name'             => __( 'Maps', 'waypoint' ),
			'name_admin_bar'        => __( 'Map', 'waypoint' ),
			'archives'              => __( 'Map Archives', 'waypoint' ),
			'attributes'            => __( 'Map Attributes', 'waypoint' ),
			'parent_item_colon'     => __( 'Parent Map:', 'waypoint' ),
			'all_items'             => __( 'All Maps', 'waypoint' ),
			'add_new_item'          => __( 'Add New Map', 'waypoint' ),
			'add_new'               => __( 'Add New', 'waypoint' ),
			'new_item'              => __( 'New Map', 'waypoint' ),
			'edit_item'             => __( 'Edit Map', 'waypoint' ),
			'update_item'           => __( 'Update Map', 'waypoint' ),
			'view_item'             => __( 'View Map', 'waypoint' ),
			'view_items'            => __( 'View Maps', 'waypoint' ),
			'search_items'          => __( 'Search Map', 'waypoint' ),
			'not_found'             => __( 'Not found', 'waypoint' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'waypoint' ),
			'featured_image'        => __( 'Featured Image', 'waypoint' ),
			'set_featured_image'    => __( 'Set featured image', 'waypoint' ),
			'remove_featured_image' => __( 'Remove featured image', 'waypoint' ),
			'use_featured_image'    => __( 'Use as featured image', 'waypoint' ),
			'insert_into_item'      => __( 'Insert into map', 'waypoint' ),
			'uploaded_to_this_item' => __( 'Uploaded to this map', 'waypoint' ),
			'items_list'            => __( 'Maps list', 'waypoint' ),
			'items_list_navigation' => __( 'Maps list navigation', 'waypoint' ),
			'filter_items_list'     => __( 'Filter maps list', 'waypoint' ),
		];

		$labels = apply_filters( 'waypoint_map_post_type_labels', $labels );

		return $labels;
	}

	public function get_args()
	{
		$args = [
			'label'               => __( 'Post Type', 'waypoint' ),
			'description'         => __( 'Post Type Description', 'waypoint' ),
			'labels'              => $this->get_labels(),
			'supports'            => ['title', 'thumbnail'],
			'taxonomies'          => [],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_icon'           => 'dashicons-map',
			'menu_position'       => 20,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		];

		$args = apply_filters( 'waypoint_map_post_type_args', $args );

		return $args;
	}

	public function register_post_type()
	{
		register_post_type( static::KEY, $this->get_args() );
	}

	public function add_submenu_page() {
		global $submenu;

		$permalink = admin_url() . '/edit.php?post_type=waypoint_map';
		$waypoint = &$submenu['edit.php?post_type=waypoint_location'];
		$waypoint[] = [ 'Maps', 'manage_options', $permalink ];

		// Change 'All Locations' -> Locations
		$waypoint[5][0] = 'Locations';
		unset( $waypoint[10] );
	}

	public function __construct()
	{
		$this->register_post_type();
		add_action( 'admin_menu', [$this, 'add_submenu_page'], 0, 1 );
	}
}