<?php
/**
 * La reserva: qué se agenda según el carrito, disponibilidad para la página Reserva y médicas.
 *
 * Una reserva es una sola cita: la valoración (presencial o virtual) o, para pacientes en seguimiento,
 * el procedimiento de los tratamientos del carrito (se agenda el servicio de Amelia más largo).
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Booking;

use TwoSkin\Core\Catalog;
use TwoSkin\Core\Content_Types;

defined( 'ABSPATH' ) || exit;

/**
 * Reglas de la reserva.
 */
final class Reservation {

	public const DAYS = 10;

	/**
	 * Engancha la ruta de disponibilidad y marca la «Cita» de Amelia como producto libre.
	 */
	public static function hooks(): void {
		add_action( 'rest_api_init', array( self::class, 'register_routes' ) );
		add_filter( 'tsk_productos_libres', array( self::class, 'free_products' ) );
	}

	/**
	 * El producto oculto «Cita» de Amelia no es un tratamiento.
	 *
	 * @param int[] $ids Productos libres.
	 */
	public static function free_products( array $ids ): array {
		$ids[] = self::cita_product_id();
		return $ids;
	}

	/**
	 * Producto de WooCommerce que Amelia usa para las citas sin variación propia.
	 */
	public static function cita_product_id(): int {
		$settings = json_decode( (string) get_option( 'amelia_settings' ), true );
		return (int) ( $settings['payments']['wc']['productId'] ?? 0 );
	}

	/**
	 * Configuración guardada en la etapa 2: servicios, variaciones y sede.
	 */
	public static function config(): array {
		return wp_parse_args(
			(array) get_option( 'tsk_amelia_services', array() ),
			array(
				'presencial' => 0,
				'virtual'    => 0,
				'variations' => array(),
				'product'    => 0,
				'location'   => 0,
			)
		);
	}

	/**
	 * Modalidad de una variación de valoración ('presencial', 'virtual' o '').
	 *
	 * @param int $variation_id Variación.
	 */
	public static function modalidad( int $variation_id ): string {
		$key = array_search( $variation_id, array_map( 'intval', (array) self::config()['variations'] ), true );
		return false === $key ? '' : (string) $key;
	}

	/**
	 * Resumen del carrito para la reserva.
	 *
	 * @return array{kind: string, valoracion: array, treatments: array, bonos: array, modalidad: string, service_id: int, duration: int, virtual: bool}
	 */
	public static function analyze(): array {
		$out = array(
			'kind'       => 'vacio',
			'valoracion' => array(),
			'treatments' => array(),
			'bonos'      => array(),
			'modalidad'  => '',
			'service_id' => 0,
			'duration'   => 0,
			'virtual'    => false,
		);
		if ( ! WC()->cart ) {
			return $out;
		}
		$config = self::config();

		foreach ( WC()->cart->get_cart() as $key => $item ) {
			$product_id = (int) $item['product_id'];
			if ( $product_id === (int) $config['product'] ) {
				$out['valoracion'][ $key ] = $item;
				$out['modalidad']          = self::modalidad( (int) $item['variation_id'] );
			} elseif ( \TwoSkin\Core\Gifts\Gifts::is_gift_item( $item ) ) {
				$out['bonos'][ $key ] = $item;
			} elseif ( Catalog::is_treatment( $product_id ) && empty( $item['ameliabooking'] ) ) {
				$out['treatments'][ $key ] = $item;
			}
		}

		if ( $out['valoracion'] && $out['treatments'] ) {
			$out['kind'] = 'mixto';
		} elseif ( $out['valoracion'] ) {
			$out['kind'] = 'valoracion';
		} elseif ( $out['treatments'] ) {
			$out['kind'] = 'tratamientos';
		} elseif ( $out['bonos'] ) {
			$out['kind'] = 'bono';
		}

		if ( 'valoracion' === $out['kind'] && $out['modalidad'] ) {
			$out['service_id'] = (int) $config[ $out['modalidad'] ];
			$out['virtual']    = 'virtual' === $out['modalidad'];
		} elseif ( 'tratamientos' === $out['kind'] ) {
			foreach ( $out['treatments'] as $item ) {
				$service = (int) get_post_meta( (int) $item['product_id'], '_tsk_amelia_servicio', true );
				$length  = self::service_duration( $service );
				if ( $service && $length > $out['duration'] ) {
					$out['service_id'] = $service;
					$out['duration']   = $length;
				}
			}
		}
		if ( $out['service_id'] && ! $out['duration'] ) {
			$out['duration'] = self::service_duration( $out['service_id'] );
		}
		return $out;
	}

	/**
	 * Servicios que se pueden consultar: los de la valoración y los de los tratamientos.
	 *
	 * @return int[]
	 */
	public static function bookable_services(): array {
		global $wpdb;
		$config   = self::config();
		$services = array( (int) $config['presencial'], (int) $config['virtual'] );
		$meta     = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s", '_tsk_amelia_servicio' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return array_values( array_filter( array_unique( array_merge( $services, array_map( 'intval', $meta ) ) ) ) );
	}

	/**
	 * Duración de un servicio de Amelia en segundos.
	 *
	 * @param int $service_id Servicio.
	 */
	public static function service_duration( int $service_id ): int {
		global $wpdb;
		if ( ! $service_id ) {
			return 0;
		}
		$table = $wpdb->prefix . 'amelia_services';
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT duration FROM {$table} WHERE id = %d", $service_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Médicas con agenda en Amelia: ID de empleada => nombre.
	 *
	 * @return array<int, string>
	 */
	public static function doctors(): array {
		$doctors = array();
		$posts   = get_posts(
			array(
				'post_type'      => Content_Types::MEDICA,
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);
		foreach ( $posts as $post ) {
			$employee = (int) get_post_meta( $post->ID, '_tsk_amelia_employee_id', true );
			if ( $employee ) {
				$doctors[ $employee ] = get_the_title( $post );
			}
		}
		return $doctors;
	}

	/**
	 * Primeros días con horarios libres de un servicio.
	 *
	 * @param int $service_id Servicio.
	 * @return array<string, array<string, int[]>>
	 */
	public static function availability( int $service_id ): array {
		$slots = Amelia::slots( $service_id, (int) self::config()['location'] );
		return array_slice( $slots, 0, self::DAYS, true );
	}

	/**
	 * Ruta pública GET /tsk/v1/disponibilidad?servicio=ID.
	 */
	public static function register_routes(): void {
		register_rest_route(
			'tsk/v1',
			'/disponibilidad',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'args'                => array(
					'servicio' => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
						'validate_callback' => static fn( $value ): bool => in_array( absint( $value ), self::bookable_services(), true ),
					),
				),
				'callback'            => static function ( \WP_REST_Request $request ): \WP_REST_Response {
					$service = (int) $request['servicio'];
					$days    = array();
					foreach ( self::availability( $service ) as $date => $times ) {
						$day   = \DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
						$hours = array();
						foreach ( $times as $time => $providers ) {
							$hours[] = array(
								'hora'     => $time,
								'etiqueta' => self::time_label( $time ),
								'medicas'  => array_values( $providers ),
							);
						}
						$days[] = array(
							'fecha'    => $date,
							'dia'      => wp_date( 'D', $day->getTimestamp() ),
							'numero'   => wp_date( 'j', $day->getTimestamp() ),
							'mes'      => wp_date( 'M', $day->getTimestamp() ),
							'etiqueta' => ucfirst( wp_date( 'l j \\d\\e F', $day->getTimestamp() ) ),
							'horas'    => $hours,
						);
					}
					$response = new \WP_REST_Response(
						array(
							'duracion' => (int) round( self::service_duration( $service ) / 60 ),
							'dias'     => $days,
						)
					);
					$response->header( 'Cache-Control', 'no-store' );
					return $response;
				},
			)
		);
	}

	/**
	 * Hora legible: 8:30 a. m.
	 *
	 * @param string $time H:i.
	 */
	public static function time_label( string $time ): string {
		[ $h, $m ] = array_map( 'intval', explode( ':', $time ) );
		$suffix    = $h < 12 ? 'a. m.' : ( 12 === $h && 0 === $m ? 'm.' : 'p. m.' );
		return sprintf( '%d:%02d %s', $h > 12 ? $h - 12 : $h, $m, $suffix );
	}
}
