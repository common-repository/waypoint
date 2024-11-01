<?php

namespace Waypoint\Core\Logger;

use Waypoint\Core\Administration\Waypoint_Settings_Page_Abstract;
use Waypoint\Core\Google\Waypoint_Logger_Settings;

class Waypoint_Logger_Admin_Settings extends Waypoint_Settings_Page_Abstract {

	const KEY = '_logs';

	const LABEL = 'Logs';

	public function render() {

        $logger_enabled = Waypoint_Logger_Settings::enabled();
		?>

		<table class="form-table">
			<tr>
				<th>Enabled</th>
				<td>
                    <input type="checkbox" name="_waypoint_settings_logger_enabled" value="1" <?php echo $logger_enabled ? ' checked="checked"' : ''; ?>>
				</td>
			</tr>

            <tr>
                <th>Purge Logs</th>
                <td>
                    <input type="checkbox" name="purge_waypoint_logs" value="1">
                </td>
            </tr>

            <tr>
                <th>Logs</th>
                <td>
                    <h3 class="nav-tab-wrapper">
                    <?php
                    $loggers = Waypoint_Logger_Container::get_instance()->get_loggers();
                    foreach( $loggers as $logger ) : ?>
                        <a href="#" class="nav-tab nav-tab-active">
		                    <?php echo $logger->get_label(); ?>
                        </a>
                    <?php endforeach; ?>
                    </h3>

                    <?php foreach( $loggers as $logger ) : ?>
                        <div class="log-tab" id="log-<?php echo $logger->get_key(); ?>">
                            <textarea style="width: 100%;" readonly rows="20"><?php echo esc_textarea( $logger->print_log() ); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </td>
            </tr>
		</table>

		<button type="submit" class="button button-primary">Save</button>
		<?php
	}

	public function save() {

	    $keys = [
            '_waypoint_settings_google_api_key'
        ];

	    foreach( $keys as $key ) {
	        if ( isset( $_POST[$key] ) ) {
	            $value = filter_var( $_POST[$key], FILTER_SANITIZE_STRING );
	            update_option( $key, $value );
            }
        }

        $checkboxes = [
            '_waypoint_settings_logger_enabled'
        ];

	    foreach( $checkboxes as $key ) {
	        if ( isset( $_POST[$key] ) ) {
		        $value = filter_var( $_POST[ $key ], FILTER_SANITIZE_STRING );
	            update_option( $key, $value );
            } else {
	            update_option( $key, '' );
            }
        }

        $this->add_success_message('Logger settings updated.');

	    // Delete log files.
	    if ( isset( $_POST['purge_waypoint_logs'] ) ) {
	        $logs = Waypoint_Logger_Container::get_instance()->get_loggers();
	        foreach( $logs as $log ) {
                $log->purge();
            }
            $this->add_success_message('Log files purged.');
        }
    }
}