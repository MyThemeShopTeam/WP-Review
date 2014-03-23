/*
List of available filters in WP Review plugin.
You can use these filterns in your theme in funtions.php file
and set different default settings.
*/

// Set colors for all displayed reviews
function new_review_colors($colors, $id) {
  $colors['bgcolor1'] = '#ff0000';
  return $colors;
}
add_filter( 'wp_review_colors', 'new_review_colors', 10, 2 );
 
// Set default colors for new reviews
function new_default_review_colors($colors) {
  $colors['bgcolor1'] = '#ff0000';
  return $colors;
}
add_filter( 'wp_review_default_colors', 'new_default_review_colors' );
 
// Set location for all displayed reviews
function new_review_location($position, $id) {
  $position = 'bottom';
  return $position;
}
add_filter( 'wp_review_location', 'new_review_location', 10, 2 );
 
// Set default location for new reviews
function new_default_review_location($position) {
  $position = 'bottom';
  return $position;
}
add_filter( 'wp_review_default_location', 'new_default_review_location' );
 
// Hide fields in "item" meta box
function hide_item_metabox_fields($fields) {
  unset($fields['location'], $fields['fontcolor'], $fields['bordercolor']);
  // Or remove all with:
  // $fields = array();
  return $fields;
}
add_filter( 'wp_review_metabox_item_fields', 'hide_item_metabox_fields' );
 
// Add default criteria
function add_default_criteria($items) {
  $items = array(__('Audio'), __('Visual'), __('UX'), __('Price'));
  return $items;
}
add_filter( 'wp_review_default_criteria', 'add_default_criteria' );