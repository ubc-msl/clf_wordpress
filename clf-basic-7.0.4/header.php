<?php
/**
 * Header Template
 *
 * The header template is generally used on every page of your site. Nearly all other
 * templates call it somewhere near the top of the file. It is used mostly as an opening
 * wrapper, which is closed with the footer.php file. It also executes key functions needed
 * by the theme, child themes, and plugins. 
 *
 *
 */
?>
<!DOCTYPE html>
<!--[if IEMobile 7]><html class="iem7 oldie" <?php language_attributes(); ?>><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html class="ie7 oldie" <?php language_attributes(); ?>><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html class="ie8 oldie" <?php language_attributes(); ?>><![endif]-->
<!--[if (IE 9)&!(IEMobile)]><html class="ie9" <?php language_attributes(); ?>><![endif]-->
<!--[[if (gt IE 9)|(gt IEMobile 7)]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<title><?php hybrid_document_title(); ?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width" /> <!-- needed for responsive -->
<link rel="dns-prefetch" href="//cdn.ubc.ca/" />

<?php do_action('before_css'); ?>

<link rel="profile" href="http://gmpg.org/xfn/11" />

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php wp_head(); // wp_head ?>

</head>

<body class="<?php hybrid_body_class(); ?>">

<?php do_atomic( 'before_html' ); // hybrid_before_html ?>

<div id="body-container" <?php if (!UBC_Collab_CLF::is_full_width()) { echo 'class="container"'; } ?>>

	<?php do_atomic( 'before_header' ); // hybrid_before_header ?>
	
	<?php do_atomic( 'header' ); // hybrid_header ?>
    
	<?php do_atomic( 'after_header' ); // hybrid_after_header ?>
	
	<?php if (UBC_Collab_CLF::is_full_width()) { echo '<div class="full-width-container">'; } ?>
	
	<div id="container" class="expand" >
	
		<?php do_atomic( 'before_container' ); // hybrid_before_container ?>