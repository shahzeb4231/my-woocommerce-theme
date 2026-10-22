<?php
defined( 'ABSPATH' ) || exit;

$user    = wp_get_current_user();
$uid     = (int) $user->ID;
$name    = $user->first_name ? $user->first_name : $user->display_name;
$vendor  = xsn_is_vendor( $uid );
$enabled = $vendor && xsn_vendor_enabled( $uid );
$dash    = $enabled ? xsn_vendor_dashboard_url() : '';
$kyc     = get_user_meta( $uid, 'xsn_kyc', true );
$kyc     = is_array( $kyc ) ? $kyc : array();
$state   = isset( $kyc['status'] ) ? $kyc['status'] : '';
$count   = (int) wc_get_customer_order_count( $uid );
$spent   = (float) wc_get_customer_total_spent( $uid );
$wishes  = function_exists( 'xsn_wishlist_ids' ) ? count( xsn_wishlist_ids( $uid ) ) : 0;
$orders  = wc_get_orders( array( 'customer_id' => $uid, 'limit' => 3, 'orderby' => 'date', 'order' => 'DESC' ) );
$shop    = wc_get_page_permalink( 'shop' );
?>
<div class="acc-dash">

	<section class="acc-hello">
		<div>
			<span class="eyebrow">Dashboard</span>
			<h2>Hello, <?php echo esc_html( $name ); ?></h2>
			<p>Manage your orders, wishlist and account details from one place.</p>
		</div>
		<div class="acc-actions">
			<a class="btn btn-gold btn-sm" href="<?php echo esc_url( $shop ); ?>">Continue shopping</a>
		</div>
	</section>

	<section class="acc-stats">
		<div class="acc-stat"><span>Orders</span><strong><?php echo (int) $count; ?></strong></div>
		<div class="acc-stat"><span>Wishlist</span><strong><?php echo (int) $wishes; ?></strong></div>
		<div class="acc-stat"><span>Total spent</span><strong><?php echo wp_kses_post( wc_price( $spent ) ); ?></strong></div>
	</section>

	<?php if ( $vendor ) : ?>
		<section class="acc-card acc-vendor <?php echo $enabled ? 'is-ok' : 'is-wait'; ?>">
			<?php if ( $enabled ) : ?>
				<div>
					<h3>Your boutique is live</h3>
					<p>Your seller account is approved. Add products, manage orders and track your earnings from the vendor dashboard.</p>
				</div>
				<?php if ( $dash ) : ?><a class="btn btn-primary btn-sm" href="<?php echo esc_url( $dash ); ?>">Open vendor dashboard</a><?php endif; ?>
			<?php elseif ( 'rejected' === $state ) : ?>
				<div>
					<h3>Application needs attention</h3>
					<p>We could not approve your seller application yet.<?php echo ! empty( $kyc['reason'] ) ? ' Reason: ' . esc_html( $kyc['reason'] ) : ''; ?> Please contact our support team to complete your verification.</p>
				</div>
				<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact support</a>
			<?php else : ?>
				<div>
					<h3>Verification in progress</h3>
					<p>Thank you for applying. Our team is reviewing your details and ID. You will receive an email as soon as your boutique is approved, and you can then start selling.</p>
				</div>
				<span class="st st-processing">Pending review</span>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<section class="acc-card">
		<div class="acc-card-head">
			<h3>Recent orders</h3>
			<?php if ( $orders ) : ?><a class="service-link" href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>">View all <span>&rarr;</span></a><?php endif; ?>
		</div>

		<?php if ( $orders ) : ?>
			<ul class="order-list">
				<?php foreach ( $orders as $o ) : ?>
					<li>
						<div>
							<b>#<?php echo esc_html( $o->get_order_number() ); ?></b>
							<small><?php echo esc_html( wc_format_datetime( $o->get_date_created() ) ); ?></small>
						</div>
						<span class="st st-<?php echo esc_attr( $o->get_status() ); ?>"><?php echo esc_html( wc_get_order_status_name( $o->get_status() ) ); ?></span>
						<strong><?php echo wp_kses_post( $o->get_formatted_order_total() ); ?></strong>
						<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( $o->get_view_order_url() ); ?>">View</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<div class="acc-empty">
				<p>You have not placed any orders yet.</p>
				<a class="btn btn-primary btn-sm" href="<?php echo esc_url( $shop ); ?>">Browse the marketplace</a>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( ! $vendor && ! user_can( $user, 'manage_options' ) ) : ?>
		<section class="acc-card acc-sell">
			<div>
				<h3>Want to sell on XASSIDA SN?</h3>
				<p>Seller accounts are created at sign up with an identity check. Register a new account with a different email and choose &ldquo;Vendor&rdquo;.</p>
			</div>
			<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( wp_logout_url( add_query_arg( 'action', 'register', wc_get_page_permalink( 'myaccount' ) ) ) ); ?>">Create a seller account</a>
		</section>
	<?php endif; ?>

	<div class="acc-hook" hidden><?php do_action( 'woocommerce_account_dashboard' ); ?></div>
</div>
