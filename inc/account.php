<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'WooCommerce' ) ) return;

/** Is this user a Dokan vendor? */
function xsn_is_vendor( $uid = 0 ) {
	$uid = $uid ? (int) $uid : get_current_user_id();
	return $uid && function_exists( 'dokan_is_user_seller' ) && dokan_is_user_seller( $uid );
}

/** Has the admin approved selling for this vendor? */
function xsn_vendor_enabled( $uid = 0 ) {
	$uid = $uid ? (int) $uid : get_current_user_id();
	return $uid && function_exists( 'dokan_is_seller_enabled' ) && dokan_is_seller_enabled( $uid );
}

function xsn_vendor_dashboard_url() {
	return function_exists( 'dokan_get_navigation_url' ) ? dokan_get_navigation_url() : '';
}

// Hide the empty Downloads tab when the customer has no downloads
add_filter( 'woocommerce_account_menu_items', function ( $items ) {
	if ( isset( $items['downloads'] ) && function_exists( 'WC' ) && WC()->customer ) {
		$downloads = WC()->customer->get_downloadable_products();
		if ( empty( $downloads ) ) unset( $items['downloads'] );
	}
	return $items;
}, 20 );

// Body class for the logged-out sign in / register screen
add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		$classes[] = 'xsn-guest-account';
	}
	return $classes;
} );
