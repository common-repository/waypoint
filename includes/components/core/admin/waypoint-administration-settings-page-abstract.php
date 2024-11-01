<?php

namespace Waypoint\Core\Administration;

abstract class Waypoint_Settings_Page_Abstract {

	/**
	 * Tab key/slug.
	 */
	const KEY = 'tab-key';

	/**
	 * Tab label/Admin page label.
	 */
	const LABEL = 'Tab Label';

	/**
	 * Add map settings to tabs.
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function add_admin_page_tab( $tabs ) {

		if ( ! isset( $tabs[ static::KEY ] ) ) {
			$tabs[ static::KEY ] = static::LABEL;
		}

		return $tabs;
	}

	/**
	 * Render the settings page HTML.
	 */
	public function render() {
		?>
		Override render() function.
		<?php
	}

	public function _save() {

	    // Do not fire save hook if not viewing the settings page.
	    if (
            ! is_admin() ||
            ! isset( $_GET['page'] ) ||
            'waypoint-settings' !== $_GET['page']
        ) {
	        return;
        }

        // No POST, no FILES, no love.
        if ( empty( $_POST ) && empty( $_FILES ) ) {
            return;
        }

        // Get current tab.
	    if ( ! isset( $_GET['tab'] ) ) {
	        $tab = Waypoint_Administration_Settings_Page_Container::get_instance()->get_current_tab();
        } else {
	        $tab = $_GET['tab'];
        }

        if ( $tab === static::KEY ) {
            $this->save();
        }
    }

    /** Overwrite in extending classes. */
	public function save() {
        echo '<pre>'; print_r( $_POST ); echo '</pre>';
	}

    /**
     * @var array An array of error notices admin messages.
     */
	protected $errors = [];

    /**
     * @var array An array of success notice admin messages.
     */
	protected $notices = [];

	/**
	 * Prints admin error notices.
	 */
	protected function print_errors() {
	    if ( empty( $this->errors ) ) {
	        return;
        }
        ?>
        <div class="notice notice-error is-dismissible">
            <?php foreach( $this->errors as $message ) : ?>
            <p><?php _e( $message, 'waypoint' ); ?></p>
            <?php endforeach; ?>
        </div>
        <?php
    }

	/**
	 * Print admin success notices.
	 */
    protected function print_notices() {
	    if ( empty( $this->notices ) ) {
		    return;
	    }
	    ?>
        <div class="notice notice-success is-dismissible">
		    <?php foreach ( $this->notices as $message ) : ?>
                <p><?php _e( $message, 'waypoint' ); ?></p>
		    <?php endforeach; ?>
        </div>
	    <?php
    }

	/**
     * Adds an admin error notice.
	 * @param $message
	 */
    public function add_error_message( $message ) {
	    $this->errors[] = $message;
    }

	/**
	 * @param $message
	 */
    public function add_success_message( $message ) {
        $this->notices[] = $message;
    }

	/**
	 * @param $callbacks
	 *
	 * @return mixed
	 */
	public function register_settings_page_callback( $callbacks ) {

		$this->print_errors();
	    $this->print_notices();

		if ( ! isset( $callbacks[ static::KEY ] ) ) {
			$callbacks[ static::KEY ] = function () {
				$this->render();
			};
		}

		return $callbacks;
	}

	public function __construct() {
		add_filter( 'waypoint_location_settings_admin_tabs', [ $this, 'add_admin_page_tab' ] );
		add_filter( 'waypoint_location_settings_submenu_pages_callbacks', [ $this, 'register_settings_page_callback' ] );
		add_action( 'waypoint_save_administration_settings', [$this, '_save'] );
	}
}