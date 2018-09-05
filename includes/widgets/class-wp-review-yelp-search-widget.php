<?php
/**
 * Yelp search widget
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Yelp_Search_Widget
 */
class WP_Review_Yelp_Search_Widget extends WP_Widget {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => $this->classname(),
			'description' => $this->description(),
		);
		parent::__construct( $this->id_base(), $this->name(), $widget_ops );
	}

	/**
	 * Gets widget class name.
	 *
	 * @return string
	 */
	protected function classname() {
		return 'widget_wp_review_yelp_search';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display Yelp business search result.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_yelp_search';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Yelp Search', 'wp-review' );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_defaults() );

		echo wp_kses_post( $args['before_widget'] );
		if ( $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		$content = sprintf(
			'[wp-review-yelp-search term="%1$s" location="%2$s" limit="%3$s" attributes="%4$s" sort_by="%5$s"]',
			esc_attr( $instance['term'] ),
			esc_attr( $instance['location'] ),
			intval( $instance['limit'] ),
			esc_attr( $instance['attributes'] ),
			esc_attr( $instance['sort_by'] )
		);

		echo do_shortcode( $content );

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_defaults() );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'term' ) ); ?>"><?php esc_html_e( 'Search term:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'term' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'term' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['term'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php esc_html_e( 'Location:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['location'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Result limit:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" min="1" step="1" value="<?php echo intval( $instance['limit'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>"><?php esc_html_e( 'Sort By:', 'wp-review' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_by' ) ); ?>">
				<option value=""><?php _e('SortBy', 'wp-review'); ?></option>
				<option value="best_match" <?php selected($instance['sort_by'], 'best_match', true); ?>><?php _e('Best Match', 'wp-review'); ?></option>
				<option value="rating" <?php selected($instance['sort_by'], 'rating', true); ?>><?php _e('Rating', 'wp-review'); ?></option>
				<option value="review_count" <?php selected($instance['sort_by'], 'review_count', true); ?>><?php _e('Review Count', 'wp-review'); ?></option>
				<option value="distance" <?php selected($instance['sort_by'], 'distance', true); ?>><?php _e('Distance', 'wp-review'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'attributes' ) ); ?>"><?php esc_html_e( 'Attributes:', 'wp-review' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'attributes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'attributes' ) ); ?>" placeholder="hot_and_new, request_a_quote, reservation, waitlist_reservation, deals, cashback, gender_neutral_restrooms"><?php echo $instance['attributes']; ?></textarea>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$defaults = $this->_get_defaults();

		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : $defaults['title'];
		$instance['term'] = ! empty( $new_instance['term'] ) ? strip_tags( $new_instance['term'] ) : $defaults['term'];
		$instance['location'] = ! empty( $new_instance['location'] ) ? strip_tags( $new_instance['location'] ) : $defaults['location'];
		$instance['limit'] = ! empty( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : $defaults['limit'];
		$instance['sort_by'] = ! empty( $new_instance['sort_by'] ) ? strip_tags( $new_instance['sort_by'] ) : $defaults['sort_by'];
		$instance['attributes'] = ! empty( $new_instance['attributes'] ) ? strip_tags( $new_instance['attributes'] ) : $defaults['attributes'];
		return $instance;
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		return array(
			'title'    => __( 'YELP Businesses', 'wp-review' ),
			'term'     => 'bar',
			'location' => 'New York',
			'limit'    => 3,
			'sort_by'	 	 => 'best_match',
			'price'			 => '',
			'attributes' => ''
		);
	}
}