<?php
/**
 * Archive Template
 *
 * The archive template is basically a placeholder for archives that don't have a template file. 
 * Ideally, all archives would be handled by a more appropriate template according to the current
 * page context.
 *
 * @package Hybrid
 * @subpackage Template
 */

get_header(); ?>

	<div id="content" class="hfeed content <?php echo apply_filters( 'ubc_collab_content_class', $content_class, 'profile-cct' ); ?>">

		<?php do_atomic( 'before_content' ); // Before content hook ?>
		<div class="archive-info hentry">

			<h1 class="archive-title"><?php _e( 'People', 'hybrid' ); ?></h1>

		</div><!-- .archive-info -->

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<div class="profile-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->

			</div><!-- .hentry -->

			<?php endwhile; ?>

		<?php else: ?>

			<p class="no-data">
				<?php _e( 'Apologies, but no results were found.', 'hybrid' ); ?>
			</p><!-- .no-data -->

		<?php endif; ?>
		<?php do_atomic( 'after_content' ); // After content hook ?>

	</div><!-- .content .hfeed -->

<?php get_footer(); ?>