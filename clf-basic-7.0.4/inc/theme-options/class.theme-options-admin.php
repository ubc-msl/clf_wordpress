<?php 



Class UBC_Collab_Theme_Options_Admin {
	
	/**
	 * init function.
	 * 
	 * @access public
	 * @return void
	 */
	function init(){
		add_action( 'init', array( __CLASS__,'start' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'wp_before_admin_bar_render', array( __CLASS__,'admin_bar_render' ) );
		
		add_filter( 'option_page_capability_ubc_collab_options', array( __CLASS__, 'page_capability' ) );
		
		// ajax functions
		add_action( 'wp_ajax_ubc-collab-save-options', 			array(__CLASS__, 'ajax_save_options' ) );
		
		
	}
	
	function start(){
	 
		
		if( current_theme_supports( 'import-export-settings' ) ):
			add_action( 'admin_init', array( __CLASS__, 'add_import_export_options') , 15  );
			add_action( 'wp_ajax_ubc-collab-import-options', 		array(__CLASS__,'ajax_import' ) );
			add_action( 'wp_ajax_ubc-collab-export', 		array(__CLASS__,'ajax_export' ) );
			add_action( 'wp_ajax_nopriv_ubc-collab-export', 		array(__CLASS__,'ajax_export' ) );
		endif;
	
	}
	/**
	 * Add our theme options page to the admin menu.
	 *
	 * This function is attached to the admin_menu action hook.
	 *
	 * @since ubc-collab 1.0
	 */
	function add_page() {
		$theme_page = add_theme_page(
			__( 'Theme Options', 'ubc_collab' ),   // Name of page
			__( 'Theme Options', 'ubc_collab' ),   // Label in menu
			'edit_theme_options',          // Capability required
			'theme_options',               // Menu slug, used to uniquely identify the page
			array( __CLASS__, 'admin_page') // Function that renders the options page
		);
		
		add_action('admin_print_styles-' . $theme_page, array( __CLASS__, 'admin_ui' ) );
		
	}
	
	/**
	 * admin_bar_render function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_bar_render(){
		global $wp_admin_bar;
		// we can add a submenu item too
		$wp_admin_bar->add_menu( array(
	        'parent' => 'appearance',
	        'id' => 'clf_theme',
	        'title' => __('Theme Options', 'ubc_collab'),
	        'href' => admin_url( 'themes.php?page=theme_options')
   		 ) );
	}
	
	
	/**
	 * export_options function.
	 * 
	 * @access public
	 * @return void
	 */
	function add_import_export_options() {
		
		/* Export Options */
		add_settings_section(
				'export', // Unique identifier for the settings section
				'Export', // Section title (we don't want one)
				'__return_false', // Section callback (we don't want anything)
				'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			);
			
		add_settings_field(
			'export-options', // Unique identifier for the field for this section
			__( '', 'ubc_collab' ), // Setting field label
			array( __CLASS__,'options_export' ), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'export' // Settings section. Same as the first argument in the add_settings_section() above
		);
		
		/* Import Options */
		add_settings_section(
				'import', // Unique identifier for the settings section
				'Import', // Section title (we don't want one)
				'__return_false', // Section callback (we don't want anything)
				'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			);
			
		add_settings_field(
			'import-options', // Unique identifier for the field for this section
			__( '', 'ubc_collab' ), // Setting field label
			array( __CLASS__,'options_import' ), // Function that renders the settings field
			'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
			'import' // Settings section. Same as the first argument in the add_settings_section() above
		);
		
	
	}
	
	/**
	 * admin_ui function.
	 * 
	 * @access public
	 * @return void
	 */
	function admin_ui(){
		
		// added colour picker options
		wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
        
		wp_enqueue_style( 'ubc-collab-js-ui', get_template_directory_uri().'/inc/theme-options/css/theme-options.css' );
		wp_enqueue_script( 'ubc-collab-js-ui', get_template_directory_uri().'/inc/theme-options/js/theme-options.js', array('jquery','jquery-ui-tabs','wp-color-picker') );
		
		$nonce_import = wp_create_nonce( 'import-UBC-COLLAB' );
		$nonce_save = wp_create_nonce( 'save-UBC-COLLAB' );
		wp_localize_script( 'ubc-collab-js-ui', 'UBC_COLLAB', apply_filters( 'ubc_collab_theme_options_localize', 
				array( 
					'nonce_import' => $nonce_import, 
					'nonce_save' => $nonce_save 
					) ) );
		
		do_action("ubc_collab_theme_options_ui");	
	}

	
	
	/**
	 * Renders the Theme Options administration screen.
	 *
	 * @since ubc-collab 1.0
	 */
	function admin_page() { ?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php printf( __( 'Theme Options', 'ubc_collab' ) ); ?></h2>
			
			<div id="setting-error-settings_updated" style="display:none;"  class="updated settings-error below-h2">
				<p></p>
			</div>
			<?php settings_errors(); ?>
	
			<form method="post" action="options.php" id="ubc-collab-theme-options-form">
				<?php
					submit_button();
					?>
				<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="Loading..."  style="display:none;" class="ubc-collab-ajax-loading ubc-collab-ajax-save-options" /> <span class="save-changes" style="display:none;">There are some unsaved changes</span>

					<?php
					settings_fields( 'ubc_collab_options' );
					UBC_Collab_Theme_Options_Admin::tabs_do_settings_sections( 'theme_options' );
					
				?>
				<p class="submit">
					<input id="submit-buttom" class="button-primary" type="submit" value="Save Changes" name="submit">
				</p>
				<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="Loading…" style="display:none;"  class="ubc-collab-ajax-loading ubc-collab-ajax-save-options" /> <span class="save-changes" style="display:none;">There are some unsaved changes</span>
			</form>
		</div>
		<?php
	}
	
	/**
	 * tabs_do_settings_sections function.
	 * 
	 * @access public
	 * @return void
	 */
	function tabs_do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
	
		if ( !isset( $wp_settings_sections ) || !isset( $wp_settings_sections[$page] ) ) {
			return;
		}
			
		?>
		<div id="theme-options-shell">
			<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all hide-if-no-js">
			<?php 
			foreach ( (array) $wp_settings_sections[$page] as $section ) { ?>
				<li><a href="#<?php echo $section['id']; ?>" title="<?php echo esc_attr( $section['title'] ); ?>"><?php echo $section['title']; ?></a></li>
			<?php } // endforeach; ?>
			</ul> <!-- // ui-tabs-nav -->
			<?php
			foreach ( (array) $wp_settings_sections[$page] as $section ) { ?>
				
				<div id="<?php echo $section['id']; ?>" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<h3><?php echo $section['title']; ?></h3>
				<?php 
				call_user_func( $section['callback'], $section);
				
				if ( isset($wp_settings_fields[$page][$section['id']]) ) { ?>			
				
					<div class="form-table-shell">
					<table class="form-table">
						<?php do_settings_fields( $page, $section['id'] ); ?>
					</table>
					</div> <!-- // form-table-shell -->
				
				<?php } // endif; ?>
				</div><!-- // ui-tabs-panel -->
				
			<?php } // endforeach; ?>
		</div><!-- #theme-options-shell -->
	<?php 
	}
	
	/**
	 * options_export function.
	 * 
	 * @access public
	 * @return void
	 */
	function options_export() { ?>
		<p> <label for="export-url">Export url: </label> ( <a href="#" class="hide-if-no-js" id="export-select-all">select url</a> )<br />
		<input size="79" id="export-url" type="text" value="<?php echo admin_url('admin-ajax.php'); ?>?action=ubc-collab-export" /> </p>
		<?php 
	}
	
	/**
	 * options_import function.
	 * 
	 * @access public
	 * @return void
	 */
	function options_import() { ?>
		<p class="hide-if-no-js"> 
			<label for="import-url">Enter export url:</label> <br />
			<input size="79" type="text" value="http://" id="import-url"  /> 
		</p>
		<p class="hide-if-no-js">
			<a href="#import" id="import-url-action" class="button" >Import Settings</a>
			<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="Loading…" style="display:none;"   id="ajax-loading-import" class="ubc-collab-ajax-loading" /><span id="import-response"></span>
			<br />
			<small>This form allows you to import options from other sites that current use the UBC Collab Theme</small>
		</p>
		<noscript>Sorry you the settings import functionality relies on Javascript being enabled!</noscript>
	<?php
	}
	
	/* Ajax Stuff */
	
	/**
	 * ajax_save_options function.
	 * 
	 * @access public
	 * @return void
	 */
	function ajax_save_options() {
		
		if( $worked = update_option( 'ubc-collab-theme-options', $_POST['ubc-collab-theme-options'] ) ) {
			echo json_encode( array('success' => "Settings saved" ) );
			die();
		} else {
			echo json_encode( array('error' => "Settings not save" ) );
			die();
		}	
		
		
		echo json_encode( $_POST );
		die();
	}
	
	/**
	 * ajax_import function.
	 * 
	 * @access public
	 * @return void
	 */
	function ajax_import(){
		
		// do ajax nonce 
		if( !wp_verify_nonce( $_POST['nonce'], 'import-UBC-COLLAB' ) ) {
			echo json_encode(array('error' => "we don't like you" ) );
			die();
		}
			
		
		// check the validitity of the url
		// should the url should have '?action=ubc-collab-export' at the end
		$url = urldecode( trim($_POST['url'] ) );
		
		$remote_options = wp_remote_fopen( $url );
		$remote_options = json_decode( $remote_options ,TRUE);
		
		if( is_array( $remote_options ) ) {
		
			if( $worked = update_option( 'ubc-collab-theme-options', $remote_options ) ) {
				echo json_encode( array('success' => "Settings saved" ) );
				die();
			} else {
				echo json_encode( array('error' => "Can't duplicate the settings" ) );
				die();
			}			
			
		} else {
			echo json_encode( array('error' => "Please double check your url" ) );
			die();
		}
		
		
	}
	/**
	 * ajax_export function.
	 * 
	 * @access public
	 * @return void
	 */
	function ajax_export(){
		
		echo json_encode( UBC_Collab_Theme_Options::get() );
		
		die();
	}
	/**
	 * page_capability function.
	 * 
	 * @access public
	 * @param mixed $capability
	 * @return void
	 * 
	 * Change the capability required to save the 'ubc_collab_options' options group.
	 *
	 * @see ubc_collab_theme_options_init() First parameter to register_setting() is the name of the options group.
	 * @see ubc_collab_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
	 *
	 * @param string $capability The capability used for the page, which is manage_options by default.
	 * @return string The capability to actually use.
	 */

	function page_capability($capability ) {
		return 'edit_theme_options';
	}

}

UBC_Collab_Theme_Options_Admin::init();