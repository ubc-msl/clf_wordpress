<?php 
/**
 * ubc-collab Layout Options
 *
 * @package ubc-collab
 * @since ubc-collab 1.0
 */
Class UBC_Collab_Layout {
	
	static $default_layout = 'l2-column-ms';
	static $layouts = array( 'l1-column', 'l2-column-ms', 'l2-column-sm', 'l3-column-msp', 'l3-column-pms', 'l3-column-psm' );
	static $meta_layout;
	static $post_meta_prefix = '_'; // we want the latout info to be hidden from view it should show up on in the custom fields
	static $prefix;
	static $sidebar_size = 4;
	static $content_size = 8;
	static $primary_sidebar_size;
	static $secondary_sidebar_size;
	static $is_default_layout;
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @return void
	 */
	function init(){
		
		self::$prefix = hybrid_get_prefix();
		
		add_action( 'admin_init', array(__CLASS__, 'admin' ) );
		
		// load the css and js that is needed for picking the layout
		add_action( 'admin_head-post-new.php', 		array( __CLASS__, 'layout_style' ) );
		add_action( 'admin_head-post.php', 			array( __CLASS__, 'layout_style' ) );
		add_action( 'ubc_collab_theme_options_ui', 	array( __CLASS__, 'layout_style' ) );
		
		add_filter('body_class', array( __CLASS__, 'body_class_filter' ) );
		// Options settings 
		// this function needs to be defined this function needs to 
		add_filter( 'ubc_collab_theme_options_validate', array( __CLASS__, 'validate' ), 10, 2 );
		// this function needs to run before we get options 
		add_filter( 'ubc_collab_default_theme_options', array( __CLASS__, 'default_options' ), 10, 1  );
		
		add_filter( 'ubc_collab_backward_compatibility_theme_options', array(__CLASS__, 'backward_compatibility'), 10, 2 );
		
		/* Use the admin_menu action to define the custom boxes */
		add_action( 'admin_menu', array( __CLASS__,'meta_box' ) );
		
		/* Use the save_post action to do something with the data entered */
		add_action( 'save_post', array( __CLASS__, 'box_save' ) );
		
		add_action( 'template_redirect', array( __CLASS__, 'create_layout' ), 5 );
		add_action( 'init', array( __CLASS__, 'add_header_widget_area' ), 5 );
		
	}
		
	/**
	 * start function.
	 * 
	 * @access public
	 * @return void
	 */
	function create_layout(){
		
		self::$is_default_layout = self::is_default_layout();	
		
		switch( UBC_Collab_Layout::get() ){
			
			case 'l1-column':
				self::$content_size = 12;
			
			break;
			
			case 'l2-column-ms':
				self::$sidebar_size = UBC_Collab_Layout::get('layout-sidebar-width');
				self::$content_size = UBC_Collab_Layout::get('layout-main-width-2-column');
				add_action(  self::$prefix."_after_container", 'hybrid_get_primary_secondary', 12 );
			break;
			
			case 'l2-column-sm':
				self::$sidebar_size = UBC_Collab_Layout::get('layout-sidebar-width');
				self::$content_size = UBC_Collab_Layout::get('layout-main-width-2-column');
				add_action(  self::$prefix."_before_container", 'hybrid_get_primary_secondary', 11 );
			break;
			/* three columns */
			case 'l3-column-msp':
				
				self::$primary_sidebar_size 	= UBC_Collab_Layout::get('layout-primary-width');
				self::$secondary_sidebar_size 	= UBC_Collab_Layout::get('layout-secondary-width');
				
				self::$content_size = UBC_Collab_Layout::get('layout-main-width-3-column');
				add_action(  self::$prefix."_after_container", 'hybrid_get_primary', 12 );
				add_action(  self::$prefix."_after_container", 'hybrid_get_secondary',12 );
				
			break;
			
			case 'l3-column-pms':
				self::$primary_sidebar_size 	= UBC_Collab_Layout::get('layout-primary-width');
				self::$secondary_sidebar_size 	= UBC_Collab_Layout::get('layout-secondary-width');
				
				self::$content_size = UBC_Collab_Layout::get('layout-main-width-3-column');
				
				add_action( self::$prefix."_before_container", 'hybrid_get_primary',11 );
				add_action( self::$prefix."_after_container", 'hybrid_get_secondary',12 );
			
			break;
			
			case 'l3-column-psm':
				self::$primary_sidebar_size 	= UBC_Collab_Layout::get('layout-primary-width');
				self::$secondary_sidebar_size 	= UBC_Collab_Layout::get('layout-secondary-width');
				
				self::$content_size = UBC_Collab_Layout::get('layout-main-width-3-column');
				add_action(  self::$prefix."_before_container", 'hybrid_get_primary',11 );
				add_action(  self::$prefix."_before_container", 'hybrid_get_secondary',12 );
			
			break;
		} /* end of switch */
		add_action( self::$prefix."_before_container", array(__CLASS__, 'add_row' ));
		add_action( self::$prefix."_after_container",array(__CLASS__, 'end_row' ),12);
		
		// apply the right width divs to the columns
		add_filter( 'ubc_collab_sidebar_class', array(__CLASS__, 'add_sidebar_class' ), 10, 2 );
		add_filter( 'ubc_collab_content_class', array(__CLASS__, 'add_contant_class' ) );
		
	}
	
	
	/**
	 * body_class_filter function.
	 * 
	 * @access public
	 * @param mixed $body_class
	 * @return void
	 */
	function body_class_filter( $body_class ){
		
		
		$defualt_class = UBC_Collab_Layout::get();
		$body_class [] = $defualt_class;
		if( 'l1-column' != $defualt_class )
		$body_class [] = substr( $defualt_class, 0, 9 ); 
		
		return $body_class;
		
	}
	/**
	 * add_row function.
	 * 
	 * @access public
	 * @return void
	 */
	function add_row(){
		echo '<div class="expand row-fluid" role="main">';
	}
	
	/**
	 * end_row function.
	 * 
	 * @access public
	 * @return void
	 */
	function end_row(){
		echo '</div>';
	}
	
	/**
	 * add_sidebar_class function.
	 * 
	 * @access public
	 * @param mixed $classes
	 * @return void
	 */
	function add_sidebar_class( $classes, $id  ) {
		switch( $id ){
			case 'utility-after-singular':
			case 'utility-after-content':
			case 'utility-before-content':
				return $classes;
			break;
			
			case 'primary':
				return $classes." span".self::$primary_sidebar_size;
			break;
			
			case 'secondary':
				return $classes." span".self::$secondary_sidebar_size;
			break;
			
			default:
				return $classes." span".self::$sidebar_size;
			break;
			
		
		
		}
		if ( in_array($id, array( "utility-before-content", "utility-after-content", "utility-after-singular" ) ) )
			return $classes;
		else if('primary' == $id )
			return $classes." span".self::$primary_sidebar_size;
		else
			return $classes." span".self::$sidebar_size;
	}
	
	/**
	 * add_contant_class function.
	 * 
	 * @access public
	 * @param mixed $classes
	 * @return void
	 */
	function add_contant_class( $classes ){
		return $classes." span".self::$content_size;
	
	}
	
	/**
	 * admin function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin(){
	
		add_settings_section(
			'layout', // Unique identifier for the settings section
			'Layout', // Section title (we don't want one)
			'__return_false', // Section callback (we don't want anything)
			'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
		);
		
		add_settings_field(
			'layout-options', // Unique identifier for the field for this section
			__( 'Default Layout', 'ubc_collab' ), // Setting field label
			array(__CLASS__,'layout_options'), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'layout' // Settings section. Same as the first argument in the add_settings_section() above
		);
		add_settings_field(
			'header-widget-area', // Unique identifier for the field for this section
			__( 'Header Widget', 'ubc_collab' ), // Setting field label
			array(__CLASS__,'header_widget_area'), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'layout' // Settings section. Same as the first argument in the add_settings_section() above
		);
		add_settings_field(
			'widget-areas', // Unique identifier for the field for this section
			__( 'Widget Areas', 'ubc_collab' ), // Setting field label
			array(__CLASS__,'widget_area'), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'layout' // Settings section. Same as the first argument in the add_settings_section() above
		);

	}
	

	
	/**
	 * layout_options function.
	 * 
	 * @access public
	 * @return void
	 */
	function layout_options(){
		 UBC_Collab_Layout::layout_view();
	}
	
	/**
	 * widget_areas function.
	 * 
	 * @access public
	 * @return void
	 */
	function header_widget_area(){ ?>
		<div><?php UBC_Collab_Theme_Options::checkbox( 'header-widget', 1, 'Enable Header widget area' ); ?></div>
		<?php
	}
	
	
	/**
	 * add_header_widget_area function.
	 * 
	 * @access public
	 * @return void
	 */
	function add_header_widget_area(){
		if( !UBC_Collab_Theme_Options::get( 'header-widget' ) )
			return;
		
		// register the frontpage widget
		register_sidebar( 
			array( 'name' => __('Header', 'hybrid'), 
			'id' => 'header-widget', 
			'description' => 'A widget area loaded in the header of the site.',
			'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">', 
			'after_widget' => '</div></div>', 
			'before_title' => '<h3 class="widget-title">', 
			'after_title' => '</h3>' ) );
	
		add_action( self::$prefix.'_after_header', array(__CLASS__, 'header_widget' ), 11 );
	
	}
	
	/**
	 * header_widget function.
	 * 
	 * @access public
	 * @return void
	 */
	function header_widget() {
		if ( is_active_sidebar( 'header-widget' ) ) : ?>
			<div class="row-fluid content expand">
				<div class="utility span12">
	
					<?php dynamic_sidebar( 'header-widget' ); ?>
	
				</div><!-- #header-widget .utility -->
			</div>
		<?php endif; 
	
	}
	/**
	 * widget_area function.
	 * 
	 * @access public
	 * @return void
	 */
	function widget_area() {
		$header_style = (UBC_Collab_Theme_Options::get( 'header-widget' ) ? "": ' style="display:none;" ')
		?>
		<div id="ubc-collab-widget-areas">
			<div id="header-widget-area" class="widget-area" <?php echo $header_style; ?>><span>Widget Area</span> Header Widget Area</div>
			<div id="ubc-collab-main-container">
				<?php UBC_Collab_Layout::layout_view_breakdown(UBC_Collab_Layout::get() ) ?>
			</div>
			<div id="ubc-collab-subsidiary-widget-area" class="widget-area"><span>Widget Area</span>Subsidiary Widget Area</div>
		</div>
		<?php
	}
	/* HELPER FUNCTIONS */
	
	
	/**
	 * layout_view function.
	 * 
	 * @access public
	 * @param string $class (default: 'UBC_Collab_Theme_Options')
	 * @return void
	 */
	function layout_view( $class = 'UBC_Collab_Theme_Options') { ?>
		<div id="layout-options">	
		    <?php if( 'UBC_Collab_Layout' == $class ): ?>
		    	<div class="option option-radio">
				<?php call_user_func( array( $class, 'radio' ), 'layout', 'default', 'Default' ); ?>
				<div class="content-shell">
					<span>Default</span>
				</div>
			</div>
		    <?php endif; ?>
			<div class="option option-radio">
				<?php  call_user_func( array( $class, 'radio' ), 'layout', 'l1-column', '1 Column' ); ?>
				<?php UBC_Collab_Layout::layout_view_breakdown( 'l1-column', $class ); ?>
			</div>
			<!-- 2 Columns -->
			<div class="option option-radio">
				<?php  call_user_func( array( $class, 'radio' ), 'layout', 'l2-column-ms', '2 Columns' ); ?>
				<?php UBC_Collab_Layout::layout_view_breakdown( 'l2-column-ms', $class ); ?>
			</div>
			<div class="option option-radio">
				<?php  call_user_func( array( $class, 'radio' ), 'layout', 'l2-column-sm', '2 Columns' ); ?>
				<?php UBC_Collab_Layout::layout_view_breakdown( 'l2-column-sm', $class ); ?>
			</div>
			<div class="break-point">
				<!-- 3 Columns -->
				<div class="option option-radio">
					<?php  call_user_func( array( $class, 'radio' ), 'layout', 'l3-column-msp', '3 Columns' ); ?>
					<?php UBC_Collab_Layout::layout_view_breakdown( 'l3-column-msp', $class ); ?>
				</div>
				<div class="option option-radio">
					<?php  call_user_func( array( $class, 'radio' ), 'layout', 'l3-column-pms', '3 Columns' ); ?>
					<?php UBC_Collab_Layout::layout_view_breakdown( 'l3-column-pms', $class ); ?>
				</div>
				<div class="option option-radio">
					<?php  call_user_func( array( $class, 'radio' ), 'layout', 'l3-column-psm', '3 Columns' ); ?>
					<?php UBC_Collab_Layout::layout_view_breakdown( 'l3-column-psm', $class ); ?>
				</div>
			</div>
		</div>
		<?php $layout_class = (self::$meta_layout ? self::$meta_layout : UBC_Collab_Layout::get() ); ?>
		<div id="layout-width" class="<?php echo esc_attr( $layout_class ); ?>" >
			
			<label>Content Width</label><br />
			<div id="content-width-range"></div>
			
			<?php if( 'UBC_Collab_Layout' == $class ): ?>
			<div id="ubc-collab-main-container">
				<?php UBC_Collab_Layout::layout_view_breakdown( UBC_Collab_Layout::get(), $class ) ?>
			</div>
		<?php endif; ?>
		</div>
		
		<?php 
		
	}
	
	/**
	 * layout_view_breakdown function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @param string $class (default: 'UBC_Collab_Theme_Options')
	 * @return void
	 */
	function layout_view_breakdown( $id, $class = 'UBC_Collab_Theme_Options' ) {
		
		switch( $id ){
			case 'l1-column': ?>
				<div class="content-shell">
					<?php UBC_Collab_Layout::main_content_layout_helper( $id, $class ); ?>
				</div>
				<?php
			break;
			case 'l2-column-ms':
				$sidebar 	= UBC_Collab_Layout::get( 'layout-sidebar-width' );
				?>
				<div class="content-shell two-column">
					<?php UBC_Collab_Layout::main_content_layout_helper( $id, $class ); ?>
					<div class="span<?php echo esc_attr( $sidebar ); ?>  side-column last-sidebar">
						
						<?php  call_user_func( array( $class, 'input' ), 'layout-sidebar-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Primary Sidebar</div>
						<div class="widget-area"><span>Widget Area</span> Secondary Sidebar</div>
					</div>
				</div>
				<?php
			break;
			case 'l2-column-sm':
				$sidebar 	= UBC_Collab_Layout::get( 'layout-sidebar-width' );
				?>
				<div class="content-shell two-column">
					<div class="span<?php echo esc_attr( $sidebar ); ?> side-column first-sidebar">
						<?php  call_user_func( array( $class, 'input' ), 'layout-sidebar-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Primary Sidebar</div>
						<div class="widget-area"><span>Widget Area</span> Secondary Sidebar</div>
					</div>
					<?php UBC_Collab_Layout::main_content_layout_helper( $id, $class ); ?>
				</div>
				<?php
			break;
			case 'l3-column-msp':
			
				$secondary_sidebar 	= UBC_Collab_Layout::get( 'layout-secondary-width' );
				$primary_sidebar 	= UBC_Collab_Layout::get( 'layout-primary-width' );
				?>
				<div class="content-shell three-column">
					<?php UBC_Collab_Layout::main_content_layout_helper( $id, $class ); ?>
					
					<div class="span<?php echo esc_attr( $primary_sidebar );?> side-column primary last-sidebar">
						<?php  call_user_func( array( $class, 'input' ), 'layout-primary-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Primary Sidebar </div>
					</div>
					<div class="span<?php echo esc_attr( $secondary_sidebar );?> side-column secondary last-sidebar">
						<?php  call_user_func( array( $class, 'input' ), 'layout-secondary-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Secondary Sidebar </div>
					</div>
				</div>
				<?php
			break;
			case 'l3-column-pms':
				$secondary_sidebar 	= UBC_Collab_Layout::get( 'layout-secondary-width' );
				$primary_sidebar 	= UBC_Collab_Layout::get( 'layout-primary-width' );
				?>
				<div class="content-shell three-column">
					<div class="span<?php echo esc_attr( $primary_sidebar );?> side-column primary first-sidebar">
						<?php  call_user_func( array( $class, 'input' ), 'layout-primary-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Primary Sidebar</div>
						
					</div>
					<?php UBC_Collab_Layout::main_content_layout_helper( $id, $class ); ?>
					<div class="span<?php echo esc_attr( $secondary_sidebar );?> side-column secondary last-sidebar">
						<?php  call_user_func( array( $class, 'input' ), 'layout-secondary-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Secondary Sidebar</div>
						
					</div>
				</div>
				<?php
			break;
			case 'l3-column-psm':
				$secondary_sidebar 	= UBC_Collab_Layout::get( 'layout-secondary-width' );
				$primary_sidebar 	= UBC_Collab_Layout::get( 'layout-primary-width' );
				?>
				<div class="content-shell three-column">
					
					<div class="side-column primary first-sidebar span<?php echo esc_attr( $primary_sidebar );?>">
						<?php  call_user_func( array( $class, 'input' ), 'layout-primary-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Primary Sidebar</div>
						
					</div>
					<div class="side-column secondary first-sidebar span<?php echo esc_attr( $secondary_sidebar );?>">
						<?php  call_user_func( array( $class, 'input' ), 'layout-secondary-width' ); ?>
						<div class="widget-area"><span>Widget Area</span> Secondary Sidebar</div>
						
					</div>
					<?php UBC_Collab_Layout::main_content_layout_helper( $id, $class ); ?>
				</div>
				<?php 
			break;
		
		
		}
	
	}
	
	/**
	 * main_content_layout_helper function.
	 * 
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	function main_content_layout_helper( $id, $class = 'UBC_Collab_Theme_Options'  ) { 
		
		switch ($id) {
			case 'l1-column':
				$name = 'layout-main-width-1-column';
			break;
			
			case 'l2-column-ms':
			case 'l2-column-sm':
				$name = 'layout-main-width-2-column';
			break;
			
			default:
				$name = 'layout-main-width-3-column';
			break;
			
		} 
		
		$size = UBC_Collab_Layout::get( $name );
	
		?>
		<div class="span<?php echo $size; ?> main-column">
			
			<?php  
			
			call_user_func( array( $class, 'input' ), $name ); ?>
			
			<div class="widget-area"><span>Widget Area</span> Utility Before Content</div>
			<div class="content-area-main">
				<strong>Page Title</strong><br />
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse eu neque eget nisi molestie rutrum sit amet eget metus. Vestibulum in nulla vitae nisi interdum egestas convallis eget mauris.</p>
				<p>Praesent elementum urna in ante gravida egestas. Nullam molestie, nulla nec viverra fermentum, eros quam lobortis lectus, ac eleifend libero purus id mauris. In fermentum nunc quis enim tincidunt id elementum nulla lacinia. Etiam in ultricies ante. Proin lacus lorem, viverra vitae convallis quis, facilisis eu nunc. </p>
			</div>
			<div class="widget-area"><span>Widget Area</span> Utility After Single</div>
			<div class="widget-area"><span>Widget Area</span> Utility After Content</div>
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
	function validate( $output, $input, $default = null ){
		if( is_null( $default ) )
			$default = self::$default_layout;
		$output['layout'] = ( isset( $input['layout'] ) && in_array( $input['layout'] ,  self::$layouts )? $input['layout'] : $default );
		$output['header-widget'] = ( $input['header-widget'] ? 1 : 0 ); 
		
		
		$range = range(1,12);
		
		$output['layout-primary-width'] 	= ( in_array( $input['layout-primary-width'], $range ) ? $input['layout-primary-width'] : 3);
		$output['layout-secondary-width'] 	= ( in_array( $input['layout-secondary-width'], $range ) ? $input['layout-secondary-width'] : 3);
		
		$remainder = 12 - $output['layout-primary-width'] - $output['layout-secondary-width'];
		
		$output['layout-main-width-3-column'] = ( in_array( $input['layout-main-width-3-column'], $range ) ? $input['layout-main-width-3-column'] : $remainder );
		
		
		$output['layout-sidebar-width'] 	= ( in_array( $input['layout-sidebar-width'], $range ) ? $input['layout-sidebar-width'] : 3);

		$remainder = 12 - $output['layout-sidebar-width'];
		$output['layout-main-width-2-column'] = ( in_array( $input['layout-main-width-2-column'], $range ) ? $input['layout-main-width-2-column'] : $remainder );
		
		return $output;
	
	}
	
	/**
	 * default function.
	 * 
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	function default_options( $options ) {
		
		$options['layout'] 						= self::$default_layout;
		$options['header-widget'] 				= 0;
		
		// one column
		$options['layout-main-width-1-column']  = 12;
		
		// 2 columns
		$options['layout-sidebar-width'] 		= 3;
		$options['layout-main-width-2-column']  = 9;
		
		// 3 columns
		$options['layout-primary-width'] 		= 3;
		$options['layout-secondary-width'] 		= 3;
		$options['layout-main-width-3-column']  = 6;
		
		return $options;
	}
	
	/**
	 * backward_compatibility function.
	 * 
	 * @access public
	 * @param mixed $options
	 * @param mixed $clf_base_options
	 * @return void
	 */
	function backward_compatibility( $options , $clf_base_options ) {
		$input['layout'] = $clf_base_options['layout'];
		$input['header-widget'] = ( isset($clf_base_options['display_options']['header']['widget-area']) ? 1: 0);
		
		return self::validate($options, $input ) ;
	}
	
	/* UI */
	
	/**
	 * layout_style function.
	 * 
	 * @access public
	 * @return void
	 */
	function layout_style() {
		global $post,$plugin_page;
		
		if( (is_object($post) && $post->post_type == 'page') || 'theme_options' == $plugin_page ):
			wp_enqueue_style( 'ubc-collab-layout', get_template_directory_uri().'/inc/layout/css/layout-panel.css' );
			wp_enqueue_script( 'ubc-collab-layout', get_template_directory_uri().'/inc/layout/js/layout-panel.js', array('jquery', 'jquery-ui-slider' ) );
		endif;
	}
	
	/* META BOX */
	
	/**
	 * meta_box function.
	 * 
	 * @access public
	 * @return void
	 */
	function meta_box() {
		add_meta_box( 'layout_box', __( 'Page Layout', 'clf_base' ), array( __CLASS__, 'box_inner') , 'page', 'side','high' );
	}
    
	/**
	 * box_inner function.
	 * 
	 * @access public
	 * @return void
	 */
	function box_inner( ){
		global $post;
		self::$meta_layout = get_post_meta( $post->ID, self::$post_meta_prefix.'layout_value', true);
		
		if( empty( self::$meta_layout ) )
			self::$meta_layout = 'default';
		
		UBC_Collab_Layout::layout_view( 'UBC_Collab_Layout' );
	}
	
	/**
	 * box_save function.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	function box_save( $post_id ) {
		global $post;
		
				
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
		
		// Check permissions
		if ( 'page' != $post->post_type || !current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		
		$validated = UBC_Collab_Layout::validate( array(), array( 
			'layout' 				=> $_POST[self::$post_meta_prefix.'layout_value'],
			'layout-sidebar-width' 	=> $_POST[self::$post_meta_prefix.'layout-sidebar-width_value'], 
			'layout-main-width-2-column' 	=> $_POST[self::$post_meta_prefix.'layout-main-width-2-column_value'], 
			
			'layout-primary-width' 			=> $_POST[self::$post_meta_prefix.'layout-primary-width_value'], 
			'layout-secondary-width' 		=> $_POST[self::$post_meta_prefix.'layout-secondary-width_value'], 
			'layout-main-width-3-column' 	=> $_POST[self::$post_meta_prefix.'layout-main-width-3-column_value']
			), '' );
		
		
		
		UBC_Collab_Layout::save_post_meta( $post_id, 'layout', $validated );
		
		UBC_Collab_Layout::save_post_meta( $post_id, 'layout-sidebar-width', 		$validated );
		UBC_Collab_Layout::save_post_meta( $post_id, 'layout-main-width-2-column', 	$validated );
		
		UBC_Collab_Layout::save_post_meta( $post_id, 'layout-primary-width', 		$validated );
		UBC_Collab_Layout::save_post_meta( $post_id, 'layout-secondary-width', 		$validated );
		UBC_Collab_Layout::save_post_meta( $post_id, 'layout-main-width-3-column', 	$validated );
		
		
		
	}
	
	/**
	 * save_post_mate function.
	 * 
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	function save_post_meta($post_id, $key, $data ) {
		
		$value = $data[$key];
		
		// var_dump($key.':'.$value);
		
		add_post_meta( $post_id, self::$post_meta_prefix.$key.'_value', $value, true) or update_post_meta( $post_id, self::$post_meta_prefix.$key.'_value', $value );
	
	}
	/**
	 * radio function.
	 * Overwrite the radio function to be used inside the Page Edit Screen 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $label
	 * @return void
	 */
	function radio( $key, $value, $label ) {
		
		$checked = checked( $value, self::$meta_layout, false );
		
		$name = self::$post_meta_prefix.$key.'_value';
		
		echo sprintf( '<label><input type="radio" name="%s" value="%s" %s  /> <span>%s</span></label>', esc_attr( $name ), esc_attr( $value ), $checked, esc_html( $label ) );
	}
	
	/**
	 * get function.
	 * 
	 * @access public
	 * @return void
	 */
	function get( $key = 'layout' ) {
		global $post, $current_screen;
		
		$test_array = array( 
					'layout', 
					'layout-main-width-1-column', 
					'layout-sidebar-width',
					'layout-main-width-2-column',
					'layout-primary-width',
					'layout-secondary-width',
					'layout-main-width-3-column'
					);
					
		if( in_array( $key, $test_array ) && @( is_page() || 'page' == $current_screen->post_type ) && !self::$is_default_layout ):
		
			$value = get_post_meta( $post->ID, self::$post_meta_prefix.$key.'_value', true );
			
			
			
			
			if( !empty($value) && 'default' != $value ):  // we don't want default values
				 
				return $value;
			endif;
			
		endif;
			
		return UBC_Collab_Theme_Options::get( $key );
	}
	
	function is_default_layout(){
		global $post;
		if( !is_page() ):
			return true;
		
		else:
			$value = get_post_meta( $post->ID, self::$post_meta_prefix.'layout_value', true );
			
			if( empty( $value ) || 'default' == $value )
				return true;
		endif;
		
		return false;
	}
	
	/**
	 * input function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	function input( $key ) {
	
		$name 	=  self::$post_meta_prefix.$key.'_value';
		$value 	=  UBC_Collab_Layout::get( $key );
		echo sprintf( '<input type="text" name="%s" value="%s"  />', esc_attr( $name ), esc_attr( $value ) );
	
	}
	

}

UBC_Collab_Layout::init();
