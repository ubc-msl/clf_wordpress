<?php
/**
 * Secondary Sidebar Template
 *
 * The Secondary sidebar template houses the HTML used for the 'Secondary' sidebar.
 * It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

if ( is_active_sidebar( 'secondary' ) ) : ?>
	
	<div id="secondary" class="sidebar aside <?php echo apply_filters('ubc_collab_sidebar_class', $sidebar_class,  'secondary' ); ?>">
		
		<?php do_atomic( 'before_secondary' ); // hybrid_before_secondary ?>

		<?php dynamic_sidebar( 'secondary' ); ?>

		<?php do_atomic( 'after_secondary' ); // hybrid_after_secondary ?>
	
	</div><!-- #secondary .aside -->

<?php endif; ?>