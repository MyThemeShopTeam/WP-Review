<?php
/**
 * Review options meta box
 *
 * @package WP_Review
 */

/**
 * Render the meta box.
 *
 * @since 1.0
 *
 * @param WP_Post $post Post object.
 */
function wp_review_render_meta_box_review_options( $post ) {
	global $post;

	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( 'wp-review-meta-box-options', 'wp-review-review-options-nonce' );

	/* Retrieve an existing value from the database. */
	$type_post_value = get_post_meta( $post->ID, 'wp_review_type', true );
	if ( '' === $type_post_value ) {
		// Default value when create post.
		$type_post_value = wp_review_option( 'review_type', 'none' );
	}
	$type = $type_post_value;

	$heading = get_post_meta( $post->ID, 'wp_review_heading', true );
	// $available_types = apply_filters('wp_review_metabox_types', wp_review_get_review_types() );
	$available_types = wp_review_get_rating_types();
	$schemas         = wp_review_schema_types();

	$rating_schema = wp_review_get_rating_schema( $post->ID );
	$custom_author = get_post_meta( $post->ID, 'wp_review_custom_author', true );
	$author        = get_post_meta( $post->ID, 'wp_review_author', true );

	$form_field = new WP_Review_Form_Field();
	?>

	<div class="js-tabs wpr-tabs">
		<div class="nav-tab-wrapper tab-titles">
			<a href="#review-box" class="nav-tab tab-title nav-tab-active"><?php esc_html_e( 'Review Box', 'wp-review' ); ?></a>
			<?php if ( ! wp_review_network_option( 'hide_popup_box_' ) && current_user_can( 'wp_review_popup' ) ) { ?>
				<a href="#popup" class="nav-tab tab-title"><?php esc_html_e( 'Popup', 'wp-review' ); ?></a>
			<?php } ?>
			<?php if ( ! wp_review_network_option( 'hide_notification_bar_' ) && current_user_can( 'wp_review_notification_bar' ) ) { ?>
				<a href="#hello-bar" class="nav-tab tab-title"><?php esc_html_e( 'Notification Bar', 'wp-review' ); ?></a>
			<?php } ?>
		</div>

		<div id="review-box" class="tab-content">
			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_type"><?php esc_html_e( 'Review Type', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<select id="wp_review_type">
						<option value="none" <?php selected( $type, 'none' ); ?>><?php esc_html_e( 'No review', 'wp-review' ); ?></option>
						<?php
						foreach ( $available_types as $key => $available_type ) {
							$disabled = 'circle' === $key || 'thumbs' === $key;
							printf(
								'<option value="%1$s" data-max="%2$s" data-decimals="%3$s" class="%4$s" %5$s>%6$s</option>',
								esc_attr( $key ),
								intval( $available_type['max'] ),
								intval( $available_type['decimals'] ),
								$disabled ? 'disabled' : '',
								selected( $type, $key, false ),
								esc_html( $available_type['label'] )
							);
						}
						?>
					</select>
					<input type="hidden" name="wp_review_type" value="<?php echo esc_attr( $type_post_value ); ?>">

					<span id="wp_review_id_hint">
						<?php
						// translators: review ID.
						printf( esc_html__( 'Review ID: %s', 'wp-review' ), '<strong>' . intval( $post->ID ) . '</strong>' );
						?>
					</span>
				</div>
			</div>

			<div class="wp-review-field" id="wp_review_heading_group">
				<div class="wp-review-field-label">
					<label for="wp_review_heading"><?php esc_html_e( 'Review Heading', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" name="wp_review_heading" id="wp_review_heading" class="large-text" value="<?php echo esc_attr( $heading ); ?>" />
				</div>
			</div>

			<div id="wp_review_schema_options_wrapper">

				<div class="wp-review-field" id="wp_review_schema_group">
					<div class="wp-review-disabled wp-review-field-label">
						<label for="wp_review_schema"><?php esc_html_e( 'Reviewed Item Schema', 'wp-review' ); ?></label>
						<?php wp_review_print_pro_text(); ?>
					</div>

					<div class="wp-review-field-option">
						<select name="wp_review_schema" id="wp_review_schema">
							<?php foreach ( $schemas as $key => $arr ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, 'Thing' ); ?> disabled><?php echo esc_html( $arr['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div id="wp_review_schema_type_options_wrap">
					<div class="wp-review-field" id="wp_review_schema_rating_group">
						<div class="wp-review-field-label">
							<label for="wp_review_rating_schema"><?php esc_html_e( 'Rating Schema', 'wp-review' ); ?></label>
						</div>
						<div class="wp-review-field-option">
							<select name="wp_review_rating_schema" id="wp_review_rating_schema">
								<option value="author" <?php selected( 'author', $rating_schema ); ?>><?php esc_html_e( 'Author Review Rating', 'wp-review' ); ?></option>
								<option value="visitors" <?php selected( 'visitors', $rating_schema ); ?>><?php esc_html_e( 'Visitors Aggregate Rating (if enabled)', 'wp-review' ); ?></option>
								<option value="comments" class="disabled"><?php esc_html_e( 'Comments Reviews Aggregate Rating (if enabled)', 'wp-review' ); ?></option>
							</select>
						</div>
					</div>
					<div id="wp_review_schema_author_wrapper"<?php if ( 'author' !== $rating_schema ) echo ' style="display: none;"'; // phpcs:ignore ?>>
						<div class="wp-review-field">
							<div class="wp-review-field-label">
								<label><?php esc_html_e( 'Custom Author', 'wp-review' ); ?></label>
							</div>
							<div class="wp-review-field-option">
								<?php
								$form_field->render_switch(
									array(
										'id'    => 'wp_review_custom_author',
										'name'  => 'wp_review_custom_author',
										'value' => $custom_author,
									)
								);
								?>
							</div>
						</div>

						<div class="wp-review-author-options"<?php if ( empty( $custom_author ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
							<div class="wp-review-field">
								<div class="wp-review-field-label">
									<label for="wp_review_author"><?php esc_html_e( 'Review Author', 'wp-review' ); ?></label>
								</div>
								<div class="wp-review-field-option">
									<input type="text" name="wp_review_author" id="wp_review_author" value="<?php echo esc_attr( $author ); ?>">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><!-- End #wp_review_schema_options_wrapper -->

			<div class="wp-review-field" id="wp_review_show_schema_data_wrapper">
				<div class="wp-review-disabled wp-review-field-label">
					<label><?php esc_html_e( 'Display Schema Data in the Box (if available)', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>

				<div class="wp-review-field-option">
					<?php
					$form_field->render_switch(
						array(
							'id'       => 'wp_review_show_schema_data',
							'name'     => 'wp_review_show_schema_data',
							'disabled' => true,
						)
					);
					?>
				</div>
			</div>

			<div id="wp_review_embed_options_wrapper">
				<div class="wp-review-field">
					<div class="wp-review-disabled wp-review-field-label">
						<label><?php esc_html_e( 'Show Embed Code', 'wp-review' ); ?></label>
						<?php wp_review_print_pro_text(); ?>
					</div>

					<div class="wp-review-field-option">
						<?php
						$form_field->render_switch(
							array(
								'id'       => 'wp_review_enable_embed',
								'name'     => 'wp_review_enable_embed',
								'disabled' => true,
							)
						);
						?>
					</div>
				</div>
			</div>
		</div><!-- End #review-box -->

		<?php if ( ! wp_review_network_option( 'hide_popup_box_' ) && current_user_can( 'wp_review_popup' ) ) { ?>
		<div id="popup" class="tab-content wp-review-popup" style="display: none;">
			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_popup_enable"><?php esc_html_e( 'Enable', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>

				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block has-bg">
						<select name="wp_review_popup[enable]" id="wp_review_popup_enable" disabled>
							<option value="default"><?php esc_html_e( 'Use global options', 'wp-review' ); ?></option>
							<option value="custom"><?php esc_html_e( 'Use custom options', 'wp-review' ); ?></option>
							<option value="none"><?php esc_html_e( 'None', 'wp-review' ); ?></option>
						</select>
					</span>
				</div>
			</div>
		</div><!-- End #popup -->
		<?php } ?>

		<?php if ( ! wp_review_network_option( 'hide_notification_bar_' ) && current_user_can( 'wp_review_notification_bar' ) ) { ?>
		<div id="hello-bar" class="tab-content wp-review-hello-bar" style="display: none;">
			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_hello_bar_enable"><?php esc_html_e( 'Enable', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>

				<div class="wp-review-field-option">
					<span class="wp-review-disabled inline-block has-bg">
						<select name="wp_review_hello_bar[enable]" id="wp_review_hello_bar_enable" disabled>
							<option value="default"><?php esc_html_e( 'Use global options', 'wp-review' ); ?></option>
							<option value="custom"><?php esc_html_e( 'Use custom options', 'wp-review' ); ?></option>
							<option value="none"><?php esc_html_e( 'None', 'wp-review' ); ?></option>
						</select>
					</span>
				</div>
			</div>
		</div><!-- End #hello-bar -->
		<?php } ?>
	</div>
	<?php
}
