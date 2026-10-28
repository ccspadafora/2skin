<?php
/**
 * Hojas de estilo, scripts y sprite de iconos.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Versión de un asset según su fecha de modificación (evita caché vieja en desarrollo).
 *
 * @param string $relative Ruta relativa al tema.
 */
function tsk_asset_version( string $relative ): string {
	$file = TSK_THEME_PATH . '/' . $relative;
	return file_exists( $file ) ? (string) filemtime( $file ) : TSK_THEME_VERSION;
}

/**
 * Encola los assets del frente.
 */
function tsk_enqueue_assets(): void {
	// EB Garamond + Inter, las fuentes originales del manual de marca.
	wp_enqueue_style( 'tsk-fonts', 'https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion

	foreach ( array( 'tokens', 'components', 'site' ) as $sheet ) {
		$path = "assets/css/{$sheet}.css";
		wp_enqueue_style( "tsk-{$sheet}", TSK_THEME_URL . '/' . $path, array( 'tokens' === $sheet ? 'tsk-fonts' : 'tsk-tokens' ), tsk_asset_version( $path ) );
	}

	wp_enqueue_script(
		'tsk-site',
		TSK_THEME_URL . '/assets/js/site.js',
		array(),
		tsk_asset_version( 'assets/js/site.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( is_page( 'diagnostico' ) ) {
		wp_enqueue_script( 'tsk-diagnostico', TSK_THEME_URL . '/assets/js/diagnostico.js', array(), tsk_asset_version( 'assets/js/diagnostico.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) {
		wp_enqueue_script(
			'tsk-reserva',
			TSK_THEME_URL . '/assets/js/reserva.js',
			array(),
			tsk_asset_version( 'assets/js/reserva.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'tsk_enqueue_assets' );

/**
 * Preconecta con Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Tipo.
 */
function tsk_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'tsk_resource_hints', 10, 2 );

/**
 * Marca <html> con .tsk-js antes de pintar, para que los componentes progresivos arranquen en su estado con JS.
 */
function tsk_js_class(): void {
	wp_print_inline_script_tag( 'document.documentElement.classList.add("tsk-js");' );
}
add_action( 'wp_head', 'tsk_js_class', 1 );

/**
 * Imprime el sprite de iconos una sola vez al inicio del body.
 */
function tsk_print_icon_sprite(): void {
	$sprite = TSK_THEME_PATH . '/assets/icons.svg';
	if ( is_readable( $sprite ) ) {
		echo file_get_contents( $sprite ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- SVG estático del tema.
	}
}
add_action( 'wp_body_open', 'tsk_print_icon_sprite', 1 );
