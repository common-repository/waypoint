<?php
namespace Waypoint\Core\Logger;

class Waypoint_Logger_Abstract
{
	const KEY = 'log';

	const LABEL = 'Log';

	private function __construct() {
		add_filter( 'waypoint_logger_log_entry', [$this, '_add_log_timestamp'] );
		add_filter( 'waypoint_logger_log_entry', [$this, '_add_log_footer'], 100 );
	}

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * @return Waypoint_Logger_Abstract
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	public static function register() {
		Waypoint_Logger_Container::get_instance()->register_logger( static::get_instance() );
	}


	public function get_key() {
		return static::KEY;
	}

	public function get_label() {
		return static::LABEL;
	}

	public function get_log_path()
	{
		$dir = wp_get_upload_dir();
		$basedir = $dir['basedir'];
		$path = $basedir . '/waypoint/';
		if ( ! is_dir( $path ) ) {
			mkdir( $path );
		}
		$path .= 'logs/';
		if ( ! is_dir( $path ) ) {
			mkdir( $path );
		}

		return $path . $this->get_filename();
	}

	public function get_filename() {
		return trim( static::get_key() ) . '.log';
	}

	public function print_log()
	{
		$path = $this->get_log_path();

		if ( file_exists( $path ) ) {
			return file_get_contents( $path );
		}

		return '';
	}

	public function get_log_separator()
	{
		$separator = '#######################' . "\n";
		return $separator;
	}

	public function _add_log_timestamp( $content ) {
		$separator = $this->get_log_separator();
		$timestamp  = $separator;
		$timestamp .= '# ' . current_time( 'Y-m-d h:i:s A') . "\n";
		$timestamp .= $separator;
		return $timestamp . $content ;
	}

	public function _add_log_footer( $content )
	{
		return $content . "\n" . $this->get_log_separator() . "\n\n";
	}

	/**
	 * Write content to the log.
	 * @param $content
	 */
	public function add( $content )
	{
		if ( ! is_string( $content ) ) {
			$content = json_encode( $content, JSON_PRETTY_PRINT );
		}

		$content = apply_filters( 'waypoint_logger_log_entry', $content, $this );

		$path = $this->get_log_path();

		file_put_contents( $path, $content, FILE_APPEND );
	}

	public function purge() {
		$path = $this->get_log_path();
		if ( file_exists( $path ) ) {
			unlink( $path );
		}
	}
}