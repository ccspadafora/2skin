<?php
/**
 * Checkout Payment Section · medios de pago de la Reserva (sección 05).
 *
 * Igual a la de WooCommerce, pero el botón de pago y su nonce están en el resumen lateral
 * (form-checkout.php). Este bloque se refresca por AJAX como fragmento .woocommerce-checkout-payment.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment tsk-payment">
	<?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods tsk-payment__methods" aria-label="<?php esc_attr_e( 'Medios de pago', '2skin' ); ?>">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li>';
				wc_print_notice( esc_html__( 'Por ahora no hay medios de pago disponibles. Escríbenos por WhatsApp para agendar.', '2skin' ), 'notice' );
				echo '</li>';
			}
			?>
		</ul>
	<?php else : ?>
		<p class="body-s tsk-text-muted"><?php esc_html_e( 'Elige tu valoración para ver los medios de pago.', '2skin' ); ?></p>
	<?php endif; ?>
	<noscript>
		<button type="submit" class="tsk-btn tsk-btn--secondary tsk-btn--sm" name="woocommerce_checkout_update_totals" value="1"><?php esc_html_e( 'Actualizar total', '2skin' ); ?></button>
	</noscript>
	<?php wc_get_template( 'checkout/terms.php' ); ?>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
