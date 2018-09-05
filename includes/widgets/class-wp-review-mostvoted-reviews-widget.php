<?php
/**
 * Most voted reviews widget
 *
 * @package WP_Review
 *
 * @since 3.0.0
 */

/**
 * Class WP_Review_Mostvoted_Reviews_Widget
 */
class WP_Review_Mostvoted_Reviews_Widget extends WP_Review_Recent_Reviews_Widget {

	/**
	 * Query type.
	 *
	 * @var string
	 */
	protected $query_type = 'mostvoted';

	/**
	 * Gets widget class name.
	 *
	 * @return string
	 */
	protected function classname() {
		return 'widget_wp_review_mostvoted_reviews';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display most voted reviews.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_mostvoted_reviews';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Most Voted Reviews', 'wp-review' );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		parent::form( $instance );
		$number_of_days = isset( $instance['number_of_days'] ) ? $instance['number_of_days'] : ''
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_days' ) ); ?>"><?php esc_html_e( 'Number of days:', 'wp-review' ); ?></label>
			<input type="number" class="widefat" min="0" step="1" id="<?php echo esc_attr( $this->get_field_id( 'number_of_days' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_days' ) ); ?>" value="<?php echo absint( isset( $instance['number_of_days'] ) ? $instance['number_of_days'] : '' ); ?>">
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
		$instance = parent::update( $new_instance, $old_instance );
		$instance['number_of_days'] = ! empty( $new_instance['number_of_days'] ) ? intval( $new_instance['number_of_days'] ) : '';
		return $instance;
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		$defaults = parent::_get_defaults();
		$defaults['title'] = __( 'Most voted reviews', 'wp-review' );
		return $defaults;
	}
}
