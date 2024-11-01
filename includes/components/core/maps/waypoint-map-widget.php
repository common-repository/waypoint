<?php
namespace Waypoint\Core\Maps;

class Waypoint_Map_Widget extends \WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'waypoint_map_widget',
            esc_html__( 'Waypoint: Map', 'waypoint' ),
            ['description' => esc_html__( 'Displays a map.', 'waypoint' )]
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo do_shortcode( "[waypoint_map id={$instance['map_id']}]");
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'waypoint' );
        $map_id = ! empty( $instance['map_id'] ) ? $instance['map_id'] : esc_html__( '', 'way_point' );
        ?>
        <p>
            <label
                for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'waypoint' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('map_id'); ?>">
                Map
            </label>
            <?php
            $maps = Waypoint_Maps::all();
            $map_id = ! empty( $instance['map_id'] ) ? $instance['map_id'] : '';
            ?>
            <select class="widefat" name="<?php echo $this->get_field_name('map_id'); ?>" id="<?php echo $this->get_field_id('map_id'); ?>">
                <?php foreach( $maps as $map ) : ?>
                    <option value="<?php echo $map->ID; ?>" <?php echo $map_id == $map->ID ? 'selected="selected"' : ''; ?>><?php echo $map->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance          = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['map_id'] = ( ! empty( $new_instance['map_id'] ) ) ? strip_tags( $new_instance['map_id'] ) : '';

        return $instance;
    }
}

add_action( 'widgets_init', function() {
    register_widget( Waypoint_Map_Widget::class );
});