<?php
/**
 * Subsidiary Sidebar Template
 *
 * The Subsidiary sidebar template houses the HTML used for the 'Subsidiary' sidebar.
 * It will first check if the sidebar is active before displaying anything.
 *
 *
 * @link http://themehybrid.com/themes/hybrid/widget-areas
 */

if ( is_active_sidebar( 'subsidiary' ) ) : ?>
	<div class="row-fluid expand content">
		<div id="subsidiary" class="sidebar aside span-12">
	
			<?php do_atomic( 'before_subsidiary' ); // hybrid_before_subsidiary ?>
	
			<?php dynamic_sidebar( 'subsidiary' ); ?>
	
			<?php do_atomic( 'after_subsidiary' ); // hybrid_after_subsidiary ?>
	
		</div><!-- #subsidiary .aside -->
	</div><!-- .row-fluid -->
<?php endif; ?>