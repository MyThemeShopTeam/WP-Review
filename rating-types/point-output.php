<?php
/**
 * Star rating type output template
 * 
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $wp_review_rating_types;

$class = 'review-point';
if (!empty($rating['args']['class']))
	$class .= ' '.sanitize_html_class( $rating['args']['class'] );

?>
<div class="<?php echo $class; ?>">
	<div class="review-result-wrapper">
		<div class="review-result" style="width:<?php echo esc_attr( $rating['value'] * 10 ); ?>%; background-color: <?php echo esc_attr( $rating['color'] ); ?>;"></div>
		<div class="review-result-text" style="color: <?php echo esc_attr( $rating['colors']['bgcolor1'] ); ?>;"><?php echo sprintf( $wp_review_rating_types[$rating['type']]['value_text'], $rating['value'] ); ?></div>
	</div>
</div><!-- .review-percentage -->