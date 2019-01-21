<?php
/**
 * Admin functions for this plugin.
 *
 * @since     1.0
 * @copyright Copyright (c) 2013, MyThemesShop
 * @author    MyThemesShop
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @package   WP_Review
 */

/**
 * Register custom style for the meta box.
 *
 * @since 1.0
 * @since 3.0.0 Add select2
 *
 * @param string $hook_suffix Admin page hook suffix.
 */
function wp_review_admin_style( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post-new.php', 'edit-comments.php', 'post.php', 'edit.php', 'widgets.php', 'settings_page_wp-review/admin/options' ), true ) ) {
		return;
	}

	if ( 'customize' === get_current_screen()->id ) {
		return;
	}

	if ( 'settings_page_wp-review/admin/options' === $hook_suffix ) {
		wp_enqueue_style( 'fontawesome', WP_REVIEW_ASSETS . 'css/font-awesome.min.css', array(), '4.7.0' );

		wp_enqueue_script( 'js-cookie', WP_REVIEW_ASSETS . 'js/js.cookie.min.js', array(), '2.1.4', true );

		wp_enqueue_script( 'wp-review-admin-import', WP_REVIEW_URI . 'admin/assets/js/admin.import.js', array( 'jquery', 'wp-util' ), WP_REVIEW_PLUGIN_VERSION, true );

		wp_localize_script(
			'wp-review-admin-import',
			'wprImportVars',
			array(
				// translators: import source.
				'confirm'              => __( 'Are you sure you want to import from %s?', 'wp-review' ),
				'server_error'         => __( 'The server responded with an error. Try again.', 'wp-review' ),
				'confirmOptionsImport' => __( 'Are you sure you want to import options? All current options will be lost.', 'wp-review' ),
				'importOptionsNonce'   => wp_create_nonce( 'wp-review-import-options' ),
			)
		);
	}

	if ( in_array( $hook_suffix, array( 'settings_page_wp-review/admin/options', 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_style( 'jquery-ui', WP_REVIEW_URI . 'admin/assets/css/jquery-ui.min.css', array(), '1.12.1' );

		wp_enqueue_script( 'select2', WP_REVIEW_URI . 'admin/assets/js/select2.min.js', array( 'jquery' ), '4.0.6-rc.0', true );
		wp_enqueue_style( 'select2', WP_REVIEW_URI . 'admin/assets/css/select2.min.css', array(), '4.0.6-rc.0' );
	}

	// Load frontend css but not on the post editor screen.
	if ( stripos( 'post.php', $hook_suffix ) === false ) {
		wp_enqueue_style( 'wp_review-style', trailingslashit( WP_REVIEW_ASSETS ) . 'css/wp-review.css', array(), WP_REVIEW_PLUGIN_VERSION, 'all' );
	}

	wp_enqueue_style( 'wp-review-admin-style', WP_REVIEW_URI . 'admin/assets/css/admin.css', array( 'wp-color-picker' ), WP_REVIEW_PLUGIN_VERSION );

	$inline_css = '.column-wp_review_rating .pro-only-notice, .latestPost-review-wrapper .pro-only-notice { display: none; }';
	wp_add_inline_style( 'wp-review-admin-style', $inline_css );

	wp_enqueue_style( 'magnificPopup', WP_REVIEW_ASSETS . 'css/magnific-popup.css', array(), '1.1.0' );
	wp_enqueue_script( 'magnificPopup', WP_REVIEW_ASSETS . 'js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );

	if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_script( 'wp-review-rating-inputs', WP_REVIEW_URI . 'admin/assets/js/rating-inputs.js', array( 'jquery-ui-slider' ), WP_REVIEW_PLUGIN_VERSION, true );

		wp_enqueue_script(
			'wp-review-review-items',
			WP_REVIEW_URI . 'admin/assets/js/review-items.js',
			array( 'backbone', 'wp-review-admin-script', 'jquery-ui-sortable' ),
			WP_REVIEW_PLUGIN_VERSION,
			true
		);
	}

	wp_enqueue_script(
		'wp-review-admin-script',
		WP_REVIEW_URI . 'admin/assets/js/admin.js',
		array(
			'wp-color-picker',
			'jquery',
			'jquery-ui-core',
			'jquery-ui-slider',
			'jquery-ui-sortable',
			'jquery-ui-datepicker',
			'wp-util',
			'magnificPopup',
			'imagesloaded',
		),
		WP_REVIEW_PLUGIN_VERSION,
		true
	);

	wp_localize_script(
		'wp-review-admin-script',
		'wprVars',
		array(
			'ratingPermissionsCommentOnly' => WP_REVIEW_REVIEW_COMMENT_ONLY,
			'ratingPermissionsBoth'        => WP_REVIEW_REVIEW_ALLOW_BOTH,
			'imgframe_title'               => __( 'Select Image', 'wp-review' ),
			'imgbutton_title'              => __( 'Insert Image', 'wp-review' ),
			'imgremove_title'              => __( 'Remove Image', 'wp-review' ),
			'title'                        => __( 'Title', 'wp-review' ),
			'searchTerm'                   => __( 'Search term', 'wp-review' ),
			'searchLocation'               => __( 'Search location', 'wp-review' ),
			'limit'                        => __( 'Limit', 'wp-review' ),
			'searchRadius'                 => __( 'Radius', 'wp-review' ),
			'searchCategories'             => __( 'Categories', 'wp-review' ),
			'searchLocale'                 => __( 'Locale', 'wp-review' ),
			'searchOffset'                 => __( 'Offset', 'wp-review' ),
			'sort_by'                      => __( 'SortBy', 'wp-review' ),
			'searchPrice'                  => __( 'Price range', 'wp-review' ),
			'open_now'                     => __( 'Open now', 'wp-review' ),
			'attributes'                   => __( 'Attributes', 'wp-review' ),
			'businessId'                   => __( 'Business ID', 'wp-review' ),
			'locationLookup'               => __( 'Location lookup', 'wp-review' ),
			'placeId'                      => __( 'Place ID', 'wp-review' ),
			'placeType'                    => __( 'Place type', 'wp-review' ),
			'all'                          => __( 'All', 'wp-review' ),
			'establishments'               => __( 'Establishments', 'wp-review' ),
			'addresses'                    => __( 'Addresses', 'wp-review' ),
			'geocodes'                     => __( 'Geocodes', 'wp-review' ),
			'pageId'                       => __( 'Page ID', 'wp-review' ),
			'generateToken'                => __( 'Generate token', 'wp-review' ),
			'reviewIds'                    => __( 'Review IDs (separate by commas)', 'wp-review' ),
			'reviewPosts'                  => __( 'Review posts', 'wp-review' ),
			'queryType'                    => __( 'Query type', 'wp-review' ),
			'recentReviews'                => __( 'Recent reviews', 'wp-review' ),
			'topRated'                     => __( 'Top rated', 'wp-review' ),
			'mostVoted'                    => __( 'Most voted', 'wp-review' ),
			'categoryReviews'              => __( 'Category reviews', 'wp-review' ),
			'reviewTypesText'              => __( 'Review types', 'wp-review' ),
			'separateByCommas'             => __( 'separate by commas', 'wp-review' ),
			'categoryIds'                  => __( 'Category IDs', 'wp-review' ),
			'allowPagination'              => __( 'Allow pagination', 'wp-review' ),
			'numberOfReviews'              => __( 'Number of reviews', 'wp-review' ),
			'titleLength'                  => __( 'Title length (words)', 'wp-review' ),
			'thumbSize'                    => __( 'Thumb size', 'wp-review' ),
			'small'                        => __( 'Small', 'wp-review' ),
			'large'                        => __( 'Large', 'wp-review' ),
			'showDate'                     => __( 'Show date', 'wp-review' ),
			'reviewBox'                    => __( 'Review box', 'wp-review' ),
			'reviewTotal'                  => __( 'Review total', 'wp-review' ),
			'visitorRating'                => __( 'Visitor rating', 'wp-review' ),
			'reviewId'                     => __( 'Review ID', 'wp-review' ),
			'leaveReviewIdEmpty'           => __( 'Leave empty to use current review ID', 'wp-review' ),
			'insert'                       => __( 'Insert', 'wp-review' ),
			'cancel'                       => __( 'Cancel', 'wp-review' ),
			'reviewTypes'                  => wp_review_get_rating_types(),
			'globalReviewType'             => wp_review_option( 'review_type', 'none' ),
			'assetsUrl'                    => WP_REVIEW_ASSETS,
			'boxTemplates'                 => wp_review_get_box_templates(),
			'purgeRatingsNonce'            => wp_create_nonce( 'wpr_purge_ratings' ),
			'confirmPurgeRatings'          => esc_html__( 'Are you sure you want to do this?', 'wp-review' ),
			'importDemoConfirm'            => __( 'Are you sure you want to import demo?', 'wp-review' ),
			'importDemoDone'               => __( 'Importing proccess finished!', 'wp-review' ),
		)
	);

	wp_enqueue_script( 'mts-product-upgrade-checkout', 'https://mythemeshop.com/check/check.js', array( 'jquery' ), '1.0.0' );
}

add_action( 'admin_enqueue_scripts', 'wp_review_admin_style' );


/**
 * Gets list of icons.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wp_review_get_icons() {
	$icons = include WP_REVIEW_ADMIN . 'font-awesome-icons.php';
	/**
	 * Filters list of font icons.
	 *
	 * @since 3.0.0
	 *
	 * @param array $icons List of icons. View file `wp-review/admin/font-awesome-icons.php
	 */
	return apply_filters( 'wp_review_icons', $icons );
}


/**
 * Gets list of animations in.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wp_review_get_animations_in() {
	return array(
		__( 'Attention Seekers', 'wp-review' )  => array(
			'bounce'     => __( 'bounce', 'wp-review' ),
			'flash'      => __( 'flash', 'wp-review' ),
			'pulse'      => __( 'pulse', 'wp-review' ),
			'rubberBand' => __( 'rubberBand', 'wp-review' ),
			'shake'      => __( 'shake', 'wp-review' ),
			'swing'      => __( 'swing', 'wp-review' ),
			'tada'       => __( 'tada', 'wp-review' ),
			'wobble'     => __( 'wobble', 'wp-review' ),
			'jello'      => __( 'jello', 'wp-review' ),
		),
		__( 'Bouncing Entrances', 'wp-review' ) => array(
			'bounceIn'      => __( 'bounceIn', 'wp-review' ),
			'bounceInDown'  => __( 'bounceInDown', 'wp-review' ),
			'bounceInLeft'  => __( 'bounceInLeft', 'wp-review' ),
			'bounceInRight' => __( 'bounceInRight', 'wp-review' ),
			'bounceInUp'    => __( 'bounceInUp', 'wp-review' ),
		),
		__( 'Fading Entrances', 'wp-review' )   => array(
			'fadeIn'         => __( 'fadeIn', 'wp-review' ),
			'fadeInDown'     => __( 'fadeInDown', 'wp-review' ),
			'fadeInDownBig'  => __( 'fadeInDownBig', 'wp-review' ),
			'fadeInLeft'     => __( 'fadeInLeft', 'wp-review' ),
			'fadeInLeftBig'  => __( 'fadeInLeftBig', 'wp-review' ),
			'fadeInRight'    => __( 'fadeInRight', 'wp-review' ),
			'fadeInRightBig' => __( 'fadeInRightBig', 'wp-review' ),
			'fadeInUp'       => __( 'fadeInUp', 'wp-review' ),
			'fadeInUpBig'    => __( 'fadeInUpBig', 'wp-review' ),
		),
		__( 'Flippers', 'wp-review' )           => array(
			'flip'     => __( 'flip', 'wp-review' ),
			'flipInX'  => __( 'flipInX', 'wp-review' ),
			'flipInY'  => __( 'flipInY', 'wp-review' ),
			'flipOutX' => __( 'flipOutX', 'wp-review' ),
			'flipOutY' => __( 'flipOutY', 'wp-review' ),
		),
		__( 'Lightspeed', 'wp-review' )         => array(
			'lightSpeedIn'  => __( 'lightSpeedIn', 'wp-review' ),
			'lightSpeedOut' => __( 'lightSpeedOut', 'wp-review' ),
		),
		__( 'Rotating Entrances', 'wp-review' ) => array(
			'rotateIn'          => __( 'rotateIn', 'wp-review' ),
			'rotateInDownLeft'  => __( 'rotateInDownLeft', 'wp-review' ),
			'rotateInDownRight' => __( 'rotateInDownRight', 'wp-review' ),
			'rotateInUpLeft'    => __( 'rotateInUpLeft', 'wp-review' ),
			'rotateInUpRight'   => __( 'rotateInUpRight', 'wp-review' ),
		),
		__( 'Sliding Entrances', 'wp-review' )  => array(
			'slideInUp'    => __( 'slideInUp', 'wp-review' ),
			'slideInDown'  => __( 'slideInDown', 'wp-review' ),
			'slideInLeft'  => __( 'slideInLeft', 'wp-review' ),
			'slideInRight' => __( 'slideInRight', 'wp-review' ),
		),
		__( 'Zoom Entrances', 'wp-review' )     => array(
			'zoomIn'      => __( 'zoomIn', 'wp-review' ),
			'zoomInDown'  => __( 'zoomInDown', 'wp-review' ),
			'zoomInLeft'  => __( 'zoomInLeft', 'wp-review' ),
			'zoomInRight' => __( 'zoomInRight', 'wp-review' ),
			'zoomInUp'    => __( 'zoomInUp', 'wp-review' ),
		),
		__( 'Specials', 'wp-review' )           => array(
			'hinge'        => __( 'hinge', 'wp-review' ),
			'jackInTheBox' => __( 'jackInTheBox', 'wp-review' ),
			'rollIn'       => __( 'rollIn', 'wp-review' ),
			'rollOut'      => __( 'rollOut', 'wp-review' ),
		),
	);
}


/**
 * Gets list of animations out.
 *
 * @since 3.0.0
 *
 * @return array
 */
function wp_review_get_animations_out() {
	return array(
		__( 'Attention Seekers', 'wp-review' ) => array(
			'bounce'     => __( 'bounce', 'wp-review' ),
			'flash'      => __( 'flash', 'wp-review' ),
			'pulse'      => __( 'pulse', 'wp-review' ),
			'rubberBand' => __( 'rubberBand', 'wp-review' ),
			'shake'      => __( 'shake', 'wp-review' ),
			'swing'      => __( 'swing', 'wp-review' ),
			'tada'       => __( 'tada', 'wp-review' ),
			'wobble'     => __( 'wobble', 'wp-review' ),
			'jello'      => __( 'jello', 'wp-review' ),
		),
		__( 'Bouncing Exits', 'wp-review' )    => array(
			'bounceOut'      => __( 'bounceOut', 'wp-review' ),
			'bounceOutDown'  => __( 'bounceOutDown', 'wp-review' ),
			'bounceOutLeft'  => __( 'bounceOutLeft', 'wp-review' ),
			'bounceOutRight' => __( 'bounceOutRight', 'wp-review' ),
			'bounceOutUp'    => __( 'bounceOutUp', 'wp-review' ),
		),
		__( 'Fading Exits', 'wp-review' )      => array(
			'fadeOut'         => __( 'fadeOut', 'wp-review' ),
			'fadeOutDown'     => __( 'fadeOutDown', 'wp-review' ),
			'fadeOutDownBig'  => __( 'fadeOutDownBig', 'wp-review' ),
			'fadeOutLeft'     => __( 'fadeOutLeft', 'wp-review' ),
			'fadeOutLeftBig'  => __( 'fadeOutLeftBig', 'wp-review' ),
			'fadeOutRight'    => __( 'fadeOutRight', 'wp-review' ),
			'fadeOutRightBig' => __( 'fadeOutRightBig', 'wp-review' ),
			'fadeOutUp'       => __( 'fadeOutUp', 'wp-review' ),
			'fadeOutUpBig'    => __( 'fadeOutUpBig', 'wp-review' ),
		),
		__( 'Flippers', 'wp-review' )          => array(
			'flip'     => __( 'flip', 'wp-review' ),
			'flipInX'  => __( 'flipInX', 'wp-review' ),
			'flipInY'  => __( 'flipInY', 'wp-review' ),
			'flipOutX' => __( 'flipOutX', 'wp-review' ),
			'flipOutY' => __( 'flipOutY', 'wp-review' ),
		),
		__( 'Lightspeed', 'wp-review' )        => array(
			'lightSpeedIn'  => __( 'lightSpeedIn', 'wp-review' ),
			'lightSpeedOut' => __( 'lightSpeedOut', 'wp-review' ),
		),
		__( 'Rotating Exits', 'wp-review' )    => array(
			'rotateOut'          => __( 'rotateOut', 'wp-review' ),
			'rotateOutDownLeft'  => __( 'rotateOutDownLeft', 'wp-review' ),
			'rotateOutDownRight' => __( 'rotateOutDownRight', 'wp-review' ),
			'rotateOutUpLeft'    => __( 'rotateOutUpLeft', 'wp-review' ),
			'rotateOutUpRight'   => __( 'rotateOutUpRight', 'wp-review' ),
		),
		__( 'Sliding Exits', 'wp-review' )     => array(
			'slideOutUp'    => __( 'slideOutUp', 'wp-review' ),
			'slideOutDown'  => __( 'slideOutDown', 'wp-review' ),
			'slideOutLeft'  => __( 'slideOutLeft', 'wp-review' ),
			'slideOutRight' => __( 'slideOutRight', 'wp-review' ),
		),
		__( 'Zoom Exits', 'wp-review' )        => array(
			'zoomOut'      => __( 'zoomOut', 'wp-review' ),
			'zoomOutDown'  => __( 'zoomOutDown', 'wp-review' ),
			'zoomOutLeft'  => __( 'zoomOutLeft', 'wp-review' ),
			'zoomOutRight' => __( 'zoomOutRight', 'wp-review' ),
			'zoomOutUp'    => __( 'zoomOutUp', 'wp-review' ),
		),
		__( 'Specials', 'wp-review' )          => array(
			'hinge'        => __( 'hinge', 'wp-review' ),
			'jackInTheBox' => __( 'jackInTheBox', 'wp-review' ),
			'rollIn'       => __( 'rollIn', 'wp-review' ),
			'rollOut'      => __( 'rollOut', 'wp-review' ),
		),
	);
}


/**
 * Animation dropdown.
 *
 * @since 3.0.0
 *
 * @param string $id    Element ID.
 * @param string $name  Element name.
 * @param string $value Selected value.
 * @param bool   $exit  Show only exit animations.
 */
function wp_review_animations_dropdown( $id = '', $name = '', $value = '', $exit = false ) {
	$animations = array( '' => esc_html__( 'No Animation', 'wp-review' ) );

	if ( ! $exit ) {
		$animations += wp_review_get_animations_in();
	} else {
		$animations += wp_review_get_animations_out();
	}

	printf( '<select id="%1$s" name="%2$s" class="js-select2">', esc_attr( $id ), esc_attr( $name ) );
		wp_review_print_select_options( $animations, $value );
	echo '</select>';
}


/**
 * Prints select options.
 *
 * @since 3.0.0
 *
 * @param array  $options Options.
 * @param string $value   Select value.
 */
function wp_review_print_select_options( $options, $value ) {
	foreach ( $options as $key => $text ) {
		if ( is_array( $text ) ) {
			printf( '<optgroup label="%s">', esc_attr( $key ) );
			wp_review_print_select_options( $text, $value );
			echo '</optgroup>';
		} else {
			printf(
				'<option value="%1$s" %3$s>%2$s</option>',
				esc_attr( $key ),
				esc_html( $text ),
				selected( $value, $key, false )
			);
		}
	}
}


/**
 * Add settings link on plugin page.
 *
 * @param  array $links Plugins setting links.
 * @return array
 */
function wpreview_plugin_settings_link( $links ) {
	$hide = wp_review_network_option( 'hide_global_options_' );
	if ( ! $hide ) {
		$settings_link = '<a href="options-general.php?page=wp-review/admin/options.php">' . __( 'Settings', 'wp-review' ) . '</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . WP_REVIEW_PLUGIN_BASE, 'wpreview_plugin_settings_link' );


/**
 * Adds editor buttons.
 *
 * @param  array $buttons Editor buttons.
 * @return array
 */
function wp_review_editor_buttons( $buttons ) {
	array_push( $buttons, 'wpreviewpro' );
	return $buttons;
}
add_filter( 'mce_buttons', 'wp_review_editor_buttons' );


/**
 * Adds editor plugin.
 *
 * @param  array $plugin_array Editor plugins.
 * @return array
 */
function wp_review_editor_js( $plugin_array ) {
	if ( is_admin() ) {
		$plugin_array['wp_review'] = WP_REVIEW_URI . 'admin/assets/js/editor-plugin.js';
	}
	return $plugin_array;
}
add_filter( 'mce_external_plugins', 'wp_review_editor_js' );


/**
 * Normalizes option value
 * Convert string as 'true' and 'false' to boolean value.
 *
 * @since 3.0.0
 *
 * @param  mixed $value Option value.
 * @return mixed
 */
function wp_review_normalize_option_value( $value ) {
	if ( 'true' === $value ) {
		return true;
	}
	if ( 'false' === $value ) {
		return false;
	}
	return $value;
}


/**
 * Gets plugin options export code.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wp_review_get_options_export_code() {
	$options = get_option( 'wp_review_options', array() );
	return wp_json_encode( $options );
}


/**
 * Imports plugin options.
 *
 * @since 3.0.0
 *
 * @param string $code Export code.
 * @return bool
 */
function wp_review_import_options( $code ) {
	$options = json_decode( $code, true );
	if ( ! $options ) {
		return false;
	}
	update_option( 'wp_review_options', $options );
	return true;
}


/**
 * Prints pro text.
 *
 * @param bool $strip_br Strip `<br>` tag.
 */
function wp_review_print_pro_text( $strip_br = false ) {
	$br = ! $strip_br ? '<br>' : '';
	echo $br . '<small class="wp-review-pro-text">' . esc_html__( 'Pro feature', 'wp-review' ) . '</small>'; // WPCS: xss ok.
}


/**
 * Prints the Pro version popup.
 */
function wp_review_print_pro_popup() {
	if ( ! wp_script_is( 'wp-review-admin-script', 'enqueued' ) ) return;
	?>
	<div id="wp-review-pro-popup-notice" class="mfp-hide">
		<div class="pro-popup-title"><?php esc_html_e( 'Buy WP Review Pro', 'wp-review' ); ?></div>
		<div class="pro-popup-content">
			<a href="https://mythemeshop.com/plugins/wp-review-pro/?utm_source=WP+Review&utm_medium=Popup&utm_content=WP+Review+Pro+LP&utm_campaign=WordPressOrg" target="_blank"><img class="pro-popup-image" src="<?php echo esc_url( WP_REVIEW_URI . 'admin/assets/images/wp-review-pro.jpg' ); ?>" /></a>
			<h2 class="pro-notice-header"><?php esc_html_e( 'Like WP Review Plugin? You will LOVE WP Review Pro!', 'wp-review' ); ?></h2>
			<p><?php esc_html_e( '15 new review box templates, 15 new Schema types, commment reviews, user can rate each feature, review popups, review notification bars, custom width, 9 new custom widgets, Google reviews, Facebook reviews, Yelp reviews and much more...', 'wp-review' ); ?></p>
			<a id="wp-review-pro-purchase-link" class="button-primary" href="#"><?php esc_html_e( 'Buy WP Review Pro', 'wp-review' ); ?></a>
		</div>
	</div>

	<style>
		.mfp-hide { display: none !important; }
		#wp-review-pro-popup-notice {
			background: #FFF;
			width: auto;
			max-width: 630px;
			margin: 20px auto;
			position: relative;
		}
		#wp-review-pro-popup-notice .mfp-close { background-color: transparent; }
		#wp-review-pro-popup-notice .pro-popup-title {
			padding: 0 10px;
			line-height: 29px;
			font-weight: 600;
			background: #fcfcfc;
			border-bottom: 1px solid #ddd;
		}
		#wp-review-pro-popup-notice .pro-popup-content {
			padding: 2px 15px 15px;
			min-height: 195px;
		}
		#wp-review-pro-popup-notice .pro-popup-content:after {
			content: " ";
			display: block;
			height: 0;
			visibility: hidden;
			clear: both;
		}
		#wp-review-pro-popup-notice .pro-popup-image {
			width: 150px;
			float: right;
			margin: 10px;
		}
	</style>
	<script>
		var WPRPHandler = MTS.Checkout.configure();
		jQuery('#wp-review-pro-purchase-link').on('click', function (e) {
			var $button = jQuery(this);
			e.preventDefault();
			WPRPHandler.open({
				action:     'buy',
				add_to_cart: 8678,
				success: function (response) {
					$button.remove();
				}
			});
		});
	</script>
	<?php
}
add_action( 'admin_footer', 'wp_review_print_pro_popup' );
