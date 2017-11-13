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

$class = 'review-star';
if (!empty($rating['args']['class']))
	$class .= ' '.sanitize_html_class( $rating['args']['class'] );

?>
<div class="<?php echo $class; ?>">
	<div class="review-result-wrapper">
		<i class="mts-icon-star"></i>
		<i class="mts-icon-star"></i>
		<i class="mts-icon-star"></i>
		<i class="mts-icon-star"></i>
		<i class="mts-icon-star"></i>
		<div class="review-result" style="width:<?php echo ( $rating['value'] * 20 ); ?>%; color:<?php echo $rating['color']; ?>;">
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
			<i class="mts-icon-star"></i>
		</div><!-- .review-result -->
	</div><!-- .review-result-wrapper -->
</div><!-- .review-star -->
