<?php
/**
 * Popup bar style
 *
 * @package WP_Review
 * @since   3.0.0
 * @version 3.0.0
 *
 * @var array $config
 */

$opacity = floatval( $config['overlay_opacity'] );

// phpcs:disable
?>
<style>
	.mfp-bg {
		background-color: <?php echo esc_attr( $config['overlay_color'] ); ?>;
		opacity: <?php echo $opacity; ?>;
		--animate-opacity: <?php echo $opacity ?>;
	}
	.wpr-popup {
		max-width: <?php echo esc_attr( $config['width'] ); ?>;
	}
</style>
