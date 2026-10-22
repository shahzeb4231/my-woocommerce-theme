<header class="site-header" id="site-header">
	<div class="container header-inner">

		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<span class="logo-mark">X</span>
				<span>XASSIDA <small>SN</small></span>
			<?php endif; ?>
		</a>

		<nav class="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'xassida-sn' ); ?>">
			<?php xsn_menu( 'primary', xsn_primary_fallback() ); ?>
		</nav>

		<div class="header-actions">
			<button class="icon-btn search-toggle" type="button" data-search-open aria-label="Search">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
			</button>
			<a class="icon-btn acc-icon" href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" aria-label="<?php echo is_user_logged_in() ? 'My account' : 'Sign in or create account'; ?>">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
			</a>
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="Cart">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a1 1 0 0 0 1 .8h9.4a1 1 0 0 0 1-.8L21 7H6"/></svg>
					<span class="cart-count"><?php echo (int) xsn_cart_count(); ?></span>
				</a>
				<a class="header-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo is_user_logged_in() ? 'My account' : 'Sign in'; ?></a>
			<?php else : ?>
				<a class="header-link" href="<?php echo esc_url( wp_login_url() ); ?>">Sign in</a>
			<?php endif; ?>
			<a class="btn btn-primary btn-sm" href="<?php echo esc_url( home_url( '/#booking' ) ); ?>">Book Now</a>
			<button class="nav-toggle" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu"><span></span></button>
		</div>

	</div>
</header>

<div class="search-overlay" id="search-overlay" hidden>
	<button class="search-backdrop" type="button" data-search-close aria-label="Close search"></button>
	<form class="search-panel" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="overlay-s">Search</label>
		<input id="overlay-s" type="search" name="s" placeholder="<?php echo class_exists( 'WooCommerce' ) ? 'Search products...' : 'Search...'; ?>" autocomplete="off">
		<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
		<button class="btn btn-gold" type="submit">Search</button>
		<button class="search-close" type="button" data-search-close aria-label="Close">&times;</button>
	</form>
</div>
