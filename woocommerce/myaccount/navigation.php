<?php
defined( 'ABSPATH' ) || exit;

$user      = wp_get_current_user();
$is_vendor = xsn_is_vendor( $user->ID );
$is_admin  = user_can( $user, 'manage_options' );
$role      = $is_admin ? 'Administrator' : ( $is_vendor ? 'Vendor' : 'Customer' );
$vend_url  = ( $is_vendor && xsn_vendor_enabled( $user->ID ) ) ? xsn_vendor_dashboard_url() : '';

$icons = array(
	'dashboard'       => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
	'orders'          => '<path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.3 7 12 12 20.7 7"/><line x1="12" y1="22" x2="12" y2="12"/>',
	'wishlist'        => '<path d="M12 21s-7-4.6-9.3-9A5.4 5.4 0 0 1 12 6a5.4 5.4 0 0 1 9.3 6c-2.3 4.4-9.3 9-9.3 9z"/>',
	'downloads'       => '<path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/>',
	'edit-address'    => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
	'edit-account'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
	'customer-logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
);
$default_icon = '<circle cx="12" cy="12" r="3"/>';
$svg = function ( $inner ) {
	return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $inner . '</svg>';
};

do_action( 'woocommerce_before_account_navigation' );
?>
<nav class="woocommerce-MyAccount-navigation acc-nav" aria-label="Account pages">
	<div class="acc-user">
		<?php echo get_avatar( $user->ID, 64 ); ?>
		<div>
			<b><?php echo esc_html( $user->display_name ); ?></b>
			<span class="role-pill"><?php echo esc_html( $role ); ?></span>
		</div>
	</div>

	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php if ( 'customer-logout' === $endpoint && $vend_url ) : ?>
				<li class="acc-vendor-link">
					<a href="<?php echo esc_url( $vend_url ); ?>"><?php echo $svg( '<path d="M3 9l1-5h16l1 5"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/>' ); // phpcs:ignore ?><span>Vendor dashboard</span></a>
				</li>
			<?php endif; ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo $svg( isset( $icons[ $endpoint ] ) ? $icons[ $endpoint ] : $default_icon ); // phpcs:ignore ?><span><?php echo esc_html( $label ); ?></span></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php do_action( 'woocommerce_after_account_navigation' ); ?>
