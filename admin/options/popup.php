<?php
/**
 * Popup options tab
 *
 * @package WP_Review
 */

$options = wp_review_popup_option();
$options = wp_parse_args( $options, wp_review_popup_defaults() );
?>
<div class="wp-review-field">
	<div class="wp-review-field-label">
		<label><?php esc_html_e( 'Enable Popup', 'wp-review' ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<?php
		$form_field->render_switch( array(
			'id'    => 'wp_review_popup_enable',
			'name'  => 'wp_review_popup[enable]',
			'value' => $options['enable'],
		) );
		?>
	</div>
</div>

<?php $hide = $options['enable'] ? '' : 'style="display: none;"'; ?>
<div id="wp-review-popup-options" <?php echo $hide; // WPCS: xss ok. ?>>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_width"><?php esc_html_e( 'Popup width', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<input name="wp_review_popup[width]" id="wp_review_popup_width" type="text" value="<?php echo esc_attr( $options['width'] ); ?>">
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_animation_in"><?php esc_html_e( 'Popup animation in', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			wp_review_animations_dropdown(
				'wp_review_popup_animation_in',
				'wp_review_popup[animation_in]',
				$options['animation_in']
			);
			?>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_animation_out"><?php esc_html_e( 'Popup animation out', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			wp_review_animations_dropdown(
				'wp_review_popup_animation_out',
				'wp_review_popup[animation_out]',
				$options['animation_out'],
				true
			);
			?>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_overlay_color"><?php esc_html_e( 'Popup overlay color', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<input type="text" class="wp-review-color" name="wp_review_popup[overlay_color]" id="wp_review_popup_overlay_color" value="<?php echo esc_attr( $options['overlay_color'] ); ?>" data-default-color="<?php echo esc_attr( $options['overlay_color'] ); ?>">
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_overlay_opacity"><?php esc_html_e( 'Popup overlay opacity', 'wp-review' ); ?></label>
			<span class="description">(0.1 - 1)</span>
		</div>

		<div class="wp-review-field-option">
			<input type="text" name="wp_review_popup[overlay_opacity]" id="wp_review_popup_overlay_opacity" class=" small-text" value="<?php echo esc_attr( $options['overlay_opacity'] ); ?>">
		</div>
	</div>

	<?php $post_types = get_post_types( array( 'public' => true ) ); ?>
	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_post_type"><?php esc_html_e( 'Post type', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<select name="wp_review_popup[post_type]" id="wp_review_popup_post_type">
				<option value=""><?php esc_html_e( 'Any', 'wp-review' ); ?></option>
				<?php foreach ( $post_types as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $options['post_type'], $key ); ?>><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_queryby"><?php esc_html_e( 'Popup content', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<select name="wp_review_popup[queryby]" id="wp_review_popup_queryby" value="<?php echo esc_attr( $options['queryby'] ); ?>">
				<option value="category" <?php selected( $options['queryby'], 'category' ); ?>><?php esc_html_e( 'From category', 'wp-review' ); ?></option>
				<option value="tag" <?php selected( $options['queryby'], 'tag' ); ?>><?php esc_html_e( 'From tag', 'wp-review' ); ?></option>
				<option value="review_type" <?php selected( $options['queryby'], 'review_type' ); ?>><?php esc_html_e( 'From review type', 'wp-review' ); ?></option>
				<option value="latest" <?php selected( $options['queryby'], 'latest' ); ?>><?php esc_html_e( 'Latest reviews', 'wp-review' ); ?></option>
			</select>
		</div>
	</div>

	<?php $hide = 'category' === $options['queryby'] ? '' : 'style="display: none;"'; ?>
	<div class="wp-review-field based-on-queryby" data-value="category" <?php echo $hide; // WPCS: xss ok. ?>>
		<div class="wp-review-field-label">
			<label for="wp_review_popup_category"><?php esc_html_e( 'Choose category', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			wp_dropdown_categories( array(
				'show_option_all' => esc_html__( 'All categories', 'wp-review' ),
				'orderby'         => 'name',
				'hide_empty'      => false,
				'selected'        => $options['category'],
				'name'            => 'wp_review_popup[category]',
				'id'              => 'wp_review_popup_category',
			) );
			?>
		</div>
	</div>

	<?php $hide = 'tag' === $options['queryby'] ? '' : 'style="display: none;"'; ?>
	<div class="wp-review-field based-on-queryby" data-value="tag" <?php echo $hide; // WPCS: xss ok. ?>>
		<div class="wp-review-field-label">
			<label for="wp_review_popup_tag"><?php esc_html_e( 'Choose tag', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			wp_dropdown_categories( array(
				'show_option_all' => esc_html__( 'All tags', 'wp-review' ),
				'orderby'         => 'name',
				'hide_empty'      => false,
				'selected'        => $options['tag'],
				'name'            => 'wp_review_popup[tag]',
				'id'              => 'wp_review_popup_tag',
				'taxonomy'        => 'post_tag',
			) );
			?>
		</div>
	</div>

	<?php $hide = 'review_type' === $options['queryby'] ? '' : 'style="display: none;"'; ?>
	<div class="wp-review-field based-on-queryby" data-value="review_type" <?php echo $hide; // WPCS: xss ok. ?>>
		<div class="wp-review-field-label">
			<label for="wp_review_popup_review_type"><?php esc_html_e( 'Choose review type', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<select name="wp_review_popup[review_type]" id="wp_review_popup_review_type">
				<?php foreach ( $review_types as $name => $review_type ) : ?>
					<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $name, $options['review_type'] ); ?>><?php echo esc_html( $review_type['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_limit"><?php esc_html_e( 'Number of Reviews', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<input type="number" min="-1" step="1" class="small-text" name="wp_review_popup[limit]" id="wp_review_popup_limit" value="<?php echo intval( $options['limit'] ); ?>">
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_popup_orderby"><?php esc_html_e( 'Popup content order', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<select name="wp_review_popup[orderby]" id="wp_review_popup_orderby" value="<?php echo esc_attr( $options['orderby'] ); ?>">
				<option value="random" <?php selected( $options['orderby'], 'random' ); ?>><?php esc_html_e( 'Random', 'wp-review' ); ?></option>
				<option value="popular" <?php selected( $options['orderby'], 'popular' ); ?>><?php esc_html_e( 'Most popular', 'wp-review' ); ?></option>
				<option value="rated" <?php selected( $options['orderby'], 'rated' ); ?>><?php esc_html_e( 'Most rated', 'wp-review' ); ?></option>
				<option value="latest" <?php selected( $options['orderby'], 'latest' ); ?>><?php esc_html_e( 'Latest', 'wp-review' ); ?></option>
			</select>
		</div>
	</div>
</div>

<div class="wp-review-field">
	<div class="wp-review-field-label">
		<label for="wp_review_popup_expiration"><?php esc_html_e( 'Hide popup for', 'wp-review' ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<input type="number" min="0" step="1" name="wp_review_popup[expiration]" class="small-text" id="wp_review_popup_expiration" value="<?php echo intval( $options['expiration'] ); ?>">
		<span><?php esc_html_e( 'day(s).', 'wp-review' ); ?></span>
	</div>

	<div class="description" style="margin-top: 10px;"><?php esc_html_e( 'Set to 0 if you want to show popup on each page load.', 'wp-review' ); ?></div>
</div>

<div class="wp-review-field no-flex">
	<label>
		<input type="checkbox" name="wp_review_popup[show_on_load]" value="1" <?php checked( 1, $options['show_on_load'] ); ?>>
		<?php
		printf(
			esc_html__( 'Show popup after %s second(s)', 'wp-review' ),
			'<input type="number" min="0" step="1" name="wp_review_popup[delay]" class="small-text" id="wp_review_popup_delay" value="' . intval( $options['delay'] ) . '">'
		);
		?>
	</label>

	<p class="description" style="margin-top: 10px;"><?php esc_html_e( 'Set to 0 if you want to show popup instantly.', 'wp-review' ); ?></p>
</div>

<div class="wp-review-field no-flex">
	<label>
		<input type="checkbox" name="wp_review_popup[show_on_reach_bottom]" value="1" <?php checked( 1, $options['show_on_reach_bottom'] ); ?>>
		<?php esc_html_e( 'Show popup when visitor reaches the end of the content (only on single posts or pages)', 'wp-review' ); ?>
	</label>
</div>

<div class="wp-review-field no-flex">
	<label>
		<input type="checkbox" name="wp_review_popup[exit_intent]" value="1" <?php checked( 1, $options['exit_intent'] ); ?>>
		<?php esc_html_e( 'Show popup when visitor is about to leave (exit intent)', 'wp-review' ); ?>
	</label>
</div>

<div class="wp-review-field">
	<label>
		<input type="checkbox" name="wp_review_popup[screen_size_check]" value="1" <?php checked( 1, $options['screen_size_check'] ); ?>>
		<?php
		printf(
			esc_html__( 'Show popup on screens larger than %s pixels', 'wp-review' ),
			'<input type="number" min="0" step="1" name="wp_review_popup[screen_width]" class="small-text" id="wp_review_popup_screen_width" value="' . intval( $options['screen_width'] ) . '">'
		);
		?>
	</label>
</div>

<div class="wp-review-field">
	<input type="hidden" name="wp_review_popup[cookie_name]" value="<?php echo esc_attr( $options['cookie_name'] ); ?>">
	<button type="button" class="button" id="wp_review_generate_popup_cookie"><?php esc_html_e( 'Generate new cookie', 'wp-review' ); ?></button>
	<span id="wp_review_generate_popup_cookie_message" class="description" style="display: none; margin-left: 0;"><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Please save the options to apply changes.', 'wp-review' ); ?></span>
	<div class="description" style="margin-top: 10px; width: 100%;"><?php esc_html_e( 'Use this option to override old cookie.', 'wp-review' ); ?></div>
</div>