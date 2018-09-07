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
 */
function wp_review_render_meta_box_review_options( $post ) {
	global $post, $wp_review_rating_types;

	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( 'wp-review-meta-box-options', 'wp-review-review-options-nonce' );

	/* Retrieve an existing value from the database. */
	$type_post_value = get_post_meta( $post->ID, 'wp_review_type', true );
	if ( '' === $type_post_value ) {
		// Default value when create post.
		$type_post_value = wp_review_option( 'review_type', 'none' );
	}
	$type = $type_post_value;

	$schema = wp_review_get_review_schema( $post->ID );
	$schema_data = get_post_meta( $post->ID, 'wp_review_schema_options', true );
	$show_schema_data = get_post_meta( $post->ID, 'wp_review_show_schema_data', true );

	$heading = get_post_meta( $post->ID, 'wp_review_heading', true );
	$rating_schema = wp_review_get_rating_schema( $post->ID );
	// $available_types = apply_filters('wp_review_metabox_types', wp_review_get_review_types() );
	$available_types = wp_review_get_rating_types();
	$schemas = wp_review_schema_types();

	$custom_author = get_post_meta( $post->ID, 'wp_review_custom_author', true );
	$author = get_post_meta( $post->ID, 'wp_review_author', true );

	/*
	 * Notification bar.
	 */
	$hello_bar_config = wp_review_get_post_hello_bar( $post->ID );
	$bg_image = wp_parse_args( $hello_bar_config['bg_image'], array(
		'id'  => '',
		'url' => '',
	) );

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
								'<option value="%1$s" data-max="%2$s" data-decimals="%3$s" %4$s %5$s>%6$s</option>',
								esc_attr( $key ),
								intval( $available_type['max'] ),
								intval( $available_type['decimals'] ),
								selected( $type, $key, false ),
								$disabled ? 'disabled' : '',
								esc_html( $available_type['label'] )
							);
						}
						?>
					</select>
					<input type="hidden" name="wp_review_type" value="<?php echo esc_attr( $type_post_value ); ?>">

					<span id="wp_review_id_hint"><?php printf( esc_html__( 'Review ID: %s', 'wp-review' ), '<strong>' . intval( $post->ID ) . '</strong>' ); ?></span>
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
					<div class="wp-review-field-label">
						<label for="wp_review_schema"><?php esc_html_e( 'Reviewed Item Schema', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<select name="wp_review_schema" id="wp_review_schema">
							<?php foreach ( $schemas as $key => $arr ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $schema ); ?>><?php echo esc_html( $arr['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div id="wp_review_schema_type_options_wrap"<?php if ( '' === $schema || 'none' === $schema ) echo ' style="display:none;"';?>>

					<?php foreach ( $schemas as $type => $arr ) : ?>
						<div class="wp_review_schema_type_options" id="wp_review_schema_type_<?php echo esc_attr( $type ); ?>" <?php if ( $type !== $schema ) echo 'style="display:none;"';?>>
							<?php if ( isset( $arr['fields'] ) ) : ?>
								<?php foreach ( $arr['fields'] as $data ) : ?>
									<div class="wp-review-field">
										<?php $values = isset( $schema_data[ $type ] ) ? $schema_data[ $type ] : array(); ?>
										<?php wp_review_schema_field( $data, $values, $type ); ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>

					<div class="wp-review-field" id="wp_review_schema_rating_group">
						<div class="wp-review-field-label">
							<label for="wp_review_rating_schema"><?php esc_html_e( 'Rating Schema', 'wp-review' ); ?></label>
						</div>

						<div class="wp-review-field-option">
							<select name="wp_review_rating_schema" id="wp_review_rating_schema">
								<option value="author" <?php selected( 'author', $rating_schema ); ?>><?php esc_html_e( 'Author Review Rating', 'wp-review' ); ?></option>
								<option value="visitors" <?php selected( 'visitors', $rating_schema ); ?>><?php esc_html_e( 'Visitors Aggregate Rating (if enabled)', 'wp-review' ); ?></option>
								<option value="comments" <?php selected( 'comments', $rating_schema ); ?>><?php esc_html_e( 'Comments Reviews Aggregate Rating (if enabled)', 'wp-review' ); ?></option>
							</select>
						</div>
					</div>

					<div id="wp_review_schema_author_wrapper"<?php if ( 'author' !== $rating_schema ) echo ' style="display: none;"'; ?>>
						<div class="wp-review-field">
							<div class="wp-review-field-label">
								<label><?php esc_html_e( 'Custom Author', 'wp-review' ); ?></label>
							</div>

							<div class="wp-review-field-option">
								<?php
								$form_field->render_switch( array(
									'id'    => 'wp_review_custom_author',
									'name'  => 'wp_review_custom_author',
									'value' => $custom_author,
								) );
								?>
							</div>
						</div>

						<div class="wp-review-author-options"<?php if ( empty( $custom_author ) ) echo ' style="display: none;"'; ?>>
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
				<div class="wp-review-field-label">
					<label><?php esc_html_e( 'Display Schema Data in the Box (if available)', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<?php
					$form_field->render_switch( array(
						'id'    => 'wp_review_show_schema_data',
						'name'  => 'wp_review_show_schema_data',
						'value' => $show_schema_data,
					) );
					?>
				</div>
			</div>

			<div id="wp_review_embed_options_wrapper">
				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label><?php esc_html_e( 'Show Embed Code', 'wp-review' ); ?></label>
						<?php wp_review_print_pro_text(); ?>
					</div>

					<div class="wp-review-field-option">
						<?php
						$form_field->render_switch( array(
							'id'       => 'wp_review_enable_embed',
							'name'     => 'wp_review_enable_embed',
							'disabled' => true,
						) );
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
						<select name="wp_review_popup[enable]" id="wp_review_popup_enable">
							<option value="default"><?php esc_html_e( 'Use global options', 'wp-review' ); ?></option>
							<option value="custom"><?php esc_html_e( 'Use custom options', 'wp-review' ); ?></option>
							<option value="none"><?php esc_html_e( 'None', 'wp-review' ); ?></option>
						</select>
					</span>
				</div>
			</div>
		</div><!-- End #popup -->
		<?php } ?>

		<?php if(! wp_review_network_option('hide_notification_bar_') &&  current_user_can('wp_review_notification_bar')) { ?>
		<div id="hello-bar" class="tab-content wp-review-hello-bar" style="display: none;">
			<div class="wp-review-field">
				<div class="wp-review-field-label">
					<label for="wp_review_hello_bar_enable"><?php esc_html_e( 'Enable', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<select name="wp_review_hello_bar[enable]" id="wp_review_hello_bar_enable">
						<option value="default" <?php selected( $hello_bar_config['enable'], 'default' ); ?>><?php esc_html_e( 'Use global options', 'wp-review' ); ?></option>
						<option value="custom" <?php selected( $hello_bar_config['enable'], 'custom' ); ?>><?php esc_html_e( 'Use custom options', 'wp-review' ); ?></option>
						<option value="none" <?php selected( $hello_bar_config['enable'], 'none' ); ?>><?php esc_html_e( 'None', 'wp-review' ); ?></option>
					</select>
				</div>
			</div>

			<?php $hide = 'custom' == $hello_bar_config['enable'] ? '' : 'hidden'; ?>
			<div id="wp-review-hello-bar-options" class="<?php echo esc_attr( $hide ); ?>">
				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_text"><?php esc_html_e( 'Text', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input name="wp_review_hello_bar[text]" id="wp_review_hello_bar_text" class="large-text" type="text" value="<?php echo esc_attr( $hello_bar_config['text'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_star_rating"><?php esc_html_e( 'Star Rating', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input name="wp_review_hello_bar[star_rating]" id="wp_review_hello_bar_star_rating" type="number" min="0.5" max="5" step="0.5" class="small-text" value="<?php echo floatval( $hello_bar_config['star_rating'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_price"><?php esc_html_e( 'Price', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input name="wp_review_hello_bar[price]" id="wp_review_hello_bar_price" type="text" value="<?php echo esc_attr( $hello_bar_config['price'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_button_label"><?php esc_html_e( 'Button label', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input name="wp_review_hello_bar[button_label]" id="wp_review_hello_bar_button_label" type="text" value="<?php echo esc_attr( $hello_bar_config['button_label'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_button_url"><?php esc_html_e( 'Button URL', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input name="wp_review_hello_bar[button_url]" id="wp_review_hello_bar_button_url" type="text" value="<?php echo esc_attr( $hello_bar_config['button_url'] ); ?>">
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
							'value' => $hello_bar_config['target_blank'],
						) );
						?>
					</div>
				</div>

				<!-- Styling -->
				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_location"><?php esc_html_e( 'Location', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<select name="wp_review_hello_bar[location]" id="wp_review_hello_bar_location">
							<option value="top" <?php selected( $hello_bar_config['location'], 'top' ); ?>><?php esc_html_e( 'Top', 'wp-review' ); ?></option>
							<option value="bottom" <?php selected( $hello_bar_config['location'], 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'wp-review' ); ?></option>
						</select>
					</div>
				</div>

				<?php $hide = 'top' == $hello_bar_config['location'] ? '' : 'hidden'; ?>
				<div class="wp-review-field <?php echo esc_attr( $hide ); ?>" id="wp-review-field-hello-bar-floating">
					<div class="wp-review-field-label">
						<label><?php esc_html_e( 'Floating', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<?php
						$form_field->render_switch( array(
							'id'    => 'wp_review_hello_bar_floating',
							'name'  => 'wp_review_hello_bar[floating]',
							'value' => $hello_bar_config['floating'],
						) );
						?>
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_bg_color"><?php esc_html_e( 'Background color', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input type="text" class="wp-review-color" name="wp_review_hello_bar[bg_color]" id="wp_review_hello_bar_bg_color" value="<?php echo esc_attr( $hello_bar_config['bg_color'] ); ?>" data-default-color="<?php echo esc_attr( $hello_bar_config['bg_color'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_bg_image"><?php esc_html_e( 'Background image', 'wp-review' ); ?></label>
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
						<label for="wp_review_hello_bar_text_color"><?php esc_html_e( 'Text color', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input type="text" class="wp-review-color" name="wp_review_hello_bar[text_color]" id="wp_review_hello_bar_text_color" value="<?php echo esc_attr( $hello_bar_config['text_color'] ); ?>" data-default-color="<?php echo esc_attr( $hello_bar_config['text_color'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_star_color"><?php esc_html_e( 'Star color', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input type="text" class="wp-review-color" name="wp_review_hello_bar[star_color]" id="wp_review_hello_bar_star_color" value="<?php echo esc_attr( $hello_bar_config['star_color'] ); ?>" data-default-color="<?php echo esc_attr( $hello_bar_config['star_color'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_button_bg_color"><?php esc_html_e( 'Button background color', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input type="text" class="wp-review-color" name="wp_review_hello_bar[button_bg_color]" id="wp_review_hello_bar_button_bg_color" value="<?php echo esc_attr( $hello_bar_config['button_bg_color'] ); ?>" data-default-color="<?php echo esc_attr( $hello_bar_config['button_bg_color'] ); ?>">
					</div>
				</div>

				<div class="wp-review-field">
					<div class="wp-review-field-label">
						<label for="wp_review_hello_bar_button_text_color"><?php esc_html_e( 'Button text color', 'wp-review' ); ?></label>
					</div>

					<div class="wp-review-field-option">
						<input type="text" class="wp-review-color" name="wp_review_hello_bar[button_text_color]" id="wp_review_hello_bar_button_text_color" value="<?php echo esc_attr( $hello_bar_config['button_text_color'] ); ?>" data-default-color="<?php echo esc_attr( $hello_bar_config['button_text_color'] ); ?>">
					</div>
				</div>
			</div>
		</div><!-- End #hello-bar -->
		<?php } ?>
	</div>
	<?php
}
