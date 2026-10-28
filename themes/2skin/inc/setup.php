<?php
/**
 * Soportes del tema y ubicaciones de menú.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Configura el tema.
 */
function tsk_setup(): void {
	load_theme_textdomain( '2skin', TSK_THEME_PATH . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/tokens.css', 'assets/css/components.css' ) );

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 640,
			'single_image_width'    => 960,
		)
	);

	register_nav_menus(
		array(
			'primary'           => __( 'Principal', '2skin' ),
			'footer-protocolos' => __( 'Pie · Protocolos', '2skin' ),
			'footer-pacientes'  => __( 'Pie · Pacientes', '2skin' ),
		)
	);

	add_image_size( 'tsk-portrait', 720, 960, true );
	add_image_size( 'tsk-landscape', 1200, 900, true );
	// Versiones pequeñas con la misma proporción: WordPress las ofrece en srcset a pantallas y tarjetas angostas.
	add_image_size( 'tsk-portrait-sm', 360, 480, true );
	add_image_size( 'tsk-landscape-sm', 600, 450, true );
}
add_action( 'after_setup_theme', 'tsk_setup' );
