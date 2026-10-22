<?php
// Asli contact details yahan likhen
$email = 'hello@xassidasn.com';
$phone = '+221 77 000 00 00';
$wa    = '221770000000';

$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
$msgs   = array(
	'sent'    => array( 'ok',  'Thank you! Your message was sent. We will reply very soon.' ),
	'invalid' => array( 'bad', 'Please fill in your name, a valid email and a message.' ),
	'error'   => array( 'bad', 'Sorry, we could not send your message. Please try again or use WhatsApp.' ),
);
?>
<section class="section" id="contact">
	<div class="container contact-grid">

		<div class="reveal">
			<span class="eyebrow">Contact</span>
			<h2>Let&rsquo;s get you <span class="text-gold">moving</span></h2>
			<p>Reach our team in Dourtoul or send a message. We respond around the clock.</p>

			<div class="contact-card">
				<div class="c-row"><span class="c-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg></span><div><b>Dakar office</b><small>Dakar, Senegal</small></div></div>
				<div class="c-row"><span class="c-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg></span><div><b>Harlem office</b><small>New York, USA</small></div></div>
				<a class="c-row" href="mailto:<?php echo esc_attr( $email ); ?>"><span class="c-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg></span><div><b>Email</b><small><?php echo esc_html( $email ); ?></small></div></a>
				<a class="c-row" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><span class="c-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg></span><div><b>Phone</b><small><?php echo esc_html( $phone ); ?></small></div></a>
			</div>

			<a class="btn btn-gold wa-btn" href="https://wa.me/<?php echo esc_attr( $wa ); ?>" target="_blank" rel="noopener">Chat on WhatsApp</a>
		</div>

		<form class="contact-form reveal" style="--d:.15s" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<h3>Send us a message</h3>

			<?php if ( isset( $msgs[ $status ] ) ) : ?>
				<div class="form-msg <?php echo esc_attr( $msgs[ $status ][0] ); ?>" role="status"><?php echo esc_html( $msgs[ $status ][1] ); ?></div>
			<?php endif; ?>

			<input type="hidden" name="action" value="xsn_contact">
			<?php wp_nonce_field( 'xsn_contact', 'xsn_nonce' ); ?>
			<div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

			<div class="cf-field"><label for="c-name">Your name</label><input id="c-name" name="name" type="text" required></div>
			<div class="cf-field"><label for="c-email">Email address</label><input id="c-email" name="email" type="email" required></div>
			<div class="cf-field"><label for="c-msg">How can we help?</label><textarea id="c-msg" name="message" rows="5" required></textarea></div>
			<button class="btn btn-primary btn-block" type="submit">Send message &rarr;</button>
		</form>

	</div>
</section>
