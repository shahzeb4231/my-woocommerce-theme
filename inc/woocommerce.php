<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'WooCommerce' ) ) return;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
} );

// Product images: 3:4 portrait
add_filter( 'woocommerce_get_image_size_thumbnail', function () {
	return array( 'width' => 600, 'height' => 800, 'crop' => 1 );
} );
add_filter( 'woocommerce_get_image_size_single', function () {
	return array( 'width' => 900, 'height' => 1200, 'crop' => 1 );
} );

add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

// Theme wrapper for shop and product pages
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', function () {
	echo '<section class="section shop-section"><div class="container">';
} );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div></section>';
} );

// Live cart updates (header cart count)
add_action( 'wp_enqueue_scripts', function () {
	if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) wp_enqueue_script( 'wc-cart-fragments' );
	if ( wp_script_is( 'wc-add-to-cart', 'registered' ) )    wp_enqueue_script( 'wc-add-to-cart' );
}, 30 );

function xsn_cart_count() {
	return ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
}
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['span.cart-count'] = '<span class="cart-count">' . xsn_cart_count() . '</span>';
	return $fragments;
} );

/** "-17%" style badge for simple products, "Sale" otherwise */
function xsn_sale_badge( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) return '';
	if ( $product->is_type( 'simple' ) ) {
		$reg  = (float) $product->get_regular_price();
		$sale = (float) $product->get_sale_price();
		if ( $reg > 0 && $sale > 0 && $sale < $reg ) {
			return '-' . round( ( 1 - $sale / $reg ) * 100 ) . '%';
		}
	}
	return 'Sale';
}
add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	$t = xsn_sale_badge( $product );
	return $t ? '<span class="onsale">' . esc_html( $t ) . '</span>' : $html;
}, 10, 3 );

/** Arguments for template-parts/cards/product-card.php */
function xsn_card_args( $p, $extra = array() ) {
	$terms = get_the_terms( $p->get_id(), 'product_cat' );
	$term  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	return array_merge( array(
		'id'       => $p->get_id(),
		'name'     => $p->get_name(),
		'cat'      => $term ? $term->name : 'Shop',
		'slug'     => $term ? $term->slug : 'shop',
		'price'    => $p->get_price_html(),
		'thumb'    => $p->get_image( 'woocommerce_thumbnail' ),
		'url'      => $p->get_permalink(),
		'badge'    => xsn_sale_badge( $p ),
		'cart_url' => $p->add_to_cart_url(),
		'ajax'     => $p->supports( 'ajax_add_to_cart' ) && $p->is_purchasable() && $p->is_in_stock(),
		'delay'    => 0,
	), $extra );
}

// Product page: cleaner description tab and trust row under the cart button
add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_action( 'woocommerce_single_product_summary', function () {
	get_template_part( 'template-parts/product-trust' );
}, 35 );

// Shop page: filter bar and toolbar wrapper
add_action( 'woocommerce_before_shop_loop', function () {
	get_template_part( 'template-parts/shop-filters' );
}, 5 );
add_action( 'woocommerce_before_shop_loop', function () { echo '<div class="shop-toolbar">'; }, 19 );
add_action( 'woocommerce_before_shop_loop', function () { echo '</div>'; }, 31 );

// "In stock only" filter (?instock=1)
add_action( 'woocommerce_product_query', function ( $q ) {
	if ( ! empty( $_GET['instock'] ) ) {
		$meta   = (array) $q->get( 'meta_query' );
		$meta[] = array( 'key' => '_stock_status', 'value' => 'instock' );
		$q->set( 'meta_query', $meta );
	}
} );

// Dokan blocks owners from buying their own products. Allowed while testing (see functions.php).
add_action( 'init', function () {
    if ( defined( 'XSN_ALLOW_OWN_PURCHASE' ) && XSN_ALLOW_OWN_PURCHASE ) {
        remove_filter( 'woocommerce_is_purchasable', 'dokan_vendor_own_product_purchase_restriction', 10 );
        remove_filter( 'woocommerce_product_review_comment_form_args', 'dokan_vendor_product_review_restriction' );
    }
}, 99 );
