<?php
namespace Waypoint\Core\Taxonomies\Filters;

class Waypoint_Taxonomy_Map_Pin_Filter
{
	protected $taxonomy;

	public function __construct( $taxonomy ) {

		// Hook.
		add_filter( "manage_edit-{$taxonomy}_columns", [ $this, 'columns' ] );
		add_filter( "manage_edit-{$taxonomy}_sortable_columns", [ $this, 'columns' ] );
		add_filter( "manage_{$taxonomy}_custom_column", [ $this, 'column_content' ], 10, 3 );
		add_action( "edited_{$taxonomy}", [ $this, 'save' ], 10, 2 );
		add_action( "created_{$taxonomy}", [ $this, 'save' ], 10, 2 );
		add_action( "{$taxonomy}_add_form_fields", [ $this, 'add_form_fields' ] );
		add_action( "{$taxonomy}_edit_form_fields", [ $this, 'edit_form_fields' ], 10, 2 );

		// Assign taxonomy to object.
		$this->taxonomy = $taxonomy;

		add_image_size( 'map_pin', 48, 48, false );
	}

	const META_KEY = '_map_pin';

	public function columns( $columns ) {
		$columns['_map_pin'] = 'Map Pin';
		return $columns;
	}

	public function column_content( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case '_map_pin':
				$image_id = get_term_meta( $term_id, '_map_pin', true );
				$image_url = wp_get_attachment_image_url( $image_id, 'map_pin' );

				if ( ! empty( $image_id ) ) {
				    echo '<img src="' . $image_url . '">';
                } else {
				    echo '--';
                }
				break;
			default:
				break;
		}
	}

	public function add_form_fields( $taxonomy ) {
		?>
		<div class="form-field">
			<label for="_map_ping">Custom Map Pin<label>
            <?php
            $option_key = '_map_pin';
            $image_id = '';
            $image_url = '';
            ?>
            <div class="wp-media-picker"
                 data-key="<?php echo esc_attr( $option_key ); ?>"
                 data-id="<?php echo $image_id; ?>"
                 data-url="<?php echo $image_url; ?>"></div>
		</div>
		<?php
	}

	public function edit_form_fields( \WP_Term $term, $taxonomy ) {
		?>
		<tr class="form-field">
			<th><label for="_sort_order">Custom Map Pin</label></th>
			<td>
				<?php
				$option_key = '_map_pin';
				$image_id = get_term_meta( $term->term_id, '_map_pin', true );
				$image_url = wp_get_attachment_image_url( $image_id, 'medium' );
				?>
                <div class="wp-media-picker"
                     data-key="<?php echo esc_attr( $option_key ); ?>"
                     data-id="<?php echo $image_id; ?>"
                     data-url="<?php echo $image_url; ?>"></div>
			</td>
		</tr>
		<?php
	}

	public function save( $term_id, $tt_id ) {

		$keys = [
			'_map_pin'
		];

		foreach ( $keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = filter_var( $_POST[ $key ], FILTER_SANITIZE_STRING );
				update_term_meta( $term_id, $key, $value );
			}
		}
	}

	public static function get_map_pin_id( $term_id ) {
	    return get_term_meta( $term_id, '_map_pin', true );
    }
}
