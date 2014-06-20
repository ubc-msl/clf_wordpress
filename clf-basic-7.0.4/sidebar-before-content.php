<?php
/**
 * Before Content Sidebar Template
 *
 * The Before Content sidebar template houses the HTML used for the 'Utility: Before Content' 
 * sidebar. It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

	if ( is_active_sidebar( 'before-content' ) ) : ?>

		<div id="utility-before-content" class="sidebar utility <?php echo apply_filters('ubc_collab_sidebar_class', $sidebar_class,  'utility-before-content'); ?>">

			<?php dynamic_sidebar( 'before-content' ); ?>

		</div><!-- #utility-before-content .utility -->

	<?php endif; ?>