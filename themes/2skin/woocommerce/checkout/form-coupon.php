<?php
/**
 * Checkout coupon form · código de bono de regalo en la Reserva.
 *
 * Igual a la de WooCommerce con los textos del bono; el código se valida como cupón.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class="woocommerce-form-coupon-toggle">
	<?php
		wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', esc_html__( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" role="button" aria-label="' . esc_attr__( 'Enter your coupon code', 'woocommerce' ) . '" aria-controls="woocommerce-checkout-form-coupon" aria-expanded="false" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'woocommerce' ) . '</a>' ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none" id="woocommerce-checkout-form-coupon">
	<p class="form-row tsk-field">
		<label for="coupon_code" class="tsk-field__label"><?php esc_html_e( 'Código del bono', '2skin' ); ?></label>
		<input type="text" name="coupon_code" class="input-text" placeholder="2SK-XXXX-XXXX" id="coupon_code" value="" autocomplete="off" />
		<span class="tsk-field__hint"><?php esc_html_e( 'El saldo del bono se descuenta del total; si sobra, queda disponible para tu próxima reserva.', '2skin' ); ?></span>
	</p>
	<p class="form-row">
		<button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Usar bono', '2skin' ); ?>"><?php esc_html_e( 'Usar bono', '2skin' ); ?></button>
	</p>
</form>
