<?php
/**
 * Category reviews widget
 *
 * @package WP_Review
 *
 * @since 3.0.0
 */

/**
 * Class WP_Review_Category_Reviews_Widget
 */
class WP_Review_Category_Reviews_Widget extends WP_Review_Recent_Reviews_Widget {

	/**
	 * Query type.
	 *
	 * @var string
	 */
	protected $query_type = 'cat';

	/**
	 * No cache flag.
	 *
	 * @var bool
	 */
	protected $no_cache = true;

	/**
	 * Gets widget class name.
	 *
	 * @return string
	 */
	protected function classname() {
		return 'widget_wp_review_category_reviews';
	}

	/**
	 * Gets widget description.
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Display reviews in current category.', 'wp-review' );
	}

	/**
	 * Gets widget base id.
	 *
	 * @return string
	 */
	protected function id_base() {
		return 'wp_review_category_reviews';
	}

	/**
	 * Gets widget name.
	 *
	 * @return string
	 */
	protected function name() {
		return __( 'WP Review: Category Reviews', 'wp-review' );
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
		$cat_ids = ! empty( $instance['cat'] ) ? (array) $instance['cat'] : array();
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><?php esc_html_e( 'Category:', 'wp-review' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'cat' ) ) ?>[]" id="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>" class="widefat" multiple>
				<?php
				$categories = get_categories( array(
					'orderby' => 'ID',
					'order'   => 'ASC',
				));
				foreach ( $categories as $category ) {
					printf(
						'<option value="%1$s" %2$s>%3$s&nbsp;&nbsp;(%4$s)</option>',
						intval( $category->term_id ),
						in_array( $category->term_id, $cat_ids ) ? 'selected' : '',
						esc_html( $category->name ),
						intval( $category->count )
					);
				}
				?>
			</select>
		</p>
		<?php
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
		parent::widget( $args, $instance );
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
		$defaults = $this->_get_defaults();
		$instance['cat'] = ! empty( $new_instance['cat'] ) ? $new_instance['cat'] : $defaults['cat'];
		return $instance;
	}

	/**
	 * Gets widget defaults.
	 *
	 * @return array
	 */
	protected function _get_defaults() {
		$defaults = parent::_get_defaults();
		$defaults['cat'] = array();
		$defaults['title']   = __( 'Category reviews', 'wp-review' );
		return $defaults;
	}
}
