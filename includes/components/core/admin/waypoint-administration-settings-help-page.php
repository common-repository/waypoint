<?php
namespace Waypoint\Core\Administration;

class Waypoint_Administration_Settings_Help_Page extends Waypoint_Settings_Page_Abstract
{
    /**
     * Tab key/slug.
     */
    const KEY = 'read-me';

    /**
     * Tab label/Admin page label.
     */
    const LABEL = 'Read Me';

    public function __construct() {
        parent::__construct();
        $this->load();
    }

    public function load() {
        require_once WAYPOINT_PLUGIN_DIR . 'includes/lib/parsedown/Parsedown.php';
    }

    public function render() {
        $parsedown = new \Parsedown;
        $path = WAYPOINT_PLUGIN_DIR . 'README.md';
        echo $parsedown->text( file_get_contents( $path ) );
    }
}