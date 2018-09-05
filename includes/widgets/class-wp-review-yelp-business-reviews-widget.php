<?php
/**
 * Yelp business user reviews widget
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Yelp_Business_Reviews_Widget
 */
class WP_Review_Yelp_Business_Reviews_Widget extends WP_Widget {

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
		return 'widget_wp_review_yelp_business_reviews';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display Yelp business user reviews data.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_yelp_business_reviews';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Yelp Business User Reviews', 'wp-review' );
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
			'[wp-review-yelp-business-reviews id="%s" review_num="%d"]',
			esc_attr( $instance['id'] ),
			intval( $instance['review_num'] )
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
			<label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php esc_html_e( 'Business ID:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['id'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'review_num' ) ); ?>"><?php esc_html_e( 'Number of reviews to show (Max allowed 3):', 'wp-review' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'review_num' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'review_num' ) ); ?>" class="widefat">
				<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
					<option value="<?php echo intval( $i ); ?>" <?php selected( $instance['review_num'], $i ); ?>><?php echo intval( $i ); ?></option>
				<?php endfor; ?>
			</select>
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
		$instance['id'] = ! empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : $defaults['id'];
		$instance['review_num'] = ! empty( $new_instance['review_num'] ) ? intval( $new_instance['review_num'] ) : $defaults['review_num'];

		return $instance;
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		return array(
			'title' => __( 'YELP Business reviews', 'wp-review' ),
			'id'    => '',
			'review_num' => 3,
		);
	}
}
