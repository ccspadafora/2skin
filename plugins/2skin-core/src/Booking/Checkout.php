<?php
/**
 * Página Reserva (pago clásico de WooCommerce): campos, reglas, agendamiento en Amelia y datos del pedido.
 *
 * Flujo: el carrito lleva la valoración o los tratamientos sin cita. Al pagar, después de validar,
 * se pide a Amelia la franja elegida; Amelia agrega su ítem con la cita (la variación de la valoración
 * o el ítem «Cita» en $0 para tratamientos) y la crea con el pedido, sincronizada con su estado.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Booking;

use TwoSkin\Core\Catalog;

defined( 'ABSPATH' ) || exit;

/**
 * Lógica de la Reserva.
 */
final class Checkout {

	private const ACTION_NONCE = 'tsk_reserva';

	/**
	 * Engancha todo.
	 */
	public static function hooks(): void {
		add_filter( 'woocommerce_checkout_fields', array( self::class, 'fields' ), 20 );
		add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
		add_filter( 'woocommerce_checkout_update_order_review_expired', '__return_false' );
		add_filter( 'woocommerce_checkout_registration_required', array( self::class, 'registration_required' ) );
		add_action( 'template_redirect', array( self::class, 'handle_actions' ), 5 );
		add_action( 'woocommerce_after_checkout_validation', array( self::class, 'validate' ), 10, 2 );
		add_action( 'woocommerce_checkout_update_customer', array( self::class, 'book' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order', array( self::class, 'save_order_meta' ), 10, 2 );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( self::class, 'admin_order_data' ) );
		add_filter( 'woocommerce_process_checkout_field_billing_phone', array( self::class, 'clean_phone' ) );
		add_filter( 'woocommerce_order_button_text', array( self::class, 'button_text' ) );
	}

	/**
	 * Texto del botón de pago: con cita o solo bonos.
	 */
	public static function button_text(): string {
		return 'bono' === Reservation::analyze()['kind'] ? __( 'Pagar bono de regalo', '2skin-core' ) : __( 'Pagar 100% y confirmar cita', '2skin-core' );
	}

	/**
	 * Política de cancelación.
	 */
	public static function policy(): string {
		return (string) apply_filters(
			'tsk_politica_cancelacion',
			__( 'Cancela o reprograma con al menos 24 horas de anticipación: tu pago se mantiene abonado y puedes reprogramar hasta dos veces. Si cancelas después o no asistes, se pierde el valor pagado y puedes reagendar una vez más, solo de forma virtual. No hay devoluciones de dinero.', '2skin-core' )
		);
	}

	/**
	 * Dirección de la sede (el tema la toma del Personalizador).
	 */
	public static function sede(): string {
		return (string) apply_filters( 'tsk_direccion_sede', 'Medicenter · Calle 120a #7-62, Bogotá' );
	}

	/**
	 * Consentimientos que aplican: clave => texto.
	 *
	 * @param bool $seguimiento Paciente en seguimiento (pide el consentimiento informado del procedimiento).
	 * @param bool $cita        La reserva agenda una cita (pide la política de cancelación).
	 * @param bool $bonos       La reserva incluye bonos de regalo (pide sus condiciones).
	 * @return array<string, string>
	 */
	public static function consents( bool $seguimiento, bool $cita = true, bool $bonos = false ): array {
		$consents = array(
			'datos'       => sprintf(
				/* translators: %s: URL de la política de datos. */
				__( 'Autorizo el tratamiento de mis datos personales, incluidos datos sensibles de salud, conforme a la <a href="%s" target="_blank" rel="noopener">política de tratamiento de datos</a> de 2Skin (Ley 1581 de 2012).', '2skin-core' ),
				esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/politica-de-datos/' ) )
			),
			'cancelacion' => __( 'Leí y acepto la política de cancelación y reprogramación. Entiendo que no hay devoluciones de dinero.', '2skin-core' ),
		);
		if ( ! $cita ) {
			unset( $consents['cancelacion'] );
		}
		if ( $seguimiento && $cita ) {
			$consents['informado'] = __( 'Recibí y acepto el consentimiento informado del procedimiento que voy a realizarme.', '2skin-core' );
		}
		if ( $bonos ) {
			$consents['bono'] = __( 'Acepto las condiciones del bono: vigencia de 60 días desde la compra, transferible solo antes de la valoración, no se redime en efectivo y no tiene devolución.', '2skin-core' );
		}
		return $consents;
	}

	/**
	 * Tratamientos para «Procedimiento de interés»: slug => nombre.
	 *
	 * @return array<string, string>
	 */
	public static function interest_options(): array {
		$options = array( '' => __( 'Aún no lo sé', '2skin-core' ) );
		foreach ( wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $product ) {
			if ( Catalog::is_treatment( $product->get_id() ) ) {
				$options[ $product->get_slug() ] = $product->get_name();
			}
		}
		return $options;
	}

	/**
	 * Campos de facturación: solo lo necesario para la cita y el comprobante.
	 *
	 * @param array $fields Campos.
	 */
	public static function fields( array $fields ): array {
		$billing = $fields['billing'] ?? array();
		$keep    = array(
			'billing_first_name' => array( __( 'Nombres', '2skin-core' ), '', 10 ),
			'billing_last_name'  => array( __( 'Apellidos', '2skin-core' ), '', 20 ),
			'billing_phone'      => array( __( 'Celular · WhatsApp', '2skin-core' ), '+57', 40 ),
			'billing_email'      => array( __( 'Correo electrónico', '2skin-core' ), 'nombre@correo.com', 50 ),
		);
		$out = array();
		foreach ( $keep as $key => [ $label, $placeholder, $priority ] ) {
			$out[ $key ] = array_merge(
				$billing[ $key ] ?? array(),
				array(
					'label'       => $label,
					'placeholder' => $placeholder,
					'priority'    => $priority,
					'required'    => true,
					'class'       => array( 'form-row-wide' ),
				)
			);
		}
		$out['billing_tsk_documento'] = array(
			'label'        => __( 'Cédula', '2skin-core' ),
			'placeholder'  => __( 'CC / CE / Pasaporte', '2skin-core' ),
			'required'     => true,
			'priority'     => 30,
			'class'        => array( 'form-row-wide' ),
			'autocomplete' => 'off',
		);
		$out['billing_phone']['description'] = __( 'Te enviamos recordatorios 24 horas y 2 horas antes de tu cita.', '2skin-core' );
		$out['billing_phone']['type']        = 'tel';

		$fields['billing']  = $out;
		$fields['shipping'] = array();
		$fields['order']    = array();
		return $fields;
	}

	/**
	 * Toda reserva crea la cuenta de la paciente (Mi piel, seguimiento). Usuario y clave se generan solos.
	 */
	public static function registration_required(): bool {
		return true;
	}

	/**
	 * Acciones por URL en la Reserva: ?modalidad=presencial|virtual y ?tsk_solo_valoracion=1 (con nonce).
	 */
	public static function handle_actions(): void {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_wc_endpoint_url() || ! WC()->cart ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- elegir modalidad no cambia datos de nadie más; la otra acción verifica nonce.
		$interes   = isset( $_GET['interes'] ) ? sanitize_title( wp_unslash( $_GET['interes'] ) ) : '';
		$modalidad = isset( $_GET['modalidad'] ) ? sanitize_key( wp_unslash( $_GET['modalidad'] ) ) : '';
		$solo      = isset( $_GET['tsk_solo_valoracion'] );
		// phpcs:enable

		if ( $modalidad && isset( Reservation::config()['variations'][ $modalidad ] ) ) {
			self::set_valoracion( $modalidad );
		} elseif ( $solo && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), self::ACTION_NONCE ) ) {
			foreach ( Reservation::analyze()['treatments'] as $key => $item ) {
				WC()->cart->remove_cart_item( $key );
			}
			if ( ! Reservation::analyze()['valoracion'] ) {
				self::set_valoracion( 'presencial' );
			}
		} else {
			return;
		}
		wp_safe_redirect( $interes ? add_query_arg( 'interes', $interes, wc_get_checkout_url() ) : wc_get_checkout_url() );
		exit;
	}

	/**
	 * URL de «Dejar solo la valoración».
	 */
	public static function keep_valoracion_url(): string {
		return wp_nonce_url( add_query_arg( 'tsk_solo_valoracion', '1', wc_get_checkout_url() ), self::ACTION_NONCE );
	}

	/**
	 * Deja en el carrito una sola valoración de la modalidad pedida.
	 *
	 * @param string $modalidad presencial | virtual.
	 */
	public static function set_valoracion( string $modalidad ): void {
		$config = Reservation::config();
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( (int) $item['product_id'] === (int) $config['product'] ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
		WC()->cart->add_to_cart( (int) $config['product'], 1, (int) $config['variations'][ $modalidad ] );
	}

	/**
	 * Celular en formato internacional (+57 por defecto), o '' si no es válido.
	 *
	 * @param string $phone Texto.
	 */
	public static function normalize_phone( string $phone ): string {
		$plus   = str_starts_with( trim( $phone ), '+' );
		$digits = preg_replace( '/\D+/', '', $phone );
		if ( ! $plus && 10 === strlen( $digits ) ) {
			$digits = '57' . $digits;
		}
		return strlen( $digits ) >= 11 && strlen( $digits ) <= 15 ? '+' . $digits : '';
	}

	/**
	 * Guarda el celular en formato internacional cuando es válido (la validación avisa si no).
	 *
	 * @param string $phone Celular enviado.
	 */
	public static function clean_phone( $phone ): string {
		$normalized = self::normalize_phone( (string) $phone );
		return $normalized ? $normalized : (string) $phone;
	}

	/**
	 * Datos de la reserva enviados con el formulario (sin validar).
	 */
	private static function posted(): array {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verificó el nonce del pago.
		$get = static fn( string $key ): string => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		$out = array(
			'tipo'      => $get( 'tsk_tipo' ),
			'interes'   => sanitize_title( $get( 'tsk_interes' ) ),
			'fecha'     => $get( 'tsk_fecha' ),
			'hora'      => $get( 'tsk_hora' ),
			'medica'    => absint( $get( 'tsk_medica' ) ),
			'factura'   => '1' === $get( 'tsk_factura' ),
			'consents'  => isset( $_POST['tsk_consent'] ) ? array_map( 'sanitize_key', array_keys( (array) wp_unslash( $_POST['tsk_consent'] ) ) ) : array(),
		);
		// phpcs:enable
		if ( 'seguimiento' !== $out['tipo'] ) {
			$out['medica'] = 0; // A la paciente nueva se le asigna médica según disponibilidad.
		}
		return $out;
	}

	/**
	 * Validación del pago (después de las reglas de WooCommerce).
	 *
	 * @param array     $data   Datos del pago.
	 * @param \WP_Error $errors Errores.
	 */
	public static function validate( array $data, \WP_Error $errors ): void {
		$cart   = Reservation::analyze();
		$posted = self::posted();

		if ( 'vacio' === $cart['kind'] ) {
			$errors->add( 'tsk_vacio', __( 'Elige tu valoración para poder agendar.', '2skin-core' ) );
			return;
		}
		$cita = 'bono' !== $cart['kind'];
		if ( $cita && ! in_array( $posted['tipo'], array( 'nuevo', 'seguimiento' ), true ) ) {
			$errors->add( 'tsk_tipo', __( 'Indícanos si es tu primera vez o si eres paciente en seguimiento.', '2skin-core' ) );
		}
		if ( 'nuevo' === $posted['tipo'] && $cart['treatments'] ) {
			$errors->add(
				'tsk_nuevo',
				sprintf(
					/* translators: %s: URL para dejar solo la valoración. */
					__( 'Como paciente nuevo solo pagas la valoración. Tu reserva incluye tratamientos: <a href="%s">déjala solo con la valoración</a> o elige «Soy paciente en seguimiento».', '2skin-core' ),
					esc_url( self::keep_valoracion_url() )
				)
			);
		} elseif ( 'mixto' === $cart['kind'] ) {
			$errors->add(
				'tsk_mixto',
				sprintf(
					/* translators: %s: URL para dejar solo la valoración. */
					__( 'Cada reserva es una cita: la valoración o tus tratamientos. <a href="%s">Deja solo la valoración</a> o quita la valoración desde Tu reserva.', '2skin-core' ),
					esc_url( self::keep_valoracion_url() )
				)
			);
		}
		if ( $cita && ! $cart['service_id'] && 'mixto' !== $cart['kind'] ) {
			$errors->add( 'tsk_servicio', __( 'Este tratamiento todavía no tiene agenda en línea. Escríbenos para agendarlo.', '2skin-core' ) );
		}

		if ( ! preg_match( '/^[A-Za-z0-9\-]{4,20}$/', (string) ( $data['billing_tsk_documento'] ?? '' ) ) ) {
			$errors->add( 'billing_tsk_documento', __( 'Escribe tu número de documento sin puntos ni espacios.', '2skin-core' ) );
		}
		if ( '' === self::normalize_phone( (string) ( $data['billing_phone'] ?? '' ) ) ) {
			$errors->add( 'billing_phone', __( 'Escribe un celular válido, por ejemplo 300 123 4567.', '2skin-core' ) );
		}

		if ( $cart['service_id'] ) {
			$providers = self::slot_providers( $cart['service_id'], $posted['fecha'], $posted['hora'] );
			if ( ! $providers ) {
				$errors->add( 'tsk_cita', __( 'Elige un día y una hora disponibles para tu cita.', '2skin-core' ) );
			} elseif ( $posted['medica'] && ! in_array( $posted['medica'], $providers, true ) ) {
				$errors->add( 'tsk_medica', __( 'Tu médica no tiene ese horario libre. Elige otra hora u otra médica.', '2skin-core' ) );
			}
		}

		$missing = array_diff( array_keys( self::consents( 'seguimiento' === $posted['tipo'], $cita, (bool) $cart['bonos'] ) ), $posted['consents'] );
		if ( $missing ) {
			$errors->add( 'tsk_consent', __( 'Acepta las políticas y el consentimiento para poder confirmar.', '2skin-core' ) );
		}
	}

	/**
	 * Médicas disponibles en una franja (vacío si la franja no existe).
	 *
	 * @param int    $service_id Servicio.
	 * @param string $date       Y-m-d.
	 * @param string $time       H:i.
	 * @return int[]
	 */
	public static function slot_providers( int $service_id, string $date, string $time ): array {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) || ! preg_match( '/^\d{2}:\d{2}$/', $time ) ) {
			return array();
		}
		$slots = Reservation::availability( $service_id );
		return $slots[ $date ][ $time ] ?? array();
	}

	/**
	 * Agenda en Amelia justo antes de crear el pedido (ya pasó toda la validación).
	 *
	 * @param \WC_Customer $customer Cliente.
	 * @param array        $data     Datos del pago.
	 * @throws \Exception Si Amelia rechaza la franja (WooCommerce lo muestra y no crea el pedido).
	 */
	public static function book( \WC_Customer $customer, array $data ): void {
		$cart   = Reservation::analyze();
		$posted = self::posted();
		if ( ! $cart['service_id'] ) {
			return;
		}
		// Un intento anterior (p. ej. pago rechazado) pudo dejar su ítem de Amelia: se reemplaza por el nuevo.
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( ! empty( $item['ameliabooking'] ) ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
		$providers = self::slot_providers( $cart['service_id'], $posted['fecha'], $posted['hora'] );
		$provider  = $posted['medica'] && in_array( $posted['medica'], $providers, true ) ? $posted['medica'] : (int) $providers[ array_rand( $providers ) ];

		$error = Amelia::add_to_cart(
			array(
				'service_id'  => $cart['service_id'],
				'location_id' => (int) Reservation::config()['location'],
				'provider_id' => $provider,
				'start'       => $posted['fecha'] . ' ' . $posted['hora'],
				'duration'    => $cart['duration'],
				'customer'    => array(
					'first_name' => (string) $data['billing_first_name'],
					'last_name'  => (string) $data['billing_last_name'],
					'email'      => (string) $data['billing_email'],
					'phone'      => self::normalize_phone( (string) $data['billing_phone'] ),
				),
			)
		);
		if ( $error ) {
			throw new \Exception( esc_html( wp_strip_all_tags( $error ) ) );
		}

		// La valoración sin cita la reemplazó el ítem de Amelia; los tratamientos se quedan.
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( isset( $cart['valoracion'][ $key ] ) && empty( $item['ameliabooking'] ) ) {
				WC()->cart->remove_cart_item( $key );
			}
		}
		WC()->cart->calculate_totals();

		WC()->session->set( 'tsk_cita', array( 'provider' => $provider ) );
	}

	/**
	 * Guarda en el pedido lo que no es clínico: tipo, interés, cita, factura y consentimientos.
	 *
	 * @param \WC_Order $order Pedido.
	 * @param array     $data  Datos del pago.
	 */
	public static function save_order_meta( \WC_Order $order, array $data ): void {
		$cart    = Reservation::analyze();
		$posted  = self::posted();
		$session = (array) WC()->session->get( 'tsk_cita', array() );
		$now     = current_time( 'mysql' );

		$cita = 'bono' !== $cart['kind'];

		$order->update_meta_data( '_tsk_factura', $posted['factura'] ? 'si' : 'no' );
		$texts = self::consents( 'seguimiento' === $posted['tipo'], $cita, (bool) $cart['bonos'] );
		$order->update_meta_data(
			'_tsk_consentimientos',
			array(
				'aceptados' => array_values( array_intersect( array_keys( $texts ), $posted['consents'] ) ),
				'fecha'     => $now,
				'version'   => substr( md5( wp_json_encode( array( $texts, self::policy() ) ) ), 0, 10 ),
			)
		);
		if ( ! $cita ) {
			return;
		}
		$order->update_meta_data( '_tsk_tipo', $posted['tipo'] );
		$order->update_meta_data( '_tsk_interes', 'nuevo' === $posted['tipo'] ? $posted['interes'] : '' );
		$order->update_meta_data(
			'_tsk_cita',
			array(
				'fecha'     => $posted['fecha'],
				'hora'      => $posted['hora'],
				'medica'    => (int) ( $session['provider'] ?? 0 ),
				'servicio'  => $cart['service_id'],
				'duracion'  => $cart['duration'],
				'virtual'   => $cart['virtual'] || 'virtual' === Reservation::modalidad( self::booked_valoracion_variation() ),
			)
		);
		WC()->session->set( 'tsk_cita', null );
	}

	/**
	 * Variación de valoración en el carrito (con o sin cita), o 0.
	 */
	private static function booked_valoracion_variation(): int {
		$product = (int) Reservation::config()['product'];
		foreach ( WC()->cart ? WC()->cart->get_cart() : array() as $item ) {
			if ( (int) $item['product_id'] === $product ) {
				return (int) $item['variation_id'];
			}
		}
		return 0;
	}

	/**
	 * Datos de la reserva para la confirmación y el administrador.
	 *
	 * @param \WC_Order $order Pedido.
	 * @return array<string, string> Etiqueta => valor.
	 */
	public static function order_facts( \WC_Order $order ): array {
		$cita    = (array) $order->get_meta( '_tsk_cita' );
		$tipo    = (string) $order->get_meta( '_tsk_tipo' );
		$doctors = Reservation::doctors();
		$facts   = array( ( empty( $cita['fecha'] ) ? __( 'Pedido', '2skin-core' ) : __( 'Reserva', '2skin-core' ) ) => '2SK-' . $order->get_order_number() );

		if ( ! empty( $cita['fecha'] ) ) {
			$day = \DateTimeImmutable::createFromFormat( '!Y-m-d', $cita['fecha'], wp_timezone() );
			if ( $day ) {
				$facts[ __( 'Cita', '2skin-core' ) ] = ucfirst( wp_date( 'l j \\d\\e F', $day->getTimestamp() ) ) . ' · ' . Reservation::time_label( (string) $cita['hora'] );
			}
			$facts[ __( 'Modalidad', '2skin-core' ) ] = ! empty( $cita['virtual'] ) ? __( 'Virtual · Google Meet', '2skin-core' ) : __( 'Presencial', '2skin-core' ) . ' · ' . self::sede();
			$facts[ __( 'Profesional', '2skin-core' ) ] = 'seguimiento' === $tipo && isset( $doctors[ (int) $cita['medica'] ] )
				? $doctors[ (int) $cita['medica'] ]
				: __( 'Médica asignada según disponibilidad', '2skin-core' );
		}
		$interes = (string) $order->get_meta( '_tsk_interes' );
		$options = self::interest_options();
		if ( $interes && isset( $options[ $interes ] ) ) {
			$facts[ __( 'Interés', '2skin-core' ) ] = $options[ $interes ];
		}
		if ( $order->get_payment_method_title() ) {
			$facts[ __( 'Medio de pago', '2skin-core' ) ] = $order->get_payment_method_title();
		}
		$facts[ __( 'Documento', '2skin-core' ) ] = 'si' === $order->get_meta( '_tsk_factura' ) ? __( 'Factura electrónica', '2skin-core' ) : __( 'Comprobante de pago', '2skin-core' );
		$facts[ __( 'Total pagado', '2skin-core' ) ] = html_entity_decode( wp_strip_all_tags( wc_price( $order->get_total() ) ), ENT_QUOTES, 'UTF-8' );
		return $facts;
	}

	/**
	 * Resumen en el pedido del administrador.
	 *
	 * @param \WC_Order $order Pedido.
	 */
	public static function admin_order_data( \WC_Order $order ): void {
		$documento = (string) $order->get_meta( '_billing_tsk_documento' );
		$consents  = (array) $order->get_meta( '_tsk_consentimientos' );
		echo '<div class="tsk-order-data"><h3>' . esc_html__( '2Skin · Reserva', '2skin-core' ) . '</h3>';
		if ( $documento ) {
			echo '<p><strong>' . esc_html__( 'Cédula', '2skin-core' ) . ':</strong> ' . esc_html( $documento ) . '</p>';
		}
		$tipo = (string) $order->get_meta( '_tsk_tipo' );
		if ( $tipo ) {
			echo '<p><strong>' . esc_html__( 'Tipo', '2skin-core' ) . ':</strong> ' . esc_html( 'seguimiento' === $tipo ? __( 'Paciente en seguimiento', '2skin-core' ) : __( 'Primera vez', '2skin-core' ) ) . '</p>';
		}
		foreach ( self::order_facts( $order ) as $label => $value ) {
			echo '<p><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ) . '</p>';
		}
		if ( ! empty( $consents['fecha'] ) ) {
			/* translators: 1: consentimientos, 2: fecha, 3: versión. */
			echo '<p><strong>' . esc_html__( 'Consentimientos', '2skin-core' ) . ':</strong> ' . esc_html( sprintf( __( '%1$s · %2$s · v%3$s', '2skin-core' ), implode( ', ', (array) $consents['aceptados'] ), $consents['fecha'], $consents['version'] ) ) . '</p>';
		}
		echo '</div>';
	}
}
