<?php
if ( ! taxonomy_exists( 'product_cat' ) ) return;

$terms = xsn_remember( 'cats', 600, function () {
	$t = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		'number'     => 8,
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );
	$out = array();
	if ( is_wp_error( $t ) ) return $out;
	foreach ( $t as $term ) {
		$l = get_term_link( $term );
		if ( is_wp_error( $l ) ) continue;
		$out[] = array( 'name' => $term->name, 'count' => (int) $term->count, 'link' => $l, 'img' => (int) get_term_meta( $term->term_id, 'thumbnail_id', true ) );
	}
	return $out;
} );
if ( empty( $terms ) ) return;
?>
<section class="section" id="categories">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">Shop by category</span>
			<h2>Find exactly <span class="text-gold">what you need</span></h2>
			<p>Browse our curated departments, from fashion and beauty to electronics and everyday essentials.</p>
		</div>

		<div class="cat-grid">
			<?php foreach ( $terms as $i => $t ) : ?>
				<a class="cat-card reveal" href="<?php echo esc_url( $t['link'] ); ?>" style="--d:<?php echo esc_attr( ( $i % 4 ) * 0.07 ); ?>s">
					<?php if ( $t['img'] ) : echo wp_get_attachment_image( $t['img'], 'large', false, array( 'loading' => 'lazy' ) ); else : ?>
						<span class="cat-ph"><?php echo esc_html( strtoupper( substr( $t['name'], 0, 1 ) ) ); ?></span>
					<?php endif; ?>
					<span class="cat-label">
						<b><?php echo esc_html( $t['name'] ); ?></b>
						<small><?php echo (int) $t['count']; ?> products</small>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
