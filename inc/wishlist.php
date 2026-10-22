<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'WooCommerce' ) ) return;

function xsn_wishlist_ids( $uid = 0 ) {
	$uid = $uid ? (int) $uid : get_current_user_id();
	if ( ! $uid ) return array();
	$ids = get_user_meta( $uid, 'xsn_wishlist', true );
	return is_array( $ids ) ? array_values( array_map( 'intval', $ids ) ) : array();
}

// Add / remove a product (logged-in customers)
add_action( 'wp_ajax_xsn_wish', function () {
	check_ajax_referer( 'xsn', 'nonce' );
	$pid = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	if ( ! $pid || 'product' !== get_post_type( $pid ) ) wp_send_json_error();
	$ids = xsn_wishlist_ids();
	$on  = in_array( $pid, $ids, true );
	$ids = $on ? array_values( array_diff( $ids, array( $pid ) ) ) : array_merge( $ids, array( $pid ) );
	update_user_meta( get_current_user_id(), 'xsn_wishlist', $ids );
	wp_send_json_success( array( 'on' => ! $on, 'count' => count( $ids ) ) );
} );

// My account > Wishlist
add_action( 'init', function () {
	add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
} );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'wishlist';
	return $vars;
} );
add_action( 'init', function () {
	if ( get_option( 'xsn_rewrite_ver' ) !== '3' ) {
		flush_rewrite_rules( false );
		update_option( 'xsn_rewrite_ver', '3' );
	}
}, 99 );

add_filter( 'woocommerce_account_menu_items', function ( $items ) {
	$out = array();
	foreach ( $items as $k => $v ) {
		$out[ $k ] = $v;
		if ( 'orders' === $k ) $out['wishlist'] = 'Wishlist';
	}
	if ( ! isset( $out['wishlist'] ) ) $out['wishlist'] = 'Wishlist';
	return $out;
}, 15 );

add_filter( 'woocommerce_endpoint_wishlist_title', function () { return 'Wishlist'; } );

add_action( 'woocommerce_account_wishlist_endpoint', function () {
	$ids = xsn_wishlist_ids();
	echo '<h2 class="acc-title">Your wishlist</h2>';
	if ( ! $ids ) {
		echo '<div class="acc-empty"><p>You have not saved any favourites yet. Tap the heart on a product to save it here.</p><a class="btn btn-primary btn-sm" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Browse the marketplace</a></div>';
		return;
	}
	$products = wc_get_products( array( 'include' => $ids, 'status' => 'publish', 'limit' => -1 ) );
	echo '<div class="wish-grid">';
	foreach ( $products as $p ) {
		get_template_part( 'template-parts/cards/product-card', null, xsn_card_args( $p ) );
	}
	echo '</div>';
} );
