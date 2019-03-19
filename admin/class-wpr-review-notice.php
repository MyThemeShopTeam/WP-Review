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
	 * Dismiss transient key.
	 *
	 * @var string
	 */
	private $dismiss_transient_key = 'wpr_review_notice_dismiss';

	/**
	 * Show the notice if number of reviews >= this value.
	 *
	 * @var int
	 */
	private $review_count;

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
			<img src="<?php echo esc_url( WP_REVIEW_URI . 'admin/assets/images/wp-review-pro.jpg' ); ?>" alt="">
			<p><?php esc_html_e( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla tempus lacinia interdum.', 'wp-review' ); ?></p>
			<p><?php esc_html_e( 'Etiam fringilla condimentum fermentum. Cras vestibulum tempor ligula sit amet fermentum. Vestibulum tempor purus eu elit mattis, vel ultrices urna convallis.', 'wp-review' ); ?></p>
			<div class="wpr-review-notice-btns">
				<a href="https://wordpress.org/support/plugin/wp-review/reviews/?filter=5#new-post" class="button button-primary wpr-review-notice-btn-dismiss" target="_blank">
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
			}
			.wpr-review-notice:after {
				content: " ";
				height: 0;
				visibility: hidden;
				display: block;
				clear: both;
			}
			.wpr-review-notice img {
				width: 100px;
				height: 100px;
				padding: 20px 30px 30px;
				float: left;
			}
			.wpr-review-notice p {
				font-size: 1.2em;
			}
			.wpr-review-notice-btns {
				padding-top: 10px;
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
			set_transient( $this->dismiss_transient_key, 1, MONTH_IN_SECONDS );
		} else {
			set_transient( $this->dismiss_transient_key, 1 );
		}
	}

	/**
	 * Checks if should show the notice.
	 */
	private function should_show() {
		if ( get_transient( $this->dismiss_transient_key ) ) {
			return false;
		}
		$query = wp_review_get_reviews_query( 'latest', array( 'post_num' => $this->review_count ) );
		return intval( $query->found_posts ) === $this->review_count;
	}
}

$review_notice = new WPR_Review_Notice();
$review_notice->init();
