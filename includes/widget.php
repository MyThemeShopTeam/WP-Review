<?php
/**
 * Loads widgets.
 *
 * @package WP_Review
 */

/**
 * Registers widgets.
 */
function wpreview_register_widget() {
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-tab-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-recent-reviews-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-toprated-reviews-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-mostvoted-reviews-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-category-reviews-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-yelp-business-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-yelp-business-reviews-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-yelp-search-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-google-place-reviews-widget.php' );
	require_once( WP_REVIEW_INCLUDES . 'widgets/class-wp-review-facebook-reviews-widget.php' );

	register_widget( 'WP_Review_Tab_Widget' );
	register_widget( 'WP_Review_Recent_Reviews_Widget' );
	register_widget( 'WP_Review_Toprated_Reviews_Widget' );
	register_widget( 'WP_Review_Mostvoted_Reviews_Widget' );
	register_widget( 'WP_Review_Category_Reviews_Widget' );
	register_widget( 'WP_Review_Yelp_Business_Widget' );
	register_widget( 'WP_Review_Yelp_Business_Reviews_Widget' );
	register_widget( 'WP_Review_Yelp_Search_Widget' );
	register_widget( 'WP_Review_Google_Place_Reviews_Widget' );
	register_widget( 'WP_Review_Facebook_Reviews_Widget' );
}
add_action( 'widgets_init', 'wpreview_register_widget' );
