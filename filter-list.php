<?php
/*
List of available filters in WP Review plugin.
You can use these filterns in your theme in funtions.php file
and set different default settings.
*/

/**
 * Filters list of icons.
 * Remember to enqueue your font and css files.
 *
 * @param  array $icons List of icons.
 * @return array
 */
function mts_wp_review_icons( $icons ) {
	$icons['icon-css-class'] = array(
		'unicode' => '\f26e',
		'name' => 'Icon title',
	);

	return $icons;
}
add_filter( 'wp_review_icons', 'mts_wp_review_icons' );

/**
 * Changes number of posts to import per request.
 *
 * @param  int $numposts Number of posts.
 * @return int
 */
function mts_wp_review_import_numposts( $numposts ) {
	return 20;
}
add_filter( 'wp_review_import_numposts', 'mts_wp_review_import_numposts' );

/**
 * Excludes post types from review.
 *
 * @param  array $excluded List of post types.
 * @return array
 */
function mts_wp_review_exclude_post_types( $excluded ) {
	// default: $excluded = array('attachment');
	$excluded[] = 'page'; // Don't allow reviews on pages.
	return $excluded;
}
add_filter( 'wp_review_excluded_post_types', 'mts_wp_review_exclude_post_types' );

/**
 * Hides fields in meta box.
 *
 * @param  array $fields List of displayed fields.
 * @return array
 */
function mts_wp_review_hide_item_metabox_fields( $fields ) {
	unset( $fields['location'], $fields['fontcolor'], $fields['bordercolor'] );
	// Or remove all (except features which can't be removed) with:
	// $fields = array();
	return $fields;
}
add_filter( 'wp_review_metabox_item_fields', 'mts_wp_review_hide_item_metabox_fields' );

/**
 * Hides rating types.
 *
 * @param  array $types List of available types.
 * @return array
 */
function mts_wp_review_hide_rating_types( $types ) {
	unset( $types['thumbs'] );
	return $types;
}
add_filter( 'wp_review_rating_types', 'mts_wp_review_hide_rating_types' );

/**
 * Set colors for selected or all reviews.
 *
 * @param  array $colors Color data.
 * @param  int   $id     Post ID.
 * @return array
 */
function mts_wp_review_new_review_colors($colors, $id) {
	$colors['bgcolor1'] = '#ff0000';
	return $colors;
}
add_filter( 'wp_review_colors', 'mts_wp_review_new_review_colors', 10, 2 );

/**
 * Filters default rating icon.
 *
 * @param  string $icon Rating icon.
 * @return string
 */
function mts_wp_review_default_rating_icon( $icon ) {
	$icon = 'fa fa-thumbs-up';
	return $icon;
}
add_filter( 'wp_review_default_rating_icon', 'mts_wp_review_default_rating_icon' );

/**
 * Sets location for selected or all reviews.
 *
 * @param  string $location Review location.
 * @param  int    $id       Post ID.
 * @return string
 */
function mts_new_review_location( $location, $id ) {
	$location = 'bottom';
	return $location;
}
add_filter( 'wp_review_location', 'mts_new_review_location', 10, 2 );

/**
 * Shows post title as review heading when the review heading field is empty.
 */
function mts_wp_review_item_title_fallback( $title ) {
	return '<h5 class="review-title">' . get_the_title() . '</h5>';
}
add_filter( 'wp_review_item_title_fallback', 'mts_wp_review_item_title_fallback' );

/**
 * Filters review description.
 *
 * @param  string $desc Review description.
 * @return string
 */
function mts_wp_review_desc( $desc, $review_id = '' ) {
	$desc .= '<br>Thank you';
	return $desc;
}
add_filter( 'wp_review_desc', 'mts_wp_review_desc', 10, 2 );


/**
 * Filters review pros content.
 *
 * @param  string $pros Review pros.
 * @return string
 */
function mts_wp_review_pros( $pros, $review_id = '' ) {
	$pros .= '<br>Lorem ipsum';
	return $pros;
}
add_filter( 'wp_review_pros', 'mts_wp_review_pros', 10, 2 );


/**
 * Filters review cons content.
 *
 * @param  string $cons Review cons.
 * @return string
 */
function mts_wp_review_cons( $cons, $review_id = '' ) {
	$cons .= '<br>Lorem ipsum';
	return $cons;
}
add_filter( 'wp_review_cons', 'mts_wp_review_cons', 10, 2 );

/**
 * Changes color css output for all templates.
 *
 * @param  string $output    CSS output, includes `<style` tag.
 * @param  int    $review_id Review ID.
 * @param  array  $colors    Review colors data.
 * @return string
 */
function mts_wp_review_color_output( $output, $review_id, $colors ) {
	$css = ".wp-review-{$review_id} { color: {$colors['color']}; }";
	// $output = str_replace( '<style type="text/css">', '<style type="text/css">' . $css, $output ); // Add to the top.
	$output = str_replace( '</style>', $css . '</style>', $output ); // Add to the bottom.
	return $output;
}
apply_filters( 'wp_review_color_output', 'mts_wp_review_color_output', 10, 3 );

/**
 * Changes color css output for a specific template
 * Replace `amazon` with template name
 * Eg: wp_review_box_template_dash_style.
 *
 * @param  string $output    CSS output, includes `<style` tag.
 * @param  int    $review_id Review ID.
 * @param  array  $colors    Review colors data.
 * @return string
 */
function mts_wp_review_box_template_amazon_style( $output, $review_id, $colors ) {
	$css = ".wp-review-{$review_id} { color: {$colors['color']}; }";
	// $output = str_replace( '<style type="text/css">', '<style type="text/css">' . $css, $output ); // Add to the top.
	$output = str_replace( '</style>', $css . '</style>', $output ); // Add to the bottom.
	return $output;
}
apply_filters( 'wp_review_box_template_amazon_style', 'mts_wp_review_box_template_amazon_style', 10, 3 );

/**
 * Changes review item image size.
 *
 * @param  string $size Image size.
 * @return string
 */
function mts_wp_review_item_reviewed_image_size( $size ) {
	return 'thumbnail';
}
add_filter( 'wp_review_item_reviewed_image_size', 'mts_wp_review_item_reviewed_image_size' );

// Hides review comments from comments list.
add_filter( 'wp_review_to_comment_type_list', '__return_false' );

/**
 * Filters value of an option
 * Hook: wp_review_option_{$option_name}.
 *
 * @param  mixed $value Option value.
 * @return mixed
 */
function mts_wp_review_option_rating_icon( $value ) {
	$value = 'fa fa-thumbs-up';
	return $value;
}
add_filter( 'wp_review_option_rating_icon', 'mts_wp_review_option_rating_icon' );

/**
 * Filters value of any options
 *
 * @param  mixed  $value       Option value.
 * @param  string $option_name Option name.
 * @return mixed
 */
function mts_wp_review_option( $value, $option_name ) {
	if ( 'rating_icon' == $option_name ) {
		$value = 'fa fa-thumbs-up';
	}
	return $value;
}
add_filter( 'wp_review_option', 'mts_wp_review_option', 10, 2 );

/**
 * Filters default review colors.
 *
 * @param  array $colors Review colors.
 * @return array
 */
function mts_wp_review_default_colors( $colors ) {
	$colors = array(
		'color' => '#1e73be',
		'inactive_color' => '',
		'fontcolor' => '#555555',
		'bgcolor1' => '#e7e7e7',
		'bgcolor2' => '#ffffff',
		'bordercolor' => '#e7e7e7',
	);

	return $colors;
}
add_filter( 'wp_review_default_colors', 'mts_wp_review_default_colors' );

/**
 * Filters default location of review.
 *
 * @param  string $location Review location.
 * @return string
 */
function mts_wp_review_default_location( $location ) {
	$location = 'top'; // accepts 'top', 'bottom', 'custom'. Default is 'bottom'.

	return $location;
}
add_filter( 'wp_review_default_location', 'mts_wp_review_default_location' );

/**
 * Adds default items.
 *
 * @param  array $items List of default items.
 * @return array
 */
function mts_add_default_items( $items ) {
	$items = array(
		__( 'Audio', 'theme-slug' ),
		__( 'Visual', 'theme-slug' ),
		__( 'UX', 'theme-slug' ),
		__( 'Price', 'theme-slug' ),
	);
	return $items;
}
add_filter( 'wp_review_default_criteria', 'mts_add_default_items' );

/**
 * Shows/hides total review on thumbnail for all or specific post.
 *
 * @param bool  $show    Show or hide.
 * @param int   $post_id Post ID.
 * @param array $args    Custom arguments.
 * @return bool
 */
function mts_wp_review_thumbnails_total( $show, $post_id, $args ) {
	return false;
}
add_filter( 'wp_review_thumbnails_total', 'mts_wp_review_thumbnails_total', 10, 3 );

/**
 * Customizes wp_review_show_total() output.
 *
 * @param  string $output The output.
 * @param  int    $id     Post ID.
 * @param  string $type   Rating type.
 * @param  float  $total  Total value.
 * @return string
 */
function mts_wp_review_custom_review_total( $output, $id, $type, $total ) {
	if ( get_the_title( $id ) == 'Special Post With Blue Rating' ) {
		$color = '#0000FF';
		$output = preg_replace( '/"review-type-[^"]+"/', '$0 style="background-color: ' . $color . ';"', $output );
	}
	return $output;
}
add_filter( 'wp_review_show_total', 'mts_wp_review_custom_review_total', 10, 4 );

/**
 * Filters review total output.
 *
 * @param  string $review  Review total output.
 * @param  int    $post_id Post ID.
 * @param  string $type    Review type.
 * @param  float  $total   Review total value.
 * @param  string $class   CSS class.
 * @param  array  $args    Custom arguments.
 * @return string
 */
function mts_wp_review_total_output( $review, $post_id, $type, $total, $class, $args ) {
	$review = '<p>Total:</p>' . $review;
	return $review;
}
add_filter( 'wp_review_total_output', 'mts_wp_review_total_output', 10, 6 );

/**
 * Filters post review type.
 *
 * @param  string $type    Review type.
 * @param  int    $post_id Post ID.
 * @return string
 */
function mts_wp_review_get_review_type( $type, $post_id ) {
	$type = 'star'; // Force using star.
	return $type;
}
add_filter( 'wp_review_get_review_type', 'mts_wp_review_get_review_type', 10, 2 );

/**
 * Filters user review type.
 *
 * @param  string $type    Review type.
 * @param  int    $post_id Post ID.
 * @return string
 */
function mts_wp_review_get_user_review_type( $type, $post_id ) {
	$type = 'star'; // Force using star.
	return $type;
}
add_filter( 'wp_review_get_user_review_type', 'mts_wp_review_get_user_review_type', 10, 2 );

/**
 * Editing/overriding the review box template
 *
 * Create a 'wp-review' directory in your (child) theme folder,
 * and make a copy there of /wp-review-pro/box-templates/default.php
 * to override it.
 *
 * Use different file name to add new template, which can be applied using filter:
 *
 */
function mts_wp_review_select_box_template( $template, $post_id ) {
	// Change box template for specific post
	if ( $post_id == '128' ) {
		$template = 'new-box.php';
		// "new-box.php" must be present in one of the template path folders (see below)
	}
	return $template;
}
add_filter( 'wp_review_get_box_template', 'mts_wp_review_select_box_template', 10, 2 );

/**
 * Template Path Directories
 *
 * By default the plugin looks for box templates in:
 * 1. wp-review-pro/box-templates
 * 2. theme_dir/wp-review
 * 3. childtheme_dir/wp-review
 * 4... Use filter to add more
 *
 */
function mts_wp_review_add_template_path( $paths ) {
	// Add a new path where we look for review box template files
	// The $paths holds default paths in reversed
	$paths[] = '/absolute/path/to/additional/templates/dir';
	return $paths;
}
add_filter( 'wp_review_box_template_paths', 'mts_wp_review_add_template_path' );

/**
 * Filters review data. This data is passed to template.
 *
 * @param  array $data Review data.
 * @param  array $args Custom arguments.
 * @return array
 */
function mts_wp_review_get_review_data( $data, $args ) {
	$data['type'] = 'circle';
	return $data;
}
add_filter( 'wp_review_get_review_data', 'mts_wp_review_get_review_data', 10, 2 );

/**
 * Filters review box output.
 *
 * @param  string $review  Review box output.
 * @param  int    $post_id Post ID.
 * @param  string $type    Review type.
 * @param  float  $total   Review total.
 * @param  array  $items   Review items.
 * @return string
 */
function mts_wp_review_get_data( $review, $post_id, $type, $total, $items ) {
	$review .= '<p>Custom content</p>';
	return $review;
}
add_filter( 'wp_review_get_data', 'mts_wp_review_get_data', 10, 5 );

/**
 * Changes schema nesting mode.
 *
 * @param  string $type Nesting mode. Accepts `type`, `rating` or `none`.
 * @return string
 */
function mts_wp_review_schema_nesting_mode( $type ) {
	return 'rating';
}
add_filter( 'wp_review_schema_nesting_mode', 'mts_wp_review_schema_nesting_mode' );

/**
 * Forces schema nesting type to rating for some schema types
 * Apply for author reviews.
 *
 * @param  array $types Schema types.
 * @return array
 */
function mts_wp_review_schema_force_nested_rating_types( $types ) {
	$types[] = 'Recipe';
	return $types;
}
add_filter( 'wp_review_schema_force_nested_rating_types', 'mts_wp_review_schema_force_nested_rating_types' );

/**
 * Forces schema nesting type to rating for some schema types
 * Apply for visitor comments.
 *
 * @param  array $types Schema types.
 * @return array
 */
function mts_wp_review_schema_force_nested_user_rating_types( $types ) {
	$types[] = 'Movie';
	return $types;
}
add_filter( 'wp_review_schema_force_nested_user_rating_types', 'mts_wp_review_schema_force_nested_user_rating_types' );

/**
 * Changes review schema output.
 *
 * @param  string $output Schema output.
 * @param  array  $review Review data.
 * @return string
 */
function mts_wp_review_get_schema( $output, $review ) {
	// Modify the $output.
	return $output;
}
add_filter( 'wp_review_get_schema', 'mts_wp_review_get_schema', 10, 2 );

/**
 * Filters schema ISO-8601 duration items.
 *
 * @param  array $items List of items.
 * @return array
 */
function mts_wp_reviev_schema_ISO_8601_duration_items( $items ) {
	unset( $items['duration'] );
	return $items;
}
add_filter( 'wp_reviev_schema_ISO_8601_duration_items', 'mts_wp_reviev_schema_ISO_8601_duration_items' );

/**
 * Changes arguments of a schema type.
 *
 * @param  array $args          Arguments.
 * @param  array $review        Review data.
 * @param  bool  $nested_rating Nested rating or not.
 * @return array
 */
function mts_wp_review_get_schema_type_args( $args, $review, $nested_rating ) {
	return $args;
}
add_filter( 'wp_review_get_schema_type_args', 'mts_wp_review_get_schema_type_args', 10, 3 );

/**
 * Changes output of a schema type.
 *
 * @param  string $output        Schema output.
 * @param  array  $args          Arguments.
 * @param  array  $review        Review data.
 * @param  bool   $nested_rating Nested rating or not.
 * @return string
 */
function mts_wp_review_get_schema_type( $output, $args, $review, $nested_rating ) {
	return $output;
}
add_filter( 'wp_review_get_schema_type', 'mts_wp_review_get_schema_type', 10, 4 );

/**
 * Changes arguments of a schema type for rating.
 *
 * @param  array $args   Arguments.
 * @param  array $review Review data.
 * @return array
 */
function mts_wp_review_get_schema_review_rating_args( $args, $review ) {
	return $args;
}
add_filter( 'wp_review_get_schema_review_rating_args', 'mts_wp_review_get_schema_review_rating_args', 10, 2 );

/**
 * Changes arguments of a schema type for nested review.
 *
 * @param  array $args   Arguments.
 * @param  array $review Review data.
 * @return array
 */
function mts_wp_review_get_schema_nested_review_args( $args, $review ) {
	return $args;
}
add_filter( 'wp_review_get_schema_nested_review_args', 'mts_wp_review_get_schema_nested_review_args', 10, 2 );

/**
 * Changes arguments of a schema type for nested item.
 *
 * @param  array $args   Arguments.
 * @param  array $review Review data.
 * @return array
 */
function mts_wp_review_get_schema_nested_item_args( $args, $review ) {
	return $args;
}
add_filter( 'wp_review_get_schema_nested_item_args', 'mts_wp_review_get_schema_nested_item_args', 10, 2 );

/**
 * Changes output of a schema type for rating.
 *
 * @param  string $output Schema output.
 * @param  array  $args   Arguments.
 * @param  array  $review Review data.
 * @return string
 */
function mts_wp_review_get_schema_review_rating( $output, $args, $review ) {
	return $output;
}
add_filter( 'wp_review_get_schema_review_rating', 'mts_wp_review_get_schema_review_rating', 10, 3 );

/**
 * Changes arguments of a schema type for user rating.
 *
 * @param  array $args   Arguments.
 * @param  array $review Review data.
 * @return array
 */
function mts_wp_review_get_schema_user_rating_args( $args, $review ) {
	return $args;
}
add_filter( 'wp_review_get_schema_user_rating_args', 'mts_wp_review_get_schema_user_rating_args', 10, 2 );

/**
 * Changes arguments of a schema type for nested user rating.
 *
 * @param  array $args   Arguments.
 * @param  array $review Review data.
 * @return array
 */
function mts_wp_review_get_schema_nested_user_rating_args( $args, $review ) {
	return $args;
}
add_filter( 'wp_review_get_schema_nested_user_rating_args', 'mts_wp_review_get_schema_nested_user_rating_args', 10, 2 );

/**
 * Changes output of a schema type for user rating.
 *
 * @param  string $output Schema output.
 * @param  array  $args   Arguments.
 * @param  array  $review Review data.
 * @return string
 */
function mts_wp_review_get_schema_user_rating( $output, $args, $review ) {
	return $output;
}
add_filter( 'wp_review_get_schema_user_rating', 'mts_wp_review_get_schema_user_rating', 10, 3 );

/**
 * Changes reviewed item name.
 *
 * @param  string $item_name Item name.
 * @param  array  $review    Review data.
 * @return string
 */
function mts_wp_review_get_reviewed_item_name( $item_name, $review ) {
	// Modify the $item_name.
	return $item_name;
}
add_filter( 'wp_review_get_reviewed_item_name', 'mts_wp_review_get_reviewed_item_name', 10, 2 );

/**
 * Changes transient expired time.
 *
 * @param  int $time Transient expired time.
 * @return int
 */
function mts_wp_review_transient_expired_time( $time ) {
	$time = MONTH_IN_SECONDS;
	return $time;
}
add_filter( 'wp_review_transient_expired_time', 'mts_wp_review_transient_expired_time' );

/**
 * Filters reviews query args.
 *
 * @param array $query_args Query args.
 * @param array $options    Query options.
 * @return array
 */
function mts_wp_review_reviews_query_args( $query_args, $options ) {
	$query_args['post_type'] = 'post'; // Only show post reviews.
	return $query_args;
}
add_filter( 'wp_review_reviews_query_args', 'mts_wp_review_reviews_query_args' );

/**
 * Add other Thing schema types.
 *
 * @param array $schemas Schema types.
 * @return array
 *
 * @link https://schema.org/docs/full.html See types under Thing
 */
function mts_wp_review_add_custom_schema_type( $schemas ) {
	$schemas['VideoGame'] = 'Video Game';
	return $schemas;
}
add_filter( 'wp_review_schema_types', 'mts_wp_review_add_custom_schema_type' );

/**
 * Hide selected review types in metabox dropdown.
 *
 * @deprecated 3.0.0 Now use `wp_review_rating_types` filter.
 *
 * @param  array $types List of rating types.
 * @return array
 */
function mts_hide_review_types( $types ) {
	unset( $types['point'], $types['percentage'] ); // remove types.
	$types['star'] = __( 'Enable Reviews' ); // Change label.
	return $types;
}
add_filter( 'wp_review_metabox_types', 'mts_hide_review_types' );

/**
 * Add new rating types with wp_review_register_rating_type()
 *
 * Refer to existing rating template files, e.g.
 * point-output.php, point-input.php
 *
 * This is an alternative way to add new rating type instead of using `wp_review_rating_types` hook.
 */
function wp_review_register_additional_rating_types() {
	wp_review_register_rating_type( 'star10', array(
		'label' => __( '10 Stars', 'wp-review' ),
		'max' => 10,
		'decimals' => 1,
		'value_text' => __( '%s Stars', 'wp-review' ),
		'value_text_singular' => __( '%s Star', 'wp-review' ),
		'input_template' => WP_REVIEW_DIR . 'rating-types/star10-input.php', // Replace with path to input template
		'output_template' => WP_REVIEW_DIR . 'rating-types/star10-output.php', // Replace with path to output template
	) );
}
add_action( 'init', 'wp_review_register_additional_rating_types' );

/**
 * Adds new box template.
 *
 * @param  array $templates Review box templates.
 * @return array
 */
function mts_wp_review_box_templates( $templates ) {
	$templates['custom-template'] = array(
		'title'                 => __( 'Custom template', 'wp-review' ),
		'image'                 => WP_REVIEW_ASSETS . 'images/largethumb.png',
		'color'                 => '#1e73be',
		'fontcolor'             => '#555',
		'bgcolor1'              => '#e7e7e7',
		'bgcolor2'              => '#fff',
		'bordercolor'           => '#e7e7e7',
		'width'                 => 100, // In percentage.
		'align'                 => 'left',
		'custom_comment_colors' => 0,
		'comment_color'         => '#ffb300',
		'rating_icon'           => 'fa fa-star',
	);
	return $templates;
}
add_filter( 'wp_review_box_templates', 'mts_wp_review_box_templates' );


/**
 * Filters value of a notification bar option
 * Hook: wp_review_hello_bar_option_{$option_name}.
 *
 * @param  mixed $value Option value.
 * @return mixed
 */
function mts_wp_review_hello_bar_option_enable( $value ) {
	$value = 'fa fa-thumbs-up';
	return $value;
}
add_filter( 'wp_review_hello_bar_option_enable', 'mts_wp_review_hello_bar_option_enable' );


/**
 * Filters value of any notification bar options
 *
 * @param  mixed  $value       Option value.
 * @param  string $option_name Option name.
 * @return mixed
 */
function mts_wp_review_hello_bar_option( $value, $option_name ) {
	if ( 'enable' == $option_name ) {
		$value = true;
	}
	return $value;
}
add_filter( 'wp_review_hello_bar_option', 'mts_wp_review_hello_bar_option', 10, 2 );


/**
 * Changes notification bar default options.
 *
 * @param  array $defaults Default options.
 * @return array
 */
function mts_wp_review_hello_bar_defaults( $defaults ) {
	$defaults['button_label'] = 'Read more';
	return $defaults;
}
add_filter( 'wp_review_hello_bar_defaults', 'mts_wp_review_hello_bar_defaults' );


/**
 * Changes notification bar config of a post.
 *
 * @param  array $config  Notification bar config.
 * @param  int   $post_id Post id.
 * @return array
 */
function mts_wp_review_post_hello_bar_config( $config, $post_id ) {
	$config['button_label'] = 'Read more';
	return $config;
}
add_filter( 'wp_review_post_hello_bar_config', 'mts_wp_review_post_hello_bar_config', 10, 2 );


/**
 * Changes notification bar config of any pages.
 *
 * @param  array $config Notification bar config.
 * @return array
 */
function mts_wp_review_get_hello_bar_config( $config ) {
	$config['button_label'] = 'Read more';
	return $config;
}
add_filter( 'wp_review_get_hello_bar_config', 'mts_wp_review_get_hello_bar_config' );


/**
 * Changes notification bar output.
 *
 * @param  string $output Notification bar output.
 * @param  array  $config Notification bar config.
 * @return string
 */
function mts_wp_review_hello_bar( $output, $config ) {
	// Modify the output.
	return $output;
}
add_filter( 'wp_review_hello_bar', 'mts_wp_review_hello_bar', 10, 2 );


/**
 * Filters value of a popup option
 * Hook: wp_review_popup_option_{$option_name}.
 *
 * @param  mixed $value Option value.
 * @return mixed
 */
function mts_wp_review_popup_option_enable( $value ) {
	$value = 'fa fa-thumbs-up';
	return $value;
}
add_filter( 'wp_review_popup_option_enable', 'mts_wp_review_popup_option_enable' );


/**
 * Filters value of any popup options
 *
 * @param  mixed  $value       Option value.
 * @param  string $option_name Option name.
 * @return mixed
 */
function mts_wp_review_popup_option( $value, $option_name ) {
	if ( 'enable' == $option_name ) {
		$value = true;
	}
	return $value;
}
add_filter( 'wp_review_popup_option', 'mts_wp_review_popup_option', 10, 2 );


/**
 * Changes popup default options.
 *
 * @param  array $defaults Default options.
 * @return array
 */
function mts_wp_review_popup_defaults( $defaults ) {
	$defaults['width'] = '1000px';
	return $defaults;
}
add_filter( 'wp_review_popup_defaults', 'mts_wp_review_popup_defaults' );


/**
 * Changes popup config of a post.
 *
 * @param  array $config  Popup config.
 * @param  int   $post_id Post id.
 * @return array
 */
function mts_wp_review_post_popup_config( $config, $post_id ) {
	$config['button_label'] = 'Read more';
	return $config;
}
add_filter( 'wp_review_post_popup_config', 'mts_wp_review_post_popup_config', 10, 2 );


/**
 * Changes popup config of any pages.
 *
 * @param  array $config Popup config.
 * @return array
 */
function mts_wp_review_get_popup_config( $config ) {
	$config['button_label'] = 'Read more';
	return $config;
}
add_filter( 'wp_review_get_popup_config', 'mts_wp_review_get_popup_config' );


/**
 * Filters popup query args.
 *
 * @param array $query_args Popup query args.
 * @param array $config     Popup config.
 * @return array
 */
function mts_wp_review_popup_query_args( $query_args, $config ) {
	$query_args['post_type'] = 'post';
	return $query_args;
}
add_filter( 'wp_review_popup_query_args', 'mts_wp_review_popup_query_args', 10, 2 );


/**
 * Changes popup output.
 *
 * @param  string $output Popup output.
 * @param  array  $config Popup config.
 * @return string
 */
function mts_wp_review_popup( $output, $config ) {
	// Modify the output.
	return $output;
}
add_filter( 'wp_review_popup', 'mts_wp_review_popup', 10, 2 );


/**
 * Changes the output of [wp-review] shortcode.
 *
 * @param  string $output Shortcode output.
 * @param  array  $atts   Shortcode attributes.
 * @return string
 */
function mts_wp_review_shortcode( $output, $atts ) {
	// Modify the output.
	return $output;
}
add_filter( 'wp_review_shortcode', 'mts_wp_review_shortcode', 10, 2 );


/**
 * Changes the output of [wp-review-total] shortcode.
 *
 * @param  string $output Shortcode output.
 * @param  array  $atts   Shortcode attributes.
 * @return string
 */
function mts_wp_review_total_shortcode( $output, $atts ) {
	// Modify the output.
	return $output;
}
add_filter( 'wp_review_total_shortcode', 'mts_wp_review_total_shortcode', 10, 2 );


/**
 * Changes the output of [wp-review-visitor-rating] shortcode.
 *
 * @param  string $output Shortcode output.
 * @param  array  $atts   Shortcode attributes.
 * @return string
 */
function mts_wp_review_visitor_rating_shortcode( $output, $atts ) {
	// Modify the output.
	return $output;
}
add_filter( 'wp_review_visitor_rating_shortcode', 'mts_wp_review_visitor_rating_shortcode', 10, 2 );


/**
 * Changes the output of [wp-review-comments-rating] shortcode.
 *
 * @param  string $output Shortcode output.
 * @param  array  $atts   Shortcode attributes.
 * @return string
 */
function mts_wp_review_comments_rating_shortcode( $output, $atts ) {
	// Modify the output.
	return $output;
}
add_filter( 'wp_review_comments_rating_shortcode', 'mts_wp_review_comments_rating_shortcode', 10, 2 );


/**
 * Changes the default length of review title in WP Review Widget.
 *
 * @param  int $length Title length.
 * @return int
 */
function mts_wpt_title_length_default( $length ) {
	$length = 10;
	return $length;
}
add_filter( 'wpt_title_length_default', 'mts_wpt_title_length_default' );

/**
 * Allow changing schema markup for Google place review.
 *
 * @since 3.0.4
 *
 * @param array $markup Schema markup.
 * @param array $review Review data.
 * @param array $place  Place data.
 */
function mts_wp_review_google_place_review_schema_markup( $markup, $review, $page ) {
	// Change markup here.
	return $markup;
}
add_filter( 'wp_review_google_place_review_schema_markup', 'mts_wp_review_google_place_review_schema_markup', 10, 3 );

/**
 * Allow changing schema markup for Yelp review.
 *
 * @since 3.0.4
 *
 * @param array $markup   Schema markup.
 * @param array $review   Review data.
 * @param array $business Business data.
 */
function mts_wp_review_yelp_review_schema_markup( $markup, $review, $business ) {
	// Change markup here.
	return $markup;
}
add_filter( 'wp_review_yelp_review_schema_markup', 'mts_wp_review_yelp_review_schema_markup', 10, 3 );

/**
 * Allow changing schema markup for Facebook page.
 *
 * @since 3.0.4
 *
 * @param array $markup Schema markup.
 * @param array $page   Page data.
 */
function mts_wp_review_facebook_page_schema_markup( $markup, $page ) {
	// Change markup here.
	return $markup;
}
add_filter( 'wp_review_facebook_page_schema_markup', 'mts_wp_review_facebook_page_schema_markup', 10, 2 );

/**
 * Allow changing schema markup for Facebook page review.
 *
 * @since 3.0.4
 *
 * @param array $markup Schema markup.
 * @param array $review Review data.
 * @param array $page   Page data.
 */
function mts_wp_review_facebook_page_review_schema_markup( $markup, $review, $page ) {
	// Change markup here.
	return $markup;
}
add_filter( 'wp_review_facebook_page_review_schema_markup', 'mts_wp_review_facebook_page_review_schema_markup', 10, 3 );

printf( '<script type="application/ld+json">%s</script>', wp_json_encode( $markup ) );

/**
 * Allow changing schema markup for Yelp.
 *
 * @since 3.0.4
 *
 * @param array $markup   Schema markup.
 * @param array $business Business data.
 */
function mts_wp_review_yelp_schema_markup( $markup, $business ) {
	// Change markup here.
	return $markup;
}
add_filter( 'wp_review_yelp_schema_markup', 'mts_wp_review_yelp_schema_markup', 10, 2 );

// Set the review options in your theme
// These will be set as the global options for the plugin upon theme activation
$new_options = array(
	'colors' => array(
		'color' => '#dd3333',
		'inactive_color' => '#dd3333',
		'fontcolor' => '#555555',
		'bgcolor1' => '#e7e7e7',
		'bgcolor2' => '#ffffff',
		'bordercolor' => '#e7e7e7',
	),
);
if ( function_exists( 'wp_review_theme_defaults' ) ) {
	wp_review_theme_defaults( $new_options );
}