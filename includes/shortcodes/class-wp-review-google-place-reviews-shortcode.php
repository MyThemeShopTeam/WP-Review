<?php
/**
 * Shortcode [wp-review-google-place-reviews]
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Google_Place_Reviews_Shortcode
 */
class WP_Review_Google_Place_Reviews_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @var string
	 */
	protected $name = 'wp-review-google-place-reviews';

	/**
	 * Shortcode alias.
	 *
	 * @var string
	 */
	protected $alias = 'wp_review_google_place_reviews';

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
			'place_id' => '',
			'review_num' => '',
		), $atts, $this->name );

		if ( ! $atts['place_id'] ) {
			return;
		}

		$response = $this->get_place( $atts['place_id'] );
		if ( ! $response ) {
			return;
		}

		$response = json_decode( $response, true );
		if ( empty( $response['result'] ) ) {
			$error = $response['status'];
			if ( ! empty( $response['error_message'] ) ) {
				$error .= ': ' . $response['error_message'];
			}
			return '<div class="wpr-error">' . $error . '</div>';
		}

		ob_start();
		wp_review_load_template( 'shortcodes/google-place-reviews.php', compact( 'response', 'atts' ) );
		return ob_get_clean();
	}

	/**
	 * Gets place detail.
	 *
	 * @param  string $place_id Place ID.
	 * @return string           Response string.
	 */
	protected function get_place( $place_id ) {
		$key = $this->get_cache_key( $place_id );
		$data = get_transient( $key );
		if ( $data ) {
			return $data;
		}

		$url = 'https://maps.googleapis.com/maps/api/place/details/json';
		$params = array(
			'key'     => wp_review_option( 'google_api_key' ),
			'placeid' => $place_id,
		);
		$response = wp_remote_get( add_query_arg( $params, $url ) );
		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		}
		if ( empty( $response['body'] ) ) {
			return;
		}

		set_transient( $key, $response['body'], DAY_IN_SECONDS );
		return $response['body'];
	}

	/**
	 * Gets cache key.
	 *
	 * @param  string $place_id Place ID.
	 * @return string
	 */
	protected function get_cache_key( $place_id ) {
		return sprintf( '%s_%s', $this->name, $place_id );
	}

}

$shortcode = new WP_Review_Google_Place_Reviews_Shortcode();
$shortcode->init();
