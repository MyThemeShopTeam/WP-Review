<?php
/**
 * Notification bar options
 *
 * @package WP_Review
 */

?>
<div data-nav-tabs>
	<div class="nav-tab-wrapper">
		<a href="#hello-bar-content" class="nav-tab nav-tab-active" data-tab="hello-bar-content"><?php esc_html_e( 'Content', 'wp-review' ); ?></a>
		<a href="#hello-bar-styling" class="nav-tab" data-tab="hello-bar-styling"><?php esc_html_e( 'Styling', 'wp-review' ); ?></a>
	</div>

	<div id="hello-bar-content" class="tab-content">
		<h3><?php esc_html_e( 'Content Settings', 'wp-review' ); ?></h3>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Enable Global Notification Bar', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_hello_bar_enable',
						'name'     => 'wp_review_hello_bar[enable]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="hide-if-hello-bar-disable">

			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label for="wp_review_text"><?php esc_html_e( 'Text', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>
				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block">
						<input name="wp_review_hello_bar[text]" id="wp_review_text" class="large-text" type="text" disabled>
					</span>
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label for="wp_review_star_rating"><?php esc_html_e( 'Star Rating', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>
				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block">
						<input name="wp_review_hello_bar[star_rating]" id="wp_review_star_rating" class="small-text" type="number" min="0.5" max="5" step="0.5" value="5" disabled>
					</span>
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label for="wp_review_price"><?php esc_html_e( 'Price', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>
				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block">
						<input name="wp_review_hello_bar[price]" id="wp_review_price" type="text" value="$20" disabled>
					</span>
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label for="wp_review_button_label"><?php esc_html_e( 'Button label', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>
				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block">
						<input name="wp_review_hello_bar[button_label]" id="wp_review_button_label" type="text" value="<?php esc_attr_e( 'Buy Now', 'wp-review' ); ?>" disabled>
					</span>
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label for="wp_review_button_url"><?php esc_html_e( 'Button URL', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>
				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block">
						<input name="wp_review_hello_bar[button_url]" id="wp_review_button_url" class="large-text" type="text" value="#" disabled>
					</span>
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label><?php esc_html_e( 'Open link in new tab', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>
				<div class="wp-review-field-option">
					<?php
					$form_field->render_switch(
						array(
							'id'       => 'wp_review_hello_bar_target_blank',
							'name'     => 'wp_review_hello_bar[target_blank]',
							'disabled' => true,
						)
					);
					?>
				</div>
			</div>
		</div>
	</div><!-- End .settings-tab-content -->

	<div id="hello-bar-styling" style="display: none;" class="tab-content">
		<h3><?php esc_html_e( 'Styling Settings', 'wp-review' ); ?></h3>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_hello_bar_location"><?php esc_html_e( 'Location', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<select name="wp_review_hello_bar[location]" id="wp_review_hello_bar_location" disabled>
						<option value="top"><?php esc_html_e( 'Top', 'wp-review' ); ?></option>
						<option value="bottom"><?php esc_html_e( 'Bottom', 'wp-review' ); ?></option>
					</select>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_max_container"><?php esc_html_e( 'Max container(px/%)', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block">
					<input name="wp_review_hello_bar[max_container]" id="wp_review_max_container" type="text" value="1010px" disabled>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_bg_color"><?php esc_html_e( 'Background color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" class="wp-review-color" name="wp_review_hello_bar[bg_color]" id="wp_review_bg_color" disabled>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_bg_image"><?php esc_html_e( 'Background image', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wpr_image_upload_field wp-review-disabled">
					<span class="clearfix" id="wp_review_bg_image-preview"></span>
					<button type="button" class="button" name="wp_review_bg_image-upload" id="wp_review_bg_image-upload" data-id="wp_review_bg_image" disabled><?php esc_html_e( 'Select Image', 'wp-review' ); ?></button>
					<span class="clear"></span>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_text_color"><?php esc_html_e( 'Text color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" class="wp-review-color" name="wp_review_hello_bar[text_color]" id="wp_review_text_color" disabled>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_star_color"><?php esc_html_e( 'Star color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" class="wp-review-color" name="wp_review_hello_bar[star_color]" id="wp_review_star_color" disabled>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_button_bg_color"><?php esc_html_e( 'Button background color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" class="wp-review-color" name="wp_review_hello_bar[button_bg_color]" id="wp_review_button_bg_color" disabled>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_button_text_color"><?php esc_html_e( 'Button text color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" class="wp-review-color" name="wp_review_hello_bar[button_text_color]" id="wp_review_button_text_color" disabled>
				</span>
			</div>
		</div>
	</div><!-- End .settings-tab-styling -->
</div>
