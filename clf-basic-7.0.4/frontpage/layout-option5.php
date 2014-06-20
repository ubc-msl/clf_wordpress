<?php
/**
* Frontpage Option 2
*
* Slider
* Content | Widget Area
*
**/

get_header(); // Loads the header.php template.

//Since this is the first frontpage layout option, the span will be 8 and 4
 ?>
</div>
<div class="row-fluid expand">
    
   <?php UBC_Collab_Spotlight::show(); ?>
          
</div>
<div class="row-fluid expand">

	<div id="content" class="hfeed span6">
		<?php UBC_Collab_Frontpage::content(); ?>
	</div><!-- .content .hfeed -->
	<?php get_template_part( 'sidebar-frontpage' ); // Loads the siderbar-frontpage.php template. ?>  
<?php get_footer(); // Loads the footer.php template. ?>