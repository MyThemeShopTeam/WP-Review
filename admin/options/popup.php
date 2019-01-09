<?php
/**
 * Popup options tab
 *
 * @package WP_Review
 */

?>
<div class="wp-review-field">
	<div class="wp-review-disabled wp-review-field-label">
		<label><?php esc_html_e( 'Enable Popup', 'wp-review' ); ?></label>
		<?php wp_review_print_pro_text(); ?>
	</div>

	<div class="wp-review-field-option">
		<?php
		$form_field->render_switch(
			array(
				'id'       => 'wp_review_popup_enable',
				'name'     => 'wp_review_popup[enable]',
				'disabled' => true,
			)
		);
		?>
	</div>
</div>

<div id="wp-review-popup-options">

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_width"><?php esc_html_e( 'Popup width', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block">
				<input name="wp_review_popup[width]" id="wp_review_popup_width" type="text" value="800px" disabled>
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_animation_in"><?php esc_html_e( 'Popup animation in', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<?php
				wp_review_animations_dropdown(
					'wp_review_popup_animation_in',
					'wp_review_popup[animation_in]'
				);
				?>
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_animation_out"><?php esc_html_e( 'Popup animation out', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<?php
				wp_review_animations_dropdown(
					'wp_review_popup_animation_out',
					'wp_review_popup[animation_out]',
					true
				);
				?>
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_overlay_color"><?php esc_html_e( 'Popup overlay color', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<input type="text" class="wp-review-color" name="wp_review_popup[overlay_color]" id="wp_review_popup_overlay_color">
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_overlay_opacity"><?php esc_html_e( 'Popup overlay opacity', 'wp-review' ); ?></label>
			<span class="description">(0.1 - 1)</span>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<input type="text" name="wp_review_popup[overlay_opacity]" id="wp_review_popup_overlay_opacity" class=" small-text" value="0.8">
			</span>
		</div>
	</div>

	<?php $post_types = get_post_types( array( 'public' => true ) ); ?>
	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_post_type"><?php esc_html_e( 'Post type', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<select name="wp_review_popup[post_type]" id="wp_review_popup_post_type" disabled>
					<option value=""><?php esc_html_e( 'Any', 'wp-review' ); ?></option>
					<?php foreach ( $post_types as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_queryby"><?php esc_html_e( 'Popup content', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<select name="wp_review_popup[queryby]" id="wp_review_popup_queryby" disabled>
					<option value="category"><?php esc_html_e( 'From category', 'wp-review' ); ?></option>
					<option value="tag"><?php esc_html_e( 'From tag', 'wp-review' ); ?></option>
					<option value="review_type"><?php esc_html_e( 'From review type', 'wp-review' ); ?></option>
					<option value="latest"><?php esc_html_e( 'Latest reviews', 'wp-review' ); ?></option>
				</select>
			</span>
		</div>
	</div>

	<div class="wp-review-field based-on-queryby" data-value="category">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_category"><?php esc_html_e( 'Choose category', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<?php
				wp_dropdown_categories(
					array(
						'show_option_all' => esc_html__( 'All categories', 'wp-review' ),
						'orderby'         => 'name',
						'hide_empty'      => false,
						'name'            => 'wp_review_popup[category]',
						'id'              => 'wp_review_popup_category',
					)
				);
				?>
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_limit"><?php esc_html_e( 'Number of Reviews', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block">
				<input type="number" min="-1" step="1" class="small-text" name="wp_review_popup[limit]" id="wp_review_popup_limit" value="6" disabled>
			</span>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_popup_orderby"><?php esc_html_e( 'Popup content order', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<select name="wp_review_popup[orderby]" id="wp_review_popup_orderby" disabled>
					<option value="random"><?php esc_html_e( 'Random', 'wp-review' ); ?></option>
					<option value="popular"><?php esc_html_e( 'Most popular', 'wp-review' ); ?></option>
					<option value="rated"><?php esc_html_e( 'Most rated', 'wp-review' ); ?></option>
					<option value="latest"><?php esc_html_e( 'Latest', 'wp-review' ); ?></option>
				</select>
			</span>
		</div>
	</div>
</div>

<div class="wp-review-field">
	<div class="wp-review-disabled wp-review-field-label">
		<label for="wp_review_popup_expiration"><?php esc_html_e( 'Hide popup for', 'wp-review' ); ?></label>
		<?php wp_review_print_pro_text(); ?>
	</div>

	<div class="wp-review-field-option">
		<span class="wp-review-disabled inline-block">
			<input type="number" min="0" step="1" name="wp_review_popup[expiration]" class="small-text" id="wp_review_popup_expiration" value="30" disabled>
			<span><?php esc_html_e( 'day(s).', 'wp-review' ); ?></span>
		</span>
	</div>

	<div class="description" style="margin-top: 10px;"><?php esc_html_e( 'Set to 0 if you want to show popup on each page load.', 'wp-review' ); ?></div>
</div>

<div class="wp-review-field no-flex">
	<span class="wp-review-disabled inline-block">
		<label>
			<input type="checkbox" name="wp_review_popup[show_on_load]" value="1" disabled>
			<?php
			printf(
				// translators: number input.
				esc_html__( 'Show popup after %s second(s)', 'wp-review' ),
				'<input type="number" min="0" step="1" name="wp_review_popup[delay]" class="small-text" id="wp_review_popup_delay" value="0" disabled>'
			);
			?>
		</label>
	</span>

	<?php wp_review_print_pro_text(); ?>

	<p class="description" style="margin-top: 10px;"><?php esc_html_e( 'Set to 0 if you want to show popup instantly.', 'wp-review' ); ?></p>
</div>

<div class="wp-review-field no-flex">
	<span class="wp-review-disabled inline-block">
		<label>
			<input type="checkbox" name="wp_review_popup[show_on_reach_bottom]" value="1" disabled>
			<?php esc_html_e( 'Show popup when visitor reaches the end of the content (only on single posts or pages)', 'wp-review' ); ?>
			<?php wp_review_print_pro_text( true ); ?>
		</label>
	</span>
</div>

<div class="wp-review-field no-flex">
	<span class="wp-review-disabled inline-block">
		<label>
			<input type="checkbox" name="wp_review_popup[exit_intent]" value="1" disabled>
			<?php esc_html_e( 'Show popup when visitor is about to leave (exit intent)', 'wp-review' ); ?>
			<?php wp_review_print_pro_text( true ); ?>
		</label>
	</span>
</div>

<div class="wp-review-field">
	<span class="wp-review-disabled inline-block">
		<label>
			<input type="checkbox" name="wp_review_popup[screen_size_check]" value="1" disabled>
			<?php
			printf(
				// translators: width input.
				esc_html__( 'Show popup on screens larger than %s pixels', 'wp-review' ),
				'<input type="number" min="0" step="1" name="wp_review_popup[screen_width]" class="small-text" id="wp_review_popup_screen_width" value="0">'
			);
			?>
			<?php wp_review_print_pro_text( true ); ?>
		</label>
	</span>
</div>

<div class="wp-review-disabled wp-review-field">
	<button type="button" class="button" id="wp_review_generate_popup_cookie" disabled><?php esc_html_e( 'Generate new cookie', 'wp-review' ); ?></button>
	<div class="description" style="margin-top: 10px; width: 100%;"><?php esc_html_e( 'Use this option to override old cookie.', 'wp-review' ); ?></div>
	<?php wp_review_print_pro_text(); ?>
</div>
