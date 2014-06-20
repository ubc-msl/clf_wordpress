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

	<div id="primary-secondary" class="sidebar aside <?php echo apply_filters('ubc_collab_sidebar_class', $sidebar_class,  'sidebar'); ?>"> 
		<div id="primary">
	
			<?php do_atomic( 'before_primary' ); // hybrid_before_primary ?>
	
			<?php dynamic_sidebar( 'primary' ); ?>
	
			<?php do_atomic( 'after_primary' ); // hybrid_after_primary ?>
	
		</div><!-- #primary -->
		
		<?php if ( is_active_sidebar( 'secondary' ) ) : ?>
		<div id="secondary">
		
			<?php do_atomic( 'before_secondary' ); // hybrid_before_secondary ?>
	
			<?php dynamic_sidebar( 'secondary' ); ?>
	
			<?php do_atomic( 'after_secondary' ); // hybrid_after_secondary ?>
		
		</div><!-- #secondary -->
		<?php endif; ?>

	</div><!-- #primary-secondary .aside -->

<?php endif; ?>