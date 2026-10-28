<?php
/**
 * Menús: lee las ubicaciones registradas y, si no tienen menú asignado, usa los enlaces del diseño.
 *
 * El icono de cada ítem se toma de una clase CSS del ítem con forma «i-<icono>» (ej. i-flask).
 *
 * @package 2skin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ítems de una ubicación de menú.
 *
 * @param string $location Ubicación.
 * @return array<int, array{title: string, url: string, icon: string, current: bool}>
 */
function tsk_nav_items( string $location ): array {
	$locations = get_nav_menu_locations();
	$items     = array();

	if ( ! empty( $locations[ $location ] ) ) {
		foreach ( (array) wp_get_nav_menu_items( $locations[ $location ] ) as $item ) {
			if ( (int) $item->menu_item_parent ) {
				continue;
			}
			$icon = '';
			foreach ( (array) $item->classes as $class ) {
				if ( str_starts_with( (string) $class, 'i-' ) ) {
					$icon = substr( $class, 2 );
				}
			}
			$items[] = array(
				'title' => $item->title,
				'url'   => $item->url,
				'icon'  => $icon,
			);
		}
	} else {
		$items = tsk_nav_fallback( $location );
	}

	return array_map(
		static fn( array $item ): array => $item + array( 'current' => tsk_is_current_url( $item['url'] ) ),
		$items
	);
}

/**
 * Enlaces del diseño cuando la ubicación no tiene menú.
 *
 * @param string $location Ubicación.
 */
function tsk_nav_fallback( string $location ): array {
	$item = static fn( string $title, string $key, string $icon = '' ): array => array(
		'title' => $title,
		'url'   => tsk_url( $key ),
		'icon'  => $icon,
	);

	switch ( $location ) {
		case 'primary':
			return array(
				$item( __( 'Tratamientos', '2skin' ), 'tratamientos', 'flask' ),
				$item( __( 'Diagnóstico', '2skin' ), 'diagnostico', 'doc' ),
				$item( __( 'Resultados', '2skin' ), 'resultados', 'eye' ),
				$item( __( 'Nosotros', '2skin' ), 'nosotros', 'pin' ),
				$item( __( 'Bonos', '2skin' ), 'bonos', 'gift' ),
				$item( __( 'Preguntas', '2skin' ), 'preguntas', 'chat' ),
			);

		case 'footer-pacientes':
			return array(
				$item( __( 'Agendar valoración', '2skin' ), 'reserva' ),
				$item( __( 'Diagnóstico de piel', '2skin' ), 'diagnostico' ),
				$item( __( 'Bonos de regalo', '2skin' ), 'bonos' ),
				$item( __( 'Mi reserva', '2skin' ), 'carrito' ),
				$item( __( 'Mi piel', '2skin' ), 'cuenta' ),
				$item( __( 'Preguntas frecuentes', '2skin' ), 'preguntas' ),
			);

		case 'footer-protocolos':
			$items = array();
			if ( function_exists( 'wc_get_products' ) ) {
				$products = wc_get_products(
					array(
						'limit'      => 5,
						'status'     => 'publish',
						'visibility' => 'catalog',
						'orderby'    => 'menu_order',
						'order'      => 'ASC',
					)
				);
				foreach ( $products as $product ) {
					$items[] = array(
						'title' => $product->get_name(),
						'url'   => $product->get_permalink(),
						'icon'  => '',
					);
				}
			}
			$items[] = $item( __( 'Ver todos', '2skin' ), 'tratamientos' );
			return $items;
	}

	return array();
}

/**
 * ¿La URL corresponde a la página actual o a una sección que la contiene?
 *
 * @param string $url URL del ítem.
 */
function tsk_is_current_url( string $url ): bool {
	$item_path = untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	if ( '' === $item_path ) {
		return false;
	}
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$current     = untrailingslashit( (string) wp_parse_url( $request_uri, PHP_URL_PATH ) );

	return $current === $item_path || str_starts_with( $current . '/', $item_path . '/' );
}
