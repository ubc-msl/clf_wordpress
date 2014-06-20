<?php
/**
* Frontpage Option 3
*
* Slider  | Widget Area
* Content | Widget Area
*
**/

get_header(); // Loads the header.php template.

//Since this is the first frontpage layout option, the span will be 8 and 4
 ?>
</div>
<div class="row-fluid expand">
    
    <div class="span8">
        <?php UBC_Collab_Spotlight::show(); ?>
        <div id="content" class="hfeed">
			<?php UBC_Collab_Frontpage::content(); ?>
		             
		</div><!-- .content .hfeed -->
    </div>
        
<?php get_template_part( 'sidebar-frontpage' ); // Loads the siderbar-frontpage.php template. ?>    
	

<?php get_footer(); // Loads the footer.php template. ?>