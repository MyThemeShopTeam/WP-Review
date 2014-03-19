<?php
ob_start();
/**
 * Front-end functions for this plugin.
 *
 * @since     1.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Display the meta box data below 'the_content' hook. */
add_filter( 'the_content', 'wp_review_display_data' );

/* Custom review color. */
add_action( 'wp_head', 'wp_review_color_output', 20 );

/*Get review with Ajax*/
add_action('wp_ajax_mts_review_get_review', 'mts_review_get_review');
add_action('wp_ajax_nopriv_mts_review_get_review', 'mts_review_get_review');


/**
 * Display the meta box data.
 *
 * @since 1.0
 * @link  http://pippinsplugins.com/playing-nice-with-the-content-filter/
 */
function wp_review_display_data( $content ) {
	global $post;
	global $blog_id;

	/* Retrieve the meta box data. */
	$heading     = get_post_meta( $post->ID, 'wp_review_heading', true );
	$desc        = get_post_meta( $post->ID, 'wp_review_desc', true );
	$items       = get_post_meta( $post->ID, 'wp_review_item', true );
	$type        = get_post_meta( $post->ID, 'wp_review_type', true );
	$total       = get_post_meta( $post->ID, 'wp_review_total', true );
	$location    = get_post_meta( $post->ID, 'wp_review_location', true );
	$allowUsers  = get_post_meta( $post->ID, 'wp_review_userReview', true );

	/* Define a custom class for bar type. */
	$class    = '';
	if ( 'point' == $type ) {
		$class = 'bar-point';
	} elseif ( 'percentage' == $type ) {
		$class = 'percentage-point';
	}

	/**
	 * Add the custom data from the meta box to the main query an
	 * make sure the hook only apply on single post.
	 */
	if ( $type != '' && is_single()  && is_main_query() ) {
		

			$review = '<div id="review" class="review-wrapper ' . $class . '" >';

				/* Review title. */
				if( $heading != '' ){
					$review .= '<h5 class="review-title">' . __( $heading ) . '</h5>';
				}


				/* Review item. */
				if ( $items ) {
					$review .= '<ul class="review-list">';
						foreach( $items as $item ) {
							
							$item['wp_review_item_title'] = ( !empty( $item['wp_review_item_title'] ) ) ? $item['wp_review_item_title'] : '';
							$item['wp_review_item_star'] = ( !empty( $item['wp_review_item_star'] ) ) ? $item['wp_review_item_star'] : '';

							if ( 'star' == $type ) {
								$result = $item['wp_review_item_star'] * 20;
								$bestresult = '<meta itemprop="best" content="5"/>';
								$best = '5';
							} elseif( 'point' == $type ) {
								$result = $item['wp_review_item_star'] * 10;
								$bestresult = '<meta itemprop="best" content="10"/>';
								$best = '10';
							} else {
								$result = $item['wp_review_item_star'] * 100 / 100;
								$bestresult = '<meta itemprop="best" content="100"/>';
								$best = '100';
							}

							$review .= '<li>';
								
								if ( 'point' == $type ) {
									$review .= '<span>' . wp_filter_post_kses( $item['wp_review_item_title'] ) . ' - ' . $item['wp_review_item_star'] . '</span>';
								} elseif( 'percentage' == $type ) {
									$review .= '<span>' . wp_filter_post_kses( $item['wp_review_item_title'] ) . ' - ' . $item['wp_review_item_star'] . '%' . '</span>';
								} else {
									$review .= '<span>' . wp_filter_post_kses( $item['wp_review_item_title'] ) . '</span>';
								}

							$review .= '<div class="review-star">';
							$review .= '<div class="review-result-wrapper">';

								if ( 'star' == $type ) {
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<div class="review-result" style="width:' . $result . '%;">';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '</div><!-- .review-result -->';
								} elseif ( 'point' == $type ) {
									$review .= '<div class="review-result" style="width:' . $result . '%;">' . $item['wp_review_item_star'] . '</div>';
								} else {
									$review .= '<div class="review-result" style="width:' . $result . '%;">' . $item['wp_review_item_star'] . '</div>';
								}
								
							$review .= '</div><!-- .review-result-wrapper -->';
							$review .= '</div><!-- .review-star -->';
							$review .= '</li>';

						}
					$review .= '</ul>';
				}

				/* Review description. */
				if ( $desc ) {
						$review .= '<div class="review-desc" >';
						$review .= '<p class="review-summary-title"><strong>' . __( 'Summary', 'mts-review' ) . '</strong></p>';
						$review .= wp_filter_post_kses( wpautop( $desc ) );
						$review .= '</div><!-- .review-desc -->';
						
				}//**END IF HAS DESCRIPTION**
				if( $total != '' ){
				$review .= '<div class="review-total-wrapper"> ';
							
							if ( 'percentage' == $type ) {
								$review .= '<span class="review-total-box"><span itemprop="review">' . $total . '</span> <i class="percentage-icon">%</i>' . '</span>';
							} else {
								$review .= '<span class="review-total-box" itemprop="review">' . $total . '</span></span>';
							}							

							if ( 'star' == $type ) {
								$review .= '<div class="review-total-star">';
									$review .= '<div class="review-result-wrapper">';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
									$review .= '<i class="mts-icon-star"></i>';
										$review .= '<div class="review-result" style="width:' . $total*20 . '%;">';
										$review .= '<i class="mts-icon-star"></i>';
										$review .= '<i class="mts-icon-star"></i>';
										$review .= '<i class="mts-icon-star"></i>';
										$review .= '<i class="mts-icon-star"></i>';
										$review .= '<i class="mts-icon-star"></i>';
										$review .= '</div><!-- .review-result -->';
									$review .= '</div><!-- .review-result-wrapper -->';
								$review .= '</div><!-- .review-star -->';
							}
														
						$review .= '</div>';

						
						$review .= '<div itemscope="itemscope" itemtype="http://data-vocabulary.org/Review">
						<meta itemprop="itemreviewed" content="'.__( $heading ).'">

						<span itemprop="rating" itemscope="itemscope"itemtype="http://data-vocabulary.org/Rating">
						  <meta itemprop="value" content="'.$total.'">
						  <meta itemprop="best" content="'.$best.'">
					    </span>
					    <span itemprop="reviewer" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">  
					    	<meta itemprop="name" content="'. get_the_author() .'">
         				 </span>   
					</div>';
					}

			/**
				* USERS REVIEW AREA
				*/

				if( is_array( $allowUsers ) && $allowUsers[0] == 1 ){								
					$allowedClass = 'allowed-to-rate';
					$hasNotRatedClass = 'has-not-rated-yet';
					$postReviews = mts_get_post_reviews( $post->ID );
					$userTotal = $postReviews[0]->reviewsAvg;
					$usersReviewsCount = $postReviews[0]->reviewsNum;
					
					$review .= '<div style="clear: both;"></div>';

					$review .= '<div class="user-review-area">';
													
					//$ip = $_SERVER['REMOTE_ADDR'];
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					    $ip = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
					} else {
					    $ip = $_SERVER['REMOTE_ADDR'];
					}
					$user_id = '';
					if ( is_user_logged_in() ) { $user_id = get_current_user_id(); }
					//echo $blog_id;
					$review .= '<input type="hidden" id="blog_id" value="'.$blog_id.'">';
					$review .= '<input type="hidden" id="post_id" value="'.$post->ID.'">';
					$review .= '<input type="hidden" id="user_id" value="'.$user_id.'">';
					$review .= '<input type="hidden" id="mts-userIP" value="'.$ip.'">';	
					
					if( $userTotal == '' ) $userTotal = '0.0';
					$review .= '<div class="user-total-wrapper"><span class="user-review-title">'.__('User Rating','mts-review').': </span><span class="review-total-box"><span id="mts-user-reviews-total">' . $userTotal . '</span> ';
					$review.= '<small>(<span id="mts-user-reviews-counter" >'.$usersReviewsCount.'</span> '.__('votes', 'mts-review').')</small></span></div>';
					
					if( hasPreviousReview( $post->ID, $user_id, $ip )) {
						$hasNotRatedClass = '';						
					}

					$review .= '<div class="review-total-star '.$allowedClass.' '.$hasNotRatedClass.'" >';
						$review .='<div class="mts-review-wait-msg"><span class="animate-spin mts-icon-loader"></span>'.__('Sending','mts-review').'</div>';
						$review .= '<div class="review-result-wrapper">';
							$review .= '<a data-input-value="1" title="1/5"><i class="mts-icon-star"></i></a>';
							$review .= '<a data-input-value="2" title="2/5"><i class="mts-icon-star"></i></a>';
							$review .= '<a data-input-value="3" title="3/5"><i class="mts-icon-star"></i></a>';
							$review .= '<a data-input-value="4" title="4/5"><i class="mts-icon-star"></i></a>';
							$review .= '<a data-input-value="5" title="5/5"><i class="mts-icon-star"></i></a>';
							$review .= '<div class="review-result" style="width:' . $userTotal*20 . '%;">';							
								$review .= '<i class="mts-icon-star"></i>';
								$review .= '<i class="mts-icon-star"></i>';
								$review .= '<i class="mts-icon-star"></i>';
								$review .= '<i class="mts-icon-star" style=""></i>';
								$review .= '<i class="mts-icon-star"></i>';
							$review .= '</div><!-- .review-result -->';
						$review .= '</div><!-- .review-result-wrapper -->';
					$review .= '</div><!-- .review-star -->';
					$review .= '<input type="hidden" id="mts-review-user-rate" value="" />';

					$review .= '</div>';
					
                $review .= '<div itemscope itemtype="http://schema.org/Review">  
						     <div itemprop="reviewRating" itemscope itemtype="http://schema.org/AggregateRating">    
						        <meta itemprop="ratingValue" content="'.$userTotal.'" /> 
						        <meta itemprop="bestRating" content="5"/>   
						        <meta itemprop="ratingCount" content="'.$usersReviewsCount.'" />
						     </div>
							</div>';

				}//**END IF USERS ALLOWED TO RATE**
			

				$review .= '</div><!-- #review -->';

			if ( 'bottom' == $location ) {
				return $content .= $review;
			} else {
				return $review .= $content;
			}

		
 
	} else {
		return $content;
	}


}

function mts_get_post_reviews( $post_id ){
	if( is_numeric( $post_id ) && $post_id > 0 ){
		global $wpdb;
		global $blog_id;
		$table_name = $wpdb->prefix . MTS_WP_REVIEW_DB_TABLE;
		if (function_exists('is_multisite') && is_multisite()) {$table_name = $wpdb->base_prefix . MTS_WP_REVIEW_DB_TABLE;}
		$reviews = $wpdb->get_results( $wpdb->prepare("SELECT ROUND( AVG(rate) ,1 ) as reviewsAvg, COUNT(id) as reviewsNum FROM $table_name WHERE blog_id = '%d' AND post_id = '%d'", $blog_id, $post_id) );		
		return $reviews;
	}
}


/**
 * Star review color
 *
 * @since 1.0
 */
function wp_review_color_output() {
	global $post;

	/* Retrieve the meta box data. */
	if(is_singular()) {
		$color = get_post_meta( $post->ID, 'wp_review_color', true );
		$type  = get_post_meta( $post->ID, 'wp_review_type', true );
		$fontcolor = get_post_meta( $post->ID, 'wp_review_fontcolor', true );
		$bgcolor1  = get_post_meta( $post->ID, 'wp_review_bgcolor1', true );
		$bgcolor2  = get_post_meta( $post->ID, 'wp_review_bgcolor2', true );
		$bordercolor  = get_post_meta( $post->ID, 'wp_review_bordercolor', true );
		$total = get_post_meta( $post->ID, 'wp_review_total', true );
		
		if( !$color ) $color = '#333333';

		if( $color ) {
			echo '<style type="text/css">';

			if ( 'star' == $type ) { ?>

				.review-result-wrapper .review-result i { color: <?php echo $color; ?>; opacity: 1; filter: alpha(opacity=100); }
				.review-result-wrapper i{ color: <?php echo $color; ?>; opacity: 0.50; filter: alpha(opacity=50); }
				
			<?php } elseif ( 'point' == $type ) { ?>

				.bar-point .review-result { background-color: <?php echo $color; ?>; }

			<?php } else { ?>

				.percentage-point .review-result { background-color: <?php echo $color; ?>; }

			<?php }
			?>
			.review-wrapper, .review-title, .review-desc p{ color: <?php echo $fontcolor; ?>;}
			.review-list li, .review-wrapper{ background: <?php echo $bgcolor2; ?>;}
			.review-title, .review-list li:nth-child(2n){background: <?php echo $bgcolor1; ?>;}

			.bar-point .allowed-to-rate .review-result, .percentage-point .allowed-to-rate .review-result{background: none;}
			.review-total-star.allowed-to-rate a i { color: <?php echo $color; ?>; opacity: 0.50; filter: alpha(opacity=50); }
			.bar-point .allowed-to-rate .review-result, .percentage-point .allowed-to-rate .review-result{text-indent:0;}
			.bar-point .allowed-to-rate .review-result i, .percentage-point .allowed-to-rate .review-result i, .mts-user-review-star-container .selected i { color: <?php echo $color; ?>; opacity: 1; filter: alpha(opacity=100); }
			.review-wrapper, .review-title, .review-list li, .review-list li:last-child, .user-review-area{border-color: <?php echo $bordercolor; ?>;}
			<?php
			if( $total == '' ){?>
				.user-review-area{border: 1px solid <?php echo $bordercolor; ?>; margin-top: 0px;}
				.review-desc{width: 100%;}
				.review-wrapper{border: none; overflow: visible;}
			<?php }
			echo '</style>';
		}
	}
}

/**
*Check if user has reviewed this post previously
*/
function hasPreviousReview( $post_id, $user_id, $ip ){	
	if( is_numeric( $post_id ) && $post_id > 0 ){
		global $wpdb;
		global $blog_id;
		$table_name = $wpdb->prefix . MTS_WP_REVIEW_DB_TABLE;
		if (function_exists('is_multisite') && is_multisite()) {$table_name = $wpdb->base_prefix . MTS_WP_REVIEW_DB_TABLE;}
		if( is_numeric( $user_id ) && $user_id > 0 ){			
			$prevRates = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(id) as reviewsNum FROM $table_name WHERE blog_id = '%d' AND post_id = '%d' AND user_id = '%d'", $blog_id, $post_id, $user_id) );			
			if( $prevRates->reviewsNum > 0 ) return true; else return false;
		}
		elseif( $ip != '' ){			
			$prevRates = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(id) as reviewsNum FROM $table_name WHERE blog_id = '%d' AND post_id = '%d' AND user_ip = '%s' AND user_id = '0'", $blog_id, $post_id, $ip) );			
			if( $prevRates->reviewsNum > 0 ) return true; else return false;
		}
		else return false;
	}
	return false;
}


/**
*Get review with Ajax
*/
function mts_review_get_review(){
	global $wpdb;
	
	$table_name = $wpdb->prefix . MTS_WP_REVIEW_DB_TABLE;
	if (function_exists('is_multisite') && is_multisite()) {$table_name = $wpdb->base_prefix . MTS_WP_REVIEW_DB_TABLE;}
	
	global $blog_id;
	$post_id = $_POST['post_id'];
	$user_id = $_POST['user_id'];
	$uip = $_POST['ip'];
	$data = $_POST['review'];
	
	if( $rows_affected = $wpdb->insert( $table_name, array('blog_id' => $blog_id, 'post_id' => $post_id, 'user_id' => $user_id, 'user_ip' => $uip, 'rate' => $data, 'date' => current_time('mysql')) ) ){
		$reviews = $wpdb->get_row( $wpdb->prepare("SELECT ROUND( AVG(rate) ,1 ) as reviewsAvg, COUNT(id) as reviewsNum FROM $table_name WHERE blog_id = '%d' AND post_id = '%d'", $blog_id, $post_id) );
		echo $reviews->reviewsAvg.'|'.$reviews->reviewsNum;	
	}
	else echo 'MTS_REVIEW_DB_ERROR';
	exit;
}
?>