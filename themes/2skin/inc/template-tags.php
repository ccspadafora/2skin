<?php
/**
 * Átomos del design system (.tsk-*) y utilidades de plantilla.
 *
 * Los componentes con más marcado viven en template-parts/components/.
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icono del sprite (assets/icons.svg).
 *
 * @param string $name  Nombre sin el prefijo «i-».
 * @param string $class Clase CSS (tsk-icon, tsk-icon--lg, tsk-icon--sm).
 */
function tsk_icon( string $name, string $class = 'tsk-icon' ): string {
	return sprintf( '<svg class="%s" aria-hidden="true" focusable="false"><use href="#i-%s"></use></svg>', esc_attr( $class ), esc_attr( $name ) );
}

/**
 * Botón o enlace con estilo de botón.
 *
 * @param string $label Texto.
 * @param string $url   Destino.
 * @param array  $args  variant (primary|secondary|sage|text), size (sm|lg), arrow (bool), class, attrs.
 */
function tsk_button( string $label, string $url, array $args = array() ): string {
	$args    = wp_parse_args(
		$args,
		array(
			'variant' => 'primary',
			'size'    => '',
			'arrow'   => null,
			'class'   => '',
			'attrs'   => array(),
		)
	);
	$arrow   = $args['arrow'] ?? ( 'text' === $args['variant'] );
	$classes = array_filter( array( 'tsk-btn', 'tsk-btn--' . $args['variant'], $args['size'] ? 'tsk-btn--' . $args['size'] : '', $args['class'] ) );
	$attrs   = '';
	foreach ( $args['attrs'] as $name => $value ) {
		$attrs .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
	}

	return sprintf(
		'<a href="%1$s" class="%2$s"%3$s><span class="tsk-btn__label">%4$s</span>%5$s</a>',
		esc_url( $url ),
		esc_attr( implode( ' ', $classes ) ),
		$attrs,
		esc_html( $label ),
		$arrow ? '<span class="tsk-btn__arrow" aria-hidden="true">→</span>' : ''
	);
}

/**
 * URL de un asset del tema.
 *
 * @param string $path Ruta relativa a assets/.
 */
function tsk_asset( string $path ): string {
	return TSK_THEME_URL . '/assets/' . ltrim( $path, '/' );
}

/**
 * Marco de imagen con proporción de marca.
 *
 * @param int|string $image ID de adjunto o ruta en assets/img.
 * @param array      $args  ratio (square|portrait|landscape|wide|''), alt, size, class, placeholder, loading, sizes (atributo sizes del srcset).
 */
function tsk_media( int|string $image, array $args = array() ): string {
	$args    = wp_parse_args(
		$args,
		array(
			'ratio'       => 'landscape',
			'alt'         => '',
			'size'        => 'large',
			'class'       => '',
			'placeholder' => __( 'Fotografía 2skin', '2skin' ),
			'loading'     => 'lazy',
			'sizes'       => '',
		)
	);
	// La imagen que se ve al cargar (la de portada) se pide con prioridad.
	$priority = 'eager' === $args['loading'] ? 'high' : 'auto';
	$classes = array_filter( array( 'tsk-media', $args['ratio'] ? 'tsk-media--' . $args['ratio'] : '', $args['class'] ) );

	if ( is_int( $image ) && $image > 0 ) {
		$img = wp_get_attachment_image(
			$image,
			$args['size'],
			false,
			array_filter(
				array(
					'alt'           => $args['alt'] ? $args['alt'] : (string) get_post_meta( $image, '_wp_attachment_image_alt', true ),
					'loading'       => $args['loading'],
					'fetchpriority' => $priority,
					'sizes'         => $args['sizes'],
				)
			)
		);
	} elseif ( is_string( $image ) && '' !== $image ) {
		$img = sprintf( '<img src="%s" alt="%s" loading="%s" fetchpriority="%s" decoding="async">', esc_url( tsk_asset( 'img/' . $image ) ), esc_attr( $args['alt'] ), esc_attr( $args['loading'] ), esc_attr( $priority ) );
	} else {
		$img = '';
	}

	if ( ! $img ) {
		$img = '<div class="tsk-media__placeholder"><span>' . esc_html( $args['placeholder'] ) . '</span></div>';
	}

	return sprintf( '<figure class="%s">%s</figure>', esc_attr( implode( ' ', $classes ) ), $img );
}

/**
 * Pesos colombianos sin decimales: $90.000.
 *
 * @param float|int $amount Valor.
 */
function tsk_cop( float|int $amount ): string {
	return '$' . number_format( (float) $amount, 0, ',', '.' );
}

/**
 * Precio de la valoración. En la etapa de catálogo lo sobreescribe el producto de WooCommerce vía el filtro.
 *
 * @param string $modalidad presencial|virtual.
 */
function tsk_valoracion_precio( string $modalidad = 'presencial' ): int {
	$defaults = array(
		'presencial' => 90000,
		'virtual'    => 50000,
	);
	return (int) apply_filters( 'tsk_valoracion_precio', $defaults[ $modalidad ] ?? 0, $modalidad );
}

/**
 * URL de una sección del sitio por clave. Centraliza los destinos de los CTA del diseño.
 *
 * @param string $key inicio|tratamientos|diagnostico|resultados|nosotros|bonos|preguntas|carrito|reserva|cuenta.
 */
function tsk_url( string $key ): string {
	$wc = function_exists( 'wc_get_page_permalink' );

	$url = match ( $key ) {
		'inicio'       => home_url( '/' ),
		'tratamientos' => $wc ? wc_get_page_permalink( 'shop' ) : home_url( '/tratamientos/' ),
		'carrito'      => $wc ? wc_get_cart_url() : home_url( '/carrito/' ),
		'reserva'      => $wc ? wc_get_checkout_url() : home_url( '/reserva/' ),
		'cuenta'       => $wc ? wc_get_page_permalink( 'myaccount' ) : home_url( '/mi-piel/' ),
		default        => tsk_page_url( $key ),
	};

	return (string) apply_filters( 'tsk_url', $url, $key );
}

/**
 * URL de una página por su slug, o /slug/ si aún no existe.
 *
 * @param string $slug Slug.
 */
function tsk_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	return $page ? (string) get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Migas: Inicio · Página actual.
 *
 * @param array  $trail   Pares [ etiqueta, url ] intermedios.
 * @param string $current Etiqueta de la página actual (por defecto, su título).
 */
function tsk_breadcrumbs( array $trail = array(), string $current = '' ): void {
	$parts = array( sprintf( '<a href="%s">%s</a>', esc_url( tsk_url( 'inicio' ) ), esc_html__( 'Inicio', '2skin' ) ) );
	foreach ( $trail as [ $label, $url ] ) {
		$parts[] = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $label ) );
	}
	$parts[] = '<span aria-current="page">' . esc_html( $current ? $current : wp_strip_all_tags( get_the_title() ) ) . '</span>';

	echo '<nav class="tsk-breadcrumbs caption" aria-label="' . esc_attr__( 'Migas de pan', '2skin' ) . '">' . implode( ' · ', $parts ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- partes escapadas arriba.
}
