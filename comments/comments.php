<?php
/**
 * The template for displaying the comments.
 *
 * This contains both the comments and the comment form.
 *
 * @package WP_Review
 */

// Do not delete these lines.
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die( __( 'Please do not load this page directly. Thanks!', 'wp-review' ) );
}

if ( post_password_required() ) { ?>
	<p class="nocomments"><?php _e( 'This post is password protected. Enter the password to view comments.', 'wp-review' ); ?></p>
	<?php
	return;
}
?>
<!-- You can start editing here. -->
<?php if ( have_comments() ) : ?>
	<div id="comments">
		<h4 class="total-comments"><?php comments_number( __( 'No Responses', 'wp-review' ), __( 'One Response', 'wp-review' ), __( '% Comments', 'wp-review' ) ); ?></h4>
		<ol class="commentlist">
			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // Are there comments to navigate through.
				?>
				<div class="navigation">
					<div class="alignleft"><?php previous_comments_link(); ?></div>
					<div class="alignright"><?php next_comments_link(); ?></div>
				</div>
				<?php
			}

			wp_list_comments( 'callback=wp_review_comments' );

			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // Are there comments to navigate through.
				?>
				<div class="navigation">
					<div class="alignleft"><?php previous_comments_link(); ?></div>
					<div class="alignright"><?php next_comments_link(); ?></div>
				</div>
			<?php } ?>
		</ol>
	</div>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
	<div id="commentsAdd">
		<div id="respond" class="box m-t-6">
			<?php
			global $aria_req;
			$comments_args = array(
				'title_reply'          => '<h4>' . __( 'Leave a Reply', 'wp-review' ) . '</h4>',
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'label_submit'         => __( 'Post Comment', 'wp-review' ),
				'comment_field'        => '<p class="comment-form-comment"><label for="comment" class="review-comment-field-msg">' . __( 'Comment*', 'wp-review' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
				'fields'               => apply_filters(
					'comment_form_default_fields',
					array(
						'author'  => '<p class="comment-form-author">' . ( $req ? '' : '' ) . '<label for="author" class="review-comment-field-msg">' . __( 'Name*', 'wp-review' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
						'email'   => '<p class="comment-form-email">' . ( $req ? '' : '' ) . '<label for="email" class="review-comment-field-msg">' . __( 'Email*', 'wp-review' ) . '</label><input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
						'url'     => '<p class="comment-form-url"><label for="url" class="review-comment-field-msg">' . __( 'Website', 'wp-review' ) . '</label><input id="url" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) . '" size="30" /></p>',
						'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" /><label for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.' ) . '</label></p>',
					)
				),
			);
			comment_form( $comments_args );
			?>
		</div>
	</div>
<?php endif; // if you delete this the sky will fall on your head. ?>
