<?php
/**
 * Archive Template
 *
 * The archive template is basically a placeholder for archives that don't have a template file. 
 * Ideally, all archives would be handled by a more appropriate template according to the current
 * page context.
 *
 *
 */

get_header(); // Loads the header.php template. ?>

	<div id="content" class="hfeed content <?php echo apply_filters('ubc_collab_content_class', $content_class, 'archive' ); ?>">

		<?php do_atomic( 'before_content' ); // hybrid_before_content ?>

		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
			
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php if( "profile_cct" != $post->post_type ): ?>
			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?> row-fluid">
				
				<?php if ( UBC_Collab_Theme_Options::get( 'display-thumbnail-on-archive') ): ?>
				<div class="span3">
				<?php get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'medium', 'width' => 198, 'height' => 132 ) ); ?>
				</div>
				<div class="span9">
				<?php else: ?>
				<div class="span12">
				<?php endif; ?>
				<?php do_atomic( 'before_entry' ); // hybrid_before_entry ?>

				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->

				<?php do_atomic( 'after_entry' ); // hybrid_after_entry ?>
				</div>
			</div><!-- .hentry -->
			<?php else: ?>
			
			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<div class="profile-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->

			</div><!-- .hentry -->

			<?php endif; ?>
			<?php endwhile; ?>

		<?php else: ?>

			<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

		<?php endif; ?>

		<?php do_atomic( 'after_content' ); // hybrid_after_content ?>

	</div><!-- .content .hfeed -->

<?php get_footer(); // Loads the footer.php template. ?>