<?php
/**
 * Frontpage Sidebar Template
 *
 * The Frontpage sidebar template houses the HTML used for the 'Frontpage' sidebar.
 * It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

if ( is_active_sidebar( 'frontpage' ) ) : ?>
	
	<div id="frontpage-siderbar" class="sidebar aside <?php echo apply_filters('ubc_collab_sidebar_class', $sidebar_class,  'frontpage'); ?>">
		
		<?php do_atomic( 'before_frontpage_siderbar' ); // hybrid_before_primary ?>
		
		<?php dynamic_sidebar( 'frontpage' ); ?>
		
		<?php do_atomic( 'after_frontpage_siderbar' ); // hybrid_after_primary ?>

	</div><!-- #primary .aside -->

<?php endif; ?>