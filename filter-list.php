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

// Remove banner from options page
add_filter( 'wp_review_remove_branding', '__return_true' );

?>