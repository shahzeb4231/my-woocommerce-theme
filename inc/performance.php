<?php
/**
 * Keeps the database calm when many visitors arrive:
 * heavy homepage queries are remembered for a few minutes instead of running on every page view.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function xsn_cache_ver() {
	return (int) get_option( 'xsn_cache_ver', 1 );
}

/** Run $cb once, remember the result for $ttl seconds */
function xsn_remember( $key, $ttl, $cb ) {
	$k = 'xsn_c' . xsn_cache_ver() . '_' . md5( $key );
	$v = get_transient( $k );
	if ( false !== $v ) return $v;
	$v = call_user_func( $cb );
	set_transient( $k, $v, (int) $ttl );
	return $v;
}

/** Forget everything (done automatically when products, categories or sales change) */
function xsn_bust_cache() {
	update_option( 'xsn_cache_ver', xsn_cache_ver() + 1, false );
}
foreach ( array( 'save_post_product', 'woocommerce_update_product', 'woocommerce_new_product', 'trashed_post', 'deleted_post', 'woocommerce_scheduled_sales', 'edited_product_cat', 'created_product_cat', 'delete_product_cat' ) as $hook ) {
	add_action( $hook, 'xsn_bust_cache' );
}

/** Load products by ID, keeping the given order */
function xsn_products_by_ids( $ids ) {
	if ( empty( $ids ) || ! function_exists( 'wc_get_products' ) ) return array();
	$found = wc_get_products( array( 'include' => $ids, 'status' => 'publish', 'limit' => count( $ids ) ) );
	$map   = array();
	foreach ( $found as $p ) { $map[ $p->get_id() ] = $p; }
	$out = array();
	foreach ( $ids as $id ) { if ( isset( $map[ $id ] ) ) $out[] = $map[ $id ]; }
	return $out;
}

/** Flash sale data: product IDs + the soonest real sale end date */
function xsn_flash_data() {
	if ( ! function_exists( 'wc_get_product_ids_on_sale' ) ) return array( 'ids' => array(), 'deadline' => 0 );
	$ids = wc_get_product_ids_on_sale();
	if ( empty( $ids ) ) return array( 'ids' => array(), 'deadline' => 0 );
	$items = wc_get_products( array( 'include' => array_slice( $ids, 0, 12 ), 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
	$out   = array();
	$ends  = array();
	foreach ( $items as $p ) {
		$out[] = $p->get_id();
		$to    = $p->get_date_on_sale_to();
		if ( $to && $to->getTimestamp() > time() ) $ends[] = $to->getTimestamp();
	}
	return array( 'ids' => $out, 'deadline' => $ends ? min( $ends ) : 0 );
}

// Lighter front end
add_action( 'init', function () {
	if ( ! is_admin() ) wp_deregister_script( 'heartbeat' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
}, 1 );
