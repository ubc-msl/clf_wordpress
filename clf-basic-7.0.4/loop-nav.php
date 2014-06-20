<?php
/**
 * Navigation Links Template
 *
 * This template is used to show your your next/previous post links on singular pages and
 * the next/previous posts links on the home/posts page and archive pages. It also integrates
 * with the WP PageNavi plugin if activated.
 *
 *
 */
?>

	<?php if ( is_attachment() ) : ?>

		<div class="navigation-links">
			<ul class="pager">
				<li class="previous"><?php previous_post_link( '%link',  __( '<i class="icon-chevron-left icon"></i> Return to entry', 'hybrid' )  ); ?></li>
			</ul>
		</div>

	<?php elseif ( is_singular( 'post' ) && UBC_Collab_Theme_Options::get( 'next-and-previous-display' )) : 
		
		$nav_links_text = UBC_Collab_Navigation::get_previous_next_text();
	?>

		<div class="navigation-links">
			<ul class="pager">
				<li class="previous"><?php previous_post_link( '%link',   __( '<i class="icon-chevron-left icon"></i> ', 'hybrid' ).$nav_links_text['previous'], UBC_Collab_Theme_Options::get( 'next-and-previous-in-categories' )  ); ?></li>
				<li class="next"><?php next_post_link( '%link',  $nav_links_text['next'].__( ' <i class="icon-chevron-right icon"></i>', 'hybrid' ) , UBC_Collab_Theme_Options::get( 'next-and-previous-in-categories' ) ); ?></li>
			</ul>
		</div><!-- .navigation-links -->

	<?php elseif ( !is_singular() && function_exists( 'wp_pagenavi' ) ) : wp_pagenavi(); ?>

	<?php elseif ( !is_singular() && current_theme_supports( 'loop-pagination' ) ) :
	
	
	@loop_pagination( $defaults ); ?>

	<?php elseif ( !is_singular() && $nav = get_posts_nav_link( array( 'sep' => '', 'prelabel' => __( '<i class="icon-chevron-left icon"></i> Previous', 'hybrid' ), 'nxtlabel' =>  __( 'Next <i class="icon-chevron-right icon"></i>', 'hybrid' ) ) ) ) : ?>

		<div class="navigation-links">
			
			<?php echo $nav; ?>
		</div><!-- .navigation-links -->

	<?php endif; ?>