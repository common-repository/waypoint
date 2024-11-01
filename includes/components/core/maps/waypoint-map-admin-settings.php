<?php
namespace Waypoint\Core\Maps;

use Waypoint\Core\Administration\Waypoint_Settings_Page_Abstract;

class Waypoint_Map_Admin_Settings extends Waypoint_Settings_Page_Abstract
{
	const KEY = '_map_settings';

	const LABEL = 'Maps';

	public function render() {
		?>
        <table class="form-table">
            <tr>
                <th>Map Pin Graphic</th>
                <td>
                    <select name="_waypoint_map_pin_type">
                        <?php $_waypoint_map_pin_type = get_option( '_waypoint_map_pin_type', 'default' ); ?>
                        <option value="default" <?php echo $_waypoint_map_pin_type == 'default' ? ' selected="selected"' : ''; ?>>Default</option>
                        <option value="custom" <?php echo $_waypoint_map_pin_type == 'custom' ? ' selected="selected"' : ''; ?>>Custom</option>
                    </select>

                    <div id="map-pin-graphic-default" style="display: none;">
                        <!-- todo: add default google pin image -->
                    </div>
                    <div id="map-pin-graphic-custom" style="display: none;">
                        <div class="wp-media-picker"
                             <?php
                             $option_key = '_waypoint_map_pin_image_id';
                             $image_id   = get_option( $option_key, '0' );
                             $image_url  = wp_get_attachment_image_url( $image_id, 'medium' );
                             ?>
                             data-key="<?php echo esc_attr( $option_key ); ?>"
                             data-id="<?php echo $image_id; ?>"
                             data-url="<?php echo $image_url; ?>"></div>
                    </div>
                    <p>
                        <i>This map pin will be used unless overridden in a location.</i>
                    </p>
                </td>
            </tr>
            <tr>
                <th>Map Height</th>
                <td>
                    <input type="number" min="100" max="1000"
                           value="<?php echo esc_attr( Waypoint_Map_Settings::get_map_height() ); ?>"
                           name="_waypoint_map_height"><br>
                    <i>Default map height in pixels.</i>
                </td>
            </tr>

            <tr>
                <th>Map Styles JSON</th>
                <td>
                    <?php
                    $style = Waypoint_Map_Settings::get_map_style();
                    ?>
                    <textarea rows="5" class="widefat" name="_waypoint_map_default_style"><?php echo $style; ?></textarea>
                    <br>
                    <i>
                        Generate a style code from
                        <a href="https://snazzymaps.com" target="_blank">Snazzy Maps</a> or
                        <a href="https://mapstyle.withgoogle.com" target="_blank">Google Maps</a>,
                        or leave empty to use default map settings.
                    </i>
                </td>
            </tr>

            <tr>
                <th>Desktop Center</th>
                <td>
                    <?php
                    $lat = Waypoint_Map_Settings::get_desktop_center_lat();
                    $lng = Waypoint_Map_Settings::get_desktop_center_lng();
                    $zoom = Waypoint_Map_Settings::get_desktop_center_zoom();
                    ?>
                    <label>
                        Lat:
                        <input type="text" name="_waypoint_map_desktop_center_lat" value="<?php echo esc_attr( $lat ); ?>">
                    </label>

                    <label>
                        Lng:
                        <input type="text" name="_waypoint_map_desktop_center_lng" value="<?php echo esc_attr( $lng ); ?>">
                    </label>

                    <label>
                        Zoom:
                        <select name="_waypoint_map_desktop_center_zoom">
		                    <?php foreach ( range( 0, 18 ) as $i ) : ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $zoom ? ' selected="selected"': ''; ?>><?php echo $i; ?></option>
		                    <?php endforeach; ?>
                        </select>
                    </label>

                    <p>
                        <i>Set the default latitude, longitude, and zoom level for desktop maps.</i>
                    </p>
                </td>
            </tr>

            <tr>
                <th>Mobile Center</th>
                <td>
	                <?php
	                $lat  = Waypoint_Map_Settings::get_mobile_center_lat();
	                $lng  = Waypoint_Map_Settings::get_mobile_center_lng();
	                $zoom = Waypoint_Map_Settings::get_mobile_center_zoom();
	                ?>
                    <label>
                        Lat:
                        <input type="text" name="_waypoint_map_mobile_center_lat" value="<?php echo esc_attr( $lat ); ?>">
                    </label>

                    <label>
                        Lng:
                        <input type="text" name="_waypoint_map_mobile_center_lng"
                               value="<?php echo esc_attr( $lng ); ?>">
                    </label>

                    <label>
                        Zoom:
                        <select name="_waypoint_map_mobile_center_zoom">
			                <?php foreach ( range( 0, 18 ) as $i ) : ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $zoom ? ' selected="selected"' : ''; ?>><?php echo $i; ?></option>
			                <?php endforeach; ?>
                        </select>
                    </label>
                    <p>
                        <i>Set the default latitude, longitude, and zoom level for mobile maps.</i>
                    </p>
                </td>
            </tr>

            <tr>
                <th>Info Window Template</th>
                <td>
                    <?php $info_window_template = Waypoint_Map_Settings::get_info_window_template(); ?>
                    <textarea class="wide" cols="45" rows="5" name="_waypoint_map_info_window_template"><?php echo esc_textarea( $info_window_template ); ?></textarea>
                    <?php
                    $variables = Waypoint_Map_Info_Window_Template::get_instance()->get_variables();

                    $variables = array_map( function( $str ) {
                        return '<code class="info-window-var">{' . $str . '}</code>';
                    }, $variables );

                    $variables = implode( ', ', $variables );
                    ?>
                    <p>
                        <i>Customize the map info window content for locations.</i><br>
                        <i>Variables: <?php echo $variables; ?></i>
                    </p>
                </td>
            </tr>

        </table>

        <button class="button button-primary">Save</button>

        <style>
            code.info-window-var {
                cursor: pointer;
            }
        </style>
		<?php

        $this->script();
	}

	public function script()
    {
        ?>
        <script>
            jQuery(document).ready(function ($) {

                // Map Pin Graphic Options.
                var map_pin_default = $('#map-pin-graphic-default'),
                    map_pin_custom = $('#map-pin-graphic-custom'),
                    map_pin_type = $('select[name=_waypoint_map_pin_type');

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
                ////

                // Append info window template codes into textarea.
                var textarea = $('textarea[name="_waypoint_map_info_window_template"]'),
                    vars = $('code.info-window-var');

                vars.click(function () {
                    var code = $(this).text();
                    textarea.val(textarea.val() + ' ' + code);
                });
                ////
            });
        </script>
        <?php
    }

	public function save() {

	    $keys = [
            '_waypoint_map_height',
		    '_waypoint_map_pin_type',
            '_waypoint_map_pin_image_id',
            '_waypoint_map_desktop_center_lat',
            '_waypoint_map_desktop_center_lng',
            '_waypoint_map_desktop_center_zoom',
            '_waypoint_map_mobile_center_lat',
            '_waypoint_map_mobile_center_lng',
            '_waypoint_map_mobile_center_zoom',
            '_waypoint_map_info_window_template',
        ];

        foreach( $keys as $key ) {
            if ( isset( $_POST[$key] ) ) {
                $value = filter_var( $_POST[$key], FILTER_SANITIZE_STRING );
                update_option( $key, $value );
            }
        }

        // Set map pin type to default if no image ID was specified.
        if ( empty( $_POST['_waypoint_map_pin_image_id'] ) ) {
            update_option( '_waypoint_map_pin_type', 'default' );
        }

        $keys = [
	        '_waypoint_map_default_style'
        ];

		foreach ( $keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = str_replace( '\"', '"', $_POST[ $key ] );
				update_option( $key, $value );
			}
		}

		// Validate style json.
        $style = $_POST['_waypoint_map_default_style'];
		if ( ! empty( $style ) ) {
            $style = str_replace( '\"', '"', $style );
			$style = json_decode( $style );
			if ( ! $style && [] !== $style ) {
				update_option( '_waypoint_map_default_style', '' );
				$this->add_error_message( 'Error: a valid JSON string is required for Map Styles.' );
			}
		}

		$this->add_success_message('Map settings saved.');
	}
}