<?php
/**
 * Facebook reviews widget
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Facebook_Reviews_Widget
 */
class WP_Review_Facebook_Reviews_Widget extends WP_Widget {

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
		return 'widget_wp_review_facebook_reviews';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display Facebook page reviews.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_facebook_reviews';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Facebook Reviews', 'wp-review' );
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
			'[wp-review-facebook-reviews page_id="%s" limit="%s"]',
			esc_attr( $instance['page_id'] ),
			intval( $instance['limit'] )
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

		<p class="wpr-fb-page">
			<label for="<?php echo esc_attr( $this->get_field_id( 'page_id' ) ); ?>"><?php esc_html_e( 'Page ID:', 'wp-review' ); ?></label>
			<input class="widefat wpr-fb-page-id" id="<?php echo esc_attr( $this->get_field_id( 'page_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_id' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['page_id'] ); ?>">
			<button type="button" class="button wpr-fb-generate" style="width: auto;"><?php esc_html_e( 'Generate token', 'wp-review' ); ?></button>
			<!-- <span class="description"><?php esc_html_e( 'Go to Your page > About > Facebook Page ID to get page ID.', 'wp-review' ); ?></span> -->
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Limit:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="number" min="1" step="1" value="<?php echo intval( $instance['limit'] ); ?>">
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
		$instance['page_id'] = ! empty( $new_instance['page_id'] ) ? strip_tags( $new_instance['page_id'] ) : $defaults['page_id'];
		$instance['limit'] = ! empty( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : $defaults['limit'];

		return $instance;
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		return array(
			'title'   => __( 'Facebook page reviews', 'wp-review' ),
			'page_id' => '',
			'limit'   => 5,
		);
	}
}
