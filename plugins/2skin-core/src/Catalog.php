<?php
/**
 * Catálogo de tratamientos: filtros por URL, preguntas y casos por tratamiento, relacionados
 * y la regla de compra (los tratamientos solo se reservan en línea con una indicación vigente; ver Patients\Follow_Up).
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Lógica del catálogo.
 */
final class Catalog {

	/**
	 * Parámetro de URL => taxonomía. Los valores van separados por comas (?necesidad=lineas,firmeza).
	 */
	public const FILTERS = array(
		'necesidad' => Content_Types::NECESIDAD,
		'tipo'      => 'product_cat',
		'zona'      => Content_Types::ZONA,
	);

	/**
	 * Engancha filtros y redirecciones.
	 */
	public static function hooks(): void {
		add_action( 'pre_get_posts', array( self::class, 'filter_query' ) );
		add_action( 'template_redirect', array( self::class, 'redirect_term_archives' ) );
		add_filter( 'woocommerce_add_to_cart_validation', array( self::class, 'validate_add_to_cart' ), 10, 4 );
	}

	/**
	 * Filtros activos leídos de la URL: parámetro => slugs de términos existentes.
	 *
	 * @return array<string, string[]>
	 */
	public static function active_filters(): array {
		$active = array();
		foreach ( self::FILTERS as $param => $taxonomy ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtro público de solo lectura.
			$raw   = isset( $_GET[ $param ] ) ? sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) : '';
			$slugs = array_filter( array_map( 'sanitize_title', explode( ',', $raw ) ) );
			$slugs = array_values( array_filter( $slugs, static fn( string $slug ): bool => (bool) term_exists( $slug, $taxonomy ) ) );
			if ( $slugs ) {
				$active[ $param ] = array_unique( $slugs );
			}
		}
		return $active;
	}

	/**
	 * URL de Tratamientos con un valor de filtro activado o desactivado. Conserva el orden elegido.
	 *
	 * @param array  $active Filtros activos.
	 * @param string $param  Parámetro.
	 * @param string $slug   Término.
	 */
	public static function toggle_url( array $active, string $param, string $slug ): string {
		$current = $active[ $param ] ?? array();
		$active[ $param ] = in_array( $slug, $current, true ) ? array_diff( $current, array( $slug ) ) : array_merge( $current, array( $slug ) );
		return self::url( $active );
	}

	/**
	 * URL de Tratamientos con unos filtros dados.
	 *
	 * @param array $active Filtros.
	 */
	public static function url( array $active = array() ): string {
		$args = array();
		foreach ( array_keys( self::FILTERS ) as $param ) {
			if ( ! empty( $active[ $param ] ) ) {
				$args[ $param ] = implode( ',', $active[ $param ] );
			}
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- orden público.
		$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
		if ( $orderby ) {
			$args['orderby'] = $orderby;
		}
		return add_query_arg( $args, wc_get_page_permalink( 'shop' ) );
	}

	/**
	 * Aplica los filtros a la consulta principal de la tienda y muestra todo sin paginar.
	 *
	 * @param \WP_Query $query Consulta.
	 */
	public static function filter_query( \WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'product' ) ) {
			return;
		}
		$tax_query = (array) $query->get( 'tax_query' );
		foreach ( self::active_filters() as $param => $slugs ) {
			$tax_query[] = array(
				'taxonomy' => self::FILTERS[ $param ],
				'field'    => 'slug',
				'terms'    => $slugs,
			);
		}
		$query->set( 'tax_query', $tax_query );
		$query->set( 'posts_per_page', -1 );
	}

	/**
	 * Los archivos de categoría y necesidad llevan a Tratamientos ya filtrado.
	 */
	public static function redirect_term_archives(): void {
		$map = array(
			'product_cat'            => 'tipo',
			Content_Types::NECESIDAD => 'necesidad',
		);
		foreach ( $map as $taxonomy => $param ) {
			if ( is_tax( $taxonomy ) ) {
				$term = get_queried_object();
				wp_safe_redirect( self::url( array( $param => array( $term->slug ) ) ), 301 );
				exit;
			}
		}
	}

	/**
	 * Preguntas frecuentes de un tratamiento.
	 *
	 * @param int $product_id Producto.
	 * @return array<int, array{question: string, answer: string}>
	 */
	public static function faq( int $product_id ): array {
		$posts = get_posts(
			array(
				'post_type'      => Content_Types::PREGUNTA,
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'meta_key'       => Content_Types::PREGUNTA_PRODUCTO, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $product_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		return array_map(
			static fn( \WP_Post $post ): array => array(
				'question' => get_the_title( $post ),
				'answer'   => apply_filters( 'the_content', $post->post_content ),
			),
			$posts
		);
	}

	/**
	 * Tratamientos relacionados: comparten una necesidad, o la valoración como punto de partida.
	 *
	 * @param \WC_Product $product Producto actual.
	 * @param int         $limit   Máximo.
	 * @return \WC_Product[]
	 */
	public static function related( \WC_Product $product, int $limit = 3 ): array {
		$needs       = wp_get_post_terms( $product->get_id(), Content_Types::NECESIDAD, array( 'fields' => 'ids' ) );
		$valoracion  = self::valoracion_id();
		$candidates  = wc_get_products(
			array(
				'status'     => 'publish',
				'visibility' => 'catalog',
				'limit'      => -1,
				'orderby'    => 'menu_order',
				'order'      => 'ASC',
				'exclude'    => array( $product->get_id() ),
			)
		);
		$related = array_filter(
			$candidates,
			static function ( \WC_Product $candidate ) use ( $needs, $valoracion ): bool {
				if ( $candidate->get_id() === $valoracion ) {
					return true;
				}
				$shared = array_intersect( $needs, wc_get_product_terms( $candidate->get_id(), Content_Types::NECESIDAD, array( 'fields' => 'ids' ) ) );
				return (bool) $shared;
			}
		);
		return array_slice( array_values( $related ), 0, $limit );
	}

	/**
	 * ID del producto de valoración (etapa 2).
	 */
	public static function valoracion_id(): int {
		$services = (array) get_option( 'tsk_amelia_services', array() );
		return (int) ( $services['product'] ?? 0 );
	}

	/**
	 * ¿Es un tratamiento que exige seguimiento? (todo producto salvo la valoración y los productos libres).
	 *
	 * @param int $product_id Producto padre.
	 */
	public static function is_treatment( int $product_id ): bool {
		$free = array_map( 'intval', (array) apply_filters( 'tsk_productos_libres', array( self::valoracion_id() ) ) );
		return 'product' === get_post_type( $product_id ) && ! in_array( $product_id, $free, true );
	}

	/**
	 * ¿Puede la persona reservar en línea este tratamiento (y opción)? Lo responde el módulo de pacientes.
	 *
	 * @param int $user_id      Usuario (0 = visitante).
	 * @param int $product_id   Tratamiento.
	 * @param int $variation_id Opción (0 = cualquiera).
	 */
	public static function can_book_treatment( int $user_id, int $product_id, int $variation_id = 0 ): bool {
		return (bool) apply_filters( 'tsk_puede_reservar_tratamiento', false, $user_id, $product_id, $variation_id );
	}

	/**
	 * Mensaje cuando el tratamiento no se puede reservar en línea.
	 *
	 * @param string $name Nombre del tratamiento u opción.
	 */
	public static function not_allowed_message( string $name ): string {
		if ( ! is_user_logged_in() ) {
			return sprintf(
				/* translators: 1: tratamiento, 2: URL de ingreso. */
				__( '%1$s se reserva en línea solo si eres paciente en seguimiento. Si ya lo eres, <a href="%2$s">ingresa a tu cuenta</a>; si es tu primera vez, empieza por la valoración.', '2skin-core' ),
				esc_html( $name ),
				esc_url( wc_get_page_permalink( 'myaccount' ) )
			);
		}
		/* translators: %s: tratamiento. */
		return sprintf( __( '%s no está indicado en tu seguimiento o la indicación venció. Agenda una valoración o escríbenos para revisarlo.', '2skin-core' ), esc_html( $name ) );
	}

	/**
	 * Solo la valoración se compra libremente; los tratamientos requieren una indicación vigente.
	 *
	 * @param bool $passed       Validación previa.
	 * @param int  $product_id   Producto padre.
	 * @param int  $quantity     Cantidad.
	 * @param int  $variation_id Opción.
	 */
	public static function validate_add_to_cart( bool $passed, int $product_id, int $quantity, $variation_id = 0 ): bool {
		if ( ! $passed || ! self::is_treatment( $product_id ) ) {
			return $passed;
		}
		if ( self::can_book_treatment( get_current_user_id(), $product_id, (int) $variation_id ) ) {
			return true;
		}
		wc_add_notice( self::not_allowed_message( self::item_label( $product_id, (int) $variation_id ) ), 'error' );
		return false;
	}

	/**
	 * Nombre de un tratamiento con su opción, sin repetir el nombre (ej. «Toxina botulínica · Bruxismo»).
	 *
	 * @param int $product_id   Tratamiento.
	 * @param int $variation_id Opción.
	 */
	public static function item_label( int $product_id, int $variation_id = 0 ): string {
		$name      = get_the_title( $product_id );
		$variation = $variation_id ? wc_get_product( $variation_id ) : null;
		$values    = $variation ? array_values( $variation->get_attributes() ) : array();
		if ( ! $values ) {
			return $name;
		}
		$option = (string) $values[0];
		return str_starts_with( $option, $name ) ? $option : $name . ' · ' . $option;
	}
}
