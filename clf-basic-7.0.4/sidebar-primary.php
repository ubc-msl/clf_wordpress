<?php
/**
 * Primary Sidebar Template
 *
 * The Primary sidebar template houses the HTML used for the 'Primary' sidebar.
 * It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

if ( is_active_sidebar( 'primary' ) ) : ?>

	<div id="primary" class="sidebar aside <?php echo apply_filters('ubc_collab_sidebar_class', $sidebar_class,  'primary' ); ?>">

		<?php do_atomic( 'before_primary' ); // hybrid_before_primary ?>

		<?php dynamic_sidebar( 'primary' ); ?>

		<?php do_atomic( 'after_primary' ); // hybrid_after_primary ?>

	</div><!-- #primary .aside -->

<?php endif; ?>