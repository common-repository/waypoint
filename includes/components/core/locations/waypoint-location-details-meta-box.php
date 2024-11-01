<?php
namespace Waypoint\Core\Locations;

/**
 * Class Waypoint_Location_Details_Meta_Box
 *
 * @package Waypoint\Core\Locations
 */
class Waypoint_Location_Details_Meta_Box
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
            '_address_street',
            '_address_city',
            '_address_state',
            '_address_zip_code',
            '_address_country',
            '_address_lat',
            '_address_lng',
            '_primary_email'
        ];

        // This filter can be used to save additional fields.
        $keys = apply_filters( 'waypoint_save_location_details_meta_keys', $keys );

        foreach( $keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = $_POST[$key];
                $value = filter_var( $value, FILTER_SANITIZE_STRING );
                update_post_meta( $post_id, $key, $value );
            }
        }

		do_action( 'waypoint_save_location_details_meta', $post_id );
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
			'waypoint-location-details',
			'Location Details',
			[$this, 'render'],
			null,
			'advanced',
			'default'
		);
	}

	public function render()
	{
	    do_action( 'waypoint_before_location_details_meta_box_form', $this->post );
	    $this->render_form();
		$this->render_styles();
	    $this->render_scripts();
		do_action( 'waypoint_after_location_details_meta_box_form', $this->post );
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

                var geocode_button = $('.geocode-address'),
                    geocode_address_input = $('input#geocode-address');

                function geocode_address() {

                    var value = geocode_address_input.val().trim();

                    if ( value.length === 0 ) {
                        alert('Please enter an address to geocode.');
                        return;
                    }

                    // Update button.
                    geocode_button.text('Please wait...');
                    geocode_button.prop('disabled', true);

                    $.ajax({
                        method: 'post',
                        url: '<?php echo Waypoint_Location_Geocoder::get_url(); ?>',
                        data: {
                            post_id: '<?php echo $this->post->ID; ?>',
                            address: value
                        }
                    })
                    .done(function(r) {
                        if ( 'undefined' !== typeof( r.street_address ) ) {
                            $('input[name=_address_street]').val(r.street_address);
                        }
                        if ( 'undefined' !== typeof( r.city ) ) {
                            $('input[name=_address_city]').val(r.city);
                        }
                        if ( 'undefined' !== typeof( r.state ) ) {
                            $('input[name=_address_state]').val(r.state);
                        }
                        if ( 'undefined' !== typeof( r.zip_code ) ) {
                            $('input[name=_address_zip_code]').val(r.zip_code);
                        }
                        if ( 'undefined' !== typeof( r.country_long ) ) {
                            $('input[name=_address_country]').val(r.country_long);
                        }
                        if ( 'undefined' !== typeof( r.lat ) ) {
                            $('input[name=_address_lat]').val(r.lat);
                        }
                        if ( 'undefined' !== typeof( r.lng ) ) {
                            $('input[name=_address_lng]').val(r.lng);
                        }
                    })
                    .fail(function(r) {
                        alert('No results found for address.');
                    })
                    .always(function() {
                        geocode_button.text('Geocode Address');
                        geocode_button.prop('disabled', false);
                    });
                }

                geocode_button.on('click', geocode_address);
            });
        </script>
        <?php
    }

	public function render_form()
    {
        $post_id = $this->post->ID;

        $address_street = get_post_meta( $post_id, '_address_street', true );
        $address_city = get_post_meta( $post_id, '_address_city', true );
        $address_state = get_post_meta( $post_id, '_address_state', true );
        $address_zip_code = get_post_meta( $post_id, '_address_zip_code', true );
        $address_country = get_post_meta( $post_id, '_address_country', true );
        $address_lat = get_post_meta( $post_id, '_address_lat', true );
        $address_lng = get_post_meta( $post_id, '_address_lng', true );
        ?>
        <div id="waypoint-details-meta-box">

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_geocode_address' ); ?>
            <p>
                <label>Geocode Address</label>
                <input type="text" class="widefat" id="geocode-address">
                <button class="button geocode-address" type="button">Geocode Address</button>
            </p
            <?php do_action( 'waypoint_location_details_meta_box_after_field', '_geocode_address' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_street' ); ?>
            <label for="_address_street">Street Address</label>
            <input type="text" class="widefat" id="_address_street" name="_address_street" value="<?php echo esc_attr( $address_street ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field','_address_street' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_city' ); ?>
            <label for="_address_city">City</label>
            <input type="text" class="widefat" id="_address_city" name="_address_city" value="<?php echo esc_attr( $address_city ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field', '_address_city' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_state' ); ?>
            <label for="_address_state">State</label>
            <input type="text" class="widefat" id="_address_state" name="_address_state" value="<?php echo esc_attr( $address_state ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field', '_address_state' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_zip_code' ); ?>
            <label for="_address_zip_code">Zip Code</label>
            <input type="text" class="widefat" id="_address_zip_code" name="_address_zip_code" value="<?php echo esc_attr( $address_zip_code ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field', '_address_zip_code' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_country' ); ?>
            <label for="_address_country">Country</label>
            <input type="text" class="widefat" id="_address_country" name="_address_country" value="<?php echo esc_attr( $address_country ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field', '_address_country' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_lat' ); ?>
            <label for="_address_lat">Latitude</label>
            <input type="text" class="widefat" id="_address_lat" name="_address_lat" value="<?php echo esc_attr( $address_lat ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field', '_address_lat' ); ?>

	        <?php do_action( 'waypoint_location_details_meta_box_before_field', '_address_lng' ); ?>
            <label for="_address_lng">Longitude</label>
            <input type="text" class="widefat" id="_address_lng" name="_address_lng" value="<?php echo esc_attr( $address_lng ); ?>">
	        <?php do_action( 'waypoint_location_details_meta_box_after_field', '_address_lng' ); ?>

        </div>

        <?php
    }
}