<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'xassida-sn' ); ?></a>

<?php get_template_part( 'template-parts/header/main-nav' ); ?>
<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>

<main id="main">
