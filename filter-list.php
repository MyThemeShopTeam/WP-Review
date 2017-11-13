<?php 
/*
List of available filters in WP Review plugin.
You can use these filterns in your theme in funtions.php file
and set different default settings.
*/

// Set colors for selected or all reviews
function mts_new_review_colors($colors, $id) {
  $colors['bgcolor1'] = '#ff0000';
  return $colors;
}
add_filter( 'wp_review_colors', 'mts_new_review_colors', 10, 2 );
 
// Set location for selected or all reviews
function mts_new_review_location($position, $id) {
  $position = 'bottom';
  return $position;
}
add_filter( 'wp_review_location', 'mts_new_review_location', 10, 2 );

// Hide fields in "item" meta box
function mts_hide_item_metabox_fields($fields) {
  unset($fields['location'], $fields['fontcolor'], $fields['bordercolor']);
  // Or remove all (except features which can't be removed) with:
  // $fields = array();
  return $fields;
}
add_filter( 'wp_review_metabox_item_fields', 'mts_hide_item_metabox_fields' );

// Hide selected review types in metabox dropdown
function mts_hide_review_types($types) {
  unset($types['point'], $types['percentage']); // remove types
  $types['star'] = __('Enable Reviews'); // Change label
  return $types;
}
add_filter( 'wp_review_metabox_types', 'mts_hide_review_types' );
 
// Add default criteria
function mts_add_default_criteria($items) {
  $items = array(__('Audio'), __('Visual'), __('UX'), __('Price'));
  return $items;
}
add_filter( 'wp_review_default_criteria', 'mts_add_default_criteria' );

// Customize wp_review_show_total() output
function mts_custom_review_total($content, $id, $type, $total) {
  if (get_the_title($id) == 'Special Post With Blue Rating') {
    $color = '#0000FF';
    $content = preg_replace('/"review-type-[^"]+"/', '$0 style="background-color: '.$color.';"', $content);
  }
return $content;
}
add_filter('wp_review_show_total', 'mts_custom_review_total', 10, 4);

// Exclude post types
function mts_wp_review_exclude_post_types($excluded) {
  // default: $excluded = array('attachment');
  $excluded[] = 'page'; // Don't allow reviews on pages
  return $excluded;
}
add_filter( 'wp_review_excluded_post_types', 'mts_wp_review_exclude_post_types' );

// Set the review options in your theme
// These will be set as the global options for the plugin upon theme activation
$new_options = array(
  'colors' => array(
    'color' => '#dd3333',
    'fontcolor' => '#555555',
    'bgcolor1' => '#e7e7e7',
    'bgcolor2' => '#ffffff',
    'bordercolor' => '#e7e7e7'
  )
);
if ( function_exists( 'wp_review_theme_defaults' )) wp_review_theme_defaults( $new_options );

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
add_filter( 'wp_review_get_box_template', 'mts_wp_review_select_box_template', 10, 2 );
function mts_wp_review_select_box_template( $template, $post_id ) {
  // Change box template for specific post
  if ( $post_id == '128' ) {
    $template = 'new-box.php'; 
    // "new-box.php" must be present in one of the template path folders (see below)
  }
  return $template;
}

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
add_filter( 'wp_review_box_template_paths', 'mts_wp_review_add_template_path', 10, 1 );
function mts_wp_review_add_template_path( $paths  ) {
  // Add a new path where we look for review box template files
  // The $paths holds default paths in reversed 
  $paths[] = '/absolute/path/to/additional/templates/dir';
  return $paths;
}

/**
 * Add new rating types with wp_review_register_rating_type()
 * 
 * Refer to existing rating template files, e.g. 
 * point-output.php, point-input.php
 */
add_action( 'init', 'wp_review_register_additional_rating_types' );
function wp_review_register_additional_rating_types() {
  wp_review_register_rating_type( 'star10', array(
    'label' => __('10 Stars', 'wp-review'),
    'max' => 10,
    'decimals' => 1,
    'value_text' => __('%s Stars', 'wp-review'),
    'value_text_singular' => __('%s Star', 'wp-review'),
    'input_template' => WP_REVIEW_DIR . 'rating-types/star10-input.php', // Replace with path to input template
    'output_template' => WP_REVIEW_DIR . 'rating-types/star10-output.php', // Replace with path to output template
  ) );
}