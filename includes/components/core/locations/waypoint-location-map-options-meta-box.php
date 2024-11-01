<?php
namespace Waypoint\Core\Locations;
use Waypoint\Core\Maps\Waypoint_Maps;

/**
 * Class Waypoint_Location_Map_Options_Meta_Box
 *
 * @package Waypoint\Core\Locations
 */
class Waypoint_Location_Map_Options_Meta_Box
{
    public function get_post_type()
    {
	    return Waypoint_Location_Post_Type::KEY;
    }

	public function __construct()
	{
	    $post_type = $this->get_post_type();
		add_action( 'save_post', [$this, 'save_post'] );
		add_action( "add_meta_boxes_{$post_type}", [$this, 'add_meta_box'] );
	}

	/**
	 * @param $post_id int
	 */
	public function save_post( $post_id )
	{
        $keys = [
            '_map_pin_type',
            '_map_pin_image_id'
        ];

        // This filter can be used to save additional fields.
        $keys = apply_filters( 'waypoint_save_map_options_meta_keys', $keys );

        foreach( $keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = $_POST[$key];
                $value = filter_var( $value, FILTER_SANITIZE_STRING );
                update_post_meta( $post_id, $key, $value );
            }
        }

        if ( isset( $_POST['_map_specific_pins'] ) ) {
            update_post_meta( $post_id, '_map_specific_pins', $_POST['_map_specific_pins'] );
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
	public function add_meta_box( \WP_Post $post )
	{
	    // Store post to object.
	    $this->post = $post;

		add_meta_box(
			'waypoint-map-options-details',
			'Map Options',
			[$this, 'render'],
			null,
			'advanced',
			'default'
		);
	}

	public function render()
	{
	    do_action( 'waypoint_before_location_map_options_meta_box_form', $this->post );
	    $this->render_form();
		$this->render_styles();
	    $this->render_scripts();
		do_action( 'waypoint_after_location_map_options_meta_box_form', $this->post );
	}

	public function render_styles()
    {
        ?>
        <style>
            #waypoint-details-meta-box p {
                position: relative;
            }
            button.geocode-address {
                bottom: 0;
                right: 0;
                position: absolute;
                float: right;
            }
        </style>
        <?php
    }

    public function render_scripts()
    {
        ?>
        <script>
            jQuery(document).ready(function($) {
            });
        </script>
        <?php
    }

	public function render_form()
    {
        $post_id = $this->post->ID;

        $map_pin_type = get_post_meta( $post_id, '_map_pin_type', true );
        ?>
        <table class="form-table">
            <tr>
                <th>Map Pin</th>
                <td>
                    <div id="waypoint-map-options-meta-box">
		                <?php do_action( 'waypoint_location_map_options_meta_box_before_field', '_map_pin_type' ); ?>
                        <label for="_map_pin_type">Map Pin Type</label>

                        <select name="_map_pin_type" id="_map_pin_type">
                            <option value="" <?php echo $map_pin_type == '' ? ' selected="selected"' : ''; ?>>Default
                            </option>
                            <option value="custom" <?php echo $map_pin_type == 'custom' ? ' selected="selected"' : ''; ?>>Custom
                            </option>
                        </select>

                        <div id="map-pin-graphic-default" style="display: none;">
                        </div>
                        <div id="map-pin-graphic-custom" style="display: none;">
                            <div class="wp-media-picker"
				                <?php
				                $option_key = '_map_pin_image_id';
				                $image_id   = get_post_meta( $post_id, $option_key, true );
				                $image_url  = wp_get_attachment_image_url( $image_id, 'medium' );
				                ?>
                                data-key="<?php echo esc_attr( $option_key ); ?>"
                                data-id="<?php echo $image_id; ?>"
                                data-url="<?php echo $image_url; ?>"></div>
                        </div>

                        <script>
                            jQuery(document).ready(function ($) {
                                var map_pin_default = $('#map-pin-graphic-default'),
                                    map_pin_custom = $('#map-pin-graphic-custom'),
                                    map_pin_type = $('select[name=_map_pin_type');

                                function toggle_view() {
                                    var value = map_pin_type.val();
                                    map_pin_default.hide();
                                    map_pin_custom.hide();
                                    if ('default' === value) {
                                        map_pin_default.show();
                                    }
                                    if ('custom' === value) {
                                        map_pin_custom.show();
                                    }
                                }

                                toggle_view();
                                map_pin_type.on('change', toggle_view);
                            });
                        </script>
		                <?php do_action( 'waypoint_location_map_options_meta_box_after_field', '_map_pin_type' ); ?>
                    </div>
                </td>
            </tr>

            <tr>
                <th>Map Specific Pins</th>
                <td>
                    <div id="map-specific-pin">
                        <div>
                            <button type="button" class="button" @click="add_map">Add</button>
                        </div>
                        <div v-if="0 === maps.length">
                            <p>
                                <i>No map specific pins assigned.</i>
                            </p>
                        </div>
                        <div v-else>
                            <div v-for="(map,i) in maps" class="map-specific-pin">
                                <select v-model="map.map_id">
                                    <option value="">Select Map...</option>
                                    <option v-for="post in options.maps" :value="post.ID" v-html="post.post_title"></option>
                                </select>

                                <span v-if="'' !== map.image_url">
                                    <img :src="map.image_url" width="32">
                                </span>

                                <a href="#" @click="select_image(map, $event)" v-if="'' === map.image_id">Select Pin Image</a>
                                <a href="#" @click="remove_image(map, $event)" v-else>Remove Pin Image</a>

                                <a href="#" @click="remove_map(i, $event)" style="float: right; position: relative; top: 3px;">Remove</a>
                            </div>
                        </div>
                        <input type="hidden" v-model="json" name="_map_specific_pins">
                    </div>

                    <?php
                    $map_specific_pins = get_post_meta( $post_id, '_map_specific_pins', true );
                    if ( empty( $map_specific_pins ) ) {
                        $map_specific_pins = '[]';
                    }
                    ?>

                    <script>
                        jQuery(document).ready(function($) {
                            var app = new Vue({

                                el: '#map-specific-pin',

                                data: {
                                    frames: {},
                                    maps: <?php echo $map_specific_pins; ?>,
                                    type: 'default',
                                    options: {
                                        maps: <?php echo json_encode( Waypoint_Maps::all() ); ?>
                                    }
                                },

                                computed: {
                                    json: function() {
                                        return JSON.stringify( this.maps );
                                    }
                                },

                                mounted: function() {
                                    var self = this;
                                    _.each( this.maps, function(map) {
                                        self.add_frame(map.key);
                                    });
                                },

                                methods: {
                                    add_map: function() {
                                        var model = {
                                            image_id: '',
                                            image_url: '',
                                            map_id: '',
                                            key: Date.now()
                                        };
                                        this.maps.push( model );
                                        this.add_frame( model.key );
                                    },
                                    remove_map: function(i,e) {
                                        this.maps.splice(i,1);
                                        e.preventDefault();
                                    },
                                    select_image: function(map,e) {
                                        this.frames[map.key].open();
                                        e.preventDefault();
                                    },
                                    remove_image: function(map,e) {
                                        map.image_id = '';
                                        map.image_url = '';
                                        e.preventDefault();
                                    },
                                    get_map_by_key: function(key) {
                                        var out;
                                        _.each( this.maps, function(map) {
                                            if ( key === map.key ) {
                                                out = map;
                                            }
                                        });
                                        return out;
                                    },
                                    add_frame: function(key) {
                                        var frame = new wp.media.view.MediaFrame.Select({
                                            title: 'Select Map Pin',
                                            multiple: false,
                                            library: {
                                                order: 'ASC',
                                                orderby: 'title',
                                                type: ['image']
                                            },
                                            button: {
                                                text: 'Select Map Pin'
                                            }
                                        });
                                        frame.state();
                                        frame.lastState();
                                        frame.on('select', function () {
                                            var selectionCollection = frame.state().get('selection'),
                                                models = selectionCollection.models,
                                                image = models[0],
                                                imageId = image.attributes.id,
                                                imageUrl = 'undefined' === typeof( image.attributes.sizes.medium ) ?
                                                    image.attributes.sizes.full.url : image.attributes.sizes.medium.url;
                                            app.get_map_by_key(key).image_id = imageId;
                                            app.get_map_by_key(key).image_url = imageUrl;
                                        });
                                        this.frames[key] = frame;
                                    }
                                }
                            });
                        });
                    </script>
                    <style>
                        .map-specific-pin {
                            padding: 5px;
                            border: dashed 1px gray;
                            margin-bottom: 10px;
                            margin-top: 10px;
                        }
                    </style>
                </td>
            </tr>
        </table>

        <hr>
        <?php
    }
}