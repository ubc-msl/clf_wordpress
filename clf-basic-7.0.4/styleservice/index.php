<?php 

$version = ( isset( $_GET['v'] ) ?  $_GET['v'] : 1 ); // version so we can update the the latest version
header("Content-type: text/css; charset: UTF-8");
$bootstrap_data = get_bootstrap_data();
$file = $bootstrap_data['file'];



// be included
$file =  $file.'_v-'.$version; 

$path_to_file = __DIR__.'/static/'.$file.'.css';



// get the cached file
if( file_exists( $path_to_file ) && false ) {
	// return the file 
	echo file_get_contents( $path_to_file );
	die();
}

require "lessphp/lessc.inc.php";

$less = new lessc;

// load the variables file 


/*
$bootstrap 
$less_content 
*/
$file_content = '';
$less_to_compile = '';

$less->addImportDir(__DIR__."/bootstrap/"); // set the directory

// we need to do it this way otherwise we can't owerwrite the variables
$less_to_compile .= file_get_contents( __DIR__."/bootstrap/variables.less" ); // INCLUDE THE DEFAULT BOOTSTRAP VARIABLES 

$less_to_compile .= ' @import "mixins.less"; '; 

// Variables 
if( isset( $_GET['o'] ) ):
	$options = explode(";", trim( urldecode( $_GET['o'] ) ) );
	foreach( $options as $option )
		$less_to_compile .= " @".$option."; ";
	
endif;

$less_to_compile .= $bootstrap_data['less_to_compile'];

$file_content.=  $less->compile( $less_to_compile );

file_put_contents ( $path_to_file, $file_content );
echo $file_content;
die();

/**
 * get_bootstrap_data function.
 * 
 * @access public
 * @return void
 */
function get_bootstrap_data( ) {
	if( !isset( $_GET['b'] ) )
		return array('file' => 'no-bootstrap', 'less_to_compile' => '');
	
	$files_to_include = array();
	
	$files_to_include_from_url = explode( ',', str_replace('.','', urldecode( $_GET['b'] ) ) );
	
	foreach( $files_to_include_from_url as $file_to_include )
		$files_to_include[] = trim( $file_to_include ).'.less';
	
	
	//
	$file_name_array = array();
	$less_to_compile = '';
	// get all the files inside the bootstrap directory as an array 
	$bootstrap_files = scandir(__DIR__."/bootstrap/");
	
	foreach( $bootstrap_files as $bootstrap_file ):
		// check if the content directory file array of the url is acctaully - is the file that we want even in the array
		// construct the array
		// $bootstrap_file = substr($bootstrap_file)
		if( in_array( $bootstrap_file, $files_to_include ) ):
			
			$file_name_array[] = substr($bootstrap_file,0,-5);
			
			$less_to_compile .=' @import "'.$bootstrap_file.'"; '. PHP_EOL;
		endif;
		
	endforeach;
	
	// loop though the directoy content array and check if the files is in the url array
	// if it is add it to the filename string
	$file_name = 'b-'.implode('-',$file_name_array);
	// return both the new file and also all the files to include this way we don't have to check again if they exist or not
	
	return array( 'file' => $file_name, 'less_to_compile' =>$less_to_compile );
}
