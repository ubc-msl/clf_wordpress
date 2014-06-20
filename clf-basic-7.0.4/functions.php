<?php
/**
 * The functions file is used to initialize everything in the theme.  It controls how the theme is loaded and 
 * sets up the supported features, default actions, and default filters.  If making customizations, users 
 * should create a child theme and make changes to its functions.php file (not this one).  Friends don't let 
 * friends modify parent theme files. ;)
 *
 * Child themes should do their setup on the 'after_setup_theme' hook with a priority of 11 if they want to
 * override parent theme features.  Use a priority of 9 if wanting to run before the parent theme.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package Hybrid
 * @subpackage Functions
 * @version 1.1.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
 * @link http://themehybrid.com/themes/hybrid
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load the core theme framework. */
require_once( trailingslashit( get_template_directory() ) . 'library/hybrid.php' );
new Hybrid();
// delete_option( 'ubc-collab-theme-options' );
/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme' , 'ubc_collab_before_setup_theme',8);
add_action( 'after_setup_theme', 'hybrid_theme_setup_theme' );
add_action( 'after_setup_theme', 'ubc_collab_after_setup_theme', 9);
add_action( 'after_setup_theme', 'ubc_collab_remove_generator', 9 );

function ubc_collab_remove_generator() {
remove_action( 'wp_head', 'wp_generator', 1 );
}

/**
 * Theme setup function.  This function adds support for theme features and defines the default theme
 * actions and filters.
 *
 * @since 0.9.0
 */
function hybrid_theme_setup_theme() {
	global $content_width;
	
	/* Get the theme prefix. */
	$prefix = hybrid_get_prefix();

	
	/* Add support for framework features. */
	add_theme_support( 'hybrid-core-menus', array( 'primary' ) );
	add_theme_support( 'hybrid-core-sidebars', array( 'primary', 'secondary', 'before-content', 'after-content', 'after-singular', 'subsidiary' ) );
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-favicon-support' );
        
	// add_theme_support( 'hybrid-core-theme-settings', array( 'about', 'footer' ) );
	// add_theme_support( 'hybrid-core-drop-downs' );
	// add_theme_support( 'hybrid-core-seo' );
	// add_theme_support( 'hybrid-core-template-hierarchy' );
	// add_theme_support( 'hybrid-core-deprecated' );    
	/* Add support for framework extensions. */
	add_theme_support( 'breadcrumb-trail' );
	// add_theme_support( 'custom-field-series' );
	add_theme_support( 'get-the-image' );
	// add_theme_support( 'post-stylesheets' );
    add_theme_support( 'loop-pagination' );
	/* Only add cleaner gallery support if not using child theme. Eventually, all child themes should support this. */
	// if ( 'hybrid' == get_stylesheet() )
	add_theme_support( 'cleaner-gallery' );
	
	// lets plugins know that we have style for twitter
	add_theme_support( 'tabs', 'twitter-bootstrap' );
	add_theme_support( 'accordions', 'twitter-bootstrap' );
	add_theme_support( 'grid', 'twitter-bootstrap' );

	/* Add support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );

	/* Register sidebars. */
	add_action( 'init', 'hybrid_theme_register_sidebars', 11 );

	/* Disables widget areas. */
	add_filter( 'sidebars_widgets', 'hybrid_theme_remove_sidebars' );

	/* Header actions. */
	// add_action( "{$prefix}_header", 'hybrid_site_title' );
	// add_action( "{$prefix}_header", 'hybrid_site_description' );

	/* Load the primary menu. */
	// add_action( "{$prefix}_after_header", 'hybrid_get_primary_menu' );

	/* Add the primary and secondary sidebars after the container. */
	//add_action( "{$prefix}_after_container", 'hybrid_get_primary' );
	
	//add_action( "{$prefix}_after_container", 'hybrid_get_secondary' );

	/* Add the breadcrumb trail and before content sidebar before the content. */
	
	
	add_action( "{$prefix}_before_content", 'hybrid_get_utility_before_content' );

	/* Add the title, byline, and entry meta before and after the entry. */
	add_action( "{$prefix}_before_entry", 'ubc_collab_entry_title' );
	add_action( "{$prefix}_before_entry", 'hybrid_byline' );
	add_action( "{$prefix}_after_entry", 'hybrid_entry_meta' );

	/* Add the after singular sidebar and custom field series extension after singular views. */
	add_action( "{$prefix}_after_singular", 'hybrid_get_utility_after_singular' );
	// add_action( "{$prefix}_after_singular", 'custom_field_series' );

	/* Add the after content sidebar and navigation links after the content. */
	add_action( "{$prefix}_after_content", 'hybrid_get_utility_after_content' );
	add_action( "{$prefix}_after_content", 'hybrid_navigation_links' );

	/* Add the subsidiary sidebar and footer insert to the footer. */
	add_action( "{$prefix}_before_footer", 'hybrid_get_subsidiary' );
	// add_action( "{$prefix}_footer", 'hybrid_footer_insert' );

	/* Add the comment avatar and comment meta before individual comments. */
	add_action( "{$prefix}_before_comment", 'hybrid_avatar' );
	add_action( "{$prefix}_before_comment", 'ubc_collab_comment_meta' );
	
	
	
	/* Add Hybrid theme-specific body classes. */
	add_filter( 'body_class', 'hybrid_theme_body_class' );
	
	add_filter( 'loop_pagination' , 'ubc_collab_loop_pagination' );
	
	add_filter( 'loop-shortcode-pagination' , 'ubc_collab_pagination_arg' );
	add_filter( 'loop_pagination_args', 'ubc_collab_pagination_arg');
	
	// Responsive Embed
	add_filter('oembed_dataparse', 'ubc_collab_responsive_embed', 10, 1);
	
	// rename Subsidiary to Footer
	add_filter( "{$prefix}_sidebar_args", 'ubc_collab_rename_subsidiary_sidebar', 10, 2 );
	
	/* Content width. */
	if ( empty( $content_width ) && !is_active_sidebar( 'primary' ) && !is_active_sidebar( 'secondary' ) )
		$content_width = 960;
	elseif ( empty( $content_width ) )
		$content_width = 620;
		
}

/**
 * ubc_collab_before_setup_theme function.
 * 
 * @access public
 * @return void
 */
function ubc_collab_before_setup_theme() {
	
	add_theme_support('clf');
	add_theme_support('collab-layout');
	add_theme_support('collab-navigation');
	
	require_once( get_template_directory() . '/inc/theme-options/class.theme-options.php' );
}

/**
 * ubc_collab_after_setup_theme function.
 * 
 * @access public
 * @return void
 */
function ubc_collab_after_setup_theme(){
	
	/** 
 	* Load the theme options that you would like to see on the theme options page 
 	* The order of the files inclided deteremins the order in which they appear in the theme options menu
 	*/
    
	require_if_theme_supports( 'clf', 	 get_template_directory(). '/inc/clf/class.clf.php' );
	require_if_theme_supports( 'collab-layout', 	 get_template_directory(). '/inc/layout/class.layout.php' );
	require_if_theme_supports( 'collab-navigation', 	 get_template_directory(). '/inc/navigation/class.navigation.php' );
	
}

/**
 * Register additional sidebars that are not a part of the core framework and are exclusive to this
 * theme.
 *
 * @since 0.9.0
 */
function hybrid_theme_register_sidebars() {

	/* Register the 404 template sidebar. */
	register_sidebar(
		array(
			'id' => 'error-404-template',
			'name' => __( '404 Template', 'hybrid' ),
			'description' => __( 'Replaces the default 404 error page content.', 'hybrid' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-inside">',
			'after_widget' => '</div></div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		)
	);
}



/**
 * Function for adding Hybrid theme <body> classes.
 *
 * @since 0.9.0
 */
function hybrid_theme_body_class( $classes ) {
	global $wp_query, $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;

	/* Singular post classes (deprecated). */
	if ( is_singular() ) {

		if ( is_page() )
			$classes[] = "page-{$wp_query->post->ID}"; // Use singular-page-ID

		elseif ( is_singular( 'post' ) )
			$classes[] = "single-{$wp_query->post->ID}"; // Use singular-post-ID
	}

	/* Browser detection. */
	$browsers = array( 'gecko' => $is_gecko, 'opera' => $is_opera, 'lynx' => $is_lynx, 'ns4' => $is_NS4, 'safari' => $is_safari, 'chrome' => $is_chrome, 'msie' => $is_IE );
	foreach ( $browsers as $key => $value ) {
		if ( $value ) {
			$classes[] = $key;
			break;
		}
	}

	/* Hybrid theme widgets detection. */
	foreach ( array( 'primary', 'secondary', 'subsidiary' ) as $sidebar )
		$classes[] = ( is_active_sidebar( $sidebar ) ) ? "{$sidebar}-active" : "{$sidebar}-inactive";

	if ( in_array( 'primary-inactive', $classes ) && in_array( 'secondary-inactive', $classes ) && in_array( 'subsidiary-inactive', $classes ) )
		$classes[] = 'no-widgets';

	/* Return the array of classes. */
	return $classes;
}


/**
 * ubc_collab_scripts function.
 * 
 * @access public
 * @return void
 */
function ubc_collab_scripts() {
	wp_enqueue_style( 'style', get_stylesheet_uri() );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'ubc_collab_scripts' );


/**
 * ubc_collab_entry_title function.
 * 
 * @access public
 * @return void
 */
function ubc_collab_entry_title($attr=""){
	
	$attr = shortcode_atts( array( 'permalink' => true ), $attr );

	$tag = is_singular() ? 'h1' : 'h2';
	$class = sanitize_html_class( get_post_type() ) . '-title entry-title';
	
	if ( is_singular() )
		$title = the_title( "<{$tag} class='{$class}'>", "</{$tag}>", false );
	else
		$title = the_title( "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>", "</a></{$tag}>", false );

	if ( empty( $title ) && !is_singular() )
		$title = "<{$tag} class='{$class}'><a href='" . get_permalink() . "'>" . __( '(Untitled)', 'hybrid-core' ) . "</a></{$tag}>";

	echo $title;

}

/**
 * ubc_collab_rename_subsidiary_sidebar function.
 * 
 * @access public
 * @return void
 */
function ubc_collab_rename_subsidiary_sidebar( $args, $sidebar ){
	if(  $sidebar == 'subsidiary' )
		$args['name'] = "Footer";
	
	return $args;
}
/**
 * Displays the post title.
 *
 * @since 0.5.0
 */
function hybrid_entry_title() {
	
	echo apply_atomic_shortcode( 'entry_title', '[entry-title]' );
}

/**
 * Default entry byline for posts.
 *
 * @since 0.5.0
 */
function hybrid_byline() {

	$byline = '';

	if ( 'post' == get_post_type() && 'link_category' !== get_query_var( 'taxonomy' ) )
		$byline = '<p class="byline">' . __( 'By [entry-author] on [entry-published] [entry-edit-link before=" | "]', 'hybrid' ) . '</p>';

	echo apply_atomic_shortcode( 'byline', $byline );
}

/**
 * Displays the default entry metadata.
 *
 * @since 0.5.0
 */
function hybrid_entry_meta() {

	$meta = '';

	if ( 'post' == get_post_type() )
		$meta = '<p class="entry-meta">' . __( '[entry-terms taxonomy="category" before="Posted in "] [entry-terms taxonomy="post_tag" before="| Tagged "] [entry-comments-link before="| "]', 'hybrid' ) . '</p>';

	elseif ( is_page() && current_user_can( 'edit_page', get_the_ID() ) )
		$meta = '<p class="entry-meta">[entry-edit-link]</p>';

	echo apply_atomic_shortcode( 'entry_meta', $meta );
}

/**
 * Function for displaying a comment's metadata.
 *
 * @since 0.7.0
 */
function ubc_collab_comment_meta() {
	echo apply_atomic_shortcode( 'comment_meta', '<div class="comment-meta comment-meta-data"><cite>[comment-author]</cite> [comment-published] <span class="comment-action">[comment-permalink before=""] [comment-edit-link before="| "] [comment-reply-link before="| "]<span></div>' );
}

/**
 * Loads the loop-nav.php template with backwards compability for navigation-links.php.
 *
 * @since 0.2.0
 * @uses locate_template() Checks for template in child and parent theme.
 */
function hybrid_navigation_links() {
	locate_template( array( 'navigation-links.php', 'loop-nav.php' ), true );
}

/**
 * Displays the footer insert from the theme settings page.
 *
 * @since 0.2.1
 */
function hybrid_footer_insert() {
	$footer_insert = hybrid_get_setting( 'footer_insert' );

	if ( !empty( $footer_insert ) )
		echo '<div class="footer-content footer-insert">' . do_shortcode( $footer_insert ) . '</div>';
}

/**
 * Removes all widget areas on the No Widgets page/post template.  No widget templates should come in
 * the form of $post_type-no-widgets.php.  This function also provides backwards compatibility with the old
 * no-widgets.php template.
 *
 * @since 0.9.0
 */
function hybrid_theme_remove_sidebars( $sidebars_widgets ) {
	global $wp_query;

	if ( is_singular() ) {
		$template = get_post_meta( $wp_query->post->ID, "_wp_{$wp_query->post->post_type}_template", true );

		if ( 'no-widgets.php' == $template || "{$wp_query->post->post_type}-no-widgets.php" == $template )
			$sidebars_widgets = array( false );
	}

	return $sidebars_widgets;
}

/**
 * Loads the sidebar-primary.php template.
 *
 * @since 0.2.2
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_primary() {
	get_sidebar( 'primary' );
}

/**
 * Loads the sidebar-secondary.php template.
 *
 * @since 0.2.2
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_secondary() {
	get_sidebar( 'secondary' );
}

/**
 * Loads the sidebar-primary-secondary.php template.
 *
 * @since 0.2.2
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_primary_secondary() {
	get_sidebar( 'primary-secondary' );
}

/**
 * Loads the sidebar-subsidiary.php template.
 *
 * @since 0.3.1
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_subsidiary() {
	get_sidebar( 'subsidiary' );
}

/**
 * Loads the sidebar-before-content.php template.
 *
 * @since 0.4.0
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_before_content() {
	get_sidebar( 'before-content' );
}

/**
 * Loads the sidebar-after-content.php template.
 *
 * @since 0.4.0
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_after_content() {
	get_sidebar( 'after-content' );
}

/**
 * Loads the sidebar-after-singular.php template.
 *
 * @since 0.7.0
 * @uses get_sidebar() Checks for the template in the child and parent theme.
 */
function hybrid_get_utility_after_singular() {
	get_sidebar( 'after-singular' );
}

/**
 * Loads the menu-primary.php template.
 *
 * @since 0.8.0
 * @uses get_template_part() Checks for template in child and parent theme.
 */
function hybrid_get_primary_menu() {
	get_template_part( 'menu', 'primary' );
}

/**
 * ubc_collab_pagination_arg function.
 * 
 * @access public
 * @param mixed $arg
 * @return void
 */
function ubc_collab_pagination_arg( $arg ){
	$arg['before'] = '<div class="pagination pagination-centered">';
	$arg['after']  = '</div>';
	$arg['next_text'] = 'Next <i class="icon-chevron-right icon"></i>';
	$arg['prev_text'] = '<i class="icon-chevron-left icon"></i> Previous';
	$arg['type'] = 'list';
	return $arg;

}
/**
 * ubc_collab_loop_pagination function.
 * change current to active
 * @access public
 * @param mixed $pagination
 * @return void
 */
function ubc_collab_loop_pagination( $pagination ) {
	return str_replace( "<li><span class='page-numbers current'", "<li class='active'><span class='page-numbers current'", $pagination );

}

/**
 * Responsive embed based on http://www.tjkdesign.com/articles/how-to-resize-videos-on-the-fly.asp
 * @access public
 * @param text $return
 * @return text $return
 */
function ubc_collab_responsive_embed ($return) {
	return '<div class="responsive-media">'.$return.'</div>';
}
/**
 * @since 0.7.0
 * @deprecated 0.9.0
 */
function hybrid_disable_styles() {
	_deprecated_function( __FUNCTION__, '0.9.0' );
}

/**
 * @since 0.4.0
 * @deprecated 0.9.0
 */
function hybrid_favicon() {
	_deprecated_function( __FUNCTION__, '0.9.0' );
}

/**
 * @since 0.4.0
 * @deprecated 0.9.0
 */
function hybrid_feed_link( $output, $feed ) {
	_deprecated_function( __FUNCTION__, '0.9.0' );
	return $output;
}

/**
 * @since 0.4.0
 * @deprecated 0.9.0
 */
function hybrid_other_feed_link( $link ) {
	_deprecated_function( __FUNCTION__, '0.9.0' );
	return $link;
}

?>