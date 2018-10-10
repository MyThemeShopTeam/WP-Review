<?php
/**
 * Review box templates list
 * This file contains the list of review box templates used in plugin
 *
 * @package WP_Review
 * @since  3.0.0
 */

/**
 * Gets review templates.
 *
 * @return array
 */
function wp_review_get_box_templates() {
	$default = array(
		'title'                  => __( 'Default', 'wp-review' ),
		'image'                  => WP_REVIEW_ASSETS . 'images/default.jpg',
		'color'                  => '#1e73be',
		'inactive_color'         => '#95bae0',
		'fontcolor'              => '#555',
		'bgcolor1'               => '#e7e7e7',
		'bgcolor2'               => '#fff',
		'bordercolor'            => '#e7e7e7',
		'width'                  => 100, // In percentage.
		'align'                  => 'left',
		'custom_comment_colors'  => false,
		'rating_icon'            => 'fa fa-star',
		'comment_color'          => '#ffb300',
		'comment_inactive_color' => '#ffb300',
	);

	$templates = apply_filters(
		'wp_review_box_templates',
		array(
			'amazon'         => array(
				'title'          => __( 'Amazon', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/amazon.jpg',
				'color'          => '#ffbe01',
				'inactive_color' => '#f4f4f4',
				'fontcolor'      => '#111111',
				'bgcolor1'       => '#ffbe01',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#ffffff',
			),
			'aqua'           => array(
				'title'          => __( 'Aqua', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/aqua.jpg',
				'color'          => '#de5ea4',
				'inactive_color' => '#f5d9e8',
				'fontcolor'      => '#4e565b',
				'bgcolor1'       => '#8e74ea',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#dfdaea',
			),
			'blue'           => array(
				'title'          => __( 'Blue', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/blue.jpg',
				'color'          => '#689FCE',
				'inactive_color' => '#b1c6dc',
				'fontcolor'      => '#999999',
				'bgcolor1'       => '#f3f3f3',
				'bgcolor2'       => '#f3f3f3',
				'bordercolor'    => '#f3f3f3',
			),
			'darkside'       => array(
				'title'                  => __( 'Darkside', 'wp-review' ),
				'image'                  => WP_REVIEW_ASSETS . 'images/darkside.jpg',
				'color'                  => '#ed576c',
				'fontcolor'              => '#d0d0d0',
				'bgcolor1'               => '#20253b',
				'bgcolor2'               => '#20253b',
				'bordercolor'            => '#41465c',
				'inactive_color'         => '#41465c',
				'comment_inactive_color' => '',
			),
			'dash'           => array(
				'title'          => __( 'Dash', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/dash.jpg',
				'color'          => '#378bcb',
				'inactive_color' => '#8bbddb',
				'fontcolor'      => '#3f3f3f',
				'bgcolor1'       => '#378bcb',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#e8e8e8',
			),
			'edge'           => array(
				'title'          => __( 'Edge', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/edge.jpg',
				'color'          => '#f1c274',
				'inactive_color' => '#f1e3cd',
				'fontcolor'      => '#6a788a',
				'bgcolor1'       => '#e77e34',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#edf2f9',
			),
			'enterprise'     => array(
				'title'          => __( 'Enterprise', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/enterprise.jpg',
				'color'          => '#f8937e',
				'fontcolor'      => '#666666',
				'bgcolor1'       => '#f8937e',
				'bgcolor2'       => '#f7e4c5',
				'bordercolor'    => '#ead7bb',
				'inactive_color' => '#efbfa7',
			),
			'facebook'       => array(
				'title'          => __( 'Facebook', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/facebook.jpg',
				'color'          => '#4267b2',
				'fontcolor'      => '#333333',
				'bgcolor1'       => '#4267b2',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#e9eaed',
				'inactive_color' => '#b7ceff',
			),
			'fizzy'          => array(
				'title'          => __( 'Fizzy', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/fizzy.jpg',
				'color'          => '#71CD7B',
				'inactive_color' => '#cde6cf',
				'fontcolor'      => '#658694',
				'bgcolor1'       => '#F5F8F8',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#c8dde6',
			),
			'gamer'          => array(
				'title'          => __( 'Gamer', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/gamer.jpg',
				'color'          => '#d04622',
				'inactive_color' => '#e2d4d1',
				'fontcolor'      => '#262626',
				'bgcolor1'       => '#2c2d31',
				'bgcolor2'       => '#f2f2f3',
				'bordercolor'    => '#cececf',
			),
			'gravity'        => array(
				'title'                  => __( 'Gravity', 'wp-review' ),
				'image'                  => WP_REVIEW_ASSETS . 'images/gravity.jpg',
				'color'                  => '#f2b852',
				'fontcolor'              => '#243a23',
				'bgcolor1'               => '#243a24',
				'bgcolor2'               => '#f1f0ec',
				'bordercolor'            => '#e3e2df',
				'inactive_color'         => '#f9e097',
				'comment_inactive_color' => '',
			),
			'shell'          => array(
				'title'          => __( 'Shell', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/shell.jpg',
				'color'          => '#ec7d77',
				'inactive_color' => '#d1eaef',
				'fontcolor'      => '#459cad',
				'bgcolor1'       => '#ffffff',
				'bgcolor2'       => '#f2f8f9',
				'bordercolor'    => '#d1eaef',
			),
			'tabbed-layout'  => array(
				'title'          => __( 'Tabbed layout', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/tabbed.jpg',
				'color'          => '#1e73be',
				'inactive_color' => '#95bae0',
				'fontcolor'      => '#555',
				'bgcolor1'       => '#e7e7e7',
				'bgcolor2'       => '#fff',
				'bordercolor'    => '#e7e7e7',
			),
			'tabbed-layout2' => array(
				'title'          => __( 'Tabbed layout 2', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/tabbed2.jpg',
				'color'          => '#98ba3b',
				'inactive_color' => '#eff1e9',
				'fontcolor'      => '#413a43',
				'bgcolor1'       => '#615a63',
				'bgcolor2'       => '#ffffff',
				'bordercolor'    => '#f1edf1',
			),
			'xiaomi'         => array(
				'title'          => __( 'Xiaomi', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/xiaomi.jpg',
				'color'          => '#795548',
				'inactive_color' => '#bcaaa4',
				'fontcolor'      => '#474747',
				'bgcolor1'       => '#efebe9',
				'bgcolor2'       => '#efebe9',
				'bordercolor'    => '#efebe9',
			),
			'zine'           => array(
				'title'          => __( 'Zine', 'wp-review' ),
				'image'          => WP_REVIEW_ASSETS . 'images/zine.jpg',
				'color'          => '#04A9F5',
				'inactive_color' => '#B3E5FC',
				'fontcolor'      => '#6a6a6a',
				'bgcolor1'       => '#f3fafb',
				'bgcolor2'       => '#f3fafb',
				'bordercolor'    => '#ffffff',
			),
		)
	);

	foreach ( $templates as $index => $template ) {
		$templates[ $index ] = wp_parse_args( $template, $default );
	}

	return array( 'default' => $default ) + $templates;
}
