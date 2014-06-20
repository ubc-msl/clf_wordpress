<?php
/**
 * Template Name: Logged In
 *
 * 
 *
 *
 */

get_header(); // Loads the header.php template. ?>

	<div id="content" class="hfeed content <?php echo apply_filters('ubc_collab_content_class', $content_class, 'singular' ); ?>">

		<?php do_atomic( 'before_content' ); // hybrid_before_content ?>

		<?php if ( have_posts() && is_user_logged_in() ) : while ( have_posts()   ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<?php do_atomic( 'before_entry' ); // hybrid_before_entry ?>

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<p class="page-links pages">' . __( 'Pages:', 'hybrid' ), 'after' => '</p>' ) ); ?>
				</div><!-- .entry-content -->

				<?php do_atomic( 'after_entry' ); // hybrid_after_entry ?>

			</div><!-- .hentry -->

			<?php do_atomic( 'after_singular' ); // hybrid_after_singular ?>

			<?php comments_template( '/comments.php', true ); // Loads the comments.php template ?>

			<?php endwhile; ?>
		<?php elseif ( have_posts() && !is_user_logged_in() ) : // If user is not logged in ?>
		
			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

				<?php do_atomic( 'before_entry' ); // hybrid_before_entry ?>

				<div class="entry-content">
					
					<p class="alert">
						<?php printf( __( 'You must be <a href="%1$s" title="Log in">logged in</a> to view the content of this page.', 'hybrid' ), wp_login_url( get_permalink() ) ); ?>

						<?php if ( get_option( 'users_can_register' ) ) printf( __( 'If you\'re not currently a member, please take a moment to <a href="%1$s" title="Register">register</a>.', 'hybrid' ), site_url( 'wp-login.php?action=register', 'login' ) ); ?>
					</p><!-- .alert -->
					
				</div><!-- .entry-content -->

				<?php do_atomic( 'after_entry' ); // hybrid_after_entry ?>

			</div><!-- .hentry -->

			<?php do_atomic( 'after_singular' ); // hybrid_after_singular ?>
		
		<?php else : ?>

			<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

		<?php endif; ?>

		<?php do_atomic( 'after_content' ); // hybrid_after_content ?>

	</div><!-- .content .hfeed -->

<?php get_footer(); // Loads the footer.php template. ?>