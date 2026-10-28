<?php
/**
 * Puente con Amelia: consulta horarios y agrega la cita al carrito con sus mismas validaciones.
 *
 * Se llaman, dentro de la misma petición, los controladores de las rutas públicas que usa el formulario
 * oficial de Amelia (/slots y /payment/wc). Así Amelia valida la franja, crea la cita con el pedido y la
 * sincroniza con sus estados, sin depender de su API con llaves (solo licencia Developer).
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Booking;

use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

defined( 'ABSPATH' ) || exit;

/**
 * Llamadas a Amelia.
 */
final class Amelia {

	/**
	 * Contenedor de Amelia (se construye una vez por petición).
	 *
	 * @var mixed
	 */
	private static $container = null;

	/**
	 * ¿Está Amelia disponible?
	 */
	public static function available(): bool {
		return defined( 'AMELIA_PATH' ) && class_exists( ServerRequestFactory::class ) && class_exists( '\\AmeliaBooking\\Application\\Controller\\PaymentGateway\\WooCommercePaymentController' );
	}

	/**
	 * Invoca un controlador de Amelia.
	 *
	 * @param string $controller Clase relativa a AmeliaBooking\Application\Controller.
	 * @param string $method     GET o POST.
	 * @param array  $query      Parámetros de consulta.
	 * @param array  $body       Cuerpo.
	 * @return array{status: int, data: mixed, message: string}
	 */
	private static function call( string $controller, string $method, array $query = array(), array $body = array() ): array {
		if ( ! self::available() ) {
			return array( 'status' => 503, 'data' => null, 'message' => 'Amelia no está activa.' );
		}
		self::$container = self::$container ?? require AMELIA_PATH . '/src/Infrastructure/ContainerConfig/container.php';

		$request = ( new ServerRequestFactory() )->createServerRequest( $method, admin_url( 'admin-ajax.php' ) )
			->withQueryParams( $query )
			->withParsedBody( $body );

		$class = '\\AmeliaBooking\\Application\\Controller\\' . $controller;
		try {
			$response = ( new $class( self::$container ) )( $request, new Response(), array(), true );
		} catch ( \Throwable $e ) {
			return array( 'status' => 500, 'data' => null, 'message' => $e->getMessage() );
		}
		$response->getBody()->rewind();
		$json = json_decode( (string) $response->getBody(), true );

		return array(
			'status'  => $response->getStatusCode(),
			'data'    => $json['data'] ?? null,
			'message' => (string) ( $json['message'] ?? '' ),
		);
	}

	/**
	 * Franjas libres de un servicio: fecha Y-m-d => hora H:i => IDs de médicas disponibles.
	 *
	 * @param int $service_id  Servicio de Amelia.
	 * @param int $location_id Sede.
	 * @return array<string, array<string, int[]>>
	 */
	public static function slots( int $service_id, int $location_id ): array {
		$result = self::call(
			'Booking\\Appointment\\GetTimeSlotsController',
			'GET',
			array(
				'serviceId'  => $service_id,
				'locationId' => $location_id,
				'persons'    => 1,
				'page'       => 'booking',
				'monthsLoad' => 2,
			)
		);

		$out = array();
		foreach ( (array) ( $result['data']['slots'] ?? array() ) as $date => $times ) {
			foreach ( (array) $times as $time => $options ) {
				$providers = array();
				foreach ( (array) $options as $option ) {
					if ( (int) ( $option[1] ?? 0 ) === $location_id || ! $location_id ) {
						$providers[] = (int) $option[0];
					}
				}
				if ( $providers ) {
					$out[ $date ][ substr( (string) $time, 0, 5 ) ] = array_values( array_unique( $providers ) );
				}
			}
		}
		ksort( $out );
		return $out;
	}

	/**
	 * Agrega la cita al carrito de WooCommerce (Amelia valida la franja y guarda los datos en el ítem).
	 *
	 * @param array $booking service_id, location_id, provider_id, start (Y-m-d H:i), duration (s), customer[first_name,last_name,email,phone], product_id.
	 * @return string Mensaje de error ('' si salió bien).
	 */
	public static function add_to_cart( array $booking ): string {
		$result = self::call(
			'PaymentGateway\\WooCommercePaymentController',
			'POST',
			array(),
			array(
				'type'               => 'appointment',
				'bookings'           => array(
					array(
						'customer'     => array(
							'id'              => null,
							'email'           => $booking['customer']['email'],
							'firstName'       => $booking['customer']['first_name'],
							'lastName'        => $booking['customer']['last_name'],
							'phone'           => self::phone_for( (string) $booking['customer']['email'], (string) $booking['customer']['phone'] ),
							'countryPhoneIso' => 'co',
							'externalId'      => null,
						),
						'customFields' => array(),
						'customerId'   => 0,
						'extras'       => array(),
						'persons'      => 1,
						'duration'     => (int) $booking['duration'],
					),
				),
				'bookingStart'       => $booking['start'],
				'notifyParticipants' => 1,
				'locationId'         => (int) $booking['location_id'],
				'providerId'         => (int) $booking['provider_id'],
				'serviceId'          => (int) $booking['service_id'],
				'utcOffset'          => null,
				'recurring'          => array(),
				'package'            => array(),
				'couponCode'         => '',
				'payment'            => array(
					'gateway'  => 'wc',
					'currency' => get_woocommerce_currency(),
					'data'     => array(),
				),
				'isCart'             => false,
				'componentProps'     => array( 'appointment' => array( 'bookings' => array( array( 'customFields' => array() ) ) ) ),
				'returnUrl'          => wc_get_checkout_url(),
				'timeZone'           => wp_timezone_string(),
			)
		);

		if ( $result['status'] < 300 && empty( $result['data']['error'] ) ) {
			return '';
		}
		return $result['message'] ? $result['message'] : __( 'No pudimos reservar ese horario. Elige otro, por favor.', '2skin-core' );
	}

	/**
	 * Teléfono que se envía a Amelia.
	 *
	 * Amelia identifica a la clienta por correo o por teléfono: si el teléfono ya es de otra clienta (una mamá
	 * que agenda para su hija), la cita quedaría a nombre de esa otra persona, con sus notificaciones y en su
	 * Mi piel. En ese caso no se envía el teléfono y Amelia crea la clienta por su correo; el teléfono queda
	 * en el pedido de WooCommerce.
	 *
	 * @param string $email Correo de la paciente.
	 * @param string $phone Teléfono normalizado.
	 */
	private static function phone_for( string $email, string $phone ): string {
		global $wpdb;
		if ( '' === $phone ) {
			return '';
		}
		$table = $wpdb->prefix . 'amelia_users';
		$owner = $wpdb->get_var( $wpdb->prepare( "SELECT email FROM {$table} WHERE phone = %s ORDER BY id LIMIT 1", $phone ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$same  = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE LOWER(email) = LOWER(%s) LIMIT 1", $email ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return null === $owner || $same || 0 === strcasecmp( (string) $owner, $email ) ? $phone : '';
	}
}
