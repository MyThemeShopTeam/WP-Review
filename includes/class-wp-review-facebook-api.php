<?php
/**
 * Facebook API
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Facebook_API
 */
class WP_Review_Facebook_API {

	/**
	 * API version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Cache key of facebook page access token.
	 *
	 * @var string
	 */
	protected $page_token_cache_key = 'wp_review_fb_page_token';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->version = WP_REVIEW_GRAPH_API_VERSION;
	}

	/**
	 * Gets App ID.
	 *
	 * @return string
	 */
	public function app_id() {
		return wp_review_option( 'facebook_app_id' );
	}

	/**
	 * Gets App secret.
	 *
	 * @return string
	 */
	public function app_secret() {
		return wp_review_option( 'facebook_app_secret' );
	}

	/**
	 * Runs a get request to API.
	 *
	 * @param  string $url    Request URL.
	 * @param  array  $params URL params.
	 * @return array|WP_Error
	 */
	public function get( $url, $params ) {
		$url = add_query_arg( $params, $url );
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$response = json_decode( $response['body'] );
		if ( ! empty( $response->error ) ) {
			return new WP_Error( $response->error->code, $response->error->message, $response->error->type );
		}
		return $response;
	}

	/**
	 * Generates long lived token from short lived token.
	 *
	 * @param  string $short_lived_token Short lived token.
	 * @return string|WP_Error
	 */
	public function exchange_token( $short_lived_token ) {
		$url = "https://graph.facebook.com/v{$this->version}/oauth/access_token";
		$params = array(
			'grant_type'        => 'fb_exchange_token',
			'client_id'         => $this->app_id(),
			'client_secret'     => $this->app_secret(),
			'fb_exchange_token' => $short_lived_token,
		);
		$response = $this->get( $url, $params );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( empty( $response->access_token ) ) {
			return new WP_Error( 'can-get-long-lived-token', __( 'Can\'t generate long lived token.', 'wp-review' ) );
		}
		return $response->access_token;
	}

	/**
	 * Generates permanent token.
	 *
	 * @param  string $page_id          Page ID.
	 * @param  string $long_lived_token Long lived token.
	 * @return string|WP_Error
	 */
	public function generate_permanent_token( $page_id, $long_lived_token ) {
		$url = "https://graph.facebook.com/v{$this->version}/{$page_id}";
		$params = array(
			'fields'       => 'access_token',
			'access_token' => $long_lived_token,
		);
		$response = $this->get( $url, $params );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( empty( $response->access_token ) ) {
			return new WP_Error( 'can-get-permanent-token', __( 'Can\'t generate permanent token or maybe you don\'t own that page.', 'wp-review' ) );
		}
		return $response->access_token;
	}

	/**
	 * Generates page access token.
	 *
	 * @param  string $page_id           Page ID.
	 * @param  string $short_lived_token Short lived token.
	 * @return string|WP_Error
	 */
	public function generate_page_token( $page_id, $short_lived_token ) {
		$long_lived_token = $this->exchange_token( $short_lived_token );
		if ( is_wp_error( $long_lived_token ) ) {
			return $long_lived_token;
		}
		return $this->generate_permanent_token( $page_id, $long_lived_token );
	}
}
