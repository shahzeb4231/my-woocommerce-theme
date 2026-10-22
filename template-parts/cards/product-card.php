<?php
/** Args: id, name, cat, slug, price, thumb, url, badge, cart_url, ajax, delay, meter */
$a = wp_parse_args( $args ?? array(), array(
	'id' => 0, 'name' => '', 'cat' => '', 'slug' => 'shop', 'price' => '', 'thumb' => '',
	'url' => '#', 'badge' => '', 'cart_url' => '', 'ajax' => false, 'delay' => 0, 'meter' => array(),
) );
$label = ( $a['cart_url'] && ! $a['ajax'] ) ? 'View' : 'Add';
?>
<article class="product-card reveal" data-cat="<?php echo esc_attr( $a['slug'] ); ?>" style="--d:<?php echo esc_attr( $a['delay'] ); ?>s">
	<div class="product-thumb">
		<?php if ( $a['badge'] ) : ?><span class="badge"><?php echo esc_html( $a['badge'] ); ?></span><?php endif; ?>
		<button class="wish" type="button" data-id="<?php echo (int) $a['id']; ?>" aria-label="Save to favourites">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.6-9.3-9A5.4 5.4 0 0 1 12 6a5.4 5.4 0 0 1 9.3 6c-2.3 4.4-9.3 9-9.3 9z"/></svg>
		</button>
		<a href="<?php echo esc_url( $a['url'] ); ?>" tabindex="-1" aria-hidden="true">
			<?php if ( $a['thumb'] ) : echo wp_kses_post( $a['thumb'] ); else : ?>
				<span class="thumb-ph"><?php echo esc_html( strtoupper( substr( $a['name'], 0, 1 ) ) ); ?></span>
			<?php endif; ?>
		</a>
	</div>
	<div class="product-body">
		<span class="product-cat"><?php echo esc_html( $a['cat'] ); ?></span>
		<h3><a href="<?php echo esc_url( $a['url'] ); ?>"><?php echo esc_html( $a['name'] ); ?></a></h3>
		<?php if ( ! empty( $a['meter'] ) ) : ?>
			<div class="stock-meter">
				<span class="bar"><i style="width:<?php echo (int) $a['meter']['pct']; ?>%"></i></span>
				<small><?php echo (int) $a['meter']['left']; ?> left in stock</small>
			</div>
		<?php endif; ?>
		<div class="product-foot">
			<span class="price"><?php echo wp_kses_post( $a['price'] ); ?></span>
			<?php if ( $a['cart_url'] ) : ?>
				<a class="add-btn<?php echo $a['ajax'] ? ' add_to_cart_button ajax_add_to_cart' : ''; ?>"
				   href="<?php echo esc_url( $a['cart_url'] ); ?>"
				   data-product_id="<?php echo (int) $a['id']; ?>" data-quantity="1" rel="nofollow"
				   aria-label="<?php echo esc_attr( 'Add ' . $a['name'] . ' to cart' ); ?>">
					<span class="add-ico">+</span><span class="add-txt"><?php echo esc_html( $label ); ?></span>
				</a>
			<?php else : ?>
				<button class="add-btn" type="button" data-demo-add aria-label="Add to cart"><span class="add-ico">+</span><span class="add-txt">Add</span></button>
			<?php endif; ?>
		</div>
	</div>
</article>
