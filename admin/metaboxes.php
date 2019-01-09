<?php
/**
 * File for registering meta box.
 *
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @package   WP_Review
 */

/* Adds a box to the Posts edit screens. */
add_action( 'add_meta_boxes', 'wp_review_add_meta_boxes' );

/* Saves the meta box custom data. */
add_action( 'save_post', 'wp_review_save_postdata', 10, 2 );
add_action( 'save_post', 'wp_review_clear_query_cache', 10, 2 );

require_once plugin_dir_path( __FILE__ ) . 'review-options-meta-box.php';

/**
 * Adds a box to the Post edit screens.
 *
 * @since 1.0
 */
function wp_review_add_meta_boxes() {
	$post_types           = get_post_types( array( 'public' => true ), 'names' );
	$excluded_post_types  = apply_filters( 'wp_review_excluded_post_types', array( 'attachment' ) );
	$hide_review_box      = wp_review_network_option( 'hide_ratings_in_posts_' );
	$hide_review_links    = wp_review_network_option( 'hide_review_links_' );
	$hide_review_desc     = wp_review_network_option( 'hide_review_description_' );
	$hide_review_features = wp_review_network_option( 'hide_features_' );
	$hide_user_reviews    = wp_review_network_option( 'hide_user_reviews_' );

	if ( ! $hide_review_box && current_user_can( 'wp_review_single_page' ) ) {
		foreach ( $post_types as $post_type ) {
			if ( ! in_array( $post_type, $excluded_post_types, true ) ) {
				add_meta_box(
					'wp-review-metabox-review',
					__( 'Review', 'wp-review' ),
					'wp_review_render_meta_box_review_options',
					$post_type,
					'normal',
					'high'
				);

				if ( ! $hide_review_features && current_user_can( 'wp_review_features' ) ) {
					add_meta_box(
						'wp-review-metabox-item',
						__( 'Review Item', 'wp-review' ),
						'wp_review_render_meta_box_item',
						$post_type,
						'normal',
						'high'
					);
				}

				if ( ! $hide_review_links && current_user_can( 'wp_review_links' ) ) {
					add_meta_box(
						'wp-review-metabox-reviewLinks',
						__( 'Review Links', 'wp-review' ),
						'wp_review_render_meta_box_review_links',
						$post_type,
						'normal',
						'high'
					);
				}
				if ( ! $hide_review_desc && current_user_can( 'wp_review_description' ) ) {
					add_meta_box(
						'wp-review-metabox-desc',
						__( 'Review Description', 'wp-review' ),
						'wp_review_render_meta_box_desc',
						$post_type,
						'normal',
						'high'
					);
				}

				if ( ! $hide_user_reviews && current_user_can( 'wp_review_user_reviews' ) ) {
					add_meta_box(
						'wp-review-metabox-userReview',
						__( 'User Reviews', 'wp-review' ),
						'wp_review_render_meta_box_user_review',
						$post_type,
						'normal',
						'high'
					);
				}
			}
		}
	}

}

/**
 * Render the meta box.
 *
 * @since 1.0
 *
 * @param WP_Post $post Post object.
 */
function wp_review_render_meta_box_item( $post ) {
	$form_field       = new WP_Review_Form_Field();
	$options          = get_option( 'wp_review_options' );
	$default_location = wp_review_get_default_location();
	$default_criteria = wp_review_get_default_criteria();

	$default_items = array();
	foreach ( $default_criteria as $index => $item ) {
		if ( empty( $item ) ) {
			continue;
		}
		$default_items[ $index ] = array(
			'id'                   => $index,
			'wp_review_item_title' => $item,
			'wp_review_item_star'  => '',
		);
	}

	$global_colors         = wp_review_get_global_colors();
	$global_color          = $global_colors['color'];
	$global_inactive_color = $global_colors['inactive_color'];

	/* Retrieve an existing value from the database. */
	$custom_colors   = get_post_meta( $post->ID, 'wp_review_custom_colors', true );
	$custom_location = get_post_meta( $post->ID, 'wp_review_custom_location', true );

	$items = wp_review_get_review_items( $post->ID );
	if ( '' === $items ) {
		$items = $default_items;
	}
	$items = array_values( $items );

	$total = get_post_meta( $post->ID, 'wp_review_total', true );

	$post_color          = get_post_meta( $post->ID, 'wp_review_color', true );
	$post_inactive_color = get_post_meta( $post->ID, 'wp_review_inactive_color', true );
	$color               = $post_color;
	$inactive_color      = $post_inactive_color;

	$location    = get_post_meta( $post->ID, 'wp_review_location', true );
	$fontcolor   = get_post_meta( $post->ID, 'wp_review_fontcolor', true );
	$bgcolor1    = get_post_meta( $post->ID, 'wp_review_bgcolor1', true );
	$bgcolor2    = get_post_meta( $post->ID, 'wp_review_bgcolor2', true );
	$bordercolor = get_post_meta( $post->ID, 'wp_review_bordercolor', true );

	if ( ! $color ) {
		$color = $global_color;
	}
	if ( ! $inactive_color ) {
		$inactive_color = $global_inactive_color;
	}

	if ( '' === $location ) {
		$location = ! empty( $options['location'] ) ? $options['location'] : $default_location;
	}
	if ( '' === $fontcolor ) {
		$fontcolor = $global_colors['fontcolor'];
	}
	if ( '' === $bgcolor1 ) {
		$bgcolor1 = $global_colors['bgcolor1'];
	}
	if ( '' === $bgcolor2 ) {
		$bgcolor2 = $global_colors['bgcolor2'];
	}
	if ( '' === $bordercolor ) {
		$bordercolor = $global_colors['bordercolor'];
	}

	$fields           = array(
		'location'         => true,
		'color'            => true,
		'inactive_color'   => true,
		'fontcolor'        => true,
		'bgcolor1'         => true,
		'bgcolor2'         => true,
		'bordercolor'      => true,
		'fontfamily'       => true,
		'custom_colors'    => true,
		'custom_location'  => true,
		'disable_features' => true,
	);
	$displayed_fields = apply_filters( 'wp_review_metabox_item_fields', $fields );

	$review_templates = wp_review_get_box_templates();
	$box_template     = get_post_meta( $post->ID, 'wp_review_box_template', true );

	if ( ! $box_template || ! in_array( $box_template, array( 'default', 'aqua' ), true ) ) {
		$box_template = wp_review_option( 'box_template', 'default' );
	}
	$box_template_img = ! empty( $review_templates[ $box_template ] ) ? $review_templates[ $box_template ]['image'] : WP_REVIEW_ASSETS . 'images/largethumb.png';

	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-item-nonce' );
	?>
	<input type="hidden" id="wpr-review-color-value" value="<?php echo esc_attr( $color ); ?>">
	<input type="hidden" id="wpr-review-inactive-color-value" value="<?php echo esc_attr( $inactive_color ); ?>">
	<input type="hidden" id="wpr-review-global-color-value" value="<?php echo esc_attr( $global_color ); ?>">
	<input type="hidden" id="wpr-review-global-inactive-color-value" value="<?php echo esc_attr( $global_inactive_color ); ?>">
	<input type="hidden" id="wpr-review-items-data" value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">

	<div id="wpr-review-items-app">
		<input type="hidden" id="wpr-review-type-2" value="">
		<div class="wpr-review-items"></div>

		<div class="setting-row final-row" style="padding-right: 10px;">
			<div class="col-1">
				<button type="button" class="button add-item"><?php esc_html_e( 'Add item', 'wp-review' ); ?></button>
			</div>
			<div class="col-2"></div>
			<div class="col-3">
				<div class="wpr-review-items-total">
					<input type="text" class="input-total" name="wp_review_total" value="<?php echo floatval( $total ); ?>" size="4">
					<label><?php esc_html_e( 'Total', 'wp-review' ); ?></label>
				</div>
			</div>
		</div>
	</div>

	<script type="text/html" id="tmpl-wpr-review-item">
		<span class="wpr-icon-move dashicons dashicons-menu"></span>

		{{{ data.itemNameSetting }}}

		{{{ data.itemRatingSetting }}}

		{{{ data.itemColorSetting }}}

		{{{ data.itemInactiveColorSetting }}}

		<input type="hidden" name="wp_review_item_id[]" value="{{ data.item_id }}">

		<button type="button" class="button delete-item"><?php esc_html_e( 'Delete', 'wp-review' ); ?></button>
	</script>

	<script type="text/html" id="tmpl-wpr-review-item-name">
		<div class="setting-row">
			<div class="col-1">
				<label for="wpr-review-item-title-{{ data.id }}"><?php esc_html_e( 'Feature Name', 'wp-review' ); ?></label>
			</div>
			<div class="col-2">
				<input type="text" id="wpr-review-item-title-{{ data.id }}" name="wp_review_item_title[]" class="input-title" value="{{ data.wp_review_item_title }}">
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-wpr-review-item-color">
		<div class="setting-row">
			<div class="wp-review-disabled col-1">
				<label for="wpr-review-item-color-{{ data.id }}"><?php esc_html_e( 'Feature Color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>
			<div class="col-2">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" id="wpr-review-item-color-{{ data.id }}" name="wp_review_item_color[]" class="input-color" value="{{ data.wp_review_item_color }}" data-default-color="{{ data.wp_review_item_color }}" disabled>
				</span>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-wpr-review-item-inactive-color">
		<div class="setting-row">
			<div class="wp-review-disabled col-1">
				<label for="wpr-review-item-inactive-color-{{ data.id }}"><?php esc_html_e( 'Inactive Color', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>
			<div class="col-2">
				<span class="wp-review-disabled inline-block has-bg">
					<input type="text" id="wpr-review-item-inactive-color-{{ data.id }}" name="wp_review_item_inactive_color[]" class="input-inactive-color" value="{{ data.wp_review_item_inactive_color }}" data-default-color="{{ data.wp_review_item_inactive_color }}" disabled>
				</span>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-wpr-review-item-rating">
		<div class="setting-row">
			<div class="col-1">
				<label for="wpr-review-item-star-{{ data.id }}"><?php esc_html_e( 'Feature Score', 'wp-review' ); ?></label>
			</div>
			<div class="col-2">
				<input type="text" id="wpr-review-item-star-{{ data.id }}" name="wp_review_item_star[]" class="input-star" value="{{ data.wp_review_item_star }}" data-type="{{ data.type }}" data-color="{{ data.wp_review_item_color }}" data-inactive-color="{{ data.wp_review_item_inactive_color }}" size="4">
			</div>
			<div class="col-3"></div>
		</div>
	</script>

	<div class="wp-review-field"<?php if ( empty( $displayed_fields['disable_features'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
		<div class="wp-review-disabled wp-review-field-label">
			<label><?php esc_html_e( 'Hide Features', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<?php
			$form_field->render_switch(
				array(
					'id'       => 'wp_review_disable_features',
					'name'     => 'wp_review_disable_features',
					'disabled' => true,
				)
			);
			?>
		</div>
	</div>
	<div class="wp-review-field"<?php if ( empty( $displayed_fields['custom_location'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
		<div class="wp-review-field-label">
			<label><?php esc_html_e( 'Custom Location', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			$form_field->render_switch(
				array(
					'id'    => 'wp_review_custom_location',
					'name'  => 'wp_review_custom_location',
					'value' => $custom_location,
				)
			);
			?>
		</div>
	</div>

	<div class="wp-review-location-options"<?php if ( empty( $custom_location ) || empty( $displayed_fields['location'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_location"><?php esc_html_e( 'Review Location', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_location" id="wp_review_location">
					<option value="bottom" <?php selected( $location, 'bottom' ); ?>><?php esc_html_e( 'After Content', 'wp-review' ); ?></option>
					<option value="top" <?php selected( $location, 'top' ); ?>><?php esc_html_e( 'Before Content', 'wp-review' ); ?></option>
					<option value="custom" <?php selected( $location, 'custom' ); ?>><?php esc_html_e( 'Custom (use shortcode)', 'wp-review' ); ?></option>
				</select>

				<p id="wp_review_shortcode_hint_field">
					<!-- <label for="wp_review_shortcode_hint"></label> -->
					<input id="wp_review_shortcode_hint" type="text" value='[wp-review id="<?php echo intval( trim( $post->ID ) ); ?>"]' readonly="readonly" />
					<span class="description"><?php esc_html_e( 'Copy &amp; paste this shortcode in the content.', 'wp-review' ); ?></span>
				</p>
			</div>
		</div>
	</div>

	<div class="wp-review-field"<?php if ( empty( $displayed_fields['custom_colors'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
		<div class="wp-review-field-label">
			<label><?php esc_html_e( 'Custom Layout', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			$form_field->render_switch(
				array(
					'id'    => 'wp_review_custom_colors',
					'name'  => 'wp_review_custom_colors',
					'value' => $custom_colors,
				)
			);
			?>
		</div>
	</div>

	<div class="wp-review-color-options"<?php if ( empty( $custom_colors ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
		<div class="wp-review-field vertical">
			<div class="wp-review-field-label">
				<label for="wp_review_box_template"><?php esc_html_e( 'Default', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<div id="wp_review_box_template_wrapper">
					<select name="wp_review_box_template" id="wp_review_box_template">
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

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['color'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_color"><?php esc_html_e( 'Review Color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" id="wp_review_color" class="wp-review-color" name="wp_review_color" value="<?php echo esc_attr( $color ); ?>" data-default-color="<?php echo esc_attr( $color ); ?>">
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['inactive_color'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_inactive_color"><?php esc_html_e( 'Inactive Review Color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" id="wp_review_inactive_color" class="wp-review-color" name="wp_review_inactive_color" value="<?php echo esc_attr( $inactive_color ); ?>" data-default-color="<?php echo esc_attr( $inactive_color ); ?>">
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['fontcolor'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_fontcolor"><?php esc_html_e( 'Font Color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_fontcolor" id ="wp_review_fontcolor" value="<?php echo esc_attr( $fontcolor ); ?>" data-default-color="<?php echo esc_attr( $fontcolor ); ?>">
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['bgcolor1'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_bgcolor1"><?php esc_html_e( 'Heading Background Color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_bgcolor1" id ="wp_review_bgcolor1" value="<?php echo esc_attr( $bgcolor1 ); ?>" data-default-color="<?php echo esc_attr( $bgcolor1 ); ?>">
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['bgcolor2'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_bgcolor2"><?php esc_html_e( 'Background Color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_bgcolor2" id="wp_review_bgcolor2" value="<?php echo esc_attr( $bgcolor2 ); ?>" data-default-color="<?php echo esc_attr( $bgcolor2 ); ?>">
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['bordercolor'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-field-label">
				<label for="wp_review_bordercolor"><?php esc_html_e( 'Border Color', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" class="wp-review-color" name="wp_review_bordercolor" id="wp_review_bordercolor" value="<?php echo esc_attr( $bordercolor ); ?>" data-default-color="<?php echo esc_attr( $bordercolor ); ?>">
			</div>
		</div>

		<div class="wp-review-field"<?php if ( empty( $displayed_fields['fontfamily'] ) ) echo ' style="display: none;"'; // phpcs:ignore ?>>
			<div class="wp-review-disabled wp-review-field-label">
				<label><?php esc_html_e( 'Google Font', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_fontfamily',
						'name'     => 'wp_review_fontfamily',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label><?php esc_html_e( 'Custom Width', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<?php
			$form_field->render_switch(
				array(
					'id'       => 'wp_review_custom_width',
					'name'     => 'wp_review_custom_width',
					'disabled' => true,
				)
			);
			?>
		</div>
	</div>
	<?php
}


/**
 * Renders desc meta box.
 *
 * @param WP_Post $post Post object.
 */
function wp_review_render_meta_box_desc( $post ) {

	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-desc-nonce' );

	/* Retrieve existing values from the database. */
	$desc       = get_post_meta( $post->ID, 'wp_review_desc', true );
	$desc_title = get_post_meta( $post->ID, 'wp_review_desc_title', true );
	if ( ! $desc_title ) {
		$desc_title = __( 'Summary', 'wp-review' );
	}
	$form_field = new WP_Review_Form_Field();
	$hidden     = wp_review_is_hidden_desc( $post->ID ) ? 'hidden' : '';
	?>
	<div id="wp_review_desc_settings" class="<?php echo esc_attr( $hidden ); ?>">
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label for="wp_review_desc_title"><?php esc_html_e( 'Description title', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<input type="text" name="wp_review_desc_title" id="wp_review_desc_title" class="large-text" value="<?php echo esc_attr( $desc_title ); ?>">
			</div>
		</div>

		<div class="wp-review-field vertical wp-review-description">
			<div class="wp-review-field-label">
				<label for="wp_review_desc"><?php esc_html_e( 'Description content', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<?php
				/* Display wp editor field. */
				wp_editor(
					$desc,
					'wp_review_desc',
					array(
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
							'toolbar2' => '',
							'toolbar3' => '',
						),
						'quicktags'     => true,
						'media_buttons' => false,
						'textarea_rows' => 6,
					)
				);
				?>
			</div>
		</div>

		<div class="wpr-flex wpr-flex-wrap border-box">
			<div class="wpr-col-1-2 pr-10">
				<p class="wp-review-disabled pros-cons-title">
					<strong><?php esc_html_e( 'Pros', 'wp-review' ); ?></strong>
					<?php wp_review_print_pro_text(); ?>
				</p>

				<span class="wp-review-disabled inline-block has-bg">
					<?php
					/* Display wp editor field. */
					wp_editor(
						'',
						'wp_review_pros',
						array(
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
								'toolbar2' => '',
								'toolbar3' => '',
							),
							'quicktags'     => true,
							'media_buttons' => false,
							'textarea_rows' => 6,
						)
					);
					?>
				</span>
			</div>

			<div class="wpr-col-1-2 pl-10">
				<p class="wp-review-disabled pros-cons-title">
					<strong><?php esc_html_e( 'Cons', 'wp-review' ); ?></strong>
					<?php wp_review_print_pro_text(); ?>
				</p>

				<span class="wp-review-disabled inline-block has-bg">
					<?php
					/* Display wp editor field. */
					wp_editor(
						'',
						'wp_review_cons',
						array(
							'tinymce'       => array(
								'toolbar1' => 'bold,italic,underline,bullist,numlist,separator,separator,link,unlink,undo,redo,removeformat',
								'toolbar2' => '',
								'toolbar3' => '',
							),
							'quicktags'     => true,
							'media_buttons' => false,
							'textarea_rows' => 6,
						)
					);
					?>
				</span>
			</div>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label><?php esc_html_e( 'Hide Description, Pros/Cons & Total Rating', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<?php
			$form_field->render_switch(
				array(
					'id'       => 'wp_review_hide_desc',
					'name'     => 'wp_review_hide_desc',
					'disabled' => true,
				)
			);
			?>
		</div>
	</div>
	<?php
}

/**
 * Maps default link texts and urls.
 *
 * @since 5.0.3 Move this function out of `wp_review_render_meta_box_review_links()`
 *
 * @param string $text Link text.
 * @param string $url  Link url.
 * @return array
 */
function wp_review_get_default_links( $text, $url ) {
	return array(
		'text' => $text,
		'url'  => $url,
	);
}

/**
 * Renders review links meta box.
 *
 * @param WP_Post $post Post object.
 */
function wp_review_render_meta_box_review_links( $post ) {

	wp_nonce_field( basename( __FILE__ ), 'wp-review-links-options-nonce' );

	wp_review_switch_to_main();
	$options = get_option( 'wp_review_options' );
	if ( is_multisite() ) {
		restore_current_blog();
	}

	$defaults = array_map(
		'wp_review_get_default_links',
		empty( $options['default_link_text'] ) ? array() : $options['default_link_text'],
		empty( $options['default_link_url'] ) ? array() : $options['default_link_url']
	);

	$items = get_post_meta( $post->ID, 'wp_review_links', true );
	if ( ! is_array( $items ) ) {
		$items = $defaults;
	}
	?>
	<table id="wp-review-links" class="wp-review-links" width="100%">

		<thead>
			<tr>
				<th width="5%"></th>
				<th width="45%"><?php esc_html_e( 'Text', 'wp-review' ); ?></th>
				<th width="40%"><?php esc_html_e( 'URL', 'wp-review' ); ?></th>
				<th width="10%"></th>
			</tr>
		</thead>

		<tbody>
		<?php if ( ! empty( $items ) && ( isset( $items[0] ) && ! empty( $items[0]['text'] ) ) ) : ?>

			<?php foreach ( $items as $item ) { ?>

				<?php if ( ! empty( $item['text'] ) && ! empty( $item['url'] ) ) : ?>
					<tr>
						<td class="handle">
							<span class="dashicons dashicons-menu"></span>
						</td>
						<td>
							<input type="text" class="widefat" name="wp_review_link_title[]" value="<?php echo esc_attr( $item['text'] ); ?>" />
						</td>
						<td>
							<input type="text" class="widefat" name="wp_review_link_url[]" value="<?php echo esc_url( $item['url'] ); ?>" />
						</td>
						<td><a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a></td>
					</tr>
				<?php endif; ?>

			<?php } ?>

		<?php else : ?>

			<tr>
				<td class="handle"><span class="dashicons dashicons-menu"></span></td>
				<td><input type="text" class="widefat" name="wp_review_link_title[]" /></td>
				<td><input type="text" class="widefat" name="wp_review_link_url[]" /></td>
				<td><a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a></td>
			</tr>

		<?php endif; ?>

		<!-- empty hidden one for jQuery -->
		<tr class="empty-row screen-reader-text">
			<td class="handle"><span class="dashicons dashicons-menu"></span></td>
			<td><input type="text" class="widefat focus-on-add" name="wp_review_link_title[]" /></td>
			<td><input type="text" class="widefat" name="wp_review_link_url[]" /></td>
			<td><a class="button remove-row" href="#"><?php esc_html_e( 'Delete', 'wp-review' ); ?></a></td>
		</tr>

		</tbody>

	</table>

	<a class="add-row button" data-target="#wp-review-links" href="#"><?php esc_html_e( 'Add another', 'wp-review' ); ?></a>
	<?php
}

/**
 * Renders user review meta box.
 *
 * @param WP_Post $post Post object.
 */
function wp_review_render_meta_box_user_review( $post ) {
	/* Add an nonce field so we can check for it later. */
	wp_nonce_field( basename( __FILE__ ), 'wp-review-userReview-nonce' );
	$enabled = wp_review_get_user_rating_setup( $post->ID );

	$type = get_post_meta( $post->ID, 'wp_review_user_review_type', true );
	if ( ! $type ) {
		$type = wp_review_option( 'review_type', 'none' );
	}

	// $available_types = apply_filters( 'wp_review_metabox_user_rating_types', wp_review_get_review_types( 'user' ) );
	$available_types = wp_review_get_rating_types();
	$product_price   = wp_review_get_product_price( $post->ID );

	$form_field = new WP_Review_Form_Field();
	?>
	<div class="wp-review-field no-flex">
		<p>
			<label>
				<input type="radio" name="wp_review_userReview" id="wp-review-userReview-disable" value="<?php echo esc_attr( WP_REVIEW_REVIEW_DISABLED ); ?>" <?php checked( WP_REVIEW_REVIEW_DISABLED, $enabled ); ?>>
				<?php esc_html_e( 'Disabled', 'wp-review' ); ?>
			</label>
		</p>

		<p>
			<label>
				<input type="radio" name="wp_review_userReview" id="wp-review-userReview-visitor" value="<?php echo esc_attr( WP_REVIEW_REVIEW_VISITOR_ONLY ); ?>" <?php checked( WP_REVIEW_REVIEW_VISITOR_ONLY, $enabled ); ?>>
				<?php esc_html_e( 'Visitor Rating Only', 'wp-review' ); ?>
			</label>
		</p>

		<p>
			<label class="wp-review-disabled">
				<input type="radio" name="wp_review_userReview" id="wp-review-userReview-comment" value="<?php echo esc_attr( WP_REVIEW_REVIEW_COMMENT_ONLY ); ?>" <?php checked( WP_REVIEW_REVIEW_COMMENT_ONLY, $enabled ); ?> disabled />
				<?php esc_html_e( 'Comment Rating Only', 'wp-review' ); ?>
				<?php wp_review_print_pro_text( true ); ?>
			</label>
		</p>

		<p>
			<label class="wp-review-disabled">
				<input type="radio" name="wp_review_userReview" id="wp-review-userReview-both" value="<?php echo esc_attr( WP_REVIEW_REVIEW_ALLOW_BOTH ); ?>" <?php checked( WP_REVIEW_REVIEW_ALLOW_BOTH, $enabled ); ?> disabled/>
				<?php esc_html_e( 'Both', 'wp-review' ); ?>
				<?php wp_review_print_pro_text( true ); ?>
			</label>
		</p>
	</div>

	<?php $hidden = WP_REVIEW_REVIEW_DISABLED == $enabled ? 'hidden' : ''; ?>
	<div class="show-if-both <?php echo esc_attr( $hidden ); ?>">
		<div class="edit-ratings-notice update-nag" style="margin-top: 0;"><?php esc_html_e( 'If you are changing User Rating Type & post already have user ratings, please edit or remove existing ratings.', 'wp-review' ); ?></div>

		<div class="wp-review-field" id="wp_review_rating_type">
			<div class="wp-review-field-label">
				<label for="rating_type"><?php esc_html_e( 'User Rating Type', 'wp-review' ); ?></label>
			</div>

			<div class="wp-review-field-option">
				<select name="wp_review_user_review_type" id="rating_type">
					<?php
					foreach ( $available_types as $key => $available_type ) {
						// Skip ones that only have output template.
						if ( ! $available_type['user_rating'] ) {
							continue;
						}

						$disabled = 'circle' === $key || 'thumbs' === $key;
						printf(
							'<option value="%1$s" class="%2$s" %3$s>%4$s</option>',
							esc_attr( $key ),
							$disabled ? 'disabled' : '',
							selected( $type, $key, false ),
							esc_html( $available_type['label'] )
						);
					}
					?>
				</select>
			</div>
		</div>
	</div>

	<?php $hidden = WP_REVIEW_REVIEW_DISABLED == $enabled ? 'hidden' : ''; ?>
	<div class="show-if-both <?php echo esc_attr( $hidden ); ?>">
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'User can:', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<span class="wp-review-disabled inline-block has-bg">
					<select name="wp_review_user_can_rate_feature" id="wp_review_user_can_rate_feature" disabled>
						<option value=""><?php esc_html_e( 'Give Overall Rating', 'wp-review' ); ?></option>
						<option value="1"><?php esc_html_e( 'Rate Each Feature', 'wp-review' ); ?></option>
					</select>
				</span>
			</div>
		</div>
	</div>

	<?php $hidden = in_array( $enabled, array( WP_REVIEW_REVIEW_DISABLED, WP_REVIEW_REVIEW_COMMENT_ONLY ) ) ? 'hidden' : ''; ?>
	<div class="show-if-visitor <?php echo esc_attr( $hidden ); ?>">
		<div class="wp-review-field">
			<div class="wp-review-field-label">
				<label><?php esc_html_e( 'Hide Visitors Rating in Review Box', 'wp-review' ); ?></label>
				<?php wp_review_print_pro_text(); ?>
			</div>

			<div class="wp-review-field-option">
				<?php
				$form_field->render_switch(
					array(
						'id'       => 'wp_review_hide_visitors_rating',
						'name'     => 'wp_review_hide_visitors_rating',
						'disabled' => true,
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-field-label">
			<label for="wp_review_product_price"><?php esc_html_e( 'Product Price', 'wp-review' ); ?></label>
		</div>

		<div class="wp-review-field-option">
			<input type="text" name="wp_review_product_price" id="wp_review_product_price" value="<?php echo esc_attr( $product_price ); ?>">
		</div>
	</div>

	<div class="wp-review-field">
		<div class="wp-review-disabled wp-review-field-label">
			<label for="wp_review_allow_comment_feedback"><?php esc_html_e( 'Comment Feedback (helpful/unhelpful)', 'wp-review' ); ?></label>
			<?php wp_review_print_pro_text(); ?>
		</div>

		<div class="wp-review-field-option">
			<span class="wp-review-disabled inline-block has-bg">
				<select name="wp_review_allow_comment_feedback" id="wp_review_allow_comment_feedback" disabled>
					<option value=""><?php esc_html_e( 'Use global options', 'wp-review' ); ?></option>
					<option value="yes"><?php esc_html_e( 'Yes', 'wp-review' ); ?></option>
					<option value="no"><?php esc_html_e( 'No', 'wp-review' ); ?></option>
				</select>
			</span>
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
				data-post-id="<?php echo intval( $post->ID ); ?>"
			><?php esc_html_e( 'Purge visitor ratings', 'wp-review' ); ?></button>
			<span class="description"><?php esc_html_e( 'Click to remove all visitor ratings of this post.', 'wp-review' ); ?></span>
		</p>
		<?php
	}
}

/**
 * Saves the meta box.
 *
 * @since 1.0
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return int
 */
function wp_review_save_postdata( $post_id, $post ) {

	/* If this is an autosave, our form has not been submitted, so we don't want to do anything. */
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( ! isset( $_POST['wp-review-review-options-nonce'] ) || ! wp_verify_nonce( $_POST['wp-review-review-options-nonce'], 'wp-review-meta-box-options' ) ) {
		return $post_id;
	}

	$hide_desc            = false;
	$hide_links           = false;
	$hide_user_reviews    = false;
	$hide_review_features = false;

	if ( is_multisite() ) {
		$hide_desc            = wp_review_network_option( 'hide_review_description_' );
		$hide_links           = wp_review_network_option( 'hide_review_links_' );
		$hide_user_reviews    = wp_review_network_option( 'hide_user_reviews_' );
		$hide_review_features = wp_review_network_option( 'hide_features_' );
	}

	if ( ! $hide_desc && ! current_user_can( 'wp_review_description' ) ) {
		$hide_desc = true;
	}

	if ( ! $hide_links && ! current_user_can( 'wp_review_links' ) ) {
		$hide_links = true;
	}

	if ( ! $hide_user_reviews && ! current_user_can( 'wp_review_user_reviews' ) ) {
		$hide_user_reviews = true;
	}

	if ( ! $hide_review_features && ! current_user_can( 'wp_review_features' ) ) {
		$hide_review_features = true;
	}

	if ( ! $hide_review_features && ( ! isset( $_POST['wp-review-item-nonce'] ) || ! wp_verify_nonce( $_POST['wp-review-item-nonce'], basename( __FILE__ ) ) ) ) {
		return $post_id;
	}

	if ( ! $hide_desc && ( ! isset( $_POST['wp-review-desc-nonce'] ) || ! wp_verify_nonce( $_POST['wp-review-desc-nonce'], basename( __FILE__ ) ) ) ) {
		return $post_id;
	}

	if ( ! $hide_links && ( ! isset( $_POST['wp-review-links-options-nonce'] ) || ! wp_verify_nonce( $_POST['wp-review-links-options-nonce'], basename( __FILE__ ) ) ) ) {
		return $post_id;
	}

	if ( ! $hide_user_reviews && ( ! isset( $_POST['wp-review-userReview-nonce'] ) || ! wp_verify_nonce( $_POST['wp-review-userReview-nonce'], basename( __FILE__ ) ) ) ) {
		return $post_id;
	}

	/* Check the user's permissions. */
	if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	$type = filter_input( INPUT_POST, 'wp_review_type', FILTER_SANITIZE_STRING );
	if ( ! $type ) {
		$type = wp_review_option( 'review_type', 'none' );
	}
	update_post_meta( $post_id, 'wp_review_type', $type );
	// Do not save review data if never enable it.
	if ( 'none' === $type && ! get_post_meta( $post_id, 'wp_review_bgcolor1', true ) ) {
		return $post_id;
	}

	$meta = array(
		'wp_review_custom_location'  => filter_input( INPUT_POST, 'wp_review_custom_location', FILTER_SANITIZE_STRING ),
		'wp_review_custom_colors'    => filter_input( INPUT_POST, 'wp_review_custom_colors', FILTER_SANITIZE_STRING ),
		'wp_review_custom_author'    => filter_input( INPUT_POST, 'wp_review_custom_author', FILTER_SANITIZE_STRING ),
		'wp_review_location'         => filter_input( INPUT_POST, 'wp_review_location', FILTER_SANITIZE_STRING ),
		'wp_review_heading'          => filter_input( INPUT_POST, 'wp_review_heading', FILTER_SANITIZE_STRING ),
		'wp_review_desc_title'       => filter_input( INPUT_POST, 'wp_review_desc_title', FILTER_SANITIZE_STRING ),
		'wp_review_desc'             => ! empty( $_POST['wp_review_desc'] ) ? wp_kses_post( wp_unslash( $_POST['wp_review_desc'] ) ) : '',
		'wp_review_hide_desc'        => filter_input( INPUT_POST, 'wp_review_hide_desc', FILTER_SANITIZE_STRING ),
		'wp_review_userReview'       => filter_input( INPUT_POST, 'wp_review_userReview', FILTER_SANITIZE_STRING ),
		'wp_review_total'            => filter_input( INPUT_POST, 'wp_review_total', FILTER_SANITIZE_STRING ),
		'wp_review_color'            => filter_input( INPUT_POST, 'wp_review_color', FILTER_SANITIZE_STRING ),
		'wp_review_inactive_color'   => filter_input( INPUT_POST, 'wp_review_inactive_color', FILTER_SANITIZE_STRING ),
		'wp_review_fontcolor'        => filter_input( INPUT_POST, 'wp_review_fontcolor', FILTER_SANITIZE_STRING ),
		'wp_review_bgcolor1'         => filter_input( INPUT_POST, 'wp_review_bgcolor1', FILTER_SANITIZE_STRING ),
		'wp_review_bgcolor2'         => filter_input( INPUT_POST, 'wp_review_bgcolor2', FILTER_SANITIZE_STRING ),
		'wp_review_bordercolor'      => filter_input( INPUT_POST, 'wp_review_bordercolor', FILTER_SANITIZE_STRING ),
		'wp_review_author'           => filter_input( INPUT_POST, 'wp_review_author', FILTER_SANITIZE_STRING ),
		'wp_review_schema'           => filter_input( INPUT_POST, 'wp_review_schema', FILTER_SANITIZE_STRING ),
		'wp_review_rating_schema'    => filter_input( INPUT_POST, 'wp_review_rating_schema', FILTER_SANITIZE_STRING ),
		'wp_review_show_schema_data' => filter_input( INPUT_POST, 'wp_review_show_schema_data', FILTER_SANITIZE_STRING ),
		'wp_review_user_review_type' => filter_input( INPUT_POST, 'wp_review_user_review_type', FILTER_SANITIZE_STRING ),
		'wp_review_product_price'    => filter_input( INPUT_POST, 'wp_review_product_price', FILTER_SANITIZE_STRING ),
		'wp_review_box_template'     => filter_input( INPUT_POST, 'wp_review_box_template', FILTER_SANITIZE_STRING ),
	);

	$default_colors   = wp_review_get_global_colors();
	$default_color    = $default_colors['color'];
	$default_inactive = $default_colors['inactive_color'];

	if ( $meta['wp_review_color'] === $default_color ) {
		$meta['wp_review_color'] = '';
	}

	if ( $meta['wp_review_inactive_color'] === $default_inactive ) {
		$meta['wp_review_inactive_color'] = '';
	}

	foreach ( $meta as $meta_key => $new_meta_value ) {
		$new_meta_value = wp_review_normalize_option_value( $new_meta_value );

		if ( false === $new_meta_value ) {
			$new_meta_value = '0';
		}

		if ( 'wp_review_box_template' === $meta_key && ! in_array( $new_meta_value, array( 'default', 'aqua' ), true ) ) {
			continue;
		}

		if ( current_user_can( 'delete_post_meta', $post_id ) && '' === $new_meta_value ) {
			delete_post_meta( $post_id, $meta_key );
			continue;
		}

		if ( current_user_can( 'edit_post_meta', $post_id ) ) {
			update_post_meta( $post_id, $meta_key, $new_meta_value );
		}
	}

	wp_review_save_review_items_data( $post_id );

	$old       = get_post_meta( $post_id, 'wp_review_item', true );
	$link_text = (array) filter_input( INPUT_POST, 'wp_review_link_title', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY );
	$link_url  = (array) filter_input( INPUT_POST, 'wp_review_link_url', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY );
	$new_links = array();

	if ( ! empty( $link_text ) ) {
		foreach ( $link_text as $key => $text ) {
			if ( ! empty( $text ) && ! empty( $link_url[ $key ] ) ) {
				$new_links[] = array(
					'text' => $text,
					'url'  => $link_url[ $key ],
				);
			}
		}
	}

	if ( empty( $new_links ) ) {
		delete_post_meta( $post_id, 'wp_review_links' );
	} else {
		update_post_meta( $post_id, 'wp_review_links', $new_links );
	}
	if ( isset( $_POST['wp_review_schema_options'] ) ) {
		update_post_meta( $post_id, 'wp_review_schema_options', $_POST['wp_review_schema_options'] );
	}

	/**
	 * Delete all data when switched to 'No Review' type.
	 */
	if ( 'none' === $type ) {
		delete_post_meta( $post_id, 'wp_review_desc', $_POST['wp_review_desc'] );
		delete_post_meta( $post_id, 'wp_review_heading', $_POST['wp_review_heading'] );
		delete_post_meta( $post_id, 'wp_review_userReview', $_POST['wp_review_userReview'] );
		delete_post_meta( $post_id, 'wp_review_item', $old );
	}
}

/**
 * Clears transients
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function wp_review_clear_query_cache( $post_id, $post ) {
	global $wpdb;
	$where = $wpdb->prepare( 'WHERE option_name REGEXP %s', '_transient(_timeout)?_wp_review_[0-9a-f]{32}' );
	$wpdb->query( "DELETE FROM {$wpdb->prefix}options {$where}" ); // WPCS: unprepared SQL ok.
}

/**
 * Saves review items data.
 *
 * @param int $post_id Post ID.
 */
function wp_review_save_review_items_data( $post_id ) {
	$old = get_post_meta( $post_id, 'wp_review_item', true );

	if ( ! empty( $_POST['wp_review_item_title'] ) ) { // WPCS: csrf ok.
		$title = $_POST['wp_review_item_title']; // WPCS: csrf ok.
		$star  = $_POST['wp_review_item_star']; // WPCS: csrf ok.
		$ids   = $_POST['wp_review_item_id']; // WPCS: csrf ok.
		$new   = array();

		$count = count( $title );

		for ( $i = 0; $i < $count; $i++ ) {
			if ( empty( $star[ $i ] ) ) {
				continue; // Prevent item without score.
			}

			$new[ $i ]                        = array();
			$new[ $i ]['wp_review_item_star'] = floatval( $star[ $i ] );

			if ( ! empty( $ids[ $i ] ) ) {
				$new[ $i ]['id'] = sanitize_text_field( wp_unslash( $ids[ $i ] ) );
			}

			if ( ! empty( $title[ $i ] ) ) {
				$new[ $i ]['wp_review_item_title'] = sanitize_text_field( wp_unslash( $title[ $i ] ) );
			}
		}

		if ( ! empty( $new ) && $new !== $old ) {
			update_post_meta( $post_id, 'wp_review_item', $new );
		} elseif ( empty( $new ) && $old ) {
			delete_post_meta( $post_id, 'wp_review_item', $old );
		}
	} else {
		delete_post_meta( $post_id, 'wp_review_item' );
	}
}


/**
 * Fix for post previews
 * With this code, the review meta data will actually get saved on Preview.
 *
 * @param array $fields Revision fields.
 * @return array
 */
function add_field_debug_preview( $fields ) {
	$fields['debug_preview'] = 'debug_preview';
	return $fields;
}
add_filter( '_wp_post_revision_fields', 'add_field_debug_preview' );


/**
 * Adds input debug preview.
 */
function add_input_debug_preview() {
	echo '<input type="hidden" name="debug_preview" value="debug_preview">';
}
add_action( 'edit_form_after_title', 'add_input_debug_preview' );


/**
 * Shows schema field.
 *
 * @param array  $args        Args.
 * @param array  $value       Schema value.
 * @param string $schema_type Schema type.
 */
function wp_review_schema_field( $args, $value, $schema_type ) {
	$type    = isset( $args['type'] ) ? $args['type'] : '';
	$name    = isset( $args['name'] ) ? $args['name'] : '';
	$label   = isset( $args['label'] ) ? $args['label'] : '';
	$options = isset( $args['options'] ) ? $args['options'] : array();
	$default = isset( $args['default'] ) ? $args['default'] : '';
	$min     = isset( $args['min'] ) ? $args['min'] : '0';
	$max     = isset( $args['max'] ) ? $args['max'] : '';
	$info    = isset( $args['info'] ) ? $args['info'] : '';

	// Option value.
	$opt_val       = isset( $value[ $name ] ) ? $value[ $name ] : $default;
	$opt_id_attr   = 'wp_review_schema_options_' . $schema_type . '_' . $name;
	$opt_name_attr = 'wp_review_schema_options[' . $schema_type . '][' . $name . ']';

	$form_field = new WP_Review_Form_Field();
	?>
	<div class="wp-review-field-label">
		<label for="<?php echo esc_attr( $opt_id_attr ); ?>" class="wp_review_schema_options_label"><?php echo esc_html( $label ); ?></label>
	</div>

	<div class="wp-review-field-option">
		<?php
		switch ( $type ) {
			case 'text':
				?>
				<input type="text" name="<?php echo esc_attr( $opt_name_attr ); ?>" id="<?php echo esc_attr( $opt_id_attr ); ?>" value="<?php echo esc_attr( $opt_val ); ?>" />
				<?php
				break;

			case 'select':
				?>
				<select name="<?php echo esc_attr( $opt_name_attr ); ?>" id="<?php echo esc_attr( $opt_id_attr ); ?>">
				<?php foreach ( $options as $val => $label ) { ?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $opt_val, $val, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
				<?php
				break;

			case 'number':
				?>
				<input type="number" step="1" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" name="<?php echo esc_attr( $opt_name_attr ); ?>" id="<?php echo esc_attr( $opt_id_attr ); ?>" value="<?php echo esc_attr( $opt_val ); ?>" class="small-text">
				<?php
				break;

			case 'textarea':
				?>
				<textarea name="<?php echo esc_attr( $opt_name_attr ); ?>" id="<?php echo esc_attr( $opt_id_attr ); ?>"><?php echo esc_textarea( $opt_val ); ?></textarea>
				<?php
				break;

			case 'checkbox':
				?>
				<input type="checkbox" name="<?php echo esc_attr( $opt_name_attr ); ?>" id="<?php echo esc_attr( $opt_id_attr ); ?>" value="1" <?php checked( $opt_val, '1', true ); ?> />
				<?php
				break;

			case 'image':
				?>
				<span class="wpr_image_upload_field">
					<span class="clearfix" id="<?php echo esc_attr( $opt_id_attr ); ?>-preview">
						<?php
						if ( ! empty( $opt_val['url'] ) ) {
							echo '<img class="wpr_image_upload_img" src="' . esc_url( $opt_val['url'] ) . '" />';
						}
						?>
					</span>
					<input type="hidden" id="<?php echo esc_attr( $opt_id_attr ); ?>-id" name="<?php echo esc_attr( $opt_name_attr ); ?>[id]" value="<?php if ( isset( $opt_val['id'] ) ) echo $opt_val['id']; // phpcs:ignore ?>" />
					<input type="hidden" id="<?php echo esc_attr( $opt_id_attr ); ?>-url" name="<?php echo esc_attr( $opt_name_attr ); ?>[url]" value="<?php if ( isset( $opt_val['url'] ) ) echo $opt_val['url']; // phpcs:ignore ?>" />
					<button class="button" name="<?php echo esc_attr( $opt_id_attr ); ?>-upload" id="<?php echo esc_attr( $opt_id_attr ); ?>-upload" data-id="<?php echo esc_attr( $opt_id_attr ); ?>" onclick="wprImageField.uploader( '<?php echo esc_attr( $opt_id_attr ); ?>' ); return false;"><?php esc_html_e( 'Select Image', 'wp-review' ); ?></button>
					<?php
					if ( ! empty( $opt_val['url'] ) ) {
						echo '<a href="#" class="button button-link clear-image">' . esc_html__( 'Remove Image', 'wp-review' ) . '</a>';
					}
					?>
					<span class="clear"></span>
				</span>
				<?php
				break;

			case 'date':
				?>
				<input class="wpr-datepicker" type="text" name="<?php echo esc_attr( $opt_name_attr ); ?>" id="<?php echo esc_attr( $opt_id_attr ); ?>" value="<?php echo esc_attr( $opt_val ); ?>" size="30" />
				<?php
				break;

			case 'switch':
				$field_args = array(
					'id'    => $opt_id_attr,
					'name'  => $opt_name_attr,
					'value' => $opt_val,
				);
				if ( ! empty( $args['on_label'] ) ) {
					$field_args['on_label'] = $args['on_label'];
				}
				if ( ! empty( $args['off_label'] ) ) {
					$field_args['off_label'] = $args['off_label'];
				}
				$form_field->render_switch( $field_args );
				break;
		}

		if ( ! empty( $info ) ) {
			printf( '<p class="description" style="color: #bbb">%s</p>', wp_kses_post( $info ) );
		}
		?>
	</div>
	<?php
}
