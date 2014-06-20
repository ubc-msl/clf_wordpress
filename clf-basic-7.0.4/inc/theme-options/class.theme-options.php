<?php
/**
 * ubc-collab Theme Options
 *
 * @package ubc-collab
 * @since ubc-collab 1.0
 */

require( get_template_directory() . '/inc/theme-options/class.theme-options-admin.php' );

Class UBC_Collab_Theme_Options {
	
	private static $options;
	
	function init(){
	
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		
	}
	
	/**
	 * Register the form setting for our ubc_collab_options array.
	 *
	 * This function is attached to the admin_init action hook.
	 *
	 * This call to register_setting() registers a validation callback, ubc_collab_theme_options_validate(),
	 * which is used when the option is saved, to ensure that our option values are properly
	 * formatted, and safe.
	 *
	 * @since ubc-collab 1.0
	 */
	function admin_init(){
		register_setting(
			'ubc_collab_options', // Options group, see settings_fields() call in ubc_collab_theme_options_render_page()
			'ubc-collab-theme-options', // Database option, see ubc_collab_get_theme_options()
			array( __CLASS__ ,'validate' ) // The sanitization callback, see ubc_collab_theme_options_validate()
		);

	}
	
	function update( $key, $value ) {
		global $blog_id;
		
		$options = self::get();
		
		if( is_array( $key ) && is_array( $value ) ):
			
			foreach( $key as $item => $key_value ):
				
				if( isset( $value[$item]) )
					$options[$item] = $value[$item];
					
			endforeach;
		else:
			$options[$key] = $value;
		endif;
		
		// $options = self::validate( $options  ); // don't know if this is redundent
		
		self::$options[$blog_id] =  $options; 
		
		update_option( 'ubc-collab-theme-options', $options );
		
	}
	
	/**
	 * get function.
	 * 
	 * @access public
	 * @param mixed $key (default: null)
	 * @return void
	 */
	function get( $key = null ) {
		global $blog_id;
		if( isset($key) && isset( self::$options[$blog_id][$key] ) && is_string(self::$options[$blog_id][$key]) )
			return  stripslashes( self::$options[$blog_id][$key] );
				
		$saved = get_option( 'ubc-collab-theme-options' );
		
		if( !$saved ):
			$backward_compatibility_options = (array) get_option('clf_base');
			// backwards compatibility 
			$backward = (array) apply_filters( 'ubc_collab_backward_compatibility_theme_options', array(), $backward_compatibility_options ) ;
			
			if( !empty( $backward ) ):
				
				$saved = wp_parse_args( $saved, $backward );
				
			endif;
		endif;
		$defaults = (array) apply_filters( 'ubc_collab_default_theme_options', array() );
		
		if( !empty( $defaults )  ):
			$options = wp_parse_args( $saved, $defaults );
			
			$options = array_intersect_key( $options, $defaults );
			
		endif;
		
		if( isset($key) && isset( $options[$key] ) ):
			self::$options[$blog_id][$key] =  $options[$key];
			return  stripslashes( $options[$key] );
		endif;
		
		self::$options[$blog_id][$key] =  $options;
		return $options;
	
	}
	
	/**
	 * validate function.
	 * 
	 * @access public
	 * @param mixed $input
	 * @return void
	 */
	function validate( $input  ) {
		$output = array();
		
		// clear the super cache 
		if( function_exists( 'wp_cache_clear_cache' ) )
			wp_cache_clear_cache();
		
		return apply_filters( 'ubc_collab_theme_options_validate', $output, $input );
	}
	
	/* different fields */
	
	/**
	 * radio function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $label
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function radio( $key, $value, $label, $echo = true ) {
		
		$checked = checked( $value, UBC_Collab_Theme_Options::get( $key ), false );
		$name = 'ubc-collab-theme-options['.$key.']';
		$input = sprintf( '<label><input type="radio" name="%s" value="%s" %s class="on-change"  /> %s</label>', esc_attr($name), esc_attr($value), $checked, esc_html($label) );

		if( $echo )
			echo $input; return;
		
		return $input;
	}
	
	/**
	 * input function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function input( $key, $echo = true ) {
		
		$value = UBC_Collab_Theme_Options::get( $key );
		$name = 'ubc-collab-theme-options['.$key.']';
		$input = sprintf( '<input type="text" name="%s" value="%s" class="on-change"  />', esc_attr($name), esc_attr($value) );

		if( $echo )
			echo $input; return;
		
		return $input;
	}
	/**
	 * checkbox function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $label
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function checkbox( $key, $value, $label, $echo = true ) {
		
		// var_dump( self::$options, UBC_Collab_Theme_Options::get( $key ) );
		$checked = checked( $value, UBC_Collab_Theme_Options::get( $key ), false );
		$name = 'ubc-collab-theme-options['.$key.']';
		$id   = 'ubc-collab-theme-options-'.$key;
		$input = sprintf( '<label for="%s"><input id="%s" type="checkbox" name="%s" value="%s" %s class="on-change"  /> %s</label>', esc_attr($id),esc_attr($id),esc_attr($name), esc_attr($value), $checked, esc_html($label) );

		if( $echo )
			echo $input; return;
		
		return $input;
	}
	
	/**
	 * text function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param string $label (default: "")
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function text( $key, $label="", $helper="", $echo = true, $maxlength = "", $class='' ) {
		
		$value = UBC_Collab_Theme_Options::get($key);
		$name = 'ubc-collab-theme-options['.$key.']';
	
		$key = esc_attr($key);
		
				
		$maxlength_text = "";
		if (is_numeric($maxlength))
			$maxlength_text = 'size="'.$maxlength.'"';
		
		$helper = 	( empty( $helper ) ? '' : '<small>'.esc_html($helper).'</small>');
		
		$label = 	( empty( $label ) ? '' : sprintf('<label class="description" for="%s">%s:</label><br />', $key, $label ) );
		
		$input = sprintf( '<input  type="text" name="%s" id="%s" value="%s" %s class="on-keypress ubc-collab-text-input '.$class.'" />', esc_attr($name), esc_attr($key), esc_attr( $value ), $maxlength_text );
		
		$html = sprintf('%s %s %s', $label, $input, $helper );
		
		if ( $echo )
			echo $html;
		else
			return $html;
	}
	
	function color_picker( $key ){
		$value = UBC_Collab_Theme_Options::get($key);
		$name = 'ubc-collab-theme-options['.$key.']';
		$id   = $key;
		$html = sprintf( '<input type="text" name="%s" id="%s"  class="on-keypress colour-picker" value="%s" />' , esc_attr( $name ),esc_attr( $id ), esc_attr( $value ) );
		echo($html);
	}
	
	/**
	 * textarea function.
	 * 
	 */
	 function textarea( $key, $label="", $helper="", $echo = true, $rows=4, $cols=80, $class="" ) {
		
		$value = UBC_Collab_Theme_Options::get($key);
		$name = 'ubc-collab-theme-options['.$key.']';
	
		$key = esc_attr($key);
		
		$helper = 	( empty( $helper ) ? '' : '<small>'.esc_html($helper).'</small>');
		
		$label = 	( empty( $label ) ? '' : sprintf('<label class="description" for="%s">%s:</label><br />', $key, $label ) );
		$class_name = ( empty($class) ? 'class="ubc-collab-textarea"' : 'class=" ubc-collab-textarea '.$class.'"' );
		$input = sprintf( '<textarea name="%s" id="%s" rows="%d" cols="%d" %s >%s</textarea>', esc_attr($name), $key, $rows, $cols,$class_name, esc_textarea(  $value ) );
		
		$html = sprintf('%s %s %s', $label, $input, $helper );
		
		if ( $echo )
			echo $html;
		else
			return $html;
	}
	
	/**
	 * option function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $label
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function option ( $key, $value, $label, $echo = true ) {
		$selected = selected( $value, UBC_Collab_Theme_Options::get($key), false );
		$input = sprintf( '<option value="%s" %s>%s</option>', esc_attr($value), $selected, esc_attr($label));
		
		if ($echo)
			echo $input;
		else
			return $input;
		
	}
	
	/**
	 * select_categories function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function select_categories( $key,  $echo = true ) { 
		$name = 'ubc-collab-theme-options['.$key.']'; 
		$input = '<select name="'.$name.'">';
		$input .= UBC_Collab_Theme_Options::option( $key, 0, __( '&mdash; Select &mdash;' ) , false );
		$input .= UBC_Collab_Theme_Options::option( $key, 'all', __( '*All Categories' ) , false );
		
		foreach ( get_categories() as $category ) {
			$input .= UBC_Collab_Theme_Options::option( $key, $category->cat_ID, $category->name , false );
		}     
		$input .='</select>';
		
		if ($echo)
			echo $input;
		else
			return $input;
	
	}
	
	/**
	 * select_pages function.
	 * 
	 * @access public
	 * @param mixed $key
	 * @param mixed $value
	 * @param bool $echo (default: true)
	 * @return void
	 */
	function select_pages( $key, $echo = true) {
		
		$name = 'ubc-collab-theme-options['.$key.']';
		
		$selected = UBC_Collab_Theme_Options::get( $key );
		
		return wp_dropdown_pages( array( 'name' => $name, 'echo' => $echo, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => $selected ) );

	}
	
	/**
	 * validate_text function.
	 * 
	 * @access public
	 * @param mixed $input
	 * @param mixed $default
	 * @return void
	 */
	function validate_text( $input, $default ) {
		if ( isset( $input ) && !empty( $input) ) {
		    return esc_html( $input );
		}
		else {
			return $default;
		}
	}
	
}
UBC_Collab_Theme_Options::init();

