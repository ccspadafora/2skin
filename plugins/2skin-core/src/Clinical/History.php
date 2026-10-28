<?php
/**
 * Historia clínica previa de la página Reserva.
 *
 * Las respuestas NO se guardan en WordPress: se entregan a un adaptador que las envía a la plataforma
 * clínica externa. Mientras no haya adaptador registrado, la sección no se muestra ni se piden los datos.
 *
 * Para conectar la plataforma, un módulo registra su adaptador:
 *   add_filter( 'tsk_historia_clinica_adaptador', fn() => new Mi_Adaptador() );
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Clinical;

defined( 'ABSPATH' ) || exit;

/**
 * Preguntas, saneamiento y envío.
 */
final class History {

	/**
	 * Preguntas: clave => [ etiqueta, opciones (vacío = texto libre), ayuda ].
	 */
	public static function questions(): array {
		$si_no = array( 'no' => __( 'No', '2skin-core' ), 'si' => __( 'Sí', '2skin-core' ) );
		return array(
			'nacimiento'    => array( __( 'Fecha de nacimiento', '2skin-core' ), 'date', '' ),
			'eps'           => array( __( 'EPS', '2skin-core' ), array(), '' ),
			'embarazo'      => array( __( '¿Embarazo o lactancia?', '2skin-core' ), array( 'no' => __( 'No', '2skin-core' ), 'embarazo' => __( 'Sí, embarazo', '2skin-core' ), 'lactancia' => __( 'Sí, lactancia', '2skin-core' ) ), '' ),
			'anticoagulantes' => array( __( '¿Tomas anticoagulantes?', '2skin-core' ), $si_no, '' ),
			'autoinmune'    => array( __( '¿Enfermedad autoinmune?', '2skin-core' ), $si_no, '' ),
			'isotretinoina' => array( __( '¿Isotretinoína en los últimos 6 meses?', '2skin-core' ), $si_no, '' ),
			'previos'       => array(
				__( 'Procedimientos estéticos recientes', '2skin-core' ),
				array(
					'ninguno'     => __( 'Ninguno', '2skin-core' ),
					'toxina'      => __( 'Toxina botulínica', '2skin-core' ),
					'hialuronico' => __( 'Ácido hialurónico', '2skin-core' ),
					'bio'         => __( 'Bioestimuladores', '2skin-core' ),
					'laser'       => __( 'Láser', '2skin-core' ),
					'varios'      => __( 'Varios', '2skin-core' ),
				),
				'',
			),
			'alergias'      => array( __( 'Alergias y medicamentos', '2skin-core' ), 'textarea', __( 'Incluye alergias a medicamentos o anestésicos.', '2skin-core' ) ),
		);
	}

	/**
	 * Adaptador activo, o null.
	 */
	public static function adapter(): ?Adapter {
		$adapter = apply_filters( 'tsk_historia_clinica_adaptador', null );
		return $adapter instanceof Adapter && $adapter->is_available() ? $adapter : null;
	}

	/**
	 * ¿Se muestra la sección?
	 */
	public static function enabled(): bool {
		return null !== self::adapter();
	}

	/**
	 * Engancha el envío tras crear el pedido.
	 */
	public static function hooks(): void {
		add_action( 'woocommerce_checkout_order_processed', array( self::class, 'dispatch' ), 20, 1 );
	}

	/**
	 * Lee las respuestas del formulario (sin guardarlas) y las entrega al adaptador.
	 *
	 * @param int $order_id Pedido.
	 */
	public static function dispatch( int $order_id ): void {
		$adapter = self::adapter();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- el pago de WooCommerce ya verificó su nonce.
		$raw = isset( $_POST['tsk_hc'] ) ? (array) wp_unslash( $_POST['tsk_hc'] ) : array();
		if ( ! $adapter || ! $raw ) {
			return;
		}
		$answers = array();
		foreach ( self::questions() as $key => [ , $type ] ) {
			$value = isset( $raw[ $key ] ) && is_string( $raw[ $key ] ) ? $raw[ $key ] : '';
			if ( is_array( $type ) ) {
				$value = array_key_exists( $value, $type ) ? $value : '';
			} elseif ( 'textarea' === $type ) {
				$value = sanitize_textarea_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
			$answers[ $key ] = $value;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}
		$sent = $adapter->send( $order, $answers );
		$order->add_order_note(
			$sent
				? __( 'Historia clínica previa enviada a la plataforma clínica.', '2skin-core' )
				: __( 'No se pudo enviar la historia clínica previa a la plataforma clínica. Pídela a la paciente antes de la cita.', '2skin-core' )
		);
	}
}
