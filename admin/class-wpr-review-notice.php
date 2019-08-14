<?php
/**
 * Review notice
 *
 * @package WP_Review
 */

/**
 * Class WPR_Review_Notice
 *
 * @since 5.2.1
 */
class WPR_Review_Notice {

	/**
	 * Dismiss key.
	 *
	 * @var string
	 */
	private $dismiss_key = 'wpr_review_notice_dismiss';
	
	/**
	 * Show on later date key.
	 *
	 * @var string
	 */
	private $dismiss_later_key = 'wpr_review_notice_later';

	/**
	 * Show the notice if number of reviews >= this value.
	 *
	 * @var int
	 */
	private $review_count = 10;

	/**
	 * Initializes class.
	 */
	public function init() {
		add_action( 'admin_notices', array( $this, 'show_notice' ) );
		add_action( 'wp_ajax_wpr_dismiss_review_notice', array( $this, 'handle_dismiss' ) );
	}

	/**
	 * Shows the notice.
	 */
	public function show_notice() {
		if ( ! $this->should_show() ) {
			return;
		}
		?>
		<div id="wpr-review-notice" class="notice is-dismissible wpr-review-notice">
			<div class="wp-review-star dashicons dashicons-star-filled"></div>
			<p><?php esc_html_e( 'Hey, we noticed you have created over 10 reviews from WP Review - that’s awesome! Could you please do us a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'wp-review' ); ?></p>
			<div class="wpr-review-notice-btns">
				<a href="https://wordpress.org/support/plugin/wp-review/reviews/?rate=5#new-post" class="button button-primary wpr-review-notice-btn-dismiss" target="_blank">
					<?php esc_html_e( 'Ok, you deserve it', 'wp-review' ); ?>
				</a>
				<button type="button" class="button button-link wpr-review-notice-btn-later">
					<span class="dashicons dashicons-calendar"></span>
					<?php esc_html_e( 'Nope, maybe later', 'wp-review' ); ?>
				</button>
				<button type="button" class="button button-link wpr-review-notice-btn-dismiss">
					<span class="dashicons dashicons-smiley"></span>
					<?php esc_html_e( 'I already did', 'wp-review' ); ?>
				</button>
			</div>
		</div>

		<style type="text/css">
			.wpr-review-notice {
				padding-left: 0;
				padding-top: 10px;
				border: 3px solid #f44336;
			}
			.wpr-review-notice:after {
				content: " ";
				height: 0;
				visibility: hidden;
				display: block;
				clear: both;
			}
			.wpr-review-notice .wp-review-star {
				font-size: 58px;
				width: 90px;
				height: 90px;
				line-height: 90px;
				float: left;
				margin: 5px 15px 15px 15px;
				color: #FFC107;
				background: #F44336;
				text-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
				border-radius: 10px;
			}
			.wpr-review-notice p {
				font-size: 14px;
			}
			.wpr-review-notice-btns a,
			.wpr-review-notice-btns button {
				margin-right: 10px !important;
				text-decoration: none !important;
			}

			.wpr-review-notice-btns .dashicons {
				margin-top: 3px;
				color: #444;
			}
		</style>
		<?php
		wp_enqueue_script( 'wpr-review-notice', WP_REVIEW_URI . 'admin/assets/js/review-notice.js', array( 'jquery' ), WP_REVIEW_PLUGIN_VERSION, true );
	}

	/**
	 * Handles dismiss notice.
	 */
	public function handle_dismiss() {
		$later = ! empty( $_POST['later'] );
		if ( $later ) {
			update_option( $this->dismiss_later_key, time() + MONTH_IN_SECONDS );
		} else {
			update_option( $this->dismiss_key, '1' );
		}
	}

	/**
	 * Checks if should show the notice.
	 */
	private function should_show() {
		
		if ( get_option( $this->dismiss_key ) ) {
			return false;
		}

		$later_date = absint( get_option( $this->dismiss_later_key ) );
		if ( $later_date > time() ) {
			return false;
		}
		
		// Backwards compatibility for transients used in versions <= 5.2.5
		if ( get_transient( 'wpr_review_notice_dismiss' ) ) {
			update_option( $this->dismiss_key, '1' );
			return false;
		}

		$query = wp_review_get_reviews_query(
			'latest',
			array(
				'post_num'    => $this->review_count,
				'post_status' => 'any',
			)
		);
		return intval( $query->post_count ) === $this->review_count;
	}
}

$review_notice = new WPR_Review_Notice();
$review_notice->init();
