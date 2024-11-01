<?php
namespace Waypoint\Core\Maps;
use Waypoint\Core\Locations\Waypoint_Location_Category_Taxonomy;
use Waypoint\Core\Locations\Waypoint_Location_Post_Type;

/**
 * Class Waypoint_Location_Details_Meta_Box
 *
 * @package Waypoint\Core\Locations
 */
class Waypoint_Map_Options_Meta_Box {
	public function get_post_type() {
		return Waypoint_Map_Post_Type::KEY;
	}

	public function __construct() {
		$post_type = $this->get_post_type();
		add_action( 'save_post', [ $this, 'save_post' ] );
		add_action( "add_meta_boxes_{$post_type}", [ $this, 'add_meta_box' ] );
	}

	/**
	 * @param $post_id int
	 */
	public function save_post( $post_id ) {
		$keys = [
			'_selected_term_ids',
            '_location_query_type',
            '_map_center_desktop_lat',
            '_map_center_desktop_lng',
            '_map_center_desktop_zoom',
            '_map_center_mobile_lat',
            '_map_center_mobile_lng',
            '_map_center_mobile_zoom',
            '_map_center_type',
            '_info_window_template_type',
            '_info_window_template',
		];

		// This filter can be used to save additional fields.
		$keys = apply_filters( 'waypoint_save_map_options_meta_keys', $keys );

		foreach ( $keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = $_POST[ $key ];
				$value = filter_var( $value, FILTER_SANITIZE_STRING );
				update_post_meta( $post_id, $key, $value );
			}
		}

		do_action( 'waypoint_save_map_options_meta', $post_id );
	}

	/**
	 * @var \WP_Post
	 */
	protected $post;

	/**
	 * @param \WP_Post $post
	 */
	public function add_meta_box( \WP_Post $post ) {
		// Store post to object.
		$this->post = $post;

		add_meta_box(
			'waypoint-map-options',
			'Map Options',
			[ $this, 'render' ],
			null,
			'advanced',
			'default'
		);
	}

	public function render() {
		do_action( 'waypoint_before_map_options_meta_box_form', $this->post );
		$this->render_form();
		$this->render_styles();
		$this->render_scripts();
		do_action( 'waypoint_after_map_options_meta_box_form', $this->post );
	}

	public function render_styles() {
		?>
		<style>
		</style>
		<?php
	}

	public function render_scripts() {

		$selected_term_ids = get_post_meta( $this->post->ID, '_selected_term_ids', true );
		if ( empty( $selected_term_ids ) ) {
			$selected_term_ids = [];
		} else {
			$selected_term_ids = explode( ',', $selected_term_ids );
		}

		// Map Center Type
		$map_center_type = get_post_meta( $this->post->ID, '_map_center_type', true );
		if ( empty( $map_center_type ) ) {
            $map_center_type = 'Default';
		}

		$info_window_template_type = get_post_meta( $this->post->ID, '_info_window_template_type', true );
        if ( empty( $info_window_template_type ) ) {
            $info_window_template_type = 'Default';
        }

		$info_window_template = get_post_meta( $this->post->ID, '_info_window_template', true );

        $location_query_type = get_post_meta( $this->post->ID, '_location_query_type', true );
        if ( empty( $location_query_type ) ) {
            $location_query_type = 'All';
        }

		?>
		<script>
            jQuery(document).ready(function ($) {
                var app = new Vue({
                    el: '#waypoint-map-options-meta-box',

                    data: {
                        options: {
                            query_types: [
                                'All',
                                'Taxonomy'
                            ],
                            zoom_levels: <?php echo json_encode( range( 0, 18 ) ); ?>,
                            map_center_types: [
                                'Default',
                                'Custom'
                            ],
                            info_window_template_types: [
                                'Default',
                                'Custom'
                            ],
                            template_variables: <?php echo json_encode( Waypoint_Map_Info_Window_Template::get_instance()->get_variables() ); ?>
                        },
                        query_type: '<?php echo $location_query_type; ?>',
	                    taxonomies: <?php echo json_encode( $this->get_taxonomies_model() ); ?>,
	                    selected_term_ids: <?php echo json_encode( $selected_term_ids ); ?>,
                        map_center_desktop_lat: '<?php echo get_post_meta( $this->post->ID, '_map_center_desktop_lat', true ); ?>',
                        map_center_desktop_lng: '<?php echo get_post_meta( $this->post->ID, '_map_center_desktop_lng', true ); ?>',
                        map_center_desktop_zoom: '<?php echo get_post_meta( $this->post->ID, '_map_center_desktop_zoom', true ); ?>',
                        map_center_mobile_lat: '<?php echo get_post_meta( $this->post->ID, '_map_center_mobile_lat', true ); ?>',
                        map_center_mobile_lng: '<?php echo get_post_meta( $this->post->ID, '_map_center_mobile_lng', true ); ?>',
                        map_center_mobile_zoom: '<?php echo get_post_meta( $this->post->ID, '_map_center_mobile_zoom', true ); ?>',
                        map_center_type: '<?php echo $map_center_type; ?>',
                        info_window_template_type: '<?php echo $info_window_template_type; ?>',
                        info_window_template: '<?php echo $info_window_template; ?>'
                    },

                    mounted: function () {
                        // Show vue templates.
                        $('#waypoint-map-options-loading').fadeOut(function () {
                            $(app.$el).fadeIn();
                        });
                    },

	                computed: {
                        selected_term_ids_string: function() {
                            var value = this.selected_term_ids;
                            if ( 0 === value.length ) {
                                return '';
                            }
                            return value.join(',');
                        }
	                },

                    methods: {
                        reset_map_center: function() {
                            this.map_center_desktop_lat = '';
                            this.map_center_desktop_lng = '';
                            this.map_center_desktop_zoom = '';
                            this.map_center_mobile_lat = '';
                            this.map_center_mobile_lng = '';
                            this.map_center_mobile_zoom = '';
                            this.map_center_type = 'Default';
                        },

                        add_template_variable: function(i) {
                            this.info_window_template = this.info_window_template + ' {' + i + '}';
                        }

                    }
                })
            });
		</script>
		<?php
	}

	public function render_form() {
		$post_id = $this->post->ID;

		$this->get_taxonomies_model();

		?>
		<div id="waypoint-map-options-loading">Loading...</div>
		<div id="waypoint-map-options-meta-box" style="display: none;">
			<?php do_action( 'waypoint_map_options_meta_box_before_field', '_location_query' ); ?>

            <h3>Locations</h3>

            <p>
                <i>Specify how the map should query locations.</i>
            </p>

            <table class="form-table">
                <tr>
                    <th>Query Locations By</th>
                    <td>
                        <select name="_location_query_type" v-model="query_type">
                            <option v-for="option in options.query_types" :value="option">{{ option }}</option>
                        </select>
                    </td>
                </tr>
            </table>

			<div v-if="'Taxonomy' === query_type">
                <table class="form-table">
                    <tr v-for="item in taxonomies">
                        <th>{{ item.label }}</th>
                        <td>
                            <div v-if="0 == item.terms.length">
                                <p>No terms exist...</p>
                            </div>
                            <div v-else>
                                <div v-for="term in item.terms">
                                    <label>
                                        <input type="checkbox" :value="term.term_id" v-model="selected_term_ids">
                                        {{ term.name }} ({{ term.count }})
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

			<input type="hidden" name="_selected_term_ids" v-model="selected_term_ids_string">

            <hr>
			<?php do_action( 'waypoint_map_options_meta_box_after_field', '_location_query' ); ?>

            <h3>
                Map Center
                <small>
                    <a href="#" id="reset-map-center" @click="reset_map_center">Clear</a>
                </small>
            </h3>

            <p>
                <i>Optionally override default map center latitude, longitude, and zoom levels on mobile and desktop views.</i>
            </p>

            <table class="form-table">
                <tr>
                    <th>Center</th>
                    <td>
                        <select v-model="map_center_type" name="_map_center_type">
                            <option v-for="option in options.map_center_types">{{ option }}</option>
                        </select>
                    </td>
                </tr>

                <tr v-if="'Custom' === map_center_type">
                    <th>Desktop</th>
                    <td>
                        <label>
                            Lat:
                            <input type="text" name="_map_center_desktop_lat" v-model="map_center_desktop_lat">
                        </label>
                        <label>
                            Lng:
                            <input type="text" name="_map_center_desktop_lng" v-model="map_center_desktop_lng">
                        </label>
                        <label>
                            Zoom:
                            <select name="_map_center_desktop_zoom" v-model="map_center_desktop_zoom">
                                <option value="">Select...</option>
                                <option v-for="v in options.zoom_levels" :value="v">
                                    {{ v }}
                                </option>
                            </select>
                        </label>
                        <p>
                            <i>Values will default to zero if left empty.</i>
                        </p>

                    </td>
                </tr>

                <tr v-if="'Custom' === map_center_type">
                    <th>Mobile</th>
                    <td>
                        <label>
                            Lat:
                            <input type="text" name="_map_center_mobile_lat" v-model="map_center_mobile_lat">
                        </label>
                        <label>
                            Lng:
                            <input type="text" name="_map_center_mobile_lng" v-model="map_center_mobile_lng">
                        </label>
                        <label>
                            Zoom:
                            <select name="_map_center_mobile_zoom" v-model="map_center_mobile_zoom">
                                <option value="">Select...</option>
                                <option v-for="v in options.zoom_levels" :value="v">
                                    {{ v }}
                                </option>
                            </select>
                        </label>
                        <p>
                            <i>Values will default to zero if left empty.</i>
                        </p>
                    </td>
                </tr>

            </table>
            <hr>

            <table class="form-table">
                <tr>
                    <th>Info Window Template</th>
                    <td>
                        <select name="_info_window_template_type" v-model="info_window_template_type">
                            <option v-for="option in options.info_window_template_types">
                                {{ option }}
                            </option>
                        </select>
                    </td>
                </tr>
                <tr v-if="'Custom' === info_window_template_type">
                    <th>Template</th>
                    <td>
                        <textarea class="wide" cols="45" rows="5" name="_info_window_template" v-model="info_window_template"></textarea>
                        <p>
                            <i>Customize the map info window content for locations.</i><br>
                            <i>Variables: <span v-for="i in options.template_variables"><code style="cursor: pointer;" @click="add_template_variable(i)">{{ '{' + i + '}' }}</code> <span> </i>
                        </p>
                    </td>
                </tr>
            </table>
		</div>
		<?php
	}

	/**
     * Returns a list of
	 * @return array
	 */
	public function get_taxonomies_model()
	{
		$post_type = Waypoint_Location_Post_Type::KEY;
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$output = [];

		foreach( $taxonomies as $taxonomy ) {

			$label = $taxonomy->label;
			$name = $taxonomy->name;

			$terms = get_terms([
				'taxonomy' => $name,
				'hide_empty' => false
			]);

			$output[] = [
				'label' => $label,
				'taxonomy' => $name,
				'terms' => $terms
			];
		}

		return $output;
	}
}