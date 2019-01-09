<?php
/**
 * Review options tab
 *
 * @package WP_Review
 */

$options = get_option( 'wp_review_options' );

$default_options = array(
	'colors'                   => array(
		'color'          => '',
		'inactive_color' => '',
		'fontcolor'      => '',
		'bgcolor1'       => '',
		'bgcolor2'       => '',
		'bordercolor'    => '',
	),
	'default_features'         => array(),
	'default_link_texts'       => array(),
	'default_link_urls'        => array(),
	'default_schema_type'      => 'Thing',
	'default_user_review_type' => WP_REVIEW_REVIEW_DISABLED,
	'image_sizes'              => array(),
);
// Set defaults.
if ( empty( $options ) ) {
	$options = $default_options;
	update_option( 'wp_review_options', $options );
}

if ( empty( $options['image_sizes'] ) ) {
	$options['image_sizes'] = array();
}

$opt_name               = 'wp_review_options_' . wp_get_theme();
$options_updated        = get_option( $opt_name );
$suggest_theme_defaults = true;
if ( ! empty( $_GET['wp-review-theme-defaults'] ) && empty( $_GET['settings-updated'] ) ) { // WPCS: csrf ok.
	wp_review_theme_defaults( $options_updated, true );
	$options                = get_option( 'wp_review_options' );
	$suggest_theme_defaults = false;
}

// Test to see if we need to sugges setting theme defaults.
if ( empty( $options_updated ) ) {
	$options_updated = array();
}

$opts_tmp = array_merge( $options, $options_updated );
if ( $opts_tmp === $options ) {
	$suggest_theme_defaults = false;
}

// Migrate.
global $wpdb;
$current_blog_id = get_current_blog_id();
$total_rows      = 0;
$rows_left       = 0;
$migrated_rows   = get_option( 'wp_review_migrated_rows', 0 );
$has_migrated    = get_option( 'wp_review_has_migrated', false );
if ( ! $has_migrated && $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->base_prefix}mts_wp_reviews'" ) === "{$wpdb->base_prefix}mts_wp_reviews" ) {
	// Table exists and not migrated (fully) yet.
	$total_rows = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->base_prefix}mts_wp_reviews WHERE blog_id = {$current_blog_id}" ); // WPCS: unprepared SQL ok.
	$rows_left  = $total_rows - $migrated_rows;
}

$comment_form_integration = ( ! empty( $options['comment_form_integration'] ) ? $options['comment_form_integration'] : 'replace' );
if ( 'replace' !== $comment_form_integration ) {
	$comment_form_integration = 'extend';
}

$comments_template = ( ! empty( $options['comments_template'] ) ? $options['comments_template'] : 'theme' );
if ( 'theme' !== $comments_template ) {
	$comments_template = 'plugin';
}

$default_colors   = wp_review_get_default_colors();
$default_location = wp_review_get_default_location();
$default_criteria = wp_review_get_default_criteria();

$default_items = array();
foreach ( $default_criteria as $item ) {
	$default_items[] = array(
		'wp_review_item_title' => $item,
		'wp_review_item_star'  => '',
	);
}
$default_schema           = $default_options['default_schema_type'];
$default_user_review_type = empty( $options['default_user_review_type'] ) ? WP_REVIEW_REVIEW_DISABLED : $options['default_user_review_type'];

$options['colors'] = apply_filters( 'wp_review_colors', $options['colors'], 0 );
if ( ! isset( $options['default'] ) ) {
	$options['default'] = array();
}
/* Retrieve an existing value from the database. */
$items          = ! empty( $options['default_features'] ) ? $options['default_features'] : '';
$link_texts     = ! empty( $options['default_link_text'] ) ? $options['default_link_text'] : array();
$link_urls      = ! empty( $options['default_link_url'] ) ? $options['default_link_url'] : array();
$location       = wp_review_option( 'review_location' );
$color          = ! empty( $options['colors']['color'] ) ? $options['colors']['color'] : '';
$inactive_color = ! empty( $options['colors']['inactive_color'] ) ? $options['colors']['inactive_color'] : '';
$fontcolor      = ! empty( $options['colors']['fontcolor'] ) ? $options['colors']['fontcolor'] : '';
$bgcolor1       = ! empty( $options['colors']['bgcolor1'] ) ? $options['colors']['bgcolor1'] : '';
$bgcolor2       = ! empty( $options['colors']['bgcolor2'] ) ? $options['colors']['bgcolor2'] : '';
$bordercolor    = ! empty( $options['colors']['bordercolor'] ) ? $options['colors']['bordercolor'] : '';

$force_user_ratings = wp_review_option( 'force_user_ratings' );

$rating_icon  = wp_review_option( 'rating_icon', apply_filters( 'wp_review_default_rating_icon', 'fa fa-star' ) );
$rating_image = wp_review_option( 'rating_image' );

$review_templates = wp_review_get_box_templates();
$box_template     = wp_review_option( 'box_template', 'default' );
$box_template_img = ! empty( $review_templates[ $box_template ] ) ? $review_templates[ $box_template ]['image'] : WP_REVIEW_ASSETS . 'images/largethumb.png';

$review_types = wp_review_get_rating_types();
$review_type  = wp_review_option( 'review_type', 'none' );

if ( '' === $items ) {
	$items = $default_items;
}
if ( '' === $color ) {
	$color = $default_colors['color'];
}
if ( '' === $inactive_color ) {
	$inactive_color = $default_colors['inactive_color'];
}
if ( '' === $location ) {
	$location = $default_location;
}
if ( '' === $fontcolor ) {
	$fontcolor = $default_colors['fontcolor'];
}
if ( '' === $bgcolor1 ) {
	$bgcolor1 = $default_colors['bgcolor1'];
}
if ( '' === $bgcolor2 ) {
	$bgcolor2 = $default_colors['bgcolor2'];
}
if ( '' === $bordercolor ) {
	$bordercolor = $default_colors['bordercolor'];
}
if ( empty( $width ) ) {
	$width = 100;
}

$fields = array(
	'location'        => true,
	'color'           => true,
	'inactive_color'  => true,
	'fontcolor'       => true,
	'bgcolor1'        => true,
	'bgcolor2'        => true,
	'bordercolor'     => true,
	'custom_colors'   => true,
	'custom_location' => true,
);

$displayed_fields = apply_filters( 'wp_review_metabox_item_fields', $fields );

$available_types = apply_filters(
	'wp_review_metabox_types',
	array(
		'star'       => __( 'Star', 'wp-review' ),
		'point'      => __( 'Point', 'wp-review' ),
		'percentage' => __( 'Percentage', 'wp-review' ),
		'circle'     => __( 'Circle', 'wp-review' ),
		'thumbs'     => __( 'Thumbs', 'wp-review' ),
	)
);

$form_field = new WP_Review_Form_Field();
?>
<div data-nav-tabs>
	<div class="nav-tab-wrapper">
		<a href="#review-general" class="nav-tab nav-tab-active"><?php esc_html_e( 'General', 'wp-review' ); ?></a>
		<a href="#review-styling" class="nav-tab"><?php esc_html_e( 'Styling', 'wp-review' ); ?></a>
		<a href="#review-defaults" class="nav-tab"><?php esc_html_e( 'Defaults', 'wp-review' ); ?></a>
		<a href="#review-embed" class="nav-tab"><?php esc_html_e( 'Embed', 'wp-review' ); ?></a>
		<?php if ( $rows_left ) : ?>
			<a href="#review-migrate" class="nav-tab"><?php esc_html_e( 'Migrate Ratings', 'wp-review' ); ?></a>
		<?php endif; ?>
	</div>

	<div id="review-general" class="settings-tab-general tab-content">
		<h3><?php esc_html_e( 'General Settings', 'wp-review' ); ?></h3>
		<?php
		$location = apply_filters( 'wp_review_location', $location, 0 );
		if ( has_filter( 'wp_review_location' ) ) {
			echo '<p class="wp-review-filter-msg"><div class="dashicons dashicons-info"></div>' . esc_html__( 'There is a filter set for the review location that may modify the options below.', 'wp-review' ) . '</p>';
		}

		if ( $suggest_theme_defaults ) {
			?>
			<div class="wp-review-theme-defaults-msg updated settings-error">
				<p class="wp-review-field">
					<?php esc_html_e( 'The current theme provides default settings for the plugin.', 'wp-review' ); ?><br />
				</p>
				<a href="<?php echo admin_url( 'options-general.php?page=wp-review/admin/options.php&wp-review-theme-defaults=1' ); // WPCS: xss ok. ?>" class="button button-primary"><?php esc_html_e( 'Set to theme defaults', 'wp-review' ); ?></a>
				<a href="#" class="dashicons dashicons-no-alt close-notice"></a>
			</div>
		<?php } ?>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Restrict rating to registered users only', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'    => 'wp_review_registered_only',
						'name'  => 'wp_review_options[registered_only]',
						'value' => ! empty( $options['registered_only'] ),
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Add total rating to thumbnails', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_show_on_thumbnails',
						'name'     => 'wp_review_options[show_on_thumbnails]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Enable User rating in old posts', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_global_user_rating',
						'name'     => 'wp_review_options[global_user_rating]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<div class="wp-review-field">
				<div class="wp-review-disabled wp-review-field-label">
					<label><?php esc_html_e( 'Replace WooCommerce rating', 'wp-review' ); ?></label>
					<?php wp_review_print_pro_text(); ?>
				</div>

				<div class="wp-review-field-option">
					<?php
					$form_field->render_switch(
						array(
							'id'       => 'wp_review_replace_wc_rating',
							'name'     => 'wp_review_options[replace_wc_rating]',
							'disabled' => true,
						)
					);
					?>
				</div>
			</div>
		<?php endif; ?>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Disable Map Script in the Backend', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_dequeue_map_backend',
						'name'     => 'wp_review_options[dequeue_map_backend]',
						'disabled' => true,
					)
				);
				?>
			</div>

			<p class="description" style="margin-top: 10px;"><?php esc_html_e( 'If map script is conflicting with other plugin in the single post editor, please enable this option.', 'wp-review' ); ?></p>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Comments template', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<div class="wpr-flex">
					<div class="pr-10 wpr-col-1-2">
						<label for="wp_review_comments_template_theme">
							<span class="wp-review-disabled inline-block has-bg">
								<input name="wp_review_options[comments_template]" id="wp_review_comments_template_theme" type="radio" value="theme" <?php checked( $comments_template, 'theme' ); ?> disabled />
								<strong><?php esc_html_e( 'Theme', 'wp-review' ); ?></strong>
							</span>
						</label>
						<br>
						<span class="description"><?php esc_html_e( 'Use theme comments template. Might need customization of comments.php', 'wp-review' ); ?></span>
					</div>

					<div class="pl-10 wpr-col-1-2">
						<label for="wp_review_comments_template_plugin">
							<span class="wp-review-disabled inline-block has-bg">
								<input name="wp_review_options[comments_template]" id="wp_review_comments_template_plugin" type="radio" value="plugin" <?php checked( $comments_template, 'plugin' ); ?> disabled />
								<strong><?php esc_html_e( 'WP Review', 'wp-review' ); ?></strong>
							</span>
						</label>
						<br>
						<span class="description"><?php esc_html_e( 'Use WP Review comments template. Better chances for out of the box integration.', 'wp-review' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Comment form integration', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<div class="wpr-flex">
					<div class="pr-10 wpr-col-1-2">
						<label for="wp_review_comment_form_integration_replace">
							<span class="wp-review-disabled inline-block has-bg">
								<input name="wp_review_options[comment_form_integration]" id="wp_review_comment_form_integration_replace" type="radio" value="replace" <?php checked( $comment_form_integration, 'replace' ); ?> disabled />
								<strong><?php esc_html_e( 'Replace', 'wp-review' ); ?></strong>
							</span>
						</label>
						<br>
						<span class="description"><?php esc_html_e( 'Replace form fields.', 'wp-review' ); ?></span>
					</div>

					<div class="pl-10 wpr-col-1-2">
						<label for="wp_review_comment_form_integration_extend">
							<span class="wp-review-disabled inline-block has-bg">
								<input name="wp_review_options[comment_form_integration]" id="wp_review_comment_form_integration_extend" type="radio" value="extend" <?php checked( $comment_form_integration, 'extend' ); ?> disabled />
								<strong><?php esc_html_e( 'Extend', 'wp-review' ); ?></strong>
							</span>
						</label>
						<br>
						<span class="description"><?php esc_html_e( 'Add new fields without modifying the default fields.', 'wp-review' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Require a rating when commenting', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_require_rating',
						'name'     => 'wp_review_options[require_rating]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Allow comment feedback (helpful/unhelpful)', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_allow_comment_feedback',
						'name'     => 'wp_review_options[allow_comment_feedback]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_record_ratings_by"><?php esc_html_e( 'Record User Ratings', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<select name="wp_review_options[record_ratings_by]" id="wp_review_record_ratings_by" disabled>
						<option value="ip"><?php esc_html_e( 'Based on IP address', 'wp-review' ); ?></option>
						<option value="cookie"><?php esc_html_e( 'Based on browser cookie', 'wp-review' ); ?></option>
					</select>
				</span>
			</div>
		</div>

		<?php
		// phpcs:disable
		/*<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Show text count with Star ratings', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch( array(
					'id'		=> 'wp_review_show_star_rating_count',
					'name'	=> 'wp_review_options[show_star_rating_count]',
					'value'	=> ! empty( $options['show_star_rating_count'] ),
				) );
				?>
			</div>
		</div>*/
		// phpcs:enable
		?>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Allow multiple reviews per account', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'    => 'wp_review_multi_reviews_per_account',
						'name'  => 'wp_review_options[multi_reviews_per_account]',
						'value' => ! empty( $options['multi_reviews_per_account'] ),
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Include Pros/Cons in comment reviews', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_comment_pros_cons',
						'name'     => 'wp_review_options[comment_pros_cons]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Approve Comment Reviews without Moderation', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_approve_ratings',
						'name'     => 'wp_review_options[approve_ratings]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>
		<?php
		if ( current_user_can( 'wp_review_purge_visitor_ratings' ) ) {
			?>
			<p style="margin-top: 50px;">
				<button
					type="button"
					class="button"
					data-remove-ratings
					data-type="visitor"
					data-processing-text="<?php esc_attr_e( 'Processing...', 'wp-review' ); ?>"
				><?php esc_html_e( 'Purge visitor ratings', 'wp-review' ); ?></button>
				<span class="description"><?php esc_html_e( 'Click to remove all visitor ratings of all posts.', 'wp-review' ); ?></span>
			</p>
		<?php } ?>
	</div>

	<div id="review-styling" class="settings-tab-styling tab-content" style="display: none;">

		<h3><?php esc_html_e( 'Styling', 'wp-review' ); ?></h3>

		<div class="wp-review-field vertical">
			<div class="wp-review-field-label">
				<label for="wp_review_box_template"><?php esc_html_e( 'Default', 'wp-review' ); ?></label>
			</div>
			<div class="wp-review-field-option">
				<div id="wp_review_box_template_wrapper">
					<select name="wp_review_options[box_template]" id="wp_review_box_template">
						<?php
						foreach ( $review_templates as $key => $value ) {
							$disabled = 'default' !== $key && 'aqua' !== $key;
							printf(
								'<option value="%1$s" %2$s %3$s>%4$s</option>',
								esc_attr( $key ),
								selected( $key, $box_template, false ),
								$disabled ? 'disabled' : '',
								esc_html( $value['title'] )
							);
						}
						?>
					</select>

					<div id="wp_review_box_template_preview" style="display: none;">
						<img src="#" alt="" id="wp_review_box_template_preview_img">
					</div>
				</div>

				<div style="margin-top: 10px;">
					<img src="<?php echo esc_url( $box_template_img ); ?>" alt="" id="wp_review_box_template_img">
				</div>
			</div>
		</div>

		<?php
		if ( has_filter( 'wp_review_colors' ) ) {
			echo '<p class="wp-review-filter-msg"><div class="dashicons dashicons-info"></div>' . esc_html__( 'There is a filter set for the review colors that may modify the options below.', 'wp-review' ) . '</p>';
		}
		?>

		<div class="wp-review-color-options">

			<div class="wp-review-field"<?php if ( empty( $displayed_fields['color'] ) ) echo ' style="display: none;"'; // phpcS:ignore ?>>
				<div class="wp-review-field-label">
					<label for="wp_review_color"><?php esc_html_e( 'Review Color', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" id="wp_review_color" class="wp-review-color" name="wp_review_options[colors][color]" value="<?php echo esc_attr( $color ); ?>" data-default-color="<?php echo esc_attr( $color ); ?>">
				</div>
			</div>

			<div class="wp-review-field"<?php if ( empty( $displayed_fields['inactive_color'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
				<div class="wp-review-field-label">
					<label for="wp_review_inactive_color"><?php esc_html_e( 'Inactive Review Color', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" id="wp_review_inactive_color" class="wp-review-color" name="wp_review_options[colors][inactive_color]" value="<?php echo esc_attr( $inactive_color ); ?>" data-default-color="<?php echo esc_attr( $inactive_color ); ?>">
				</div>
			</div>

			<div class="wp-review-field"<?php if ( empty( $displayed_fields['fontcolor'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
				<div class="wp-review-field-label">
					<label for="wp_review_fontcolor"><?php esc_html_e( 'Font Color', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" class="wp-review-color" name="wp_review_options[colors][fontcolor]" id ="wp_review_fontcolor" value="<?php echo esc_attr( $fontcolor ); ?>" data-default-color="<?php echo esc_attr( $fontcolor ); ?>">
				</div>
			</div>

			<div class="wp-review-field"<?php if ( empty( $displayed_fields['bgcolor1'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
				<div class="wp-review-field-label">
					<label for="wp_review_bgcolor1"><?php esc_html_e( 'Heading Background Color', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" class="wp-review-color" name="wp_review_options[colors][bgcolor1]" id ="wp_review_bgcolor1" value="<?php echo esc_attr( $bgcolor1 ); ?>" data-default-color="<?php echo esc_attr( $bgcolor1 ); ?>">
				</div>
			</div>

			<div class="wp-review-field"<?php if ( empty( $displayed_fields['bgcolor2'] ) ) echo ' style="display: none;"'; //phpcs:ignore ?>>
				<div class="wp-review-field-label">
					<label for="wp_review_bgcolor2"><?php esc_html_e( 'Background Color', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" class="wp-review-color" name="wp_review_options[colors][bgcolor2]" id="wp_review_bgcolor2" value="<?php echo esc_attr( $bgcolor2 ); ?>" data-default-color="<?php echo esc_attr( $bgcolor2 ); ?>">
				</div>
			</div>

			<div class="wp-review-field"<?php if ( empty( $displayed_fields['bordercolor'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
				<div class="wp-review-field-label">
					<label for="wp_review_bordercolor"><?php esc_html_e( 'Border Color', 'wp-review' ); ?></label>
				</div>

				<div class="wp-review-field-option">
					<input type="text" class="wp-review-color" name="wp_review_options[colors][bordercolor]" id="wp_review_bordercolor" value="<?php echo esc_attr( $bordercolor ); ?>" data-default-color="<?php echo esc_attr( $bordercolor ); ?>">
				</div>
			</div>
		</div>


		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_width"><?php esc_html_e( 'Review Box Width', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option wp-review-disabled has-bg">
				<input type="number" min="1" max="100" step="1" name="wp_review_options[width]" id="wp_review_width" value="100" disabled /> %
				<div id="wp-review-width-slider"></div>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Google Font', 'wp-review' ); ?></label><br>
				<span class="description">
					<?php _e( 'Many templates use Google Font, select <code>No</code> to use default theme font.', 'wp-review' ); // WPCS: xss ok. ?>
				</span>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_fontfamily',
						'name'     => 'wp_review_options[fontfamily]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Comment Rating Color', 'wp-review' ); ?></label><br>
				<span class="description">
					<?php esc_html_e( 'Use different color for ratings in comments', 'wp-review' ); ?>
				</span>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_custom_comment_colors',
						'name'     => 'wp_review_options[custom_comment_colors]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field wp-review-rating-icon">
			<div class="wp-review-field-label">
				<label for="wp_review_rating_icon"><?php esc_html_e( 'Rating icon', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_options[rating_icon]" id="wp_review_rating_icon" class="js-select2">
					<?php
					$icons = wp_review_get_icons();
					foreach ( $icons as $name => $icon ) {
						printf(
							'<option value="%1$s" data-icon="%1$s" %2$s %3$s>%4$s</option>',
							esc_attr( $name ),
							selected( $rating_icon, $name, false ),
							'fa fa-star' !== $name ? 'disabled' : '',
							esc_html( $icon['name'] )
						);
					}
					?>
				</select>
			</div>
			<a href="#" class="wpr-toggle-rating wp-review-disabled inline-block has-bg"><?php esc_html_e( 'Use Image', 'wp-review' ); ?></a>
		</div>
	</div>

	<div id="review-defaults" class="settings-tab-defaults tab-content" style="display: none;">
		<h3><?php esc_html_e( 'Defaults', 'wp-review' ); ?></h3>

		<?php $has_criteria_filter = has_filter( 'wp_review_default_criteria' ); ?>
		<?php $schemas = wp_review_schema_types(); ?>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_type"><?php esc_html_e( 'Review type', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_options[review_type]" id="wp_review_type">
					<option value="none" <?php selected( $review_type, 'none' ); ?>><?php esc_html_e( 'No Review', 'wp-review' ); ?></option>
					<?php
					foreach ( $review_types as $key => $value ) {
						if ( ! isset( $available_types[ $key ] ) ) {
							continue;
						}

						$disabled = 'circle' === $key || 'thumbs' === $key;
						printf(
							'<option value="%1$s" class="%2$s" %3$s>%4$s</option>',
							esc_attr( $key ),
							$disabled ? 'disabled' : '',
							selected( $review_type, $key, false ),
							esc_html( $value['label'] )
						);
					}
					?>
				</select>
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['location'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_location"><?php esc_html_e( 'Review Location', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_options[review_location]" id="wp_review_location">
					<option value="bottom" <?php selected( $location, 'bottom' ); ?>><?php esc_html_e( 'After Content', 'wp-review' ); ?></option>
					<option value="top" <?php selected( $location, 'top' ); ?>><?php esc_html_e( 'Before Content', 'wp-review' ); ?></option>
					<option value="custom" <?php selected( $location, 'custom' ); ?>><?php esc_html_e( 'Custom (use shortcode)', 'wp-review' ); ?></option>
				</select>

				<p id="wp_review_shortcode_hint_field">
					<input id="wp_review_shortcode_hint" type="text" value="[wp-review]" readonly="readonly">
					<span><?php esc_html_e( 'Copy & paste this shortcode in the post content.', 'wp-review' ); ?></span>
				</p>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label for="wp_review_schema"><?php esc_html_e( 'Review Schema', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_options[default_schema_type]" id="wp_review_schema">
					<?php foreach ( $schemas as $key => $arr ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $default_schema ); ?> disabled><?php echo esc_html( $arr['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Features', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<table id="wp-review-item">
					<?php if ( $has_criteria_filter ) : ?>
						<?php foreach ( $default_criteria as $item ) : ?>
							<?php if ( ! empty( $item ) ) : ?>
								<tr>
									<td style="padding:0">
										<input type="text" name="wp_review_options[default_features][]" value="<?php if ( ! empty( $item ) ) echo esc_attr( $item ); // phpcs:ignore ?>" <?php echo $has_criteria_filter ? 'disabled="disabled" readonly="readonly"' : ''; ?> />
										<?php if ( ! $has_criteria_filter ) : ?>
											<a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php else : ?>
						<?php foreach ( $items as $item ) : ?>
							<?php if ( ! empty( $item ) ) : ?>
								<tr>
									<td style="padding:0">
										<input type="text" name="wp_review_options[default_features][]" value="<?php if ( ! empty( $item ) ) echo esc_attr( $item ); // phpcs:ignore ?>" <?php echo $has_criteria_filter ? 'disabled="disabled" readonly="readonly"' : ''; ?> />
										<?php if ( ! $has_criteria_filter ) : ?>
											<a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<tr class="empty-row screen-reader-text">
						<td style="padding:0">
							<input class="focus-on-add" type="text" name="wp_review_options[default_features][]" />
							<a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a>
						</td>
					</tr>
				</table>
				<?php if ( $has_criteria_filter ) : ?>
					<p class="description"><?php esc_html_e( 'Default features are set by a filter function. Remove it to change.', 'wp-review' ); ?></p>
				<?php else : ?>
					<a class="add-row button" data-target="#wp-review-item" href="#"><?php esc_html_e( 'Add default feature', 'wp-review' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Links', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<table id="wp-review-link">
					<?php if ( ! empty( $link_texts ) ) : ?>
						<?php foreach ( $link_texts as $key => $text ) : ?>
							<?php if ( ! empty( $text ) && ! empty( $link_urls[ $key ] ) ) : ?>
								<tr>
									<td style="padding:0">
										<input type="text" name="wp_review_options[default_link_text][]" placeholder="Text" value="<?php echo esc_attr( $text ); ?>" />
										<input type="text" name="wp_review_options[default_link_url][]" placeholder="URL" value="<?php echo esc_url( $link_urls[ $key ] ); ?>" />
										<a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a>
									</td>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<tr class="empty-row screen-reader-text">
						<td style="padding:0">
							<input class="focus-on-add" type="text" name="wp_review_options[default_link_text][]" placeholder="Text" />
							<input type="text" name="wp_review_options[default_link_url][]" placeholder="URL" />
							<a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a>
						</td>
					</tr>
				</table>
				<a class="add-row button" data-target="#wp-review-link" href="#"><?php esc_html_e( 'Add default link', 'wp-review' ); ?></a>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'User Ratings', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<label>
					<input type="radio" name="wp_review_options[default_user_review_type]" id="wp-review-userReview-disable" value="<?php echo esc_attr( WP_REVIEW_REVIEW_DISABLED ); ?>" <?php checked( WP_REVIEW_REVIEW_DISABLED, $default_user_review_type ); ?> />
					<?php esc_html_e( 'Disabled', 'wp-review' ); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="wp_review_options[default_user_review_type]" id="wp-review-userReview-visitor" value="<?php echo esc_attr( WP_REVIEW_REVIEW_VISITOR_ONLY ); ?>" <?php checked( WP_REVIEW_REVIEW_VISITOR_ONLY, $default_user_review_type ); ?> />
					<?php esc_html_e( 'Visitor Rating Only', 'wp-review' ); ?>
				</label>
				<br>
				<label class="wp-review-disabled">
					<input type="radio" name="wp_review_options[default_user_review_type]" id="wp-review-userReview-comment" value="<?php echo esc_attr( WP_REVIEW_REVIEW_COMMENT_ONLY ); ?>" <?php checked( WP_REVIEW_REVIEW_COMMENT_ONLY, $default_user_review_type ); ?> disabled />
					<?php esc_html_e( 'Comment Rating Only', 'wp-review' ); ?>
					<?php wp_review_print_pro_text( true ); ?>
				</label>
				<br>
				<label class="wp-review-disabled">
					<input type="radio" name="wp_review_options[default_user_review_type]" id="wp-review-userReview-both" value="<?php echo esc_attr( WP_REVIEW_REVIEW_ALLOW_BOTH ); ?>" <?php checked( WP_REVIEW_REVIEW_ALLOW_BOTH, $default_user_review_type ); ?> disabled />
					<?php esc_html_e( 'Both', 'wp-review' ); ?>
					<?php wp_review_print_pro_text( true ); ?>
				</label>
			</div>
		</div>
	</div>

	<div id="review-embed" class="settings-tab-embed tab-content" style="display: none;">
		<h3><?php esc_html_e( 'Embed', 'wp-review' ); ?></h3>
		<p class="description"><?php esc_html_e( 'From here you can enable embed feature, which will show embed code in the frontend which site visitors can use to embed review on their site.', 'wp-review' ); ?></p>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Enable Embed', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_enable_embed',
						'name'     => 'wp_review_options[enable_embed]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Show Title', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_embed_show_title',
						'name'     => 'wp_review_options[embed_show_title]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Show Thumbnail', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_embed_show_thumbnail',
						'name'     => 'wp_review_options[embed_show_thumbnail]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Show Excerpt', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_embed_show_excerpt',
						'name'     => 'wp_review_options[embed_show_excerpt]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Show Rating Box', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_embed_show_rating_box',
						'name'     => 'wp_review_options[embed_show_rating_box]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>

		<div class="wp-review-field">
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Show Credit', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_embed_show_credit',
						'name'     => 'wp_review_options[embed_show_credit]',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php if ( $rows_left ) : ?>
		<div id="review-migrate" class="settings-tab-migrate tab-content" style="display: none;">
			<div id="settings-allow-migrate">
				<p><?php esc_html_e( 'Here you can import your existing user ratings from WP Review 1.x and WP Review Pro 1.x.', 'wp-review' ); ?></p>
				<p class="migrate-items">
					<?php
					// translators: number of rows left.
					printf( esc_html__( '%s ratings left to import.', 'wp-review' ), '<span id="migrate-items-num">' . intval( $rows_left ) . '</span>' );
					?>
				</p>
				<a href="#" class="button button-secondary" id="start-migrate" data-start="<?php echo esc_attr( $migrated_rows ); ?>"><?php esc_html_e( 'Start import', 'wp-review' ); ?></a>
				<textarea id="wp-review-migrate-log"></textarea>
			</div>
			<p class="already-migrated-msg"><?php esc_html_e( 'Ratings have already been migrated.', 'wp-review' ); ?></p>
		</div>
	<?php endif; ?>
</div>
