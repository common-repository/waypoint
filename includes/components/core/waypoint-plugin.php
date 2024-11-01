<?php

namespace Waypoint\Core;

/**
 * Class Waypoint_Plugin
 *
 * @package Waypoint
 */
class Waypoint_Plugin {

    /** @var static */
    private static $instance;

    /**
     * @return Waypoint_Plugin
     */
    public static function get_instance() {
        if ( null === static::$instance ) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @var bool
     */
    protected $has_initialized = false;

    /**
     * @var Waypoint_Component_Abstract[]
     */
    protected $components = [];

    public function register_component( $class_name ) {
        if ( ! in_array( $class_name, $this->components ) ) {
            $this->components[] = $class_name;
        }
    }

    /**
     * Initialize plugins.
     */
    public function initialize() {
        add_action( 'init', function () {

            if ( $this->has_initialized ) {
                return false;
            }

            // Do nothing if no components are registered.
            if ( empty( $this->components ) ) {
                return false;
            }

            // Register components.
            foreach ( $this->components as $component ) {
                /** @var $the_component Waypoint_Component_Abstract */
                $the_component = new $component;
                $the_component->register();
            }

            $this->has_initialized = true;

            do_action( 'waypoint_loaded' );

            return true;
        }, 0 );
    }
}