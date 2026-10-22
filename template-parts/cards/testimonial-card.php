<?php
/** Args: name, place, text, delay */
$a = wp_parse_args( $args ?? array(), array( 'name' => '', 'place' => '', 'text' => '', 'delay' => 0 ) );
$initials = '';
foreach ( explode( ' ', $a['name'] ) as $w ) { $initials .= strtoupper( substr( $w, 0, 1 ) ); }
?>
<figure class="t-card reveal" style="--d:<?php echo esc_attr( $a['delay'] ); ?>s">
	<div class="stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
	<blockquote><?php echo esc_html( $a['text'] ); ?></blockquote>
	<figcaption>
		<span class="avatar"><?php echo esc_html( substr( $initials, 0, 2 ) ); ?></span>
		<span><b><?php echo esc_html( $a['name'] ); ?></b><small><?php echo esc_html( $a['place'] ); ?></small></span>
	</figcaption>
</figure>
