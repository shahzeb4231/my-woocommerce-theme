<?php
$products = array();

if ( class_exists( 'WooCommerce' ) ) {
	$ids = xsn_remember( 'market_ids', 300, function () {
		return wc_get_products( array(
			'status'     => 'publish',
			'visibility' => 'catalog',
			'limit'      => 8,
			'orderby'    => 'date',
			'order'      => 'DESC',
			'return'     => 'ids',
		) );
	} );
	foreach ( xsn_products_by_ids( $ids ) as $p ) {
		$products[] = xsn_card_args( $p );
	}
}

// Demo products until real WooCommerce products are added
if ( empty( $products ) ) {
	$demo = array(
		array( 'Royal Emerald Boubou',          'Fashion',     'fashion',     '24 000 CFA', 'New' ),
		array( 'Gold Trim Hijab & Abaya Set',   'Fashion',     'fashion',     '18 500 CFA', '' ),
		array( 'Studio Wireless Headphones',    'Electronics', 'electronics', '60 000 CFA', 'Hot' ),
		array( 'Emerald Ceramic Vase Set',      'Home',        'home',        '12 000 CFA', '' ),
		array( 'Signature Oud Perfume',         'Beauty',      'beauty',      '30 000 CFA', 'New' ),
		array( 'Premium Oud Embroidered Kaftan','Fashion',     'fashion',     '22 000 CFA', '' ),
		array( 'Luxe Home Accent Lamp',         'Home',        'home',        '13 000 CFA', '' ),
		array( 'Flowing Emerald Abaya',         'Fashion',     'fashion',     '18 000 CFA', 'Sale' ),
	);
	foreach ( $demo as $d ) {
		$products[] = array(
			'id' => 0, 'name' => $d[0], 'cat' => $d[1], 'slug' => $d[2],
			'price' => $d[3], 'thumb' => '', 'url' => '#marketplace', 'badge' => $d[4],
			'cart_url' => '', 'ajax' => false,
		);
	}
}

$tabs = array();
foreach ( $products as $p ) { $tabs[ $p['slug'] ] = $p['cat']; }

$shop_url = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : '#marketplace';
?>
<section class="section section--white" id="marketplace">
	<div class="container">

		<div class="market-head reveal">
			<div>
				<span class="eyebrow">Marketplace</span>
				<h2>Shop premium, <span class="text-gold">delivered fast</span></h2>
				<p>Fashion, electronics, home and more, curated and delivered across Senegal and beyond.</p>
			</div>
			<div class="filter-tabs" role="tablist" aria-label="Product categories">
				<button class="tab is-active" type="button" data-filter="all">All</button>
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<button class="tab" type="button" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="product-grid" data-products>
			<?php foreach ( $products as $i => $p ) :
				$p['delay'] = ( $i % 4 ) * 0.07;
				get_template_part( 'template-parts/cards/product-card', null, $p );
			endforeach; ?>
		</div>

		<div class="market-cta reveal">
			<a class="btn btn-primary" href="<?php echo esc_url( $shop_url ); ?>">Explore full marketplace</a>
		</div>

	</div>
</section>
