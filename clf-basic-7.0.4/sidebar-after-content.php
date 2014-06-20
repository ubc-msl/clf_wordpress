<?php
/**
 * After Content Sidebar Template
 *
 * The After Content sidebar template houses the HTML used for the 'Utility: After Content' 
 * sidebar. It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

	if ( is_active_sidebar( 'after-content' ) ) : ?>

		<div id="utility-after-content" class="sidebar utility <?php echo apply_filters('ubc_collab_sidebar_class',$sidebar_class,  'utility-after-content'); ?>">

			<?php dynamic_sidebar( 'after-content' ); ?>

		</div><!-- #utility-after-content .utility -->

	<?php endif; ?>