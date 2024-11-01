<?php
namespace Waypoint\Core\Administration;

use Waypoint\Core\Waypoint_Component_Abstract;

class Waypoint_Administration_Component extends Waypoint_Component_Abstract
{
	public function load()
	{
		require_once __DIR__ . '/waypoint-administration-settings-page-abstract.php';
		require_once __DIR__ . '/waypoint-administration-settings-page-container.php';
		require_once __DIR__ . '/waypoint-administration-settings-help-page.php';
	}

	public function register() {
		$this->load();
		Waypoint_Administration_Settings_Page_Container::get_instance();
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_admin_assets'] );

		// Load readme with a later hook, so that it displays towards the end of the tabs list.
		add_action( 'init', [$this, 'register_read_me_page'], 1000 );
	}

	public function register_read_me_page() {
        new Waypoint_Administration_Settings_Help_Page;
    }

	public function enqueue_admin_assets() {
		wp_enqueue_media();
		wp_enqueue_script( 'vuejs', WAYPOINT_PLUGIN_URL . 'assets/js/vue.min.js', ['jquery'], null, true );
		wp_enqueue_script( 'wp-media-picker-jquery', WAYPOINT_PLUGIN_URL . 'assets/js/wp-media-picker-jquery.js', ['jquery'], null, true );
	}
}