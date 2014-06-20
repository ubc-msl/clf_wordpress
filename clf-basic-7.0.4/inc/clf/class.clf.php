<?php

Class UBC_Collab_CLF{
	
	static $cdn_path = '//cdn.ubc.ca/clf';
	static $cdn_ver  = '7.0.4';
	static $cdn_css;
	static $cdn_js;
	static $cdn_img;
	static $cdn_ref; // Redirect path
	static $prefix;
	static $full_width = false;
	
	/**
	 * init function.
	 */
	function init(){
		
		self::$prefix = hybrid_get_prefix();
		
		// Define CDN shortcut
		self::$cdn_css = self::$cdn_path."/".self::$cdn_ver."/css";
		self::$cdn_js  = self::$cdn_path."/".self::$cdn_ver."/js";
		self::$cdn_img = self::$cdn_path."/".self::$cdn_ver."/img";
		self::$cdn_ref = self::$cdn_path."/ref";
		
		add_action('ubc_collab_theme_options_ui', array( __CLASS__, 'admin_ui'));
		
               
        
		add_filter('body_class', array( __CLASS__, 'body_class_filter' ) );
		
		// Theme Options
		add_action( 'admin_init',array(__CLASS__, 'admin' ) );
		add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );
		add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );
		add_filter( 'ubc_collab_backward_compatibility_theme_options', array(__CLASS__, 'backward_compatibility'), 10, 2 );
		
		
		// HTML and CSS
		add_action( 'init', array(__CLASS__, 'start'));
		add_action( 'before_css', array(__CLASS__, 'clf_css') );
		add_action( 'wp_head', array( __CLASS__,'wp_head' ) );
		add_action( self::$prefix.'_header', array(__CLASS__, 'header' ) );
		add_filter( 'breadcrumb_trail_items', array( __CLASS__, 'breadcrumb_trail_items' ) );
		add_action( self::$prefix.'_footer', array(__CLASS__, 'footer' ) );
		add_action( 'wp_footer', array(__CLASS__, 'wp_footer' ) );
	}	
	
	function admin_ui() {
		// include CLF specific css file
		wp_register_style('theme-option-clf-style', get_template_directory_uri().'/inc/clf/css/clf-admin.css');
		wp_enqueue_style('theme-option-clf-style');
        
		
		// include CLF specific js file
        wp_register_script('theme-option-clf-script', get_template_directory_uri().'/inc/clf/js/clf-admin.js');
		wp_enqueue_script('theme-option-clf-script');
	}
	/**
	 * Theme Options
	 */
	function admin(){
		
		add_settings_section(
			'clf', // Unique identifier for the settings section
			'UBC CLF', // Section title (we don't want one)
			'__return_false', // Section callback (we don't want anything)
			'theme_options' // Menu slug, used to uniquely identify the page; see ubc-collab-theme-options-add-page()
		);
		
		// add_settings_field(
			// 'clf-description',
			// '',
			// array(__CLASS__, 'clf_description'),
			// 'theme_options',
			// 'clf'
		// );
		
		// UBC CLF Colour Themes
		add_settings_field(
		   'clf-colour-theme',
		   __('Standard CLF Colour Options', 'ubc_collab'),
		   array(__CLASS__, 'theme_options'),
		   'theme_options',
		   'clf'
	    );
		
		// UBC Campus Identifier
		add_settings_field(
			'clf-campus',
			__('Campus Identity', 'ubc_collab'),
			array(__CLASS__, 'campus'),
			'theme_options',
			'clf'
		);
		
		// UBC CLF Faculty Input Box
		add_settings_field(
		   'clf-unit-bar-faculty-unit',
		   __('Unit/Website Information', 'ubc_collab'),
		   array(__CLASS__, 'unit_bar_faculty'),
		   'theme_options',
		   'clf'
	    );
		
		
		// Unit Contact Info for CLF Footer
		add_settings_field(
			'clf-unit-contact',
			__('Unit Contact Info', 'ubc_collab'),
			array(__CLASS__, 'unit_contact'),
			'theme_options',
			'clf'
		);
		
		wp_enqueue_style('farbtastic');
		wp_enqueue_script('farbtastic');
	}
	
	/**
	 * body_class_filter function.
	 * 
	 * @access public
	 * @param mixed $body_class
	 * @return void
	 */
	function body_class_filter($body_class){
		
		if ( UBC_Collab_CLF::is_full_width() ) {
		 	$body_class[] = 'full-width';
		}
		
		if (class_exists("UBC_Full_Width_Theme_Options") && strcasecmp(UBC_Full_Width_Theme_Options::get_align(), "left") == 0) {
			$body_class[] = "full-width-left";
		}
		return $body_class;
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
		    'clf-colour-theme'      => 'wb', 
		    'clf-campus-identifier' => 'b',
		    'clf-administrative'	=> 'no',
		    'clf-unit-faculty'      => '',
		    'clf-faculty-breadcrumb'=> true,
		    'clf-unit-unit-website' => 'Unit/Website Name',
		    'clf-unit-colour'		=> '#2F5D7C',
		    'clf-unit-url'			=> site_url(),
		    'clf-unit-search'		=> '*.ubc.ca',
		    'clf-unit-name'			=> 'Unit Name',
        	'clf-faculty-address' => 'no',
        	'clf-faculty-address-display' => '',
		    'clf-unit-address1'		=> '1234 Street',
		    'clf-unit-address2'		=> '',
		    'clf-unit-city'			=> 'Vancouver',
		    'clf-unit-province'		=> 'BC',
		    'clf-unit-country'		=> 'Canada',
		    'clf-unit-postal'		=> 'V0V 0V0',
		    'clf-unit-e-mail'		=> '',
		    'clf-unit-website'		=> '',
		    'clf-unit-phone'		=> '',
		    'clf-unit-fax'			=> '',
		    'clf-unit-facebook'		=> '',
		    'clf-unit-twitter'		=> '',
		    'clf-unit-google-plus'  => '',
		    'clf-unit-linkedin'		=> '',
		    'clf-unit-youtube'		=> ''
		);
		
		$options = array_merge( $options, $defaults );
		
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
		    
		    $input['clf-colour-theme']      = self::convert_color_theme( $clf_base_options['clf_header']['option'] );
		    $input['clf-campus-identifier'] = self::convert_campus_identifier( $clf_base_options['clf_header']['location'] );
		    $input['clf-administrative']	= 'no';
		    $input['clf-unit-faculty']      = '';
		    $input['clf-unit-unit-website'] = self::if_else( $clf_base_options['clf_header']['unit_name'], 'Unit/Website Name' );
		    $input['clf-unit-colour']	  = "#".self::if_else( $clf_base_options['clf_header']['hexcode'],'2F5D7C');
		    $input['clf-unit-url']		  = self::if_else( $clf_base_options['clf_header']['unit_link'], site_url());
		    $input['clf-unit-search']	  = self::if_else( $clf_base_options['clf']['search_domain'], '*.ubc.ca' );
		    $input['clf-unit-name']		  = self::if_else( $clf_base_options['clf']['unit_name'], 'Unit Name');
		    $input['clf-unit-address1']	  = self::if_else( $clf_base_options['clf']['unit_address1'], '1234 Street');
		    $input['clf-unit-address2']	  = self::if_else( $clf_base_options['clf']['unit_address2'], '');
		    $input['clf-unit-city']		  = self::if_else( $clf_base_options['clf']['unit_city'], 'Vancouver');
		    $input['clf-unit-province']	  = self::if_else( $clf_base_options['clf']['unit_province'], 'BC');
		    $input['clf-unit-country']	  = self::if_else( $clf_base_options['clf']['unit_country'], 'Canada');
		    $input['clf-unit-postal']	  = self::if_else( $clf_base_options['clf']['unit_postal'], 'V0V 0V0');
		    $input['clf-unit-e-mail']	  = self::if_else( $clf_base_options['clf']['unit_email'], '');
		    $input['clf-unit-website']	  = self::if_else( $clf_base_options['clf']['unit_website'], '');
		    $input['clf-unit-phone']	  = self::if_else( $clf_base_options['clf']['unit_phone'], '');
		    $input['clf-unit-fax']		  = self::if_else( $clf_base_options['clf']['unit_fax'], '');
			
			
		return self::validate( $options, $input );
	}
	
	function convert_color_theme( $option ){
		switch( (int) $option ){
			case 1:
				return 'wb';
			break;
			case 2:
				return 'bw';
			break;
			case 3:
				return 'wg';
			break;
			case 4:
				return 'gw';
			break;
			
			default:
				return 'wb';
			break;
		}
	}
	
	function convert_campus_identifier( $option ){
		switch( $option ){
			case 'vancouver':
				return 'v';
			break;
			case 'okanagan':
				return 'o';
			break;
			
			default:
				return 'b';
			break;
		}
	}
	
	function if_else( $option , $else ) {
		return ( isset ( $option ) ? $option : $else );
	}
	
	/*******************************************************
	 *  Theme Options
	 * 
	 * 
	 * */
	 
	 /**
 	  * Returns an array of CLF Colour Theme options 
 	  */
	function ubc_clf_colour_theme() {
    	$clf_themes = array(
	        'wb' => array(
	            'value' => 'wb',
	            'label' => __( 'White on Blue', 'ubc-clf' )
	        ),
	        'bw' => array(
	            'value' => 'bw',
	            'label' => __( 'Blue on White', 'ubc-clf' )
	        ),
	        'gw' => array(
	            'value' => 'gw',
	            'label' => __( 'Grey on White', 'ubc-clf' )
	        ),
	        'wg' => array(
	            'value' => 'wg',
	            'label' => __( 'White on Grey', 'ubc-clf' )
	        )
	    );
	   return $clf_themes;
	}
	
	function ubc_clf_administrative() {
		return array(
			'yes' => array(
				'value' => 'yes',
				'label' => __( 'Yes', 'ubc-clf' )
			),
			'no' => array(
				'value' => 'no',
				'label' => __( 'No', 'ubc-clf')
			)
		);
		
	}
	
	/**
	 * Returns and array of Campus Identifier
	 */
	function ubc_clf_campus_identifier() {
		$clf_campus = array(
	        'v' => array(
	            'value' => 'v',
	            'label' => __( 'Vancouver Campus', 'ubc-clf' )
	        ),
	        'o' => array(
	            'value' => 'o',
	            'label' => __( 'Okanagan Campus', 'ubc-clf' )
	        ),
	        'b' => array(
	            'value' => 'b',
	            'label' => __( 'Institution-wide mandate / Not applicable', 'ubc-clf' )
	        )
	    );
	   return $clf_campus;
	}

	/**
	 * Returns and array of Faculty Name
	 */
	function ubc_faculty() {
		$ubc_faculty = array(
			'na' => array(
				'value' => 'na',
				'label' => __( 'Not Applicable', 'ubc-clf')
			),
			'applied_science' => array(
				'value' => 'applied_science',
				'label' => __( 'Faculty of Applied Science', 'ubc-clf' ),
				'url'   => 'http://apsc.ubc.ca'
			),
			'arts' => array(
				'value' => 'arts',
				'label' => __( 'Faculty of Arts', 'ubc-clf' ),
				'url'   => 'http://arts.ubc.ca'
			),
			'dentistry' => array(
				'value' => 'dentistry',
				'label' => __( 'Faculty of Dentistry', 'ubc-clf' ),
				'url'   => 'http://www.dentistry.ubc.ca'
			),
			'education' => array(
				'value' => 'education',
				'label' => __( 'Faculty of Education', 'ubc-clf' ),
				'url'   => 'http://educ.ubc.ca'
			),
			'forestry' => array(
				'value' => 'forestry',
				'label' => __( 'Faculty of Forestry', 'ubc-clf' ),
				'url'   => 'http://www.forestry.ubc.ca'
			),
			'graduate' => array(
				'value' => 'graduate',
				'label' => __( 'Faculty of Graduate and Postdoctoral Studies', 'ubc-clf' ),
				'url'   => 'http://grad.ubc.ca'
			),
			'land-food' => array(
				'value' => 'land-food',
				'label' => __( 'Faculty of Land and Food Systems', 'ubc-clf' ),
				'url'   => 'http://landfood.ubc.ca'
			),
			'law' => array(
				'value' => 'law',
				'label' => __( 'Faculty of Law', 'ubc-clf' ),
				'url'   => 'http://www.law.ubc.ca'
			),
			'medicine' => array(
				'value' => 'medicine',
				'label' => __( 'Faculty of Medicine', 'ubc-clf' ),
				'url'   => 'http://med.ubc.ca'
			),
			'pharmacy' => array(
				'value' => 'pharmacy',
				'label' => __( 'Faculty of Pharmaceutical Sciences', 'ubc-clf' ),
				'url'   => 'http://www.pharmacy.ubc.ca'
			),
			'science' => array(
				'value' => 'science',
				'label' => __( 'Faculty of Science', 'ubc-clf' ),
				'url'   => 'http://science.ubc.ca'
			),
			'sauder' => array(
				'value' => 'sauder',
				'label' => __( 'Sauder School of Business', 'ubc-clf'),
				'url'   => 'http://sauder.ubc.ca'
			)
		); 	
		
		return $ubc_faculty;
	}

	function clf_description() { ?>
<div>The Basic CLF 7.0 Wordpress theme is a responsive <a href="http://clf.ubc.ca" title="UBC Common Look and Feel" target="_blank">UBC CLF</a> (Common Look and Feel) theme, developed and distributed by Communications &amp; Marketing. For support or to report an issue with this theme, please <a href="mail:web.admin@ubc.ca" target="_blank" title="Email to Web Communications">contact us</a>.</div> 
	<?php }

	/**
	 * Colour Theme Options
	 */
	function theme_options(){
		
		$class = 'UBC_Collab_Theme_Options';
		?>
		<div class="explanation"><a href="#" class="explanation-help">Info</a>
			
			<div>View colour theme options and <a href="http://clf.ubc.ca/design-specifications/#theme-options" title="CLF Design Specifications" target="_blank">design specifications</a>.</div>
		</div>
		 
		<div id="clf-theme">
		<?php
	    foreach ( UBC_Collab_CLF::ubc_clf_colour_theme() as $button ) {
	    ?>
	    <div class="layout">
	        <label class="description">
	        	
	        	<?php
	        		$element = '<div class="theme-sample" id="'.$button['value'].'"></div>';
	        		$label = $button['label'];
	        	 UBC_Collab_Theme_Options::radio( 'clf-colour-theme', $button['value'], $label); 
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
	 * Renders the header Faculty/Unit text input setting field.
	 */
	function unit_bar_faculty() { 
		
		$key = "clf-unit-faculty";
		$select_name = 'ubc-collab-theme-options['.$key.']';?>
		<div class="explanation"><a href="#" class="explanation-help">Info</a>
			
			<div>Choose between:<br /><br />
				<ul>
					<li>UBC Blue (if you have selected the Blue on White or the White on Grey theme)</li>
					<li>UBC Grey (if you have selected the White on Blue or the Grey on White theme)</li>
					<li>or your own unit colour, if you have chosen one.</li>
				</ul><br />
				See design specifications for <a href="http://clf.ubc.ca/design-specifications/#unit-colors" title="Website Bar Background Colours">Unit/Website Name background colours</a>
			</div>
		</div>
		<div id="clf-unit-colour-box">
			<label><b>Unit/Website Bar Background Colour:</b></label>
			<label id="ubc-blue"><input type="radio" name="default-colour" class="ubc-default-colour" value="#002145" id="colour-ubc-blue" /> &nbsp; &nbsp; UBC Blue</label>
			<label id="ubc-grey"><input type="radio" name="default-colour" class="ubc-default-colour" value="#2F5D7C" id="colour-ubc-grey" /> &nbsp; &nbsp; UBC Grey</label>
			<input type="radio" name="default-colour" id="clf-default-colour" checked="checked" /> <?php  UBC_Collab_Theme_Options::text( 'clf-unit-colour' ); ?>
		</div>
		<label>Is your unit part of a Faculty?</label>
		<div id="faculty-ans">
	    <?php
	    foreach ( UBC_Collab_CLF::ubc_clf_administrative() as $button ) {
	        $label = $button['label'];
	        UBC_Collab_Theme_Options::radio( 'clf-administrative', $button['value'], $label); 
	    }
		?>
		</div>
		<br />
		<div id="faculty-name">
			<label>Faculty Name:</label><br />
			<select name="<?php echo $select_name; ?>">
				<?php foreach ( UBC_Collab_CLF::ubc_faculty() as $option ) {
					UBC_Collab_Theme_Options::option( $key, $option['value'], $option['label']);
				} ?>
			</select>
			<br />
			<?php UBC_Collab_Theme_Options::checkbox( 'clf-faculty-breadcrumb', 1, 'Display the Faculty Link in the breadcrumb' ); ?>
		</div>
		<label><b>Website Name:</b></label><br />
		<?php 
		UBC_Collab_Theme_Options::text( 'clf-unit-unit-website');
		?>
		<br />
		<div class="half">
			<label><b>Website Home URL:</b></label><br />
			<?php 
			UBC_Collab_Theme_Options::text( 'clf-unit-url');
			?>
		</div>
		<div class="half">
			<label><b>Website Search:</b></label><br />
			<?php 
			UBC_Collab_Theme_Options::text( 'clf-unit-search');
			?>
			
		</div>
		<?php
	}
	
	
	function campus() { ?>
		<div class="explanation"><a href="#" class="explanation-help">Info</a>
			<div>This shows your unit's campus mandate: Vancouver Campus or Okanagan Campus. If your unit has an institution-wide mandate or if neither choice is applicable, select the third option. See <a href="http://clf.ubc.ca/parts-of-the-clf/#campus-identity" title="CLF Campus Identity" target="_blank" >Campus Identity</a> for guidelines.</div>
		</div>
		<?php

		foreach ( UBC_Collab_CLF::ubc_clf_campus_identifier() as $option ) {
	    	$element = '<div class="theme-sample" id="'.$option['value'].'"></div>';
	        $label = $option['label'];
		?>
		<div class="layout">
		<?php UBC_Collab_Theme_Options::radio( 'clf-campus-identifier', $option['value'], $option['label']); ?>
		</div>
		<?php }
	} 
	
	/**
	 * clf_base_theme_options_clf_unit_contact function.
	 * 
	 * @access public
	 * @return void
	 */
	function unit_contact() { ?>
		<div class="explanation"><a href="#" class="explanation-help">Info</a>
			<div>The Unit Sub Footer provides a standardized unit contact information area.</div>
		</div>
		
		<?php UBC_Collab_Theme_Options::text( 'clf-unit-name', 'Unit Name'); ?><br />
		<label>Show faculty name in the address?</label>
		<div id="faculty-address-ans">
		<?php foreach ( UBC_Collab_CLF::ubc_clf_administrative() as $button ) {
			$label = $button['label'];
			UBC_Collab_Theme_Options::radio( 'clf-faculty-address', $button['value'], $label);
		} ?>
		</div>
		<br />
		<div id="faculty-address">
			<label>Faculty Name:</label><br />
			<select name="ubc-collab-theme-options[clf-faculty-address-display]">
				<?php foreach ( UBC_Collab_CLF::ubc_faculty() as $option ) {
					UBC_Collab_Theme_Options::option( 'clf-faculty-address-display', $option['value'], $option['label']);
				} ?>
			</select>
		</div>
		<div class="group">
		<h4>Address</h4>
		
		<?php UBC_Collab_Theme_Options::text( 'clf-unit-address1', 'Street Address', '', true); ?><br />
		<?php UBC_Collab_Theme_Options::text( 'clf-unit-address2', null,'(Optional)', true); ?><br />
		
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-city', 'City'); ?> 
		</div>
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-province','Province / State / Region'); ?>  
		</div>
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-postal', 'Postal / Zip Code'); ?>  
		</div>
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-country', 'Country'); ?>  
		</div>
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-e-mail', 'Email');  ?> 
		</div>
		
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-website', 'Website Url'); ?>  
		</div>
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-phone', 'Phone'); ?>  
		</div>
		<div class="half">
			<?php UBC_Collab_Theme_Options::text( 'clf-unit-fax', 'Fax'); ?>  
		</div>
		</div>
		<div id="unit-social" class="clear group">
			<h4>Social Links</h4>
			<label>Facebook:</label> <?php UBC_Collab_Theme_Options::text( 'clf-unit-facebook'); ?> <br />
			<label>Twitter:</label> <?php UBC_Collab_Theme_Options::text( 'clf-unit-twitter'); ?><br />
			<label>Google+:</label> <?php UBC_Collab_Theme_Options::text( 'clf-unit-google-plus'); ?><br />
			<label>LinkedIn:</label> <?php UBC_Collab_Theme_Options::text( 'clf-unit-linkedin'); ?><br />
			<label>YouTube:</label> <?php UBC_Collab_Theme_Options::text( 'clf-unit-youtube'); ?>
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
		$starter = UBC_Collab_CLF::default_values( array() );
		

	    // Validate Colour Theme
	    if ( isset( $input['clf-colour-theme'] ) && array_key_exists( $input['clf-colour-theme'], UBC_Collab_CLF::ubc_clf_colour_theme() ) ) {
	        $starter['clf-colour-theme'] = $input['clf-colour-theme'];
	    }
		
		// Validate Campus Identifier
		if ( isset( $input['clf-campus-identifier'] ) && array_key_exists( $input['clf-campus-identifier'], UBC_Collab_CLF::ubc_clf_campus_identifier() ) ) {
	        $starter['clf-campus-identifier'] = $input['clf-campus-identifier'];
		}

		// Validate Administrative Unit
	    if ( isset( $input['clf-administrative'] ) && array_key_exists( $input['clf-administrative'], UBC_Collab_CLF::ubc_clf_administrative() ) ) {
	        $starter['clf-administrative'] = $input['clf-administrative'];
			
			if (strcasecmp($input['clf-administrative'], "yes") == 0) {
				// Validate Unit Faculty (Optional Field)
		    	$starter['clf-unit-faculty'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-faculty'], $starter['clf-unit-faculty'] );
		    	
		    	$starter['clf-faculty-breadcrumb'] = (bool) $input['clf-faculty-breadcrumb'];
			}
	    }
	    
	    // Validate Unit Website (Required Field)
	    $starter['clf-unit-unit-website'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-unit-website'], $starter['clf-unit-unit-website'] );
		
		// Validate Unit URL (Required Field)
		$starter['clf-unit-url'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-url'], $starter['clf-unit-url'] );
		
		// Validate search
		$starter['clf-unit-search'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-search'], $starter['clf-unit-search'] );
		
		// Validate Unit Colour (Required Field)
		$starter['clf-unit-colour'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-colour'], $starter['clf-unit-colour'] );
		
		// Validate Unit Name (Required Field)
	    $starter['clf-unit-name'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-name'], $starter['clf-unit-name'] );

	    // Validate Faculty Address
	    if ( isset( $input['clf-faculty-address'] ) && array_key_exists( $input['clf-faculty-address'], UBC_Collab_CLF::ubc_clf_administrative() ) ) {
	      	$starter['clf-faculty-address'] = $input['clf-faculty-address'];
	
	      	if (strcasecmp($input['clf-faculty-address'], "yes") == 0) {
	        	// Validate Faculty Address Display (Optional Field)
	          	$starter['clf-faculty-address-display'] = UBC_Collab_Theme_Options::validate_text($input['clf-faculty-address-display'], $starter['clf-faculty-address-display'] );
	      	}
	    }
		
		// Validate Unit Address1 (Required Field)
	    $starter['clf-unit-address1'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-address1'], $starter['clf-unit-address1'] );
		
		// Validate Unit Address2 (Optional Field)
	    $starter['clf-unit-address2'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-address2'], $starter['clf-unit-address2'] );
		    
		// Validate Unit City (Required Field)
	    $starter['clf-unit-city'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-city'], $starter['clf-unit-city'] );
		    
		// Validate Unit Province (Required Field)
	    $starter['clf-unit-province'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-province'], $starter['clf-unit-province'] );
		
		// Validate Unit Country (Required Field)
	    $starter['clf-unit-country'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-country'], $starter['clf-unit-country'] );
		
		// Validate Unit Postal Code (Required Field)
	    $starter['clf-unit-postal'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-postal'], $starter['clf-unit-postal'] );
		    
		// Validate Unit E-mail (Optional Field)
	    $starter['clf-unit-e-mail'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-e-mail'], $starter['clf-unit-e-mail'] );
		    
		// Validate Unit Website (Optional Field)
	    $starter['clf-unit-website'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-website'], $starter['clf-unit-website'] );
		
		// Validate Unit Phone Number (Required Field)
	    $starter['clf-unit-phone'] = UBC_Collab_CLF::format_phone(UBC_Collab_Theme_Options::validate_text($input['clf-unit-phone'], $starter['clf-unit-phone'] ));
		
		// Validate Unit Fax Number (Optional Field)
	    $starter['clf-unit-fax'] = UBC_Collab_CLF::format_phone(UBC_Collab_Theme_Options::validate_text($input['clf-unit-fax'], $starter['clf-unit-fax'] ));
		
		// Validate Unit Facebook (Optional Field)
	    $starter['clf-unit-facebook'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-facebook'], $starter['clf-unit-facebook'] );
		
		// Validate Unit Twitter (Optional Field)
	    $starter['clf-unit-twitter'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-twitter'], $starter['clf-unit-twitter'] );
		
		// Validate Unit LinkedIn (Optional Field)
	    $starter['clf-unit-linkedin'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-linkedin'], $starter['clf-unit-linkedin'] );
		
		// Validate Unit YouTube (Optional Field)
	    $starter['clf-unit-youtube'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-youtube'], $starter['clf-unit-youtube'] );
		
		// Validate Unit Google+ (Optional Field)
	    $starter['clf-unit-google-plus'] = UBC_Collab_Theme_Options::validate_text($input['clf-unit-google-plus'], $starter['clf-unit-google-plus'] );
		
		$output = array_merge($output, $starter);
	    
		return $output;
	}

	// Validate text box 
	function validate_text($input, $default) {
		if ( isset( $input ) && !empty( $input) ) {
	        return wp_filter_nohtml_kses( $input );
		}
		else {
			return $default;
		}
	}
	
	// Format Phone Number for storage
	function format_phone($phone) {
		$clean_phone = UBC_Collab_CLF::clean_phone($phone);
		
		if (is_numeric($clean_phone) && strlen($clean_phone) == 10) {
			return substr($clean_phone, 0, 3)." ".substr($clean_phone, 3, 3)." ".substr($clean_phone, 6, 4);	
		}
		else if (is_numeric($clean_phone) && strlen($clean_phone) == 11) {
			return substr($clean_phone, 0, 1)." ".substr($clean_phone, 1, 3)." ".substr($clean_phone, 4, 3)." ".substr($clean_phone, 7, 4);
		}
		else {
			return $phone;
		}
	}
	
	// Strip all non-digit in phone/fax number
	function clean_phone($phone) {
		$items = Array('/\ /', '/\+/', '/\-/', '/\./', '/\,/', '/\(/', '/\)/', '/[a-zA-Z]/');
        return preg_replace($items, '', $phone);
	}

	/** 
	 * Hook CLF JS into footer
	 */
	function start() {
		wp_register_script('clf', self::$cdn_js.'/ubc-clf.min.js',array('jquery'),1, true );
		
	}
		
	
    /*****
     * CLF CSS File
     * 
     * @return text CLF CSS
     */
     
    
	function clf_css() { 
		$css = UBC_Collab_CLF::get_css();		
		?>
<!-- Stylesheets -->
<link href="<?php echo $css; ?>" rel="stylesheet">
	<?php }
	
	
	/**
	 * wp_head function.
	 * 
	 * @access public
	 * @return void
	 */
	function wp_head(){ ?>

<!--[if lte IE 7]>
<link href="<?php echo self::$cdn_css; ?>/font-awesome-ie7.css" rel="stylesheet">
<![endif]-->
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Le fav and touch icons -->
<?php if( !class_exists('UBC_Favicon')): ?>
<link rel="shortcut icon" href="<?php echo self::$cdn_img; ?>/favicon.ico">
<?php endif; ?>
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo self::$cdn_img; ?>/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo self::$cdn_img; ?>/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo self::$cdn_img; ?>/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="<?php echo self::$cdn_img; ?>/apple-touch-icon-57-precomposed.png">
<style type="text/css" media="screen">#ubc7-unit { background: <?php echo UBC_Collab_Theme_Options::get("clf-unit-colour"); ?>; }</style> 
<?php
	}
	
	/*************************************************************************
	 * Header 
	 ************************************************************************/
	function header() {
		UBC_Collab_CLF::global_utility_menu();
		
		UBC_Collab_CLF::clf_header();
		
		UBC_Collab_CLF::unit_name_bar();
	
	}
	
	function is_full_width() {
		return self::$full_width;
	}
	
	/**
	 * Global Utility Area for search and quicklinks
	 */
	function global_utility_menu() {
		$name = (string)( UBC_Collab_Theme_Options::get('clf-unit-unit-website'));
		if (!empty($name))
			$label = UBC_Collab_Theme_Options::get('clf-unit-unit-website');
		
		?>
		<!-- UBC Global Utility Menu -->
        <div class="collapse expand" id="ubc7-global-menu">
            <div id="ubc7-search" class="expand">
            	<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
                <div id="ubc7-search-box">
                    <form class="form-search" method="get" action="http://www.ubc.ca/search/refine/" role="search">
                        <input type="text" name="q" placeholder="Search <?php echo (isset($label))? $label : "this website";  ?>" class="input-xlarge search-query">
                        <input type="hidden" name="label" value="<?php echo (isset($label))? $label : "UBC Website"; ?>" />
                        <input type="hidden" name="site" value="<?php echo UBC_Collab_Theme_Options::get('clf-unit-search'); ?>" />
                        <button type="submit" class="btn">Search</button>
                    </form>
                </div>
                <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div id="ubc7-global-header" class="expand">
                <!-- Global Utility Header from CDN -->
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </div>
        <!-- End of UBC Global Utility Menu -->		
	<?php }
	
	/**
	 * UBC CLF header elements
	 */
	function clf_header() { 
		$campus_id = UBC_Collab_CLF::get_campus_id();
		$campus_name = UBC_Collab_CLF::get_campus_name(false);
		
		if (UBC_Collab_Theme_Options::get("clf-campus-identifier") == 'o') {
			$link = "http://www.ubc.ca/okanagan";
		}
		else {
			$link = "http://www.ubc.ca/";		
		}
		
		if (!empty($campus_id)) {
			$campus_identifier = '<span class="ubc7-campus" id="'.$campus_id.'">'.$campus_name.'</span>';
		} 
		?>
		<!-- UBC Header -->
        <header id="ubc7-header" class="row-fluid expand" role="banner">
        	<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div class="span1">
                <div id="ubc7-logo">
                    <a href="http://www.ubc.ca" title="The University of British Columbia (UBC)">The University of British Columbia</a>
                </div>
            </div>
            <div class="span2">
                <div id="ubc7-apom">
                    <a href="<?php echo self::$cdn_ref; ?>/aplaceofmind" title="UBC a place of mind">UBC - A Place of Mind</a>                        
                </div>
            </div>
            <div class="span9" id="ubc7-wordmark-block">
                <div id="ubc7-wordmark">
                    <a href="<?php echo $link; ?>" title="The University of British Columbia (UBC)">The University of British Columbia</a>
                    <?php if (isset($campus_identifier)) { echo $campus_identifier; } ?>
                </div>
                <div id="ubc7-global-utility">
                    <button type="button" data-toggle="collapse" data-target="#ubc7-global-menu"><span>UBC Search</span></button>
                    <noscript><a id="ubc7-global-utility-no-script" href="http://www.ubc.ca/" title="UBC Search">UBC Search</a></noscript>
                </div>
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </header>
        <!-- End of UBC Header -->
		
	<?php }

	/**
	 * Unit Name Bar
	 */
	function unit_name_bar() { 
		$faculty_name = UBC_Collab_CLF::get_faculty_name(UBC_Collab_Theme_Options::get("clf-unit-faculty"));
		$website_name = UBC_Collab_Theme_Options::get("clf-unit-unit-website");
		$unit_url = UBC_Collab_Theme_Options::get("clf-unit-url");
			
		$single_treatment = (empty($faculty_name)) ? 'class="ubc7-single-element"':"";
		?>
	 	<!-- UBC Unit Identifier -->
        <div id="ubc7-unit" class="row-fluid expand">
        	<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div class="span12">
                <!-- Mobile Menu Icon -->
                <div class="navbar">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target="#ubc7-unit-navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                </div>
                <!-- Unit Name -->
                <div id="ubc7-unit-name" <?php echo $single_treatment; ?>>
                    <a href="<?php echo $unit_url; ?>" title="<?php echo $website_name; ?>"><span id="ubc7-unit-faculty"><?php echo $faculty_name; ?></span><span id="ubc7-unit-identifier"><?php echo $website_name; ?></span></a>
                </div>
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </div>
        <!-- End of UBC Unit Identifier -->
	<?php }


	/********************************************************************
	 * Footer
	 ********************************************************************/
	function footer() { ?>
<!-- CLF Footer -->	
<footer id="ubc7-footer" class="expand" role="contentinfo">
	<?php
		
		UBC_Collab_CLF::unit_footer();
		do_action( self::$prefix.'_after_unit_footer' );
		UBC_Collab_CLF::back_to_top();
		UBC_Collab_CLF::global_utility_footer();
		
	?>
</footer>
<!-- End of CLF Footer -->
	<?php }
	
	/**
	 * Unit Footer Area
	 */
	function unit_footer() { 
		$campus_name = UBC_Collab_CLF::get_campus_name();
		
		$unit['address1'] 	= UBC_Collab_Theme_Options::get("clf-unit-address1");
		$unit['address2'] 	= UBC_Collab_Theme_Options::get("clf-unit-address2");
		$unit['city'] 		= UBC_Collab_Theme_Options::get("clf-unit-city");
		$unit['province'] 	= UBC_Collab_Theme_Options::get("clf-unit-province");
		$unit['country'] 	= UBC_Collab_Theme_Options::get("clf-unit-country");
		$unit['postal']		= UBC_Collab_Theme_Options::get("clf-unit-postal");
		$unit['e-mail'] 	= UBC_Collab_Theme_Options::get("clf-unit-e-mail");
		$unit['website'] 	= UBC_Collab_Theme_Options::get("clf-unit-website");
		$unit['phone'] 		= UBC_Collab_Theme_Options::get("clf-unit-phone");
		$unit['fax'] 		= UBC_Collab_Theme_Options::get("clf-unit-fax");
		$unit['facebook']	= UBC_Collab_Theme_Options::get("clf-unit-facebook");
		$unit['twitter']	= UBC_Collab_Theme_Options::get("clf-unit-twitter");
		$unit['google-plus']= UBC_Collab_Theme_Options::get("clf-unit-google-plus");
		$unit['linkedin']	= UBC_Collab_Theme_Options::get("clf-unit-linkedin");
		$unit['youtube']	= UBC_Collab_Theme_Options::get("clf-unit-youtube");
		
    $faculty = UBC_Collab_Theme_Options::get('clf-faculty-address-display');
    // No Faculty
    if (!empty($faculty) && $faculty != "na") {
      $address_faculty = '<div id="ubc7-address-faculty"><a href="'.UBC_Collab_CLF::get_faculty_url($faculty).
      '">'.UBC_Collab_CLF::get_faculty_name($faculty).'</a></div>';
    }

		// Campus Name
		if (!empty($campus_name)) {
			$address_campus = '<div id="ubc7-address-campus">'.$campus_name.'</div>';
		}
		
		foreach ($unit as $k => $v) {
			if (empty($v))
				continue;
			
			switch($k) {
				case 'address1':
					$address_address1 = '<div id="ubc7-address-street">'.$v.'</div>';
					break;
				case 'address2':
					$address_address2 = '<div id="ubc7-address-street2">'.$v.'</div>';
					break;
				case 'city':
					$address_city = '<span id="ubc7-address-city">'.$v.'</span>';
					break;
				case 'province':
					$address_province = '<span id="ubc7-address-province">'.$v.'</span>';
					break;
				case 'country':
					$address_country = '<span id="ubc7-address-country">'.$v.'</span>';
					break;
				case 'postal':
					$address_postal = '<span id="ubc7-address-postal">'.$v.'</span>';
					break;
				case 'e-mail':
					$address_email = '<div id="ubc7-address-email">Email <a href="mailto:'.antispambot($v).'">'.antispambot($v).'</a></div>';
					break;
				case 'phone':
					$address_phone = '<div id="ubc7-address-phone">Tel '.$v.'</div>';
					break;
				case 'fax':
					$address_fax = '<div id="ubc7-address-fax">Fax '.$v.'</div>';
					break;
				case 'website':
					$address_website = '<div id="ubc7-address-website">Website <a href="'.UBC_Collab_CLF::add_http($v).'">'.preg_replace( '#^https?://#', '', $v ).'</a></div>';
					break;
				case 'facebook':
					$social_media['facebook'] = '<a href="'.UBC_Collab_CLF::add_http($v).'" title="Facebook icon"><i class="icon-facebook-sign"></i></a>&nbsp;';
					break;
				case 'twitter':
					$social_media['twitter'] = '<a href="'.UBC_Collab_CLF::add_http($v).'" title="Twitter icon"><i class="icon-twitter-sign"></i></a>&nbsp;';
					break;
				case 'linkedin':
					$social_media['linkedin'] = '<a href="'.UBC_Collab_CLF::add_http($v).'" title="LinkedIn icon"><i class="icon-linkedin-sign"></i></a>&nbsp;';
					break;
				case 'youtube':
					$social_media['youtube'] = '<a href="'.UBC_Collab_CLF::add_http($v).'" title="YouTube icon"><i class="icon-youtube"></i></a>&nbsp;';
					break;
				case 'google-plus':
					$social_media['google-plus'] = '<a href="'.UBC_Collab_CLF::add_http($v).'" title="Google+ icon"><i class="icon-google-plus-sign"></i></a>&nbsp;';
					break;
			}
			
		}

		// Display Social Media Icons only when available
		if (isset($social_media)) {
		
			$social_icons = "";
			
			foreach ($social_media as $v) {
				if (!empty($v))
					$social_icons .= $v;
			}
			
			if (!empty($social_icons) ) {
				$unit_social_media = '<div class="span2">
               	<strong>Find us on</strong>
                <div id="ubc7-unit-social-icons">'.
                $social_icons.
                '</div>
            </div>';
			}
		}
		?>
		<div class="row-fluid expand" id="ubc7-unit-footer">
			<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div class="span10" id="ubc7-unit-address">
                <div id="ubc7-address-unit-name"><?php echo UBC_Collab_CLF::get_unit_name(); ?></div>
                <?php if(isset($address_faculty)) { echo $address_faculty; }?>
                <?php if(isset($address_campus)) { echo $address_campus; } ?>
                <?php if(isset($address_address1)) { echo $address_address1; } ?>
                <?php if(isset($address_address2)) { echo $address_address2; } ?>
                <div id="ubc7-address-location">
                    <?php echo $address_city.", ".$address_province." ".$address_country." ".$address_postal; ?> 
                </div>
                <?php if(isset($address_phone)) { echo $address_phone; } ?>
                <?php if(isset($address_fax)) { echo $address_fax; } ?>
                <?php if(isset($address_website)) { echo $address_website; } ?>
                <?php if(isset($address_email)) { echo $address_email; } ?>
            </div>
            <?php if(isset($unit_social_media)) { echo $unit_social_media; } ?>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </div>		
	<?php }
	function add_http( $url ) {
	    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
	        $url = "http://" . $url;
	    }
	    return esc_url( $url);
	}
	/**
	 * Global Utility Footer
	 */
	function global_utility_footer() { ?>
		<div class="row-fluid expand" id="ubc7-global-footer">
			<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div class="span5" id="ubc7-signature"><a href="http://www.ubc.ca/" title="The University of British Columbia (UBC)">The University of British Columbia</a></div>
            <div class="span7" id="ubc7-footer-menu">
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </div>
        <div class="row-fluid expand" id="ubc7-minimal-footer">
        	<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div class="span12">
                <ul>
                    <li><a href="<?php echo self::$cdn_ref; ?>/emergency" title="Emergency Procedures">Emergency Procedures</a> <span class="divider">|</span></li>
                    <li><a href="<?php echo self::$cdn_ref; ?>/terms" title="Terms of Use">Terms of Use</a> <span class="divider">|</span></li>
                    <li><a href="<?php echo self::$cdn_ref; ?>/copyright" title="UBC Copyright">Copyright</a> <span class="divider">|</span></li>
                    <li><a href="<?php echo self::$cdn_ref; ?>/accessibility" title="Accessibility">Accessibility</a></li>
                </ul>
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </div>
	<?php }

	/**
	 * Footer for JS
	 * 
	 */
	 function wp_footer() { ?>
<!-- Placed javascript at the end for faster loading -->	
<?php wp_print_scripts('clf'); 
	wp_print_scripts('farbtastic');
	  }
	 
	/*******************************
	 * Other
	 *******************************/
	 
	/**
	 *  Add faculty URL to breadcrumb
	 */
	function breadcrumb_trail_items($items) {
		
		if( !UBC_Collab_Theme_Options::get('clf-faculty-breadcrumb') )
			return $items;
			
		$faculty = UBC_Collab_Theme_Options::get('clf-unit-faculty');
		
		
		// No Faculty, return original breadcrumb
		if (empty($faculty) || $faculty == "na") return $items;
		
		$faculty_name = UBC_Collab_CLF::get_faculty_name($faculty);
		$faculty_url  = UBC_Collab_CLF::get_faculty_url($faculty);
		
		if ( !empty( $items ) )
			array_unshift($items,'<a href="'.$faculty_url.'">'.$faculty_name.'</a>');
		
		return $items;		
	}
	
	 
	/** 
	 * Return CLF CSS Path in full
	 * @return Text CSS filename
	 */
	function get_css() {
    	
		// UBC CLF CDN Path and Version
		$url = self::$cdn_css."/";
		$css = UBC_Collab_Theme_Options::get("clf-colour-theme");
		
		switch($css) {
			case "bw":
				$css_file = "ubc-clf-full-bw.min.css";
				break;
			case "gw":
				$css_file = "ubc-clf-full-gw.min.css";
				break;
			case "wg":
				$css_file = "ubc-clf-full-wg.min.css";
				break;
			default:
				$css_file = "ubc-clf-full.min.css";
				break;
		}
		
		return $url.$css_file;	
    }
	
	/**
	 * Return Campus ID tag used for CLF header
	 * @return text campus ID
	 */
	function get_campus_id() {
		$campus = UBC_Collab_Theme_Options::get("clf-campus-identifier");
		switch($campus) {
			case 'v':
				$campus_id = 'ubc7-vancouver-campus';
				break;
			case 'o':
				$campus_id = 'ubc7-okanagan-campus';
				break; 
			default:
				$campus_id = "";
		}
		
		return $campus_id;
	}
	
	/**
	 * Return Campus Name
	 * @param Boolean Uppercase on campus
	 * @return Text Campus Name
	 */
	function get_campus_name($uppercase = true) {
		$campus = UBC_Collab_Theme_Options::get("clf-campus-identifier");
		switch($campus) {
			case 'v':
				$campus_name = 'Vancouver campus';
				break;
			case 'o':
				$campus_name = 'Okanagan campus';
				break; 
			default:
				$campus_name = "";
		}
		if ($uppercase) {
			return ucwords($campus_name);
		} else {
			return $campus_name;
		}
		
	}
	
	/**
	 * Return Unit Name
	 * 
	 * @return Text Unit Name
	 */
	function get_unit_name() {
		return UBC_Collab_Theme_Options::get("clf-unit-name");
	}
	
	/**
	 * Return Faculty Name
	 * 
	 * @return Text Faculty Name
	 */
	function get_faculty_name($faculty) {
		if ($faculty == "na")
			return;
		if (array_key_exists($faculty, UBC_Collab_CLF::ubc_faculty()) ) {
			$faculty_list = UBC_Collab_CLF::ubc_faculty();
			
			return $faculty_list[$faculty]['label'];
		}
	}
	
	/**
	 * Return Faculty URL
	 * 
	 * @return Text Faculty Name
	 */
	function get_faculty_url($faculty) {
		if ($faculty == "na")
			return;
		if (array_key_exists($faculty, UBC_Collab_CLF::ubc_faculty()) ) {
			$faculty_list = UBC_Collab_CLF::ubc_faculty();
			
			return $faculty_list[$faculty]['url'];
		}
	}

	 
	/**
	 * Back to Top
	 */
	function back_to_top() { ?>
		<div class="row-fluid expand ubc7-back-to-top">
			<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="container">'; } ?>
            <div class="span2">
                <a href="#" title="Back to top">Back to top <div class="ubc7-arrow up-arrow grey"></div></a>
            </div>
            <?php if (UBC_Collab_CLF::is_full_width()) { echo '</div>'; } ?>
        </div>
	<?php }

}
UBC_Collab_CLF::init();
