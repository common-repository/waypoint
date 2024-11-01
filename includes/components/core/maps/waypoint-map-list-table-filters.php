<?php
namespace Waypoint\Core\Maps;

class Waypoint_Map_List_Table_Filters
{
	public function __construct() {
		$post_type = Waypoint_Map_Post_Type::KEY;
		add_filter( "manage_{$post_type}_posts_columns", [ $this, 'columns' ], 10 );
		add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'column_content' ], 10, 2 );
	}

	public function columns( $columns ) {
		$columns['shortcode_map'] = 'Map';
		// Commented out until further developed.
		//$columns['shortcode_list'] = 'Location List';
		return $columns;
	}

	public function column_content( $column_name, $post_id )
	{
		if ( 'shortcode_map' === $column_name ) {
			?>
			<code>[waypoint_map id=<?php echo $post_id; ?>]</code>
			<?php
		}

		/*
		if ( 'shortcode_list' === $column_name ) {
			?>
			<code>[waypoint_map_locations id=<?php echo $post_id; ?>]</code>
			<?php
		}
		*/
	}
}