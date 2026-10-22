<?php
$vendors = array();

if ( function_exists( 'dokan' ) ) {
	$vendors = xsn_remember( 'boutiques', 300, function () {
		$out   = array();
		$users = get_users( array(
			'role'       => 'seller',
			'number'     => 4,
			'meta_key'   => 'dokan_enable_selling',
			'meta_value' => 'yes',
			'orderby'    => 'registered',
			'order'      => 'DESC',
		) );
		foreach ( $users as $u ) {
			$s = get_user_meta( $u->ID, 'dokan_profile_settings', true );
			if ( ! is_array( $s ) ) $s = array();
			$rating = function_exists( 'dokan_get_seller_rating' ) ? dokan_get_seller_rating( $u->ID ) : array();
			$out[]  = array(
				'name'   => ! empty( $s['store_name'] ) ? $s['store_name'] : $u->display_name,
				'banner' => ! empty( $s['banner'] ) ? wp_get_attachment_image_url( (int) $s['banner'], 'large' ) : '',
				'avatar' => ! empty( $s['gravatar'] ) ? wp_get_attachment_image_url( (int) $s['gravatar'], 'thumbnail' ) : '',
				'url'    => function_exists( 'dokan_get_store_url' ) ? dokan_get_store_url( $u->ID ) : get_author_posts_url( $u->ID ),
				'count'  => (int) count_user_posts( $u->ID, 'product', true ),
				'since'  => mysql2date( 'Y', $u->user_registered ),
				'rating' => ( is_array( $rating ) && ! empty( $rating['count'] ) ) ? array( 'avg' => (float) $rating['rating'], 'n' => (int) $rating['count'] ) : array(),
			);
		}
		return $out;
	} );
}

$join_url = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'myaccount' ) : wp_registration_url();
$join_url = add_query_arg( 'action', 'register', $join_url );
?>
<section class="section" id="boutiques">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">Featured boutiques</span>
			<h2>Meet our <span class="text-gold">trusted sellers</span></h2>
			<p>Independent boutiques and artisans, each with their own store, products and reviews.</p>
		</div>

		<?php if ( $vendors ) : ?>
			<div class="grid grid-4">
				<?php foreach ( $vendors as $i => $v ) : ?>
					<a class="boutique reveal" href="<?php echo esc_url( $v['url'] ); ?>" style="--d:<?php echo esc_attr( $i * 0.07 ); ?>s">
						<span class="b-banner"<?php echo $v['banner'] ? ' style="background-image:url(\'' . esc_url( $v['banner'] ) . '\')"' : ''; ?>></span>
						<span class="b-avatar">
							<?php if ( $v['avatar'] ) : ?><img src="<?php echo esc_url( $v['avatar'] ); ?>" alt="" loading="lazy"><?php else : echo esc_html( strtoupper( substr( $v['name'], 0, 1 ) ) ); endif; ?>
						</span>
						<span class="b-body">
							<b><?php echo esc_html( $v['name'] ); ?></b>
							<small><?php echo (int) $v['count']; ?> products &middot; Since <?php echo esc_html( $v['since'] ); ?></small>
							<?php if ( $v['rating'] ) : ?>
								<span class="b-rating">&#9733; <?php echo esc_html( number_format( $v['rating']['avg'], 1 ) ); ?> <small>(<?php echo (int) $v['rating']['n']; ?>)</small></span>
							<?php endif; ?>
							<span class="service-link">Visit boutique <span>&rarr;</span></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="empty-note reveal">Our first boutiques are opening soon.</p>
		<?php endif; ?>

		<div class="sell-cta reveal">
			<div>
				<h3>Open your digital boutique</h3>
				<p>Join XASSIDA SN as a seller and reach customers across Senegal and abroad.</p>
			</div>
			<a class="btn btn-gold" href="<?php echo esc_url( $join_url ); ?>">Become a seller</a>
		</div>
	</div>
</section>
