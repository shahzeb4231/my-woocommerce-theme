<?php
if ( ! class_exists( 'WooCommerce' ) ) return;

$ids = xsn_remember( 'tailored_ids', 600, function () {
	$pool = wc_get_products( array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 24,
		'orderby'    => 'date',
		'order'      => 'DESC',
	) );
	// Best sellers first, then best rated
	usort( $pool, function ( $a, $b ) {
		$s = (int) $b->get_total_sales() <=> (int) $a->get_total_sales();
		return $s ? $s : ( (float) $b->get_average_rating() <=> (float) $a->get_average_rating() );
	} );
	return array_map( function ( $p ) { return $p->get_id(); }, array_slice( $pool, 0, 8 ) );
} );
if ( count( $ids ) < 3 ) return;

$items = xsn_products_by_ids( $ids );
if ( count( $items ) < 3 ) return;
?>
<section class="section section--white" id="tailored">
	<div class="container">
		<div class="market-head reveal">
			<div>
				<span class="eyebrow">Tailored for you</span>
				<h2>Customer <span class="text-gold">favourites</span></h2>
				<p>Hand-picked products our shoppers love the most.</p>
			</div>
			<div class="scroll-ctrl">
				<button class="ctrl" type="button" data-scroll-prev aria-label="Previous">&larr;</button>
				<button class="ctrl" type="button" data-scroll-next aria-label="Next">&rarr;</button>
			</div>
		</div>

		<div class="scroller-track" data-scroller>
			<?php foreach ( $items as $i => $p ) :
				get_template_part( 'template-parts/cards/product-card', null, xsn_card_args( $p, array( 'delay' => ( $i % 4 ) * 0.07 ) ) );
			endforeach; ?>
		</div>
	</div>
</section>
