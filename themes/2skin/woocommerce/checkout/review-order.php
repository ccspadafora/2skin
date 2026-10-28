<?php
/**
 * Review order table · resumen lateral de la Reserva.
 *
 * Mantiene la clase raíz woocommerce-checkout-review-order-table: WooCommerce la reemplaza por AJAX.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table tsk-review">
	<?php if ( WC()->cart->is_empty() ) : ?>
		<div class="tsk-stack tsk-stack--3">
			<p class="body-s tsk-text-secondary"><?php esc_html_e( 'Elige tu valoración.', '2skin' ); ?></p>
			<?php
			foreach ( array( 'presencial' => __( 'Presencial', '2skin' ), 'virtual' => __( 'Virtual', '2skin' ) ) as $tsk_key => $tsk_label ) {
				echo tsk_button( $tsk_label . ' · ' . tsk_cop( tsk_valoracion_precio( $tsk_key ) ), add_query_arg( 'modalidad', $tsk_key, wc_get_checkout_url() ), array( 'variant' => 'secondary', 'class' => 'tsk-btn--block' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	<?php else : ?>
		<dl class="tsk-summary__rows">
			<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
				$visible  = apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
				if ( ! ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) ) {
					continue;
				}
				$tsk_name = class_exists( '\TwoSkin\Core\Catalog' )
					? \TwoSkin\Core\Catalog::item_label( (int) $cart_item['product_id'], (int) $cart_item['variation_id'] )
					: WC()->cart->get_item_product_name( $cart_item, $_product );
				$tsk_name = wp_strip_all_tags( apply_filters( 'woocommerce_cart_item_name', $tsk_name, $cart_item, $cart_item_key ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
				?>
				<div class="tsk-summary__row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<dt><?php echo esc_html( $tsk_name . ( $cart_item['quantity'] > 1 ? ' × ' . $cart_item['quantity'] : '' ) ); ?></dt>
					<dd><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></dd>
				</div>
				<?php
			}

			do_action( 'woocommerce_review_order_after_cart_contents' );
			?>

			<?php foreach ( WC()->cart->get_coupons() as $tsk_code => $tsk_coupon ) : ?>
				<div class="tsk-summary__row cart-discount coupon-<?php echo esc_attr( sanitize_title( $tsk_code ) ); ?>">
					<dt><?php wc_cart_totals_coupon_label( $tsk_coupon ); ?></dt>
					<dd><?php wc_cart_totals_coupon_html( $tsk_coupon ); ?></dd>
				</div>
			<?php endforeach; ?>

			<?php foreach ( WC()->cart->get_fees() as $tsk_fee ) : ?>
				<div class="tsk-summary__row fee">
					<dt><?php echo esc_html( $tsk_fee->name ); ?></dt>
					<dd><?php wc_cart_totals_fee_html( $tsk_fee ); ?></dd>
				</div>
			<?php endforeach; ?>

			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
			<div class="tsk-summary__row tsk-summary__row--total order-total">
				<dt class="body"><?php esc_html_e( 'Total · sin IVA', '2skin' ); ?></dt>
				<dd class="tsk-summary__total"><?php wc_cart_totals_order_total_html(); ?></dd>
			</div>
			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
		</dl>
	<?php endif; ?>
</div>
