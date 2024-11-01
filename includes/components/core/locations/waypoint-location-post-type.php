<?php
namespace Waypoint\Core\Locations;

/**
 * Class Waypoint_Location_Post_Type
 *
 * @package Waypoint\Core\Locations
 */
class Waypoint_Location_Post_Type
{
	const KEY = 'waypoint_location';

	public function get_labels()
	{
		$labels = [
			'name'                  => _x( 'Locations', 'Post Type General Name', 'waypoint' ),
			'singular_name'         => _x( 'Location', 'Post Type Singular Name', 'waypoint' ),
			'menu_name'             => __( 'Waypoint', 'waypoint' ),
			'name_admin_bar'        => __( 'Location', 'waypoint' ),
			'archives'              => __( 'Location Archives', 'waypoint' ),
			'attributes'            => __( 'Location Attributes', 'waypoint' ),
			'parent_item_colon'     => __( 'Parent Location:', 'waypoint' ),
			'all_items'             => __( 'All Locations', 'waypoint' ),
			'add_new_item'          => __( 'Add New Location', 'waypoint' ),
			'add_new'               => __( 'Add New', 'waypoint' ),
			'new_item'              => __( 'New Location', 'waypoint' ),
			'edit_item'             => __( 'Edit Location', 'waypoint' ),
			'update_item'           => __( 'Update Location', 'waypoint' ),
			'view_item'             => __( 'View Location', 'waypoint' ),
			'view_items'            => __( 'View Locations', 'waypoint' ),
			'search_items'          => __( 'Search Location', 'waypoint' ),
			'not_found'             => __( 'Not found', 'waypoint' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'waypoint' ),
			'featured_image'        => __( 'Featured Image', 'waypoint' ),
			'set_featured_image'    => __( 'Set featured image', 'waypoint' ),
			'remove_featured_image' => __( 'Remove featured image', 'waypoint' ),
			'use_featured_image'    => __( 'Use as featured image', 'waypoint' ),
			'insert_into_item'      => __( 'Insert into location', 'waypoint' ),
			'uploaded_to_this_item' => __( 'Uploaded to this location', 'waypoint' ),
			'items_list'            => __( 'Locations list', 'waypoint' ),
			'items_list_navigation' => __( 'Locations list navigation', 'waypoint' ),
			'filter_items_list'     => __( 'Filter locations list', 'waypoint' ),
		];

		$labels = apply_filters( 'waypoint_location_post_type_labels', $labels );

		return $labels;
	}

	public function get_args()
	{
		$args = [
			'label'               => __( 'Post Type', 'waypoint' ),
			'description'         => __( 'Post Type Description', 'waypoint' ),
			'labels'              => $this->get_labels(),
			'supports'            => ['title', 'editor', 'thumbnail',],
			'taxonomies'          => [],
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-location',
			'menu_position'       => 20,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		];

		$args = apply_filters( 'waypoint_location_post_type_args', $args );

		return $args;
	}

	public function register_post_type()
	{
		register_post_type( static::KEY, $this->get_args() );
	}

	public function __construct()
	{
		$this->register_post_type();
	}
}