<?php
if ( ! function_exists( 'wc_get_page_permalink' ) ) return;

$shop_url = wc_get_page_permalink( 'shop' );
$cats     = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => 0,
	'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
) );
$current  = is_product_category() ? (int) get_queried_object_id() : 0;
$action   = $shop_url;
if ( $current ) {
	$link = get_term_link( $current, 'product_cat' );
	if ( ! is_wp_error( $link ) ) $action = $link;
}
$min     = isset( $_GET['min_price'] ) ? absint( wp_unslash( $_GET['min_price'] ) ) : '';
$max     = isset( $_GET['max_price'] ) ? absint( wp_unslash( $_GET['max_price'] ) ) : '';
$stock   = ! empty( $_GET['instock'] );
$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
?>
<div class="shop-top">
	<?php if ( ! is_wp_error( $cats ) && $cats ) : ?>
		<div class="cat-pills" aria-label="Categories">
			<a class="cat-pill<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( $shop_url ); ?>">All</a>
			<?php foreach ( $cats as $c ) :
				$l = get_term_link( $c );
				if ( is_wp_error( $l ) ) continue; ?>
				<a class="cat-pill<?php echo $current === (int) $c->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( $l ); ?>">
					<?php echo esc_html( $c->name ); ?> <small><?php echo (int) $c->count; ?></small>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="shop-tools">
		<form class="shop-search" role="search" method="get" action="<?php echo esc_url( $shop_url ); ?>">
			<label class="screen-reader-text" for="shop-s">Search products</label>
			<input id="shop-s" type="search" name="s" placeholder="Search products..." value="<?php echo esc_attr( get_search_query() ); ?>">
			<input type="hidden" name="post_type" value="product">
			<button class="btn btn-primary btn-sm" type="submit">Search</button>
		</form>

		<form class="shop-filter" method="get" action="<?php echo esc_url( $action ); ?>">
			<div class="sf-field"><label for="sf-min">Min price</label><input id="sf-min" type="number" name="min_price" min="0" step="1" value="<?php echo esc_attr( $min ); ?>" placeholder="0"></div>
			<div class="sf-field"><label for="sf-max">Max price</label><input id="sf-max" type="number" name="max_price" min="0" step="1" value="<?php echo esc_attr( $max ); ?>" placeholder="Any"></div>
			<label class="sf-check"><input type="checkbox" name="instock" value="1" <?php checked( $stock ); ?>> In stock only</label>
			<?php if ( $orderby ) : ?><input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>"><?php endif; ?>
			<button class="btn btn-ghost btn-sm" type="submit">Apply</button>
			<?php if ( '' !== $min || '' !== $max || $stock ) : ?><a class="sf-clear" href="<?php echo esc_url( $action ); ?>">Reset</a><?php endif; ?>
		</form>
	</div>
</div>
