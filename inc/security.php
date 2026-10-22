<?php
/**
 * Basic hardening: login throttling, generic login errors, less information leaking,
 * security headers, weak password check.
 * (On the live server also use Cloudflare + a security plugin, see the notes.)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Visitor IP. Forwarded headers are trusted only when XSN_BEHIND_CLOUDFLARE is set. */
function xsn_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
	if ( defined( 'XSN_BEHIND_CLOUDFLARE' ) && XSN_BEHIND_CLOUDFLARE && ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : 'unknown';
}

/* ---------- 1. Login throttling (stops password guessing) ---------- */
function xsn_lk( $type, $value ) {
	return 'xsn_lf_' . $type . '_' . md5( strtolower( (string) $value ) );
}

/** 6 failures from one IP, or 12 failures on one account, within 15 minutes = locked for 15 minutes */
function xsn_login_locked( $username ) {
	return (int) get_transient( xsn_lk( 'ip', xsn_client_ip() ) ) >= 6
		|| (int) get_transient( xsn_lk( 'u', $username ) ) >= 12;
}

add_filter( 'authenticate', function ( $user, $username ) {
	if ( ! is_string( $username ) || '' === $username ) return $user;
	if ( xsn_login_locked( $username ) ) {
		// Do not even check the password while locked (saves server work during an attack)
		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
		remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
		return new WP_Error( 'xsn_locked', 'Too many failed attempts. Please wait 15 minutes and try again.' );
	}
	return $user;
}, 1, 2 );

add_action( 'wp_login_failed', function ( $username ) {
	if ( ! is_string( $username ) || '' === $username ) return;
	if ( xsn_login_locked( $username ) ) return; // blocked attempts do not extend the lock
	foreach ( array( xsn_lk( 'ip', xsn_client_ip() ), xsn_lk( 'u', $username ) ) as $k ) {
		set_transient( $k, (int) get_transient( $k ) + 1, 15 * MINUTE_IN_SECONDS );
	}
} );

add_action( 'wp_login', function ( $login ) {
	delete_transient( xsn_lk( 'u', $login ) );
	delete_transient( xsn_lk( 'ip', xsn_client_ip() ) );
} );

// Same message for "wrong user" and "wrong password" so attackers cannot find real accounts
add_filter( 'authenticate', function ( $user ) {
	if ( is_wp_error( $user ) && in_array( $user->get_error_code(), array( 'invalid_username', 'invalid_email', 'incorrect_password' ), true ) ) {
		return new WP_Error( 'invalid_login', 'Incorrect email/username or password.' );
	}
	return $user;
}, 99 );

/* ---------- 2. Leak less information ---------- */
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );

add_action( 'template_redirect', function () {
	if ( ! is_user_logged_in() && isset( $_GET['author'] ) ) { // ?author=1 user guessing
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );

/* ---------- 3. Security headers ---------- */
add_action( 'send_headers', function () {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
	if ( is_ssl() ) header( 'Strict-Transport-Security: max-age=31536000' );
} );

/* ---------- 4. No theme/plugin editing from wp-admin ---------- */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) define( 'DISALLOW_FILE_EDIT', true );

/* ---------- 5. Weak password check (used by registration) ---------- */
function xsn_weak_password( $pass, $email = '' ) {
	$p      = strtolower( (string) $pass );
	$common = array( 'password', 'password1', 'password123', '12345678', '123456789', '1234567890', 'qwertyui', 'qwerty123', 'azerty123', 'iloveyou', 'admin123', 'welcome1', 'abc12345', '11111111', '00000000', 'letmein1', 'passw0rd', 'senegal1', 'dakar123', 'xassida1', 'xassidasn' );
	if ( in_array( $p, $common, true ) ) return true;
	if ( preg_match( '/^(.)\1+$/', (string) $pass ) ) return true;   // one character repeated
	if ( preg_match( '/^[0-9]+$/', (string) $pass ) ) return true;   // digits only
	$local = strtolower( (string) strstr( (string) $email, '@', true ) );
	if ( strlen( $local ) >= 4 && false !== strpos( $p, $local ) ) return true; // contains the email name
	return false;
}
