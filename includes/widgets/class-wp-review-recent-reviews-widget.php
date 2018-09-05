<?php
/**
 * Recent reviews widget
 *
 * @package WP_Review
 *
 * @since 3.0.0
 */

/**
 * Class WP_Review_Recent_Reviews_Widget
 */
class WP_Review_Recent_Reviews_Widget extends WP_Widget {

	/**
	 * Query type.
	 *
	 * @var string
	 */
	protected $query_type = 'recent';

	/**
	 * No cache flag.
	 *
	 * @var bool
	 */
	protected $no_cache = false;

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
		return 'widget_wp_review_recent_reviews';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display recent reviews.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_recent_reviews';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Recent Reviews', 'wp-review' );
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
		$options = $this->get_options( $args, $instance );

		echo wp_kses_post( $args['before_widget'] );
		if ( $instance['title'] ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		printf(
			'<div class="js-reviews-placeholder wp-reviews-list" data-options="%s"></div>',
			esc_attr( wp_json_encode( $options ) )
		);

		echo wp_kses_post( $args['after_widget'] );

		wp_review_enqueue_rating_type_scripts( 'output', $options['review_type'] );
	}

	/**
	 * Gets reviews query options.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 * @return array
	 */
	protected function get_options( $args, $instance ) {
		$options = $instance;
		$options += $args;
		$options['_type'] = $this->query_type;
		if ( $this->no_cache ) {
			$options['no_cache'] = 1;
		}
		return $options;
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
		$instance['review_type'] = (array) $instance['review_type'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'review_type' ) ); ?>"><?php esc_html_e( 'Review type:', 'wp-review' ); ?></label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'review_type' ) ); ?>[]" id="<?php echo esc_attr( $this->get_field_id( 'review_type' ) ); ?>" multiple>
				<?php
				$review_types = wp_review_get_rating_types();
				foreach ( $review_types as $name => $review_type ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $name ),
						in_array( $name, $instance['review_type'] ) ? 'selected' : '',
						esc_html( $review_type['label'] )
					);
				}
				?>
			</select>
		</p>

		<p>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'allow_pagination' ) ); ?>" value="1" <?php checked( $instance['allow_pagination'], 1 ); ?>>
				<?php esc_html_e( 'Allow pagination', 'wp-review' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_num' ) ); ?>"><?php esc_html_e( 'Number of reviews to show:', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_num' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_num' ) ); ?>" type="number" min="-1" step="1" value="<?php echo intval( $instance['post_num'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_length' ) ); ?>"><?php esc_html_e( 'Title length (words):', 'wp-review' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title_length' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_length' ) ); ?>" type="number" min="-1" step="1" value="<?php echo intval( $instance['title_length'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'thumb_size' ) ); ?>"><?php esc_html_e( 'Thumbnail size:', 'wp-review' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'thumb_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'thumb_size' ) ); ?>">
				<option value="small" <?php selected( $instance['thumb_size'], 'small', true ); ?>><?php esc_html_e( 'Small', 'wp-review' ); ?></option>
				<option value="large" <?php selected( $instance['thumb_size'], 'large', true ); ?>><?php esc_html_e( 'Large', 'wp-review' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ) ?>"><?php esc_html_e( 'Extra information', 'wp-review' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" class="widefat">
				<option value=""><?php esc_html_e( 'None', 'wp-review' ); ?></option>
				<option value="1" <?php selected( $instance['show_date'], 1 ); ?>><?php esc_html_e( 'Post date', 'wp-review' ); ?></option>
				<option value="2" <?php selected( $instance['show_date'], 2 ); ?>><?php esc_html_e( 'Number of reviews', 'wp-review' ); ?></option>
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
		$instance['review_type'] = ! empty( $new_instance['review_type'] ) ? $new_instance['review_type'] : $defaults['review_type'];
		$instance['allow_pagination'] = intval( ! empty( $new_instance['allow_pagination'] ) );
		$instance['post_num'] = ! empty( $new_instance['post_num'] ) ? intval( $new_instance['post_num'] ) : $defaults['post_num'];
		$instance['title_length'] = ! empty( $new_instance['title_length'] ) ? intval( $new_instance['title_length'] ) : $defaults['title_length'];
		$instance['thumb_size'] = ! empty( $new_instance['thumb_size'] ) ? strip_tags( $new_instance['thumb_size'] ) : $defaults['thumb_size'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? intval( $new_instance['show_date'] ) : $defaults['show_date'];

		if ( ! $this->no_cache ) {
			wp_review_clear_cache();
		}

		return $instance;
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		return array(
			'title'            => __( 'Recent reviews', 'wp-review' ),
			'review_type'      => array(),
			'allow_pagination' => 1,
			'post_num'         => 5,
			'title_length'     => 15,
			'thumb_size'       => 'small',
			'show_date'        => 1,
		);
	}
}
