<?php
/**
 * Plugin Name: Full Width CLF 
 * Plugin URI: http://www.ubc.ca
 * Description: Full Width of CLF 7.0 as according to UBC CLF requirements. Once activated, alignment options available in <a href="/wp-admin/themes.php?page=theme_options">Theme Options</a>
 * Version: 1.1
 * Author: Michael Kam 
 * Author URI: http://www.ubc.ca/
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

function make_full_width() {
	UBC_Collab_CLF::$full_width = true;
}

add_action('init', 'make_full_width');

Class UBC_Full_Width_Theme_Options {
	
	static $prefix;
	
	function init() {
		self::$prefix = "wp-hybrid-clf";
		
		add_action('ubc_collab_theme_options_ui', array( __CLASS__, 'admin_ui'));
		
		add_action("admin_init", array(__CLASS__, "admin"));
		add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );
		add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );
	}
	
	function admin_ui() {
		// include CLF admin specific css file
		wp_register_style('clf-full-width-admin-style', plugins_url('/css/admin-style.css' , __FILE__) );
		wp_enqueue_style('clf-full-width-admin-style');
	}
	
	function admin() {
		
		// add_settings_section(
			// 'clf-full', // Unique identifier for the settings section
			// 'CLF Full Width', // Section title (we don't want one)
			// '__return_false', // Section callback (we don't want anything)
			// 'theme_options' // Menu slug, used to uniquely identify the page; see ubc-collab-theme-options-add-page()
		// );
		
		add_settings_field(
		   'clf-full-option',
		   __('CLF Full Width Options', 'ubc_collab'),
		   array(__CLASS__, 'full_options'),
		   'theme_options',
		   'clf'
	    );   
	}
	
	/*********** 
	 * Default Options
	 * 
	 * Returns the options array for ubc-clf.
	 *
	 * @since ubc-clf 1.0
	 */
	function default_values( $options ) {
			
		if (!is_array($options)) { 
			$options = array();
		}
		
		$defaults = array(
		    'clf-full-width' => 'center'
		);
		
		$options = array_merge( $options, $defaults );
		
		return $options;
	}
	
	/**
 	  * Returns an array of CLF Full Width Theme options 
 	  */
	function ubc_clf_full_width_theme() {
    	$clf_themes = array(
    		'center' => array(
	            'value' => 'center',
	            'label' => __( 'Center Align', 'ubc-clf' )
	        ),
	        'left' => array(
	            'value' => 'left',
	            'label' => __( 'Left Align', 'ubc-clf' )
	        )
	    );
	   return $clf_themes;
	}	
	
	/**
	 * CLF Full Width Options
	 */
	function full_options(){
		
		$class = 'UBC_Collab_Theme_Options';
		?>
		 
		<div id="clf-full-width">
		<?php
	    foreach ( UBC_Full_Width_Theme_Options::ubc_clf_full_width_theme() as $button ) {
	    ?>
	    <div class="layout">
	        <label class="description">
	        	
	        	<?php
	        		$element = '<div class="theme-sample" id="'.$button['value'].'"><img src="'.plugins_url('/img/'.$button['value'].'-align.jpg' , __FILE__).'" alt="'.$button['value'].'" /></div>';
	        		$label = $button['label'];
	        	 	UBC_Collab_Theme_Options::radio( 'clf-full-width', $button['value'], $label); 
	        	 	echo $element; ?>
	        </label>
	    </div>
	    <?php
	    }
		?>
		</div>
		<?php
	}
	
	/**
	 * Sanitize and validate form input. Accepts an array, return a sanitized array.
	 *
	 * @see ubc_clf_theme_options_init()
	 * @todo set up Reset Options action
	 *
	 * @param array $input Unknown values.
	 * @return array Sanitized theme options ready to be stored in the database.
	 *
	 * @since ubc-clf 1.0
	 */
	function validate( $output, $input ) {
		
		// Grab default values as base
		$starter = UBC_Full_Width_Theme_Options::default_values( array() );
		

	    // Validate Colour Theme
	    if ( isset( $input['clf-full-width'] ) && array_key_exists( $input['clf-full-width'], UBC_Full_Width_Theme_Options::ubc_clf_full_width_theme() ) ) {
	        $starter['clf-full-width'] = $input['clf-full-width'];
	    }
	    
	    $output = array_merge($output, $starter);
	    
		return $output;
	}
	
	/** 
	 * Return selected Full Width alignment option
	 */
	function get_align() {
		return UBC_Collab_Theme_Options::get("clf-full-width");
	}
}


UBC_Full_Width_Theme_Options::init();
