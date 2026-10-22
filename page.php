<?php
get_header();

$has_wc     = function_exists( 'is_woocommerce' );
$is_account = $has_wc && is_account_page();
$is_cart    = $has_wc && is_cart();
$is_checkout = $has_wc && is_checkout();
$wide       = $is_account || $is_cart || $is_checkout;

// Checkout progress: 1 Cart > 2 Checkout > 3 Complete
$step = 0;
if ( $is_cart ) {
	$step = 1;
} elseif ( $is_checkout ) {
	$step = is_wc_endpoint_url( 'order-received' ) ? 3 : 2;
}
?>

<?php if ( $step ) : ?>
	<section class="page-hero page-hero--slim">
		<div class="container">
			<ol class="flow-steps" aria-label="Checkout progress">
				<?php foreach ( array( 1 => 'Cart', 2 => 'Checkout', 3 => 'Complete' ) as $n => $label ) : ?>
					<li class="<?php echo $n < $step ? 'is-done' : ( $n === $step ? 'is-current' : '' ); ?>"><span><?php echo (int) $n; ?></span><?php echo esc_html( $label ); ?></li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
<?php elseif ( $is_account && is_user_logged_in() ) : ?>
	<section class="page-hero page-hero--slim">
		<div class="container"><span class="eyebrow">XASSIDA SN</span><h1>My account</h1></div>
	</section>
<?php elseif ( ! $is_account ) : ?>
	<section class="page-hero">
		<div class="container">
			<span class="eyebrow">XASSIDA SN</span>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>
<?php endif; ?>

<section class="section page-body">
	<div class="container<?php echo $wide ? '' : ' container--narrow'; ?>">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'entry' ); ?>>
				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php get_footer(); ?>
