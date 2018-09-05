<?php
/**
 * Shortcode [wp-review-posts]
 *
 * @package WP_Review
 */

class WP_Review_Posts_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @var string
	 */
	protected $name = 'wp-review-posts';

	/**
	 * Shortcode alias.
	 *
	 * @var string
	 */
	protected $alias = 'wp_review_posts';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( $this->name, array( $this, 'render' ) );
		add_shortcode( $this->alias, array( $this, 'render' ) );
	}

	/**
	 * Renders shortcode.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = shortcode_atts( array(
			'type'             => 'recent',
			// 'post_type'        => 'post',
			'review_type'      => '',
			'cat'              => '',
			'allow_pagination' => '',
			'post_num'         => 5,
			'title_length'     => '',
			'thumb_size'       => '',
			'show_date'        => '',
			'number_of_days'   => '',
		), $atts, $this->name );

		$options = $this->get_options( $atts );

		wp_review_enqueue_rating_type_scripts( 'output', $options['review_type'] );

		return sprintf(
			'<div class="js-reviews-placeholder wp-reviews-list" data-options="%s"></div>',
			esc_attr( wp_json_encode( $options ) )
		);
	}

	/**
	 * Gets js options.
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return array
	 */
	protected function get_options( $atts ) {
		$atts['_type'] = $atts['type'];
		$atts['review_type'] = $atts['review_type'] ? array_map( 'trim', explode( ',', $atts['review_type'] ) ) : array();
		$atts['cat'] = $atts['cat'] ? array_map( 'trim', explode( ',', $atts['cat'] ) ) : array();
		if ( 'false' === $atts['allow_pagination'] || '0' === $atts['allow_pagination'] ) {
			$atts['allow_pagination'] = false;
		}
		if ( 'false' === $atts['show_date'] || '0' === $atts['show_date'] ) {
			$atts['show_date'] = false;
		}
		if ( isset( $_GET['clear'] ) ) {
			$atts['no_cache'] = 1;
		}
		return $atts;
	}
}

new WP_Review_Posts_Shortcode();
