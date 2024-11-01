<?php
namespace Waypoint\Core\Taxonomies\Filters;

/**
 * Class Waypoint_Taxonomy_Order_Filter
 *
 * Adds the ability to sort and order taxonomies.
 *
 * @package Waypoint\Core\Taxonomies\Filters
 */
class Waypoint_Taxonomy_Order_Filter
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
		add_action( "quick_edit_custom_box", [ $this, "quick_edit"], 10, 3 );
		add_action( 'pre_get_terms', [$this, 'admin_sort_taxonomy_order'], 10, 2 );

		// Assign taxonomy to object.
		$this->taxonomy = $taxonomy;
	}

	const META_KEY = '_sort_order';

	public static function get_sort_order( $term_id ) {
		$value = get_term_meta( $term_id, static::META_KEY, true );
		if ( ! empty( $value ) ) {
			return $value;
		}

		return 0;
	}

	public static function set_sort_order( $term_id, $value = 0 ) {
		update_term_meta( $term_id, static::META_KEY, true );
	}

	public function admin_sort_taxonomy_order( \WP_Term_Query $query ) {

		if ( ! is_admin() || ! isset( $_REQUEST['orderby'] ) || 'Order' !== $_REQUEST['orderby'] ) {
			return;
		}

		$screen = get_current_screen();

		if (
			! isset( $screen->taxonomy ) ||
			'edit-tags' !== $screen->base ||
			$this->taxonomy !== $screen->taxonomy
		) {
			return;
		}

		$query->meta_query->queries[] = [
			'key' => static::META_KEY,
			'type' => 'NUMERIC'
		];

		$query->query_vars['orderby'] = 'meta_value_num';
	}

	public function columns( $columns ) {
		$columns['_sort_order'] = 'Order';
		return $columns;
	}

	public function column_content( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case '_sort_order':
				echo static::get_sort_order( $term_id );
				break;
			default:
				break;
		}
	}

	public function add_form_fields( $taxonomy ) {
		?>
		<div class="form-field">
			<label for="_sort_order">Sort Order</label>
			<input type="number" name="_sort_order" id="_sort_order" value="0">
		</div>
		<?php
	}

	public function edit_form_fields( \WP_Term $term, $taxonomy ) {
		?>
		<tr class="form-field">
			<th><label for="_sort_order">Sort Order</label></th>
			<td>
				<input type="number" name="_sort_order" id="_sort_order" value="<?php echo esc_attr( static::get_sort_order( $term->term_id ) ); ?>">
			</td>
		</tr>
		<?php
	}

	public function quick_edit( $column_name, $screen, $name ) {
		if ( static::META_KEY !== $column_name || $this->taxonomy !== $name ) {
			return;
		}
		?>
		<fieldset>
			<div class="inline-edit-col">
				<label>
					<span class="title">Sort Order</span>
					<span class="input-number-wrap"><input type="number" name="_sort_order" id="_sort_order" value=""></span>
				</label>
			</div>
		</fieldset>

		<script>
			jQuery(document).ready(function($) {
			    var quick_edit = $('.editinline');
			    quick_edit.on('click', function() {
			        var parent = $(this).parents('tr'),
				        cur_value = parent.find('td.column-<?php echo static::META_KEY; ?>').text();
			        setTimeout(function() {
                        var el = $('td input#<?php echo static::META_KEY; ?>');
                        el.val( cur_value );
			        }, 50 );
			    });
			});
		</script>
		<?php
	}

	public function save( $term_id, $tt_id ) {
		$keys = [
			'_sort_order'
		];

		foreach ( $keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = filter_var( $_POST[ $key ], FILTER_SANITIZE_STRING );
				update_term_meta( $term_id, $key, $value );
			}
		}
	}
}
