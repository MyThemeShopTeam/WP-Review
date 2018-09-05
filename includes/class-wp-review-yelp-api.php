<?php
/**
 * Yelp API class
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Yelp_API
 */
class WP_Review_Yelp_API {

	/**
	 * API host.
	 *
	 * @var string
	 */
	protected $api_host = 'https://api.yelp.com';

	/**
	 * Search path.
	 *
	 * @var string
	 */
	protected $search_path = '/v3/businesses/search';

	/**
	 * Busines path.
	 *
	 * @var string
	 */
	protected $business_path = '/v3/businesses/';  // Business ID will come after slash.

	/**
	 * Search limit.
	 *
	 * @var int
	 */
	protected $search_limit = 3;

	/**
	 * Shows exception error.
	 *
	 * @param Exception $exception Exception.
	 */
	protected function show_exception( Exception $exception ) {
		trigger_error(
			sprintf(
				/* translators: 1: error code, 2: error message */
				esc_html__( 'Curl failed with error #%1$s: %2$s', 'wp-review' ),
				esc_html( $exception->getCode() ),
				esc_html( $exception->getMessage() )
			),
			E_USER_ERROR
		);
	}

	/**
	 * Gets API Key.
	 *
	 * @return string
	 */
	protected function get_api_key() {
		return wp_review_option( 'yelp_api_key' );
	}

	/**
	 * Makes a request to the Yelp API and returns the response.
	 *
	 * @param string $host       The domain host of the API.
	 * @param string $path       The path of the API after the domain.
	 * @param array  $url_params Array of query-string parameters.
	 * @return array|WP_Error    The response from the request or WP_Error on failure.
	 */
	protected function request( $host, $path, $url_params = array() ) {
		// Send Yelp API Call.
		try {
			$api_key = $this->get_api_key();

			if ( ! $api_key ) {
				throw new Exception( __( 'Can not get Yelp API Key', 'wp-review' ) );
			}

			$curl = curl_init();

			if ( false === $curl ) {
				throw new Exception( __( 'Failed to initialize CURL', 'wp-review' ) );
			}

			$url = $host . $path;
			if ( $url_params ) {
				$url .= '?' . http_build_query( $url_params );
			}

			curl_setopt_array( $curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,  // Capture response.
				CURLOPT_ENCODING => '',  // Accept gzip/deflate/whatever.
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'authorization: Bearer ' . $api_key,
					'cache-control: no-cache',
				),
			) );

			$response = curl_exec( $curl );
			if ( false === $response ) {
				throw new Exception( curl_error( $curl ), curl_errno( $curl ) );
			}

			$http_status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
			if ( 200 != $http_status ) {
				throw new Exception( $response, $http_status );
			}

			curl_close( $curl );
		} catch ( Exception $e ) {
			return new WP_Error( 'yelp-request', $e->getMessage() );
		}

		return json_decode( $response, true );
	}

	/**
	 * Query the Search API by a search term and location.
	 *
	 * @param string $term     The search term passed to the API.
	 * @param string $location The search location passed to the API.
	 * @param array  $args     Custom arguments.
	 * @return array
	 */
	public function search( $term, $location, $args = array() ) {
		$url_params = $args;
		$url_params['term'] = $term;
		$url_params['location'] = $location;
		return $this->request( $this->api_host, $this->search_path, $url_params );
	}

	/**
	 * Query the Business API by business_id.
	 *
	 * @param string $business_id The ID of the business to query.
	 * @param array  $args        Custom arguments.
	 * @return array
	 */
	public function get_business( $business_id, $args = array() ) {
		$path = $this->business_path . urlencode( $business_id );
		return $this->request( $this->api_host, $path, $args );
	}

	/**
	 * Query the Business Reviews by business_id.
	 *
	 * @param string $business_id The ID of the business to query.
	 * @param array  $args        Custom arguments.
	 * @return array
	 */
	public function get_business_reviews( $business_id, $args = array() ) {
		$path = $this->business_path . urlencode( $business_id ) . '/reviews';
		return $this->request( $this->api_host, $path, $args );
	}
}
