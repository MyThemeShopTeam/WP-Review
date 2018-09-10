<?php
/**
 * Import options
 *
 * @package WP_Review
 */

?>

<div class="wp-review-field">
	<div class="wp-review-field-label">
		<label><?php esc_html_e( 'Demo data import', 'wp-review' ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<button type="button" id="wp-review-import-demo-button" class="button" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_review_import_demo' ) ); ?>"><?php esc_html_e( 'Import', 'wp-review' ); ?></button>
		<p class="description"><?php esc_html_e( 'Click above button to import demo data, imported data will be saved as new draft posts.', 'wp-review' ); ?></p>
	</div>
</div>

<div class="wp-review-field">
	<div class="wp-review-field-label">
		<label for="wp-review-import-source"><?php esc_html_e( 'Select plugin', 'wp-review' ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<select id="wp-review-import-source">
			<option value="">---</option>
			<option value="yet-another-stars-rating">Yasr – Yet Another Stars Rating</option>
			<option value="author-hreview">Author hReview</option>
			<option value="wp-rich-snippets">WP Rich Snippets</option>
			<option value="ultimate-reviews">Reviews</option>
			<option value="wp-product-review">WP Product Review</option>
			<option value="gd-rating-system">GD Rating System</option>
		</select>

		<span class="wp-review-disabled">
			<?php submit_button( __( 'Import', 'wp-review' ), 'large', 'wp-review-import', false, array( 'disabled' => 'disabled' ) ); ?>
		</span>

		<p class="description">
			<?php
			printf(
				'<strong>%1$s</strong>: %2$s ',
				esc_html__( 'Warning', 'wp-review' ),
				esc_html__( 'This action is IRREVERSIBLE! Take a backup of your database before proceeding.', 'wp-review' )
			);
			?>
		</p>

		<input type="hidden" id="wp-review-import-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp_review_import_rating' ) ); ?>">
	</div>
</div>

<div class="wp-review-field">
	<div class="wp-review-field-label">
		<label><?php esc_html_e( 'Import settings', 'wp-review' ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<button type="button" class="button" onclick="jQuery(this).next().slideToggle( 'fast' )"><?php esc_html_e( 'Import code', 'wp-review' ); ?></button>
		<div style="display: none;">
			<p class="description"><?php esc_html_e( 'Insert your backup code below and hit Import to restore your plugin options from a backup.', 'wp-review' ); ?></p>
			<p><textarea id="wp-review-import-options-code" class="widefat" cols="30" rows="10"></textarea></p>
			<p><button type="button" class="button button-primary" id="wp-review-import-options-btn"><?php esc_html_e( 'Import', 'wp-review' ); ?></button></p>
		</div>
	</div>
</div>

<div class="wp-review-field">
	<div class="wp-review-field-label">
		<label><?php esc_html_e( 'Export settings', 'wp-review' ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<button type="button" class="button" onclick="jQuery(this).next().slideToggle( 'fast' )"><?php esc_html_e( 'Show export code', 'wp-review' ); ?></button>
		<p style="display: none;"><textarea class="widefat" cols="30" rows="10" onfocus="this.select()"><?php echo esc_textarea( wp_review_get_options_export_code() ); ?></textarea></p>
	</div>
</div>
