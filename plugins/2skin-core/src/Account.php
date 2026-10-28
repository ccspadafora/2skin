<?php
/**
 * Mi piel (Mi cuenta de WooCommerce), primera versión sin datos clínicos.
 *
 * Secciones: Mis citas (desde Amelia), Tratamientos indicados (seguimiento), Pedidos, Bonos y Mis datos.
 * La evolución fotográfica y las indicaciones de cuidado llegarán desde la plataforma clínica externa.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core;

use TwoSkin\Core\Booking\Reservation;
use TwoSkin\Core\Gifts\Repository as Gifts;
use TwoSkin\Core\Patients\Follow_Up;
use TwoSkin\Core\Patients\Repository as Patients;

defined( 'ABSPATH' ) || exit;

/**
 * Endpoints, menú y datos de Mi piel.
 */
final class Account {

	public const TRATAMIENTOS = 'tratamientos-indicados';
	public const BONOS        = 'mis-bonos';

	private const REWRITE_OPTION = 'tsk_account_rewrite';
	private const REWRITE_VER    = '1';

	/**
	 * Engancha todo.
	 */
	public static function hooks(): void {
		add_action( 'init', array( self::class, 'endpoints' ) );
		add_filter( 'woocommerce_get_query_vars', array( self::class, 'query_vars' ) );
		add_filter( 'woocommerce_account_menu_items', array( self::class, 'menu' ) );
		add_filter( 'woocommerce_endpoint_' . self::TRATAMIENTOS . '_title', static fn() => __( 'Tratamientos indicados', '2skin-core' ) );
		add_filter( 'woocommerce_endpoint_' . self::BONOS . '_title', static fn() => __( 'Mis bonos', '2skin-core' ) );
		add_action( 'woocommerce_account_' . self::TRATAMIENTOS . '_endpoint', array( self::class, 'render_indications' ) );
		add_action( 'woocommerce_account_' . self::BONOS . '_endpoint', array( self::class, 'render_gifts' ) );
		// Toda reserva crea cuenta (Checkout::hooks), así que no hay pedidos de invitada que vincular.
		add_filter( 'woocommerce_customer_email_verification_should_show_prompt', '__return_false' );
		// Cada servicio lleva su cita y los bonos su valor: «Pedir de nuevo» no aplica.
		remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
	}

	/**
	 * Pestaña Tratamientos (plantilla del tema: woocommerce/myaccount/tratamientos.php).
	 */
	public static function render_indications(): void {
		wc_get_template( 'myaccount/tratamientos.php', array( 'indicaciones' => self::indications( get_current_user_id() ) ) );
	}

	/**
	 * Pestaña Bonos (plantilla del tema: woocommerce/myaccount/bonos.php).
	 */
	public static function render_gifts(): void {
		wc_get_template( 'myaccount/bonos.php', array( 'bonos' => self::gifts( get_current_user_id() ) ) );
	}

	/**
	 * Qué se compró en un pedido, en una línea (sin la cita técnica de Amelia si hay algo más).
	 *
	 * @param \WC_Order $order Pedido.
	 */
	public static function order_summary( \WC_Order $order ): string {
		$names = array();
		$cita  = Reservation::cita_product_id();
		foreach ( $order->get_items() as $item ) {
			if ( $item instanceof \WC_Order_Item_Product && ( ! $cita || $item->get_product_id() !== $cita ) ) {
				$names[] = $item->get_name();
			}
		}
		if ( ! $names ) {
			foreach ( $order->get_items() as $item ) {
				$names[] = $item->get_name();
			}
		}
		return implode( ' · ', $names );
	}

	/**
	 * Registra los endpoints y regenera las URL una sola vez por versión.
	 */
	public static function endpoints(): void {
		add_rewrite_endpoint( self::TRATAMIENTOS, EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( self::BONOS, EP_ROOT | EP_PAGES );
		if ( get_option( self::REWRITE_OPTION ) !== self::REWRITE_VER ) {
			flush_rewrite_rules( false );
			update_option( self::REWRITE_OPTION, self::REWRITE_VER, false );
		}
	}

	/**
	 * Endpoints conocidos por WooCommerce.
	 *
	 * @param array $vars Variables.
	 */
	public static function query_vars( array $vars ): array {
		$vars[ self::TRATAMIENTOS ] = self::TRATAMIENTOS;
		$vars[ self::BONOS ]        = self::BONOS;
		return $vars;
	}

	/**
	 * Pestañas de Mi piel, en el orden del diseño. Sin descargas ni direcciones (no se piden).
	 *
	 * @param array $items Ítems.
	 */
	public static function menu( array $items ): array {
		return array(
			'dashboard'        => __( 'Mis citas', '2skin-core' ),
			self::TRATAMIENTOS => __( 'Tratamientos', '2skin-core' ),
			'orders'           => __( 'Pedidos', '2skin-core' ),
			self::BONOS        => __( 'Bonos', '2skin-core' ),
			'edit-account'     => __( 'Mis datos', '2skin-core' ),
			'customer-logout'  => $items['customer-logout'] ?? __( 'Salir', '2skin-core' ),
		);
	}

	/**
	 * Citas de la paciente en Amelia (por su correo), de la más próxima a la más antigua.
	 *
	 * @param int $user_id Usuario.
	 * @return array{proximas: array, pasadas: array}
	 */
	public static function appointments( int $user_id ): array {
		global $wpdb;
		$out  = array( 'proximas' => array(), 'pasadas' => array() );
		$user = get_userdata( $user_id );
		if ( ! $user || ! defined( 'AMELIA_PATH' ) ) {
			return $out;
		}
		$t    = $wpdb->prefix . 'amelia_';
		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- prefijo de tabla.
				"SELECT a.id, a.bookingStart, a.status, a.providerId, a.serviceId, a.googleMeetUrl, b.status AS booking_status, s.name AS service
				FROM {$t}customer_bookings b
				INNER JOIN {$t}appointments a ON a.id = b.appointmentId
				INNER JOIN {$t}users c ON c.id = b.customerId
				LEFT JOIN {$t}services s ON s.id = a.serviceId
				WHERE c.type = 'customer' AND ( c.email = %s OR c.externalId = %d )
				ORDER BY a.bookingStart DESC
				LIMIT 50",
				$user->user_email,
				$user_id
			),
			ARRAY_A
		);

		$doctors = Reservation::doctors();
		$config  = Reservation::config();
		$now     = time();
		foreach ( (array) $rows as $row ) {
			$start  = ( new \DateTimeImmutable( (string) $row['bookingStart'], new \DateTimeZone( 'UTC' ) ) )->setTimezone( wp_timezone() );
			$status = self::status_key( (string) $row['status'], (string) $row['booking_status'] );
			$item   = array(
				'id'       => (int) $row['id'],
				'fecha'    => ucfirst( wp_date( 'l j \\d\\e F', $start->getTimestamp() ) ),
				'hora'     => Reservation::time_label( $start->format( 'H:i' ) ),
				'corta'    => wp_date( 'j M Y', $start->getTimestamp() ),
				'servicio' => (string) $row['service'],
				'virtual'  => (int) $row['serviceId'] === (int) $config['virtual'],
				'medica'   => $doctors[ (int) $row['providerId'] ] ?? '',
				'estado'   => $status,
				'meet'     => (string) $row['googleMeetUrl'],
			);
			if ( $start->getTimestamp() >= $now && in_array( $status, array( 'confirmada', 'pendiente' ), true ) ) {
				$item['proxima'] = true;
				array_unshift( $out['proximas'], $item );
			} else {
				$item['proxima']  = false;
				$item['estado']   = 'confirmada' === $status ? 'realizada' : $status;
				$out['pasadas'][] = $item;
			}
		}
		return $out;
	}

	/**
	 * Estado de una cita: confirmada, pendiente, cancelada o no_asistio (las pasadas confirmadas pasan a realizada).
	 *
	 * @param string $appointment Estado de la cita.
	 * @param string $booking     Estado de la reserva de la paciente.
	 */
	private static function status_key( string $appointment, string $booking ): string {
		if ( in_array( $booking, array( 'canceled', 'rejected' ), true ) || in_array( $appointment, array( 'canceled', 'rejected' ), true ) ) {
			return 'cancelada';
		}
		if ( 'no-show' === $booking || 'no-show' === $appointment ) {
			return 'no_asistio';
		}
		return 'pending' === $appointment ? 'pendiente' : 'confirmada';
	}

	/**
	 * Etiquetas de estado de cita.
	 */
	public static function status_labels(): array {
		return array(
			'confirmada' => __( 'Agendada', '2skin-core' ),
			'realizada'  => __( 'Realizada', '2skin-core' ),
			'pendiente'  => __( 'Pendiente de pago', '2skin-core' ),
			'cancelada'  => __( 'Cancelada', '2skin-core' ),
			'no_asistio' => __( 'No asistió', '2skin-core' ),
		);
	}

	/**
	 * Tratamientos indicados vigentes, con producto y opciones legibles.
	 *
	 * @param int $user_id Usuario.
	 * @return array<int, array{product: \WC_Product, opciones: string, vence: string}>
	 */
	public static function indications( int $user_id ): array {
		$out = array();
		foreach ( Patients::for_user( $user_id, true ) as $row ) {
			$product = wc_get_product( $row['product_id'] );
			if ( ! $product || 'publish' !== $product->get_status() ) {
				continue;
			}
			$out[] = array(
				'product'  => $product,
				'opciones' => Follow_Up::options_label( $row ),
				'vence'    => $row['vence'] ? Patients::format_date( $row['vence'] ) : '',
			);
		}
		return $out;
	}

	/**
	 * Bonos comprados por la paciente.
	 *
	 * @param int $user_id Usuario.
	 */
	public static function gifts( int $user_id ): array {
		global $wpdb;
		$table = Gifts::table();
		$rows  = $wpdb->get_col( $wpdb->prepare( "SELECT codigo FROM {$table} WHERE user_id = %d ORDER BY id DESC", $user_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return array_values( array_filter( array_map( array( Gifts::class, 'find' ), (array) $rows ) ) );
	}
}
