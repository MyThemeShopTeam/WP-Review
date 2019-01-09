<?php
/**
 * Facebook options
 *
 * @package WP_Review
 */

?>
<div class="wp-review-field no-flex">
	<?php
	printf(
		/* translators: Facebook App link. */
		esc_html__( '%s to create Facebook App. Remember to add your domain to app.', 'wp-review' ),
		'<a href="https://mythemeshop.com/kb/wp-review-pro/facebook-reviews/" target="_blank">' . esc_html__( 'Click here', 'wp-review' ) . '</a>'
	);
	?>
</div>

<div class="wp-review-field">
	<div class="wp-review-disabled wp-review-field-label">
		<label for="wp_review_facebook_app_id"><?php esc_html_e( 'App ID', 'wp-review' ); ?></label>
		<?php wp_review_print_pro_text(); ?>
	</div>

	<div class="wp-review-field-option">
		<span class="wp-review-disabled inline-block large-text">
			<input name="wp_review_options[facebook_app_id]" id="wp_review_facebook_app_id" type="text" class="widefat" disabled>
		</span>
	</div>
</div>

<div class="wp-review-field">
	<div class="wp-review-disabled wp-review-field-label">
		<label for="wp_review_facebook_app_secret"><?php esc_html_e( 'App secret', 'wp-review' ); ?></label>
		<?php wp_review_print_pro_text(); ?>
	</div>

	<div class="wp-review-field-option">
		<span class="wp-review-disabled inline-block large-text">
			<input name="wp_review_options[facebook_app_secret]" id="wp_review_facebook_app_secret" class="widefat" type="password" disabled>
		</span>
	</div>
</div>

<p class="description"><?php esc_html_e( 'Please re-generate access token in shortcodes and widgets each time you change App ID or App secret.', 'wp-review' ); ?></p>
