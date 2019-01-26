<?php
/**
 * Yelp options
 *
 * @package WP_Review
 */

?>

<div class="wp-review-field">
	<div class="wp-review-disabled wp-review-field-label">
		<label for="wp_review_yelp_api_key"><?php esc_html_e( 'API Key', 'wp-review' ); ?></label>
		<?php wp_review_print_pro_text(); ?>
	</div>

	<div class="wp-review-field-option">
		<span class="wp-review-disabled inline-block large-text">
			<input name="wp_review_options[yelp_api_key]" id="wp_review_yelp_api_key" class="widefat" type="password" disabled>
		</span>
	</div>

	<span class="description">
		<?php
		printf(
			/* translators: Yelp App link. */
			esc_html__( '%s to get Yelp API Key.', 'wp-review' ),
			'<a href="https://mythemeshop.com/kb/wp-review-pro/yelp-reviews/" target="_blank">' . esc_html__( 'Click here', 'wp-review' ) . '</a>'
		);
		?>
	</span>
</div>
