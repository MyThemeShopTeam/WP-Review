<?php
/**
 * Notification bar options
 *
 * @package WP_Review
 */

$options = wp_review_hello_bar_option();
$options = wp_parse_args( $options, wp_review_hello_bar_defaults() );

$bg_image = wp_parse_args( $options['bg_image'], array(
	'id'  => '',
	'url' => '',
) );
?>
<div data-nav-tabs>
	<div class="nav-tab-wrapper">
		<a href="#hello-bar-content" class="nav-tab nav-tab-active" data-tab="hello-bar-content"><?php esc_html_e( 'Content', 'wp-review' ); ?></a>
		<a href="#hello-bar-styling" class="nav-tab" data-tab="hello-bar-styling"><?php esc_html_e( 'Styling', 'wp-review' ); ?></a>
	</div>

	<div id="hello-bar-content" class="tab-content">
		<h3><?php esc_html_e( 'Content Settings', 'wp-review' ); ?></h3>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Enable Global Notification Bar', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch( array(
					'id'    => 'wp_review_hello_bar_enable',
					'name'  => 'wp_review_hello_bar[enable]',
					'value' => $options['enable'],
				) );
				?>
			</div>
		</div>

		<?php $hide = $options['enable'] ? '' : 'hidden'; ?>
		<div class="hide-if-hello-bar-disable <?php echo esc_attr( $hide ); ?>">

			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_text"><?php esc_html_e( 'Text', 'wp-review' ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<input name="wp_review_hello_bar[text]" id="wp_review_text" class="large-text" type="text" value="<?php echo esc_attr( $options['text'] ); ?>">
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_star_rating"><?php esc_html_e( 'Star Rating', 'wp-review' ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<input name="wp_review_hello_bar[star_rating]" id="wp_review_star_rating" class="small-text" type="number" min="0.5" max="5" step="0.5" value="<?php echo floatval( $options['star_rating'] ); ?>">
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_price"><?php esc_html_e( 'Price', 'wp-review' ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<input name="wp_review_hello_bar[price]" id="wp_review_price" type="text" value="<?php echo esc_attr( $options['price'] ); ?>">
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_button_label"><?php esc_html_e( 'Button label', 'wp-review' ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<input name="wp_review_hello_bar[button_label]" id="wp_review_button_label" type="text" value="<?php echo esc_attr( $options['button_label'] ); ?>">
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_button_url"><?php esc_html_e( 'Button URL', 'wp-review' ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<input name="wp_review_hello_bar[button_url]" id="wp_review_button_url" class="large-text" type="text" value="<?php echo esc_attr( $options['button_url'] ); ?>">
				</div>
			</div>

			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label><?php esc_html_e( 'Open link in new tab', 'wp-review' ); ?></label>
				</div>
				<div class="wp-review-field-option">
					<?php
					$form_field->render_switch( array(
						'id'    => 'wp_review_hello_bar_target_blank',
						'name'  => 'wp_review_hello_bar[target_blank]',
						'value' => $options['target_blank'],
					) );
					?>
				</div>
			</div>
		</div>
	</div><!-- End .settings-tab-content -->

	<div id="hello-bar-styling" style="display: none;" class="tab-content">
		<h3><?php esc_html_e( 'Styling Settings', 'wp-review' ); ?></h3>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_hello_bar_location"><?php esc_html_e( 'Location', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_hello_bar[location]" id="wp_review_hello_bar_location">
					<option value="top" <?php selected( $options['location'], 'top' ); ?>><?php esc_html_e( 'Top', 'wp-review' ); ?></option>
					<option value="bottom" <?php selected( $options['location'], 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'wp-review' ); ?></option>
				</select>
			</div>
		</div>

		<?php $hide = 'top' == $options['location'] ? '' : 'wpr-hide'; ?>
		<div class="wp-review-field <?php echo esc_attr( $hide ); ?>" id="wp-review-field-hello-bar-floating">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Floating', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch( array(
					'id'    => 'wp_review_hello_bar_floating',
					'name'  => 'wp_review_hello_bar[floating]',
					'value' => $options['floating'],
				) );
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_max_container"><?php esc_html_e( 'Max container(px/%)', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input name="wp_review_hello_bar[max_container]" id="wp_review_max_container" type="text" value="<?php echo esc_attr( $options['max_container'] ); ?>">
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_bg_color"><?php esc_html_e( 'Background color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_hello_bar[bg_color]" id="wp_review_bg_color" value="<?php echo esc_attr( $options['bg_color'] ); ?>" data-default-color="<?php echo esc_attr( $options['bg_color'] ); ?>">
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_bg_image"><?php esc_html_e( 'Background image', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<span class="wpr_image_upload_field">
					<span class="clearfix" id="wp_review_bg_image-preview">
						<?php
						if ( ! empty( $bg_image['url'] ) ) {
							echo '<img class="wpr_image_upload_img" src="' . esc_url( $bg_image['url'] ) . '">';
						}
						?>
					</span>
					<input type="hidden" id="wp_review_bg_image-id" name="wp_review_hello_bar[bg_image][id]" value="<?php echo intval( $bg_image['id'] ); ?>">
					<input type="hidden" id="wp_review_bg_image-url" name="wp_review_hello_bar[bg_image][url]" value="<?php echo esc_url( $bg_image['url'] ); ?>">
					<button type="button" class="button" name="wp_review_bg_image-upload" id="wp_review_bg_image-upload" data-id="wp_review_bg_image" onclick="wprImageField.uploader( 'wp_review_bg_image' ); return false;"><?php esc_html_e( 'Select Image', 'wp-review' ); ?></button>
					<?php
					if ( ! empty( $bg_image['url'] ) ) {
						echo '<a href="#" class="button button-link clear-image">' . esc_html__( 'Remove Image', 'wp-review' ) . '</a>';
					}
					?>
					<span class="clear"></span>
				</span>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_text_color"><?php esc_html_e( 'Text color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_hello_bar[text_color]" id="wp_review_text_color" value="<?php echo esc_attr( $options['text_color'] ); ?>" data-default-color="<?php echo esc_attr( $options['text_color'] ); ?>">
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_star_color"><?php esc_html_e( 'Star color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_hello_bar[star_color]" id="wp_review_star_color" value="<?php echo esc_attr( $options['star_color'] ); ?>" data-default-color="<?php echo esc_attr( $options['star_color'] ); ?>">
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_button_bg_color"><?php esc_html_e( 'Button background color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_hello_bar[button_bg_color]" id="wp_review_button_bg_color" value="<?php echo esc_attr( $options['button_bg_color'] ); ?>" data-default-color="<?php echo esc_attr( $options['button_bg_color'] ); ?>">
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_button_text_color"><?php esc_html_e( 'Button text color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_hello_bar[button_text_color]" id="wp_review_button_text_color" value="<?php echo esc_attr( $options['button_text_color'] ); ?>" data-default-color="<?php echo esc_attr( $options['button_text_color'] ); ?>">
			</div>
		</div>
	</div><!-- End .settings-tab-styling -->
</div>
