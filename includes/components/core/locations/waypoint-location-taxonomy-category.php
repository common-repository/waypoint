<?php
namespace Waypoint\Core\Locations;

use Waypoint\Core\Taxonomies\Filters\Waypoint_Taxonomy_Map_Pin_Filter;
use Waypoint\Core\Taxonomies\Filters\Waypoint_Taxonomy_Order_Filter;

class Waypoint_Location_Category_Taxonomy
{
	const KEY = 'waypoint_location_category';

	public function __construct() {
		add_action( 'waypoint_loaded', [$this, 'register'] );
	}

	public function get_labels() {
		$labels = [
			'name'                       => _x( 'Categories', 'Category General Name', 'waypoint' ),
			'singular_name'              => _x( 'Category', 'Category Singular Name', 'waypoint' ),
			'menu_name'                  => __( 'Categories', 'waypoint' ),
			'all_items'                  => __( 'All Items', 'waypoint' ),
			'parent_item'                => __( 'Parent Item', 'waypoint' ),
			'parent_item_colon'          => __( 'Parent Item:', 'waypoint' ),
			'new_item_name'              => __( 'New Item Name', 'waypoint' ),
			'add_new_item'               => __( 'Add New Item', 'waypoint' ),
			'edit_item'                  => __( 'Edit Item', 'waypoint' ),
			'update_item'                => __( 'Update Item', 'waypoint' ),
			'view_item'                  => __( 'View Item', 'waypoint' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'waypoint' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'waypoint' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'waypoint' ),
			'popular_items'              => __( 'Popular Items', 'waypoint' ),
			'search_items'               => __( 'Search Items', 'waypoint' ),
			'not_found'                  => __( 'Not Found', 'waypoint' ),
			'no_terms'                   => __( 'No items', 'waypoint' ),
			'items_list'                 => __( 'Items list', 'waypoint' ),
			'items_list_navigation'      => __( 'Items list navigation', 'waypoint' ),
		];

		return apply_filters( 'waypoint_location_category_taxonomy_labels', $labels );
	}

	public function get_args() {
		$args = [
			'labels'            => $this->get_labels(),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
		];

		return apply_filters( 'waypoint_location_category_taxonomy_args', $args );
	}

	public function register() {
		register_taxonomy( static::KEY, [Waypoint_Location_Post_Type::KEY], $this->get_args() );

		// Add sort order.
		// new Waypoint_Taxonomy_Order_Filter( static::KEY );

		// Add category images.
		// new Waypoint_Taxonomy_Map_Pin_Filter( static::KEY );
	}
}