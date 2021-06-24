<?php
/**
* Custom AJax requests file, much faster than the standard admin-ajax.php file as it doesn't load the backend hooks.
* Future improvements: Avoid the loading of plugins
*/

/**
 *
 * This Feature is disabled right now, it caused 403 error on some servers
 */

// Set Ajax and
define( 'DOING_AJAX', true );

// Laod basic WordPress functionality only
// May be in the future
//define( 'SHORTINIT',  true );


// load the main wp-laod file
$base = dirname(__FILE__);
$path = false;

// Find the path to the wp-load.php
if( file_exists( '../../../../wp-load.php' ) ){
	$path = '../../../../wp-load.php';
}
elseif( file_exists( '../../../../../wp-load.php' ) ){
	$path = '../../../../../wp-load.php';
}
elseif( file_exists( '../../../../../../wp-load.php' ) ){
	$path = '../../../../../../wp-load.php';
}

if( $path != false ){
	require_once( $path );
}
else{
	die( 'wp-load path error' );
}

// Load the only files we want
/*

	May be in the future

*/

/** Allow for cross-domain requests (from the front end). */
send_origin_headers();

// Require an action parameter
if ( empty( $_REQUEST['action'] ) ){
	wp_die( '0', 400 );
}

/** Setup headers */
@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
@header( 'X-Robots-Tag: noindex' );

// Send a HTTP header to disable content type sniffing in browsers which support it.
send_nosniff_header();

// Set headers to prevent caching for browsers.
nocache_headers();


$action = esc_attr( trim( $_POST['action'] ) );

if( is_user_logged_in() ){
	do_action( 'wp_ajax_'.$action );
}
else{
	do_action( 'wp_ajax_nopriv_'.$action );
}

// Default status
wp_die( '0' );
