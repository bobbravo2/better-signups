<?php
/**
 * WordPress User Page
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package WordPress
 */
			
	// Redirect to https login if forced to use SSL
	if ( force_ssl_admin() && ! is_ssl() ) {
		if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
			wp_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
			exit();
		} else {
			wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			exit();
		}
	}

	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';
	$errors = new WP_Error();
	
	
	if ( isset( $_GET['key'] ) )
		$action = 'resetpass';
	
	// validate action so as to default to the login screen
	if ( !in_array( $action, array( 'postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login' ), true ) && false === has_filter( 'login_form_' . $action ) )
		$action = 'login';
	
	nocache_headers();
	
	header('Content-Type: '.get_bloginfo( 'html_type' ).'; charset='.get_bloginfo( 'charset' ) );
	
	if ( defined( 'RELOCATE' ) && RELOCATE ) { // Move flag is set
		if ( isset( $_SERVER['PATH_INFO'] ) && ( $_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF'] ) )
			$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );
	
		$url = dirname( set_url_scheme( 'http://' .  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ) );
		if ( $url != get_option( 'siteurl' ) )
			update_option( 'siteurl', $url );
	}
	
	//Set a cookie now to see if they are supported by the browser.
	setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN );
	if ( SITECOOKIEPATH != COOKIEPATH )
		setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN );
	
	//debug
	if ( defined ( 'BSIGN_DEBUG' ) )
		_debug_echo( '$action : ' . $action , __FILE__ , __LINE__ );
	
	// allow plugins to override the default actions, and to add extra actions if they want
	do_action( 'login_init' );
	do_action( 'login_form_' . $action );
	
	do_login_form();
