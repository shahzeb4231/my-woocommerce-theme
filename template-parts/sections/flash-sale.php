<?php
// Flash sale: only real products that are on sale right now. Hidden when there are none.
if ( ! class_exists( 'WooCommerce' ) ) return;

$data = xsn_remember( 'flash', 300, 'xsn_flash_data' );
if ( ! empty( $data['deadline'] ) && $data['deadline'] <= time() ) { // a sale just ended: refresh
	xsn_bust_cache();
	$data = xsn_flash_data();
}
if ( empty( $data['ids'] ) ) return;

$items = xsn_products_by_ids( $data['ids'] );
if ( empty( $items ) ) return;
$deadline = (int) $data['deadline'];
?>
<section class="section section--dark flash" id="flash-sale">
	<div class="container">

		<div class="flash-head reveal">
			<div>
				<span class="eyebrow">Limited time</span>
				<h2>Lightning <span class="text-gold">deals</span></h2>
				<p>Premium products at special prices while stocks last.</p>
			</div>

			<?php if ( $deadline ) : ?>
				<div class="countdown" data-countdown="<?php echo (int) $deadline; ?>" aria-label="Time left">
					<div class="cd-box"><b data-d>00</b><span>Days</span></div>
					<div class="cd-box"><b data-h>00</b><span>Hours</span></div>
					<div class="cd-box"><b data-m>00</b><span>Min</span></div>
					<div class="cd-box"><b data-s>00</b><span>Sec</span></div>
				</div>
			<?php endif; ?>
		</div>

		<div class="product-grid">
			<?php foreach ( $items as $i => $p ) :
				$meter = array();
				if ( $p->managing_stock() && null !== $p->get_stock_quantity() ) {
					$left  = max( 0, (int) $p->get_stock_quantity() );
					$sold  = (int) $p->get_total_sales();
					$total = $left + $sold;
					$meter = array( 'left' => $left, 'pct' => $total > 0 ? (int) round( $sold / $total * 100 ) : 0 );
				}
				get_template_part( 'template-parts/cards/product-card', null, xsn_card_args( $p, array( 'delay' => $i * 0.07, 'meter' => $meter ) ) );
			endforeach; ?>
		</div>

	</div>
</section>
