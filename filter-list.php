<?php
/*
List of available filters in WP Review plugin.
You can use these filterns in your theme in funtions.php file
and set different default settings.
*/

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
	unset( $types['point'] );
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
 * Replace `aqua` with template name
 * Eg: wp_review_box_template_dash_style.
 *
 * @param  string $output    CSS output, includes `<style` tag.
 * @param  int    $review_id Review ID.
 * @param  array  $colors    Review colors data.
 * @return string
 */
function mts_wp_review_box_template_aqua_style( $output, $review_id, $colors ) {
	$css = ".wp-review-{$review_id} { color: {$colors['color']}; }";
	// $output = str_replace( '<style type="text/css">', '<style type="text/css">' . $css, $output ); // Add to the top.
	$output = str_replace( '</style>', $css . '</style>', $output ); // Add to the bottom.
	return $output;
}
apply_filters( 'wp_review_box_template_aqua_style', 'mts_wp_review_box_template_aqua_style', 10, 3 );

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
 * and make a copy there of /wp-review/box-templates/default.php
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
 * 1. wp-review/box-templates
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

printf( '<script type="application/ld+json">%s</script>', wp_json_encode( $markup ) );

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
