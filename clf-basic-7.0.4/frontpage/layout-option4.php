<?php
/**
* Frontpage Option 4
*
* Slider  
* Content 
*
**/


get_header(); // Loads the header.php template.

//Since this is the first frontpage layout option, the span will be 8 and 4
 ?>
</div>
<div class="row-fluid expand">
    <div class="span12">
        <?php UBC_Collab_Spotlight::show(); ?>
    </div>
</div>
<div class="row-fluid expand">
	<div id="content" class="hfeed span12">
           
		<?php do_atomic( 'before_content' ); // hybrid_before_content ?>
                
		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<?php //do_atomic( 'before_entry' ); // hybrid_before_entry -- Frontpage should not have a title unless the user specifies it. ?>
				<div class="entry-content"> 
					<?php the_content( sprintf( __( 'Continue reading %1$s', 'hybrid' ), the_title( ' "', '"', false ) ) ); ?>
					<?php wp_link_pages( array( 'before' => '<p class="page-links pages">' . __( 'Pages:', 'hybrid' ), 'after' => '</p>' ) ); ?>
				</div><!-- .entry-content -->

				<?php do_atomic( 'after_entry' ); // hybrid_after_entry ?>

			</div><!-- .hentry -->

			<?php if ( is_singular() ) { ?>

				<?php do_atomic( 'after_singular' ); // hybrid_after_singular ?>

				<?php comments_template( '/comments.php', true ); // Loads the comments.php template ?>

			<?php } ?>

			<?php endwhile; ?>

		<?php else: ?>

			<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

		<?php endif; ?>

		<?php do_atomic( 'after_content' ); // hybrid_after_content ?>

	</div><!-- .content .hfeed -->

<?php get_footer(); // Loads the footer.php template. ?>