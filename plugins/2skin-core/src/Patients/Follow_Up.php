<?php
/**
 * Seguimiento: quién puede reservar en línea qué tratamiento.
 *
 * - Se indica a mano desde el perfil de la paciente (médica o recepción).
 * - Se concede sola cuando un pedido con un tratamiento pasa a «Completado» (se hizo el procedimiento):
 *   queda habilitada esa opción para mantenimiento durante la vigencia del tratamiento.
 * - Se vuelve a comprobar en el carrito y en el pago, por si la indicación venció o se revocó.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Patients;

use TwoSkin\Core\Catalog;

defined( 'ABSPATH' ) || exit;

/**
 * Reglas de seguimiento.
 */
final class Follow_Up {

	/**
	 * Meses de vigencia por defecto de una indicación concedida por pedido.
	 */
	public const DEFAULT_MONTHS = 12;

	/**
	 * Engancha reglas y privacidad.
	 */
	public static function hooks(): void {
		add_filter( 'tsk_puede_reservar_tratamiento', array( self::class, 'filter_can_book' ), 10, 4 );
		add_action( 'woocommerce_order_status_completed', array( self::class, 'grant_from_order' ) );
		add_action( 'woocommerce_check_cart_items', array( self::class, 'check_cart' ) );
		add_action( 'deleted_user', array( Repository::class, 'delete_user' ) );
		add_filter( 'wp_privacy_personal_data_exporters', array( self::class, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( self::class, 'register_eraser' ) );
	}

	/**
	 * Indicación vigente que cubre un tratamiento (y opción, si se indica), o null.
	 *
	 * @param int $user_id      Paciente.
	 * @param int $product_id   Tratamiento.
	 * @param int $variation_id Opción (0 = cualquiera).
	 */
	public static function indication( int $user_id, int $product_id, int $variation_id = 0 ): ?array {
		if ( ! $user_id ) {
			return null;
		}
		foreach ( Repository::for_user( $user_id, true ) as $row ) {
			if ( $row['product_id'] !== $product_id ) {
				continue;
			}
			if ( ! $variation_id || ! $row['variation_ids'] || in_array( $variation_id, $row['variation_ids'], true ) ) {
				return $row;
			}
		}
		return null;
	}

	/**
	 * Responde al filtro de Catalog.
	 *
	 * @param bool $allowed      Valor previo.
	 * @param int  $user_id      Paciente.
	 * @param int  $product_id   Tratamiento.
	 * @param int  $variation_id Opción.
	 */
	public static function filter_can_book( bool $allowed, int $user_id, int $product_id, int $variation_id ): bool {
		return $allowed || null !== self::indication( $user_id, $product_id, $variation_id );
	}

	/**
	 * Concede o amplía el seguimiento por cada tratamiento de un pedido completado.
	 *
	 * @param int $order_id Pedido.
	 */
	public static function grant_from_order( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}
		$user_id = (int) $order->get_customer_id();
		$granted = array();

		foreach ( $order->get_items() as $item ) {
			$product_id = (int) $item->get_product_id();
			if ( ! Catalog::is_treatment( $product_id ) ) {
				continue;
			}
			if ( ! $user_id ) {
				$order->add_order_note( __( 'Seguimiento: el pedido no tiene cuenta de paciente; no se habilitó la reserva en línea del tratamiento.', '2skin-core' ) );
				return;
			}
			$months = (int) apply_filters( 'tsk_meses_seguimiento', self::DEFAULT_MONTHS, $product_id );
			$vence  = wp_date( 'Y-m-d', strtotime( "+{$months} months" ) );
			Repository::grant( $user_id, $product_id, array_filter( array( (int) $item->get_variation_id() ) ), $vence, 'pedido', $order_id );
			$granted[] = $item->get_name();
		}

		if ( $granted ) {
			/* translators: %s: tratamientos. */
			$order->add_order_note( sprintf( __( 'Seguimiento habilitado para: %s.', '2skin-core' ), implode( ', ', $granted ) ) );
		}
	}

	/**
	 * Revalida los tratamientos del carrito (carrito, pago clásico y Store API).
	 */
	public static function check_cart(): void {
		if ( ! WC()->cart ) {
			return;
		}
		foreach ( WC()->cart->get_cart() as $item ) {
			$product_id = (int) $item['product_id'];
			if ( ! Catalog::is_treatment( $product_id ) ) {
				continue;
			}
			if ( ! Catalog::can_book_treatment( get_current_user_id(), $product_id, (int) $item['variation_id'] ) ) {
				wc_add_notice( Catalog::not_allowed_message( Catalog::item_label( $product_id, (int) $item['variation_id'] ) ), 'error' );
			}
		}
	}

	/**
	 * Registra el exportador de datos personales.
	 *
	 * @param array $exporters Exportadores.
	 */
	public static function register_exporter( array $exporters ): array {
		$exporters['tsk-seguimiento'] = array(
			'exporter_friendly_name' => __( '2Skin · Seguimiento', '2skin-core' ),
			'callback'               => array( self::class, 'export' ),
		);
		return $exporters;
	}

	/**
	 * Registra el borrador de datos personales.
	 *
	 * @param array $erasers Borradores.
	 */
	public static function register_eraser( array $erasers ): array {
		$erasers['tsk-seguimiento'] = array(
			'eraser_friendly_name' => __( '2Skin · Seguimiento', '2skin-core' ),
			'callback'             => array( self::class, 'erase' ),
		);
		return $erasers;
	}

	/**
	 * Exporta las indicaciones de una persona.
	 *
	 * @param string $email Correo.
	 */
	public static function export( string $email ): array {
		$user  = get_user_by( 'email', $email );
		$items = array();
		foreach ( $user ? Repository::for_user( $user->ID ) : array() as $row ) {
			$items[] = array(
				'group_id'    => 'tsk-seguimiento',
				'group_label' => __( 'Seguimiento de tratamientos', '2skin-core' ),
				'item_id'     => 'tsk-seguimiento-' . $row['id'],
				'data'        => array(
					array( 'name' => __( 'Tratamiento', '2skin-core' ), 'value' => get_the_title( $row['product_id'] ) ),
					array( 'name' => __( 'Opciones', '2skin-core' ), 'value' => self::options_label( $row ) ),
					array( 'name' => __( 'Vence', '2skin-core' ), 'value' => $row['vence'] ?? __( 'Sin vencimiento', '2skin-core' ) ),
					array( 'name' => __( 'Estado', '2skin-core' ), 'value' => $row['estado'] ),
				),
			);
		}
		return array( 'data' => $items, 'done' => true );
	}

	/**
	 * Borra las indicaciones de una persona.
	 *
	 * @param string $email Correo.
	 */
	public static function erase( string $email ): array {
		$user    = get_user_by( 'email', $email );
		$removed = $user ? Repository::delete_user( $user->ID ) : 0;
		return array(
			'items_removed'  => $removed > 0,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	/**
	 * Texto de las opciones cubiertas por una indicación.
	 *
	 * @param array $row Indicación.
	 */
	public static function options_label( array $row ): string {
		if ( ! $row['variation_ids'] ) {
			return __( 'Todas las opciones', '2skin-core' );
		}
		$names = array();
		foreach ( $row['variation_ids'] as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			$values    = $variation ? array_values( $variation->get_attributes() ) : array();
			$names[]   = $values ? (string) $values[0] : '#' . $variation_id;
		}
		return implode( ', ', $names );
	}
}
