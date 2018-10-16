<?php
/**
 * Form fields
 *
 * @package WP_Review
 * @since 3.0.0
 */

/**
 * Class WP_Review_Form_Field
 */
class WP_Review_Form_Field {

	/**
	 * Renders switch field.
	 *
	 * @param array $args Field arguments.
	 * @return string
	 */
	public function render_switch( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'        => '',
				'class'     => '',
				'name'      => '',
				'on_label'  => _x( 'Yes', 'switch label', 'wp-review' ),
				'off_label' => _x( 'No', 'switch label', 'wp-review' ),
				'value'     => 0,
				'echo'      => true,
				'disabled'  => false,
			)
		);

		$switch_id   = $args['id'] ? $args['id'] : 'wpr-switch-' . mt_rand( 100, 999 );
		$switch_name = $args['name'] ? $args['name'] : $switch_id;
		$value       = intval( $args['value'] );

		if ( $args['disabled'] ) {
			$args['class'] .= ' wp-review-disabled';
		}
		ob_start();
		?>
		<div id="<?php echo esc_attr( $switch_id ); ?>" class="wpr-switch <?php echo esc_attr( $args['class'] ); ?>">
			<input type="radio" id="<?php echo esc_attr( $switch_id ); ?>-on" name="<?php echo esc_attr( $switch_name ); ?>" class="wpr-switch__on" value="1" <?php checked( $value, 1 ); ?>>
			<label for="<?php echo esc_attr( $switch_id ); ?>-on" class="button button-secondary"><?php echo esc_html( $args['on_label'] ); ?></label>
			<input type="radio" id="<?php echo esc_attr( $switch_id ); ?>-off" name="<?php echo esc_attr( $switch_name ); ?>" value="0" class="wpr-switch__off" <?php checked( $value, 0 ); ?>>
			<label for="<?php echo esc_attr( $switch_id ); ?>-off" class="button button-secondary"><?php echo esc_html( $args['off_label'] ); ?></label>
		</div>
		<?php
		$output = ob_get_clean();
		if ( ! $args['echo'] ) {
			return $output;
		}
		echo $output;
	}
}
