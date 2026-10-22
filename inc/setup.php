<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'xassida-sn', XSN_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 60, 'width' => 200, 'flex-width' => true, 'flex-height' => true ) );

	register_nav_menus( array(
		'primary'         => __( 'Primary Menu', 'xassida-sn' ),
		'footer_services' => __( 'Footer: Services', 'xassida-sn' ),
		'footer_company'  => __( 'Footer: Company', 'xassida-sn' ),
		'footer_support'  => __( 'Footer: Support', 'xassida-sn' ),
	) );
} );