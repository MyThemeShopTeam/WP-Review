<?php
/**
 * Top rated reviews widget
 *
 * @package WP_Review
 *
 * @since 3.0.0
 */

/**
 * Class WP_Review_Toprated_Reviews_Widget
 */
class WP_Review_Toprated_Reviews_Widget extends WP_Review_Recent_Reviews_Widget {

	/**
	 * Query type.
	 *
	 * @var string
	 */
	protected $query_type = 'toprated';

	/**
	 * Gets widget class name.
	 *
	 * @return string
	 */
	protected function classname() {
		return 'widget_wp_review_toprated_reviews';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display top rated reviews.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_toprated_reviews';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Top Rated Reviews', 'wp-review' );
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		$defaults = parent::_get_defaults();
		$defaults['title'] = __( 'Top rated reviews', 'wp-review' );
		return $defaults;
	}
}
