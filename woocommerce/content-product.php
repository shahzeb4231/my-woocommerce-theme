<?php
defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) return;
?>
<li <?php wc_product_class( 'xsn-li', $product ); ?>>
	<?php get_template_part( 'template-parts/cards/product-card', null, xsn_card_args( $product ) ); ?>
</li>
