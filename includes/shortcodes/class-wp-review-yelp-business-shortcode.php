<?php
/**
 * Shortcode [wp-review-yelp-business]
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Yelp_Business_Shortcode
 */
class WP_Review_Yelp_Business_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @var string
	 */
	protected $name = 'wp-review-yelp-business';

	/**
	 * Shortcode alias.
	 *
	 * @var string
	 */
	protected $alias = 'wp_review_yelp_business';

	/**
	 * Class init.
	 */
	public function init() {
		add_shortcode( $this->name, array( $this, 'render' ) );
		add_shortcode( $this->alias, array( $this, 'render' ) );
	}

	/**
	 * Renders shortcode.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '',
		), $atts, $this->name );

		$key = $this->get_cache_key( $atts );
		$response = get_transient( $key );
		if ( ! $response ) {
			$yelp_api = new WP_Review_Yelp_API();
			$response = $yelp_api->get_business( $atts['id'], $atts );
			if ( ! is_wp_error( $response ) ) {
				set_transient( $key, $response, DAY_IN_SECONDS ); // Should cache only 1 day.
			}
		}

		if ( is_wp_error( $response ) ) {
			return $this->get_error_message( $response );
		}

		ob_start();
		wp_review_load_template( 'shortcodes/yelp-business.php', compact( 'response', 'atts' ) );
		return ob_get_clean();
	}

	/**
	 * Gets error message from an error object.
	 *
	 * @param WP_Error $response Error object.
	 * @return string
	 */
	public function get_error_message( $response ) {
		$response = json_decode( $response->get_error_message() );
		return $response->error->code . ': ' . $response->error->description;
	}

	/**
	 * Gets cache key.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string
	 */
	protected function get_cache_key( $atts ) {
		return sprintf( '%s_%s', $this->name, serialize( $atts ) );
	}
}

$shortcode = new WP_Review_Yelp_Business_Shortcode();
$shortcode->init();
