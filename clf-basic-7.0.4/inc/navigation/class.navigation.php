<?php 
/**
 * ubc-collab Navigation Options
 *
 * @package ubc-collab
 * @since ubc-collab 1.0
 */
Class UBC_Collab_Navigation{
	
	static $prefix;
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @return void
	 */
	function init(){
	
		require('class.bootstrap-walker-nav-menu.php');
		
		self::$prefix = hybrid_get_prefix();
		
		add_filter( 'breadcrumb_trail', array(__CLASS__, 'breadcrumb_rewrite') );
		add_filter( 'breadcrumb_trail_args', array(__CLASS__, 'breadcrumb_trail_args' ) );
		add_action( 'admin_init',array(__CLASS__, 'admin' ) );
		
		add_filter( 'ubc_collab_theme_options_validate', array( __CLASS__, 'validate' ), 10, 2 );
		add_filter( 'ubc_collab_default_theme_options', array( __CLASS__, 'default_options' ), 10, 1  );
		add_filter( 'ubc_collab_backward_compatibility_theme_options', array(__CLASS__, 'backward_compatibility'), 10, 2 );
		
		
		// the if statements have to come after the other filters have veen added other wise it doesn't work	
		if( UBC_Collab_Theme_Options::get( 'navigation-header-display' ) )
			add_action( self::$prefix.'_header', array(__CLASS__, 'header_menu'), 12 );
		
		if( UBC_Collab_Theme_Options::get( 'breadcrumb-display' ) )
			add_action( self::$prefix."_before_container", array(__CLASS__, 'breadcrumb') , 8 );
		
		// support for the menu shortcode	
		add_filter( 'menu-shortcode-attributes', array( __CLASS__, 'menu_shortcode_attr') );
		
		add_action( 'ubc_collab_theme_options_ui', array( __CLASS__, 'admin_ui' ) );
	}
	/*
	 * This function includes the css and js for this specifc admin option
	 *
	 * @access public
	 * @return void
	 */
	 function admin_ui(){
	 		wp_register_script( 'theme-option-navigation-script', get_template_directory_uri().'/inc/navigation/js/navigation.js' );
            wp_enqueue_script( 'theme-option-navigation-script', array('jquery'));
     }
	 function admin(){
		add_settings_section(
			'navigation', // Unique identifier for the settings section
			'Navigation', // Section title
			'__return_false', // Section callback (we don't want anything)
			'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
		);
		
        //Frontpage layout option
		add_settings_field(
			'navigation-layout', // Unique identifier for the field for this section
			'Style', // Setting field label
			array(__CLASS__,'navigation_layout'), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'navigation' // Settings section. Same as the first argument in the add_settings_section() above
		);
		
        //Frontpage layout option
		add_settings_field(
			'navigation-breadcrumb', // Unique identifier for the field for this section
			__( 'Breadcrumbs', 'ubc_collab' ), // Setting field label
			array(__CLASS__,'navigation_breadcrumb'), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'navigation' // Settings section. Same as the first argument in the add_settings_section() above
		); 
		
		add_settings_field(
			'next-and-previous-navigation', // Unique identifier for the field for this section
			__( 'Next and Previous Posts', 'ubc_collab' ), // Setting field label
			array(__CLASS__,'next_and_previous_navigation'), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'navigation' // Settings section. Same as the first argument in the add_settings_section() above
		); 
		
	
	}
	
	/**
	 * navigation_layout function.
	 * 
	 * @access public
	 * @return void
	 */
	function navigation_layout(){ 
		
		UBC_Collab_Theme_Options::checkbox( 'navigation-header-display', 1, 'Display the navigation' ); 
	
	}
	
	function navigation_breadcrumb(){
	
		?>
		<div>
		<?php UBC_Collab_Theme_Options::checkbox( 'breadcrumb-display', 1, 'Display the breadcrumbs.' ); ?>
		</div>	
		<div class="align-left"> <?php UBC_Collab_Theme_Options::text( 'breadcrumb-before-text', 'Before text'); ?> </div>
		<div class="align-left"> <?php UBC_Collab_Theme_Options::text( 'breadcrumb-home-text', 'Home text' ); ?> </div>
		<div class="align-left"> <?php UBC_Collab_Theme_Options::text( 'breadcrumb-seperator', 'Seperator' ); ?> </div>
		<div class="align-left"> <p>Child Page<br /><a href="#">Child Page</a> </p></div>
		<div class="align-left"> <?php UBC_Collab_Theme_Options::text( 'breadcrumb-after-text', 'After text' ); ?> </div>
		
		<?php 
		
	}
	
	/**
	 * navigation_layout function.
	 * 
	 * @access public
	 * @return void
	 */
	function next_and_previous_navigation(){ 
		
		UBC_Collab_Theme_Options::checkbox( 'next-and-previous-display', 1, 'Display previous and next post navigation' );
		?><br /> <?php
		UBC_Collab_Theme_Options::checkbox( 'next-and-previous-in-categories', 1, 'Next and Previous links stay inside the category' ); 
		?>
		
		<div class="explanation"><a href="#" class="explanation-help">info</a>
                <div>Previous and next post navigation allows users to easily move from one post to the next in a series of posts. This is especially useful for blogs or news websites where there is a long series of entries to navigate through.</div>
        </div>
        
		<p>Customized the wording in your previous and next post navigation:</p>
		<ul id="previous-next-options">
			<li><?php UBC_Collab_Theme_Options::radio( 'next-and-previous-text', 'default', ' Previous | Next ' );?></li>
			<li><?php UBC_Collab_Theme_Options::radio( 'next-and-previous-text', 'post-title', ' Previous Post Title | Next Post Title ' );?></li>
			<li><?php UBC_Collab_Theme_Options::radio( 'next-and-previous-text', 'custom', ' Custom ' );?></li>
		</ul>
		<div class="half">
		<?php UBC_Collab_Theme_Options::text( 'previous-post-text', 'Previous Post Text'); ?>
		</div>
		<div class="half">
		<?php UBC_Collab_Theme_Options::text( 'next-post-text', 'Next Post Text'); ?>
		</div>
		
		<?php
	
	}
	
	/**
	 * validate function.
	 * 
	 * @access public
	 * @param mixed $output
	 * @param mixed $input
	 * @return void
	 */
	function validate( $output, $input ){
		
		// todo: validate the input
		// breadcrumbs
		$output['breadcrumb-display'] 		= (bool) $input['breadcrumb-display'];
		$output['breadcrumb-before-text'] 	= ( empty( $input['breadcrumb-before-text'] ) ? '' : esc_html($input['breadcrumb-before-text']) );
		$output['breadcrumb-home-text'] 	= ( empty( $input['breadcrumb-home-text'] ) ? 'Home' : esc_html($input['breadcrumb-home-text']) );
		$output['breadcrumb-seperator'] 	= ( empty( $input['breadcrumb-seperator'] ) ? '/' : esc_html($input['breadcrumb-seperator']) );
		$output['breadcrumb-after-text'] 	= ( empty( $input['breadcrumb-after-text']) ? '' : esc_html($input['breadcrumb-after-text']) );
		
		// style
		$output['navigation-header-display'] = (bool)$input['navigation-header-display'];
		
		
		
		$output
		['next-and-previous-display'] = (bool)$input['next-and-previous-display'];
		$output['next-and-previous-text']    = ( in_array( $input['next-and-previous-text'], array('custom', 'default', 'post-title') ) ?  $input['next-and-previous-text'] : 'default' );
		$output['previous-post-text']        = ( empty( $input['previous-post-text']  ) ? 'Previous': esc_html( $input['previous-post-text'] ) );
		$output['next-post-text']            = ( empty( $input['next-post-text'] ) ? 'Next': esc_html( $input['next-post-text'] ) );
		
		$output['next-and-previous-in-categories'] = (bool)$input['next-and-previous-in-categories'];
		return $output;
		
	}
	
	/**
	 * default_options function.
	 * 
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	function default_options( $options ){
		if ( !is_array( $options ) ) { 
			$options = array();
		}
		
		$defaults = array(
			'breadcrumb-display' => true,
			'breadcrumb-before-text' => '',
			'breadcrumb-home-text' => 'Home',
			'breadcrumb-seperator' => '/',
			'breadcrumb-after-text' => '',
			'navigation-header-display'=> true
		);
		
		$options = array_merge( $options, $defaults );
		$options['navigation-header-display'] = true;
		
		$options['next-and-previous-display'] = true;
		$options['next-and-previous-text']    = 'default';
		$options['previous-post-text']        = 'Previous';
		$options['next-post-text']            = 'Next';
		$options['next-and-previous-in-categories'] = false;
		
		return $options;
	
	
	}
	
	function get_previous_next_text(){
		
		switch( UBC_Collab_Theme_Options::get( 'next-and-previous-text' ) ){
			case 'default':
				$previous_link_text = 'Previous';
				$next_link_text     = 'Next';
			break;
			
			case 'post-title':
				$previous_link_text = '%title';
				$next_link_text     = '%title';
			break;
				
			case 'custom':
				$previous_link_text = UBC_Collab_Theme_Options::get( 'previous-post-text' );
				$next_link_text     = UBC_Collab_Theme_Options::get( 'next-post-text' );
			break;
		}
		return array( 'next' =>$next_link_text, 'previous'=> $previous_link_text );
	}
	
	function backward_compatibility( $options, $clf_base_options ){
		
		$input['navigation-header-display'] = self::if_else( $clf_base_options['navigation']['top']['enable'], true );
		
		$input['breadcrumb-display'] 		= self::if_else( $clf_base_options['navigation']['breadcrumbs']['enable'], true );	
		$input['breadcrumb-before-text'] 	= self::if_else( $clf_base_options['navigation']['breadcrumbs']['before'], '' );	
		$input['breadcrumb-home-text'] 		= self::if_else( $clf_base_options['navigation']['breadcrumbs']['show_home'], 'Home' );	
		$input['breadcrumb-seperator'] 		= self::if_else( $clf_base_options['navigation']['breadcrumbs']['separator'], '/' );	
		$input['breadcrumb-after-text'] 	= self::if_else( $clf_base_options['navigation']['breadcrumbs']['after'], '' );	
		
		return self::validate( $options, $input );
	}
	
	function if_else( $option , $else ) {
		return ( isset ( $option ) ? $option : $else );
	}
	
	/**
	 * breadcrumb function.
	 * 
	 * @access public
	 * @return void
	 */
	function breadcrumb(){
		if ( current_theme_supports( 'breadcrumb-trail' ) )
		breadcrumb_trail( array( 'before' => '', 'front_page' => false ) );
	
	}
	
	/**
	 * breadcrumb_rewrite function.
	 * 
	 * @access public
	 * @param mixed $breadcrumb
	 * @return void
	 */
	function breadcrumb_rewrite( $breadcrumb ){
		$breadcrumb =  str_replace('<div class="breadcrumb-trail breadcrumbs"', '<div class="breadcrumb expand"', $breadcrumb );
		return str_replace('<span class="sep">', '<span class="divider">', $breadcrumb );

	}
	
	/**
	 * breadcrumb_trail_args function.
	 * 
	 * @access public
	 * @param mixed $arg
	 * @return void
	 */
	function breadcrumb_trail_args( $arg ) {
		 
		 $arg['separator'] 	= UBC_Collab_Theme_Options::get( 'breadcrumb-seperator' );
		 $arg['before'] 	= UBC_Collab_Theme_Options::get( 'breadcrumb-before-text' );
		 $arg['after'] 		= UBC_Collab_Theme_Options::get( 'breadcrumb-after-text' );
		 $arg['show_home'] 	= UBC_Collab_Theme_Options::get( 'breadcrumb-home-text' );
		
		return  $arg;
	}
	/**
	 * register_navigration function.
	 * 
	 * @access public
	 * @return void
	 */
	function register_navigration(){
		
	}
	
	/**
	 * header_menu function.
	 * 
	 * @access public
	 * @return void
	 */
	function header_menu() {
		
		?>
		<!-- UBC Unit Navigation -->
        <div id="ubc7-unit-menu" class="navbar expand" role="navigation">
            <div class="navbar-inner expand">
                <div class="container">
                 <?php wp_nav_menu( array( 'theme_location' => 'primary', 'walker' => new Bootstrap_Walker_Nav_Menu(), 'container_class' => 'nav-collapse collapse', 'container_id'=> 'ubc7-unit-navigation' , 'fallback_cb' => array(__CLASS__, 'pages_nav'), 'menu_class' => 'nav') ); ?>
                    
                </div>
            </div><!-- /navbar-inner -->
        </div><!-- /navbar -->
        <!-- End of UBC Unit Navigation -->
		<?php
	}
	
	/**
	 * pages_nav function.
	 * 
	 * @access public
	 * @param mixed $args
	 * @return void
	 */
	function pages_nav($args){
		
		$args['title_li'] = '';
		$args['walker']   = new Bootstrap_Walker_Nav_Page();
		$args['depth'] = 2;
		$args['echo'] = 0;
		
		?>
		<div id="ubc7-unit-navigation" class="nav-collapse collapse"><ul class="nav">
		<?php 
		
		$items = wp_list_pages( $args );
		
		$items_args = (object)  array('theme_location'=>'primary' );
		echo apply_filters( 'wp_nav_menu_items', $items, $items_args );
		
		?>
		</ul></div>
		<?php
	}
	/**
	 * menu_shortcode_attr function.
	 * 
	 * @access public
	 * @param mixed $attr
	 * @return void
	 */
	function menu_shortcode_attr( $attr ) {
		
		
		if( empty( $attr['walker']) )
			$attr['walker'] = new Bootstrap_Walker_Nav_Menu();
		
		if( 'wp_page_menu' == $attr['fallback_cb'] )
			$attr['fallback_cb'] = array('UBC_Collab_Navigation', 'pages_nav');
		
		return $attr;
	}
}
UBC_Collab_Navigation::init();
