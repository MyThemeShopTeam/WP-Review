<?php
/**
 * Shortcode [wp-review-yelp-business-reviews]
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Yelp_Business_Shortcode_Reviews
 */
class WP_Review_Yelp_Business_Shortcode_Reviews {

	/**
	 * Shortcode name.
	 *
	 * @var string
	 */
	protected $name = 'wp-review-yelp-business-reviews';

	/**
	 * Shortcode alias.
	 *
	 * @var string
	 */
	protected $alias = 'wp_review_yelp_business_reviews';

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
			'id'    => '',
			'title' => '',
			'review_num' => '',
		), $atts, $this->name );

		$key = $this->get_cache_key( $atts );
		$response = get_transient( $key );
		$yelp_api = new WP_Review_Yelp_API();
		if ( ! $response ) {
			$response = $yelp_api->get_business_reviews( $atts['id'], $atts );
			if ( ! is_wp_error( $response ) ) {
				set_transient( $key, $response, DAY_IN_SECONDS ); // Should cache only 1 day.
			}
		}

		if ( is_wp_error( $response ) ) {
			return $this->get_error_message( $response );
		}

		if ( empty( $response['reviews'] ) ) {
			return __( 'There is no review', 'wp-review' );
		}

		// Get business data for schema data.
		$key = $this->get_business_cache_key( $atts );
		$business = get_transient( $key );
		if ( ! $business ) {
			$business = $yelp_api->get_business( $atts['id'], $atts );
			if ( ! is_wp_error( $business ) ) {
				set_transient( $key, $business, DAY_IN_SECONDS );
			}
		}
		if ( is_wp_error( $business ) ) {
			return $this->get_error_message( $response );
		}

		$reviews = $response['reviews'];
		if ( intval( $atts['review_num'] ) ) {
			$reviews = array_slice( $reviews, 0, intval( $atts['review_num'] ) );
		}

		ob_start();
		wp_review_load_template( 'shortcodes/yelp-business-reviews.php', compact( 'response', 'reviews', 'business', 'atts' ) );
		return ob_get_clean();
	}

	/**
	 * Gets error message from an error object.
	 *
	 * @param WP_Error $response Error object.
	 * @return string
	 */
	public function get_error_message( $response ) {
		return $response->get_error_code() . ': ' . $response->get_error_message();
	}

	/**
	 * Gets cache key.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string
	 */
	protected function get_cache_key( $atts ) {
		return sprintf( '%s_%s_reviews', $this->name, serialize( $atts ) );
	}

	/**
	 * Gets business cache key.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string
	 */
	protected function get_business_cache_key( $atts ) {
		return sprintf( '%s_%s', $this->name, serialize( $atts ) );
	}
}

$shortcode = new WP_Review_Yelp_Business_Shortcode_Reviews();
$shortcode->init();
