<?php
namespace Waypoint\Core\Maps;

use Waypoint\Core\Locations\Waypoint_Location_Query;

class Waypoint_Map_Info_Window_Template
{
	protected $default_variables = [
		'post_title',
		'post_content',
		'excerpt',
		'street_address',
		'city',
		'state',
		'zip_code',
		'country',
	];

	/**
	 * @return array
	 */
	public function get_variables()
	{
		return apply_filters( 'waypoint_map_google_info_window_content_variables_array', $this->default_variables );
	}

	public function prepare_variables( $post ) {

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! isset( $post->meta ) ) {
			$post->meta = Waypoint_Location_Query::get_meta( $post->ID );
		}

		// Prepare replacements.
		$variables = [
			'post_title'     => $post->post_title,
			'post_content'   => $post->post_content,
			'excerpt'        => $post->post_excerpt,
			'street_address' => $post->meta['street_address'],
			'city'           => $post->meta['city'],
			'state'          => $post->meta['state'],
			'zip_code'       => $post->meta['zip_code'],
			'country'        => $post->meta['country']
		];

		$variables = apply_filters( 'waypoint_location_info_window_content_prepare_variables', $variables, $post->ID );

		return $variables;
	}

	/**
	 * @param $template string
	 * @param $variables array of keys
	 *
	 * @return string
	 */
	public function replace_variables( $template, $variables ) {

		foreach ( $variables as $search => $replace ) {
			$template = str_replace( '{'. $search . '}', $replace, $template );
		}

		return wpautop( $template );
	}

	/**
	 * @var static
	 */
	private static $instance;

	/**
	 * @return Waypoint_Map_Info_Window_Template
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static;
		}
		return static::$instance;
	}
}