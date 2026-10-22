<?php
/** Args: icon, title, text, featured, delay */
$a = wp_parse_args( $args ?? array(), array( 'icon' => '', 'title' => '', 'text' => '', 'featured' => false, 'delay' => 0 ) );
?>
<a class="service-card reveal<?php echo $a['featured'] ? ' is-featured' : ''; ?>" href="#booking" style="--d:<?php echo esc_attr( $a['delay'] ); ?>s">
	<span class="service-icon">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo $a['icon']; // phpcs:ignore -- trusted static SVG ?></svg>
	</span>
	<h3><?php echo esc_html( $a['title'] ); ?></h3>
	<p><?php echo esc_html( $a['text'] ); ?></p>
	<span class="service-link">Learn more <span>&rarr;</span></span>
</a>