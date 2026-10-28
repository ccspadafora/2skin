<?php
/**
 * Bonos de regalo: compra, emisión al pagar y redención en línea.
 *
 * - Compra: un producto oculto «Bono de regalo»; el valor elegido viaja en el ítem del carrito.
 * - Emisión: al quedar pagado el pedido, un código por unidad con vigencia de 60 días.
 * - Redención en línea: el código se escribe en el campo de cupón; se convierte en un cupón virtual
 *   de WooCommerce por el saldo (es un descuento, el cobro lo sigue haciendo la pasarela). El saldo se
 *   descuenta al crear el pedido y se devuelve si el pedido se cancela, falla o se reembolsa.
 *
 * @package TwoSkin\Core
 */

namespace TwoSkin\Core\Gifts;

defined( 'ABSPATH' ) || exit;

/**
 * Lógica de los bonos.
 */
final class Gifts {

	public const VIGENCIA_DIAS = 60;
	public const OPTION        = 'tsk_bono_producto';

	private const ITEM_KEY  = 'tsk_bono_valor';
	private const LIMIT_KEY = 'tsk_bono_intentos_';

	/**
	 * Engancha todo.
	 */
	public static function hooks(): void {
		add_filter( 'tsk_productos_libres', array( self::class, 'free_products' ) );

		// Compra.
		add_filter( 'woocommerce_add_to_cart_validation', array( self::class, 'validate_add' ), 5, 3 );
		add_filter( 'woocommerce_add_cart_item_data', array( self::class, 'cart_item_data' ), 10, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( self::class, 'apply_price' ) );
		add_filter( 'woocommerce_add_cart_item', array( self::class, 'apply_price' ) );
		add_filter( 'woocommerce_cart_item_name', array( self::class, 'cart_item_name' ), 20, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( self::class, 'order_item' ), 10, 3 );

		// Emisión y anulación.
		add_action( 'woocommerce_order_status_processing', array( self::class, 'issue_for_order' ), 5 );
		add_action( 'woocommerce_order_status_completed', array( self::class, 'issue_for_order' ), 5 );
		add_action( 'woocommerce_order_status_cancelled', array( self::class, 'void_for_order' ) );
		add_action( 'woocommerce_order_status_refunded', array( self::class, 'void_for_order' ) );

		// Redención en línea.
		add_filter( 'woocommerce_get_shop_coupon_data', array( self::class, 'coupon_data' ), 10, 2 );
		add_filter( 'woocommerce_coupon_error', array( self::class, 'coupon_error' ), 10, 3 );
		add_filter( 'woocommerce_coupon_message', array( self::class, 'coupon_success' ), 10, 3 );
		add_filter( 'woocommerce_cart_totals_coupon_label', array( self::class, 'coupon_label' ), 10, 2 );
		add_filter( 'woocommerce_checkout_coupon_message', array( self::class, 'coupon_message' ) );
		add_action( 'woocommerce_checkout_order_created', array( self::class, 'debit_order' ) );
		add_action( 'woocommerce_order_status_cancelled', array( self::class, 'reverse_order' ), 5 );
		add_action( 'woocommerce_order_status_failed', array( self::class, 'reverse_order' ), 5 );
		add_action( 'woocommerce_order_status_refunded', array( self::class, 'reverse_order' ), 5 );

		add_action( 'deleted_user', array( Repository::class, 'detach_user' ) );
	}

	/**
	 * Producto «Bono de regalo».
	 */
	public static function product_id(): int {
		return (int) get_option( self::OPTION, 0 );
	}

	/**
	 * Valores sugeridos del diseño, mínimo y máximo del valor libre.
	 *
	 * @return array{presets: int[], min: int, max: int}
	 */
	public static function limits(): array {
		return (array) apply_filters(
			'tsk_bonos_valores',
			array(
				'presets' => array( 500000, 1000000 ),
				'min'     => 500000,
				'max'     => 10000000,
			)
		);
	}

	/**
	 * El bono no es un tratamiento: no pide seguimiento.
	 *
	 * @param int[] $ids Productos libres.
	 */
	public static function free_products( array $ids ): array {
		$ids[] = self::product_id();
		return $ids;
	}

	/**
	 * ¿Es un ítem de bono?
	 *
	 * @param array $item Ítem del carrito.
	 */
	public static function is_gift_item( array $item ): bool {
		return self::product_id() && (int) ( $item['product_id'] ?? 0 ) === self::product_id();
	}

	/**
	 * Valor enviado al añadir el bono (0 si no es válido).
	 */
	private static function posted_value(): int {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- añadir al carrito no usa nonce en WooCommerce; el valor se valida aquí.
		$raw    = isset( $_REQUEST[ self::ITEM_KEY ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ self::ITEM_KEY ] ) ) : '';
		$value  = (int) preg_replace( '/\D+/', '', $raw );
		$limits = self::limits();
		return $value >= $limits['min'] && $value <= $limits['max'] && 0 === $value % 1000 ? $value : 0;
	}

	/**
	 * Valida el valor del bono al añadirlo.
	 *
	 * @param bool $passed     Resultado previo.
	 * @param int  $product_id Producto.
	 * @param int  $quantity   Cantidad.
	 */
	public static function validate_add( bool $passed, int $product_id, int $quantity ): bool {
		if ( ! $passed || $product_id !== self::product_id() ) {
			return $passed;
		}
		if ( ! self::posted_value() ) {
			$limits = self::limits();
			wc_add_notice(
				sprintf(
					/* translators: 1: mínimo, 2: máximo. */
					__( 'Elige un valor entre %1$s y %2$s, en miles de pesos.', '2skin-core' ),
					wp_strip_all_tags( wc_price( $limits['min'] ) ),
					wp_strip_all_tags( wc_price( $limits['max'] ) )
				),
				'error'
			);
			return false;
		}
		return true;
	}

	/**
	 * Guarda el valor en el ítem (bonos de distinto valor quedan en líneas distintas).
	 *
	 * @param array $data       Datos del ítem.
	 * @param int   $product_id Producto.
	 */
	public static function cart_item_data( array $data, int $product_id ): array {
		if ( $product_id === self::product_id() ) {
			$data[ self::ITEM_KEY ] = self::posted_value();
		}
		return $data;
	}

	/**
	 * Precio del ítem = valor del bono.
	 *
	 * @param array $item Ítem.
	 */
	public static function apply_price( array $item ): array {
		if ( ! empty( $item[ self::ITEM_KEY ] ) && isset( $item['data'] ) && $item['data'] instanceof \WC_Product ) {
			$item['data']->set_price( (float) $item[ self::ITEM_KEY ] );
		}
		return $item;
	}

	/**
	 * Nombre con el valor: «Bono de regalo · $500.000».
	 *
	 * @param string $name Nombre.
	 * @param array  $item Ítem.
	 */
	public static function cart_item_name( string $name, array $item ): string {
		if ( empty( $item[ self::ITEM_KEY ] ) ) {
			return $name;
		}
		return self::item_name( (float) $item[ self::ITEM_KEY ] );
	}

	/**
	 * Nombre de un bono por valor.
	 *
	 * @param float $value Valor.
	 */
	public static function item_name( float $value ): string {
		return __( 'Bono de regalo', '2skin-core' ) . ' · ' . html_entity_decode( wp_strip_all_tags( wc_price( $value ) ), ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Lleva el valor y el nombre al ítem del pedido.
	 *
	 * @param \WC_Order_Item_Product $item     Ítem del pedido.
	 * @param string                 $cart_key Clave del carrito.
	 * @param array                  $values   Ítem del carrito.
	 */
	public static function order_item( \WC_Order_Item_Product $item, string $cart_key, array $values ): void {
		if ( empty( $values[ self::ITEM_KEY ] ) ) {
			return;
		}
		$item->set_name( self::item_name( (float) $values[ self::ITEM_KEY ] ) );
		$item->add_meta_data( '_tsk_bono_valor', (int) $values[ self::ITEM_KEY ], true );
	}

	/**
	 * Emite los códigos de un pedido pagado (una vez). Corre antes de los correos de WooCommerce,
	 * así los códigos salen en el correo del pedido.
	 *
	 * @param int $order_id Pedido.
	 */
	public static function issue_for_order( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order || 'si' === $order->get_meta( '_tsk_bonos_emitidos' ) ) {
			return;
		}
		$issued = array();
		$vence  = wp_date( 'Y-m-d', strtotime( '+' . self::VIGENCIA_DIAS . ' days' ) );
		foreach ( $order->get_items() as $item ) {
			$value = (int) $item->get_meta( '_tsk_bono_valor' );
			if ( ! $value ) {
				continue;
			}
			$codes = array();
			for ( $i = 0; $i < $item->get_quantity(); $i++ ) {
				$bono    = Repository::issue( $value, $vence, $order_id, $item->get_id(), $order->get_customer_id() );
				$codes[] = $bono['codigo'];
			}
			$item->update_meta_data( __( 'Código', '2skin-core' ), implode( ', ', $codes ) );
			$item->update_meta_data( __( 'Vence', '2skin-core' ), \TwoSkin\Core\Patients\Repository::format_date( $vence ) );
			$item->save();
			$issued = array_merge( $issued, $codes );
		}
		if ( ! $issued ) {
			return;
		}
		$order->update_meta_data( '_tsk_bonos_emitidos', 'si' );
		/* translators: %s: códigos. */
		$order->add_order_note( sprintf( __( 'Bonos emitidos: %s', '2skin-core' ), implode( ', ', $issued ) ) );
		$order->save();
	}

	/**
	 * Anula los bonos de un pedido cancelado o reembolsado (solo si no se han usado).
	 *
	 * @param int $order_id Pedido.
	 */
	public static function void_for_order( int $order_id ): void {
		$order = wc_get_order( $order_id );
		foreach ( Repository::for_order( $order_id ) as $bono ) {
			if ( 'anulado' === $bono['estado'] ) {
				continue;
			}
			if ( $bono['saldo'] < $bono['valor'] ) {
				/* translators: %s: código. */
				$order && $order->add_order_note( sprintf( __( 'El bono %s ya tiene consumos: no se anuló automáticamente. Revísalo en Bonos.', '2skin-core' ), $bono['codigo'] ) );
				continue;
			}
			Repository::void( $bono['id'], $order_id, __( 'Pedido de compra cancelado o reembolsado', '2skin-core' ) );
			/* translators: %s: código. */
			$order && $order->add_order_note( sprintf( __( 'Bono %s anulado.', '2skin-core' ), $bono['codigo'] ) );
		}
	}

	/**
	 * Cupón virtual por el saldo de un bono válido. Limita los intentos fallidos por IP.
	 *
	 * @param mixed $data Datos previos.
	 * @param mixed $code Código escrito.
	 * @return mixed
	 */
	public static function coupon_data( $data, $code ) {
		if ( $data || ! is_string( $code ) || ! Repository::looks_like( $code ) ) {
			return $data;
		}
		if ( self::too_many_attempts() ) {
			return $data;
		}
		$bono = Repository::find( $code );
		if ( ! $bono || ! $bono['usable'] ) {
			self::count_attempt();
			return $data;
		}
		$expires = \DateTimeImmutable::createFromFormat( '!Y-m-d', $bono['vence'], wp_timezone() );
		return array(
			'code'                 => strtolower( $bono['codigo'] ),
			'discount_type'        => 'fixed_cart',
			'amount'               => $bono['saldo'],
			'individual_use'       => false,
			'excluded_product_ids' => array( self::product_id() ),
			'date_expires'         => $expires ? $expires->modify( '+1 day' )->getTimestamp() : null,
			'description'          => __( 'Bono de regalo 2Skin', '2skin-core' ),
		);
	}

	/**
	 * ¿Superó la IP los intentos fallidos (10 por hora)?
	 */
	private static function too_many_attempts(): bool {
		return (int) get_transient( self::LIMIT_KEY . self::ip_hash() ) >= 10;
	}

	/**
	 * Suma un intento fallido.
	 */
	private static function count_attempt(): void {
		$key = self::LIMIT_KEY . self::ip_hash();
		set_transient( $key, (int) get_transient( $key ) + 1, HOUR_IN_SECONDS );
	}

	/**
	 * Huella de la IP (no se guarda la IP).
	 */
	private static function ip_hash(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return substr( wp_hash( $ip ), 0, 16 );
	}

	/**
	 * Mensaje claro cuando el código de bono no sirve.
	 *
	 * @param string     $message Mensaje.
	 * @param int        $code    Código de error.
	 * @param \WC_Coupon $coupon  Cupón.
	 */
	public static function coupon_error( $message, $code, $coupon ) {
		$typed = $coupon instanceof \WC_Coupon ? $coupon->get_code() : '';
		if ( ! Repository::looks_like( (string) $typed ) ) {
			return $message;
		}
		if ( self::too_many_attempts() ) {
			return __( 'Demasiados intentos con códigos no válidos. Intenta de nuevo en una hora o escríbenos.', '2skin-core' );
		}
		if ( \WC_Coupon::E_WC_COUPON_NOT_EXIST === (int) $code ) {
			return __( 'Este bono no existe, está vencido o ya no tiene saldo.', '2skin-core' );
		}
		if ( \WC_Coupon::E_WC_COUPON_NOT_APPLICABLE === (int) $code || \WC_Coupon::E_WC_COUPON_EXCLUDED_PRODUCTS === (int) $code ) {
			return __( 'Un bono no se puede usar para comprar otro bono.', '2skin-core' );
		}
		return $message;
	}

	/**
	 * Mensajes de éxito con el saldo del bono.
	 *
	 * @param string     $message Mensaje.
	 * @param int        $code    Código del mensaje.
	 * @param \WC_Coupon $coupon  Cupón.
	 */
	public static function coupon_success( $message, $code, $coupon ) {
		if ( ! $coupon instanceof \WC_Coupon || ! Repository::looks_like( $coupon->get_code() ) ) {
			return $message;
		}
		if ( \WC_Coupon::WC_COUPON_SUCCESS === (int) $code ) {
			/* translators: %s: saldo. */
			return sprintf( __( 'Bono aplicado. Saldo disponible: %s; lo que no uses queda para tu próxima reserva.', '2skin-core' ), html_entity_decode( wp_strip_all_tags( wc_price( $coupon->get_amount() ) ), ENT_QUOTES, 'UTF-8' ) );
		}
		if ( \WC_Coupon::WC_COUPON_REMOVED === (int) $code ) {
			return __( 'Quitaste el bono de esta reserva.', '2skin-core' );
		}
		return $message;
	}

	/**
	 * «Bono 2SK-…» en los totales.
	 *
	 * @param string     $label  Etiqueta.
	 * @param \WC_Coupon $coupon Cupón.
	 */
	public static function coupon_label( $label, $coupon ) {
		if ( $coupon instanceof \WC_Coupon && Repository::looks_like( $coupon->get_code() ) ) {
			/* translators: %s: código. */
			return sprintf( __( 'Bono %s', '2skin-core' ), Repository::normalize( $coupon->get_code() ) );
		}
		return $label;
	}

	/**
	 * Enlace para escribir el código en la Reserva.
	 */
	public static function coupon_message(): string {
		return esc_html__( '¿Tienes un bono de regalo?', '2skin-core' ) . ' <a href="#" role="button" aria-label="' . esc_attr__( 'Escribe el código de tu bono', '2skin-core' ) . '" aria-controls="woocommerce-checkout-form-coupon" aria-expanded="false" class="showcoupon">' . esc_html__( 'Úsalo aquí', '2skin-core' ) . '</a>';
	}

	/**
	 * Descuenta del saldo lo que el pedido usó de cada bono. Si el pedido ya tenía descuentos (reintento
	 * de pago del mismo pedido), primero los devuelve. Si no alcanza el saldo, no crea el pedido.
	 *
	 * @param \WC_Order $order Pedido.
	 * @throws \Exception Si un bono ya no tiene el saldo usado.
	 */
	public static function debit_order( \WC_Order $order ): void {
		self::reverse( $order, __( 'Pedido actualizado antes del pago', '2skin-core' ) );

		$debited = array();
		foreach ( $order->get_items( 'coupon' ) as $item ) {
			if ( ! Repository::looks_like( $item->get_code() ) ) {
				continue;
			}
			$bono   = Repository::find( $item->get_code() );
			$amount = round( (float) $item->get_discount() + (float) $item->get_discount_tax(), 2 );
			if ( ! $bono || $amount <= 0 ) {
				continue;
			}
			if ( ! Repository::debit( $bono['id'], $amount, 'redencion_online', $order->get_id() ) ) {
				foreach ( $debited as $id => $done ) {
					Repository::credit( $id, $done, $order->get_id(), __( 'Pedido no creado', '2skin-core' ) );
				}
				$order->update_meta_data( '_tsk_bonos_debitados', array() );
				$order->set_status( 'cancelled' );
				$order->save();
				throw new \Exception( esc_html__( 'El saldo de tu bono cambió. Quítalo y vuelve a escribirlo para ver el saldo actual.', '2skin-core' ) );
			}
			$debited[ $bono['id'] ] = $amount;
		}
		$order->update_meta_data( '_tsk_bonos_debitados', $debited );
		$order->save();
		if ( $debited ) {
			/* translators: %s: monto. */
			$order->add_order_note( sprintf( __( 'Saldo de bonos descontado: %s', '2skin-core' ), wp_strip_all_tags( wc_price( array_sum( $debited ) ) ) ) );
		}
	}

	/**
	 * Devuelve el saldo usado por un pedido que no se pagó (una sola vez por descuento).
	 *
	 * @param int $order_id Pedido.
	 */
	public static function reverse_order( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			self::reverse( $order, __( 'Pedido cancelado, fallido o reembolsado', '2skin-core' ) );
		}
	}

	/**
	 * Devuelve a cada bono lo descontado por el pedido y limpia el registro.
	 *
	 * @param \WC_Order $order Pedido (la misma instancia que se guardará después).
	 * @param string    $nota  Motivo.
	 */
	private static function reverse( \WC_Order $order, string $nota ): void {
		$debited = array_filter( array_map( 'floatval', (array) $order->get_meta( '_tsk_bonos_debitados' ) ) );
		if ( ! $debited ) {
			return;
		}
		foreach ( $debited as $id => $amount ) {
			Repository::credit( (int) $id, $amount, $order->get_id(), $nota );
		}
		$order->update_meta_data( '_tsk_bonos_debitados', array() );
		$order->save();
		/* translators: %s: monto. */
		$order->add_order_note( sprintf( __( 'Saldo de bonos devuelto: %s', '2skin-core' ), wp_strip_all_tags( wc_price( array_sum( $debited ) ) ) ) );
	}
}
