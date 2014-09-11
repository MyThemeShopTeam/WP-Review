<?php
ob_start();
/**
 * WP Review
 *
 * @since     2.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

$options = get_option('wp_review_options');

/* Display the meta box data below 'the_content' hook. */
add_filter( 'the_content', 'wp_review_inject_data' );

/* Get review with Ajax */
add_action('wp_ajax_mts_review_get_review', 'mts_review_get_review');
add_action('wp_ajax_nopriv_mts_review_get_review', 'mts_review_get_review');

/* Show with shortcode */
add_shortcode('wp-review', 'wp_review_get_data');
add_shortcode('wp-review-total', 'wp_review_total_shortcode');
// aliases
add_shortcode('wp_review', 'wp_review_get_data');
add_shortcode('wp_review_total', 'wp_review_total_shortcode');

// image sizes for the widgets
add_image_size( 'wp_review_large', 320, 200, true ); 
add_image_size( 'wp_review_small', 65, 65, true ); 

/**
 * Get the meta box data.
 *
 * @since 1.0
 * 
 */
function wp_review_get_data() {
	global $post;
	global $blog_id;
    $options = get_option('wp_review_options');

	/* Retrieve the meta box data. */
	$heading     = get_post_meta( $post->ID, 'wp_review_heading', true );
	$desc_title  = get_post_meta( $post->ID, 'wp_review_desc_title', true );
	if ( ! $desc_title ) $desc_title = __('Summary', 'wp-review');
	$desc        = get_post_meta( $post->ID, 'wp_review_desc', true );
	$items       = get_post_meta( $post->ID, 'wp_review_item', true );
	$type        = get_post_meta( $post->ID, 'wp_review_type', true );
	$total       = get_post_meta( $post->ID, 'wp_review_total', true );
	$hide_desc   = get_post_meta( $post->ID, 'wp_review_hide_desc', true );
	$allowUsers  = get_post_meta( $post->ID, 'wp_review_userReview', true );
	
	$colors = array();
	$colors['custom_colors'] = get_post_meta( $post->ID, 'wp_review_custom_colors', true );
	$colors['color'] = get_post_meta( $post->ID, 'wp_review_color', true );
	$colors['type']  = get_post_meta( $post->ID, 'wp_review_type', true );
	$colors['fontcolor'] = get_post_meta( $post->ID, 'wp_review_fontcolor', true );
	$colors['bgcolor1']  = get_post_meta( $post->ID, 'wp_review_bgcolor1', true );
	$colors['bgcolor2']  = get_post_meta( $post->ID, 'wp_review_bgcolor2', true );
	$colors['bordercolor']  = get_post_meta( $post->ID, 'wp_review_bordercolor', true );
	$colors['total'] = get_post_meta( $post->ID, 'wp_review_total', true );
    
    if (!$colors['custom_colors'] && !empty($options['colors']) && is_array($options['colors'])) {
		$colors = array_merge($colors, $options['colors']);
	}
    $colors = apply_filters('wp_review_colors', $colors, $post->ID);
    $color = $colors['color'];

	/* Define a custom class for bar type. */
	$class    = '';
	if ( 'point' == $type ) {
		$class = 'bar-point';
	} elseif ( 'percentage' == $type ) {
		$class = 'percentage-point';
	}
    $post_types = get_post_types( array('public' => true), 'names' );
    $excluded_post_types = apply_filters('wp_review_excluded_post_types', array('attachment'));
    $allowed_post_types = array();
    foreach ($post_types as $i => $post_type) {
        if (!in_array($post_type, $excluded_post_types)) {
            $allowed_post_types[] = $post_type; // allow it if it's not excluded
        }
    }
    
	/**
	 * Add the custom data from the meta box to the main query an
	 * make sure the hook only apply on allowed post types
	 */
	if ( $type != '' && is_singular($allowed_post_types) && is_main_query() ) {
	//if ( $type != '' && is_main_query() && in_array(get_post_type($post->ID), $allowed_post_types)) {	
	// using this second if() instead of the first will allow reviews to be displayed on archive pages, but it may mess up excerpts
			$review = '<div id="review" class="review-wrapper wp-review-'.$post->ID.' ' . $class . ' delay-animation" >';

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
							} else { // percentage
								$result = $item['wp_review_item_star'] * 100 / 100;
								$bestresult = '<meta itemprop="best" content="100"/>';
								$best = '100';
							}

							$review .= '<li>';
								
								if ( 'point' == $type ) {
									$review .= '<span>' . wp_kses_post( $item['wp_review_item_title'] ) . ' - <span>' . $item['wp_review_item_star'] . '/10</span></span>';
								} elseif( 'percentage' == $type ) {
									$review .= '<span>' . wp_kses_post( $item['wp_review_item_title'] ) . ' - <span>' . $item['wp_review_item_star'] . '%' . '</span></span>';
								} elseif ( 'star' == $type ) {
									$review .= '<span>' . wp_kses_post( $item['wp_review_item_title'] ) . '</span>';
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
									$review .= '<div class="review-result" style="width:' . $result . '%;">' . $item['wp_review_item_star'] . '/10</div>';
								} elseif ( 'percentage' == $type ) {
									$review .= '<div class="review-result" style="width:' . $result . '%;">' . $item['wp_review_item_star'] . '</div>';
								}
								
							$review .= '</div><!-- .review-result-wrapper -->';
							$review .= '</div><!-- .review-star -->';
							$review .= '</li>';

						}
					$review .= '</ul>';
				}

				/* Review description. */
				if ( ! $hide_desc ) {
				if ( $desc ) {
						$review .= '<div class="review-desc" >';
						$review .= '<p class="review-summary-title"><strong>' . $desc_title . '</strong></p>';
						$review .= do_shortcode ( shortcode_unautop( wp_kses_post( wpautop( $desc ) ) ) );
						$review .= '</div><!-- .review-desc -->';
						
				}//**END IF HAS DESCRIPTION**
				if( $total != '' ){
				$review .= '<div class="review-total-wrapper"> ';
							
							if ( 'percentage' == $type ) {
								$review .= '<span class="review-total-box"><span itemprop="review">' . $total . '</span> <i class="percentage-icon">%</i>' . '</span>';
							} elseif ( 'point' == $type ) {
								$review .= '<span class="review-total-box" itemprop="review">' . $total . '/10</span>';
							} elseif ( 'star' == $type ) {
								$review .= '<span class="review-total-box" itemprop="review">' . $total . '</span>';

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

					$review .= '<div class="user-review-area" title="'.__('Click on the stars to rate!', 'wp-review').'">';
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
					$review .= '<input type="hidden" id="token" value="'.wp_create_nonce( "wp-review-security" ).'">';	
                    
					
					if( $userTotal == '' ) $userTotal = '0.0';
					$review .= '<div class="user-total-wrapper"><span class="user-review-title">'.__('User Rating','wp-review').': </span><span class="review-total-box"><span id="mts-user-reviews-total">' . $userTotal . '</span> ';
					$review.= '<small>(<span id="mts-user-reviews-counter" >'.$usersReviewsCount.'</span> '.__('votes', 'wp-review').')</small></span></div>';
					
					if( hasPreviousReview( $post->ID, $user_id, $ip )) {
						$hasNotRatedClass = '';						
					}

					$review .= '<div class="review-total-star '.$allowedClass.' '.$hasNotRatedClass.'" >';
						$review .='<div class="mts-review-wait-msg"><span class="animate-spin mts-icon-loader"></span>'.__('Sending','wp-review').'</div>';
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
                
                $review = apply_filters('wp_review_get_data', $review, $post->ID, $type, $total, $items);

                return $review . wp_review_color_output(); // add color CSS to output
	} else {
		return '';
	}
}

function wp_review_inject_data( $content ) {
    global $post;
    $options = get_option('wp_review_options');
	$custom_location = get_post_meta( $post->ID, 'wp_review_custom_location', true );
	$location = get_post_meta( $post->ID, 'wp_review_location', true );
	if (!$custom_location && !empty($options['review_location'])) {
		$location = $options['review_location'];
	}
    
	$location = apply_filters('wp_review_location', $location, $post->ID);

    if (empty($location) || $location == 'custom') {
        return $content;
    }
    $review = wp_review_get_data();
    if ( 'bottom' == $location ) {
        global $multipage, $numpages, $page;
        if( $multipage ) {
            if ($page == $numpages) {
                return $content .= $review;
            } else {
                return $content;
            }
        } else {
            return $content .= $review;
        }
	} elseif ( 'top' == $location ) {
		return $review .= $content;
	} else {
        return $content;
	}
}

/**
 * Retrieve only total rating.
 * To be used on archive pages, etc.
 *
 * @since 1.0
 * 
 */
function wp_review_show_total($echo = true, $class = 'review-total-only') {
    global $post;

    $type = get_post_meta( $post->ID, 'wp_review_type', true );
	$total = get_post_meta( $post->ID, 'wp_review_total', true );
    $review = '';
    
	$options = get_option('wp_review_options');
    $colors = array();
	$colors['custom_location'] = get_post_meta( $post->ID, 'wp_review_custom_location', true );
	$colors['custom_colors'] = get_post_meta( $post->ID, 'wp_review_custom_colors', true );
	$colors['custom_width'] = get_post_meta( $post->ID, 'wp_review_custom_width', true );

	$colors['color'] = get_post_meta( $post->ID, 'wp_review_color', true );
	$colors['type']  = get_post_meta( $post->ID, 'wp_review_type', true );
	$colors['fontcolor'] = get_post_meta( $post->ID, 'wp_review_fontcolor', true );
	$colors['bgcolor1']  = get_post_meta( $post->ID, 'wp_review_bgcolor1', true );
	$colors['bgcolor2']  = get_post_meta( $post->ID, 'wp_review_bgcolor2', true );
	$colors['bordercolor']  = get_post_meta( $post->ID, 'wp_review_bordercolor', true );
	$colors['total'] = get_post_meta( $post->ID, 'wp_review_total', true );
    
    if (!$colors['custom_colors'] && !empty($options['colors']) && is_array($options['colors'])) {
		$colors = array_merge($colors, $options['colors']);
	}
    $colors = apply_filters('wp_review_colors', $colors, $post->ID);

    if (!empty($type) && (!empty($total) || $total === '0')) {
        wp_enqueue_style( 'wp_review-style', trailingslashit( WP_REVIEW_ASSETS ) . 'css/wp-review.css', array(), '1.1', 'all' );

        $review = '<div class="review-type-'.$type.' '.esc_attr($class).' wp-review-show-total wp-review-total-'.$post->ID.'"> ';
    	
    	if ( 'percentage' == $type ) {
    		$review .= '<span class="review-total-box"><span>' . $total . '</span> <i class="percentage-icon">%</i>' . '</span>';
    	} elseif ( 'point' == $type ) {
    		$review .= '<span class="review-total-box">' . $total . '/'.__('10','wp-review').'</span></span>';
    	} elseif ( 'star' == $type ) {
    	    // star
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
    }
    
    $review = apply_filters('wp_review_show_total', $review, $post->ID, $type, $total);
    
    if ($echo)
        echo $review;
    else
        return $review;
}
function wp_review_total_shortcode($atts, $content) {
    if (empty($atts['class']))
        $atts['class'] = 'review-total-only review-total-shortcode';
    
    return wp_review_show_total(false, $atts['class']);
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
	$style = '';
	$options = get_option('wp_review_options');
	if (empty($options['colors'])) $options['colors'] = array();
	/* Retrieve the meta box data. */
	if(is_singular()) {
        $colors = array();
		$colors['custom_location'] = get_post_meta( $post->ID, 'wp_review_custom_location', true );
		$colors['custom_colors'] = get_post_meta( $post->ID, 'wp_review_custom_colors', true );
		$colors['custom_width'] = get_post_meta( $post->ID, 'wp_review_custom_width', true );

		$colors['color'] = get_post_meta( $post->ID, 'wp_review_color', true );
		$colors['type']  = get_post_meta( $post->ID, 'wp_review_type', true );
		$colors['fontcolor'] = get_post_meta( $post->ID, 'wp_review_fontcolor', true );
		$colors['bgcolor1']  = get_post_meta( $post->ID, 'wp_review_bgcolor1', true );
		$colors['bgcolor2']  = get_post_meta( $post->ID, 'wp_review_bgcolor2', true );
		$colors['bordercolor']  = get_post_meta( $post->ID, 'wp_review_bordercolor', true );
		$colors['total'] = get_post_meta( $post->ID, 'wp_review_total', true );
        
        if (!$colors['custom_colors']) {
			$colors = array_merge($colors, $options['colors']);
		}
        $colors = apply_filters('wp_review_colors', $colors, $post->ID);

        extract($colors, EXTR_SKIP);

		if( !$color ) $color = '#333333';

		$style = '<style type="text/css">';

		if ( 'star' == $type ) {

			$style .= '.wp-review-'.$post->ID.' .review-result-wrapper .review-result i { color: '.$color.'; opacity: 1; filter: alpha(opacity=100); }
				.wp-review-'.$post->ID.' .review-result-wrapper i{ color: '.$color.'; opacity: 0.50; filter: alpha(opacity=50); }';
				
		} elseif ( 'point' == $type ) {

			$style .= '.wp-review-'.$post->ID.'.bar-point .review-result { background-color: '.$color.'; }';

		} elseif ( 'percentage' == $type ) { 

			$style .= '.wp-review-'.$post->ID.'.percentage-point .review-result { background-color: '.$color.'; }';

		}

		$style .= '.wp-review-'.$post->ID.'.review-wrapper, .wp-review-'.$post->ID.' .review-title, .wp-review-'.$post->ID.' .review-desc p{ color: '.$fontcolor.';}
		.wp-review-'.$post->ID.' .review-list li, .wp-review-'.$post->ID.'.review-wrapper{ background: '.$bgcolor2.';}
		.wp-review-'.$post->ID.' .review-title, .wp-review-'.$post->ID.' .review-list li:nth-child(2n){background: '.$bgcolor1.';}

		.wp-review-'.$post->ID.'.bar-point .allowed-to-rate .review-result, .wp-review-'.$post->ID.'.percentage-point .allowed-to-rate .review-result{background: none;}
		.wp-review-'.$post->ID.' .review-total-star.allowed-to-rate a i, .wp-review-comment-field a i, .wp-review-comment-rating a i { color: '.$color.'; opacity: 0.50; filter: alpha(opacity=50); }
		.wp-review-'.$post->ID.'.bar-point .allowed-to-rate .review-result, .wp-review-'.$post->ID.'.percentage-point .allowed-to-rate .review-result{text-indent:0;}
		.wp-review-'.$post->ID.'.bar-point .allowed-to-rate .review-result i, .wp-review-'.$post->ID.'.percentage-point .allowed-to-rate .review-result i, .wp-review-'.$post->ID.' .mts-user-review-star-container .selected i, .wp-review-'.$post->ID.' .user-review-area .review-result i, .wp-review-comment-field .review-result i, .wp-review-comment-rating .review-result i { color: '.$color.'; opacity: 1; filter: alpha(opacity=100); }
		.wp-review-'.$post->ID.'.review-wrapper, .wp-review-'.$post->ID.' .review-title, .wp-review-'.$post->ID.' .review-list li, .wp-review-'.$post->ID.' .review-list li:last-child, .wp-review-'.$post->ID.' .user-review-area{border-color: '.$bordercolor.';}';
		
		if( $total == '' ){

		$style .= '.wp-review-'.$post->ID.' .user-review-area{border: 1px solid '.$bordercolor.'; margin-top: 0px;}
			.wp-review-'.$post->ID.' .review-desc{width: 100%;}
			.wp-review-'.$post->ID.'.review-wrapper{border: none; overflow: visible;}';
		}
		$style .= '</style>';

		return $style;
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

function getPreviousReview( $post_id, $user_id, $ip ) {
	if( is_numeric( $post_id ) && $post_id > 0 ){
		global $wpdb;
		global $blog_id;
		$table_name = $wpdb->prefix . MTS_WP_REVIEW_DB_TABLE;
		if (function_exists('is_multisite') && is_multisite()) {$table_name = $wpdb->base_prefix . MTS_WP_REVIEW_DB_TABLE;}
		if( is_numeric( $user_id ) && $user_id > 0 ){			
			$prevRates = $wpdb->get_row( $wpdb->prepare("SELECT rate FROM $table_name WHERE blog_id = '%d' AND post_id = '%d' AND user_id = '%d'", $blog_id, $post_id, $user_id) );			
			if( $prevRates->rate ) return $prevRates->rate; else return 0;
		}
		elseif( $ip != '' ){			
			$prevRates = $wpdb->get_row( $wpdb->prepare("SELECT rate FROM $table_name WHERE blog_id = '%d' AND post_id = '%d' AND user_ip = '%s' AND user_id = '0'", $blog_id, $post_id, $ip) );			
			if( $prevRates->rate ) return $prevRates->rate; else return 0;
		}
		else return false;
	}
	return false;
}

/**
*Get review with Ajax
*/
function mts_review_get_review(){
    // security
    check_ajax_referer( 'wp-review-security', 'nonce' );
    
	global $wpdb;
	
	$table_name = $wpdb->prefix . MTS_WP_REVIEW_DB_TABLE;
	if (function_exists('is_multisite') && is_multisite()) {$table_name = $wpdb->base_prefix . MTS_WP_REVIEW_DB_TABLE;}
	
	global $blog_id;
	$post_id = intval($_POST['post_id']);
	$user_id = intval($_POST['user_id']);
	
    //$ip = $_SERVER['REMOTE_ADDR'];
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	    $uip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	    $uip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	    $uip = $_SERVER['REMOTE_ADDR'];
	}
    
    if (!hasPreviousReview($post_id, $user_id, $uip)) {
    	$data = intval($_POST['review']);
    	
    	if( $rows_affected = $wpdb->insert( $table_name, array('blog_id' => $blog_id, 'post_id' => $post_id, 'user_id' => $user_id, 'user_ip' => $uip, 'rate' => $data, 'date' => current_time('mysql')) ) ){
    		$reviews = $wpdb->get_row( $wpdb->prepare("SELECT ROUND( AVG(rate) ,1 ) as reviewsAvg, COUNT(id) as reviewsNum FROM $table_name WHERE blog_id = '%d' AND post_id = '%d'", $blog_id, $post_id) );
    		echo $reviews->reviewsAvg.'|'.$reviews->reviewsNum;	
    	} else {
    	    echo 'MTS_REVIEW_DB_ERROR';
    	} 
    } else {
        echo 'MTS_REVIEW_DB_ERROR';
    }
	exit;
}

function wp_review_theme_defaults($new_options, $force_change = false) {
	global $pagenow;
	$opt_name = 'wp_review_options_'.wp_get_theme();
	$options = get_option('wp_review_options');
	if (empty($options)) $options = array();
	$options_updated = get_option( $opt_name );
	// if the theme was just activated OR options weren't updated yet
	if ( empty( $options_updated ) || $options_updated != $new_options || $force_change || ( isset( $_GET['activated'] ) && $pagenow == 'themes.php' )) {
		update_option( 'wp_review_options', array_merge($options, $new_options) );
		update_option( $opt_name, $new_options );
	}
}

function wp_review_get_all_image_sizes() {
	global $_wp_additional_image_sizes;
 
	$default_image_sizes = array( 'thumbnail', 'medium', 'large' );
	 
	foreach ( $default_image_sizes as $size ) {
		$image_sizes[$size]['width']	= intval( get_option( "{$size}_size_w") );
		$image_sizes[$size]['height'] = intval( get_option( "{$size}_size_h") );
		$image_sizes[$size]['crop']	= get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}
	
	if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		
	return $image_sizes;
}
?>