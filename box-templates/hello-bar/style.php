<?php
/**
 * Notification bar style
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array $config
 */

?>
<style>
	.hello-bar {
		background-color: <?php echo esc_attr( $config['bg_color'] ); ?>;
		<?php if ( ! empty( $config['bg_image']['url'] ) ) : ?>
			background-image: url(<?php echo esc_url( $config['bg_image']['url'] ); ?>);
		<?php endif; ?>
		color: <?php echo esc_attr( $config['text_color'] ); ?>;
	}

	.hello-bar__container {
		max-width: <?php echo esc_attr( $config['max_container'] ); ?>
	}

	.hello-bar__button {
		background-color: <?php echo esc_attr( $config['button_bg_color'] ); ?>;
		color: <?php echo esc_attr( $config['button_text_color'] ); ?> !important;
	}

	.hello-bar__star-rating .review-result-wrapper {
		color: <?php echo esc_attr( $config['star_color'] ); ?>;
	}
</style>
