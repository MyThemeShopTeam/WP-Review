<?php
/**
 * Shortcode [wp-review-facebook-reviews]
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Facebook_Reviews_Shortcode
 */
class WP_Review_Facebook_Reviews_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @var string
	 */
	protected $name = 'wp-review-facebook-reviews';

	/**
	 * Shortcode alias.
	 *
	 * @var string
	 */
	protected $alias = 'wp_review_facebook_reviews';

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
			'page_id' => '',
			'limit'   => 5,
		), $atts, $this->name );

		if ( ! $atts['page_id'] ) {
			return;
		}

		$args = array( 'limit' => $atts['limit'] );
		$response = $this->get_page_reviews( $atts['page_id'], $args );
		if ( is_wp_error( $response ) ) {
			delete_transient( $this->get_cache_key( $atts['page_id'], $args ) );
			delete_transient( "wp_review_fb_page_{$atts['page_id']}_access_token" );
			return wp_kses_post( $response->get_error_message() );
		}

		$page = $this->get_page( $atts['page_id'] );
		if ( is_wp_error( $page ) ) {
			delete_transient( $this->get_page_cache_key( $atts['page_id'] ) );
			delete_transient( "wp_review_fb_page_{$atts['page_id']}_access_token" );
			return wp_kses_post( $page->get_error_message() );
		}

		ob_start();
		wp_review_load_template( 'shortcodes/facebook-reviews.php', compact( 'response', 'page', 'atts' ) );
		return ob_get_clean();
	}

	/**
	 * Gets page reviews.
	 *
	 * @param  array $page_id Page ID.
	 * @param  array $args    Custom arguments.
	 * @return string          Response string.
	 */
	protected function get_page_reviews( $page_id, $args = array() ) {
		$key = $this->get_cache_key( $page_id, $args );
		$data = get_transient( $key );
		if ( $data ) {
			return $data;
		}

		var_dump( 'Process...' );

		$page_access_token = get_transient( "wp_review_fb_page_token_{$page_id}" );
		if ( ! $page_access_token ) {
			return new WP_Error( 'empty-page-token', '<div class="wpr-error">' . __( 'Please re-generate Facebook page access token.', 'wp-review' ) . '</div>' );
		}

		$fb_api = new WP_Review_Facebook_API();
		$api_version = WP_REVIEW_GRAPH_API_VERSION;
		$url = "https://graph.facebook.com/v{$api_version}/{$page_id}/ratings";
		$args['access_token'] = $page_access_token;

		$response = $fb_api->get( $url, $args );

		if ( ! is_wp_error( $response ) ) {
			set_transient( $key, $response, DAY_IN_SECONDS );
		}

		return $response;
	}

	/**
	 * Gets facebook page data.
	 *
	 * @param  string $page_id Page ID.
	 * @return string|WP_Error
	 */
	protected function get_page( $page_id ) {
		$key = $this->get_page_cache_key( $page_id );
		$data = get_transient( $key );
		if ( $data ) {
			return $data;
		}

		$page_access_token = get_transient( "wp_review_fb_page_token_{$page_id}" );
		if ( ! $page_access_token ) {
			return new WP_Error( 'empty-page-token', '<div class="wpr-error">' . __( 'Please re-generate Facebook page access token.', 'wp-review' ) . '</div>' );
		}

		$fb_api = new WP_Review_Facebook_API();
		$api_version = WP_REVIEW_GRAPH_API_VERSION;
		$url = "https://graph.facebook.com/v{$api_version}/{$page_id}";
		$args['access_token'] = $page_access_token;

		$response = $fb_api->get( $url, $args );

		if ( ! is_wp_error( $response ) ) {
			set_transient( $key, $response, DAY_IN_SECONDS );
		}

		return $response;
	}

	/**
	 * Gets cache key.
	 *
	 * @param  string $page_id Page ID.
	 * @param  array  $args    Custom arguments.
	 * @return string
	 */
	protected function get_cache_key( $page_id, $args = array() ) {
		return sprintf( '%s_%s_%s', $this->name, $page_id, serialize( $args ) );
	}

	/**
	 * Gets page cache key.
	 *
	 * @param  string $page_id Page ID.
	 * @return string
	 */
	protected function get_page_cache_key( $page_id ) {
		return "wp_review_facebook_page_{$page_id}";
	}
}

$shortcode = new WP_Review_Facebook_Reviews_Shortcode();
$shortcode->init();
