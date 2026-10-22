<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function xsn_ver( $rel ) {
	$p = XSN_DIR . $rel;
	return file_exists( $p ) ? filemtime( $p ) : XSN_VERSION;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'xsn-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap',
		array(), null
	);

	$css  = array( 'variables', 'base', 'components', 'sections', 'sections-2', 'sections-3', 'woocommerce', 'sections-4', 'account', 'responsive' );
	$prev = array( 'xsn-fonts' );
	foreach ( $css as $name ) {
		$rel = "/assets/css/{$name}.css";
		wp_enqueue_style( "xsn-{$name}", XSN_URI . $rel, $prev, xsn_ver( $rel ) );
		$prev = array( "xsn-{$name}" );
	}
	wp_enqueue_style( 'xsn-style', get_stylesheet_uri(), $prev, XSN_VERSION );

	$js = array( 'main', 'mobile-menu', 'marketplace', 'shop' );
	if ( is_front_page() ) {
		$js = array_merge( $js, array( 'faq', 'tracking', 'countdown', 'booking', 'fleet' ) );
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$js[] = 'account';
	}
	foreach ( $js as $name ) {
		$rel = "/assets/js/{$name}.js";
		wp_enqueue_script( "xsn-{$name}", XSN_URI . $rel, array(), xsn_ver( $rel ), true );
	}

	// Data for the scripts (ajax URL, nonce, wishlist)
	wp_localize_script( 'xsn-main', 'XSN', array(
		'ajax'   => admin_url( 'admin-ajax.php' ),
		'nonce'  => wp_create_nonce( 'xsn' ),
		'logged' => is_user_logged_in(),
		'login'  => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url(),
		'wish'   => function_exists( 'xsn_wishlist_ids' ) ? xsn_wishlist_ids() : array(),
	) );
}, 20 );

add_filter( 'wp_resource_hints', function ( $urls, $type ) {
	if ( 'preconnect' === $type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );
