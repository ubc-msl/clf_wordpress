<?php
/**
 * Comment Template
 *
 * The comment template displays an individual comment. This can be overwritten by templates specific
 * to the comment type (comment.php, comment-{$comment_type}.php, comment-pingback.php, 
 * comment-trackback.php) in a child theme.
 *
 *
 */

	global $post, $comment;
?>

	<li id="comment-<?php comment_ID(); ?>" class="<?php hybrid_comment_class(); ?>">
		<div class="comment-wrap">
			<?php do_atomic( 'before_comment' ); // hybrid_before_comment ?>
	
			<div class="comment-text">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="alert moderation"><?php _e( 'Your comment is awaiting moderation.', 'hybrid' ); ?></p>
				<?php endif; ?>
	
				<?php comment_text( $comment->comment_ID ); ?>
			</div><!-- .comment-text -->
	
			<?php do_atomic( 'after_comment' ); // hybrid_after_comment ?>
		</div><!-- end of comment-wrap -->
	<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>