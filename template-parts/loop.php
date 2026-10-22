<?php
if ( is_search() ) {
	$title = sprintf( 'Search: %s', get_search_query() );
} elseif ( is_archive() ) {
	$title = get_the_archive_title();
} else {
	$title = 'News & Updates';
}
?>
<section class="page-hero">
	<div class="container">
		<span class="eyebrow">XASSIDA SN</span>
		<h1><?php echo wp_kses_post( $title ); ?></h1>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid-3">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
						<?php endif; ?>
						<div class="post-body">
							<span class="post-date"><?php echo esc_html( get_the_date() ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							<a class="service-link" href="<?php the_permalink(); ?>">Read more <span>&rarr;</span></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
		<?php else : ?>
			<div class="empty-state">
				<h2>Nothing found</h2>
				<p>Try a different search.</p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
