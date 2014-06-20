<?php
/**
 * After Singular Sidebar Template
 *
 * The After Singular sidebar template houses the HTML used for the 'Utility: After Singular' 
 * sidebar. It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

	if ( is_active_sidebar( 'after-singular' ) ) : ?>

		<div id="utility-after-singular" class="sidebar utility <?php echo apply_filters('ubc_collab_sidebar_class', $sidebar_class,  'utility-after-singular'); ?>">

			<?php dynamic_sidebar( 'after-singular' ); ?>

		</div><!-- #utility-after-singular .utility -->

	<?php endif; ?>